-- =================================================================================================
-- Filename: 20251210_fix_triggers_final.sql
-- Description: Definitive solution for Error 1442 on the `project` table.
-- This script rebuilds all triggers on the `project` table following best practices
-- to prevent recursive trigger errors.
--
-- Logic:
-- 1. BEFORE INSERT/UPDATE: All calculations (stock allocation, warnings) are performed,
--    and the `NEW` row is modified directly. No `UPDATE` on the `project` table itself.
-- 2. AFTER INSERT/UPDATE/DELETE: The `finished_stock` table is updated based on the changes.
--    This separation is key to avoiding the error.
--
-- Instructions: Run this script to drop all old triggers and create the new, correct ones.
-- =================================================================================================

DELIMITER $$

-- Step 1: Drop all existing triggers on the `project` table for a clean slate.
DROP TRIGGER IF EXISTS `trg_project_after_insert_deduct_stock`$$
DROP TRIGGER IF EXISTS `trg_project_after_update_deduct_stock`$$
DROP TRIGGER IF EXISTS `trg_project_after_delete_restore_stock`$$
DROP TRIGGER IF EXISTS `trg_project_before_insert_manage_stock`$$
DROP TRIGGER IF EXISTS `trg_project_before_update_manage_stock`$$
DROP TRIGGER IF EXISTS `trg_project_before_delete_restore_stock`$$
DROP TRIGGER IF EXISTS `trg_project_after_insert_update_warnings`$$
DROP TRIGGER IF EXISTS `trg_project_after_update_update_warnings`$$
DROP TRIGGER IF EXISTS `trg_project_before_insert`$$
DROP TRIGGER IF EXISTS `trg_project_after_insert`$$
DROP TRIGGER IF EXISTS `trg_project_before_update`$$
DROP TRIGGER IF EXISTS `trg_project_after_update`$$
DROP TRIGGER IF EXISTS `trg_project_after_delete`$$

-- =================================================================================================
-- TRIGGER 1: BEFORE INSERT
-- Purpose: Calculate stock allocation and warnings BEFORE the new order is saved.
-- =================================================================================================
CREATE TRIGGER `trg_project_before_insert` BEFORE INSERT ON `project` FOR EACH ROW
BEGIN
    DECLARE v_stock_available INT DEFAULT 0;
    DECLARE v_qty_to_allocate INT DEFAULT 0;

    -- Only apply logic for active order statuses
    IF NEW.pr_status IN (1, 2) THEN
        -- Get current available stock for the product
        SELECT COALESCE(fs.quantity_in_stock, 0)
        INTO v_stock_available
        FROM `finished_stock` fs
        WHERE fs.id_product = NEW.id_product
        LIMIT 1;

        -- Determine how much can be allocated from stock
        SET v_qty_to_allocate = LEAST(NEW.qty_request, v_stock_available);

        -- Set values directly on the NEW row
        SET NEW.finished_stock_available = v_stock_available;
        SET NEW.qty_allocated = v_qty_to_allocate;

        IF v_qty_to_allocate > 0 THEN
            SET NEW.allocation_status = 'allocated';
        ELSE
            SET NEW.allocation_status = 'unallocated';
        END IF;

        -- Generate warning flag and details
        IF v_stock_available >= NEW.qty_request THEN
            SET NEW.warning_flag = 1; -- "Sufficient"
            SET NEW.warning_details = JSON_OBJECT(
                'stock_status', 'sufficient',
                'message', CONCAT('Tồn kho (', v_stock_available, ') đủ đáp ứng. Sẵn sàng giao ngay.')
            );
        ELSE
            SET NEW.warning_flag = 2; -- "Insufficient"
            SET NEW.warning_details = JSON_OBJECT(
                'stock_status', 'insufficient',
                'message', CONCAT('Tồn kho (', v_stock_available, ') không đủ. Cần sản xuất thêm ', NEW.qty_request - v_stock_available, ' cái.')
            );
        END IF;
    ELSE
        -- For other statuses, ensure allocation fields are reset
        SET NEW.qty_allocated = 0;
        SET NEW.allocation_status = 'none';
        SET NEW.warning_flag = 0;
        SET NEW.warning_details = NULL;
    END IF;
END$$

-- =================================================================================================
-- TRIGGER 2: AFTER INSERT
-- Purpose: Update the `finished_stock` table AFTER the order has been successfully inserted.
-- =================================================================================================
CREATE TRIGGER `trg_project_after_insert` AFTER INSERT ON `project` FOR EACH ROW
BEGIN
    -- If stock was allocated, deduct it from the inventory.
    IF NEW.qty_allocated > 0 THEN
        UPDATE `finished_stock`
        SET
            quantity_in_stock = quantity_in_stock - NEW.qty_allocated,
            quantity_issued = quantity_issued + NEW.qty_allocated,
            last_updated = NOW()
        WHERE id_product = NEW.id_product;
    END IF;
END$$

-- =================================================================================================
-- TRIGGER 3: BEFORE UPDATE
-- Purpose: Recalculate everything for the updated order.
-- =================================================================================================
CREATE TRIGGER `trg_project_before_update` BEFORE UPDATE ON `project` FOR EACH ROW
BEGIN
    DECLARE v_stock_available INT DEFAULT 0;
    DECLARE v_qty_to_allocate INT DEFAULT 0;
    DECLARE v_stock_after_restoring_old INT DEFAULT 0;

    -- Get current stock and add back the quantity that was previously allocated to this order.
    -- This gives us the "true" available stock before this update is applied.
    SELECT COALESCE(fs.quantity_in_stock, 0)
    INTO v_stock_available
    FROM `finished_stock` fs
    WHERE fs.id_product = OLD.id_product
    LIMIT 1;

    SET v_stock_after_restoring_old = v_stock_available + COALESCE(OLD.qty_allocated, 0);

    -- Now, perform the same logic as the INSERT trigger, but using the updated stock value.
    IF NEW.pr_status IN (1, 2) THEN
        -- Determine how much can be allocated for the NEW request
        SET v_qty_to_allocate = LEAST(NEW.qty_request, v_stock_after_restoring_old);

        -- Set values directly on the NEW row
        SET NEW.finished_stock_available = v_stock_after_restoring_old;
        SET NEW.qty_allocated = v_qty_to_allocate;

        IF v_qty_to_allocate > 0 THEN
            SET NEW.allocation_status = 'allocated';
        ELSE
            SET NEW.allocation_status = 'unallocated';
        END IF;

        -- Generate warning flag and details
        IF v_stock_after_restoring_old >= NEW.qty_request THEN
            SET NEW.warning_flag = 1; -- "Sufficient"
            SET NEW.warning_details = JSON_OBJECT(
                'stock_status', 'sufficient',
                'message', CONCAT('Tồn kho (', v_stock_after_restoring_old, ') đủ đáp ứng. Sẵn sàng giao ngay.')
            );
        ELSE
            SET NEW.warning_flag = 2; -- "Insufficient"
            SET NEW.warning_details = JSON_OBJECT(
                'stock_status', 'insufficient',
                'message', CONCAT('Tồn kho (', v_stock_after_restoring_old, ') không đủ. Cần sản xuất thêm ', NEW.qty_request - v_qty_to_allocate, ' cái.')
            );
        END IF;
    ELSE
        -- If order is cancelled or moved to a non-active state, reset values.
        -- The stock will be restored in the AFTER UPDATE trigger.
        SET NEW.qty_allocated = 0;
        SET NEW.allocation_status = 'cancelled';
        SET NEW.warning_flag = 0;
        SET NEW.warning_details = NULL;
    END IF;
END$$

-- =================================================================================================
-- TRIGGER 4: AFTER UPDATE
-- Purpose: Apply the final stock change to the `finished_stock` table.
-- =================================================================================================
CREATE TRIGGER `trg_project_after_update` AFTER UPDATE ON `project` FOR EACH ROW
BEGIN
    DECLARE v_qty_diff INT DEFAULT 0;
    
    -- Calculate the difference between the new allocation and the old one
    SET v_qty_diff = NEW.qty_allocated - OLD.qty_allocated;

    IF v_qty_diff != 0 THEN
        UPDATE `finished_stock`
        SET
            quantity_in_stock = quantity_in_stock - v_qty_diff,
            quantity_issued = quantity_issued + v_qty_diff,
            last_updated = NOW()
        WHERE id_product = NEW.id_product;
    END IF;
END$$

-- =================================================================================================
-- TRIGGER 5: AFTER DELETE
-- Purpose: Restore allocated stock to inventory if an order is deleted.
-- =================================================================================================
CREATE TRIGGER `trg_project_after_delete` AFTER DELETE ON `project` FOR EACH ROW
BEGIN
    IF OLD.qty_allocated > 0 THEN
        UPDATE `finished_stock`
        SET
            quantity_in_stock = quantity_in_stock + OLD.qty_allocated,
            quantity_issued = GREATEST(0, quantity_issued - OLD.qty_allocated),
            last_updated = NOW()
        WHERE id_product = OLD.id_product;
    END IF;
END$$

DELIMITER ;
