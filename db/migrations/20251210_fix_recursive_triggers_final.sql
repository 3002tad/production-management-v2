-- =================================================================================================
-- Filename: 20251210_fix_recursive_triggers_final.sql
-- Description: Corrects recursive trigger error (Error 1442) by converting AFTER triggers to BEFORE triggers.
-- This allows modification of row values before they are committed, avoiding illegal updates on the same table.
-- This script is the definitive fix.
--
-- Instructions: Run this script in your database management tool (e.g., phpMyAdmin) to fix the issue.
-- Make sure your database is selected.
-- =================================================================================================

DELIMITER $$

-- Step 1: Drop all old, faulty, and potentially new triggers to ensure a clean slate.
DROP TRIGGER IF EXISTS `trg_project_after_insert_deduct_stock`$$
DROP TRIGGER IF EXISTS `trg_project_after_update_deduct_stock`$$
DROP TRIGGER IF EXISTS `trg_project_after_delete_restore_stock`$$

-- Drop the new trigger names as well in case of a previously failed partial execution.
DROP TRIGGER IF EXISTS `trg_project_before_insert_manage_stock`$$
DROP TRIGGER IF EXISTS `trg_project_before_update_manage_stock`$$
DROP TRIGGER IF EXISTS `trg_project_before_delete_restore_stock`$$ -- Renaming for consistency

-- =================================================================================================
-- CREATE NEW `BEFORE INSERT` TRIGGER
-- Logic: Calculates stock allocation before the new project row is created.
-- It modifies the `NEW` row directly instead of performing a subsequent `UPDATE`.
-- =================================================================================================

CREATE TRIGGER `trg_project_before_insert_manage_stock` BEFORE INSERT ON `project` FOR EACH ROW
BEGIN
    DECLARE stock_available INT DEFAULT 0;
    DECLARE qty_to_deduct INT DEFAULT 0;
    DECLARE product_variant_count INT DEFAULT 0;

    -- Safety check: Only auto-allocate for simple products (one variant)
    SELECT COUNT(DISTINCT diameter) INTO product_variant_count
    FROM `product`
    WHERE `id_product` = NEW.id_product;

    IF NEW.pr_status IN (1, 2) AND product_variant_count = 1 THEN
        -- Get available finished stock
        SELECT COALESCE(fs.quantity_in_stock, 0) 
        INTO stock_available
        FROM `finished_stock` fs
        WHERE fs.id_product = NEW.id_product
        LIMIT 1;
        
        SET qty_to_deduct = LEAST(NEW.qty_request, stock_available);
        
        IF qty_to_deduct > 0 THEN
            -- Directly modify the new row's values. This is the correct way to avoid Error 1442.
            SET NEW.qty_allocated = qty_to_deduct;
            SET NEW.allocation_status = 'allocated';

            -- Update the separate `finished_stock` table
            UPDATE `finished_stock`
            SET 
                quantity_in_stock = quantity_in_stock - qty_to_deduct,
                quantity_issued = quantity_issued + qty_to_deduct,
                last_updated = NOW()
            WHERE id_product = NEW.id_product;
        END IF;
    END IF;
END$$

-- =================================================================================================
-- CREATE NEW `BEFORE UPDATE` TRIGGER
-- Logic: Manages stock changes when an order is updated (e.g., status change, quantity change).
-- Also modifies the `NEW` row directly.
-- =================================================================================================

CREATE TRIGGER `trg_project_before_update_manage_stock` BEFORE UPDATE ON `project` FOR EACH ROW
BEGIN
    DECLARE stock_available INT DEFAULT 0;
    DECLARE qty_diff INT DEFAULT 0;
    DECLARE old_qty_allocated INT DEFAULT 0;
    DECLARE new_qty_to_allocate INT DEFAULT 0;
    DECLARE product_variant_count INT DEFAULT 0;

    -- Safety check for simple products
    SELECT COUNT(DISTINCT diameter) INTO product_variant_count
    FROM `product`
    WHERE `id_product` = NEW.id_product;

    IF product_variant_count = 1 THEN
        SET old_qty_allocated = COALESCE(OLD.qty_allocated, 0);

        -- Case 1: Order is newly approved (e.g., draft -> approved)
        IF OLD.pr_status NOT IN (1, 2) AND NEW.pr_status IN (1, 2) THEN
            SELECT COALESCE(fs.quantity_in_stock, 0) INTO stock_available
            FROM `finished_stock` fs
            WHERE fs.id_product = NEW.id_product LIMIT 1;
            
            SET new_qty_to_allocate = LEAST(NEW.qty_request, stock_available);
            
            IF new_qty_to_allocate > 0 THEN
                UPDATE `finished_stock` SET quantity_in_stock = quantity_in_stock - new_qty_to_allocate, quantity_issued = quantity_issued + new_qty_to_allocate, last_updated = NOW() WHERE id_product = NEW.id_product;
                SET NEW.qty_allocated = new_qty_to_allocate;
                SET NEW.allocation_status = 'allocated';
            END IF;

        -- Case 2: Approved order changes quantity
        ELSEIF OLD.pr_status IN (1, 2) AND NEW.pr_status IN (1, 2) AND OLD.qty_request != NEW.qty_request THEN
            SELECT COALESCE(fs.quantity_in_stock, 0) INTO stock_available
            FROM `finished_stock` fs
            WHERE fs.id_product = NEW.id_product LIMIT 1;
            
            SET new_qty_to_allocate = LEAST(NEW.qty_request, stock_available + old_qty_allocated);
            SET qty_diff = new_qty_to_allocate - old_qty_allocated;
            
            IF qty_diff != 0 THEN
                UPDATE `finished_stock` SET quantity_in_stock = quantity_in_stock - qty_diff, quantity_issued = quantity_issued + qty_diff, last_updated = NOW() WHERE id_product = NEW.id_product;
                SET NEW.qty_allocated = new_qty_to_allocate;
            END IF;

        -- Case 3: Order is cancelled or moved to a non-approved status
        ELSEIF OLD.pr_status IN (1, 2) AND NEW.pr_status NOT IN (1, 2) THEN
            IF old_qty_allocated > 0 THEN
                UPDATE `finished_stock` SET quantity_in_stock = quantity_in_stock + old_qty_allocated, quantity_issued = GREATEST(0, quantity_issued - old_qty_allocated), last_updated = NOW() WHERE id_product = OLD.id_product;
                SET NEW.qty_allocated = 0;
                SET NEW.allocation_status = 'cancelled';
            END IF;
        END IF;
    END IF;
END$$

-- =================================================================================================
-- RE-CREATE THE `AFTER DELETE` TRIGGER 
-- Logic: If a project is deleted, its allocated stock must be returned.
-- An AFTER trigger is appropriate here because we need to know the final state of the deleted row (OLD).
-- This trigger does not modify the `project` table, so it is safe.
-- =================================================================================================

CREATE TRIGGER `trg_project_after_delete_restore_stock` AFTER DELETE ON `project` FOR EACH ROW
BEGIN
    DECLARE qty_to_restore INT DEFAULT 0;
    DECLARE product_variant_count INT DEFAULT 0;

    -- Safety check for simple products
    SELECT COUNT(DISTINCT diameter) INTO product_variant_count
    FROM `product`
    WHERE `id_product` = OLD.id_product;

    SET qty_to_restore = COALESCE(OLD.qty_allocated, 0);
    
    -- Only restore stock if the deleted order was approved and had stock allocated.
    IF OLD.pr_status IN (1, 2) AND qty_to_restore > 0 AND product_variant_count = 1 THEN
        UPDATE `finished_stock`
        SET 
            quantity_in_stock = quantity_in_stock + qty_to_restore,
            quantity_issued = GREATEST(0, quantity_issued - qty_to_restore),
            last_updated = NOW()
        WHERE id_product = OLD.id_product;
    END IF;
END$$

DELIMITER ;
