-- ============================================================================
-- MIGRATION: Thêm các cột mới cho Use Case "Tiếp nhận & Tạo đơn hàng"
-- Database: db_production
-- Table: project
-- Date: 2025-11-01
-- ============================================================================

USE `db_production`;

-- ============================================================================
-- BƯỚC 1: Thêm cột risk_flag (Cờ nguy cơ trễ hạn)
-- ============================================================================
-- Mục đích: Đánh dấu đơn hàng có nguy cơ trễ hạn do vượt công suất
-- Giá trị: 0 = Bình thường, 1 = Nguy cơ trễ hạn

ALTER TABLE `project` 
ADD COLUMN `risk_flag` TINYINT(1) NOT NULL DEFAULT 0 
COMMENT 'Cờ nguy cơ trễ hạn: 0=Bình thường, 1=Nguy cơ trễ' 
AFTER `pr_status`;

-- ============================================================================
-- BƯỚC 2: Thêm cột customer_request (Yêu cầu khách hàng)
-- ============================================================================
-- Mục đích: Lưu yêu cầu đặc biệt của khách hàng khi đặt hàng
-- Kiểu dữ liệu: TEXT (cho phép nội dung dài)
-- Nullable: Cho phép NULL vì không phải lúc nào cũng có yêu cầu

ALTER TABLE `project` 
ADD COLUMN `customer_request` TEXT NULL 
COMMENT 'Yêu cầu đặc biệt của khách hàng' 
AFTER `risk_flag`;

-- ============================================================================
-- BƯỚC 3: Thêm cột created_at (Thời gian tạo đơn hàng)
-- ============================================================================
-- Mục đích: Theo dõi thời gian tạo đơn hàng, phục vụ:
--   - Tạo tên project tự động (ORD-{id_cust}-{YYYYMMDD}-{seq})
--   - Sắp xếp đơn hàng theo thứ tự mới nhất
--   - Audit trail

ALTER TABLE `project` 
ADD COLUMN `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP 
COMMENT 'Thời gian tạo đơn hàng' 
AFTER `customer_request`;

-- ============================================================================
-- BƯỚC 4: Thêm Index để tối ưu query
-- ============================================================================
-- Index cho việc tìm kiếm đơn hàng theo ngày tạo và khách hàng
-- Phục vụ function generateProjectName() trong OrderModel

CREATE INDEX idx_created_cust 
ON `project` (id_cust, created_at);

-- Index cho việc tìm kiếm đơn hàng có nguy cơ trễ hạn
CREATE INDEX idx_risk_status 
ON `project` (risk_flag, pr_status);

-- ============================================================================
-- BƯỚC 5: Verify kết quả migration
-- ============================================================================

-- Kiểm tra cấu trúc bảng sau khi thêm cột
DESCRIBE `project`;

-- Expected Output:
-- +------------------+--------------+------+-----+-------------------+----------------+
-- | Field            | Type         | Null | Key | Default           | Extra          |
-- +------------------+--------------+------+-----+-------------------+----------------+
-- | id_project       | int(25)      | NO   | PRI | NULL              | auto_increment |
-- | project_name     | varchar(50)  | NO   |     | NULL              |                |
-- | id_cust          | int(25)      | NO   | MUL | NULL              |                |
-- | id_product       | int(25)      | NO   | MUL | NULL              |                |
-- | diameter         | int(25)      | NO   |     | NULL              |                |
-- | qty_request      | int(15)      | NO   |     | NULL              |                |
-- | entry_date       | date         | NO   |     | NULL              |                |
-- | pr_status        | int(5)       | NO   |     | NULL              |                |
-- | risk_flag        | tinyint(1)   | NO   | MUL | 0                 |                | ← NEW
-- | customer_request | text         | YES  |     | NULL              |                | ← NEW
-- | created_at       | timestamp    | NO   | MUL | CURRENT_TIMESTAMP |                | ← NEW
-- +------------------+--------------+------+-----+-------------------+----------------+

-- Kiểm tra indexes
SHOW INDEX FROM `project`;

-- Expected: Ngoài PRIMARY và các FK, có thêm:
-- - idx_created_cust (id_cust, created_at)
-- - idx_risk_status (risk_flag, pr_status)

-- ============================================================================
-- BƯỚC 6: Update dữ liệu hiện có (Nếu có)
-- ============================================================================
-- Cập nhật các đơn hàng cũ: risk_flag = 0, created_at = entry_date

UPDATE `project` 
SET 
    created_at = CONCAT(entry_date, ' 00:00:00'),
    risk_flag = 0
WHERE created_at IS NULL OR created_at = '0000-00-00 00:00:00';

-- ============================================================================
-- ROLLBACK SCRIPT (Nếu cần)
-- ============================================================================
-- Lưu ý: Chỉ chạy nếu cần rollback migration này

-- ALTER TABLE `project` DROP INDEX idx_created_cust;
-- ALTER TABLE `project` DROP INDEX idx_risk_status;
-- ALTER TABLE `project` DROP COLUMN created_at;
-- ALTER TABLE `project` DROP COLUMN customer_request;
-- ALTER TABLE `project` DROP COLUMN risk_flag;

-- ============================================================================
-- KẾT LUẬN
-- ============================================================================
-- Migration này thêm 3 cột mới vào bảng project:
-- 1. risk_flag: Đánh dấu đơn hàng nguy cơ trễ hạn
-- 2. customer_request: Lưu yêu cầu đặc biệt của khách hàng
-- 3. created_at: Timestamp tạo đơn hàng
--
-- Và 2 indexes để tối ưu performance:
-- - idx_created_cust: Cho generateProjectName()
-- - idx_risk_status: Cho filter đơn hàng có vấn đề
--
-- Tương thích với Use Case "Tiếp nhận & Tạo đơn hàng bút bi"
-- ============================================================================
