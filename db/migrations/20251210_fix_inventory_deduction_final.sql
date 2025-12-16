-- =====================================================
-- MIGRATION: FIX INVENTORY DEDUCTION - NVL & THÀNH PHẨM
-- Date: 2025-12-10
-- Database: db_production (Dec 07, 2025 backup)
-- Purpose: Trừ tồn kho NVL và thành phẩm đúng logic khi tạo/cập nhật đơn hàng
-- =====================================================

-- QUAN TRỌNG: Chọn database trước khi chạy
USE `db_production`;

-- =========================
-- PHẦN 1: CHUẨN BỊ CẤU TRÚC
-- =========================

-- 1.1: Thêm cột tracking vào bảng project
ALTER TABLE `project` 
ADD COLUMN IF NOT EXISTS `qty_allocated` INT DEFAULT 0 
COMMENT 'Số lượng thành phẩm đã trừ khỏi kho',
ADD COLUMN IF NOT EXISTS `material_deducted` TINYINT(1) DEFAULT 0 
COMMENT 'Đã trừ NVL chưa (0=Chưa, 1=Đã trừ)',
ADD COLUMN IF NOT EXISTS `allocation_status` VARCHAR(20) DEFAULT NULL
COMMENT 'Trạng thái phân bổ: null=chưa xử lý, pending=chờ, allocated=đã trừ kho';

-- 1.2: Cập nhật dữ liệu cũ (đánh dấu đơn đã duyệt là đã trừ để tránh trừ lại)
UPDATE `project` 
SET 
    `qty_allocated` = `qty_request`,
    `material_deducted` = 1,
    `allocation_status` = 'allocated'
WHERE `pr_status` IN (1, 2) 
  AND (`qty_allocated` IS NULL OR `qty_allocated` = 0);

-- =========================
-- PHẦN 2: XÓA TRIGGER CŨ
-- =========================
DROP TRIGGER IF EXISTS `trg_project_after_insert_deduct_materials`;
DROP TRIGGER IF EXISTS `trg_project_after_update_materials`;
DROP TRIGGER IF EXISTS `trg_project_after_delete_restore_materials`;
DROP TRIGGER IF EXISTS `trg_project_after_insert_deduct_stock`;
DROP TRIGGER IF EXISTS `trg_project_after_update_deduct_stock`;
DROP TRIGGER IF EXISTS `trg_project_after_delete_restore_stock`;

-- ============================================================
-- PHẦN 3: TRIGGER TRỪ TỒN KHO NGUYÊN VẬT LIỆU (MATERIAL)
-- ============================================================

DELIMITER $$

-- -----------------------------------------------
-- TRIGGER 1: TRỪ NVL KHI TẠO ĐƠN HÀNG MỚI
-- -----------------------------------------------
CREATE TRIGGER `trg_project_after_insert_deduct_materials`
AFTER INSERT ON `project`
FOR EACH ROW
BEGIN
    DECLARE bom_json LONGTEXT;
    DECLARE bom_length INT DEFAULT 0;
    DECLARE i INT DEFAULT 0;
    DECLARE material_id INT;
    DECLARE material_qty DECIMAL(10,2);
    DECLARE total_material_needed DECIMAL(10,2);
    DECLARE current_stock DECIMAL(10,2);
    
    -- CHỈ TRỪ NVL KHI:
    -- 1. Đơn hàng được duyệt (pr_status IN (1,2))
    -- 2. Chưa trừ NVL (material_deducted = 0)
    IF NEW.pr_status IN (1, 2) AND (NEW.material_deducted = 0 OR NEW.material_deducted IS NULL) THEN
        
        -- Lấy BOM từ bảng product với JOIN chính xác id_product VÀ diameter
        SELECT p.bom INTO bom_json
        FROM product p
        WHERE p.id_product = NEW.id_product
          AND p.diameter = NEW.diameter
        LIMIT 1;
        
        -- Kiểm tra BOM có tồn tại và hợp lệ không
        IF bom_json IS NOT NULL AND JSON_VALID(bom_json) THEN
            
            SET bom_length = JSON_LENGTH(bom_json);
            SET i = 0;
            
            -- Duyệt qua từng NVL trong BOM
            WHILE i < bom_length DO
                
                -- Lấy id_material và quantity_per_unit
                SET material_id = JSON_EXTRACT(bom_json, CONCAT('$[', i, '].id_material'));
                SET material_qty = JSON_EXTRACT(bom_json, CONCAT('$[', i, '].quantity_per_unit'));
                
                -- Bỏ dấu ngoặc kép nếu có (JSON_EXTRACT trả về string có quotes)
                IF material_id IS NOT NULL THEN
                    SET material_id = CAST(material_id AS UNSIGNED);
                END IF;
                
                IF material_qty IS NOT NULL THEN
                    SET material_qty = CAST(material_qty AS DECIMAL(10,2));
                END IF;
                
                -- Tính tổng NVL cần = quantity_per_unit × qty_request
                IF material_id IS NOT NULL AND material_id > 0 AND material_qty > 0 THEN
                    
                    SET total_material_needed = material_qty * NEW.qty_request;
                    
                    -- Lấy tồn kho hiện tại
                    SELECT stock INTO current_stock
                    FROM material
                    WHERE id_material = material_id;
                    
                    -- Trừ NVL (nếu có tồn kho)
                    IF current_stock >= total_material_needed THEN
                        -- Đủ NVL, trừ toàn bộ
                        UPDATE material
                        SET stock = stock - total_material_needed
                        WHERE id_material = material_id;
                        
                        -- Audit log: Trừ material đủ
                        INSERT INTO audit_log (
                            user_id, username, action, module, record_id,
                            old_value, new_value, ip_address, created_at
                        ) VALUES (
                            NULL,
                            'SYSTEM_TRIGGER',
                            'deduct_material',
                            'material',
                            NEW.id_project,
                            JSON_OBJECT('id_material', material_id, 'stock_before', current_stock),
                            JSON_OBJECT('qty_deducted', total_material_needed, 'stock_after', current_stock - total_material_needed),
                            '127.0.0.1',
                            NOW()
                        );
                        
                    ELSEIF current_stock > 0 THEN
                        -- Không đủ NVL, trừ hết số còn lại
                        UPDATE material
                        SET stock = 0
                        WHERE id_material = material_id;
                        
                        -- Audit log: Trừ material không đủ
                        INSERT INTO audit_log (
                            user_id, username, action, module, record_id,
                            old_value, new_value, ip_address, created_at
                        ) VALUES (
                            NULL,
                            'SYSTEM_TRIGGER',
                            'deduct_material_insufficient',
                            'material',
                            NEW.id_project,
                            JSON_OBJECT('id_material', material_id, 'stock_before', current_stock, 'qty_needed', total_material_needed),
                            JSON_OBJECT('qty_deducted', current_stock, 'stock_after', 0, 'shortage', total_material_needed - current_stock),
                            '127.0.0.1',
                            NOW()
                        );
                        
                    END IF;
                    
                END IF;
                
                SET i = i + 1;
                
            END WHILE;
            
            -- Đánh dấu đã trừ NVL
            UPDATE project
            SET material_deducted = 1
            WHERE id_project = NEW.id_project;
            
        END IF;
        
    END IF;
    
END$$

-- -----------------------------------------------
-- TRIGGER 2: CẬP NHẬT NVL KHI SỬA ĐƠN HÀNG
-- -----------------------------------------------
CREATE TRIGGER `trg_project_after_update_materials`
AFTER UPDATE ON `project`
FOR EACH ROW
BEGIN
    DECLARE bom_json LONGTEXT;
    DECLARE bom_length INT DEFAULT 0;
    DECLARE i INT DEFAULT 0;
    DECLARE material_id INT;
    DECLARE material_qty DECIMAL(10,2);
    DECLARE old_total_needed DECIMAL(10,2);
    DECLARE new_total_needed DECIMAL(10,2);
    DECLARE qty_diff DECIMAL(10,2);
    DECLARE current_stock DECIMAL(10,2);
    
    -- Case 1: Đơn hàng CHUYỂN SANG trạng thái Đã duyệt
    IF OLD.pr_status NOT IN (1, 2) AND NEW.pr_status IN (1, 2) THEN
        
        -- Lấy BOM với JOIN chính xác
        SELECT p.bom INTO bom_json 
        FROM product p 
        WHERE p.id_product = NEW.id_product 
          AND p.diameter = NEW.diameter
        LIMIT 1;
        
        IF bom_json IS NOT NULL AND JSON_VALID(bom_json) THEN
            SET bom_length = JSON_LENGTH(bom_json);
            SET i = 0;
            
            WHILE i < bom_length DO
                SET material_id = CAST(JSON_EXTRACT(bom_json, CONCAT('$[', i, '].id_material')) AS UNSIGNED);
                SET material_qty = CAST(JSON_EXTRACT(bom_json, CONCAT('$[', i, '].quantity_per_unit')) AS DECIMAL(10,2));
                
                IF material_id > 0 AND material_qty > 0 THEN
                    SET new_total_needed = material_qty * NEW.qty_request;
                    
                    -- Lấy stock trước khi trừ
                    SELECT stock INTO current_stock FROM material WHERE id_material = material_id;
                    
                    -- Trừ NVL
                    UPDATE material
                    SET stock = GREATEST(0, stock - new_total_needed)
                    WHERE id_material = material_id;
                    
                    -- Audit log
                    INSERT INTO audit_log (
                        user_id, username, action, module, record_id,
                        old_value, new_value, ip_address, created_at
                    ) VALUES (
                        NULL,
                        'SYSTEM_TRIGGER',
                        'deduct_material_on_approve',
                        'material',
                        NEW.id_project,
                        JSON_OBJECT('id_material', material_id, 'stock_before', current_stock),
                        JSON_OBJECT('qty_deducted', new_total_needed, 'stock_after', GREATEST(0, current_stock - new_total_needed)),
                        '127.0.0.1',
                        NOW()
                    );
                END IF;
                
                SET i = i + 1;
            END WHILE;
            
            UPDATE project SET material_deducted = 1 WHERE id_project = NEW.id_project;
        END IF;
        
    -- Case 2: Đơn ĐÃ DUYỆT, thay đổi số lượng
    ELSEIF OLD.pr_status IN (1, 2) AND NEW.pr_status IN (1, 2) 
           AND OLD.qty_request != NEW.qty_request THEN
        
        SELECT p.bom INTO bom_json 
        FROM product p 
        WHERE p.id_product = NEW.id_product 
          AND p.diameter = NEW.diameter
        LIMIT 1;
        
        IF bom_json IS NOT NULL AND JSON_VALID(bom_json) THEN
            SET bom_length = JSON_LENGTH(bom_json);
            SET i = 0;
            
            WHILE i < bom_length DO
                SET material_id = CAST(JSON_EXTRACT(bom_json, CONCAT('$[', i, '].id_material')) AS UNSIGNED);
                SET material_qty = CAST(JSON_EXTRACT(bom_json, CONCAT('$[', i, '].quantity_per_unit')) AS DECIMAL(10,2));
                
                IF material_id > 0 AND material_qty > 0 THEN
                    SET old_total_needed = material_qty * OLD.qty_request;
                    SET new_total_needed = material_qty * NEW.qty_request;
                    SET qty_diff = new_total_needed - old_total_needed;
                    
                    -- Lấy stock hiện tại
                    SELECT stock INTO current_stock FROM material WHERE id_material = material_id;
                    
                    -- Điều chỉnh NVL theo chênh lệch
                    IF qty_diff > 0 THEN
                        -- Tăng số lượng => trừ thêm NVL
                        UPDATE material
                        SET stock = GREATEST(0, stock - qty_diff)
                        WHERE id_material = material_id;
                        
                        -- Audit log: Trừ thêm
                        INSERT INTO audit_log (
                            user_id, username, action, module, record_id,
                            old_value, new_value, ip_address, created_at
                        ) VALUES (
                            NULL,
                            'SYSTEM_TRIGGER',
                            'deduct_material_increase_qty',
                            'material',
                            NEW.id_project,
                            JSON_OBJECT('id_material', material_id, 'stock_before', current_stock, 'old_qty', OLD.qty_request, 'new_qty', NEW.qty_request),
                            JSON_OBJECT('qty_deducted', qty_diff, 'stock_after', GREATEST(0, current_stock - qty_diff)),
                            '127.0.0.1',
                            NOW()
                        );
                        
                    ELSEIF qty_diff < 0 THEN
                        -- Giảm số lượng => hoàn trả NVL
                        UPDATE material
                        SET stock = stock + ABS(qty_diff)
                        WHERE id_material = material_id;
                        
                        -- Audit log: Hoàn trả
                        INSERT INTO audit_log (
                            user_id, username, action, module, record_id,
                            old_value, new_value, ip_address, created_at
                        ) VALUES (
                            NULL,
                            'SYSTEM_TRIGGER',
                            'restore_material_decrease_qty',
                            'material',
                            NEW.id_project,
                            JSON_OBJECT('id_material', material_id, 'stock_before', current_stock, 'old_qty', OLD.qty_request, 'new_qty', NEW.qty_request),
                            JSON_OBJECT('qty_restored', ABS(qty_diff), 'stock_after', current_stock + ABS(qty_diff)),
                            '127.0.0.1',
                            NOW()
                        );
                    END IF;
                END IF;
                
                SET i = i + 1;
            END WHILE;
        END IF;
        
    -- Case 3: Đơn BỊ HỦY hoặc chuyển về Chờ duyệt
    ELSEIF OLD.pr_status IN (1, 2) AND NEW.pr_status NOT IN (1, 2) THEN
        
        SELECT p.bom INTO bom_json 
        FROM product p 
        WHERE p.id_product = OLD.id_product 
          AND p.diameter = OLD.diameter
        LIMIT 1;
        
        IF bom_json IS NOT NULL AND JSON_VALID(bom_json) THEN
            SET bom_length = JSON_LENGTH(bom_json);
            SET i = 0;
            
            WHILE i < bom_length DO
                SET material_id = CAST(JSON_EXTRACT(bom_json, CONCAT('$[', i, '].id_material')) AS UNSIGNED);
                SET material_qty = CAST(JSON_EXTRACT(bom_json, CONCAT('$[', i, '].quantity_per_unit')) AS DECIMAL(10,2));
                
                IF material_id > 0 AND material_qty > 0 THEN
                    SET old_total_needed = material_qty * OLD.qty_request;
                    
                    -- Lấy stock hiện tại
                    SELECT stock INTO current_stock FROM material WHERE id_material = material_id;
                    
                    -- Hoàn trả NVL
                    UPDATE material
                    SET stock = stock + old_total_needed
                    WHERE id_material = material_id;
                    
                    -- Audit log: Hoàn trả khi hủy
                    INSERT INTO audit_log (
                        user_id, username, action, module, record_id,
                        old_value, new_value, ip_address, created_at
                    ) VALUES (
                        NULL,
                        'SYSTEM_TRIGGER',
                        'restore_material_cancel_order',
                        'material',
                        NEW.id_project,
                        JSON_OBJECT('id_material', material_id, 'stock_before', current_stock, 'pr_status_old', OLD.pr_status, 'pr_status_new', NEW.pr_status),
                        JSON_OBJECT('qty_restored', old_total_needed, 'stock_after', current_stock + old_total_needed),
                        '127.0.0.1',
                        NOW()
                    );
                END IF;
                
                SET i = i + 1;
            END WHILE;
            
            UPDATE project SET material_deducted = 0 WHERE id_project = NEW.id_project;
        END IF;
        
    END IF;
    
END$$

-- -----------------------------------------------
-- TRIGGER 3: HOÀN TRẢ NVL KHI XÓA ĐƠN HÀNG
-- -----------------------------------------------
CREATE TRIGGER `trg_project_after_delete_restore_materials`
AFTER DELETE ON `project`
FOR EACH ROW
BEGIN
    DECLARE bom_json LONGTEXT;
    DECLARE bom_length INT DEFAULT 0;
    DECLARE i INT DEFAULT 0;
    DECLARE material_id INT;
    DECLARE material_qty DECIMAL(10,2);
    DECLARE total_to_restore DECIMAL(10,2);
    DECLARE current_stock DECIMAL(10,2);
    
    -- Chỉ hoàn trả nếu đơn đã duyệt VÀ đã trừ NVL
    IF OLD.pr_status IN (1, 2) AND OLD.material_deducted = 1 THEN
        
        SELECT p.bom INTO bom_json 
        FROM product p 
        WHERE p.id_product = OLD.id_product 
          AND p.diameter = OLD.diameter
        LIMIT 1;
        
        IF bom_json IS NOT NULL AND JSON_VALID(bom_json) THEN
            SET bom_length = JSON_LENGTH(bom_json);
            SET i = 0;
            
            WHILE i < bom_length DO
                SET material_id = CAST(JSON_EXTRACT(bom_json, CONCAT('$[', i, '].id_material')) AS UNSIGNED);
                SET material_qty = CAST(JSON_EXTRACT(bom_json, CONCAT('$[', i, '].quantity_per_unit')) AS DECIMAL(10,2));
                
                IF material_id > 0 AND material_qty > 0 THEN
                    SET total_to_restore = material_qty * OLD.qty_request;
                    
                    -- Lấy stock hiện tại
                    SELECT stock INTO current_stock FROM material WHERE id_material = material_id;
                    
                    -- Hoàn trả NVL vào kho
                    UPDATE material
                    SET stock = stock + total_to_restore
                    WHERE id_material = material_id;
                    
                    -- Audit log: Hoàn trả khi xóa đơn
                    INSERT INTO audit_log (
                        user_id, username, action, module, record_id,
                        old_value, new_value, ip_address, created_at
                    ) VALUES (
                        NULL,
                        'SYSTEM_TRIGGER',
                        'restore_material_delete_order',
                        'material',
                        OLD.id_project,
                        JSON_OBJECT('id_material', material_id, 'stock_before', current_stock, 'project_deleted', OLD.project_name),
                        JSON_OBJECT('qty_restored', total_to_restore, 'stock_after', current_stock + total_to_restore),
                        '127.0.0.1',
                        NOW()
                    );
                END IF;
                
                SET i = i + 1;
            END WHILE;
        END IF;
        
    END IF;
    
END$$

-- ============================================================
-- PHẦN 4: TRIGGER TRỪ TỒN KHO THÀNH PHẨM (FINISHED_STOCK)
-- ============================================================

-- -----------------------------------------------
-- TRIGGER 4: TRỪ KHO THÀNH PHẨM KHI TẠO ĐƠN MỚI
-- -----------------------------------------------
CREATE TRIGGER `trg_project_after_insert_deduct_stock`
AFTER INSERT ON `project`
FOR EACH ROW
BEGIN
    DECLARE stock_available INT DEFAULT 0;
    DECLARE qty_to_deduct INT DEFAULT 0;
    
    -- CHỈ TRỪ KHO KHI:
    -- 1. Đơn hàng ở trạng thái "Đã duyệt" (pr_status = 1) hoặc "Đang sản xuất" (pr_status = 2)
    -- 2. allocation_status KHÔNG phải 'allocated' (tránh trừ trùng)
    IF NEW.pr_status IN (1, 2) 
       AND (NEW.allocation_status IS NULL OR NEW.allocation_status != 'allocated') THEN
        
        -- Lấy tồn kho thành phẩm hiện tại với JOIN chính xác
        SELECT COALESCE(fs.quantity_in_stock, 0) 
        INTO stock_available
        FROM finished_stock fs
        INNER JOIN product p ON fs.id_product = p.id_product
        WHERE fs.id_product = NEW.id_product
          AND p.diameter = NEW.diameter
        LIMIT 1;
        
        -- Tính số lượng cần trừ (không vượt quá tồn kho)
        SET qty_to_deduct = LEAST(NEW.qty_request, stock_available);
        
        -- Trừ inventory nếu có hàng tồn
        IF qty_to_deduct > 0 THEN
            UPDATE finished_stock
            SET quantity_in_stock = quantity_in_stock - qty_to_deduct,
                quantity_issued = quantity_issued + qty_to_deduct,
                last_updated = NOW()
            WHERE id_product = NEW.id_product;
            
            -- Cập nhật qty_allocated trong project
            UPDATE project
            SET qty_allocated = qty_to_deduct,
                allocation_status = 'allocated'
            WHERE id_project = NEW.id_project;
            
            -- Log action (nếu có bảng audit_log)
            INSERT INTO audit_log (
                user_id, username, action, module, record_id, 
                old_value, new_value, ip_address, created_at
            )
            VALUES (
                NULL, 
                'SYSTEM_TRIGGER', 
                'deduct_finished_stock', 
                'finished_stock', 
                NEW.id_project,
                JSON_OBJECT('stock_before', stock_available),
                JSON_OBJECT('qty_deducted', qty_to_deduct, 'stock_after', stock_available - qty_to_deduct),
                '127.0.0.1',
                NOW()
            );
        END IF;
        
    END IF;
END$$

-- -----------------------------------------------
-- TRIGGER 5: CẬP NHẬT KHO THÀNH PHẨM KHI SỬA ĐƠN
-- -----------------------------------------------
CREATE TRIGGER `trg_project_after_update_deduct_stock`
AFTER UPDATE ON `project`
FOR EACH ROW
BEGIN
    DECLARE stock_available INT DEFAULT 0;
    DECLARE qty_diff INT DEFAULT 0;
    DECLARE old_qty_allocated INT DEFAULT 0;
    DECLARE new_qty_to_allocate INT DEFAULT 0;
    
    -- Lấy số lượng đã phân bổ trước đó
    SET old_qty_allocated = COALESCE(OLD.qty_allocated, 0);
    
    -- Case 1: Đơn hàng CHUYỂN SANG trạng thái Đã duyệt/Đang sản xuất
    IF OLD.pr_status NOT IN (1, 2) AND NEW.pr_status IN (1, 2) THEN
        
        SELECT COALESCE(fs.quantity_in_stock, 0) 
        INTO stock_available
        FROM finished_stock fs
        INNER JOIN product p ON fs.id_product = p.id_product
        WHERE fs.id_product = NEW.id_product
          AND p.diameter = NEW.diameter
        LIMIT 1;
        
        SET new_qty_to_allocate = LEAST(NEW.qty_request, stock_available);
        
        IF new_qty_to_allocate > 0 THEN
            UPDATE finished_stock
            SET quantity_in_stock = quantity_in_stock - new_qty_to_allocate,
                quantity_issued = quantity_issued + new_qty_to_allocate,
                last_updated = NOW()
            WHERE id_product = NEW.id_product;
            
            UPDATE project
            SET qty_allocated = new_qty_to_allocate,
                allocation_status = 'allocated'
            WHERE id_project = NEW.id_project;
        END IF;
        
    -- Case 2: Đơn ĐÃ DUYỆT, thay đổi số lượng (qty_request)
    ELSEIF OLD.pr_status IN (1, 2) AND NEW.pr_status IN (1, 2) 
           AND OLD.qty_request != NEW.qty_request THEN
        
        SELECT COALESCE(fs.quantity_in_stock, 0) 
        INTO stock_available
        FROM finished_stock fs
        INNER JOIN product p ON fs.id_product = p.id_product
        WHERE fs.id_product = NEW.id_product
          AND p.diameter = NEW.diameter
        LIMIT 1;
        
        -- Tính chênh lệch ĐÚNG:
        -- 1. Tồn kho khả dụng = tồn kho hiện tại + số đã phân bổ trước đó
        -- 2. Số cần phân bổ mới = MIN(qty_request mới, tồn kho khả dụng)
        -- 3. Chênh lệch = số mới - số cũ
        SET new_qty_to_allocate = LEAST(NEW.qty_request, stock_available + old_qty_allocated);
        SET qty_diff = new_qty_to_allocate - old_qty_allocated;
        
        -- Cập nhật kho theo chênh lệch
        IF qty_diff != 0 THEN
            UPDATE finished_stock
            SET quantity_in_stock = quantity_in_stock - qty_diff,
                quantity_issued = quantity_issued + qty_diff,
                last_updated = NOW()
            WHERE id_product = NEW.id_product;
            
            UPDATE project
            SET qty_allocated = new_qty_to_allocate
            WHERE id_project = NEW.id_project;
        END IF;
        
    -- Case 3: Đơn BỊ HỦY hoặc chuyển về Chờ duyệt
    ELSEIF OLD.pr_status IN (1, 2) AND NEW.pr_status NOT IN (1, 2) THEN
        
        IF old_qty_allocated > 0 THEN
            UPDATE finished_stock
            SET quantity_in_stock = quantity_in_stock + old_qty_allocated,
                quantity_issued = GREATEST(0, quantity_issued - old_qty_allocated),
                last_updated = NOW()
            WHERE id_product = NEW.id_product;
            
            UPDATE project
            SET qty_allocated = 0,
                allocation_status = NULL
            WHERE id_project = NEW.id_project;
        END IF;
        
    END IF;
    
END$$

-- -----------------------------------------------
-- TRIGGER 6: HOÀN TRẢ KHO THÀNH PHẨM KHI XÓA ĐƠN
-- -----------------------------------------------
CREATE TRIGGER `trg_project_after_delete_restore_stock`
AFTER DELETE ON `project`
FOR EACH ROW
BEGIN
    DECLARE qty_to_restore INT DEFAULT 0;
    
    SET qty_to_restore = COALESCE(OLD.qty_allocated, 0);
    
    -- Chỉ hoàn trả nếu đơn đã duyệt VÀ có qty_allocated > 0
    IF OLD.pr_status IN (1, 2) AND qty_to_restore > 0 THEN
        
        UPDATE finished_stock
        SET quantity_in_stock = quantity_in_stock + qty_to_restore,
            quantity_issued = GREATEST(0, quantity_issued - qty_to_restore),
            last_updated = NOW()
        WHERE id_product = OLD.id_product;
        
        INSERT INTO audit_log (
            user_id, username, action, module, record_id, 
            old_value, new_value, ip_address, created_at
        )
        VALUES (
            NULL,
            'SYSTEM_TRIGGER',
            'restore_finished_stock',
            'finished_stock',
            OLD.id_project,
            JSON_OBJECT('qty_restored', qty_to_restore),
            JSON_OBJECT('reason', 'order_deleted'),
            '127.0.0.1',
            NOW()
        );
        
    END IF;
END$$

DELIMITER ;

-- =====================================================
-- PHẦN 5: VERIFY VÀ KIỂM TRA
-- =====================================================

-- Kiểm tra triggers đã tạo
SELECT 
    TRIGGER_NAME,
    EVENT_MANIPULATION,
    EVENT_OBJECT_TABLE,
    ACTION_TIMING
FROM 
    information_schema.TRIGGERS
WHERE 
    TRIGGER_SCHEMA = 'db_production'
    AND EVENT_OBJECT_TABLE = 'project'
ORDER BY 
    EVENT_OBJECT_TABLE, ACTION_TIMING, EVENT_MANIPULATION;

-- Kiểm tra cột mới trong bảng project
SELECT 
    COLUMN_NAME, 
    COLUMN_TYPE, 
    COLUMN_DEFAULT, 
    COLUMN_COMMENT
FROM 
    information_schema.COLUMNS
WHERE 
    TABLE_SCHEMA = 'db_production'
    AND TABLE_NAME = 'project'
    AND COLUMN_NAME IN ('qty_allocated', 'material_deducted', 'allocation_status');

-- =====================================================
-- END OF MIGRATION
-- =====================================================
