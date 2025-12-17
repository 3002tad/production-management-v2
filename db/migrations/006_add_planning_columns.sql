-- Migration: 006_add_planning_columns.sql
-- Add optional columns to `planning` table so the web form can save fields directly.
-- This script uses `ALTER TABLE ... ADD COLUMN IF NOT EXISTS` and `ADD INDEX IF NOT EXISTS`,
-- which require MySQL 8.0.16+ (or compatible MariaDB). If your MySQL is older, run the
-- equivalent ALTER statements manually after checking for column/index existence.

START TRANSACTION;

-- Ensure plan_name length is sufficient (will modify if exists)
ALTER TABLE `planning` 
  ADD COLUMN IF NOT EXISTS `plan_name` varchar(255) NOT NULL;
ALTER TABLE `planning` 
  MODIFY `plan_name` varchar(255) NOT NULL;

-- Dates
ALTER TABLE `planning`
  ADD COLUMN IF NOT EXISTS `end_date` DATE DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `start_date` DATE DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `finish_date` DATE DEFAULT NULL;

-- Machine allocation / suggested shifts
ALTER TABLE `planning`
  ADD COLUMN IF NOT EXISTS `machine_id` int(15) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `suggested_shifts` int(11) DEFAULT NULL;

-- Materials JSON, note and optional lines data
ALTER TABLE `planning`
  ADD COLUMN IF NOT EXISTS `materials` LONGTEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `note` LONGTEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `lines` LONGTEXT DEFAULT NULL;

-- Add index on machine_id to speed lookups
ALTER TABLE `planning`
  ADD INDEX IF NOT EXISTS `fk_planning_machine` (`machine_id`);

COMMIT;

-- If you run into errors "IF NOT EXISTS" is not supported on your server,
-- you can generate equivalent statements by checking INFORMATION_SCHEMA.COLUMNS
-- and INFORMATION_SCHEMA.STATISTICS and then running ALTER TABLE ... ADD COLUMN / ADD INDEX
-- only when needed.

-- Example command to apply this file (run from shell):
-- mysql -u root -p db_production < db/migrations/006_add_planning_columns.sql
