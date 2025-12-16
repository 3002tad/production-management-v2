-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2025 at 05:25 PM
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
(35, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 15:16:36'),
(36, 3, 'bod', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 15:29:20'),
(37, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 15:29:26'),
(38, 1, NULL, 'create', 'user', 9, NULL, '{\"username\":\"testuser01\",\"password\":\"Testuser9@\",\"role_id\":\"1\",\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"0937836789\",\"is_active\":1,\"must_change_password\":1,\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-01 23:02:29\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:02:29'),
(39, 9, 'testuser01', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:05:58'),
(40, 9, 'testuser01', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:06:27'),
(41, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:06:30'),
(42, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:08:56'),
(43, 3, 'bod', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:09:14'),
(44, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:09:17'),
(45, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:16:12'),
(46, 1, NULL, 'reset_password', 'user', 9, '{\"user_id\":\"9\",\"username\":\"testuser01\",\"password\":\"Testuser9@\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"0937836789\",\"is_active\":\"1\",\"last_login\":\"2025-12-01 23:05:58\",\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-01 23:05:58\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"temp_password_generated\":\"YES\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:18:36'),
(47, 1, NULL, 'reset_password', 'user', 9, '{\"user_id\":\"9\",\"username\":\"testuser01\",\"password\":\"760024B3\",\"temp_password\":\"760024B3\",\"must_change_password\":\"1\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"0937836789\",\"is_active\":\"1\",\"last_login\":\"2025-12-01 23:05:58\",\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-01 23:18:36\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"temp_password_generated\":\"YES\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:25:19'),
(48, 9, 'testuser01', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:26:07'),
(49, 9, 'testuser01', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:26:13'),
(50, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:26:16'),
(51, 1, NULL, 'lock', 'user', 9, '{\"user_id\":\"9\",\"username\":\"testuser01\",\"password\":\"2EB34DFE\",\"temp_password\":\"2EB34DFE\",\"must_change_password\":\"1\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"0937836789\",\"is_active\":\"1\",\"last_login\":\"2025-12-01 23:26:07\",\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-01 23:26:07\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"reason\":\"vi ph\\u1ea1m\",\"is_active\":0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:27:38'),
(52, 1, NULL, 'unlock', 'user', 9, '{\"user_id\":\"9\",\"username\":\"testuser01\",\"password\":\"2EB34DFE\",\"temp_password\":\"2EB34DFE\",\"must_change_password\":\"1\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"0937836789\",\"is_active\":\"0\",\"last_login\":\"2025-12-01 23:26:07\",\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-01 23:27:38\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:27:47'),
(53, 1, NULL, 'lock', 'user', 9, '{\"user_id\":\"9\",\"username\":\"testuser01\",\"password\":\"2EB34DFE\",\"temp_password\":\"2EB34DFE\",\"must_change_password\":\"1\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"0937836789\",\"is_active\":\"1\",\"last_login\":\"2025-12-01 23:26:07\",\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-01 23:27:47\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"reason\":\"vi\",\"is_active\":0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:28:02'),
(54, 1, NULL, 'unlock', 'user', 9, '{\"user_id\":\"9\",\"username\":\"testuser01\",\"password\":\"2EB34DFE\",\"temp_password\":\"2EB34DFE\",\"must_change_password\":\"1\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"0937836789\",\"is_active\":\"0\",\"last_login\":\"2025-12-01 23:26:07\",\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-01 23:28:02\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:28:10'),
(55, 1, NULL, 'reset_password', 'user', 9, '{\"user_id\":\"9\",\"username\":\"testuser01\",\"password\":\"2EB34DFE\",\"temp_password\":\"2EB34DFE\",\"must_change_password\":\"1\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"0937836789\",\"is_active\":\"1\",\"last_login\":\"2025-12-01 23:26:07\",\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-01 23:28:10\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"temp_password_generated\":\"YES\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:36:43'),
(56, 1, NULL, 'lock', 'user', 9, '{\"user_id\":\"9\",\"username\":\"testuser01\",\"password\":\"2DB96F2E\",\"temp_password\":\"2DB96F2E\",\"must_change_password\":\"1\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"0937836789\",\"is_active\":\"1\",\"last_login\":\"2025-12-01 23:26:07\",\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-01 23:36:43\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"reason\":\"vi\",\"is_active\":0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:36:49'),
(57, 1, NULL, 'unlock', 'user', 9, '{\"user_id\":\"9\",\"username\":\"testuser01\",\"password\":\"2DB96F2E\",\"temp_password\":\"2DB96F2E\",\"must_change_password\":\"1\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"0937836789\",\"is_active\":\"0\",\"last_login\":\"2025-12-01 23:26:07\",\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-01 23:36:49\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:36:53'),
(58, 1, NULL, 'reset_password', 'user', 9, '{\"user_id\":\"9\",\"username\":\"testuser01\",\"password\":\"2DB96F2E\",\"temp_password\":\"2DB96F2E\",\"must_change_password\":\"1\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"0937836789\",\"is_active\":\"1\",\"last_login\":\"2025-12-01 23:26:07\",\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-01 23:36:53\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"temp_password_generated\":\"YES\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:40:06'),
(59, 9, 'testuser01', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:40:54'),
(60, 9, 'testuser01', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:44:20'),
(61, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:44:22'),
(62, 9, 'testuser01', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:57:44'),
(63, 9, 'testuser01', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:57:55'),
(64, 9, 'testuser01', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 16:58:55'),
(65, 9, 'testuser01', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 17:02:53'),
(66, 9, 'testuser01', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 17:03:20'),
(67, 9, 'testuser01', 'change_password', 'auth', 0, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 17:04:04'),
(68, 9, 'testuser01', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 17:09:04'),
(69, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 17:09:06'),
(70, 1, NULL, 'reset_password', 'user', 9, '{\"user_id\":\"9\",\"username\":\"testuser01\",\"password\":\"cc03e747a6a\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"0937836789\",\"is_active\":\"1\",\"last_login\":\"2025-12-02 00:03:20\",\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-02 00:04:04\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"temp_password_generated\":\"YES\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 17:15:53'),
(71, 9, 'testuser01', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 17:22:54'),
(72, 9, 'testuser01', 'change_password', 'auth', 0, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 17:23:12'),
(73, 9, 'testuser01', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 17:23:51'),
(74, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 17:24:22'),
(75, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 17:25:27'),
(76, 3, 'bod', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 17:26:29'),
(77, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 17:26:48'),
(78, 3, 'bod', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 17:26:58'),
(79, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 17:27:52'),
(80, 3, 'bod', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 17:28:00'),
(81, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 17:28:27'),
(82, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 17:31:15'),
(83, 1, NULL, 'reset_password', 'user', 9, '{\"user_id\":\"9\",\"username\":\"testuser01\",\"password\":\"cc03e747a6a\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"0937836789\",\"is_active\":\"1\",\"last_login\":\"2025-12-02 00:22:54\",\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-02 00:23:12\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"temp_password_generated\":\"YES\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 17:33:01'),
(84, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 18:01:13'),
(85, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 18:02:22'),
(86, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 18:10:08'),
(87, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 18:10:37'),
(88, 3, 'bod', 'update', 'customer', 1002, '{\"id_cust\":\"1002\",\"cust_name\":\"danh\",\"address\":\"nhỏ\",\"telp\":\"976688686\",\"email\":\"danh@gmail.com\",\"is_active\":\"1\",\"notes\":\"thường\",\"created_at\":\"2025-11-28 15:43:33\",\"updated_at\":\"2025-11-28 15:43:33\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"created_by_username\":\"bod\"}', '{\"cust_name\":\"danh\",\"address\":\"vấp\",\"telp\":\"976688686\",\"email\":\"danh@gmail.com\",\"notes\":\"thường\",\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 18:10:54'),
(89, 3, 'bod', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 18:10:57'),
(90, 9, 'testuser01', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 18:12:26'),
(91, 9, 'testuser01', 'change_password', 'auth', 0, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 18:13:02'),
(92, 9, 'testuser01', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 18:13:26'),
(93, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 18:13:28'),
(94, 1, NULL, 'lock', 'user', 9, '{\"user_id\":\"9\",\"username\":\"testuser01\",\"password\":\"cc03e747a6a\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"0937836789\",\"is_active\":\"1\",\"last_login\":\"2025-12-02 01:12:25\",\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-02 01:13:02\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"reason\":\"vi\",\"is_active\":0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 18:31:11'),
(95, 1, NULL, 'unlock', 'user', 9, '{\"user_id\":\"9\",\"username\":\"testuser01\",\"password\":\"cc03e747a6a\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"0937836789\",\"is_active\":\"0\",\"last_login\":\"2025-12-02 01:12:25\",\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-02 01:31:11\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 18:31:25'),
(96, 1, NULL, 'update', 'user', 9, '{\"user_id\":\"9\",\"username\":\"testuser01\",\"password\":\"cc03e747a6a\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"0937836789\",\"is_active\":\"1\",\"last_login\":\"2025-12-02 01:12:25\",\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-02 01:31:25\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09378367778\",\"role_id\":\"1\",\"updated_at\":\"2025-12-02 01:37:57\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 18:37:57'),
(97, 1, NULL, 'update', 'user', 9, '{\"user_id\":\"9\",\"username\":\"testuser01\",\"password\":\"cc03e747a6a\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09378367778\",\"is_active\":\"1\",\"last_login\":\"2025-12-02 01:12:25\",\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-02 01:37:57\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09378367778\",\"role_id\":\"1\",\"updated_at\":\"2025-12-02 01:38:23\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 18:38:23'),
(98, 1, NULL, 'update', 'user', 9, '{\"user_id\":\"9\",\"username\":\"testuser01\",\"password\":\"cc03e747a6a\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09378367778\",\"is_active\":\"1\",\"last_login\":\"2025-12-02 01:12:25\",\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-02 01:38:23\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09378367778\",\"role_id\":\"7\",\"updated_at\":\"2025-12-02 01:38:47\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 18:38:47'),
(99, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 18:49:41'),
(100, 1, NULL, 'update', 'user', 9, '{\"user_id\":\"9\",\"username\":\"testuser01\",\"password\":\"cc03e747a6a\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09378367778\",\"is_active\":\"1\",\"last_login\":\"2025-12-02 01:12:25\",\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-02 01:38:47\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09378367778\",\"role_id\":\"6\",\"updated_at\":\"2025-12-02 01:49:55\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 18:49:55'),
(101, 1, NULL, 'update', 'user', 9, '{\"user_id\":\"9\",\"username\":\"testuser01\",\"password\":\"cc03e747a6a\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"6\",\"staff_id\":null,\"full_name\":\"c\\u00f4ng danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09378367778\",\"is_active\":\"1\",\"last_login\":\"2025-12-02 01:12:25\",\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-02 01:49:55\",\"role_name\":\"technical_staff\",\"role_display_name\":\"Nh\\u00e2n vi\\u00ean K\\u1ef9 thu\\u1eadt\",\"role_level\":\"60\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"c\\u00f4ng \",\"email\":\"danh12345@gmail.com\",\"phone\":\"09378367778\",\"role_id\":\"6\",\"updated_at\":\"2025-12-02 01:52:06\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 18:52:06'),
(102, 1, NULL, 'update', 'user', 9, '{\"user_id\":\"9\",\"username\":\"testuser01\",\"password\":\"cc03e747a6a\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"6\",\"staff_id\":null,\"full_name\":\"c\\u00f4ng \",\"email\":\"danh12345@gmail.com\",\"phone\":\"09378367778\",\"is_active\":\"1\",\"last_login\":\"2025-12-02 01:12:25\",\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-02 01:52:06\",\"role_name\":\"technical_staff\",\"role_display_name\":\"Nh\\u00e2n vi\\u00ean K\\u1ef9 thu\\u1eadt\",\"role_level\":\"60\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"c\\u00f4ng \",\"email\":\"danh12345@gmail.com\",\"phone\":\"09378367778\",\"role_id\":\"7\",\"updated_at\":\"2025-12-02 01:52:25\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 18:52:25'),
(103, 1, NULL, 'update', 'user', 9, '{\"user_id\":\"9\",\"username\":\"testuser01\",\"password\":\"cc03e747a6a\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"c\\u00f4ng \",\"email\":\"danh12345@gmail.com\",\"phone\":\"09378367778\",\"is_active\":\"1\",\"last_login\":\"2025-12-02 01:12:25\",\"created_by\":\"1\",\"created_at\":\"2025-12-01 23:02:29\",\"updated_at\":\"2025-12-02 01:52:25\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"c\\u00f4ng \",\"email\":\"danh12345@gmail.com\",\"phone\":\"09378367788\",\"role_id\":\"7\",\"updated_at\":\"2025-12-02 01:58:28\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 18:58:28'),
(104, 1, NULL, 'create', 'user', 10, NULL, '{\"username\":\"danh\",\"password\":\"123456\",\"role_id\":\"5\",\"full_name\":\"danh\",\"email\":\"danh94@gmail.com\",\"phone\":\"\",\"is_active\":1,\"must_change_password\":1,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:03:59\",\"updated_at\":\"2025-12-02 02:03:59\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:03:59'),
(105, 1, NULL, 'lock', 'user', 10, '{\"user_id\":\"10\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"5\",\"staff_id\":null,\"full_name\":\"danh\",\"email\":\"danh94@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:03:59\",\"updated_at\":\"2025-12-02 02:03:59\",\"role_name\":\"qc_staff\",\"role_display_name\":\"Nh\\u00e2n vi\\u00ean Ki\\u1ec3m so\\u00e1t Ch\\u1ea5t l\\u01b0\\u1ee3ng\",\"role_level\":\"60\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"reason\":\"vi\",\"is_active\":0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:04:34'),
(106, 1, NULL, 'unlock', 'user', 10, '{\"user_id\":\"10\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"5\",\"staff_id\":null,\"full_name\":\"danh\",\"email\":\"danh94@gmail.com\",\"phone\":\"\",\"is_active\":\"0\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:03:59\",\"updated_at\":\"2025-12-02 02:04:34\",\"role_name\":\"qc_staff\",\"role_display_name\":\"Nh\\u00e2n vi\\u00ean Ki\\u1ec3m so\\u00e1t Ch\\u1ea5t l\\u01b0\\u1ee3ng\",\"role_level\":\"60\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:04:43'),
(107, 1, NULL, 'update', 'user', 10, '{\"user_id\":\"10\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"5\",\"staff_id\":null,\"full_name\":\"danh\",\"email\":\"danh94@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:03:59\",\"updated_at\":\"2025-12-02 02:04:43\",\"role_name\":\"qc_staff\",\"role_display_name\":\"Nh\\u00e2n vi\\u00ean Ki\\u1ec3m so\\u00e1t Ch\\u1ea5t l\\u01b0\\u1ee3ng\",\"role_level\":\"60\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"danh\",\"email\":\"danh94@gmail.com\",\"phone\":\"09977878987\",\"role_id\":\"5\",\"updated_at\":\"2025-12-02 02:05:45\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:05:45'),
(108, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:07:53'),
(109, 1, NULL, 'update', 'user', 10, '{\"user_id\":\"10\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"5\",\"staff_id\":null,\"full_name\":\"danh\",\"email\":\"danh94@gmail.com\",\"phone\":\"09977878987\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:03:59\",\"updated_at\":\"2025-12-02 02:05:45\",\"role_name\":\"qc_staff\",\"role_display_name\":\"Nh\\u00e2n vi\\u00ean Ki\\u1ec3m so\\u00e1t Ch\\u1ea5t l\\u01b0\\u1ee3ng\",\"role_level\":\"60\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"danh\",\"email\":\"danh94@gmail.com\",\"phone\":\"09977878988\",\"role_id\":\"5\",\"updated_at\":\"2025-12-02 02:08:09\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:08:09'),
(110, 1, NULL, 'update', 'user', 10, '{\"user_id\":\"10\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"5\",\"staff_id\":null,\"full_name\":\"danh\",\"email\":\"danh94@gmail.com\",\"phone\":\"09977878988\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:03:59\",\"updated_at\":\"2025-12-02 02:08:09\",\"role_name\":\"qc_staff\",\"role_display_name\":\"Nh\\u00e2n vi\\u00ean Ki\\u1ec3m so\\u00e1t Ch\\u1ea5t l\\u01b0\\u1ee3ng\",\"role_level\":\"60\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"danh\",\"email\":\"danh94@gmail.com\",\"phone\":\"09977878588\",\"role_id\":\"5\",\"updated_at\":\"2025-12-02 02:10:52\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:10:52'),
(111, 1, NULL, 'update', 'user', 10, '{\"user_id\":\"10\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"5\",\"staff_id\":null,\"full_name\":\"danh\",\"email\":\"danh94@gmail.com\",\"phone\":\"09977878588\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:03:59\",\"updated_at\":\"2025-12-02 02:10:52\",\"role_name\":\"qc_staff\",\"role_display_name\":\"Nh\\u00e2n vi\\u00ean Ki\\u1ec3m so\\u00e1t Ch\\u1ea5t l\\u01b0\\u1ee3ng\",\"role_level\":\"60\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"danh\",\"email\":\"danh94@gmail.com\",\"phone\":\"09977878988\",\"role_id\":\"5\",\"updated_at\":\"2025-12-02 02:10:59\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:10:59'),
(112, 1, NULL, 'update', 'user', 10, '{\"user_id\":\"10\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"5\",\"staff_id\":null,\"full_name\":\"danh\",\"email\":\"danh94@gmail.com\",\"phone\":\"09977878988\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:03:59\",\"updated_at\":\"2025-12-02 02:10:59\",\"role_name\":\"qc_staff\",\"role_display_name\":\"Nh\\u00e2n vi\\u00ean Ki\\u1ec3m so\\u00e1t Ch\\u1ea5t l\\u01b0\\u1ee3ng\",\"role_level\":\"60\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"danh do\",\"email\":\"danh94@gmail.com\",\"phone\":\"09977878988\",\"role_id\":\"5\",\"updated_at\":\"2025-12-02 02:11:25\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:11:25'),
(113, 1, NULL, 'create', 'user', 11, NULL, '{\"username\":\"danh\",\"password\":\"123456\",\"role_id\":\"7\",\"full_name\":\"danh\",\"email\":\"danh8765454@gmail.com\",\"phone\":\"\",\"is_active\":1,\"must_change_password\":1,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:12:52\",\"updated_at\":\"2025-12-02 02:12:52\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:12:52'),
(114, 1, NULL, 'update', 'user', 11, '{\"user_id\":\"11\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"danh\",\"email\":\"danh8765454@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:12:52\",\"updated_at\":\"2025-12-02 02:12:52\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"danh do\",\"email\":\"danh8765454@gmail.com\",\"phone\":\"\",\"role_id\":\"7\",\"updated_at\":\"2025-12-02 02:16:32\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:16:32'),
(115, 1, NULL, 'create', 'user', 12, NULL, '{\"username\":\"danh\",\"password\":\"123456\",\"role_id\":\"7\",\"full_name\":\"\\u0111anho\",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"is_active\":1,\"must_change_password\":1,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:17:29\",\"updated_at\":\"2025-12-02 02:17:29\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:17:29'),
(116, 1, NULL, 'update', 'user', 12, '{\"user_id\":\"12\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"\\u0111anho\",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:17:29\",\"updated_at\":\"2025-12-02 02:17:29\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"\\u0111anho ddd\",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"role_id\":\"7\",\"updated_at\":\"2025-12-02 02:17:55\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:17:55'),
(117, 1, NULL, 'update', 'user', 12, '{\"user_id\":\"12\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"\\u0111anho ddd\",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:17:29\",\"updated_at\":\"2025-12-02 02:17:55\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"\\u0111anho \",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"role_id\":\"7\",\"updated_at\":\"2025-12-02 02:20:08\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:20:08'),
(118, 1, NULL, 'update', 'user', 12, '{\"user_id\":\"12\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"\\u0111anho \",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:17:29\",\"updated_at\":\"2025-12-02 02:20:08\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"\\u0111an\",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"role_id\":\"7\",\"updated_at\":\"2025-12-02 02:20:16\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:20:16'),
(119, 1, NULL, 'update', 'user', 12, '{\"user_id\":\"12\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"\\u0111an\",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:17:29\",\"updated_at\":\"2025-12-02 02:20:16\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"\\u0111anh\",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"role_id\":\"7\",\"updated_at\":\"2025-12-02 02:20:42\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:20:42'),
(120, 1, NULL, 'update', 'user', 12, '{\"user_id\":\"12\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"\\u0111anh\",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:17:29\",\"updated_at\":\"2025-12-02 02:20:42\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"\\u0111anh ddd\",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"role_id\":\"7\",\"updated_at\":\"2025-12-02 02:22:59\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:22:59'),
(121, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:23:32'),
(122, 1, NULL, 'update', 'user', 12, '{\"user_id\":\"12\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"\\u0111anh ddd\",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:17:29\",\"updated_at\":\"2025-12-02 02:22:59\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"\\u0111anh \",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"role_id\":\"7\",\"updated_at\":\"2025-12-02 02:23:44\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:23:44');
INSERT INTO `audit_log` (`log_id`, `user_id`, `username`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES
(123, 1, NULL, 'update', 'user', 12, '{\"user_id\":\"12\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"\\u0111anh \",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:17:29\",\"updated_at\":\"2025-12-02 02:23:44\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"\\u0111anh dddd\",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"role_id\":\"7\",\"updated_at\":\"2025-12-02 02:24:03\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:24:03'),
(124, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:26:37'),
(125, 1, NULL, 'update', 'user', 12, '{\"user_id\":\"12\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"\\u0111anh dddd\",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:17:29\",\"updated_at\":\"2025-12-02 02:24:03\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"\\u0111anh \",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"role_id\":\"7\",\"updated_at\":\"2025-12-02 02:26:47\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:26:47'),
(126, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:32:55'),
(127, 1, NULL, 'update', 'user', 12, '{\"user_id\":\"12\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"\\u0111anh \",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:17:29\",\"updated_at\":\"2025-12-02 02:26:47\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"\\u0111anh dd\",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"role_id\":\"7\",\"updated_at\":\"2025-12-02 02:33:06\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:33:06'),
(128, 1, NULL, 'update', 'user', 12, '{\"user_id\":\"12\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"\\u0111anh dd\",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:17:29\",\"updated_at\":\"2025-12-02 02:33:06\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"\\u0111anh ddooo\",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"role_id\":\"7\",\"updated_at\":\"2025-12-02 02:43:14\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:43:14'),
(129, 1, NULL, 'update', 'user', 12, '{\"user_id\":\"12\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"\\u0111anh ddooo\",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:17:29\",\"updated_at\":\"2025-12-02 02:43:14\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"\\u0111anh ddo\",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"role_id\":\"7\",\"updated_at\":\"2025-12-02 02:43:33\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:43:33'),
(130, 1, NULL, 'update', 'user', 12, '{\"user_id\":\"12\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"\\u0111anh ddo\",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:17:29\",\"updated_at\":\"2025-12-02 02:43:33\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"\\u0111anh ddoo\",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"role_id\":\"7\",\"updated_at\":\"2025-12-02 02:43:46\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:43:46'),
(131, 1, NULL, 'update', 'user', 12, '{\"user_id\":\"12\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"\\u0111anh ddoo\",\"email\":\"danh9888@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:17:29\",\"updated_at\":\"2025-12-02 02:43:46\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"\\u0111anh ddoo\",\"email\":\"danh9888@gmail.com\",\"phone\":\"09999999888\",\"role_id\":\"7\",\"updated_at\":\"2025-12-02 02:44:10\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:44:10'),
(132, 1, NULL, 'create', 'user', 13, NULL, '{\"username\":\"a\",\"password\":\"11111111111\",\"role_id\":\"7\",\"full_name\":\"a\",\"email\":\"danh1@gmail.com\",\"phone\":\"\",\"is_active\":1,\"must_change_password\":1,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:44:52\",\"updated_at\":\"2025-12-02 02:44:52\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:44:52'),
(133, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:46:50'),
(134, 1, NULL, 'create', 'user', 14, NULL, '{\"username\":\"b\",\"password\":\"111111111\",\"role_id\":\"7\",\"full_name\":\"a\",\"email\":\"danh1@gmail.com\",\"phone\":\"\",\"is_active\":1,\"must_change_password\":1,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:47:37\",\"updated_at\":\"2025-12-02 02:47:37\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:47:37'),
(135, 1, NULL, 'update', 'user', 14, '{\"user_id\":\"14\",\"username\":\"b\",\"password\":\"111111111\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"a\",\"email\":\"danh1@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:47:37\",\"updated_at\":\"2025-12-02 02:47:37\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"ag\",\"email\":\"danh1@gmail.com\",\"phone\":\"\",\"role_id\":\"7\",\"updated_at\":\"2025-12-02 02:47:48\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:47:48'),
(136, 1, NULL, 'update', 'user', 14, '{\"user_id\":\"14\",\"username\":\"b\",\"password\":\"111111111\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"ag\",\"email\":\"danh1@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:47:37\",\"updated_at\":\"2025-12-02 02:47:48\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"agfff\",\"email\":\"danh1@gmail.com\",\"phone\":\"\",\"role_id\":\"7\",\"updated_at\":\"2025-12-02 02:54:13\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:54:13'),
(137, 1, NULL, 'update', 'user', 14, '{\"user_id\":\"14\",\"username\":\"b\",\"password\":\"111111111\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"agfff\",\"email\":\"danh1@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:47:37\",\"updated_at\":\"2025-12-02 02:54:13\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"agfffff\",\"email\":\"danh1@gmail.com\",\"phone\":\"\",\"role_id\":\"7\",\"updated_at\":\"2025-12-02 02:54:18\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:54:18'),
(138, 1, NULL, 'update', 'user', 14, '{\"user_id\":\"14\",\"username\":\"b\",\"password\":\"111111111\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"agfffff\",\"email\":\"danh1@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:47:37\",\"updated_at\":\"2025-12-02 02:54:18\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"agfffffffffffffffff\",\"email\":\"danh1@gmail.com\",\"phone\":\"\",\"role_id\":\"7\",\"updated_at\":\"2025-12-02 02:54:25\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 19:54:25'),
(139, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 07:11:40'),
(140, 3, 'bod', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 08:08:03'),
(141, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 08:08:19'),
(142, 1, NULL, 'lock', 'user', 14, '{\"user_id\":\"14\",\"username\":\"b\",\"password\":\"111111111\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"agfffffffffffffffff\",\"email\":\"danh1@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:47:37\",\"updated_at\":\"2025-12-02 02:54:25\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"reason\":\"vi\",\"is_active\":0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 08:09:33'),
(143, 1, NULL, 'unlock', 'user', 14, '{\"user_id\":\"14\",\"username\":\"b\",\"password\":\"111111111\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"agfffffffffffffffff\",\"email\":\"danh1@gmail.com\",\"phone\":\"\",\"is_active\":\"0\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-02 02:47:37\",\"updated_at\":\"2025-12-02 15:09:33\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 08:10:04'),
(144, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:27:31'),
(145, 1, NULL, 'create', 'user', 15, NULL, '{\"username\":\"danh\",\"password\":\"123456\",\"role_id\":\"7\",\"full_name\":\"danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"\",\"is_active\":1,\"must_change_password\":1,\"created_by\":\"1\",\"created_at\":\"2025-12-03 02:32:59\",\"updated_at\":\"2025-12-03 02:32:59\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:32:59'),
(146, 1, NULL, 'update', 'user', 15, '{\"user_id\":\"15\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-03 02:32:59\",\"updated_at\":\"2025-12-03 02:32:59\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"danhhhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"\",\"role_id\":\"7\",\"updated_at\":\"2025-12-03 02:35:42\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:35:42'),
(147, 1, NULL, 'update', 'user', 15, '{\"user_id\":\"15\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"danhhhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-03 02:32:59\",\"updated_at\":\"2025-12-03 02:35:42\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"danhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987776899\",\"role_id\":\"7\",\"updated_at\":\"2025-12-03 02:35:58\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:35:58'),
(148, 1, NULL, 'lock', 'user', 15, '{\"user_id\":\"15\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"danhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987776899\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-03 02:32:59\",\"updated_at\":\"2025-12-03 02:35:58\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"reason\":\"vi\",\"is_active\":0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:39:22'),
(149, 1, NULL, 'unlock', 'user', 15, '{\"user_id\":\"15\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"danhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987776899\",\"is_active\":\"0\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-03 02:32:59\",\"updated_at\":\"2025-12-03 02:39:22\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:39:27'),
(150, 1, NULL, 'reset_password', 'user', 15, '{\"user_id\":\"15\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"danhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987776899\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-03 02:32:59\",\"updated_at\":\"2025-12-03 02:39:27\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"temp_password_generated\":\"YES\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:39:32'),
(151, 15, 'danh', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:41:38'),
(152, 15, 'danh', 'change_password', 'auth', 0, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:42:46'),
(153, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:44:31'),
(154, 1, NULL, 'update', 'user', 15, '{\"user_id\":\"15\",\"username\":\"danh\",\"password\":\"e10adc3949b\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"7\",\"staff_id\":null,\"full_name\":\"danhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987776899\",\"is_active\":\"1\",\"last_login\":\"2025-12-03 02:41:38\",\"created_by\":\"1\",\"created_at\":\"2025-12-03 02:32:59\",\"updated_at\":\"2025-12-03 02:42:46\",\"role_name\":\"worker\",\"role_display_name\":\"C\\u00f4ng nh\\u00e2n S\\u1ea3n xu\\u1ea5t\",\"role_level\":\"10\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"danhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987776899\",\"role_id\":\"5\",\"updated_at\":\"2025-12-03 02:45:00\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:45:00'),
(155, 15, 'danh', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:51:43'),
(156, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:52:43'),
(157, 1, NULL, 'update', 'user', 15, '{\"user_id\":\"15\",\"username\":\"danh\",\"password\":\"e10adc3949b\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"5\",\"staff_id\":null,\"full_name\":\"danhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987776899\",\"is_active\":\"1\",\"last_login\":\"2025-12-03 02:51:43\",\"created_by\":\"1\",\"created_at\":\"2025-12-03 02:32:59\",\"updated_at\":\"2025-12-03 02:51:43\",\"role_name\":\"qc_staff\",\"role_display_name\":\"Nh\\u00e2n vi\\u00ean Ki\\u1ec3m so\\u00e1t Ch\\u1ea5t l\\u01b0\\u1ee3ng\",\"role_level\":\"60\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"danhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987776899\",\"role_id\":\"1\",\"updated_at\":\"2025-12-03 02:53:00\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:53:00'),
(158, 15, 'danh', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:54:33'),
(159, 15, 'danh', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:55:34'),
(160, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:55:38'),
(161, 1, NULL, 'reset_password', 'user', 15, '{\"user_id\":\"15\",\"username\":\"danh\",\"password\":\"e10adc3949b\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"danhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987776899\",\"is_active\":\"1\",\"last_login\":\"2025-12-03 02:54:33\",\"created_by\":\"1\",\"created_at\":\"2025-12-03 02:32:59\",\"updated_at\":\"2025-12-03 02:54:33\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"temp_password_generated\":\"YES\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:55:46'),
(162, 15, 'danh', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:57:45'),
(163, 15, 'danh', 'change_password', 'auth', 0, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:58:28'),
(164, 15, 'danh', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:58:48'),
(165, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 19:58:53'),
(166, 1, NULL, 'reset_password', 'user', 15, '{\"user_id\":\"15\",\"username\":\"danh\",\"password\":\"e10adc3949b\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"danhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987776899\",\"is_active\":\"1\",\"last_login\":\"2025-12-03 02:57:45\",\"created_by\":\"1\",\"created_at\":\"2025-12-03 02:32:59\",\"updated_at\":\"2025-12-03 02:58:28\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"temp_password_generated\":\"YES\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:00:36'),
(167, 15, 'danh', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:01:02'),
(168, 15, 'danh', 'change_password', 'auth', 0, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:01:28'),
(169, 15, 'danh', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:01:44'),
(170, 15, 'danh', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:02:21'),
(171, 15, 'danh', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:07:35'),
(172, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:07:46'),
(173, 1, NULL, 'reset_password', 'user', 15, '{\"user_id\":\"15\",\"username\":\"danh\",\"password\":\"e10adc3949b\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"danhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987776899\",\"is_active\":\"1\",\"last_login\":\"2025-12-03 03:02:21\",\"created_by\":\"1\",\"created_at\":\"2025-12-03 02:32:59\",\"updated_at\":\"2025-12-03 03:02:21\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"temp_password_generated\":\"YES\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:08:03'),
(174, 15, 'danh', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:08:27'),
(175, 15, 'danh', 'change_password', 'auth', 0, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:08:42'),
(176, 15, 'danh', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:08:54'),
(177, 15, 'danh', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:09:03'),
(178, 15, 'danh', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:09:15'),
(179, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:09:21'),
(180, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:12:05'),
(181, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:12:21'),
(182, 3, 'bod', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:12:38'),
(183, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:14:35'),
(184, 1, NULL, 'lock', 'user', 15, '{\"user_id\":\"15\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"danhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987776899\",\"is_active\":\"1\",\"last_login\":\"2025-12-03 03:09:03\",\"created_by\":\"1\",\"created_at\":\"2025-12-03 02:32:59\",\"updated_at\":\"2025-12-03 03:09:03\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"reason\":\"vi\",\"is_active\":0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:14:50'),
(185, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:15:16'),
(186, 1, NULL, 'unlock', 'user', 15, '{\"user_id\":\"15\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"danhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987776899\",\"is_active\":\"0\",\"last_login\":\"2025-12-03 03:09:03\",\"created_by\":\"1\",\"created_at\":\"2025-12-03 02:32:59\",\"updated_at\":\"2025-12-03 03:14:50\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:15:24'),
(187, 15, 'danh', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:15:36'),
(188, 15, 'danh', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:16:10'),
(189, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:30:18'),
(190, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:34:39'),
(191, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:35:17'),
(192, 3, 'bod', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:35:23'),
(193, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:37:19'),
(194, 1, NULL, 'update', 'user', 15, '{\"user_id\":\"15\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"danhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987776899\",\"is_active\":\"1\",\"last_login\":\"2025-12-03 03:15:36\",\"created_by\":\"1\",\"created_at\":\"2025-12-03 02:32:59\",\"updated_at\":\"2025-12-03 03:15:36\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"danhhhhhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987776899\",\"role_id\":\"1\",\"updated_at\":\"2025-12-03 03:41:22\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:41:22'),
(195, 1, NULL, 'lock', 'user', 15, '{\"user_id\":\"15\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"danhhhhhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987776899\",\"is_active\":\"1\",\"last_login\":\"2025-12-03 03:15:36\",\"created_by\":\"1\",\"created_at\":\"2025-12-03 02:32:59\",\"updated_at\":\"2025-12-03 03:41:22\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"reason\":\"vi\",\"is_active\":0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-02 20:41:30'),
(196, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-03 08:52:07'),
(197, 1, NULL, 'unlock', 'user', 15, '{\"user_id\":\"15\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"danhhhhhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987776899\",\"is_active\":\"0\",\"last_login\":\"2025-12-03 03:15:36\",\"created_by\":\"1\",\"created_at\":\"2025-12-03 02:32:59\",\"updated_at\":\"2025-12-03 03:41:30\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-03 08:52:17'),
(198, 1, NULL, 'reset_password', 'user', 15, '{\"user_id\":\"15\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"danhhhhhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987776899\",\"is_active\":\"1\",\"last_login\":\"2025-12-03 03:15:36\",\"created_by\":\"1\",\"created_at\":\"2025-12-03 02:32:59\",\"updated_at\":\"2025-12-03 15:52:17\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"temp_password_generated\":\"YES\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-03 08:52:27'),
(199, 1, NULL, 'lock', 'user', 15, '{\"user_id\":\"15\",\"username\":\"danh\",\"password\":\"06049835\",\"temp_password\":\"06049835\",\"must_change_password\":\"1\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"danhhhhhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987776899\",\"is_active\":\"1\",\"last_login\":\"2025-12-03 03:15:36\",\"created_by\":\"1\",\"created_at\":\"2025-12-03 02:32:59\",\"updated_at\":\"2025-12-03 15:52:27\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"reason\":\"vi\",\"is_active\":0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-03 08:52:40'),
(200, 1, NULL, 'unlock', 'user', 15, '{\"user_id\":\"15\",\"username\":\"danh\",\"password\":\"06049835\",\"temp_password\":\"06049835\",\"must_change_password\":\"1\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"danhhhhhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987776899\",\"is_active\":\"0\",\"last_login\":\"2025-12-03 03:15:36\",\"created_by\":\"1\",\"created_at\":\"2025-12-03 02:32:59\",\"updated_at\":\"2025-12-03 15:52:40\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-03 08:52:44'),
(201, 1, NULL, 'update', 'user', 15, '{\"user_id\":\"15\",\"username\":\"danh\",\"password\":\"06049835\",\"temp_password\":\"06049835\",\"must_change_password\":\"1\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"danhhhhhh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987776899\",\"is_active\":\"1\",\"last_login\":\"2025-12-03 03:15:36\",\"created_by\":\"1\",\"created_at\":\"2025-12-03 02:32:59\",\"updated_at\":\"2025-12-03 15:52:44\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"full_name\":\"danh\",\"email\":\"danh12345@gmail.com\",\"phone\":\"09987779999\",\"role_id\":\"1\",\"updated_at\":\"2025-12-03 15:53:07\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-03 08:53:07'),
(202, 1, NULL, 'create', 'user', 16, NULL, '{\"username\":\"danh\",\"password\":\"123456\",\"role_id\":\"1\",\"full_name\":\"anh\",\"email\":\"danh@gmail.com\",\"phone\":\"09877976688\",\"is_active\":1,\"must_change_password\":1,\"created_by\":\"1\",\"created_at\":\"2025-12-03 15:55:13\",\"updated_at\":\"2025-12-03 15:55:13\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-03 08:55:13'),
(203, 1, NULL, 'reset_password', 'user', 16, '{\"user_id\":\"16\",\"username\":\"danh\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"anh\",\"email\":\"danh@gmail.com\",\"phone\":\"09877976688\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-03 15:55:13\",\"updated_at\":\"2025-12-03 15:55:13\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"temp_password_generated\":\"YES\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-03 08:57:55'),
(204, 16, 'danh', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-03 09:06:12'),
(205, 16, 'danh', 'change_password', 'auth', 0, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-03 09:06:44'),
(206, 16, 'danh', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-03 09:06:57'),
(207, 16, 'danh', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-03 09:07:10'),
(208, 16, 'danh', 'update', 'customer', 1002, '{\"id_cust\":\"1002\",\"cust_name\":\"danh\",\"address\":\"vấp\",\"telp\":\"976688686\",\"email\":\"danh@gmail.com\",\"is_active\":\"1\",\"notes\":\"thường\",\"created_at\":\"2025-11-28 15:43:33\",\"updated_at\":\"2025-12-02 01:10:54\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"created_by_username\":\"bod\"}', '{\"cust_name\":\"danh\",\"address\":\"gò\",\"telp\":\"9766888888\",\"email\":\"danh@gmail.com\",\"notes\":\"thường\",\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-03 09:07:31'),
(209, 16, 'danh', 'update', 'product', 1005, '{\"id_product\":\"1005\",\"product_name\":\"bút mực\",\"summary\":\"\",\"application\":\"tím\",\"diameter\":\"0.7\",\"bom\":null,\"is_active\":\"1\",\"created_at\":\"2025-11-28 15:41:49\",\"updated_at\":\"2025-11-28 15:41:49\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"diameter_display\":\"0.7mm\",\"created_by_username\":\"bod\",\"bom_data\":{\"materials\":[]}}', '{\"product_name\":\"bút mực\",\"summary\":\"\",\"application\":\"xanh lá\",\"diameter\":\"0.7\",\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-03 09:07:48'),
(210, 3, 'bod', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 11:37:19'),
(211, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 11:37:23'),
(212, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 14:57:12'),
(213, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 15:34:49'),
(214, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 15:43:37'),
(215, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 16:25:17'),
(216, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 16:33:35'),
(217, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 16:58:47'),
(218, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 17:32:26'),
(219, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 17:43:49'),
(220, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 17:44:20'),
(221, 3, 'bod', 'update', 'customer', 1002, '{\"id_cust\":\"1002\",\"cust_name\":\"danh\",\"address\":\"gò\",\"telp\":\"2147483647\",\"email\":\"danh@gmail.com\",\"is_active\":\"1\",\"notes\":\"thường\",\"created_at\":\"2025-11-28 15:43:33\",\"updated_at\":\"2025-12-03 16:07:31\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"created_by_username\":\"bod\"}', '{\"cust_name\":\"danh\",\"address\":\"gò\",\"telp\":\"2147483647\",\"email\":\"danh@gmail.com\",\"notes\":\"thườ\",\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 18:19:37'),
(222, 3, 'bod', 'update', 'customer', 1002, '{\"id_cust\":\"1002\",\"cust_name\":\"danh\",\"address\":\"gò\",\"telp\":\"2147483647\",\"email\":\"danh@gmail.com\",\"is_active\":\"1\",\"notes\":\"thườ\",\"created_at\":\"2025-11-28 15:43:33\",\"updated_at\":\"2025-12-07 01:19:37\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"created_by_username\":\"bod\"}', '{\"cust_name\":\"danh\",\"address\":\"gòo\",\"telp\":\"2147483647\",\"email\":\"danh@gmail.com\",\"notes\":\"thườ\",\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 18:19:45'),
(223, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 18:37:25'),
(224, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 18:50:59'),
(225, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 18:55:14'),
(226, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 19:06:44'),
(227, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 19:14:22'),
(228, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 19:33:49'),
(229, 16, 'danh', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 19:50:58'),
(230, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 20:02:28'),
(231, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 20:05:32'),
(232, 3, 'bod', 'update', 'product', 1001, '{\"id_product\":\"1001\",\"product_name\":\"Bút bi TL-079\",\"summary\":\"Bút bi mực gel, thân nhựa trong suốt, viết mượt\",\"application\":\"Xanh dương\",\"diameter\":\"0.5\",\"bom\":null,\"is_active\":\"1\",\"created_at\":\"2025-11-24 22:53:58\",\"updated_at\":\"2025-11-24 22:53:58\",\"created_by\":null,\"total_orders\":\"1\",\"total_quantity\":\"10\",\"completed_orders\":\"0\",\"active_orders\":\"1\",\"last_order_date\":\"2025-11-02 01:11:56\",\"diameter_display\":\"0.5mm\",\"created_by_username\":null,\"bom_data\":{\"materials\":[]}}', '{\"product_name\":\"Bút bi TL-079\",\"summary\":\"Bút bi mực gel, thân nhựa trong suốt, viết mượt\",\"application\":\"Xanh dương\",\"diameter\":\"0.5\",\"is_active\":1,\"bom\":\"[{\\\"id_material\\\":\\\"1006\\\",\\\"material_name\\\":\\\"Bi kim loại 0.7mm\\\",\\\"quantity\\\":10,\\\"unit\\\":\\\"g\\\"}]\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 20:12:16'),
(233, 3, 'bod', 'update', 'product', 1001, '{\"id_product\":\"1001\",\"product_name\":\"Bút bi TL-079\",\"summary\":\"Bút bi mực gel, thân nhựa trong suốt, viết mượt\",\"application\":\"Xanh dương\",\"diameter\":\"0.5\",\"bom\":\"[{\\\"id_material\\\":\\\"1006\\\",\\\"material_name\\\":\\\"Bi kim loại 0.7mm\\\",\\\"quantity\\\":10,\\\"unit\\\":\\\"g\\\"}]\",\"is_active\":\"1\",\"created_at\":\"2025-11-24 22:53:58\",\"updated_at\":\"2025-12-07 03:12:16\",\"created_by\":null,\"total_orders\":\"2\",\"total_quantity\":\"50\",\"completed_orders\":\"0\",\"active_orders\":\"2\",\"last_order_date\":\"2025-12-07 03:09:37\",\"diameter_display\":\"0.5mm\",\"created_by_username\":null,\"bom_data\":{\"materials\":[{\"id_material\":\"1006\",\"material_name\":\"Bi kim loại 0.7mm\",\"quantity\":10,\"unit\":\"g\"}]}}', '{\"product_name\":\"Bút bi TL-079\",\"summary\":\"Bút bi mực gel, thân nhựa trong suốt, viết mượt\",\"application\":\"Xanh dương\",\"diameter\":\"0.5\",\"is_active\":1,\"bom\":\"[{\\\"id_material\\\":\\\"1006\\\",\\\"material_name\\\":\\\"Lò xo thép\\\",\\\"quantity\\\":10,\\\"unit\\\":\\\"g\\\"}]\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 20:14:50'),
(234, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 20:24:02'),
(235, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 07:29:32'),
(236, 3, 'bod', 'create', 'customer', 1003, NULL, '{\"cust_name\":\"cong\",\"address\":\"a\",\"telp\":\"090980978978969\",\"email\":\"danh868686@gmail.com\",\"notes\":\"\",\"is_active\":1,\"id_cust\":1003,\"created_by\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 07:30:50'),
(237, 3, 'bod', 'update', 'customer', 1003, '{\"id_cust\":\"1003\",\"cust_name\":\"cong\",\"address\":\"a\",\"telp\":\"2147483647\",\"email\":\"danh868686@gmail.com\",\"is_active\":\"1\",\"notes\":\"\",\"created_at\":\"2025-12-07 14:30:50\",\"updated_at\":\"2025-12-07 14:30:50\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"created_by_username\":\"bod\"}', '{\"cust_name\":\"cong\",\"address\":\"a\",\"telp\":\"888888888888888\",\"email\":\"danh868686@gmail.com\",\"notes\":\"\",\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 07:31:13'),
(238, 3, 'bod', 'update', 'customer', 1003, '{\"id_cust\":\"1003\",\"cust_name\":\"cong\",\"address\":\"a\",\"telp\":\"2147483647\",\"email\":\"danh868686@gmail.com\",\"is_active\":\"1\",\"notes\":\"\",\"created_at\":\"2025-12-07 14:30:50\",\"updated_at\":\"2025-12-07 14:30:50\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"created_by_username\":\"bod\"}', '{\"cust_name\":\"cong\",\"address\":\"a\",\"telp\":\"0975858999\",\"email\":\"danh868686@gmail.com\",\"notes\":\"\",\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 07:31:44'),
(239, 3, 'bod', 'delete', 'customer', 1003, '{\"id_cust\":\"1003\",\"cust_name\":\"cong\",\"address\":\"a\",\"telp\":\"975858999\",\"email\":\"danh868686@gmail.com\",\"is_active\":\"1\",\"notes\":\"\",\"created_at\":\"2025-12-07 14:30:50\",\"updated_at\":\"2025-12-07 14:31:44\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"created_by_username\":\"bod\"}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 07:31:58'),
(240, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 10:07:52'),
(241, 3, 'bod', 'create', 'product', 1006, NULL, '{\"product_name\":\"Bút bi TEST-H2-001\",\"summary\":\"vip\",\"application\":\"tím\",\"diameter\":\"0.7\",\"is_active\":1,\"bom\":\"[{\\\"id_material\\\":null,\\\"material_name\\\":\\\"Mực xanh H2-TEST\\\",\\\"quantity_per_unit\\\":10,\\\"unit\\\":\\\"g\\\"},{\\\"id_material\\\":null,\\\"material_name\\\":\\\"Vỏ nhựa H2-TEST\\\",\\\"quantity_per_unit\\\":8,\\\"unit\\\":\\\"cái\\\"}]\",\"id_product\":1006,\"created_by\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 10:25:51');
INSERT INTO `audit_log` (`log_id`, `user_id`, `username`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES
(242, 3, 'bod', 'update', 'product', 1006, '{\"id_product\":\"1006\",\"product_name\":\"Bút bi TEST-H2-001\",\"summary\":\"vip\",\"application\":\"tím\",\"diameter\":\"0.7\",\"bom\":\"[{\\\"id_material\\\":null,\\\"material_name\\\":\\\"Mực xanh H2-TEST\\\",\\\"quantity_per_unit\\\":10,\\\"unit\\\":\\\"g\\\"},{\\\"id_material\\\":null,\\\"material_name\\\":\\\"Vỏ nhựa H2-TEST\\\",\\\"quantity_per_unit\\\":8,\\\"unit\\\":\\\"cái\\\"}]\",\"is_active\":\"1\",\"created_at\":\"2025-12-07 17:25:51\",\"updated_at\":\"2025-12-07 17:25:51\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"diameter_display\":\"0.7mm\",\"created_by_username\":\"bod\",\"bom_data\":{\"materials\":[{\"id_material\":null,\"material_name\":\"Mực xanh H2-TEST\",\"quantity_per_unit\":10,\"unit\":\"g\"},{\"id_material\":null,\"material_name\":\"Vỏ nhựa H2-TEST\",\"quantity_per_unit\":8,\"unit\":\"cái\"}]}}', '{\"product_name\":\"Bút bi TEST-H2-001\",\"summary\":\"vip\",\"application\":\"tím\",\"diameter\":\"0.7\",\"is_active\":1,\"bom\":\"[{\\\"id_material\\\":null,\\\"material_name\\\":\\\"Mực xanh H2-TEST\\\",\\\"quantity_per_unit\\\":10,\\\"unit\\\":\\\"g\\\"},{\\\"id_material\\\":null,\\\"material_name\\\":\\\"Vỏ nhựa H2-TEST\\\",\\\"quantity_per_unit\\\":8,\\\"unit\\\":\\\"cái\\\"},{\\\"id_material\\\":\\\"1004\\\",\\\"material_name\\\":\\\"Mực gel đen\\\",\\\"quantity_per_unit\\\":8,\\\"unit\\\":\\\"g\\\"}]\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 10:28:02'),
(243, 3, 'bod', 'update', 'product', 1006, '{\"id_product\":\"1006\",\"product_name\":\"Bút bi TEST-H2-001\",\"summary\":\"vip\",\"application\":\"tím\",\"diameter\":\"0.7\",\"bom\":\"[{\\\"id_material\\\":null,\\\"material_name\\\":\\\"Mực xanh H2-TEST\\\",\\\"quantity_per_unit\\\":10,\\\"unit\\\":\\\"g\\\"},{\\\"id_material\\\":null,\\\"material_name\\\":\\\"Vỏ nhựa H2-TEST\\\",\\\"quantity_per_unit\\\":8,\\\"unit\\\":\\\"cái\\\"},{\\\"id_material\\\":\\\"1004\\\",\\\"material_name\\\":\\\"Mực gel đen\\\",\\\"quantity_per_unit\\\":8,\\\"unit\\\":\\\"g\\\"}]\",\"is_active\":\"1\",\"created_at\":\"2025-12-07 17:25:51\",\"updated_at\":\"2025-12-07 17:28:02\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"diameter_display\":\"0.7mm\",\"created_by_username\":\"bod\",\"bom_data\":{\"materials\":[{\"id_material\":null,\"material_name\":\"Mực xanh H2-TEST\",\"quantity_per_unit\":10,\"unit\":\"g\"},{\"id_material\":null,\"material_name\":\"Vỏ nhựa H2-TEST\",\"quantity_per_unit\":8,\"unit\":\"cái\"},{\"id_material\":\"1004\",\"material_name\":\"Mực gel đen\",\"quantity_per_unit\":8,\"unit\":\"g\"}]}}', '{\"product_name\":\"Bút bi TEST-H2-001\",\"summary\":\"vip\",\"application\":\"tím\",\"diameter\":\"0.7\",\"is_active\":1,\"bom\":\"[{\\\"id_material\\\":null,\\\"material_name\\\":\\\"Mực xanh H2-TEST\\\",\\\"quantity_per_unit\\\":12,\\\"unit\\\":\\\"g\\\"},{\\\"id_material\\\":null,\\\"material_name\\\":\\\"Vỏ nhựa H2-TEST\\\",\\\"quantity_per_unit\\\":8,\\\"unit\\\":\\\"cái\\\"},{\\\"id_material\\\":\\\"1004\\\",\\\"material_name\\\":\\\"Mực gel đen\\\",\\\"quantity_per_unit\\\":8,\\\"unit\\\":\\\"g\\\"},{\\\"id_material\\\":null,\\\"material_name\\\":\\\"Ruột bút H2-TEST\\\",\\\"quantity_per_unit\\\":8,\\\"unit\\\":\\\"cái\\\"}]\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 10:32:04'),
(244, 3, 'bod', 'delete', 'product', 1005, '{\"id_product\":\"1005\",\"product_name\":\"bút mực\",\"summary\":\"\",\"application\":\"xanh lá\",\"diameter\":\"0.7\",\"bom\":null,\"is_active\":\"1\",\"created_at\":\"2025-11-28 15:41:49\",\"updated_at\":\"2025-12-03 16:07:48\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"diameter_display\":\"0.7mm\",\"created_by_username\":\"bod\",\"bom_data\":{\"materials\":[]}}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 10:34:15'),
(245, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 10:56:50'),
(246, 3, 'bod', 'update', 'product', 1006, '{\"id_product\":\"1006\",\"product_name\":\"Bút bi TEST-H2-001\",\"summary\":\"vip\",\"application\":\"tím\",\"diameter\":\"0.7\",\"bom\":\"[{\\\"id_material\\\":null,\\\"material_name\\\":\\\"Mực xanh H2-TEST\\\",\\\"quantity_per_unit\\\":12,\\\"unit\\\":\\\"g\\\"},{\\\"id_material\\\":null,\\\"material_name\\\":\\\"Vỏ nhựa H2-TEST\\\",\\\"quantity_per_unit\\\":8,\\\"unit\\\":\\\"cái\\\"},{\\\"id_material\\\":\\\"1004\\\",\\\"material_name\\\":\\\"Mực gel đen\\\",\\\"quantity_per_unit\\\":8,\\\"unit\\\":\\\"g\\\"},{\\\"id_material\\\":null,\\\"material_name\\\":\\\"Ruột bút H2-TEST\\\",\\\"quantity_per_unit\\\":8,\\\"unit\\\":\\\"cái\\\"}]\",\"is_active\":\"1\",\"created_at\":\"2025-12-07 17:25:51\",\"updated_at\":\"2025-12-07 17:32:04\",\"created_by\":\"3\",\"total_orders\":\"1\",\"total_quantity\":\"10000\",\"completed_orders\":\"0\",\"active_orders\":\"1\",\"last_order_date\":\"2025-12-07 17:43:44\",\"diameter_display\":\"0.7mm\",\"created_by_username\":\"bod\",\"bom_data\":{\"materials\":[{\"id_material\":null,\"material_name\":\"Mực xanh H2-TEST\",\"quantity_per_unit\":12,\"unit\":\"g\"},{\"id_material\":null,\"material_name\":\"Vỏ nhựa H2-TEST\",\"quantity_per_unit\":8,\"unit\":\"cái\"},{\"id_material\":\"1004\",\"material_name\":\"Mực gel đen\",\"quantity_per_unit\":8,\"unit\":\"g\"},{\"id_material\":null,\"material_name\":\"Ruột bút H2-TEST\",\"quantity_per_unit\":8,\"unit\":\"cái\"}]}}', '{\"product_name\":\"Bút bi TEST-H2-001\",\"summary\":\"vip\",\"application\":\"tím\",\"diameter\":\"0.7\",\"is_active\":1,\"bom\":\"[{\\\"id_material\\\":null,\\\"material_name\\\":\\\"Mực xanh H2-TEST\\\",\\\"quantity_per_unit\\\":12,\\\"unit\\\":\\\"g\\\"},{\\\"id_material\\\":null,\\\"material_name\\\":\\\"Vỏ nhựa H2-TEST\\\",\\\"quantity_per_unit\\\":8,\\\"unit\\\":\\\"cái\\\"},{\\\"id_material\\\":null,\\\"material_name\\\":\\\"Ruột bút H2-TEST\\\",\\\"quantity_per_unit\\\":8,\\\"unit\\\":\\\"cái\\\"}]\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 11:26:11'),
(247, 3, 'bod', 'create', 'customer', 1003, NULL, '{\"cust_name\":\"cong\",\"address\":\"gò vấp\",\"telp\":\"09759769\",\"email\":\"danh12367@gmail.com\",\"notes\":\"vip\",\"is_active\":1,\"id_cust\":1003,\"created_by\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 12:22:48'),
(248, 3, 'bod', 'update', 'customer', 1003, '{\"id_cust\":\"1003\",\"cust_name\":\"cong\",\"address\":\"gò vấp\",\"telp\":\"9759769\",\"email\":\"danh12367@gmail.com\",\"is_active\":\"1\",\"notes\":\"vip\",\"created_at\":\"2025-12-07 19:22:48\",\"updated_at\":\"2025-12-07 19:22:48\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"created_by_username\":\"bod\"}', '{\"cust_name\":\"cong\",\"address\":\"gò vấp\",\"telp\":\"975976999009909\",\"email\":\"danh12367@gmail.com\",\"notes\":\"vip\",\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 12:23:07'),
(249, 3, 'bod', 'delete', 'customer', 1003, '{\"id_cust\":\"1003\",\"cust_name\":\"cong\",\"address\":\"gò vấp\",\"telp\":\"2147483647\",\"email\":\"danh12367@gmail.com\",\"is_active\":\"1\",\"notes\":\"vip\",\"created_at\":\"2025-12-07 19:22:48\",\"updated_at\":\"2025-12-07 19:23:07\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"created_by_username\":\"bod\"}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 12:23:19'),
(250, 3, 'bod', 'create', 'product', 1007, NULL, '{\"product_name\":\"danh\",\"summary\":\"\",\"application\":\"xanh\",\"diameter\":\"0.5\",\"is_active\":1,\"bom\":\"[{\\\"id_material\\\":null,\\\"material_name\\\":\\\"danh\\\",\\\"quantity_per_unit\\\":10,\\\"unit\\\":\\\"thằng\\\"},{\\\"id_material\\\":\\\"1007\\\",\\\"material_name\\\":\\\"Bi kim loại 1.0mm\\\",\\\"quantity_per_unit\\\":20,\\\"unit\\\":\\\"g\\\"}]\",\"id_product\":1007,\"created_by\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 12:24:09'),
(251, 3, 'bod', 'update', 'product', 1007, '{\"id_product\":\"1007\",\"product_name\":\"danh\",\"summary\":\"\",\"application\":\"xanh\",\"diameter\":\"0.5\",\"bom\":\"[{\\\"id_material\\\":null,\\\"material_name\\\":\\\"danh\\\",\\\"quantity_per_unit\\\":10,\\\"unit\\\":\\\"thằng\\\"},{\\\"id_material\\\":\\\"1007\\\",\\\"material_name\\\":\\\"Bi kim loại 1.0mm\\\",\\\"quantity_per_unit\\\":20,\\\"unit\\\":\\\"g\\\"}]\",\"is_active\":\"1\",\"created_at\":\"2025-12-07 19:24:09\",\"updated_at\":\"2025-12-07 19:24:09\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"diameter_display\":\"0.5mm\",\"created_by_username\":\"bod\",\"bom_data\":{\"materials\":[{\"id_material\":null,\"material_name\":\"danh\",\"quantity_per_unit\":10,\"unit\":\"thằng\"},{\"id_material\":\"1007\",\"material_name\":\"Bi kim loại 1.0mm\",\"quantity_per_unit\":20,\"unit\":\"g\"}]}}', '{\"product_name\":\"danh\",\"summary\":\"vip\",\"application\":\"tím\",\"diameter\":\"0.5\",\"is_active\":1,\"bom\":\"[{\\\"id_material\\\":null,\\\"material_name\\\":\\\"danh\\\",\\\"quantity_per_unit\\\":10,\\\"unit\\\":\\\"thằng\\\"},{\\\"id_material\\\":\\\"1007\\\",\\\"material_name\\\":\\\"Bi kim loại 1.0mm\\\",\\\"quantity_per_unit\\\":20,\\\"unit\\\":\\\"g\\\"}]\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 12:24:35'),
(252, 3, 'bod', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 14:34:28'),
(253, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 14:35:43'),
(254, 1, NULL, 'create', 'user', 17, NULL, '{\"username\":\"cong\",\"password\":\"123445\",\"role_id\":\"1\",\"full_name\":\"cong danh\",\"email\":\"danh66667@gmail.com\",\"phone\":\"\",\"is_active\":1,\"must_change_password\":1,\"created_by\":\"1\",\"created_at\":\"2025-12-07 21:38:47\",\"updated_at\":\"2025-12-07 21:38:47\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 14:38:47'),
(255, 17, 'cong', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 14:48:03'),
(256, 17, 'cong', 'change_password', 'auth', 0, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 14:48:55'),
(257, 17, 'cong', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 14:49:13'),
(258, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 14:49:16'),
(259, 1, NULL, 'lock', 'user', 17, '{\"user_id\":\"17\",\"username\":\"cong\",\"password\":\"123456D@\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"cong danh\",\"email\":\"danh66667@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":\"2025-12-07 21:48:03\",\"created_by\":\"1\",\"created_at\":\"2025-12-07 21:38:47\",\"updated_at\":\"2025-12-07 21:48:55\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"reason\":\"vi\",\"is_active\":0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 14:49:34'),
(260, 1, NULL, 'unlock', 'user', 17, '{\"user_id\":\"17\",\"username\":\"cong\",\"password\":\"123456D@\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"cong danh\",\"email\":\"danh66667@gmail.com\",\"phone\":\"\",\"is_active\":\"0\",\"last_login\":\"2025-12-07 21:48:03\",\"created_by\":\"1\",\"created_at\":\"2025-12-07 21:38:47\",\"updated_at\":\"2025-12-07 21:49:34\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 14:49:40'),
(261, 1, NULL, 'lock', 'user', 17, '{\"user_id\":\"17\",\"username\":\"cong\",\"password\":\"123456D@\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"cong danh\",\"email\":\"danh66667@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":\"2025-12-07 21:48:03\",\"created_by\":\"1\",\"created_at\":\"2025-12-07 21:38:47\",\"updated_at\":\"2025-12-07 21:49:40\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"reason\":\"vi\",\"is_active\":0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 14:51:21'),
(262, 1, NULL, 'unlock', 'user', 17, '{\"user_id\":\"17\",\"username\":\"cong\",\"password\":\"123456D@\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"cong danh\",\"email\":\"danh66667@gmail.com\",\"phone\":\"\",\"is_active\":\"0\",\"last_login\":\"2025-12-07 21:48:03\",\"created_by\":\"1\",\"created_at\":\"2025-12-07 21:38:47\",\"updated_at\":\"2025-12-07 21:51:21\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 14:51:32'),
(263, 1, NULL, 'lock', 'user', 17, '{\"user_id\":\"17\",\"username\":\"cong\",\"password\":\"123456D@\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"cong danh\",\"email\":\"danh66667@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":\"2025-12-07 21:48:03\",\"created_by\":\"1\",\"created_at\":\"2025-12-07 21:38:47\",\"updated_at\":\"2025-12-07 21:51:32\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"reason\":\"vi\",\"is_active\":0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 14:52:26'),
(264, 1, NULL, 'unlock', 'user', 17, '{\"user_id\":\"17\",\"username\":\"cong\",\"password\":\"123456D@\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"cong danh\",\"email\":\"danh66667@gmail.com\",\"phone\":\"\",\"is_active\":\"0\",\"last_login\":\"2025-12-07 21:48:03\",\"created_by\":\"1\",\"created_at\":\"2025-12-07 21:38:47\",\"updated_at\":\"2025-12-07 21:52:26\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 14:52:34'),
(265, 1, NULL, 'reset_password', 'user', 17, '{\"user_id\":\"17\",\"username\":\"cong\",\"password\":\"123456D@\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":null,\"full_name\":\"cong danh\",\"email\":\"danh66667@gmail.com\",\"phone\":\"\",\"is_active\":\"1\",\"last_login\":\"2025-12-07 21:48:03\",\"created_by\":\"1\",\"created_at\":\"2025-12-07 21:38:47\",\"updated_at\":\"2025-12-07 21:52:34\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"temp_password_generated\":\"YES\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 14:53:06'),
(266, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 14:54:17'),
(267, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-07 14:54:52'),
(268, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-08 11:21:25'),
(269, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-08 16:57:45'),
(270, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-08 17:01:04'),
(271, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-08 17:58:36'),
(272, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-09 08:58:05'),
(273, 3, 'bod', 'update', 'product', 1001, '{\"id_product\":\"1001\",\"product_name\":\"Bút bi TL-079\",\"summary\":\"Bút bi mực gel, thân nhựa trong suốt, viết mượt\",\"application\":\"Xanh dương\",\"diameter\":\"0.5\",\"bom\":\"[{\\\"id_material\\\": 1001, \\\"quantity_per_unit\\\": 10.0}, {\\\"id_material\\\": 1002, \\\"quantity_per_unit\\\": 5.0}, {\\\"id_material\\\": 1003, \\\"quantity_per_unit\\\": 3.0}, {\\\"id_material\\\": 1006, \\\"quantity_per_unit\\\": 0.5}]\",\"is_active\":\"1\",\"created_at\":\"2025-11-24 22:53:58\",\"updated_at\":\"2025-12-07 14:55:46\",\"created_by\":null,\"total_orders\":\"1\",\"total_quantity\":\"10\",\"completed_orders\":\"0\",\"active_orders\":\"1\",\"last_order_date\":\"2025-11-02 01:11:56\",\"diameter_display\":\"0.5mm\",\"created_by_username\":null,\"bom_data\":{\"materials\":[{\"id_material\":1001,\"quantity_per_unit\":10,\"material_name\":\"Test Matereal\",\"unit\":\"g\",\"uom\":\"g\"},{\"id_material\":1002,\"quantity_per_unit\":5,\"material_name\":\"Nhựa ABS\",\"unit\":\"g\",\"uom\":\"g\"},{\"id_material\":1003,\"quantity_per_unit\":3,\"material_name\":\"Mực gel xanh\",\"unit\":\"g\",\"uom\":\"g\"},{\"id_material\":1006,\"quantity_per_unit\":0.5,\"material_name\":\"Bi kim loại 0.7mm\",\"unit\":\"mm\",\"uom\":\"mm\"}]}}', '{\"product_name\":\"Bút bi TL-079\",\"summary\":\"Bút bi mực gel, thân nhựa trong suốt, viết mượt\",\"application\":\"Xanh dương\",\"diameter\":\"0.5\",\"is_active\":1,\"bom\":\"[{\\\"id_material\\\":\\\"1001\\\",\\\"material_name\\\":\\\"Test Matereal\\\",\\\"quantity_per_unit\\\":10,\\\"unit\\\":\\\"g\\\"},{\\\"id_material\\\":\\\"1002\\\",\\\"material_name\\\":\\\"Nhựa ABS\\\",\\\"quantity_per_unit\\\":10,\\\"unit\\\":\\\"g\\\"},{\\\"id_material\\\":\\\"1003\\\",\\\"material_name\\\":\\\"Mực gel xanh\\\",\\\"quantity_per_unit\\\":3,\\\"unit\\\":\\\"g\\\"},{\\\"id_material\\\":\\\"1006\\\",\\\"material_name\\\":\\\"Bi kim loại 0.7mm\\\",\\\"quantity_per_unit\\\":0.5,\\\"unit\\\":\\\"mm\\\"}]\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-09 09:01:40'),
(274, 3, 'bod', 'update', 'product', 1002, '{\"id_product\":\"1002\",\"product_name\":\"Bút bi TL-050\",\"summary\":\"Bút bi dầu, thân nhựa màu, giá rẻ\",\"application\":\"Đen\",\"diameter\":\"0.5\",\"bom\":\"[{\\\"id_material\\\": 1002, \\\"quantity_per_unit\\\": 8.0}, {\\\"id_material\\\": 1004, \\\"quantity_per_unit\\\": 2.0}, {\\\"id_material\\\": 1005, \\\"quantity_per_unit\\\": 1.0}, {\\\"id_material\\\": 1006, \\\"quantity_per_unit\\\": 0.3}]\",\"is_active\":\"1\",\"created_at\":\"2025-11-24 22:53:58\",\"updated_at\":\"2025-12-07 14:55:46\",\"created_by\":null,\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"diameter_display\":\"0.5mm\",\"created_by_username\":null,\"bom_data\":{\"materials\":[{\"id_material\":1002,\"quantity_per_unit\":8,\"material_name\":\"Nhựa ABS\",\"unit\":\"g\",\"uom\":\"g\"},{\"id_material\":1004,\"quantity_per_unit\":2,\"material_name\":\"Mực gel đen\",\"unit\":\"g\",\"uom\":\"g\"},{\"id_material\":1005,\"quantity_per_unit\":1,\"material_name\":\"Bi kim loại 0.5mm\",\"unit\":\"mm\",\"uom\":\"mm\"},{\"id_material\":1006,\"quantity_per_unit\":0.3,\"material_name\":\"Bi kim loại 0.7mm\",\"unit\":\"mm\",\"uom\":\"mm\"}]}}', '{\"product_name\":\"Bút bi TL-050\",\"summary\":\"Bút bi dầu, thân nhựa màu, giá rẻ\",\"application\":\"Đen\",\"diameter\":\"0.5\",\"is_active\":1,\"bom\":\"[{\\\"id_material\\\":\\\"1002\\\",\\\"material_name\\\":\\\"Nhựa ABS\\\",\\\"quantity_per_unit\\\":10,\\\"unit\\\":\\\"g\\\"},{\\\"id_material\\\":\\\"1004\\\",\\\"material_name\\\":\\\"Mực gel đen\\\",\\\"quantity_per_unit\\\":3,\\\"unit\\\":\\\"g\\\"},{\\\"id_material\\\":\\\"1005\\\",\\\"material_name\\\":\\\"Bi kim loại 0.5mm\\\",\\\"quantity_per_unit\\\":5,\\\"unit\\\":\\\"mm\\\"},{\\\"id_material\\\":\\\"1006\\\",\\\"material_name\\\":\\\"Bi kim loại 0.7mm\\\",\\\"quantity_per_unit\\\":0.3,\\\"unit\\\":\\\"mm\\\"}]\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-09 09:02:19'),
(275, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-09 17:48:20'),
(276, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-09 18:33:06'),
(277, NULL, 'SYSTEM_TRIGGER', 'restore_stock', 'finished_stock', 1004, '{\"qty_restored\": \"35000\"}', '{\"reason\": \"order_deleted\"}', '127.0.0.1', NULL, '2025-12-09 19:30:51'),
(278, NULL, 'SYSTEM_TRIGGER', 'restore_stock', 'finished_stock', 1003, '{\"qty_restored\": \"30\"}', '{\"reason\": \"order_deleted\"}', '127.0.0.1', NULL, '2025-12-09 19:30:55'),
(279, NULL, 'SYSTEM_TRIGGER', 'restore_stock', 'finished_stock', 1002, '{\"qty_restored\": \"30\"}', '{\"reason\": \"order_deleted\"}', '127.0.0.1', NULL, '2025-12-09 19:30:59'),
(280, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-09 19:31:10'),
(281, NULL, 'SYSTEM_TRIGGER', 'deduct_stock', 'finished_stock', 1002, '{\"stock_before\": \"35095\"}', '{\"qty_deducted\": \"15\", \"stock_after\": 35080}', '127.0.0.1', NULL, '2025-12-09 19:32:15'),
(282, NULL, 'SYSTEM_TRIGGER', 'deduct_stock', 'finished_stock', 1003, '{\"stock_before\": \"35080\"}', '{\"qty_deducted\": \"4000\", \"stock_after\": 31080}', '127.0.0.1', NULL, '2025-12-09 19:33:45'),
(283, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-09 19:58:13'),
(284, NULL, 'SYSTEM_TRIGGER', 'restore_stock', 'finished_stock', 1003, '{\"qty_restored\": \"40000\"}', '{\"reason\": \"order_deleted\"}', '127.0.0.1', NULL, '2025-12-09 19:58:19'),
(285, NULL, 'SYSTEM_TRIGGER', 'restore_stock', 'finished_stock', 1002, '{\"qty_restored\": \"15\"}', '{\"reason\": \"order_deleted\"}', '127.0.0.1', NULL, '2025-12-09 19:58:23'),
(286, NULL, 'SYSTEM_TRIGGER', 'deduct_stock', 'finished_stock', 1002, '{\"stock_before\": \"40015\"}', '{\"qty_deducted\": \"1000\", \"stock_after\": 39015}', '127.0.0.1', NULL, '2025-12-09 19:59:06'),
(287, NULL, 'SYSTEM_TRIGGER', 'deduct_stock', 'finished_stock', 1003, '{\"stock_before\": \"39015\"}', '{\"qty_deducted\": \"39015\", \"stock_after\": 0}', '127.0.0.1', NULL, '2025-12-09 19:59:58'),
(288, NULL, 'SYSTEM_TRIGGER', 'restore_stock', 'finished_stock', 1003, '{\"qty_restored\": \"40000\"}', '{\"reason\": \"order_deleted\"}', '127.0.0.1', NULL, '2025-12-09 20:00:42'),
(289, NULL, 'SYSTEM_TRIGGER', 'deduct_stock', 'finished_stock', 1003, '{\"stock_before\": \"40000\"}', '{\"qty_deducted\": \"39955\", \"stock_after\": 45}', '127.0.0.1', NULL, '2025-12-09 20:01:51'),
(290, NULL, 'SYSTEM_TRIGGER', 'restore_stock', 'finished_stock', 1003, '{\"qty_restored\": \"39955\"}', '{\"reason\": \"order_deleted\"}', '127.0.0.1', NULL, '2025-12-09 20:05:37'),
(291, NULL, 'SYSTEM_TRIGGER', 'restore_stock', 'finished_stock', 1002, '{\"qty_restored\": \"1000\"}', '{\"reason\": \"order_deleted\"}', '127.0.0.1', NULL, '2025-12-09 20:06:49'),
(292, NULL, 'SYSTEM_TRIGGER', 'deduct_stock', 'finished_stock', 1002, '{\"stock_before\": \"41000\"}', '{\"qty_deducted\": \"1000\", \"stock_after\": 40000}', '127.0.0.1', NULL, '2025-12-09 20:07:24'),
(293, NULL, 'SYSTEM_TRIGGER', 'deduct_stock', 'finished_stock', 1003, '{\"stock_before\": \"40000\"}', '{\"qty_deducted\": \"40000\", \"stock_after\": 0}', '127.0.0.1', NULL, '2025-12-09 20:09:02'),
(294, NULL, 'system_migration', 'drop_triggers', 'project', NULL, NULL, '{\"details\": \"Removed all stock management triggers from the project table to centralize logic in the application layer.\"}', NULL, NULL, '2025-12-10 16:46:23'),
(295, NULL, 'system_migration', 'add_column', 'project', NULL, NULL, '{\"column\": \"stock_allocation\", \"type\": \"JSON\", \"purpose\": \"Track inventory allocation per order\"}', NULL, NULL, '2025-12-10 17:29:15'),
(296, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 14:53:10'),
(297, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 17:58:32'),
(298, 1, NULL, 'create', 'user', 18, NULL, '{\"staff_id\":\"1033\",\"username\":\"lead\",\"password\":\"Lead123@\",\"role_id\":\"1\",\"full_name\":\"danh\",\"email\":\"danh77656@gmail.com\",\"phone\":\"09769857\",\"is_active\":1,\"must_change_password\":1,\"created_by\":\"1\",\"created_at\":\"2025-12-12 01:21:59\",\"updated_at\":\"2025-12-12 01:21:59\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 18:21:59'),
(299, 1, NULL, 'update', 'user', 18, '{\"user_id\":\"18\",\"username\":\"lead\",\"password\":\"Lead123@\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"1\",\"staff_id\":\"1033\",\"full_name\":\"danh\",\"email\":\"danh77656@gmail.com\",\"phone\":\"09769857\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-12 01:21:59\",\"updated_at\":\"2025-12-12 01:21:59\",\"staff_name\":\"danh\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"role_id\":\"4\",\"updated_at\":\"2025-12-12 01:26:19\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 18:26:19'),
(300, 1, NULL, 'reset_password', 'user', 18, '{\"user_id\":\"18\",\"username\":\"lead\",\"password\":\"Lead123@\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"4\",\"staff_id\":\"1033\",\"full_name\":\"danh\",\"email\":\"danh77656@gmail.com\",\"phone\":\"09769857\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-12 01:21:59\",\"updated_at\":\"2025-12-12 01:26:19\",\"staff_name\":\"danh\",\"role_name\":\"system_admin\",\"role_display_name\":\"Qu\\u1ea3n tr\\u1ecb vi\\u00ean H\\u1ec7 th\\u1ed1ng\",\"role_level\":\"90\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"temp_password_generated\":\"YES\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 18:27:10'),
(301, 1, NULL, 'lock', 'user', 18, '{\"user_id\":\"18\",\"username\":\"lead\",\"password\":\"7ECB8D3E\",\"temp_password\":\"7ECB8D3E\",\"must_change_password\":\"1\",\"role_id\":\"4\",\"staff_id\":\"1033\",\"full_name\":\"danh\",\"email\":\"danh77656@gmail.com\",\"phone\":\"09769857\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-12 01:21:59\",\"updated_at\":\"2025-12-12 01:27:10\",\"staff_name\":\"danh\",\"role_name\":\"system_admin\",\"role_display_name\":\"Qu\\u1ea3n tr\\u1ecb vi\\u00ean H\\u1ec7 th\\u1ed1ng\",\"role_level\":\"90\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"reason\":\"vi\",\"is_active\":0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 18:27:37'),
(302, 1, NULL, 'unlock', 'user', 18, '{\"user_id\":\"18\",\"username\":\"lead\",\"password\":\"7ECB8D3E\",\"temp_password\":\"7ECB8D3E\",\"must_change_password\":\"1\",\"role_id\":\"4\",\"staff_id\":\"1033\",\"full_name\":\"danh\",\"email\":\"danh77656@gmail.com\",\"phone\":\"09769857\",\"is_active\":\"0\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-12 01:21:59\",\"updated_at\":\"2025-12-12 01:27:37\",\"staff_name\":\"danh\",\"role_name\":\"system_admin\",\"role_display_name\":\"Qu\\u1ea3n tr\\u1ecb vi\\u00ean H\\u1ec7 th\\u1ed1ng\",\"role_level\":\"90\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 18:27:42'),
(303, 1, NULL, 'reset_password', 'user', 18, '{\"user_id\":\"18\",\"username\":\"lead\",\"password\":\"7ECB8D3E\",\"temp_password\":\"7ECB8D3E\",\"must_change_password\":\"1\",\"role_id\":\"4\",\"staff_id\":\"1033\",\"full_name\":\"danh\",\"email\":\"danh77656@gmail.com\",\"phone\":\"09769857\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-12 01:21:59\",\"updated_at\":\"2025-12-12 01:27:42\",\"staff_name\":\"danh\",\"role_name\":\"system_admin\",\"role_display_name\":\"Qu\\u1ea3n tr\\u1ecb vi\\u00ean H\\u1ec7 th\\u1ed1ng\",\"role_level\":\"90\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"temp_password_generated\":\"YES\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 18:27:51'),
(304, 1, NULL, 'update', 'user', 2, '{\"user_id\":\"2\",\"username\":\"leader\",\"password\":\"leader\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"2\",\"staff_id\":\"1004\",\"full_name\":\"Tr\\u01b0\\u1edfng d\\u00e2y chuy\\u1ec1n\",\"email\":\"\",\"phone\":\"0\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":null,\"created_at\":\"2025-11-01 22:49:53\",\"updated_at\":\"2025-12-11 22:22:31\",\"staff_name\":\"Tr\\u01b0\\u1edfng d\\u00e2y chuy\\u1ec1n\",\"role_name\":\"line_manager\",\"role_display_name\":\"Tr\\u01b0\\u1edfng d\\u00e2y chuy\\u1ec1n\",\"role_level\":\"70\",\"created_by_username\":null,\"created_by_fullname\":null}', '{\"role_id\":\"1\",\"updated_at\":\"2025-12-12 01:28:21\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 18:28:21'),
(305, 18, 'lead', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 18:29:54'),
(306, 18, 'lead', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 18:48:57'),
(307, 18, 'lead', 'change_password', 'auth', 0, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 18:57:32'),
(308, 18, 'lead', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 19:06:32'),
(309, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 19:06:37'),
(310, 1, 'admin', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 19:06:43'),
(311, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 19:06:48'),
(312, 3, 'bod', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 19:06:50'),
(313, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 19:24:51'),
(314, 1, NULL, 'create', 'user', 19, NULL, '{\"staff_id\":\"1001\",\"username\":\"lead1\",\"password\":\"123456\",\"role_id\":\"1\",\"full_name\":\"Leader1\",\"email\":\"leader1@mail.com\",\"phone\":\"8212312\",\"is_active\":1,\"must_change_password\":1,\"created_by\":\"1\",\"created_at\":\"2025-12-12 02:25:47\",\"updated_at\":\"2025-12-12 02:25:47\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 19:25:47'),
(315, 1, NULL, 'reset_password', 'user', 19, '{\"user_id\":\"19\",\"username\":\"lead1\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"1\",\"role_id\":\"1\",\"staff_id\":\"1001\",\"full_name\":\"Leader1\",\"email\":\"leader1@mail.com\",\"phone\":\"8212312\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-12 02:25:47\",\"updated_at\":\"2025-12-12 02:25:47\",\"staff_name\":\"Leader1\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"temp_password_generated\":\"YES\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 19:25:59'),
(316, 1, NULL, 'lock', 'user', 19, '{\"user_id\":\"19\",\"username\":\"lead1\",\"password\":\"6EB32AA2\",\"temp_password\":\"6EB32AA2\",\"must_change_password\":\"1\",\"role_id\":\"1\",\"staff_id\":\"1001\",\"full_name\":\"Leader1\",\"email\":\"leader1@mail.com\",\"phone\":\"8212312\",\"is_active\":\"1\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-12 02:25:47\",\"updated_at\":\"2025-12-12 02:25:59\",\"staff_name\":\"Leader1\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"reason\":\"vi\",\"is_active\":0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 19:26:05'),
(317, 1, NULL, 'unlock', 'user', 19, '{\"user_id\":\"19\",\"username\":\"lead1\",\"password\":\"6EB32AA2\",\"temp_password\":\"6EB32AA2\",\"must_change_password\":\"1\",\"role_id\":\"1\",\"staff_id\":\"1001\",\"full_name\":\"Leader1\",\"email\":\"leader1@mail.com\",\"phone\":\"8212312\",\"is_active\":\"0\",\"last_login\":null,\"created_by\":\"1\",\"created_at\":\"2025-12-12 02:25:47\",\"updated_at\":\"2025-12-12 02:26:05\",\"staff_name\":\"Leader1\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"is_active\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 19:26:09'),
(318, 1, 'admin', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 19:26:19'),
(319, 19, 'lead1', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 19:26:40'),
(320, 19, 'lead1', 'change_password', 'auth', 0, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 19:27:00'),
(321, 19, 'lead1', 'logout', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 19:27:08'),
(322, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 19:27:35'),
(323, 1, NULL, 'update', 'user', 19, '{\"user_id\":\"19\",\"username\":\"lead1\",\"password\":\"123456\",\"temp_password\":null,\"must_change_password\":\"0\",\"role_id\":\"1\",\"staff_id\":\"1001\",\"full_name\":\"Leader1\",\"email\":\"leader1@mail.com\",\"phone\":\"8212312\",\"is_active\":\"1\",\"last_login\":\"2025-12-12 02:26:40\",\"created_by\":\"1\",\"created_at\":\"2025-12-12 02:25:47\",\"updated_at\":\"2025-12-12 02:27:00\",\"staff_name\":\"Leader1\",\"role_name\":\"bod\",\"role_display_name\":\"Ban Gi\\u00e1m \\u0110\\u1ed1c\",\"role_level\":\"100\",\"created_by_username\":\"admin\",\"created_by_fullname\":\"Administrator\"}', '{\"role_id\":\"4\",\"updated_at\":\"2025-12-12 02:28:01\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-11 19:28:01'),
(324, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-12 08:29:36'),
(325, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-14 04:07:17'),
(326, 3, 'bod', 'delete', 'customer', 1017, '{\"id_cust\":\"1017\",\"cust_name\":\"Order Test Customer 1765699338\",\"address\":\"123 Order Test Street\",\"telp\":\"123456789\",\"email\":\"order.test1765699338@exam\",\"is_active\":\"1\",\"notes\":null,\"created_at\":\"2025-12-14 09:02:18\",\"updated_at\":\"2025-12-14 09:02:18\",\"created_by\":\"1\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"created_by_username\":\"admin\"}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-14 08:41:59'),
(327, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.107.0 Chrome/142.0.7444.175 Electron/39.2.3 Safari/537.36', '2025-12-14 17:30:04'),
(328, 3, 'bod', 'update', 'customer', 1002, '{\"id_cust\":\"1002\",\"cust_name\":\"danh\",\"address\":\"gòo\",\"telp\":\"2147483647\",\"email\":\"danh@gmail.com\",\"is_active\":\"1\",\"notes\":\"thườ\",\"created_at\":\"2025-11-28 15:43:33\",\"updated_at\":\"2025-12-07 01:19:45\",\"created_by\":\"3\",\"total_orders\":\"2\",\"total_quantity\":\"35\",\"completed_orders\":\"0\",\"active_orders\":\"2\",\"last_order_date\":\"2025-12-15 19:35:21\",\"created_by_username\":\"bod\"}', '{\"notes\":\"thường\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 12:51:41'),
(329, 3, 'bod', 'update', 'customer', 1002, '{\"id_cust\":\"1002\",\"cust_name\":\"danh\",\"address\":\"gòo\",\"telp\":\"2147483647\",\"email\":\"danh@gmail.com\",\"is_active\":\"1\",\"notes\":\"thường\",\"created_at\":\"2025-11-28 15:43:33\",\"updated_at\":\"2025-12-15 19:51:41\",\"created_by\":\"3\",\"total_orders\":\"2\",\"total_quantity\":\"35\",\"completed_orders\":\"0\",\"active_orders\":\"2\",\"last_order_date\":\"2025-12-15 19:35:21\",\"created_by_username\":\"bod\"}', '{\"notes\":\"danh\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 12:52:08'),
(330, 3, 'bod', 'update', 'customer', 1002, '{\"id_cust\":\"1002\",\"cust_name\":\"danh\",\"address\":\"gòo\",\"telp\":\"2147483647\",\"email\":\"danh@gmail.com\",\"is_active\":\"1\",\"notes\":\"danh\",\"created_at\":\"2025-11-28 15:43:33\",\"updated_at\":\"2025-12-15 19:52:08\",\"created_by\":\"3\",\"total_orders\":\"2\",\"total_quantity\":\"35\",\"completed_orders\":\"0\",\"active_orders\":\"2\",\"last_order_date\":\"2025-12-15 19:35:21\",\"created_by_username\":\"bod\"}', '{\"notes\":\"dan\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 12:52:28'),
(331, 3, 'bod', 'update', 'customer', 1002, '{\"id_cust\":\"1002\",\"cust_name\":\"danh\",\"address\":\"gòo\",\"telp\":\"2147483647\",\"email\":\"danh@gmail.com\",\"is_active\":\"1\",\"notes\":\"dan\",\"created_at\":\"2025-11-28 15:43:33\",\"updated_at\":\"2025-12-15 19:52:28\",\"created_by\":\"3\",\"total_orders\":\"2\",\"total_quantity\":\"35\",\"completed_orders\":\"0\",\"active_orders\":\"2\",\"last_order_date\":\"2025-12-15 19:35:21\",\"created_by_username\":\"bod\"}', '{\"notes\":\"vip\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 12:56:41'),
(332, 3, 'bod', 'update', 'customer', 1001, '{\"id_cust\":\"1001\",\"cust_name\":\"Tes Customer\",\"address\":\"Indonesia\",\"telp\":\"21293383\",\"email\":\"tes@mail.com\",\"is_active\":\"1\",\"notes\":\"vip\",\"created_at\":\"2025-11-24 22:53:34\",\"updated_at\":\"2025-12-10 01:20:01\",\"created_by\":null,\"total_orders\":\"7\",\"total_quantity\":\"1215\",\"completed_orders\":\"0\",\"active_orders\":\"7\",\"last_order_date\":\"2025-12-15 03:33:30\",\"created_by_username\":null}', '{\"notes\":\"thường\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 13:02:13'),
(333, 3, 'bod', 'update', 'customer', 1001, '{\"id_cust\":\"1001\",\"cust_name\":\"Tes Customer\",\"address\":\"Indonesia\",\"telp\":\"21293383\",\"email\":\"tes@mail.com\",\"is_active\":\"1\",\"notes\":\"thường\",\"created_at\":\"2025-11-24 22:53:34\",\"updated_at\":\"2025-12-15 20:02:13\",\"created_by\":null,\"total_orders\":\"7\",\"total_quantity\":\"1215\",\"completed_orders\":\"0\",\"active_orders\":\"7\",\"last_order_date\":\"2025-12-15 03:33:30\",\"created_by_username\":null}', '{\"notes\":\"vip\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 13:03:17'),
(334, 3, 'bod', 'update', 'customer', 1001, '{\"id_cust\":\"1001\",\"cust_name\":\"Tes Customer\",\"address\":\"Indonesia\",\"telp\":\"21293383\",\"email\":\"tes@mail.com\",\"is_active\":\"1\",\"notes\":\"vip\",\"created_at\":\"2025-11-24 22:53:34\",\"updated_at\":\"2025-12-15 20:03:17\",\"created_by\":null,\"total_orders\":\"7\",\"total_quantity\":\"1215\",\"completed_orders\":\"0\",\"active_orders\":\"7\",\"last_order_date\":\"2025-12-15 03:33:30\",\"created_by_username\":null}', '{\"notes\":\"thường\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 13:03:31'),
(335, 3, 'bod', 'update', 'customer', 1001, '{\"id_cust\":\"1001\",\"cust_name\":\"Tes Customer\",\"address\":\"Indonesia\",\"telp\":\"21293383\",\"email\":\"tes@mail.com\",\"is_active\":\"1\",\"notes\":\"thường\",\"created_at\":\"2025-11-24 22:53:34\",\"updated_at\":\"2025-12-15 20:03:31\",\"created_by\":null,\"total_orders\":\"7\",\"total_quantity\":\"1215\",\"completed_orders\":\"0\",\"active_orders\":\"7\",\"last_order_date\":\"2025-12-15 03:33:30\",\"created_by_username\":null}', '{\"notes\":\"vip\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 13:03:51'),
(336, 3, 'bod', 'update', 'customer', 1002, '{\"id_cust\":\"1002\",\"cust_name\":\"danh\",\"address\":\"gòo\",\"telp\":\"2147483647\",\"email\":\"danh@gmail.com\",\"is_active\":\"1\",\"notes\":\"vip\",\"created_at\":\"2025-11-28 15:43:33\",\"updated_at\":\"2025-12-15 19:56:41\",\"created_by\":\"3\",\"total_orders\":\"2\",\"total_quantity\":\"35\",\"completed_orders\":\"0\",\"active_orders\":\"2\",\"last_order_date\":\"2025-12-15 19:35:21\",\"created_by_username\":\"bod\"}', '{\"notes\":\"danh\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 13:06:56'),
(337, 3, 'bod', 'update', 'customer', 1002, '{\"id_cust\":\"1002\",\"cust_name\":\"danh\",\"address\":\"gòo\",\"telp\":\"2147483647\",\"email\":\"danh@gmail.com\",\"is_active\":\"1\",\"notes\":\"danh\",\"created_at\":\"2025-11-28 15:43:33\",\"updated_at\":\"2025-12-15 20:06:56\",\"created_by\":\"3\",\"total_orders\":\"2\",\"total_quantity\":\"35\",\"completed_orders\":\"0\",\"active_orders\":\"2\",\"last_order_date\":\"2025-12-15 19:35:21\",\"created_by_username\":\"bod\"}', '{\"notes\":\"vip\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 13:10:28'),
(338, 3, 'bod', 'update', 'customer', 1002, '{\"id_cust\":\"1002\",\"cust_name\":\"danh\",\"address\":\"gòo\",\"telp\":\"2147483647\",\"email\":\"danh@gmail.com\",\"is_active\":\"1\",\"notes\":\"vip\",\"created_at\":\"2025-11-28 15:43:33\",\"updated_at\":\"2025-12-15 20:10:28\",\"created_by\":\"3\",\"total_orders\":\"2\",\"total_quantity\":\"35\",\"completed_orders\":\"0\",\"active_orders\":\"2\",\"last_order_date\":\"2025-12-15 19:35:21\",\"created_by_username\":\"bod\"}', '{\"notes\":\"danh\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 13:20:36'),
(339, 3, 'bod', 'update', 'customer', 1002, '{\"id_cust\":\"1002\",\"cust_name\":\"danh\",\"address\":\"gòo\",\"telp\":\"2147483647\",\"email\":\"danh@gmail.com\",\"is_active\":\"1\",\"notes\":\"danh\",\"created_at\":\"2025-11-28 15:43:33\",\"updated_at\":\"2025-12-15 20:20:36\",\"created_by\":\"3\",\"total_orders\":\"2\",\"total_quantity\":\"35\",\"completed_orders\":\"0\",\"active_orders\":\"2\",\"last_order_date\":\"2025-12-15 19:35:21\",\"created_by_username\":\"bod\"}', '{\"notes\":\"danh\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 13:20:57'),
(340, 3, 'bod', 'create', 'customer', 1003, NULL, '{\"cust_name\":\"test\",\"email\":\"danhe54365356@gmail.com\",\"telp\":\"098787878\",\"address\":\"hue\",\"notes\":\"hi\",\"id_cust\":1003,\"is_active\":1,\"created_by\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 13:23:55'),
(341, 3, 'bod', 'update', 'customer', 1003, '{\"id_cust\":\"1003\",\"cust_name\":\"test\",\"address\":\"hue\",\"telp\":\"98787878\",\"email\":\"danhe54365356@gmail.com\",\"is_active\":\"1\",\"notes\":\"hi\",\"created_at\":\"2025-12-15 20:23:55\",\"updated_at\":\"2025-12-15 20:23:55\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"created_by_username\":\"bod\"}', '{\"id_cust\":\"1003\",\"cust_name\":\"test\",\"email\":\"danhe54365356@gmail.com\",\"telp\":\"9878787888\",\"address\":\"hue\",\"notes\":\"hi\",\"is_active\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 13:24:29'),
(342, 3, 'bod', 'delete', 'customer', 1003, '{\"id_cust\":\"1003\",\"cust_name\":\"test\",\"address\":\"hue\",\"telp\":\"2147483647\",\"email\":\"danhe54365356@gmail.com\",\"is_active\":\"1\",\"notes\":\"hi\",\"created_at\":\"2025-12-15 20:23:55\",\"updated_at\":\"2025-12-15 20:24:29\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"created_by_username\":\"bod\"}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 13:24:45'),
(343, 3, 'bod', 'create', 'customer', 1003, NULL, '{\"cust_name\":\"t\",\"email\":\"danh8888@gmail.com\",\"telp\":\"099798699797796\",\"address\":\"h\",\"notes\":\"\",\"id_cust\":1003,\"is_active\":1,\"created_by\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 14:27:08'),
(344, 3, 'bod', 'delete', 'customer', 1003, '{\"id_cust\":\"1003\",\"cust_name\":\"t\",\"address\":\"h\",\"telp\":\"099798699797796\",\"email\":\"danh8888@gmail.com\",\"is_active\":\"1\",\"notes\":\"\",\"created_at\":\"2025-12-15 21:27:08\",\"updated_at\":\"2025-12-15 21:27:08\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"created_by_username\":\"bod\"}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 14:27:21');
INSERT INTO `audit_log` (`log_id`, `user_id`, `username`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES
(345, 3, 'bod', 'create', 'customer', 1003, NULL, '{\"cust_name\":\"j\",\"email\":\"danh66878@gmail.com\",\"telp\":\"09908899\",\"address\":\"l\",\"notes\":\"\",\"id_cust\":1003,\"is_active\":1,\"created_by\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 14:54:23'),
(346, 3, 'bod', 'delete', 'customer', 1003, '{\"id_cust\":\"1003\",\"cust_name\":\"j\",\"address\":\"l\",\"telp\":\"09908899\",\"email\":\"danh66878@gmail.com\",\"is_active\":\"1\",\"notes\":\"\",\"created_at\":\"2025-12-15 21:54:23\",\"updated_at\":\"2025-12-15 21:54:23\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"created_by_username\":\"bod\"}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 14:54:38'),
(347, 3, 'bod', 'create', 'customer', 1003, NULL, '{\"cust_name\":\"k\",\"email\":\"danho80980@gmail.com\",\"telp\":\"098686879\",\"address\":\"l\",\"notes\":\"\",\"id_cust\":1003,\"is_active\":1,\"created_by\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 14:55:29'),
(348, 3, 'bod', 'update', 'customer', 1003, '{\"id_cust\":\"1003\",\"cust_name\":\"k\",\"address\":\"l\",\"telp\":\"098686879\",\"email\":\"danho80980@gmail.com\",\"is_active\":\"1\",\"notes\":\"\",\"created_at\":\"2025-12-15 21:55:29\",\"updated_at\":\"2025-12-15 21:55:29\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"created_by_username\":\"bod\"}', '{\"id_cust\":\"1003\",\"cust_name\":\"k\",\"email\":\"danho80980@gmail.com\",\"telp\":\"0986868999\",\"address\":\"l\",\"notes\":\"\",\"is_active\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 14:55:40'),
(349, 3, 'bod', 'create', 'customer', 1004, NULL, '{\"cust_name\":\"cong\",\"email\":\"danh1322@gmail.com\",\"telp\":\"090908089\",\"address\":\"g\",\"notes\":\"\",\"id_cust\":1004,\"is_active\":1,\"created_by\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 15:05:55'),
(350, 3, 'bod', 'delete', 'customer', 1004, '{\"id_cust\":\"1004\",\"cust_name\":\"cong\",\"address\":\"g\",\"telp\":\"090908089\",\"email\":\"danh1322@gmail.com\",\"is_active\":\"1\",\"notes\":\"\",\"created_at\":\"2025-12-15 22:05:55\",\"updated_at\":\"2025-12-15 22:05:55\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"created_by_username\":\"bod\"}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 15:11:38'),
(351, 3, 'bod', 'delete', 'customer', 1003, '{\"id_cust\":\"1003\",\"cust_name\":\"k\",\"address\":\"l\",\"telp\":\"0986868999\",\"email\":\"danho80980@gmail.com\",\"is_active\":\"1\",\"notes\":\"\",\"created_at\":\"2025-12-15 21:55:29\",\"updated_at\":\"2025-12-15 21:55:40\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"created_by_username\":\"bod\"}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 15:11:55'),
(352, 3, 'bod', 'create', 'customer', 1003, NULL, '{\"cust_name\":\"o\",\"email\":\"danh878866@gmail.com\",\"telp\":\"09889898\",\"address\":\"p\",\"notes\":\"\",\"id_cust\":1003,\"is_active\":1,\"created_by\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 15:12:30'),
(353, 3, 'bod', 'update', 'customer', 1003, '{\"id_cust\":\"1003\",\"cust_name\":\"o\",\"address\":\"p\",\"telp\":\"09889898\",\"email\":\"danh878866@gmail.com\",\"is_active\":\"1\",\"notes\":\"\",\"created_at\":\"2025-12-15 22:12:30\",\"updated_at\":\"2025-12-15 22:12:30\",\"created_by\":\"3\",\"total_orders\":\"0\",\"total_quantity\":\"0\",\"completed_orders\":\"0\",\"active_orders\":\"0\",\"last_order_date\":null,\"created_by_username\":\"bod\"}', '{\"id_cust\":\"1003\",\"cust_name\":\"o\",\"email\":\"danh878866@gmail.com\",\"telp\":\"09889845453\",\"address\":\"p\",\"notes\":\"\",\"is_active\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-15 15:12:42');

-- --------------------------------------------------------

--
-- Table structure for table `capacity_config`
--

CREATE TABLE `capacity_config` (
  `id_config` int(11) NOT NULL,
  `level` tinyint(1) NOT NULL COMMENT 'Cấp độ công suất (1=Tiêu chuẩn, 2=Tối đa)',
  `level_name` varchar(50) NOT NULL COMMENT 'Tên mức công suất',
  `hours_per_shift` int(11) NOT NULL COMMENT 'Số giờ/ca',
  `shifts_per_day` int(11) NOT NULL DEFAULT 2 COMMENT 'Số ca/ngày',
  `efficiency_rate` decimal(3,2) DEFAULT 0.80 COMMENT 'Tỷ lệ hiệu suất (0.00-1.00)',
  `description` varchar(255) DEFAULT NULL COMMENT 'Mô tả chi tiết',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'Trạng thái sử dụng',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `products_per_shift` int(11) DEFAULT 35
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cấu hình công suất sản xuất 2 mức cố định';

--
-- Dumping data for table `capacity_config`
--

INSERT INTO `capacity_config` (`id_config`, `level`, `level_name`, `hours_per_shift`, `shifts_per_day`, `efficiency_rate`, `description`, `is_active`, `created_at`, `updated_at`, `products_per_shift`) VALUES
(1, 1, 'Công suất tiêu chuẩn', 8, 2, 0.80, '8 giờ/ca × 2 ca/ngày = 16 giờ/ngày (hiệu suất 80%)', 1, '2025-12-06 09:59:44', '2025-12-06 10:02:18', 35),
(2, 2, 'Công suất tối đa', 12, 2, 0.85, '12 giờ/ca × 2 ca/ngày = 24 giờ/ngày (hiệu suất 85%)', 1, '2025-12-06 09:59:44', '2025-12-14 06:46:44', 50);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id_cust` int(25) NOT NULL,
  `cust_name` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL,
  `telp` varchar(20) NOT NULL COMMENT 'Số điện thoại (chuỗi chữ số)',
  `email` varchar(25) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Hoạt động, 0=Ngừng hợp tác',
  `notes` text DEFAULT NULL COMMENT 'Ghi chú về khách hàng',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Thời gian tạo',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Thời gian cập nhật',
  `created_by` int(11) DEFAULT NULL COMMENT 'User ID người tạo (FK user.user_id)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id_cust`, `cust_name`, `address`, `telp`, `email`, `is_active`, `notes`, `created_at`, `updated_at`, `created_by`) VALUES
(1001, 'Tes Customer', 'Indonesia', '21293383', 'tes@mail.com', 1, 'vip', '2025-11-24 15:53:34', '2025-12-15 13:03:51', NULL),
(1002, 'danh', 'gòo', '2147483647', 'danh@gmail.com', 1, 'danh', '2025-11-28 08:43:33', '2025-12-15 13:20:36', 3),
(1003, 'o', 'p', '09889845453', 'danh878866@gmail.com', 1, '', '2025-12-15 15:12:30', '2025-12-15 15:12:42', 3);

-- --------------------------------------------------------

--
-- Table structure for table `finished_issue`
--

CREATE TABLE `finished_issue` (
  `id_issue` int(11) NOT NULL,
  `issue_code` varchar(50) NOT NULL COMMENT 'Mã phiếu xuất (tự động sinh: XTP-YYYYMMDDHHMMSS-###)',
  `id_project` int(11) NOT NULL COMMENT 'Liên kết tới đơn hàng/dự án',
  `quantity_requested` int(11) NOT NULL COMMENT 'Số lượng yêu cầu giao',
  `quantity_issued` int(11) NOT NULL COMMENT 'Số lượng thực tế xuất',
  `created_by` int(11) DEFAULT NULL COMMENT 'ID nhân viên kho tạo phiếu',
  `created_by_name` varchar(100) DEFAULT NULL COMMENT 'Tên nhân viên tạo phiếu',
  `created_date` datetime DEFAULT current_timestamp() COMMENT 'Ngày giờ tạo phiếu',
  `notes` text DEFAULT NULL COMMENT 'Ghi chú thêm',
  `status` enum('full','partial','cancelled') DEFAULT 'full' COMMENT 'full=giao đủ, partial=giao một phần, cancelled=hủy'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Phiếu xuất giao hàng thành phẩm cho khách';

-- --------------------------------------------------------

--
-- Table structure for table `finished_receipt`
--

CREATE TABLE `finished_receipt` (
  `id_receipt` int(11) NOT NULL,
  `receipt_code` varchar(50) NOT NULL COMMENT 'Mã phiếu nhập (tự động sinh: NTP-YYYYMMDDHHMMSS-###)',
  `id_project` int(11) NOT NULL COMMENT 'Liên kết tới đơn hàng/dự án',
  `id_finished_report` int(11) DEFAULT NULL COMMENT 'Liên kết tới báo cáo QC (nếu có)',
  `quantity_received` int(11) NOT NULL COMMENT 'Số lượng thành phẩm nhập kho',
  `quantity_planned` int(11) DEFAULT NULL COMMENT 'Số lượng theo kế hoạch',
  `created_by` int(11) DEFAULT NULL COMMENT 'ID nhân viên kho tạo phiếu',
  `created_by_name` varchar(100) DEFAULT NULL COMMENT 'Tên nhân viên tạo phiếu',
  `created_date` datetime DEFAULT current_timestamp() COMMENT 'Ngày giờ tạo phiếu',
  `notes` text DEFAULT NULL COMMENT 'Ghi chú thêm',
  `status` enum('posted','cancelled') DEFAULT 'posted' COMMENT 'Trạng thái phiếu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Phiếu nhập thành phẩm từ QC vào kho';

--
-- Dumping data for table `finished_receipt`
--

INSERT INTO `finished_receipt` (`id_receipt`, `receipt_code`, `id_project`, `id_finished_report`, `quantity_received`, `quantity_planned`, `created_by`, `created_by_name`, `created_date`, `notes`, `status`) VALUES
(1, 'NTP-20231107180000-001', 1001, 1001, 20, 20, 1, 'John Doe', '2023-11-07 18:00:00', 'Nhập từ QC đợt 1', 'posted'),
(2, 'NTP-20231108090000-002', 1001, 1001, 15, 20, 1, 'John Doe', '2023-11-08 09:00:00', 'Nhập từ QC đợt 2', 'posted');

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
-- Table structure for table `finished_stock`
--

CREATE TABLE `finished_stock` (
  `id_stock` int(11) NOT NULL,
  `id_product` int(11) NOT NULL COMMENT 'Loại sản phẩm',
  `diameter` decimal(3,1) NOT NULL,
  `quantity_in_stock` int(11) DEFAULT 0 COMMENT 'Số lượng tồn kho hiện tại',
  `quantity_received` int(11) DEFAULT 0 COMMENT 'Tổng số lượng đã nhập (lũy kế)',
  `quantity_issued` int(11) DEFAULT 0 COMMENT 'Tổng số lượng đã xuất (lũy kế)',
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Cập nhật lần cuối'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tồn kho thành phẩm (cập nhật tự động qua trigger)';

--
-- Dumping data for table `finished_stock`
--

INSERT INTO `finished_stock` (`id_stock`, `id_product`, `diameter`, `quantity_in_stock`, `quantity_received`, `quantity_issued`, `last_updated`) VALUES
(1, 1001, 0.5, 0, 35, 41000, '2025-12-15 03:13:20'),
(5, 1002, 0.0, 0, 10, 0, '2025-12-15 04:40:56'),
(6, 1003, 0.0, 0, 200, 0, '2025-12-15 19:36:56'),
(7, 1004, 0.0, 50, 50, 0, '2025-12-14 23:54:35');

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
  `stock` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Tồn kho hiện tại (hỗ trợ số thập phân)',
  `min_stock` int(11) DEFAULT 0 COMMENT 'Định mức tồn kho tối thiểu',
  `uom` varchar(10) NOT NULL DEFAULT 'g' COMMENT 'Đơn vị đo (g, kg, m, pcs...)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng nguyên liệu - stock: gram';

--
-- Dumping data for table `material`
--

INSERT INTO `material` (`id_material`, `material_name`, `stock`, `min_stock`, `uom`) VALUES
(1001, 'Test Matereal', 230.00, 1000, 'g'),
(1002, 'Nhựa ABS', 510.00, 1000, 'g'),
(1003, 'Mực gel xanh', 5175.00, 1000, 'g'),
(1004, 'Mực gel đen', 9985.00, 1000, 'g'),
(1005, 'Bi kim loại 0.5mm', 8173.50, 500, 'mm'),
(1006, 'Bi kim loại 0.7mm', 8000.00, 500, 'mm'),
(1007, 'Bi kim loại 1.0mm', 6000.00, 500, 'mm'),
(1008, 'Lò xo thép', 5000.00, 1000, 'g'),
(9998, 'NVL mới test', 100.00, 10, 'cái'),
(9999, 'NVL mới test', 100.00, 10, 'cái');

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
  `diameter` decimal(3,1) NOT NULL DEFAULT 0.5 COMMENT 'Đường kính bi viết (mm)',
  `bom` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Định mức nguyên vật liệu (JSON)' CHECK (json_valid(`bom`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Đang sản xuất, 0=Ngừng',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Thời gian tạo',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Thời gian cập nhật',
  `created_by` int(11) DEFAULT NULL COMMENT 'User ID người tạo (FK user.user_id)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id_product`, `product_name`, `summary`, `application`, `diameter`, `bom`, `is_active`, `created_at`, `updated_at`, `created_by`) VALUES
(1001, 'Bút bi TL-079', 'Bút bi mực gel, thân nhựa trong suốt, viết mượt', 'Xanh dương', 0.5, '[{\"id_material\":1001,\"material_name\":\"Test Material\",\"quantity_per_unit\":1,\"uom\":\"pcs\"},{\"id_material\":1002,\"material_name\":\"Nhu1ef1a ABS\",\"quantity_per_unit\":2,\"uom\":\"pcs\"}]', 1, '2025-11-24 15:53:58', '2025-12-14 17:23:59', NULL),
(1002, 'Bút bi TL-050', 'Bút bi dầu, thân nhựa màu, giá rẻ', 'Đen', 0.5, '[{\"id_material\":1002,\"material_name\":\"Nhu1ef1a ABS\",\"quantity_per_unit\":10,\"uom\":\"pcs\"},{\"id_material\":1004,\"material_name\":\"Giu1ea5y\",\"quantity_per_unit\":3,\"uom\":\"pcs\"},{\"id_material\":1005,\"material_name\":\"Kim lou1ea1i\",\"quantity_per_unit\":0.3,\"uom\":\"pcs\"}]', 1, '2025-11-24 15:53:58', '2025-12-14 17:23:59', NULL),
(1003, 'Bút bi TL-100', 'Bút bi cao cấp, thân kim loại', 'Đỏ', 0.5, '[{\"id_material\":1003,\"material_name\":\"M\\u1ef1c gel xanh\",\"quantity_per_unit\":1,\"uom\":\"pcs\"},{\"id_material\":1005,\"material_name\":\"Bi kim lo\\u1ea1i 0.5mm\",\"quantity_per_unit\":1,\"uom\":\"pcs\"}]', 1, '2025-11-24 15:53:58', '2025-12-14 04:54:52', NULL),
(1004, 'Bút bi TL-Multi', 'Bút bi 4 màu, đa năng', 'nhiều màu', 0.5, NULL, 1, '2025-11-24 15:53:58', '2025-12-14 17:23:59', NULL);

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
  `customer_request` text DEFAULT NULL COMMENT 'Yêu cầu đặc biệt của khách hàng',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Thời gian tạo đơn hàng',
  `warning_flag` tinyint(1) DEFAULT 0 COMMENT 'Có cảnh báo (0=Không, 1=Có)',
  `warning_type` varchar(50) DEFAULT NULL,
  `warning_details` text DEFAULT NULL COMMENT 'Chi tiết các cảnh báo (JSON format)',
  `stock_allocation` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Stores details of stock allocation, e.g., {"from_stock": 10, "for_production": 90}' CHECK (json_valid(`stock_allocation`)),
  `capacity_level_used` tinyint(1) DEFAULT 1 COMMENT 'Mức công suất (1=Tiêu chuẩn 8h, 2=Tối đa 12h)',
  `material_shifts_available` int(11) DEFAULT NULL COMMENT 'Số ca có thể làm với NVL hiện tại',
  `finished_stock_available` int(11) DEFAULT 0 COMMENT 'Số lượng thành phẩm có sẵn trong kho'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng dự án - qty_request: cái (số lượng bút), diameter: mm (đường kính bi)';

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`id_project`, `project_name`, `id_cust`, `id_product`, `diameter`, `qty_request`, `entry_date`, `pr_status`, `customer_request`, `created_at`, `warning_flag`, `warning_type`, `warning_details`, `stock_allocation`, `capacity_level_used`, `material_shifts_available`, `finished_stock_available`) VALUES
(1001, 'PJ-TEST', 1001, 1001, 0.5, 10, '2025-12-31', 1, '', '2025-11-01 18:11:56', 1, 'ok', '{\"finished_stock_info\":\"\\ud83c\\udfed C\\u1ea7n s\\u1ea3n xu\\u1ea5t 10 c\\u00e1i\",\"stock_status\":\"depleted\",\"material_details\":[{\"material_name\":\"Test Matereal\",\"stock\":\"210.00\",\"uom\":\"g\",\"quantity_per_unit\":1,\"quantity_needed\":10,\"products_possible\":210,\"shifts_possible\":1,\"is_bottleneck\":true,\"is_sufficient\":true},{\"material_name\":\"Nh\\u1ef1a ABS\",\"stock\":\"470.00\",\"uom\":\"g\",\"quantity_per_unit\":2,\"quantity_needed\":20,\"products_possible\":235,\"shifts_possible\":1,\"is_bottleneck\":false,\"is_sufficient\":true}],\"material_status\":\"\\u2705 NVL \\u0111\\u1ee7 cho 10 s\\u1ea3n ph\\u1ea9m (~1 ca)\",\"bottleneck_material\":\"Test Matereal\",\"capacity_info\":{\"level\":1,\"products_per_shift_level1\":3200,\"products_per_shift_level2\":5100}}', '{\"from_stock\":0,\"for_production\":10,\"total_allocated\":0,\"production_needed\":10}', 1, 1, 0),
(1765699484, 'ORD-1001-20251214-001', 1001, 1001, 0.5, 30, '2025-12-30', 1, '', '2025-12-14 19:57:24', 1, 'ok', '{\"finished_stock_info\":\"\\ud83c\\udfed C\\u1ea7n s\\u1ea3n xu\\u1ea5t 30 c\\u00e1i\",\"stock_status\":\"depleted\",\"material_details\":[{\"material_name\":\"Test Matereal\",\"stock\":\"210.00\",\"uom\":\"g\",\"quantity_per_unit\":1,\"quantity_needed\":30,\"products_possible\":210,\"shifts_possible\":1,\"is_bottleneck\":true,\"is_sufficient\":true},{\"material_name\":\"Nh\\u1ef1a ABS\",\"stock\":\"470.00\",\"uom\":\"g\",\"quantity_per_unit\":2,\"quantity_needed\":60,\"products_possible\":235,\"shifts_possible\":1,\"is_bottleneck\":false,\"is_sufficient\":true}],\"material_status\":\"\\u2705 NVL \\u0111\\u1ee7 cho 30 s\\u1ea3n ph\\u1ea9m (~1 ca)\",\"bottleneck_material\":\"Test Matereal\",\"capacity_info\":{\"level\":1,\"products_per_shift_level1\":3200,\"products_per_shift_level2\":5100}}', '{\"from_stock\":0,\"for_production\":30,\"total_allocated\":0,\"production_needed\":30}', 1, 1, 0),
(1765699485, 'ORD-1001-20251214-002', 1001, 1001, 0.5, 5, '2025-12-25', 1, '', '2025-12-14 20:08:55', 1, 'ok', '{\"finished_stock_info\":\"\\ud83c\\udfed C\\u1ea7n s\\u1ea3n xu\\u1ea5t 5 c\\u00e1i\",\"stock_status\":\"depleted\",\"material_details\":[{\"material_name\":\"Test Matereal\",\"stock\":\"210.00\",\"uom\":\"g\",\"quantity_per_unit\":1,\"quantity_needed\":5,\"products_possible\":210,\"shifts_possible\":1,\"is_bottleneck\":true,\"is_sufficient\":true},{\"material_name\":\"Nh\\u1ef1a ABS\",\"stock\":\"470.00\",\"uom\":\"g\",\"quantity_per_unit\":2,\"quantity_needed\":10,\"products_possible\":235,\"shifts_possible\":1,\"is_bottleneck\":false,\"is_sufficient\":true}],\"material_status\":\"\\u2705 NVL \\u0111\\u1ee7 cho 5 s\\u1ea3n ph\\u1ea9m (~1 ca)\",\"bottleneck_material\":\"Test Matereal\",\"capacity_info\":{\"level\":1,\"products_per_shift_level1\":3200,\"products_per_shift_level2\":5100}}', '{\"from_stock\":0,\"for_production\":5,\"total_allocated\":0,\"production_needed\":5}', 1, 1, 0),
(1765699487, 'ORD-1001-20251214-003', 1001, 1001, 0.5, 900, '2025-12-25', 1, '', '2025-12-14 20:13:20', 1, 'material_shortage', '{\"finished_stock_info\":\"\\ud83c\\udfed C\\u1ea7n s\\u1ea3n xu\\u1ea5t 900 c\\u00e1i\",\"stock_status\":\"depleted\",\"material_details\":[{\"material_name\":\"Test Matereal\",\"stock\":\"210.00\",\"uom\":\"g\",\"quantity_per_unit\":1,\"quantity_needed\":900,\"products_possible\":210,\"shifts_possible\":1,\"is_bottleneck\":true,\"is_sufficient\":false,\"quantity_shortage\":690},{\"material_name\":\"Nh\\u1ef1a ABS\",\"stock\":\"470.00\",\"uom\":\"g\",\"quantity_per_unit\":2,\"quantity_needed\":1800,\"products_possible\":235,\"shifts_possible\":1,\"is_bottleneck\":false,\"is_sufficient\":false,\"quantity_shortage\":1330}],\"material_warning\":\"\\u26a0\\ufe0f Thi\\u1ebfu 690 g Test Matereal, Thi\\u1ebfu 1330 g Nh\\u1ef1a ABS\",\"material_status\":\"\\u2705 NVL \\u0111\\u1ee7 cho 900 s\\u1ea3n ph\\u1ea9m (~1 ca)\",\"bottleneck_material\":\"Test Matereal\",\"capacity_info\":{\"level\":1,\"products_per_shift_level1\":3200,\"products_per_shift_level2\":5100}}', '{\"from_stock\":0,\"for_production\":900,\"total_allocated\":0,\"production_needed\":900}', 1, 1, 0),
(1765699492, 'ORD-1001-20251214-004', 1001, 1001, 0.5, 250, '2025-12-25', 1, '', '2025-12-14 20:25:53', 1, 'material_shortage', '{\"finished_stock_info\":\"\\ud83c\\udfed C\\u1ea7n s\\u1ea3n xu\\u1ea5t 250 c\\u00e1i\",\"stock_status\":\"depleted\",\"material_details\":[{\"material_name\":\"Test Matereal\",\"stock\":\"210.00\",\"uom\":\"g\",\"quantity_per_unit\":1,\"quantity_needed\":250,\"products_possible\":210,\"shifts_possible\":1,\"is_bottleneck\":true,\"is_sufficient\":false,\"quantity_shortage\":40},{\"material_name\":\"Nh\\u1ef1a ABS\",\"stock\":\"470.00\",\"uom\":\"g\",\"quantity_per_unit\":2,\"quantity_needed\":500,\"products_possible\":235,\"shifts_possible\":1,\"is_bottleneck\":false,\"is_sufficient\":false,\"quantity_shortage\":30}],\"material_warning\":\"\\u26a0\\ufe0f Thi\\u1ebfu 40 g Test Matereal, Thi\\u1ebfu 30 g Nh\\u1ef1a ABS\",\"material_status\":\"\\u2705 NVL \\u0111\\u1ee7 cho 250 s\\u1ea3n ph\\u1ea9m (~1 ca)\",\"bottleneck_material\":\"Test Matereal\",\"capacity_info\":{\"level\":1,\"products_per_shift_level1\":3200,\"products_per_shift_level2\":5100}}', '{\"from_stock\":0,\"for_production\":250,\"total_allocated\":0,\"production_needed\":250}', 1, 1, 0),
(1765699496, 'ORD-1001-20251214-005', 1001, 1001, 0.5, 10, '2025-12-14', 1, '', '2025-12-14 20:33:30', NULL, 'deadline_overdue', NULL, NULL, NULL, NULL, NULL),
(1765699519, 'ORD-1002-20251215-001', 1002, 1002, 0.5, 15, '2025-12-17', 1, '', '2025-12-14 21:40:45', 1, 'deadline_too_close', '{\"finished_stock_info\":\"\\ud83c\\udfed C\\u1ea7n s\\u1ea3n xu\\u1ea5t 15 c\\u00e1i\",\"stock_status\":\"depleted\",\"material_details\":[{\"material_name\":\"Nh\\u1ef1a ABS\",\"stock\":\"470.00\",\"uom\":\"g\",\"quantity_per_unit\":10,\"quantity_needed\":150,\"products_possible\":47,\"shifts_possible\":1,\"is_bottleneck\":true,\"is_sufficient\":true},{\"material_name\":\"M\\u1ef1c gel \\u0111en\",\"stock\":\"9985.00\",\"uom\":\"g\",\"quantity_per_unit\":3,\"quantity_needed\":45,\"products_possible\":3328,\"shifts_possible\":2,\"is_bottleneck\":false,\"is_sufficient\":true},{\"material_name\":\"Bi kim lo\\u1ea1i 0.5mm\",\"stock\":\"7998.50\",\"uom\":\"mm\",\"quantity_per_unit\":0.3,\"quantity_needed\":4.5,\"products_possible\":26661,\"shifts_possible\":9,\"is_bottleneck\":false,\"is_sufficient\":true}],\"material_status\":\"\\u2705 NVL \\u0111\\u1ee7 cho 15 s\\u1ea3n ph\\u1ea9m (~1 ca)\",\"bottleneck_material\":\"Nh\\u1ef1a ABS\",\"deadline_warning\":\"\\u23f0 C\\u00f2n 2 ng\\u00e0y \\u0111\\u1ebfn deadline, c\\u1ea7n \\u01b0u ti\\u00ean\",\"deadline_details\":\"C\\u00f2n 2 ng\\u00e0y, c\\u1ea7n 15 s\\u1ea3n ph\\u1ea9m\",\"capacity_info\":{\"level\":1,\"products_per_shift_level1\":3200,\"products_per_shift_level2\":5100}}', '{\"from_stock\":0,\"for_production\":15,\"total_allocated\":0,\"production_needed\":15}', 1, 1, 0),
(1765699520, 'ORD-1002-20251215-002', 1002, 1003, 0.5, 20, '2025-12-16', 1, '', '2025-12-15 12:35:21', 0, 'stock_available', '{\"finished_stock_info\":\"\\u2713 C\\u00f3 200 c\\u00e1i trong kho, \\u0111\\u00e3 ph\\u00e2n b\\u1ed5 0, c\\u00f2n 200 c\\u00e1i kh\\u1ea3 d\\u1ee5ng, d\\u00f9ng 20 cho \\u0111\\u01a1n n\\u00e0y\",\"stock_status\":\"sufficient\",\"capacity_info\":{\"level\":0,\"products_per_shift_level1\":3200,\"products_per_shift_level2\":5100}}', '{\"from_stock\":20,\"for_production\":0,\"total_allocated\":20,\"production_needed\":0}', 0, NULL, 180),
(1765699525, 'ORD-1003-20251215-001', 1003, 1003, 0.5, 5, '2025-12-25', 1, '', '2025-12-15 15:13:02', 1, 'ok', '{\"finished_stock_info\":\"\\ud83c\\udfed C\\u1ea7n s\\u1ea3n xu\\u1ea5t 5 c\\u00e1i\",\"stock_status\":\"depleted\",\"material_details\":[{\"material_name\":\"M\\u1ef1c gel xanh\",\"stock\":\"5180.00\",\"uom\":\"g\",\"quantity_per_unit\":1,\"quantity_needed\":5,\"products_possible\":5180,\"shifts_possible\":2,\"is_bottleneck\":true,\"is_sufficient\":true},{\"material_name\":\"Bi kim lo\\u1ea1i 0.5mm\",\"stock\":\"8178.50\",\"uom\":\"mm\",\"quantity_per_unit\":1,\"quantity_needed\":5,\"products_possible\":8178,\"shifts_possible\":3,\"is_bottleneck\":false,\"is_sufficient\":true}],\"material_status\":\"\\u2705 NVL \\u0111\\u1ee7 cho 5 s\\u1ea3n ph\\u1ea9m (~1 ca)\",\"bottleneck_material\":\"M\\u1ef1c gel xanh\",\"capacity_info\":{\"level\":1,\"products_per_shift_level1\":3200,\"products_per_shift_level2\":5100}}', '{\"from_stock\":0,\"for_production\":5,\"total_allocated\":0,\"production_needed\":5}', 1, 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `project_backup_20251207`
--

CREATE TABLE `project_backup_20251207` (
  `id_project` int(25) NOT NULL DEFAULT 0,
  `project_name` varchar(50) NOT NULL,
  `id_cust` int(25) NOT NULL,
  `id_product` int(25) NOT NULL,
  `diameter` decimal(3,1) NOT NULL COMMENT 'Đường kính bi viết (mm): 0.5, 0.7, 1.0',
  `qty_request` int(15) NOT NULL,
  `entry_date` date NOT NULL,
  `pr_status` int(5) NOT NULL,
  `risk_flag` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Cờ nguy cơ trễ hạn: 0=Bình thường, 1=Nguy cơ trễ',
  `customer_request` text DEFAULT NULL COMMENT 'Yêu cầu đặc biệt của khách hàng',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Thời gian tạo đơn hàng',
  `warning_flag` tinyint(1) DEFAULT 0 COMMENT 'Có cảnh báo (0=Không, 1=Có)',
  `warning_details` text DEFAULT NULL COMMENT 'Chi tiết các cảnh báo (JSON format)',
  `capacity_level_used` tinyint(1) DEFAULT 1 COMMENT 'Mức công suất (1=Tiêu chuẩn 8h, 2=Tối đa 12h)',
  `material_shifts_available` int(11) DEFAULT NULL COMMENT 'Số ca có thể làm với NVL hiện tại',
  `finished_stock_available` int(11) DEFAULT 0 COMMENT 'Số lượng thành phẩm có sẵn trong kho'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_backup_20251207`
--

INSERT INTO `project_backup_20251207` (`id_project`, `project_name`, `id_cust`, `id_product`, `diameter`, `qty_request`, `entry_date`, `pr_status`, `risk_flag`, `customer_request`, `created_at`, `warning_flag`, `warning_details`, `capacity_level_used`, `material_shifts_available`, `finished_stock_available`) VALUES
(1001, 'PJ-TEST', 1001, 1001, 0.7, 10, '2025-12-31', 1, 0, '', '2025-11-01 18:11:56', 1, '{\"finished_stock_info\":\"✓ Có 35 cái sẵn trong kho, có thể giao ngay\",\"stock_status\":\"sufficient\"}', 0, NULL, 35),
(1002, 'ORD-1001-20251128-001', 1001, 1003, 0.5, 3, '2025-12-31', 1, 0, 'đỏ', '2025-11-27 18:46:32', 1, '{\"deadline_status\":\"Bình thường\",\"material_status\":\"Ước tính đủ NVL cho ~20 ca\"}', 1, 20, 0),
(1003, 'ORD-1001-20251207-001', 1001, 1001, 0.5, 300000, '2025-12-10', 1, 0, '', '2025-12-06 20:24:34', 1, '{\"capacity_shortage\":\"Cần 299,965 cái, chỉ làm được tối đa 79,560 cái\"}', 1, NULL, 0),
(1004, 'ORD-1001-20251207-002', 1001, 1006, 0.7, 10000, '2025-12-31', 1, 0, '', '2025-12-07 10:43:44', 1, '{\"deadline_status\":\"Bình thường\",\"missing_materials_warning\":\"Sản phẩm có 3 NVL chưa tồn tại trong kho\",\"missing_materials_list\":\"Mực xanh H2-TEST, Vỏ nhựa H2-TEST, Ruột bút H2-TEST\",\"material_status\":\"Ước tính đủ NVL cho ~48 ca\"}', 1, 48, 0);

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
(1001, 7),
(1002, 1);

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
(365, 7, 22, '2025-11-01 15:54:52'),
(367, 1, 165, '2025-12-01 15:22:55'),
(368, 1, 157, '2025-12-01 15:22:55');

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
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `st_status` int(2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id_staff`, `staff_name`, `phone`, `email`, `department`, `position`, `st_status`, `created_at`, `updated_at`) VALUES
(1001, 'Leader1', '8212312', 'leader1@mail.com', 'Sản Xuất', 'Leader', 2, '2025-12-11 15:09:49', '2025-12-12 08:21:14'),
(1002, 'Leader2', '8923321', 'leader2@mail.com', 'Sản Xuất', 'Leader', 1, '2025-12-11 15:09:49', '2025-12-12 08:21:14'),
(1003, 'Administrator', '0', '', 'IT', 'Administrator', 1, '2025-11-01 15:49:53', '2025-12-12 08:21:14'),
(1004, 'Trưởng dây chuyền', '0', '', 'Sản Xuất', 'Trưởng Dây Chuyền', 1, '2025-11-01 15:49:53', '2025-12-12 08:21:14'),
(1005, 'Nguyễn Văn A - Giám Đốc', '0', 'bod@company.com', 'Ban Giám Đốc', 'Giám Đốc', 1, '2025-11-01 15:53:44', '2025-12-12 08:21:14'),
(1006, 'Trần Văn B - Trưởng line 2', '0', 'linemanager@company.com', 'Sản Xuất', 'Trưởng Dây Chuyền', 1, '2025-11-01 15:53:45', '2025-12-12 08:21:14'),
(1007, 'Lê Thị C - Nhân viên kho', '0', 'warehouse@company.com', 'Kho', 'Nhân Viên Kho', 1, '2025-11-01 15:53:45', '2025-12-12 08:21:14'),
(1008, 'Phạm Văn D - Nhân viên QC', '0', 'qc@company.com', 'QC', 'Nhân Viên QC', 1, '2025-11-01 15:53:45', '2025-12-12 08:21:14'),
(1009, 'Hoàng Văn E - Kỹ thuật viên', '0', 'technical@company.com', 'Kỹ Thuật', 'Kỹ Thuật Viên', 1, '2025-11-01 15:53:45', '2025-12-12 08:21:14'),
(1010, 'Nguyễn Thị F - Công nhân', '0', 'worker@company.com', 'Sản Xuất', 'Công Nhân', 1, '2025-11-01 15:53:45', '2025-12-12 08:21:14');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(11) NOT NULL,
  `password` varchar(11) NOT NULL,
  `temp_password` varchar(50) DEFAULT NULL COMMENT 'Mật khẩu tạm (plaintext) sau reset, NULL khi đã đổi',
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=Bắt buộc đổi password lần đầu, 0=Bình thường',
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

INSERT INTO `user` (`user_id`, `username`, `password`, `temp_password`, `must_change_password`, `role_id`, `staff_id`, `full_name`, `email`, `phone`, `is_active`, `last_login`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin', NULL, 0, 4, 1003, 'Administrator', NULL, NULL, 1, '2025-12-14 17:30:04', NULL, '2025-11-01 15:49:53', '2025-12-14 17:30:04'),
(2, 'leader', 'leader', NULL, 0, 1, 1004, 'Trưởng dây chuyền', NULL, NULL, 1, NULL, NULL, '2025-11-01 15:49:53', '2025-12-11 18:28:21'),
(3, 'bod', 'bod123', NULL, 0, 1, 1005, 'Nguyễn Văn A - Giám Đốc', 'bod@company.com', NULL, 1, '2025-12-14 04:07:17', NULL, '2025-11-01 15:53:44', '2025-12-14 04:07:17'),
(4, 'line_manage', 'line123', NULL, 0, 2, 1006, 'Trần Văn B - Trưởng line 2', 'linemanager@company.com', NULL, 1, NULL, NULL, '2025-11-01 15:53:45', '2025-12-11 15:22:31'),
(5, 'warehouse', 'wh123', NULL, 0, 3, 1007, 'Lê Thị C - Nhân viên kho', 'warehouse@company.com', NULL, 1, NULL, NULL, '2025-11-01 15:53:45', '2025-12-11 15:22:31'),
(6, 'qc', 'qc123', NULL, 0, 5, 1008, 'Phạm Văn D - Nhân viên QC', 'qc@company.com', NULL, 1, NULL, NULL, '2025-11-01 15:53:45', '2025-12-11 15:22:31'),
(7, 'technical', 'tech123', NULL, 0, 6, 1009, 'Hoàng Văn E - Kỹ thuật viên', 'technical@company.com', NULL, 1, NULL, NULL, '2025-11-01 15:53:45', '2025-12-11 15:22:31'),
(8, 'worker', 'worker123', NULL, 0, 7, 1010, 'Nguyễn Thị F - Công nhân', 'worker@company.com', NULL, 1, NULL, NULL, '2025-11-01 15:53:45', '2025-12-11 15:22:31');

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
-- Indexes for table `capacity_config`
--
ALTER TABLE `capacity_config`
  ADD PRIMARY KEY (`id_config`),
  ADD UNIQUE KEY `unique_level` (`level`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id_cust`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_customer_search` (`cust_name`,`email`,`telp`),
  ADD KEY `idx_customer_active` (`is_active`,`id_cust`);

--
-- Indexes for table `finished_issue`
--
ALTER TABLE `finished_issue`
  ADD PRIMARY KEY (`id_issue`),
  ADD UNIQUE KEY `issue_code` (`issue_code`),
  ADD KEY `idx_project` (`id_project`),
  ADD KEY `idx_created_date` (`created_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `finished_receipt`
--
ALTER TABLE `finished_receipt`
  ADD PRIMARY KEY (`id_receipt`),
  ADD UNIQUE KEY `receipt_code` (`receipt_code`),
  ADD KEY `idx_project` (`id_project`),
  ADD KEY `idx_finished_report` (`id_finished_report`),
  ADD KEY `idx_created_date` (`created_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `finished_report`
--
ALTER TABLE `finished_report`
  ADD PRIMARY KEY (`id_finished`),
  ADD KEY `fk_finished_project` (`id_project`);

--
-- Indexes for table `finished_stock`
--
ALTER TABLE `finished_stock`
  ADD PRIMARY KEY (`id_stock`),
  ADD UNIQUE KEY `unique_stock_product` (`id_product`),
  ADD UNIQUE KEY `unique_stock_product_diameter` (`id_product`,`diameter`),
  ADD KEY `idx_last_updated` (`last_updated`);

--
-- Indexes for table `machine`
--
ALTER TABLE `machine`
  ADD PRIMARY KEY (`id_machine`);

--
-- Indexes for table `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`id_material`),
  ADD KEY `idx_material_stock` (`stock`,`material_name`);

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
  ADD PRIMARY KEY (`id_product`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_diameter` (`diameter`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_product_search` (`product_name`,`application`),
  ADD KEY `idx_product_active` (`is_active`,`id_product`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id_project`),
  ADD KEY `fk_project_product` (`id_product`),
  ADD KEY `idx_created_cust` (`id_cust`,`created_at`),
  ADD KEY `idx_risk_status` (`pr_status`),
  ADD KEY `idx_project_customer_status` (`id_cust`,`pr_status`,`created_at`),
  ADD KEY `idx_project_product_status` (`id_product`,`pr_status`,`created_at`),
  ADD KEY `idx_project_status` (`pr_status`,`created_at`),
  ADD KEY `idx_project_stats` (`id_cust`,`id_product`,`pr_status`,`qty_request`),
  ADD KEY `idx_warning_flag` (`warning_flag`),
  ADD KEY `idx_capacity_level` (`capacity_level_used`);

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
  ADD KEY `fk_user_role` (`role_id`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_role_active` (`role_id`,`is_active`),
  ADD KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `log_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=354;

--
-- AUTO_INCREMENT for table `capacity_config`
--
ALTER TABLE `capacity_config`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id_cust` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1026;

--
-- AUTO_INCREMENT for table `finished_issue`
--
ALTER TABLE `finished_issue`
  MODIFY `id_issue` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `finished_receipt`
--
ALTER TABLE `finished_receipt`
  MODIFY `id_receipt` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `finished_stock`
--
ALTER TABLE `finished_stock`
  MODIFY `id_stock` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `machine`
--
ALTER TABLE `machine`
  MODIFY `id_machine` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1003;

--
-- AUTO_INCREMENT for table `material`
--
ALTER TABLE `material`
  MODIFY `id_material` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000;

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
  MODIFY `id_product` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1765700412;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `id_project` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1765699526;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=372;

--
-- AUTO_INCREMENT for table `shiftment`
--
ALTER TABLE `shiftment`
  MODIFY `id_shift` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1004;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id_staff` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1034;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `finished_issue`
--
ALTER TABLE `finished_issue`
  ADD CONSTRAINT `fk_issue_project` FOREIGN KEY (`id_project`) REFERENCES `project` (`id_project`) ON UPDATE CASCADE;

--
-- Constraints for table `finished_receipt`
--
ALTER TABLE `finished_receipt`
  ADD CONSTRAINT `fk_receipt_finished_report` FOREIGN KEY (`id_finished_report`) REFERENCES `finished_report` (`id_finished`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_receipt_project` FOREIGN KEY (`id_project`) REFERENCES `project` (`id_project`) ON UPDATE CASCADE;

--
-- Constraints for table `finished_report`
--
ALTER TABLE `finished_report`
  ADD CONSTRAINT `fk_finished_project` FOREIGN KEY (`id_project`) REFERENCES `project` (`id_project`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `finished_stock`
--
ALTER TABLE `finished_stock`
  ADD CONSTRAINT `fk_stock_product` FOREIGN KEY (`id_product`) REFERENCES `product` (`id_product`) ON DELETE CASCADE ON UPDATE CASCADE;

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
