-- ===================================================================
-- FIX LỖI FONT TIẾNG VIỆT - UTF-8 CHARSET
-- Created: 2025-10-26
-- Description: Sửa lỗi hiển thị tiếng Việt bị mã hóa sai
-- LƯU Ý: Phải chạy db_production.sql TRƯỚC KHI chạy file này!
-- ===================================================================

-- Kiểm tra database tồn tại
USE `db_production`;

-- Kiểm tra các bảng đã tồn tại chưa
SELECT 'Đang kiểm tra bảng...' AS '';
SHOW TABLES;

-- Nếu không có bảng nào, hãy chạy db_production.sql trước!

-- ===================================================================
-- BƯỚC 1: ĐỔI CHARSET CỦA DATABASE
-- ===================================================================

ALTER DATABASE `db_production` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- ===================================================================
-- BƯỚC 2: ĐỔI CHARSET CỦA TẤT CẢ CÁC BẢNG
-- ===================================================================

-- Bảng customer
ALTER TABLE `customer` 
CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng product
ALTER TABLE `product` 
CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng project
ALTER TABLE `project` 
CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng planning
ALTER TABLE `planning` 
CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng plan_shift
ALTER TABLE `plan_shift` 
CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng machine
ALTER TABLE `machine` 
CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng material
ALTER TABLE `material` 
CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng staff
ALTER TABLE `staff` 
CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng shiftment
ALTER TABLE `shiftment` 
CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng sorting_report
ALTER TABLE `sorting_report` 
CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng finished_report
ALTER TABLE `finished_report` 
CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng p_machine
ALTER TABLE `p_machine` 
CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng p_material
ALTER TABLE `p_material` 
CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng user
ALTER TABLE `user` 
CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ===================================================================
-- BƯỚC 3: SỬA DỮ LIỆU BỊ MÃ HÓA SAI (CHỈ KHI DỮ LIỆU ĐÃ TỒN TẠI)
-- ===================================================================

-- Kiểm tra xem có dữ liệu không
SELECT COUNT(*) AS total_products FROM `product`;

-- Nếu dữ liệu đã bị lưu sai, cần cập nhật lại thủ công
-- CHỈ CHẠY CÁC LỆNH UPDATE NẾU BẢNG CÓ DỮ LIỆU!

-- Cập nhật product (chỉ nếu tồn tại)
UPDATE `product` SET 
    `product_name` = 'Bút bi TL-079',
    `summary` = 'Bút bi mực gel, thân nhựa trong suốt, viết mượt',
    `application` = 'Xanh dương'
WHERE `id_product` = 1001 AND EXISTS (SELECT 1 FROM `product` WHERE `id_product` = 1001);

UPDATE `product` SET 
    `product_name` = 'Bút bi TL-050',
    `summary` = 'Bút bi dầu, thân nhựa màu, giá rẻ',
    `application` = 'Đen'
WHERE `id_product` = 1002 AND EXISTS (SELECT 1 FROM `product` WHERE `id_product` = 1002);

UPDATE `product` SET 
    `product_name` = 'Bút bi TL-100',
    `summary` = 'Bút bi cao cấp, thân kim loại',
    `application` = 'Đỏ'
WHERE `id_product` = 1003 AND EXISTS (SELECT 1 FROM `product` WHERE `id_product` = 1003);

UPDATE `product` SET 
    `product_name` = 'Bút bi TL-Multi',
    `summary` = 'Bút bi 4 màu, đa năng',
    `application` = 'Nhiều màu'
WHERE `id_product` = 1004 AND EXISTS (SELECT 1 FROM `product` WHERE `id_product` = 1004);

-- Cập nhật material
UPDATE `material` SET `material_name` = 'Nhựa ABS' WHERE `id_material` = 1002;
UPDATE `material` SET `material_name` = 'Mực gel xanh' WHERE `id_material` = 1003;
UPDATE `material` SET `material_name` = 'Mực gel đen' WHERE `id_material` = 1004;
UPDATE `material` SET `material_name` = 'Bi kim loại 0.5mm' WHERE `id_material` = 1005;
UPDATE `material` SET `material_name` = 'Bi kim loại 0.7mm' WHERE `id_material` = 1006;
UPDATE `material` SET `material_name` = 'Bi kim loại 1.0mm' WHERE `id_material` = 1007;
UPDATE `material` SET `material_name` = 'Lò xo thép' WHERE `id_material` = 1008;

-- Cập nhật shiftment
UPDATE `shiftment` SET `shift_name` = 'Ca Sáng' WHERE `shift_name` LIKE '%Pagi%' OR `shift_name` LIKE '%pagi%';
UPDATE `shiftment` SET `shift_name` = 'Ca Chiều' WHERE `shift_name` LIKE '%Siang%' OR `shift_name` LIKE '%siang%';
UPDATE `shiftment` SET `shift_name` = 'Ca Tối' WHERE `shift_name` LIKE '%Malam%' OR `shift_name` LIKE '%malam%';

-- ===================================================================
-- BƯỚC 4: KIỂM TRA SAU KHI FIX
-- ===================================================================

SELECT '=== KIỂM TRA CHARSET ===' AS '';

-- Kiểm tra charset database
SELECT 
    DEFAULT_CHARACTER_SET_NAME, 
    DEFAULT_COLLATION_NAME 
FROM INFORMATION_SCHEMA.SCHEMATA 
WHERE SCHEMA_NAME = 'db_production';

-- Kiểm tra charset các bảng
SELECT 
    TABLE_NAME,
    TABLE_COLLATION
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = 'db_production'
ORDER BY TABLE_NAME;

-- Kiểm tra dữ liệu tiếng Việt
SELECT '=== KIỂM TRA DỮ LIỆU TIẾNG VIỆT ===' AS '';

SELECT id_product, product_name, summary, application FROM product;
SELECT id_material, material_name FROM material;
SELECT id_shift, shift_name FROM shiftment;

-- ===================================================================
-- LƯU Ý QUAN TRỌNG
-- ===================================================================
-- 
-- 1. TRƯỚC KHI CHẠY: Backup database!
--    mysqldump -u root -p db_production > backup_before_charset_fix.sql
--
-- 2. SAU KHI CHẠY: Cần cấu hình CodeIgniter để dùng UTF-8
--    File: application/config/database.php
--    'char_set' => 'utf8mb4',
--    'dbcollat' => 'utf8mb4_unicode_ci',
--
-- 3. Nếu dữ liệu cũ đã bị lưu sai charset, cần cập nhật thủ công
--    hoặc nhập lại dữ liệu sau khi đổi charset
--
-- 4. Đảm bảo MySQL connection trong PHP dùng UTF-8:
--    $db['default']['char_set'] = 'utf8mb4';
--    $db['default']['dbcollat'] = 'utf8mb4_unicode_ci';
--
-- 5. Thêm vào đầu file PHP (nếu cần):
--    header('Content-Type: text/html; charset=utf-8');
--
-- ===================================================================

COMMIT;
