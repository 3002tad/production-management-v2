-- Migration 006: Update `material` table to add new fields for warehouse details
-- Adds: material_type, unit, supplier, date_entry, attachment, created_at, updated_at
-- Then backfills sample values for existing material rows (from screenshot)

USE `db_production`;

SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;

-- Add new columns if they do not exist
ALTER TABLE `material`
  ADD COLUMN IF NOT EXISTS `material_type` VARCHAR(100) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `unit` VARCHAR(32) DEFAULT 'cái',
  ADD COLUMN IF NOT EXISTS `supplier` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `date_entry` DATE DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `attachment` VARCHAR(512) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP NULL DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;

-- Backfill sample values for existing materials (IDs taken from the screenshot / current DB)
-- Adjust dates/suppliers to match your real data as needed.

UPDATE `material` SET
  `material_type` = 'Raw Material',
  `unit` = 'cái',
  `supplier` = 'Công ty ABC',
  `date_entry` = '2025-10-01',
  `stock` = 500,
  `attachment` = NULL,
  `updated_at` = NOW()
WHERE `id_material` = 1001;

UPDATE `material` SET
  `material_type` = 'Raw Material',
  `unit` = 'kg',
  `supplier` = 'Nhà cung cấp 1',
  `date_entry` = '2025-09-20',
  `stock` = 100,
  `attachment` = NULL,
  `updated_at` = NOW()
WHERE `id_material` = 1002;

UPDATE `material` SET
  `material_type` = 'Ink',
  `unit` = 'ml',
  `supplier` = 'Nhà cung cấp Mực',
  `date_entry` = '2025-10-30',
  `stock` = 1000,
  `attachment` = NULL,
  `updated_at` = NOW()
WHERE `id_material` = 1003;

UPDATE `material` SET
  `material_type` = 'Ink',
  `unit` = 'ml',
  `supplier` = 'Nhà cung cấp Mực',
  `date_entry` = '2025-09-22',
  `stock` = 5000,
  `attachment` = NULL,
  `updated_at` = NOW()
WHERE `id_material` = 1004;

UPDATE `material` SET
  `material_type` = 'Ball',
  `unit` = 'cái',
  `supplier` = 'Công ty Bi Kim',
  `date_entry` = '2025-08-15',
  `stock` = 200,
  `attachment` = NULL,
  `updated_at` = NOW()
WHERE `id_material` = 1005;

UPDATE `material` SET
  `material_type` = 'Ball',
  `unit` = 'cái',
  `supplier` = 'Công ty Bi Kim',
  `date_entry` = '2025-08-15',
  `stock` = 300,
  `attachment` = NULL,
  `updated_at` = NOW()
WHERE `id_material` = 1006;

UPDATE `material` SET
  `material_type` = 'Ball',
  `unit` = 'cái',
  `supplier` = 'Công ty Bi Kim',
  `date_entry` = '2025-08-15',
  `stock` = 200,
  `attachment` = NULL,
  `updated_at` = NOW()
WHERE `id_material` = 1007;

UPDATE `material` SET
  `material_type` = 'Spring',
  `unit` = 'cái',
  `supplier` = 'Nhà cung cấp Lò xo',
  `date_entry` = '2025-07-30',
  `stock` = 2000,
  `attachment` = NULL,
  `updated_at` = NOW()
WHERE `id_material` = 1008;

UPDATE `material` SET
  `material_type` = 'Raw Material',
  `unit` = 'cái',
  `supplier` = 'Công Ti B',
  `date_entry` = '2025-10-30',
  `stock` = 10,
  `attachment` = NULL,
  `updated_at` = NOW()
WHERE `id_material` = 1009;

-- If you want to auto-fill created_at for existing rows where it was null:
UPDATE `material` SET `created_at` = NOW() WHERE `created_at` IS NULL;

COMMIT;
SET FOREIGN_KEY_CHECKS = 1;

-- End of migration 006
