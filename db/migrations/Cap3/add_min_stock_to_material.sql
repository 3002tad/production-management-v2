-- Migration: Thêm cột min_stock vào bảng material
-- Ngày tạo: 2025-11-02
-- Mục đích: Bổ sung định mức tồn kho tối thiểu cho từng nguyên liệu

START TRANSACTION;

-- Kiểm tra xem cột đã tồn tại chưa để tránh lỗi khi chạy nhiều lần
SET @column_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_NAME = 'material'
      AND COLUMN_NAME = 'min_stock'
      AND TABLE_SCHEMA = DATABASE()
);

-- Nếu chưa có thì thêm mới
SET @sql := IF(@column_exists = 0,
    'ALTER TABLE material
     ADD COLUMN min_stock INT DEFAULT 0 NULL
     COMMENT "";',
    'SELECT "Cột min_stock đã tồn tại, bỏ qua.";'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;
