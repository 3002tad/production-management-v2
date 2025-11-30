-- =====================================================
-- CAP2 MIGRATION UC15_BCSC 002: Create Incident Reports Table
-- Description: Create incident_reports table with proper schema
-- Author: Production Management Team
-- Date: 2025-11-30
-- Module: UC15_BCSC (Worker Incident Management)
-- =====================================================

USE `db_production`;

-- =====================================================
-- Create Incident Reports Table
-- =====================================================
-- Tạo bảng báo cáo sự cố (Incident Reports)
CREATE TABLE IF NOT EXISTS `incident_reports` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `id_machine` int(11) NOT NULL,
    `id_planshift` int(15) NOT NULL,
    `incident_description` TEXT NOT NULL COMMENT 'Mô tả chi tiết sự cố',
    `media_path` VARCHAR(255) NULL COMMENT 'Đường dẫn ảnh/video (tuỳ chọn)',
    `status` TINYINT DEFAULT 0 COMMENT '0: Chưa hoàn thành, 1: Đã hoàn thành',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo báo cáo',
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật',
    FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`id_machine`) REFERENCES `machine`(`id_machine`) ON DELETE CASCADE,
    FOREIGN KEY (`id_planshift`) REFERENCES `plan_shift`(`id_planshift`) ON DELETE CASCADE,
    INDEX `idx_user` (`user_id`),
    INDEX `idx_machine` (`id_machine`),
    INDEX `idx_status` (`status`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- VERIFICATION
-- =====================================================
SELECT 'Incident Reports Table Created:' AS '';
DESCRIBE `incident_reports`;

-- =====================================================
-- SUMMARY
-- =====================================================
-- ✓ incident_reports table created
-- ✓ Foreign keys: user, machine, plan_shift
-- ✓ Indexes: user_id, id_machine, status, created_at
-- ✓ Charset: utf8mb4 (supports Vietnamese)
-- =====================================================

SELECT 'CAP2 UC15_BCSC Migration 002: Complete ✓' AS status;
