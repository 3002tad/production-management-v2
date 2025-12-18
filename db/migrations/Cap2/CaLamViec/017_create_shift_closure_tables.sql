-- Migration 017: Tạo bảng phiếu chốt ca và đề nghị nhập kho
-- Created: 2025-12-18
-- Description: Hệ thống chốt ca tự động với tổng hợp dữ liệu, cảnh báo vượt định mức, và tạo đề nghị nhập kho

-- ==================================================
-- 1. Bảng shift_closures - Phiếu chốt ca
-- ==================================================
CREATE TABLE IF NOT EXISTS `shift_closures` (
    `closure_id` INT(11) NOT NULL AUTO_INCREMENT,
    `shift_id` INT(11) NOT NULL COMMENT 'FK to production_shifts',
    `closure_code` VARCHAR(50) NOT NULL COMMENT 'Mã phiếu chốt (SC-YYYYMMDD-XXX)',
    `closure_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày giờ chốt ca',
    `closed_by` INT(11) NOT NULL COMMENT 'FK to user - Người chốt ca',
    
    -- Tổng hợp ca
    `total_target` INT(11) NOT NULL DEFAULT 0 COMMENT 'Tổng mục tiêu ca',
    `total_produced` INT(11) NOT NULL DEFAULT 0 COMMENT 'Tổng sản lượng thô (good + defect)',
    `total_good` INT(11) NOT NULL DEFAULT 0 COMMENT 'Tổng thành phẩm',
    `total_defect` INT(11) NOT NULL DEFAULT 0 COMMENT 'Tổng phế phẩm',
    `total_downtime` INT(11) NOT NULL DEFAULT 0 COMMENT 'Tổng downtime (phút)',
    
    -- Tỷ lệ tính toán
    `efficiency_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Hiệu suất = produced/target * 100',
    `defect_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Tỷ lệ lỗi = defect/produced * 100',
    
    -- Cảnh báo
    `has_warnings` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = Có cảnh báo vượt định mức',
    `warning_details` TEXT NULL COMMENT 'Chi tiết cảnh báo (JSON)',
    
    -- Ghi chú
    `notes` TEXT NULL COMMENT 'Ghi chú từ người chốt ca',
    `confirmed_quantities` TEXT NULL COMMENT 'Số lượng đã xác nhận (JSON)',
    
    -- Trạng thái
    `status` ENUM('draft', 'confirmed', 'cancelled') NOT NULL DEFAULT 'draft' COMMENT 'draft=Nháp, confirmed=Đã xác nhận, cancelled=Hủy',
    
    -- Warehouse import request
    `warehouse_request_id` INT(11) NULL COMMENT 'FK to warehouse_import_requests',
    
    -- Audit
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`closure_id`),
    UNIQUE KEY `unique_closure_code` (`closure_code`),
    KEY `idx_shift` (`shift_id`),
    KEY `idx_closed_by` (`closed_by`),
    KEY `idx_closure_date` (`closure_date`),
    KEY `idx_status` (`status`),
    KEY `idx_warehouse_request` (`warehouse_request_id`),
    
    CONSTRAINT `fk_shift_closure_shift` FOREIGN KEY (`shift_id`) REFERENCES `production_shifts` (`shift_id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_shift_closure_user` FOREIGN KEY (`closed_by`) REFERENCES `user` (`user_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Phiếu chốt ca làm việc';

-- ==================================================
-- 2. Bảng shift_closure_machines - Chi tiết chốt ca theo máy
-- ==================================================
CREATE TABLE IF NOT EXISTS `shift_closure_machines` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `closure_id` INT(11) NOT NULL COMMENT 'FK to shift_closures',
    `machine_id` INT(11) NOT NULL COMMENT 'FK to machines',
    `staff_id` INT(11) NULL COMMENT 'FK to user - Nhân viên vận hành',
    
    -- Dữ liệu máy
    `target_count` INT(11) NOT NULL DEFAULT 0 COMMENT 'Mục tiêu của máy',
    `produced_count` INT(11) NOT NULL DEFAULT 0 COMMENT 'Sản lượng thô (good + defect)',
    `good_count` INT(11) NOT NULL DEFAULT 0 COMMENT 'Thành phẩm',
    `defect_count` INT(11) NOT NULL DEFAULT 0 COMMENT 'Phế phẩm',
    `downtime_minutes` INT(11) NOT NULL DEFAULT 0 COMMENT 'Downtime (phút)',
    
    -- Tỷ lệ
    `efficiency_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Hiệu suất máy',
    `defect_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Tỷ lệ lỗi',
    
    -- Xác nhận
    `confirmed_good` INT(11) NULL COMMENT 'Số thành phẩm được xác nhận',
    `confirmed_defect` INT(11) NULL COMMENT 'Số phế phẩm được xác nhận',
    
    -- Phế phẩm chi tiết (JSON)
    `defect_details` TEXT NULL COMMENT 'Chi tiết phế phẩm theo lý do [{reason_id, reason_name, quantity}]',
    
    -- Downtime chi tiết (JSON)
    `downtime_details` TEXT NULL COMMENT 'Chi tiết downtime [{reason_id, reason, duration, category}]',
    
    -- Cảnh báo
    `is_warning` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = Máy này vượt định mức lỗi',
    
    -- Ghi chú
    `notes` TEXT NULL COMMENT 'Ghi chú riêng cho máy',
    
    -- Audit
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    KEY `idx_closure` (`closure_id`),
    KEY `idx_machine` (`machine_id`),
    KEY `idx_staff` (`staff_id`),
    
    CONSTRAINT `fk_closure_machine_closure` FOREIGN KEY (`closure_id`) REFERENCES `shift_closures` (`closure_id`) ON DELETE CASCADE,
    CONSTRAINT `fk_closure_machine_machine` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_closure_machine_staff` FOREIGN KEY (`staff_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Chi tiết chốt ca theo máy';

-- ==================================================
-- 3. Bảng warehouse_import_requests - Đề nghị nhập kho
-- ==================================================
CREATE TABLE IF NOT EXISTS `warehouse_import_requests` (
    `request_id` INT(11) NOT NULL AUTO_INCREMENT,
    `request_code` VARCHAR(50) NOT NULL COMMENT 'Mã đề nghị (WIR-YYYYMMDD-XXX)',
    `closure_id` INT(11) NOT NULL COMMENT 'FK to shift_closures',
    `shift_id` INT(11) NOT NULL COMMENT 'FK to production_shifts',
    
    -- Thông tin sản phẩm
    `product_id` INT(11) NULL COMMENT 'FK to products (nếu có bảng products)',
    `product_name` VARCHAR(255) NOT NULL COMMENT 'Tên sản phẩm',
    `product_code` VARCHAR(100) NULL COMMENT 'Mã sản phẩm',
    
    -- Số lượng
    `quantity` INT(11) NOT NULL COMMENT 'Số lượng thành phẩm đề nghị nhập kho',
    `unit` VARCHAR(50) NOT NULL DEFAULT 'cái' COMMENT 'Đơn vị tính',
    
    -- Trạng thái
    `status` ENUM('pending_qc', 'qc_approved', 'qc_rejected', 'imported', 'cancelled') NOT NULL DEFAULT 'pending_qc' COMMENT 'Trạng thái QC và nhập kho',
    
    -- QC
    `qc_by` INT(11) NULL COMMENT 'FK to user - Người QC',
    `qc_date` DATETIME NULL COMMENT 'Ngày QC',
    `qc_notes` TEXT NULL COMMENT 'Ghi chú QC',
    `qc_approved_quantity` INT(11) NULL COMMENT 'Số lượng được duyệt',
    `qc_rejected_quantity` INT(11) NULL COMMENT 'Số lượng bị từ chối',
    
    -- Nhập kho
    `imported_by` INT(11) NULL COMMENT 'FK to user - Người nhập kho',
    `imported_date` DATETIME NULL COMMENT 'Ngày nhập kho',
    `warehouse_location` VARCHAR(100) NULL COMMENT 'Vị trí kho',
    
    -- Ghi chú
    `notes` TEXT NULL COMMENT 'Ghi chú đề nghị nhập kho',
    
    -- Người tạo
    `created_by` INT(11) NOT NULL COMMENT 'FK to user - Người tạo đề nghị',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`request_id`),
    UNIQUE KEY `unique_request_code` (`request_code`),
    UNIQUE KEY `unique_closure` (`closure_id`),
    KEY `idx_shift` (`shift_id`),
    KEY `idx_status` (`status`),
    KEY `idx_created_by` (`created_by`),
    KEY `idx_qc_by` (`qc_by`),
    KEY `idx_imported_by` (`imported_by`),
    
    CONSTRAINT `fk_warehouse_request_closure` FOREIGN KEY (`closure_id`) REFERENCES `shift_closures` (`closure_id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_warehouse_request_shift` FOREIGN KEY (`shift_id`) REFERENCES `production_shifts` (`shift_id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_warehouse_request_creator` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_warehouse_request_qc` FOREIGN KEY (`qc_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
    CONSTRAINT `fk_warehouse_request_importer` FOREIGN KEY (`imported_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Đề nghị nhập kho thành phẩm';

-- ==================================================
-- 4. Bảng defect_reasons - Lý do phế phẩm (nếu chưa có)
-- ==================================================
CREATE TABLE IF NOT EXISTS `defect_reasons` (
    `reason_id` INT(11) NOT NULL AUTO_INCREMENT,
    `reason_code` VARCHAR(50) NOT NULL COMMENT 'Mã lý do (DR001, DR002...)',
    `reason_name` VARCHAR(255) NOT NULL COMMENT 'Tên lý do (Lỗi nguyên liệu, Lỗi máy móc...)',
    `category` ENUM('material', 'machine', 'operator', 'process', 'other') NOT NULL DEFAULT 'other' COMMENT 'Phân loại',
    `description` TEXT NULL COMMENT 'Mô tả chi tiết',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = Active, 0 = Inactive',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`reason_id`),
    UNIQUE KEY `unique_reason_code` (`reason_code`),
    KEY `idx_category` (`category`),
    KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Danh mục lý do phế phẩm';

-- Insert default defect reasons
INSERT INTO `defect_reasons` (`reason_code`, `reason_name`, `category`, `description`) VALUES
('DR001', 'Lỗi nguyên liệu đầu vào', 'material', 'Nguyên liệu không đạt chất lượng, có tạp chất'),
('DR002', 'Lỗi máy móc - Hỏng bộ phận', 'machine', 'Máy móc hỏng hóc, bộ phận không hoạt động tốt'),
('DR003', 'Lỗi vận hành - Sai thao tác', 'operator', 'Nhân viên thao tác sai quy trình'),
('DR004', 'Lỗi quy trình sản xuất', 'process', 'Quy trình sản xuất chưa tối ưu'),
('DR005', 'Lỗi hiệu chuẩn máy', 'machine', 'Máy chưa được hiệu chuẩn đúng'),
('DR006', 'Lỗi khuôn mẫu', 'machine', 'Khuôn mẫu bị mòn hoặc lệch'),
('DR007', 'Lỗi bề mặt sản phẩm', 'process', 'Bề mặt sản phẩm bị trầy xước, không nhẵn'),
('DR008', 'Lỗi kích thước', 'process', 'Kích thước sản phẩm không đạt tiêu chuẩn'),
('DR009', 'Lỗi màu sắc', 'material', 'Màu sắc không đồng đều hoặc không đúng'),
('DR010', 'Lỗi khác', 'other', 'Các lỗi khác không thuộc các nhóm trên')
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;

-- ==================================================
-- 5. Bảng shift_closure_defects - Phế phẩm chi tiết theo lý do
-- ==================================================
CREATE TABLE IF NOT EXISTS `shift_closure_defects` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `closure_machine_id` INT(11) NOT NULL COMMENT 'FK to shift_closure_machines',
    `reason_id` INT(11) NOT NULL COMMENT 'FK to defect_reasons',
    `quantity` INT(11) NOT NULL DEFAULT 0 COMMENT 'Số lượng phế phẩm',
    `notes` TEXT NULL COMMENT 'Ghi chú cho lý do này',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_closure_machine` (`closure_machine_id`),
    KEY `idx_reason` (`reason_id`),
    CONSTRAINT `fk_closure_defect_machine` FOREIGN KEY (`closure_machine_id`) REFERENCES `shift_closure_machines` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_closure_defect_reason` FOREIGN KEY (`reason_id`) REFERENCES `defect_reasons` (`reason_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Chi tiết phế phẩm theo lý do';

-- ==================================================
-- 6. System configuration - Định mức cảnh báo
-- ==================================================
CREATE TABLE IF NOT EXISTS `system_config` (
    `config_key` VARCHAR(100) NOT NULL,
    `config_value` TEXT NOT NULL,
    `config_type` ENUM('string', 'number', 'boolean', 'json') NOT NULL DEFAULT 'string',
    `description` TEXT NULL,
    `updated_by` INT(11) NULL,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cấu hình hệ thống';

-- Insert default configurations
INSERT INTO `system_config` (`config_key`, `config_value`, `config_type`, `description`) VALUES
('defect_rate_warning_threshold', '5.0', 'number', 'Ngưỡng cảnh báo tỷ lệ phế phẩm (%). Nếu vượt sẽ hiển thị cảnh báo khi chốt ca'),
('defect_rate_critical_threshold', '10.0', 'number', 'Ngưỡng nghiêm trọng tỷ lệ phế phẩm (%). Nếu vượt sẽ yêu cầu giải trình'),
('efficiency_warning_threshold', '70.0', 'number', 'Ngưỡng cảnh báo hiệu suất thấp (%). Dưới mức này sẽ cảnh báo'),
('closure_auto_save_interval', '300', 'number', 'Tự động lưu nháp phiếu chốt ca sau mỗi X giây'),
('warehouse_request_auto_generate', 'true', 'boolean', 'Tự động tạo đề nghị nhập kho khi chốt ca')
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;

-- ==================================================
-- 7. View - shift_closure_summary
-- ==================================================
CREATE OR REPLACE VIEW `v_shift_closure_summary` AS
SELECT 
    sc.closure_id,
    sc.closure_code,
    sc.closure_date,
    sc.status,
    ps.shift_name,
    ps.shift_date,
    ps.shift_status,
    u.username AS closed_by_name,
    sc.total_target,
    sc.total_produced,
    sc.total_good,
    sc.total_defect,
    sc.total_downtime,
    sc.efficiency_rate,
    sc.defect_rate,
    sc.has_warnings,
    wir.request_code AS warehouse_request_code,
    wir.status AS warehouse_status,
    COUNT(scm.id) AS total_machines
FROM shift_closures sc
INNER JOIN production_shifts ps ON sc.shift_id = ps.shift_id
INNER JOIN user u ON sc.closed_by = u.user_id
LEFT JOIN warehouse_import_requests wir ON sc.warehouse_request_id = wir.request_id
LEFT JOIN shift_closure_machines scm ON sc.closure_id = scm.closure_id
GROUP BY sc.closure_id;

-- ==================================================
-- 8. Update production_shifts - Add closure fields FIRST (before creating FK)
-- ==================================================
-- Check and add columns one by one
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'production_shifts' AND COLUMN_NAME = 'started_at');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `production_shifts` ADD COLUMN `started_at` DATETIME NULL COMMENT ''Thời điểm bắt đầu ca thực tế'' AFTER `shift_status`', 
    'SELECT ''Column started_at already exists''');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'production_shifts' AND COLUMN_NAME = 'started_by');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `production_shifts` ADD COLUMN `started_by` INT(11) NULL COMMENT ''Người bắt đầu ca'' AFTER `started_at`', 
    'SELECT ''Column started_by already exists''');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'production_shifts' AND COLUMN_NAME = 'ended_at');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `production_shifts` ADD COLUMN `ended_at` DATETIME NULL COMMENT ''Thời điểm kết thúc ca thực tế'' AFTER `started_by`', 
    'SELECT ''Column ended_at already exists''');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'production_shifts' AND COLUMN_NAME = 'ended_by');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `production_shifts` ADD COLUMN `ended_by` INT(11) NULL COMMENT ''Người kết thúc ca'' AFTER `ended_at`', 
    'SELECT ''Column ended_by already exists''');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'production_shifts' AND COLUMN_NAME = 'is_closed');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `production_shifts` ADD COLUMN `is_closed` TINYINT(1) NOT NULL DEFAULT 0 COMMENT ''1 = Đã chốt ca'' AFTER `ended_by`', 
    'SELECT ''Column is_closed already exists''');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ==================================================
-- 9. Add indexes for performance
-- ==================================================
-- Add index to production_shifts if not exists
SET @index_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'production_shifts' AND INDEX_NAME = 'idx_shift_status');
SET @sql = IF(@index_exists = 0, 
    'ALTER TABLE `production_shifts` ADD INDEX `idx_shift_status` (`shift_status`)', 
    'SELECT ''Index idx_shift_status already exists''');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @index_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'production_shifts' AND INDEX_NAME = 'idx_shift_date');
SET @sql = IF(@index_exists = 0, 
    'ALTER TABLE `production_shifts` ADD INDEX `idx_shift_date` (`shift_date`)', 
    'SELECT ''Index idx_shift_date already exists''');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @index_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'production_shifts' AND INDEX_NAME = 'idx_started_by');
SET @sql = IF(@index_exists = 0, 
    'ALTER TABLE `production_shifts` ADD INDEX `idx_started_by` (`started_by`)', 
    'SELECT ''Index idx_started_by already exists''');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @index_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'production_shifts' AND INDEX_NAME = 'idx_ended_by');
SET @sql = IF(@index_exists = 0, 
    'ALTER TABLE `production_shifts` ADD INDEX `idx_ended_by` (`ended_by`)', 
    'SELECT ''Index idx_ended_by already exists''');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ==================================================
-- ROLLBACK Script (Run manually if needed)
-- ==================================================
-- DROP VIEW IF EXISTS `v_shift_closure_summary`;
-- DROP TABLE IF EXISTS `shift_closure_defects`;
-- DROP TABLE IF EXISTS `warehouse_import_requests`;
-- DROP TABLE IF EXISTS `shift_closure_machines`;
-- DROP TABLE IF EXISTS `shift_closures`;
-- DROP TABLE IF EXISTS `defect_reasons`;
-- DELETE FROM `system_config` WHERE config_key LIKE '%defect%' OR config_key LIKE '%efficiency%' OR config_key LIKE '%closure%' OR config_key LIKE '%warehouse_request%';
-- ALTER TABLE `production_shifts` DROP COLUMN `started_at`, DROP COLUMN `started_by`, DROP COLUMN `ended_at`, DROP COLUMN `ended_by`, DROP COLUMN `is_closed`;

-- ==================================================
-- End of Migration 017
-- ==================================================
