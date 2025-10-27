# 🔧 FIX LỖI FONT TIẾNG VIỆT - HƯỚNG DẪN NHANH

## ❌ Vấn đề

Tiếng Việt hiển thị sai trong database:
- ❌ "Bút bi m?c gel, thân nh?a trong su?t, vi?t m??t"
- ❌ "?en", "??"
- ❌ "Màu m?c: Xanh, ?en, ?ô, Nhiều màu"

## ✅ Giải pháp

### ⚠️ ĐIỀU KIỆN TIÊN QUYẾT

**Database phải đã được tạo và có các bảng!**

Nếu chưa có database:
1. Tạo database `db_production` trong phpMyAdmin
2. Import file `db_production.sql` để tạo các bảng
3. Sau đó mới chạy fix charset

### BƯỚC 0: KIỂM TRA DATABASE

```sql
-- Chạy trong phpMyAdmin để kiểm tra
USE db_production;
SHOW TABLES;

-- Phải thấy danh sách bảng:
-- customer, product, project, planning, machine, material, staff, shiftment, v.v.
```

**Nếu không có bảng nào → Chạy `db_production.sql` trước!**

### BƯỚC 1: BACKUP (BẮT BUỘC!)

```
phpMyAdmin → db_production → Export → Tải file backup
```

### BƯỚC 2: CHẠY FIX CHARSET

**Cách 1: Qua phpMyAdmin (Khuyến nghị)**

1. Mở: http://localhost:8080/phpmyadmin
2. Chọn database: `db_production`
3. Click tab "SQL"
4. Mở file: `fix_vietnamese_charset.sql`
5. Copy TOÀN BỘ nội dung
6. Paste vào ô SQL và click "Go"
7. Chờ chạy xong (~5-10 giây)

**Cách 2: Qua Command Line**

```bash
# Windows PowerShell
cd C:\xampp\mysql\bin
.\mysql.exe -u root -p db_production < D:\Code\PTUD\production-management-v2\db\fix_vietnamese_charset.sql
```

### BƯỚC 3: KIỂM TRA

Chạy các lệnh SQL sau trong phpMyAdmin:

```sql
-- Kiểm tra charset database
SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME 
FROM INFORMATION_SCHEMA.SCHEMATA 
WHERE SCHEMA_NAME = 'db_production';

-- Kết quả mong đợi:
-- utf8mb4 | utf8mb4_unicode_ci

-- Kiểm tra dữ liệu tiếng Việt
SELECT * FROM product;
SELECT * FROM material;
SELECT * FROM shiftment;
```

**✅ Thành công nếu thấy:**
- "Bút bi mực gel, thân nhựa trong suốt, viết mượt"
- "Xanh dương", "Đen", "Đỏ"
- "Nhựa ABS", "Mực gel xanh"

### BƯỚC 4: CẬP NHẬT CONFIG (ĐÃ TỰ ĐỘNG)

File `application/config/database.php` đã được cập nhật:

```php
'char_set' => 'utf8mb4',  // ✅ Đã sửa từ 'utf8'
'dbcollat' => 'utf8mb4_unicode_ci',  // ✅ Đã sửa từ 'utf8_general_ci'
```

## 🚨 Nếu vẫn bị lỗi

### Lỗi 1: Dữ liệu cũ đã bị lưu sai

**Giải pháp:** Nhập lại dữ liệu thủ công sau khi đổi charset

Ví dụ:
```sql
UPDATE product SET 
    product_name = 'Bút bi TL-079',
    summary = 'Bút bi mực gel, thân nhựa trong suốt',
    application = 'Xanh dương'
WHERE id_product = 1001;
```

### Lỗi 2: Browser không hiển thị UTF-8

**Giải pháp:** Thêm vào đầu file PHP:

```php
// application/controllers/Admin.php
header('Content-Type: text/html; charset=utf-8');
```

Hoặc trong views:
```html
<meta charset="UTF-8">
```

### Lỗi 3: phpMyAdmin hiển thị sai

**Giải pháp:** Cấu hình phpMyAdmin

File: `C:\xampp\phpMyAdmin\config.inc.php`

Thêm:
```php
$cfg['DefaultCharset'] = 'utf8mb4';
$cfg['DefaultConnectionCollation'] = 'utf8mb4_unicode_ci';
```

## 📊 Checklist sau khi Fix

- [ ] Database charset = utf8mb4_unicode_ci
- [ ] Tất cả bảng charset = utf8mb4_unicode_ci
- [ ] File database.php đã cập nhật
- [ ] Test hiển thị tiếng Việt trên web
- [ ] Test nhập dữ liệu tiếng Việt mới
- [ ] Backup database sau khi fix thành công

## 💡 Tips

1. **Luôn backup trước khi fix**
2. **Test trên database test trước** (nếu có)
3. **Chạy fix_vietnamese_charset.sql TRƯỚC các migration khác**
4. **Kiểm tra kỹ sau khi fix**

## 🔄 Rollback nếu cần

```bash
# Khôi phục từ backup
cd C:\xampp\mysql\bin
.\mysql.exe -u root -p db_production < backup_before_charset_fix.sql
```

---

**Lưu ý:** Sau khi fix charset thành công, mới chạy các migration khác!
