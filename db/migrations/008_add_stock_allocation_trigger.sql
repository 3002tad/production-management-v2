-- ========================================
-- MIGRATION 008: Stock Allocation System
-- Tự động trừ tồn kho khi tạo/cập nhật/xóa đơn hàng
-- ========================================
-- Ngày tạo: 09/12/2025
-- Mục đích: Fix bug tồn kho không được cập nhật thực tế
-- ========================================

START TRANSACTION;

-- ========================================
-- BƯỚC 1: Tạo bảng stock_allocation
-- Lưu lịch sử phân bổ tồn kho cho mỗi đơn hàng
-- ========================================
CREATE TABLE IF NOT EXISTS stock_allocation (
    id_allocation INT AUTO_INCREMENT PRIMARY KEY,
    
    id_project VARCHAR(50) NOT NULL
        COMMENT 'Mã đơn hàng',
    
    id_product INT NOT NULL
        COMMENT 'Mã sản phẩm',
    
    quantity_allocated INT NOT NULL
        COMMENT 'Số lượng tồn kho đã phân bổ cho đơn hàng này',
    
    allocated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        COMMENT 'Thời điểm phân bổ',
    
    allocation_type ENUM('order_create', 'order_update', 'order_delete', 'manual_adjust') DEFAULT 'order_create'
        COMMENT 'Loại phân bổ',
    
    notes TEXT NULL
        COMMENT 'Ghi chú',
    
    FOREIGN KEY (id_project) REFERENCES project(id_project) ON DELETE CASCADE,
    FOREIGN KEY (id_product) REFERENCES product(id_product) ON DELETE CASCADE,
    
    INDEX idx_project (id_project),
    INDEX idx_product (id_product),
    INDEX idx_allocated_at (allocated_at)
) ENGINE=InnoDB 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Lịch sử phân bổ tồn kho cho đơn hàng';

-- ========================================
-- BƯỚC 2: Thêm cột tracking vào bảng product
-- ========================================
ALTER TABLE product
ADD COLUMN quantity_in_stock INT DEFAULT 0
    COMMENT 'Tổng số lượng tồn kho thành phẩm (thực tế)'
    AFTER stock,
    
ADD COLUMN quantity_allocated INT DEFAULT 0
    COMMENT 'Tổng số lượng đã phân bổ cho các đơn hàng'
    AFTER quantity_in_stock,
    
ADD COLUMN quantity_available INT GENERATED ALWAYS AS (quantity_in_stock - quantity_allocated) STORED
    COMMENT 'Số lượng khả dụng = tồn kho - đã phân bổ (tự động tính)'
    AFTER quantity_allocated;

-- ========================================
-- BƯỚC 3: Migrate dữ liệu cũ
-- Copy stock sang quantity_in_stock
-- ========================================
UPDATE product 
SET quantity_in_stock = COALESCE(stock, 0)
WHERE quantity_in_stock IS NULL OR quantity_in_stock = 0;

-- ========================================
-- BƯỚC 4: Tính lại quantity_allocated từ các đơn đang tồn tại
-- ========================================
UPDATE product p
LEFT JOIN (
    SELECT 
        id_product,
        SUM(
            CASE 
                WHEN finished_stock_available > 0 
                THEN LEAST(qty_request, finished_stock_available)
                ELSE 0
            END
        ) AS total_allocated
    FROM project
    WHERE pr_status IN (1, 2) -- Chỉ tính đơn đã duyệt và đang sản xuất
    GROUP BY id_product
) alloc ON p.id_product = alloc.id_product
SET p.quantity_allocated = COALESCE(alloc.total_allocated, 0);

-- ========================================
-- BƯỚC 5: Tạo TRIGGER khi INSERT project mới
-- ========================================
DROP TRIGGER IF EXISTS after_project_insert_stock;

DELIMITER $$
CREATE TRIGGER after_project_insert_stock
AFTER INSERT ON project
FOR EACH ROW
BEGIN
    DECLARE available_stock INT DEFAULT 0;
    DECLARE qty_to_allocate INT DEFAULT 0;
    
    -- Chỉ xử lý nếu đơn đã được duyệt (pr_status = 1 or 2)
    IF NEW.pr_status IN (1, 2) AND NEW.finished_stock_available > 0 THEN
        
        -- Lấy tồn kho khả dụng hiện tại
        SELECT quantity_available INTO available_stock
        FROM product
        WHERE id_product = NEW.id_product;
        
        -- Tính số lượng cần phân bổ = MIN(qty_request, available_stock)
        SET qty_to_allocate = LEAST(NEW.qty_request, available_stock);
        
        -- Cập nhật quantity_allocated trong product
        IF qty_to_allocate > 0 THEN
            UPDATE product
            SET quantity_allocated = quantity_allocated + qty_to_allocate
            WHERE id_product = NEW.id_product;
            
            -- Ghi log vào stock_allocation
            INSERT INTO stock_allocation 
                (id_project, id_product, quantity_allocated, allocation_type, notes)
            VALUES 
                (NEW.id_project, NEW.id_product, qty_to_allocate, 'order_create', 
                 CONCAT('Đơn hàng ', NEW.project_name, ' - Yêu cầu: ', NEW.qty_request, ' cái'));
        END IF;
    END IF;
END$$
DELIMITER ;

-- ========================================
-- BƯỚC 6: Tạo TRIGGER khi UPDATE project
-- ========================================
DROP TRIGGER IF EXISTS after_project_update_stock;

DELIMITER $$
CREATE TRIGGER after_project_update_stock
AFTER UPDATE ON project
FOR EACH ROW
BEGIN
    DECLARE old_allocated INT DEFAULT 0;
    DECLARE new_allocated INT DEFAULT 0;
    DECLARE available_stock INT DEFAULT 0;
    
    -- Chỉ xử lý nếu có thay đổi về số lượng hoặc trạng thái
    IF OLD.qty_request != NEW.qty_request 
       OR OLD.pr_status != NEW.pr_status 
       OR OLD.id_product != NEW.id_product THEN
        
        -- Hoàn trả stock cũ (nếu có)
        IF OLD.pr_status IN (1, 2) AND OLD.finished_stock_available > 0 THEN
            SET old_allocated = LEAST(OLD.qty_request, OLD.finished_stock_available);
            
            UPDATE product
            SET quantity_allocated = quantity_allocated - old_allocated
            WHERE id_product = OLD.id_product;
            
            -- Log hoàn trả
            INSERT INTO stock_allocation 
                (id_project, id_product, quantity_allocated, allocation_type, notes)
            VALUES 
                (OLD.id_project, OLD.id_product, -old_allocated, 'order_update', 
                 CONCAT('Hoàn trả khi cập nhật đơn ', OLD.project_name));
        END IF;
        
        -- Phân bổ stock mới
        IF NEW.pr_status IN (1, 2) AND NEW.finished_stock_available > 0 THEN
            SELECT quantity_available INTO available_stock
            FROM product
            WHERE id_product = NEW.id_product;
            
            SET new_allocated = LEAST(NEW.qty_request, available_stock);
            
            IF new_allocated > 0 THEN
                UPDATE product
                SET quantity_allocated = quantity_allocated + new_allocated
                WHERE id_product = NEW.id_product;
                
                -- Log phân bổ mới
                INSERT INTO stock_allocation 
                    (id_project, id_product, quantity_allocated, allocation_type, notes)
                VALUES 
                    (NEW.id_project, NEW.id_product, new_allocated, 'order_update', 
                     CONCAT('Phân bổ mới sau cập nhật đơn ', NEW.project_name));
            END IF;
        END IF;
    END IF;
END$$
DELIMITER ;

-- ========================================
-- BƯỚC 7: Tạo TRIGGER khi DELETE project
-- ========================================
DROP TRIGGER IF EXISTS after_project_delete_stock;

DELIMITER $$
CREATE TRIGGER after_project_delete_stock
AFTER DELETE ON project
FOR EACH ROW
BEGIN
    DECLARE allocated_amount INT DEFAULT 0;
    
    -- Hoàn trả stock khi xóa đơn
    IF OLD.pr_status IN (1, 2) AND OLD.finished_stock_available > 0 THEN
        SET allocated_amount = LEAST(OLD.qty_request, OLD.finished_stock_available);
        
        UPDATE product
        SET quantity_allocated = quantity_allocated - allocated_amount
        WHERE id_product = OLD.id_product;
        
        -- Log hoàn trả (không cần INSERT vì CASCADE sẽ xóa)
    END IF;
END$$
DELIMITER ;

-- ========================================
-- BƯỚC 8: Tạo VIEW để xem stock real-time
-- ========================================
CREATE OR REPLACE VIEW v_product_stock_status AS
SELECT 
    p.id_product,
    p.product_name,
    p.quantity_in_stock AS total_stock,
    p.quantity_allocated AS allocated_to_orders,
    p.quantity_available AS available_stock,
    COUNT(DISTINCT pr.id_project) AS active_orders,
    COALESCE(SUM(pr.qty_request), 0) AS total_demand,
    p.uom,
    p.updated_at AS last_update
FROM product p
LEFT JOIN project pr ON p.id_product = pr.id_product 
    AND pr.pr_status IN (1, 2) -- Chỉ tính đơn đang hoạt động
GROUP BY p.id_product;

-- ========================================
-- BƯỚC 9: Tạo stored procedure để kiểm tra consistency
-- ========================================
DROP PROCEDURE IF EXISTS sp_check_stock_consistency;

DELIMITER $$
CREATE PROCEDURE sp_check_stock_consistency()
BEGIN
    -- Kiểm tra sự khớp đúng giữa quantity_allocated và tổng các đơn
    SELECT 
        p.id_product,
        p.product_name,
        p.quantity_allocated AS allocated_in_product,
        COALESCE(SUM(
            CASE 
                WHEN pr.finished_stock_available > 0 
                THEN LEAST(pr.qty_request, pr.finished_stock_available)
                ELSE 0
            END
        ), 0) AS calculated_from_orders,
        (p.quantity_allocated - COALESCE(SUM(
            CASE 
                WHEN pr.finished_stock_available > 0 
                THEN LEAST(pr.qty_request, pr.finished_stock_available)
                ELSE 0
            END
        ), 0)) AS difference
    FROM product p
    LEFT JOIN project pr ON p.id_product = pr.id_product 
        AND pr.pr_status IN (1, 2)
    GROUP BY p.id_product
    HAVING ABS(difference) > 0.01;
END$$
DELIMITER ;

COMMIT;

-- ========================================
-- HƯỚNG DẪN SỬ DỤNG
-- ========================================
-- 1. Run migration này: SOURCE 008_add_stock_allocation_trigger.sql;
-- 
-- 2. Kiểm tra consistency:
--    CALL sp_check_stock_consistency();
-- 
-- 3. Xem trạng thái stock real-time:
--    SELECT * FROM v_product_stock_status;
-- 
-- 4. Xem lịch sử phân bổ:
--    SELECT * FROM stock_allocation ORDER BY allocated_at DESC LIMIT 20;
-- 
-- 5. Test trigger:
--    -- Tạo đơn mới
--    INSERT INTO project (...) VALUES (...);
--    -- Check stock đã trừ chưa
--    SELECT * FROM v_product_stock_status WHERE id_product = X;
-- ========================================
