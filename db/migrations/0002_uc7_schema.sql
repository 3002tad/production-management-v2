-- Migration: Add UC7 schema
-- 1) finished_stock
-- 2) capacity_config
-- 3) add bom to product and diameter
-- 4) extend material table
-- 5) add project columns for warnings/allocations

-- 1) finished_stock
CREATE TABLE IF NOT EXISTS `finished_stock` (
  `id_product` INT(25) NOT NULL,
  `quantity_in_stock` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_product`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2) capacity_config
CREATE TABLE IF NOT EXISTS `capacity_config` (
  `id_capacity_config` INT(11) NOT NULL AUTO_INCREMENT,
  `level` INT(2) NOT NULL,
  `hours_per_shift` INT(3) NOT NULL DEFAULT 8,
  `shifts_per_day` INT(2) NOT NULL DEFAULT 2,
  `efficiency_rate` DECIMAL(4,2) NOT NULL DEFAULT 0.80,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_capacity_config`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3) add bom and diameter to product
ALTER TABLE `product` 
  ADD COLUMN IF NOT EXISTS `diameter` DECIMAL(4,1) DEFAULT 0.5,
  ADD COLUMN IF NOT EXISTS `bom` LONGTEXT NULL;

-- 4) extend material
ALTER TABLE `material`
  ADD COLUMN IF NOT EXISTS `uom` VARCHAR(10) DEFAULT 'g',
  ADD COLUMN IF NOT EXISTS `min_stock` INT(11) DEFAULT 0;

-- 5) extend project table
ALTER TABLE `project`
  ADD COLUMN IF NOT EXISTS `warning_flag` TINYINT(1) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `warning_type` VARCHAR(50) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `warning_details` LONGTEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `finished_stock_available` INT(11) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `material_shifts_available` INT(11) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `stock_allocation` LONGTEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `capacity_level_used` INT(2) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN IF NOT EXISTS `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP;

-- Useful indexes
ALTER TABLE `project` ADD INDEX IF NOT EXISTS `idx_project_product` (`id_product`);
ALTER TABLE `product` ADD INDEX IF NOT EXISTS `idx_product_name` (`product_name`(50));
