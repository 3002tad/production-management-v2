-- ============================================
-- FIX: Thêm cột diameter vào bảng product
-- Chạy file này nếu bạn gặp lỗi "Undefined property: diameter"
-- ============================================

USE `db_production`;

-- Kiểm tra xem cột diameter đã tồn tại chưa
-- Nếu chưa có, thêm vào
SET @dbname = 'db_production';
SET @tablename = 'product';
SET @columnname = 'diameter';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 'Column diameter already exists in product table' AS message;",
  "ALTER TABLE `product` ADD COLUMN `diameter` DECIMAL(3,1) NOT NULL DEFAULT 0.5 COMMENT 'Đường kính bi viết (mm)' AFTER `application`;"
));

PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Cập nhật dữ liệu mẫu cho các product hiện có (nếu có)
UPDATE `product` 
SET `diameter` = 0.5 
WHERE `diameter` IS NULL OR `diameter` = 0;

-- Verify kết quả
SELECT 
    id_product,
    product_name,
    CONCAT(diameter, ' mm') AS diameter_display
FROM `product`;

-- Kiểm tra cấu trúc bảng
DESCRIBE `product`;
