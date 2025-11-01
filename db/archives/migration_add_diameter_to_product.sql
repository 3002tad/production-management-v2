-- ============================================
-- Migration: Thêm cột diameter vào bảng product
-- Mục đích: Đồng bộ thông tin đường kính bi viết giữa product và project
-- Ngày: 26/10/2025
-- UPDATED: Sử dụng DECIMAL(3,1) để lưu giá trị thực (0.5, 0.7, 1.0)
-- ============================================

USE `db_production`;

-- Kiểm tra và thêm cột diameter vào bảng product
-- Cột này sẽ lưu đường kính bi viết tiêu chuẩn của sản phẩm
-- Đơn vị: DECIMAL(3,1) (lưu trực tiếp: 0.5mm, 0.7mm, 1.0mm)

ALTER TABLE `product` 
ADD COLUMN `diameter` DECIMAL(3,1) NOT NULL DEFAULT 0.5 COMMENT 'Đường kính bi viết (mm)' 
AFTER `application`;

-- Cập nhật dữ liệu mẫu cho sản phẩm hiện có
-- Giả sử sản phẩm test có đường kính 0.5mm
UPDATE `product` 
SET `diameter` = 0.5 
WHERE `id_product` = 1001;

-- ============================================
-- LƯU Ý QUAN TRỌNG:
-- ============================================
-- 1. Cột diameter trong product và project đều dùng DECIMAL(3,1)
--    Lưu giá trị thực: 0.5, 0.7, 1.0 (mm)
--
-- 2. Khi thêm product mới, cần điền giá trị diameter
--    VD: 0.5, 0.7, 1.0
--
-- 3. Khi tạo project:
--    - Tự động copy giá trị diameter từ product (auto-fill)
--    - Có thể override nếu dự án cần đường kính khác
--
-- 4. View files đã cập nhật:
--    - application/views/admin/product/AddProduct.php (input DECIMAL)
--    - application/views/admin/product/Product.php (display DECIMAL)
--    - application/views/admin/project/AddProject.php (auto-fill)
--    - application/views/admin/project/UpdateProject.php (auto-fill)
--    - application/controllers/Admin.php (floatval)
--
-- ============================================
-- KẾT QUẢ SAU KHI CHẠY:
-- ============================================
-- Bảng product sẽ có cấu trúc:
-- - id_product (INT)
-- - product_name (VARCHAR)
-- - summary (LONGTEXT)
-- - application (LONGTEXT) - Màu mực
-- - diameter (DECIMAL(3,1)) - Đường kính bi viết (0.5, 0.7, 1.0)
--
-- ============================================
-- VERIFICATION:
-- ============================================
-- Chạy lệnh sau để kiểm tra:

SELECT 
    id_product,
    product_name,
    application AS 'Màu mực',
    CONCAT(diameter, ' mm') AS 'Đường kính'
FROM `product`;

-- Kiểm tra cấu trúc bảng:
DESCRIBE `product`;
