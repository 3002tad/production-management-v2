# 🧪 HƯỚNG DẪN TEST TẤT CẢ TRẠNG THÁI ĐƠN HÀNG

## 📊 TỔNG QUAN DỮ LIỆU HIỆN CÓ

### **Sản phẩm trong hệ thống:**
| ID | Tên sản phẩm | BOM | Tồn kho TP |
|----|--------------|-----|------------|
| 1001 | Bút bi TL-079 | 4 NVL | 35 cái |
| 1002 | Bút bi TL-050 | 4 NVL | 0 cái |
| 1003 | Bút bi TL-100 | 4 NVL | 0 cái |
| 1006 | Bút bi TEST-H2-001 | 3 NVL | 0 cái |
| 1007 | danh | 2 NVL | 0 cái |

### **Nguyên vật liệu trong kho:**
| ID | Tên NVL | Tồn kho | Min stock | Đơn vị |
|----|---------|---------|-----------|--------|
| 1001 | Test Matereal | 5,000 g | 1,000 g | g |
| 1002 | Nhựa ABS | 10,000 g | 1,000 g | g |
| 1003 | Mực gel xanh | 5,000 g | 1,000 g | g |
| 1004 | Mực gel đen | 5,000 g | 1,000 g | g |
| 1005 | Bi kim loại 0.5mm | 2,000 g | 500 g | mm |
| 1006 | Bi kim loại 0.7mm | 3,000 g | 500 g | mm |
| 1007 | Bi kim loại 1.0mm | 2,000 g | 500 g | mm |
| 1008 | Lò xo thép | 1,000 g | 1,000 g | g |

### **Đơn hàng hiện có:**
| Mã đơn | Sản phẩm | Số lượng | Deadline | Trạng thái |
|--------|----------|----------|----------|-----------|
| ORD-1001-20251207-001 | TL-079 | 3 | 10/12/2025 | ĐÃ DUYỆT |
| PJ-TEST | TL-079 | 10 | 31/12/2025 | ĐÃ DUYỆT |
| ORD-1001-20251207-002 | TL-079 | 50 | 31/12/2025 | ✓ BÌNH THƯỜNG |
| ORD-1001-20251207-003 | TL-079 | 600,000 | 31/12/2025 | 🔴 CẢNH BÁO |
| ORD-1001-20251207-004 | danh | 10 | 13/12/2025 | ⚠️ THIẾU NVL |
| ORD-1002-20251207-001 | TL-050 | 550,000 | 13/12/2025 | ✓ OK |

---

## 🎯 KỊCH BẢN TEST CHI TIẾT

### ✅ **TEST 1: ĐƠN HÀNG BÌNH THƯỜNG (OK)**

**Mục đích:** Test đơn hàng đủ mọi điều kiện

**Dữ liệu test:**
- Sản phẩm: **TL-079** (có tồn kho 35, BOM đầy đủ)
- Số lượng: **50 cái**
- Deadline: 31/12/2025 (còn 24 ngày)

**Các bước:**
1. Đăng nhập BOD
2. Click **"➕ Thêm đơn hàng"**
3. Điền:
   ```
   Khách hàng: Tes Customer
   Sản phẩm: TL-079
   Số lượng: 50
   Entry date: 31/12/2025
   Priority: Medium
   ```
4. Click **"💾 Lưu"**

**Kết quả mong đợi:**

**📦 Modal "CHI TIẾT ĐƠN HÀNG":**

```
✓ TỒN KHO THÀNH PHẨM
→ Tồn kho: 35 cái
→ Đã phân bổ: 13 cái (cho 2 đơn trước)
→ Còn khả dụng: 22 cái
→ Dùng: 22 cái
→ Cần sản xuất thêm: 28 cái

⚙️ CÔNG SUẤT SẢN XUẤT
→ Đủ công suất Level 1 (8h×2ca=16h)
→ Cần: 0 ca (< 1 ca)

📦 NGUYÊN VẬT LIỆU

✓ Nhựa ABS                     ĐỦ - CÒN 312 CA
→ Tồn kho hiện tại: 10,000 g
→ Định mức: 10 g/sản phẩm
→ Cần cho ĐH này: 280 g (28 sp × 10g)
→ Sau khi trừ ĐH: còn 9,720 g
→ Hiện tại đủ cho: 312 ca (10,000 sản phẩm)
→ Sau khi trừ ĐH: còn 303 ca (~151 ngày)

✓ Mực gel xanh                 ĐỦ - CÒN 310 CA
→ Tồn kho hiện tại: 5,000 g
→ Định mức: 5 g/sản phẩm
→ Cần cho ĐH này: 140 g
→ Sau khi trừ ĐH: còn 4,860 g
→ Hiện tại đủ cho: 312 ca
→ Sau khi trừ ĐH: còn 303 ca

✓ Bi kim loại 0.7mm            ĐỦ - CÒN 286 CA
→ Tồn kho hiện tại: 3,000 g
→ Định mức: 3 g/sản phẩm
→ Cần cho ĐH này: 84 g
→ Sau khi trừ ĐH: còn 2,916 g
→ Hiện tại đủ cho: 312 ca
→ Sau khi trừ ĐH: còn 303 ca

✓ Lò xo thép                   ĐỦ - CÒN 308 CA
→ Tồn kho hiện tại: 1,000 g
→ Định mức: 0.5 g/sản phẩm
→ Cần cho ĐH này: 14 g
→ Sau khi trừ ĐH: còn 986 g
→ Hiện tại đủ cho: 312 ca
→ Sau khi trừ ĐH: còn 308 ca

⏰ DEADLINE
→ Còn 24 ngày đến hạn giao
```

**Badge trạng thái:** `✓ BÌNH THƯỜNG` (màu xanh)

---

### 🔴 **TEST 2: ĐƠN HÀNG CẢNH BÁO (VƯỢT CÔNG SUẤT MỨC 1)**

**Mục đích:** Test đơn cần dùng công suất tối đa (Level 2)

**Dữ liệu test:**
- Sản phẩm: **TL-079**
- Số lượng: **60,000 cái** (vượt Level 1)
- Deadline: 31/12/2025

**Các bước:**
1. Tạo đơn như Test 1
2. Số lượng: **60000**

**Kết quả mong đợi:**

```
⚠️ CÔNG SUẤT SẢN XUẤT
→ Vượt Level 1 (8h×2ca=16h)
→ Chuyển sang Level 2 (12h×2ca=24h)
→ Cần: 18 ca (~9 ngày)
→ Capacity: 500 sp/giờ × 12h × 0.8 = 4,800 sp/ca
→ 60,000 ÷ 4,800 = 12.5 ca → Làm tròn 13 ca

📦 NGUYÊN VẬT LIỆU
→ Tất cả NVL vẫn ĐỦ (vì có 10,000g, 5,000g...)
→ Nhựa ABS: Cần 600,000g (60k × 10g) → THIẾU 590,000g
```

**Badge trạng thái:** `🔴 CẢNH BÁO` (màu cam/đỏ)

---

### ❌ **TEST 3: ĐƠN HÀNG THIẾU NVL**

**Mục đích:** Test khi 1 NVL thiếu, còn lại đủ

**Chuẩn bị:**
```sql
-- Giảm stock Bi kim loại 0.7mm xuống 100g
UPDATE material SET stock = 100 WHERE id_material = 1006;
```

**Dữ liệu test:**
- Sản phẩm: **TL-079**
- Số lượng: **100 cái**
- Deadline: 10/12/2025

**Kết quả mong đợi:**

```
📦 NGUYÊN VẬT LIỆU

✓ Nhựa ABS                     ĐỦ - CÒN 309 CA
→ Tồn kho hiện tại: 10,000 g
→ Cần cho ĐH này: 1,000 g
→ Sau khi trừ: còn 9,000 g

✓ Mực gel xanh                 ĐỦ - CÒN 309 CA
→ Tồn kho hiện tại: 5,000 g
→ Cần cho ĐH này: 500 g
→ Sau khi trừ: còn 4,500 g

❌ Bi kim loại 0.7mm ← GIỚI HẠN     THIẾU
→ Tồn kho hiện tại: 100 g
→ Định mức: 3 g/sản phẩm
→ Cần cho ĐH này: 300 g
→ THIẾU 200 g (cần nhập thêm)
→ Hiện tại đủ cho: 0 ca (33 sản phẩm)
→ Sau khi trừ ĐH: còn 0 ca

✓ Lò xo thép                   ĐỦ - CÒN 309 CA
→ Tồn kho hiện tại: 1,000 g
→ Cần cho ĐH này: 50 g
→ Sau khi trừ: còn 950 g

⚠️ NVL giới hạn sản xuất: Bi kim loại 0.7mm
→ NVL này sẽ cạn kiệt trước, cần ưu tiên nhập thêm
```

**Badge trạng thái:** `⚠️ THIẾU NVL` (màu cam)

**Khôi phục:**
```sql
UPDATE material SET stock = 3000 WHERE id_material = 1006;
```

---

### ⚠️ **TEST 4: BOM THIẾU NVL (NULL)**

**Mục đích:** Test sản phẩm có NVL NULL trong BOM

**Dữ liệu test:**
- Sản phẩm: **danh** (id_product = 1007)
- BOM: `[{"id_material": null, "material_name": "danh", "quantity_per_unit": 10}, ...]`
- Số lượng: **10 cái**

**Kết quả mong đợi:**

```
⚠️ BOM THIẾU NVL (chưa có trong kho):
• danh
→ Cần bổ sung NVL vào kho trước khi sản xuất

📦 NGUYÊN VẬT LIỆU

✓ Bi kim loại 1.0mm            ĐỦ - CÒN 311 CA
→ Tồn kho hiện tại: 2,000 g
→ Định mức: 20 g/sản phẩm
→ Cần cho ĐH này: 200 g
→ Sau khi trừ: còn 1,800 g
```

**Badge trạng thái:** `⚠️ BOM THIẾU NVL` (màu cam)

---

### 🔴 **TEST 5: VƯỢT QUÁ CÔNG SUẤT TỐI ĐA (Level 2)**

**Mục đích:** Test đơn vượt cả Level 2 → TỪ CHỐI

**Dữ liệu test:**
- Sản phẩm: **TL-079**
- Số lượng: **2,000,000 cái** (cực lớn)
- Deadline: 10/12/2025 (chỉ còn 3 ngày)

**Tính toán:**
```
Level 2: 4,800 sp/ca × 2 ca/ngày × 3 ngày = 28,800 sp
2,000,000 > 28,800 → VƯỢT QUÁ
```

**Kết quả mong đợi:**

```
❌ CÔNG SUẤT SẢN XUẤT
→ Vượt cả Level 2 (12h×2ca=24h)
→ Cần: 416 ca (~208 ngày)
→ Thời gian có: 3 ngày
→ KHÔNG THỂ ĐÁP ỨNG

→ Đề xuất: 
  • Giảm số lượng xuống tối đa 28,800 cái
  • Hoặc gia hạn deadline thêm 205 ngày
```

**Badge trạng thái:** `❌ TỪ CHỐI` (màu đỏ)

---

### 📅 **TEST 6: SẮP ĐẾN HẠN GIAO**

**Mục đích:** Test cảnh báo deadline

**Dữ liệu test:**
- Sản phẩm: **TL-079**
- Số lượng: **50 cái**
- Deadline: **09/12/2025** (còn 2 ngày)

**Kết quả mong đợi:**

```
⏰ DEADLINE
⚠️ Sắp đến hạn giao: còn 2 ngày
→ Ưu tiên sản xuất ngay
```

**Badge trạng thái:** `⏰ SẮP HẾT HẠN` (màu vàng)

---

### 🚀 **TEST 7: STOCK ALLOCATION (ƯU TIÊN ĐƠN HÀNG)**

**Mục đích:** Test phân bổ stock cho đơn ưu tiên cao hơn

**Dữ liệu test:**
Tạo 3 đơn liên tiếp cho TL-079 (stock = 35):

**Đơn 1 (Priority: High, Deadline: 09/12):**
- Số lượng: 10 cái
- Kết quả: Dùng 10 từ stock, còn 25

**Đơn 2 (Priority: Medium, Deadline: 10/12):**
- Số lượng: 20 cái
- Kết quả: 
  ```
  → Tồn kho: 35 cái
  → Đã phân bổ: 10 cái (cho đơn #1)
  → Còn khả dụng: 25 cái
  → Dùng: 20 cái
  → Cần sản xuất: 0 cái
  ```

**Đơn 3 (Priority: Low, Deadline: 15/12):**
- Số lượng: 50 cái
- Kết quả:
  ```
  → Tồn kho: 35 cái
  → Đã phân bổ: 30 cái (cho đơn #1 + #2)
  → Còn khả dụng: 5 cái
  → Dùng: 5 cái
  → Cần sản xuất: 45 cái
  ```

---

## 📊 BẢNG TỔNG HỢP CÁC TRẠNG THÁI

| Trạng thái | Icon | Màu | Điều kiện |
|------------|------|-----|-----------|
| **BÌNH THƯỜNG** | ✓ | Xanh | Đủ mọi điều kiện |
| **CẢNH BÁO** | ⚠️ | Cam | Vượt Level 1, dùng Level 2 |
| **THIẾU NVL** | ⚠️ | Cam | 1+ NVL không đủ |
| **BOM THIẾU** | ⚠️ | Cam | Có NVL NULL trong BOM |
| **SẮP HẾT HẠN** | ⏰ | Vàng | Còn < 3 ngày |
| **TỪ CHỐI** | ❌ | Đỏ | Vượt cả Level 2 |

---

## ✅ CHECKLIST KIỂM TRA

### **Modal "CHI TIẾT ĐƠN HÀNG":**
- [ ] **Section 1: TỒN KHO THÀNH PHẨM**
  - [ ] Hiển thị tồn kho
  - [ ] Hiển thị đã phân bổ (nếu có)
  - [ ] Tính đúng khả dụng
  - [ ] Tính đúng cần sản xuất thêm

- [ ] **Section 2: CÔNG SUẤT SẢN XUẤT**
  - [ ] Kiểm tra Level 1 trước
  - [ ] Nếu vượt → chuyển Level 2
  - [ ] Hiển thị số ca cần
  - [ ] KHÔNG ghi "cần tăng ca X giờ"

- [ ] **Section 3: NGUYÊN VẬT LIỆU**
  - [ ] Hiển thị đầy đủ NVL trong BOM
  - [ ] Mỗi NVL có box riêng
  - [ ] Icon + màu sắc đúng (✓/⚠️/❌)
  - [ ] Status text rõ ràng
  - [ ] Hiển thị chi tiết:
    - [ ] Tồn kho hiện tại
    - [ ] Định mức
    - [ ] Cần cho ĐH này
    - [ ] Dư/Thiếu bao nhiêu
    - [ ] Hiện tại đủ X ca
    - [ ] Sau trừ ĐH còn Y ca
  - [ ] Đánh dấu bottleneck "← GIỚI HẠN"
  - [ ] Hiển thị NVL missing nếu có

- [ ] **Section 4: DEADLINE**
  - [ ] Hiển thị thời gian còn lại
  - [ ] Cảnh báo nếu < 3 ngày

### **Danh sách đơn hàng:**
- [ ] Badge trạng thái đúng màu
- [ ] Text trạng thái rõ ràng
- [ ] Sort theo priority + deadline

---

## 🔄 KHÔI PHỤC DỮ LIỆU SAU TEST

```sql
-- Khôi phục stock NVL
UPDATE material SET stock = 10000 WHERE id_material = 1002; -- Nhựa ABS
UPDATE material SET stock = 5000 WHERE id_material = 1003;  -- Mực gel xanh
UPDATE material SET stock = 5000 WHERE id_material = 1004;  -- Mực gel đen
UPDATE material SET stock = 2000 WHERE id_material = 1005;  -- Bi 0.5mm
UPDATE material SET stock = 3000 WHERE id_material = 1006;  -- Bi 0.7mm
UPDATE material SET stock = 2000 WHERE id_material = 1007;  -- Bi 1.0mm
UPDATE material SET stock = 1000 WHERE id_material = 1008;  -- Lò xo

-- Xóa đơn test
DELETE FROM project WHERE qty_request >= 1000; -- Xóa đơn test lớn
```

---

## 📝 GHI CHÚ

- **products_per_shift Level 1:** 500 sp/giờ × 8h × 0.8 = 3,200 sp/ca
- **products_per_shift Level 2:** 500 sp/giờ × 12h × 0.8 = 4,800 sp/ca
- **2 ca/ngày** (sáng + chiều)
- **Priority:** High > Medium > Low > entry_date sớm nhất
