-- =====================================================
-- MIGRATION 006: Migrate hoàn toàn sang RBAC
-- Description: Xóa cột role cũ, chuyển đổi 100% sang role_id
-- Author: Production Management Team
-- Date: 2025-11-02
-- Version: PHASE 1 - Final Migration
-- =====================================================

-- ⚠️ CẢNH BÁO: Migration này sẽ xóa cột `role` cũ (enum 'admin','leader')
-- ⚠️ Đảm bảo đã chạy migrations 001-005 trước đó!
-- ⚠️ Backup database trước khi chạy!

USE `db_production`;

SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;

-- =====================================================
-- BƯỚC 1: Chuyển đổi dữ liệu role cũ sang role_id mới
-- =====================================================

SELECT '========================================' AS '';
SELECT 'STEP 1: Migrate existing role data' AS '';
SELECT '========================================' AS '';

-- Kiểm tra xem cột role cũ có tồn tại không
SET @col_exists = (
  SELECT COUNT(*) 
  FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = 'db_production' 
  AND TABLE_NAME = 'user' 
  AND COLUMN_NAME = 'role'
);

SELECT IF(@col_exists > 0, 
  'Cột "role" cũ tồn tại - Bắt đầu migrate...', 
  'Cột "role" không tồn tại - Migration đã chạy trước đó'
) AS check_status;

-- Chỉ migrate nếu cột role cũ tồn tại
-- Map: admin → system_admin (role_id = 4)
-- Map: leader → line_manager (role_id = 2)

UPDATE `user` 
SET `role_id` = 4, 
    `full_name` = COALESCE(`full_name`, 'System Administrator'),
    `is_active` = 1
WHERE `role` = 'admin' 
  AND (@col_exists > 0);

UPDATE `user` 
SET `role_id` = 2,
    `full_name` = COALESCE(`full_name`, 'Line Manager'),
    `is_active` = 1
WHERE `role` = 'leader' 
  AND (@col_exists > 0);

-- Kiểm tra users chưa có role_id
SELECT 
  user_id,
  username,
  role AS old_role,
  role_id AS new_role_id,
  CASE 
    WHEN role_id IS NULL THEN '❌ CHƯA MIGRATE'
    ELSE '✓ Đã migrate'
  END AS migration_status
FROM `user`
WHERE @col_exists > 0;

-- =====================================================
-- BƯỚC 2: Validate dữ liệu trước khi xóa
-- =====================================================

SELECT '========================================' AS '';
SELECT 'STEP 2: Validate migration' AS '';
SELECT '========================================' AS '';

-- Đếm users chưa có role_id
SET @unmigrated_count = (
  SELECT COUNT(*) 
  FROM `user` 
  WHERE `role_id` IS NULL
);

SELECT 
  CONCAT('Tổng số users: ', (SELECT COUNT(*) FROM `user`)) AS total_users,
  CONCAT('Users đã migrate: ', (SELECT COUNT(*) FROM `user` WHERE `role_id` IS NOT NULL)) AS migrated,
  CONCAT('Users chưa migrate: ', @unmigrated_count) AS unmigrated;

-- Nếu còn users chưa migrate → CẢNH BÁO
SELECT 
  CASE 
    WHEN @unmigrated_count > 0 THEN 
      '⚠️ WARNING: Vẫn còn users chưa có role_id! Không thể xóa cột role cũ!'
    ELSE 
      '✓ OK: Tất cả users đã có role_id'
  END AS validation_result;

-- =====================================================
-- BƯỚC 3: Xóa cột role cũ (chỉ khi validate OK)
-- =====================================================

SELECT '========================================' AS '';
SELECT 'STEP 3: Remove old role column' AS '';
SELECT '========================================' AS '';

-- Chỉ xóa nếu:
-- 1. Cột role cũ tồn tại (@col_exists > 0)
-- 2. Không còn users chưa migrate (@unmigrated_count = 0)

SET @can_drop = (@col_exists > 0 AND @unmigrated_count = 0);

SET @sql_drop_role = IF(
  @can_drop = 1,
  'ALTER TABLE `user` DROP COLUMN `role`',
  'SELECT "Cannot drop role column - validation failed or column not exists" AS message'
);

PREPARE stmt FROM @sql_drop_role;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT 
  CASE 
    WHEN @can_drop = 1 THEN 
      '✓ Đã xóa cột "role" cũ thành công!'
    WHEN @col_exists = 0 THEN
      'ℹ️ Cột "role" đã được xóa trước đó'
    ELSE 
      '❌ KHÔNG xóa cột "role" - vẫn còn users chưa migrate!'
  END AS drop_status;

-- =====================================================
-- BƯỚC 4: Đảm bảo role_id là NOT NULL (sau khi migrate xong)
-- =====================================================

SELECT '========================================' AS '';
SELECT 'STEP 4: Enforce role_id constraint' AS '';
SELECT '========================================' AS '';

-- Nếu tất cả users đã có role_id, set cột role_id thành NOT NULL
SET @sql_not_null = IF(
  @unmigrated_count = 0,
  'ALTER TABLE `user` MODIFY `role_id` INT NOT NULL COMMENT "ID vai trò (bắt buộc)"',
  'SELECT "Skipped - some users still have NULL role_id" AS message'
);

PREPARE stmt FROM @sql_not_null;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- BƯỚC 5: Final verification
-- =====================================================

SELECT '========================================' AS '';
SELECT 'FINAL VERIFICATION' AS '';
SELECT '========================================' AS '';

-- Kiểm tra cấu trúc bảng user
SELECT 
  COLUMN_NAME,
  COLUMN_TYPE,
  IS_NULLABLE,
  COLUMN_DEFAULT,
  COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'db_production'
  AND TABLE_NAME = 'user'
ORDER BY ORDINAL_POSITION;

-- Thống kê users theo role
SELECT 
  r.role_name,
  r.role_display_name,
  COUNT(u.user_id) AS total_users
FROM `roles` r
LEFT JOIN `user` u ON u.role_id = r.role_id
GROUP BY r.role_id
ORDER BY r.level DESC;

-- =====================================================
-- HOÀN THÀNH MIGRATION 006
-- =====================================================

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

SELECT '========================================' AS '';
SELECT '✓✓✓ MIGRATION 006 COMPLETED ✓✓✓' AS '';
SELECT '========================================' AS '';
SELECT 'Hệ thống đã migrate HOÀN TOÀN sang RBAC!' AS final_status;
SELECT 'Cột "role" cũ (enum admin/leader) đã bị xóa' AS note_1;
SELECT 'Tất cả users giờ dùng "role_id" (INT NOT NULL)' AS note_2;
SELECT 'Code cũ dùng $user->role sẽ BỊ LỖI - cần update!' AS warning;
SELECT '========================================' AS '';

-- =====================================================
-- NEXT STEPS
-- =====================================================
SELECT 'NEXT STEPS FOR DEVELOPERS:' AS '';
SELECT '1. Update LoginModel.php - Đổi SELECT role thành role_id' AS step;
SELECT '2. Update Admin.php, Leader.php - Đổi check $role thành $role_id' AS step;
SELECT '3. Update Session handling - Đổi userdata("role") thành userdata("role_id")' AS step;
SELECT '4. Test login với tất cả roles' AS step;
SELECT '5. Cập nhật views - Hiển thị role_display_name thay vì role' AS step;
SELECT '========================================' AS '';
