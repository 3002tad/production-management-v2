# 📁 Database Migrations - RBAC System

## 🎯 Tổng quan

Thư mục này chứa các file SQL migration để triển khai hệ thống **RBAC (Role-Based Access Control)** cho Production Management System.

## 📋 Danh sách Migrations

| # | File | Mô tả | Status |
|---|------|-------|--------|
| 001 | `001_create_rbac_core_tables.sql` | Tạo bảng RBAC core (roles, modules, permissions, role_permissions, audit_log) và cập nhật bảng user | ⭐ Core |
| 002 | `002_seed_roles_data.sql` | Insert 7 vai trò chính + tạo sample users | ⭐ Core |
| 003 | `003_seed_modules_data.sql` | Insert 18 modules + 10 sub-modules (tổng 28) | ⭐ Core |
| 004 | `004_seed_permissions_data.sql` | Insert 174+ permissions cho tất cả modules | ⭐ Core |
| 005 | `005_map_role_permissions.sql` | Map permissions cho 7 roles theo nghiệp vụ | ⭐ Core |

## 🚀 Hướng dẫn Chạy Migrations

### **Option 1: Chạy từng file (Khuyến nghị cho Development)**

```bash
# Bước 1: Kết nối MySQL
mysql -u root -p

# Bước 2: Chạy từng migration theo thứ tự
source d:/Code/PTUD/production-management-v2/db/migrations/001_create_rbac_core_tables.sql
source d:/Code/PTUD/production-management-v2/db/migrations/002_seed_roles_data.sql
source d:/Code/PTUD/production-management-v2/db/migrations/003_seed_modules_data.sql
source d:/Code/PTUD/production-management-v2/db/migrations/004_seed_permissions_data.sql
source d:/Code/PTUD/production-management-v2/db/migrations/005_map_role_permissions.sql
```

### **Option 2: Chạy qua phpMyAdmin**

1. Mở phpMyAdmin: `http://localhost/phpmyadmin`
2. Chọn database: `db_production`
3. Click tab **SQL**
4. Copy-paste nội dung từng file migration theo thứ tự 001 → 005
5. Click **Go**

### **Option 3: Import toàn bộ qua terminal (Windows)**

```powershell
# PowerShell - chạy tất cả migrations
cd d:\Code\PTUD\production-management-v2\db\migrations

# Kết nối và chạy từng file
Get-ChildItem -Filter "*.sql" | Sort-Object Name | ForEach-Object {
    Write-Host "Running migration: $($_.Name)" -ForegroundColor Green
    mysql -u root -p db_production < $_.FullName
}
```

### **Option 4: MySQL Workbench**

1. Mở MySQL Workbench
2. Connect to database
3. File → Run SQL Script
4. Chọn từng file migration theo thứ tự
5. Execute

## ✅ Verification (Kiểm tra sau khi chạy)

### **1. Kiểm tra bảng đã tạo:**

```sql
USE db_production;

-- Xem tất cả bảng mới
SHOW TABLES LIKE '%role%';
SHOW TABLES LIKE '%permission%';
SHOW TABLES LIKE 'modules';
SHOW TABLES LIKE 'audit_log';

-- Kết quả mong đợi:
-- ✓ roles
-- ✓ modules
-- ✓ permissions
-- ✓ role_permissions
-- ✓ audit_log
-- ✓ bảng user đã được cập nhật thêm cột
```

### **2. Kiểm tra dữ liệu:**

```sql
-- Kiểm tra 7 roles
SELECT role_id, role_name, role_display_name, level FROM roles ORDER BY level DESC;
-- Kết quả: 7 roles (BOD, Line Manager, Warehouse, Admin, QC, Technical, Worker)

-- Kiểm tra modules
SELECT COUNT(*) AS total_modules FROM modules;
-- Kết quả: 28 modules

-- Kiểm tra permissions
SELECT COUNT(*) AS total_permissions FROM permissions;
-- Kết quả: 174+ permissions

-- Kiểm tra mapping role-permissions
SELECT 
  r.role_name,
  COUNT(rp.permission_id) AS total_permissions
FROM roles r
LEFT JOIN role_permissions rp ON r.role_id = rp.role_id
GROUP BY r.role_id, r.role_name
ORDER BY total_permissions DESC;
-- Kết quả: Mỗi role có số permissions khác nhau
```

### **3. Kiểm tra sample users:**

```sql
SELECT 
  user_id, 
  username, 
  full_name,
  r.role_display_name,
  is_active
FROM user u
LEFT JOIN roles r ON u.role_id = r.role_id
ORDER BY u.user_id;

-- Kết quả mong đợi:
-- ✓ admin → System Admin
-- ✓ leader → Line Manager
-- ✓ bod → Ban Giám Đốc
-- ✓ warehouse → Warehouse Staff
-- ✓ qc → QC Staff
-- ✓ technical → Technical Staff
-- ✓ worker → Worker
```

## 🔐 Sample Login Credentials (Testing)

| Username | Password | Role | Full Name |
|----------|----------|------|-----------|
| `bod` | `bod123` | Ban Giám Đốc | Nguyễn Văn A - Giám Đốc |
| `admin` | `admin` | Quản trị viên | Administrator |
| `leader` | `leader` | Trưởng dây chuyền | Trưởng dây chuyền |
| `line_manager` | `line123` | Trưởng dây chuyền | Trần Văn B - Trưởng line 2 |
| `warehouse` | `wh123` | Nhân viên Kho | Lê Thị C - Nhân viên kho |
| `qc` | `qc123` | Nhân viên QC | Phạm Văn D - Nhân viên QC |
| `technical` | `tech123` | Nhân viên Kỹ thuật | Hoàng Văn E - Kỹ thuật viên |
| `worker` | `worker123` | Công nhân | Nguyễn Thị F - Công nhân |

> ⚠️ **Lưu ý:** Đổi password ngay sau khi login lần đầu trong production!

## 📊 Database Schema Overview

```
┌─────────────────────────────────────────────────────────┐
│                    RBAC SCHEMA                          │
└─────────────────────────────────────────────────────────┘

roles (7 roles)
  ├── role_id (PK)
  ├── role_name (UNIQUE)
  ├── role_display_name
  ├── level (100, 90, 70, 60, 50, 10)
  └── is_active

modules (28 modules)
  ├── module_id (PK)
  ├── module_name (UNIQUE)
  ├── module_display_name
  ├── parent_id (FK → modules) [cho sub-modules]
  ├── route
  └── sort_order

permissions (174+ permissions)
  ├── permission_id (PK)
  ├── module_id (FK → modules)
  ├── permission_name (UNIQUE) [format: module.action]
  ├── permission_display_name
  └── action (view, create, edit, delete, approve, etc.)

role_permissions (Many-to-Many)
  ├── id (PK)
  ├── role_id (FK → roles)
  └── permission_id (FK → permissions)
  └── UNIQUE(role_id, permission_id)

user (updated)
  ├── user_id (PK)
  ├── username
  ├── password
  ├── role_id (FK → roles) ← NEW
  ├── staff_id (FK → staff) ← NEW
  ├── full_name ← NEW
  ├── email ← NEW
  ├── phone ← NEW
  ├── is_active ← NEW
  ├── last_login ← NEW
  ├── created_by ← NEW
  ├── created_at ← NEW
  └── updated_at ← NEW

audit_log (tracking all activities)
  ├── log_id (PK)
  ├── user_id (FK → user)
  ├── action (login, logout, create, update, delete, approve)
  ├── module (customer, product, order, etc.)
  ├── record_id
  ├── old_value (JSON)
  ├── new_value (JSON)
  ├── ip_address
  └── created_at
```

## 🎯 Permissions Distribution (Dự kiến)

| Role | Total Permissions | Access Level |
|------|-------------------|--------------|
| **Ban Giám Đốc** | ~70+ | Strategic: Customer, Product, Order, Planning Approval, All Reports |
| **Trưởng dây chuyền** | ~80+ | Operational: Staff, Machine, Planning, Shift, Production, Incident, Closing |
| **Nhân viên Kho** | ~40+ | Inventory: Material, Warehouse (Receipt/Issue), Stock Reports |
| **Quản trị viên** | ~20+ | System: User Management, Role Management, Audit Log, Settings |
| **Nhân viên QC** | ~25+ | Quality: QC Inspection, Defect, Approve TP Receipt, Quality Reports |
| **Nhân viên Kỹ thuật** | ~20+ | Maintenance: Incident Handling, Machine Status, Maintenance, BOM Approve |
| **Công nhân** | ~10 | Minimal: View Own Schedule, Confirm Task, Report Incident, View Own Production |

## 🔧 Troubleshooting

### **Lỗi: "Table already exists"**

```sql
-- Drop tables nếu cần reset (CẨN THẬN - MẤT DỮ LIỆU!)
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS modules;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS audit_log;

-- Sau đó chạy lại migrations
```

### **Lỗi: "Foreign key constraint fails"**

- Chạy migrations đúng thứ tự: 001 → 002 → 003 → 004 → 005
- Kiểm tra database đã tồn tại: `USE db_production;`

### **Lỗi: "Duplicate entry"**

- Migrations đã chạy rồi, bỏ qua hoặc dùng `ON DUPLICATE KEY UPDATE`
- Hoặc xóa dữ liệu cũ trước khi chạy lại

## 📝 Next Steps (PHASE 2)

Sau khi hoàn thành PHASE 1, tiếp tục:

1. ✅ PHASE 1: Database & Core RBAC (DONE)
2. ⏭️ **PHASE 2: Backend Core**
   - Tạo `AuthModel.php`
   - Tạo `Auth.php` library
   - Tạo `auth_helper.php`
   - Tạo `MY_Controller.php`
   - Update `Login.php` controller

3. ⏭️ **PHASE 3: Update Controllers**
   - Thêm `require_permission()` vào từng controller

4. ⏭️ **PHASE 4: Update Views**
   - Thêm `can()` checks vào views
   - Ẩn/hiện buttons theo permissions

## 📚 Documentation

- [ERD Diagram](../db_production_erd.md) - Database structure
- [DEPLOY_GITHUB.md](../../DEPLOY_GITHUB.md) - Git workflow
- [README.md](../../README.md) - Project overview

## 🤝 Contributors

- Production Management Team
- Date: November 1, 2025

---

**Status:** ✅ PHASE 1 COMPLETED - Ready for Backend Development
