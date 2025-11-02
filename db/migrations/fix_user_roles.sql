-- Fix user bod - Đổi role_id từ 4 (system_admin) thành 1 (bod)
-- CHẠY NGAY TRONG phpMyAdmin!

USE db_production;

-- Fix user bod: role_id = 4 → role_id = 1
UPDATE user 
SET role_id = 1 
WHERE username = 'bod';

-- Fix các user khác (nếu sai)
UPDATE user SET role_id = 3 WHERE username = 'warehouse';  -- warehouse_staff
UPDATE user SET role_id = 5 WHERE username = 'qc';         -- qc_staff  
UPDATE user SET role_id = 6 WHERE username = 'technical';  -- technical_staff
UPDATE user SET role_id = 7 WHERE username = 'worker';     -- worker

-- Verify
SELECT 
    u.user_id, 
    u.username, 
    u.role_id,
    r.role_name,
    r.role_display_name,
    u.is_active
FROM user u
LEFT JOIN roles r ON u.role_id = r.role_id
WHERE u.username IN ('bod', 'admin', 'warehouse', 'qc', 'technical', 'worker', 'leader')
ORDER BY u.user_id;
