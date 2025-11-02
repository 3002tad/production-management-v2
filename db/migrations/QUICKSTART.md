# 🚀 QUICK START - RBAC Migration

## ⚡ Chạy nhanh (3 phút)

### **Cách 1: Chạy All-in-One + Permissions (Khuyến nghị)**

```bash
# Mở MySQL/phpMyAdmin và chạy theo thứ tự:

# Step 1: Chạy core structure (1 phút)
source d:/Code/PTUD/production-management-v2/db/migrations/000_all_in_one_migration.sql

# Step 2: Chạy full permissions (1 phút)
source d:/Code/PTUD/production-management-v2/db/migrations/004_seed_permissions_data.sql

# Step 3: Map permissions cho roles (1 phút)
source d:/Code/PTUD/production-management-v2/db/migrations/005_map_role_permissions.sql
```

### **Cách 2: Chạy từng file đầy đủ**

```bash
source d:/Code/PTUD/production-management-v2/db/migrations/001_create_rbac_core_tables.sql
source d:/Code/PTUD/production-management-v2/db/migrations/002_seed_roles_data.sql
source d:/Code/PTUD/production-management-v2/db/migrations/003_seed_modules_data.sql
source d:/Code/PTUD/production-management-v2/db/migrations/004_seed_permissions_data.sql
source d:/Code/PTUD/production-management-v2/db/migrations/005_map_role_permissions.sql

# 🔥 OPTIONAL - Migrate HOÀN TOÀN sang RBAC (Breaking Change!)
# ⚠️ CHỈ CHẠY nếu đã sẵn sàng update code (LoginModel, Controllers, Views)
# source d:/Code/PTUD/production-management-v2/db/migrations/006_migrate_to_full_rbac.sql
```

---

## 🔥 Migration 006 - XÓA cột `role` cũ

### ⚠️ **CẢNH BÁO: BREAKING CHANGE!**

Migration 006 sẽ **XÓA HOÀN TOÀN** cột `role` (enum 'admin','leader') và chuyển sang `role_id` (INT NOT NULL).

**CHỈ CHẠY KHI:**
- ✅ Đã backup database
- ✅ Đã chuẩn bị update LoginModel.php
- ✅ Đã chuẩn bị update Controllers (Admin.php, Leader.php)
- ✅ Có thời gian fix code ngay sau đó (2-3 giờ)

**NẾU CHƯA SẴN SÀNG:** Bỏ qua migration 006, hệ thống vẫn hoạt động với cả 2 cột (`role` + `role_id`).

```bash
# Khi đã sẵn sàng:
source d:/Code/PTUD/production-management-v2/db/migrations/006_migrate_to_full_rbac.sql
```

---

## ✅ Kiểm tra nhanh

```sql
-- Xem roles
SELECT * FROM roles;

-- Xem users mới
SELECT username, full_name, r.role_display_name 
FROM user u 
LEFT JOIN roles r ON u.role_id = r.role_id;

-- Đếm permissions
SELECT COUNT(*) FROM permissions;

-- Xem permissions của BOD
SELECT 
  r.role_display_name,
  COUNT(rp.permission_id) AS total_permissions
FROM roles r
LEFT JOIN role_permissions rp ON r.role_id = rp.role_id
WHERE r.role_name = 'bod'
GROUP BY r.role_id;
```

## 🔑 Test Login

| Username | Password | Role |
|----------|----------|------|
| `bod` | `bod123` | Ban Giám Đốc |
| `admin` | `admin` | Quản trị viên |
| `leader` | `leader` | Trưởng dây chuyền |
| `warehouse` | `wh123` | Nhân viên Kho |
| `qc` | `qc123` | Nhân viên QC |
| `technical` | `tech123` | Kỹ thuật viên |
| `worker` | `worker123` | Công nhân |

## 📋 Kết quả mong đợi

```
✅ 7 roles
✅ 28 modules
✅ 174+ permissions
✅ 7 sample users
✅ Permissions đã được map cho từng role
```

## 🆘 Lỗi?

Xem file `README.md` trong thư mục này để troubleshooting chi tiết.

---

**Next:** Chuyển sang PHASE 2 - Backend Development (AuthModel, Auth library, MY_Controller)
