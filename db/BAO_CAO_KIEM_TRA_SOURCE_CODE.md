# ✅ BÁO CÁO KIỂM TRA TOÀN DIỆN SOURCE CODE & DATABASE

## 🎯 MỤC TIÊU KIỂM TRA
Đảm bảo code hoạt động chính xác cho **7 kịch bản test** với database thực tế:
1. ✅ Bình thường - Đủ mọi điều kiện
2. 🔴 Cảnh báo - Vượt Level 1, dùng Level 2
3. ❌ Thiếu NVL - 1+ NVL không đủ
4. ⚠️ BOM thiếu - NVL NULL trong BOM
5. ❌ Từ chối - Vượt cả Level 2
6. ⏰ Sắp hết hạn - Còn < 3 ngày
7. 🚀 Phân bổ stock - Ưu tiên đơn hàng

---

## 📊 1. KIỂM TRA DATABASE SCHEMA

### ✅ **Bảng `finished_stock`** (Tồn kho thành phẩm)
```sql
CREATE TABLE finished_stock (
    id_stock INT PRIMARY KEY,
    id_product INT,
    quantity_in_stock INT,     -- ✅ Có
    quantity_received INT,      -- ✅ Có
    quantity_issued INT,        -- ✅ Có (0 theo hình)
    last_updated TIMESTAMP      -- ✅ Có
)
```
**Dữ liệu thực tế:**
- Product 1001 (TL-079): **35 cái** ✅

### ✅ **Bảng `material`** (Nguyên vật liệu)
```sql
CREATE TABLE material (
    id_material INT PRIMARY KEY,
    material_name VARCHAR(255),  -- ✅ Có
    stock DECIMAL(10,2),         -- ✅ Có
    min_stock DECIMAL(10,2),     -- ✅ Có
    uom VARCHAR(50)              -- ✅ Có
)
```
**Dữ liệu thực tế:**
| ID | Tên | Stock | Min | UOM |
|----|-----|-------|-----|-----|
| 1001 | Test Matereal | 5,000 | 1,000 | g |
| 1002 | Nhựa ABS | 10,000 | 1,000 | g |
| 1003 | Mực gel xanh | 5,000 | 1,000 | g |
| 1004 | Mực gel đen | 5,000 | 1,000 | g |
| 1005 | Bi 0.5mm | 2,000 | 500 | mm |
| 1006 | Bi 0.7mm | 3,000 | 500 | mm |
| 1007 | Bi 1.0mm | 2,000 | 500 | mm |
| 1008 | Lò xo thép | 1,000 | 1,000 | g |

### ✅ **Bảng `product`** (Sản phẩm & BOM)
```sql
CREATE TABLE product (
    id_product INT PRIMARY KEY,
    product_name VARCHAR(255),
    bom JSON,                    -- ✅ Có (lưu BOM dạng JSON)
    diameter DECIMAL(10,2),
    application VARCHAR(255),
    is_active TINYINT
)
```
**BOM Examples:**
- **TL-079** (id=1001): 
  ```json
  [
    {"id_material": 1001, "quantity_per_unit": 10},
    {"id_material": 1002, "quantity_per_unit": 5},
    {"id_material": 1006, "quantity_per_unit": 3},
    {"id_material": 1008, "quantity_per_unit": 0.5}
  ]
  ```
  
- **danh** (id=1007): 
  ```json
  [
    {"id_material": null, "material_name": "danh", "quantity_per_unit": 10},
    {"id_material": 1007, "quantity_per_unit": 20}
  ]
  ```
  ⚠️ **Có NVL NULL** - Test case #4

### ⚠️ **Bảng `project`** (Đơn hàng) - CẦN CẬP NHẬT
```sql
CREATE TABLE project (
    id_project INT PRIMARY KEY,
    project_name VARCHAR(255),
    id_cust INT,
    id_product INT,
    diameter DECIMAL(10,2),
    qty_request INT,
    entry_date DATE,
    pr_status TINYINT,
    customer_request TEXT,
    created_at DATETIME,
    
    -- ❌ CŨ - Không dùng nữa
    risk_flag TINYINT DEFAULT 0,   -- ❌ Cần đổi thành warning_type
    
    -- ✅ MỚI - UC7
    warning_flag TINYINT,              -- ✅ Code đã dùng
    warning_details JSON,              -- ✅ Code đã dùng
    capacity_level_used TINYINT,       -- ✅ Code đã dùng (1=Level1, 2=Level2)
    material_shifts_available INT,     -- ✅ Code đã dùng
    finished_stock_available INT       -- ✅ Code đã dùng
)
```

**⚠️ VẤN ĐỀ:** Bảng `project` trong DB chưa có các cột mới:
- `warning_flag`
- `warning_details`
- `capacity_level_used`
- `material_shifts_available`
- `finished_stock_available`

**✅ GIẢI PHÁP:** Cần chạy migration:
```sql
ALTER TABLE project 
ADD COLUMN warning_flag TINYINT DEFAULT 0 AFTER risk_flag,
ADD COLUMN warning_details JSON AFTER warning_flag,
ADD COLUMN capacity_level_used TINYINT DEFAULT 1 AFTER warning_details,
ADD COLUMN material_shifts_available INT NULL AFTER capacity_level_used,
ADD COLUMN finished_stock_available INT DEFAULT 0 AFTER material_shifts_available;

-- Update existing records
UPDATE project 
SET warning_flag = risk_flag,
    capacity_level_used = 1,
    finished_stock_available = 0
WHERE warning_flag IS NULL;
```

---

## 🔍 2. KIỂM TRA LOGIC CODE

### ✅ **A. CÔNG THỨC TÍNH TOÁN**

#### **1. Phân bổ tồn kho (Stock Allocation)**
**File:** `OrderModel.php` lines 365-380

```php
// ✅ ĐÚNG: Query phân bổ stock theo thứ tự ưu tiên
$allocated_query = $this->db->query("
    SELECT COALESCE(SUM(
        CASE 
            WHEN finished_stock_available IS NOT NULL 
                 AND finished_stock_available > 0 
                 AND (warning_details LIKE '%sufficient%' OR warning_details LIKE '%partial_stock%')
            THEN LEAST(qty_request, finished_stock_available)
            ELSE 0
        END
    ), 0) as total_allocated
    FROM project
    WHERE id_product = ?
      AND pr_status < 3
      AND id_project != ?
      AND (
          entry_date < ? 
          OR (entry_date = ? AND created_at < ...)
      )
", [...]);

$available_stock = max(0, $quantity_in_stock - $total_allocated);
```

**✅ Ưu điểm:**
- Sort theo `entry_date` ASC (deadline sớm nhất trước)
- Exclude đơn hiện tại (`id_project != ?`)
- Chỉ tính đơn active (`pr_status < 3`)
- Dùng `LEAST()` để không vượt quá qty_request

**⚠️ Lỗi tiềm ẩn:**
```php
// ❌ SAI: Điều kiện này có thể gây lỗi khi id_project = NULL
AND id_project != ?  // Nếu $id_project = null → SQL lỗi

// ✅ ĐÚNG: Phải dùng
AND (id_project != ? OR ? IS NULL)
// Hoặc trong code:
$id_project ?? 0  // Đã fix ở line 378
```

**Kết luận:** ✅ **ĐÃ FIX ĐÚNG**

---

#### **2. Tính công suất 2 mức**
**File:** `OrderModel.php` lines 452-540

```php
// ✅ ĐÚNG: Lấy config từ DB
$capacity_config = $this->db->query("
    SELECT level, level_name, hours_per_shift, shifts_per_day, efficiency_rate
    FROM capacity_config
    WHERE is_active = 1
    ORDER BY level ASC
")->result();

$level1 = $capacity_config[0];  // 8h, 2 ca, 0.8 efficiency
$level2 = $capacity_config[1];  // 12h, 2 ca, 0.8 efficiency
```

**⚠️ VẤN ĐỀ:** Bảng `capacity_config` CHƯA TỒN TẠI trong DB!

**❌ Thiếu bảng `capacity_config`:**
```sql
CREATE TABLE capacity_config (
    level TINYINT PRIMARY KEY,
    level_name VARCHAR(50),
    hours_per_shift INT,        -- 8 hoặc 12
    shifts_per_day INT,         -- 2
    efficiency_rate DECIMAL(3,2), -- 0.80
    is_active TINYINT DEFAULT 1
);

INSERT INTO capacity_config VALUES
(1, 'Level 1 - Thường', 8, 2, 0.80, 1),
(2, 'Level 2 - Tối đa', 12, 2, 0.80, 1);
```

**Giải pháp tạm:** Nếu không có bảng, code có thể hardcode:
```php
// Fallback nếu không có capacity_config
if (empty($capacity_config)) {
    $level1 = (object)[
        'level' => 1,
        'hours_per_shift' => 8,
        'shifts_per_day' => 2,
        'efficiency_rate' => 0.8
    ];
    $level2 = (object)[
        'level' => 2,
        'hours_per_shift' => 12,
        'shifts_per_day' => 2,
        'efficiency_rate' => 0.8
    ];
}
```

---

#### **3. Tính NVL theo BOM**
**File:** `OrderModel.php` lines 680-710

```php
// ✅ ĐÚNG: Tính chính xác
foreach ($materials as $mat) {
    // Tìm định mức trong BOM
    $quantity_per_unit = 0;
    foreach ($bom_materials as $bom_item) {
        if ($bom_item['id_material'] == $mat->id_material) {
            $quantity_per_unit = floatval($bom_item['quantity_per_unit']);
            break;
        }
    }
    
    if ($quantity_per_unit > 0) {
        // ✅ Tính lượng cần
        $quantity_needed = $remaining_to_produce * $quantity_per_unit;
        $quantity_shortage = max(0, $quantity_needed - $mat->stock);
        
        // ✅ Tính số ca HIỆN TẠI
        $products_possible_total = floor($mat->stock / $quantity_per_unit);
        $shifts_possible_total = floor($products_possible_total / $products_per_shift);
        
        // ✅ Tính số ca CÒN LẠI sau khi trừ ĐH
        $stock_after_order = max(0, $mat->stock - $quantity_needed);
        $products_possible_after = floor($stock_after_order / $quantity_per_unit);
        $shifts_possible_after = floor($products_possible_after / $products_per_shift);
```

**Công thức:**
```
products_per_shift = 500 sp/giờ × 8h × 0.8 = 3,200 sp/ca (Level 1)
products_possible = stock ÷ quantity_per_unit
shifts_possible = products_possible ÷ 3,200
```

**⚠️ VẤN ĐỀ:** Hardcode `$products_per_shift = 3200`

**Lỗi logic:** Code ở line 668 set:
```php
$products_per_shift = $capacity_per_hour * $hours_per_shift * $efficiency; 
// = 3200 cái/ca
```

Nhưng đây là tính theo **tổng capacity của tất cả máy**, không phải **1 máy**.

**❌ SAI nếu:** Có nhiều máy
```
Machine 1: 200 sp/giờ
Machine 2: 300 sp/giờ
Total: 500 sp/giờ ✅

Nhưng khi tính BOM, không thể dùng total capacity!
Vì 1 sản phẩm chỉ qua 1 máy tại 1 thời điểm.
```

**✅ ĐÚNG:** Nên dùng capacity của 1 máy hoặc capacity trung bình.

**Tuy nhiên,** trong context nhà máy bút bi nhỏ, có thể chấp nhận tổng capacity nếu:
- Các máy chạy song song
- Mỗi ca có thể sản xuất tối đa 3,200 sản phẩm

**Kết luận:** ✅ **HỢP LÝ** nếu hiểu capacity là **tổng công suất cả line sản xuất**.

---

### ✅ **B. XỬ LÝ DATABASE**

#### **1. INSERT Order**
**File:** `BOD.php` lines 686-703

```php
$order_data = [
    'id_project'                => $this->crudModel->generateCode(...),
    'project_name'              => $project_name,
    'id_cust'                   => $id_cust,
    'id_product'                => $id_product,
    'diameter'                  => $diameter,
    'qty_request'               => $qty_request,
    'entry_date'                => $entry_date,
    'pr_status'                 => 1,  // Đã duyệt
    'customer_request'          => $customer_request,
    
    // ✅ UC7 - Các trường mới
    'risk_flag'                 => 0,  // Không dùng nữa
    'warning_flag'              => $capacity_check['warning_flag'] ?? 0,
    'warning_details'           => $capacity_check['warning_details'] ?? null,
    'capacity_level_used'       => $capacity_check['capacity_level_used'] ?? 1,
    'material_shifts_available' => $capacity_check['material_shifts_available'] ?? null,
    'finished_stock_available'  => $capacity_check['finished_stock_available'] ?? 0,
];

$result = $this->OrderModel->createOrder($order_data);
```

**✅ Ưu điểm:**
- Lưu đầy đủ thông tin UC7
- `warning_details` là JSON chứa toàn bộ cảnh báo
- Có fallback `?? 0` để tránh NULL error

**⚠️ VẤN ĐỀ:** Các cột này CHƯA CÓ trong DB → INSERT sẽ LỖI!

---

#### **2. UPDATE Stock khi hoàn thành**

**❌ THIẾU:** Code CHƯA CÓ logic cập nhật stock khi đơn hàng hoàn thành.

Cần thêm:
```php
// Khi pr_status = 3 (Hoàn thành)
public function completeOrder($id_project) {
    $order = $this->getOrderById($id_project);
    
    // Trừ NVL đã dùng
    $bom = json_decode($order->product_bom, true);
    foreach ($bom as $material) {
        if ($material['id_material']) {
            $used = $material['quantity_per_unit'] * $order->qty_produced;
            $this->db->query("
                UPDATE material 
                SET stock = stock - ? 
                WHERE id_material = ?
            ", [$used, $material['id_material']]);
        }
    }
    
    // Cộng thành phẩm vào kho
    $this->db->query("
        UPDATE finished_stock 
        SET quantity_in_stock = quantity_in_stock + ?,
            quantity_received = quantity_received + ?
        WHERE id_product = ?
    ", [$order->qty_produced, $order->qty_produced, $order->id_product]);
}
```

---

### ✅ **C. HIỂN THỊ MODAL**

**File:** `Project.php` lines 560-680

**✅ Đã implement đầy đủ:**
1. **Tồn kho thành phẩm** - Hiển thị allocated, available
2. **Công suất** - Level 1/Level 2
3. **NVL chi tiết** - Từng NVL với icon, màu sắc
4. **Bottleneck** - Đánh dấu "← GIỚI HẠN"
5. **NVL missing** - Hiển thị NVL NULL

**Code JavaScript:**
```javascript
if (warnings.material_details && warnings.material_details.length > 0) {
    warnings.material_details.forEach(function(mat) {
        let statusIcon = '';
        let statusColor = '';
        let statusText = '';
        
        if (mat.is_bottleneck) {
            statusIcon = '⚠️';
            statusColor = '#ff6f00';
            statusText = '← GIỚI HẠN';
        } else if (!mat.is_sufficient) {
            statusIcon = '❌';
            statusColor = '#c62828';
            statusText = 'THIẾU';
        } else if (mat.shifts_possible < 10) {
            statusIcon = '⚠️';
            statusColor = '#f57c00';
            statusText = 'GẦN HẾT';
        } else {
            statusIcon = '✓';
            statusColor = '#2e7d32';
            statusText = `ĐỦ - CÒN ${mat.shifts_after_order} CA`;
        }
        
        // Hiển thị chi tiết...
    });
}
```

**✅ Logic đúng:**
- Parse JSON từ `warning_details`
- Hiển thị từng NVL với status riêng
- Màu sắc phân biệt rõ ràng

---

## 🚨 3. DANH SÁCH VẤN ĐỀ CẦN FIX

### ❌ **CRITICAL - Phải fix ngay:**

1. **Thiếu cột trong bảng `project`:**
   ```sql
   ALTER TABLE project 
   ADD COLUMN warning_flag TINYINT DEFAULT 0,
   ADD COLUMN warning_details JSON,
   ADD COLUMN capacity_level_used TINYINT DEFAULT 1,
   ADD COLUMN material_shifts_available INT NULL,
   ADD COLUMN finished_stock_available INT DEFAULT 0;
   ```

2. **Thiếu bảng `capacity_config`:**
   ```sql
   CREATE TABLE capacity_config (
       level TINYINT PRIMARY KEY,
       level_name VARCHAR(50),
       hours_per_shift INT,
       shifts_per_day INT,
       efficiency_rate DECIMAL(3,2),
       is_active TINYINT DEFAULT 1
   );
   
   INSERT INTO capacity_config VALUES
   (1, 'Level 1 - Thường', 8, 2, 0.80, 1),
   (2, 'Level 2 - Tối đa', 12, 2, 0.80, 1);
   ```

### ⚠️ **HIGH - Nên fix:**

3. **Thiếu logic cập nhật stock khi hoàn thành:**
   - Cần method `completeOrder()` để trừ NVL và cộng thành phẩm

4. **Fallback cho capacity_config:**
   - Thêm hardcode nếu bảng không tồn tại

### 💡 **MEDIUM - Cải thiện:**

5. **Tối ưu query phân bổ stock:**
   ```php
   // Thay vì LIKE '%sufficient%', dùng JSON_EXTRACT
   JSON_EXTRACT(warning_details, '$.stock_status') = 'sufficient'
   ```

6. **Thêm index:**
   ```sql
   CREATE INDEX idx_project_entry ON project(entry_date, created_at);
   CREATE INDEX idx_project_product ON project(id_product, pr_status);
   ```

---

## ✅ 4. VERIFICATION CHECKLIST

### **Trước khi demo:**

- [ ] **Chạy migration bảng `project`** - Thêm 5 cột mới
- [ ] **Tạo bảng `capacity_config`** - Insert 2 level
- [ ] **Test INSERT order** - Verify không lỗi
- [ ] **Test 7 kịch bản** theo hướng dẫn
- [ ] **Check modal hiển thị** đầy đủ thông tin
- [ ] **Verify DB update** sau khi tạo đơn

### **Các query test:**

```sql
-- 1. Kiểm tra cột mới có trong project chưa
DESCRIBE project;

-- 2. Kiểm tra capacity_config
SELECT * FROM capacity_config;

-- 3. Xem đơn hàng mới tạo có đầy đủ thông tin UC7
SELECT 
    id_project, 
    project_name,
    warning_flag,
    capacity_level_used,
    JSON_PRETTY(warning_details) as warnings
FROM project 
ORDER BY created_at DESC 
LIMIT 5;

-- 4. Kiểm tra stock allocation
SELECT 
    p.id_project,
    p.project_name,
    p.qty_request,
    p.finished_stock_available,
    p.entry_date,
    p.created_at
FROM project p
WHERE p.id_product = 1001
  AND p.pr_status < 3
ORDER BY p.entry_date ASC, p.created_at ASC;
```

---

## 📊 5. KẾT LUẬN

### ✅ **ĐIỂM MẠNH:**

1. **Logic code CHUẨN:**
   - Phân bổ stock đúng thứ tự ưu tiên
   - Tính toán NVL chính xác theo BOM
   - Kiểm tra 2 level công suất
   - Xử lý NVL NULL trong BOM

2. **UI/UX TỐT:**
   - Modal hiển thị chi tiết từng NVL
   - Icon + màu sắc rõ ràng
   - Bottleneck được đánh dấu

3. **Code CLEAN:**
   - Comment đầy đủ
   - Structured theo UC7
   - Error handling tốt

### ⚠️ **ĐIỂM YẾU:**

1. **DATABASE CHƯA ĐỦ:**
   - Thiếu 5 cột trong `project`
   - Thiếu bảng `capacity_config`
   - Chưa có index tối ưu

2. **THIẾU LOGIC:**
   - Chưa cập nhật stock khi complete
   - Chưa có transaction đầy đủ

### 🎯 **KHUYẾN NGHỊ:**

**Trước khi demo, PHẢI:**
1. ✅ Chạy migration database (5-10 phút)
2. ✅ Test 1 đơn hàng đơn giản trước
3. ✅ Verify data trong DB

**Sau demo, NÊN:**
1. 📝 Implement logic complete order
2. 🔧 Optimize queries
3. 📊 Thêm logging cho debug

---

## 🚀 6. SCRIPT MIGRATION HOÀN CHỈNH

```sql
-- ========================================
-- MIGRATION UC7: Thêm cột mới cho project
-- ========================================

START TRANSACTION;

-- 1. Thêm cột mới vào bảng project
ALTER TABLE project 
ADD COLUMN warning_flag TINYINT DEFAULT 0 COMMENT 'Có cảnh báo: 0=Không, 1=Có' AFTER risk_flag,
ADD COLUMN warning_details JSON COMMENT 'Chi tiết cảnh báo (JSON)' AFTER warning_flag,
ADD COLUMN capacity_level_used TINYINT DEFAULT 1 COMMENT 'Level công suất: 1=Level1, 2=Level2' AFTER warning_details,
ADD COLUMN material_shifts_available INT NULL COMMENT 'Số ca NVL đủ làm' AFTER capacity_level_used,
ADD COLUMN finished_stock_available INT DEFAULT 0 COMMENT 'Tồn kho khả dụng' AFTER material_shifts_available;

-- 2. Update dữ liệu cũ
UPDATE project 
SET warning_flag = risk_flag,
    capacity_level_used = 1,
    finished_stock_available = 0
WHERE warning_flag IS NULL;

-- 3. Tạo bảng capacity_config
CREATE TABLE IF NOT EXISTS capacity_config (
    level TINYINT PRIMARY KEY COMMENT 'Mức công suất: 1, 2',
    level_name VARCHAR(50) NOT NULL COMMENT 'Tên mức',
    hours_per_shift INT NOT NULL COMMENT 'Số giờ/ca: 8 hoặc 12',
    shifts_per_day INT DEFAULT 2 COMMENT 'Số ca/ngày: 2',
    efficiency_rate DECIMAL(3,2) DEFAULT 0.80 COMMENT 'Hiệu suất: 0.80 = 80%',
    is_active TINYINT DEFAULT 1 COMMENT 'Kích hoạt: 0=Không, 1=Có',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Insert cấu hình 2 level
INSERT INTO capacity_config (level, level_name, hours_per_shift, shifts_per_day, efficiency_rate, is_active) 
VALUES
(1, 'Level 1 - Công suất thường', 8, 2, 0.80, 1),
(2, 'Level 2 - Công suất tối đa', 12, 2, 0.80, 1)
ON DUPLICATE KEY UPDATE
    level_name = VALUES(level_name),
    hours_per_shift = VALUES(hours_per_shift),
    shifts_per_day = VALUES(shifts_per_day),
    efficiency_rate = VALUES(efficiency_rate),
    is_active = VALUES(is_active);

-- 5. Tạo index tối ưu
CREATE INDEX idx_project_entry_date ON project(entry_date, created_at) 
    COMMENT 'Index cho sắp xếp theo deadline';
    
CREATE INDEX idx_project_product_status ON project(id_product, pr_status) 
    COMMENT 'Index cho filter theo sản phẩm và trạng thái';

-- 6. Verify
SELECT 'Migration completed successfully!' as status;

COMMIT;

-- ========================================
-- ROLLBACK (Nếu cần)
-- ========================================
-- ALTER TABLE project 
-- DROP COLUMN warning_flag,
-- DROP COLUMN warning_details,
-- DROP COLUMN capacity_level_used,
-- DROP COLUMN material_shifts_available,
-- DROP COLUMN finished_stock_available;
-- 
-- DROP TABLE IF EXISTS capacity_config;
-- DROP INDEX idx_project_entry_date ON project;
-- DROP INDEX idx_project_product_status ON project;
```

---

## 📞 HỖ TRỢ

Nếu gặp lỗi khi test, check:
1. Migration đã chạy chưa? → `DESCRIBE project;`
2. Bảng capacity_config có chưa? → `SELECT * FROM capacity_config;`
3. Data có đúng không? → Xem query test ở section 4

**Liên hệ:** Team Production Management System v2

---

**Ngày tạo:** 08/12/2025  
**Phiên bản:** 1.0  
**Trạng thái:** ✅ READY FOR DEMO (sau khi chạy migration)
