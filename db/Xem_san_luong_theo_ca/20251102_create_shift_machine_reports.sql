-- Migration: create tables for shift machine reports and events
-- Run this SQL in your database to add the reporting tables used by the Shift Report feature

-- Table: shift_machine_reports
CREATE TABLE IF NOT EXISTS `shift_machine_reports` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_shift` INT NOT NULL,
  `id_machine` INT NOT NULL,
  `produced_qty` INT DEFAULT 0,
  `target_qty` INT DEFAULT 0,
  `downtime_seconds` INT DEFAULT 0,
  `events` JSON DEFAULT NULL,
  `status` ENUM('running','paused','stopped','completed') DEFAULT 'running',
  `last_updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `recorded_by` INT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_shift_machine` (`id_shift`,`id_machine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: shift_machine_events (optional detailed events)
CREATE TABLE IF NOT EXISTS `shift_machine_events` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_shift` INT NOT NULL,
  `id_machine` INT NOT NULL,
  `event_type` VARCHAR(50) NOT NULL,
  `detail` TEXT,
  `ts` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX (`id_shift`),
  INDEX (`id_machine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Note: add foreign keys to your shifts/machines/users tables as needed.
