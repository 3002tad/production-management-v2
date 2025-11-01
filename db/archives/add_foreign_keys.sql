-- ============================================
-- ADD FOREIGN KEY CONSTRAINTS (RELATIONSHIPS)
-- Production Management System - Database Schema
-- Created: 27/10/2025
-- ============================================

USE `db_production`;

-- Tắt foreign key checks tạm thời để tránh lỗi khi thêm
SET FOREIGN_KEY_CHECKS=0;

-- ============================================
-- 1. TABLE: project
-- Relationships: customer, product
-- ============================================

-- project.id_cust -> customer.id_cust
ALTER TABLE `project`
ADD CONSTRAINT `fk_project_customer` 
FOREIGN KEY (`id_cust`) 
REFERENCES `customer`(`id_cust`) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;

-- project.id_product -> product.id_product
ALTER TABLE `project`
ADD CONSTRAINT `fk_project_product` 
FOREIGN KEY (`id_product`) 
REFERENCES `product`(`id_product`) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;

-- ============================================
-- 2. TABLE: planning
-- Relationships: project
-- ============================================

-- planning.id_project -> project.id_project
ALTER TABLE `planning`
ADD CONSTRAINT `fk_planning_project` 
FOREIGN KEY (`id_project`) 
REFERENCES `project`(`id_project`) 
ON DELETE CASCADE 
ON UPDATE CASCADE;

-- ============================================
-- 3. TABLE: plan_shift
-- Relationships: planning, shiftment, staff
-- ============================================

-- plan_shift.id_plan -> planning.id_plan
ALTER TABLE `plan_shift`
ADD CONSTRAINT `fk_planshift_planning` 
FOREIGN KEY (`id_plan`) 
REFERENCES `planning`(`id_plan`) 
ON DELETE CASCADE 
ON UPDATE CASCADE;

-- plan_shift.id_shift -> shiftment.id_shift
ALTER TABLE `plan_shift`
ADD CONSTRAINT `fk_planshift_shift` 
FOREIGN KEY (`id_shift`) 
REFERENCES `shiftment`(`id_shift`) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;

-- plan_shift.id_staff -> staff.id_staff
ALTER TABLE `plan_shift`
ADD CONSTRAINT `fk_planshift_staff` 
FOREIGN KEY (`id_staff`) 
REFERENCES `staff`(`id_staff`) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;

-- ============================================
-- 4. TABLE: p_machine
-- Relationships: plan_shift, machine
-- ============================================

-- p_machine.id_planshift -> plan_shift.id_planshift
ALTER TABLE `p_machine`
ADD CONSTRAINT `fk_pmachine_planshift` 
FOREIGN KEY (`id_planshift`) 
REFERENCES `plan_shift`(`id_planshift`) 
ON DELETE CASCADE 
ON UPDATE CASCADE;

-- p_machine.id_machine -> machine.id_machine
ALTER TABLE `p_machine`
ADD CONSTRAINT `fk_pmachine_machine` 
FOREIGN KEY (`id_machine`) 
REFERENCES `machine`(`id_machine`) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;

-- ============================================
-- 5. TABLE: p_material
-- Relationships: plan_shift, material
-- ============================================

-- p_material.id_planshift -> plan_shift.id_planshift
ALTER TABLE `p_material`
ADD CONSTRAINT `fk_pmaterial_planshift` 
FOREIGN KEY (`id_planshift`) 
REFERENCES `plan_shift`(`id_planshift`) 
ON DELETE CASCADE 
ON UPDATE CASCADE;

-- p_material.id_material -> material.id_material
ALTER TABLE `p_material`
ADD CONSTRAINT `fk_pmaterial_material` 
FOREIGN KEY (`id_material`) 
REFERENCES `material`(`id_material`) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;

-- ============================================
-- 6. TABLE: sorting_report
-- Relationships: plan_shift
-- ============================================

-- sorting_report.id_planshift -> plan_shift.id_planshift
ALTER TABLE `sorting_report`
ADD CONSTRAINT `fk_sorting_planshift` 
FOREIGN KEY (`id_planshift`) 
REFERENCES `plan_shift`(`id_planshift`) 
ON DELETE CASCADE 
ON UPDATE CASCADE;

-- ============================================
-- 7. TABLE: finished_report
-- Relationships: project
-- ============================================

-- finished_report.id_project -> project.id_project
ALTER TABLE `finished_report`
ADD CONSTRAINT `fk_finished_project` 
FOREIGN KEY (`id_project`) 
REFERENCES `project`(`id_project`) 
ON DELETE CASCADE 
ON UPDATE CASCADE;

-- Bật lại foreign key checks
SET FOREIGN_KEY_CHECKS=1;

-- ============================================
-- VERIFICATION: Kiểm tra Foreign Keys
-- ============================================

-- Liệt kê tất cả foreign keys trong database
SELECT 
    TABLE_NAME,
    CONSTRAINT_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE
    REFERENCED_TABLE_SCHEMA = 'db_production'
    AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY
    TABLE_NAME, CONSTRAINT_NAME;

-- ============================================
-- DATABASE RELATIONSHIPS SUMMARY
-- ============================================
/*

RELATIONSHIP STRUCTURE:

1. customer (1) -> (N) project
   - Một khách hàng có nhiều dự án
   - ON DELETE RESTRICT: Không xóa được customer nếu còn project

2. product (1) -> (N) project
   - Một sản phẩm có thể trong nhiều dự án
   - ON DELETE RESTRICT: Không xóa được product nếu còn project

3. project (1) -> (N) planning
   - Một dự án có nhiều kế hoạch sản xuất
   - ON DELETE CASCADE: Xóa project sẽ xóa tất cả planning

4. project (1) -> (N) finished_report
   - Một dự án có nhiều báo cáo hoàn thành
   - ON DELETE CASCADE: Xóa project sẽ xóa tất cả finished_report

5. planning (1) -> (N) plan_shift
   - Một kế hoạch có nhiều ca sản xuất
   - ON DELETE CASCADE: Xóa planning sẽ xóa tất cả plan_shift

6. shiftment (1) -> (N) plan_shift
   - Một ca làm việc có thể trong nhiều plan
   - ON DELETE RESTRICT: Không xóa được shift nếu đang được dùng

7. staff (1) -> (N) plan_shift
   - Một nhân viên có thể nhiều ca làm việc
   - ON DELETE RESTRICT: Không xóa được staff nếu đang được gán

8. plan_shift (1) -> (N) p_machine
   - Một plan_shift sử dụng nhiều máy móc
   - ON DELETE CASCADE: Xóa plan_shift sẽ xóa tất cả p_machine

9. plan_shift (1) -> (N) p_material
   - Một plan_shift sử dụng nhiều nguyên liệu
   - ON DELETE CASCADE: Xóa plan_shift sẽ xóa tất cả p_material

10. plan_shift (1) -> (N) sorting_report
    - Một plan_shift có nhiều báo cáo phân loại
    - ON DELETE CASCADE: Xóa plan_shift sẽ xóa tất cả sorting_report

11. machine (1) -> (N) p_machine
    - Một máy móc có thể được dùng nhiều lần
    - ON DELETE RESTRICT: Không xóa được machine nếu đang được dùng

12. material (1) -> (N) p_material
    - Một nguyên liệu có thể được dùng nhiều lần
    - ON DELETE RESTRICT: Không xóa được material nếu đang được dùng

FOREIGN KEY POLICIES:

- ON DELETE RESTRICT: 
  Ngăn không cho xóa bản ghi cha nếu còn bản ghi con
  Áp dụng cho: customer, product, machine, material, shiftment, staff
  Lý do: Dữ liệu master, không nên xóa khi đang được tham chiếu

- ON DELETE CASCADE: 
  Tự động xóa tất cả bản ghi con khi xóa bản ghi cha
  Áp dụng cho: planning, plan_shift, p_machine, p_material, sorting_report, finished_report
  Lý do: Dữ liệu transaction, phụ thuộc vào cha

- ON UPDATE CASCADE: 
  Tự động cập nhật khóa ngoại khi khóa chính thay đổi
  Áp dụng cho: Tất cả foreign keys
  Lý do: Đảm bảo tính nhất quán dữ liệu

*/

-- ============================================
-- ROLLBACK (NẾU CẦN XÓA TẤT CẢ FOREIGN KEYS)
-- ============================================
/*
SET FOREIGN_KEY_CHECKS=0;

ALTER TABLE `project` DROP FOREIGN KEY `fk_project_customer`;
ALTER TABLE `project` DROP FOREIGN KEY `fk_project_product`;
ALTER TABLE `planning` DROP FOREIGN KEY `fk_planning_project`;
ALTER TABLE `plan_shift` DROP FOREIGN KEY `fk_planshift_planning`;
ALTER TABLE `plan_shift` DROP FOREIGN KEY `fk_planshift_shift`;
ALTER TABLE `plan_shift` DROP FOREIGN KEY `fk_planshift_staff`;
ALTER TABLE `p_machine` DROP FOREIGN KEY `fk_pmachine_planshift`;
ALTER TABLE `p_machine` DROP FOREIGN KEY `fk_pmachine_machine`;
ALTER TABLE `p_material` DROP FOREIGN KEY `fk_pmaterial_planshift`;
ALTER TABLE `p_material` DROP FOREIGN KEY `fk_pmaterial_material`;
ALTER TABLE `sorting_report` DROP FOREIGN KEY `fk_sorting_planshift`;
ALTER TABLE `finished_report` DROP FOREIGN KEY `fk_finished_project`;

SET FOREIGN_KEY_CHECKS=1;
*/

COMMIT;
