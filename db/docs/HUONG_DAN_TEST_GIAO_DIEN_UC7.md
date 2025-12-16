# 📋 HƯỚNG DẪN TEST GIAO DIỆN UC7 - HỆ THỐNG CẢNH BÁO ĐƠN HÀNG

> **MỤC ĐÍCH**: Hướng dẫn chi tiết cách test 7 kịch bản UC7 qua giao diện web, kèm cách kiểm tra database để chứng minh code kết nối chặt chẽ với DB.

---

## 🎯 MỤC TIÊU TEST

✅ **UI → Code → Database**: Kiểm tra dữ liệu nhập vào giao diện được xử lý và lưu đúng vào DB  
✅ **Database → Code → UI**: Kiểm tra dữ liệu từ DB được hiển thị chính xác trên modal chi tiết  
✅ **Công thức tính toán**: Xác minh các công thức capacity và NVL được tính đúng  
✅ **Toast messages**: Xác nhận thông báo hiển thị phù hợp với từng trường hợp  
✅ **Modal details**: Kiểm tra 5 sections hiển thị đầy đủ và chính xác

---

## 🔧 CHUẨN BỊ TEST

### 1. Đăng nhập hệ thống
- **URL**: `http://localhost/production-management-v2/`
- **Username**: Tài khoản BOD (có quyền quản lý đơn hàng)
- **Password**: Mật khẩu của bạn

### 2. Truy cập trang Quản lý Đơn hàng
- Từ Dashboard → Click menu **"Quản lý Đơn hàng"**
- URL: `http://localhost/production-management-v2/BOD/project`

### 3. Chuẩn bị công cụ kiểm tra Database
- Mở **HeidiSQL** hoặc **phpMyAdmin**
- Connect database: `db_production`
- Chuẩn bị sẵn các query để kiểm tra sau mỗi test

---

## 📊 BẢNG CÔNG THỨC THAM KHẢO

| **Công thức** | **Cách tính** | **Giá trị** |
|---------------|---------------|-------------|
| **Level 1 capacity** | 500 sp/h × 8h × 0.80 | 3,200 sp/ca |
| **Level 2 capacity** | 500 sp/h × 12h × 0.85 | 5,100 sp/ca |
| **Số ca cần** | qty_to_produce ÷ capacity_per_shift | ceil(qty/cap) |
| **Số ngày cần** | số_ca_cần ÷ 2 | ceil(ca/2) |
| **NVL cần** | qty_to_produce × quantity_per_unit | số lượng NVL |
| **Số ca NVL** | stock ÷ (qty_per_unit × 3200) | floor(stock/need) |

---

# 🧪 7 KỊCH BẢN TEST

---

## **TEST SCENARIO 1: NORMAL - ĐỦ HÀM TỒN KHO**

### 📌 **Mục tiêu**
Kiểm tra trường hợp có đủ hàng tồn kho thành phẩm, không cần sản xuất.

### 🔢 **Dữ liệu đầu vào**

| **Field** | **Giá trị nhập** | **Ghi chú** |
|-----------|------------------|-------------|
| **Khách hàng** | Chọn bất kỳ | VD: "Công ty ABC" |
| **Sản phẩm** | `TL-079` (id_product = 1001) | Có sẵn 35 cái trong tồn kho |
| **Đường kính** | `0.5` | mm |
| **Số lượng** | `20` | Ít hơn tồn kho (35 cái) |
| **Deadline** | `2025-12-31` | Ngày trong tương lai xa |
| **Yêu cầu KH** | `Giao đúng hạn` | (Tùy chọn) |

### 🚀 **Các bước test**

1. **Login** với tài khoản BOD
2. Vào menu **"Quản lý Đơn hàng"** → Click **"Thêm Đơn hàng"**
3. Nhập đầy đủ thông tin theo bảng trên
4. Click nút **"Lưu"**

### ✅ **Kết quả mong đợi trên UI**

#### **Toast notification (góc phải màn hình)**
```
✓ Tạo đơn thành công!
Đơn hàng "[Tên đơn]" đã được tạo và duyệt
```
- **Màu**: Xanh lá (success)
- **Icon**: ✓ (checkmark)
- **Thời gian hiển thị**: 3 giây

#### **Modal chi tiết (khi click "Chi tiết")**

**SECTION 1: THÔNG TIN CƠ BẢN**
```
Sản phẩm: TL-079
Số lượng yêu cầu: 20 cái
Deadline: 31/12/2025
```

**SECTION 2: TỒN KHO THÀNH PHẨM**
```
✓ Tồn kho: 35 cái, dùng 20 cái cho đơn này → Có thể giao ngay
  - Tổng tồn kho: 35 cái
  - Đã phân bổ cho đơn khác: 0 cái
  - Khả dụng cho đơn này: 35 cái
  - Sử dụng: 20 cái
  - Còn lại sau khi dùng: 15 cái
```

**SECTION 3: CÔNG SUẤT SẢN XUẤT**
```
Mức công suất: KHÔNG CẦN SẢN XUẤT (capacity_level_used = 0)
```

**SECTION 4: CHI TIẾT NVL**
```
(Không hiển thị - vì không cần sản xuất)
```

**SECTION 5: CẢNH BÁO**
```
(Không có cảnh báo)
```

### 🔍 **Kiểm tra Database**

#### **Bước 1: Lấy ID đơn hàng vừa tạo**
```sql
SELECT id_project, project_name, qty_request, entry_date, created_at
FROM project
WHERE id_product = 1001
ORDER BY created_at DESC
LIMIT 1;
```

**Kết quả mong đợi:**
```
id_project | project_name | qty_request | entry_date | created_at
-----------|--------------|-------------|------------|-------------------
[ID mới]   | ORD-xxx-...  | 20          | 2025-12-31 | 2025-05-XX ...
```

#### **Bước 2: Kiểm tra 5 cột UC7**
```sql
SELECT 
    id_project,
    warning_flag,
    capacity_level_used,
    material_shifts_available,
    finished_stock_available,
    JSON_PRETTY(warning_details) AS warning_details_formatted
FROM project
WHERE id_project = '[ID từ bước 1]';
```

**Kết quả mong đợi:**

| **Column** | **Expected Value** | **Ý nghĩa** |
|------------|-------------------|-------------|
| `warning_flag` | `1` | Có thông tin (không phải cảnh báo xấu) |
| `capacity_level_used` | `0` | Không cần sản xuất |
| `material_shifts_available` | `NULL` | Không tính NVL vì không sản xuất |
| `finished_stock_available` | `35` | Tồn kho khả dụng |
| `warning_details` | JSON có `finished_stock_info`, `stock_status='sufficient'` | Chi tiết tồn kho |

**JSON `warning_details` phải chứa:**
```json
{
  "finished_stock_info": "✓ Tồn kho: 35 cái, dùng 20 cái cho đơn này → Có thể giao ngay",
  "stock_status": "sufficient",
  "stock_breakdown": {
    "total_stock": 35,
    "allocated_to_others": 0,
    "available_for_this": 35,
    "used_for_this_order": 20,
    "remaining_after": 15
  }
}
```

### ✔️ **Checklist thành công**

- [ ] Toast hiển thị màu xanh với message "Tạo đơn thành công"
- [ ] Modal section 2 hiển thị "Có thể giao ngay"
- [ ] Modal section 3 hiển thị "KHÔNG CẦN SẢN XUẤT"
- [ ] DB: `warning_flag = 1`
- [ ] DB: `capacity_level_used = 0`
- [ ] DB: `finished_stock_available = 35`
- [ ] DB: `warning_details` có key `stock_status = 'sufficient'`
- [ ] Công thức: `total_stock - allocated - used = remaining` → `35 - 0 - 20 = 15` ✓

---

## **TEST SCENARIO 2: LEVEL 2 - VUỢt CÔNG SUẤT LEVEL 1**

### 📌 **Mục tiêu**
Kiểm tra khi số lượng yêu cầu vượt quá capacity Level 1 (8h×2ca=16h) nhưng còn trong Level 2 (12h×2ca=24h).

### 🔢 **Dữ liệu đầu vào**

| **Field** | **Giá trị nhập** | **Ghi chú** |
|-----------|------------------|-------------|
| **Khách hàng** | Chọn bất kỳ | VD: "Công ty XYZ" |
| **Sản phẩm** | `TL-079` (id_product = 1001) | |
| **Đường kính** | `0.5` | mm |
| **Số lượng** | `5000` | Vượt Level 1 (3,200 sp/ca) |
| **Deadline** | `Ngày hôm nay + 1 ngày` | VD: Nếu hôm nay 20/05 thì nhập 21/05 |
| **Yêu cầu KH** | `Cần gấp` | |

### 🚀 **Các bước test**

1. Vào **"Thêm Đơn hàng"**
2. Nhập dữ liệu theo bảng trên
3. Click **"Lưu"**

### ✅ **Kết quả mong đợi trên UI**

#### **Toast notification**
```
⚠️ Đã tạo đơn với cảnh báo!
Đơn hàng "[Tên]" đã được tạo.
⚠️ Vượt Level 1 (8h×2ca=16h), chuyển sang Level 2 (12h×2ca=24h)
```
- **Màu**: Vàng cam (warning)
- **Icon**: ⚠️
- **Thời gian**: 5 giây

#### **Modal chi tiết**

**SECTION 2: TỒN KHO**
```
✓ Tồn kho: 35 cái, dùng 35 cái, cần sản xuất thêm 4965 cái
```

**SECTION 3: CÔNG SUẤT**
```
⚠️ Mức công suất: LEVEL 2 (12 giờ × 2 ca/ngày)
Số ca cần: [X ca] (tính theo công thức)
Số ngày cần: [Y ngày] (= ceil(X/2))
Cảnh báo: Vượt Level 1 (8h×2ca=16h), chuyển sang Level 2 (12h×2ca=24h)
```

**SECTION 4: CHI TIẾT NVL**
```
[Danh sách 5-7 NVL theo BOM]
Mỗi dòng hiển thị:
- Tên NVL (ĐVT)
- Tồn kho: [X] kg
- Cần cho đơn: [Y] kg
- Thiếu/Đủ: [Z] kg
- Số ca sau đơn: [N ca]
```

### 🔍 **Kiểm tra Database**

```sql
-- Lấy đơn mới nhất
SELECT id_project, project_name, qty_request
FROM project
WHERE id_product = 1001
ORDER BY created_at DESC
LIMIT 1;

-- Kiểm tra UC7 columns
SELECT 
    warning_flag,
    capacity_level_used,
    material_shifts_available,
    finished_stock_available,
    JSON_PRETTY(warning_details) AS details
FROM project
WHERE id_project = '[ID vừa lấy]';
```

**Kết quả mong đợi:**

| **Column** | **Expected Value** | **Ý nghĩa** |
|------------|-------------------|-------------|
| `warning_flag` | `1` | Có cảnh báo |
| `capacity_level_used` | `2` | Dùng Level 2 (12h) |
| `material_shifts_available` | `>= 0` | Số ca NVL có thể hỗ trợ |
| `finished_stock_available` | `35` | Tồn kho đã dùng hết |

**JSON phải có keys:**
- `finished_stock_info`
- `capacity_warning`: "Vượt Level 1..."
- `estimated_shifts`: Số ca cần
- `estimated_days`: Số ngày cần
- `material_details`: Array chi tiết từng NVL

### ✔️ **Checklist thành công**

- [ ] Toast màu vàng cam với icon ⚠️
- [ ] Toast hiển thị "Vượt Level 1..."
- [ ] Modal section 3 hiển thị "LEVEL 2"
- [ ] Modal section 4 hiển thị chi tiết NVL
- [ ] DB: `capacity_level_used = 2`
- [ ] DB: `warning_details` có key `capacity_warning`
- [ ] Công thức: Số ca = ceil(4965 / 5100) = 1 ca ✓ (nếu 1 ngày)

---

## **TEST SCENARIO 3: MATERIAL SHORTAGE - THIẾU NVL**

### 📌 **Mục tiêu**
Kiểm tra cảnh báo khi NVL không đủ để sản xuất số lượng yêu cầu.

### 🔧 **Chuẩn bị dữ liệu**

**Trước khi test, giảm tồn kho NVL:**

```sql
-- Giảm tồn kho NVL id_material = 1001 xuống 5 kg
UPDATE material
SET stock = 5
WHERE id_material = 1001;
```

### 🔢 **Dữ liệu đầu vào**

| **Field** | **Giá trị nhập** |
|-----------|------------------|
| **Sản phẩm** | `TL-079` (1001) |
| **Số lượng** | `1000` |
| **Deadline** | Ngày hôm nay + 5 ngày |

*(Các field khác: như bình thường)*

### 🚀 **Các bước test**

1. Chạy SQL giảm tồn kho NVL (bên trên)
2. Vào **"Thêm Đơn hàng"**
3. Nhập dữ liệu
4. Click **"Lưu"**

### ✅ **Kết quả mong đợi trên UI**

#### **Toast notification**
```
⚠️ Đã tạo đơn với cảnh báo!
Đơn hàng "[Tên]" đã được tạo.
📦 ⚠️ NVL chỉ đủ cho khoảng X ca, cần nhập thêm!
   Chi tiết: [Tên NVL]: chỉ còn 5.00 kg (tối thiểu: 10.00)
```
- **Màu**: Vàng cam (warning)
- **Icon**: ⚠️ và 📦

#### **Modal chi tiết - SECTION 4: CHI TIẾT NVL**

```
📦 Nguyên vật liệu:
  [Tên NVL 1] (kg)
    - Tồn kho: 5.00 kg
    - Cần cho đơn: 50.00 kg
    - ❌ THIẾU: 45.00 kg
    - Số ca NVL còn đủ: 0 ca

  [Tên NVL 2] (kg)
    - Tồn kho: 100.00 kg
    - Cần cho đơn: 20.00 kg
    - ✓ ĐỦ: Dư 80.00 kg
    - Số ca NVL sau đơn: 12 ca
    
  [🔴 NVL giới hạn (Bottleneck)]: [Tên NVL 1]
```

### 🔍 **Kiểm tra Database**

```sql
SELECT 
    warning_flag,
    capacity_level_used,
    material_shifts_available,
    JSON_PRETTY(warning_details) AS details
FROM project
WHERE id_project = '[ID mới nhất]';
```

**Kết quả mong đợi:**

| **Column** | **Value** | **Ý nghĩa** |
|------------|-----------|-------------|
| `warning_flag` | `1` | Có cảnh báo |
| `material_shifts_available` | `0` hoặc số nhỏ | NVL thiếu → ít ca |

**JSON phải có:**
```json
{
  "material_warning": "⚠️ NVL chỉ đủ cho khoảng X ca, cần nhập thêm!",
  "material_shortage_details": "[Tên NVL]: chỉ còn 5.00 kg (tối thiểu: 10.00)",
  "material_details": [
    {
      "material_name": "[Tên]",
      "stock": 5,
      "quantity_needed": 50,
      "quantity_shortage": 45,
      "shifts_possible": 0,
      "is_sufficient": false,
      "is_bottleneck": true
    }
  ],
  "bottleneck_material": "[Tên NVL thiếu nhất]"
}
```

### ✔️ **Checklist thành công**

- [ ] Toast có icon 📦 và text "NVL chỉ đủ cho..."
- [ ] Modal section 4 hiển thị "❌ THIẾU" cho NVL thiếu
- [ ] Modal hiển thị "🔴 NVL giới hạn (Bottleneck)"
- [ ] DB: `material_shifts_available` là số nhỏ hoặc 0
- [ ] DB: `warning_details` có key `material_warning`
- [ ] DB: `material_details[X].is_bottleneck = true` cho NVL thiếu nhất

### 🔄 **Khôi phục dữ liệu sau test**
```sql
-- Trả lại tồn kho NVL về ban đầu
UPDATE material
SET stock = 50
WHERE id_material = 1001;
```

---

## **TEST SCENARIO 4: MISSING BOM - SẢN PHẨM CHƯA CÓ BOM**

### 📌 **Mục tiêu**
Kiểm tra cảnh báo khi sản phẩm chưa có BOM hoặc BOM có NVL chưa tồn tại trong kho.

### 🔧 **Chuẩn bị dữ liệu**

**Xóa tạm BOM của sản phẩm TL-079:**

```sql
-- Backup BOM hiện tại (để khôi phục sau)
SELECT id_product, product_name, bom
FROM product
WHERE id_product = 1001;

-- Copy đoạn JSON bom ra notepad để backup

-- Xóa BOM
UPDATE product
SET bom = NULL
WHERE id_product = 1001;
```

### 🔢 **Dữ liệu đầu vào**

| **Field** | **Giá trị** |
|-----------|-------------|
| **Sản phẩm** | TL-079 (1001) |
| **Số lượng** | 500 |
| **Deadline** | Hôm nay + 3 ngày |

### 🚀 **Các bước test**

1. Chạy SQL xóa BOM (bên trên)
2. Vào **"Thêm Đơn hàng"**
3. Nhập dữ liệu
4. Click **"Lưu"**

### ✅ **Kết quả mong đợi trên UI**

#### **Toast notification**
```
⚠️ Đã tạo đơn với cảnh báo!
Đơn hàng "[Tên]" đã được tạo.
📦 Sản phẩm chưa có BOM chi tiết, ước tính NVL theo min_stock
```

#### **Modal chi tiết - SECTION 4: CHI TIẾT NVL**

```
📦 Nguyên vật liệu (ước tính - chưa có BOM):
  [NVL 1] (kg)
    - Tồn kho: 50.00 kg
    - Min stock: 10.00 kg
    - Số ca ước tính: 5 ca
    
  [NVL 2] (kg)
    - Tồn kho: 80.00 kg
    - Min stock: 15.00 kg
    - Số ca ước tính: 5 ca
    
  ⚠️ Chú ý: Sản phẩm chưa có BOM, không thể tính chính xác NVL
```

### 🔍 **Kiểm tra Database**

```sql
-- Kiểm tra product có BOM không
SELECT id_product, product_name, bom
FROM product
WHERE id_product = 1001;

-- Kết quả: bom = NULL

-- Kiểm tra đơn hàng
SELECT 
    warning_flag,
    material_shifts_available,
    JSON_PRETTY(warning_details) AS details
FROM project
WHERE id_project = '[ID mới nhất]';
```

**JSON phải có:**
```json
{
  "material_details": [
    {
      "material_name": "[Tên NVL]",
      "stock": 50,
      "min_stock": 10,
      "shifts_possible": 5,
      "is_sufficient": true,
      "is_bottleneck": false
    }
  ],
  "material_status": "Ước tính đủ NVL cho ~X ca"
}
```

**Lưu ý:**
- `material_details` không có field `quantity_needed`, `quantity_shortage` (vì không có BOM)
- Chỉ có `min_stock` và `shifts_possible` (ước tính)

### ✔️ **Checklist thành công**

- [ ] Toast có cảnh báo về BOM
- [ ] Modal section 4 hiển thị "ước tính - chưa có BOM"
- [ ] DB: `material_details` không có `quantity_needed`
- [ ] DB: `material_details` có `min_stock` và `shifts_possible`

### 🔄 **Khôi phục dữ liệu sau test**
```sql
-- Paste lại đoạn JSON BOM đã backup từ notepad
UPDATE product
SET bom = '[JSON BOM từ backup]'
WHERE id_product = 1001;
```

---

## **TEST SCENARIO 5: REJECTION - VƯỢT QUÁ CÔNG SUẤT TỐI ĐA**

### 📌 **Mục tiêu**
Kiểm tra hệ thống TỪ CHỐI đơn hàng khi vượt quá công suất Level 2 (không thể sản xuất).

### 🔢 **Dữ liệu đầu vào**

| **Field** | **Giá trị** |
|-----------|-------------|
| **Sản phẩm** | TL-079 (1001) |
| **Số lượng** | `50000` |
| **Deadline** | **Hôm nay + 1 ngày** |

*(Số lượng 50,000 với deadline 1 ngày → Vượt cả Level 2)*

### 🚀 **Các bước test**

1. Vào **"Thêm Đơn hàng"**
2. Nhập số lượng **50000** và deadline **1 ngày sau**
3. Click **"Lưu"**

### ✅ **Kết quả mong đợi trên UI**

#### **Toast notification**
```
❌ Không thể tạo đơn hàng!
Vượt công suất tối đa (Level 2), không thể sản xuất
Cần 50,000 cái, chỉ làm được tối đa 10,200 cái
```
- **Màu**: Đỏ (error)
- **Icon**: ❌
- **Hành động**: Quay lại form thêm đơn hàng (không lưu vào DB)

### 🔍 **Kiểm tra Database**

```sql
-- Kiểm tra xem đơn có được tạo không
SELECT id_project, project_name, qty_request, created_at
FROM project
WHERE id_product = 1001
  AND qty_request = 50000
ORDER BY created_at DESC
LIMIT 1;
```

**Kết quả mong đợi:**
```
Empty set (0 rows)
```

**👉 Đơn hàng KHÔNG được lưu vào database** → Đúng!

### ✔️ **Checklist thành công**

- [ ] Toast màu đỏ với icon ❌
- [ ] Toast hiển thị "Vượt công suất tối đa (Level 2)"
- [ ] Form quay lại trang thêm đơn hàng (không redirect đến danh sách)
- [ ] DB: Đơn hàng KHÔNG được tạo (query trả về 0 rows)
- [ ] Công thức: Level 2 capacity × 2 ca × 1 ngày = 5100 × 2 = 10,200 < 50,000 → TỪ CHỐI ✓

---

## **TEST SCENARIO 6: DEADLINE WARNING - GẦN HẠN GIAO**

### 📌 **Mục tiêu**
Kiểm tra cảnh báo khi deadline còn ≤ 3 ngày.

### 🔢 **Dữ liệu đầu vào**

| **Field** | **Giá trị** |
|-----------|-------------|
| **Sản phẩm** | TL-079 (1001) |
| **Số lượng** | 2000 |
| **Deadline** | **Hôm nay + 2 ngày** |

### 🚀 **Các bước test**

1. Vào **"Thêm Đơn hàng"**
2. Nhập deadline = **hôm nay + 2 ngày** (VD: 20/05 → deadline 22/05)
3. Click **"Lưu"**

### ✅ **Kết quả mong đợi trên UI**

#### **Toast notification**
```
⚠️ Đã tạo đơn với cảnh báo!
Đơn hàng "[Tên]" đã được tạo.
⏰ Gần deadline - cần ưu tiên
```
- **Màu**: Vàng cam (warning)
- **Icon**: ⏰

#### **Modal chi tiết - SECTION 5: CẢNH BÁO**
```
⏰ Deadline: Gần deadline - cần ưu tiên (còn 2 ngày)
```

### 🔍 **Kiểm tra Database**

```sql
SELECT 
    id_project,
    entry_date,
    DATEDIFF(entry_date, CURDATE()) AS days_remaining,
    warning_flag,
    JSON_PRETTY(warning_details) AS details
FROM project
WHERE id_project = '[ID mới nhất]';
```

**Kết quả mong đợi:**

| **Column** | **Value** | **Ý nghĩa** |
|------------|-----------|-------------|
| `days_remaining` | `2` | Còn 2 ngày |
| `warning_flag` | `1` | Có cảnh báo |

**JSON phải có:**
```json
{
  "deadline_warning": "Gần deadline - cần ưu tiên"
}
```

### ✔️ **Checklist thành công**

- [ ] Toast có icon ⏰ và text "Gần deadline"
- [ ] Modal section 5 hiển thị cảnh báo deadline
- [ ] DB: `warning_details` có key `deadline_warning`
- [ ] Công thức: DATEDIFF(entry_date, CURDATE()) ≤ 3 → Hiển thị cảnh báo ✓

---

## **TEST SCENARIO 7: STOCK ALLOCATION - PHÂN BỔ TỒN KHO THEO ƯU TIÊN**

### 📌 **Mục tiêu**
Kiểm tra hệ thống phân bổ tồn kho thành phẩm theo thứ tự ưu tiên (deadline sớm hơn được ưu tiên cao hơn).

### 🔧 **Chuẩn bị dữ liệu**

**Tạo 2 đơn hàng với deadline khác nhau:**

#### **Đơn 1 (Ưu tiên thấp - deadline xa)**
| **Field** | **Giá trị** |
|-----------|-------------|
| **Sản phẩm** | TL-079 (1001) |
| **Số lượng** | 30 |
| **Deadline** | 2025-12-31 |

→ Click **"Lưu"** → Thành công

#### **Đơn 2 (Ưu tiên cao - deadline gần)**
| **Field** | **Giá trị** |
|-----------|-------------|
| **Sản phẩm** | TL-079 (1001) |
| **Số lượng** | 20 |
| **Deadline** | **Hôm nay + 3 ngày** |

→ Click **"Lưu"**

### 🚀 **Các bước test**

1. Tạo **Đơn 1** trước (deadline xa)
2. Tạo **Đơn 2** sau (deadline gần)
3. Click vào **"Chi tiết"** của **Đơn 1** (đơn deadline xa)

### ✅ **Kết quả mong đợi trên UI**

#### **Toast notification cho Đơn 2**
```
⚠️ Đã tạo đơn với cảnh báo!
Đơn hàng "[Tên Đơn 2]" đã được tạo.
⏰ Gần deadline - cần ưu tiên
```

#### **Modal chi tiết của ĐƠN 1 (sau khi tạo Đơn 2)**

**SECTION 2: TỒN KHO**
```
⚠️ Tồn kho 35 cái đã phân bổ hết cho các đơn ưu tiên cao hơn (deadline sớm hơn), 
   cần sản xuất 30 cái cho đơn này

Stock breakdown:
  - Tổng tồn kho: 35 cái
  - Đã phân bổ cho đơn khác: 20 cái (Đơn 2 - deadline gần hơn)
  - Khả dụng cho đơn này: 15 cái
  - Cần sản xuất: 15 cái (vì 30 - 15 = 15)
```

**Giải thích logic:**
- Tổng tồn kho: 35 cái
- Đơn 2 (deadline gần) được ưu tiên → Lấy hết 20 cái
- Đơn 1 chỉ còn: 35 - 20 = 15 cái khả dụng
- Đơn 1 cần 30 cái → Thiếu 15 cái → Phải sản xuất 15 cái

### 🔍 **Kiểm tra Database**

```sql
-- Lấy 2 đơn hàng vừa tạo
SELECT 
    id_project,
    project_name,
    qty_request,
    entry_date,
    finished_stock_available,
    created_at,
    JSON_EXTRACT(warning_details, '$.stock_breakdown') AS stock_breakdown
FROM project
WHERE id_product = 1001
ORDER BY created_at DESC
LIMIT 2;
```

**Kết quả mong đợi:**

| **id_project** | **qty_request** | **entry_date** | **finished_stock_available** | **Giải thích** |
|----------------|-----------------|----------------|------------------------------|----------------|
| [ID Đơn 2]     | 20              | 2025-05-23     | 35 (hoặc 20)                 | Đơn ưu tiên cao → Lấy 20 cái |
| [ID Đơn 1]     | 30              | 2025-12-31     | 15                           | Đơn ưu tiên thấp → Còn 15 cái |

**Kiểm tra chi tiết `stock_breakdown` của Đơn 1:**
```sql
SELECT JSON_PRETTY(warning_details)
FROM project
WHERE id_project = '[ID Đơn 1]';
```

**JSON phải chứa:**
```json
{
  "stock_breakdown": {
    "total_stock": 35,
    "allocated_to_others": 20,
    "available_for_this": 15,
    "used_from_stock": 15,
    "need_produce": 15
  },
  "stock_status": "partial"
}
```

### ✔️ **Checklist thành công**

- [ ] Đơn 2 (deadline gần) hiển thị "Có thể giao ngay" hoặc có stock ưu tiên
- [ ] Đơn 1 (deadline xa) hiển thị "đã phân bổ hết cho các đơn ưu tiên cao hơn"
- [ ] Modal Đơn 1 section 2 hiển thị "Đã phân bổ: 20 cái"
- [ ] DB Đơn 1: `finished_stock_available = 15`
- [ ] DB Đơn 2: `finished_stock_available = 35` hoặc 20
- [ ] DB Đơn 1: `stock_breakdown.allocated_to_others = 20`
- [ ] Công thức: `total(35) - allocated(20) = available(15)` ✓

### 🔄 **Test bổ sung: Cập nhật đơn hàng**

**Bước 1**: Vào "Sửa đơn hàng" của **Đơn 2** (đơn ưu tiên cao)  
**Bước 2**: Đổi deadline của Đơn 2 thành **2025-12-30** (trước Đơn 1 1 ngày nhưng vẫn deadline xa)  
**Bước 3**: Click **"Lưu"**  
**Bước 4**: Xem lại chi tiết **Đơn 1**

**Kết quả mong đợi:**
- Đơn 1 giờ có ưu tiên CAO HƠN (vì deadline 2025-12-31 muộn hơn 2025-12-30 của Đơn 2)
- KHÔNG ĐÚNG: Vì created_at của Đơn 1 sớm hơn → Nếu cùng deadline thì Đơn 1 ưu tiên hơn

**Lưu ý**: Thứ tự ưu tiên = `ORDER BY entry_date ASC, created_at ASC`  
→ Deadline sớm hơn luôn được ưu tiên cao hơn  
→ Nếu cùng deadline, đơn tạo trước (created_at sớm hơn) được ưu tiên cao hơn

---

# 📊 BẢNG TỔNG HỢP 7 KỊCH BẢN

| **#** | **Tên kịch bản** | **Số lượng** | **Deadline** | **Toast màu** | **capacity_level_used** | **Cảnh báo chính** |
|-------|------------------|--------------|--------------|---------------|-------------------------|-------------------|
| 1     | Normal           | 20           | Xa (2025-12-31) | Xanh ✅      | 0                       | Không có          |
| 2     | Level 2          | 5000         | +1 ngày      | Vàng ⚠️     | 2                       | Vượt Level 1      |
| 3     | Material Shortage | 1000        | +5 ngày      | Vàng ⚠️     | 1 hoặc 2                | Thiếu NVL         |
| 4     | Missing BOM      | 500          | +3 ngày      | Vàng ⚠️     | 1                       | Chưa có BOM       |
| 5     | Rejection        | 50000        | +1 ngày      | Đỏ ❌       | (Không lưu)             | Vượt Level 2      |
| 6     | Deadline Warning | 2000         | +2 ngày      | Vàng ⚠️     | 1                       | Gần deadline      |
| 7     | Stock Allocation | 30 & 20      | Xa & gần     | Vàng ⚠️     | 1 hoặc 0                | Phân bổ stock     |

---

# 🎯 CHECKLIST TỔNG QUÁT

## 🧪 **Mỗi test case phải kiểm tra:**

### **1. UI - Toast Notification**
- [ ] Màu sắc phù hợp (xanh/vàng/đỏ)
- [ ] Icon phù hợp (✓/⚠️/❌/⏰/📦)
- [ ] Message chính xác theo kịch bản
- [ ] Thời gian hiển thị hợp lý (3-5 giây)

### **2. UI - Modal Chi Tiết (5 Sections)**
- [ ] **Section 1**: Thông tin cơ bản (sản phẩm, số lượng, deadline)
- [ ] **Section 2**: Tồn kho thành phẩm (total/allocated/available/used/remaining)
- [ ] **Section 3**: Công suất (Level 0/1/2, số ca, số ngày)
- [ ] **Section 4**: Chi tiết NVL (stock/needed/shortage/shifts, bottleneck)
- [ ] **Section 5**: Cảnh báo (capacity/material/deadline warnings)

### **3. Database - 5 Cột UC7**
- [ ] `warning_flag` = 0 hoặc 1 phù hợp
- [ ] `capacity_level_used` = 0/1/2 đúng logic
- [ ] `material_shifts_available` = số ca hợp lý
- [ ] `finished_stock_available` = tồn kho khả dụng
- [ ] `warning_details` JSON có đầy đủ keys

### **4. Database - JSON Structure**
- [ ] JSON parse thành công (không lỗi syntax)
- [ ] Keys phù hợp: `finished_stock_info`, `capacity_warning`, `material_warning`, `deadline_warning`
- [ ] Arrays có đầy đủ phần tử: `material_details[]`, `stock_breakdown{}`
- [ ] Các giá trị số đúng công thức tính toán

### **5. Công Thức Tính Toán**
- [ ] Level 1: 500 × 8 × 0.80 = 3,200 sp/ca
- [ ] Level 2: 500 × 12 × 0.85 = 5,100 sp/ca
- [ ] Số ca = ceil(qty_to_produce / capacity_per_shift)
- [ ] Số ngày = ceil(số_ca / 2)
- [ ] NVL cần = qty × quantity_per_unit
- [ ] Số ca NVL = floor(stock / (qty_per_unit × 3200))
- [ ] Stock allocation: total - allocated = available

### **6. Luồng UI → Code → DB → UI**
- [ ] Dữ liệu nhập vào UI → Được xử lý bởi checkCapacity() trong OrderModel
- [ ] Kết quả checkCapacity() → Lưu vào 5 cột UC7 trong bảng `project`
- [ ] Dữ liệu từ DB → Hiển thị chính xác trên modal chi tiết
- [ ] Không có dữ liệu bị mất hoặc sai lệch giữa các bước

---

# 🛠️ CÔNG CỤ HỖ TRỢ

## **SQL Queries Tiện Ích**

### **1. Lấy đơn hàng mới nhất**
```sql
SELECT id_project, project_name, qty_request, entry_date, created_at
FROM project
WHERE id_product = 1001
ORDER BY created_at DESC
LIMIT 1;
```

### **2. Xem chi tiết UC7 của 1 đơn**
```sql
SELECT 
    id_project,
    project_name,
    warning_flag,
    capacity_level_used,
    material_shifts_available,
    finished_stock_available,
    JSON_PRETTY(warning_details) AS details
FROM project
WHERE id_project = ?;
```

### **3. So sánh 2 đơn hàng (Stock Allocation)**
```sql
SELECT 
    id_project,
    project_name,
    qty_request,
    entry_date,
    finished_stock_available,
    created_at,
    JSON_EXTRACT(warning_details, '$.stock_breakdown.allocated_to_others') AS allocated
FROM project
WHERE id_product = 1001
ORDER BY entry_date ASC, created_at ASC;
```

### **4. Kiểm tra tồn kho NVL**
```sql
SELECT id_material, material_name, stock, min_stock, uom
FROM material
WHERE id_material IN (1001, 1002, 1003, 1004, 1005);
```

### **5. Kiểm tra tồn kho thành phẩm**
```sql
SELECT fs.id_product, p.product_name, fs.quantity_in_stock
FROM finished_stock fs
JOIN product p ON fs.id_product = p.id_product
WHERE fs.id_product = 1001;
```

### **6. Reset dữ liệu test (xóa đơn test)**
```sql
-- Xóa các đơn hàng test (cẩn thận!)
DELETE FROM project
WHERE id_project IN (?, ?, ?);

-- Hoặc xóa theo thời gian
DELETE FROM project
WHERE created_at >= '2025-05-20 10:00:00';
```

---

# 📸 SCREENSHOT CHECKLIST

**Khi demo cho stakeholders, chuẩn bị screenshots:**

1. **Scenario 1**: Toast xanh "Tạo đơn thành công" + Modal "Có thể giao ngay"
2. **Scenario 2**: Toast vàng "Vượt Level 1" + Modal "LEVEL 2"
3. **Scenario 3**: Toast "Thiếu NVL" + Modal chi tiết NVL với "❌ THIẾU"
4. **Scenario 5**: Toast đỏ "Không thể tạo đơn" + Form quay lại
5. **Scenario 7**: Modal Đơn 1 hiển thị "Đã phân bổ cho đơn ưu tiên cao hơn"
6. **Database screenshots**: 
   - SELECT kết quả 5 cột UC7
   - JSON_PRETTY của warning_details
   - So sánh 2 đơn stock allocation

---

# ✅ TIÊU CHÍ THÀNH CÔNG

## **Demo được coi là thành công khi:**

✅ **7/7 kịch bản** test đều hiển thị đúng UI và DB  
✅ **Toast notifications** màu sắc và message phù hợp 100%  
✅ **Modal 5 sections** hiển thị đầy đủ thông tin chính xác  
✅ **Database 5 cột UC7** lưu đúng giá trị theo công thức  
✅ **JSON warning_details** có đầy đủ keys và values đúng  
✅ **Stock allocation** ưu tiên đúng thứ tự (deadline → created_at)  
✅ **Công thức tính toán** Level 1, Level 2, NVL shifts đều chính xác  
✅ **Không có lỗi** hiển thị trên UI hay dữ liệu sai lệch trong DB

---

# 🚀 DEMO FLOW ĐỀ XUẤT (30 PHÚT)

### **Phần 1: Giới thiệu (5 phút)**
- Giải thích 5 cột UC7 mới trong database
- Giải thích công thức capacity Level 1 & Level 2
- Giải thích logic phân bổ stock theo ưu tiên

### **Phần 2: Demo 4 kịch bản chính (20 phút)**
1. **Scenario 1 (Normal)** - 4 phút
   - Tạo đơn → Toast xanh → Xem modal → Query DB → So sánh UI vs DB
   
2. **Scenario 2 (Level 2)** - 5 phút
   - Tạo đơn → Toast vàng → Xem modal section 3 (LEVEL 2) → Query DB capacity_level_used = 2
   
3. **Scenario 3 (Material Shortage)** - 6 phút
   - Giảm stock NVL → Tạo đơn → Toast cảnh báo NVL → Xem modal section 4 (chi tiết NVL) → Query DB material_details
   
4. **Scenario 5 (Rejection)** - 5 phút
   - Tạo đơn 50k với deadline 1 ngày → Toast đỏ → Query DB không có đơn → Chứng minh hệ thống từ chối

### **Phần 3: Q&A (5 phút)**
- Trả lời câu hỏi
- Giải thích thêm công thức nếu cần

---

# 🎉 KẾT LUẬN

Hướng dẫn này cung cấp **đầy đủ 7 kịch bản test** với:

✅ **Dữ liệu đầu vào cụ thể** cho từng test  
✅ **Các bước thực hiện** chi tiết từng bước  
✅ **Kết quả mong đợi** trên UI (toast + modal)  
✅ **SQL queries** để kiểm tra database sau mỗi test  
✅ **Checklist thành công** để xác nhận test đạt yêu cầu  
✅ **Chứng minh code kết nối chặt chẽ với DB** (UI → Code → DB → UI)

**Với hướng dẫn này, bạn có thể:**
- Test đầy đủ UC7 qua giao diện web
- Chứng minh cho stakeholders code hoạt động chính xác
- Demo tất cả 7 kịch bản trong 30 phút
- Xác minh mọi công thức tính toán đều đúng

---

**📝 Ghi chú**: Nếu gặp lỗi trong quá trình test, kiểm tra lại:
1. Database có đủ 5 cột UC7 chưa
2. `capacity_config` table có 2 levels chưa
3. Test data (finished_stock, material, product BOM) đã chuẩn bị đúng chưa
4. Code `OrderModel.php` checkCapacity() line 343+ đã cập nhật chưa
5. Code `BOD.php` addProject() line 625+ và updateProject() line 778+ đã lưu 5 cột UC7 chưa

**Good luck với demo! 🚀**
