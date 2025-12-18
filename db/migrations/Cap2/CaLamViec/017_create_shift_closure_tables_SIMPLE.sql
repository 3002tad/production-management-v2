-- Migration 017 - SIMPLIFIED VERSION
-- Tạo bảng shift closure với FK được thêm sau

-- ==================================================
-- STEP 1: Tạo các bảng KHÔNG CÓ FK
-- ==================================================

-- 1. Bảng shift_closures (chỉ có FK đến production_shifts và user - 2 bảng đã tồn tại)
CREATE TABLE IF NOT EXISTS `shift_closures` (
    `closure_id` INT(11) NOT NULL AUTO_INCREMENT,
    `shift_id` INT(11) NOT NULL,
    `closure_code` VARCHAR(50) NOT NULL,
    `closure_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `closed_by` INT(11) NOT NULL,
    `total_target` INT(11) NOT NULL DEFAULT 0,
    `total_produced` INT(11) NOT NULL DEFAULT 0,
    `total_good` INT(11) NOT NULL DEFAULT 0,
    `total_defect` INT(11) NOT NULL DEFAULT 0,
    `total_downtime` INT(11) NOT NULL DEFAULT 0,
    `efficiency_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `defect_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `has_warnings` TINYINT(1) NOT NULL DEFAULT 0,
    `warning_details` TEXT NULL,
    `notes` TEXT NULL,
    `confirmed_quantities` TEXT NULL,
    `status` ENUM('draft', 'confirmed', 'cancelled') NOT NULL DEFAULT 'draft',
    `warehouse_request_id` INT(11) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`closure_id`),
    UNIQUE KEY `unique_closure_code` (`closure_code`),
    KEY `idx_shift` (`shift_id`),
    KEY `idx_closed_by` (`closed_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Bảng shift_closure_machines (BỎ FK tạm thời)
CREATE TABLE IF NOT EXISTS `shift_closure_machines` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `closure_id` INT(11) NOT NULL,
    `machine_id` INT(11) NOT NULL,
    `staff_id` INT(11) NULL,
    `target_count` INT(11) NOT NULL DEFAULT 0,
    `produced_count` INT(11) NOT NULL DEFAULT 0,
    `good_count` INT(11) NOT NULL DEFAULT 0,
    `defect_count` INT(11) NOT NULL DEFAULT 0,
    `downtime_minutes` INT(11) NOT NULL DEFAULT 0,
    `efficiency_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `defect_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `confirmed_good` INT(11) NULL,
    `confirmed_defect` INT(11) NULL,
    `defect_details` TEXT NULL,
    `downtime_details` TEXT NULL,
    `is_warning` TINYINT(1) NOT NULL DEFAULT 0,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_closure` (`closure_id`),
    KEY `idx_machine` (`machine_id`),
    KEY `idx_staff` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bảng warehouse_import_requests (BỎ FK tạm thời)
CREATE TABLE IF NOT EXISTS `warehouse_import_requests` (
    `request_id` INT(11) NOT NULL AUTO_INCREMENT,
    `request_code` VARCHAR(50) NOT NULL,
    `closure_id` INT(11) NOT NULL,
    `shift_id` INT(11) NOT NULL,
    `product_id` INT(11) NULL,
    `product_name` VARCHAR(255) NOT NULL,
    `product_code` VARCHAR(100) NULL,
    `quantity` INT(11) NOT NULL,
    `unit` VARCHAR(50) NOT NULL DEFAULT 'cái',
    `status` ENUM('pending_qc', 'qc_approved', 'qc_rejected', 'imported', 'cancelled') NOT NULL DEFAULT 'pending_qc',
    `qc_by` INT(11) NULL,
    `qc_date` DATETIME NULL,
    `qc_notes` TEXT NULL,
    `qc_approved_quantity` INT(11) NULL,
    `qc_rejected_quantity` INT(11) NULL,
    `imported_by` INT(11) NULL,
    `imported_date` DATETIME NULL,
    `warehouse_location` VARCHAR(100) NULL,
    `notes` TEXT NULL,
    `created_by` INT(11) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`request_id`),
    UNIQUE KEY `unique_request_code` (`request_code`),
    UNIQUE KEY `unique_closure` (`closure_id`),
    KEY `idx_shift` (`shift_id`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bảng defect_reasons
CREATE TABLE IF NOT EXISTS `defect_reasons` (
    `reason_id` INT(11) NOT NULL AUTO_INCREMENT,
    `reason_code` VARCHAR(50) NOT NULL,
    `reason_name` VARCHAR(255) NOT NULL,
    `category` ENUM('material', 'machine', 'operator', 'process', 'other') NOT NULL DEFAULT 'other',
    `description` TEXT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`reason_id`),
    UNIQUE KEY `unique_reason_code` (`reason_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `defect_reasons` (`reason_code`, `reason_name`, `category`, `description`) VALUES
('DR001', 'Lỗi nguyên liệu đầu vào', 'material', 'Nguyên liệu không đạt chất lượng'),
('DR002', 'Lỗi máy móc', 'machine', 'Máy móc hỏng hóc'),
('DR003', 'Lỗi vận hành', 'operator', 'Nhân viên thao tác sai'),
('DR004', 'Lỗi quy trình', 'process', 'Quy trình chưa tối ưu'),
('DR010', 'Lỗi khác', 'other', 'Các lỗi khác')
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;

-- 5. Bảng shift_closure_defects (BỎ FK tạm thời)
CREATE TABLE IF NOT EXISTS `shift_closure_defects` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `closure_machine_id` INT(11) NOT NULL,
    `reason_id` INT(11) NOT NULL,
    `quantity` INT(11) NOT NULL DEFAULT 0,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_closure_machine` (`closure_machine_id`),
    KEY `idx_reason` (`reason_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Bảng system_config
CREATE TABLE IF NOT EXISTS `system_config` (
    `config_key` VARCHAR(100) NOT NULL,
    `config_value` TEXT NOT NULL,
    `config_type` ENUM('string', 'number', 'boolean', 'json') NOT NULL DEFAULT 'string',
    `description` TEXT NULL,
    `updated_by` INT(11) NULL,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `system_config` (`config_key`, `config_value`, `config_type`, `description`) VALUES
('defect_rate_warning_threshold', '5.0', 'number', 'Ngưỡng cảnh báo phế phẩm'),
('defect_rate_critical_threshold', '10.0', 'number', 'Ngưỡng nghiêm trọng phế phẩm'),
('efficiency_warning_threshold', '70.0', 'number', 'Ngưỡng cảnh báo hiệu suất thấp')
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;

-- ==================================================
-- STEP 2: Thêm cột vào production_shifts
-- ==================================================
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'production_shifts' AND COLUMN_NAME = 'started_at');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `production_shifts` ADD COLUMN `started_at` DATETIME NULL AFTER `shift_status`', 
    'SELECT ''started_at exists''');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'production_shifts' AND COLUMN_NAME = 'started_by');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `production_shifts` ADD COLUMN `started_by` INT(11) NULL AFTER `started_at`', 
    'SELECT ''started_by exists''');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'production_shifts' AND COLUMN_NAME = 'ended_at');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `production_shifts` ADD COLUMN `ended_at` DATETIME NULL AFTER `started_by`', 
    'SELECT ''ended_at exists''');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'production_shifts' AND COLUMN_NAME = 'ended_by');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `production_shifts` ADD COLUMN `ended_by` INT(11) NULL AFTER `ended_at`', 
    'SELECT ''ended_by exists''');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'production_shifts' AND COLUMN_NAME = 'is_closed');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `production_shifts` ADD COLUMN `is_closed` TINYINT(1) NOT NULL DEFAULT 0 AFTER `ended_by`', 
    'SELECT ''is_closed exists''');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ==================================================
-- STEP 3: Thêm FK sau khi tất cả bảng đã tồn tại
-- ==================================================

-- FK cho shift_closures
ALTER TABLE `shift_closures`
    ADD CONSTRAINT `fk_shift_closure_shift` FOREIGN KEY (`shift_id`) REFERENCES `production_shifts` (`shift_id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_shift_closure_user` FOREIGN KEY (`closed_by`) REFERENCES `user` (`user_id`) ON DELETE RESTRICT;

-- FK cho shift_closure_machines
ALTER TABLE `shift_closure_machines`
    ADD CONSTRAINT `fk_closure_machine_closure` FOREIGN KEY (`closure_id`) REFERENCES `shift_closures` (`closure_id`) ON DELETE CASCADE,
    ADD CONSTRAINT `fk_closure_machine_machine` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_closure_machine_staff` FOREIGN KEY (`staff_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

-- FK cho warehouse_import_requests
ALTER TABLE `warehouse_import_requests`
    ADD CONSTRAINT `fk_warehouse_request_closure` FOREIGN KEY (`closure_id`) REFERENCES `shift_closures` (`closure_id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_warehouse_request_shift` FOREIGN KEY (`shift_id`) REFERENCES `production_shifts` (`shift_id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_warehouse_request_creator` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_warehouse_request_qc` FOREIGN KEY (`qc_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
    ADD CONSTRAINT `fk_warehouse_request_importer` FOREIGN KEY (`imported_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

-- FK cho shift_closure_defects
ALTER TABLE `shift_closure_defects`
    ADD CONSTRAINT `fk_closure_defect_machine` FOREIGN KEY (`closure_machine_id`) REFERENCES `shift_closure_machines` (`id`) ON DELETE CASCADE,
    ADD CONSTRAINT `fk_closure_defect_reason` FOREIGN KEY (`reason_id`) REFERENCES `defect_reasons` (`reason_id`) ON DELETE RESTRICT;

-- ==================================================
-- Success Message
-- ==================================================
SELECT 'Migration 017 completed successfully!' AS status;
