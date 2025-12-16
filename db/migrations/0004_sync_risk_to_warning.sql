-- Migration: Sync legacy `risk_flag` to UC7 `warning_flag`/`warning_type`
-- This preserves historical 'risk_flag' values by populating the new columns.

-- 1) Ensure warning columns exist (safe to run multiple times)
ALTER TABLE `project` 
  ADD COLUMN IF NOT EXISTS `warning_flag` TINYINT(1) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `warning_type` VARCHAR(50) DEFAULT NULL;

-- 2) Map legacy risk_flag -> warning_flag and default warning_type
UPDATE `project` SET 
  `warning_flag` = CASE WHEN `risk_flag` = 1 THEN 1 ELSE `warning_flag` END,
  `warning_type` = CASE WHEN `risk_flag` = 1 AND (`warning_type` IS NULL OR `warning_type` = '') THEN 'deadline_too_close' ELSE `warning_type` END
WHERE `risk_flag` = 1;

-- Note: After migration, UI uses `warning_flag`/`warning_type`. Keep `risk_flag` for historical reasons until fully deprecated.
