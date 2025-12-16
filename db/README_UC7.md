# 📦 UC7: HỆ THỐNG CẢNH BÁO ĐƠN HÀNG CHI TIẾT

## 🎯 TỔNG QUAN

**Use Case 7 (UC7)** nâng cấp hệ thống kiểm tra đơn hàng từ đơn giản ("OK" hoặc "Không OK") sang **phân tích chi tiết 3 yếu tố:**

1. **📦 Tồn kho thành phẩm** - Dùng stock trước, sản xuất sau (theo ưu tiên deadline)
2. **⚙️ Công suất sản xuất** - Level 1 (8h/ca) hoặc Level 2 (12h/ca tăng ca)
3. **🧪 Nguyên vật liệu** - TỪNG NVL: đủ/thiếu bao nhiêu, còn làm được bao nhiêu ca

**Kết quả:** Người dùng biết chính xác:
- ✅ NVL nào ĐỦ, còn làm được bao nhiêu ca
- ❌ NVL nào THIẾU, thiếu bao nhiêu gram/kg/cái
- ⚠️ NVL nào là BOTTLENECK (giới hạn sản xuất)
- 📊 Tất cả data lưu vào DB để report/export

---

## 📂 CẤU TRÚC FILE

```
production-management-v2/
├── db/
│   ├── migrations/
│   │   └── 007_add_uc7_warning_system.sql         ← Migration chính (CHẠY ĐẦU TIÊN!)
│   ├── BAO_CAO_KIEM_TRA_SOURCE_CODE.md            ← Code audit report
│   ├── CHECKLIST_DEMO_UC7.md                      ← Hướng dẫn demo chi tiết
│   ├── reset_test_data_uc7.sql                    ← Script reset để test lại
│   └── README_UC7.md                              ← File này
│
├── application/
│   ├── models/
│   │   ├── OrderModel.php                         ← checkCapacity() - CORE LOGIC
│   │   └── ProductModel.php                       ← getProductById() - BOM enrichment
│   ├── controllers/
│   │   └── BOD.php                                ← addProject(), updateProject()
│   └── views/
│       └── bod/project/
│           └── Project.php                        ← Modal hiển thị chi tiết
│
└── docs/
    └── HUONG_DAN_TEST_TRANG_THAI_DON_HANG.md      ← Test scenarios
```

---

## 🚀 CÁCH BẮT ĐẦU (QUICKSTART)

### Bước 1: Chạy Migration Database (BẮT BUỘC!)
```bash
# Windows Command Prompt
cd d:\PHAT TRIEN UNG DUNG\production-management-v2

# Backup trước
mysqldump -u root -p db_production > db\backups\backup_before_uc7.sql

# Chạy migration
mysql -u root -p db_production < db\migrations\007_add_uc7_warning_system.sql
```

**Verify:**
```sql
-- Trong MySQL:
USE db_production;

-- Kiểm tra cột mới
DESCRIBE project;
-- Phải thấy: warning_flag, warning_details, capacity_level_used, ...

-- Kiểm tra bảng mới
SELECT * FROM capacity_config;
-- Phải thấy: 2 rows (Level 1 và Level 2)
```

### Bước 2: Test Đơn Giản
1. Login vào hệ thống (BOD account)
2. Vào **BOD → Đơn hàng**
3. Click **+ Thêm đơn hàng**
4. Nhập:
   - Sản phẩm: TL-079
   - Số lượng: 50 cái
   - Deadline: 2025-12-20
5. Click **Lưu**
6. Click **icon ⚠️** để xem chi tiết

**Kỳ vọng:** Modal hiển thị đầy đủ thông tin stock, capacity, NVL từng loại.

### Bước 3: Test Đầy Đủ
Làm theo **CHECKLIST_DEMO_UC7.md** - có 7 test cases chi tiết.

---

## 📊 CÔNG THỨC TÍNH TOÁN

### 1. Công suất sản xuất
```
Capacity 1 máy = 500 sản phẩm/giờ

Level 1 (bình thường):
- 8 giờ/ca × 2 ca/ngày = 16 giờ/ngày
- Hiệu suất: 80%
- Capacity/ca = 500 × 8 × 0.8 = 3,200 sản phẩm/ca
- Capacity/ngày = 3,200 × 2 = 6,400 sản phẩm/ngày

Level 2 (tăng ca):
- 12 giờ/ca × 2 ca/ngày = 24 giờ/ngày
- Hiệu suất: 80%
- Capacity/ca = 500 × 12 × 0.8 = 4,800 sản phẩm/ca
- Capacity/ngày = 4,800 × 2 = 9,600 sản phẩm/ngày
```

### 2. Phân bổ tồn kho
```
Thứ tự ưu tiên:
1. Entry date (deadline sớm hơn)
2. Created_at (tạo trước)

Available stock = Total stock - Allocated to higher priority orders

VD:
- Stock: 35 cái
- Order A (deadline 10/12): 20 cái → được 20 cái
- Order B (deadline 15/12): 20 cái → được 15 cái, cần sản xuất 5 cái
```

### 3. Nguyên vật liệu
```
Quantity needed = Qty to produce × Quantity per unit (from BOM)

Products possible = Stock ÷ Quantity per unit
Shifts possible = Products possible ÷ Products per shift
Days possible = Shifts possible ÷ 2 ca/ngày

Shortage = Quantity needed - Stock (nếu > 0)

Stock after order = Stock - Quantity needed
Shifts after order = (Stock after order ÷ Quantity per unit) ÷ Products per shift
```

**VD:**
```
BOM: TL-079 cần 10g Test Matereal/sản phẩm
Stock hiện tại: 5,000g
Đơn hàng: 50 cái

Quantity needed = 50 × 10 = 500g
Products possible = 5000 ÷ 10 = 500 sản phẩm
Shifts possible = 500 ÷ 3200 = 0.15 ca ≈ 0 ca (làm tròn xuống)
Stock after order = 5000 - 500 = 4,500g
Shifts after order = 4500 ÷ 10 ÷ 3200 = 0.14 ca ≈ 0 ca

→ Hiển thị: "✓ ĐỦ - CÒN 0 CA"
```

---

## 🗄️ DATABASE SCHEMA

### Bảng `project` (Updated)
```sql
ALTER TABLE project 
ADD COLUMN warning_flag TINYINT DEFAULT 0,          -- 0=OK, 1=Có cảnh báo
ADD COLUMN warning_details JSON,                    -- Chi tiết cảnh báo
ADD COLUMN capacity_level_used TINYINT DEFAULT 1,   -- 1=Level1, 2=Level2
ADD COLUMN material_shifts_available INT NULL,      -- Số ca NVL đủ làm
ADD COLUMN finished_stock_available INT DEFAULT 0;  -- Stock khả dụng
```

### Bảng `capacity_config` (New)
```sql
CREATE TABLE capacity_config (
    level TINYINT PRIMARY KEY,              -- 1, 2
    level_name VARCHAR(50),                 -- "Level 1 - Công suất thường"
    hours_per_shift INT,                    -- 8 hoặc 12
    shifts_per_day INT DEFAULT 2,           -- 2
    efficiency_rate DECIMAL(3,2) DEFAULT 0.80,  -- 0.80 = 80%
    is_active TINYINT DEFAULT 1
);
```

### JSON Structure: warning_details
```json
{
  "stock_status": "partial",
  "capacity_warning": "Vượt Level 1, dùng Level 2",
  "material_warning": "Thiếu 1 NVL",
  "deadline_warning": "Chỉ còn 2 ngày đến deadline",
  "material_details": [
    {
      "id_material": 1001,
      "material_name": "Test Matereal",
      "stock": 5000,
      "uom": "g",
      "quantity_per_unit": 10,
      "quantity_needed": 500,
      "quantity_shortage": 0,
      "stock_after_order": 4500,
      "products_possible": 500,
      "shifts_possible": 0,
      "shifts_after_order": 0,
      "days_possible": 0,
      "is_sufficient": true,
      "is_bottleneck": false
    }
  ],
  "bottleneck_material": "Lò xo thép",
  "missing_materials_list": []
}
```

---

## 🔍 FLOW HOẠT ĐỘNG

### 1. Khi tạo/sửa đơn hàng:
```
User nhập form
    ↓
BOD.php → addProject() / updateProject()
    ↓
Validation (product, qty, date)
    ↓
OrderModel.php → checkCapacity(id_product, qty, entry_date, id_project)
    |
    ├─→ Query stock allocation theo thứ tự ưu tiên
    ├─→ Calculate capacity (Level 1 → Level 2)
    ├─→ Get BOM from product.bom (JSON)
    ├─→ Check each material:
    |       - Stock vs needed
    |       - Shifts possible (current vs after order)
    |       - Mark bottleneck
    ├─→ Handle NULL materials in BOM
    └─→ Return: feasible, warning_flag, warning_details (JSON), capacity_level_used, ...
    ↓
BOD.php → Save to DB:
    - INSERT/UPDATE project table
    - warning_flag = 0/1
    - warning_details = JSON string
    - capacity_level_used = 1/2
    ↓
Redirect với flashdata:
    - success_js (không cảnh báo)
    - warning_js (có cảnh báo)
    - error_js (không feasible)
    ↓
Project.php → Hiển thị toast notification
    ↓
User click icon ⚠️ → Modal popup
    ↓
Parse warning_details JSON → Hiển thị chi tiết:
    - 📦 Stock section
    - ⚙️ Capacity section
    - 🧪 Material details (per-material boxes)
    - ⚠️ Missing materials
```

### 2. Khi xem chi tiết đơn hàng:
```
Click icon ⚠️
    ↓
JavaScript parse warning_details JSON
    ↓
Loop through material_details array
    ↓
Render boxes with:
    - Icon: ✓ (đủ), ❌ (thiếu), ⚠️ (gần hết/bottleneck)
    - Color: Green (đủ), Red (thiếu), Orange (cảnh báo)
    - Text: "ĐỦ - CÒN X CA" hoặc "THIẾU X g"
    - Detail: Stock, needed, after order, shifts
```

---

## 🧪 7 TEST SCENARIOS

| # | Tên Test | Mục tiêu | Expected |
|---|----------|----------|----------|
| 1 | OK - Bình thường | Đủ mọi điều kiện | ✅ Toast xanh, modal hiển thị đủ |
| 2 | Warning - Vượt Level 1 | Trigger capacity warning | ⚠️ Toast vàng, capacity_level_used=2 |
| 3 | Shortage - Thiếu NVL | Material insufficient | ❌ Hiển thị "THIẾU X g" |
| 4 | Missing BOM | NULL material trong BOM | ⚠️ Section "NVL THIẾU TRONG KHO" |
| 5 | Rejected - Vượt Level 2 | Không feasible | 🚫 Toast đỏ, đơn KHÔNG TẠO |
| 6 | Deadline - Gần hết hạn | < 3 ngày | ⏰ Warning "Chỉ còn X ngày" |
| 7 | Allocation - Ưu tiên | Stock allocation | 🚀 Đơn sớm hơn được stock trước |

Chi tiết: Xem **CHECKLIST_DEMO_UC7.md**

---

## 📝 CODE QUAN TRỌNG

### OrderModel.php - checkCapacity()
**File:** `application/models/OrderModel.php`  
**Lines:** 343-850  
**Chức năng:**
- Tính stock allocation theo priority
- Check capacity Level 1 → Level 2
- Calculate material details per-material
- Mark bottleneck material
- Handle NULL materials

**Key variables:**
- `$products_per_shift` = 3200 (Level 1) hoặc 4800 (Level 2)
- `$material_details[]` = Array chứa chi tiết từng NVL
- `$bottleneck_material` = NVL giới hạn nhất

### BOD.php - addProject() / updateProject()
**File:** `application/controllers/BOD.php`  
**Lines:** 625-750 (addProject), 778-920 (updateProject)  
**Chức năng:**
- Validate input
- Call `checkCapacity()`
- Save UC7 fields to DB:
  - `warning_flag`
  - `warning_details` (JSON)
  - `capacity_level_used`
  - `material_shifts_available`
  - `finished_stock_available`
- Show appropriate toast (success/warning/error)

### Project.php - Modal Display
**File:** `application/views/bod/project/Project.php`  
**Lines:** 560-680  
**Chức năng:**
- Parse `warning_details` JSON
- Render 4 sections:
  1. Stock (allocated, available, remaining)
  2. Capacity (Level 1/2)
  3. Material details (per-material boxes with icon + color)
  4. Missing materials (NULL in BOM)

---

## ⚠️ TROUBLESHOOTING

### 1. Error: Column 'warning_flag' doesn't exist
**Fix:** Chưa chạy migration
```bash
mysql -u root -p db_production < db\migrations\007_add_uc7_warning_system.sql
```

### 2. Toast không hiện
**Fix:** Check console (F12) xem lỗi JS  
**Kiểm tra:** File `Project.php` có include jQuery chưa

### 3. Modal không hiển thị material_details
**Fix:** Check DB xem `warning_details` có data không
```sql
SELECT JSON_PRETTY(warning_details) FROM project WHERE id_project = 'PJ-XXX';
```

### 4. capacity_config not found
**Fix:** Tạo bảng
```sql
CREATE TABLE capacity_config (...);  -- Xem trong migration
```

### 5. Stock allocation sai
**Fix:** Có thể có đơn cũ, xóa hoặc set completed
```sql
-- Xóa đơn test
DELETE FROM project WHERE project_name LIKE '%test%';

-- Hoặc reset
SOURCE db/reset_test_data_uc7.sql;
```

---

## 📚 TÀI LIỆU THAM KHẢO

1. **BAO_CAO_KIEM_TRA_SOURCE_CODE.md**
   - Code audit chi tiết
   - Kiểm tra công thức
   - Database schema requirements
   - Migration script

2. **CHECKLIST_DEMO_UC7.md**
   - Hướng dẫn demo từng bước
   - 7 test cases chi tiết
   - Scripts demo cho người thuyết trình
   - Troubleshooting

3. **HUONG_DAN_TEST_TRANG_THAI_DON_HANG.md**
   - Test scenarios
   - Expected results
   - Verify steps

4. **reset_test_data_uc7.sql**
   - Reset stock về ban đầu
   - Xóa đơn test
   - Sẵn sàng test lại

---

## 📞 HỖ TRỢ

**Nếu gặp vấn đề:**
1. Check **Troubleshooting** section ở trên
2. Xem **BAO_CAO_KIEM_TRA_SOURCE_CODE.md** section 3
3. Chạy query verify:
   ```sql
   DESCRIBE project;
   SELECT * FROM capacity_config;
   SELECT JSON_PRETTY(warning_details) FROM project ORDER BY created_at DESC LIMIT 1;
   ```

**Best practices:**
- Luôn backup DB trước khi chạy migration
- Test trên môi trường dev trước
- Verify data sau mỗi bước
- Dùng `reset_test_data_uc7.sql` để test lại

---

## ✅ READY TO DEMO?

**Final checklist:**
- [ ] Migration đã chạy (verify: `DESCRIBE project;`)
- [ ] capacity_config có 2 rows
- [ ] Web server đang chạy
- [ ] Test 1 đơn đơn giản OK
- [ ] Modal hiển thị đúng

**→ SẴN SÀNG! 🚀**

---

**Ngày tạo:** 08/12/2025  
**Version:** 1.0  
**Team:** Production Management System v2
