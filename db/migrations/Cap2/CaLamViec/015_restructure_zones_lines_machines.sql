-- Migration 015: Xác định lại cấu trúc Zone - Line - Machine
-- Purpose: 
--   1. Mỗi zone cố định 2 line chính (sản xuất thô, lắp ráp-QC) + 1 line ảo
--   2. Thêm máy backup để quản lý
-- Author: GitHub Copilot
-- Date: 2025-12-18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

START TRANSACTION;

-- ==================== 1. CẬP NHẬT BẢNG PRODUCTION_LINES ====================

-- Thêm cột line_type để phân biệt line thường và line ảo
SET @column_exists_line_type = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'production_lines' 
    AND COLUMN_NAME = 'line_type'
);

SET @sql_add_line_type = IF(
    @column_exists_line_type = 0,
    'ALTER TABLE `production_lines` 
     ADD COLUMN `line_type` ENUM(''production_raw'', ''assembly_qc'', ''virtual'') NOT NULL DEFAULT ''production_raw'' 
     COMMENT ''production_raw=Sản xuất thô, assembly_qc=Lắp ráp-QC, virtual=Line ảo điều phối'' 
     AFTER `line_name`',
    'SELECT ''Column line_type already exists'' AS message'
);

PREPARE stmt FROM @sql_add_line_type;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Thêm cột is_primary để đánh dấu line chính (để sau này không xóa nhầm)
SET @column_exists_is_primary = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'production_lines' 
    AND COLUMN_NAME = 'is_primary'
);

SET @sql_add_is_primary = IF(
    @column_exists_is_primary = 0,
    'ALTER TABLE `production_lines` 
     ADD COLUMN `is_primary` TINYINT(1) NOT NULL DEFAULT 0 
     COMMENT ''1=Line chính (cố định), 0=Line phụ/ảo (có thể xóa)'' 
     AFTER `line_type`',
    'SELECT ''Column is_primary already exists'' AS message'
);

PREPARE stmt FROM @sql_add_is_primary;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ==================== 2. CẬP NHẬT BẢNG MACHINES ====================

-- Thêm cột machine_role để phân biệt máy chính và máy backup
SET @column_exists_machine_role = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'machines' 
    AND COLUMN_NAME = 'machine_role'
);

SET @sql_add_machine_role = IF(
    @column_exists_machine_role = 0,
    'ALTER TABLE `machines` 
     ADD COLUMN `machine_role` ENUM(''primary'', ''backup'') NOT NULL DEFAULT ''primary'' 
     COMMENT ''primary=Máy chính, backup=Máy dự phòng'' 
     AFTER `status`',
    'SELECT ''Column machine_role already exists'' AS message'
);

PREPARE stmt FROM @sql_add_machine_role;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Thêm index cho machine_role
SET @index_exists_machine_role = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'machines' 
    AND INDEX_NAME = 'idx_machine_role'
);

SET @sql_add_index_machine_role = IF(
    @index_exists_machine_role = 0,
    'ALTER TABLE `machines` ADD INDEX `idx_machine_role` (`machine_role`)',
    'SELECT ''Index idx_machine_role already exists'' AS message'
);

PREPARE stmt FROM @sql_add_index_machine_role;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ==================== 3. CẬP NHẬT DỮ LIỆU MẪU ====================

-- Cập nhật Zone A là zone đang hoạt động
UPDATE `zones` SET 
    `status` = 1,
    `description` = 'Khu sản xuất chính - Đang hoạt động'
WHERE `zone_code` = 'ZONE_A';

-- Cập nhật Zone B, C là dự trữ
UPDATE `zones` SET 
    `status` = 0,
    `description` = 'Khu dự trữ - Chờ mở rộng sản xuất'
WHERE `zone_code` IN ('ZONE_B', 'ZONE_C');

-- Cập nhật 2 line chính của Zone A
UPDATE `production_lines` SET 
    `line_type` = 'production_raw',
    `is_primary` = 1,
    `line_name` = 'Dây chuyền sản xuất thô',
    `description` = 'Line chính 1: Sản xuất thô (ép nhựa, đúc...)'
WHERE `line_code` = 'LINE01' AND `zone_id` = 1;

UPDATE `production_lines` SET 
    `line_type` = 'assembly_qc',
    `is_primary` = 1,
    `line_name` = 'Dây chuyền lắp ráp - QC',
    `description` = 'Line chính 2: Lắp ráp và kiểm định chất lượng'
WHERE `line_code` = 'LINE02' AND `zone_id` = 1;

-- Tạo line ảo nếu chưa có (LINE03)
INSERT IGNORE INTO `production_lines` 
    (`line_code`, `line_name`, `line_type`, `is_primary`, `zone_id`, `description`, `capacity_per_hour`, `status`) 
VALUES 
    ('LINE_VIRTUAL_01', 'Line điều phối ảo', 'virtual', 0, 1, 'Line ảo để điều phối khi ép công suất hoặc sự cố lớn', 0, 1);

-- Đánh dấu một số máy mẫu là backup (nếu có máy)
-- Giả sử máy có id > 10 là máy backup
UPDATE `machines` SET 
    `machine_role` = 'backup',
    `description` = CONCAT(IFNULL(`description`, ''), ' - Máy dự phòng')
WHERE `id` > 10 AND `status` = 'active';

-- ==================== 4. THÊM CONSTRAINTS (Tùy chọn) ====================

-- Đảm bảo mỗi zone chỉ có tối đa 1 line ảo
-- (Có thể thêm trigger hoặc check trong application logic)

COMMIT;

-- ==================== 5. VERIFICATION QUERIES ====================
-- Uncomment để kiểm tra sau khi chạy migration

-- SELECT * FROM zones;
-- SELECT zone_id, line_code, line_name, line_type, is_primary, status FROM production_lines ORDER BY zone_id, is_primary DESC;
-- SELECT id, code, name, machine_role, status, line_id FROM machines ORDER BY line_id, machine_role;

-- Kiểm tra số lượng line mỗi zone
-- SELECT 
--     z.zone_code, 
--     z.zone_name,
--     COUNT(pl.id) as total_lines,
--     SUM(CASE WHEN pl.line_type = 'production_raw' THEN 1 ELSE 0 END) as raw_lines,
--     SUM(CASE WHEN pl.line_type = 'assembly_qc' THEN 1 ELSE 0 END) as assembly_lines,
--     SUM(CASE WHEN pl.line_type = 'virtual' THEN 1 ELSE 0 END) as virtual_lines
-- FROM zones z
-- LEFT JOIN production_lines pl ON pl.zone_id = z.zone_id
-- GROUP BY z.zone_id;

-- Kiểm tra phân bổ máy
-- SELECT 
--     pl.line_code,
--     pl.line_name,
--     COUNT(m.id) as total_machines,
--     SUM(CASE WHEN m.machine_role = 'primary' THEN 1 ELSE 0 END) as primary_machines,
--     SUM(CASE WHEN m.machine_role = 'backup' THEN 1 ELSE 0 END) as backup_machines
-- FROM production_lines pl
-- LEFT JOIN machines m ON m.line_id = pl.id
-- GROUP BY pl.id;
