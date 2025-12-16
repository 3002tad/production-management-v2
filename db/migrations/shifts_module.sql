SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

START TRANSACTION;

-- Bảng dây chuyền sản xuất
CREATE TABLE IF NOT EXISTS `production_lines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `line_code` varchar(50) NOT NULL COMMENT 'Mã dây chuyền: LINE01, LINE02...',
  `line_name` varchar(100) NOT NULL COMMENT 'Tên dây chuyền',
  `description` text DEFAULT NULL COMMENT 'Mô tả',
  `capacity_per_hour` int(11) DEFAULT 0 COMMENT 'Công suất/giờ',
  `status` tinyint(2) NOT NULL DEFAULT 1 COMMENT '1=Hoạt động, 2=Bảo trì, 3=Ngừng hoạt động',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_line_code` (`line_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng dây chuyền sản xuất';

-- Insert sample production lines (sử dụng INSERT IGNORE để tránh lỗi duplicate)
INSERT IGNORE INTO `production_lines` (`line_code`, `line_name`, `description`, `capacity_per_hour`, `status`) VALUES
('LINE01', 'Dây chuyền 1', 'Dây chuyền sản xuất chính', 100, 1),
('LINE02', 'Dây chuyền 2', 'Dây chuyền sản xuất phụ', 80, 1),
('LINE03', 'Dây chuyền 3', 'Dây chuyền lắp ráp', 60, 1);

-- Bảng ca sản xuất
CREATE TABLE IF NOT EXISTS `production_shifts` (
  `shift_id` int(11) NOT NULL AUTO_INCREMENT,
  `shift_code` varchar(50) NOT NULL COMMENT 'Mã ca: CA01, CA02...',
  `shift_name` varchar(100) NOT NULL COMMENT 'Tên ca: Ca sáng, Ca chiều...',
  `line_id` int(11) NOT NULL COMMENT 'ID dây chuyền',
  `plan_id` int(11) DEFAULT NULL COMMENT 'ID kế hoạch sản xuất',
  `shift_date` date NOT NULL COMMENT 'Ngày làm việc',
  `start_time` time NOT NULL COMMENT 'Giờ bắt đầu ca',
  `end_time` time NOT NULL COMMENT 'Giờ kết thúc ca',
  `target_quantity` int(11) DEFAULT 0 COMMENT 'Chỉ tiêu sản lượng',
  `actual_quantity` int(11) DEFAULT 0 COMMENT 'Sản lượng thực tế',
  `shift_status` tinyint(2) NOT NULL DEFAULT 1 COMMENT '1=Chưa bắt đầu, 2=Đang chạy, 3=Hoàn thành, 4=Tạm dừng',
  `staff_status` varchar(20) DEFAULT 'pending' COMMENT 'pending/sufficient/insufficient/conflict',
  `machine_status` varchar(20) DEFAULT 'unassigned' COMMENT 'unassigned/assigned/maintenance/down',
  `leader_id` int(11) DEFAULT NULL COMMENT 'Leader phụ trách ca này',
  `notes` text DEFAULT NULL COMMENT 'Ghi chú',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`shift_id`),
  UNIQUE KEY `unique_shift` (`line_id`, `shift_date`, `start_time`),
  KEY `idx_shift_date` (`shift_date`),
  KEY `idx_line_id` (`line_id`),
  KEY `idx_shift_status` (`shift_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng ca sản xuất';

-- Bảng phân công nhân sự cho ca
CREATE TABLE IF NOT EXISTS `shift_staff_assignments` (
  `assignment_id` int(11) NOT NULL AUTO_INCREMENT,
  `shift_id` int(11) NOT NULL COMMENT 'ID ca',
  `staff_id` int(11) NOT NULL COMMENT 'ID nhân viên',
  `role_in_shift` varchar(50) DEFAULT 'worker' COMMENT 'leader/worker/qc/technical',
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_by` int(11) DEFAULT NULL COMMENT 'User ID người phân công',
  `status` tinyint(2) DEFAULT 1 COMMENT '1=Active, 0=Removed',
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`assignment_id`),
  UNIQUE KEY `unique_staff_shift` (`shift_id`, `staff_id`, `status`),
  KEY `idx_shift_id` (`shift_id`),
  KEY `idx_staff_id` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Phân công nhân sự cho ca';

-- Bảng gán máy cho ca
CREATE TABLE IF NOT EXISTS `shift_machine_assignments` (
  `machine_assignment_id` int(11) NOT NULL AUTO_INCREMENT,
  `shift_id` int(11) NOT NULL COMMENT 'ID ca',
  `machine_id` int(11) NOT NULL COMMENT 'ID máy',
  `start_at` datetime NOT NULL COMMENT 'Thời điểm bắt đầu sử dụng máy',
  `end_at` datetime DEFAULT NULL COMMENT 'Thời điểm kết thúc (NULL = đang chạy)',
  `assignment_status` varchar(20) DEFAULT 'active' COMMENT 'active/completed/breakdown',
  `breakdown_reason` text DEFAULT NULL COMMENT 'Lý do breakdown nếu có',
  `assigned_by` int(11) DEFAULT NULL COMMENT 'User ID người gán máy',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`machine_assignment_id`),
  KEY `idx_shift_id` (`shift_id`),
  KEY `idx_machine_id` (`machine_id`),
  KEY `idx_status` (`assignment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Gán máy cho ca sản xuất';

-- Bảng nhật ký thay đổi máy (breakdown log)
CREATE TABLE IF NOT EXISTS `machine_breakdown_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `shift_id` int(11) NOT NULL,
  `old_machine_id` int(11) NOT NULL COMMENT 'Máy cũ bị breakdown',
  `new_machine_id` int(11) NOT NULL COMMENT 'Máy mới thay thế',
  `breakdown_time` datetime NOT NULL COMMENT 'Thời điểm breakdown',
  `reason` text NOT NULL COMMENT 'Lý do thay máy',
  `downtime_minutes` int(11) DEFAULT 0 COMMENT 'Thời gian chết (phút)',
  `handled_by` int(11) DEFAULT NULL COMMENT 'User ID người xử lý',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `idx_shift_id` (`shift_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Nhật ký thay đổi máy do breakdown';

-- Sample data cho testing (sử dụng INSERT IGNORE để tránh lỗi duplicate)
INSERT IGNORE INTO `production_shifts` 
(`shift_code`, `shift_name`, `line_id`, `shift_date`, `start_time`, `end_time`, `target_quantity`, `shift_status`, `staff_status`, `machine_status`, `notes`, `created_by`)
VALUES
('CA-S-001', 'Ca Sáng - Dây chuyền 1', 1, CURDATE(), '07:00:00', '15:00:00', 500, 1, 'pending', 'unassigned', 'Ca sáng hôm nay', 1),
('CA-C-001', 'Ca Chiều - Dây chuyền 1', 1, CURDATE(), '15:00:00', '23:00:00', 500, 1, 'pending', 'unassigned', 'Ca chiều hôm nay', 1),
('CA-S-002', 'Ca Sáng - Dây chuyền 2', 2, CURDATE(), '07:00:00', '15:00:00', 800, 1, 'pending', 'unassigned', NULL, 1);

COMMIT;
