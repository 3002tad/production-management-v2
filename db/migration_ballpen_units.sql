-- ===================================================================
-- MIGRATION SCRIPT: Cập nhật đơn vị và trường cho sản xuất bút bi
-- Created: 2025-10-26
-- Description: Chuyển đổi đơn vị từ Kg sang cái/gram và cập nhật schema
-- ===================================================================

USE `db_production`;

-- ===================================================================
-- 1. THÊM COMMENT VÀO CÁC BẢNG ĐỂ LƯU Ý ĐƠN VỊ
-- ===================================================================

-- Bảng machine: capacity đơn vị là cái/giờ (pieces per hour)
ALTER TABLE `machine` 
COMMENT = 'Bảng máy móc - capacity: cái/giờ, mc_status: 1=Sẵn sàng, 2=Đang dùng, 3=Sự cố, 4=Bảo trì';

-- Bảng material: stock đơn vị là gram
ALTER TABLE `material` 
COMMENT = 'Bảng nguyên liệu - stock: gram';

-- Bảng project: qty_request đơn vị là cái, diameter đơn vị là mm
ALTER TABLE `project` 
COMMENT = 'Bảng dự án - qty_request: cái (số lượng bút), diameter: mm (đường kính bi)';

-- Bảng planning: qty_target đơn vị là cái/ca
ALTER TABLE `planning` 
COMMENT = 'Bảng kế hoạch - qty_target: cái/ca (số lượng bút mỗi ca)';

-- Bảng sorting_report: waste và finished đơn vị là cái
ALTER TABLE `sorting_report` 
COMMENT = 'Báo cáo phân loại - waste & finished: cái (số lượng bút)';

-- Bảng finished_report: total_finished đơn vị là cái
ALTER TABLE `finished_report` 
COMMENT = 'Báo cáo hoàn thành - total_finished: cái (số lượng bút)';

-- Bảng p_material: used_stock đơn vị là gram
ALTER TABLE `p_material` 
COMMENT = 'Nguyên liệu sản xuất - used_stock: gram';

-- ===================================================================
-- 2. CÂY NHẬT DỮ LIỆU MẪU (Ví dụ cho bút bi)
-- ===================================================================

-- Cập nhật machine capacity (giả sử 1 Kg ~ 200 cái bút)
-- Ví dụ: 300 Kg → 300 * 200 = 60000 cái/ca → 60000/8 = 7500 cái/giờ
-- Hoặc để giá trị thực tế phù hợp: 500-1000 cái/giờ

UPDATE `machine` SET 
    `capacity` = 500,
    `mc_status` = 1
WHERE `id_machine` = 1001;

UPDATE `machine` SET 
    `capacity` = 800,
    `mc_status` = 1
WHERE `id_machine` = 1002;

-- Cập nhật material stock (Kg → gram)
-- Ví dụ: 819 Kg = 819000 gram (nhưng thực tế nên nhỏ hơn)
UPDATE `material` SET 
    `stock` = 5000
WHERE `id_material` = 1001;

-- Cập nhật project qty_request (Kg → cái)
-- Ví dụ: 200 Kg → 200 * 200 = 40000 cái hoặc giá trị thực tế
UPDATE `project` SET 
    `qty_request` = 10000,
    `diameter` = 7  -- 0.7 mm (lưu là 7 để tránh decimal, hoặc dùng DECIMAL)
WHERE `id_project` = 1001;

-- Cập nhật planning qty_target (Kg/ca → cái/ca)
-- Ví dụ: 12 Kg/ca → 12 * 200 = 2400 cái/ca
UPDATE `planning` SET 
    `qty_target` = 2000
WHERE `id_plan` = 1001;

-- Cập nhật sorting_report (Kg → cái)
UPDATE `sorting_report` SET 
    `waste` = 50,      -- 10 Kg → ~2000 cái phế phẩm (hoặc giá trị thực tế)
    `finished` = 1950  -- 20 Kg → ~4000 cái hoàn thành
WHERE `id_sorting` = 1001;

-- Cập nhật finished_report (Kg → cái)
UPDATE `finished_report` SET 
    `total_finished` = 2000  -- 20 Kg → ~4000 cái
WHERE `id_finished` = 1001;

-- Cập nhật p_material used_stock (Kg → gram)
UPDATE `p_material` SET 
    `used_stock` = 500  -- 90 Kg → 90000 gram (hoặc 500 gram cho thực tế)
WHERE `id_pmaterial` = 1001;

-- ===================================================================
-- 3. THÊM CỘT MỚI (NẾU CẦN) - Màu mực cho product
-- ===================================================================

-- Kiểm tra nếu cột 'application' đang lưu "Ứng dụng", 
-- có thể đổi tên hoặc thêm comment

ALTER TABLE `product` 
MODIFY COLUMN `application` VARCHAR(100) NOT NULL 
COMMENT 'Màu mực: Xanh, Đen, Đỏ, Nhiều màu';

ALTER TABLE `product` 
MODIFY COLUMN `summary` LONGTEXT NOT NULL 
COMMENT 'Thông tin chi tiết sản phẩm bút bi';

-- ===================================================================
-- 4. THÊM DỮ LIỆU MẪU PHÙ HỢP VỚI BÚT BI
-- ===================================================================

-- Cập nhật product mẫu
UPDATE `product` SET 
    `product_name` = 'Bút bi TL-079',
    `summary` = 'Bút bi mực gel, thân nhựa trong suốt, viết mượt',
    `application` = 'Xanh dương'
WHERE `id_product` = 1001;

-- Thêm các sản phẩm bút bi khác (ví dụ)
INSERT INTO `product` (`id_product`, `product_name`, `summary`, `application`) VALUES
(1002, 'Bút bi TL-050', 'Bút bi dầu, thân nhựa màu, giá rẻ', 'Đen'),
(1003, 'Bút bi TL-100', 'Bút bi cao cấp, thân kim loại', 'Đỏ'),
(1004, 'Bút bi TL-Multi', 'Bút bi 4 màu, đa năng', 'Nhiều màu')
ON DUPLICATE KEY UPDATE `product_name` = VALUES(`product_name`);

-- Thêm các nguyên liệu cho bút bi
INSERT INTO `material` (`id_material`, `material_name`, `stock`) VALUES
(1002, 'Nhựa ABS', 10000),
(1003, 'Mực gel xanh', 5000),
(1004, 'Mực gel đen', 5000),
(1005, 'Bi kim loại 0.5mm', 2000),
(1006, 'Bi kim loại 0.7mm', 3000),
(1007, 'Bi kim loại 1.0mm', 2000),
(1008, 'Lò xo thép', 1000)
ON DUPLICATE KEY UPDATE `material_name` = VALUES(`material_name`);

-- ===================================================================
-- 5. TẠO TRIGGER ĐỂ TỰ ĐỘNG VALIDATE (TÙY CHỌN)
-- ===================================================================

-- Trigger để kiểm tra capacity máy móc phải > 0
DELIMITER $$
CREATE TRIGGER `validate_machine_capacity` 
BEFORE INSERT ON `machine`
FOR EACH ROW
BEGIN
    IF NEW.capacity <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Công suất máy phải lớn hơn 0 cái/giờ';
    END IF;
    
    -- Kiểm tra mc_status hợp lệ (1=Sẵn sàng, 2=Đang dùng, 3=Sự cố, 4=Bảo trì)
    IF NEW.mc_status NOT IN (1, 2, 3, 4) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Trạng thái máy không hợp lệ (1-4)';
    END IF;
END$$
DELIMITER ;

-- Trigger để kiểm tra stock nguyên liệu
DELIMITER $$
CREATE TRIGGER `validate_material_stock` 
BEFORE INSERT ON `material`
FOR EACH ROW
BEGIN
    IF NEW.stock < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Tồn kho không được âm';
    END IF;
END$$
DELIMITER ;

-- ===================================================================
-- 6. CẬP NHẬT AUTO_INCREMENT (Đảm bảo không trùng ID)
-- ===================================================================

ALTER TABLE `product` AUTO_INCREMENT = 1005;
ALTER TABLE `material` AUTO_INCREMENT = 1009;

-- ===================================================================
-- 7. TẠO VIEW ĐỂ DỄ XEM DỮ LIỆU VỚI ĐƠN VỊ
-- ===================================================================

-- View hiển thị máy móc với đơn vị rõ ràng
CREATE OR REPLACE VIEW `v_machine_status` AS
SELECT 
    `id_machine`,
    `machine_name`,
    CONCAT(`capacity`, ' cái/giờ') AS `capacity_display`,
    CASE `mc_status`
        WHEN 1 THEN 'Sẵn sàng'
        WHEN 2 THEN 'Đang sử dụng'
        WHEN 3 THEN 'Sự cố'
        WHEN 4 THEN 'Bảo trì'
        ELSE 'Không xác định'
    END AS `status_name`,
    `mc_status`
FROM `machine`;

-- View hiển thị nguyên liệu với đơn vị
CREATE OR REPLACE VIEW `v_material_stock` AS
SELECT 
    `id_material`,
    `material_name`,
    CONCAT(`stock`, ' gram') AS `stock_display`,
    `stock`
FROM `material`;

-- View hiển thị dự án với đơn vị
CREATE OR REPLACE VIEW `v_project_details` AS
SELECT 
    p.`id_project`,
    p.`project_name`,
    c.`cust_name`,
    pr.`product_name`,
    CONCAT(p.`diameter` / 10, ' mm') AS `diameter_display`,
    CONCAT(p.`qty_request`, ' cái') AS `qty_request_display`,
    p.`entry_date`,
    p.`pr_status`
FROM `project` p
LEFT JOIN `customer` c ON p.`id_cust` = c.`id_cust`
LEFT JOIN `product` pr ON p.`id_product` = pr.`id_product`;

-- ===================================================================
-- 8. KIỂM TRA DỮ LIỆU SAU KHI MIGRATION
-- ===================================================================

SELECT '=== KIỂM TRA DỮ LIỆU SAU MIGRATION ===' AS '';

SELECT 'Máy móc:' AS '';
SELECT * FROM `v_machine_status`;

SELECT 'Nguyên liệu:' AS '';
SELECT * FROM `v_material_stock`;

SELECT 'Dự án:' AS '';
SELECT * FROM `v_project_details`;

-- ===================================================================
-- LƯU Ý QUAN TRỌNG:
-- ===================================================================
-- 1. Đơn vị diameter: Hiện lưu INT, nên lưu 7 cho 0.7mm, 10 cho 1.0mm
--    Hoặc đổi sang DECIMAL(3,1) để lưu 0.7, 1.0 trực tiếp
-- 
-- 2. Cập nhật lại tất cả dữ liệu cũ theo tỷ lệ:
--    - 1 Kg bút bi ≈ 200-300 cái (tùy loại)
--    - Material: 1 Kg = 1000 gram
--
-- 3. Trạng thái máy móc (mc_status):
--    1 = Sẵn sàng (màu xanh)
--    2 = Đang sử dụng (màu vàng)
--    3 = Sự cố (màu đỏ)
--    4 = Bảo trì (màu xanh dương) - MỚI THÊM
--
-- 4. Backup database trước khi chạy migration này!
--    mysqldump -u root -p db_production > backup_before_migration.sql
-- ===================================================================

COMMIT;
