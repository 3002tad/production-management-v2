# 📦 Archived Migrations

Thư mục này chứa các migration đã được áp dụng vào database. 

⚠️ **KHÔNG chạy lại** các migrations này - chúng chỉ để tham khảo lịch sử.

---

## 📋 Danh sách Migrations (Theo thứ tự thời gian)

### **1. migration_ballpen_units.sql**
- **Ngày áp dụng:** 2023-11-09
- **Mô tả:** Chuyển đổi đơn vị sản xuất từ Kg sang pieces (cái) và gram
- **Thay đổi:**
  - Table `product`: Đổi unit từ "Kg" → "pieces" (cái)
  - Table `production`: Đổi unit từ "Kg" → "gram"
  - Phù hợp với sản xuất bút bi
- **Trạng thái:** ✅ Đã áp dụng vào `db_production_complete.sql`

---

### **2. migration_add_diameter_to_product.sql**
- **Ngày áp dụng:** 2023-11-09  
- **Mô tả:** Thêm cột `diameter` vào bảng `product`
- **Thay đổi:**
  - Thêm column: `diameter VARCHAR(10) DEFAULT '0.5' COMMENT 'Đường kính bút (0.5mm, 0.7mm, 1.0mm)'`
  - Application: "Đầu bút", "Màu mực" → "Đường kính", "Màu mực"
- **Trạng thái:** ✅ Đã áp dụng

---

### **3. fix_add_diameter_column.sql**
- **Ngày áp dụng:** 2023-11-09
- **Mô tả:** Fix lỗi khi thêm diameter column (nếu đã tồn tại)
- **Thay đổi:**
  - Kiểm tra column tồn tại trước khi thêm
  - Tránh lỗi "Duplicate column name"
- **Trạng thái:** ✅ Fixed

---

### **4. migration_optional_diameter_decimal.sql**
- **Ngày áp dụng:** 2023-11-09
- **Mô tả:** Chuyển đổi `diameter` từ VARCHAR sang DECIMAL, cho phép NULL
- **Thay đổi:**
  - `diameter VARCHAR(10)` → `diameter DECIMAL(3,1) NULL COMMENT 'Đường kính bút: 0.5, 0.7, 1.0 mm'`
  - Lý do: Dữ liệu số chính xác hơn, dễ validate
  - Cho phép NULL: Sản phẩm không phải bút có thể bỏ trống
- **Trạng thái:** ✅ Đã áp dụng

---

### **5. add_foreign_keys.sql**
- **Ngày áp dụng:** 2023-11-09
- **Mô tả:** Tạo 12 Foreign Key constraints để đảm bảo referential integrity
- **Thay đổi:**
  
  **Foreign Keys được tạo:**
  1. `project.id_cust` → `customer.id_cust`
  2. `project.id_product` → `product.id_product`
  3. `planning.id_project` → `project.id_project`
  4. `plan_shift.id_plan` → `planning.id_plan`
  5. `plan_shift.id_shift` → `shiftment.id_shift`
  6. `production.id_planshift` → `plan_shift.id_planshift`
  7. `production.id_machine` → `machine.id_machine`
  8. `sorting.id_planshift` → `plan_shift.id_planshift`
  9. `sorting.id_staff` → `staff.id_staff`
  10. `finished_report.id_project` → `project.id_project`
  11. `finished.id_finished` → `finished_report.id_finished`
  12. `finished.id_product` → `product.id_product`

- **Lợi ích:**
  - Đảm bảo dữ liệu nhất quán
  - Không thể xóa record đang được tham chiếu
  - Cascade updates/deletes nếu cần
  
- **Trạng thái:** ✅ Đã áp dụng

---

### **6. fix_vietnamese_charset.sql**
- **Ngày áp dụng:** 2023-11-09
- **Mô tả:** Fix encoding UTF-8 cho tiếng Việt
- **Thay đổi:**
  - Database: `CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci`
  - Tất cả tables: Convert sang `utf8mb4_unicode_ci`
  - Fix hiển thị tiếng Việt bị garbled
- **Trạng thái:** ✅ Đã áp dụng

---

## 🔍 Khi nào dùng các files này?

### ✅ **NÊN dùng để:**
- 📖 Tham khảo lịch sử thay đổi database
- 🐛 Debug khi có vấn đề với features cũ
- 📝 Hiểu logic nghiệp vụ đã thay đổi như thế nào
- 👥 Onboarding thành viên mới (học lịch sử dự án)

### ❌ **KHÔNG NÊN:**
- ❌ Chạy lại các migrations này (đã được apply)
- ❌ Modify các files này (chỉ đọc)
- ❌ Dùng làm source of truth (dùng `db_production_complete.sql`)

---

## 📊 Impact Summary

| Migration | Tables Affected | Records Changed | Risk Level |
|-----------|----------------|-----------------|------------|
| ballpen_units | product, production | ~10 | Low |
| add_diameter | product | 0 (new column) | Low |
| optional_diameter_decimal | product | 0 (type change) | Low |
| add_foreign_keys | 12 tables | 0 (constraints) | Medium |
| vietnamese_charset | All tables | All | Medium |

---

## 🔗 Related Documentation

- [DATABASE_RELATIONSHIPS.md](../docs/DATABASE_RELATIONSHIPS.md) - ERD với 12 FKs
- [CHANGELOG_DIAMETER.md](../docs/CHANGELOG_DIAMETER.md) - Chi tiết diameter feature
- [FIX_FONT_GUIDE.md](../docs/FIX_FONT_GUIDE.md) - UTF-8 encoding guide

---

## 📝 Notes

- Tất cả changes trong folder này đã được merge vào `db_production_complete.sql`
- Nếu cần rollback feature, phải viết migration mới (không dùng lại file cũ)
- Migrations mới phải đặt trong folder `/migrations/` với số thứ tự tiếp theo

---

**Last Updated:** November 1, 2025  
**Status:** Archived ✓
