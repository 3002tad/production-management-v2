-- ===================================================================
-- MIGRATION TÙY CHỌN: Đổi diameter từ INT sang DECIMAL
-- Created: 2025-10-26
-- Description: Cho phép lưu đường kính chính xác như 0.5, 0.7, 1.0 mm
-- ===================================================================

USE `db_production`;

-- ===================================================================
-- LƯU Ý: CHẠY FILE NÀY CHỈ KHI MUỐN LƯU DECIMAL CHO DIAMETER
-- ===================================================================

-- Backup dữ liệu diameter hiện tại
CREATE TABLE IF NOT EXISTS `project_diameter_backup` AS
SELECT `id_project`, `diameter` FROM `project`;

-- Đổi kiểu dữ liệu diameter từ INT sang DECIMAL(3,1)
-- Cho phép lưu giá trị như: 0.5, 0.7, 1.0, 1.2 mm
ALTER TABLE `project` 
MODIFY COLUMN `diameter` DECIMAL(3,1) NOT NULL 
COMMENT 'Đường kính bi viết (mm): 0.5, 0.7, 1.0';

-- Cập nhật dữ liệu cũ (giả sử đang lưu INT, ví dụ: 20 → 2.0 mm hoặc 0.7 mm)
-- Tùy theo cách lưu cũ, điều chỉnh công thức
-- Nếu lưu 7 cho 0.7mm: chia 10
UPDATE `project` SET `diameter` = `diameter` / 10 WHERE `diameter` > 10;

-- Hoặc nếu muốn reset về giá trị mặc định
-- UPDATE `project` SET `diameter` = 0.7 WHERE `id_project` = 1001;

-- ===================================================================
-- Kiểm tra sau khi đổi
-- ===================================================================

SELECT 
    `id_project`,
    `project_name`,
    CONCAT(`diameter`, ' mm') AS `diameter_display`,
    `qty_request`
FROM `project`;

-- ===================================================================
-- LƯU Ý:
-- - Nếu không cần độ chính xác cao, giữ INT và lưu 5 cho 0.5mm, 7 cho 0.7mm
-- - Nếu cần chính xác, dùng DECIMAL(3,1)
-- - Nhớ cập nhật logic trong PHP nếu đổi kiểu dữ liệu
-- ===================================================================

COMMIT;
