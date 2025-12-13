-- Create table for UC16 incident coordination history
CREATE TABLE IF NOT EXISTS `incident_coordination` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `incident_id` INT UNSIGNED NOT NULL,
  `leader_id` INT UNSIGNED NULL,
  `action_type` VARCHAR(50) NOT NULL,
  `assignee_id` INT UNSIGNED DEFAULT NULL,
  `machine_id` VARCHAR(50) DEFAULT NULL,
  `shift_info` VARCHAR(255) DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `status` VARCHAR(50) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`incident_id`),
  INDEX (`leader_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
