-- =====================================================
-- MIGRATION 002: Seed Roles Data
-- Description: Insert 7 vai trò chính vào hệ thống
-- Author: Production Management Team
-- Date: 2025-11-01
-- =====================================================

USE `db_production`;

-- =====================================================
-- INSERT ROLES DATA
-- =====================================================

INSERT INTO `roles` (`role_id`, `role_name`, `role_display_name`, `description`, `level`, `is_active`) VALUES
(1, 'bod', 'Ban Giám Đốc', 'Quản trị cấp cao, phê duyệt chiến lược, xem báo cáo tổng hợp. Quản lý khách hàng, sản phẩm, đơn hàng, phê duyệt kế hoạch sản xuất.', 100, 1),

(2, 'line_manager', 'Trưởng dây chuyền', 'Vận hành line sản xuất, phân ca, điều phối nhân sự và máy móc, chốt ca. Quản lý nhân sự line, lập kế hoạch, theo dõi sản lượng, xử lý sự cố.', 70, 1),

(3, 'warehouse_staff', 'Nhân viên Kho', 'Quản lý nguyên vật liệu và thành phẩm, xử lý chứng từ kho. Nhập/xuất kho NVL, nhập/xuất kho thành phẩm, xem báo cáo tồn kho.', 50, 1),

(4, 'system_admin', 'Quản trị viên Hệ thống', 'Quản lý tài khoản người dùng và phân quyền. Tạo/sửa/khóa user, gán role và permissions, theo dõi nhật ký truy cập, cài đặt hệ thống.', 90, 1),

(5, 'qc_staff', 'Nhân viên Kiểm soát Chất lượng', 'Kiểm soát chất lượng sản phẩm sau mỗi ca sản xuất. Kiểm tra theo checklist/AQL, phê duyệt/reject, ghi nhận lỗi, xác nhận cho nhập kho thành phẩm.', 60, 1),

(6, 'technical_staff', 'Nhân viên Kỹ thuật', 'Bảo trì máy móc và xử lý sự cố kỹ thuật. Tiếp nhận sự cố, cập nhật tình trạng máy, xác nhận máy Ready, lập lịch bảo trì, xác nhận định mức NVL.', 60, 1),

(7, 'worker', 'Công nhân Sản xuất', 'Thực hiện ca sản xuất và phản hồi hiện trường. Xem lịch ca, xác nhận nhận việc, báo cáo sự cố, xem sản lượng của mình.', 10, 1)

ON DUPLICATE KEY UPDATE
  `role_display_name` = VALUES(`role_display_name`),
  `description` = VALUES(`description`),
  `level` = VALUES(`level`),
  `is_active` = VALUES(`is_active`),
  `updated_at` = CURRENT_TIMESTAMP;

-- =====================================================
-- UPDATE EXISTING USERS (Migration từ role cũ sang role mới)
-- =====================================================

-- Update admin user → system_admin role
UPDATE `user` 
SET `role_id` = 4,
    `full_name` = 'Administrator',
    `is_active` = 1
WHERE `role` = 'admin' AND `role_id` IS NULL;

-- Update leader user → line_manager role
UPDATE `user` 
SET `role_id` = 2,
    `full_name` = 'Trưởng dây chuyền',
    `is_active` = 1
WHERE `role` = 'leader' AND `role_id` IS NULL;

-- =====================================================
-- CREATE SAMPLE USERS (cho testing)
-- =====================================================

-- BOD user (nếu chưa tồn tại)
INSERT INTO `user` (`username`, `password`, `role_id`, `full_name`, `email`, `is_active`)
SELECT 'bod', 'bod123', 1, 'Nguyễn Văn A - Giám Đốc', 'bod@company.com', 1
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE `username` = 'bod');

-- Line Manager user (nếu chưa tồn tại, ngoài leader đã có)
INSERT INTO `user` (`username`, `password`, `role_id`, `full_name`, `email`, `is_active`)
SELECT 'line_manager', 'line123', 2, 'Trần Văn B - Trưởng line 2', 'linemanager@company.com', 1
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE `username` = 'line_manager');

-- Warehouse Staff user
INSERT INTO `user` (`username`, `password`, `role_id`, `full_name`, `email`, `is_active`)
SELECT 'warehouse', 'wh123', 3, 'Lê Thị C - Nhân viên kho', 'warehouse@company.com', 1
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE `username` = 'warehouse');

-- QC Staff user
INSERT INTO `user` (`username`, `password`, `role_id`, `full_name`, `email`, `is_active`)
SELECT 'qc', 'qc123', 5, 'Phạm Văn D - Nhân viên QC', 'qc@company.com', 1
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE `username` = 'qc');

-- Technical Staff user
INSERT INTO `user` (`username`, `password`, `role_id`, `full_name`, `email`, `is_active`)
SELECT 'technical', 'tech123', 6, 'Hoàng Văn E - Kỹ thuật viên', 'technical@company.com', 1
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE `username` = 'technical');

-- Worker user
INSERT INTO `user` (`username`, `password`, `role_id`, `full_name`, `email`, `is_active`)
SELECT 'worker', 'worker123', 7, 'Nguyễn Thị F - Công nhân', 'worker@company.com', 1
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE `username` = 'worker');

-- =====================================================
-- VERIFICATION
-- =====================================================

-- Hiển thị kết quả
SELECT 
  role_id,
  role_name,
  role_display_name,
  level,
  is_active,
  (SELECT COUNT(*) FROM `user` WHERE `user`.`role_id` = `roles`.`role_id`) AS total_users
FROM `roles`
ORDER BY `level` DESC;

-- =====================================================
-- HOÀN THÀNH MIGRATION 002
-- =====================================================
-- Kết quả:
-- ✓ Tạo 7 roles: BOD, Line Manager, Warehouse, Admin, QC, Technical, Worker
-- ✓ Update users cũ sang role mới
-- ✓ Tạo sample users cho testing
-- =====================================================

SELECT 'Migration 002: Seed Roles Data - COMPLETED ✓' AS status;
