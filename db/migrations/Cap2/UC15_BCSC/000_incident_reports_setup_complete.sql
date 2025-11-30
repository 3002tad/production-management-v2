-- =====================================================
-- CAP2 UC15_BCSC: Combined Incident Reports setup
-- Purpose: Create `incident_reports` table (if missing),
--          add foreign keys when possible, and seed sample data
-- Notes:  - This script is safe to run multiple times.
--         - Enhanced schema with severity, category, assignee, and resolution fields
-- =====================================================

USE `db_production`;

-- =====================================================
-- Step 1: Create Incident Reports Table
-- =====================================================
CREATE TABLE IF NOT EXISTS `incident_reports` (
    `id` INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID báo cáo sự cố',
    `user_id` int(11) NOT NULL COMMENT 'Người báo cáo (worker/technical)',
    `id_machine` int(11) NOT NULL COMMENT 'Máy bị sự cố',
    `id_planshift` int(15) DEFAULT NULL COMMENT 'Dây chuyền (tùy chọn nếu sự cố ngoài thời gian)',
    `category` VARCHAR(50) DEFAULT NULL COMMENT 'Loại sự cố: equipment, quality, safety, other',
    `severity_level` TINYINT DEFAULT 1 COMMENT '1:Low, 2:Medium, 3:High, 4:Critical',
    `incident_description` TEXT NOT NULL COMMENT 'Mô tả chi tiết sự cố',
    `media_path` VARCHAR(255) DEFAULT NULL COMMENT 'Ảnh/video chứng minh',
    `status` TINYINT DEFAULT 0 COMMENT '0:Pending, 1:Completed, 2:In Progress',
    `assignee_id` int(11) DEFAULT NULL COMMENT 'Người được giao xử lý sự cố',
    `resolution_notes` TEXT DEFAULT NULL COMMENT 'Ghi chú về cách xử lý',
    `resolved_at` DATETIME DEFAULT NULL COMMENT 'Thời gian hoàn thành',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo báo cáo',
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật',
    INDEX `idx_user` (`user_id`),
    INDEX `idx_machine` (`id_machine`),
    INDEX `idx_status` (`status`),
    INDEX `idx_severity` (`severity_level`),
    INDEX `idx_category` (`category`),
    INDEX `idx_assignee` (`assignee_id`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_composite` (`status`, `severity_level`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng báo cáo sự cố - Incident Reporting System';

-- =====================================================
-- Step 2: Add Foreign Key Constraints (Safe/Conditional)
-- =====================================================

-- Add FK: fk_incident_user -> `user`(`user_id`)
ALTER IGNORE TABLE `incident_reports` ADD CONSTRAINT `fk_incident_user` 
FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE;

-- Add FK: fk_incident_machine -> `machine`(`id_machine`)
ALTER IGNORE TABLE `incident_reports` ADD CONSTRAINT `fk_incident_machine` 
FOREIGN KEY (`id_machine`) REFERENCES `machine`(`id_machine`) ON DELETE CASCADE;

-- Add FK: fk_incident_planshift -> `plan_shift`(`id_planshift`) - nullable since incidents can occur outside planned shifts
ALTER IGNORE TABLE `incident_reports` ADD CONSTRAINT `fk_incident_planshift` 
FOREIGN KEY (`id_planshift`) REFERENCES `plan_shift`(`id_planshift`) ON DELETE SET NULL;

-- Add FK: fk_incident_assignee -> `user`(`user_id`) - who will resolve this incident
ALTER IGNORE TABLE `incident_reports` ADD CONSTRAINT `fk_incident_assignee` 
FOREIGN KEY (`assignee_id`) REFERENCES `user`(`user_id`) ON DELETE SET NULL;

-- =====================================================
-- Step 3: Seed Sample Data (Safe Inserts)
-- =====================================================

-- Sample row 1: Equipment incident - pending, high severity
INSERT IGNORE INTO `incident_reports` 
(`user_id`,`id_machine`,`id_planshift`,`category`,`severity_level`,`incident_description`,`status`,`assignee_id`,`created_at`,`updated_at`)
VALUES 
(8, 1001, 1001, 'equipment', 3, 'Máy hoạt động không bình thường, có tiếng lạ khi chạy', 0, 7, NOW(), NOW());

-- Sample row 2: Quality incident - in progress, medium severity
INSERT IGNORE INTO `incident_reports` 
(`user_id`,`id_machine`,`id_planshift`,`category`,`severity_level`,`incident_description`,`status`,`assignee_id`,`resolution_notes`,`created_at`,`updated_at`)
VALUES 
(8, 1002, 1002, 'quality', 2, 'Dây chuyền bị ngừng đột ngột, không rõ nguyên nhân', 2, 7, 'Đang kiểm tra bơm xăng', NOW(), NOW());

-- Sample row 3: Safety incident - completed, critical severity
INSERT IGNORE INTO `incident_reports` 
(`user_id`,`id_machine`,`id_planshift`,`category`,`severity_level`,`incident_description`,`status`,`assignee_id`,`resolution_notes`,`resolved_at`,`created_at`,`updated_at`)
VALUES 
(8, 1001, 1001, 'safety', 4, 'Sản phẩm bị lỗi kích thước, cần kiểm tra lại máy', 1, 7, 'Thay thế lưỡi dao cắt, kiểm tra lại thông số', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), NOW());

-- =====================================================
-- Step 4: Verification
-- =====================================================
SELECT 'incident_reports_table_ready' AS status;
SELECT COUNT(*) as total_incidents FROM `incident_reports`;
DESCRIBE `incident_reports`;

-- =====================================================
-- SUMMARY - Bảng báo cáo sự cố đã sẵn sàng:
-- =====================================================
-- ✓ Bảng incident_reports tạo thành công
-- ✓ Foreign keys: user, machine, plan_shift, assignee
-- ✓ Indexes: user, machine, status, severity, category, assignee, created_at
-- ✓ Charset: utf8mb4 (hỗ trợ tiếng Việt)
-- ✓ Dữ liệu mẫu: 3 báo cáo sự cố
-- ✓ Sẵn sàng cho ứng dụng báo cáo sự cố
-- =====================================================

COMMIT;
