-- =====================================================
-- MIGRATION 001: RBAC Core Tables
-- Description: Tạo bảng roles, modules, permissions, role_permissions
-- Author: Production Management Team
-- Date: 2025-11-01
-- =====================================================

-- Sử dụng database
USE `db_production`;

-- =====================================================
-- 1. Bảng ROLES (Vai trò)
-- =====================================================
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` INT PRIMARY KEY AUTO_INCREMENT,
  `role_name` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Tên role (code): bod, line_manager, warehouse_staff, etc.',
  `role_display_name` VARCHAR(100) NOT NULL COMMENT 'Tên hiển thị: Ban Giám Đốc, Trưởng dây chuyền, etc.',
  `description` TEXT COMMENT 'Mô tả chi tiết vai trò',
  `level` INT DEFAULT 0 COMMENT 'Cấp độ quyền hạn: BOD=100, Admin=90, Manager=70, Staff=50, Worker=10',
  `is_active` TINYINT(1) DEFAULT 1 COMMENT '1=Active, 0=Inactive',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_role_name` (`role_name`),
  INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bảng vai trò người dùng trong hệ thống';

-- =====================================================
-- 2. Bảng MODULES (Nhóm chức năng)
-- =====================================================
CREATE TABLE IF NOT EXISTS `modules` (
  `module_id` INT PRIMARY KEY AUTO_INCREMENT,
  `module_name` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Tên module (code): customer, product, order, etc.',
  `module_display_name` VARCHAR(100) COMMENT 'Tên hiển thị: Quản lý Khách hàng, etc.',
  `description` TEXT COMMENT 'Mô tả chức năng module',
  `icon` VARCHAR(50) COMMENT 'Font Awesome icon class: fa-users, fa-box, etc.',
  `parent_id` INT NULL COMMENT 'Module cha (cho menu đa cấp)',
  `route` VARCHAR(100) COMMENT 'Route URL: customer/, product/, etc.',
  `sort_order` INT DEFAULT 0 COMMENT 'Thứ tự hiển thị trong menu',
  `is_active` TINYINT(1) DEFAULT 1 COMMENT '1=Active, 0=Inactive',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`parent_id`) REFERENCES `modules`(`module_id`) ON DELETE SET NULL,
  INDEX `idx_module_name` (`module_name`),
  INDEX `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bảng nhóm chức năng/module trong hệ thống';

-- =====================================================
-- 3. Bảng PERMISSIONS (Quyền hạn)
-- =====================================================
CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_id` INT PRIMARY KEY AUTO_INCREMENT,
  `module_id` INT COMMENT 'Thuộc module nào',
  `permission_name` VARCHAR(100) NOT NULL UNIQUE COMMENT 'Tên quyền: customer.view, product.create, etc.',
  `permission_display_name` VARCHAR(200) COMMENT 'Tên hiển thị: Xem khách hàng, Tạo sản phẩm, etc.',
  `action` VARCHAR(50) COMMENT 'Hành động: view, create, edit, delete, approve, etc.',
  `description` TEXT COMMENT 'Mô tả chi tiết quyền',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`module_id`) REFERENCES `modules`(`module_id`) ON DELETE CASCADE,
  INDEX `idx_permission_name` (`permission_name`),
  INDEX `idx_module_id` (`module_id`),
  INDEX `idx_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bảng quyền hạn chi tiết trong hệ thống';

-- =====================================================
-- 4. Bảng ROLE_PERMISSIONS (Liên kết Role-Permission)
-- =====================================================
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `role_id` INT NOT NULL COMMENT 'ID vai trò',
  `permission_id` INT NOT NULL COMMENT 'ID quyền hạn',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`role_id`) ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`permission_id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_role_permission` (`role_id`, `permission_id`),
  INDEX `idx_role_id` (`role_id`),
  INDEX `idx_permission_id` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bảng liên kết nhiều-nhiều giữa Role và Permission';

-- =====================================================
-- 5. Bảng AUDIT_LOG (Nhật ký hoạt động)
-- =====================================================
CREATE TABLE IF NOT EXISTS `audit_log` (
  `log_id` BIGINT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT COMMENT 'ID người thực hiện',
  `username` VARCHAR(50) COMMENT 'Tên đăng nhập',
  `action` VARCHAR(100) COMMENT 'Hành động: login, logout, create, update, delete, approve, reject',
  `module` VARCHAR(50) COMMENT 'Module bị tác động: customer, product, order, etc.',
  `record_id` INT COMMENT 'ID của bản ghi bị tác động',
  `old_value` TEXT COMMENT 'Giá trị cũ (JSON format)',
  `new_value` TEXT COMMENT 'Giá trị mới (JSON format)',
  `ip_address` VARCHAR(45) COMMENT 'Địa chỉ IP (hỗ trợ IPv6)',
  `user_agent` TEXT COMMENT 'Thông tin trình duyệt',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_action` (`action`),
  INDEX `idx_module` (`module`),
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_composite` (`user_id`, `action`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bảng ghi nhật ký mọi hoạt động trong hệ thống';

-- =====================================================
-- 6. Cập nhật bảng USER (thêm RBAC fields)
-- =====================================================

-- Kiểm tra và thêm cột role_id
SET @sql_role_id = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_SCHEMA = 'db_production' 
   AND TABLE_NAME = 'user' 
   AND COLUMN_NAME = 'role_id') = 0,
  'ALTER TABLE `user` ADD COLUMN `role_id` INT NULL COMMENT "ID vai trò" AFTER `password`',
  'SELECT "Column role_id already exists" AS message'
);
PREPARE stmt FROM @sql_role_id;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Kiểm tra và thêm cột staff_id
SET @sql_staff_id = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_SCHEMA = 'db_production' 
   AND TABLE_NAME = 'user' 
   AND COLUMN_NAME = 'staff_id') = 0,
  'ALTER TABLE `user` ADD COLUMN `staff_id` INT NULL COMMENT "Link to staff table nếu là công nhân" AFTER `role_id`',
  'SELECT "Column staff_id already exists" AS message'
);
PREPARE stmt FROM @sql_staff_id;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Kiểm tra và thêm cột full_name
SET @sql_full_name = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_SCHEMA = 'db_production' 
   AND TABLE_NAME = 'user' 
   AND COLUMN_NAME = 'full_name') = 0,
  'ALTER TABLE `user` ADD COLUMN `full_name` VARCHAR(100) NULL COMMENT "Họ và tên đầy đủ" AFTER `staff_id`',
  'SELECT "Column full_name already exists" AS message'
);
PREPARE stmt FROM @sql_full_name;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Kiểm tra và thêm cột email
SET @sql_email = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_SCHEMA = 'db_production' 
   AND TABLE_NAME = 'user' 
   AND COLUMN_NAME = 'email') = 0,
  'ALTER TABLE `user` ADD COLUMN `email` VARCHAR(100) NULL COMMENT "Email liên hệ" AFTER `full_name`',
  'SELECT "Column email already exists" AS message'
);
PREPARE stmt FROM @sql_email;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Kiểm tra và thêm cột phone
SET @sql_phone = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_SCHEMA = 'db_production' 
   AND TABLE_NAME = 'user' 
   AND COLUMN_NAME = 'phone') = 0,
  'ALTER TABLE `user` ADD COLUMN `phone` VARCHAR(20) NULL COMMENT "Số điện thoại" AFTER `email`',
  'SELECT "Column phone already exists" AS message'
);
PREPARE stmt FROM @sql_phone;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Kiểm tra và thêm cột is_active
SET @sql_is_active = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_SCHEMA = 'db_production' 
   AND TABLE_NAME = 'user' 
   AND COLUMN_NAME = 'is_active') = 0,
  'ALTER TABLE `user` ADD COLUMN `is_active` TINYINT(1) DEFAULT 1 COMMENT "1=Active, 0=Locked" AFTER `phone`',
  'SELECT "Column is_active already exists" AS message'
);
PREPARE stmt FROM @sql_is_active;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Kiểm tra và thêm cột last_login
SET @sql_last_login = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_SCHEMA = 'db_production' 
   AND TABLE_NAME = 'user' 
   AND COLUMN_NAME = 'last_login') = 0,
  'ALTER TABLE `user` ADD COLUMN `last_login` TIMESTAMP NULL COMMENT "Lần đăng nhập cuối" AFTER `is_active`',
  'SELECT "Column last_login already exists" AS message'
);
PREPARE stmt FROM @sql_last_login;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Kiểm tra và thêm cột created_by
SET @sql_created_by = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_SCHEMA = 'db_production' 
   AND TABLE_NAME = 'user' 
   AND COLUMN_NAME = 'created_by') = 0,
  'ALTER TABLE `user` ADD COLUMN `created_by` INT NULL COMMENT "User tạo tài khoản này" AFTER `last_login`',
  'SELECT "Column created_by already exists" AS message'
);
PREPARE stmt FROM @sql_created_by;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Kiểm tra và thêm cột created_at
SET @sql_created_at = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_SCHEMA = 'db_production' 
   AND TABLE_NAME = 'user' 
   AND COLUMN_NAME = 'created_at') = 0,
  'ALTER TABLE `user` ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `created_by`',
  'SELECT "Column created_at already exists" AS message'
);
PREPARE stmt FROM @sql_created_at;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Kiểm tra và thêm cột updated_at
SET @sql_updated_at = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_SCHEMA = 'db_production' 
   AND TABLE_NAME = 'user' 
   AND COLUMN_NAME = 'updated_at') = 0,
  'ALTER TABLE `user` ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`',
  'SELECT "Column updated_at already exists" AS message'
);
PREPARE stmt FROM @sql_updated_at;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Thêm foreign keys (kiểm tra trước khi thêm)
SET @fk_check = (SELECT COUNT(*) 
  FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
  WHERE TABLE_SCHEMA = 'db_production' 
  AND TABLE_NAME = 'user' 
  AND CONSTRAINT_NAME = 'fk_user_role');

SET @sql_fk_role = IF(
  @fk_check = 0,
  'ALTER TABLE `user` ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`role_id`) ON DELETE SET NULL',
  'SELECT "Foreign key fk_user_role already exists" AS message'
);
PREPARE stmt FROM @sql_fk_role;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @fk_staff_check = (SELECT COUNT(*) 
  FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
  WHERE TABLE_SCHEMA = 'db_production' 
  AND TABLE_NAME = 'user' 
  AND CONSTRAINT_NAME = 'fk_user_staff');

SET @sql_fk_staff = IF(
  @fk_staff_check = 0,
  'ALTER TABLE `user` ADD CONSTRAINT `fk_user_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff`(`id_staff`) ON DELETE SET NULL',
  'SELECT "Foreign key fk_user_staff already exists" AS message'
);
PREPARE stmt FROM @sql_fk_staff;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Thêm indexes
CREATE INDEX IF NOT EXISTS `idx_user_role_id` ON `user`(`role_id`);
CREATE INDEX IF NOT EXISTS `idx_user_is_active` ON `user`(`is_active`);
CREATE INDEX IF NOT EXISTS `idx_user_email` ON `user`(`email`);

-- =====================================================
-- HOÀN THÀNH MIGRATION 001
-- =====================================================
-- Kết quả:
-- ✓ Tạo bảng roles (vai trò)
-- ✓ Tạo bảng modules (nhóm chức năng)
-- ✓ Tạo bảng permissions (quyền hạn)
-- ✓ Tạo bảng role_permissions (liên kết role-permission)
-- ✓ Tạo bảng audit_log (nhật ký hoạt động)
-- ✓ Cập nhật bảng user (thêm RBAC fields)
-- =====================================================

SELECT 'Migration 001: RBAC Core Tables - COMPLETED ✓' AS status;
