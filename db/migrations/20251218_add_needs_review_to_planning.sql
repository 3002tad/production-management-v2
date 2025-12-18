-- Migration: 20251218_add_needs_review_to_planning.sql
-- Add a lightweight flag `needs_review` to planning to mark plans that need manual review
-- when the related project is modified after plan approval.

START TRANSACTION;

ALTER TABLE `planning`
  ADD COLUMN IF NOT EXISTS `needs_review` TINYINT(1) NOT NULL DEFAULT 0;

-- Add an index for faster queries when listing/filtering plans
ALTER TABLE `planning`
  ADD INDEX IF NOT EXISTS `idx_planning_needs_review` (`needs_review`);

COMMIT;

-- Notes:
-- - If your MySQL/MariaDB does not support "ADD COLUMN IF NOT EXISTS" or "ADD INDEX IF NOT EXISTS",
--   run the equivalent ALTER TABLE only after checking INFORMATION_SCHEMA.COLUMNS and INFORMATION_SCHEMA.STATISTICS.
-- - This migration is intentionally small and backward compatible. The code already falls back to writing an audit_log
--   entry when this column is not present; after applying this migration, the code will set `needs_review` directly.

-- To apply:
-- mysql -u <user> -p <db> < db/migrations/20251218_add_needs_review_to_planning.sql