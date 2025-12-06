-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 05, 2025 lúc 05:40 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `db_production`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `audit_log`
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
-- Đang đổ dữ liệu cho bảng `audit_log`
--

INSERT INTO `audit_log` (`log_id`, `user_id`, `username`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-22 18:26:39'),
(2, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-22 18:27:25'),
(3, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-22 18:42:58'),
(4, 3, 'bod', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-22 19:02:38'),
(5, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-22 19:02:41'),
(6, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-23 15:30:09'),
(7, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-24 13:16:27'),
(8, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-24 15:55:31'),
(9, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-24 16:16:37'),
(10, 3, 'bod', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-24 16:16:50'),
(11, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-24 16:16:53'),
(12, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-24 16:17:11'),
(13, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-24 16:30:27'),
(14, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-24 16:33:51'),
(15, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-24 16:37:20'),
(16, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-24 16:42:06'),
(17, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-24 16:54:57'),
(18, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-24 16:58:32'),
(19, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-24 17:01:48'),
(20, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-24 17:06:32'),
(21, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-24 17:12:45'),
(22, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-27 18:09:03'),
(23, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-27 18:45:22'),
(24, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-28 08:24:19'),
(25, 3, 'bod', 'create', 'customer', 1002, NULL, '{\"cust_name\":\"danh\",\"address\":\"gò vấp\",\"telp\":\"0978678987\",\"email\":\"danh@gmail.com\",\"notes\":\"nhanh nhé\",\"is_active\":1,\"id_cust\":1002,\"created_by\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-28 08:35:11'),
(26, 3, 'bod', 'delete', 'customer', 1002, '{\"id_cust\":\"1002\",\"cust_name\":\"danh\",\"address\":\"gò vấp\",\"telp\":\"978678987\",\"email\":\"danh@gmail.com\",\"is_active\":\"1\",\"notes\":\"nhanh nhé\",\"created_at\":\"2025-11-28 15:35:11\",\"updated_at\":\"2025-11-28 15:35:11\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"created_by_username\":\"bod\"}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-28 08:36:38'),
(27, 3, 'bod', 'create', 'customer', 1002, NULL, '{\"cust_name\":\"danh\",\"address\":\"gò vấp\",\"telp\":\"0986789876\",\"email\":\"danh@gmail.com\",\"notes\":\"vip\",\"is_active\":1,\"id_cust\":1002,\"created_by\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-28 08:39:37'),
(28, 3, 'bod', 'update', 'customer', 1002, '{\"id_cust\":\"1002\",\"cust_name\":\"danh\",\"address\":\"gò vấp\",\"telp\":\"986789876\",\"email\":\"danh@gmail.com\",\"is_active\":\"1\",\"notes\":\"vip\",\"created_at\":\"2025-11-28 15:39:37\",\"updated_at\":\"2025-11-28 15:39:37\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"created_by_username\":\"bod\"}', '{\"cust_name\":\"danh\",\"address\":\"gò vấ\",\"telp\":\"986789876\",\"email\":\"danh@gmail.com\",\"notes\":\"vip\",\"is_active\":0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-28 08:39:54'),
(29, 3, 'bod', 'delete', 'customer', 1002, '{\"id_cust\":\"1002\",\"cust_name\":\"danh\",\"address\":\"gò vấ\",\"telp\":\"986789876\",\"email\":\"danh@gmail.com\",\"is_active\":\"0\",\"notes\":\"vip\",\"created_at\":\"2025-11-28 15:39:37\",\"updated_at\":\"2025-11-28 15:39:54\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"created_by_username\":\"bod\"}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-28 08:40:53'),
(30, 3, 'bod', 'create', 'product', 1005, NULL, '{\"product_name\":\"bút mực\",\"summary\":\"\",\"application\":\"tím\",\"diameter\":\"0.7\",\"is_active\":1,\"bom\":null,\"id_product\":1005,\"created_by\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-28 08:41:49'),
(31, 3, 'bod', 'update', 'product', 1004, '{\"id_product\":\"1004\",\"product_name\":\"Bút bi TL-Multi\",\"summary\":\"Bút bi 4 màu, đa năng\",\"application\":\"Nhiều màu\",\"diameter\":\"0.5\",\"bom\":null,\"is_active\":\"1\",\"created_at\":\"2025-11-24 22:53:58\",\"updated_at\":\"2025-11-24 22:53:58\",\"created_by\":null,\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"diameter_display\":\"0.5mm\",\"created_by_username\":null,\"bom_data\":{\"materials\":[]}}', '{\"product_name\":\"Bút bi TL-Multi\",\"summary\":\"Bút bi 4 màu, đa năng\",\"application\":\"tím\",\"diameter\":\"0.5\",\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-28 08:42:10'),
(32, 3, 'bod', 'update', 'product', 1004, '{\"id_product\":\"1004\",\"product_name\":\"Bút bi TL-Multi\",\"summary\":\"Bút bi 4 màu, đa năng\",\"application\":\"tím\",\"diameter\":\"0.5\",\"bom\":null,\"is_active\":\"1\",\"created_at\":\"2025-11-24 22:53:58\",\"updated_at\":\"2025-11-28 15:42:10\",\"created_by\":null,\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"diameter_display\":\"0.5mm\",\"created_by_username\":null,\"bom_data\":{\"materials\":[]}}', '{\"product_name\":\"Bút bi TL-Multi\",\"summary\":\"Bút bi 4 màu, đa năng\",\"application\":\"nhiều màu\",\"diameter\":\"0.5\",\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-28 08:42:55'),
(33, 3, 'bod', 'create', 'customer', 1002, NULL, '{\"cust_name\":\"danh\",\"address\":\"nhỏ\",\"telp\":\"0976688686\",\"email\":\"danh@gmail.com\",\"notes\":\"thường\",\"is_active\":1,\"id_cust\":1002,\"created_by\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-28 08:43:33'),
(34, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-29 09:34:02'),
(35, 3, 'bod', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 07:28:59'),
(36, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 07:29:06'),
(37, 3, 'bod', 'create', 'planning', 1002, NULL, '{\"plan_name\":\"test\",\"id_project\":1002,\"qty_target\":10000,\"end_date\":\"2025-12-05\",\"pl_status\":0}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 08:21:24'),
(38, 3, 'bod', 'create', 'planning', 1003, NULL, '{\"plan_name\":\"test\",\"id_project\":1002,\"qty_target\":10000,\"end_date\":\"2025-12-02\",\"pl_status\":0}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 09:02:02'),
(39, 3, 'bod', 'create', 'planning', 1004, NULL, '{\"plan_name\":\"test\",\"id_project\":1003,\"qty_target\":1000,\"end_date\":\"2025-12-03\",\"pl_status\":0}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 09:05:11'),
(40, 3, 'bod', 'create', 'planning', 1005, NULL, '{\"plan_name\":\"test\",\"id_project\":1003,\"qty_target\":1000,\"end_date\":\"2025-12-03\",\"pl_status\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 09:34:27'),
(41, 3, 'bod', 'approve', 'planning', 1005, '{\"id_plan\":\"1005\",\"plan_name\":\"test\",\"id_project\":\"1003\",\"qty_target\":\"1000\",\"end_date\":\"2025-12-03\",\"pl_status\":\"1\"}', '{\"pl_status\":1,\"note\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 09:39:28'),
(42, 3, 'bod', 'create', 'planning', 1006, NULL, '{\"plan_name\":\"test\",\"id_project\":1003,\"qty_target\":1000,\"end_date\":\"2025-12-01\",\"pl_status\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 10:02:29'),
(43, 3, 'bod', 'create', 'planning', 1007, NULL, '{\"plan_name\":\"test\",\"id_project\":1003,\"qty_target\":1000,\"end_date\":\"2025-12-03\",\"pl_status\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 10:29:05'),
(44, 3, 'bod', 'create', 'planning', 1008, NULL, '{\"plan_name\":\"test\",\"id_project\":1002,\"qty_target\":10000,\"end_date\":\"2025-12-03\",\"pl_status\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 10:30:14'),
(45, 3, 'bod', 'create', 'planning', 1009, NULL, '{\"plan_name\":\"test\",\"id_project\":1002,\"qty_target\":10000,\"end_date\":\"2025-12-01\",\"pl_status\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 10:35:05'),
(46, 3, 'bod', 'create', 'planning', 1013, NULL, '{\"plan_name\":\"test01\",\"id_project\":1002,\"order_qty\":9999,\"qty_target\":1000,\"end_date\":\"2025-11-10\",\"pl_status\":1,\"note\":\"\",\"materials_json\":\"[{\\\"id_material\\\":\\\"1001\\\",\\\"qty\\\":1000}]\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 11:40:25'),
(47, 3, 'bod', 'create', 'planning', 1018, NULL, '{\"plan_name\":\"test01\",\"id_project\":1002,\"order_qty\":9999,\"qty_target\":10000,\"end_date\":\"2025-12-01\",\"pl_status\":1,\"note\":\"Bút đỏ\",\"materials_json\":\"[]\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 12:08:38'),
(48, 3, 'bod', 'create', 'planning', 1020, NULL, '{\"plan_name\":\"test02\",\"id_project\":1002,\"order_qty\":9999,\"qty_target\":10000,\"end_date\":\"2025-12-03\",\"pl_status\":1,\"note\":\"màu đỏ\",\"materials_json\":\"[]\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 12:10:00'),
(49, 3, 'bod', 'create', 'planning', 1021, NULL, '{\"plan_name\":\"test01\",\"id_project\":1002,\"order_qty\":9999,\"qty_target\":10000,\"end_date\":\"2025-12-03\",\"pl_status\":1,\"note\":\"màu đỏ\",\"materials_json\":\"[]\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 12:12:30'),
(50, 3, 'bod', 'create', 'planning', 1022, NULL, '{\"plan_name\":\"test03\",\"id_project\":1002,\"order_qty\":9999,\"qty_target\":100000,\"end_date\":\"2025-12-03\",\"pl_status\":1,\"note\":\"mau\",\"materials_json\":\"[]\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 12:15:09'),
(51, 3, 'bod', 'create', 'planning', 1023, NULL, '{\"plan_name\":\"test04\",\"id_project\":1003,\"order_qty\":1000,\"qty_target\":10000,\"end_date\":\"2025-12-03\",\"pl_status\":1,\"note\":\"màu đỏ\",\"materials_json\":\"[{\\\"material_name\\\":\\\"Nhựa ABS\\\",\\\"so_luong\\\":10000}]\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 12:17:48'),
(52, 3, 'bod', 'create', 'planning', 1026, NULL, '{\"plan_name\":\"test\",\"id_project\":1002,\"order_qty\":9999,\"qty_target\":10000,\"end_date\":\"2025-12-02\",\"pl_status\":1,\"note\":\"mau \",\"materials_json\":\"[]\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 12:22:59'),
(53, 3, 'bod', 'create', 'planning', 1027, NULL, '{\"plan_name\":\"test\",\"id_project\":1002,\"order_qty\":9999,\"qty_target\":1000,\"end_date\":\"2025-12-02\",\"pl_status\":1,\"note\":\"mau do\",\"materials_json\":\"[{\\\"material_name\\\":\\\"Lò xo thép\\\",\\\"so_luong\\\":10}]\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 12:24:17'),
(54, 3, 'bod', 'create', 'planning', 1029, NULL, '{\"plan_name\":\"test01\",\"id_project\":1003,\"order_qty\":1000,\"qty_target\":100000,\"end_date\":\"2025-12-01\",\"pl_status\":1,\"note\":\"mau do\",\"materials_json\":\"[{\\\"material_name\\\":\\\"Bi kim loại 1.0mm\\\",\\\"so_luong\\\":111},{\\\"material_name\\\":\\\"Lò xo thép\\\",\\\"so_luong\\\":222}]\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 12:27:21'),
(55, 3, 'bod', 'create', 'planning', 1031, NULL, '{\"plan_name\":\"test04\",\"id_project\":1003,\"order_qty\":1000,\"qty_target\":11,\"end_date\":\"2025-12-02\",\"pl_status\":1,\"note\":\"mau\",\"materials_json\":\"[{\\\"material_name\\\":\\\"Bi kim loại 1.0mm\\\",\\\"so_luong\\\":11},{\\\"material_name\\\":\\\"Lò xo thép\\\",\\\"so_luong\\\":12}]\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 12:37:12'),
(56, 3, 'bod', 'create', 'planning', 1032, NULL, '{\"plan_name\":\"testcuoi\",\"id_project\":1003,\"order_qty\":1000,\"qty_target\":10000,\"end_date\":\"2025-12-01\",\"pl_status\":1,\"note\":\"mau do\",\"materials_json\":\"[{\\\"material_name\\\":\\\"Bi kim loại 0.5mm\\\",\\\"so_luong\\\":11},{\\\"material_name\\\":\\\"Bi kim loại 1.0mm\\\",\\\"so_luong\\\":1}]\",\"id_shift\":1003,\"id_staff\":1002,\"start_date\":\"2025-11-20\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 12:43:38'),
(57, 3, 'bod', 'create', 'planning', 1033, NULL, '{\"plan_name\":\"But cua Hung\",\"id_project\":1003,\"order_qty\":1000,\"qty_target\":1000,\"end_date\":\"2025-12-02\",\"pl_status\":1,\"note\":\"màu đỏ\",\"materials_json\":\"[{\\\"material_name\\\":\\\"Bi kim loại 0.5mm\\\",\\\"so_luong\\\":11},{\\\"material_name\\\":\\\"Bi kim loại 0.7mm\\\",\\\"so_luong\\\":12},{\\\"material_name\\\":\\\"Bi kim loại 1.0mm\\\",\\\"so_luong\\\":12}]\",\"id_shift\":1001,\"id_staff\":1001,\"start_date\":\"2024-12-01\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-01 12:45:10'),
(58, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 06:56:26'),
(59, 3, 'bod', 'approve', 'planning', 1032, '{\"id_plan\":\"1032\",\"plan_name\":\"testcuoi\",\"id_project\":\"1003\",\"qty_target\":\"10000\",\"end_date\":\"2025-12-01\",\"pl_status\":\"1\",\"order_qty\":\"1000\",\"start_date\":\"2025-11-20\",\"id_shift\":\"1003\",\"materials_json\":\"[{\\\"material_name\\\":\\\"Bi kim loại 0.5mm\\\",\\\"so_luong\\\":11},{\\\"material_name\\\":\\\"Bi kim loại 1.0mm\\\",\\\"so_luong\\\":1}]\",\"created_at\":\"2025-12-01 19:43:38\",\"updated_at\":null,\"note\":\"mau do\",\"id_staff\":\"1002\"}', '{\"pl_status\":1,\"note\":\"mau do\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 07:04:49'),
(60, 3, 'bod', 'create', 'planning', 1034, NULL, '{\"plan_name\":\"test04\",\"id_project\":1002,\"order_qty\":9999,\"qty_target\":10000,\"end_date\":\"2025-12-02\",\"pl_status\":1,\"note\":\"\",\"materials_json\":\"[{\\\"material_name\\\":\\\"Nhựa ABS\\\",\\\"so_luong\\\":0},{\\\"material_name\\\":\\\"Mực gel đen\\\",\\\"so_luong\\\":4999}]\",\"id_shift\":1001,\"id_staff\":1001,\"start_date\":\"2025-02-11\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 07:39:10'),
(61, 3, 'bod', 'create', 'planning', 1035, NULL, '{\"plan_name\":\"But\",\"id_project\":1002,\"order_qty\":9999,\"qty_target\":0,\"end_date\":\"2025-12-03\",\"pl_status\":0,\"note\":\"NVL đủ dùng cho 2 ca\",\"materials_json\":\"[{\\\"material_name\\\":\\\"Test Matereal\\\",\\\"so_luong\\\":4999},{\\\"material_name\\\":\\\"Nhựa ABS\\\",\\\"so_luong\\\":0},{\\\"material_name\\\":\\\"Bi kim loại 0.5mm\\\",\\\"so_luong\\\":7999}]\",\"id_shift\":1001,\"id_staff\":1001,\"start_date\":\"2025-11-11\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 08:22:44'),
(62, 3, 'bod', 'approve', 'planning', 1035, '{\"id_plan\":\"1035\",\"plan_name\":\"But\",\"id_project\":\"1002\",\"qty_target\":\"0\",\"end_date\":\"2025-12-03\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-11-11\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"material_name\\\":\\\"Test Matereal\\\",\\\"so_luong\\\":4999},{\\\"material_name\\\":\\\"Nhựa ABS\\\",\\\"so_luong\\\":0},{\\\"material_name\\\":\\\"Bi kim loại 0.5mm\\\",\\\"so_luong\\\":7999}]\",\"created_at\":\"2025-12-02 15:22:44\",\"updated_at\":\"2025-12-02 15:42:53\",\"note\":\"NVL đủ dùng cho 2 ca\",\"id_staff\":\"1001\"}', '{\"pl_status\":1,\"note\":\"NVL đủ dùng \"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 08:42:53'),
(63, 3, 'bod', 'approve', 'planning', 1035, '{\"id_plan\":\"1035\",\"plan_name\":\"But\",\"id_project\":\"1002\",\"qty_target\":\"0\",\"end_date\":\"2025-12-03\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-11-11\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"material_name\\\":\\\"Test Matereal\\\",\\\"so_luong\\\":4999},{\\\"material_name\\\":\\\"Nhựa ABS\\\",\\\"so_luong\\\":0},{\\\"material_name\\\":\\\"Bi kim loại 0.5mm\\\",\\\"so_luong\\\":7999}]\",\"created_at\":\"2025-12-02 15:22:44\",\"updated_at\":\"2025-12-02 15:42:53\",\"note\":\"NVL đủ dùng cho 2 ca\",\"id_staff\":\"1001\"}', '{\"pl_status\":1,\"note\":\"NVL đủ dùng \"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 08:43:28'),
(64, 3, 'bod', 'approve', 'planning', 1035, '{\"id_plan\":\"1035\",\"plan_name\":\"But bi\",\"id_project\":\"1002\",\"qty_target\":\"0\",\"end_date\":\"2025-12-03\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-11-11\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"material_name\\\":\\\"Test Matereal\\\",\\\"so_luong\\\":4999},{\\\"material_name\\\":\\\"Nhựa ABS\\\",\\\"so_luong\\\":0},{\\\"material_name\\\":\\\"Bi kim loại 0.5mm\\\",\\\"so_luong\\\":7999}]\",\"created_at\":\"2025-12-02 15:22:44\",\"updated_at\":\"2025-12-02 15:45:49\",\"note\":\"NVL đủ dùng cho 2 ca\",\"id_staff\":\"1001\"}', '{\"pl_status\":1,\"note\":\"NVL đủ dùng cho 2 ca\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 08:45:49'),
(65, 3, 'bod', 'approve', 'planning', 1033, '{\"id_plan\":\"1033\",\"plan_name\":\"But cua Hung\",\"id_project\":\"1003\",\"qty_target\":\"1000\",\"end_date\":\"2025-12-02\",\"pl_status\":\"1\",\"order_qty\":\"1000\",\"start_date\":\"2024-12-01\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"material_name\\\":\\\"Bi kim loại 0.5mm\\\",\\\"so_luong\\\":11},{\\\"material_name\\\":\\\"Bi kim loại 0.7mm\\\",\\\"so_luong\\\":12},{\\\"material_name\\\":\\\"Bi kim loại 1.0mm\\\",\\\"so_luong\\\":12}]\",\"created_at\":\"2025-12-01 19:45:10\",\"updated_at\":null,\"note\":\"màu đỏ\",\"id_staff\":\"1001\"}', '{\"pl_status\":1,\"note\":\"màu đỏ\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 08:49:11'),
(66, 3, 'bod', 'approve', 'planning', 1034, '{\"id_plan\":\"1034\",\"plan_name\":\"test04\",\"id_project\":\"1002\",\"qty_target\":\"10000\",\"end_date\":\"2025-12-02\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-02-11\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"material_name\\\":\\\"Nhựa ABS\\\",\\\"so_luong\\\":0},{\\\"material_name\\\":\\\"Mực gel đen\\\",\\\"so_luong\\\":4999}]\",\"created_at\":\"2025-12-02 14:39:10\",\"updated_at\":null,\"note\":\"\",\"id_staff\":\"1001\"}', '{\"pl_status\":1,\"note\":\"test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 08:54:41'),
(67, 3, 'bod', 'approve', 'planning', 1034, '{\"id_plan\":\"1034\",\"plan_name\":\"test04\",\"id_project\":\"1002\",\"qty_target\":\"10000\",\"end_date\":\"2025-12-02\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-02-11\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"material_name\\\":\\\"Nhựa ABS\\\",\\\"so_luong\\\":0},{\\\"material_name\\\":\\\"Mực gel đen\\\",\\\"so_luong\\\":4999}]\",\"created_at\":\"2025-12-02 14:39:10\",\"updated_at\":\"2025-12-02 15:55:31\",\"note\":\"\",\"id_staff\":\"1002\"}', '{\"pl_status\":1,\"note\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 08:55:31'),
(68, 3, 'bod', 'create', 'planning', 1036, NULL, '{\"plan_name\":\"met\",\"id_project\":1002,\"order_qty\":9999,\"qty_target\":0,\"end_date\":\"2025-11-11\",\"pl_status\":1,\"note\":\"test\",\"materials_json\":\"[{\\\"material_name\\\":\\\"Bi kim loại 0.5mm\\\",\\\"so_luong\\\":7999}]\",\"id_shift\":1001,\"id_staff\":1001,\"start_date\":\"2025-11-02\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 08:56:46'),
(69, 3, 'bod', 'approve', 'planning', 1036, '{\"id_plan\":\"1036\",\"plan_name\":\"met\",\"id_project\":\"1002\",\"qty_target\":\"0\",\"end_date\":\"2025-11-11\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-11-02\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"material_name\\\":\\\"Bi kim loại 0.5mm\\\",\\\"so_luong\\\":7999}]\",\"created_at\":\"2025-12-02 15:56:46\",\"updated_at\":null,\"note\":\"test\",\"id_staff\":\"1001\"}', '{\"pl_status\":1,\"note\":\"test111\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 08:57:03'),
(70, 3, 'bod', 'approve', 'planning', 1036, '{\"id_plan\":\"1036\",\"plan_name\":\"met11\",\"id_project\":\"1002\",\"qty_target\":\"0\",\"end_date\":\"2025-11-12\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-11-05\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"material_name\\\":\\\"Bi kim loại 0.5mm\\\",\\\"so_luong\\\":7999}]\",\"created_at\":\"2025-12-02 15:56:46\",\"updated_at\":\"2025-12-02 15:57:38\",\"note\":\"test\",\"id_staff\":\"1001\"}', '{\"pl_status\":1,\"note\":\"test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 08:57:38'),
(71, 3, 'bod', 'create', 'planning', 1037, NULL, '{\"plan_name\":\"test\",\"id_project\":1002,\"order_qty\":9999,\"qty_target\":0,\"end_date\":\"2025-12-02\",\"pl_status\":1,\"note\":\"test\",\"materials_json\":\"[{\\\"material_name\\\":\\\"Nhựa ABS\\\",\\\"so_luong\\\":0},{\\\"material_name\\\":\\\"Mực gel xanh\\\",\\\"so_luong\\\":4999},{\\\"material_name\\\":\\\"Bi kim loại 0.7mm\\\",\\\"so_luong\\\":6999}]\",\"id_shift\":1001,\"id_staff\":1001,\"start_date\":\"2025-11-11\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 09:01:40'),
(72, 3, 'bod', 'approve', 'planning', 1037, '{\"id_plan\":\"1037\",\"plan_name\":\"test1\",\"id_project\":\"1002\",\"qty_target\":\"0\",\"end_date\":\"2025-12-03\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-11-12\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"material_name\\\":\\\"Nhựa ABS\\\",\\\"so_luong\\\":0},{\\\"material_name\\\":\\\"Mực gel xanh\\\",\\\"so_luong\\\":4999},{\\\"material_name\\\":\\\"Bi kim loại 0.7mm\\\",\\\"so_luong\\\":6999}]\",\"created_at\":\"2025-12-02 16:01:40\",\"updated_at\":\"2025-12-02 16:02:23\",\"note\":\"test11\",\"id_staff\":\"1002\"}', '{\"pl_status\":1,\"note\":\"test11\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 09:02:23'),
(73, 3, 'bod', 'create', 'planning', 1038, NULL, '{\"plan_name\":\"test\",\"id_project\":1002,\"order_qty\":9999,\"qty_target\":0,\"end_date\":\"2025-12-03\",\"pl_status\":1,\"note\":\"test\",\"materials_json\":\"[{\\\"id_material\\\":1005,\\\"material_name\\\":\\\"Bi kim loại 0.5mm\\\",\\\"so_luong\\\":7999}]\",\"id_shift\":1001,\"id_staff\":1001,\"start_date\":\"2025-11-22\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 09:06:38'),
(74, 3, 'bod', 'approve', 'planning', 1038, '{\"id_plan\":\"1038\",\"plan_name\":\"test1\",\"id_project\":\"1002\",\"qty_target\":\"0\",\"end_date\":\"2025-12-02\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-11-23\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"id_material\\\":1005,\\\"material_name\\\":\\\"Bi kim loại 0.5mm\\\",\\\"so_luong\\\":7999}]\",\"created_at\":\"2025-12-02 16:06:38\",\"updated_at\":\"2025-12-02 16:07:27\",\"note\":\"test11\",\"id_staff\":\"1002\"}', '{\"pl_status\":1,\"note\":\"test11\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 09:07:27'),
(75, 3, 'bod', 'approve', 'planning', 1038, '{\"id_plan\":\"1038\",\"plan_name\":\"test1\",\"id_project\":\"1002\",\"qty_target\":\"0\",\"end_date\":\"2025-12-02\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-11-23\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"id_material\\\":1005,\\\"material_name\\\":\\\"Bi kim loại 0.5mm\\\",\\\"so_luong\\\":7999}]\",\"created_at\":\"2025-12-02 16:06:38\",\"updated_at\":\"2025-12-02 16:07:27\",\"note\":\"test11\",\"id_staff\":\"1002\"}', '{\"pl_status\":1,\"note\":\"test11\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 09:10:21'),
(76, 3, 'bod', 'approve', 'planning', 1038, '{\"id_plan\":\"1038\",\"plan_name\":\"test1\",\"id_project\":\"1002\",\"qty_target\":\"0\",\"end_date\":\"2025-12-02\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-11-23\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"id_material\\\":1005,\\\"material_name\\\":\\\"Bi kim loại 0.5mm\\\",\\\"so_luong\\\":7999}]\",\"created_at\":\"2025-12-02 16:06:38\",\"updated_at\":\"2025-12-02 16:07:27\",\"note\":\"test11\",\"id_staff\":\"1002\"}', '{\"pl_status\":1,\"note\":\"test11\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 09:15:09'),
(77, 3, 'bod', 'create', 'planning', 1039, NULL, '{\"plan_name\":\"test\",\"id_project\":1002,\"order_qty\":9999,\"qty_target\":0,\"end_date\":\"2025-12-02\",\"pl_status\":1,\"note\":\"test\",\"materials_json\":\"[{\\\"id_material\\\":1001,\\\"material_name\\\":\\\"Test Matereal\\\",\\\"so_luong\\\":4999}]\",\"id_shift\":1001,\"id_staff\":1001,\"start_date\":\"2025-11-11\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 09:18:36'),
(78, 3, 'bod', 'approve', 'planning', 1039, '{\"id_plan\":\"1039\",\"plan_name\":\"test\",\"id_project\":\"1002\",\"qty_target\":\"0\",\"end_date\":\"2025-12-02\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-11-11\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"id_material\\\":1001,\\\"material_name\\\":\\\"Test Matereal\\\",\\\"so_luong\\\":4999}]\",\"created_at\":\"2025-12-02 16:18:36\",\"updated_at\":null,\"note\":\"test\",\"id_staff\":\"1001\"}', '{\"pl_status\":1,\"note\":\"test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 09:19:06'),
(79, 3, 'bod', 'approve', 'planning', 1039, '{\"id_plan\":\"1039\",\"plan_name\":\"test\",\"id_project\":\"1002\",\"qty_target\":\"0\",\"end_date\":\"2025-12-02\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-11-11\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"id_material\\\":1001,\\\"material_name\\\":\\\"Test Matereal\\\",\\\"so_luong\\\":4999}]\",\"created_at\":\"2025-12-02 16:18:36\",\"updated_at\":null,\"note\":\"test\",\"id_staff\":\"1001\"}', '{\"pl_status\":1,\"note\":\"test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 09:21:41'),
(80, 3, 'bod', 'approve', 'planning', 1039, '{\"id_plan\":\"1039\",\"plan_name\":\"test\",\"id_project\":\"1002\",\"qty_target\":\"0\",\"end_date\":\"2025-12-02\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-11-11\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"id_material\\\":1001,\\\"material_name\\\":\\\"Test Matereal\\\",\\\"so_luong\\\":4999}]\",\"created_at\":\"2025-12-02 16:18:36\",\"updated_at\":null,\"note\":\"test\",\"id_staff\":\"1001\"}', '{\"pl_status\":1,\"note\":\"test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 09:21:59'),
(81, 3, 'bod', 'approve', 'planning', 1039, '{\"id_plan\":\"1039\",\"plan_name\":\"test\",\"id_project\":\"1002\",\"qty_target\":\"0\",\"end_date\":\"2025-12-02\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-11-11\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"id_material\\\":1001,\\\"material_name\\\":\\\"Test Matereal\\\",\\\"so_luong\\\":4999}]\",\"created_at\":\"2025-12-02 16:18:36\",\"updated_at\":null,\"note\":\"test\",\"id_staff\":\"1001\"}', '{\"pl_status\":1,\"note\":\"test\"}', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Mobile Safari/537.36', '2025-12-02 09:23:47'),
(82, 3, 'bod', 'approve', 'planning', 1039, '{\"id_plan\":\"1039\",\"plan_name\":\"test\",\"id_project\":\"1002\",\"qty_target\":\"0\",\"end_date\":\"2025-12-02\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-11-11\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"id_material\\\":1001,\\\"material_name\\\":\\\"Test Matereal\\\",\\\"so_luong\\\":4999}]\",\"created_at\":\"2025-12-02 16:18:36\",\"updated_at\":null,\"note\":\"test\",\"id_staff\":\"1001\"}', '{\"pl_status\":1,\"note\":\"test\"}', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Mobile Safari/537.36', '2025-12-02 09:25:17'),
(83, 3, 'bod', 'create', 'planning', 1040, NULL, '{\"plan_name\":\"test\",\"id_project\":1002,\"order_qty\":9999,\"qty_target\":0,\"end_date\":\"2025-12-01\",\"pl_status\":1,\"note\":\"tét\",\"materials_json\":\"[{\\\"id_material\\\":1001,\\\"material_name\\\":\\\"Test Matereal\\\",\\\"so_luong\\\":4999}]\",\"id_shift\":1001,\"id_staff\":1001,\"start_date\":\"2025-11-11\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 09:30:06'),
(84, 3, 'bod', 'approve', 'planning', 1040, '{\"id_plan\":\"1040\",\"plan_name\":\"test\",\"id_project\":\"1002\",\"qty_target\":\"0\",\"end_date\":\"2025-12-01\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-11-11\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"id_material\\\":1001,\\\"material_name\\\":\\\"Test Matereal\\\",\\\"so_luong\\\":4999}]\",\"created_at\":\"2025-12-02 16:30:06\",\"updated_at\":null,\"note\":\"tét\",\"id_staff\":\"1001\"}', '{\"pl_status\":1,\"note\":\"tét\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 09:30:26'),
(85, 3, 'bod', 'create', 'planning', 1041, NULL, '{\"plan_name\":\"test\",\"id_project\":1002,\"order_qty\":9999,\"qty_target\":0,\"end_date\":\"0025-12-01\",\"pl_status\":1,\"note\":\"t\",\"materials_json\":\"[{\\\"id_material\\\":1001,\\\"material_name\\\":\\\"Test Matereal\\\",\\\"so_luong\\\":4999}]\",\"id_shift\":1001,\"id_staff\":1002,\"start_date\":\"2025-11-11\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 09:31:07'),
(86, 3, 'bod', 'approve', 'planning', 1041, '{\"id_plan\":\"1041\",\"plan_name\":\"test\",\"id_project\":\"1002\",\"qty_target\":\"0\",\"end_date\":\"0025-12-01\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-11-11\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"id_material\\\":1001,\\\"material_name\\\":\\\"Test Matereal\\\",\\\"so_luong\\\":4999}]\",\"created_at\":\"2025-12-02 16:31:07\",\"updated_at\":null,\"note\":\"t\",\"id_staff\":\"1002\"}', '{\"pl_status\":1,\"note\":\"t\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 09:31:29'),
(87, 3, 'bod', 'create', 'planning', 1042, NULL, '{\"plan_name\":\"test\",\"id_project\":1002,\"order_qty\":9999,\"qty_target\":0,\"end_date\":\"2025-02-12\",\"pl_status\":1,\"note\":\"t\",\"materials_json\":\"[{\\\"id_material\\\":1003,\\\"material_name\\\":\\\"Mực gel xanh\\\",\\\"so_luong\\\":4999},{\\\"id_material\\\":1004,\\\"material_name\\\":\\\"Mực gel đen\\\",\\\"so_luong\\\":4999}]\",\"start_date\":\"2025-11-11\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 09:34:32'),
(88, 3, 'bod', 'approve', 'planning', 1042, '{\"id_plan\":\"1042\",\"plan_name\":\"test\",\"id_project\":\"1002\",\"qty_target\":\"0\",\"end_date\":\"2025-02-12\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-11-11\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"id_material\\\":1003,\\\"material_name\\\":\\\"Mực gel xanh\\\",\\\"so_luong\\\":4999},{\\\"id_material\\\":1004,\\\"material_name\\\":\\\"Mực gel đen\\\",\\\"so_luong\\\":4999}]\",\"created_at\":\"2025-12-02 16:34:32\",\"updated_at\":\"2025-12-02 16:41:34\",\"note\":\"t\",\"id_staff\":\"1001\"}', '{\"pl_status\":1,\"note\":\"t\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 09:41:34'),
(89, 3, 'bod', 'approve', 'planning', 1042, '{\"id_plan\":\"1042\",\"plan_name\":\"test\",\"id_project\":\"1002\",\"qty_target\":\"0\",\"end_date\":\"2025-02-12\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-11-11\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"id_material\\\":1003,\\\"material_name\\\":\\\"Mực gel xanh\\\",\\\"so_luong\\\":4999},{\\\"id_material\\\":1004,\\\"material_name\\\":\\\"Mực gel đen\\\",\\\"so_luong\\\":4999}]\",\"created_at\":\"2025-12-02 16:34:32\",\"updated_at\":\"2025-12-02 16:41:34\",\"note\":\"t\",\"id_staff\":\"1001\"}', '{\"pl_status\":1,\"note\":\"t\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 10:35:28'),
(90, 3, 'bod', 'approve', 'planning', 1042, '{\"id_plan\":\"1042\",\"plan_name\":\"test\",\"id_project\":\"1002\",\"qty_target\":\"0\",\"end_date\":\"2025-02-12\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-11-11\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"id_material\\\":1003,\\\"material_name\\\":\\\"Mực gel xanh\\\",\\\"so_luong\\\":4999},{\\\"id_material\\\":1004,\\\"material_name\\\":\\\"Mực gel đen\\\",\\\"so_luong\\\":4999}]\",\"created_at\":\"2025-12-02 16:34:32\",\"updated_at\":\"2025-12-02 16:41:34\",\"note\":\"t\",\"id_staff\":\"1001\"}', '{\"pl_status\":1,\"note\":\"t\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 10:39:08'),
(91, 3, 'bod', 'approve', 'planning', 1042, '{\"id_plan\":\"1042\",\"plan_name\":\"test\",\"id_project\":\"1002\",\"qty_target\":\"0\",\"end_date\":\"2025-02-12\",\"pl_status\":\"1\",\"order_qty\":\"9999\",\"start_date\":\"2025-11-11\",\"id_shift\":\"1001\",\"materials_json\":\"[{\\\"id_material\\\":1003,\\\"material_name\\\":\\\"Mực gel xanh\\\",\\\"so_luong\\\":4999},{\\\"id_material\\\":1004,\\\"material_name\\\":\\\"Mực gel đen\\\",\\\"so_luong\\\":4999}]\",\"created_at\":\"2025-12-02 16:34:32\",\"updated_at\":\"2025-12-02 16:41:34\",\"note\":\"t\",\"id_staff\":\"1001\"}', '{\"pl_status\":1,\"note\":\"t\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-02 10:43:35'),
(92, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-03 00:30:50'),
(93, 3, 'bod', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-03 00:31:18'),
(94, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-03 00:31:36'),
(95, 3, 'bod', 'create_plan', 'planning', 1043, NULL, '{\"plan_name\":\"KH-1001-1764731660\",\"id_project\":\"1001\",\"qty_target\":10000,\"end_date\":\"2023-11-06\",\"pl_status\":0,\"start_date\":\"2023-11-06\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-03 03:14:20'),
(96, 3, 'bod', 'create_plan', 'planning', 1044, NULL, '{\"plan_name\":\"KH-1001-1764733025\",\"id_project\":\"1001\",\"qty_target\":10000,\"end_date\":\"2023-11-06\",\"pl_status\":0,\"start_date\":\"2023-11-04\",\"note\":\"màu đỏ\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-03 03:37:05'),
(97, 3, 'bod', 'create_plan', 'planning', 1045, NULL, '{\"plan_name\":\"KH-1001-1764733077\",\"id_project\":\"1001\",\"qty_target\":10000,\"end_date\":\"2023-11-06\",\"pl_status\":0,\"start_date\":\"2023-11-06\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-03 03:37:57'),
(98, 3, 'bod', 'create_plan', 'planning', 1046, NULL, '{\"plan_name\":\"KH-1001-1764734469\",\"id_project\":\"1001\",\"qty_target\":10000,\"end_date\":\"2023-11-06\",\"pl_status\":0,\"start_date\":\"2023-11-06\",\"suggested_shifts\":0}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-03 04:01:09'),
(99, 3, 'bod', 'create_plan', 'planning', 1047, NULL, '{\"plan_name\":\"KH-1002-1764734763\",\"id_project\":\"1002\",\"qty_target\":9999,\"end_date\":\"2025-12-04\",\"pl_status\":0,\"start_date\":\"2025-11-11\",\"note\":\"test\",\"suggested_shifts\":0}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-03 04:06:03'),
(100, 3, 'bod', 'create_plan', 'planning', 1048, NULL, '{\"plan_name\":\"test01\",\"id_project\":\"1001\",\"qty_target\":10000,\"end_date\":\"2023-11-06\",\"pl_status\":0,\"start_date\":\"2023-11-06\",\"note\":\"test\",\"suggested_shifts\":3,\"materials\":\"[\\\"Bi kim loại 0.5mm — 8,000\\\",\\\"Lò xo thép — 9,000\\\",\\\"Mực gel xanh — 5,000\\\",\\\"Nhựa ABS — 0\\\"]\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-03 04:09:05'),
(101, 3, 'bod', 'create_plan', 'planning', 1049, NULL, '{\"plan_name\":\"test01\",\"id_project\":\"1001\",\"qty_target\":10000,\"end_date\":\"2023-11-06\",\"pl_status\":0,\"start_date\":\"2023-11-06\",\"note\":\"tét\",\"suggested_shifts\":3,\"materials\":\"[\\\"Bi kim loại 0.5mm — 8,000\\\",\\\"Lò xo thép — 9,000\\\",\\\"Mực gel đen — 5,000\\\",\\\"Nhựa ABS — 0\\\"]\",\"lines\":\"[]\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-03 04:11:05'),
(102, 3, 'bod', 'create_plan', 'planning', 1050, NULL, '{\"plan_name\":\"test01\",\"id_project\":\"1001\",\"qty_target\":10000,\"end_date\":\"2023-11-06\",\"pl_status\":0,\"start_date\":\"2023-11-06\",\"note\":\"te\",\"suggested_shifts\":3,\"materials\":\"[\\\"Bi kim loại 0.7mm — 7,000\\\",\\\"Lò xo thép — 9,000\\\",\\\"Mực gel đen — 5,000\\\",\\\"Nhựa ABS — 0\\\"]\",\"lines\":\"[]\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-03 04:12:56'),
(103, 3, 'bod', 'create_plan', 'planning', 1051, NULL, '{\"plan_name\":\"test\",\"id_project\":\"1001\",\"qty_target\":10000,\"end_date\":\"2023-11-06\",\"pl_status\":0,\"start_date\":\"2023-11-06\",\"note\":\"7\",\"suggested_shifts\":3,\"materials\":\"[\\\"Bi kim loại 1.0mm — 8,000\\\",\\\"Nhựa ABS — 0\\\"]\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-03 04:13:43'),
(104, 3, 'bod', 'create_plan', 'planning', 1052, NULL, '{\"plan_name\":\"KH-1001-1764735381\",\"id_project\":\"1001\",\"qty_target\":10000,\"end_date\":\"2023-11-06\",\"pl_status\":0,\"start_date\":\"2023-11-06\",\"suggested_shifts\":0,\"materials\":\"[\\\"Bi kim loại 0.5mm — 8,000\\\",\\\"Lò xo thép — 9,000\\\",\\\"Mực gel đen — 5,000\\\",\\\"Nhựa ABS — 0\\\"]\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-03 04:16:21'),
(105, 3, 'bod', 'create_plan', 'planning', 1053, NULL, '{\"plan_name\":\"test1\",\"id_project\":\"1002\",\"qty_target\":9999,\"end_date\":\"2025-12-04\",\"pl_status\":0,\"start_date\":\"2025-12-03\",\"note\":\"ff\",\"suggested_shifts\":3,\"materials\":\"[\\\"Bi kim loại 0.5mm — 7,999\\\",\\\"Lò xo thép — 8,999\\\",\\\"Mực gel xanh — 4,999\\\",\\\"Nhựa ABS — 1\\\"]\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-03 04:27:23'),
(106, 3, 'bod', 'create_plan', 'planning', 1054, NULL, '{\"plan_name\":\"test1\",\"id_project\":\"1001\",\"qty_target\":10000,\"end_date\":\"2023-11-06\",\"pl_status\":0,\"start_date\":\"2023-11-06\",\"note\":\"t\",\"suggested_shifts\":3,\"materials\":\"[\\\"Bi kim loại 0.7mm — 7,000\\\",\\\"Lò xo thép — 9,000\\\",\\\"Mực gel đen — 5,000\\\",\\\"Nhựa ABS — 0\\\"]\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', '2025-12-03 04:28:50'),
(107, 5, 'warehouse', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-12-05 16:19:47');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customer`
--

CREATE TABLE `customer` (
  `id_cust` int(25) NOT NULL,
  `cust_name` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL,
  `telp` int(20) NOT NULL,
  `email` varchar(25) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Hoạt động, 0=Ngừng hợp tác',
  `notes` text DEFAULT NULL COMMENT 'Ghi chú về khách hàng',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Thời gian tạo',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Thời gian cập nhật',
  `created_by` int(11) DEFAULT NULL COMMENT 'User ID người tạo (FK user.user_id)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `customer`
--

INSERT INTO `customer` (`id_cust`, `cust_name`, `address`, `telp`, `email`, `is_active`, `notes`, `created_at`, `updated_at`, `created_by`) VALUES
(1001, 'Tes Customer', 'Indonesia', 21293383, 'tes@mail.com', 1, NULL, '2025-11-24 15:53:34', '2025-11-24 15:53:34', NULL),
(1002, 'danh', 'nhỏ', 976688686, 'danh@gmail.com', 1, 'thường', '2025-11-28 08:43:33', '2025-11-28 08:43:33', 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `finished_issue`
--

CREATE TABLE `finished_issue` (
  `id_issue` int(11) NOT NULL,
  `issue_code` varchar(50) NOT NULL COMMENT 'Mã phiếu xuất',
  `id_project` int(11) NOT NULL COMMENT 'Liên kết tới project/đơn hàng',
  `quantity_requested` int(11) NOT NULL COMMENT 'Số lượng yêu cầu giao',
  `quantity_issued` int(11) NOT NULL COMMENT 'Số lượng thực tế xuất',
  `created_by` int(11) DEFAULT NULL COMMENT 'ID nhân viên kho tạo phiếu',
  `created_by_name` varchar(100) DEFAULT NULL COMMENT 'Tên nhân viên tạo',
  `created_date` datetime DEFAULT current_timestamp() COMMENT 'Ngày giờ tạo',
  `notes` text DEFAULT NULL COMMENT 'Ghi chú',
  `status` enum('full','partial','cancelled') DEFAULT 'full' COMMENT 'Trạng thái: full=giao đủ, partial=giao một phần, cancelled=hủy'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Phiếu xuất giao hàng thành phẩm';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `finished_receipt`
--

CREATE TABLE `finished_receipt` (
  `id_receipt` int(11) NOT NULL,
  `receipt_code` varchar(50) NOT NULL COMMENT 'Mã phiếu nhập (tự động sinh)',
  `id_project` int(11) NOT NULL COMMENT 'Liên kết tới project',
  `id_finished_report` int(11) DEFAULT NULL COMMENT 'Liên kết tới báo cáo QC',
  `quantity_received` int(11) NOT NULL COMMENT 'Số lượng nhập',
  `quantity_planned` int(11) DEFAULT NULL COMMENT 'Số lượng theo kế hoạch',
  `created_by` int(11) DEFAULT NULL COMMENT 'ID nhân viên kho tạo phiếu',
  `created_by_name` varchar(100) DEFAULT NULL COMMENT 'Tên nhân viên tạo',
  `created_date` datetime DEFAULT current_timestamp() COMMENT 'Ngày giờ tạo',
  `notes` text DEFAULT NULL COMMENT 'Ghi chú',
  `status` enum('posted','cancelled') DEFAULT 'posted' COMMENT 'Trạng thái phiếu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Phiếu nhập thành phẩm';

--
-- Đang đổ dữ liệu cho bảng `finished_receipt`
--

INSERT INTO `finished_receipt` (`id_receipt`, `receipt_code`, `id_project`, `id_finished_report`, `quantity_received`, `quantity_planned`, `created_by`, `created_by_name`, `created_date`, `notes`, `status`) VALUES
(1, 'NTP-20231107180000-001', 1001, 1001, 20, 20, 1, 'John Doe', '2023-11-07 18:00:00', 'Nhập từ QC đợt 1', 'posted'),
(2, 'NTP-20231108090000-002', 1001, 1001, 15, 20, 1, 'John Doe', '2023-11-08 09:00:00', 'Nhập từ QC đợt 2', 'posted');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `finished_report`
--

CREATE TABLE `finished_report` (
  `id_finished` int(11) NOT NULL,
  `id_project` int(11) NOT NULL,
  `total_finished` int(11) NOT NULL,
  `fdate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Báo cáo hoàn thành - total_finished: cái (số lượng bút)';

--
-- Đang đổ dữ liệu cho bảng `finished_report`
--

INSERT INTO `finished_report` (`id_finished`, `id_project`, `total_finished`, `fdate`) VALUES
(1001, 1001, 2000, '2025-11-01 15:30:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `finished_stock`
--

CREATE TABLE `finished_stock` (
  `id_stock` int(11) NOT NULL,
  `id_product` int(11) DEFAULT NULL COMMENT 'Loại sản phẩm (nếu cần phân loại)',
  `quantity_in_stock` int(11) DEFAULT 0 COMMENT 'Số lượng tồn hiện tại',
  `quantity_received` int(11) DEFAULT 0 COMMENT 'Tổng nhập',
  `quantity_issued` int(11) DEFAULT 0 COMMENT 'Tổng xuất',
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Cập nhật lần cuối'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tồn kho thành phẩm';

--
-- Đang đổ dữ liệu cho bảng `finished_stock`
--

INSERT INTO `finished_stock` (`id_stock`, `id_product`, `quantity_in_stock`, `quantity_received`, `quantity_issued`, `last_updated`) VALUES
(1, 1001, 35, 35, 0, '2025-12-02 15:29:57');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `machine`
--

CREATE TABLE `machine` (
  `id_machine` int(50) NOT NULL,
  `machine_name` varchar(50) NOT NULL,
  `capacity` int(15) NOT NULL,
  `mc_status` int(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng máy móc - capacity: cái/giờ, mc_status: 1=Sẵn sàng, 2=Đang dùng, 3=Sự cố, 4=Bảo trì';

--
-- Bẫy `machine`
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
-- Cấu trúc bảng cho bảng `machines`
--

CREATE TABLE `machines` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL COMMENT 'Mã máy/dây chuyền (unique)',
  `name` varchar(100) NOT NULL COMMENT 'Tên máy/dây chuyền',
  `capacity` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Công suất (pieces/hour)',
  `stage_type` enum('molding','assembly','packaging','quality_check','other') NOT NULL DEFAULT 'other' COMMENT 'Loại công đoạn',
  `status` enum('active','maintenance','inactive','broken') NOT NULL DEFAULT 'active' COMMENT 'Trạng thái máy',
  `description` text DEFAULT NULL COMMENT 'Mô tả chi tiết',
  `location` varchar(100) DEFAULT NULL COMMENT 'Vị trí đặt máy',
  `purchase_date` date DEFAULT NULL COMMENT 'Ngày mua/lắp đặt',
  `warranty_until` date DEFAULT NULL COMMENT 'Hết hạn bảo hành',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` varchar(50) DEFAULT NULL COMMENT 'Người tạo (username)',
  `updated_by` varchar(50) DEFAULT NULL COMMENT 'Người cập nhật cuối'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý máy/dây chuyền sản xuất';

--
-- Đang đổ dữ liệu cho bảng `machines`
--

INSERT INTO `machines` (`id`, `code`, `name`, `capacity`, `stage_type`, `status`, `description`, `location`, `purchase_date`, `warranty_until`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'ML001', 'Máy ép nhựa số 1', 1000.00, 'molding', 'active', 'Máy ép nhựa chính cho sản xuất vỏ bút', 'Khu A - Line 1', NULL, NULL, '2025-12-03 00:08:19', '2025-12-03 00:08:19', 'system', NULL),
(2, 'ML002', 'Máy ép nhựa số 2', 1200.00, 'molding', 'active', 'Máy ép nhựa dự phòng', 'Khu A - Line 2', NULL, NULL, '2025-12-03 00:08:19', '2025-12-03 00:08:19', 'system', NULL),
(3, 'AS001', 'Dây chuyền lắp ráp 1', 800.00, 'assembly', 'active', 'Dây chuyền lắp ráp chính', 'Khu B - Line 1', NULL, NULL, '2025-12-03 00:08:19', '2025-12-03 00:08:19', 'system', NULL),
(4, 'AS002', 'Dây chuyền lắp ráp 2', 750.00, 'assembly', 'maintenance', 'Dây chuyền lắp ráp phụ', 'Khu B - Line 2', NULL, NULL, '2025-12-03 00:08:19', '2025-12-03 00:08:19', 'system', NULL),
(5, 'PK001', 'Máy đóng gói tự động', 2000.00, 'packaging', 'active', 'Máy đóng gói và dán nhãn tự động', 'Khu C - Line 1', NULL, NULL, '2025-12-03 00:08:19', '2025-12-03 00:08:19', 'system', NULL),
(6, 'QC001', 'Máy kiểm tra chất lượng', 500.00, 'quality_check', 'active', 'Máy kiểm tra tự động', 'Khu D - QC', NULL, NULL, '2025-12-03 00:08:19', '2025-12-03 00:08:19', 'system', NULL),
(7, 'QC002', 'hsdhsa', 1000.00, 'quality_check', 'maintenance', NULL, NULL, NULL, NULL, '2025-12-03 02:13:23', '2025-12-03 02:35:51', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `machine_maintenances`
--

CREATE TABLE `machine_maintenances` (
  `id` int(11) NOT NULL,
  `machine_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL COMMENT 'Tiêu đề bảo trì',
  `description` text DEFAULT NULL COMMENT 'Mô tả chi tiết',
  `start_time` datetime NOT NULL COMMENT 'Thời gian bắt đầu bảo trì',
  `end_time` datetime NOT NULL COMMENT 'Thời gian kết thúc bảo trì',
  `maintenance_type` enum('preventive','corrective','emergency','upgrade') NOT NULL DEFAULT 'preventive' COMMENT 'Loại bảo trì',
  `status` enum('planned','in_progress','completed','cancelled') NOT NULL DEFAULT 'planned' COMMENT 'Trạng thái bảo trì',
  `estimated_cost` decimal(15,2) DEFAULT 0.00 COMMENT 'Chi phí ước tính',
  `actual_cost` decimal(15,2) DEFAULT 0.00 COMMENT 'Chi phí thực tế',
  `technician_name` varchar(100) DEFAULT NULL COMMENT 'Tên kỹ thuật viên',
  `created_by_user_id` int(11) DEFAULT NULL COMMENT 'ID người tạo',
  `created_by_username` varchar(50) DEFAULT NULL COMMENT 'Username người tạo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `completed_at` datetime DEFAULT NULL COMMENT 'Thời gian hoàn thành thực tế',
  `notes` text DEFAULT NULL COMMENT 'Ghi chú bảo trì'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Lịch bảo trì máy/dây chuyền';

--
-- Đang đổ dữ liệu cho bảng `machine_maintenances`
--

INSERT INTO `machine_maintenances` (`id`, `machine_id`, `title`, `description`, `start_time`, `end_time`, `maintenance_type`, `status`, `estimated_cost`, `actual_cost`, `technician_name`, `created_by_user_id`, `created_by_username`, `created_at`, `updated_at`, `completed_at`, `notes`) VALUES
(1, 4, 'Bảo trì định kỳ hàng tháng', 'Kiểm tra và bảo trì tổng thể dây chuyền lắp ráp 2', '2025-12-01 08:00:00', '2025-12-01 18:00:00', 'preventive', 'planned', 0.00, 0.00, NULL, NULL, 'system', '2025-12-03 00:08:19', '2025-12-03 00:08:19', NULL, NULL),
(2, 2, 'Nâng cấp phần mềm điều khiển', 'Cập nhật firmware và software điều khiển', '2025-12-05 20:00:00', '2025-12-06 06:00:00', 'upgrade', 'planned', 0.00, 0.00, NULL, NULL, 'system', '2025-12-03 00:08:19', '2025-12-03 00:08:19', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `machine_status_logs`
--

CREATE TABLE `machine_status_logs` (
  `id` int(11) NOT NULL,
  `machine_id` int(11) NOT NULL,
  `old_status` enum('active','maintenance','inactive','broken') DEFAULT NULL COMMENT 'Trạng thái cũ',
  `new_status` enum('active','maintenance','inactive','broken') NOT NULL COMMENT 'Trạng thái mới',
  `reason` varchar(255) DEFAULT NULL COMMENT 'Lý do thay đổi',
  `changed_by_user_id` int(11) DEFAULT NULL COMMENT 'ID người thay đổi',
  `changed_by_username` varchar(50) DEFAULT NULL COMMENT 'Username người thay đổi',
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL COMMENT 'Ghi chú thêm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log thay đổi trạng thái máy';

--
-- Đang đổ dữ liệu cho bảng `machine_status_logs`
--

INSERT INTO `machine_status_logs` (`id`, `machine_id`, `old_status`, `new_status`, `reason`, `changed_by_user_id`, `changed_by_username`, `changed_at`, `notes`) VALUES
(1, 4, 'active', 'maintenance', 'Bảo trì định kỳ theo lịch', NULL, 'system', '2025-12-03 00:08:19', NULL),
(2, 1, NULL, 'active', 'Khởi tạo máy mới', NULL, 'system', '2025-12-03 00:08:19', NULL),
(3, 2, NULL, 'active', 'Khởi tạo máy mới', NULL, 'system', '2025-12-03 00:08:19', NULL),
(4, 3, NULL, 'active', 'Khởi tạo máy mới', NULL, 'system', '2025-12-03 00:08:19', NULL),
(5, 5, NULL, 'active', 'Khởi tạo máy mới', NULL, 'system', '2025-12-03 00:08:19', NULL),
(6, 6, NULL, 'active', 'Khởi tạo máy mới', NULL, 'system', '2025-12-03 00:08:19', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `material`
--

CREATE TABLE `material` (
  `id_material` int(50) NOT NULL,
  `material_name` varchar(50) NOT NULL,
  `stock` int(50) NOT NULL,
  `min_stock` int(11) DEFAULT 0,
  `uom` varchar(10) NOT NULL DEFAULT 'g',
  `material_type` varchar(100) DEFAULT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `date_entry` date DEFAULT NULL,
  `attachment` varchar(512) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng nguyên liệu - stock: gram';

--
-- Đang đổ dữ liệu cho bảng `material`
--

INSERT INTO `material` (`id_material`, `material_name`, `stock`, `min_stock`, `uom`, `material_type`, `supplier`, `date_entry`, `attachment`, `created_at`, `updated_at`) VALUES
(1001, 'Test Matereal', 5000, 0, 'g', NULL, NULL, NULL, NULL, NULL, NULL),
(1002, 'Nhựa ABS', 10000, 0, 'g', NULL, NULL, NULL, NULL, NULL, NULL),
(1003, 'Mực gel xanh', 5000, 0, 'g', NULL, NULL, NULL, NULL, NULL, NULL),
(1004, 'Mực gel đen', 5000, 0, 'g', NULL, NULL, NULL, NULL, NULL, NULL),
(1005, 'Bi kim loại 0.5mm', 2000, 0, 'g', NULL, NULL, NULL, NULL, NULL, NULL),
(1006, 'Bi kim loại 0.7mm', 3000, 0, 'g', NULL, NULL, NULL, NULL, NULL, NULL),
(1007, 'Bi kim loại 1.0mm', 1998, 0, 'g', NULL, NULL, NULL, NULL, NULL, '2025-12-05 16:34:50'),
(1008, 'Lò xo thép', 1000, 0, 'g', NULL, NULL, NULL, NULL, NULL, NULL),
(1009, 'Hung', 24, 2, 'box', 'a', '1', '2025-12-04', 'asset/uploads/material/f5608fbdf1986af03359430a1121eb37.jpg', '2025-12-05 16:32:24', '2025-12-05 16:33:01');

--
-- Bẫy `material`
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
-- Cấu trúc bảng cho bảng `material_entry`
--

CREATE TABLE `material_entry` (
  `id` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `date_entry` date DEFAULT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `attachment` varchar(512) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `material_entry`
--

INSERT INTO `material_entry` (`id`, `id_material`, `quantity`, `date_entry`, `supplier`, `attachment`, `created_at`) VALUES
(1, 1009, 23, '2025-12-04', '1', 'asset/uploads/material/f5608fbdf1986af03359430a1121eb37.jpg', '2025-12-05 16:33:01');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `material_out`
--

CREATE TABLE `material_out` (
  `id` int(11) NOT NULL,
  `id_planshift` int(11) DEFAULT NULL,
  `id_plan` int(11) DEFAULT NULL,
  `id_material` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `date_out` date DEFAULT NULL,
  `note` varchar(512) DEFAULT NULL,
  `attachment` varchar(512) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `material_out`
--

INSERT INTO `material_out` (`id`, `id_planshift`, `id_plan`, `id_material`, `quantity`, `date_out`, `note`, `attachment`, `created_at`) VALUES
(1, 1, 1051, 1007, 2, '2025-12-05', 'a', 'asset/uploads/material/5cdba558ef5fb0faa0b03b892c27329f.jpg', '2025-12-05 16:34:50');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `modules`
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
-- Đang đổ dữ liệu cho bảng `modules`
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
-- Cấu trúc bảng cho bảng `permissions`
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
-- Đang đổ dữ liệu cho bảng `permissions`
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
-- Cấu trúc bảng cho bảng `planning`
--

CREATE TABLE `planning` (
  `id_plan` int(15) NOT NULL,
  `plan_name` varchar(25) NOT NULL,
  `id_project` int(15) NOT NULL,
  `end_date` date NOT NULL,
  `pl_status` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `note` text DEFAULT NULL,
  `suggested_shifts` int(11) DEFAULT NULL,
  `materials` longtext DEFAULT NULL,
  `lines` longtext DEFAULT NULL,
  `qty_target` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng kế hoạch - qty_target: cái/ca (số lượng bút mỗi ca)';

--
-- Đang đổ dữ liệu cho bảng `planning`
--

INSERT INTO `planning` (`id_plan`, `plan_name`, `id_project`, `end_date`, `pl_status`, `start_date`, `created_at`, `updated_at`, `note`, `suggested_shifts`, `materials`, `lines`, `qty_target`) VALUES
(1051, 'test', 1001, '2023-11-06', 1, '2023-11-06', '2025-12-03 11:13:43', '2025-12-05 23:33:24', '7', 3, '[\"Bi kim loại 1.0mm — 8,000\",\"Nhựa ABS — 0\"]', NULL, 10000),
(1053, 'test1', 1002, '2025-12-04', 0, '2025-12-03', '2025-12-03 11:27:23', NULL, 'ff', 3, '[\"Bi kim loại 0.5mm — 7,999\",\"Lò xo thép — 8,999\",\"Mực gel xanh — 4,999\",\"Nhựa ABS — 1\"]', NULL, 9999),
(1054, 'test1', 1001, '2023-11-06', 0, '2023-11-06', '2025-12-03 11:28:50', NULL, 't', 3, '[\"Bi kim loại 0.7mm — 7,000\",\"Lò xo thép — 9,000\",\"Mực gel đen — 5,000\",\"Nhựa ABS — 0\"]', NULL, 10000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `plan_shift`
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
-- Đang đổ dữ liệu cho bảng `plan_shift`
--

INSERT INTO `plan_shift` (`id_planshift`, `id_plan`, `id_shift`, `id_staff`, `start_date`, `ps_status`) VALUES
(1, 1051, 1002, 1001, '2025-12-05', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product`
--

CREATE TABLE `product` (
  `id_product` int(25) NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `summary` longtext NOT NULL COMMENT 'Thông tin chi tiết sản phẩm bút bi',
  `application` varchar(100) NOT NULL COMMENT 'Màu mực: Xanh, Đen, Đỏ, Nhiều màu',
  `diameter` decimal(3,1) NOT NULL DEFAULT 0.5 COMMENT 'Đường kính bi viết (mm)',
  `bom` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Định mức nguyên vật liệu (JSON)' CHECK (json_valid(`bom`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Đang sản xuất, 0=Ngừng',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Thời gian tạo',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Thời gian cập nhật',
  `created_by` int(11) DEFAULT NULL COMMENT 'User ID người tạo (FK user.user_id)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product`
--

INSERT INTO `product` (`id_product`, `product_name`, `summary`, `application`, `diameter`, `bom`, `is_active`, `created_at`, `updated_at`, `created_by`) VALUES
(1001, 'Bút bi TL-079', 'Bút bi mực gel, thân nhựa trong suốt, viết mượt', 'Xanh dương', 0.5, NULL, 1, '2025-11-24 15:53:58', '2025-11-24 15:53:58', NULL),
(1002, 'Bút bi TL-050', 'Bút bi dầu, thân nhựa màu, giá rẻ', 'Đen', 0.5, NULL, 1, '2025-11-24 15:53:58', '2025-11-24 15:53:58', NULL),
(1003, 'Bút bi TL-100', 'Bút bi cao cấp, thân kim loại', 'Đỏ', 0.5, NULL, 1, '2025-11-24 15:53:58', '2025-11-24 15:53:58', NULL),
(1004, 'Bút bi TL-Multi', 'Bút bi 4 màu, đa năng', 'nhiều màu', 0.5, NULL, 1, '2025-11-24 15:53:58', '2025-11-28 08:42:55', NULL),
(1005, 'bút mực', '', 'tím', 0.7, NULL, 1, '2025-11-28 08:41:49', '2025-11-28 08:41:49', 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `project`
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
-- Đang đổ dữ liệu cho bảng `project`
--

INSERT INTO `project` (`id_project`, `project_name`, `id_cust`, `id_product`, `diameter`, `qty_request`, `entry_date`, `pr_status`, `risk_flag`, `customer_request`, `created_at`) VALUES
(1001, 'PJ-TEST', 1001, 1001, 7.0, 10000, '2023-11-06', 1, 0, NULL, '2025-11-01 18:11:56'),
(1002, 'ORD-1001-20251128-001', 1001, 1003, 0.5, 9999, '2025-12-04', 1, 0, 'đỏ', '2025-11-27 18:46:32'),
(1003, 'Hung', 1002, 1005, 0.7, 1000, '2025-12-04', 1, 1, 'In hình doremon', '2025-12-01 09:02:59');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `project_diameter_backup`
--

CREATE TABLE `project_diameter_backup` (
  `id_project` int(25) NOT NULL DEFAULT 0,
  `diameter` int(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `project_diameter_backup`
--

INSERT INTO `project_diameter_backup` (`id_project`, `diameter`) VALUES
(1001, 7),
(1001, 7);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `p_machine`
--

CREATE TABLE `p_machine` (
  `id_pmachine` int(15) NOT NULL,
  `id_planshift` int(15) NOT NULL,
  `id_machine` int(15) NOT NULL,
  `mc_stats` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `p_material`
--

CREATE TABLE `p_material` (
  `id_pmaterial` int(15) NOT NULL,
  `id_planshift` int(15) NOT NULL,
  `id_material` int(15) NOT NULL,
  `used_stock` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Nguyên liệu sản xuất - used_stock: gram';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
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
-- Đang đổ dữ liệu cho bảng `roles`
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
-- Cấu trúc bảng cho bảng `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL COMMENT 'ID vai trò',
  `permission_id` int(11) NOT NULL COMMENT 'ID quyền hạn',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng liên kết nhiều-nhiều giữa Role và Permission';

--
-- Đang đổ dữ liệu cho bảng `role_permissions`
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
-- Cấu trúc bảng cho bảng `shiftment`
--

CREATE TABLE `shiftment` (
  `id_shift` int(11) NOT NULL,
  `shift_name` varchar(50) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `shiftment`
--

INSERT INTO `shiftment` (`id_shift`, `shift_name`, `start_time`, `end_time`) VALUES
(1001, 'Ca Sáng', '09:00:00', '17:00:00'),
(1002, 'Ca Chiều', '14:00:00', '22:00:00'),
(1003, 'Ca Tối', '22:00:00', '06:00:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sorting_report`
--

CREATE TABLE `sorting_report` (
  `id_sorting` int(15) NOT NULL,
  `id_planshift` int(15) NOT NULL,
  `waste` int(50) NOT NULL,
  `finished` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Báo cáo phân loại - waste & finished: cái (số lượng bút)';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `staff`
--

CREATE TABLE `staff` (
  `id_staff` int(11) NOT NULL,
  `staff_name` varchar(50) NOT NULL,
  `phone` int(15) NOT NULL,
  `email` varchar(25) NOT NULL,
  `st_status` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `staff`
--

INSERT INTO `staff` (`id_staff`, `staff_name`, `phone`, `email`, `st_status`) VALUES
(1001, 'Leader1', 8212312, 'leader1@mail.com', 2),
(1002, 'Leader2', 8923321, 'leader2@mail.com', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user`
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
-- Đang đổ dữ liệu cho bảng `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `role_id`, `staff_id`, `full_name`, `email`, `phone`, `is_active`, `last_login`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin', 4, NULL, 'Administrator', NULL, NULL, 1, '2025-11-24 16:16:53', NULL, '2025-11-01 15:49:53', '2025-11-24 16:16:53'),
(2, 'leader', 'leader', 2, NULL, 'Trưởng dây chuyền', NULL, NULL, 1, NULL, NULL, '2025-11-01 15:49:53', '2025-11-01 15:53:44'),
(3, 'bod', 'bod123', 1, NULL, 'Nguyễn Văn A - Giám Đốc', 'bod@company.com', NULL, 1, '2025-12-03 00:31:36', NULL, '2025-11-01 15:53:44', '2025-12-03 00:31:36'),
(4, 'line_manage', 'line123', 2, NULL, 'Trần Văn B - Trưởng line 2', 'linemanager@company.com', NULL, 1, NULL, NULL, '2025-11-01 15:53:45', '2025-11-22 18:20:46'),
(5, 'warehouse', 'wh123', 3, NULL, 'Lê Thị C - Nhân viên kho', 'warehouse@company.com', NULL, 1, '2025-12-05 16:19:47', NULL, '2025-11-01 15:53:45', '2025-12-05 16:19:47'),
(6, 'qc', 'qc123', 5, NULL, 'Phạm Văn D - Nhân viên QC', 'qc@company.com', NULL, 1, NULL, NULL, '2025-11-01 15:53:45', '2025-11-22 18:20:46'),
(7, 'technical', 'tech123', 6, NULL, 'Hoàng Văn E - Kỹ thuật viên', 'technical@company.com', NULL, 1, NULL, NULL, '2025-11-01 15:53:45', '2025-11-22 18:20:46'),
(8, 'worker', 'worker123', 7, NULL, 'Nguyễn Thị F - Công nhân', 'worker@company.com', NULL, 1, NULL, NULL, '2025-11-01 15:53:45', '2025-11-22 18:20:46');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `v_material_stock`
--

CREATE TABLE `v_material_stock` (
  `id_material` int(50) DEFAULT NULL,
  `material_name` varchar(50) DEFAULT NULL,
  `stock_display` varchar(55) DEFAULT NULL,
  `stock` int(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `v_project_details`
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
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_module` (`module`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_composite` (`user_id`,`action`,`created_at`);

--
-- Chỉ mục cho bảng `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id_cust`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_customer_search` (`cust_name`,`email`,`telp`),
  ADD KEY `idx_customer_active` (`is_active`,`id_cust`);

--
-- Chỉ mục cho bảng `finished_issue`
--
ALTER TABLE `finished_issue`
  ADD PRIMARY KEY (`id_issue`),
  ADD UNIQUE KEY `issue_code` (`issue_code`),
  ADD KEY `idx_project` (`id_project`),
  ADD KEY `idx_created_date` (`created_date`),
  ADD KEY `idx_status` (`status`);

--
-- Chỉ mục cho bảng `finished_receipt`
--
ALTER TABLE `finished_receipt`
  ADD PRIMARY KEY (`id_receipt`),
  ADD UNIQUE KEY `receipt_code` (`receipt_code`),
  ADD KEY `idx_project` (`id_project`),
  ADD KEY `idx_finished_report` (`id_finished_report`),
  ADD KEY `idx_created_date` (`created_date`);

--
-- Chỉ mục cho bảng `finished_report`
--
ALTER TABLE `finished_report`
  ADD PRIMARY KEY (`id_finished`),
  ADD KEY `fk_finished_project` (`id_project`);

--
-- Chỉ mục cho bảng `finished_stock`
--
ALTER TABLE `finished_stock`
  ADD PRIMARY KEY (`id_stock`),
  ADD UNIQUE KEY `unique_stock` (`id_product`),
  ADD KEY `idx_updated` (`last_updated`);

--
-- Chỉ mục cho bảng `machine`
--
ALTER TABLE `machine`
  ADD PRIMARY KEY (`id_machine`);

--
-- Chỉ mục cho bảng `machines`
--
ALTER TABLE `machines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_machine_code` (`code`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_stage_type` (`stage_type`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_available_machines` (`status`,`stage_type`,`capacity`);

--
-- Chỉ mục cho bảng `machine_maintenances`
--
ALTER TABLE `machine_maintenances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_machine_id` (`machine_id`),
  ADD KEY `idx_start_time` (`start_time`),
  ADD KEY `idx_end_time` (`end_time`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_maintenance_type` (`maintenance_type`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_maintenance_schedule` (`machine_id`,`start_time`,`end_time`,`status`);

--
-- Chỉ mục cho bảng `machine_status_logs`
--
ALTER TABLE `machine_status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_machine_id` (`machine_id`),
  ADD KEY `idx_changed_at` (`changed_at`),
  ADD KEY `idx_new_status` (`new_status`),
  ADD KEY `idx_audit_trail` (`machine_id`,`changed_at`,`new_status`);

--
-- Chỉ mục cho bảng `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`id_material`),
  ADD KEY `idx_material_stock` (`stock`,`material_name`);

--
-- Chỉ mục cho bảng `material_entry`
--
ALTER TABLE `material_entry`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_material_entry_material` (`id_material`);

--
-- Chỉ mục cho bảng `material_out`
--
ALTER TABLE `material_out`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_material_out_planshift` (`id_planshift`),
  ADD KEY `idx_material_out_material` (`id_material`);

--
-- Chỉ mục cho bảng `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`module_id`),
  ADD UNIQUE KEY `module_name` (`module_name`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `idx_module_name` (`module_name`),
  ADD KEY `idx_sort_order` (`sort_order`);

--
-- Chỉ mục cho bảng `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`),
  ADD KEY `idx_permission_name` (`permission_name`),
  ADD KEY `idx_module_id` (`module_id`),
  ADD KEY `idx_action` (`action`);

--
-- Chỉ mục cho bảng `planning`
--
ALTER TABLE `planning`
  ADD PRIMARY KEY (`id_plan`),
  ADD KEY `fk_planning_project` (`id_project`);

--
-- Chỉ mục cho bảng `plan_shift`
--
ALTER TABLE `plan_shift`
  ADD PRIMARY KEY (`id_planshift`),
  ADD KEY `fk_planshift_planning` (`id_plan`),
  ADD KEY `fk_planshift_shift` (`id_shift`),
  ADD KEY `fk_planshift_staff` (`id_staff`);

--
-- Chỉ mục cho bảng `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id_product`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_diameter` (`diameter`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_product_search` (`product_name`,`application`),
  ADD KEY `idx_product_active` (`is_active`,`id_product`);

--
-- Chỉ mục cho bảng `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id_project`),
  ADD KEY `fk_project_product` (`id_product`),
  ADD KEY `idx_created_cust` (`id_cust`,`created_at`),
  ADD KEY `idx_risk_status` (`risk_flag`,`pr_status`),
  ADD KEY `idx_project_customer_status` (`id_cust`,`pr_status`,`created_at`),
  ADD KEY `idx_project_product_status` (`id_product`,`pr_status`,`created_at`),
  ADD KEY `idx_project_status` (`pr_status`,`created_at`),
  ADD KEY `idx_project_stats` (`id_cust`,`id_product`,`pr_status`,`qty_request`);

--
-- Chỉ mục cho bảng `p_machine`
--
ALTER TABLE `p_machine`
  ADD PRIMARY KEY (`id_pmachine`),
  ADD KEY `fk_pmachine_planshift` (`id_planshift`),
  ADD KEY `fk_pmachine_machine` (`id_machine`);

--
-- Chỉ mục cho bảng `p_material`
--
ALTER TABLE `p_material`
  ADD PRIMARY KEY (`id_pmaterial`),
  ADD KEY `fk_pmaterial_planshift` (`id_planshift`),
  ADD KEY `fk_pmaterial_material` (`id_material`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`),
  ADD KEY `idx_role_name` (`role_name`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Chỉ mục cho bảng `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_permission` (`role_id`,`permission_id`),
  ADD KEY `idx_role_id` (`role_id`),
  ADD KEY `idx_permission_id` (`permission_id`);

--
-- Chỉ mục cho bảng `shiftment`
--
ALTER TABLE `shiftment`
  ADD PRIMARY KEY (`id_shift`);

--
-- Chỉ mục cho bảng `sorting_report`
--
ALTER TABLE `sorting_report`
  ADD PRIMARY KEY (`id_sorting`),
  ADD KEY `fk_sorting_planshift` (`id_planshift`);

--
-- Chỉ mục cho bảng `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id_staff`);

--
-- Chỉ mục cho bảng `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `fk_user_role` (`role_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `log_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT cho bảng `customer`
--
ALTER TABLE `customer`
  MODIFY `id_cust` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1003;

--
-- AUTO_INCREMENT cho bảng `finished_issue`
--
ALTER TABLE `finished_issue`
  MODIFY `id_issue` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `finished_receipt`
--
ALTER TABLE `finished_receipt`
  MODIFY `id_receipt` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `finished_stock`
--
ALTER TABLE `finished_stock`
  MODIFY `id_stock` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `machine`
--
ALTER TABLE `machine`
  MODIFY `id_machine` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1003;

--
-- AUTO_INCREMENT cho bảng `machines`
--
ALTER TABLE `machines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `machine_maintenances`
--
ALTER TABLE `machine_maintenances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `machine_status_logs`
--
ALTER TABLE `machine_status_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `material`
--
ALTER TABLE `material`
  MODIFY `id_material` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1010;

--
-- AUTO_INCREMENT cho bảng `material_entry`
--
ALTER TABLE `material_entry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `material_out`
--
ALTER TABLE `material_out`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `modules`
--
ALTER TABLE `modules`
  MODIFY `module_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT cho bảng `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

--
-- AUTO_INCREMENT cho bảng `planning`
--
ALTER TABLE `planning`
  MODIFY `id_plan` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1055;

--
-- AUTO_INCREMENT cho bảng `plan_shift`
--
ALTER TABLE `plan_shift`
  MODIFY `id_planshift` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1070;

--
-- AUTO_INCREMENT cho bảng `product`
--
ALTER TABLE `product`
  MODIFY `id_product` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1006;

--
-- AUTO_INCREMENT cho bảng `project`
--
ALTER TABLE `project`
  MODIFY `id_project` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1008;

--
-- AUTO_INCREMENT cho bảng `p_material`
--
ALTER TABLE `p_material`
  MODIFY `id_pmaterial` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=366;

--
-- AUTO_INCREMENT cho bảng `shiftment`
--
ALTER TABLE `shiftment`
  MODIFY `id_shift` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1004;

--
-- AUTO_INCREMENT cho bảng `staff`
--
ALTER TABLE `staff`
  MODIFY `id_staff` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1003;

--
-- AUTO_INCREMENT cho bảng `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `finished_issue`
--
ALTER TABLE `finished_issue`
  ADD CONSTRAINT `finished_issue_ibfk_1` FOREIGN KEY (`id_project`) REFERENCES `project` (`id_project`);

--
-- Các ràng buộc cho bảng `finished_receipt`
--
ALTER TABLE `finished_receipt`
  ADD CONSTRAINT `finished_receipt_ibfk_1` FOREIGN KEY (`id_project`) REFERENCES `project` (`id_project`),
  ADD CONSTRAINT `finished_receipt_ibfk_2` FOREIGN KEY (`id_finished_report`) REFERENCES `finished_report` (`id_finished`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `finished_report`
--
ALTER TABLE `finished_report`
  ADD CONSTRAINT `fk_finished_project` FOREIGN KEY (`id_project`) REFERENCES `project` (`id_project`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `machine_maintenances`
--
ALTER TABLE `machine_maintenances`
  ADD CONSTRAINT `fk_machine_maintenances_machine_id` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `machine_status_logs`
--
ALTER TABLE `machine_status_logs`
  ADD CONSTRAINT `fk_machine_logs_machine_id` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `modules`
--
ALTER TABLE `modules`
  ADD CONSTRAINT `modules_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `modules` (`module_id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `modules` (`module_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `planning`
--
ALTER TABLE `planning`
  ADD CONSTRAINT `fk_planning_project` FOREIGN KEY (`id_project`) REFERENCES `project` (`id_project`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `plan_shift`
--
ALTER TABLE `plan_shift`
  ADD CONSTRAINT `fk_planshift_planning` FOREIGN KEY (`id_plan`) REFERENCES `planning` (`id_plan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_planshift_shift` FOREIGN KEY (`id_shift`) REFERENCES `shiftment` (`id_shift`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_planshift_staff` FOREIGN KEY (`id_staff`) REFERENCES `staff` (`id_staff`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `fk_project_customer` FOREIGN KEY (`id_cust`) REFERENCES `customer` (`id_cust`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_project_product` FOREIGN KEY (`id_product`) REFERENCES `product` (`id_product`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `p_machine`
--
ALTER TABLE `p_machine`
  ADD CONSTRAINT `fk_pmachine_machine` FOREIGN KEY (`id_machine`) REFERENCES `machine` (`id_machine`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pmachine_planshift` FOREIGN KEY (`id_planshift`) REFERENCES `plan_shift` (`id_planshift`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `p_material`
--
ALTER TABLE `p_material`
  ADD CONSTRAINT `fk_pmaterial_material` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pmaterial_planshift` FOREIGN KEY (`id_planshift`) REFERENCES `plan_shift` (`id_planshift`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `sorting_report`
--
ALTER TABLE `sorting_report`
  ADD CONSTRAINT `fk_sorting_planshift` FOREIGN KEY (`id_planshift`) REFERENCES `plan_shift` (`id_planshift`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
