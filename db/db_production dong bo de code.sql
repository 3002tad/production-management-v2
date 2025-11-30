-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 23, 2025 at 04:32 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_production`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `log_id` bigint(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'ID người thực hiện',
  `username` varchar(50) DEFAULT NULL COMMENT 'Tên đăng nhập',
  `action` varchar(100) DEFAULT NULL COMMENT 'Hành động: login, logout, create, update, delete, approve, reject',
  `module` varchar(50) DEFAULT NULL COMMENT 'Module bị tác động: customer, product, order, etc.',
  `record_id` int(11) DEFAULT NULL COMMENT 'ID của bản ghi bị tác động',
  `old_value` text DEFAULT NULL COMMENT 'Giá trị cũ (JSON format)',
  `new_value` text DEFAULT NULL COMMENT 'Giá trị mới (JSON format)',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'Địa chỉ IP (hỗ trợ IPv6)',
  `user_agent` text DEFAULT NULL COMMENT 'Thông tin trình duyệt',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng ghi nhật ký mọi hoạt động trong hệ thống';

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`log_id`, `user_id`, `username`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-22 18:26:39'),
(2, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-22 18:27:25'),
(3, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-22 18:42:58'),
(4, 3, 'bod', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-22 19:02:38'),
(5, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-22 19:02:41'),
(6, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-23 15:30:09');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id_cust` int(25) NOT NULL,
  `cust_name` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL,
  `telp` int(20) NOT NULL,
  `email` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id_cust`, `cust_name`, `address`, `telp`, `email`) VALUES
(1001, 'Tes Customer', 'Indonesia', 21293383, 'tes@mail.com');

-- --------------------------------------------------------

--
-- Table structure for table `finished_report`
--

CREATE TABLE `finished_report` (
  `id_finished` int(11) NOT NULL,
  `id_project` int(11) NOT NULL,
  `total_finished` int(11) NOT NULL,
  `fdate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Báo cáo hoàn thành - total_finished: cái (số lượng bút)';

--
-- Dumping data for table `finished_report`
--

INSERT INTO `finished_report` (`id_finished`, `id_project`, `total_finished`, `fdate`) VALUES
(1001, 1001, 2000, '2025-11-01 15:30:04');

-- --------------------------------------------------------

--
-- Table structure for table `machine`
--

CREATE TABLE `machine` (
  `id_machine` int(50) NOT NULL,
  `machine_name` varchar(50) NOT NULL,
  `capacity` int(15) NOT NULL,
  `mc_status` int(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng máy móc - capacity: cái/giờ, mc_status: 1=Sẵn sàng, 2=Đang dùng, 3=Sự cố, 4=Bảo trì';

--
-- Dumping data for table `machine`
--

INSERT INTO `machine` (`id_machine`, `machine_name`, `capacity`, `mc_status`) VALUES
(1001, 'Machine 1', 500, 1),
(1002, 'Tes Machine', 800, 1);

--
-- Triggers `machine`
--
DELIMITER $$
CREATE TRIGGER `validate_machine_capacity` BEFORE INSERT ON `machine` FOR EACH ROW BEGIN
    IF NEW.capacity <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Công suất máy phải lớn hơn 0 cái/giờ';
    END IF;
    
    -- Kiểm tra mc_status hợp lệ (1=Sẵn sàng, 2=Đang dùng, 3=Sự cố, 4=Bảo trì)
    IF NEW.mc_status NOT IN (1, 2, 3, 4) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Trạng thái máy không hợp lệ (1-4)';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `material`
--

CREATE TABLE `material` (
  `id_material` int(50) NOT NULL,
  `material_name` varchar(50) NOT NULL,
  `stock` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng nguyên liệu - stock: gram';

--
-- Dumping data for table `material`
--

INSERT INTO `material` (`id_material`, `material_name`, `stock`) VALUES
(1001, 'Test Matereal', 5000),
(1002, 'Nhựa ABS', 10000),
(1003, 'Mực gel xanh', 5000),
(1004, 'Mực gel đen', 5000),
(1005, 'Bi kim loại 0.5mm', 2000),
(1006, 'Bi kim loại 0.7mm', 3000),
(1007, 'Bi kim loại 1.0mm', 2000),
(1008, 'Lò xo thép', 1000);

--
-- Triggers `material`
--
DELIMITER $$
CREATE TRIGGER `validate_material_stock` BEFORE INSERT ON `material` FOR EACH ROW BEGIN
    IF NEW.stock < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Tồn kho không được âm';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `module_id` int(11) NOT NULL,
  `module_name` varchar(50) NOT NULL COMMENT 'Tên module (code): customer, product, order, etc.',
  `module_display_name` varchar(100) DEFAULT NULL COMMENT 'Tên hiển thị: Quản lý Khách hàng, etc.',
  `description` text DEFAULT NULL COMMENT 'Mô tả chức năng module',
  `icon` varchar(50) DEFAULT NULL COMMENT 'Font Awesome icon class: fa-users, fa-box, etc.',
  `parent_id` int(11) DEFAULT NULL COMMENT 'Module cha (cho menu đa cấp)',
  `route` varchar(100) DEFAULT NULL COMMENT 'Route URL: customer/, product/, etc.',
  `sort_order` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị trong menu',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1=Active, 0=Inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng nhóm chức năng/module trong hệ thống';

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`module_id`, `module_name`, `module_display_name`, `description`, `icon`, `parent_id`, `route`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'customer', 'Quản lý Khách hàng', 'Quản lý thông tin khách hàng, liên hệ, lịch sử đơn hàng', 'fa-users', NULL, 'customer/', 1, 1, '2025-11-01 15:54:07'),
(2, 'product', 'Quản lý Sản phẩm', 'Quản lý sản phẩm bút bi và các biến thể (0.5mm, 0.7mm, 1.0mm, màu mực)', 'fa-box', NULL, 'product/', 2, 1, '2025-11-01 15:54:07'),
(3, 'order', 'Quản lý Đơn hàng', 'Tiếp nhận và quản lý đơn hàng từ khách hàng', 'fa-shopping-cart', NULL, 'order/', 3, 1, '2025-11-01 15:54:07'),
(4, 'project', 'Quản lý Dự án', 'Quản lý dự án sản xuất từ đơn hàng', 'fa-project-diagram', NULL, 'project/', 4, 1, '2025-11-01 15:54:07'),
(5, 'bom', 'Định mức Nguyên vật liệu', 'Quản lý BOM (Bill of Materials) - định mức NVL cho từng sản phẩm', 'fa-list-alt', NULL, 'bom/', 5, 1, '2025-11-01 15:54:07'),
(6, 'planning', 'Kế hoạch Sản xuất', 'Lập và phê duyệt kế hoạch sản xuất tổng, kế hoạch line', 'fa-calendar-alt', NULL, 'planning/', 6, 1, '2025-11-01 15:54:07'),
(7, 'shift', 'Quản lý Ca làm việc', 'Quản lý ca sản xuất, phân công nhân sự, gán máy cho ca', 'fa-clock', NULL, 'shift/', 7, 1, '2025-11-01 15:54:07'),
(8, 'production', 'Báo cáo Sản xuất', 'Báo cáo sản lượng sản xuất theo máy, ca, line', 'fa-industry', NULL, 'production/', 8, 1, '2025-11-01 15:54:07'),
(9, 'shift_closing', 'Chốt ca Sản xuất', 'Tạo và phê duyệt phiếu chốt ca (Finished/Waste, lý do lỗi)', 'fa-check-square', NULL, 'shift_closing/', 9, 1, '2025-11-01 15:54:07'),
(10, 'machine', 'Quản lý Máy móc', 'Quản lý máy móc, dây chuyền, lịch bảo trì, trạng thái máy', 'fa-cogs', NULL, 'machine/', 10, 1, '2025-11-01 15:54:07'),
(11, 'incident', 'Quản lý Sự cố', 'Ghi nhận và xử lý sự cố máy móc, chất lượng, an toàn', 'fa-exclamation-triangle', NULL, 'incident/', 11, 1, '2025-11-01 15:54:07'),
(12, 'material', 'Danh mục Nguyên vật liệu', 'Quản lý danh mục nguyên vật liệu, tồn kho NVL', 'fa-cubes', NULL, 'material/', 12, 1, '2025-11-01 15:54:07'),
(13, 'warehouse', 'Quản lý Kho', 'Nhập/xuất kho NVL, nhập/xuất kho thành phẩm, phiếu kho', 'fa-warehouse', NULL, 'warehouse/', 13, 1, '2025-11-01 15:54:07'),
(14, 'qc', 'Kiểm soát Chất lượng', 'Kiểm tra QC, phê duyệt/reject sản phẩm, ghi nhận lỗi', 'fa-check-circle', NULL, 'qc/', 14, 1, '2025-11-01 15:54:07'),
(15, 'staff', 'Quản lý Nhân sự', 'Quản lý thông tin nhân viên, công nhân sản xuất', 'fa-user-tie', NULL, 'staff/', 15, 1, '2025-11-01 15:54:07'),
(16, 'report', 'Báo cáo & Dashboard', 'Dashboard tổng quan, báo cáo tổng hợp, phân tích dữ liệu', 'fa-chart-bar', NULL, 'report/', 16, 1, '2025-11-01 15:54:07'),
(17, 'user', 'Quản lý Người dùng', 'Quản lý tài khoản người dùng, phân quyền, khóa/mở user', 'fa-user-cog', NULL, 'user/', 17, 1, '2025-11-01 15:54:07'),
(18, 'system', 'Cài đặt Hệ thống', 'Cài đặt chung, tham số hệ thống, nhật ký hoạt động', 'fa-wrench', NULL, 'system/', 18, 1, '2025-11-01 15:54:07'),
(19, 'warehouse_receipt', 'Phiếu Nhập kho NVL', 'Nhập kho nguyên vật liệu theo PO', 'fa-arrow-down', 13, 'warehouse/receipt/', 131, 1, '2025-11-01 15:54:07'),
(20, 'warehouse_issue', 'Phiếu Xuất kho NVL', 'Xuất kho NVL cho ca sản xuất', 'fa-arrow-up', 13, 'warehouse/issue/', 132, 1, '2025-11-01 15:54:07'),
(21, 'finished_goods_receipt', 'Phiếu Nhập kho Thành phẩm', 'Nhập kho thành phẩm sau QC', 'fa-download', 13, 'warehouse/fg_receipt/', 133, 1, '2025-11-01 15:54:07'),
(22, 'finished_goods_issue', 'Phiếu Xuất kho Thành phẩm', 'Xuất kho thành phẩm giao hàng', 'fa-upload', 13, 'warehouse/fg_issue/', 134, 1, '2025-11-01 15:54:07'),
(23, 'stock_report', 'Báo cáo Tồn kho', 'Xem báo cáo tồn kho và luân chuyển', 'fa-boxes', 13, 'warehouse/stock_report/', 135, 1, '2025-11-01 15:54:07'),
(24, 'dashboard_bod', 'Dashboard Ban Giám Đốc', 'Dashboard tổng quan cho BOD', 'fa-tachometer-alt', 16, 'report/dashboard_bod/', 161, 1, '2025-11-01 15:54:07'),
(25, 'dashboard_line', 'Dashboard Dây chuyền', 'Dashboard vận hành line', 'fa-chart-line', 16, 'report/dashboard_line/', 162, 1, '2025-11-01 15:54:07'),
(26, 'report_production', 'Báo cáo Sản xuất', 'Báo cáo tổng hợp sản xuất', 'fa-industry', 16, 'report/production/', 163, 1, '2025-11-01 15:54:07'),
(27, 'report_quality', 'Báo cáo Chất lượng', 'Báo cáo tổng hợp QC', 'fa-check-double', 16, 'report/quality/', 164, 1, '2025-11-01 15:54:07'),
(28, 'report_inventory', 'Báo cáo Tồn kho', 'Báo cáo tồn kho và luân chuyển', 'fa-warehouse', 16, 'report/inventory/', 165, 1, '2025-11-01 15:54:07');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `module_id` int(11) DEFAULT NULL COMMENT 'Thuộc module nào',
  `permission_name` varchar(100) NOT NULL COMMENT 'Tên quyền: customer.view, product.create, etc.',
  `permission_display_name` varchar(200) DEFAULT NULL COMMENT 'Tên hiển thị: Xem khách hàng, Tạo sản phẩm, etc.',
  `action` varchar(50) DEFAULT NULL COMMENT 'Hành động: view, create, edit, delete, approve, etc.',
  `description` text DEFAULT NULL COMMENT 'Mô tả chi tiết quyền',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quyền hạn chi tiết trong hệ thống';

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `module_id`, `permission_name`, `permission_display_name`, `action`, `description`, `created_at`) VALUES
(1, 1, 'customer.view', 'Xem danh sách khách hàng', 'view', 'Xem danh sách và thông tin chi tiết khách hàng', '2025-11-01 15:54:30'),
(2, 1, 'customer.create', 'Tạo khách hàng mới', 'create', 'Thêm khách hàng mới vào hệ thống', '2025-11-01 15:54:30'),
(3, 1, 'customer.edit', 'Sửa thông tin khách hàng', 'edit', 'Cập nhật thông tin khách hàng', '2025-11-01 15:54:30'),
(4, 1, 'customer.delete', 'Xóa khách hàng', 'delete', 'Xóa khách hàng khỏi hệ thống', '2025-11-01 15:54:30'),
(5, 1, 'customer.export', 'Xuất Excel khách hàng', 'export', 'Xuất danh sách khách hàng ra file Excel', '2025-11-01 15:54:30'),
(6, 2, 'product.view', 'Xem danh sách sản phẩm', 'view', 'Xem danh sách sản phẩm bút bi', '2025-11-01 15:54:30'),
(7, 2, 'product.create', 'Tạo sản phẩm mới', 'create', 'Thêm sản phẩm mới', '2025-11-01 15:54:30'),
(8, 2, 'product.edit', 'Sửa thông tin sản phẩm', 'edit', 'Cập nhật thông tin sản phẩm', '2025-11-01 15:54:30'),
(9, 2, 'product.delete', 'Xóa sản phẩm', 'delete', 'Xóa sản phẩm khỏi danh mục', '2025-11-01 15:54:30'),
(10, 2, 'product_variant.view', 'Xem biến thể sản phẩm', 'view', 'Xem các biến thể (0.5mm, 0.7mm, 1.0mm, màu mực)', '2025-11-01 15:54:30'),
(11, 2, 'product_variant.create', 'Tạo biến thể mới', 'create', 'Thêm biến thể mới cho sản phẩm', '2025-11-01 15:54:30'),
(12, 2, 'product_variant.edit', 'Sửa biến thể', 'edit', 'Cập nhật thông tin biến thể', '2025-11-01 15:54:30'),
(13, 2, 'product_variant.delete', 'Xóa biến thể', 'delete', 'Xóa biến thể sản phẩm', '2025-11-01 15:54:30'),
(14, 2, 'product.export', 'Xuất Excel sản phẩm', 'export', 'Xuất danh sách sản phẩm ra Excel', '2025-11-01 15:54:30'),
(15, 3, 'order.view', 'Xem danh sách đơn hàng', 'view', 'Xem đơn hàng từ khách hàng', '2025-11-01 15:54:30'),
(16, 3, 'order.create', 'Tạo đơn hàng mới', 'create', 'Tiếp nhận đơn hàng mới', '2025-11-01 15:54:30'),
(17, 3, 'order.edit', 'Sửa đơn hàng', 'edit', 'Cập nhật thông tin đơn hàng', '2025-11-01 15:54:30'),
(18, 3, 'order.delete', 'Xóa đơn hàng', 'delete', 'Hủy đơn hàng', '2025-11-01 15:54:30'),
(19, 3, 'order.approve', 'Phê duyệt đơn hàng', 'approve', 'BOD phê duyệt đơn hàng', '2025-11-01 15:54:30'),
(20, 3, 'order.reject', 'Từ chối đơn hàng', 'reject', 'BOD từ chối đơn hàng', '2025-11-01 15:54:30'),
(21, 3, 'order.export', 'Xuất Excel đơn hàng', 'export', 'Xuất danh sách đơn hàng ra Excel', '2025-11-01 15:54:30'),
(22, 4, 'project.view', 'Xem danh sách dự án', 'view', 'Xem dự án sản xuất', '2025-11-01 15:54:30'),
(23, 4, 'project.create', 'Tạo dự án mới', 'create', 'Tạo dự án sản xuất từ đơn hàng', '2025-11-01 15:54:30'),
(24, 4, 'project.edit', 'Sửa thông tin dự án', 'edit', 'Cập nhật thông tin dự án', '2025-11-01 15:54:30'),
(25, 4, 'project.delete', 'Xóa dự án', 'delete', 'Xóa dự án', '2025-11-01 15:54:30'),
(26, 4, 'project.approve', 'Phê duyệt dự án', 'approve', 'BOD phê duyệt dự án', '2025-11-01 15:54:30'),
(27, 4, 'project.export', 'Xuất Excel dự án', 'export', 'Xuất danh sách dự án', '2025-11-01 15:54:30'),
(28, 5, 'bom.view', 'Xem định mức NVL', 'view', 'Xem BOM (Bill of Materials)', '2025-11-01 15:54:30'),
(29, 5, 'bom.create', 'Tạo định mức NVL', 'create', 'Tạo BOM mới cho sản phẩm', '2025-11-01 15:54:30'),
(30, 5, 'bom.edit', 'Sửa định mức NVL', 'edit', 'Cập nhật định mức NVL', '2025-11-01 15:54:30'),
(31, 5, 'bom.delete', 'Xóa định mức NVL', 'delete', 'Xóa BOM', '2025-11-01 15:54:30'),
(32, 5, 'bom.approve', 'Xác nhận định mức NVL', 'approve', 'Kỹ thuật viên xác nhận BOM', '2025-11-01 15:54:30'),
(33, 5, 'bom.export', 'Xuất Excel BOM', 'export', 'Xuất BOM ra Excel', '2025-11-01 15:54:30'),
(34, 6, 'planning.view', 'Xem kế hoạch sản xuất', 'view', 'Xem kế hoạch sản xuất tổng', '2025-11-01 15:54:30'),
(35, 6, 'planning.create', 'Lập kế hoạch sản xuất', 'create', 'Tạo kế hoạch sản xuất mới', '2025-11-01 15:54:30'),
(36, 6, 'planning.edit', 'Sửa kế hoạch sản xuất', 'edit', 'Điều chỉnh kế hoạch', '2025-11-01 15:54:30'),
(37, 6, 'planning.delete', 'Xóa kế hoạch sản xuất', 'delete', 'Xóa kế hoạch', '2025-11-01 15:54:30'),
(38, 6, 'planning.submit', 'Gửi duyệt kế hoạch', 'submit', 'Line Manager gửi kế hoạch lên BOD', '2025-11-01 15:54:30'),
(39, 6, 'planning.approve', 'Phê duyệt kế hoạch', 'approve', 'BOD phê duyệt kế hoạch sản xuất', '2025-11-01 15:54:30'),
(40, 6, 'planning.reject', 'Từ chối kế hoạch', 'reject', 'BOD từ chối kế hoạch', '2025-11-01 15:54:30'),
(41, 6, 'planning_line.view', 'Xem kế hoạch line', 'view', 'Xem kế hoạch từng dây chuyền', '2025-11-01 15:54:30'),
(42, 6, 'planning_line.create', 'Tạo kế hoạch line', 'create', 'Tạo kế hoạch cho line', '2025-11-01 15:54:30'),
(43, 6, 'planning_line.edit', 'Sửa kế hoạch line', 'edit', 'Điều chỉnh kế hoạch line', '2025-11-01 15:54:30'),
(44, 6, 'planning_line.approve', 'Phê duyệt kế hoạch line', 'approve', 'BOD phê duyệt kế hoạch line', '2025-11-01 15:54:30'),
(45, 7, 'shift.view', 'Xem danh sách ca', 'view', 'Xem thông tin ca làm việc', '2025-11-01 15:54:30'),
(46, 7, 'shift.view_own', 'Xem lịch ca của mình', 'view', 'Công nhân xem lịch ca của mình', '2025-11-01 15:54:30'),
(47, 7, 'shift.create', 'Tạo ca làm việc', 'create', 'Tạo ca sản xuất mới', '2025-11-01 15:54:30'),
(48, 7, 'shift.edit', 'Sửa thông tin ca', 'edit', 'Cập nhật thông tin ca', '2025-11-01 15:54:30'),
(49, 7, 'shift.delete', 'Xóa ca làm việc', 'delete', 'Xóa ca', '2025-11-01 15:54:30'),
(50, 7, 'shift_assignment.view', 'Xem phân công ca', 'view', 'Xem phân công nhân sự cho ca', '2025-11-01 15:54:30'),
(51, 7, 'shift_assignment.create', 'Phân công nhân sự', 'create', 'Gán nhân sự vào ca', '2025-11-01 15:54:30'),
(52, 7, 'shift_assignment.edit', 'Sửa phân công ca', 'edit', 'Thay đổi phân công', '2025-11-01 15:54:30'),
(53, 7, 'shift_assignment.delete', 'Xóa phân công', 'delete', 'Hủy phân công nhân sự', '2025-11-01 15:54:30'),
(54, 7, 'shift_assignment.confirm', 'Xác nhận nhận việc', 'confirm', 'Công nhân xác nhận nhận việc', '2025-11-01 15:54:30'),
(55, 7, 'machine_assignment.view', 'Xem gán máy cho ca', 'view', 'Xem máy được gán cho ca', '2025-11-01 15:54:30'),
(56, 7, 'machine_assignment.create', 'Gán máy cho ca', 'create', 'Gán máy móc vào ca sản xuất', '2025-11-01 15:54:30'),
(57, 7, 'machine_assignment.edit', 'Sửa gán máy', 'edit', 'Thay đổi máy cho ca', '2025-11-01 15:54:30'),
(58, 7, 'machine_assignment.delete', 'Xóa gán máy', 'delete', 'Hủy gán máy', '2025-11-01 15:54:30'),
(59, 8, 'production.view', 'Xem báo cáo sản xuất', 'view', 'Xem tổng hợp sản lượng sản xuất', '2025-11-01 15:54:30'),
(60, 8, 'production.view_own', 'Xem sản lượng của mình', 'view', 'Công nhân xem sản lượng của mình', '2025-11-01 15:54:30'),
(61, 8, 'production.create', 'Nhập báo cáo sản xuất', 'create', 'Nhập sản lượng sản xuất', '2025-11-01 15:54:30'),
(62, 8, 'production.edit', 'Sửa báo cáo sản xuất', 'edit', 'Cập nhật sản lượng', '2025-11-01 15:54:30'),
(63, 8, 'production.delete', 'Xóa báo cáo sản xuất', 'delete', 'Xóa báo cáo sản lượng', '2025-11-01 15:54:30'),
(64, 8, 'production_by_machine.view', 'Xem sản lượng theo máy', 'view', 'Theo dõi sản lượng từng máy', '2025-11-01 15:54:30'),
(65, 8, 'production_by_shift.view', 'Xem sản lượng theo ca', 'view', 'Theo dõi sản lượng từng ca', '2025-11-01 15:54:30'),
(66, 8, 'production_by_line.view', 'Xem sản lượng theo line', 'view', 'Theo dõi sản lượng từng dây chuyền', '2025-11-01 15:54:30'),
(67, 8, 'production.export', 'Xuất Excel sản lượng', 'export', 'Xuất báo cáo sản lượng ra Excel', '2025-11-01 15:54:30'),
(68, 9, 'shift_closing.view', 'Xem phiếu chốt ca', 'view', 'Xem phiếu chốt ca sản xuất', '2025-11-01 15:54:30'),
(69, 9, 'shift_closing.create', 'Tạo phiếu chốt ca', 'create', 'Tạo phiếu Finished/Waste sau ca', '2025-11-01 15:54:30'),
(70, 9, 'shift_closing.edit', 'Sửa phiếu chốt ca', 'edit', 'Cập nhật phiếu chốt ca', '2025-11-01 15:54:30'),
(71, 9, 'shift_closing.delete', 'Xóa phiếu chốt ca', 'delete', 'Xóa phiếu chốt ca', '2025-11-01 15:54:30'),
(72, 9, 'shift_closing.submit', 'Gửi phiếu chốt ca', 'submit', 'Line Manager gửi phiếu lên QC', '2025-11-01 15:54:30'),
(73, 9, 'shift_closing.approve', 'Phê duyệt chốt ca', 'approve', 'Line Manager phê duyệt chốt ca', '2025-11-01 15:54:30'),
(74, 9, 'shift_closing.reject', 'Từ chối chốt ca', 'reject', 'Từ chối phiếu chốt ca', '2025-11-01 15:54:30'),
(75, 9, 'waste_reason.create', 'Ghi nhận lý do lỗi', 'create', 'Ghi lý do phế phẩm', '2025-11-01 15:54:30'),
(76, 9, 'waste_reason.view', 'Xem lý do lỗi', 'view', 'Xem nguyên nhân phế phẩm', '2025-11-01 15:54:30'),
(77, 10, 'machine.view', 'Xem danh sách máy móc', 'view', 'Xem thông tin máy móc, dây chuyền', '2025-11-01 15:54:30'),
(78, 10, 'machine.create', 'Thêm máy móc mới', 'create', 'Thêm máy mới vào hệ thống', '2025-11-01 15:54:30'),
(79, 10, 'machine.edit', 'Sửa thông tin máy', 'edit', 'Cập nhật thông tin máy', '2025-11-01 15:54:30'),
(80, 10, 'machine.delete', 'Xóa máy móc', 'delete', 'Xóa máy khỏi hệ thống', '2025-11-01 15:54:30'),
(81, 10, 'machine.update_status', 'Cập nhật trạng thái máy', 'update', 'Kỹ thuật viên cập nhật trạng thái', '2025-11-01 15:54:30'),
(82, 10, 'machine.confirm_ready', 'Xác nhận máy Ready', 'confirm', 'Kỹ thuật viên xác nhận máy sẵn sàng', '2025-11-01 15:54:30'),
(83, 10, 'machine_maintenance.view', 'Xem lịch bảo trì', 'view', 'Xem kế hoạch bảo trì máy', '2025-11-01 15:54:30'),
(84, 10, 'machine_maintenance.create', 'Lập lịch bảo trì', 'create', 'Tạo kế hoạch bảo trì', '2025-11-01 15:54:30'),
(85, 10, 'machine_maintenance.edit', 'Sửa lịch bảo trì', 'edit', 'Cập nhật lịch bảo trì', '2025-11-01 15:54:30'),
(86, 10, 'machine_maintenance.delete', 'Xóa lịch bảo trì', 'delete', 'Hủy lịch bảo trì', '2025-11-01 15:54:30'),
(87, 10, 'machine_maintenance.complete', 'Hoàn thành bảo trì', 'complete', 'Đánh dấu bảo trì hoàn thành', '2025-11-01 15:54:30'),
(88, 11, 'incident.view', 'Xem danh sách sự cố', 'view', 'Xem sự cố máy móc, chất lượng', '2025-11-01 15:54:30'),
(89, 11, 'incident.create', 'Báo cáo sự cố', 'create', 'Công nhân/Line Manager báo cáo sự cố', '2025-11-01 15:54:30'),
(90, 11, 'incident.edit', 'Sửa thông tin sự cố', 'edit', 'Cập nhật thông tin sự cố', '2025-11-01 15:54:30'),
(91, 11, 'incident.delete', 'Xóa sự cố', 'delete', 'Xóa báo cáo sự cố', '2025-11-01 15:54:30'),
(92, 11, 'incident.assign', 'Phân công xử lý sự cố', 'assign', 'Gán kỹ thuật viên xử lý', '2025-11-01 15:54:30'),
(93, 11, 'incident.update', 'Cập nhật xử lý sự cố', 'update', 'Kỹ thuật viên cập nhật tiến độ', '2025-11-01 15:54:30'),
(94, 11, 'incident.resolve', 'Giải quyết sự cố', 'resolve', 'Đánh dấu sự cố đã xử lý', '2025-11-01 15:54:30'),
(95, 11, 'incident.close', 'Đóng sự cố', 'close', 'Đóng sự cố sau khi xử lý xong', '2025-11-01 15:54:30'),
(96, 11, 'incident.export', 'Xuất Excel sự cố', 'export', 'Xuất danh sách sự cố', '2025-11-01 15:54:30'),
(97, 12, 'material.view', 'Xem danh mục NVL', 'view', 'Xem danh sách nguyên vật liệu', '2025-11-01 15:54:30'),
(98, 12, 'material.create', 'Thêm NVL mới', 'create', 'Thêm nguyên vật liệu mới', '2025-11-01 15:54:30'),
(99, 12, 'material.edit', 'Sửa thông tin NVL', 'edit', 'Cập nhật thông tin NVL', '2025-11-01 15:54:30'),
(100, 12, 'material.delete', 'Xóa NVL', 'delete', 'Xóa nguyên vật liệu', '2025-11-01 15:54:30'),
(101, 12, 'material.view_stock', 'Xem tồn kho NVL', 'view', 'Xem số lượng tồn kho', '2025-11-01 15:54:30'),
(102, 12, 'material.export', 'Xuất Excel NVL', 'export', 'Xuất danh sách NVL', '2025-11-01 15:54:30'),
(103, 13, 'warehouse.view', 'Xem tổng quan kho', 'view', 'Xem thông tin kho tổng quát', '2025-11-01 15:54:30'),
(104, 13, 'warehouse_receipt.view', 'Xem phiếu nhập kho NVL', 'view', 'Xem phiếu nhập NVL', '2025-11-01 15:54:30'),
(105, 13, 'warehouse_receipt.create', 'Tạo phiếu nhập NVL', 'create', 'Nhập kho NVL theo PO', '2025-11-01 15:54:30'),
(106, 13, 'warehouse_receipt.edit', 'Sửa phiếu nhập NVL', 'edit', 'Cập nhật phiếu nhập', '2025-11-01 15:54:30'),
(107, 13, 'warehouse_receipt.delete', 'Xóa phiếu nhập NVL', 'delete', 'Hủy phiếu nhập', '2025-11-01 15:54:30'),
(108, 13, 'warehouse_issue.view', 'Xem phiếu xuất kho NVL', 'view', 'Xem phiếu xuất NVL', '2025-11-01 15:54:30'),
(109, 13, 'warehouse_issue.create', 'Tạo phiếu xuất NVL', 'create', 'Xuất NVL cho ca (theo BOM)', '2025-11-01 15:54:30'),
(110, 13, 'warehouse_issue.edit', 'Sửa phiếu xuất NVL', 'edit', 'Cập nhật phiếu xuất', '2025-11-01 15:54:30'),
(111, 13, 'warehouse_issue.delete', 'Xóa phiếu xuất NVL', 'delete', 'Hủy phiếu xuất', '2025-11-01 15:54:30'),
(112, 13, 'finished_goods_receipt.view', 'Xem phiếu nhập TP', 'view', 'Xem phiếu nhập thành phẩm', '2025-11-01 15:54:30'),
(113, 13, 'finished_goods_receipt.create', 'Tạo phiếu nhập TP', 'create', 'Nhập kho TP sau QC', '2025-11-01 15:54:30'),
(114, 13, 'finished_goods_receipt.edit', 'Sửa phiếu nhập TP', 'edit', 'Cập nhật phiếu nhập TP', '2025-11-01 15:54:30'),
(115, 13, 'finished_goods_receipt.delete', 'Xóa phiếu nhập TP', 'delete', 'Hủy phiếu nhập TP', '2025-11-01 15:54:30'),
(116, 13, 'finished_goods_receipt.approve', 'Phê duyệt nhập TP', 'approve', 'QC xác nhận cho nhập TP', '2025-11-01 15:54:30'),
(117, 13, 'finished_goods_issue.view', 'Xem phiếu xuất TP', 'view', 'Xem phiếu xuất thành phẩm', '2025-11-01 15:54:30'),
(118, 13, 'finished_goods_issue.create', 'Tạo phiếu xuất TP', 'create', 'Xuất TP giao hàng', '2025-11-01 15:54:30'),
(119, 13, 'finished_goods_issue.edit', 'Sửa phiếu xuất TP', 'edit', 'Cập nhật phiếu xuất TP', '2025-11-01 15:54:30'),
(120, 13, 'finished_goods_issue.delete', 'Xóa phiếu xuất TP', 'delete', 'Hủy phiếu xuất TP', '2025-11-01 15:54:30'),
(121, 13, 'stock.view', 'Xem tồn kho', 'view', 'Xem tồn kho NVL và TP', '2025-11-01 15:54:30'),
(122, 13, 'stock_report.view', 'Xem báo cáo tồn kho', 'view', 'Báo cáo tồn và luân chuyển', '2025-11-01 15:54:30'),
(123, 13, 'stock_report.export', 'Xuất Excel tồn kho', 'export', 'Xuất báo cáo tồn kho', '2025-11-01 15:54:30'),
(124, 14, 'qc_inspection.view', 'Xem phiếu kiểm tra QC', 'view', 'Xem phiếu QC sau ca', '2025-11-01 15:54:30'),
(125, 14, 'qc_inspection.create', 'Tạo phiếu kiểm tra QC', 'create', 'Tạo phiếu kiểm tra chất lượng', '2025-11-01 15:54:30'),
(126, 14, 'qc_inspection.edit', 'Sửa phiếu QC', 'edit', 'Cập nhật phiếu QC', '2025-11-01 15:54:30'),
(127, 14, 'qc_inspection.delete', 'Xóa phiếu QC', 'delete', 'Xóa phiếu QC', '2025-11-01 15:54:30'),
(128, 14, 'qc_inspection.approve', 'Phê duyệt QC', 'approve', 'QC approve sản phẩm đạt', '2025-11-01 15:54:30'),
(129, 14, 'qc_inspection.reject', 'Từ chối QC', 'reject', 'QC reject sản phẩm lỗi', '2025-11-01 15:54:30'),
(130, 14, 'qc_checklist.view', 'Xem checklist QC', 'view', 'Xem tiêu chuẩn kiểm tra', '2025-11-01 15:54:30'),
(131, 14, 'qc_defect.view', 'Xem lỗi phát hiện', 'view', 'Xem danh sách lỗi', '2025-11-01 15:54:30'),
(132, 14, 'qc_defect.create', 'Ghi nhận lỗi', 'create', 'Ghi lỗi và nguyên nhân', '2025-11-01 15:54:30'),
(133, 14, 'qc_defect.edit', 'Sửa lỗi', 'edit', 'Cập nhật thông tin lỗi', '2025-11-01 15:54:30'),
(134, 14, 'aql_standard.view', 'Xem tiêu chuẩn AQL', 'view', 'Xem Acceptable Quality Level', '2025-11-01 15:54:30'),
(135, 14, 'qc_report.view', 'Xem báo cáo QC', 'view', 'Báo cáo chất lượng tổng hợp', '2025-11-01 15:54:30'),
(136, 14, 'qc_report.export', 'Xuất Excel báo cáo QC', 'export', 'Xuất báo cáo chất lượng', '2025-11-01 15:54:30'),
(137, 15, 'staff.view', 'Xem danh sách nhân sự', 'view', 'Xem thông tin nhân viên, công nhân', '2025-11-01 15:54:30'),
(138, 15, 'staff.create', 'Thêm nhân sự mới', 'create', 'Thêm nhân viên/công nhân', '2025-11-01 15:54:30'),
(139, 15, 'staff.edit', 'Sửa thông tin nhân sự', 'edit', 'Cập nhật thông tin nhân sự', '2025-11-01 15:54:30'),
(140, 15, 'staff.delete', 'Xóa nhân sự', 'delete', 'Xóa nhân sự khỏi hệ thống', '2025-11-01 15:54:30'),
(141, 15, 'staff.export', 'Xuất Excel nhân sự', 'export', 'Xuất danh sách nhân sự', '2025-11-01 15:54:30'),
(142, 16, 'dashboard.view_all', 'Xem dashboard tổng quan', 'view', 'Dashboard cho BOD', '2025-11-01 15:54:30'),
(143, 16, 'dashboard.view_line', 'Xem dashboard dây chuyền', 'view', 'Dashboard cho Line Manager', '2025-11-01 15:54:30'),
(144, 16, 'dashboard.view_warehouse', 'Xem dashboard kho', 'view', 'Dashboard cho Warehouse', '2025-11-01 15:54:30'),
(145, 16, 'report.production_summary', 'Báo cáo tổng hợp sản xuất', 'view', 'Tổng hợp sản lượng, hiệu suất', '2025-11-01 15:54:30'),
(146, 16, 'report.quality_summary', 'Báo cáo tổng hợp chất lượng', 'view', 'Tổng hợp QC, tỷ lệ lỗi', '2025-11-01 15:54:30'),
(147, 16, 'report.inventory', 'Báo cáo tồn kho', 'view', 'Báo cáo tồn NVL và TP', '2025-11-01 15:54:30'),
(148, 16, 'report.material_movement', 'Báo cáo luân chuyển NVL', 'view', 'Nhập/xuất NVL theo thời gian', '2025-11-01 15:54:30'),
(149, 16, 'report.line_performance', 'Báo cáo hiệu suất line', 'view', 'Hiệu suất vận hành dây chuyền', '2025-11-01 15:54:30'),
(150, 16, 'report.shift_summary', 'Báo cáo tổng hợp ca', 'view', 'Tổng hợp sản lượng theo ca', '2025-11-01 15:54:30'),
(151, 16, 'report.efficiency', 'Báo cáo hiệu suất tổng thể', 'view', 'OEE, hiệu suất toàn hệ thống', '2025-11-01 15:54:30'),
(152, 16, 'report.financial', 'Báo cáo tài chính', 'view', 'Doanh thu, chi phí (BOD only)', '2025-11-01 15:54:30'),
(153, 16, 'report.defect_analysis', 'Phân tích lỗi sản phẩm', 'view', 'Phân tích nguyên nhân lỗi', '2025-11-01 15:54:30'),
(154, 16, 'report.maintenance', 'Báo cáo bảo trì', 'view', 'Lịch sử bảo trì máy móc', '2025-11-01 15:54:30'),
(155, 16, 'report.incident_history', 'Báo cáo lịch sử sự cố', 'view', 'Lịch sử sự cố và xử lý', '2025-11-01 15:54:30'),
(156, 16, 'report.export_all', 'Xuất tất cả báo cáo', 'export', 'Quyền xuất Excel tất cả báo cáo', '2025-11-01 15:54:30'),
(157, 17, 'user.view', 'Xem danh sách người dùng', 'view', 'Xem tài khoản user', '2025-11-01 15:54:30'),
(158, 17, 'user.create', 'Tạo người dùng mới', 'create', 'Thêm tài khoản user', '2025-11-01 15:54:30'),
(159, 17, 'user.edit', 'Sửa thông tin người dùng', 'edit', 'Cập nhật thông tin user', '2025-11-01 15:54:30'),
(160, 17, 'user.delete', 'Xóa người dùng', 'delete', 'Xóa tài khoản user', '2025-11-01 15:54:30'),
(161, 17, 'user.reset_password', 'Đặt lại mật khẩu', 'reset', 'Reset password cho user', '2025-11-01 15:54:30'),
(162, 17, 'user.lock', 'Khóa tài khoản', 'lock', 'Khóa user không cho đăng nhập', '2025-11-01 15:54:30'),
(163, 17, 'user.unlock', 'Mở khóa tài khoản', 'unlock', 'Mở khóa tài khoản user', '2025-11-01 15:54:30'),
(164, 17, 'user_role.assign', 'Gán vai trò cho user', 'assign', 'Phân quyền role cho user', '2025-11-01 15:54:30'),
(165, 17, 'audit_log.view', 'Xem nhật ký hoạt động', 'view', 'Xem audit log của user', '2025-11-01 15:54:30'),
(166, 17, 'audit_log.export', 'Xuất Excel nhật ký', 'export', 'Xuất audit log ra Excel', '2025-11-01 15:54:30'),
(167, 18, 'role.view', 'Xem danh sách vai trò', 'view', 'Xem roles trong hệ thống', '2025-11-01 15:54:30'),
(168, 18, 'role.create', 'Tạo vai trò mới', 'create', 'Thêm role mới', '2025-11-01 15:54:30'),
(169, 18, 'role.edit', 'Sửa vai trò', 'edit', 'Cập nhật role', '2025-11-01 15:54:30'),
(170, 18, 'role.delete', 'Xóa vai trò', 'delete', 'Xóa role', '2025-11-01 15:54:30'),
(171, 18, 'permission.view', 'Xem danh sách quyền', 'view', 'Xem permissions', '2025-11-01 15:54:30'),
(172, 18, 'role_permission.assign', 'Gán quyền cho vai trò', 'assign', 'Assign permissions cho role', '2025-11-01 15:54:30'),
(173, 18, 'system_settings.view', 'Xem cài đặt hệ thống', 'view', 'Xem system settings', '2025-11-01 15:54:30'),
(174, 18, 'system_settings.edit', 'Sửa cài đặt hệ thống', 'edit', 'Thay đổi system settings', '2025-11-01 15:54:30');

-- --------------------------------------------------------

--
-- Table structure for table `planning`
--

CREATE TABLE `planning` (
  `id_plan` int(15) NOT NULL,
  `plan_name` varchar(25) NOT NULL,
  `id_project` int(15) NOT NULL,
  `qty_target` int(11) NOT NULL,
  `end_date` date NOT NULL,
  `pl_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng kế hoạch - qty_target: cái/ca (số lượng bút mỗi ca)';

--
-- Dumping data for table `planning`
--

INSERT INTO `planning` (`id_plan`, `plan_name`, `id_project`, `qty_target`, `end_date`, `pl_status`) VALUES
(1001, 'Plan-test', 1001, 2000, '2023-11-14', 1);

-- --------------------------------------------------------

--
-- Table structure for table `plan_shift`
--

CREATE TABLE `plan_shift` (
  `id_planshift` int(15) NOT NULL,
  `id_plan` int(15) NOT NULL,
  `id_shift` int(15) NOT NULL,
  `id_staff` int(15) NOT NULL,
  `start_date` date NOT NULL,
  `ps_status` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plan_shift`
--

INSERT INTO `plan_shift` (`id_planshift`, `id_plan`, `id_shift`, `id_staff`, `start_date`, `ps_status`) VALUES
(1001, 1001, 1001, 1001, '2023-11-07', 1),
(1002, 1001, 1002, 1002, '2023-11-08', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id_product` int(25) NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `summary` longtext NOT NULL COMMENT 'Thông tin chi tiết sản phẩm bút bi',
  `application` varchar(100) NOT NULL COMMENT 'Màu mực: Xanh, Đen, Đỏ, Nhiều màu',
  `diameter` decimal(3,1) NOT NULL DEFAULT 0.5 COMMENT 'Đường kính bi viết (mm)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id_product`, `product_name`, `summary`, `application`, `diameter`) VALUES
(1001, 'Bút bi TL-079', 'Bút bi mực gel, thân nhựa trong suốt, viết mượt', 'Xanh dương', 0.5),
(1002, 'Bút bi TL-050', 'Bút bi dầu, thân nhựa màu, giá rẻ', 'Đen', 0.5),
(1003, 'Bút bi TL-100', 'Bút bi cao cấp, thân kim loại', 'Đỏ', 0.5),
(1004, 'Bút bi TL-Multi', 'Bút bi 4 màu, đa năng', 'Nhiều màu', 0.5);

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `id_project` int(25) NOT NULL,
  `project_name` varchar(50) NOT NULL,
  `id_cust` int(25) NOT NULL,
  `id_product` int(25) NOT NULL,
  `diameter` decimal(3,1) NOT NULL COMMENT 'Đường kính bi viết (mm): 0.5, 0.7, 1.0',
  `qty_request` int(15) NOT NULL,
  `entry_date` date NOT NULL,
  `pr_status` int(5) NOT NULL,
  `risk_flag` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Cờ nguy cơ trễ hạn: 0=Bình thường, 1=Nguy cơ trễ',
  `customer_request` text DEFAULT NULL COMMENT 'Yêu cầu đặc biệt của khách hàng',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Thời gian tạo đơn hàng'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng dự án - qty_request: cái (số lượng bút), diameter: mm (đường kính bi)';

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`id_project`, `project_name`, `id_cust`, `id_product`, `diameter`, `qty_request`, `entry_date`, `pr_status`, `risk_flag`, `customer_request`, `created_at`) VALUES
(1001, 'PJ-TEST', 1001, 1001, 7.0, 10000, '2023-11-06', 1, 0, NULL, '2025-11-01 18:11:56'),
(1002, 'ORD-1001-20251102-002', 1001, 1001, 0.5, 2000, '2025-11-10', 1, 0, '', '2025-11-02 10:14:37');

-- --------------------------------------------------------

--
-- Table structure for table `project_diameter_backup`
--

CREATE TABLE `project_diameter_backup` (
  `id_project` int(25) NOT NULL DEFAULT 0,
  `diameter` int(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_diameter_backup`
--

INSERT INTO `project_diameter_backup` (`id_project`, `diameter`) VALUES
(1001, 7);

-- --------------------------------------------------------

--
-- Table structure for table `p_machine`
--

CREATE TABLE `p_machine` (
  `id_pmachine` int(15) NOT NULL,
  `id_planshift` int(15) NOT NULL,
  `id_machine` int(15) NOT NULL,
  `mc_stats` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `p_machine`
--

INSERT INTO `p_machine` (`id_pmachine`, `id_planshift`, `id_machine`, `mc_stats`) VALUES
(1001, 1002, 1001, 1);

-- --------------------------------------------------------

--
-- Table structure for table `p_material`
--

CREATE TABLE `p_material` (
  `id_pmaterial` int(15) NOT NULL,
  `id_planshift` int(15) NOT NULL,
  `id_material` int(15) NOT NULL,
  `used_stock` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Nguyên liệu sản xuất - used_stock: gram';

--
-- Dumping data for table `p_material`
--

INSERT INTO `p_material` (`id_pmaterial`, `id_planshift`, `id_material`, `used_stock`) VALUES
(1001, 1002, 1001, 500);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL COMMENT 'Tên role (code): bod, line_manager, warehouse_staff, etc.',
  `role_display_name` varchar(100) NOT NULL COMMENT 'Tên hiển thị: Ban Giám Đốc, Trưởng dây chuyền, etc.',
  `description` text DEFAULT NULL COMMENT 'Mô tả chi tiết vai trò',
  `level` int(11) DEFAULT 0 COMMENT 'Cấp độ quyền hạn: BOD=100, Admin=90, Manager=70, Staff=50, Worker=10',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1=Active, 0=Inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng vai trò người dùng trong hệ thống';

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `role_display_name`, `description`, `level`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'bod', 'Ban Giám Đốc', 'Quản trị cấp cao, phê duyệt chiến lược, xem báo cáo tổng hợp. Quản lý khách hàng, sản phẩm, đơn hàng, phê duyệt kế hoạch sản xuất.', 100, 1, '2025-11-01 15:53:44', '2025-11-01 15:53:44'),
(2, 'line_manager', 'Trưởng dây chuyền', 'Vận hành line sản xuất, phân ca, điều phối nhân sự và máy móc, chốt ca. Quản lý nhân sự line, lập kế hoạch, theo dõi sản lượng, xử lý sự cố.', 70, 1, '2025-11-01 15:53:44', '2025-11-01 15:53:44'),
(3, 'warehouse_staff', 'Nhân viên Kho', 'Quản lý nguyên vật liệu và thành phẩm, xử lý chứng từ kho. Nhập/xuất kho NVL, nhập/xuất kho thành phẩm, xem báo cáo tồn kho.', 50, 1, '2025-11-01 15:53:44', '2025-11-01 15:53:44'),
(4, 'system_admin', 'Quản trị viên Hệ thống', 'Quản lý tài khoản người dùng và phân quyền. Tạo/sửa/khóa user, gán role và permissions, theo dõi nhật ký truy cập, cài đặt hệ thống.', 90, 1, '2025-11-01 15:53:44', '2025-11-01 15:53:44'),
(5, 'qc_staff', 'Nhân viên Kiểm soát Chất lượng', 'Kiểm soát chất lượng sản phẩm sau mỗi ca sản xuất. Kiểm tra theo checklist/AQL, phê duyệt/reject, ghi nhận lỗi, xác nhận cho nhập kho thành phẩm.', 60, 1, '2025-11-01 15:53:44', '2025-11-01 15:53:44'),
(6, 'technical_staff', 'Nhân viên Kỹ thuật', 'Bảo trì máy móc và xử lý sự cố kỹ thuật. Tiếp nhận sự cố, cập nhật tình trạng máy, xác nhận máy Ready, lập lịch bảo trì, xác nhận định mức NVL.', 60, 1, '2025-11-01 15:53:44', '2025-11-01 15:53:44'),
(7, 'worker', 'Công nhân Sản xuất', 'Thực hiện ca sản xuất và phản hồi hiện trường. Xem lịch ca, xác nhận nhận việc, báo cáo sự cố, xem sản lượng của mình.', 10, 1, '2025-11-01 15:53:44', '2025-11-01 15:53:44');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL COMMENT 'ID vai trò',
  `permission_id` int(11) NOT NULL COMMENT 'ID quyền hạn',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng liên kết nhiều-nhiều giữa Role và Permission';

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `created_at`) VALUES
(1, 1, 33, '2025-11-01 15:54:51'),
(2, 1, 28, '2025-11-01 15:54:51'),
(3, 1, 2, '2025-11-01 15:54:51'),
(4, 1, 4, '2025-11-01 15:54:51'),
(5, 1, 3, '2025-11-01 15:54:51'),
(6, 1, 5, '2025-11-01 15:54:51'),
(7, 1, 1, '2025-11-01 15:54:51'),
(8, 1, 19, '2025-11-01 15:54:51'),
(9, 1, 16, '2025-11-01 15:54:51'),
(10, 1, 18, '2025-11-01 15:54:51'),
(11, 1, 17, '2025-11-01 15:54:51'),
(12, 1, 21, '2025-11-01 15:54:51'),
(13, 1, 20, '2025-11-01 15:54:51'),
(14, 1, 15, '2025-11-01 15:54:51'),
(15, 1, 11, '2025-11-01 15:54:51'),
(16, 1, 13, '2025-11-01 15:54:51'),
(17, 1, 12, '2025-11-01 15:54:51'),
(18, 1, 10, '2025-11-01 15:54:51'),
(19, 1, 7, '2025-11-01 15:54:51'),
(20, 1, 9, '2025-11-01 15:54:51'),
(21, 1, 8, '2025-11-01 15:54:51'),
(22, 1, 14, '2025-11-01 15:54:51'),
(23, 1, 6, '2025-11-01 15:54:51'),
(24, 1, 26, '2025-11-01 15:54:51'),
(25, 1, 23, '2025-11-01 15:54:51'),
(26, 1, 24, '2025-11-01 15:54:51'),
(27, 1, 27, '2025-11-01 15:54:51'),
(28, 1, 22, '2025-11-01 15:54:51'),
(32, 1, 44, '2025-11-01 15:54:51'),
(33, 1, 41, '2025-11-01 15:54:51'),
(34, 1, 39, '2025-11-01 15:54:51'),
(35, 1, 35, '2025-11-01 15:54:51'),
(36, 1, 40, '2025-11-01 15:54:51'),
(37, 1, 34, '2025-11-01 15:54:51'),
(39, 1, 55, '2025-11-01 15:54:51'),
(40, 1, 50, '2025-11-01 15:54:51'),
(41, 1, 45, '2025-11-01 15:54:51'),
(42, 1, 66, '2025-11-01 15:54:51'),
(43, 1, 64, '2025-11-01 15:54:51'),
(44, 1, 65, '2025-11-01 15:54:51'),
(45, 1, 59, '2025-11-01 15:54:51'),
(46, 1, 68, '2025-11-01 15:54:51'),
(47, 1, 76, '2025-11-01 15:54:51'),
(49, 1, 142, '2025-11-01 15:54:51'),
(50, 1, 143, '2025-11-01 15:54:51'),
(51, 1, 144, '2025-11-01 15:54:51'),
(52, 1, 145, '2025-11-01 15:54:51'),
(53, 1, 146, '2025-11-01 15:54:51'),
(54, 1, 147, '2025-11-01 15:54:51'),
(55, 1, 148, '2025-11-01 15:54:51'),
(56, 1, 149, '2025-11-01 15:54:51'),
(57, 1, 150, '2025-11-01 15:54:51'),
(58, 1, 151, '2025-11-01 15:54:51'),
(59, 1, 152, '2025-11-01 15:54:51'),
(60, 1, 153, '2025-11-01 15:54:51'),
(61, 1, 154, '2025-11-01 15:54:51'),
(62, 1, 155, '2025-11-01 15:54:51'),
(63, 1, 156, '2025-11-01 15:54:51'),
(64, 2, 139, '2025-11-01 15:54:51'),
(65, 2, 141, '2025-11-01 15:54:51'),
(66, 2, 137, '2025-11-01 15:54:51'),
(67, 2, 77, '2025-11-01 15:54:51'),
(68, 2, 78, '2025-11-01 15:54:51'),
(69, 2, 79, '2025-11-01 15:54:51'),
(70, 2, 80, '2025-11-01 15:54:51'),
(71, 2, 81, '2025-11-01 15:54:51'),
(72, 2, 82, '2025-11-01 15:54:51'),
(73, 2, 83, '2025-11-01 15:54:51'),
(74, 2, 84, '2025-11-01 15:54:51'),
(75, 2, 85, '2025-11-01 15:54:51'),
(76, 2, 86, '2025-11-01 15:54:51'),
(77, 2, 87, '2025-11-01 15:54:51'),
(82, 2, 42, '2025-11-01 15:54:51'),
(83, 2, 43, '2025-11-01 15:54:51'),
(84, 2, 41, '2025-11-01 15:54:51'),
(85, 2, 35, '2025-11-01 15:54:51'),
(86, 2, 36, '2025-11-01 15:54:51'),
(87, 2, 38, '2025-11-01 15:54:51'),
(88, 2, 34, '2025-11-01 15:54:51'),
(89, 2, 45, '2025-11-01 15:54:51'),
(90, 2, 47, '2025-11-01 15:54:51'),
(91, 2, 48, '2025-11-01 15:54:51'),
(92, 2, 49, '2025-11-01 15:54:51'),
(93, 2, 50, '2025-11-01 15:54:51'),
(94, 2, 51, '2025-11-01 15:54:51'),
(95, 2, 52, '2025-11-01 15:54:51'),
(96, 2, 53, '2025-11-01 15:54:51'),
(97, 2, 54, '2025-11-01 15:54:51'),
(98, 2, 55, '2025-11-01 15:54:51'),
(99, 2, 56, '2025-11-01 15:54:51'),
(100, 2, 57, '2025-11-01 15:54:51'),
(101, 2, 58, '2025-11-01 15:54:51'),
(104, 2, 66, '2025-11-01 15:54:51'),
(105, 2, 64, '2025-11-01 15:54:51'),
(106, 2, 65, '2025-11-01 15:54:51'),
(107, 2, 61, '2025-11-01 15:54:51'),
(108, 2, 62, '2025-11-01 15:54:51'),
(109, 2, 67, '2025-11-01 15:54:51'),
(110, 2, 59, '2025-11-01 15:54:51'),
(111, 2, 88, '2025-11-01 15:54:51'),
(112, 2, 89, '2025-11-01 15:54:51'),
(113, 2, 90, '2025-11-01 15:54:51'),
(114, 2, 91, '2025-11-01 15:54:51'),
(115, 2, 92, '2025-11-01 15:54:51'),
(116, 2, 93, '2025-11-01 15:54:51'),
(117, 2, 94, '2025-11-01 15:54:51'),
(118, 2, 95, '2025-11-01 15:54:51'),
(119, 2, 96, '2025-11-01 15:54:51'),
(126, 2, 68, '2025-11-01 15:54:51'),
(127, 2, 69, '2025-11-01 15:54:51'),
(128, 2, 70, '2025-11-01 15:54:51'),
(129, 2, 71, '2025-11-01 15:54:51'),
(130, 2, 72, '2025-11-01 15:54:51'),
(131, 2, 73, '2025-11-01 15:54:51'),
(132, 2, 74, '2025-11-01 15:54:51'),
(133, 2, 75, '2025-11-01 15:54:51'),
(134, 2, 76, '2025-11-01 15:54:51'),
(141, 2, 143, '2025-11-01 15:54:51'),
(142, 2, 153, '2025-11-01 15:54:51'),
(143, 2, 155, '2025-11-01 15:54:51'),
(144, 2, 149, '2025-11-01 15:54:51'),
(145, 2, 145, '2025-11-01 15:54:51'),
(146, 2, 150, '2025-11-01 15:54:51'),
(148, 2, 28, '2025-11-01 15:54:51'),
(149, 2, 10, '2025-11-01 15:54:51'),
(150, 2, 6, '2025-11-01 15:54:51'),
(151, 2, 22, '2025-11-01 15:54:51'),
(155, 3, 97, '2025-11-01 15:54:51'),
(156, 3, 98, '2025-11-01 15:54:51'),
(157, 3, 99, '2025-11-01 15:54:51'),
(158, 3, 100, '2025-11-01 15:54:51'),
(159, 3, 101, '2025-11-01 15:54:51'),
(160, 3, 102, '2025-11-01 15:54:51'),
(162, 3, 103, '2025-11-01 15:54:51'),
(163, 3, 104, '2025-11-01 15:54:51'),
(164, 3, 105, '2025-11-01 15:54:51'),
(165, 3, 106, '2025-11-01 15:54:51'),
(166, 3, 107, '2025-11-01 15:54:51'),
(167, 3, 108, '2025-11-01 15:54:51'),
(168, 3, 109, '2025-11-01 15:54:51'),
(169, 3, 110, '2025-11-01 15:54:51'),
(170, 3, 111, '2025-11-01 15:54:51'),
(171, 3, 112, '2025-11-01 15:54:51'),
(172, 3, 113, '2025-11-01 15:54:51'),
(173, 3, 114, '2025-11-01 15:54:51'),
(174, 3, 115, '2025-11-01 15:54:51'),
(175, 3, 116, '2025-11-01 15:54:51'),
(176, 3, 117, '2025-11-01 15:54:51'),
(177, 3, 118, '2025-11-01 15:54:51'),
(178, 3, 119, '2025-11-01 15:54:51'),
(179, 3, 120, '2025-11-01 15:54:51'),
(180, 3, 121, '2025-11-01 15:54:51'),
(181, 3, 122, '2025-11-01 15:54:51'),
(182, 3, 123, '2025-11-01 15:54:51'),
(193, 3, 28, '2025-11-01 15:54:51'),
(194, 3, 15, '2025-11-01 15:54:51'),
(195, 3, 22, '2025-11-01 15:54:51'),
(197, 3, 34, '2025-11-01 15:54:51'),
(198, 3, 45, '2025-11-01 15:54:51'),
(200, 3, 144, '2025-11-01 15:54:51'),
(201, 3, 147, '2025-11-01 15:54:51'),
(202, 3, 148, '2025-11-01 15:54:51'),
(207, 4, 157, '2025-11-01 15:54:51'),
(208, 4, 158, '2025-11-01 15:54:51'),
(209, 4, 159, '2025-11-01 15:54:51'),
(210, 4, 160, '2025-11-01 15:54:51'),
(211, 4, 161, '2025-11-01 15:54:51'),
(212, 4, 162, '2025-11-01 15:54:51'),
(213, 4, 163, '2025-11-01 15:54:51'),
(214, 4, 164, '2025-11-01 15:54:51'),
(215, 4, 165, '2025-11-01 15:54:51'),
(216, 4, 166, '2025-11-01 15:54:51'),
(222, 4, 167, '2025-11-01 15:54:51'),
(223, 4, 168, '2025-11-01 15:54:51'),
(224, 4, 169, '2025-11-01 15:54:51'),
(225, 4, 170, '2025-11-01 15:54:51'),
(226, 4, 171, '2025-11-01 15:54:51'),
(227, 4, 172, '2025-11-01 15:54:51'),
(228, 4, 173, '2025-11-01 15:54:51'),
(229, 4, 174, '2025-11-01 15:54:51'),
(237, 4, 1, '2025-11-01 15:54:51'),
(238, 4, 6, '2025-11-01 15:54:51'),
(239, 4, 10, '2025-11-01 15:54:51'),
(240, 4, 15, '2025-11-01 15:54:51'),
(241, 4, 22, '2025-11-01 15:54:51'),
(242, 4, 28, '2025-11-01 15:54:51'),
(243, 4, 34, '2025-11-01 15:54:51'),
(244, 4, 41, '2025-11-01 15:54:51'),
(245, 4, 45, '2025-11-01 15:54:51'),
(246, 4, 46, '2025-11-01 15:54:51'),
(247, 4, 50, '2025-11-01 15:54:51'),
(248, 4, 55, '2025-11-01 15:54:51'),
(249, 4, 59, '2025-11-01 15:54:51'),
(250, 4, 60, '2025-11-01 15:54:51'),
(251, 4, 64, '2025-11-01 15:54:51'),
(252, 4, 65, '2025-11-01 15:54:51'),
(253, 4, 66, '2025-11-01 15:54:51'),
(254, 4, 68, '2025-11-01 15:54:51'),
(255, 4, 76, '2025-11-01 15:54:51'),
(256, 4, 77, '2025-11-01 15:54:51'),
(257, 4, 83, '2025-11-01 15:54:51'),
(258, 4, 88, '2025-11-01 15:54:51'),
(259, 4, 97, '2025-11-01 15:54:51'),
(260, 4, 101, '2025-11-01 15:54:51'),
(261, 4, 103, '2025-11-01 15:54:51'),
(262, 4, 104, '2025-11-01 15:54:51'),
(263, 4, 108, '2025-11-01 15:54:51'),
(264, 4, 112, '2025-11-01 15:54:51'),
(265, 4, 117, '2025-11-01 15:54:51'),
(266, 4, 121, '2025-11-01 15:54:51'),
(267, 4, 122, '2025-11-01 15:54:51'),
(268, 4, 124, '2025-11-01 15:54:51'),
(269, 4, 130, '2025-11-01 15:54:51'),
(270, 4, 131, '2025-11-01 15:54:51'),
(271, 4, 134, '2025-11-01 15:54:51'),
(272, 4, 135, '2025-11-01 15:54:51'),
(273, 4, 137, '2025-11-01 15:54:51'),
(274, 4, 142, '2025-11-01 15:54:51'),
(275, 4, 143, '2025-11-01 15:54:51'),
(276, 4, 144, '2025-11-01 15:54:51'),
(277, 4, 145, '2025-11-01 15:54:51'),
(278, 4, 146, '2025-11-01 15:54:51'),
(279, 4, 147, '2025-11-01 15:54:51'),
(280, 4, 148, '2025-11-01 15:54:51'),
(281, 4, 149, '2025-11-01 15:54:51'),
(282, 4, 150, '2025-11-01 15:54:51'),
(283, 4, 151, '2025-11-01 15:54:51'),
(284, 4, 152, '2025-11-01 15:54:51'),
(285, 4, 153, '2025-11-01 15:54:51'),
(286, 4, 154, '2025-11-01 15:54:51'),
(287, 4, 155, '2025-11-01 15:54:51'),
(300, 5, 124, '2025-11-01 15:54:51'),
(301, 5, 125, '2025-11-01 15:54:51'),
(302, 5, 126, '2025-11-01 15:54:51'),
(303, 5, 127, '2025-11-01 15:54:51'),
(304, 5, 128, '2025-11-01 15:54:51'),
(305, 5, 129, '2025-11-01 15:54:51'),
(306, 5, 130, '2025-11-01 15:54:51'),
(307, 5, 131, '2025-11-01 15:54:51'),
(308, 5, 132, '2025-11-01 15:54:51'),
(309, 5, 133, '2025-11-01 15:54:51'),
(310, 5, 134, '2025-11-01 15:54:51'),
(311, 5, 135, '2025-11-01 15:54:51'),
(312, 5, 136, '2025-11-01 15:54:51'),
(315, 5, 68, '2025-11-01 15:54:51'),
(316, 5, 76, '2025-11-01 15:54:51'),
(318, 5, 116, '2025-11-01 15:54:51'),
(319, 5, 112, '2025-11-01 15:54:51'),
(321, 5, 10, '2025-11-01 15:54:51'),
(322, 5, 6, '2025-11-01 15:54:51'),
(323, 5, 22, '2025-11-01 15:54:51'),
(324, 5, 153, '2025-11-01 15:54:51'),
(325, 5, 146, '2025-11-01 15:54:51'),
(327, 6, 95, '2025-11-01 15:54:51'),
(328, 6, 96, '2025-11-01 15:54:51'),
(329, 6, 94, '2025-11-01 15:54:51'),
(330, 6, 93, '2025-11-01 15:54:51'),
(331, 6, 88, '2025-11-01 15:54:51'),
(334, 6, 87, '2025-11-01 15:54:51'),
(335, 6, 84, '2025-11-01 15:54:51'),
(336, 6, 86, '2025-11-01 15:54:51'),
(337, 6, 85, '2025-11-01 15:54:51'),
(338, 6, 83, '2025-11-01 15:54:51'),
(339, 6, 82, '2025-11-01 15:54:51'),
(340, 6, 81, '2025-11-01 15:54:51'),
(341, 6, 77, '2025-11-01 15:54:51'),
(349, 6, 32, '2025-11-01 15:54:51'),
(350, 6, 28, '2025-11-01 15:54:51'),
(352, 6, 55, '2025-11-01 15:54:52'),
(353, 6, 34, '2025-11-01 15:54:52'),
(354, 6, 45, '2025-11-01 15:54:52'),
(355, 6, 155, '2025-11-01 15:54:52'),
(356, 6, 154, '2025-11-01 15:54:52'),
(358, 7, 54, '2025-11-01 15:54:52'),
(359, 7, 50, '2025-11-01 15:54:52'),
(360, 7, 46, '2025-11-01 15:54:52'),
(361, 7, 60, '2025-11-01 15:54:52'),
(362, 7, 89, '2025-11-01 15:54:52'),
(363, 7, 10, '2025-11-01 15:54:52'),
(364, 7, 6, '2025-11-01 15:54:52'),
(365, 7, 22, '2025-11-01 15:54:52');

-- --------------------------------------------------------

--
-- Table structure for table `shiftment`
--

CREATE TABLE `shiftment` (
  `id_shift` int(11) NOT NULL,
  `shift_name` varchar(50) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shiftment`
--

INSERT INTO `shiftment` (`id_shift`, `shift_name`, `start_time`, `end_time`) VALUES
(1001, 'Ca Sáng', '09:00:00', '17:00:00'),
(1002, 'Ca Chiều', '14:00:00', '22:00:00'),
(1003, 'Ca Tối', '22:00:00', '06:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `sorting_report`
--

CREATE TABLE `sorting_report` (
  `id_sorting` int(15) NOT NULL,
  `id_planshift` int(15) NOT NULL,
  `waste` int(50) NOT NULL,
  `finished` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Báo cáo phân loại - waste & finished: cái (số lượng bút)';

--
-- Dumping data for table `sorting_report`
--

INSERT INTO `sorting_report` (`id_sorting`, `id_planshift`, `waste`, `finished`) VALUES
(1001, 1002, 50, 1950);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id_staff` int(11) NOT NULL,
  `staff_name` varchar(50) NOT NULL,
  `phone` int(15) NOT NULL,
  `email` varchar(25) NOT NULL,
  `st_status` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id_staff`, `staff_name`, `phone`, `email`, `st_status`) VALUES
(1001, 'Leader1', 8212312, 'leader1@mail.com', 2),
(1002, 'Leader2', 8923321, 'leader2@mail.com', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(11) NOT NULL,
  `password` varchar(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL COMMENT 'ID vai trò',
  `staff_id` int(11) DEFAULT NULL COMMENT 'Link to staff table nếu là công nhân',
  `full_name` varchar(100) DEFAULT NULL COMMENT 'Họ và tên đầy đủ',
  `email` varchar(100) DEFAULT NULL COMMENT 'Email liên hệ',
  `phone` varchar(20) DEFAULT NULL COMMENT 'Số điện thoại',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1=Active, 0=Locked',
  `last_login` timestamp NULL DEFAULT NULL COMMENT 'Lần đăng nhập cuối',
  `created_by` int(11) DEFAULT NULL COMMENT 'User tạo tài khoản này',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `role_id`, `staff_id`, `full_name`, `email`, `phone`, `is_active`, `last_login`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin', 4, NULL, 'Administrator', NULL, NULL, 1, '2025-11-22 19:02:41', NULL, '2025-11-01 15:49:53', '2025-11-22 19:02:41'),
(2, 'leader', 'leader', 2, NULL, 'Trưởng dây chuyền', NULL, NULL, 1, NULL, NULL, '2025-11-01 15:49:53', '2025-11-01 15:53:44'),
(3, 'bod', 'bod123', 1, NULL, 'Nguyễn Văn A - Giám Đốc', 'bod@company.com', NULL, 1, '2025-11-23 15:30:09', NULL, '2025-11-01 15:53:44', '2025-11-23 15:30:09'),
(4, 'line_manage', 'line123', 2, NULL, 'Trần Văn B - Trưởng line 2', 'linemanager@company.com', NULL, 1, NULL, NULL, '2025-11-01 15:53:45', '2025-11-22 18:20:46'),
(5, 'warehouse', 'wh123', 3, NULL, 'Lê Thị C - Nhân viên kho', 'warehouse@company.com', NULL, 1, NULL, NULL, '2025-11-01 15:53:45', '2025-11-22 18:20:46'),
(6, 'qc', 'qc123', 5, NULL, 'Phạm Văn D - Nhân viên QC', 'qc@company.com', NULL, 1, NULL, NULL, '2025-11-01 15:53:45', '2025-11-22 18:20:46'),
(7, 'technical', 'tech123', 6, NULL, 'Hoàng Văn E - Kỹ thuật viên', 'technical@company.com', NULL, 1, NULL, NULL, '2025-11-01 15:53:45', '2025-11-22 18:20:46'),
(8, 'worker', 'worker123', 7, NULL, 'Nguyễn Thị F - Công nhân', 'worker@company.com', NULL, 1, NULL, NULL, '2025-11-01 15:53:45', '2025-11-22 18:20:46');

-- --------------------------------------------------------

--
-- Table structure for table `v_material_stock`
--

CREATE TABLE `v_material_stock` (
  `id_material` int(50) DEFAULT NULL,
  `material_name` varchar(50) DEFAULT NULL,
  `stock_display` varchar(55) DEFAULT NULL,
  `stock` int(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `v_project_details`
--

CREATE TABLE `v_project_details` (
  `id_project` int(25) DEFAULT NULL,
  `project_name` varchar(50) DEFAULT NULL,
  `cust_name` varchar(50) DEFAULT NULL,
  `product_name` varchar(50) DEFAULT NULL,
  `diameter_display` varchar(12) DEFAULT NULL,
  `qty_request_display` varchar(19) DEFAULT NULL,
  `entry_date` date DEFAULT NULL,
  `pr_status` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_module` (`module`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_composite` (`user_id`,`action`,`created_at`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id_cust`);

--
-- Indexes for table `finished_report`
--
ALTER TABLE `finished_report`
  ADD PRIMARY KEY (`id_finished`),
  ADD KEY `fk_finished_project` (`id_project`);

--
-- Indexes for table `machine`
--
ALTER TABLE `machine`
  ADD PRIMARY KEY (`id_machine`);

--
-- Indexes for table `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`id_material`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`module_id`),
  ADD UNIQUE KEY `module_name` (`module_name`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `idx_module_name` (`module_name`),
  ADD KEY `idx_sort_order` (`sort_order`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`),
  ADD KEY `idx_permission_name` (`permission_name`),
  ADD KEY `idx_module_id` (`module_id`),
  ADD KEY `idx_action` (`action`);

--
-- Indexes for table `planning`
--
ALTER TABLE `planning`
  ADD PRIMARY KEY (`id_plan`),
  ADD KEY `fk_planning_project` (`id_project`);

--
-- Indexes for table `plan_shift`
--
ALTER TABLE `plan_shift`
  ADD PRIMARY KEY (`id_planshift`),
  ADD KEY `fk_planshift_planning` (`id_plan`),
  ADD KEY `fk_planshift_shift` (`id_shift`),
  ADD KEY `fk_planshift_staff` (`id_staff`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id_product`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id_project`),
  ADD KEY `fk_project_product` (`id_product`),
  ADD KEY `idx_created_cust` (`id_cust`,`created_at`),
  ADD KEY `idx_risk_status` (`risk_flag`,`pr_status`);

--
-- Indexes for table `p_machine`
--
ALTER TABLE `p_machine`
  ADD PRIMARY KEY (`id_pmachine`),
  ADD KEY `fk_pmachine_planshift` (`id_planshift`),
  ADD KEY `fk_pmachine_machine` (`id_machine`);

--
-- Indexes for table `p_material`
--
ALTER TABLE `p_material`
  ADD PRIMARY KEY (`id_pmaterial`),
  ADD KEY `fk_pmaterial_planshift` (`id_planshift`),
  ADD KEY `fk_pmaterial_material` (`id_material`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`),
  ADD KEY `idx_role_name` (`role_name`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_permission` (`role_id`,`permission_id`),
  ADD KEY `idx_role_id` (`role_id`),
  ADD KEY `idx_permission_id` (`permission_id`);

--
-- Indexes for table `shiftment`
--
ALTER TABLE `shiftment`
  ADD PRIMARY KEY (`id_shift`);

--
-- Indexes for table `sorting_report`
--
ALTER TABLE `sorting_report`
  ADD PRIMARY KEY (`id_sorting`),
  ADD KEY `fk_sorting_planshift` (`id_planshift`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id_staff`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `fk_user_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `log_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id_cust` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1003;

--
-- AUTO_INCREMENT for table `machine`
--
ALTER TABLE `machine`
  MODIFY `id_machine` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1003;

--
-- AUTO_INCREMENT for table `material`
--
ALTER TABLE `material`
  MODIFY `id_material` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1009;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `module_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

--
-- AUTO_INCREMENT for table `planning`
--
ALTER TABLE `planning`
  MODIFY `id_plan` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1002;

--
-- AUTO_INCREMENT for table `plan_shift`
--
ALTER TABLE `plan_shift`
  MODIFY `id_planshift` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1003;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id_product` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1005;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `id_project` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1008;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=366;

--
-- AUTO_INCREMENT for table `shiftment`
--
ALTER TABLE `shiftment`
  MODIFY `id_shift` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1004;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id_staff` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1003;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `finished_report`
--
ALTER TABLE `finished_report`
  ADD CONSTRAINT `fk_finished_project` FOREIGN KEY (`id_project`) REFERENCES `project` (`id_project`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `modules`
--
ALTER TABLE `modules`
  ADD CONSTRAINT `modules_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `modules` (`module_id`) ON DELETE SET NULL;

--
-- Constraints for table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `modules` (`module_id`) ON DELETE CASCADE;

--
-- Constraints for table `planning`
--
ALTER TABLE `planning`
  ADD CONSTRAINT `fk_planning_project` FOREIGN KEY (`id_project`) REFERENCES `project` (`id_project`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `plan_shift`
--
ALTER TABLE `plan_shift`
  ADD CONSTRAINT `fk_planshift_planning` FOREIGN KEY (`id_plan`) REFERENCES `planning` (`id_plan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_planshift_shift` FOREIGN KEY (`id_shift`) REFERENCES `shiftment` (`id_shift`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_planshift_staff` FOREIGN KEY (`id_staff`) REFERENCES `staff` (`id_staff`) ON UPDATE CASCADE;

--
-- Constraints for table `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `fk_project_customer` FOREIGN KEY (`id_cust`) REFERENCES `customer` (`id_cust`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_project_product` FOREIGN KEY (`id_product`) REFERENCES `product` (`id_product`) ON UPDATE CASCADE;

--
-- Constraints for table `p_machine`
--
ALTER TABLE `p_machine`
  ADD CONSTRAINT `fk_pmachine_machine` FOREIGN KEY (`id_machine`) REFERENCES `machine` (`id_machine`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pmachine_planshift` FOREIGN KEY (`id_planshift`) REFERENCES `plan_shift` (`id_planshift`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `p_material`
--
ALTER TABLE `p_material`
  ADD CONSTRAINT `fk_pmaterial_material` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pmaterial_planshift` FOREIGN KEY (`id_planshift`) REFERENCES `plan_shift` (`id_planshift`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE CASCADE;

--
-- Constraints for table `sorting_report`
--
ALTER TABLE `sorting_report`
  ADD CONSTRAINT `fk_sorting_planshift` FOREIGN KEY (`id_planshift`) REFERENCES `plan_shift` (`id_planshift`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
