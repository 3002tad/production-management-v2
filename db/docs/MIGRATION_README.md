# HƯỚNG DẪN MIGRATION DATABASE CHO HỆ THỐNG SẢN XUẤT BÚT BI

## 📋 Tổng quan

Các file migration này được tạo để cập nhật database từ hệ thống sản xuất chung sang hệ thống sản xuất bút bi chuyên biệt, với các thay đổi về đơn vị đo lường, trạng thái và **sửa lỗi font tiếng Việt**.

## 📁 Các file migration

```
db/
├── db_production.sql                          # Database gốc
├── fix_vietnamese_charset.sql                 # Fix lỗi font tiếng Việt (CHẠY ĐẦU TIÊN!)
├── migration_ballpen_units.sql                # Migration chính (BẮT BUỘC)
├── migration_optional_diameter_decimal.sql    # Migration tùy chọn
└── MIGRATION_README.md                        # File này
```

## ⚠️ QUAN TRỌNG - THỨ TỰ CHẠY MIGRATION

### **BƯỚC 0: FIX LỖI FONT TIẾNG VIỆT (CHẠY ĐẦU TIÊN!)**

**Vấn đề:** Tiếng Việt hiển thị dạng "Bút bi m?c gel" thay vì "Bút bi mực gel"

**Giải pháp:**
1. **Backup database** (QUAN TRỌNG!)
2. Chạy file: `fix_vietnamese_charset.sql`
3. File này sẽ:
   - Đổi charset database sang `utf8mb4`
   - Đổi charset tất cả bảng sang `utf8mb4_unicode_ci`
   - Sửa lại dữ liệu bị mã hóa sai

**Cách chạy:**
```
1. phpMyAdmin → db_production → SQL
2. Copy toàn bộ nội dung file: fix_vietnamese_charset.sql
3. Paste và click "Go"
4. Kiểm tra dữ liệu hiển thị đúng tiếng Việt
```

## ⚠️ QUAN TRỌNG - PHẢI LÀM TRƯỚC KHI MIGRATION

### **THỨ TỰ THỰC HIỆN (QUAN TRỌNG!):**

```
📌 SETUP MỚI (Database chưa có):
1. Tạo database → db_production.sql ✅
2. Fix charset → fix_vietnamese_charset.sql ✅
3. Migration đơn vị → migration_ballpen_units.sql ✅
4. (Tùy chọn) → migration_optional_diameter_decimal.sql

📌 DATABASE ĐÃ CÓ SẴN:
1. Backup database ✅
2. Fix charset → fix_vietnamese_charset.sql ✅
3. Migration đơn vị → migration_ballpen_units.sql ✅
4. (Tùy chọn) → migration_optional_diameter_decimal.sql
```

### 1. Backup Database (BẮT BUỘC)

```bash
# Windows (PowerShell)
cd C:\xampp\mysql\bin
.\mysqldump.exe -u root -p db_production > D:\Code\PTUD\production-management-v2\db\backup_before_migration.sql

# hoặc từ phpMyAdmin: Export → SQL → Tải về
```

### 2. Tạo Database (NẾU CHƯA CÓ)

**Nếu database chưa tồn tại:**

```
1. phpMyAdmin → New → Tạo database: db_production
2. Tab SQL
3. Copy toàn bộ file: db_production.sql
4. Paste và click "Go"
5. Đợi tạo xong tất cả bảng
```

- MariaDB: 10.4.27 trở lên
- MySQL: 5.7 trở lên

## 🚀 Cách chạy Migration

### **THỨ TỰ THỰC HIỆN (QUAN TRỌNG):**

```
0. Backup Database ✅
1. Fix lỗi font tiếng Việt (fix_vietnamese_charset.sql) ✅ CHẠY ĐẦU TIÊN
2. Migration chính (migration_ballpen_units.sql) ✅ 
3. Migration tùy chọn (migration_optional_diameter_decimal.sql) - Nếu cần
```

### Bước 0: FIX LỖI FONT (BẮT BUỘC)

**Qua phpMyAdmin:**
1. Mở phpMyAdmin: http://localhost:8080/phpmyadmin
2. Chọn database `db_production`
3. Tab "SQL"
4. Copy toàn bộ nội dung file: `fix_vietnamese_charset.sql`
5. Paste vào và click "Go"
6. **Kiểm tra:** SELECT * FROM product; → Xem tiếng Việt hiển thị đúng chưa

### Bước 1: Chạy Migration chính (BẮT BUỘC)

**Qua phpMyAdmin:**
1. Mở phpMyAdmin: http://localhost:8080/phpmyadmin
2. Chọn database `db_production`
3. Tab "SQL"
4. Copy toàn bộ nội dung file `migration_ballpen_units.sql`
5. Paste vào và click "Go"

**Qua Command Line:**
```bash
# Windows
cd C:\xampp\mysql\bin
.\mysql.exe -u root -p db_production < D:\Code\PTUD\production-management-v2\db\migration_ballpen_units.sql
```

### Bước 2: Chạy Migration tùy chọn (NẾU CẦN)

Chỉ chạy file `migration_optional_diameter_decimal.sql` nếu muốn:
- Lưu đường kính chính xác dạng 0.5, 0.7, 1.0 mm (DECIMAL)
- Thay vì lưu dạng 5, 7, 10 (INT)

**Lưu ý:** Nếu chạy migration này, cần cập nhật code PHP để xử lý DECIMAL.

## 📊 Các thay đổi chính

### 1. Đơn vị đo lường

| Bảng | Cột | Đơn vị cũ | Đơn vị mới | Ghi chú |
|------|-----|-----------|------------|---------|
| `machine` | `capacity` | Kg | **cái/giờ** | Số bút sản xuất/giờ |
| `material` | `stock` | Kg | **gram** | Tồn kho nguyên liệu |
| `material` | `used_stock` | Kg | **gram** | Nguyên liệu đã dùng |
| `project` | `qty_request` | Kg | **cái** | Số lượng bút yêu cầu |
| `project` | `diameter` | mm | **mm** | Đường kính bi (0.5, 0.7, 1.0) |
| `planning` | `qty_target` | Kg/ca | **cái/ca** | Mục tiêu sản xuất/ca |
| `sorting_report` | `waste` | Kg | **cái** | Số bút phế phẩm |
| `sorting_report` | `finished` | Kg | **cái** | Số bút hoàn thành |
| `finished_report` | `total_finished` | Kg | **cái** | Tổng bút hoàn thành |

### 2. Trạng thái máy móc (`mc_status`)

| Giá trị | Trạng thái | Màu hiển thị | Mô tả |
|---------|------------|--------------|-------|
| **1** | Sẵn sàng | 🟢 Xanh lá | Máy có thể bắt đầu sản xuất |
| **2** | Đang sử dụng | 🟡 Vàng | Máy đang trong ca sản xuất |
| **3** | Sự cố | 🔴 Đỏ | Máy hỏng, cần sửa chữa |
| **4** | Bảo trì | 🔵 Xanh dương | Máy đang bảo dưỡng (**MỚI**) |

### 3. Cập nhật Product schema

- Cột `application`: Đổi ý nghĩa từ "Ứng dụng" → **"Màu mực"**
  - Ví dụ: "Xanh", "Đen", "Đỏ", "Nhiều màu"
- Cột `summary`: Thông tin chi tiết sản phẩm bút bi

## 🔍 Kiểm tra sau Migration

### 1. Kiểm tra Views đã tạo

```sql
-- Xem trạng thái máy móc
SELECT * FROM v_machine_status;

-- Xem tồn kho nguyên liệu
SELECT * FROM v_material_stock;

-- Xem chi tiết dự án
SELECT * FROM v_project_details;
```

### 2. Kiểm tra dữ liệu mẫu

```sql
-- Kiểm tra sản phẩm bút bi
SELECT * FROM product;

-- Kiểm tra nguyên liệu
SELECT material_name, CONCAT(stock, ' gram') AS stock_display 
FROM material;

-- Kiểm tra máy móc
SELECT machine_name, CONCAT(capacity, ' cái/giờ') AS capacity_display,
       CASE mc_status 
           WHEN 1 THEN 'Sẵn sàng'
           WHEN 2 THEN 'Đang dùng'
           WHEN 3 THEN 'Sự cố'
           WHEN 4 THEN 'Bảo trì'
       END AS status
FROM machine;
```

## 📝 Dữ liệu mẫu đã thêm

### Sản phẩm (Product)
- Bút bi TL-079 (Xanh dương)
- Bút bi TL-050 (Đen)
- Bút bi TL-100 (Đỏ)
- Bút bi TL-Multi (Nhiều màu)

### Nguyên liệu (Material)
- Nhựa ABS: 10000 gram
- Mực gel xanh: 5000 gram
- Mực gel đen: 5000 gram
- Bi kim loại 0.5mm: 2000 gram
- Bi kim loại 0.7mm: 3000 gram
- Bi kim loại 1.0mm: 2000 gram
- Lò xo thép: 1000 gram

## 🔧 Triggers đã tạo

1. `validate_machine_capacity`: Kiểm tra công suất máy > 0
2. `validate_material_stock`: Kiểm tra tồn kho không âm

## ⚡ Rollback (Khôi phục)

Nếu có lỗi, khôi phục từ backup:

```bash
# Windows
cd C:\xampp\mysql\bin
.\mysql.exe -u root -p db_production < D:\Code\PTUD\production-management-v2\db\backup_before_migration.sql
```

## 📌 Lưu ý quan trọng

### Về diameter (Đường kính bi):

**Cách 1: Giữ INT (Khuyến nghị - đơn giản)**
- Lưu: 5 cho 0.5mm, 7 cho 0.7mm, 10 cho 1.0mm
- Hiển thị: Chia 10 khi xuất ra view
- Ưu điểm: Không cần sửa code nhiều

**Cách 2: Đổi sang DECIMAL (Chính xác hơn)**
- Lưu: 0.5, 0.7, 1.0 trực tiếp
- Cần chạy: `migration_optional_diameter_decimal.sql`
- Cần cập nhật: Code PHP xử lý input/output

### Về tỷ lệ chuyển đổi:

Migration này dùng tỷ lệ ước tính:
- **1 Kg bút bi ≈ 200 cái** (tùy loại bút)
- **1 Kg nguyên liệu = 1000 gram**

⚠️ **Điều chỉnh lại dữ liệu thực tế sau khi migration!**

## 📞 Hỗ trợ

Nếu gặp lỗi khi migration:
1. Kiểm tra file log: `C:\xampp\mysql\data\mysql_error.log`
2. Xem lỗi trong phpMyAdmin
3. Rollback về backup và báo lỗi để sửa

## ✅ Checklist sau Migration

- [ ] Backup database hoàn tất
- [ ] Chạy migration_ballpen_units.sql thành công
- [ ] Kiểm tra dữ liệu với các SELECT query
- [ ] Test trên giao diện web
- [ ] Cập nhật Controller/Model nếu cần xử lý đặc biệt
- [ ] Đào tạo người dùng về đơn vị mới

---

**Ngày tạo:** 26/10/2025  
**Phiên bản:** 1.0  
**Tương thích:** MariaDB 10.4+, MySQL 5.7+
