-- Migration: Update incident_reports table for zone-line-machine hierarchy
-- Simplifies incident reporting by using zone/line structure
-- Date: 2025-12-17
-- IDEMPOTENT: Safe to run multiple times

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

START TRANSACTION;

-- Step 1: Drop old foreign key constraints if they exist
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'incident_reports' 
    AND CONSTRAINT_NAME = 'fk_incident_machine');
SET @sql = IF(@fk_exists > 0, 'ALTER TABLE `incident_reports` DROP FOREIGN KEY `fk_incident_machine`', 'SELECT "FK fk_incident_machine does not exist"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Drop constraint to production_lines if exists
SET @fk_line_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'incident_reports' 
    AND CONSTRAINT_NAME = 'fk_incident_line');
SET @sql2 = IF(@fk_line_exists > 0, 'ALTER TABLE `incident_reports` DROP FOREIGN KEY `fk_incident_line`', 'SELECT "FK fk_incident_line does not exist"');
PREPARE stmt2 FROM @sql2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

-- Drop new FK if it exists (for re-run safety)
SET @fk_new_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'incident_reports' 
    AND CONSTRAINT_NAME = 'fk_incident_machine_new');
SET @sql3 = IF(@fk_new_exists > 0, 'ALTER TABLE `incident_reports` DROP FOREIGN KEY `fk_incident_machine_new`', 'SELECT "FK fk_incident_machine_new does not exist"');
PREPARE stmt3 FROM @sql3;
EXECUTE stmt3;
DEALLOCATE PREPARE stmt3;

-- Step 2: Change id_machine to allow NULL (machine is now optional)
ALTER TABLE `incident_reports` 
MODIFY COLUMN `id_machine` int(11) DEFAULT NULL COMMENT 'Máy móc cụ thể (optional - NULL nếu sự cố cả dây chuyền)';

-- Step 3: Add line_id column if it doesn't exist
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'incident_reports' 
    AND COLUMN_NAME = 'line_id');
SET @sql4 = IF(@col_exists = 0, 
    'ALTER TABLE `incident_reports` ADD COLUMN `line_id` int(11) DEFAULT NULL COMMENT "Dây chuyền liên quan (required)" AFTER `id_machine`',
    'SELECT "Column line_id already exists"');
PREPARE stmt4 FROM @sql4;
EXECUTE stmt4;
DEALLOCATE PREPARE stmt4;

-- Step 4: Add index for line_id if it doesn't exist
SET @idx_exists = (SELECT COUNT(*) FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'incident_reports' 
    AND INDEX_NAME = 'idx_line_id');
SET @sql5 = IF(@idx_exists = 0, 
    'ALTER TABLE `incident_reports` ADD KEY `idx_line_id` (`line_id`)',
    'SELECT "Index idx_line_id already exists"');
PREPARE stmt5 FROM @sql5;
EXECUTE stmt5;
DEALLOCATE PREPARE stmt5;

-- Step 5: Update existing records: Set line_id based on machine's line from NEW machines table
UPDATE `incident_reports` ir
INNER JOIN `machines` m ON ir.id_machine = m.id
SET ir.line_id = m.line_id
WHERE ir.line_id IS NULL AND ir.id_machine IS NOT NULL AND m.line_id IS NOT NULL;

-- Step 5.5: Clean up orphaned id_machine values (machines that don't exist in new machines table)
-- Set id_machine to NULL for records where machine doesn't exist
UPDATE `incident_reports` ir
LEFT JOIN `machines` m ON ir.id_machine = m.id
SET ir.id_machine = NULL
WHERE ir.id_machine IS NOT NULL AND m.id IS NULL;

-- Step 6: Add foreign key pointing to NEW machines table
ALTER TABLE `incident_reports`
ADD CONSTRAINT `fk_incident_machine_new` 
FOREIGN KEY (`id_machine`) REFERENCES `machines`(`id`) 
ON DELETE SET NULL 
ON UPDATE CASCADE;

-- Step 7: Add foreign key for line_id pointing to production_lines
ALTER TABLE `incident_reports`
ADD CONSTRAINT `fk_incident_line` 
FOREIGN KEY (`line_id`) REFERENCES `production_lines`(`id`) 
ON DELETE SET NULL 
ON UPDATE CASCADE;

-- Step 8: Mark id_planshift as deprecated
ALTER TABLE `incident_reports` 
MODIFY COLUMN `id_planshift` int(11) DEFAULT NULL COMMENT 'DEPRECATED - Use line_id instead';

-- Step 9: Update table comment
ALTER TABLE `incident_reports` 
COMMENT 'Báo cáo sự cố sản xuất - Categories: equipment (thiết bị), quality (chất lượng), safety (an toàn), other (khác)';

COMMIT;
