-- =====================================================
-- MIGRATION 007: Add Finished Report Data
-- Description: Thêm dữ liệu initial cho bảng finished_report
-- Author: Production Management Team
-- Date: 2025-12-14
-- =====================================================

USE `db_production`;

SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- Thêm dữ liệu vào bảng finished_report
-- =====================================================

-- Kiểm tra và thêm bản ghi với id_finished = 1 nếu chưa tồn tại
INSERT IGNORE INTO `finished_report` (`id_finished`, `id_project`, `total_finished`, `fdate`)
VALUES 
(1, 1001, 5000, NOW()),
(2, 1001, 3000, NOW()),
(3, 1002, 2500, NOW());

-- =====================================================
-- Commit transaction
-- =====================================================

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- =====================================================
-- Log
-- =====================================================
-- ✓ Đã thêm dữ liệu initial cho bảng finished_report
-- ✓ Giải quyết lỗi Foreign Key Constraint khi insert finished_receipt
