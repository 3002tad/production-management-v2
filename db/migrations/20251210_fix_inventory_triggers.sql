-- Fixes issues with database triggers automatically deducting stock for products with multiple variants, preventing data corruption.
-- Drops the old, faulty triggers and creates new, safer versions.
--
-- USE `db_production`; -- Make sure you have selected your database in phpMyAdmin before running this script.

DELIMITER $$

-- Drop the old triggers if they exist
DROP TRIGGER IF EXISTS `trg_project_after_insert_deduct_stock`$$
DROP TRIGGER IF EXISTS `trg_project_after_update_deduct_stock`$$
DROP TRIGGER IF EXISTS `trg_project_after_delete_restore_stock`$$


-- =================================================================================================
-- CREATE NEW, SAFER TRIGGER FOR INSERT
-- Logic: Only deduct stock automatically if the product has only ONE unique diameter.
-- This prevents deducting from the wrong stock pool for products with multiple variants.
-- =================================================================================================

CREATE TRIGGER `trg_project_after_insert_deduct_stock` AFTER INSERT ON `project` FOR EACH ROW
BEGIN
    DECLARE stock_available INT DEFAULT 0;
    DECLARE qty_to_deduct INT DEFAULT 0;
    DECLARE product_variant_count INT DEFAULT 0;

    -- SAFETY CHECK: Count how many unique diameters this product ID has.
    SELECT COUNT(DISTINCT diameter) INTO product_variant_count
    FROM `product`
    WHERE `id_product` = NEW.id_product;

    -- ONLY PROCEED IF:
    -- 1. The order is approved/in production.
    -- 2. Stock has not already been allocated.
    -- 3. The product is "simple" (only has one variant), making it safe to auto-deduct.
    IF NEW.pr_status IN (1, 2) 
       AND (NEW.allocation_status IS NULL OR NEW.allocation_status != 'allocated')
       AND product_variant_count = 1 THEN

        -- Get current finished stock. This is safe because we've confirmed only one variant exists.
        SELECT COALESCE(fs.quantity_in_stock, 0) 
        INTO stock_available
        FROM `finished_stock` fs
        WHERE fs.id_product = NEW.id_product
        LIMIT 1;
        
        -- Deduct from inventory if stock is available
        SET qty_to_deduct = LEAST(NEW.qty_request, stock_available);
        
        IF qty_to_deduct > 0 THEN
            -- Update the stock table
            UPDATE `finished_stock`
            SET 
                quantity_in_stock = quantity_in_stock - qty_to_deduct,
                quantity_issued = quantity_issued + qty_to_deduct,
                last_updated = NOW()
            WHERE id_product = NEW.id_product;
            
            -- Update the project itself to mark allocation
            UPDATE `project`
            SET 
                qty_allocated = qty_to_deduct,
                allocation_status = 'allocated'
            WHERE id_project = NEW.id_project;
        END IF;
    END IF;
END$$


-- =================================================================================================
-- CREATE NEW, SAFER TRIGGER FOR UPDATE
-- Logic: Same safety check. Only process stock changes for "simple" products.
-- =================================================================================================

CREATE TRIGGER `trg_project_after_update_deduct_stock` AFTER UPDATE ON `project` FOR EACH ROW
BEGIN
    DECLARE stock_available INT DEFAULT 0;
    DECLARE qty_diff INT DEFAULT 0;
    DECLARE old_qty_allocated INT DEFAULT 0;
    DECLARE new_qty_to_allocate INT DEFAULT 0;
    DECLARE product_variant_count INT DEFAULT 0;

    -- SAFETY CHECK: Count how many unique diameters this product ID has.
    SELECT COUNT(DISTINCT diameter) INTO product_variant_count
    FROM `product`
    WHERE `id_product` = NEW.id_product;
    
    -- Only proceed if the product is "simple" (one variant)
    IF product_variant_count = 1 THEN
    
        SET old_qty_allocated = COALESCE(OLD.qty_allocated, 0);

        -- Case 1: Order is newly approved
        IF OLD.pr_status NOT IN (1, 2) AND NEW.pr_status IN (1, 2) THEN
            SELECT COALESCE(fs.quantity_in_stock, 0) INTO stock_available
            FROM `finished_stock` fs
            WHERE fs.id_product = NEW.id_product LIMIT 1;
            
            SET new_qty_to_allocate = LEAST(NEW.qty_request, stock_available);
            
            IF new_qty_to_allocate > 0 THEN
                UPDATE `finished_stock` SET quantity_in_stock = quantity_in_stock - new_qty_to_allocate, quantity_issued = quantity_issued + new_qty_to_allocate, last_updated = NOW() WHERE id_product = NEW.id_product;
                UPDATE `project` SET qty_allocated = new_qty_to_allocate, allocation_status = 'allocated' WHERE id_project = NEW.id_project;
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
                UPDATE `project` SET qty_allocated = new_qty_to_allocate WHERE id_project = NEW.id_project;
            END IF;

        -- Case 3: Order is cancelled
        ELSEIF OLD.pr_status IN (1, 2) AND NEW.pr_status NOT IN (1, 2) THEN
            IF old_qty_allocated > 0 THEN
                UPDATE `finished_stock` SET quantity_in_stock = quantity_in_stock + old_qty_allocated, quantity_issued = GREATEST(0, quantity_issued - old_qty_allocated), last_updated = NOW() WHERE id_product = OLD.id_product;
                UPDATE `project` SET qty_allocated = 0, allocation_status = 'cancelled' WHERE id_project = NEW.id_project;
            END IF;
        END IF;
    END IF;
END$$


-- =================================================================================================
-- CREATE NEW, SAFER TRIGGER FOR DELETE
-- Logic: Same safety check. Only restore stock for "simple" products.
-- =================================================================================================

CREATE TRIGGER `trg_project_after_delete_restore_stock` AFTER DELETE ON `project` FOR EACH ROW
BEGIN
    DECLARE qty_to_restore INT DEFAULT 0;
    DECLARE product_variant_count INT DEFAULT 0;

    -- SAFETY CHECK: Count how many unique diameters this product ID has.
    SELECT COUNT(DISTINCT diameter) INTO product_variant_count
    FROM `product`
    WHERE `id_product` = OLD.id_product;

    SET qty_to_restore = COALESCE(OLD.qty_allocated, 0);
    
    -- Only restore stock if:
    -- 1. The order was approved.
    -- 2. There was stock allocated.
    -- 3. The product is "simple" (safe to modify stock).
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
