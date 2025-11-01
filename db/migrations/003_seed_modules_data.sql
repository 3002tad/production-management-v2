-- =====================================================
-- MIGRATION 003: Seed Modules Data
-- Description: Insert 16 modules (nhóm chức năng) vào hệ thống
-- Author: Production Management Team
-- Date: 2025-11-01
-- =====================================================

USE `db_production`;

-- =====================================================
-- INSERT MODULES DATA
-- =====================================================

INSERT INTO `modules` (`module_id`, `module_name`, `module_display_name`, `description`, `icon`, `parent_id`, `route`, `sort_order`, `is_active`) VALUES

-- Module quản lý cơ bản (1-5)
(1, 'customer', 'Quản lý Khách hàng', 'Quản lý thông tin khách hàng, liên hệ, lịch sử đơn hàng', 'fa-users', NULL, 'customer/', 1, 1),

(2, 'product', 'Quản lý Sản phẩm', 'Quản lý sản phẩm bút bi và các biến thể (0.5mm, 0.7mm, 1.0mm, màu mực)', 'fa-box', NULL, 'product/', 2, 1),

(3, 'order', 'Quản lý Đơn hàng', 'Tiếp nhận và quản lý đơn hàng từ khách hàng', 'fa-shopping-cart', NULL, 'order/', 3, 1),

(4, 'project', 'Quản lý Dự án', 'Quản lý dự án sản xuất từ đơn hàng', 'fa-project-diagram', NULL, 'project/', 4, 1),

(5, 'bom', 'Định mức Nguyên vật liệu', 'Quản lý BOM (Bill of Materials) - định mức NVL cho từng sản phẩm', 'fa-list-alt', NULL, 'bom/', 5, 1),

-- Module sản xuất (6-11)
(6, 'planning', 'Kế hoạch Sản xuất', 'Lập và phê duyệt kế hoạch sản xuất tổng, kế hoạch line', 'fa-calendar-alt', NULL, 'planning/', 6, 1),

(7, 'shift', 'Quản lý Ca làm việc', 'Quản lý ca sản xuất, phân công nhân sự, gán máy cho ca', 'fa-clock', NULL, 'shift/', 7, 1),

(8, 'production', 'Báo cáo Sản xuất', 'Báo cáo sản lượng sản xuất theo máy, ca, line', 'fa-industry', NULL, 'production/', 8, 1),

(9, 'shift_closing', 'Chốt ca Sản xuất', 'Tạo và phê duyệt phiếu chốt ca (Finished/Waste, lý do lỗi)', 'fa-check-square', NULL, 'shift_closing/', 9, 1),

(10, 'machine', 'Quản lý Máy móc', 'Quản lý máy móc, dây chuyền, lịch bảo trì, trạng thái máy', 'fa-cogs', NULL, 'machine/', 10, 1),

(11, 'incident', 'Quản lý Sự cố', 'Ghi nhận và xử lý sự cố máy móc, chất lượng, an toàn', 'fa-exclamation-triangle', NULL, 'incident/', 11, 1),

-- Module kho & NVL (12-13)
(12, 'material', 'Danh mục Nguyên vật liệu', 'Quản lý danh mục nguyên vật liệu, tồn kho NVL', 'fa-cubes', NULL, 'material/', 12, 1),

(13, 'warehouse', 'Quản lý Kho', 'Nhập/xuất kho NVL, nhập/xuất kho thành phẩm, phiếu kho', 'fa-warehouse', NULL, 'warehouse/', 13, 1),

-- Module chất lượng (14)
(14, 'qc', 'Kiểm soát Chất lượng', 'Kiểm tra QC, phê duyệt/reject sản phẩm, ghi nhận lỗi', 'fa-check-circle', NULL, 'qc/', 14, 1),

-- Module nhân sự (15)
(15, 'staff', 'Quản lý Nhân sự', 'Quản lý thông tin nhân viên, công nhân sản xuất', 'fa-user-tie', NULL, 'staff/', 15, 1),

-- Module báo cáo & dashboard (16)
(16, 'report', 'Báo cáo & Dashboard', 'Dashboard tổng quan, báo cáo tổng hợp, phân tích dữ liệu', 'fa-chart-bar', NULL, 'report/', 16, 1),

-- Module hệ thống (17-18)
(17, 'user', 'Quản lý Người dùng', 'Quản lý tài khoản người dùng, phân quyền, khóa/mở user', 'fa-user-cog', NULL, 'user/', 17, 1),

(18, 'system', 'Cài đặt Hệ thống', 'Cài đặt chung, tham số hệ thống, nhật ký hoạt động', 'fa-wrench', NULL, 'system/', 18, 1)

ON DUPLICATE KEY UPDATE
  `module_display_name` = VALUES(`module_display_name`),
  `description` = VALUES(`description`),
  `icon` = VALUES(`icon`),
  `route` = VALUES(`route`),
  `sort_order` = VALUES(`sort_order`),
  `is_active` = VALUES(`is_active`);

-- =====================================================
-- SUB-MODULES (Menu đa cấp - Optional)
-- =====================================================

-- Sub-modules của Warehouse (13)
INSERT INTO `modules` (`module_name`, `module_display_name`, `description`, `icon`, `parent_id`, `route`, `sort_order`, `is_active`) VALUES
('warehouse_receipt', 'Phiếu Nhập kho NVL', 'Nhập kho nguyên vật liệu theo PO', 'fa-arrow-down', 13, 'warehouse/receipt/', 131, 1),
('warehouse_issue', 'Phiếu Xuất kho NVL', 'Xuất kho NVL cho ca sản xuất', 'fa-arrow-up', 13, 'warehouse/issue/', 132, 1),
('finished_goods_receipt', 'Phiếu Nhập kho Thành phẩm', 'Nhập kho thành phẩm sau QC', 'fa-download', 13, 'warehouse/fg_receipt/', 133, 1),
('finished_goods_issue', 'Phiếu Xuất kho Thành phẩm', 'Xuất kho thành phẩm giao hàng', 'fa-upload', 13, 'warehouse/fg_issue/', 134, 1),
('stock_report', 'Báo cáo Tồn kho', 'Xem báo cáo tồn kho và luân chuyển', 'fa-boxes', 13, 'warehouse/stock_report/', 135, 1)

ON DUPLICATE KEY UPDATE
  `module_display_name` = VALUES(`module_display_name`),
  `description` = VALUES(`description`),
  `route` = VALUES(`route`);

-- Sub-modules của Report (16)
INSERT INTO `modules` (`module_name`, `module_display_name`, `description`, `icon`, `parent_id`, `route`, `sort_order`, `is_active`) VALUES
('dashboard_bod', 'Dashboard Ban Giám Đốc', 'Dashboard tổng quan cho BOD', 'fa-tachometer-alt', 16, 'report/dashboard_bod/', 161, 1),
('dashboard_line', 'Dashboard Dây chuyền', 'Dashboard vận hành line', 'fa-chart-line', 16, 'report/dashboard_line/', 162, 1),
('report_production', 'Báo cáo Sản xuất', 'Báo cáo tổng hợp sản xuất', 'fa-industry', 16, 'report/production/', 163, 1),
('report_quality', 'Báo cáo Chất lượng', 'Báo cáo tổng hợp QC', 'fa-check-double', 16, 'report/quality/', 164, 1),
('report_inventory', 'Báo cáo Tồn kho', 'Báo cáo tồn kho và luân chuyển', 'fa-warehouse', 16, 'report/inventory/', 165, 1)

ON DUPLICATE KEY UPDATE
  `module_display_name` = VALUES(`module_display_name`),
  `description` = VALUES(`description`),
  `route` = VALUES(`route`);

-- =====================================================
-- VERIFICATION
-- =====================================================

-- Hiển thị kết quả modules chính
SELECT 
  module_id,
  module_name,
  module_display_name,
  icon,
  parent_id,
  route,
  sort_order,
  is_active
FROM `modules`
WHERE parent_id IS NULL
ORDER BY `sort_order`;

-- Hiển thị sub-modules
SELECT 
  m1.module_display_name AS 'Parent Module',
  m2.module_id,
  m2.module_name,
  m2.module_display_name AS 'Sub Module',
  m2.route,
  m2.sort_order
FROM `modules` m2
LEFT JOIN `modules` m1 ON m2.parent_id = m1.module_id
WHERE m2.parent_id IS NOT NULL
ORDER BY m2.parent_id, m2.sort_order;

-- =====================================================
-- HOÀN THÀNH MIGRATION 003
-- =====================================================
-- Kết quả:
-- ✓ Tạo 18 modules chính
-- ✓ Tạo 10 sub-modules (Warehouse: 5, Report: 5)
-- ✓ Tổng cộng: 28 modules
-- =====================================================

SELECT 'Migration 003: Seed Modules Data - COMPLETED ✓' AS status;
SELECT CONCAT('Total Modules: ', COUNT(*)) AS summary FROM `modules`;
