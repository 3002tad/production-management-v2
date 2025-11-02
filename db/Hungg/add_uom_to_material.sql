-- Migration: Thêm cột uom vào bảng material
-- Ngày tạo: 2025-11-02
-- Mục đích: Lưu đơn vị đo lường (UoM) cho nguyên liệu

START TRANSACTION;

-- Kiểm tra xem cột đã tồn tại chưa để tránh lỗi khi chạy nhiều lần
SET @column_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_NAME = 'material'
      AND COLUMN_NAME = 'uom'
      AND TABLE_SCHEMA = DATABASE()
);

-- Nếu chưa có thì thêm mới
SET @sql := IF(@column_exists = 0,
    'ALTER TABLE material
     ADD COLUMN uom VARCHAR(10) NOT NULL DEFAULT "g" AFTER min_stock;',
    'SELECT "Cột uom đã tồn tại, bỏ qua.";'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;
