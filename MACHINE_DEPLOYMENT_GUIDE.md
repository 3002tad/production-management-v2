# MACHINE MANAGEMENT MODULE - DEPLOYMENT GUIDE

## 📋 Tổng quan Module

**Module Quản lý Máy/Dây chuyền** cho phép Trưởng dây chuyền quản lý thông tin máy móc sản xuất, lập lịch bảo trì và theo dõi trạng thái hoạt động.

### 🎯 Chức năng chính:
- ✅ **CRUD máy/dây chuyền**: Thêm, sửa, xóa, xem danh sách
- ✅ **Quản lý trạng thái**: Active, Maintenance, Inactive, Broken với status logging
- ✅ **Lịch bảo trì**: Tạo, theo dõi lịch bảo trì với conflict checking
- ✅ **Tích hợp RBAC**: Phân quyền theo role (Trưởng dây chuyền, Ban giám đốc)
- ✅ **API Support**: Endpoints cho integration với module khác

---

## 🚀 DEPLOYMENT CHECKLIST

### Step 1: Database Migration

```sql
-- Chạy migration tạo bảng
SOURCE db/migrations/008_create_machine_management_tables.sql;
```

**Verification:**
```sql
-- Kiểm tra bảng đã tạo
SHOW TABLES LIKE '%machine%';
-- Expected: machines, machine_status_logs, machine_maintenances

-- Kiểm tra dữ liệu mẫu
SELECT COUNT(*) FROM machines; -- Expected: 6 machines
SELECT COUNT(*) FROM machine_status_logs; -- Expected: 6 logs
SELECT COUNT(*) FROM machine_maintenances; -- Expected: 2 schedules
```

### Step 2: Upload Code Files

**✅ Controller:**
- `application/controllers/Machine.php`

**✅ Model:**
- `application/models/MachineModel.php`

**✅ Views:**
- `application/views/leader/machine/index.php` (Danh sách)
- `application/views/leader/machine/create.php` (Thêm mới)
- `application/views/leader/machine/detail.php` (Chi tiết)
- `application/views/leader/machine/edit.php` (Chỉnh sửa)
- `application/views/leader/machine/maintenance.php` (Bảo trì)

**✅ Routes:**
- Updated `application/config/routes.php` with machine routes

### Step 3: Permissions Setup

**Yêu cầu permissions trong RBAC:**
```sql
-- Kiểm tra permissions (nếu đã có RBAC system)
SELECT * FROM permissions WHERE module = 'machine_management';
```

**Required Permissions:**
- `manage_machine_line` - Trưởng dây chuyền (CRUD)
- `view_reports` - Ban giám đốc (Read Only)

### Step 4: Verification Tests

**🧪 Test Cases:**

1. **Access Control:**
   ```
   ✅ Trưởng dây chuyền: Full CRUD access
   ✅ Ban giám đốc: Read-only access
   ❌ Other roles: No access (redirect)
   ```

2. **CRUD Operations:**
   ```
   ✅ Create machine với validation
   ✅ List với filter và pagination
   ✅ Edit với status change logging
   ✅ View detail với history
   ```

3. **Maintenance Scheduling:**
   ```
   ✅ Create maintenance với conflict checking
   ✅ List maintenances với filter
   ✅ Status tracking
   ```

4. **API Endpoints:**
   ```bash
   # Test available machines API
   GET /machine/api/available?stage_type=molding&start_time=2025-12-01T08:00&end_time=2025-12-01T18:00
   
   # Test statistics API
   GET /machine/api/statistics
   ```

---

## 🔧 CONFIGURATION

### Database Tables Structure

**1. `machines` - Thông tin máy chính**
```sql
-- Key fields: id, code (unique), name, capacity, stage_type, status
-- Indexes: code, status, stage_type, created_at
```

**2. `machine_status_logs` - Lịch sử thay đổi trạng thái**
```sql
-- Key fields: machine_id, old_status, new_status, changed_by, changed_at
-- Foreign Key: machine_id → machines.id
```

**3. `machine_maintenances` - Lịch bảo trì**
```sql
-- Key fields: machine_id, start_time, end_time, status, maintenance_type
-- Foreign Key: machine_id → machines.id
-- Indexes: machine_id, start_time, end_time, status
```

### URL Routes

```
/machine                     → List machines (index)
/machine/create             → Create form
/machine/detail/{id}        → View detail
/machine/edit/{id}          → Edit form
/machine/maintenance/{id}   → Maintenance management
/machine/api/available      → API: Get available machines
/machine/api/statistics     → API: Get statistics
```

---

## 🎨 UI/UX Features

### Design System Applied:
- ✅ **Material Design 3.0** với gradient color system
- ✅ **Responsive Bootstrap** grid layout
- ✅ **Status color coding** cho visual feedback
- ✅ **Interactive hover effects** và smooth transitions
- ✅ **Consistent navigation** với breadcrumbs
- ✅ **Flash messages** với auto-hide
- ✅ **Modal forms** cho maintenance scheduling

### Color Coding:
```css
Active: #43A047 → #66BB6A (Green)
Maintenance: #FFA726 → #FB8C00 (Orange)  
Inactive: #78909C → #90A4AE (Gray)
Broken: #E53935 → #EF5350 (Red)
```

---

## 📊 SAMPLE DATA

Module đã được seed với 6 máy mẫu:

| Code  | Name | Stage Type | Status | Capacity |
|-------|------|------------|--------|----------|
| ML001 | Máy ép nhựa số 1 | molding | active | 1000 |
| ML002 | Máy ép nhựa số 2 | molding | active | 1200 |
| AS001 | Dây chuyền lắp ráp 1 | assembly | active | 800 |
| AS002 | Dây chuyền lắp ráp 2 | assembly | maintenance | 750 |
| PK001 | Máy đóng gói tự động | packaging | active | 2000 |
| QC001 | Máy kiểm tra chất lượng | quality_check | active | 500 |

---

## 🔗 INTEGRATION

### Tích hợp với Module khác:

**1. Production Shift Assignment:**
```php
// Lấy máy khả dụng cho ca sản xuất
$available_machines = $this->MachineModel->getAvailableMachines(
    'molding',                    // stage_type
    '2025-12-01 08:00:00',       // start_time
    '2025-12-01 18:00:00'        // end_time
);
```

**2. Planning Module:**
```php
// Check machine capacity cho planning
$machine = $this->MachineModel->getMachineById($machine_id);
$hourly_capacity = $machine->capacity; // pieces/hour
```

---

## 🆘 TROUBLESHOOTING

### Common Issues:

**1. Migration Fails:**
```sql
-- Check if tables already exist
SHOW TABLES LIKE '%machine%';

-- Drop if needed (WARNING: Will lose data!)
DROP TABLE IF EXISTS machine_maintenances;
DROP TABLE IF EXISTS machine_status_logs; 
DROP TABLE IF EXISTS machines;
```

**2. Permission Denied:**
```php
// Check user role and level
$role_name = $this->session->userdata('role_name');
$level = $this->session->userdata('level');
// Required: level >= 50 for edit access
```

**3. Route Not Working:**
```php
// Verify routes.php updated
// Check .htaccess for URL rewriting
```

**4. API Endpoints 404:**
```php
// Ensure routes.php has API routes
$route['machine/api/available'] = 'machine/getAvailableMachines';
$route['machine/api/statistics'] = 'machine/getStatistics';
```

---

## 📚 NEXT STEPS

### Phase 2 Features (Future):
- [ ] **Maintenance Cost Tracking** - Chi phí bảo trì chi tiết
- [ ] **Preventive Maintenance Automation** - Tự động tạo lịch bảo trì định kỳ
- [ ] **Machine Performance Analytics** - Báo cáo hiệu suất máy
- [ ] **Mobile App Integration** - App mobile cho technician
- [ ] **IoT Sensor Integration** - Kết nối cảm biến thời gian thực

### Integration Roadmap:
- [ ] **Production Planning** - Tự động assign máy cho kế hoạch
- [ ] **Inventory Management** - Theo dõi phụ tùng thay thế
- [ ] **Quality Control** - Liên kết QC với machine status

---

## 👥 SUPPORT

**Developed by:** Copilot AI  
**Date:** November 27, 2025  
**Version:** 1.0  
**Framework:** CodeIgniter 3 + Material Design  
**Database:** MySQL/MariaDB  

**Contact:** Technical team for issues or enhancements

---

🎯 **Module Machine Management đã sẵn sàng production!**