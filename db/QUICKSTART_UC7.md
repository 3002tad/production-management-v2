# 🚀 QUICKSTART UC7 - 5 PHÚT

## 📌 BẮT ĐẦU NGAY

### 1️⃣ Chạy Migration (1 phút)
```bash
cd d:\PHAT TRIEN UNG DUNG\production-management-v2
mysql -u root -p db_production < db\migrations\007_add_uc7_warning_system.sql
```

### 2️⃣ Verify (30 giây)
```sql
USE db_production;
DESCRIBE project;  -- Phải thấy: warning_flag, warning_details
SELECT * FROM capacity_config;  -- Phải thấy: 2 rows
```

### 3️⃣ Test Đơn Giản (2 phút)
1. Login → **BOD → Đơn hàng**
2. **+ Thêm đơn hàng:**
   - Sản phẩm: **TL-079**
   - Số lượng: **50**
   - Deadline: **2025-12-20**
3. Click **Lưu**
4. Click **icon ⚠️** → Xem modal chi tiết

### 4️⃣ Kỳ Vọng
- ✅ Toast màu xanh: "Đơn hàng đã được tạo"
- 📊 Modal hiển thị:
  - Tồn kho: 35 cái
  - Công suất: Level 1
  - NVL: 4 loại, tất cả ĐỦ
  
---

## 🧪 TEST NHANH 7 CASES

| # | Input | Expected |
|---|-------|----------|
| 1 | 50 cái | ✅ OK |
| 2 | 60,000 cái | ⚠️ Vượt Level 1 → Level 2 |
| 3 | 100 cái (giảm lò xo = 100g) | ❌ Thiếu NVL |
| 4 | Product "danh" | ⚠️ BOM thiếu NVL |
| 5 | 2,000,000 cái | 🚫 Từ chối (vượt Level 2) |
| 6 | Deadline = hôm nay +2 | ⏰ Gần deadline |
| 7 | 2 đơn (20+20), stock=35 | 🚀 Priority allocation |

**Chi tiết:** `CHECKLIST_DEMO_UC7.md`

---

## 🔧 FIX LỖI NHANH

### ❌ Column 'warning_flag' không tồn tại
```sql
SOURCE db/migrations/007_add_uc7_warning_system.sql;
```

### ❌ capacity_config not found
```sql
CREATE TABLE capacity_config (...);  -- Xem trong migration
```

### ❌ Modal không hiển thị NVL
```sql
-- Check data
SELECT JSON_PRETTY(warning_details) 
FROM project 
ORDER BY created_at DESC LIMIT 1;
```

### ❌ Reset để test lại
```sql
SOURCE db/reset_test_data_uc7.sql;
```

---

## 📊 CÔNG THỨC QUAN TRỌNG

```
Level 1: 500 sp/h × 8h × 0.8 = 3,200 sp/ca
Level 2: 500 sp/h × 12h × 0.8 = 4,800 sp/ca

Shifts còn lại = (Stock - Needed) ÷ (3200 × Qty_per_unit)
```

---

## 📂 FILE QUAN TRỌNG

| File | Mục đích |
|------|----------|
| `007_add_uc7_warning_system.sql` | Migration chính (CHẠY ĐẦU TIÊN) |
| `README_UC7.md` | Tài liệu đầy đủ |
| `CHECKLIST_DEMO_UC7.md` | Hướng dẫn demo |
| `BAO_CAO_KIEM_TRA_SOURCE_CODE.md` | Code audit |
| `reset_test_data_uc7.sql` | Reset để test lại |

---

## ✅ CHECKLIST TRƯỚC DEMO

- [ ] Migration đã chạy
- [ ] capacity_config có 2 rows
- [ ] Web server chạy
- [ ] Test 1 đơn OK
- [ ] Modal hiển thị đúng

**→ SẴN SÀNG DEMO! 🎉**

---

**Support:** Xem `README_UC7.md` để biết thêm chi tiết
