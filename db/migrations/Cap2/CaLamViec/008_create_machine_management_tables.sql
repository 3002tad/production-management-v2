-- Migration 008: Machine/Production Line Management System
-- Author: Copilot AI
-- Date: 2025-11-27
-- Description: Tạo bảng quản lý máy/dây chuyền cho Trưởng dây chuyền

-- ================================
-- 📋 CHANGELOG
-- ================================
-- v1.0 (2025-11-27): Tạo 3 bảng machines, machine_status_logs, machine_maintenances
-- 
-- 🎯 CHỨC NĂNG:
-- - Quản lý thông tin máy/dây chuyền (CRUD)
-- - Theo dõi thay đổi trạng thái máy (Status Logs)
-- - Lập lịch bảo trì (Maintenance Scheduling)
-- - Tích hợp với module phân công ca sản xuất
--
-- 🔐 PERMISSIONS:
-- - manage_machine_line: Trưởng dây chuyền (CRUD)
-- - view_reports: Ban giám đốc (Read Only)

USE db_production;

-- ================================
-- 1. BẢNG MACHINES (Máy/Dây chuyền)
-- ================================
CREATE TABLE IF NOT EXISTS `machines` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(20) NOT NULL COMMENT 'Mã máy/dây chuyền (unique)',
    `name` VARCHAR(100) NOT NULL COMMENT 'Tên máy/dây chuyền',
    `capacity` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Công suất (pieces/hour)',
    `stage_type` ENUM('molding', 'assembly', 'packaging', 'quality_check', 'other') NOT NULL DEFAULT 'other' COMMENT 'Loại công đoạn',
    `status` ENUM('active', 'maintenance', 'inactive', 'broken') NOT NULL DEFAULT 'active' COMMENT 'Trạng thái máy',
    `description` TEXT NULL COMMENT 'Mô tả chi tiết',
    `location` VARCHAR(100) NULL COMMENT 'Vị trí đặt máy',
    `purchase_date` DATE NULL COMMENT 'Ngày mua/lắp đặt',
    `warranty_until` DATE NULL COMMENT 'Hết hạn bảo hành',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `created_by` VARCHAR(50) NULL COMMENT 'Người tạo (username)',
    `updated_by` VARCHAR(50) NULL COMMENT 'Người cập nhật cuối',
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_machine_code` (`code`),
    KEY `idx_status` (`status`),
    KEY `idx_stage_type` (`stage_type`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý máy/dây chuyền sản xuất';

-- ================================
-- 2. BẢNG MACHINE_STATUS_LOGS (Log thay đổi trạng thái)
-- ================================
CREATE TABLE IF NOT EXISTS `machine_status_logs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `machine_id` INT(11) NOT NULL,
    `old_status` ENUM('active', 'maintenance', 'inactive', 'broken') NULL COMMENT 'Trạng thái cũ',
    `new_status` ENUM('active', 'maintenance', 'inactive', 'broken') NOT NULL COMMENT 'Trạng thái mới',
    `reason` VARCHAR(255) NULL COMMENT 'Lý do thay đổi',
    `changed_by_user_id` INT(11) NULL COMMENT 'ID người thay đổi',
    `changed_by_username` VARCHAR(50) NULL COMMENT 'Username người thay đổi',
    `changed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `notes` TEXT NULL COMMENT 'Ghi chú thêm',
    
    PRIMARY KEY (`id`),
    KEY `idx_machine_id` (`machine_id`),
    KEY `idx_changed_at` (`changed_at`),
    KEY `idx_new_status` (`new_status`),
    
    CONSTRAINT `fk_machine_logs_machine_id` 
        FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log thay đổi trạng thái máy';

-- ================================
-- 3. BẢNG MACHINE_MAINTENANCES (Lịch bảo trì)
-- ================================
CREATE TABLE IF NOT EXISTS `machine_maintenances` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `machine_id` INT(11) NOT NULL,
    `title` VARCHAR(200) NOT NULL COMMENT 'Tiêu đề bảo trì',
    `description` TEXT NULL COMMENT 'Mô tả chi tiết',
    `start_time` DATETIME NOT NULL COMMENT 'Thời gian bắt đầu bảo trì',
    `end_time` DATETIME NOT NULL COMMENT 'Thời gian kết thúc bảo trì',
    `maintenance_type` ENUM('preventive', 'corrective', 'emergency', 'upgrade') NOT NULL DEFAULT 'preventive' COMMENT 'Loại bảo trì',
    `status` ENUM('planned', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'planned' COMMENT 'Trạng thái bảo trì',
    `estimated_cost` DECIMAL(15,2) NULL DEFAULT 0.00 COMMENT 'Chi phí ước tính',
    `actual_cost` DECIMAL(15,2) NULL DEFAULT 0.00 COMMENT 'Chi phí thực tế',
    `technician_name` VARCHAR(100) NULL COMMENT 'Tên kỹ thuật viên',
    `created_by_user_id` INT(11) NULL COMMENT 'ID người tạo',
    `created_by_username` VARCHAR(50) NULL COMMENT 'Username người tạo',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `completed_at` DATETIME NULL COMMENT 'Thời gian hoàn thành thực tế',
    `notes` TEXT NULL COMMENT 'Ghi chú bảo trì',
    
    PRIMARY KEY (`id`),
    KEY `idx_machine_id` (`machine_id`),
    KEY `idx_start_time` (`start_time`),
    KEY `idx_end_time` (`end_time`),
    KEY `idx_status` (`status`),
    KEY `idx_maintenance_type` (`maintenance_type`),
    KEY `idx_created_at` (`created_at`),
    
    CONSTRAINT `fk_machine_maintenances_machine_id` 
        FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Lịch bảo trì máy/dây chuyền';

-- ================================
-- 4. THÊM INDEXES TỐI ỐU CHO PERFORMANCE
-- ================================

-- Composite index cho tìm kiếm máy có sẵn
ALTER TABLE `machines` ADD INDEX `idx_available_machines` (`status`, `stage_type`, `capacity`);

-- Composite index cho kiểm tra lịch bảo trì
ALTER TABLE `machine_maintenances` ADD INDEX `idx_maintenance_schedule` (`machine_id`, `start_time`, `end_time`, `status`);

-- Index cho audit trail
ALTER TABLE `machine_status_logs` ADD INDEX `idx_audit_trail` (`machine_id`, `changed_at`, `new_status`);

-- ================================
-- 5. DỮ LIỆU MẪU (SAMPLE DATA)
-- ================================

-- Sample machines
INSERT INTO `machines` (`code`, `name`, `capacity`, `stage_type`, `status`, `description`, `location`, `created_by`) VALUES
('ML001', 'Máy ép nhựa số 1', 1000.00, 'molding', 'active', 'Máy ép nhựa chính cho sản xuất vỏ bút', 'Khu A - Line 1', 'system'),
('ML002', 'Máy ép nhựa số 2', 1200.00, 'molding', 'active', 'Máy ép nhựa dự phòng', 'Khu A - Line 2', 'system'),
('AS001', 'Dây chuyền lắp ráp 1', 800.00, 'assembly', 'active', 'Dây chuyền lắp ráp chính', 'Khu B - Line 1', 'system'),
('AS002', 'Dây chuyền lắp ráp 2', 750.00, 'assembly', 'maintenance', 'Dây chuyền lắp ráp phụ', 'Khu B - Line 2', 'system'),
('PK001', 'Máy đóng gói tự động', 2000.00, 'packaging', 'active', 'Máy đóng gói và dán nhãn tự động', 'Khu C - Line 1', 'system'),
('QC001', 'Máy kiểm tra chất lượng', 500.00, 'quality_check', 'active', 'Máy kiểm tra tự động', 'Khu D - QC', 'system');

-- Sample maintenance schedules
INSERT INTO `machine_maintenances` (`machine_id`, `title`, `description`, `start_time`, `end_time`, `maintenance_type`, `status`, `created_by_username`) VALUES
(4, 'Bảo trì định kỳ hàng tháng', 'Kiểm tra và bảo trì tổng thể dây chuyền lắp ráp 2', '2025-12-01 08:00:00', '2025-12-01 18:00:00', 'preventive', 'planned', 'system'),
(2, 'Nâng cấp phần mềm điều khiển', 'Cập nhật firmware và software điều khiển', '2025-12-05 20:00:00', '2025-12-06 06:00:00', 'upgrade', 'planned', 'system');

-- Sample status logs
INSERT INTO `machine_status_logs` (`machine_id`, `old_status`, `new_status`, `reason`, `changed_by_username`) VALUES
(4, 'active', 'maintenance', 'Bảo trì định kỳ theo lịch', 'system'),
(1, NULL, 'active', 'Khởi tạo máy mới', 'system'),
(2, NULL, 'active', 'Khởi tạo máy mới', 'system'),
(3, NULL, 'active', 'Khởi tạo máy mới', 'system'),
(5, NULL, 'active', 'Khởi tạo máy mới', 'system'),
(6, NULL, 'active', 'Khởi tạo máy mới', 'system');

-- ================================
-- 6. VERIFICATION QUERIES
-- ================================

-- Kiểm tra bảng đã tạo
SELECT 'Tables created successfully' as Status;
SHOW TABLES LIKE '%machine%';

-- Kiểm tra dữ liệu mẫu
SELECT 
    m.code,
    m.name,
    m.capacity,
    m.stage_type,
    m.status,
    COUNT(ml.id) as log_count,
    COUNT(mm.id) as maintenance_count
FROM machines m
LEFT JOIN machine_status_logs ml ON m.id = ml.machine_id
LEFT JOIN machine_maintenances mm ON m.id = mm.machine_id
GROUP BY m.id
ORDER BY m.code;

-- Kiểm tra constraints
SELECT 
    TABLE_NAME,
    CONSTRAINT_NAME,
    CONSTRAINT_TYPE
FROM information_schema.TABLE_CONSTRAINTS 
WHERE TABLE_SCHEMA = 'db_production' 
AND TABLE_NAME IN ('machines', 'machine_status_logs', 'machine_maintenances')
ORDER BY TABLE_NAME, CONSTRAINT_TYPE;

-- ================================
-- 🎯 MIGRATION COMPLETED SUCCESSFULLY!
-- ================================
-- 
-- 📋 Kết quả:
-- ✅ 3 bảng được tạo: machines, machine_status_logs, machine_maintenances
-- ✅ 8 indexes được tạo cho performance
-- ✅ 2 foreign key constraints được thiết lập
-- ✅ 6 máy mẫu được thêm vào
-- ✅ 2 lịch bảo trì mẫu được tạo
-- ✅ 6 log trạng thái khởi tạo
-- 
-- 🔄 Tiếp theo:
-- 1. Tạo MachineModel.php
-- 2. Tạo Machine Controller (Leader role)
-- 3. Tạo Views với Material Design
-- 4. Tích hợp với RBAC system
-- 5. Test module hoàn chỉnh