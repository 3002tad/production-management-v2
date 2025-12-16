# HƯỚNG DẪN FIX BUG: STOCK ALLOCATION

## 🔴 VẤN ĐỀ

**Bug nghiêm trọng**: Tồn kho **KHÔNG được cập nhật** trong database khi tạo đơn hàng mới.

### Hiện trạng:
```
Đơn 1: 10 cái → DB vẫn hiển thị 35 cái available ❌
Đơn 2: 20 cái → DB vẫn hiển thị 35 cái available ❌  
Đơn 3: 5,000 cái → DB vẫn hiển thị 35 cái available ❌
```

### Mong muốn:
```
Đơn 1: 10 cái → DB còn 25 cái available ✓
Đơn 2: 20 cái → DB còn 5 cái available ✓
Đơn 3: 5,000 cái → DB còn 0 cái available, cần SX 4,995 cái ✓
```

---

## 🛠️ GIẢI PHÁP

### 1. Tạo hệ thống Stock Allocation với Trigger

**File migration**: `008_add_stock_allocation_trigger.sql`

**Tính năng**:
- ✅ Tự động trừ stock khi INSERT project mới
- ✅ Tự động hoàn trả stock khi UPDATE/DELETE project
- ✅ Tracking lịch sử phân bổ trong bảng `stock_allocation`
- ✅ Cột `quantity_available` tự động tính = `quantity_in_stock - quantity_allocated`

### 2. Cập nhật code PHP

**File**: `application/models/OrderModel.php`

**Thay đổi**:
- Query đơn giản hơn: Lấy `quantity_available` từ DB (do trigger tính)
- Không cần query phức tạp để tính allocated

---

## 📋 BƯỚC THỰC HIỆN

### Bước 1: Backup Database
```bash
cd d:\PHAT TRIEN UNG DUNG\production-management-v2\db\backups
mysqldump -u root -p db_production > backup_before_stock_fix_20251209.sql
```

### Bước 2: Chạy Migration
```bash
cd d:\PHAT TRIEN UNG DUNG\production-management-v2\db\migrations

# Chạy migration
mysql -u root -p db_production < 008_add_stock_allocation_trigger.sql
```

**Kết quả**:
- ✅ Tạo bảng `stock_allocation`
- ✅ Thêm 3 cột mới vào `product`: `quantity_in_stock`, `quantity_allocated`, `quantity_available`
- ✅ Tạo 3 triggers: INSERT, UPDATE, DELETE
- ✅ Tạo VIEW `v_product_stock_status`
- ✅ Tạo stored procedure `sp_check_stock_consistency`

### Bước 3: Migrate Dữ Liệu Cũ
```sql
-- Copy stock cũ sang quantity_in_stock
UPDATE product 
SET quantity_in_stock = COALESCE(stock, 0);

-- Tính lại quantity_allocated từ các đơn đang tồn tại
UPDATE product p
LEFT JOIN (
    SELECT 
        id_product,
        SUM(
            CASE 
                WHEN finished_stock_available > 0 
                THEN LEAST(qty_request, finished_stock_available)
                ELSE 0
            END
        ) AS total_allocated
    FROM project
    WHERE pr_status IN (1, 2)
    GROUP BY id_product
) alloc ON p.id_product = alloc.id_product
SET p.quantity_allocated = COALESCE(alloc.total_allocated, 0);
```

### Bước 4: Test Migration
```bash
# Chạy test script
mysql -u root -p db_production < 008_test_stock_allocation.sql
```

**Kỳ vọng output**:
```
TEST CASE 1: ✓ PASS (allocated=10, available=25)
TEST CASE 2: ✓ PASS (allocated=30, available=5)
TEST CASE 3: ✓ PASS (allocated=35, available=0)
FINAL: ✓✓✓ ALL TESTS PASS ✓✓✓
```

### Bước 5: Kiểm Tra Consistency
```sql
-- Kiểm tra xem có đơn nào không khớp không
CALL sp_check_stock_consistency();

-- Nếu không có output → Perfect!
-- Nếu có output → Cần kiểm tra lại
```

### Bước 6: Xem Stock Real-Time
```sql
-- Xem trạng thái stock của tất cả sản phẩm
SELECT * FROM v_product_stock_status;

-- Xem lịch sử phân bổ
SELECT * FROM stock_allocation 
ORDER BY allocated_at DESC 
LIMIT 20;
```

---

## 🧪 TEST THỰC TẾ

### Scenario: Tạo 3 đơn như mô tả

```sql
-- Setup: Reset stock TL-079 về 35 cái
UPDATE product 
SET quantity_in_stock = 35, quantity_allocated = 0 
WHERE id_product = 1001;

-- Đơn 1: 10 cái, deadline 31/12/2025
INSERT INTO project (...) VALUES (
    ..., 10, '2025-12-31', ...
);

-- Check stock
SELECT * FROM v_product_stock_status WHERE id_product = 1001;
-- Expected: total=35, allocated=10, available=25 ✓

-- Đơn 2: 20 cái, deadline 20/12/2025 (ưu tiên hơn)
INSERT INTO project (...) VALUES (
    ..., 20, '2025-12-20', ...
);

-- Check stock
SELECT * FROM v_product_stock_status WHERE id_product = 1001;
-- Expected: total=35, allocated=30, available=5 ✓

-- Đơn 3: 5,000 cái, deadline 11/12/2025
INSERT INTO project (...) VALUES (
    ..., 5000, '2025-12-11', ...
);

-- Check stock
SELECT * FROM v_product_stock_status WHERE id_product = 1001;
-- Expected: total=35, allocated=35, available=0 ✓
-- Note: Đơn 3 chỉ được phân bổ 5 cái, còn lại 4,995 cái phải sản xuất
```

---

## 🔍 VERIFY BADGE HIỂN THỊ ĐÚNG

Sau khi fix, badge sẽ hiển thị:

### Đơn 1 (10 cái, deadline xa)
- **Badge**: "Có sẵn kho" (xanh dương 🏪)
- **Lý do**: Có 35 cái, dùng 10 → Đủ, không cần SX

### Đơn 2 (20 cái, deadline gần)
- **Badge**: "Gần deadline" (cam ⏰) HOẶC "Có sẵn kho" (xanh dương)
- **Lý do**: Còn 25 cái (sau Đơn 1), dùng 20 → Đủ, nhưng gần deadline

### Đơn 3 (5,000 cái, deadline gần nhất)
- **Badge**: "Bình thường" (xanh lá ✓) hoặc "Gần deadline" (cam)
- **Lý do**: Chỉ còn 5 cái, cần SX thêm 4,995 → Capacity Level 1
- **Modal**: 
  ```
  🏪 TỒN KHO: 
  ✓ Tồn kho: 35 cái (đã phân bổ 30 cái cho đơn ưu tiên cao hơn, 
                      còn 5 cái khả dụng)
  Dùng 5 cái, cần sản xuất thêm 4,995 cái
  
  ⚙️ CÔNG SUẤT:
  → Công suất máy: 500 sp/h
  → Sản phẩm/ca: 3,200 cái (500 × 8h × 80%)
  → Cần 2 ca (~1 ngày)
  ```

---

## 🔧 ROLLBACK (Nếu Cần)

```sql
-- Restore backup
mysql -u root -p db_production < backup_before_stock_fix_20251209.sql

-- Hoặc xóa thủ công
DROP TRIGGER IF EXISTS after_project_insert_stock;
DROP TRIGGER IF EXISTS after_project_update_stock;
DROP TRIGGER IF EXISTS after_project_delete_stock;
DROP TABLE IF EXISTS stock_allocation;
DROP VIEW IF EXISTS v_product_stock_status;
DROP PROCEDURE IF EXISTS sp_check_stock_consistency;

ALTER TABLE product 
    DROP COLUMN quantity_in_stock,
    DROP COLUMN quantity_allocated,
    DROP COLUMN quantity_available;
```

---

## 📊 MONITORING

### Query hữu ích:

```sql
-- 1. Xem stock của 1 sản phẩm
SELECT * FROM v_product_stock_status WHERE id_product = 1001;

-- 2. Xem lịch sử phân bổ của 1 đơn
SELECT * FROM stock_allocation WHERE id_project = 'ORD-XXX';

-- 3. Xem đơn nào đang giữ stock
SELECT 
    p.id_project,
    p.project_name,
    p.qty_request,
    sa.quantity_allocated,
    p.entry_date
FROM project p
JOIN stock_allocation sa ON p.id_project = sa.id_project
WHERE p.id_product = 1001
ORDER BY p.entry_date;

-- 4. Check consistency định kỳ
CALL sp_check_stock_consistency();
```

---

## ✅ CHECKLIST

- [ ] Backup database
- [ ] Chạy migration 008
- [ ] Test với 3 đơn thử nghiệm
- [ ] Verify badge hiển thị đúng
- [ ] Verify modal hiển thị stock allocation
- [ ] Check consistency
- [ ] Test tạo đơn mới trên giao diện
- [ ] Test cập nhật đơn (stock hoàn trả đúng)
- [ ] Test xóa đơn (stock hoàn trả đúng)

---

## 🎯 KẾT QUẢ MONG ĐỢI

Sau khi fix:
1. ✅ Stock được trừ THẬT trong DB
2. ✅ Đơn ưu tiên cao được phân bổ trước
3. ✅ Badge hiển thị chính xác
4. ✅ Modal giải thích rõ stock allocation
5. ✅ User hiểu vì sao đơn của họ chỉ có X cái available

---

## 📞 HỖ TRỢ

Nếu gặp vấn đề:
1. Check log: `SELECT * FROM stock_allocation ORDER BY allocated_at DESC;`
2. Check consistency: `CALL sp_check_stock_consistency();`
3. Verify trigger: `SHOW TRIGGERS LIKE 'project';`
4. Check column: `DESC product;` (phải có 3 cột mới)
