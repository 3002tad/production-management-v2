# 🗄️ Database Documentation

## � SETUP LẦN ĐẦU - Thứ tự Chạy

### **Bước 1: Tạo Database**
```bash
mysql -u root -p
CREATE DATABASE db_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

### **Bước 2: Import Database Gốc**
```bash
mysql -u root -p db_production < db_production.sql
```

### **Bước 3: Chạy Archived Migrations (theo thứ tự)**
```bash
mysql -u root -p db_production < archives/migration_ballpen_units.sql
mysql -u root -p db_production < archives/migration_add_diameter_to_product.sql
mysql -u root -p db_production < archives/migration_optional_diameter_decimal.sql
mysql -u root -p db_production < archives/add_foreign_keys.sql
mysql -u root -p db_production < archives/fix_vietnamese_charset.sql
```

### **Bước 4: (Optional) Setup RBAC System**
```bash
mysql -u root -p db_production < migrations/000_all_in_one_migration.sql
mysql -u root -p db_production < migrations/004_seed_permissions_data.sql
mysql -u root -p db_production < migrations/005_map_role_permissions.sql
```

### **Bước 5: Verify**
```sql
mysql -u root -p db_production
SHOW TABLES;
SELECT * FROM user;
-- Nếu có RBAC: SELECT * FROM roles;
```

---

## 📁 Cấu trúc Thư mục

```
db/
├── 📄 db_production.sql              # ⭐ Database gốc - IMPORT FILE NÀY TRƯỚC
│
├── 📂 migrations/                    # ⭐ RBAC System Migrations (PHASE 1)
│   ├── 000_all_in_one_migration.sql # Quick start - Chạy nhanh
│   ├── 001_create_rbac_core_tables.sql
│   ├── 002_seed_roles_data.sql
│   ├── 003_seed_modules_data.sql
│   ├── 004_seed_permissions_data.sql
│   ├── 005_map_role_permissions.sql
│   ├── QUICKSTART.md                # Hướng dẫn chạy nhanh 3 phút
│   └── README.md                    # Hướng dẫn chi tiết migrations
│
├── 📂 archives/                      # Old migrations (đã áp dụng)
│   ├── migration_ballpen_units.sql
│   ├── migration_add_diameter_to_product.sql
│   ├── migration_optional_diameter_decimal.sql
│   ├── add_foreign_keys.sql
│   ├── fix_add_diameter_column.sql
│   └── fix_vietnamese_charset.sql
│
├── 📂 docs/                          # Documentation & Changelogs
│   ├── DATABASE_RELATIONSHIPS.md    # ERD & Foreign Keys
│   ├── MIGRATION_README.md          # Old migration guide
│   ├── CHANGELOG_DIAMETER.md        # Diameter feature changelog
│   ├── UPDATE_SUMMARY_DECIMAL_AUTOFILL.md
│   └── FIX_FONT_GUIDE.md            # Font encoding fix guide
│
└── 📂 backups/                       # Database backups (empty - tạo khi cần)
```

---

## 🎯 Mục đích Từng Thư mục

### 📂 **Root Files**

#### `db_production.sql` ⭐
- **Mô tả:** Database schema gốc - FILE CHÍNH ĐỂ IMPORT
- **Dung lượng:** ~15 KB
- **Nội dung:** 14 tables cơ bản (customer, product, project, planning, production, machine, material, staff, shiftment, finished, sorting, user)
- **Trạng thái:** ✅ Current - Dùng file này để setup
- **Khuyến nghị:** Import file này TRƯỚC, sau đó chạy migrations trong `archives/` theo thứ tự

---

### 📂 **migrations/** ⭐ QUAN TRỌNG

**Mục đích:** RBAC (Role-Based Access Control) System - PHASE 1

#### **Quick Start Files:**
- `QUICKSTART.md` - Hướng dẫn chạy nhanh 3 phút
- `000_all_in_one_migration.sql` - Chạy core structure nhanh

#### **Detailed Migrations:**
- `001_create_rbac_core_tables.sql` - Tạo 5 bảng RBAC + cập nhật user table
- `002_seed_roles_data.sql` - 7 roles: BOD, Line Manager, Warehouse, Admin, QC, Technical, Worker
- `003_seed_modules_data.sql` - 28 modules (18 main + 10 sub)
- `004_seed_permissions_data.sql` - 174 permissions
- `005_map_role_permissions.sql` - Map permissions cho từng role

#### **Khi nào dùng:**
- ✅ Triển khai hệ thống phân quyền đa vai trò
- ✅ Sau khi import `db_production.sql` và chạy migrations trong `archives/`
- ✅ Follow roadmap PHASE 1 → PHASE 2 → PHASE 3

#### **Cách dùng:**
```sql
-- Option 1: Quick (3 phút)
source migrations/000_all_in_one_migration.sql
source migrations/004_seed_permissions_data.sql
source migrations/005_map_role_permissions.sql

-- Option 2: Full (5 phút)
source migrations/001_create_rbac_core_tables.sql
source migrations/002_seed_roles_data.sql
source migrations/003_seed_modules_data.sql
source migrations/004_seed_permissions_data.sql
source migrations/005_map_role_permissions.sql
```

**Xem chi tiết:** [migrations/README.md](migrations/README.md)

---

### 📂 **archives/**

**Mục đích:** Lưu trữ các migrations cũ đã được áp dụng vào database

#### **Danh sách Archived Migrations:**

| File | Đã áp dụng | Mô tả |
|------|------------|-------|
| `migration_ballpen_units.sql` | ✅ | Chuyển đổi đơn vị: Kg → pieces/gram |
| `migration_add_diameter_to_product.sql` | ✅ | Thêm cột diameter vào product table |
| `migration_optional_diameter_decimal.sql` | ✅ | Đổi diameter sang DECIMAL(3,1) |
| `add_foreign_keys.sql` | ✅ | Tạo 12 Foreign Key constraints |
| `fix_add_diameter_column.sql` | ✅ | Fix lỗi thêm diameter column |
| `fix_vietnamese_charset.sql` | ✅ | Fix UTF-8 encoding cho tiếng Việt |

#### **Khi nào dùng:**
- ✅ PHẢI chạy sau khi import `db_production.sql`
- ✅ Chạy theo thứ tự để có database đầy đủ
- ✅ Tham khảo khi cần hiểu lịch sử thay đổi
- ✅ Debug nếu có vấn đề với features cũ

#### **Lưu ý:**
- ⚠️ Phải chạy THEO THỨ TỰ: ballpen_units → diameter → optional_diameter → foreign_keys → charset
- Giữ lại để tham khảo và version history
- Không chạy lại nếu đã chạy rồi (check xem tables đã có features chưa)

---

### 📂 **docs/**

**Mục đích:** Tài liệu kỹ thuật, changelogs, hướng dẫn

#### **Danh sách Documents:**

| File | Nội dung | Dùng khi nào |
|------|----------|--------------|
| `DATABASE_RELATIONSHIPS.md` | ERD diagram, 12 Foreign Keys | Hiểu cấu trúc database, relationships |
| `MIGRATION_README.md` | Hướng dẫn migrations cũ | Tham khảo lịch sử migrations |
| `CHANGELOG_DIAMETER.md` | Changelog diameter feature | Hiểu feature diameter được thêm như thế nào |
| `UPDATE_SUMMARY_DECIMAL_AUTOFILL.md` | Tóm tắt updates DECIMAL & Auto-fill | Review thay đổi lớn |
| `FIX_FONT_GUIDE.md` | Fix encoding UTF-8 | Troubleshoot font issues |

#### **Khi nào đọc:**
- 🔍 Cần hiểu cấu trúc database
- 📖 Onboarding thành viên mới
- 🐛 Troubleshooting encoding/font issues
- 📝 Review lịch sử thay đổi

---

### 📂 **backups/**

**Mục đích:** Lưu trữ database backups

#### **Trạng thái:** Hiện đang trống

#### **Cách tạo backup:**

```bash
# Backup toàn bộ database
mysqldump -u root -p db_production > backups/db_production_backup_YYYYMMDD.sql

# Backup với timestamp
mysqldump -u root -p db_production > backups/db_production_$(date +%Y%m%d_%H%M%S).sql

# Backup chỉ structure (không có data)
mysqldump -u root -p --no-data db_production > backups/db_production_structure_only.sql

# Backup chỉ data (không có structure)
mysqldump -u root -p --no-create-info db_production > backups/db_production_data_only.sql
```

#### **Khi nào backup:**
- ⚠️ Trước khi chạy migrations mới
- ⚠️ Trước khi update production
- ⚠️ Trước khi thử nghiệm thay đổi lớn
- 📅 Backup định kỳ hàng tuần/tháng

#### **Restore backup:**

```bash
# Restore từ backup
mysql -u root -p db_production < backups/db_production_backup_YYYYMMDD.sql
```

---

## � Setup Scripts Tổng hợp

### **Script 1: Setup Database Hoàn chỉnh (PowerShell)**

```powershell
# Tạo database
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS db_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import database gốc
mysql -u root -p db_production < db_production.sql

# Chạy archived migrations theo thứ tự
mysql -u root -p db_production < archives/migration_ballpen_units.sql
mysql -u root -p db_production < archives/migration_add_diameter_to_product.sql
mysql -u root -p db_production < archives/migration_optional_diameter_decimal.sql
mysql -u root -p db_production < archives/add_foreign_keys.sql
mysql -u root -p db_production < archives/fix_vietnamese_charset.sql

# (Optional) Setup RBAC
mysql -u root -p db_production < migrations/000_all_in_one_migration.sql
mysql -u root -p db_production < migrations/004_seed_permissions_data.sql
mysql -u root -p db_production < migrations/005_map_role_permissions.sql

Write-Host "Database setup complete!" -ForegroundColor Green
```

### **Script 2: Setup RBAC Only (đã có database)**

```bash
# Chạy RBAC migrations
mysql -u root -p db_production < migrations/000_all_in_one_migration.sql
mysql -u root -p db_production < migrations/004_seed_permissions_data.sql
mysql -u root -p db_production < migrations/005_map_role_permissions.sql
```

---

## �🚀 Quick Start Guide (Scenarios)

### **Scenario 1: Setup Database Lần Đầu - ĐẦY ĐỦ**

```bash
# Step 1: Tạo database
mysql -u root -p
CREATE DATABASE db_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;

# Step 2: Import database gốc
mysql -u root -p db_production < db_production.sql

# Step 3: Chạy archived migrations
cd archives
mysql -u root -p db_production < migration_ballpen_units.sql
mysql -u root -p db_production < migration_add_diameter_to_product.sql
mysql -u root -p db_production < migration_optional_diameter_decimal.sql
mysql -u root -p db_production < add_foreign_keys.sql
mysql -u root -p db_production < fix_vietnamese_charset.sql
cd ..

# Step 4: (Optional) Setup RBAC
cd migrations
mysql -u root -p db_production < 000_all_in_one_migration.sql
mysql -u root -p db_production < 004_seed_permissions_data.sql
mysql -u root -p db_production < 005_map_role_permissions.sql
cd ..
```

### **Scenario 2: Chỉ Chạy RBAC Migrations (đã có database đầy đủ)**

```bash
# Xem: migrations/QUICKSTART.md
cd migrations
mysql -u root -p db_production < 000_all_in_one_migration.sql
mysql -u root -p db_production < 004_seed_permissions_data.sql
mysql -u root -p db_production < 005_map_role_permissions.sql
```

### **Scenario 3: Backup Trước Khi Update**

```bash
# Backup trước
mysqldump -u root -p db_production > backups/backup_before_update_$(date +%Y%m%d).sql

# Chạy migrations
source migrations/xxx.sql

# Nếu có lỗi, restore
mysql -u root -p db_production < backups/backup_before_update_YYYYMMDD.sql
```

---

## 📊 Database Schema Summary

### **Current Tables (14):**

| Table | Mô tả | Records |
|-------|-------|---------|
| `customer` | Khách hàng | ~1 |
| `product` | Sản phẩm bút bi | ~1 |
| `project` | Dự án sản xuất | ~1 |
| `planning` | Kế hoạch sản xuất | ~1 |
| `plan_shift` | Kế hoạch ca | ~2 |
| `production` | Báo cáo sản xuất | ~2 |
| `finished_report` | Báo cáo thành phẩm | ~1 |
| `sorting` | Phân loại sản phẩm | ~1 |
| `machine` | Máy móc | ~2 |
| `material` | Nguyên vật liệu | ~1 |
| `staff` | Nhân viên | ~2 |
| `shiftment` | Ca làm việc | ~3 |
| `user` | Người dùng | ~2 |
| `finished` | Thành phẩm hoàn thiện | ~0 |

### **RBAC Tables (5) - Sau khi chạy migrations:**

| Table | Mô tả | Records |
|-------|-------|---------|
| `roles` | Vai trò | 7 |
| `modules` | Modules/Chức năng | 28 |
| `permissions` | Quyền hạn | 174 |
| `role_permissions` | Map role-permission | ~300+ |
| `audit_log` | Nhật ký hoạt động | 0 (sẽ tăng khi dùng) |

---

## 🔧 Maintenance Tasks

### **Dọn dẹp định kỳ:**

```sql
-- Xóa audit logs cũ hơn 6 tháng
DELETE FROM audit_log WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);

-- Optimize tables
OPTIMIZE TABLE customer, product, project, planning, production;
```

### **Check database health:**

```sql
-- Kiểm tra kích thước tables
SELECT 
  table_name AS 'Table',
  ROUND((data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
FROM information_schema.TABLES 
WHERE table_schema = 'db_production'
ORDER BY (data_length + index_length) DESC;

-- Kiểm tra Foreign Keys
SELECT 
  CONSTRAINT_NAME,
  TABLE_NAME,
  COLUMN_NAME,
  REFERENCED_TABLE_NAME,
  REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'db_production' 
  AND REFERENCED_TABLE_NAME IS NOT NULL;
```

---

## 📋 Checklist Khi Thêm Migration Mới

- [ ] Đặt tên file theo format: `00X_description.sql`
- [ ] Thêm header comment (mô tả, author, date)
- [ ] Test trên database development trước
- [ ] Backup database trước khi chạy production
- [ ] Update README.md và CHANGELOG
- [ ] Kiểm tra rollback plan
- [ ] Document trong `/docs` nếu là thay đổi lớn

---

## 🆘 Troubleshooting

### **Lỗi: "Table already exists"**
```sql
-- Drop table nếu cần reset
DROP TABLE IF EXISTS table_name;
```

### **Lỗi: "Foreign key constraint fails"**
```sql
-- Tắt foreign key check tạm thời
SET FOREIGN_KEY_CHECKS = 0;
-- Chạy migration
-- Bật lại
SET FOREIGN_KEY_CHECKS = 1;
```

### **Lỗi: "Character encoding issues"**
```sql
-- Xem charset hiện tại
SHOW VARIABLES LIKE 'character_set%';

-- Fix charset
ALTER DATABASE db_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## 📚 Related Documentation

- [Project README](../README.md) - Tổng quan dự án
- [CHANGELOG](../CHANGELOG.md) - Lịch sử thay đổi
- [CONTRIBUTING](../CONTRIBUTING.md) - Hướng dẫn đóng góp
- [DEPLOY_GITHUB](../DEPLOY_GITHUB.md) - Git workflow

---

## 👥 Team & Support

- **Database Admin:** Production Management Team
- **Last Updated:** November 1, 2025
- **Version:** 2.0.0 (with RBAC)

---

## 📝 Version History

| Version | Date | Changes | Migration Files |
|---------|------|---------|-----------------|
| 2.0.0 | 2025-11-01 | ⭐ RBAC System | migrations/001-005 |
| 1.3.0 | 2023-11-09 | Foreign Keys | archives/add_foreign_keys.sql |
| 1.2.0 | 2023-11-09 | Diameter DECIMAL | archives/migration_optional_diameter_decimal.sql |
| 1.1.0 | 2023-11-09 | Ballpen Units | archives/migration_ballpen_units.sql |
| 1.0.0 | 2023-11-09 | Initial Database | db_production.sql |

---

**🎯 Current Focus:** PHASE 1 Complete - Ready for PHASE 2 (Backend Development)
