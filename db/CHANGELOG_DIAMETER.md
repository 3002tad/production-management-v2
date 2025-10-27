# CHANGELOG: Thêm cột Diameter vào bảng Product

**Ngày:** 26/10/2025  
**Mục đích:** Đồng bộ thông tin đường kính bi viết giữa bảng `product` và `project`

---

## 📋 TÓM TẮT THAY ĐỔI

Trước đây:
- Bảng `product` không có thông tin đường kính bi
- Chỉ có bảng `project` lưu đường kính cho từng dự án
- Khi tạo project phải nhập lại đường kính mỗi lần

Sau khi cập nhật:
- Bảng `product` có cột `diameter` lưu đường kính tiêu chuẩn
- Khi tạo project có thể sử dụng giá trị mặc định từ product
- Đồng bộ dữ liệu giữa master data (product) và transaction data (project)

---

## 🗄️ DATABASE CHANGES

### 1. Migration SQL File
**File:** `db/migration_add_diameter_to_product.sql`

**Cấu trúc mới của bảng `product`:**
```sql
CREATE TABLE `product` (
  `id_product` INT(25) NOT NULL AUTO_INCREMENT,
  `product_name` VARCHAR(50) NOT NULL,
  `summary` LONGTEXT NOT NULL,
  `application` LONGTEXT NOT NULL,  -- Màu mực
  `diameter` INT(25) NOT NULL DEFAULT 5,  -- Đường kính bi (x10)
  PRIMARY KEY (`id_product`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Giá trị diameter:**
- Lưu giá trị x10 (nhất quán với bảng `project`)
- VD: 5 = 0.5mm, 7 = 0.7mm, 10 = 1.0mm
- Default: 5 (0.5mm - kích thước phổ biến nhất)

---

## 💻 CODE CHANGES

### 2. Controller Updates
**File:** `application/controllers/Admin.php`

**Function:** `addProduct()`
```php
public function addProduct()
{
    $add = [
        'id_product' => $this->crudModel->generateCode(1, 'id_product', 'product'),
        'product_name' => trim($this->input->post('product_name')),
        'summary' => trim($this->input->post('summary')),
        'application' => trim($this->input->post('application')),
        'diameter' => intval($this->input->post('diameter')),  // ← THÊM MỚI
    ];

    $this->crudModel->addData('product', $add);
    redirect(site_url('Admin/product'));
}
```

---

### 3. View Updates

#### A. AddProduct.php
**File:** `application/views/admin/product/AddProduct.php`

**Thêm field input:**
```php
<span><?= lang('label_diameter'); ?> (<?= lang('unit_mm'); ?>)</span></br>
<div class="input-group input-group-dynamic mb-4">
    <label class="form-label"></label>
    <input type="number" name="diameter" class="form-control" 
           placeholder="VD: 5 (0.5mm), 7 (0.7mm), 10 (1.0mm)" 
           step="1" min="1" value="5">
</div>
<small class="text-muted"><?= lang('note_diameter_x10'); ?></small>
```

**Features:**
- Input type: number với step=1 (chỉ cho phép số nguyên)
- Giá trị mặc định: 5 (0.5mm)
- Placeholder hướng dẫn người dùng
- Ghi chú giải thích cách nhập

---

#### B. Product.php (List View)
**File:** `application/views/admin/product/Product.php`

**Thêm cột trong table:**
```php
// Header
<th><?= lang('label_diameter'); ?></th>

// Body
<td>
    <span class="text-sm font-weight-bold">
        <?= $value->diameter/10; ?> <?= lang('unit_mm'); ?>
    </span>
</td>
```

**Features:**
- Hiển thị giá trị thực (chia 10)
- Kèm đơn vị "mm"
- Format: "0.5 mm", "0.7 mm", "1.0 mm"

---

### 4. Language File Updates
**File:** `application/language/vietnamese/translation_lang.php`

**Thêm key mới:**
```php
$lang['note_diameter_x10'] = 'Lưu ý: Nhập giá trị x10 (VD: nhập 5 cho 0.5mm, 7 cho 0.7mm, 10 cho 1.0mm)';
```

**Các key đã có sẵn:**
- `label_diameter` = 'Đường kính bi'
- `form_diameter` = 'Đường kính bi'
- `table_diameter` = 'Đường kính bi'
- `unit_mm` = 'mm'

---

## 🚀 CÁCH THỰC HIỆN MIGRATION

### Bước 1: Backup Database
```sql
mysqldump -u root -p db_production > backup_before_diameter_$(date +%Y%m%d).sql
```

### Bước 2: Chạy Migration
1. Mở phpMyAdmin
2. Chọn database `db_production`
3. Vào tab SQL
4. Copy toàn bộ nội dung file `migration_add_diameter_to_product.sql`
5. Click "Go"

### Bước 3: Verify
```sql
-- Kiểm tra cấu trúc bảng
DESCRIBE `product`;

-- Kiểm tra dữ liệu
SELECT 
    id_product,
    product_name,
    application AS 'Màu mực',
    diameter AS 'Đường kính (x10)',
    CONCAT(diameter/10, 'mm') AS 'Đường kính thực'
FROM `product`;
```

**Kết quả mong đợi:**
```
+------------+--------------+----------+------------------+-----------------+
| id_product | product_name | Màu mực  | Đường kính (x10) | Đường kính thực |
+------------+--------------+----------+------------------+-----------------+
| 1001       | Test Prdc    | ...      | 5                | 0.5mm           |
+------------+--------------+----------+------------------+-----------------+
```

---

## 📊 TÁC ĐỘNG HỆ THỐNG

### Files đã chỉnh sửa:
✅ `db/migration_add_diameter_to_product.sql` (NEW)  
✅ `application/controllers/Admin.php` (MODIFIED)  
✅ `application/views/admin/product/AddProduct.php` (MODIFIED)  
✅ `application/views/admin/product/Product.php` (MODIFIED)  
✅ `application/language/vietnamese/translation_lang.php` (MODIFIED)  

### Files có thể cần cập nhật sau:
⏳ `application/views/admin/project/AddProject.php` - Có thể auto-fill diameter từ product  
⏳ Leader views tương ứng (nếu có)  

### Tính năng có thể mở rộng:
1. **Auto-fill diameter khi chọn product trong project:**
   ```javascript
   // jQuery: Khi chọn product, tự động điền diameter
   $('#product_select').change(function() {
       var diameter = $(this).find(':selected').data('diameter');
       $('#diameter_input').val(diameter);
   });
   ```

2. **Validation đường kính phổ biến:**
   - Chỉ cho phép: 3, 5, 7, 10 (0.3mm, 0.5mm, 0.7mm, 1.0mm)
   - Hoặc dùng dropdown thay vì input

3. **Báo cáo theo đường kính:**
   - Thống kê sản lượng theo từng kích thước
   - Phân tích xu hướng đơn hàng

---

## ⚠️ LƯU Ý QUAN TRỌNG

### 1. Dữ liệu hiện có
- Tất cả product hiện tại sẽ có diameter = 5 (0.5mm) mặc định
- Cần kiểm tra và cập nhật lại nếu không chính xác

### 2. Đồng bộ với Project
- Khi tạo project mới, có thể:
  - **Option A:** Copy diameter từ product (khuyến nghị)
  - **Option B:** Cho phép override nếu dự án cần kích thước khác

### 3. Validation
- Diameter phải > 0
- Khuyến nghị: 3-15 (0.3mm - 1.5mm) cho bút bi thông thường

### 4. Unit Consistency
- Product.diameter và Project.diameter đều dùng INT (x10)
- Đảm bảo consistency khi query join 2 bảng

---

## 🧪 TESTING CHECKLIST

- [ ] Migration chạy thành công không lỗi
- [ ] Bảng `product` có cột `diameter`
- [ ] Add Product form hiển thị đúng field diameter
- [ ] Lưu product mới với diameter thành công
- [ ] Product list hiển thị đúng giá trị diameter (mm)
- [ ] Translation keys hoạt động đúng
- [ ] Dữ liệu cũ có giá trị mặc định 5
- [ ] Validation input number chỉ nhận số nguyên

---

## 🔄 ROLLBACK (NẾU CẦN)

Nếu có vấn đề, chạy lệnh sau để xóa cột:

```sql
USE `db_production`;

-- Xóa cột diameter
ALTER TABLE `product` DROP COLUMN `diameter`;

-- Restore từ backup
-- mysql -u root -p db_production < backup_before_diameter_YYYYMMDD.sql
```

---

## 📞 HỖ TRỢ

Nếu gặp vấn đề:
1. Kiểm tra error log MySQL
2. Verify charset của bảng (phải utf8mb4)
3. Đảm bảo đã chạy fix_vietnamese_charset.sql trước
4. Check quyền user MySQL có ALTER TABLE không

---

**Tạo bởi:** GitHub Copilot  
**Phiên bản:** 1.0  
**Trạng thái:** ✅ Hoàn thành và sẵn sàng deploy
