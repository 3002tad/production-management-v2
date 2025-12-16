-- Migration: Add warning_type column to project table
-- Date: 2025-12-10
-- Purpose: Replace risk_flag with warning_type for UC7 badge system

USE db_production;

-- Add warning_type column
ALTER TABLE project ADD COLUMN warning_type VARCHAR(50) DEFAULT 'level_1_feasible' AFTER warning_flag;

-- Update existing records based on current warning_flag and warning_details
UPDATE project SET warning_type = 'stock_available' WHERE warning_flag = 0 AND finished_stock_available >= qty_request;
UPDATE project SET warning_type = 'level_1_feasible' WHERE warning_flag = 1 AND capacity_level_used = 1 AND material_shifts_available > 0;
UPDATE project SET warning_type = 'level_2_feasible' WHERE warning_flag = 1 AND capacity_level_used = 2 AND material_shifts_available > 0;
UPDATE project SET warning_type = 'material_shortage' WHERE warning_flag = 1 AND material_shifts_available = 0;
UPDATE project SET warning_type = 'capacity_overload' WHERE warning_flag = 1 AND capacity_level_used = 0;

-- Set default for any remaining records
UPDATE project SET warning_type = 'level_1_feasible' WHERE warning_type IS NULL OR warning_type = '';

-- Optional: Drop old risk_flag column if it exists
-- ALTER TABLE project DROP COLUMN risk_flag;

SELECT 'Migration completed successfully' as status;