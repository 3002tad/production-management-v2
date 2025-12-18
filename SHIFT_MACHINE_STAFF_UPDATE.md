# 🔄 Cập nhật Logic Phân Ca - Máy (Shift-Machine-Staff Assignment)

## 📌 Tổng quan thay đổi

### **Mô hình CŨ (Deprecated):**
- Phân máy vào ca (`shift_machine_assignments`)
- Leader gán máy cho từng ca làm việc
- Máy không cố định theo dây chuyền

### **Mô hình MỚI:**
- **Máy đã được gán CỐ ĐỊNH vào dây chuyền** theo công năng
- **Không phân máy vào ca** nữa
- **Chỉ phân công nhân sự vào từng máy** trong ca đó
- **Check role tự động**:
  - Máy **production** (sản xuất) → Chỉ hiển thị **Worker**
  - Máy **quality_control** (QC) → Chỉ hiển thị **QC staff**

---

## 🗄️ Database Changes

### **Migration 013** (`db/migrations/Cap2/CaLamViec/013_update_shift_machine_staff_assignment.sql`)

#### 1. Bảng mới: `shift_machine_staff`
```sql
CREATE TABLE `shift_machine_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shift_id` int(11) NOT NULL COMMENT 'ID ca làm việc',
  `machine_id` int(11) NOT NULL COMMENT 'ID máy (từ machines)',
  `staff_id` int(11) NOT NULL COMMENT 'ID nhân viên',
  `assigned_at` timestamp DEFAULT current_timestamp(),
  `assigned_by` int(11) DEFAULT NULL,
  `status` tinyint(2) DEFAULT 1 COMMENT '1=Active, 0=Removed',
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_shift_machine_staff` (`shift_id`, `machine_id`, `staff_id`, `status`),
  CONSTRAINT `fk_sms_shift` FOREIGN KEY (`shift_id`) REFERENCES `production_shifts` (`shift_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sms_machine` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sms_staff` FOREIGN KEY (`staff_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
);
```

#### 2. Thêm field `machine_type` vào bảng `machines`
```sql
ALTER TABLE machines ADD machine_type VARCHAR(50) DEFAULT 'production' 
  COMMENT 'production/quality_control/maintenance' AFTER stage_type;
```

#### 3. Cập nhật máy QC mẫu
```sql
UPDATE machines 
SET machine_type = 'quality_control' 
WHERE stage_type LIKE '%quality%' OR stage_type LIKE '%QC%' OR stage_type LIKE '%inspect%';
```

**Note:** Bảng `shift_machine_assignments` vẫn giữ lại để tham khảo lịch sử, nhưng không dùng nữa.

---

## 🔧 Model Updates

### **ShiftModel** (`application/models/leader/ShiftModel.php`)

#### New Properties
```php
protected $table_machine_staff = 'shift_machine_staff';
```

#### New Methods

**1. getMachinesByLine($line_id)**
```php
// Lấy tất cả máy theo dây chuyền (đã cố định)
public function getMachinesByLine($line_id)
{
    $this->db->select('machines.*, 
        machines.code as machine_code, 
        machines.name as machine_name, 
        machines.stage_type as machine_type,
        machines.machine_type as equipment_category');
    $this->db->from('machines');
    $this->db->where('machines.line_id', $line_id);
    $this->db->where('machines.status', 'active');
    $this->db->order_by('machines.code', 'ASC');
    return $this->db->get()->result();
}
```

**2. getMachinesWithStaff($shift_id, $line_id)**
```php
// Lấy máy + danh sách nhân sự đã gán
public function getMachinesWithStaff($shift_id, $line_id)
{
    $machines = $this->getMachinesByLine($line_id);
    
    foreach ($machines as &$machine) {
        $machine->assigned_staff = $this->getStaffByMachine($shift_id, $machine->id);
    }
    
    return $machines;
}
```

**3. assignStaffToMachine($shift_id, $machine_id, $staff_id, $assigned_by, $notes)**
```php
// Phân công nhân sự vào máy cụ thể
public function assignStaffToMachine($shift_id, $machine_id, $staff_id, $assigned_by, $notes = null)
{
    // Check duplicate
    $exists = $this->db->where([
        'shift_id' => $shift_id,
        'machine_id' => $machine_id,
        'staff_id' => $staff_id,
        'status' => 1
    ])->get($this->table_machine_staff)->row();

    if ($exists) {
        return false; // Already assigned
    }

    $data = [
        'shift_id' => $shift_id,
        'machine_id' => $machine_id,
        'staff_id' => $staff_id,
        'assigned_by' => $assigned_by,
        'notes' => $notes
    ];

    return $this->db->insert($this->table_machine_staff, $data);
}
```

**4. getStaffByRole($role_names)**
```php
// Lấy nhân viên theo role (worker, qc)
public function getStaffByRole($role_names = ['worker', 'qc'])
{
    $this->db->select('user.user_id, user.username, user.email, 
        staff.id_staff, staff.staff_name as full_name, 
        staff.department, staff.position,
        roles.role_name');
    $this->db->from('user');
    $this->db->join('staff', 'staff.id_staff = user.user_id', 'left');
    $this->db->join('roles', 'roles.role_id = user.role_id');
    $this->db->where_in('roles.role_name', $role_names);
    $this->db->order_by('staff.staff_name', 'ASC');
    return $this->db->get()->result();
}
```

---

## 🎮 Controller Updates

### **Shift** (`application/controllers/leader/Shift.php`)

#### New Endpoints

**1. get_machines_by_line() - AJAX**
```php
public function get_machines_by_line()
{
    header('Content-Type: application/json');
    
    $line_id = $this->input->post('line_id');
    $shift_id = $this->input->post('shift_id');
    
    if ($shift_id) {
        $machines = $this->shiftModel->getMachinesWithStaff($shift_id, $line_id);
    } else {
        $machines = $this->shiftModel->getMachinesByLine($line_id);
    }
    
    echo json_encode([
        'success' => true, 
        'data' => $machines ?: [], 
        'count' => count($machines)
    ]);
}
```

**2. get_staff_by_role() - AJAX**
```php
public function get_staff_by_role()
{
    header('Content-Type: application/json');
    
    $machine_type = $this->input->post('machine_type'); // 'production' or 'quality_control'
    
    // Map machine type to roles
    $roles = [];
    if ($machine_type === 'quality_control') {
        $roles = ['qc'];
    } else {
        $roles = ['worker'];
    }
    
    $staff = $this->shiftModel->getStaffByRole($roles);
    
    echo json_encode([
        'success' => true, 
        'data' => $staff ?: [], 
        'count' => count($staff),
        'roles_filter' => $roles
    ]);
}
```

**3. assign_staff_to_machine()**
```php
public function assign_staff_to_machine()
{
    $shift_id = $this->input->post('shift_id');
    $machine_id = $this->input->post('machine_id');
    $staff_id = $this->input->post('staff_id');
    $notes = $this->input->post('notes');

    $assigned_by = $this->session->userdata('user_id');

    $result = $this->shiftModel->assignStaffToMachine($shift_id, $machine_id, $staff_id, $assigned_by, $notes);

    if ($result) {
        $this->session->set_flashdata('success', 'Phân công nhân sự vào máy thành công');
    } else {
        $this->session->set_flashdata('error', 'Nhân sự đã được gán vào máy này');
    }

    redirect('leader/shift/detail/' . $shift_id . '?tab=machine');
}
```

**4. remove_staff_from_machine()**
```php
public function remove_staff_from_machine()
{
    $assignment_id = $this->input->post('assignment_id');
    $shift_id = $this->input->post('shift_id');

    $result = $this->shiftModel->removeStaffFromMachine($assignment_id);

    if ($result) {
        $this->session->set_flashdata('success', 'Đã xóa phân công');
    } else {
        $this->session->set_flashdata('error', 'Xóa thất bại');
    }

    redirect('leader/shift/detail/' . $shift_id . '?tab=machine');
}
```

---

## 🎨 View Updates

### **detail.php** (`application/views/leader/shift/detail.php`)

#### Machine Tab - OLD vs NEW

**OLD:**
```php
<!-- Table showing assigned machines to shift -->
<table class="table">
  <thead>
    <tr>
      <th>Máy</th>
      <th>Loại</th>
      <th>Thời gian gán</th>
      <th>Trạng thái</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($assigned_machines as $machine): ?>
      <tr>
        <td><?= $machine->machine_code ?></td>
        ...
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<button data-bs-toggle="modal" data-bs-target="#addMachineModal">Gán máy</button>
```

**NEW:**
```php
<!-- Info alert -->
<div class="alert alert-info">
  <strong>Lưu ý:</strong> Máy đã được gán cố định vào dây chuyền <?= $shift->line_code ?>. 
  Chỉ cần phân công nhân sự vào từng máy.
</div>

<!-- Machine cards grid -->
<div class="row" id="machineCardsContainer">
  <!-- Machines loaded via AJAX -->
</div>
```

#### New Modal: Assign Staff to Machine
```php
<div class="modal fade" id="assignStaffToMachineModal">
  <div class="modal-dialog">
    <form method="POST" action="<?= site_url('leader/shift/assign_staff_to_machine'); ?>">
      <input type="hidden" name="shift_id" value="<?= $shift->shift_id ?>">
      <input type="hidden" name="machine_id" id="assignMachineId">
      
      <div class="modal-body">
        <div class="mb-3">
          <label>Máy</label>
          <input type="text" id="assignMachineName" class="form-control" readonly>
        </div>
        
        <div class="mb-3">
          <label>Loại máy</label>
          <input type="text" id="assignMachineType" class="form-control" readonly>
          <small id="assignRoleHint"></small>
        </div>
        
        <div class="mb-3">
          <label>Chọn nhân viên</label>
          <select name="staff_id" class="form-control" id="assignStaffSelect">
            <!-- Loaded via AJAX based on machine type -->
          </select>
        </div>
        
        <div class="mb-3">
          <label>Ghi chú</label>
          <textarea name="notes" class="form-control"></textarea>
        </div>
      </div>
      
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Phân công</button>
      </div>
    </form>
  </div>
</div>
```

#### JavaScript Logic

**1. Load machines on page load/tab show**
```javascript
$('button[data-bs-target="#machine"]').on('shown.bs.tab', function (e) {
    loadMachinesByLine();
});
```

**2. Load machines via AJAX**
```javascript
function loadMachinesByLine() {
    const lineId = <?= $shift->line_id ?>;
    const shiftId = <?= $shift->shift_id ?>;
    
    $.ajax({
        url: '<?= site_url('leader/shift/get_machines_by_line'); ?>',
        method: 'POST',
        data: { line_id: lineId, shift_id: shiftId },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data.length > 0) {
                displayMachineCards(response.data);
            }
        }
    });
}
```

**3. Display machine cards with staff**
```javascript
function displayMachineCards(machines) {
    let html = '';
    machines.forEach(function(machine) {
        const machineType = machine.equipment_category || 'production';
        const machineTypeText = machineType === 'quality_control' ? 'Kiểm định chất lượng (QC)' : 'Sản xuất';
        const badgeClass = machineType === 'quality_control' ? 'bg-warning' : 'bg-info';
        
        let staffList = '';
        if (machine.assigned_staff && machine.assigned_staff.length > 0) {
            machine.assigned_staff.forEach(function(staff) {
                staffList += `
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <p class="mb-0">${staff.full_name || staff.username}</p>
                            <small>${staff.role_name}</small>
                        </div>
                        <form method="POST" action="remove_staff_from_machine">
                            <input type="hidden" name="assignment_id" value="${staff.id}">
                            <button type="submit" class="btn btn-link text-danger">
                                <i class="material-icons">delete</i>
                            </button>
                        </form>
                    </div>
                `;
            });
        } else {
            staffList = '<p class="text-muted"><em>Chưa có nhân sự</em></p>';
        }
        
        html += `
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6>${machine.machine_code}</h6>
                        <p class="text-xs">${machine.machine_name}</p>
                        <span class="badge ${badgeClass}">${machineTypeText}</span>
                    </div>
                    <div class="card-body">
                        <h6 class="text-xs text-uppercase">Nhân sự:</h6>
                        ${staffList}
                        <button class="btn btn-sm btn-outline-primary w-100 mt-3" 
                                onclick="openAssignStaffModal(${machine.id}, '${machine.machine_code}', '${machine.machine_name}', '${machineType}', '${machineTypeText}')">
                            <i class="material-icons">person_add</i> Thêm nhân sự
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    $('#machineCardsContainer').html(html);
}
```

**4. Open assign modal with role filtering**
```javascript
function openAssignStaffModal(machineId, machineCode, machineName, machineType, machineTypeText) {
    $('#assignMachineId').val(machineId);
    $('#assignMachineName').val(machineCode + ' - ' + machineName);
    $('#assignMachineType').val(machineTypeText);
    
    // Set role hint
    if (machineType === 'quality_control') {
        $('#assignRoleHint').html('Chỉ hiển thị nhân viên QC');
    } else {
        $('#assignRoleHint').html('Chỉ hiển thị công nhân (Worker)');
    }
    
    // Load staff by role
    $.ajax({
        url: '<?= site_url('leader/shift/get_staff_by_role'); ?>',
        method: 'POST',
        data: { machine_type: machineType },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data.length > 0) {
                let options = '<option value="">-- Chọn nhân viên --</option>';
                response.data.forEach(function(staff) {
                    options += `<option value="${staff.user_id}">${staff.full_name || staff.username} (${staff.role_name})</option>`;
                });
                $('#assignStaffSelect').html(options);
            } else {
                $('#assignStaffSelect').html('<option value="">Không có nhân viên phù hợp</option>');
            }
        }
    });
    
    $('#assignStaffToMachineModal').modal('show');
}
```

---

## 📋 Workflow mới

### **1. Leader tạo ca:**
```
Chọn dây chuyền → Ca tự động có danh sách máy (đã cố định)
```

### **2. Leader phân công nhân sự:**
```
Vào tab "Máy/Dây Chuyền" → Xem các máy card
→ Click "Thêm nhân sự" trên từng máy
→ Chọn nhân viên (đã lọc theo role: worker/qc)
→ Phân công
```

### **3. Role filtering tự động:**
```
- Máy production → Dropdown chỉ hiện Worker
- Máy quality_control → Dropdown chỉ hiện QC
```

### **4. Xem phân công:**
```
Mỗi máy card hiển thị:
- Mã máy + Tên
- Badge: "Sản xuất" (xanh) hoặc "Kiểm định chất lượng" (vàng)
- Danh sách nhân sự đã gán (có thể xóa)
- Nút "Thêm nhân sự"
```

---

## 🧪 Testing Steps

### **1. Run Migration 013**
```sql
-- Via phpMyAdmin
SOURCE db/migrations/Cap2/CaLamViec/013_update_shift_machine_staff_assignment.sql;
```

### **2. Check Database**
```sql
-- Check new table
DESCRIBE shift_machine_staff;

-- Check machine_type field
SELECT id, code, name, machine_type FROM machines LIMIT 10;

-- Update some machines to quality_control for testing
UPDATE machines SET machine_type = 'quality_control' WHERE id IN (3, 7, 11);
```

### **3. Test Workflow**
1. Login as Leader
2. Navigate to **Leader → Shift Management → Detail**
3. Click tab **"Máy/Dây Chuyền"**
4. Verify: Machine cards loaded automatically
5. Click **"Thêm nhân sự"** on a **production machine**
   - Verify: Only Workers displayed in dropdown
6. Click **"Thêm nhân sự"** on a **quality_control machine**
   - Verify: Only QC staff displayed in dropdown
7. Assign staff to machines
8. Verify: Staff appears in machine card
9. Click delete button on assigned staff
10. Verify: Staff removed from machine

### **4. Check AJAX Endpoints**
```javascript
// In browser console
// Test 1: Get machines by line
fetch('/leader/shift/get_machines_by_line', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'line_id=1&shift_id=1'
}).then(r => r.json()).then(console.log);

// Test 2: Get staff by role
fetch('/leader/shift/get_staff_by_role', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'machine_type=quality_control'
}).then(r => r.json()).then(console.log);
```

---

## ✅ Checklist

- [x] Create migration 013 with `shift_machine_staff` table
- [x] Add `machine_type` field to `machines` table
- [x] Update `ShiftModel` with new methods
- [x] Add AJAX endpoints in `Shift` controller
- [x] Update `detail.php` view with machine cards
- [x] Add assign staff modal with role filtering
- [x] Add JavaScript for AJAX loading and display
- [ ] Run migration 013 in phpMyAdmin
- [ ] Test assign worker to production machine
- [ ] Test assign QC to quality_control machine
- [ ] Test remove staff from machine
- [ ] Verify role filtering works correctly

---

## 🎯 Benefits

1. **Simplified Workflow** - No more manual machine assignment to shifts
2. **Fixed Machine-Line Relationship** - Machines permanently tied to lines
3. **Role-Based Security** - Automatic filtering prevents wrong assignments
4. **Better UX** - Visual machine cards with staff lists
5. **Data Integrity** - FK constraints ensure valid references

---

**Updated by:** GitHub Copilot  
**Date:** December 18, 2025  
**Status:** ✅ Code Complete - Ready for migration and testing
