-- ========================================
-- TEST SCRIPT: Stock Allocation Logic
-- Kiểm tra trigger trừ stock tự động
-- ========================================
-- Ngày: 09/12/2025
-- Mục đích: Verify bug fix - stock phải cập nhật đúng
-- ========================================

-- ========================================
-- CHUẨN BỊ: Reset test data
-- ========================================
START TRANSACTION;

-- Xóa test data cũ
DELETE FROM project WHERE project_name LIKE 'TEST-STOCK%';
DELETE FROM stock_allocation WHERE notes LIKE '%TEST-STOCK%';

-- Setup: Tạo 1 sản phẩm test với stock = 35
UPDATE product 
SET 
    quantity_in_stock = 35,
    quantity_allocated = 0
WHERE id_product = 1001; -- TL-079

COMMIT;

-- ========================================
-- TEST CASE 1: Đơn 1 - Yêu cầu 10 cái
-- Expected: quantity_allocated = 10, available = 25
-- ========================================
SELECT '========== TEST CASE 1: ĐƠN 1 (10 cái) ==========' AS test_step;

-- Trước khi tạo đơn
SELECT 
    'BEFORE ORDER 1' AS timing,
    id_product,
    product_name,
    quantity_in_stock AS total,
    quantity_allocated AS allocated,
    quantity_available AS available
FROM product 
WHERE id_product = 1001;

-- Tạo Đơn 1
INSERT INTO project (
    id_project, project_name, id_cust, id_product, 
    qty_request, entry_date, pr_status,
    warning_flag, capacity_level_used, 
    finished_stock_available
) VALUES (
    'TEST-STOCK-001', 'TEST-STOCK-ORDER-1', 1, 1001,
    10, '2025-12-31', 1,
    0, 0, 35
);

-- Sau khi tạo đơn
SELECT 
    'AFTER ORDER 1' AS timing,
    id_product,
    product_name,
    quantity_in_stock AS total,
    quantity_allocated AS allocated,
    quantity_available AS available,
    CASE 
        WHEN quantity_allocated = 10 AND quantity_available = 25 
        THEN '✓ PASS' 
        ELSE '✗ FAIL' 
    END AS test_result
FROM product 
WHERE id_product = 1001;

-- Check allocation log
SELECT 
    'ALLOCATION LOG' AS log_type,
    id_project,
    quantity_allocated,
    allocation_type,
    notes,
    allocated_at
FROM stock_allocation 
WHERE id_project = 'TEST-STOCK-001';

-- ========================================
-- TEST CASE 2: Đơn 2 - Yêu cầu 20 cái
-- Expected: quantity_allocated = 30, available = 5
-- ========================================
SELECT '========== TEST CASE 2: ĐƠN 2 (20 cái) ==========' AS test_step;

-- Trước khi tạo đơn
SELECT 
    'BEFORE ORDER 2' AS timing,
    id_product,
    product_name,
    quantity_in_stock AS total,
    quantity_allocated AS allocated,
    quantity_available AS available
FROM product 
WHERE id_product = 1001;

-- Tạo Đơn 2 (deadline gần hơn → ưu tiên cao hơn)
INSERT INTO project (
    id_project, project_name, id_cust, id_product, 
    qty_request, entry_date, pr_status,
    warning_flag, capacity_level_used, 
    finished_stock_available
) VALUES (
    'TEST-STOCK-002', 'TEST-STOCK-ORDER-2', 1, 1001,
    20, '2025-12-20', 1,
    0, 0, 35
);

-- Sau khi tạo đơn
SELECT 
    'AFTER ORDER 2' AS timing,
    id_product,
    product_name,
    quantity_in_stock AS total,
    quantity_allocated AS allocated,
    quantity_available AS available,
    CASE 
        WHEN quantity_allocated = 30 AND quantity_available = 5 
        THEN '✓ PASS' 
        ELSE '✗ FAIL' 
    END AS test_result
FROM product 
WHERE id_product = 1001;

-- ========================================
-- TEST CASE 3: Đơn 3 - Yêu cầu 5,000 cái
-- Expected: quantity_allocated = 35, available = 0
-- Cảnh báo: Chỉ có 5 cái available, cần sản xuất 4,995 cái
-- ========================================
SELECT '========== TEST CASE 3: ĐƠN 3 (5,000 cái) ==========' AS test_step;

-- Trước khi tạo đơn
SELECT 
    'BEFORE ORDER 3' AS timing,
    id_product,
    product_name,
    quantity_in_stock AS total,
    quantity_allocated AS allocated,
    quantity_available AS available
FROM product 
WHERE id_product = 1001;

-- Tạo Đơn 3 (lớn, cần sản xuất nhiều)
INSERT INTO project (
    id_project, project_name, id_cust, id_product, 
    qty_request, entry_date, pr_status,
    warning_flag, capacity_level_used, 
    finished_stock_available
) VALUES (
    'TEST-STOCK-003', 'TEST-STOCK-ORDER-3', 1, 1001,
    5000, '2025-12-11', 1,
    1, 1, 35
);

-- Sau khi tạo đơn
SELECT 
    'AFTER ORDER 3' AS timing,
    id_product,
    product_name,
    quantity_in_stock AS total,
    quantity_allocated AS allocated,
    quantity_available AS available,
    CASE 
        WHEN quantity_allocated = 35 AND quantity_available = 0 
        THEN '✓ PASS' 
        ELSE '✗ FAIL' 
    END AS test_result
FROM product 
WHERE id_product = 1001;

-- ========================================
-- VERIFY: Tổng hợp kết quả
-- ========================================
SELECT '========== SUMMARY: TỔNG HỢP KẾT QUẢ ==========' AS test_step;

SELECT 
    id_project,
    project_name,
    qty_request,
    entry_date,
    finished_stock_available AS stock_at_creation,
    (SELECT quantity_allocated FROM stock_allocation WHERE id_project = p.id_project LIMIT 1) AS allocated_amount
FROM project p
WHERE project_name LIKE 'TEST-STOCK%'
ORDER BY entry_date;

-- Expected allocation:
-- Đơn 1: 10 cái
-- Đơn 2: 20 cái (ưu tiên hơn vì deadline gần)
-- Đơn 3: 5 cái (chỉ còn 5 available)
-- TỔNG: 35 cái

SELECT 
    'FINAL CHECK' AS check_type,
    id_product,
    product_name,
    quantity_in_stock AS total_stock,
    quantity_allocated AS total_allocated,
    quantity_available AS remaining_available,
    CASE 
        WHEN quantity_allocated = 35 AND quantity_available = 0 
        THEN '✓✓✓ ALL TESTS PASS ✓✓✓' 
        ELSE '✗✗✗ TESTS FAILED ✗✗✗' 
    END AS final_result
FROM product 
WHERE id_product = 1001;

-- ========================================
-- VERIFY: Check consistency
-- ========================================
SELECT '========== CONSISTENCY CHECK ==========' AS test_step;
CALL sp_check_stock_consistency();

-- Nếu không có output → Perfect! (consistent)
-- Nếu có output → Có vấn đề, cần fix

-- ========================================
-- CLEANUP (optional)
-- ========================================
-- Uncomment để xóa test data:
-- DELETE FROM project WHERE project_name LIKE 'TEST-STOCK%';
-- UPDATE product SET quantity_in_stock = 35, quantity_allocated = 0 WHERE id_product = 1001;

-- ========================================
-- EXPECTED OUTPUT
-- ========================================
/*
TEST CASE 1:
- BEFORE: total=35, allocated=0, available=35
- AFTER:  total=35, allocated=10, available=25 ✓ PASS

TEST CASE 2:
- BEFORE: total=35, allocated=10, available=25
- AFTER:  total=35, allocated=30, available=5 ✓ PASS

TEST CASE 3:
- BEFORE: total=35, allocated=30, available=5
- AFTER:  total=35, allocated=35, available=0 ✓ PASS

FINAL CHECK: ✓✓✓ ALL TESTS PASS ✓✓✓
*/
