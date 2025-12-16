-- MIGRATION: Add `stock_allocation` column to `project` table
-- REASON: This column is critical to accurately track how inventory was allocated for an order (split between finished stock and production).
-- It solves the bug where reversing an order (update/delete) would incorrectly restore inventory.

ALTER TABLE `project`
ADD COLUMN `stock_allocation` JSON NULL COMMENT 'Stores details of stock allocation, e.g., {"from_stock": 10, "for_production": 90}' AFTER `warning_details`;

-- Log this action
INSERT INTO `audit_log` (`username`, `action`, `module`, `new_value`)
VALUES ('system_migration', 'add_column', 'project', '{"column": "stock_allocation", "type": "JSON", "purpose": "Track inventory allocation per order"}');

COMMIT;
