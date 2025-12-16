# 🎨 MÔ TẢ CHI TIẾT MODAL "CHI TIẾT ĐƠN HÀNG" CHO TỪNG LOẠI CẢNH BÁO

> **Mục đích**: Mô tả chi tiết modal hiển thị gì khi click vào button "CÓ SẴN KHO" (xanh) hoặc icon 👁️ (mắt) ở từng kịch bản cảnh báo.

---

## 📋 CẤU TRÚC MODAL TỔNG QUAN

Modal "CHI TIẾT ĐƠN HÀNG" có **5 SECTIONS** chính:

```
┌─────────────────────────────────────────────────────────┐
│  📋 CHI TIẾT ĐƠN HÀNG                            [X]    │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ▼ SECTION 1: THÔNG TIN CƠ BẢN                        │
│     - Mã đơn hàng                                       │
│     - Khách hàng                                        │
│     - Sản phẩm                                          │
│     - Đường kính                                        │
│     - Số lượng yêu cầu                                  │
│     - Deadline (hạn giao)                               │
│                                                         │
│  ▼ SECTION 2: TỒN KHO THÀNH PHẨM                       │
│     [Nội dung thay đổi theo từng kịch bản]            │
│                                                         │
│  ▼ SECTION 3: CÔNG SUẤT SẢN XUẤT                       │
│     [Nội dung thay đổi theo từng kịch bản]            │
│                                                         │
│  ▼ SECTION 4: CHI TIẾT NGUYÊN VẬT LIỆU                │
│     [Nội dung thay đổi theo từng kịch bản]            │
│                                                         │
│  ▼ SECTION 5: CẢNH BÁO                                 │
│     [Chỉ hiển thị khi có cảnh báo thực sự]            │
│                                                         │
│                                      [Đóng]            │
└─────────────────────────────────────────────────────────┘
```

---

# 📊 CHI TIẾT MODAL CHO TỪNG KỊCH BẢN

---

## **SCENARIO 1: NORMAL - ĐỦ TỒN KHO** ✅

### **Khi nào xuất hiện:**
- Button cột "CẢNH BÁO": **"CÓ SẴN KHO"** (màu xanh lá)
- Điều kiện: `available_stock >= qty_request` (tồn kho đủ giao ngay)

### **Cách test:**
1. Tạo đơn hàng: TL-079, số lượng **20 cái**, deadline xa
2. Click button **"CÓ SẴN KHO"** (màu xanh)
3. Modal hiển thị

---

### **SECTION 1: THÔNG TIN CƠ BẢN**
```
📋 THÔNG TIN ĐƠN HÀNG
──────────────────────────────────────
Mã đơn hàng: PJ-TEST
Khách hàng: Tes Customer
Sản phẩm: Bút bi TL-079
Đường kính: 0.7 MM
Số lượng yêu cầu: 20 cái
Hạn giao: 31/12/2025
Trạng thái: ĐÃ DUYỆT
```

---

### **SECTION 2: TỒN KHO THÀNH PHẨM** (Màu xanh lá - Positive)
```
┌─────────────────────────────────────────────────────┐
│  ✅ TỒN KHO THÀNH PHẨM                              │ (Nền xanh nhạt #e8f5e9)
├─────────────────────────────────────────────────────┤
│  ✓ Tồn kho: 35 cái, dùng 20 cái cho đơn này        │
│  → Có thể giao ngay                                 │
│                                                     │
│  Chi tiết phân bổ:                                  │
│    • Tổng tồn kho hiện tại: 35 cái                 │
│    • Đã phân bổ cho đơn khác: 0 cái                │
│    • Khả dụng cho đơn này: 35 cái                  │
│    • Sử dụng cho đơn này: 20 cái                   │
│    • Còn lại sau đơn này: 15 cái                   │
└─────────────────────────────────────────────────────┘
```

**Màu sắc:**
- Background: `#e8f5e9` (xanh lá nhạt)
- Text chính: `#2e7d32` (xanh đậm)
- Icon: ✅ hoặc ✓

**Công thức hiển thị:**
```
total_stock (35) - allocated_to_others (0) = available (35)
available (35) >= qty_request (20) → ĐỦ
used (20), remaining = 35 - 20 = 15
```

---

### **SECTION 3: CÔNG SUẤT SẢN XUẤT** (Màu xanh - Positive)
```
┌─────────────────────────────────────────────────────┐
│  🏭 CÔNG SUẤT SẢN XUẤT                              │ (Nền xanh nhạt)
├─────────────────────────────────────────────────────┤
│  Mức công suất: KHÔNG CẦN SẢN XUẤT                  │
│  (Đủ hàng tồn kho, có thể giao ngay)               │
└─────────────────────────────────────────────────────┘
```

**Lý do:** `capacity_level_used = 0` (không cần sản xuất)

---

### **SECTION 4: CHI TIẾT NGUYÊN VẬT LIỆU**
```
(KHÔNG HIỂN THỊ - Vì không cần sản xuất)
```

**Lý do:** Khi đủ tồn kho thành phẩm, không cần kiểm tra NVL.

---

### **SECTION 5: CẢNH BÁO**
```
(KHÔNG HIỂN THỊ - Không có cảnh báo)
```

---

### **✅ Điểm đặc biệt:**
- **Màu xanh lá** chủ đạo (positive vibe)
- **Không có section 4 & 5** (không cần NVL và không có cảnh báo)
- **Button "CÓ SẴN KHO"** → Nhấn mạnh có thể giao ngay

---

## **SCENARIO 2: LEVEL 2 - VƯỢT CÔNG SUẤT LEVEL 1** ⚠️

### **Khi nào xuất hiện:**
- Button cột "CẢNH BÁO": **"CÓ SẴN KHO"** (màu xanh, nhưng toast vàng khi tạo)
- Điều kiện: `remaining_to_produce` vượt Level 1 (3,200 sp/ca) nhưng còn trong Level 2 (5,100 sp/ca)

### **Cách test:**
1. Tạo đơn: TL-079, số lượng **5000 cái**, deadline **hôm nay + 1 ngày**
2. Click button **"CÓ SẴN KHO"**
3. Modal hiển thị

---

### **SECTION 1: THÔNG TIN CƠ BẢN**
```
Mã đơn hàng: ORD-xxx-20251208-001
Sản phẩm: Bút bi TL-079
Số lượng yêu cầu: 5000 cái
Hạn giao: 09/12/2025 (còn 1 ngày)
```

---

### **SECTION 2: TỒN KHO THÀNH PHẨM** (Màu xanh nhạt + vàng)
```
┌─────────────────────────────────────────────────────┐
│  ✅ TỒN KHO THÀNH PHẨM                              │ (Nền xanh nhạt)
├─────────────────────────────────────────────────────┤
│  ✓ Tồn kho: 35 cái, dùng 35 cái                    │
│  ⚠️ Cần sản xuất thêm: 4,965 cái                   │
│                                                     │
│  Chi tiết phân bổ:                                  │
│    • Tổng tồn kho: 35 cái                          │
│    • Đã phân bổ cho đơn khác: 0 cái                │
│    • Khả dụng cho đơn này: 35 cái                  │
│    • Sử dụng từ tồn kho: 35 cái                    │
│    • Cần sản xuất: 4,965 cái                       │
└─────────────────────────────────────────────────────┘
```

**Status:** `stock_status = 'partial'`

---

### **SECTION 3: CÔNG SUẤT SẢN XUẤT** (Màu vàng cam - Warning)
```
┌─────────────────────────────────────────────────────┐
│  ⚠️ CÔNG SUẤT SẢN XUẤT                              │ (Nền vàng cam #fff3e0)
├─────────────────────────────────────────────────────┤
│  Mức công suất: LEVEL 2 (12 giờ × 2 ca/ngày = 24h) │
│  Số ca cần: 1 ca                                    │
│  Số ngày cần: 1 ngày                                │
│                                                     │
│  ⚠️ CẢNH BÁO:                                       │
│  Vượt Level 1 (8h×2ca=16h), chuyển sang Level 2    │
│  (12h×2ca=24h) - Cần tăng ca làm việc              │
│                                                     │
│  Chi tiết:                                          │
│    • Level 1 capacity: 3,200 sp/ca (8h, eff 80%)   │
│    • Level 2 capacity: 5,100 sp/ca (12h, eff 85%)  │
│    • Cần sản xuất: 4,965 cái                       │
│    • → Dùng Level 2: 1 ca (đủ)                     │
└─────────────────────────────────────────────────────┘
```

**Công thức:**
```
remaining_to_produce = 4965
Level 1 capacity = 3200 sp/ca → Không đủ
Level 2 capacity = 5100 sp/ca → Đủ
Shifts needed = ceil(4965 / 5100) = 1 ca
Days = ceil(1 / 2) = 1 ngày
```

---

### **SECTION 4: CHI TIẾT NGUYÊN VẬT LIỆU** (Màu xanh - Có đủ)
```
┌─────────────────────────────────────────────────────┐
│  📦 NGUYÊN VẬT LIỆU                                 │ (Nền xanh nhạt #e8f5e9)
├─────────────────────────────────────────────────────┤
│                                                     │
│  ✓ Nhựa PP (kg)                                     │ ĐỦ - CÒN 8 CA
│    → Tồn kho hiện tại: 300.00 kg                   │
│    → Định mức: 0.05 kg/sản phẩm                    │
│    → Cần cho ĐH này: 248.25 kg                     │
│    → Sau khi trừ ĐH: còn 51.75 kg                  │
│    → Hiện tại đủ cho: 10 ca (6000 sản phẩm)        │
│    → Sau khi trừ ĐH: còn 8 ca (~4 ngày)            │
│  ─────────────────────────────────────────────────  │
│  ✓ Mực xanh (lít)                                   │ ĐỦ - CÒN 12 CA
│    → Tồn kho: 50.00 lít                            │
│    → Định mức: 0.01 lít/sản phẩm                   │
│    → Cần cho ĐH: 49.65 lít                         │
│    → Sau khi trừ: còn 0.35 lít                     │
│    → Hiện tại đủ cho: 15 ca                        │
│    → Sau khi trừ: còn 12 ca (~6 ngày)              │
│  ─────────────────────────────────────────────────  │
│  ... (còn 3-5 NVL khác tương tự)                   │
│                                                     │
│  ⚠️ NVL giới hạn sản xuất: Mực xanh                │
│  → NVL này sẽ cạn kiệt trước, cần ưu tiên nhập     │
└─────────────────────────────────────────────────────┘
```

**Đặc điểm:**
- Mỗi NVL có **border trái màu xanh** (đủ)
- Hiển thị **đầy đủ 7 thông tin**: stock, định mức, cần cho ĐH, sau trừ, số ca hiện tại, số ca sau, số ngày
- **Bottleneck material** được highlight (NVL có số ca ít nhất)

---

### **SECTION 5: CẢNH BÁO** (Màu vàng cam)
```
┌─────────────────────────────────────────────────────┐
│  ⚠️ CẢNH BÁO                                        │ (Nền vàng cam #fff3e0)
├─────────────────────────────────────────────────────┤
│  ⚠️ Công suất: Vượt Level 1 (8h×2ca=16h),          │
│               chuyển sang Level 2 (12h×2ca=24h)     │
│                                                     │
│  📦 NVL: Ước tính đủ NVL cho ~8 ca                  │
│        (giới hạn bởi Mực xanh)                      │
└─────────────────────────────────────────────────────┘
```

---

### **✅ Điểm đặc biệt:**
- **Section 2**: Màu xanh (có stock) + text vàng (cần sản xuất)
- **Section 3**: Màu vàng cam (warning về Level 2)
- **Section 4**: Màu xanh (NVL đủ) + highlight bottleneck
- **Section 5**: Tổng hợp cảnh báo

---

## **SCENARIO 3: MATERIAL SHORTAGE - THIẾU NVL** 📦❌

### **Khi nào xuất hiện:**
- Button cột "CẢNH BÁO": **"CÓ SẴN KHO"** (màu xanh, nhưng có icon 📦 trong toast)
- Điều kiện: `material_warning` tồn tại (NVL không đủ cho sản xuất)

### **Cách test:**
1. Giảm tồn kho NVL: `UPDATE material SET stock = 5 WHERE id_material = 1001;`
2. Tạo đơn: TL-079, số lượng **1000 cái**, deadline +5 ngày
3. Click button **"CÓ SẴN KHO"**
4. Modal hiển thị

---

### **SECTION 1: THÔNG TIN CƠ BẢN**
```
Mã đơn hàng: ORD-xxx-20251208-002
Sản phẩm: Bút bi TL-079
Số lượng yêu cầu: 1000 cái
Hạn giao: 13/12/2025 (còn 5 ngày)
```

---

### **SECTION 2: TỒN KHO THÀNH PHẨM** (Màu xanh nhạt)
```
┌─────────────────────────────────────────────────────┐
│  ✅ TỒN KHO THÀNH PHẨM                              │
├─────────────────────────────────────────────────────┤
│  ✓ Tồn kho: 35 cái, dùng 35 cái                    │
│  ⚠️ Cần sản xuất thêm: 965 cái                     │
│                                                     │
│  Chi tiết:                                          │
│    • Tổng tồn kho: 35 cái                          │
│    • Sử dụng: 35 cái                               │
│    • Cần sản xuất: 965 cái                         │
└─────────────────────────────────────────────────────┘
```

---

### **SECTION 3: CÔNG SUẤT SẢN XUẤT** (Màu xanh - Đủ capacity)
```
┌─────────────────────────────────────────────────────┐
│  🏭 CÔNG SUẤT SẢN XUẤT                              │ (Nền xanh #e8f5e9)
├─────────────────────────────────────────────────────┤
│  Mức công suất: LEVEL 1 (8 giờ × 2 ca/ngày)        │
│  Số ca cần: 1 ca                                    │
│  Số ngày cần: 1 ngày                                │
│  Thời gian còn lại: 5 ngày                          │
│                                                     │
│  ✓ Capacity đủ (trong Level 1)                     │
└─────────────────────────────────────────────────────┘
```

---

### **SECTION 4: CHI TIẾT NGUYÊN VẬT LIỆU** (Màu ĐỎ - Warning)
```
┌─────────────────────────────────────────────────────┐
│  📦 NGUYÊN VẬT LIỆU                                 │ (Nền đỏ nhạt #ffebee)
├─────────────────────────────────────────────────────┤
│                                                     │
│  ❌ Nhựa PP (kg)                        THIẾU       │ (Border trái đỏ #c62828)
│    → Tồn kho hiện tại: 5.00 kg                     │
│    → Định mức: 0.05 kg/sản phẩm                    │
│    → Cần cho ĐH này: 50.00 kg                      │
│    → ❌ THIẾU: 45.00 kg (cần nhập thêm)            │
│    → Hiện tại đủ cho: 0 ca (100 sản phẩm)          │
│    → Sau khi trừ ĐH: còn 0 ca (~0 ngày)            │
│  ─────────────────────────────────────────────────  │
│  ✓ Mực xanh (lít)                      ĐỦ - CÒN 15 CA
│    → Tồn kho: 100.00 lít                           │
│    → Định mức: 0.01 lít/sản phẩm                   │
│    → Cần cho ĐH: 10.00 lít                         │
│    → ✓ ĐỦ: Dư 90.00 lít                            │
│    → Hiện tại đủ cho: 20 ca                        │
│    → Sau khi trừ: còn 15 ca (~7 ngày)              │
│  ─────────────────────────────────────────────────  │
│  ... (còn 3-5 NVL khác)                            │
│                                                     │
│  🔴 NVL giới hạn (Bottleneck): Nhựa PP             │
│  → NVL này THIẾU, cần nhập gấp trước khi sản xuất  │
└─────────────────────────────────────────────────────┘
```

**Đặc điểm:**
- **Background section**: Đỏ nhạt `#ffebee`
- **NVL thiếu**: Border trái đỏ, icon ❌, text "THIẾU" màu đỏ
- **NVL đủ**: Border trái xanh, icon ✓, text "ĐỦ" màu xanh
- **Bottleneck**: Màu đỏ đậm, nhấn mạnh cần nhập gấp

---

### **SECTION 5: CẢNH BÁO** (Màu đỏ)
```
┌─────────────────────────────────────────────────────┐
│  ⚠️ CẢNH BÁO                                        │ (Nền đỏ #ffebee)
├─────────────────────────────────────────────────────┤
│  📦 NVL: ⚠️ NVL chỉ đủ cho khoảng 0 ca,            │
│         cần nhập thêm!                              │
│                                                     │
│  Chi tiết:                                          │
│    • Nhựa PP: chỉ còn 5.00 kg (tối thiểu: 10.00)  │
│    • THIẾU 45.00 kg để sản xuất đơn này            │
└─────────────────────────────────────────────────────┘
```

---

### **✅ Điểm đặc biệt:**
- **Section 4**: Background ĐỎ (alert về NVL thiếu)
- **NVL thiếu**: Hiển thị rõ số lượng thiếu với icon ❌
- **NVL đủ**: Vẫn hiển thị để so sánh
- **Bottleneck material**: Highlight đỏ đậm

---

## **SCENARIO 4: MISSING BOM - SẢN PHẨM CHƯA CÓ BOM** ⚠️🔧

### **Khi nào xuất hiện:**
- Sản phẩm có `bom = NULL` hoặc BOM có NVL chưa tồn tại trong kho (`id_material = NULL`)
- Button: **"CÓ SẴN KHO"** (màu xanh, nhưng có cảnh báo BOM)

### **Cách test:**
1. Xóa BOM: `UPDATE product SET bom = NULL WHERE id_product = 1001;`
2. Tạo đơn: TL-079, số lượng **500 cái**, deadline +3 ngày
3. Click button **"CÓ SẴN KHO"**
4. Modal hiển thị

---

### **SECTION 1: THÔNG TIN CƠ BẢN**
```
Mã đơn hàng: ORD-xxx-20251208-003
Sản phẩm: Bút bi TL-079
Số lượng yêu cầu: 500 cái
Hạn giao: 11/12/2025 (còn 3 ngày)
```

---

### **SECTION 2 & 3**: Giống Scenario 2 (có stock + Level 1)

---

### **SECTION 4: CHI TIẾT NGUYÊN VẬT LIỆU** (Màu vàng cam - Warning)
```
┌─────────────────────────────────────────────────────┐
│  📦 NGUYÊN VẬT LIỆU (ƯỚC TÍNH)                      │ (Nền vàng #fff3e0)
├─────────────────────────────────────────────────────┤
│  ⚠️ Sản phẩm chưa có BOM chi tiết                   │
│  → Không thể tính chính xác NVL cần thiết          │
│  → Số liệu dưới đây chỉ mang tính THAM KHẢO        │
│                                                     │
│  ⚠️ Nhựa PP (kg)                    ƯỚC TÍNH: 5 CA │
│    → Tồn kho hiện tại: 50.00 kg                    │
│    → Min stock: 10.00 kg                           │
│    → Số ca ước tính: 5 ca                          │
│  ─────────────────────────────────────────────────  │
│  ⚠️ Mực xanh (lít)                  ƯỚC TÍNH: 5 CA │
│    → Tồn kho: 80.00 lít                            │
│    → Min stock: 15.00 lít                          │
│    → Số ca ước tính: 5 ca                          │
│  ─────────────────────────────────────────────────  │
│  ... (các NVL khác)                                │
│                                                     │
│  ⚠️ CHÚ Ý:                                          │
│  • Không có định mức (quantity_per_unit)           │
│  • Không tính được NVL cần cho đơn hàng            │
│  • Cần bổ sung BOM trước khi sản xuất              │
└─────────────────────────────────────────────────────┘
```

**Đặc điểm:**
- **Background**: Vàng cam `#fff3e0`
- **Không hiển thị**: `quantity_needed`, `quantity_shortage`, `stock_after_order`
- **Chỉ hiển thị**: `stock`, `min_stock`, `shifts_possible` (ước tính)
- **Warning text**: Nhấn mạnh "chưa có BOM, không chính xác"

---

### **SECTION 4B: TRƯỜNG HỢP BOM CÓ NVL CHƯA TỒN TẠI**

Nếu BOM có NVL nhưng `id_material = NULL`:

```
┌─────────────────────────────────────────────────────┐
│  📦 NGUYÊN VẬT LIỆU                                 │ (Nền vàng #fff3e0)
├─────────────────────────────────────────────────────┤
│  ... (các NVL có trong kho - hiển thị bình thường) │
│                                                     │
│  ⚠️ BOM THIẾU NVL (chưa có trong kho):             │ (Nền đỏ nhạt)
│    • Mực đỏ đặc biệt                               │
│    • Ruột bút ABC                                  │
│    • Nắp bút XYZ                                   │
│  → Cần bổ sung NVL vào kho trước khi sản xuất      │
└─────────────────────────────────────────────────────┘
```

---

### **SECTION 5: CẢNH BÁO** (Màu vàng cam)
```
┌─────────────────────────────────────────────────────┐
│  ⚠️ CẢNH BÁO                                        │
├─────────────────────────────────────────────────────┤
│  🔧 BOM: Sản phẩm chưa có BOM chi tiết hoặc        │
│         BOM có 3 NVL chưa tồn tại trong kho        │
│                                                     │
│  → Cần bổ sung BOM hoặc nhập NVL trước khi SX      │
└─────────────────────────────────────────────────────┘
```

---

## **SCENARIO 5: REJECTION - TỪ CHỐI ĐƠN HÀNG** ❌

### **Khi nào xuất hiện:**
- **KHÔNG CÓ MODAL** vì đơn hàng không được lưu vào database
- Toast đỏ hiển thị: "❌ Không thể tạo đơn hàng! Vượt công suất tối đa (Level 2)"

### **Cách test:**
1. Tạo đơn: TL-079, số lượng **50000 cái**, deadline **+1 ngày**
2. Click "Lưu"
3. **Toast đỏ xuất hiện** → Form quay lại trang thêm đơn hàng
4. **KHÔNG CÓ button "CÓ SẴN KHO"** vì đơn không được tạo

---

### **UI hiển thị:**
```
❌ TOAST (màu đỏ):
┌─────────────────────────────────────────────────────┐
│  ❌ Không thể tạo đơn hàng!                         │
│  Vượt công suất tối đa (Level 2), không thể SX     │
│  Cần 50,000 cái, chỉ làm được tối đa 10,200 cái    │
└─────────────────────────────────────────────────────┘

→ Quay lại form thêm đơn hàng (không redirect)
→ Database: 0 rows (đơn không được lưu)
```

**Không có modal** vì:
- `feasible = false` → Không lưu vào DB
- Không có ID đơn hàng → Không có gì để hiển thị chi tiết

---

## **SCENARIO 6: DEADLINE WARNING - GẦN HẠN GIAO** ⏰

### **Khi nào xuất hiện:**
- Button: **"CÓ SẴN KHO"** (màu xanh, nhưng toast có icon ⏰)
- Điều kiện: `days_remaining <= 3` (còn 3 ngày hoặc ít hơn đến deadline)

### **Cách test:**
1. Tạo đơn: TL-079, số lượng **2000 cái**, deadline **hôm nay + 2 ngày**
2. Click button **"CÓ SẴN KHO"**
3. Modal hiển thị

---

### **SECTION 1: THÔNG TIN CƠ BẢN**
```
Mã đơn hàng: ORD-xxx-20251208-004
Sản phẩm: Bút bi TL-079
Số lượng yêu cầu: 2000 cái
Hạn giao: 10/12/2025 (còn 2 ngày) ⏰ GẦN HẠN
```

**Đặc điểm:** Deadline có icon ⏰ và text "GẦN HẠN" màu đỏ/cam

---

### **SECTION 2 & 3 & 4**: Giống các scenario trước (tùy stock và capacity)

---

### **SECTION 5: CẢNH BÁO** (Màu cam đậm)
```
┌─────────────────────────────────────────────────────┐
│  ⚠️ CẢNH BÁO                                        │ (Nền cam #ffebee)
├─────────────────────────────────────────────────────┤
│  ⏰ Deadline: GẦN HẠN - CẦN ƯU TIÊN                 │
│              (Còn 2 ngày đến hạn giao)              │
│                                                     │
│  → Cần ưu tiên sản xuất và giao hàng sớm           │
│  → Chuẩn bị NVL và sắp xếp công suất trước         │
└─────────────────────────────────────────────────────┘
```

**Đặc điểm:**
- Icon ⏰ (đồng hồ) nổi bật
- Text màu cam/đỏ đậm
- Nhấn mạnh "CẦN ƯU TIÊN"

---

## **SCENARIO 7: STOCK ALLOCATION - PHÂN BỔ TỒN KHO** 🔄

### **Khi nào xuất hiện:**
- Có nhiều đơn hàng cùng sản phẩm với deadline khác nhau
- Đơn có deadline muộn hơn sẽ hiển thị "Tồn kho đã phân bổ cho đơn ưu tiên cao hơn"

### **Cách test:**
1. Tạo **Đơn 1**: TL-079, số lượng **30 cái**, deadline **2025-12-31** (xa)
2. Tạo **Đơn 2**: TL-079, số lượng **20 cái**, deadline **hôm nay + 3 ngày** (gần)
3. Click button "CÓ SẴN KHO" của **Đơn 1** (deadline xa)
4. Modal hiển thị

---

### **SECTION 2: TỒN KHO THÀNH PHẨM** (Màu vàng cam - Warning)
```
┌─────────────────────────────────────────────────────┐
│  ⚠️ TỒN KHO THÀNH PHẨM                              │ (Nền vàng cam #fff3e0)
├─────────────────────────────────────────────────────┤
│  ⚠️ Tồn kho 35 cái đã phân bổ cho các đơn          │
│     ưu tiên cao hơn (deadline sớm hơn)              │
│  → Cần sản xuất 15 cái cho đơn này                 │
│                                                     │
│  Chi tiết phân bổ:                                  │
│    • Tổng tồn kho hiện tại: 35 cái                 │
│    • Đã phân bổ cho đơn khác: 20 cái               │
│      └─ ORD-xxx-xxx-002 (deadline 11/12/2025)      │ ← Đơn ưu tiên cao hơn
│    • Khả dụng cho đơn này: 15 cái                  │
│    • Sử dụng từ tồn kho: 15 cái                    │
│    • Cần sản xuất: 15 cái                          │
│                                                     │
│  📋 Thứ tự ưu tiên:                                 │
│    1. Deadline sớm hơn → Ưu tiên cao hơn           │
│    2. Nếu cùng deadline → Đơn tạo trước ưu tiên    │
└─────────────────────────────────────────────────────┘
```

**Đặc điểm:**
- **Background**: Vàng cam (warning về phân bổ)
- **Hiển thị rõ**: Đơn nào đã lấy stock (với mã đơn và deadline)
- **Giải thích logic**: Thứ tự ưu tiên `ORDER BY entry_date ASC, created_at ASC`

---

### **SECTION 3 & 4**: Hiển thị capacity và NVL cho phần cần sản xuất (15 cái)

---

### **SECTION 5: CẢNH BÁO** (Màu vàng cam)
```
┌─────────────────────────────────────────────────────┐
│  ⚠️ CẢNH BÁO                                        │
├─────────────────────────────────────────────────────┤
│  🔄 Stock: Tồn kho đã phân bổ cho đơn ưu tiên      │
│          cao hơn (20 cái cho ORD-xxx-002)          │
│                                                     │
│  → Đơn này cần sản xuất thêm 15 cái                │
│  → Có thể điều chỉnh deadline để tăng ưu tiên      │
└─────────────────────────────────────────────────────┘
```

---

# 📊 BẢNG SO SÁNH MODAL CHO 7 KỊCH BẢN

| **Kịch bản** | **Section 2 màu** | **Section 3 màu** | **Section 4 màu** | **Section 5** | **Điểm nhấn** |
|--------------|-------------------|-------------------|-------------------|---------------|---------------|
| **1. Normal** | ✅ Xanh | ✅ Xanh | (Không có) | (Không có) | "Giao ngay" |
| **2. Level 2** | ✅ Xanh + ⚠️ | ⚠️ Vàng cam | ✅ Xanh | ⚠️ Vàng | "Level 2" + Bottleneck |
| **3. NVL thiếu** | ✅ Xanh + ⚠️ | ✅ Xanh | ❌ Đỏ | ❌ Đỏ | "THIẾU" + Số lượng thiếu |
| **4. Missing BOM** | ✅ Xanh + ⚠️ | ✅ Xanh | ⚠️ Vàng | ⚠️ Vàng | "Ước tính" + Không chính xác |
| **5. Từ chối** | (Không có modal) | - | - | - | Toast đỏ + Không lưu DB |
| **6. Deadline gần** | ✅ Xanh | ✅ Xanh | ✅ Xanh | ⏰ Cam | "GẦN HẠN" + Icon ⏰ |
| **7. Stock phân bổ** | ⚠️ Vàng cam | ✅/⚠️ | ✅/⚠️ | 🔄 Vàng | "Đã phân bổ" + Mã đơn ưu tiên |

---

# 🎨 MÀU SẮC VÀ Ý NGHĨA

| **Màu** | **Hex Code** | **Ý nghĩa** | **Dùng cho** |
|---------|--------------|-------------|--------------|
| ✅ Xanh lá | `#e8f5e9` (bg) `#2e7d32` (text) | Positive, OK | Đủ stock, đủ capacity, NVL đủ |
| ⚠️ Vàng cam | `#fff3e0` (bg) `#ff6f00` (text) | Warning | Level 2, deadline gần, missing BOM, allocation |
| ❌ Đỏ | `#ffebee` (bg) `#c62828` (text) | Error, Thiếu | NVL thiếu, từ chối đơn hàng |
| 🔵 Xanh dương | `#e3f2fd` (bg) `#1976d2` (text) | Info | Thông tin bổ sung |

---

# 🧪 CÁCH TEST MODAL CHO TỪNG KỊCH BẢN

## **Test flow chung:**

```
1. Đăng nhập BOD
2. Vào "Quản lý Đơn hàng"
3. Tạo đơn hàng theo data từng kịch bản
4. Xác nhận toast hiển thị đúng
5. Click button "CÓ SẴN KHO" (hoặc icon 👁️)
6. ✅ KIỂM TRA MODAL:
   ├─ Section 1: Thông tin đúng
   ├─ Section 2: Màu sắc + nội dung phù hợp
   ├─ Section 3: Capacity đúng Level
   ├─ Section 4: NVL chi tiết đầy đủ (nếu có)
   └─ Section 5: Cảnh báo chính xác
7. So sánh modal với database:
   ├─ SELECT warning_details
   └─ JSON_PRETTY để xem cấu trúc
8. Xác nhận tất cả dữ liệu khớp
```

---

## **Checklist test modal:**

### ✅ **Layout và UI**
- [ ] Modal hiển thị đúng giữa màn hình
- [ ] Nút [X] đóng modal hoạt động
- [ ] Scroll được khi nội dung dài
- [ ] Responsive trên các màn hình khác nhau

### ✅ **Section 1: Thông tin cơ bản**
- [ ] Mã đơn hàng chính xác
- [ ] Khách hàng, sản phẩm đúng
- [ ] Số lượng và deadline hiển thị đúng
- [ ] Format ngày tháng chuẩn (DD/MM/YYYY)

### ✅ **Section 2: Tồn kho thành phẩm**
- [ ] Màu sắc phù hợp với trạng thái (xanh/vàng/đỏ)
- [ ] `stock_breakdown` hiển thị đầy đủ 5 giá trị
- [ ] Công thức: `total - allocated = available` ✓
- [ ] Text mô tả rõ ràng (giao ngay/cần SX)

### ✅ **Section 3: Công suất sản xuất**
- [ ] Level 0/1/2 chính xác
- [ ] Số ca và số ngày tính đúng
- [ ] Công thức: `ceil(qty / capacity)` ✓
- [ ] Cảnh báo Level 2 hiển thị (nếu có)

### ✅ **Section 4: Chi tiết NVL**
- [ ] Mỗi NVL có border trái màu phù hợp
- [ ] Icon ✓/❌/⚠️ chính xác
- [ ] Hiển thị đủ 7 thông tin (nếu có BOM)
- [ ] Bottleneck material được highlight
- [ ] Missing materials (NULL) hiển thị riêng (nếu có)

### ✅ **Section 5: Cảnh báo**
- [ ] Chỉ hiển thị khi có cảnh báo THỰC SỰ
- [ ] Icon phù hợp (⚠️/❌/⏰/📦/🔄)
- [ ] Text mô tả đầy đủ và rõ ràng
- [ ] Màu sắc nhấn mạnh mức độ nghiêm trọng

### ✅ **So sánh với Database**
- [ ] `warning_details` JSON parse thành công
- [ ] Tất cả keys trong JSON hiển thị trên UI
- [ ] Giá trị số khớp 100% (stock, qty, shifts, days)
- [ ] `capacity_level_used` khớp với "Level X" trên UI

---

# 🎯 KẾT LUẬN VÀ ĐÁNH GIÁ

## **✅ Đã đủ các trường hợp test:**

1. ✅ **Normal** - Đủ tồn kho giao ngay
2. ✅ **Level 2** - Vượt công suất Level 1
3. ✅ **Material Shortage** - Thiếu NVL
4. ✅ **Missing BOM** - Chưa có BOM hoặc BOM thiếu NVL
5. ✅ **Rejection** - Từ chối đơn (không có modal)
6. ✅ **Deadline Warning** - Gần hạn giao
7. ✅ **Stock Allocation** - Phân bổ tồn kho theo ưu tiên

## **✅ Modal design hợp lý:**

### **Ưu điểm:**
- ✅ **5 sections rõ ràng**: Thông tin → Tồn kho → Capacity → NVL → Cảnh báo
- ✅ **Màu sắc phân biệt**: Xanh (OK), Vàng (Warning), Đỏ (Error)
- ✅ **Chi tiết đầy đủ**: Mỗi NVL có 7 thông tin (stock, định mức, cần, thiếu, ca, ngày)
- ✅ **Highlight bottleneck**: NVL giới hạn được nhấn mạnh
- ✅ **Stock allocation logic**: Giải thích rõ thứ tự ưu tiên
- ✅ **Responsive info**: Chỉ hiển thị sections cần thiết cho từng case

### **Điểm cải tiến (nếu cần):**
- 💡 **Thêm actions**: Button "In đơn hàng", "Gửi email KH" trong modal
- 💡 **Timeline visualization**: Biểu đồ timeline cho deadline và ngày SX dự kiến
- 💡 **Material chart**: Biểu đồ tròn % tồn kho NVL
- 💡 **History log**: Lịch sử thay đổi đơn hàng (nếu có update)

## **✅ Hợp lý cho demo:**

**Với 7 kịch bản này, bạn có thể chứng minh:**
1. ✅ **UI → Code → DB → UI**: Vòng lặp dữ liệu hoàn chỉnh
2. ✅ **Màu sắc phân biệt**: User nhận biết ngay tình trạng đơn hàng
3. ✅ **Chi tiết minh bạch**: Tất cả tính toán đều hiển thị rõ ràng
4. ✅ **Logic nghiệp vụ**: Phân bổ stock, ưu tiên deadline, bottleneck NVL
5. ✅ **Cảnh báo chủ động**: Hệ thống cảnh báo trước khi có vấn đề

**→ Modal design rất hợp lý và đủ để demo cho stakeholders!** 🎉

---

**📝 Tài liệu này mô tả chi tiết modal cho 7 kịch bản. Sử dụng cùng với `HUONG_DAN_TEST_GIAO_DIEN_UC7.md` để test đầy đủ.**
