-- Seed data for UC7
-- Insert capacity levels (level 1 & level 2)
INSERT INTO `capacity_config` (`level`, `hours_per_shift`, `shifts_per_day`, `efficiency_rate`, `is_active`) VALUES
(1, 8, 2, 0.80, 1),
(2, 12, 2, 0.85, 1);

-- Insert finished_stock sample for product 1001 (from UI test docs, use 35 as example)
INSERT INTO `finished_stock` (`id_product`, `quantity_in_stock`) VALUES
(1001, 35)
ON DUPLICATE KEY UPDATE quantity_in_stock = VALUES(quantity_in_stock);

-- Add BOM for product 1001 (linking to material 1001 from db_production.sql)
UPDATE `product` SET `bom` = '[{"id_material":1001,"material_name":"Test Matereal","quantity_per_unit":10,"unit":"g"}]', `diameter` = 0.7 WHERE id_product = 1001;

-- Ensure material 1001 has uom and min_stock
UPDATE `material` SET `uom` = COALESCE(`uom`, 'g'), `min_stock` = COALESCE(`min_stock`, 0) WHERE id_material = 1001;

-- Example: refresh existing projects to have initial warnings (do not run automatically here)
-- For manual run: call: php index.php cron refreshWarnings or use OrderModel::refreshAllWarnings()
