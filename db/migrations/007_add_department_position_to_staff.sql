-- Migration: 007_add_department_position_to_staff.sql
-- Thêm trường department và position vào bảng staff (nếu chưa có)
-- Cập nhật dữ liệu hiện tại dựa trên tên nhân viên

-- An toàn: Chỉ thêm cột nếu chưa tồn tại
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'staff' AND COLUMN_NAME = 'department') = 0,
    'ALTER TABLE `staff` ADD COLUMN `department` VARCHAR(100) DEFAULT NULL AFTER `email`;',
    'SELECT "Column department already exists - skipping" as message;'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'staff' AND COLUMN_NAME = 'position') = 0,
    'ALTER TABLE `staff` ADD COLUMN `position` VARCHAR(100) DEFAULT NULL AFTER `department`;',
    'SELECT "Column position already exists - skipping" as message;'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Cập nhật dữ liệu department và position dựa trên tên nhân viên
-- Ban Giám Đốc
UPDATE `staff` SET `department` = 'Ban Giám Đốc', `position` = 'Giám Đốc'
WHERE `staff_name` LIKE '%Giám Đốc%' OR `staff_name` LIKE '%BOD%' OR `staff_name` LIKE '%Ban Giám Đốc%';

-- Trưởng dây chuyền / Line Manager
UPDATE `staff` SET `department` = 'Sản Xuất', `position` = 'Trưởng Dây Chuyền'
WHERE `staff_name` LIKE '%Trưởng dây chuyền%' OR `staff_name` LIKE '%Trưởng line%' OR `staff_name` LIKE '%Line Manager%';

-- Nhân viên kho / Warehouse
UPDATE `staff` SET `department` = 'Kho', `position` = 'Nhân Viên Kho'
WHERE `staff_name` LIKE '%Nhân viên kho%' OR `staff_name` LIKE '%Warehouse%' OR `staff_name` LIKE '%kho%';

-- Nhân viên QC
UPDATE `staff` SET `department` = 'QC', `position` = 'Nhân Viên QC'
WHERE `staff_name` LIKE '%Nhân viên QC%' OR `staff_name` LIKE '%QC%' OR `staff_name` LIKE '%kiểm tra chất lượng%';

-- Kỹ thuật viên / Technical Staff
UPDATE `staff` SET `department` = 'Kỹ Thuật', `position` = 'Kỹ Thuật Viên'
WHERE `staff_name` LIKE '%Kỹ thuật viên%' OR `staff_name` LIKE '%Technical%' OR `staff_name` LIKE '%kỹ thuật%';

-- Công nhân / Worker
UPDATE `staff` SET `department` = 'Sản Xuất', `position` = 'Công Nhân'
WHERE `staff_name` LIKE '%Công nhân%' OR `staff_name` LIKE '%Worker%' OR `staff_name` LIKE '%công nhân%';

-- Administrator
UPDATE `staff` SET `department` = 'IT', `position` = 'Administrator'
WHERE `staff_name` LIKE '%Administrator%' OR `staff_name` LIKE '%Admin%';

-- Leader (có thể là trưởng dây chuyền hoặc quản lý)
UPDATE `staff` SET `department` = 'Sản Xuất', `position` = 'Leader'
WHERE `staff_name` LIKE '%Leader%' AND `department` IS NULL;

-- Cập nhật các record còn lại chưa được phân loại
UPDATE `staff` SET `department` = 'Chưa Phân Loại', `position` = 'Chưa Phân Loại'
WHERE `department` IS NULL OR `position` IS NULL;