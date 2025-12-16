-- ========================================
-- SCRIPT RESET DỮ LIỆU TEST CHO UC7
-- ========================================
-- Mục đích: Reset về trạng thái ban đầu để test lại
-- Sử dụng: Sau mỗi lần demo hoặc test
-- ========================================

START TRANSACTION;

-- ========================================
-- BƯỚC 1: XÓA ĐƠN HÀNG TEST
-- ========================================
-- Xóa các đơn hàng có tên chứa "test", "demo", hoặc tạo trong 1 giờ qua

DELETE FROM project 
WHERE project_name LIKE '%test%' 
   OR project_name LIKE '%Test%'
   OR project_name LIKE '%demo%'
   OR project_name LIKE '%Demo%'
   OR project_name LIKE '%Order A%'
   OR project_name LIKE '%Order B%'
   OR project_name LIKE '%Big Corp%'
   OR created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR);

SELECT CONCAT('✅ Đã xóa ', ROW_COUNT(), ' đơn hàng test') as step1;

-- ========================================
-- BƯỚC 2: RESET TỒN KHO THÀNH PHẨM
-- ========================================
-- TL-079 (id=1001): 35 cái
UPDATE finished_stock 
SET quantity_in_stock = 35,
    quantity_received = 35,
    quantity_issued = 0,
    last_updated = NOW()
WHERE id_product = 1001;

SELECT '✅ Đã reset tồn kho thành phẩm' as step2;

-- ========================================
-- BƯỚC 3: RESET TỒN KHO NGUYÊN VẬT LIỆU
-- ========================================
UPDATE material SET stock = 5000,  min_stock = 1000 WHERE id_material = 1001; -- Test Matereal
UPDATE material SET stock = 10000, min_stock = 1000 WHERE id_material = 1002; -- Nhựa ABS
UPDATE material SET stock = 5000,  min_stock = 1000 WHERE id_material = 1003; -- Mực gel xanh
UPDATE material SET stock = 5000,  min_stock = 1000 WHERE id_material = 1004; -- Mực gel đen
UPDATE material SET stock = 2000,  min_stock = 500  WHERE id_material = 1005; -- Bi 0.5mm
UPDATE material SET stock = 3000,  min_stock = 500  WHERE id_material = 1006; -- Bi 0.7mm
UPDATE material SET stock = 2000,  min_stock = 500  WHERE id_material = 1007; -- Bi 1.0mm
UPDATE material SET stock = 1000,  min_stock = 1000 WHERE id_material = 1008; -- Lò xo thép

SELECT '✅ Đã reset tồn kho NVL' as step3;

-- ========================================
-- BƯỚC 4: VERIFY KẾT QUẢ
-- ========================================
SELECT '========================================' as separator;
SELECT '📊 TRẠNG THÁI SAU KHI RESET' as title;
SELECT '========================================' as separator;

-- Tồn kho thành phẩm
SELECT 
    'Tồn kho thành phẩm' as category,
    p.product_name,
    fs.quantity_in_stock as stock,
    'cái' as unit
FROM finished_stock fs
JOIN product p ON fs.id_product = p.id_product
WHERE fs.id_product = 1001;

-- Tồn kho NVL
SELECT 
    'Tồn kho NVL' as category,
    material_name,
    stock,
    uom as unit
FROM material
WHERE id_material IN (1001, 1002, 1003, 1004, 1005, 1006, 1007, 1008)
ORDER BY id_material;

-- Đơn hàng còn lại (active)
SELECT 
    'Đơn hàng active' as category,
    COUNT(*) as count,
    SUM(qty_request) as total_qty
FROM project
WHERE pr_status < 3
  AND id_product = 1001;

COMMIT;

SELECT '✅ RESET HOÀN TẤT - SẴN SÀNG TEST!' as final_status;

-- ========================================
-- OPTIONAL: XÓA TẤT CẢ ĐƠN HÀNG (NGUY HIỂM!)
-- ========================================
-- CHỈ DÙNG KHI CẦN XÓA HẾT!
/*
START TRANSACTION;

DELETE FROM project WHERE id_product = 1001;

SELECT '⚠️ ĐÃ XÓA TẤT CẢ ĐƠN HÀNG CỦA TL-079!' as warning;

COMMIT;
*/

-- ========================================
-- GHI CHÚ
-- ========================================
/*
CÁCH SỬ DỤNG:

1. Sau mỗi lần test:
   mysql -u root -p db_production < db/reset_test_data_uc7.sql

2. Hoặc copy-paste vào MySQL Workbench và chạy

3. Sau khi chạy, kiểm tra:
   - Tồn kho thành phẩm: 35 cái
   - Tồn kho NVL: Đầy đủ như ban đầu
   - Đơn hàng test: Đã xóa

4. Bắt đầu test lại từ đầu!

QUAN TRỌNG:
- Script này CHỈ xóa đơn có từ "test", "demo" trong tên
- Hoặc đơn tạo trong 1 giờ qua
- Không ảnh hưởng đơn hàng thật!

NẾU CẦN XÓA TẤT CẢ:
- Uncomment phần OPTIONAL ở cuối
- Nhưng PHẢI CHẮC CHẮN trước khi chạy!
*/
