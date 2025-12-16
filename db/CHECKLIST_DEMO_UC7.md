# ✅ CHECKLIST DEMO UC7 - HỆ THỐNG CẢNH BÁO ĐƠN HÀNG

## 📋 CHUẨN BỊ TRƯỚC KHI DEMO (30 phút)

### 1. ✅ Chạy Migration Database
```bash
# Bước 1: Backup database hiện tại
cd d:\PHAT TRIEN UNG DUNG\production-management-v2
mysqldump -u root -p db_production > db\backups\backup_before_uc7_%date:~0,4%%date:~5,2%%date:~8,2%.sql

# Bước 2: Chạy migration
mysql -u root -p db_production < db\migrations\007_add_uc7_warning_system.sql

# Bước 3: Verify
mysql -u root -p db_production
```

**Kiểm tra trong MySQL:**
```sql
-- 1. Kiểm tra cột mới trong project
DESCRIBE project;
-- Phải thấy: warning_flag, warning_details, capacity_level_used, 
--            material_shifts_available, finished_stock_available

-- 2. Kiểm tra bảng capacity_config
SELECT * FROM capacity_config;
-- Phải thấy 2 rows: Level 1 (8h) và Level 2 (12h)

-- 3. Kiểm tra index
SHOW INDEX FROM project WHERE Key_name LIKE 'idx_project_%';
-- Phải thấy: idx_project_entry_date, idx_project_product_status, idx_project_warning
```

### 2. ✅ Verify Source Code
```bash
# Kiểm tra các file quan trọng đã update chưa
cd application

# OrderModel.php - checkCapacity() phải có material_details
grep -n "material_details" models/OrderModel.php

# BOD.php - addProject() và updateProject() phải save UC7 fields
grep -n "warning_flag\|warning_details\|capacity_level_used" controllers/BOD.php

# ProductModel.php - getProductById() phải enrichment với material JOIN
grep -n "material_name\|uom" models/ProductModel.php

# Project.php (view) - Modal phải hiển thị material_details
grep -n "material_details" views/bod/project/Project.php
```

### 3. ✅ Test Môi Trường Development
```bash
# Start XAMPP/WAMP/MAMP
# Truy cập: http://localhost/production-management-v2
# Login với account BOD:
#   Username: bod_user
#   Password: [your_password]
```

**Kiểm tra nhanh:**
- [ ] Trang BOD/Project load được
- [ ] Modal thêm đơn hàng mở được
- [ ] Dropdown sản phẩm hiển thị đúng
- [ ] Không có lỗi JavaScript console

---

## 🧪 7 TEST CASES - CHẠY THEO THỨ TỰ

### ✅ Test 1: Đơn hàng bình thường (OK)
**Mục tiêu:** Đủ mọi điều kiện, không có cảnh báo

**Input:**
```
Product:  TL-079 (id=1001)
Quantity: 50 cái
Deadline: 2025-12-20 (còn 12 ngày)
Customer: ABC Corp
```

**Expected Output:**
- ✅ Toast màu XANH: "Đơn hàng đã được tạo thành công"
- ✅ Modal chi tiết hiển thị:
  - 📦 Tồn kho: 35 cái (đủ), còn 15 cái cần sản xuất
  - ⚙️ Công suất: Level 1 (đủ)
  - 🧪 NVL: 
    - Test Matereal: ✓ ĐỦ - CÒN 142 CA
    - Nhựa ABS: ✓ ĐỦ - CÒN 284 CA
    - Bi 0.7mm: ✓ ĐỦ - CÒN 284 CA
    - Lò xo thép: ✓ ĐỦ - CÒN 284 CA

**Verify Database:**
```sql
SELECT 
    id_project, 
    project_name, 
    qty_request,
    warning_flag,
    capacity_level_used,
    finished_stock_available,
    JSON_PRETTY(warning_details) as warnings
FROM project 
WHERE project_name LIKE '%test%'
ORDER BY created_at DESC 
LIMIT 1;
```

**Expected DB:**
- warning_flag = 0
- capacity_level_used = 1
- finished_stock_available = 35
- warning_details = JSON with stock_status: "sufficient"

---

### 🔴 Test 2: Vượt Level 1, dùng Level 2
**Mục tiêu:** Trigger cảnh báo công suất

**Setup:**
```sql
-- Giảm stock để trigger capacity warning
UPDATE finished_stock SET quantity_in_stock = 0 WHERE id_product = 1001;
```

**Input:**
```
Product:  TL-079
Quantity: 60,000 cái  (Vượt Level 1: 3200 × 12 ngày = 38,400)
Deadline: 2025-12-20 (12 ngày)
Customer: Big Corp
```

**Expected Output:**
- ⚠️ Toast màu VÀNG: "Đơn hàng đã được tạo với cảnh báo"
- Modal chi tiết:
  - ⚙️ Công suất: **Level 2 - Cần tăng ca**
  - "⚠️ Vượt công suất Level 1, cần dùng Level 2 (12 giờ/ca)"
  - Số ca cần: ~13 ca Level 2 (60,000 ÷ 4,800)

**Verify Database:**
- warning_flag = 1
- capacity_level_used = **2** ← Quan trọng!
- warning_details chứa: "capacity_warning": "Vượt Level 1..."

**Cleanup:**
```sql
UPDATE finished_stock SET quantity_in_stock = 35 WHERE id_product = 1001;
```

---

### ❌ Test 3: Thiếu 1 NVL
**Mục tiêu:** Material shortage warning

**Setup:**
```sql
-- Giảm stock của 1 NVL
UPDATE material SET stock = 100 WHERE id_material = 1008;  -- Lò xo thép
```

**Input:**
```
Product:  TL-079
Quantity: 100 cái (cần 50g lò xo, nhưng chỉ còn 100g)
Deadline: 2025-12-15
Customer: Test Corp
```

**Expected Output:**
- ⚠️ Toast màu VÀNG với cảnh báo thiếu NVL
- Modal chi tiết:
  - Test Matereal: ✓ ĐỦ
  - Nhựa ABS: ✓ ĐỦ
  - Bi 0.7mm: ✓ ĐỦ
  - **Lò xo thép: ❌ THIẾU ← GIỚI HẠN**
    - Tồn kho: 100 g
    - Cần: 50 g (100 × 0.5)
    - **THIẾU: 0 g** (vừa đủ nhưng không còn cho đơn sau)
    - Hiện tại đủ: 200 sản phẩm = 0 ca
    - Sau khi trừ: 0 ca

**Verify Database:**
- warning_flag = 1
- warning_details chứa:
  - "material_warning": "Thiếu 1 NVL"
  - "bottleneck_material": "Lò xo thép"
  - material_details[3].is_bottleneck = true

**Cleanup:**
```sql
UPDATE material SET stock = 1000 WHERE id_material = 1008;
```

---

### ⚠️ Test 4: BOM có NVL NULL
**Mục tiêu:** Handle missing materials

**Input:**
```
Product:  danh (id=1007) ← Có NVL NULL trong BOM
Quantity: 10 cái
Deadline: 2025-12-15
```

**Expected Output:**
- ⚠️ Toast cảnh báo: "BOM thiếu 1 NVL"
- Modal chi tiết:
  - **❌ NGUYÊN VẬT LIỆU THIẾU TRONG KHO:**
    - "danh" - 10 g/sản phẩm
  - **📦 NGUYÊN VẬT LIỆU KHẢ DỤNG:**
    - Bi 1.0mm: ✓ ĐỦ

**Verify Database:**
- warning_flag = 1
- warning_details chứa:
  - "missing_materials_list": ["danh"]

---

### ❌ Test 5: Vượt cả Level 2 → Từ chối
**Mục tiêu:** Feasibility check

**Setup:**
```sql
UPDATE finished_stock SET quantity_in_stock = 0 WHERE id_product = 1001;
```

**Input:**
```
Product:  TL-079
Quantity: 2,000,000 cái  (Vượt cả Level 2: 4800 × 12 × 2 = 115,200)
Deadline: 2025-12-20 (12 ngày)
```

**Expected Output:**
- 🚫 Toast màu ĐỎ: "Không thể tạo đơn hàng"
- Popup error:
  - "❌ Vượt cả Level 2! Không đủ công suất."
  - "Cần: ~347 ca Level 2"
  - "Có: 24 ca Level 2 (12 ngày)"
- **Đơn hàng KHÔNG ĐƯỢC TẠO**

**Verify Database:**
```sql
-- Không có đơn hàng mới
SELECT COUNT(*) FROM project 
WHERE project_name LIKE '%2000000%';  -- Phải = 0
```

**Cleanup:**
```sql
UPDATE finished_stock SET quantity_in_stock = 35 WHERE id_product = 1001;
```

---

### ⏰ Test 6: Deadline gần (< 3 ngày)
**Mục tiêu:** Deadline warning

**Input:**
```
Product:  TL-079
Quantity: 50 cái
Deadline: 2025-12-10  ← Chỉ còn 2 ngày!
```

**Expected Output:**
- ⚠️ Toast cảnh báo: "Deadline gần"
- Modal chi tiết:
  - "⏰ Chỉ còn 2 ngày đến deadline! Cần ưu tiên sản xuất."

**Verify Database:**
- warning_flag = 1
- warning_details chứa: "deadline_warning": "Chỉ còn 2 ngày..."

---

### 🚀 Test 7: Phân bổ stock theo thứ tự ưu tiên
**Mục tiêu:** Stock allocation priority

**Setup:**
```sql
-- Đảm bảo chỉ có 35 cái trong kho
UPDATE finished_stock SET quantity_in_stock = 35 WHERE id_product = 1001;

-- Xóa đơn cũ
DELETE FROM project WHERE id_product = 1001;
```

**Bước 1: Tạo đơn A (deadline sớm)**
```
Product:  TL-079
Quantity: 20 cái
Deadline: 2025-12-10  ← SỚM
Customer: Customer A
```

**Expected:** Được phân 20 cái từ stock → Còn 15 cái

**Bước 2: Tạo đơn B (deadline muộn hơn)**
```
Product:  TL-079
Quantity: 20 cái
Deadline: 2025-12-15  ← MUỘN
Customer: Customer B
```

**Expected:** Được phân 15 cái từ stock, cần sản xuất 5 cái

**Verify Database:**
```sql
SELECT 
    id_project,
    project_name,
    qty_request,
    entry_date,
    finished_stock_available,
    created_at,
    JSON_EXTRACT(warning_details, '$.stock_status') as stock_status
FROM project 
WHERE id_product = 1001
ORDER BY entry_date ASC, created_at ASC;
```

**Expected:**
| project_name | qty_request | entry_date | finished_stock_available | stock_status |
|--------------|-------------|------------|--------------------------|--------------|
| Order A      | 20          | 2025-12-10 | 20                       | "sufficient" |
| Order B      | 20          | 2025-12-15 | 15                       | "partial"    |

**Bước 3: Sửa đơn A thành 10 cái**
- Click Edit đơn A
- Đổi qty_request = 10
- Save

**Expected sau khi sửa:**
| project_name | qty_request | finished_stock_available |
|--------------|-------------|--------------------------|
| Order A      | **10**      | **10** ← Giảm             |
| Order B      | 20          | **20** ← Tăng lên vì A giải phóng 10 cái! |

```sql
-- Verify B được tăng stock
SELECT 
    id_project,
    project_name,
    finished_stock_available,
    JSON_EXTRACT(warning_details, '$.stock_status') as stock_status
FROM project 
WHERE project_name LIKE '%Order B%';
-- Phải thấy: finished_stock_available = 20, stock_status = "sufficient"
```

---

## 📊 CHECKLIST TRONG KHI DEMO

### Trước khi bắt đầu:
- [ ] Mở 2 tab: 1 cho Web App, 1 cho MySQL Workbench
- [ ] Chuẩn bị data: Chạy script reset nếu cần
- [ ] Giải thích tổng quan: "Hệ thống UC7 kiểm tra 3 yếu tố: Stock, Capacity, Material"

### Khi demo từng test case:
- [ ] **Giải thích input:** "Tôi sẽ tạo đơn hàng với..."
- [ ] **Thao tác trên UI:** Nhập liệu vào form
- [ ] **Chỉ kết quả:** Toast notification, modal chi tiết
- [ ] **Mở DB ngay lập tức:** Show query kết quả
- [ ] **Giải thích logic:** "Vì... nên hệ thống cảnh báo..."

### Sau mỗi test:
- [ ] Hỏi câu hỏi: "Có thắc mắc gì không?"
- [ ] Cleanup nếu cần: Xóa đơn test, reset stock

---

## 🚨 XỬ LÝ LỖI THƯỜNG GẶP

### 1. ❌ Error: Column 'warning_flag' doesn't exist
**Nguyên nhân:** Chưa chạy migration

**Fix:**
```sql
-- Chạy ngay:
SOURCE db/migrations/007_add_uc7_warning_system.sql;
```

### 2. ❌ Toast không hiện
**Nguyên nhân:** JavaScript console có lỗi

**Fix:**
- F12 → Console → Xem lỗi
- Kiểm tra file `Project.php` có include jQuery chưa

### 3. ❌ Modal không hiển thị material_details
**Nguyên nhân:** warning_details không có dữ liệu

**Fix:**
```sql
-- Kiểm tra data
SELECT 
    id_project, 
    JSON_PRETTY(warning_details) 
FROM project 
WHERE id_project = 'PJ-XXX';

-- Phải thấy: material_details array
```

### 4. ❌ Stock allocation sai
**Nguyên nhân:** Có đơn cũ với entry_date trước

**Fix:**
```sql
-- Xóa đơn cũ
DELETE FROM project WHERE id_product = 1001 AND pr_status < 3;

-- Hoặc set status = completed
UPDATE project SET pr_status = 3 WHERE id_project = 'PJ-XXX';
```

### 5. ❌ Capacity_config not found
**Nguyên nhân:** Bảng chưa tạo

**Fix:**
```sql
CREATE TABLE capacity_config (...);  -- Xem trong migration
INSERT INTO capacity_config VALUES (...);
```

---

## 📝 SCRIPT DEMO (Nói với người xem)

### Giới thiệu (2 phút):
```
"Chào mọi người! Hôm nay tôi sẽ demo hệ thống UC7 - Cảnh báo đơn hàng.

Trước đây, khi tạo đơn hàng, hệ thống chỉ nói 'Đơn hàng OK' hoặc 'Không đủ công suất'.
Nhưng KHÔNG CHI TIẾT - không biết thiếu cái gì, thiếu bao nhiêu.

Bây giờ với UC7, hệ thống sẽ:
1. ✅ Kiểm tra tồn kho thành phẩm - Dùng stock trước, sản xuất sau
2. ⚙️ Kiểm tra công suất - Level 1 (8h) hoặc Level 2 (12h tăng ca)
3. 🧪 Kiểm tra NVL - TỪNG NGUYÊN VẬT LIỆU, đủ bao nhiêu ca

Và quan trọng nhất: NẾU THIẾU, hệ thống chỉ rõ THIẾU CÁI GÌ, THIẾU BAO NHIÊU.

Tất cả thông tin này đều lưu vào DATABASE, để sau này report, export đều có.

Bây giờ chúng ta test 7 trường hợp."
```

### Kết thúc (1 phút):
```
"Vậy là chúng ta đã test đủ 7 trường hợp:
1. ✅ Bình thường
2. 🔴 Vượt Level 1
3. ❌ Thiếu NVL
4. ⚠️ BOM thiếu
5. 🚫 Từ chối (vượt Level 2)
6. ⏰ Deadline gần
7. 🚀 Phân bổ stock theo ưu tiên

Hệ thống hoạt động CHÍNH XÁC, dữ liệu đồng bộ giữa CODE và DATABASE.

Có câu hỏi nào không ạ?"
```

---

## ✅ FINAL CHECK TRƯỚC KHI DEMO

```bash
# 1. Database ready?
mysql -u root -p db_production -e "DESCRIBE project;" | grep warning_flag

# 2. Code ready?
grep -r "material_details" application/models/OrderModel.php

# 3. Web server running?
curl http://localhost/production-management-v2

# 4. Test data ready?
mysql -u root -p db_production -e "SELECT id_product, product_name FROM product LIMIT 5;"

# 5. Backup created?
ls -lh db/backups/backup_before_uc7*
```

**Nếu tất cả ✅ → READY TO DEMO! 🚀**

---

**Chúc demo thành công! 🎉**
