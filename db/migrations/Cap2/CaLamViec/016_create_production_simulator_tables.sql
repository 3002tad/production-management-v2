-- ============================================
-- Migration 016: Create Production Simulator Tables
-- ============================================
-- Description: Tạo bảng để lưu dữ liệu giả lập sản lượng và cấu hình simulator
-- Author: Copilot AI
-- Date: 2025-12-18
-- ============================================

-- ============================================
-- 1. Production Records Table
-- ============================================
-- Lưu dữ liệu sản lượng theo từng máy trong ca

CREATE TABLE IF NOT EXISTS `production_records` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `shift_id` INT NOT NULL COMMENT 'ID ca làm việc',
    `machine_id` INT NOT NULL COMMENT 'ID máy',
    `staff_id` INT NULL COMMENT 'ID nhân viên (nếu có)',
    `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời điểm ghi nhận',
    `good_count` INT NOT NULL DEFAULT 0 COMMENT 'Số lượng thành phẩm tốt',
    `defect_count` INT NOT NULL DEFAULT 0 COMMENT 'Số lượng phế phẩm',
    `target_count` INT NOT NULL DEFAULT 0 COMMENT 'Mục tiêu sản lượng',
    `downtime_minutes` INT NOT NULL DEFAULT 0 COMMENT 'Thời gian ngừng máy (phút)',
    `downtime_reason` VARCHAR(255) NULL COMMENT 'Lý do downtime',
    `efficiency_rate` DECIMAL(5,2) NULL COMMENT 'Tỷ lệ hiệu suất (%)',
    `defect_rate` DECIMAL(5,2) NULL COMMENT 'Tỷ lệ phế phẩm (%)',
    `notes` TEXT NULL COMMENT 'Ghi chú',
    `is_simulated` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Dữ liệu giả lập, 0=Dữ liệu thực',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_shift_machine` (`shift_id`, `machine_id`),
    INDEX `idx_timestamp` (`timestamp`),
    INDEX `idx_is_simulated` (`is_simulated`),
    
    CONSTRAINT `fk_production_records_shift`
        FOREIGN KEY (`shift_id`) REFERENCES `production_shifts` (`shift_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
        
    CONSTRAINT `fk_production_records_machine`
        FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
        
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bảng lưu dữ liệu sản lượng sản xuất theo máy';


-- ============================================
-- 2. Simulator Settings Table
-- ============================================
-- Lưu cấu hình cho production simulator

CREATE TABLE IF NOT EXISTS `simulator_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE COMMENT 'Khóa cấu hình',
    `setting_value` TEXT NOT NULL COMMENT 'Giá trị cấu hình (JSON hoặc text)',
    `setting_type` ENUM('boolean', 'integer', 'float', 'string', 'json') NOT NULL DEFAULT 'string' COMMENT 'Kiểu dữ liệu',
    `description` VARCHAR(255) NULL COMMENT 'Mô tả cấu hình',
    `updated_by` VARCHAR(50) NULL COMMENT 'Người cập nhật cuối',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_setting_key` (`setting_key`)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bảng cấu hình cho production simulator';


-- ============================================
-- 3. Insert Default Simulator Settings
-- ============================================

INSERT INTO `simulator_settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
    ('simulator_enabled', '0', 'boolean', 'Bật/tắt simulator (0=Off, 1=On)'),
    ('simulator_interval', '300', 'integer', 'Khoảng thời gian ghi nhận (giây)'),
    ('good_count_min', '50', 'integer', 'Số lượng thành phẩm tối thiểu mỗi lần ghi'),
    ('good_count_max', '200', 'integer', 'Số lượng thành phẩm tối đa mỗi lần ghi'),
    ('defect_rate_min', '1', 'float', 'Tỷ lệ phế phẩm tối thiểu (%)'),
    ('defect_rate_max', '8', 'float', 'Tỷ lệ phế phẩm tối đa (%)'),
    ('downtime_probability', '0.15', 'float', 'Xác suất xảy ra downtime (0-1)'),
    ('downtime_min', '5', 'integer', 'Thời gian downtime tối thiểu (phút)'),
    ('downtime_max', '30', 'integer', 'Thời gian downtime tối đa (phút)'),
    ('target_multiplier', '1.2', 'float', 'Hệ số nhân cho target (target = good_count * multiplier)'),
    ('simulate_active_shifts_only', '1', 'boolean', 'Chỉ giả lập cho ca đang chạy (1=Yes, 0=No)')
ON DUPLICATE KEY UPDATE 
    `setting_value` = VALUES(`setting_value`),
    `updated_at` = CURRENT_TIMESTAMP;


-- ============================================
-- 4. Create View for Production Summary
-- ============================================

CREATE OR REPLACE VIEW `v_production_summary` AS
SELECT 
    pr.shift_id,
    pr.machine_id,
    m.code AS machine_code,
    m.name AS machine_name,
    ps.shift_name,
    ps.shift_date,
    ps.start_time,
    ps.end_time,
    COUNT(pr.id) AS record_count,
    SUM(pr.good_count) AS total_good,
    SUM(pr.defect_count) AS total_defect,
    SUM(pr.good_count + pr.defect_count) AS total_produced,
    AVG(pr.target_count) AS avg_target,
    SUM(pr.downtime_minutes) AS total_downtime_minutes,
    AVG(pr.efficiency_rate) AS avg_efficiency,
    AVG(pr.defect_rate) AS avg_defect_rate,
    MIN(pr.timestamp) AS first_record_time,
    MAX(pr.timestamp) AS last_record_time,
    MAX(pr.is_simulated) AS has_simulated_data
FROM production_records pr
INNER JOIN machines m ON pr.machine_id = m.id
INNER JOIN production_shifts ps ON pr.shift_id = ps.shift_id
GROUP BY pr.shift_id, pr.machine_id;


-- ============================================
-- 5. Sample Downtime Reasons
-- ============================================

CREATE TABLE IF NOT EXISTS `downtime_reasons` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `reason_code` VARCHAR(20) NOT NULL UNIQUE COMMENT 'Mã lý do',
    `reason_name` VARCHAR(100) NOT NULL COMMENT 'Tên lý do',
    `category` ENUM('mechanical', 'material', 'quality', 'operator', 'other') NOT NULL DEFAULT 'other' COMMENT 'Phân loại',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Inactive',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX `idx_category` (`category`),
    INDEX `idx_is_active` (`is_active`)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bảng danh mục lý do downtime';


-- Insert sample downtime reasons
INSERT INTO `downtime_reasons` (`reason_code`, `reason_name`, `category`) VALUES
    ('MAINT', 'Bảo trì định kỳ', 'mechanical'),
    ('BREAKDOWN', 'Hỏng hóc máy móc', 'mechanical'),
    ('MATERIAL_OUT', 'Hết nguyên liệu', 'material'),
    ('MATERIAL_DEFECT', 'Nguyên liệu lỗi', 'material'),
    ('QUALITY_ISSUE', 'Vấn đề chất lượng', 'quality'),
    ('SETUP', 'Thiết lập/chuyển đổi sản phẩm', 'operator'),
    ('BREAK', 'Giờ nghỉ', 'operator'),
    ('NO_STAFF', 'Thiếu nhân lực', 'operator'),
    ('POWER_OUTAGE', 'Mất điện', 'other'),
    ('OTHER', 'Lý do khác', 'other')
ON DUPLICATE KEY UPDATE 
    `reason_name` = VALUES(`reason_name`),
    `category` = VALUES(`category`);


-- ============================================
-- 6. Verification Queries (Comment out before running)
-- ============================================

/*
-- Check tables created
SHOW TABLES LIKE '%production%' OR LIKE '%simulator%' OR LIKE '%downtime%';

-- Check production_records structure
DESCRIBE production_records;

-- Check simulator_settings
SELECT * FROM simulator_settings ORDER BY setting_key;

-- Check view exists
SHOW CREATE VIEW v_production_summary;

-- Check downtime_reasons
SELECT * FROM downtime_reasons WHERE is_active = 1;

-- Check indexes
SHOW INDEX FROM production_records;
SHOW INDEX FROM simulator_settings;
*/

-- ============================================
-- Migration Complete
-- ============================================

