-- =================================================================================================
-- Filename: 20251210_consolidated_trigger_fix.sql
-- Description: A comprehensive solution to definitively resolve Error 1442 (recursive trigger)
-- by cleaning up and rebuilding triggers on ALL related tables (`project`, `finished_stock`).
--
-- Root Cause of Error 1442:
-- The error occurs when an action on one table (e.g., INSERT on `project`) fires a trigger
-- that causes an update on a second table (e.g., `finished_stock`), which in turn has ITS OWN
-- trigger that tries to update the ORIGINAL table (`project`). This creates a loop.
--
-- Solution:
-- 1. Drop ALL potentially conflicting triggers on `project`.
-- 2. Drop ALL potentially conflicting triggers on `finished_stock`. THIS IS THE CRITICAL NEW STEP.
-- 3. Re-create a clean, correct set of triggers ONLY on the `project` table.
--    - BEFORE triggers will prepare data for the `project` row (setting flags, allocation amounts).
--    - AFTER triggers will update OTHER tables (like `finished_stock`).
--
-- This script ensures there is no trigger path leading back to the `project` table.
-- =================================================================================================

DELIMITER $$

-- Step 1: Drop all existing triggers on the `project` table to ensure a clean slate.
DROP TRIGGER IF EXISTS `trg_project_before_insert`$$
DROP TRIGGER IF EXISTS `trg_project_after_insert`$$
DROP TRIGGER IF EXISTS `trg_project_before_update`$$
DROP TRIGGER IF EXISTS `trg_project_after_update`$$
DROP TRIGGER IF EXISTS `trg_project_after_delete`$$

-- Drop older, legacy trigger names as well, just in case.
DROP TRIGGER IF EXISTS `trg_project_after_insert_deduct_stock`$$
DROP TRIGGER IF EXISTS `trg_project_after_update_deduct_stock`$$
DROP TRIGGER IF EXISTS `trg_project_after_delete_restore_stock`$$
DROP TRIGGER IF EXISTS `trg_project_before_insert_manage_stock`$$
DROP TRIGGER IF EXISTS `trg_project_before_update_manage_stock`$$

-- Step 2: CRITICAL - Drop triggers on `finished_stock` that may be causing the recursive loop.
-- We assume a trigger exists here that tries to update `project`.
DROP TRIGGER IF EXISTS `trg_finished_stock_after_update`$$
DROP TRIGGER IF EXISTS `trg_update_project_warnings_on_stock_change`$$

-- =================================================================================================
-- TRIGGER 1: BEFORE INSERT on `project`
-- Purpose: Calculate stock allocation and warnings BEFORE the new order is saved.
-- Modifies the NEW row directly; does NOT update any tables.
-- =================================================================================================
CREATE TRIGGER `trg_project_before_insert` BEFORE INSERT ON `project` FOR EACH ROW
BEGIN
    DECLARE v_stock_available INT DEFAULT 0;
    DECLARE v_qty_to_allocate INT DEFAULT 0;

    -- This trigger only handles active orders.
    IF NEW.pr_status IN (1, 2) THEN
        -- 1. Get current available stock for the product.
        SELECT COALESCE(fs.quantity_in_stock, 0)
        INTO v_stock_available
        FROM `finished_stock` fs
        WHERE fs.id_product = NEW.id_product
        LIMIT 1;

        -- 2. Determine how much of the requested quantity can be allocated from stock.
        SET v_qty_to_allocate = LEAST(NEW.qty_request, v_stock_available);

        -- 3. Set the calculated values directly on the NEW row being inserted.
        SET NEW.finished_stock_available = v_stock_available; -- Store snapshot of stock level at time of order
        SET NEW.qty_allocated = v_qty_to_allocate;
        SET NEW.allocation_status = IF(v_qty_to_allocate > 0, 'allocated', 'unallocated');

        -- 4. Generate the correct warning flag and JSON details based on stock availability.
        IF v_stock_available >= NEW.qty_request THEN
            SET NEW.warning_flag = 1; -- "Sufficient"
            SET NEW.warning_details = JSON_OBJECT(
                'stock_status', 'sufficient',
                'message', CONCAT('Tồn kho (', v_stock_available, ') đủ. Phân bổ ', v_qty_to_allocate, ' cái. Sẵn sàng giao ngay.')
            );
        ELSE
            SET NEW.warning_flag = 2; -- "Insufficient"
            SET NEW.warning_details = JSON_OBJECT(
                'stock_status', 'insufficient',
                'message', CONCAT('Tồn kho (', v_stock_available, ') không đủ. Phân bổ ', v_qty_to_allocate, ' cái. Cần sản xuất thêm ', NEW.qty_request - v_qty_to_allocate, ' cái.')
            );
        END IF;
    ELSE
        -- For non-active statuses (e.g., draft, cancelled), ensure allocation fields are zero/null.
        SET NEW.qty_allocated = 0;
        SET NEW.allocation_status = 'none';
        SET NEW.warning_flag = 0;
        SET NEW.warning_details = NULL;
        SET NEW.finished_stock_available = 0;
    END IF;
END$$

-- =================================================================================================
-- TRIGGER 2: AFTER INSERT on `project`
-- Purpose: Update the `finished_stock` table AFTER the order has been successfully inserted.
-- This is safe because it only updates a different table.
-- =================================================================================================
CREATE TRIGGER `trg_project_after_insert` AFTER INSERT ON `project` FOR EACH ROW
BEGIN
    -- If stock was allocated for the new order, deduct that quantity from the inventory.
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
-- TRIGGER 3: BEFORE UPDATE on `project`
-- Purpose: Recalculate stock and warnings before an order update is saved.
-- =================================================================================================
CREATE TRIGGER `trg_project_before_update` BEFORE UPDATE ON `project` FOR EACH ROW
BEGIN
    DECLARE v_current_stock INT DEFAULT 0;
    DECLARE v_stock_for_recalculation INT DEFAULT 0;
    DECLARE v_qty_to_allocate INT DEFAULT 0;

    -- 1. Get the actual current stock from the inventory table.
    SELECT COALESCE(fs.quantity_in_stock, 0)
    INTO v_current_stock
    FROM `finished_stock` fs
    WHERE fs.id_product = OLD.id_product
    LIMIT 1;

    -- 2. Calculate the stock level available for this order's recalculation.
    -- This is the current stock PLUS what was previously allocated to THIS order.
    SET v_stock_for_recalculation = v_current_stock + COALESCE(OLD.qty_allocated, 0);

    -- 3. Apply the allocation and warning logic, same as the INSERT trigger.
    IF NEW.pr_status IN (1, 2) THEN
        SET v_qty_to_allocate = LEAST(NEW.qty_request, v_stock_for_recalculation);

        SET NEW.finished_stock_available = v_stock_for_recalculation; -- Store snapshot
        SET NEW.qty_allocated = v_qty_to_allocate;
        SET NEW.allocation_status = IF(v_qty_to_allocate > 0, 'allocated', 'unallocated');

        IF v_stock_for_recalculation >= NEW.qty_request THEN
            SET NEW.warning_flag = 1; -- "Sufficient"
            SET NEW.warning_details = JSON_OBJECT(
                'stock_status', 'sufficient',
                'message', CONCAT('Tồn kho có thể sử dụng (', v_stock_for_recalculation, ') đủ. Phân bổ ', v_qty_to_allocate, ' cái.')
            );
        ELSE
            SET NEW.warning_flag = 2; -- "Insufficient"
            SET NEW.warning_details = JSON_OBJECT(
                'stock_status', 'insufficient',
                'message', CONCAT('Tồn kho có thể sử dụng (', v_stock_for_recalculation, ') không đủ. Phân bổ ', v_qty_to_allocate, '. Cần sản xuất thêm ', NEW.qty_request - v_qty_to_allocate, ' cái.')
            );
        END IF;
    ELSE
        -- If order is moved to a non-active state, reset values. Stock will be restored in the AFTER UPDATE trigger.
        SET NEW.qty_allocated = 0;
        SET NEW.allocation_status = 'cancelled';
        SET NEW.warning_flag = 0;
        SET NEW.warning_details = NULL;
        SET NEW.finished_stock_available = v_current_stock;
    END IF;
END$$

-- =================================================================================================
-- TRIGGER 4: AFTER UPDATE on `project`
-- Purpose: Apply the final net stock change to the `finished_stock` table.
-- =================================================================================================
CREATE TRIGGER `trg_project_after_update` AFTER UPDATE ON `project` FOR EACH ROW
BEGIN
    DECLARE v_qty_diff INT DEFAULT 0;
    
    -- Calculate the net change in allocated quantity.
    -- e.g., if old was 5 and new is 8, diff is 3 (deduct 3 more from stock).
    -- e.g., if old was 5 and new is 2, diff is -3 (add 3 back to stock).
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
-- TRIGGER 5: AFTER DELETE on `project`
-- Purpose: Restore allocated stock to inventory if an order is deleted.
-- =================================================================================================
CREATE TRIGGER `trg_project_after_delete` AFTER DELETE ON `project` FOR EACH ROW
BEGIN
    -- If the deleted order had stock allocated, add it back to the inventory.
    IF OLD.qty_allocated > 0 THEN
        UPDATE `finished_stock`
        SET
            quantity_in_stock = quantity_in_stock + OLD.qty_allocated,
            quantity_issued = GREATEST(0, quantity_issued - OLD.qty_allocated), -- Prevent issued going below 0
            last_updated = NOW()
        WHERE id_product = OLD.id_product;
    END IF;
END$$

DELIMITER ;