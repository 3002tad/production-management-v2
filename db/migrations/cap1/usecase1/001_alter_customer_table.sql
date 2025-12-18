-- =====================================================
-- Migration: UC1 - Quản lý khách hàng
-- Description: Thêm cột mới cho customer table (KHÔNG MODIFY cột cũ)
-- Author: Cap 1 Team  
-- Date: 2025-11-24
-- Database: db_production
-- =====================================================
-- QUAN TRỌNG: Database hiện tại có schema:
--   - telp: INT(20) - Chỉ lưu số (VD: 21293383)
--   - email: VARCHAR(25) - Giới hạn 25 ký tự
--   - address: VARCHAR(50) - Giới hạn 50 ký tự
-- KHÔNG được MODIFY các cột này để tránh mất dữ liệu
-- =====================================================

USE db_production;

-- Thêm cột is_active (nếu chưa có)
ALTER TABLE `customer` 
ADD COLUMN IF NOT EXISTS `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Hoạt động, 0=Ngừng hợp tác' AFTER `email`;

-- Thêm cột notes (nếu chưa có)
ALTER TABLE `customer` 
ADD COLUMN IF NOT EXISTS `notes` TEXT NULL COMMENT 'Ghi chú về khách hàng' AFTER `is_active`;

-- Thêm cột created_at (nếu chưa có)
ALTER TABLE `customer` 
ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo' AFTER `notes`;

-- Thêm cột updated_at (nếu chưa có)
ALTER TABLE `customer` 
ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật' AFTER `created_at`;

-- Thêm cột created_by (nếu chưa có)
ALTER TABLE `customer` 
ADD COLUMN IF NOT EXISTS `created_by` INT(11) NULL COMMENT 'User ID người tạo (FK user.user_id)' AFTER `updated_at`;

-- Thêm indexes (bỏ qua nếu đã tồn tại)
ALTER TABLE `customer` 
ADD INDEX IF NOT EXISTS `idx_is_active` (`is_active`);

ALTER TABLE `customer` 
ADD INDEX IF NOT EXISTS `idx_created_at` (`created_at`);

ALTER TABLE `customer` 
ADD INDEX IF NOT EXISTS `idx_email` (`email`);

-- Cập nhật dữ liệu cũ (chỉ các row chưa có timestamp)
UPDATE `customer` 
SET `is_active` = 1,
    `created_at` = COALESCE(`created_at`, NOW()),
    `updated_at` = COALESCE(`updated_at`, NOW())
WHERE `created_at` IS NULL OR `updated_at` IS NULL;

-- =====================================================
-- Quy tắc sử dụng:
-- - telp: Chỉ lưu SỐ (VD: 0909123456 -> 909123456)
-- - email: Tối đa 25 ký tự (VD: abc@mail.com)
-- - address: Tối đa 50 ký tự
-- - Không xóa customer nếu có project (FK constraint)
-- - Check với OrderModel.getCustomers() trong UC7
-- =====================================================
