-- =====================================================
-- Migrate & Update table `user`
-- Database: db_production
-- Server: MariaDB 10.4+
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

START TRANSACTION;

-- =====================================================
-- 1. ADD MISSING COLUMNS (SAFE MIGRATION)
-- =====================================================

-- temp_password
ALTER TABLE `user`
ADD COLUMN `temp_password` varchar(50) DEFAULT NULL
  COMMENT 'Mật khẩu tạm (plaintext) sau reset, NULL khi đã đổi'
AFTER `password`;

-- must_change_password
ALTER TABLE `user`
ADD COLUMN `must_change_password` tinyint(1) NOT NULL DEFAULT 0
  COMMENT '1=Bắt buộc đổi password lần đầu, 0=Bình thường'
AFTER `temp_password`;

-- =====================================================
-- 2. UPDATE USER DATA
-- =====================================================

UPDATE `user` SET
  username = 'admin',
  password = 'admin',
  temp_password = NULL,
  must_change_password = 0,
  role_id = 4,
  staff_id = 1003,
  full_name = 'Administrator',
  email = NULL,
  phone = NULL,
  is_active = 1,
  last_login = '2025-12-12 08:29:36',
  created_by = NULL,
  created_at = '2025-11-01 15:49:53',
  updated_at = '2025-12-12 08:29:36'
WHERE user_id = 1;

UPDATE `user` SET
  username = 'leader',
  password = 'leader',
  temp_password = NULL,
  must_change_password = 0,
  role_id = 2,
  staff_id = 1004,
  full_name = 'Trưởng dây chuyền',
  email = NULL,
  phone = NULL,
  is_active = 1,
  last_login = NULL,
  created_by = NULL,
  created_at = '2025-11-01 15:49:53',
  updated_at = '2025-12-11 18:28:21'
WHERE user_id = 2;

UPDATE `user` SET
  username = 'bod',
  password = 'bod123',
  temp_password = NULL,
  must_change_password = 0,
  role_id = 1,
  staff_id = 1005,
  full_name = 'Nguyễn Văn A - Giám Đốc',
  email = 'bod@company.com',
  phone = NULL,
  is_active = 1,
  last_login = '2025-12-11 19:06:48',
  created_by = NULL,
  created_at = '2025-11-01 15:53:44',
  updated_at = '2025-12-11 19:06:48'
WHERE user_id = 3;

UPDATE `user` SET
  username = 'line_manage',
  password = 'line123',
  temp_password = NULL,
  must_change_password = 0,
  role_id = 2,
  staff_id = 1006,
  full_name = 'Trần Văn B - Trưởng line 2',
  email = 'linemanager@company.com',
  phone = NULL,
  is_active = 1,
  last_login = NULL,
  created_by = NULL,
  created_at = '2025-11-01 15:53:45',
  updated_at = '2025-12-11 15:22:31'
WHERE user_id = 4;

UPDATE `user` SET
  username = 'warehouse',
  password = 'wh123',
  temp_password = NULL,
  must_change_password = 0,
  role_id = 3,
  staff_id = 1007,
  full_name = 'Lê Thị C - Nhân viên kho',
  email = 'warehouse@company.com',
  phone = NULL,
  is_active = 1,
  last_login = NULL,
  created_by = NULL,
  created_at = '2025-11-01 15:53:45',
  updated_at = '2025-12-11 15:22:31'
WHERE user_id = 5;

UPDATE `user` SET
  username = 'qc',
  password = 'qc123',
  temp_password = NULL,
  must_change_password = 0,
  role_id = 5,
  staff_id = 1008,
  full_name = 'Phạm Văn D - Nhân viên QC',
  email = 'qc@company.com',
  phone = NULL,
  is_active = 1,
  last_login = NULL,
  created_by = NULL,
  created_at = '2025-11-01 15:53:45',
  updated_at = '2025-12-11 15:22:31'
WHERE user_id = 6;

UPDATE `user` SET
  username = 'technical',
  password = 'tech123',
  temp_password = NULL,
  must_change_password = 0,
  role_id = 6,
  staff_id = 1009,
  full_name = 'Hoàng Văn E - Kỹ thuật viên',
  email = 'technical@company.com',
  phone = NULL,
  is_active = 1,
  last_login = NULL,
  created_by = NULL,
  created_at = '2025-11-01 15:53:45',
  updated_at = '2025-12-11 15:22:31'
WHERE user_id = 7;

UPDATE `user` SET
  username = 'worker',
  password = 'worker123',
  temp_password = NULL,
  must_change_password = 0,
  role_id = 7,
  staff_id = 1010,
  full_name = 'Nguyễn Thị F - Công nhân',
  email = 'worker@company.com',
  phone = NULL,
  is_active = 1,
  last_login = NULL,
  created_by = NULL,
  created_at = '2025-11-01 15:53:45',
  updated_at = '2025-12-11 15:22:31'
WHERE user_id = 8;

UPDATE `user` SET
  username = 'testuser01',
  password = 'cc03e747a6a',
  temp_password = NULL,
  must_change_password = 0,
  role_id = 7,
  staff_id = 1011,
  full_name = 'công ',
  email = 'danh12345@gmail.com',
  phone = '09378367788',
  is_active = 1,
  last_login = '2025-12-01 18:12:25',
  created_by = 1,
  created_at = '2025-12-01 16:02:29',
  updated_at = '2025-12-11 15:22:31'
WHERE user_id = 9;

UPDATE `user` SET
  username = 'danh',
  password = '123456',
  temp_password = NULL,
  must_change_password = 0,
  role_id = 1,
  staff_id = 1012,
  full_name = 'anh',
  email = 'danh@gmail.com',
  phone = '09877976688',
  is_active = 1,
  last_login = '2025-12-06 19:50:58',
  created_by = 1,
  created_at = '2025-12-03 08:55:13',
  updated_at = '2025-12-11 15:22:31'
WHERE user_id = 16;

UPDATE `user` SET
  username = 'cong',
  password = 'B1E33B69',
  temp_password = 'B1E33B69',
  must_change_password = 1,
  role_id = 1,
  staff_id = 1013,
  full_name = 'cong danh',
  email = 'danh66667@gmail.com',
  phone = '',
  is_active = 1,
  last_login = '2025-12-07 14:48:03',
  created_by = 1,
  created_at = '2025-12-07 14:38:47',
  updated_at = '2025-12-11 15:22:31'
WHERE user_id = 17;

UPDATE `user` SET
  username = 'lead',
  password = '123456',
  temp_password = NULL,
  must_change_password = 0,
  role_id = 4,
  staff_id = 1033,
  full_name = 'danh',
  email = 'danh77656@gmail.com',
  phone = '09769857',
  is_active = 1,
  last_login = '2025-12-11 18:48:57',
  created_by = 1,
  created_at = '2025-12-11 18:21:59',
  updated_at = '2025-12-11 18:57:32'
WHERE user_id = 18;

UPDATE `user` SET
  username = 'lead1',
  password = '123456',
  temp_password = NULL,
  must_change_password = 0,
  role_id = 4,
  staff_id = 1001,
  full_name = 'Leader1',
  email = 'leader1@mail.com',
  phone = '8212312',
  is_active = 1,
  last_login = '2025-12-11 19:26:40',
  created_by = 1,
  created_at = '2025-12-11 19:25:47',
  updated_at = '2025-12-11 19:28:01'
WHERE user_id = 19;

COMMIT;
