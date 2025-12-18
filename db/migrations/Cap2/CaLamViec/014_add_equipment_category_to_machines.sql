-- Migration 014: Add equipment_category field to machines table
-- Purpose: Replace machine_type with equipment_category for better categorization
-- Author: GitHub Copilot
-- Date: 2025-12-18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

START TRANSACTION;

-- Check if equipment_category column already exists
SET @column_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'machines' 
    AND COLUMN_NAME = 'equipment_category'
);

-- Add equipment_category column if it doesn't exist
SET @sql_add_column = IF(
    @column_exists = 0,
    'ALTER TABLE `machines` ADD COLUMN `equipment_category` VARCHAR(50) DEFAULT ''production'' COMMENT ''production, quality_control, maintenance'' AFTER `machine_type`',
    'SELECT ''Column equipment_category already exists'' AS message'
);

PREPARE stmt FROM @sql_add_column;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update existing machines: Set quality_control for QC machines
UPDATE `machines` 
SET `equipment_category` = 'quality_control' 
WHERE `stage_type` LIKE '%quality%' 
   OR `stage_type` LIKE '%QC%'
   OR `name` LIKE '%kiểm%'
   OR `name` LIKE '%QC%';

-- Add index for faster queries
SET @index_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'machines' 
    AND INDEX_NAME = 'idx_equipment_category'
);

SET @sql_add_index = IF(
    @index_exists = 0,
    'ALTER TABLE `machines` ADD INDEX `idx_equipment_category` (`equipment_category`)',
    'SELECT ''Index idx_equipment_category already exists'' AS message'
);

PREPARE stmt FROM @sql_add_index;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;

-- Verification queries (comment out in production)
-- SELECT equipment_category, COUNT(*) as count FROM machines GROUP BY equipment_category;
-- DESCRIBE machines;
