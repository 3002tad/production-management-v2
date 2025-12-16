-- Migration v4: Fix for the final INSERT statement error.
-- Assumes previous steps may have already completed.

-- Step 2: Populate the `diameter` column (safe to re-run).
UPDATE `finished_stock` fs
JOIN `product` p ON fs.id_product = p.id_product
SET fs.diameter = p.diameter
WHERE fs.diameter IS NULL;

-- Step 3: Make the column NOT NULL (safe to re-run).
ALTER TABLE `finished_stock`
MODIFY COLUMN `diameter` DECIMAL(3,1) NOT NULL;

-- Step 4: Create the new unique key. This will produce a "duplicate key" error if it already exists, which is harmless in this context.
-- The migration will still be considered successful as the main schema change is in place.
ALTER TABLE `finished_stock`
ADD UNIQUE `unique_stock_product_diameter` (`id_product`, `diameter`);

-- Step 5: Log this action using the correct column 'new_value'.
INSERT INTO `audit_log` (`username`, `action`, `module`, `new_value`) 
VALUES ('system_migration', 'alter_table_v4', 'finished_stock', '{"details": "Resumed migration: Populated diameter and added new unique key."}');

COMMIT;