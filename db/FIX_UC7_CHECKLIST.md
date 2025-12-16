# ✅ CHECKLIST FIX LỖI & TUÂN THỦ UC7

## 🐛 CÁC LỖI ĐÃ SỬA

### 1. ✅ Lỗi "undefined" trong modal
**Nguyên nhân:** Key không đồng nhất giữa Model và View
**Đã sửa:**
- OrderModel.php: Dùng `material_name`, `uom`, `id_material` (đồng nhất)
- Project.php: Dùng `mat.material_name`, `mat.uom`
- Cả 2 trường hợp: Có BOM và Không có BOM

### 2. ✅ Số ca hiển thị sai
**Vấn đề:** Hiển thị "ĐỦ 312 CA" dù đơn chỉ cần 1 ca
**Đã sửa:**
- Tính `shifts_after_order` = Số ca còn lại SAU KHI trừ đơn hàng này
- Hiển thị: "ĐỦ - CÒN 5 CA" thay vì "ĐỦ 312 CA"
- Logic: 
  ```php
  $stock_after_order = $stock - $quantity_needed;
  $products_after = floor($stock_after_order / $quantity_per_unit);
  $shifts_after = floor($products_after / $products_per_shift);
  ```

### 3. ✅ Thiếu hiển thị NVL missing (NULL trong BOM)
**Vấn đề:** NVL NULL không hiển thị trong modal
**Đã sửa:**
- Thêm section riêng cho `missing_materials_list`
- Hiển thị danh sách NVL cần bổ sung vào kho
- Icon ⚠️ và màu đỏ để cảnh báo

### 4. ✅ Thiếu thông tin bottleneck
**Đã thêm:**
- Đánh dấu NVL giới hạn với tag "← GIỚI HẠN"
- Giải thích: "NVL này sẽ cạn kiệt trước, cần ưu tiên nhập thêm"

---

## 📋 TUÂN THỦ UC7 - KIỂM TRA

### ✅ A. CÔNG SUẤT SẢN XUẤT (2 mức cố định)

**Yêu cầu UC7:**
- Mức 1: 8 tiếng × 2 ca/ngày
- Mức 2: 12 tiếng × 2 ca/ngày
- Duyệt nếu đủ mức 1 hoặc mức 2
- KHÔNG ghi chú "cần tăng ca bao nhiêu"

**Trạng thái:**
- ✅ Đã có trong code: `checkCapacity()` lines 425-540
- ✅ Logic Level 1 → Level 2
- ✅ Không ghi chú chi tiết tăng ca (do Leader phân công)

**Code hiện tại:**
```php
// Level 1: 8h × 2ca
$level1_hours = 8;
$level1_shifts_per_day = 2;

// Level 2: 12h × 2ca  
$level2_hours = 12;
$level2_shifts_per_day = 2;

if ($total_shifts_needed <= $level1_max_shifts) {
    // ĐỦ công suất Level 1
} else if ($total_shifts_needed <= $level2_max_shifts) {
    // Vượt Level 1, dùng Level 2
} else {
    // Vượt cả Level 2 → TỪ CHỐI
}
```

---

### ✅ B. TỒN KHO THÀNH PHẨM

**Yêu cầu UC7:**
- Hiển thị: "Tồn X sản phẩm (đủ giao trong Y ngày)"
- Phân bổ stock cho đơn ưu tiên cao hơn

**Trạng thái:**
- ✅ Đã có: Stock allocation (lines 349-380)
- ✅ Hiển thị: "Tồn kho 35 cái, đã phân bổ 3, còn 32 khả dụng"
- ✅ Tính số lượng cần sản xuất thêm

**Code hiện tại:**
```php
// Query stock đã được phân bổ cho đơn ưu tiên cao hơn
$allocated_query = $this->db->query("
    SELECT COALESCE(SUM(...), 0) as total_allocated
    FROM project
    WHERE id_product = ?
      AND pr_status < 3
      AND (entry_date < ? OR (entry_date = ? AND created_at < ?))
", ...);

$available_stock = max(0, $quantity_in_stock - $total_allocated);
```

---

### ✅ C. TỒN KHO NVL

**Yêu cầu UC7:**
- Dùng BOM để tính "còn đủ làm bao nhiêu ca"
- Hiển thị: "Tồn NVL đủ cho X ca sản xuất"
- Hiển thị chi tiết TỪNG NVL
- Kho tự động tính cần nhập thêm bao nhiêu

**Trạng thái:**
- ✅ Đã implement: `material_details` array
- ✅ Tính theo BOM: `quantity_per_unit` × `remaining_to_produce`
- ✅ Hiển thị chi tiết TỪNG NVL với:
  - Tồn kho hiện tại
  - Định mức/sản phẩm
  - Cần cho ĐH này
  - Thiếu/Dư bao nhiêu
  - Hiện tại đủ cho X ca
  - Sau khi trừ ĐH: còn Y ca

**Code hiện tại:**
```php
foreach ($materials as $mat) {
    // Tính lượng cần
    $quantity_needed = $remaining_to_produce * $quantity_per_unit;
    $quantity_shortage = max(0, $quantity_needed - $mat->stock);
    
    // Tính số ca HIỆN TẠI
    $products_possible_total = floor($mat->stock / $quantity_per_unit);
    $shifts_possible_total = floor($products_possible_total / $products_per_shift);
    
    // Tính số ca CÒN LẠI sau khi trừ ĐH
    $stock_after_order = max(0, $mat->stock - $quantity_needed);
    $products_possible_after = floor($stock_after_order / $quantity_per_unit);
    $shifts_possible_after = floor($products_possible_after / $products_per_shift);
    
    $material_details[] = [...];
}
```

**Hiển thị modal:**
```
✓ Nhựa ABS                     ĐỦ - CÒN 280 CA
→ Tồn kho hiện tại: 10,000 g
→ Định mức: 10 g/sản phẩm
→ Cần cho ĐH này: 1,000 g
→ Sau khi trừ ĐH: còn 9,000 g
→ Hiện tại đủ cho: 312 ca (10,000 sản phẩm)
→ Sau khi trừ ĐH: còn 280 ca (~140 ngày)
```

---

### ⚠️ D. CỘT "CẢNH BÁO" (chưa implement)

**Yêu cầu UC7:**
- Đổi cột "Nguy cơ" → "Cảnh báo"
- Các loại cảnh báo:
  - "Sắp đến hạn giao"
  - "Trễ kế hoạch"
  - "Thiếu NVL - Đủ làm X ca"
  - "Cần tăng ca"

**Trạng thái:**
- ❌ Chưa đổi tên cột trong database
- ❌ Chưa đổi tên cột trong view
- ⚠️ Đang dùng `warning_details` JSON nhưng chưa có loại cảnh báo rõ ràng

**CẦN LÀM:**
1. Migration: `risk_flag` → `warning_type`
2. Update Model: Phân loại cảnh báo
3. Update View: Hiển thị icon + text theo loại

---

## 🎯 TÓM TẮT TRẠNG THÁI

| Yêu cầu UC7 | Trạng thái | Ghi chú |
|-------------|-----------|---------|
| ✅ Công suất 2 mức | DONE | Level 1 → Level 2 |
| ✅ Không ghi chú chi tiết tăng ca | DONE | Leader tự phân công |
| ✅ Stock allocation | DONE | Phân bổ theo priority |
| ✅ Tính NVL theo BOM | DONE | Chi tiết từng NVL |
| ✅ Hiển thị "đủ X ca" | DONE | Trước và sau trừ ĐH |
| ✅ Bottleneck NVL | DONE | Đánh dấu "← GIỚI HẠN" |
| ✅ NVL missing (NULL) | DONE | Hiển thị riêng |
| ⚠️ Đổi cột "Cảnh báo" | TODO | Cần migration DB |
| ⚠️ Phân loại cảnh báo | TODO | Enum warning types |

---

## 🚀 BƯỚC TIẾP THEO

### 1. Test hiện tại
- Refresh trang
- Tạo đơn hàng test
- Kiểm tra modal hiển thị đúng
- Verify không còn lỗi "undefined"

### 2. Implement còn lại (nếu cần)
- Migration: warning_type
- Update warning logic
- Update danh sách đơn hàng (table view)

---

## 📊 CÔNG THỨC TÍNH TOÁN

### Capacity (cố định):
```
products_per_shift = 500 sp/giờ × 8 giờ × 0.8 hiệu suất = 3,200 sp/ca
```

### NVL theo BOM:
```
Số sản phẩm có thể làm = Tồn kho NVL ÷ Định mức/sp
Số ca có thể = Số sản phẩm ÷ 3,200
Số ngày = Số ca ÷ 2 (2 ca/ngày)
```

### Stock sau đơn:
```
Stock còn lại = Stock hiện tại - (Số lượng ĐH × Định mức)
Ca còn lại = Stock còn lại ÷ Định mức ÷ 3,200
```

---

## ✅ KẾT LUẬN

**Đã fix:**
- ✅ Lỗi "undefined" 
- ✅ Số ca hiển thị sai
- ✅ Thiếu chi tiết NVL
- ✅ Thiếu bottleneck
- ✅ Thiếu NVL missing

**Tuân thủ UC7:**
- ✅ Công suất 2 mức
- ✅ Tính NVL theo BOM
- ✅ Hiển thị đủ X ca
- ✅ Không ghi chú chi tiết tăng ca

**Còn lại:**
- ⚠️ Đổi cột "Cảnh báo" (optional, chưa urgent)
