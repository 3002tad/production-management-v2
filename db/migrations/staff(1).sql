SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

START TRANSACTION;

ALTER TABLE `staff`
ADD COLUMN `department` varchar(100) DEFAULT NULL AFTER `email`,
ADD COLUMN `position` varchar(100) DEFAULT NULL AFTER `department`,
ADD COLUMN `created_at` timestamp NOT NULL DEFAULT current_timestamp() AFTER `st_status`,
ADD COLUMN `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
  ON UPDATE current_timestamp() AFTER `created_at`;

UPDATE `staff` SET
  staff_name = 'Leader1',
  phone = '8212312',
  email = 'leader1@mail.com',
  department = 'Sản Xuất',
  position = 'Leader',
  st_status = 2,
  created_at = '2025-12-11 22:09:49',
  updated_at = '2025-12-12 15:21:14'
WHERE id_staff = 1001;

UPDATE `staff` SET
  staff_name = 'Leader2',
  phone = '8923321',
  email = 'leader2@mail.com',
  department = 'Sản Xuất',
  position = 'Leader',
  st_status = 1,
  created_at = '2025-12-11 22:09:49',
  updated_at = '2025-12-12 15:21:14'
WHERE id_staff = 1002;

INSERT INTO `staff`
(id_staff, staff_name, phone, email, department, position, st_status, created_at, updated_at)
VALUES
(1003, 'Administrator', '0', '', 'IT', 'Administrator', 1, '2025-11-01 15:49:53', '2025-12-12 08:21:14'),
(1004, 'Trưởng dây chuyền', '0', '', 'Sản Xuất', 'Trưởng Dây Chuyền', 1, '2025-11-01 15:49:53', '2025-12-12 08:21:14'),
(1005, 'Nguyễn Văn A - Giám Đốc', '0', 'bod@company.com', 'Ban Giám Đốc', 'Giám Đốc', 1, '2025-11-01 15:53:44', '2025-12-12 08:21:14'),
(1006, 'Trần Văn B - Trưởng line 2', '0', 'linemanager@company.com', 'Sản Xuất', 'Trưởng Dây Chuyền', 1, '2025-11-01 15:53:45', '2025-12-12 08:21:14'),
(1007, 'Lê Thị C - Nhân viên kho', '0', 'warehouse@company.com', 'Kho', 'Nhân Viên Kho', 1, '2025-11-01 15:53:45', '2025-12-12 08:21:14'),
(1008, 'Phạm Văn D - Nhân viên QC', '0', 'qc@company.com', 'QC', 'Nhân Viên QC', 1, '2025-11-01 15:53:45', '2025-12-12 08:21:14'),
(1009, 'Hoàng Văn E - Kỹ thuật viên', '0', 'technical@company.com', 'Kỹ Thuật', 'Kỹ Thuật Viên', 1, '2025-11-01 15:53:45', '2025-12-12 08:21:14'),
(1010, 'Nguyễn Thị F - Công nhân', '0', 'worker@company.com', 'Sản Xuất', 'Công Nhân', 1, '2025-11-01 15:53:45', '2025-12-12 08:21:14'),
(1011, 'công ', '2147483647', 'danh12345@gmail.com', 'Chưa Phân Loại', 'Chưa Phân Loại', 1, '2025-12-01 16:02:29', '2025-12-12 08:21:14'),
(1012, 'anh', '2147483647', 'danh@gmail.com', 'Chưa Phân Loại', 'Chưa Phân Loại', 1, '2025-12-03 08:55:13', '2025-12-12 08:21:14'),
(1013, 'cong danh', '0', 'danh66667@gmail.com', 'Chưa Phân Loại', 'Chưa Phân Loại', 1, '2025-12-07 14:38:47', '2025-12-12 08:21:14'),
(1033, 'danh', '09769857', 'danh77656@gmail.com', 'Chưa Phân Loại', 'Chưa Phân Loại', 1, '2025-12-03 08:45:13', '2025-12-12 08:21:14');

COMMIT;