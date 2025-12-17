-- Migration: Add shift tracking to incident reports
-- Allows incidents to be linked to specific production shifts
-- Date: 2025-12-17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

START TRANSACTION;

-- Add shift_id column to incident_reports
ALTER TABLE `incident_reports` 
ADD COLUMN `shift_id` int(11) DEFAULT NULL COMMENT 'Ca làm việc khi xảy ra sự cố (optional)' AFTER `line_id`,
ADD KEY `idx_shift_id` (`shift_id`);

-- Add foreign key constraint
ALTER TABLE `incident_reports`
ADD CONSTRAINT `fk_incident_shift` 
FOREIGN KEY (`shift_id`) REFERENCES `production_shifts`(`shift_id`) 
ON DELETE SET NULL 
ON UPDATE CASCADE;

COMMIT;
