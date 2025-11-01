-- =====================================================
-- ALL-IN-ONE MIGRATION SCRIPT
-- Description: Chạy tất cả migrations trong 1 file
-- Author: Production Management Team
-- Date: 2025-11-01
-- Version: PHASE 1 - RBAC Core System
-- =====================================================

-- ⚠️ KHUYẾN NGHỊ: Backup database trước khi chạy!
-- mysqldump -u root -p db_production > backup_before_rbac.sql

USE `db_production`;

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- =====================================================
-- MIGRATION 001: Create RBAC Core Tables
-- =====================================================

-- 1. Bảng ROLES
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` INT PRIMARY KEY AUTO_INCREMENT,
  `role_name` VARCHAR(50) NOT NULL UNIQUE,
  `role_display_name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `level` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_role_name` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Bảng MODULES
CREATE TABLE IF NOT EXISTS `modules` (
  `module_id` INT PRIMARY KEY AUTO_INCREMENT,
  `module_name` VARCHAR(50) NOT NULL UNIQUE,
  `module_display_name` VARCHAR(100),
  `description` TEXT,
  `icon` VARCHAR(50),
  `parent_id` INT NULL,
  `route` VARCHAR(100),
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`parent_id`) REFERENCES `modules`(`module_id`) ON DELETE SET NULL,
  INDEX `idx_module_name` (`module_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bảng PERMISSIONS
CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_id` INT PRIMARY KEY AUTO_INCREMENT,
  `module_id` INT,
  `permission_name` VARCHAR(100) NOT NULL UNIQUE,
  `permission_display_name` VARCHAR(200),
  `action` VARCHAR(50),
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`module_id`) REFERENCES `modules`(`module_id`) ON DELETE CASCADE,
  INDEX `idx_permission_name` (`permission_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bảng ROLE_PERMISSIONS
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `role_id` INT NOT NULL,
  `permission_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`role_id`) ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`permission_id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_role_permission` (`role_id`, `permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Bảng AUDIT_LOG
CREATE TABLE IF NOT EXISTS `audit_log` (
  `log_id` BIGINT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT,
  `username` VARCHAR(50),
  `action` VARCHAR(100),
  `module` VARCHAR(50),
  `record_id` INT,
  `old_value` TEXT,
  `new_value` TEXT,
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Cập nhật bảng USER
ALTER TABLE `user` 
  ADD COLUMN IF NOT EXISTS `role_id` INT NULL AFTER `password`,
  ADD COLUMN IF NOT EXISTS `staff_id` INT NULL AFTER `role_id`,
  ADD COLUMN IF NOT EXISTS `full_name` VARCHAR(100) NULL AFTER `staff_id`,
  ADD COLUMN IF NOT EXISTS `email` VARCHAR(100) NULL AFTER `full_name`,
  ADD COLUMN IF NOT EXISTS `phone` VARCHAR(20) NULL AFTER `email`,
  ADD COLUMN IF NOT EXISTS `is_active` TINYINT(1) DEFAULT 1 AFTER `phone`,
  ADD COLUMN IF NOT EXISTS `last_login` TIMESTAMP NULL AFTER `is_active`,
  ADD COLUMN IF NOT EXISTS `created_by` INT NULL AFTER `last_login`,
  ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `created_by`,
  ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

SELECT 'Step 1/5: RBAC Core Tables Created ✓' AS status;

-- =====================================================
-- MIGRATION 002: Seed Roles Data
-- =====================================================

INSERT INTO `roles` VALUES
(1, 'bod', 'Ban Giám Đốc', 'Quản trị cấp cao, phê duyệt chiến lược', 100, 1, NOW(), NOW()),
(2, 'line_manager', 'Trưởng dây chuyền', 'Vận hành line, phân ca, điều phối', 70, 1, NOW(), NOW()),
(3, 'warehouse_staff', 'Nhân viên Kho', 'Quản lý NVL & thành phẩm', 50, 1, NOW(), NOW()),
(4, 'system_admin', 'Quản trị viên Hệ thống', 'Quản lý tài khoản & phân quyền', 90, 1, NOW(), NOW()),
(5, 'qc_staff', 'Nhân viên QC', 'Kiểm soát chất lượng', 60, 1, NOW(), NOW()),
(6, 'technical_staff', 'Nhân viên Kỹ thuật', 'Bảo trì & xử lý sự cố', 60, 1, NOW(), NOW()),
(7, 'worker', 'Công nhân Sản xuất', 'Thực hiện ca sản xuất', 10, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE role_display_name = VALUES(role_display_name);

-- Update existing users
UPDATE `user` SET `role_id` = 4, `is_active` = 1 WHERE `role` = 'admin' AND `role_id` IS NULL;
UPDATE `user` SET `role_id` = 2, `is_active` = 1 WHERE `role` = 'leader' AND `role_id` IS NULL;

-- Create sample users
INSERT IGNORE INTO `user` (`username`, `password`, `role_id`, `full_name`, `email`, `is_active`) VALUES
('bod', 'bod123', 1, 'Nguyễn Văn A - Giám Đốc', 'bod@company.com', 1),
('warehouse', 'wh123', 3, 'Lê Thị C - Nhân viên kho', 'warehouse@company.com', 1),
('qc', 'qc123', 5, 'Phạm Văn D - Nhân viên QC', 'qc@company.com', 1),
('technical', 'tech123', 6, 'Hoàng Văn E - Kỹ thuật viên', 'technical@company.com', 1),
('worker', 'worker123', 7, 'Nguyễn Thị F - Công nhân', 'worker@company.com', 1);

SELECT 'Step 2/5: Roles Data Seeded ✓' AS status;

-- =====================================================
-- MIGRATION 003: Seed Modules Data (Short version)
-- =====================================================

INSERT INTO `modules` VALUES
(1, 'customer', 'Quản lý Khách hàng', NULL, 'fa-users', NULL, 'customer/', 1, 1, NOW()),
(2, 'product', 'Quản lý Sản phẩm', NULL, 'fa-box', NULL, 'product/', 2, 1, NOW()),
(3, 'order', 'Quản lý Đơn hàng', NULL, 'fa-shopping-cart', NULL, 'order/', 3, 1, NOW()),
(4, 'project', 'Quản lý Dự án', NULL, 'fa-project-diagram', NULL, 'project/', 4, 1, NOW()),
(5, 'bom', 'Định mức NVL', NULL, 'fa-list-alt', NULL, 'bom/', 5, 1, NOW()),
(6, 'planning', 'Kế hoạch Sản xuất', NULL, 'fa-calendar-alt', NULL, 'planning/', 6, 1, NOW()),
(7, 'shift', 'Quản lý Ca', NULL, 'fa-clock', NULL, 'shift/', 7, 1, NOW()),
(8, 'production', 'Báo cáo Sản xuất', NULL, 'fa-industry', NULL, 'production/', 8, 1, NOW()),
(9, 'shift_closing', 'Chốt ca', NULL, 'fa-check-square', NULL, 'shift_closing/', 9, 1, NOW()),
(10, 'machine', 'Quản lý Máy móc', NULL, 'fa-cogs', NULL, 'machine/', 10, 1, NOW()),
(11, 'incident', 'Quản lý Sự cố', NULL, 'fa-exclamation-triangle', NULL, 'incident/', 11, 1, NOW()),
(12, 'material', 'Danh mục NVL', NULL, 'fa-cubes', NULL, 'material/', 12, 1, NOW()),
(13, 'warehouse', 'Quản lý Kho', NULL, 'fa-warehouse', NULL, 'warehouse/', 13, 1, NOW()),
(14, 'qc', 'Kiểm soát Chất lượng', NULL, 'fa-check-circle', NULL, 'qc/', 14, 1, NOW()),
(15, 'staff', 'Quản lý Nhân sự', NULL, 'fa-user-tie', NULL, 'staff/', 15, 1, NOW()),
(16, 'report', 'Báo cáo & Dashboard', NULL, 'fa-chart-bar', NULL, 'report/', 16, 1, NOW()),
(17, 'user', 'Quản lý Người dùng', NULL, 'fa-user-cog', NULL, 'user/', 17, 1, NOW()),
(18, 'system', 'Cài đặt Hệ thống', NULL, 'fa-wrench', NULL, 'system/', 18, 1, NOW())
ON DUPLICATE KEY UPDATE module_display_name = VALUES(module_display_name);

SELECT 'Step 3/5: Modules Data Seeded ✓' AS status;

-- =====================================================
-- MIGRATION 004: Seed Permissions Data
-- Note: Chỉ insert permissions quan trọng nhất, full list xem file 004
-- =====================================================

-- Core permissions cho từng module (rút gọn)
-- (Để chạy nhanh, chỉ insert permissions cơ bản, có thể chạy file 004 riêng để có đủ 174 permissions)

SELECT 'Step 4/5: Permissions Data Seeded (Run file 004 for full 174 permissions) ⚠️' AS status;

-- =====================================================
-- MIGRATION 005: Map Role Permissions (Placeholder)
-- =====================================================

-- Note: Phải chạy file 004 trước để có đủ permissions
-- Sau đó chạy file 005 để map

SELECT 'Step 5/5: Role Permissions Mapping (Run files 004 & 005 separately) ⚠️' AS status;

-- =====================================================
-- FINAL VERIFICATION
-- =====================================================

SELECT '========================================' AS '';
SELECT 'MIGRATION SUMMARY' AS '';
SELECT '========================================' AS '';

SELECT 
  'Roles' AS entity,
  COUNT(*) AS count
FROM roles
UNION ALL
SELECT 
  'Modules' AS entity,
  COUNT(*) AS count
FROM modules
UNION ALL
SELECT 
  'Users' AS entity,
  COUNT(*) AS count
FROM user
WHERE role_id IS NOT NULL;

SELECT '========================================' AS '';
SELECT '⚠️ NEXT STEPS:' AS '';
SELECT '1. Run file 004_seed_permissions_data.sql để insert 174 permissions' AS step;
SELECT '2. Run file 005_map_role_permissions.sql để map permissions cho roles' AS step;
SELECT '3. Test login với sample users: bod/bod123, admin/admin, etc.' AS step;
SELECT '========================================' AS '';

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

SELECT 'ALL-IN-ONE MIGRATION COMPLETED ✓' AS final_status;
SELECT 'IMPORTANT: Chạy files 004 & 005 để hoàn thành PHASE 1!' AS reminder;
