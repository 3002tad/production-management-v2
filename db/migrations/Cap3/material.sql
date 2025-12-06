-- Check if the primary key exists on 'id_material', if not, add it
-- This checks for existing primary key and only adds it if not present
-- Drop existing primary key if needed, and add the primary key to `id_material`
ALTER TABLE `material`
  DROP PRIMARY KEY, 
  ADD PRIMARY KEY (`id_material`);

-- Check if the 'material_name' column exists, if not, add it
ALTER TABLE `material`
  ADD COLUMN IF NOT EXISTS `material_name` VARCHAR(50) NOT NULL;

-- Check if the 'stock' column exists, if not, add it
ALTER TABLE `material`
  ADD COLUMN IF NOT EXISTS `stock` INT(50) NOT NULL;

-- Check if the 'min_stock' column exists, if not, add it
ALTER TABLE `material`
  ADD COLUMN IF NOT EXISTS `min_stock` INT(11) DEFAULT 0;

-- Check if the 'uom' column exists, if not, add it
ALTER TABLE `material`
  ADD COLUMN IF NOT EXISTS `uom` VARCHAR(10) NOT NULL DEFAULT 'g';

-- Check if the 'material_type' column exists, if not, add it
ALTER TABLE `material`
  ADD COLUMN IF NOT EXISTS `material_type` VARCHAR(100) DEFAULT NULL;

-- Check if the 'supplier' column exists, if not, add it
ALTER TABLE `material`
  ADD COLUMN IF NOT EXISTS `supplier` VARCHAR(255) DEFAULT NULL;

-- Check if the 'date_entry' column exists, if not, add it
ALTER TABLE `material`
  ADD COLUMN IF NOT EXISTS `date_entry` DATE DEFAULT NULL;

-- Check if the 'attachment' column exists, if not, add it
ALTER TABLE `material`
  ADD COLUMN IF NOT EXISTS `attachment` VARCHAR(512) DEFAULT NULL;

-- Check if the 'created_at' column exists, if not, add it
ALTER TABLE `material`
  ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP NULL DEFAULT NULL;

-- Check if the 'updated_at' column exists, if not, add it
ALTER TABLE `material`
  ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;

-- Check if the index for 'stock' and 'material_name' exists, if not, add it
ALTER TABLE `material`
  ADD INDEX IF NOT EXISTS `idx_material_stock` (`stock`, `material_name`);
