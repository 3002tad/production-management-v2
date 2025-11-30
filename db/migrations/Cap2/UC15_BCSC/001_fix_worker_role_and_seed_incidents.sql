-- =====================================================
-- CAP2 MIGRATION UC15_BCSC 001: Fix Worker Role & Seed Incident Data
-- Description: 
--   1. Fix worker users role from 'admin' to 'worker'
--   2. Insert sample incident reports for testing
-- Author: Production Management Team
-- Date: 2025-11-30
-- Module: UC15_BCSC (Worker Incident Management)
-- =====================================================

USE `db_production`;

-- =====================================================
-- STEP 1: Fix Worker Role in User Table
-- =====================================================
-- Worker users had role='admin' from migration, fix to 'worker'
UPDATE `user` 
SET `role` = 'worker' 
WHERE `username` = 'worker' AND `role_id` = 7 AND `role` != 'worker';

-- Verify the fix
SELECT 'Worker Role Fix:' AS '';
SELECT `user_id`, `username`, `role_id`, `role`, `is_active` 
FROM `user` 
WHERE `role_id` = 7 
ORDER BY `user_id`;

-- =====================================================
-- STEP 2: Insert Sample Incident Reports
-- =====================================================
-- Purpose: Provide test data for worker incident management testing
-- Worker users: user_id=7, 12 (both with role_id=7)
-- Machines: id_machine=1001, 1002 (from existing data)
-- Plan shifts: id_planshift=1001, 1002 (from existing data)

-- Only insert if incident_reports table is empty or has < 3 records
SET @incident_count = (SELECT COUNT(*) FROM `incident_reports`);

SET @sql = IF(
  @incident_count < 3,
  'INSERT INTO `incident_reports` 
   (`user_id`, `id_machine`, `id_planshift`, `incident_description`, `status`, `created_at`, `updated_at`)
   SELECT 7, 1001, 1001, "Máy hoạt động không bình thường, có tiếng lạ khi chạy", 1, NOW(), NOW()
   WHERE NOT EXISTS (SELECT 1 FROM `incident_reports` WHERE `user_id`=7 AND `id_machine`=1001 LIMIT 1)
   UNION ALL
   SELECT 7, 1002, 1002, "Dây chuyền bị ngừng đột ngột, không rõ nguyên nhân", 0, NOW(), NOW()
   WHERE NOT EXISTS (SELECT 1 FROM `incident_reports` WHERE `user_id`=7 AND `id_machine`=1002 LIMIT 1)
   UNION ALL
   SELECT 12, 1001, 1001, "Sản phẩm bị lỗi kích thước, cần kiểm tra lại máy", 1, NOW(), NOW()
   WHERE NOT EXISTS (SELECT 1 FROM `incident_reports` WHERE `user_id`=12 LIMIT 1)',
  'SELECT "Sample incidents already seeded" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verify the inserts
SELECT 'Incident Reports Status:' AS '';
SELECT 
  COUNT(*) as total_incidents,
  SUM(CASE WHEN `status`=0 THEN 1 ELSE 0 END) as pending,
  SUM(CASE WHEN `status`=1 THEN 1 ELSE 0 END) as completed
FROM `incident_reports`;

-- =====================================================
-- SUMMARY
-- =====================================================
-- ✓ Worker role fixed from 'admin' to 'worker' for user_id=7,12
-- ✓ Sample incident reports seeded (only if table had < 3 records)
-- ✓ Data ready for worker incident management testing
-- =====================================================

SELECT 'CAP2 UC15_BCSC Migration 001: Complete ✓' AS status;
