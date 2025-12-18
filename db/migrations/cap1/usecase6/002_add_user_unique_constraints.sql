-- Add unique indexes to enforce constraints for UC6
-- Creates unique index on username and (unique) index on staff_id to avoid duplicate user assignments
-- WARNING: If there are existing duplicate values this script may fail. Review data before running.

-- Add unique index on username if not exists
SET @cnt := (SELECT COUNT(1) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'user' AND index_name = 'ux_user_username');
SET @sql := IF(@cnt = 0, 'ALTER TABLE `user` ADD UNIQUE INDEX `ux_user_username` (`username`)', 'SELECT "ux_user_username already exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add unique index on staff_id if not exists (allows multiple NULLs)
SET @cnt := (SELECT COUNT(1) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'user' AND index_name = 'ux_user_staff_id');
SET @sql := IF(@cnt = 0, 'ALTER TABLE `user` ADD UNIQUE INDEX `ux_user_staff_id` (`staff_id`)', 'SELECT "ux_user_staff_id already exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- End of migration
