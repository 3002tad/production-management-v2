-- Fix migration: Correct warning_type for existing orders
-- Date: 2025-12-14
-- Purpose: Fix incorrect warning_type assignments from previous migration

USE db_production;

-- Fix orders with stock available (capacity_level_used = 0)
UPDATE project SET warning_type = 'stock_available'
WHERE capacity_level_used = 0
  AND finished_stock_available >= qty_request
  AND (finished_stock_available - qty_request) >= 1000; -- Not low stock

UPDATE project SET warning_type = 'low_stock_warning'
WHERE capacity_level_used = 0
  AND finished_stock_available >= qty_request
  AND (finished_stock_available - qty_request) < 1000; -- Low stock

-- Fix orders that need production but are feasible
UPDATE project SET warning_type = 'level_1_feasible'
WHERE capacity_level_used = 1
  AND warning_type NOT IN ('material_shortage', 'bom_missing', 'deadline_overdue', 'deadline_tight', 'capacity_overload');

UPDATE project SET warning_type = 'level_2_feasible'
WHERE capacity_level_used = 2
  AND warning_type NOT IN ('material_shortage', 'bom_missing', 'deadline_overdue', 'deadline_tight', 'capacity_overload');

-- Update warning_flag based on warning_type
UPDATE project SET warning_flag = 0 WHERE warning_type IN ('stock_available');
UPDATE project SET warning_flag = 1 WHERE warning_type NOT IN ('stock_available');

SELECT 'Migration fix completed successfully' as status;