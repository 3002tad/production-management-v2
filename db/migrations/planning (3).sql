SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
START TRANSACTION;

ALTER TABLE `planning`
  ADD COLUMN IF NOT EXISTS `start_date` DATE DEFAULT NULL AFTER `pl_status`,
  ADD COLUMN IF NOT EXISTS `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN IF NOT EXISTS `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  ADD COLUMN IF NOT EXISTS `note` TEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `suggested_shifts` INT(11) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `materials` LONGTEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `lines` LONGTEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `qty_target` INT(11) DEFAULT 0;

UPDATE `planning` SET
  plan_name = 'Plan-test',
  id_project = 1001,
  qty_target = 2000,
  end_date = '2023-11-14',
  pl_status = 1,
  updated_at = CURRENT_TIMESTAMP
WHERE id_plan = 1001;

INSERT IGNORE INTO `planning`
(`id_plan`, `plan_name`, `id_project`, `start_date`, `end_date`, `pl_status`,
 `created_at`, `updated_at`, `note`, `suggested_shifts`, `materials`, `lines`, `qty_target`)
VALUES
(
  1087,
  'KH-1001-1765116526',
  1001,
  '2023-11-04',
  '2023-11-06',
  1,
  '2025-12-07 21:08:46',
  '2025-12-07 21:08:46',
  NULL,
  4,
  '["Bi kim loại 0.7mm — Yêu cầu: 9,965 — Thiếu: 6,965","Lò xo thép — Yêu cầu: 9,965 — Thiếu: 8,965"]',
  'Dây chuyền 1 (công suất: 500.00)',
  9965
);

INSERT IGNORE INTO `planning`
(`id_plan`, `plan_name`, `id_project`, `start_date`, `end_date`, `pl_status`,
 `created_at`, `updated_at`, `note`, `suggested_shifts`, `materials`, `lines`, `qty_target`)
VALUES
(
  1088,
  'test04',
  1005,
  '2026-11-08',
  '2026-11-11',
  1,
  '2025-12-14 14:05:37',
  '2025-12-14 14:05:37',
  NULL,
  2,
  '["Lò xo thép — Yêu cầu: 10,000 — Thiếu: 9,000"]',
  'Dây chuyền 2 (công suất: 1000.00)',
  10000
);

COMMIT;