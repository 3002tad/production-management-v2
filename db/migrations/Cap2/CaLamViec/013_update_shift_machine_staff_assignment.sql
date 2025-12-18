-- Migration 013: Thay đổi logic phân ca - máy
-- Máy được gán cố định vào dây chuyền, chỉ phân công nhân sự vào máy

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

START TRANSACTION;

-- Bảng mới: Phân công nhân sự vào máy cụ thể trong ca
-- Thay thế logic shift_machine_assignments (gán máy vào ca)
CREATE TABLE IF NOT EXISTS `shift_machine_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shift_id` int(11) NOT NULL COMMENT 'ID ca làm việc',
  `machine_id` int(11) NOT NULL COMMENT 'ID máy (từ bảng machines)',
  `staff_id` int(11) NOT NULL COMMENT 'ID nhân viên',
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Thời điểm phân công',
  `assigned_by` int(11) DEFAULT NULL COMMENT 'User ID người phân công',
  `status` tinyint(2) DEFAULT 1 COMMENT '1=Active, 0=Removed',
  `notes` text DEFAULT NULL COMMENT 'Ghi chú',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_shift_machine_staff` (`shift_id`, `machine_id`, `staff_id`, `status`),
  KEY `idx_shift_id` (`shift_id`),
  KEY `idx_machine_id` (`machine_id`),
  KEY `idx_staff_id` (`staff_id`),
  CONSTRAINT `fk_sms_shift` FOREIGN KEY (`shift_id`) REFERENCES `production_shifts` (`shift_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sms_machine` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sms_staff` FOREIGN KEY (`staff_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Phân công nhân sự vào máy cụ thể trong ca (máy đã cố định theo dây chuyền)';

-- Thêm field machine_type vào bảng machines nếu chưa có (phân biệt máy thường vs máy QC)
SET @dbname = DATABASE();
SET @tablename = 'machines';
SET @columnname = 'machine_type';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD ", @columnname, " VARCHAR(50) DEFAULT 'production' COMMENT 'production/quality_control/maintenance' AFTER stage_type")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Cập nhật một số máy mẫu làm máy QC (giả sử máy có stage_type = 'Quality Check')
UPDATE machines 
SET machine_type = 'quality_control' 
WHERE stage_type LIKE '%quality%' OR stage_type LIKE '%QC%' OR stage_type LIKE '%inspect%'
LIMIT 5;

-- Comment: Bảng shift_machine_assignments vẫn giữ lại để lưu lịch sử
-- nhưng không dùng nữa. Logic mới là:
-- 1. Ca được tạo với line_id
-- 2. Load tất cả máy của dây chuyền đó (từ machines WHERE line_id = ?)
-- 3. Phân công nhân sự vào từng máy (INSERT vào shift_machine_staff)
-- 4. Check role: worker → máy production, qc → máy quality_control

COMMIT;

-- Sample data test
-- Giả sử shift_id = 1, line_id = 1
-- Máy thuộc line 1: machine_id = 1, 2, 3
-- INSERT IGNORE INTO shift_machine_staff (shift_id, machine_id, staff_id, assigned_by, notes)
-- VALUES
-- (1, 1, 2, 1, 'Worker gán vào máy M001'),
-- (1, 2, 3, 1, 'Worker gán vào máy M002'),
-- (1, 3, 5, 1, 'QC gán vào máy QC001');
