-- Explicit sample seed for shift_machine_reports and shift_machine_events
-- This file inserts predictable rows for local testing.
-- Adjust id_shift / id_machine values if your DB uses different IDs.

USE `db_production`;

START TRANSACTION;

-- Insert two sample machine reports for plan_shift 1001 (exists in db_production.sql)
INSERT INTO `shift_machine_reports` (`id_shift`, `id_machine`, `produced_qty`, `target_qty`, `downtime_seconds`, `events`, `status`, `last_updated_at`, `recorded_by`, `created_at`)
VALUES
(1001, 1001, 1200, 1500, 900, NULL, 'running', NOW(), 1001, NOW()),
(1001, 1002, 900, 1500, 1800, NULL, 'running', NOW(), 1001, NOW());

-- Insert sample event rows for those machines
INSERT INTO `shift_machine_events` (`id_shift`, `id_machine`, `event_type`, `detail`, `ts`, `created_by`)
VALUES
(1001, 1001, 'hư hỏng', 'thời gian đổi máy 15 phút', NOW() - INTERVAL 1 HOUR, 1001),
(1001, 1002, 'bảo trì', 'kiểm tra định kỳ', NOW() - INTERVAL 2 HOUR, 1001);

COMMIT;

-- Quick verification selects (optional):
-- SELECT * FROM shift_machine_reports WHERE id_shift = 1001;
-- SELECT * FROM shift_machine_events WHERE id_shift = 1001;
