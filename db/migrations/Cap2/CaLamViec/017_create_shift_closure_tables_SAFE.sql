-- ================================================================================
-- Migration 017: SHIFT CLOSURE - VERSION SAFE (Split FK)
-- Tách riêng: Tạo bảng trước, thêm FK sau
-- ================================================================================

-- ================================================================================
-- TẮT FOREIGN KEY CHECK - GIỮ TẮT CHO ĐẾN KHI HOÀN THÀNH TẤT CẢ
-- ================================================================================
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- ================================================================================
-- BƯỚC 1: XÓA FOREIGN KEY từ các bảng BÊN NGOÀI trỏ vào shift_closures
-- ================================================================================

-- Tìm thấy 2 bảng bên ngoài có FK trỏ vào shift_closures:
-- 1. adjustment_requests (FK: adjustment_requests_ibfk_1)
-- 2. qc_sessions (FK: qc_sessions_ibfk_1)

-- Drop FK từ adjustment_requests (nếu tồn tại)
SET @sql1 = IF(
    EXISTS(
        SELECT 1 FROM information_schema.TABLE_CONSTRAINTS 
        WHERE CONSTRAINT_NAME = 'adjustment_requests_ibfk_1' 
        AND TABLE_SCHEMA = DATABASE()
    ),
    'ALTER TABLE `adjustment_requests` DROP FOREIGN KEY `adjustment_requests_ibfk_1`',
    'SELECT "FK adjustment_requests_ibfk_1 not exists" AS Info'
);
PREPARE stmt1 FROM @sql1;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;

-- Drop FK từ qc_sessions (nếu tồn tại)
SET @sql2 = IF(
    EXISTS(
        SELECT 1 FROM information_schema.TABLE_CONSTRAINTS 
        WHERE CONSTRAINT_NAME = 'qc_sessions_ibfk_1' 
        AND TABLE_SCHEMA = DATABASE()
    ),
    'ALTER TABLE `qc_sessions` DROP FOREIGN KEY `qc_sessions_ibfk_1`',
    'SELECT "FK qc_sessions_ibfk_1 not exists" AS Info'
);
PREPARE stmt2 FROM @sql2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

-- ================================================================================
-- BƯỚC 2: DROP VIEW và TẤT CẢ các bảng
-- ================================================================================

-- Drop VIEW trước (nếu tồn tại)
DROP VIEW IF EXISTS `v_shift_closure_summary`;

-- BƯỚC 2A: DROP các bảng child (không có FK từ bảng khác trỏ vào)
DROP TABLE IF EXISTS `shift_closure_defects`;

-- BƯỚC 2B: DROP các bảng có FK trỏ vào shift_closures
-- Phải DROP trước vì chúng phụ thuộc vào shift_closures
DROP TABLE IF EXISTS `shift_closure_machines`;
DROP TABLE IF EXISTS `warehouse_import_requests`;

-- BƯỚC 2C: DROP bảng parent shift_closures (sau khi đã DROP hết FK và child)
DROP TABLE IF EXISTS `shift_closures`;

-- BƯỚC 2D: DROP bảng defect_reasons (independent)
DROP TABLE IF EXISTS `defect_reasons`;

-- Không DROP system_config vì có thể dùng chung cho nhiều module

-- ================================================================================
-- BƯỚC 3: Tạo tất cả bảng KHÔNG CÓ FK
-- ================================================================================

-- 2.1. Bảng shift_closures
CREATE TABLE `shift_closures` (
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
    UNIQUE KEY `unique_closure_code` (`closure_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.2. Bảng shift_closure_machines
CREATE TABLE `shift_closure_machines` (
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
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.3. Bảng warehouse_import_requests
CREATE TABLE `warehouse_import_requests` (
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
    UNIQUE KEY `unique_closure` (`closure_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.4. Bảng defect_reasons
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

-- Insert default defect reasons
INSERT INTO `defect_reasons` (`reason_code`, `reason_name`, `category`, `description`) VALUES
('DR001', 'Lỗi nguyên liệu đầu vào', 'material', 'Nguyên liệu không đạt chất lượng'),
('DR002', 'Lỗi máy móc', 'machine', 'Máy móc hỏng hóc'),
('DR003', 'Lỗi vận hành', 'operator', 'Nhân viên thao tác sai'),
('DR004', 'Lỗi quy trình', 'process', 'Quy trình chưa tối ưu'),
('DR005', 'Lỗi khác', 'other', 'Các lỗi khác')
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;

-- 2.5. Bảng shift_closure_defects
CREATE TABLE `shift_closure_defects` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `closure_machine_id` INT(11) NOT NULL,
    `reason_id` INT(11) NOT NULL,
    `quantity` INT(11) NOT NULL DEFAULT 0,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.6. Bảng system_config
CREATE TABLE IF NOT EXISTS `system_config` (
    `config_key` VARCHAR(100) NOT NULL,
    `config_value` TEXT NOT NULL,
    `config_type` ENUM('string', 'number', 'boolean', 'json') NOT NULL DEFAULT 'string',
    `description` TEXT NULL,
    `updated_by` INT(11) NULL,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default configs
INSERT INTO `system_config` (`config_key`, `config_value`, `config_type`, `description`) VALUES
('defect_rate_warning_threshold', '5.0', 'number', 'Ngưỡng cảnh báo tỷ lệ phế phẩm (%)'),
('defect_rate_critical_threshold', '10.0', 'number', 'Ngưỡng nghiêm trọng tỷ lệ phế phẩm (%)'),
('efficiency_warning_threshold', '70.0', 'number', 'Ngưỡng cảnh báo hiệu suất thấp (%)')
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;

-- ================================================================================
-- BƯỚC 4: Thêm INDEX
-- ================================================================================

ALTER TABLE `shift_closures`
    ADD INDEX `idx_shift` (`shift_id`),
    ADD INDEX `idx_closed_by` (`closed_by`),
    ADD INDEX `idx_status` (`status`);

ALTER TABLE `shift_closure_machines`
    ADD INDEX `idx_closure` (`closure_id`),
    ADD INDEX `idx_machine` (`machine_id`),
    ADD INDEX `idx_staff` (`staff_id`);

ALTER TABLE `warehouse_import_requests`
    ADD INDEX `idx_shift` (`shift_id`),
    ADD INDEX `idx_status` (`status`),
    ADD INDEX `idx_created_by` (`created_by`);

ALTER TABLE `shift_closure_defects`
    ADD INDEX `idx_closure_machine` (`closure_machine_id`),
    ADD INDEX `idx_reason` (`reason_id`);

-- ================================================================================
-- BƯỚC 5: Thêm FOREIGN KEY CONSTRAINTS
-- ================================================================================

-- FK cho shift_closures
ALTER TABLE `shift_closures`
    ADD CONSTRAINT `fk_shift_closure_shift` 
        FOREIGN KEY (`shift_id`) REFERENCES `production_shifts` (`shift_id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_shift_closure_user` 
        FOREIGN KEY (`closed_by`) REFERENCES `user` (`user_id`) ON DELETE RESTRICT;

-- FK cho shift_closure_machines
ALTER TABLE `shift_closure_machines`
    ADD CONSTRAINT `fk_closure_machine_closure` 
        FOREIGN KEY (`closure_id`) REFERENCES `shift_closures` (`closure_id`) ON DELETE CASCADE,
    ADD CONSTRAINT `fk_closure_machine_machine` 
        FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_closure_machine_staff` 
        FOREIGN KEY (`staff_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

-- FK cho warehouse_import_requests
ALTER TABLE `warehouse_import_requests`
    ADD CONSTRAINT `fk_warehouse_request_closure` 
        FOREIGN KEY (`closure_id`) REFERENCES `shift_closures` (`closure_id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_warehouse_request_shift` 
        FOREIGN KEY (`shift_id`) REFERENCES `production_shifts` (`shift_id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `fk_warehouse_request_creator` 
        FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE RESTRICT;

-- FK cho shift_closure_defects
ALTER TABLE `shift_closure_defects`
    ADD CONSTRAINT `fk_closure_defect_machine` 
        FOREIGN KEY (`closure_machine_id`) REFERENCES `shift_closure_machines` (`id`) ON DELETE CASCADE,
    ADD CONSTRAINT `fk_closure_defect_reason` 
        FOREIGN KEY (`reason_id`) REFERENCES `defect_reasons` (`reason_id`) ON DELETE RESTRICT;

-- ================================================================================
-- BƯỚC 6: KHÔNG TẠO LẠI FK cho các bảng bên ngoài
-- ================================================================================

-- LƯU Ý: Không tạo lại FK cho adjustment_requests và qc_sessions vì:
-- 1. Chúng thuộc module khác (không phải shift_closure)
-- 2. Kiểu dữ liệu closure_id có thể không tương thích (lỗi FK constraint)
-- 3. Nếu cần, admin có thể tạo lại FK thủ công sau với lệnh:
--    ALTER TABLE `adjustment_requests` ADD CONSTRAINT `adjustment_requests_ibfk_1` 
--        FOREIGN KEY (`closure_id`) REFERENCES `shift_closures` (`closure_id`) ON DELETE RESTRICT;
--    ALTER TABLE `qc_sessions` ADD CONSTRAINT `qc_sessions_ibfk_1` 
--        FOREIGN KEY (`closure_id`) REFERENCES `shift_closures` (`closure_id`) ON DELETE RESTRICT;

-- ================================================================================
-- BƯỚC 7: Tạo VIEW
-- ================================================================================

CREATE OR REPLACE VIEW `v_shift_closure_summary` AS
SELECT 
    sc.closure_id,
    sc.closure_code,
    sc.closure_date,
    sc.status,
    ps.shift_name,
    ps.shift_date,
    u.username AS closed_by_name,
    sc.total_target,
    sc.total_good,
    sc.total_defect,
    sc.efficiency_rate,
    sc.defect_rate,
    COUNT(scm.id) AS total_machines
FROM shift_closures sc
INNER JOIN production_shifts ps ON sc.shift_id = ps.shift_id
INNER JOIN user u ON sc.closed_by = u.user_id
LEFT JOIN shift_closure_machines scm ON sc.closure_id = scm.closure_id
GROUP BY sc.closure_id;

-- ================================================================================
-- BẬT LẠI FOREIGN KEY CHECK - HOÀN THÀNH!
-- ================================================================================
SET FOREIGN_KEY_CHECKS = 1;
