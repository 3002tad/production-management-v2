SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

START TRANSACTION;

-- Sửa lại gán line_id cho machines dựa trên dữ liệu location hiện có
-- Mapping từ location cũ:
-- 'Khu A - Line 1' → LINE01 (Dây chuyền 1) - có ML001
-- 'Khu A - Line 2' → LINE01 (Dây chuyền 1) - có ML002  
-- 'Khu B - Line 1' → LINE01 (Dây chuyền 1) - có AS001
-- 'Khu B - Line 2' → LINE02 (Dây chuyền 2) - có AS002, PK001
-- 'Khu D - QC'     → LINE02 (Dây chuyền 2) - có QC001

-- Gán máy vào Dây chuyền 1 (LINE01)
UPDATE `machines` 
SET `line_id` = (SELECT id FROM production_lines WHERE line_code = 'LINE01' LIMIT 1)
WHERE `code` IN ('ML001', 'ML002', 'AS001');

-- Gán máy vào Dây chuyền 2 (LINE02)
UPDATE `machines` 
SET `line_id` = (SELECT id FROM production_lines WHERE line_code = 'LINE02' LIMIT 1)
WHERE `code` IN ('AS002', 'PK001', 'QC001');

-- Xác nhận cập nhật zone_id cho production_lines
-- Cả 2 dây chuyền đều trong Khu A
UPDATE `production_lines` 
SET `zone_id` = (SELECT zone_id FROM zones WHERE zone_code = 'ZONE_A' LIMIT 1)
WHERE `line_code` IN ('LINE01', 'LINE02');

-- Kiểm tra kết quả mapping
SELECT 
    z.zone_code,
    z.zone_name,
    pl.line_code,
    pl.line_name,
    m.code as machine_code,
    m.name as machine_name,
    m.location as old_location
FROM zones z
LEFT JOIN production_lines pl ON z.zone_id = pl.zone_id
LEFT JOIN machines m ON pl.id = m.line_id
ORDER BY z.zone_id, pl.id, m.code;

COMMIT;
