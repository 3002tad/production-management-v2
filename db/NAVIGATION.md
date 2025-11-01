# 🗺️ Database Navigation Guide

**Lạc lối trong thư mục `db/`?** Hướng dẫn này sẽ giúp bạn tìm đúng file cần thiết!

---

## 🎯 Tôi muốn...

### **Setup database lần đầu - ĐẦY ĐỦ**
→ Dùng: [`db_production.sql`](db_production.sql) + migrations trong [`archives/`](archives/)  
→ Hướng dẫn: [README.md - Setup Lần Đầu](README.md#-setup-lần-đầu---thứ-tự-chạy)

**Thứ tự chạy:**
```bash
# 1. Import database gốc
mysql -u root -p db_production < db_production.sql

# 2. Chạy archived migrations theo thứ tự
mysql -u root -p db_production < archives/migration_ballpen_units.sql
mysql -u root -p db_production < archives/migration_add_diameter_to_product.sql
mysql -u root -p db_production < archives/migration_optional_diameter_decimal.sql
mysql -u root -p db_production < archives/add_foreign_keys.sql
mysql -u root -p db_production < archives/fix_vietnamese_charset.sql

# 3. (Optional) Setup RBAC
mysql -u root -p db_production < migrations/000_all_in_one_migration.sql
mysql -u root -p db_production < migrations/004_seed_permissions_data.sql
mysql -u root -p db_production < migrations/005_map_role_permissions.sql
```

---

### **Triển khai hệ thống phân quyền (RBAC)**
→ Folder: [`migrations/`](migrations/)  
→ Quick Start: [migrations/QUICKSTART.md](migrations/QUICKSTART.md)  
→ Chi tiết: [migrations/README.md](migrations/README.md)

**3 phút setup:**
```sql
source migrations/000_all_in_one_migration.sql
source migrations/004_seed_permissions_data.sql
source migrations/005_map_role_permissions.sql
```

---

### **Hiểu cấu trúc database**
→ Đọc: [docs/DATABASE_RELATIONSHIPS.md](docs/DATABASE_RELATIONSHIPS.md)  
→ Xem ERD và 12 Foreign Keys

**Quick view:**
```
customer → project → planning → plan_shift → production
                                           ↘ sorting
```

---

### **Fix tiếng Việt bị lỗi font**
→ Đọc: [docs/FIX_FONT_GUIDE.md](docs/FIX_FONT_GUIDE.md)

**Quick fix:**
```sql
ALTER DATABASE db_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### **Hiểu tại sao diameter là DECIMAL?**
→ Đọc: [docs/CHANGELOG_DIAMETER.md](docs/CHANGELOG_DIAMETER.md)  
→ Tóm tắt: VARCHAR → DECIMAL(3,1) để validate dễ hơn

---

### **Backup database trước khi làm gì đó nguy hiểm**
→ Folder: [`backups/`](backups/)  
→ Hướng dẫn: [backups/README.md](backups/README.md)

```bash
mysqldump -u root -p db_production > backups/backup_$(date +%Y%m%d).sql
```

---

### **Xem lịch sử migrations cũ**
→ Folder: [`archives/`](archives/)  
→ Index: [archives/README.md](archives/README.md)  
→ ⚠️ Chỉ đọc, không chạy lại!

---

### **Tìm tài liệu kỹ thuật**
→ Folder: [`docs/`](docs/)  
→ Index: [docs/INDEX.md](docs/INDEX.md)

**Documents có:**
- Database ERD & Foreign Keys
- Changelogs
- Troubleshooting guides
- Migration history

---

## 📂 Cấu trúc Tóm tắt

```
db/
├── 📄 db_production.sql             ⭐ DATABASE GỐC - IMPORT FILE NÀY TRƯỚC
│
├── 📂 migrations/                   ⭐ RBAC System (PHASE 1)
│   ├── QUICKSTART.md                → Chạy nhanh 3 phút
│   └── 001-005 *.sql                → Chi tiết 5 migrations
│
├── 📂 archives/                     ⚠️ PHẢI CHẠY SAU db_production.sql
│   └── 6 files *.sql                → Theo thứ tự: ballpen → diameter → FK → charset
│
├── 📂 docs/                         📚 Tài liệu kỹ thuật
│   ├── INDEX.md                     → Danh mục documents
│   ├── DATABASE_RELATIONSHIPS.md    → ERD
│   ├── CHANGELOG_DIAMETER.md        → Diameter feature
│   └── FIX_FONT_GUIDE.md            → UTF-8 fix
│
└── 📂 backups/                      💾 Database backups
    └── README.md                    → Hướng dẫn backup/restore
```

---

## 🚦 Workflow Thông dụng

### **Scenario 1: Developer mới join team**

```bash
# 1. Clone repo
git clone https://github.com/3002tad/production-management-v2.git
cd production-management-v2/db

# 2. Đọc overview
cat README.md

# 3. Setup database GỐC
mysql -u root -p
CREATE DATABASE db_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit

mysql -u root -p db_production < db_production.sql

# 4. Chạy archived migrations theo thứ tự
mysql -u root -p db_production < archives/migration_ballpen_units.sql
mysql -u root -p db_production < archives/migration_add_diameter_to_product.sql
mysql -u root -p db_production < archives/migration_optional_diameter_decimal.sql
mysql -u root -p db_production < archives/add_foreign_keys.sql
mysql -u root -p db_production < archives/fix_vietnamese_charset.sql

# 5. (Optional) Setup RBAC
cd migrations
mysql -u root -p db_production < 000_all_in_one_migration.sql
mysql -u root -p db_production < 004_seed_permissions_data.sql
mysql -u root -p db_production < 005_map_role_permissions.sql

# 6. Đọc docs để hiểu hệ thống
cd ../docs
cat INDEX.md
cat DATABASE_RELATIONSHIPS.md
```

---

### **Scenario 2: Chạy RBAC migrations**

```bash
cd db/migrations

# Quick way (3 phút)
mysql -u root -p db_production < 000_all_in_one_migration.sql
mysql -u root -p db_production < 004_seed_permissions_data.sql
mysql -u root -p db_production < 005_map_role_permissions.sql

# Verify
mysql -u root -p db_production
SELECT * FROM roles;
SELECT COUNT(*) FROM permissions;
```

---

### **Scenario 3: Backup trước khi update**

```bash
cd db/backups

# Tạo backup
mysqldump -u root -p db_production > backup_before_update_$(date +%Y%m%d).sql

# Verify backup
ls -lh *.sql

# Nếu có lỗi, restore
mysql -u root -p db_production < backup_before_update_20251101.sql
```

---

### **Scenario 4: Debug lỗi Foreign Key**

```bash
# 1. Đọc docs
cat docs/DATABASE_RELATIONSHIPS.md

# 2. Check constraints trong database
mysql -u root -p db_production

SHOW CREATE TABLE project;
SELECT * FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_NAME = 'project' AND REFERENCED_TABLE_NAME IS NOT NULL;

# 3. Nếu cần drop constraint
ALTER TABLE project DROP FOREIGN KEY fk_project_customer;
```

---

### **Scenario 5: Fix tiếng Việt lỗi**

```bash
# 1. Đọc guide
cat docs/FIX_FONT_GUIDE.md

# 2. Apply fix
mysql -u root -p db_production

ALTER DATABASE db_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE customer CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Repeat cho các tables khác...

# 3. Verify
SHOW CREATE DATABASE db_production;
SHOW CREATE TABLE customer;
```

---

## 📖 Tài liệu Bắt buộc Đọc (cho Developer mới)

### **Must Read (30 phút):**
1. [README.md](README.md) - Overview toàn bộ folder
2. [docs/DATABASE_RELATIONSHIPS.md](docs/DATABASE_RELATIONSHIPS.md) - ERD & FKs
3. [migrations/README.md](migrations/README.md) - RBAC system

### **Should Read (1 giờ):**
4. [docs/CHANGELOG_DIAMETER.md](docs/CHANGELOG_DIAMETER.md)
5. [docs/FIX_FONT_GUIDE.md](docs/FIX_FONT_GUIDE.md)
6. [archives/README.md](archives/README.md)

### **Nice to Have (khi cần):**
7. [docs/MIGRATION_README.md](docs/MIGRATION_README.md)
8. [docs/UPDATE_SUMMARY_DECIMAL_AUTOFILL.md](docs/UPDATE_SUMMARY_DECIMAL_AUTOFILL.md)

---

## ❓ FAQ

### **Q: File nào là database mới nhất?**
**A:** `db_production_complete.sql` - Đây là file đầy đủ nhất.

### **Q: Tôi có cần chạy migrations trong archives/?**
**A:** KHÔNG. Chúng đã được apply vào `db_production_complete.sql`.

### **Q: RBAC là gì? Bắt buộc phải dùng không?**
**A:** Role-Based Access Control - hệ thống phân quyền. Không bắt buộc nhưng khuyến nghị cho project thật.

### **Q: Tôi xóa nhầm data, restore như thế nào?**
**A:** Dùng backup trong `backups/`. Nếu không có, phải import lại `db_production_complete.sql`.

### **Q: Làm sao biết migration nào đã chạy?**
**A:** Kiểm tra tables trong database:
```sql
SHOW TABLES LIKE 'roles';  -- Nếu có = đã chạy RBAC migrations
SELECT * FROM roles;       -- Nếu có 7 roles = migrations hoàn tất
```

### **Q: Tôi muốn thêm migration mới, đặt ở đâu?**
**A:** Folder `migrations/`, đặt tên `006_description.sql` (tiếp theo số cuối).

---

## 🆘 Troubleshooting Quick Links

| Problem | Solution |
|---------|----------|
| Tiếng Việt bị ??? | [docs/FIX_FONT_GUIDE.md](docs/FIX_FONT_GUIDE.md) |
| Foreign key error | [docs/DATABASE_RELATIONSHIPS.md](docs/DATABASE_RELATIONSHIPS.md) |
| Migration fails | [migrations/README.md](migrations/README.md#troubleshooting) |
| Diameter validation lỗi | [docs/CHANGELOG_DIAMETER.md](docs/CHANGELOG_DIAMETER.md) |
| Không biết bắt đầu từ đâu | [README.md](README.md) |

---

## 📞 Support

- **GitHub Issues:** [production-management-v2/issues](https://github.com/3002tad/production-management-v2/issues)
- **Documentation:** Các file README trong từng folder
- **Team:** Production Management Team

---

**🎯 Pro Tip:** Bookmark file này để tìm đường nhanh! 

**Last Updated:** November 1, 2025
