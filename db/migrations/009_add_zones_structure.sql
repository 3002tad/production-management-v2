SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

START TRANSACTION;

-- Bảng khu vực (zones)
CREATE TABLE IF NOT EXISTS `zones` (
  `zone_id` int(11) NOT NULL AUTO_INCREMENT,
  `zone_code` varchar(50) NOT NULL COMMENT 'Mã khu: ZONE_A, ZONE_B...',
  `zone_name` varchar(100) NOT NULL COMMENT 'Tên khu vực: Khu A, Khu B...',
  `description` text DEFAULT NULL COMMENT 'Mô tả khu vực',
  `floor` varchar(50) DEFAULT NULL COMMENT 'Tầng',
  `building` varchar(50) DEFAULT NULL COMMENT 'Tòa nhà',
  `status` tinyint(2) NOT NULL DEFAULT 1 COMMENT '1=Hoạt động, 0=Ngừng hoạt động',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`zone_id`),
  UNIQUE KEY `unique_zone_code` (`zone_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng khu vực sản xuất';

-- Thêm zone_id vào production_lines
ALTER TABLE `production_lines` 
ADD COLUMN `zone_id` int(11) DEFAULT NULL AFTER `id`,
ADD KEY `idx_zone_id` (`zone_id`);

-- Thêm line_id vào machines (nếu chưa có)
ALTER TABLE `machines` 
ADD COLUMN `line_id` int(11) DEFAULT NULL AFTER `id`,
ADD KEY `idx_line_id` (`line_id`);

-- Insert sample zones
INSERT IGNORE INTO `zones` (`zone_code`, `zone_name`, `description`, `floor`, `building`, `status`) VALUES
('ZONE_A', 'Khu A', 'Khu sản xuất chính', 'Tầng 1', 'Nhà máy A', 1),
('ZONE_B', 'Khu B', 'Khu lắp ráp', 'Tầng 1', 'Nhà máy A', 1),
('ZONE_C', 'Khu C', 'Khu đóng gói', 'Tầng 2', 'Nhà máy A', 1);

-- Update production_lines với zone_id
UPDATE `production_lines` SET `zone_id` = 1 WHERE `id` = 1;
UPDATE `production_lines` SET `zone_id` = 1 WHERE `id` = 2;
UPDATE `production_lines` SET `zone_id` = 2 WHERE `id` = 3;

-- Update machines với line_id (từ location hoặc mặc định)
UPDATE `machines` SET `line_id` = 1 WHERE `id` IN (SELECT id FROM (SELECT id FROM machines LIMIT 3) AS temp);
UPDATE `machines` SET `line_id` = 2 WHERE `id` IN (SELECT id FROM (SELECT id FROM machines LIMIT 3, 3) AS temp);

COMMIT;
