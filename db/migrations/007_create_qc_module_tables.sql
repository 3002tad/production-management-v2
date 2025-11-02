-- =====================================================
-- Migration 007: QC Module - Quality Control & Verification
-- =====================================================
-- Purpose: Implement QC inspection system for shift closures
-- Author: AI Pair Programmer
-- Date: 2025-11-02
-- Dependencies: Requires shift_closures, users, roles tables
-- =====================================================

-- 1. CREATE shift_closures TABLE
-- Tracks end-of-shift production closures
DROP TABLE IF EXISTS `shift_closures`;
CREATE TABLE `shift_closures` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Format: SC-YYYYMMDD-LINE-SHIFT',
  `line_code` VARCHAR(20) NOT NULL COMMENT 'Production line identifier',
  `shift_code` VARCHAR(10) NOT NULL COMMENT 'Shift identifier (e.g., CA1, CA2, CA3)',
  `project_code` VARCHAR(50) NULL COMMENT 'FK to project.id_project',
  `lot_code` VARCHAR(50) NULL COMMENT 'Production lot code',
  `product_code` VARCHAR(50) NOT NULL COMMENT 'FK to product.id_product',
  `variant` VARCHAR(50) NULL COMMENT 'Product variant/specs',
  `qty_finished` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Finished goods quantity',
  `qty_waste` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Waste/defect quantity',
  `status` ENUM('PENDING_QC', 'VERIFIED', 'REJECTED') NOT NULL DEFAULT 'PENDING_QC',
  `can_receive_fg` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Flag: can warehouse receive finished goods',
  `closed_at` DATETIME NOT NULL COMMENT 'When shift was closed',
  `closed_by` VARCHAR(50) NULL COMMENT 'User code who closed the shift',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  INDEX `idx_status_line_shift` (`status`, `line_code`, `shift_code`),
  INDEX `idx_project_code` (`project_code`),
  INDEX `idx_closed_at` (`closed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Shift closure records awaiting QC verification';

-- 2. CREATE qc_sessions TABLE
-- QC inspection sessions
DROP TABLE IF EXISTS `qc_sessions`;
CREATE TABLE `qc_sessions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Format: QCS-YYYYMMDD-NNNN',
  `closure_id` INT UNSIGNED NOT NULL COMMENT 'FK to shift_closures.id',
  `inspector_code` VARCHAR(50) NOT NULL COMMENT 'QC inspector user code',
  `inspector_name` VARCHAR(100) NULL COMMENT 'Cached inspector name',
  `started_at` DATETIME NOT NULL,
  `status` ENUM('OPEN', 'DECIDED') NOT NULL DEFAULT 'OPEN',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`closure_id`) REFERENCES `shift_closures`(`id`) ON DELETE CASCADE,
  INDEX `idx_closure_id` (`closure_id`),
  INDEX `idx_inspector_status` (`inspector_code`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='QC inspection session records';

-- 3. CREATE qc_items TABLE
-- Individual checklist items and inspection results
DROP TABLE IF EXISTS `qc_items`;
CREATE TABLE `qc_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `session_id` INT UNSIGNED NOT NULL COMMENT 'FK to qc_sessions.id',
  `checklist_item_code` VARCHAR(50) NOT NULL COMMENT 'Checklist item identifier',
  `checklist_item_name` VARCHAR(200) NULL COMMENT 'Item description',
  `measure_value` DECIMAL(10,2) NULL COMMENT 'Measured value (if applicable)',
  `defect_code` VARCHAR(50) NULL COMMENT 'Defect type code if failed',
  `defect_count` INT UNSIGNED NULL DEFAULT 0 COMMENT 'Number of defects found',
  `severity` ENUM('MINOR', 'MAJOR', 'CRITICAL') NULL COMMENT 'Defect severity',
  `result` ENUM('PASS', 'FAIL') NOT NULL COMMENT 'Item inspection result',
  `note` TEXT NULL COMMENT 'Inspector notes',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`session_id`) REFERENCES `qc_sessions`(`id`) ON DELETE CASCADE,
  INDEX `idx_session_id` (`session_id`),
  INDEX `idx_result` (`result`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='QC checklist item inspection results';

-- 4. CREATE qc_decisions TABLE
-- Final QC decision (APPROVE/REJECT)
DROP TABLE IF EXISTS `qc_decisions`;
CREATE TABLE `qc_decisions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `session_id` INT UNSIGNED NOT NULL COMMENT 'FK to qc_sessions.id',
  `result` ENUM('APPROVE', 'REJECT') NOT NULL COMMENT 'Final decision',
  `aql` DECIMAL(5,2) NULL COMMENT 'Acceptance Quality Limit used',
  `defect_rate` DECIMAL(5,2) NULL COMMENT 'Calculated defect rate %',
  `reason` TEXT NULL COMMENT 'Reason for decision (required for REJECT)',
  `decided_at` DATETIME NOT NULL,
  `decided_by` VARCHAR(50) NULL COMMENT 'User code who made decision',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`session_id`) REFERENCES `qc_sessions`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `idx_session_decision` (`session_id`),
  INDEX `idx_result` (`result`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='QC final decisions';

-- 5. CREATE qc_attachments TABLE
-- Photos/videos for evidence
DROP TABLE IF EXISTS `qc_attachments`;
CREATE TABLE `qc_attachments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `session_id` INT UNSIGNED NOT NULL COMMENT 'FK to qc_sessions.id',
  `filename` VARCHAR(255) NOT NULL COMMENT 'Original filename',
  `path` VARCHAR(500) NOT NULL COMMENT 'Relative path from uploads root',
  `mime_type` VARCHAR(100) NULL COMMENT 'File MIME type',
  `file_size` INT UNSIGNED NULL COMMENT 'File size in bytes',
  `uploaded_by` VARCHAR(50) NULL COMMENT 'User code who uploaded',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`session_id`) REFERENCES `qc_sessions`(`id`) ON DELETE CASCADE,
  INDEX `idx_session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='QC inspection evidence attachments';

-- 6. CREATE adjustment_requests TABLE
-- Generated when QC rejects a closure
DROP TABLE IF EXISTS `adjustment_requests`;
CREATE TABLE `adjustment_requests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Format: AR-YYYYMMDD-NNNN',
  `closure_id` INT UNSIGNED NOT NULL COMMENT 'FK to shift_closures.id',
  `created_by` VARCHAR(50) NOT NULL COMMENT 'QC inspector who rejected',
  `assigned_to` VARCHAR(50) NULL COMMENT 'Leader/manager assigned to fix',
  `reason` TEXT NOT NULL COMMENT 'Reason for rejection',
  `status` ENUM('OPEN', 'ACKED', 'DONE') NOT NULL DEFAULT 'OPEN',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `acknowledged_at` DATETIME NULL,
  `completed_at` DATETIME NULL,
  
  FOREIGN KEY (`closure_id`) REFERENCES `shift_closures`(`id`) ON DELETE CASCADE,
  INDEX `idx_status` (`status`),
  INDEX `idx_assigned_to` (`assigned_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Adjustment requests generated from QC rejections';

-- 7. CREATE qc_checklist_master TABLE
-- Master checklist items by product
DROP TABLE IF EXISTS `qc_checklist_master`;
CREATE TABLE `qc_checklist_master` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Checklist item code',
  `product_code` VARCHAR(50) NOT NULL COMMENT 'Applicable product code',
  `variant` VARCHAR(50) NULL COMMENT 'Specific variant (NULL = all)',
  `item_name` VARCHAR(200) NOT NULL COMMENT 'Checklist item description',
  `criteria` TEXT NULL COMMENT 'Pass/fail criteria',
  `sample_size` INT UNSIGNED NULL COMMENT 'Required sample size',
  `aql` DECIMAL(5,2) NULL DEFAULT 2.5 COMMENT 'Acceptance Quality Limit %',
  `category` VARCHAR(50) NULL COMMENT 'Item category (visual, dimensional, functional)',
  `sequence` INT UNSIGNED NULL DEFAULT 0 COMMENT 'Display order',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  INDEX `idx_product_variant` (`product_code`, `variant`),
  INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='QC checklist master data';

-- 8. CREATE qc_config TABLE
-- QC configuration parameters
DROP TABLE IF EXISTS `qc_config`;
CREATE TABLE `qc_config` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `config_key` VARCHAR(100) NOT NULL UNIQUE,
  `config_value` VARCHAR(500) NULL,
  `description` TEXT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='QC configuration settings';

-- Insert default QC config
INSERT INTO `qc_config` (`config_key`, `config_value`, `description`) VALUES
('QC_AQL_DEFAULT', '2.5', 'Default Acceptance Quality Limit (%)'),
('QC_NEAR_THRESHOLD_MARGIN', '5', 'Margin for near-threshold warning (%)'),
('QC_MAX_UPLOAD_SIZE', '10485760', 'Max upload file size in bytes (10MB)'),
('QC_ALLOWED_MIME_TYPES', 'image/jpeg,image/png,image/gif,video/mp4,video/quicktime', 'Allowed attachment MIME types');

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================

-- Check tables created
SELECT TABLE_NAME, ENGINE, TABLE_ROWS, TABLE_COLLATION 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME IN (
    'shift_closures', 'qc_sessions', 'qc_items', 'qc_decisions', 
    'qc_attachments', 'adjustment_requests', 'qc_checklist_master', 'qc_config'
  )
ORDER BY TABLE_NAME;

-- Check indexes
SELECT TABLE_NAME, INDEX_NAME, COLUMN_NAME, SEQ_IN_INDEX
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('shift_closures', 'qc_sessions', 'qc_items')
ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX;

-- =====================================================
-- END OF MIGRATION 007
-- =====================================================
