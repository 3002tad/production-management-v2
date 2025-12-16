-- ===================================================================
-- MIGRATION: Fix JSON Encoding Issues in warning_details
-- Date: December 7, 2025
-- Purpose: Fix invalid JSON in project.warning_details field
-- ===================================================================

-- Bước 1: Backup table trước khi fix
CREATE TABLE IF NOT EXISTS project_backup_20251207 AS SELECT * FROM project;

-- Bước 2: Fix các record có warning_details không hợp lệ
-- Set NULL cho các JSON không parse được
UPDATE project 
SET warning_details = NULL
WHERE warning_flag = 1 
  AND warning_details IS NOT NULL
  AND warning_details != ''
  AND warning_details NOT REGEXP '^\\{.*\\}$';

-- Bước 3: Verify kết quả
SELECT 
    COUNT(*) as total_orders,
    SUM(CASE WHEN warning_flag = 1 AND warning_details IS NOT NULL THEN 1 ELSE 0 END) as has_warnings,
    SUM(CASE WHEN warning_flag = 1 AND warning_details IS NULL THEN 1 ELSE 0 END) as warnings_cleared
FROM project;

-- Bước 4: Test JSON decode
-- Kiểm tra xem còn record nào có JSON lỗi không
SELECT 
    id_project,
    project_name,
    warning_flag,
    LEFT(warning_details, 100) as warning_preview
FROM project
WHERE warning_flag = 1 
  AND warning_details IS NOT NULL
LIMIT 10;

-- ===================================================================
-- CHÚ THÍCH:
-- - Record có warning_flag = 1 nhưng warning_details = NULL sẽ được
--   tính lại tự động khi user sửa đơn hoặc khi chạy cron job
-- - Backup table: project_backup_20251207 (giữ trong 30 ngày)
-- ===================================================================
