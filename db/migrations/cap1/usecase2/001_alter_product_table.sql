-- =====================================================
-- Migration: UC2 - Quản lý sản phẩm & BOM
-- Description: Thêm BOM & tracking columns cho product table
-- Author: Cap 1 Team
-- Date: 2025-11-24
-- Database: db_production
-- =====================================================
-- QUAN TRỌNG: Database hiện tại có schema:
--   - product_name: VARCHAR(50)
--   - summary: LONGTEXT (chứa thông tin chi tiết)
--   - application: VARCHAR(100) (màu mực: Xanh, Đen, Đỏ, Nhiều màu)
--   - diameter: DECIMAL(3,1) DEFAULT 0.5 (0.5, 0.7, 1.0)
-- Đã có đủ các cột cơ bản, chỉ thêm BOM và tracking
-- =====================================================

USE db_production;

-- Thêm cột bom (nếu chưa có)
ALTER TABLE `product` 
ADD COLUMN IF NOT EXISTS `bom` JSON NULL COMMENT 'Định mức nguyên vật liệu (JSON)' AFTER `diameter`;

-- Thêm cột is_active (nếu chưa có)
ALTER TABLE `product` 
ADD COLUMN IF NOT EXISTS `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Đang sản xuất, 0=Ngừng' AFTER `bom`;

-- Thêm cột created_at (nếu chưa có)
ALTER TABLE `product` 
ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo' AFTER `is_active`;

-- Thêm cột updated_at (nếu chưa có)
ALTER TABLE `product` 
ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật' AFTER `created_at`;

-- Thêm cột created_by (nếu chưa có)
ALTER TABLE `product` 
ADD COLUMN IF NOT EXISTS `created_by` INT(11) NULL COMMENT 'User ID người tạo (FK user.user_id)' AFTER `updated_at`;

-- Thêm indexes (bỏ qua nếu đã tồn tại)
ALTER TABLE `product` 
ADD INDEX IF NOT EXISTS `idx_is_active` (`is_active`);

ALTER TABLE `product` 
ADD INDEX IF NOT EXISTS `idx_diameter` (`diameter`);

ALTER TABLE `product` 
ADD INDEX IF NOT EXISTS `idx_created_at` (`created_at`);

-- Cập nhật dữ liệu cũ (chỉ các row chưa có timestamp)
UPDATE `product` 
SET `is_active` = 1,
    `created_at` = COALESCE(`created_at`, NOW()),
    `updated_at` = COALESCE(`updated_at`, NOW())
WHERE `created_at` IS NULL OR `updated_at` IS NULL;

-- =====================================================
-- Sample BOM JSON Structure (cho ProductModel.php)
-- =====================================================
/*
{
  "materials": [
    {
      "id_material": 1002,
      "material_name": "Nhựa ABS",
      "quantity": 5.5,
      "unit": "gram"
    },
    {
      "id_material": 1003,
      "material_name": "Mực gel xanh",
      "quantity": 2.0,
      "unit": "gram"
    },
    {
      "id_material": 1005,
      "material_name": "Bi kim loại 0.5mm",
      "quantity": 1,
      "unit": "cái"
    }
  ]
}
*/

-- =====================================================
-- Quy tắc sử dụng:
-- - product_name: Tối đa 50 ký tự (VD: Bút bi TL-079)
-- - summary: LONGTEXT - Mô tả chi tiết sản phẩm
-- - application: VARCHAR(100) - Màu mực (Xanh, Đen, Đỏ, Nhiều màu)
-- - diameter: DECIMAL(3,1) - 0.5, 0.7, 1.0 (mm)
-- - bom: JSON - Link với material table via id_material
-- - Không xóa product nếu có project (FK constraint)
-- - Dùng trong UC7 (OrderModel.getProducts())
-- =====================================================
  DROP INDEX `idx_diameter`,
  DROP INDEX `idx_created_at`;

ALTER TABLE `product` 
  MODIFY COLUMN `product_name` VARCHAR(50) NOT NULL,
  MODIFY COLUMN `application` VARCHAR(50) NOT NULL;
*/
