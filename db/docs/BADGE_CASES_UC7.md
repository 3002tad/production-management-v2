# 🎨 CÁC TRƯỜNG HỢP BADGE CẢNH BÁO UC7

## 📊 TỔNG QUAN

Hệ thống UC7 có **6 trường hợp badge** với **4 mức độ ưu tiên** khác nhau.

### Thứ tự ưu tiên (từ cao xuống thấp):
1. 🔴 **BOM thiếu NVL** (DANGER - Đỏ) - KHÔNG THỂ SẢN XUẤT
2. 🟠 **NVL không đủ** (WARNING - Cam) - Cần nhập thêm NVL
3. 🔵 **Có sẵn kho** (INFO - Xanh dương) - Tốt nhất
4. 🟠 **Gần deadline** (WARNING - Cam) - Cần ưu tiên
5. 🟠 **Level 2** (WARNING - Cam) - Cần tăng ca
6. 🟢 **Bình thường** (SUCCESS - Xanh lá) - OK

---

## 🔴 **TRƯỜNG HỢP 1: BOM THIẾU NVL** 

### Hiển thị:
```
🔴 ⚠️ BOM thiếu NVL
```

### Điều kiện:
```php
if ($has_missing_material) {
    $badge_color = 'danger'; // ĐỎ
    $badge_icon = '⚠️';
    $badge_text = 'BOM thiếu NVL';
}
```

### Khi nào xảy ra:
- BOM sản phẩm có NVL nhưng **id_material = NULL** (chưa tồn tại trong bảng `material`)
- Ví dụ: BOM yêu cầu "Nhựa PVC đặc biệt" nhưng trong kho chưa có NVL này

### Mức độ nghiêm trọng:
**⭐⭐⭐⭐⭐ CRITICAL** - Ưu tiên cao nhất

### Lý do ưu tiên cao nhất:
- **KHÔNG THỂ SẢN XUẤT** cho đến khi bổ sung NVL vào kho
- Không thể giải quyết bằng cách nhập thêm stock (vì NVL chưa tồn tại)
- Cần action: Tạo master data cho NVL mới hoặc thay thế BOM

### Thông tin chi tiết (modal):
```
⚠️ BOM THIẾU NVL
Sản phẩm có 2 NVL chưa tồn tại trong kho:
   → Nhựa PVC đặc biệt
   → Mực in UV-resistant

→ Cần bổ sung NVL vào kho trước khi sản xuất
```

### Hành động cần làm:
1. Liên hệ phòng Kho để thêm NVL vào hệ thống
2. Hoặc cập nhật BOM với NVL thay thế đã có sẵn
3. Hoặc đàm phán với khách hàng thay đổi spec

---

## 🟠 **TRƯỜNG HỢP 2: NVL KHÔNG ĐỦ**

### Hiển thị:
```
🟠 📦 NVL không đủ
```

### Điều kiện:
```php
elseif ($has_material_warning) {
    $badge_color = 'warning'; // CAM
    $badge_icon = '📦';
    $badge_text = 'NVL không đủ';
}
```

### Khi nào xảy ra:
- NVL **tồn tại trong kho** nhưng **stock < quantity_needed**
- Ví dụ: Cần 500 kg nhựa nhưng chỉ còn 300 kg

### Mức độ nghiêm trọng:
**⭐⭐⭐⭐ HIGH**

### Chi tiết:
```
📦 NGUYÊN VẬT LIỆU

❌ THIẾU - Nhựa PVC
📦 Tồn kho: 300 kg
📏 Định mức BOM: 0.5 kg/sản phẩm
🎯 Cần cho ĐH này: 500 kg

⚠️ THIẾU 200 kg (cần nhập thêm)

Công suất hiện tại:
→ Đủ cho: 10 ca (6,000 sản phẩm)
→ Sau ĐH: còn 5 ca (~2 ngày)
```

### Hành động cần làm:
1. Nhập thêm NVL thiếu
2. Hoặc tạm dừng sản xuất đơn khác để dành NVL
3. Hoặc chia đơn thành nhiều lô (làm trước phần có NVL)

---

## 🔵 **TRƯỜNG HỢP 3: CÓ SẴN KHO**

### Hiển thị:
```
🔵 🏪 Có sẵn kho
```

### Điều kiện:
```php
elseif ($stock_status === 'sufficient' && $capacity_level == 0) {
    $badge_color = 'info'; // XANH DƯƠNG
    $badge_icon = '🏪';
    $badge_text = 'Có sẵn kho';
}
```

### Khi nào xảy ra:
- Tồn kho thành phẩm **đủ để giao ngay**
- **KHÔNG CẦN SẢN XUẤT** (`capacity_level_used = 0`)
- Không có cảnh báo về NVL hoặc deadline

### Mức độ:
**⭐⭐⭐ NORMAL** - Trạng thái tốt nhất

### Chi tiết:
```
🏪 TỒN KHO THÀNH PHẨM
✓ Tồn kho: 100 cái
  (đã phân bổ 50 cái cho đơn ưu tiên cao hơn, còn 50 cái khả dụng)
  Dùng 30 cái cho đơn này → Có thể giao ngay

⚙️ CÔNG SUẤT SẢN XUẤT
✓ Không cần sản xuất (đủ tồn kho)
```

### Ý nghĩa:
- ✅ Đơn hàng có thể giao NGAY
- ✅ Không tốn công suất máy
- ✅ Tiết kiệm NVL
- ✅ Giảm lead time

### Lưu ý:
Chỉ hiển thị khi **CẢ HAI** điều kiện:
1. `stock_status === 'sufficient'` (đủ stock)
2. `capacity_level_used == 0` (không cần SX)

**Nếu thiếu điều kiện 2**: Mặc dù có stock nhưng vẫn cần SX thêm → Badge "Bình thường"

---

## 🟠 **TRƯỜNG HỢP 4: GẦN DEADLINE**

### Hiển thị:
```
🟠 ⏰ Gần deadline
```

### Điều kiện:
```php
elseif ($has_deadline_warning) {
    $badge_color = 'warning'; // CAM
    $badge_icon = '⏰';
    $badge_text = 'Gần deadline';
}
```

### Khi nào xảy ra:
**Level 1** (công suất thường):
- Còn ≤ 7 ngày
- HOẶC: `days_remaining < (days_needed + 2)` (không đủ thời gian + 2 ngày buffer)

**Level 2** (tăng ca):
- Còn ≤ 5 ngày
- HOẶC: `days_remaining < (days_needed + 1)` (không đủ thời gian + 1 ngày buffer)

### Mức độ:
**⭐⭐⭐ MEDIUM-HIGH**

### Chi tiết:
```
⏰ DEADLINE
Gần deadline - cần ưu tiên
Còn 5 ngày, cần 4 ngày sản xuất

⚙️ CÔNG SUẤT SẢN XUẤT
✓ Level 1: 8 giờ × 2 ca/ngày
→ Công suất máy: 500 sp/h
→ Sản phẩm/ca: 3,200 cái (500 × 8h × 80%)
→ Cần 8 ca (~4 ngày)
```

### Hành động cần làm:
1. Ưu tiên sản xuất đơn này trước
2. Chuẩn bị NVL sẵn sàng
3. Monitor tiến độ hàng ngày
4. Có thể cần tăng ca nếu gặp vấn đề

---

## 🟠 **TRƯỜNG HỢP 5: LEVEL 2**

### Hiển thị:
```
🟠 ⚙️ Level 2
```

### Điều kiện:
```php
elseif ($has_capacity_warning && $capacity_level == 2) {
    $badge_color = 'warning'; // CAM
    $badge_icon = '⚙️';
    $badge_text = 'Level 2';
}
```

### Khi nào xảy ra:
- Công suất Level 1 (8h × 2ca = 16h/ngày) **KHÔNG ĐỦ**
- Cần chuyển sang **Level 2** (12h × 2ca = 24h/ngày)
- Tức là cần **TĂNG CA** thêm 4 giờ/ca

### Mức độ:
**⭐⭐⭐ MEDIUM**

### Chi tiết:
```
⚠️ CÔNG SUẤT SẢN XUẤT
Vượt Level 1 (8h×2ca=16h), chuyển sang Level 2 (12h×2ca=24h)

→ Công suất máy: 500 sp/h
→ Level 1: 3,200 cái/ca (500 × 8h × 80%)
→ Level 2: 5,100 cái/ca (500 × 12h × 85%)
→ Cần 4 ca Level 2 (~2 ngày)
```

### So sánh Level 1 vs Level 2:

| Tiêu chí | Level 1 | Level 2 |
|----------|---------|---------|
| Giờ/ca | 8h | 12h (+4h OT) |
| Ca/ngày | 2 | 2 |
| Giờ/ngày | 16h | 24h |
| Hiệu suất | 80% | 85% |
| SP/ca | 3,200 | 5,100 |
| SP/ngày | 6,400 | 10,200 |
| Chi phí | Bình thường | +30% (OT) |

### Hành động cần làm:
1. Xác nhận lịch tăng ca với bộ phận nhân sự
2. Chuẩn bị đủ NVL cho ca đêm
3. Đảm bảo bảo trì máy móc trước khi chạy dài
4. Tính toán chi phí tăng ca

---

## 🟢 **TRƯỜNG HỢP 6: BÌNH THƯỜNG**

### Hiển thị:
```
🟢 ✓ Bình thường
```

### Điều kiện:
```php
elseif ($capacity_level == 1 || $stock_status === 'partial' || $stock_status === 'depleted') {
    $badge_color = 'success'; // XANH LÁ
    $badge_icon = '✓';
    $badge_text = 'Bình thường';
}
```

### Khi nào xảy ra:
- Cần sản xuất **Level 1** (8h × 2ca)
- Có thể có một phần stock hoặc không có stock
- **KHÔNG có** cảnh báo nghiêm trọng:
  - ✓ NVL đủ
  - ✓ Deadline còn xa
  - ✓ Không cần Level 2

### Mức độ:
**⭐⭐ NORMAL** - Trạng thái bình thường

### Chi tiết:
```
🏪 TỒN KHO THÀNH PHẨM
✓ Tồn kho: 35 cái (đã phân bổ 30 cái cho đơn ưu tiên cao hơn, 
                    còn 5 cái khả dụng)
Dùng 5 cái, cần sản xuất thêm 95 cái

⚙️ CÔNG SUẤT SẢN XUẤT
✓ Level 1: 8 giờ × 2 ca/ngày
→ Công suất máy: 500 sp/h
→ Sản phẩm/ca: 3,200 cái (500 × 8h × 80%)
→ Cần 1 ca (~1 ngày)

⏰ DEADLINE
Bình thường - còn thời gian
```

### Ý nghĩa:
- Đơn hàng trong tầm kiểm soát
- Sản xuất theo lịch bình thường
- Không cần action đặc biệt

---

## 📋 BẢNG TỔNG HỢP

| # | Badge | Màu | Icon | Điều kiện chính | Mức độ | Action |
|---|-------|-----|------|----------------|--------|--------|
| 1 | **BOM thiếu NVL** | 🔴 Đỏ | ⚠️ | `has_missing_material` | CRITICAL | Bổ sung NVL vào master data |
| 2 | **NVL không đủ** | 🟠 Cam | 📦 | `has_material_warning` | HIGH | Nhập thêm NVL |
| 3 | **Có sẵn kho** | 🔵 Xanh dương | 🏪 | `stock=sufficient` && `level=0` | GOOD | Giao ngay |
| 4 | **Gần deadline** | 🟠 Cam | ⏰ | `has_deadline_warning` | MEDIUM | Ưu tiên sản xuất |
| 5 | **Level 2** | 🟠 Cam | ⚙️ | `capacity_level=2` | MEDIUM | Tăng ca |
| 6 | **Bình thường** | 🟢 Xanh lá | ✓ | `capacity_level=1` | NORMAL | Sản xuất bình thường |

---

## 🎯 LOGIC DECISION TREE

```
START
  │
  ├─ Has missing_material? (BOM có NVL NULL?)
  │  └─ YES → 🔴 BOM thiếu NVL (STOP - ƯU TIÊN CAO NHẤT)
  │  └─ NO → Tiếp tục
  │
  ├─ Has material_warning? (Stock NVL không đủ?)
  │  └─ YES → 🟠 NVL không đủ (STOP)
  │  └─ NO → Tiếp tục
  │
  ├─ Stock sufficient AND capacity_level = 0?
  │  └─ YES → 🔵 Có sẵn kho (STOP)
  │  └─ NO → Tiếp tục
  │
  ├─ Has deadline_warning? (Gần deadline?)
  │  └─ YES → 🟠 Gần deadline (STOP)
  │  └─ NO → Tiếp tục
  │
  ├─ capacity_level = 2 AND has_capacity_warning?
  │  └─ YES → 🟠 Level 2 (STOP)
  │  └─ NO → Tiếp tục
  │
  └─ Default → 🟢 Bình thường
```

---

## 💡 LƯU Ý QUAN TRỌNG

### 1. **Thứ tự ưu tiên rất quan trọng**
Code check theo thứ tự từ trên xuống. Điều kiện nào match trước sẽ dừng lại.

**Ví dụ**: Nếu đơn vừa "BOM thiếu NVL" vừa "Gần deadline":
- ✅ Hiển thị: 🔴 BOM thiếu NVL
- ❌ KHÔNG hiển thị: 🟠 Gần deadline (bị skip)

→ Đúng logic vì BOM thiếu = KHÔNG THỂ SẢN XUẤT, ưu tiên hơn deadline

### 2. **Badge "Có sẵn kho" yêu cầu 2 điều kiện**
```php
$stock_status === 'sufficient' && $capacity_level == 0
```

**Nếu thiếu 1 trong 2**:
- Có stock nhưng `capacity_level = 1` → Badge "Bình thường" (vì vẫn cần SX thêm)
- `capacity_level = 0` nhưng stock không đủ → Không thể xảy ra (logic sai)

### 3. **Badge "Level 2" chỉ hiện khi có cảnh báo**
```php
$has_capacity_warning && $capacity_level == 2
```

Nếu `capacity_level = 2` nhưng không có `capacity_warning` → Badge "Bình thường"

### 4. **Deadline warning có 2 mức**
- Level 1: ≤ 7 ngày hoặc thiếu 2 ngày buffer
- Level 2: ≤ 5 ngày hoặc thiếu 1 ngày buffer

→ Level 2 khắt khe hơn vì đã tăng ca, thời gian ít hơn

---

## 🧪 TEST CASES

### Case 1: Đơn có sẵn kho
```
Input:
- Stock: 100 cái
- qty_request: 50 cái
- capacity_level_used: 0

Expected: 🔵 Có sẵn kho
```

### Case 2: Đơn cần sản xuất bình thường
```
Input:
- Stock: 10 cái
- qty_request: 100 cái
- capacity_level_used: 1
- Deadline: 20 ngày
- NVL: Đủ

Expected: 🟢 Bình thường
```

### Case 3: Đơn gần deadline
```
Input:
- qty_request: 1,000 cái
- Deadline: 5 ngày
- days_needed: 3 ngày
- Buffer: 2 ngày

Expected: 🟠 Gần deadline (vì 5 < 3+2)
```

### Case 4: Đơn Level 2
```
Input:
- qty_request: 20,000 cái
- Level 1 capacity: 16,000 cái
- Level 2 capacity: 25,000 cái
- capacity_level_used: 2

Expected: 🟠 Level 2
```

### Case 5: NVL không đủ
```
Input:
- NVL trong kho: 300 kg
- NVL cần: 500 kg
- has_material_warning: true

Expected: 🟠 NVL không đủ
```

### Case 6: BOM thiếu NVL
```
Input:
- BOM có NVL: "Nhựa PVC đặc biệt" (id_material = NULL)
- has_missing_material: true

Expected: 🔴 BOM thiếu NVL (ưu tiên cao nhất)
```

---

## 📞 SUMMARY

**6 trường hợp badge** theo thứ tự ưu tiên:

1. 🔴 **BOM thiếu NVL** - KHÔNG THỂ SX → Action: Bổ sung master data
2. 🟠 **NVL không đủ** - Cần nhập thêm → Action: Nhập NVL
3. 🔵 **Có sẵn kho** - Tốt nhất → Action: Giao ngay
4. 🟠 **Gần deadline** - Cần ưu tiên → Action: Lên kế hoạch
5. 🟠 **Level 2** - Cần tăng ca → Action: Chuẩn bị OT
6. 🟢 **Bình thường** - OK → Action: Sản xuất theo lịch

**Logic decision**: Check từ trên xuống, điều kiện nào match trước thì dừng.
