# Dashboard Ca Làm Việc - Cập nhật Cấu trúc Theo Kế hoạch

## 📋 Thay đổi chính

### **Trước (OLD)**:
- Hiển thị danh sách ca dạng flat list
- Filter mặc định: `shift_date` = hôm nay
- Hiển thị cả staff status và machine status
- Phân công nhân sự riêng biệt khỏi máy

### **Sau (NEW)**:
- **Hiển thị theo cấu trúc Plan → Shifts**
- Mỗi kế hoạch hiển thị:
  - Tên kế hoạch
  - Số ca gợi ý (từ `plan_shift`)
  - Số ca thực tế (từ `production_shifts`)
  - Số ca đã hoàn thành
- Dưới mỗi kế hoạch là danh sách các ca
- Nếu chưa có ca → Nút "Tạo Ca Mới"
- **Bộ lọc mặc định**: KHÔNG áp dụng, hiển thị toàn bộ
- **Bỏ phần Staff Status** (vì giờ gán nhân sự trực tiếp vào máy)

---

## 🔧 Controller Changes (`application/controllers/leader/Shift.php`)

### Method: `index()`

**OLD Logic:**
```php
public function index()
{
    $filters = [
        'shift_date' => $this->input->get('shift_date') ?: date('Y-m-d'), // DEFAULT TODAY
    ];
    
    $shifts = $this->shiftModel->getShifts($filters);
    
    $data = [
        'shifts' => $shifts,
        'filters' => $filters
    ];
}
```

**NEW Logic:**
```php
public function index()
{
    $filters = [
        'shift_date' => $this->input->get('shift_date'), // NO DEFAULT
        'plan_id' => $this->input->get('plan_id')
    ];
    
    // 1. Get production plans with shift counts
    $this->db->select('
        planning.id_plan, 
        planning.plan_name, 
        planning.pl_status,
        COUNT(DISTINCT plan_shift.id_shift) as suggested_shift_count,
        COUNT(DISTINCT ps.shift_id) as actual_shift_count,
        SUM(CASE WHEN ps.shift_status = 3 THEN 1 ELSE 0 END) as completed_shift_count
    ');
    $this->db->from('planning');
    $this->db->join('plan_shift', 'plan_shift.id_plan = planning.id_plan', 'left');
    $this->db->join('production_shifts ps', 'ps.plan_id = planning.id_plan', 'left');
    $this->db->group_by('planning.id_plan');
    $plans = $this->db->get()->result();
    
    // 2. Get shifts for each plan
    foreach ($plans as $plan) {
        $this->db->select('production_shifts.*, 
            pl.line_code, pl.line_name,
            z.zone_code, z.zone_name,
            COUNT(DISTINCT sms.staff_id) as assigned_staff_count');
        $this->db->from('production_shifts');
        $this->db->join('shift_machine_staff sms', 'sms.shift_id = production_shifts.shift_id AND sms.status = 1', 'left');
        $this->db->where('production_shifts.plan_id', $plan->id_plan);
        
        // Apply filters
        if (!empty($filters['line_id'])) {
            $this->db->where('production_shifts.line_id', $filters['line_id']);
        }
        // ... other filters
        
        $plan->shifts = $this->db->get()->result();
    }
    
    $data = [
        'plans' => $plans,
        'all_plans' => $all_plans, // For filter dropdown
        'filters' => $filters
    ];
}
```

**Key Changes:**
1. Query `planning` table first
2. Count shifts from both `plan_shift` (suggested) and `production_shifts` (actual)
3. For each plan, query its shifts
4. Join `shift_machine_staff` instead of `shift_staff_assignments`
5. NO default date filter

---

## 🎨 View Changes (`application/views/leader/shift/dashboard.php`)

### 1. Statistics Cards

**OLD:**
```php
<div class="col-xl-3">
    <p>Tổng Số Ca</p>
    <h4><?= count($shifts) ?></h4>
</div>
<div class="col-xl-3">
    <p>Thiếu Nhân Sự</p>
    <h4><?= count(array_filter($shifts, fn($s) => $s->staff_status == 'insufficient')) ?></h4>
</div>
```

**NEW:**
```php
<?php 
$total_plans = count($plans);
$total_shifts = array_sum(array_map(function($p) { return count($p->shifts); }, $plans));
$total_completed = array_sum(array_map(function($p) { return $p->completed_shift_count; }, $plans));
?>

<div class="col-xl-3">
    <p>Kế Hoạch</p>
    <h4><?= $total_plans ?></h4>
</div>
<div class="col-xl-3">
    <p>Tổng Số Ca</p>
    <h4><?= $total_shifts ?></h4>
</div>
<div class="col-xl-3">
    <p>Hoàn Thành</p>
    <h4><?= $total_completed ?></h4>
</div>
```

**Removed:** "Thiếu Nhân Sự" card (không còn staff_status)

---

### 2. Filter Form

**OLD:**
```php
<div class="col-md-3">
    <label>Dây chuyền</label>
    <select name="line_id">...</select>
</div>
<div class="col-md-2">
    <label>Ngày</label>
    <input type="date" name="shift_date" value="<?= $filters['shift_date'] ?>">
</div>
<div class="col-md-2">
    <label>Từ ngày</label>
    ...
</div>
```

**NEW:**
```php
<div class="col-md-3">
    <label>Kế hoạch</label>
    <select name="plan_id">
        <option value="">Tất cả</option>
        <?php foreach ($all_plans as $plan): ?>
            <option value="<?= $plan->id_plan ?>"><?= $plan->plan_name ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="col-md-3">
    <label>Dây chuyền</label>
    <select name="line_id">...</select>
</div>
<div class="col-md-2">
    <label>Từ ngày</label>
    <input type="date" name="date_from" value="<?= $filters['date_from'] ?>">
</div>
<div class="col-md-2">
    <label>Đến ngày</label>
    <input type="date" name="date_to" value="<?= $filters['date_to'] ?>">
</div>
```

**Key Changes:**
1. Added "Kế hoạch" filter at first position
2. Removed single "Ngày" field
3. Keep only "Từ ngày" / "Đến ngày" range
4. No default values

---

### 3. Main Display Structure

**OLD:**
```php
<!-- Flat list of shift cards -->
<?php if (empty($shifts)): ?>
    <div>Không có ca nào</div>
<?php else: ?>
    <?php foreach ($shifts as $shift): ?>
        <div class="card">
            <!-- Shift details -->
            <span class="badge"><?= $staff_text ?></span>
            <span class="badge"><?= $machine_text ?></span>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
```

**NEW:**
```php
<!-- Grouped by Plan -->
<?php if (empty($plans)): ?>
    <div>Chưa có kế hoạch sản xuất nào</div>
<?php else: ?>
    <?php foreach ($plans as $plan): ?>
        <!-- Plan Container -->
        <div class="card border-2 border-primary">
            <!-- Plan Header -->
            <div class="card-header bg-gradient-primary">
                <h5><?= $plan->plan_name ?></h5>
                <div class="row">
                    <div class="col-4">
                        <p>Ca gợi ý</p>
                        <h6><?= $plan->suggested_shift_count ?></h6>
                    </div>
                    <div class="col-4">
                        <p>Ca thực tế</p>
                        <h6><?= $plan->actual_shift_count ?></h6>
                    </div>
                    <div class="col-4">
                        <p>Hoàn thành</p>
                        <h6><?= $plan->completed_shift_count ?></h6>
                    </div>
                </div>
            </div>

            <!-- Shifts List under this Plan -->
            <div class="card-body">
                <?php if (empty($plan->shifts)): ?>
                    <div class="text-center">
                        <p>Chưa có ca làm việc cho kế hoạch này</p>
                        <a href="<?= site_url('leader/shift/create?plan_id=' . $plan->id_plan); ?>" 
                           class="btn btn-primary">
                            <i class="material-icons">add</i> Tạo Ca Mới
                        </a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($plan->shifts as $shift): ?>
                            <div class="col-xl-4 col-md-6 mb-3">
                                <div class="card">
                                    <!-- Shift details -->
                                    <span class="badge">
                                        <i class="material-icons">people</i> 
                                        <?= $shift->assigned_staff_count ?? 0 ?> Người
                                    </span>
                                    <!-- NO machine status badge -->
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
```

**Key Changes:**
1. **Outer loop**: Plans
2. **Inner loop**: Shifts under each plan
3. **Plan Header**: Shows counts (suggested, actual, completed)
4. **Empty state**: Shows "Tạo Ca Mới" button with plan_id
5. **Staff count**: Only shows total assigned staff (from `shift_machine_staff`)
6. **Removed**: Machine status badge, staff status badge

---

## 📊 Database Relationships

```
planning (kế hoạch sản xuất)
├── id_plan (PK)
├── plan_name
├── qty_target
├── end_date
└── pl_status

plan_shift (ca gợi ý - từ kế hoạch cũ)
├── id_planshift (PK)
├── id_plan (FK → planning.id_plan)
├── id_shift (FK → shiftment.id_shift)
├── id_staff
└── start_date

production_shifts (ca thực tế - mới)
├── shift_id (PK)
├── plan_id (FK → planning.id_plan) [NEW]
├── line_id (FK → production_lines.id)
├── shift_date
├── start_time
├── end_time
└── shift_status

shift_machine_staff (phân công nhân sự vào máy)
├── id (PK)
├── shift_id (FK → production_shifts.shift_id)
├── machine_id (FK → machines.id)
├── staff_id (FK → user.user_id)
└── status
```

---

## 🎯 UI Flow

### **Before:**
```
[Filter: Date=Today] → [List of Shifts (flat)]
```

### **After:**
```
[Filter: Plan/Line/Date Range (no default)] → [Plans grouped view]

Plan A (2 suggested / 3 actual / 1 completed)
├── Shift 1 (5 người)
├── Shift 2 (8 người)
└── Shift 3 (6 người)

Plan B (4 suggested / 0 actual / 0 completed)
└── [Nút Tạo Ca Mới]
```

---

## ✅ Checklist

- [x] Update controller `index()` - Query plans → shifts
- [x] Remove default date filter
- [x] Add plan_id filter
- [x] Join `shift_machine_staff` instead of `shift_staff_assignments`
- [x] Update statistics cards (Kế hoạch, Tổng ca, Hoàn thành)
- [x] Add plan filter dropdown
- [x] Update main display to group by plan
- [x] Show plan header with counts (suggested, actual, completed)
- [x] Show "Tạo Ca Mới" button when no shifts
- [x] Remove machine status badge
- [x] Show only staff count from `shift_machine_staff`
- [ ] Test with existing data
- [ ] Update create shift form to include plan_id

---

## 🚀 Next Steps

1. **Test Dashboard**: 
   ```sql
   -- Check if plans have shifts
   SELECT 
       p.plan_name,
       COUNT(DISTINCT ps.id_shift) as suggested,
       COUNT(DISTINCT prds.shift_id) as actual
   FROM planning p
   LEFT JOIN plan_shift ps ON ps.id_plan = p.id_plan
   LEFT JOIN production_shifts prds ON prds.plan_id = p.id_plan
   GROUP BY p.id_plan;
   ```

2. **Update Create Shift Form**: 
   - Add `plan_id` field
   - Pre-select plan if coming from dashboard "Tạo Ca Mới" button
   - Remove staff assignment section (handled in detail page → machine tab)

3. **Update production_shifts table**:
   ```sql
   -- Add plan_id column if not exists
   ALTER TABLE production_shifts ADD plan_id INT(11) DEFAULT NULL AFTER line_id;
   ALTER TABLE production_shifts ADD CONSTRAINT fk_shift_plan 
       FOREIGN KEY (plan_id) REFERENCES planning(id_plan) ON DELETE SET NULL;
   ```

---

**Updated by:** GitHub Copilot  
**Date:** December 18, 2025  
**Status:** ✅ Controller & View Updated - Ready for testing
