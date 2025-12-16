# 📋 BẢNG THAM CHIẾU NHANH - 7 KỊCH BẢN TEST UC7

> **Sử dụng**: In ra hoặc mở sẵn trên màn hình phụ khi test/demo

---

## 🎯 TÓM TẮT 7 KỊCH BẢN

| # | Tên | Số lượng | Deadline | Toast | Level | Cảnh báo | DB Check |
|---|-----|----------|----------|-------|-------|----------|----------|
| **1** | Normal | 20 | 2025-12-31 | ✅ Xanh | 0 | Không | `capacity_level_used = 0` |
| **2** | Level 2 | 5000 | +1 ngày | ⚠️ Vàng | 2 | Vượt L1 | `capacity_level_used = 2` |
| **3** | NVL thiếu | 1000 | +5 ngày | ⚠️ Vàng | 1-2 | Thiếu NVL | `material_shifts_available` nhỏ |
| **4** | Missing BOM | 500 | +3 ngày | ⚠️ Vàng | 1 | Chưa BOM | `material_details` không có `qty_needed` |
| **5** | Từ chối | 50000 | +1 ngày | ❌ Đỏ | - | Quá capacity | DB: 0 rows (không lưu) |
| **6** | Deadline gần | 2000 | +2 ngày | ⚠️ Vàng | 1 | Gần hạn | `warning_details` có `deadline_warning` |
| **7** | Stock phân bổ | 30, 20 | Xa, gần | ⚠️ Vàng | 0-1 | Ưu tiên | `stock_breakdown.allocated_to_others` |

---

## 📊 CÔNG THỨC TÍNH TOÁN

### **Capacity**
```
Level 1: 500 sp/h × 8h × 0.80 = 3,200 sp/ca
Level 2: 500 sp/h × 12h × 0.85 = 5,100 sp/ca

Số ca cần = ceil(qty_to_produce / capacity_per_shift)
Số ngày cần = ceil(số_ca / 2)
```

### **Nguyên vật liệu**
```
NVL cần = qty_to_produce × quantity_per_unit
Số ca NVL có thể = floor(stock / (qty_per_unit × 3200))
Bottleneck = NVL có số ca ít nhất
```

### **Stock Allocation**
```
Thứ tự ưu tiên: ORDER BY entry_date ASC, created_at ASC
Available stock = total_stock - allocated_to_higher_priority
```

---

## 🔍 SQL QUERIES NHANH

### **1. Lấy đơn mới nhất**
```sql
SELECT id_project, project_name, qty_request, entry_date, created_at
FROM project WHERE id_product = 1001 ORDER BY created_at DESC LIMIT 1;
```

### **2. Kiểm tra UC7 của 1 đơn**
```sql
SELECT id_project, warning_flag, capacity_level_used, 
       material_shifts_available, finished_stock_available,
       JSON_PRETTY(warning_details) AS details
FROM project WHERE id_project = ?;
```

### **3. Xem stock breakdown**
```sql
SELECT JSON_EXTRACT(warning_details, '$.stock_breakdown') AS stock_info
FROM project WHERE id_project = ?;
```

### **4. Xem material details**
```sql
SELECT JSON_EXTRACT(warning_details, '$.material_details') AS materials
FROM project WHERE id_project = ?;
```

### **5. So sánh 2 đơn (Allocation)**
```sql
SELECT id_project, qty_request, entry_date, finished_stock_available,
       JSON_EXTRACT(warning_details, '$.stock_breakdown.allocated_to_others') AS allocated
FROM project WHERE id_product = 1001 ORDER BY entry_date ASC, created_at ASC;
```

---

## ✅ CHECKLIST NHANH MỖI TEST

### **UI - Toast**
- [ ] Màu đúng (xanh/vàng/đỏ)
- [ ] Icon đúng (✓/⚠️/❌/⏰/📦)
- [ ] Message chính xác

### **UI - Modal (5 sections)**
- [ ] Section 1: Thông tin cơ bản
- [ ] Section 2: Tồn kho thành phẩm
- [ ] Section 3: Công suất (Level 0/1/2)
- [ ] Section 4: Chi tiết NVL
- [ ] Section 5: Cảnh báo

### **Database - 5 cột UC7**
- [ ] `warning_flag` = 0/1
- [ ] `capacity_level_used` = 0/1/2
- [ ] `material_shifts_available` = số hợp lý
- [ ] `finished_stock_available` = stock khả dụng
- [ ] `warning_details` JSON parse OK

### **Công thức**
- [ ] Level capacity đúng (3200 hoặc 5100)
- [ ] Số ca = ceil(qty/capacity)
- [ ] NVL shifts = floor(stock/(qty_per_unit × 3200))
- [ ] Stock allocation = total - allocated

---

## 🛠️ CHUẨN BỊ TRƯỚC KHI TEST

### **Test data cần có**
```sql
-- 1. Tồn kho thành phẩm TL-079
SELECT * FROM finished_stock WHERE id_product = 1001; -- Phải có 35 cái

-- 2. Material có stock
SELECT id_material, material_name, stock, min_stock FROM material WHERE stock > 0;

-- 3. Product TL-079 có BOM
SELECT id_product, product_name, LENGTH(bom) AS bom_length FROM product WHERE id_product = 1001;

-- 4. Capacity config 2 levels
SELECT * FROM capacity_config WHERE is_active = 1;
```

### **Reset nếu cần**
```sql
-- Xóa đơn test (thay ? bằng ID thực tế)
DELETE FROM project WHERE id_project IN (?, ?, ?);

-- Hoặc xóa theo thời gian
DELETE FROM project WHERE created_at >= '2025-05-20 10:00:00';

-- Reset tồn kho NVL về ban đầu (nếu đã giảm cho test scenario 3)
UPDATE material SET stock = 50 WHERE id_material = 1001;
```

---

## 📸 SCREENSHOT LIST CHO DEMO

**Cần chụp màn hình:**

1. ✅ **S1 - Normal**: Toast xanh + Modal "Giao ngay" + DB `capacity_level_used=0`
2. ⚠️ **S2 - Level 2**: Toast vàng + Modal "LEVEL 2" + DB `capacity_level_used=2`
3. 📦 **S3 - NVL thiếu**: Toast cảnh báo NVL + Modal section 4 chi tiết NVL + DB `material_details` JSON
4. ❌ **S5 - Rejection**: Toast đỏ "Không thể tạo" + DB query trả về 0 rows
5. 🔄 **S7 - Allocation**: 2 đơn → Modal Đơn 1 hiển thị "Đã phân bổ" + DB so sánh `finished_stock_available`

---

## 🚨 LƯU Ý QUAN TRỌNG

### **Nếu toast không hiển thị đúng:**
- Kiểm tra `BOD.php` addProject() line 625+ có set flashdata chưa
- Check logic phân biệt "cảnh báo thực sự" vs "thông tin tốt" (line 714-729)

### **Nếu modal không hiển thị đủ:**
- Kiểm tra `Project.php` view file có đủ 5 sections
- Kiểm tra `warning_details` JSON có đầy đủ keys

### **Nếu DB không lưu đúng:**
- Kiểm tra `BOD.php` addProject() line 680-698 có lưu 5 cột UC7 chưa
- Kiểm tra `OrderModel.php` checkCapacity() return structure có đầy đủ fields

### **Nếu công thức sai:**
- Level 1: Phải là 500 × 8 × 0.80 = 3,200
- Level 2: Database có 0.85 → 500 × 12 × 0.85 = 5,100 (KHÔNG phải 4,800)
- Kiểm tra code line 674-676 có dùng đúng efficiency từ DB chưa

---

## 🎯 DEMO 30 PHÚT - TIMELINE

| Thời gian | Nội dung | Kịch bản |
|-----------|----------|----------|
| 0-5 phút | Giới thiệu UC7, giải thích 5 cột DB | - |
| 5-9 phút | Demo S1 (Normal) - UI + DB | #1 |
| 9-14 phút | Demo S2 (Level 2) - Toast vàng + Modal | #2 |
| 14-20 phút | Demo S3 (NVL thiếu) - Chi tiết material_details | #3 |
| 20-25 phút | Demo S5 (Rejection) - Chứng minh không lưu DB | #5 |
| 25-30 phút | Q&A | - |

**Tùy chọn thêm**: Nếu có thời gian, demo S7 (Stock Allocation) để show logic ưu tiên deadline.

---

## 📱 QUICK ACCESS LINKS

### **Hệ thống**
- URL: `http://localhost/production-management-v2/`
- BOD Dashboard: `http://localhost/production-management-v2/BOD/project`
- Thêm đơn: `http://localhost/production-management-v2/BOD/project/addproject`

### **Database**
- Database: `db_production`
- Table: `project`
- 5 cột UC7: `warning_flag`, `warning_details`, `capacity_level_used`, `material_shifts_available`, `finished_stock_available`

### **Code references**
- `OrderModel.php` checkCapacity(): Line 343-850
- `BOD.php` addProject(): Line 625-750
- `BOD.php` updateProject(): Line 778-920
- `Project.php` modal: Line 560-680

---

## 🎯 TIÊU CHÍ THÀNH CÔNG

✅ **7/7 test cases đạt**  
✅ **Toast màu đúng 100%**  
✅ **Modal 5 sections đầy đủ**  
✅ **DB 5 cột lưu chính xác**  
✅ **JSON parse thành công**  
✅ **Công thức tính đúng**  
✅ **Stock allocation ưu tiên đúng**

---

**🚀 Ready to Demo!**
