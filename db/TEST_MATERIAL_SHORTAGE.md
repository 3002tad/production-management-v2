# 🧪 TEST CASE: NVL ĐÃ CÓ TRONG KHO NHƯNG THIẾU

## 📋 MỤC TIÊU
Test trường hợp: NVL đã tồn tại trong kho (không phải NULL/thiếu BOM) nhưng **KHÔNG ĐỦ** để sản xuất đơn hàng

---

## 🎯 CHUẨN BỊ

### **Tình huống:**
- Sản phẩm: **TL-079 Bút bi nhựa** (đã có BOM đầy đủ)
- BOM có: 4 NVL (Nhựa ABS, Mực gel, Bi kim loại, Lò xo)
- **Mục đích:** Tạo đơn hàng để test hiển thị chi tiết NVL

---

## 📊 BƯỚC 1: KIỂM TRA TỒN KHO HIỆN TẠI

### **Bằng Giao Diện:**

1. **Đăng nhập** với role **Admin** hoặc **Warehouse**

2. **Vào menu: QUẢN LÝ KHO → Nguyên vật liệu**

3. **Tìm sản phẩm TL-079:**
   - Gõ "TL-079" vào ô tìm kiếm
   - Hoặc lọc theo loại sản phẩm "Bút bi"

4. **Click vào TL-079 → Xem BOM**
   - Sẽ thấy danh sách 4 NVL với định mức

5. **Ghi lại tồn kho hiện tại:**

| NVL | Tồn kho hiện tại | Định mức/sp |
|-----|------------------|-------------|
| Nhựa ABS | ? g | 10 g |
| Mực gel | ? g | 5 g |
| Bi kim loại | ? g | 3 g |
| Lò xo thép | ? g | 0.5 g |

---

## 🧪 BƯỚC 2: KỊCH BẢN TEST 1 - ĐƠN HÀNG LỚN (TẤT CẢ NVL THIẾU)

### **Mục đích:** Test khi tất cả NVL đều không đủ

### **Các bước thực hiện:**

#### **2.1. Đăng nhập role BOD**
- Username: `bod_user` (hoặc admin)
- Vào menu: **QUẢN LÝ ĐƠN HÀNG**

#### **2.2. Click nút "➕ Thêm đơn hàng mới"**

#### **2.3. Điền form đơn hàng:**
```
┌─────────────────────────────────────┐
│  📝 THÊM ĐƠN HÀNG MỚI              │
├─────────────────────────────────────┤
│  Khách hàng: [Chọn KH test      ▼] │
│  Sản phẩm:   [TL-079 Bút bi    ▼]  │
│  Số lượng:   [500000            ]   │
│  Entry date: [08/12/2025       📅]  │
│  Priority:   [Medium            ▼]  │
│  Ghi chú:    [Test thiếu NVL    ]   │
│                                     │
│         [Hủy]  [💾 Lưu đơn hàng]   │
└─────────────────────────────────────┘
```

**Lưu ý:**
- Số lượng: **500000** (500 nghìn) - Đủ lớn để tất cả NVL đều thiếu
- Entry date: Chọn ngày mai (để tránh quá hạn)

#### **2.4. Click "💾 Lưu đơn hàng"**

#### **2.5. Hệ thống sẽ hiển thị Modal "CHI TIẾT ĐƠN HÀNG"**

### **✅ Kết quả mong đợi:**
   ```
   📦 NGUYÊN VẬT LIỆU
   
   ❌ Nhựa ABS                    THIẾU
   → Tồn kho hiện tại: 10,000 g
   → Định mức: 10 g/sản phẩm
   → Cần cho ĐH này: 5,000,000 g
   → THIẾU 4,990,000 g (cần nhập thêm)
   → Hiện tại đủ cho: 0 ca (1,000 sản phẩm)
   → Sau khi trừ ĐH: còn 0 ca (~0 ngày)
   
   ❌ Mực gel                     THIẾU
   → Tồn kho hiện tại: 5,000 g
   → Định mức: 5 g/sản phẩm
   → Cần cho ĐH này: 2,500,000 g
   → THIẾU 2,495,000 g (cần nhập thêm)
   → Hiện tại đủ cho: 0 ca (1,000 sản phẩm)
   → Sau khi trừ ĐH: còn 0 ca (~0 ngày)
   
   ❌ Bi kim loại ← GIỚI HẠN     THIẾU
   → Tồn kho hiện tại: 2,000 g
   → Định mức: 3 g/sản phẩm
   → Cần cho ĐH này: 1,500,000 g
   → THIẾU 1,498,000 g (cần nhập thêm)
   → Hiện tại đủ cho: 0 ca (666 sản phẩm)
   → Sau khi trừ ĐH: còn 0 ca (~0 ngày)
   
   ❌ Lò xo thép                  THIẾU
   → Tồn kho hiện tại: 1,000 g
   → Định mức: 0.5 g/sản phẩm
   → Cần cho ĐH này: 250,000 g
   → THIẾU 249,000 g (cần nhập thêm)
   → Hiện tại đủ cho: 0 ca (2,000 sản phẩm)
   → Sau khi trừ ĐH: còn 0 ca (~0 ngày)
   ```

---

## 🧪 BƯỚC 3: KỊCH BẢN TEST 2 - 1 NVL THIẾU, CÒN LẠI ĐỦ

### **Mục đích:** Test hiển thị hỗn hợp (có NVL đủ, có NVL thiếu)

### **Chuẩn bị:**

#### **3.1. Giảm stock 1 NVL xuống thấp**

**Bằng giao diện:**
1. Đăng nhập role **Warehouse** hoặc **Admin**
2. Vào: **QUẢN LÝ KHO → Nguyên vật liệu**
3. Tìm **"Bi kim loại 1.0mm"**
4. Click **"✏️ Sửa"**
5. Sửa:
   ```
   Tồn kho (stock): [100] g  ← Giảm xuống 100g
   ```
6. Click **"💾 Lưu"**

**Hoặc dùng SQL nhanh (Adminer/phpMyAdmin):**
```sql
UPDATE material 
SET stock = 100 
WHERE material_name = 'Bi kim loại 1.0mm';
```

### **Các bước test:**

#### **3.2. Đăng nhập role BOD**

#### **3.3. Tạo đơn hàng VỪA PHẢI:**
```
┌─────────────────────────────────────┐
│  📝 THÊM ĐƠN HÀNG MỚI              │
├─────────────────────────────────────┤
│  Khách hàng: [KH test           ▼]  │
│  Sản phẩm:   [TL-079 Bút bi    ▼]  │
│  Số lượng:   [100               ]   │  ← 100 cái thôi
│  Entry date: [08/12/2025       📅]  │
│  Priority:   [Medium            ▼]  │
│  Ghi chú:    [Test 1 NVL thiếu ]   │
│                                     │
│         [Hủy]  [💾 Lưu đơn hàng]   │
└─────────────────────────────────────┘
```

#### **3.4. Click "💾 Lưu đơn hàng"**

### **✅ Kết quả mong đợi:**
   ```
   📦 NGUYÊN VẬT LIỆU
   
   ✓ Nhựa ABS                     ĐỦ - CÒN 312 CA
   → Tồn kho hiện tại: 10,000 g
   → Định mức: 10 g/sản phẩm
   → Cần cho ĐH này: 1,000 g
   → Sau khi trừ ĐH: còn 9,000 g
   → Hiện tại đủ cho: 312 ca (1,000,000 sản phẩm)
   → Sau khi trừ ĐH: còn 312 ca (~156 ngày)
   
   ✓ Mực gel                      ĐỦ - CÒN 312 CA
   → Tồn kho hiện tại: 5,000 g
   → Định mức: 5 g/sản phẩm
   → Cần cho ĐH này: 500 g
   → Sau khi trừ ĐH: còn 4,500 g
   → Hiện tại đủ cho: 312 ca (1,000,000 sản phẩm)
   → Sau khi trừ ĐH: còn 281 ca (~140 ngày)
   
   ❌ Bi kim loại ← GIỚI HẠN     THIẾU
   → Tồn kho hiện tại: 100 g
   → Định mức: 3 g/sản phẩm
   → Cần cho ĐH này: 300 g
   → THIẾU 200 g (cần nhập thêm)
   → Hiện tại đủ cho: 0 ca (33 sản phẩm)
   → Sau khi trừ ĐH: còn 0 ca (~0 ngày)
   
   ✓ Lò xo thép                   ĐỦ - CÒN 312 CA
   → Tồn kho hiện tại: 1,000 g
   → Định mức: 0.5 g/sản phẩm
   → Cần cho ĐH này: 50 g
   → Sau khi trừ ĐH: còn 950 g
   → Hiện tại đủ cho: 312 ca (2,000,000 sản phẩm)
   → Sau khi trừ ĐH: còn 296 ca (~148 ngày)
   ```

---

## ✅ BƯỚC 4: KIỂM TRA KẾT QUẢ TRÊN MODAL

### **4.1. Kiểm tra Modal "CHI TIẾT ĐƠN HÀNG"**

Sau khi click "💾 Lưu đơn hàng", modal sẽ hiển thị. Kiểm tra các phần:

#### **📦 Section: TỒN KHO THÀNH PHẨM**
```
✓ Hiển thị số lượng tồn kho
✓ Hiển thị số lượng đã phân bổ (nếu có đơn ưu tiên cao hơn)
✓ Tính số lượng cần sản xuất thêm
```

#### **⚙️ Section: CÔNG SUẤT SẢN XUẤT**
```
✓ Hiển thị đủ công suất (Level 1: 8h×2ca)
✓ Hoặc vượt → Level 2 (12h×2ca)
✓ Tính số ca cần thiết
```

#### **📦 Section: NGUYÊN VẬT LIỆU** ← **QUAN TRỌNG NHẤT**

**Checklist chi tiết cho TỪNG NVL:**

- [ ] **Hiển thị đầy đủ 4 NVL** (Nhựa ABS, Mực gel, Bi kim loại, Lò xo)
- [ ] **Mỗi NVL có box riêng** với border màu (xanh/cam/đỏ)
- [ ] **Icon và status rõ ràng:**
  - ✓ = ĐỦ (màu xanh)
  - ⚠️ = GIỚI HẠN hoặc GẦN HẾT (màu cam)
  - ❌ = THIẾU (màu đỏ)

**Chi tiết TRONG MỖI BOX:**
- [ ] **Tồn kho hiện tại:** Số lượng + đơn vị (g/kg/cái)
- [ ] **Định mức:** X g/sản phẩm
- [ ] **Cần cho ĐH này:** Số lượng chính xác
- [ ] **Dư/Thiếu:** 
  - Nếu ĐỦ: "→ Sau khi trừ ĐH: còn X g" (màu xanh)
  - Nếu THIẾU: "→ THIẾU X g (cần nhập thêm)" (màu đỏ)
- [ ] **Hiện tại đủ cho:** X ca (Y sản phẩm)
- [ ] **Sau khi trừ ĐH:** Còn X ca (~Y ngày)

**Đánh dấu bottleneck:**
- [ ] **NVL ít nhất** có tag "← GIỚI HẠN" (màu cam đậm)

#### **⏰ Section: DEADLINE**
```
✓ Hiển thị ngày deadline
✓ Cảnh báo nếu gần hết hạn
```

---

## 🧹 BƯỚC 5: DỌN DẸP SAU TEST

### **5.1. Xóa đơn hàng test**

**Bằng giao diện:**
1. Vào **QUẢN LÝ ĐƠN HÀNG**
2. Tìm đơn test vừa tạo (500,000 cái hoặc 100 cái)
3. Click **"🗑️ Xóa"**
4. Xác nhận xóa

### **5.2. Khôi phục stock NVL (nếu đã sửa)**

**Bằng giao diện:**
1. Vào **QUẢN LÝ KHO → Nguyên vật liệu**
2. Tìm **"Bi kim loại 1.0mm"**
3. Click **"✏️ Sửa"**
4. Sửa lại: `Tồn kho: [2000] g` (giá trị ban đầu)
5. Click **"💾 Lưu"**

**Hoặc SQL:**
```sql
UPDATE material SET stock = 2000 
WHERE material_name = 'Bi kim loại 1.0mm';
```

---

## 📸 BƯỚC 6: CHỤP SCREENSHOT (Optional)

Để report lỗi hoặc confirm kết quả:

1. **Chụp Modal "CHI TIẾT ĐƠN HÀNG"** đầy đủ
2. **Chụp riêng section "📦 NGUYÊN VẬT LIỆU"** để thấy rõ chi tiết
3. **Chụp 1 NVL THIẾU** để thấy cảnh báo màu đỏ
4. **Chụp 1 NVL ĐỦ** để thấy "CÒN X CA"

---

## 🐛 BƯỚC 7: CÁC LỖI CÓ THỂ GẶP

### **Lỗi 1: Modal không hiển thị chi tiết NVL**
**Nguyên nhân:** 
- Sản phẩm chưa có BOM
- BOM bị NULL hoặc format sai

**Cách fix:**
1. Vào **QUẢN LÝ SẢN PHẨM → TL-079**
2. Kiểm tra tab **"BOM"**
3. Đảm bảo có đủ 4 NVL với định mức

### **Lỗi 2: Hiển thị "ĐỦ 999 CA" dù NVL thiếu**
**Nguyên nhân:** 
- Logic tính toán chưa update
- Cache cũ

**Cách fix:**
1. Clear cache trình duyệt (Ctrl + Shift + Del)
2. Hard refresh (Ctrl + F5)
3. Kiểm tra file `OrderModel.php` đã update chưa

### **Lỗi 3: Không thấy "← GIỚI HẠN" cho NVL bottleneck**
**Nguyên nhân:**
- Logic đánh dấu bottleneck chưa chạy
- Tất cả NVL đều thiếu (không có bottleneck rõ)

**Cách fix:**
- Test với đơn hàng nhỏ hơn (100 cái)
- Chỉ giảm stock 1 NVL

---

## 📝 GHI CHÚ KỸ THUẬT

### **Cách tính số ca:**
```
Capacity/ca = 500 sp/giờ × 8 giờ × 80% = 3,200 sp/ca

Số sản phẩm có thể làm = Tồn kho ÷ Định mức/sp
Số ca có thể = Số sản phẩm ÷ 3,200
Số ngày = Số ca ÷ 2 (2 ca/ngày)
```

### **Logic mới vs cũ:**

| Tiêu chí | Logic CŨ ❌ | Logic MỚI ✅ |
|----------|------------|-------------|
| **Hiển thị status** | "ĐỦ" (không rõ) | "ĐỦ - CÒN 50 CA" |
| **Tính số ca** | Tổng kho → X ca | Còn lại sau trừ ĐH → Y ca |
| **Chi tiết NVL** | Chỉ tổng kết | Chi tiết TỪNG NVL |
| **Bottleneck** | Không có | Đánh dấu "← GIỚI HẠN" |
| **Số lượng thiếu** | Không rõ | "THIẾU X g (cần nhập)" |

### **Ví dụ tính toán:**
```
Tồn kho Bi kim loại: 2,000 g
Định mức: 3 g/sp
Đơn hàng: 100 sp

Cần cho ĐH: 100 × 3 = 300 g ✓
Sau trừ ĐH: 2,000 - 300 = 1,700 g
Có thể làm: 1,700 ÷ 3 = 566 sp
Số ca còn lại: 566 ÷ 3,200 = 0 ca (chưa đủ 1 ca)

→ Hiển thị: "ĐỦ - CÒN 0 CA"
```

---

## 🎯 KẾT LUẬN

Test case này đã cover:

✅ **NVL có trong kho nhưng THIẾU** (không phải NULL/missing BOM)  
✅ **Tính toán chính xác** số lượng thiếu cho từng NVL  
✅ **Hiển thị số ca CÒN LẠI** sau khi trừ đơn hàng (không phải tổng kho)  
✅ **Phân biệt rõ ràng:** Tồn kho hiện tại vs Sau khi trừ ĐH  
✅ **Đánh dấu bottleneck** - NVL nào giới hạn sản xuất  
✅ **UI/UX tốt:** Màu sắc, icon, format dễ đọc  

---

## 🚀 BƯỚC TIẾP THEO

Sau khi test xong, có thể test thêm:

1. **Multiple orders** - Nhiều đơn cùng sản phẩm → Stock bị phân bổ
2. **Priority orders** - Đơn ưu tiên cao chiếm stock trước
3. **Mixed products** - Nhiều sản phẩm khác nhau dùng chung NVL
4. **Low stock alert** - NVL < min_stock cảnh báo màu cam
