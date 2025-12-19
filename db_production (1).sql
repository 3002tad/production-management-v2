-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2025 at 03:14 AM
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
CREATE DATABASE IF NOT EXISTS `db_production` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_production`;

-- --------------------------------------------------------

--
-- Table structure for table `adjustment_requests`
--

DROP TABLE IF EXISTS `adjustment_requests`;
CREATE TABLE `adjustment_requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL COMMENT 'Format: AR-YYYYMMDD-NNNN',
  `closure_id` int(10) UNSIGNED NOT NULL COMMENT 'FK to shift_closures.id',
  `created_by` varchar(50) NOT NULL COMMENT 'QC inspector who rejected',
  `assigned_to` varchar(50) DEFAULT NULL COMMENT 'Leader/manager assigned to fix',
  `reason` text NOT NULL COMMENT 'Reason for rejection',
  `status` enum('OPEN','ACKED','DONE') NOT NULL DEFAULT 'OPEN',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `acknowledged_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Adjustment requests generated from QC rejections';

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

DROP TABLE IF EXISTS `audit_log`;
CREATE TABLE `audit_log` (
  `log_id` bigint(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `module` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`log_id`, `user_id`, `username`, `action`, `module`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 12:41:30'),
(2, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 12:48:29'),
(3, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 12:48:55'),
(4, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 12:51:23'),
(5, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 12:51:53'),
(6, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 12:54:00'),
(7, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 12:54:14'),
(8, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 12:58:30'),
(9, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 13:00:41'),
(10, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 13:04:47'),
(11, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 13:10:31'),
(12, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 13:12:34'),
(13, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 13:14:16'),
(14, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 13:19:03'),
(15, 3, 'bod', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 13:20:04'),
(16, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 13:24:23'),
(17, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 13:24:39'),
(18, 3, 'bod', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 13:25:00'),
(19, 5, 'qc', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 14:28:09'),
(20, 5, 'qc', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 14:34:53'),
(21, 5, 'qc', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 14:38:40'),
(22, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 14:38:46'),
(23, 5, 'qc', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 14:49:16'),
(24, 5, 'qc', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 14:50:18'),
(25, 5, 'qc', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 14:52:06'),
(26, 5, 'qc', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 15:03:51'),
(27, 5, 'qc', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 15:07:22'),
(28, 5, 'qc', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 15:08:16'),
(29, 5, 'qc', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 15:08:21'),
(30, 5, 'qc', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 15:49:14'),
(31, 5, 'qc', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 15:49:19'),
(32, 5, 'qc', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 16:00:19'),
(33, 5, 'qc', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-02 16:00:23'),
(34, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-03 02:17:56'),
(35, 3, 'bod', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-03 02:18:21'),
(36, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-03 02:26:46'),
(37, 3, 'bod', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-03 02:32:54'),
(38, 5, 'qc', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-03 02:34:57'),
(39, 5, 'qc', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-03 02:35:33'),
(40, 5, 'qc', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-03 02:35:54'),
(41, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-12-05 19:13:02'),
(42, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-12-05 19:14:49'),
(43, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-12-05 19:20:16'),
(44, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-12-05 19:28:18'),
(45, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-12-05 19:30:41'),
(46, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-07 14:08:12'),
(47, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-13 18:56:40'),
(48, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-13 19:17:31'),
(49, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-13 19:37:13'),
(50, 2, 'leader', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-13 19:47:55'),
(51, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-13 19:51:04'),
(52, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-13 19:54:37'),
(53, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 13:04:38'),
(54, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 13:05:00'),
(55, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 13:18:23'),
(56, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 13:19:05'),
(57, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 14:09:21'),
(58, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 14:14:32'),
(59, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-15 20:10:18'),
(60, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-16 10:30:22'),
(61, 2, 'leader', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-16 10:30:39'),
(62, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-16 10:30:46'),
(63, 2, 'leader', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-16 10:35:49'),
(64, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-16 10:35:55'),
(65, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-16 10:53:13'),
(66, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-16 10:53:47'),
(67, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-16 15:05:46'),
(68, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-16 15:06:26'),
(69, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 07:58:11'),
(70, 8, 'worker', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 08:37:03'),
(71, 8, 'worker', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 08:38:12'),
(72, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 08:38:18'),
(73, 8, 'worker', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 08:39:03'),
(74, 8, 'worker', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 09:19:43'),
(75, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 09:19:50'),
(76, 8, 'worker', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 09:23:43'),
(77, 8, 'worker', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 09:44:42'),
(78, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 09:45:41'),
(79, 8, 'worker', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 09:46:22'),
(80, 8, 'worker', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 09:47:35'),
(81, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 09:47:42'),
(82, 8, 'worker', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 09:48:48'),
(83, 8, 'worker', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 09:54:13'),
(84, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 09:54:20'),
(85, 8, 'worker', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 09:55:07'),
(86, 8, 'worker', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 10:26:29'),
(87, 8, 'worker', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 10:26:37'),
(88, 8, 'worker', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 10:37:30'),
(89, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 10:41:36'),
(90, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 12:01:18'),
(91, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 12:47:46'),
(92, 6, 'qc', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 14:31:32'),
(93, 6, 'qc', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 14:32:07'),
(94, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 14:32:14'),
(95, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 15:15:30'),
(96, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 15:17:15'),
(97, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 15:20:51'),
(98, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 15:21:27'),
(99, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 15:24:12'),
(100, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 15:28:22'),
(101, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 15:32:10'),
(102, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 15:35:10'),
(103, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 15:39:12'),
(104, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 15:43:16'),
(105, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 15:46:20'),
(106, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 15:49:05'),
(107, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 17:48:04'),
(108, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 17:49:31'),
(109, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 18:34:47'),
(110, 5, 'warehouse', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 18:50:01'),
(111, 5, 'warehouse', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 18:51:09'),
(112, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 19:05:24'),
(113, 2, 'leader', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 19:07:12'),
(114, 3, 'bod', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 19:15:46'),
(115, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 19:16:44'),
(116, 6, 'qc', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 19:24:37'),
(117, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 19:26:16'),
(118, 6, 'qc', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 19:27:52'),
(119, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 19:28:51'),
(120, 6, 'qc', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 19:49:16'),
(121, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 19:49:33'),
(122, 5, 'warehouse', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 19:54:23'),
(123, 5, 'warehouse', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 19:57:41'),
(124, 6, 'qc', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 19:57:46'),
(125, 6, 'qc', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-18 20:02:54'),
(126, 5, 'warehouse', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-19 01:47:58'),
(127, 5, 'warehouse', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-19 01:50:01'),
(128, 1, 'admin', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-19 01:50:08'),
(129, 6, 'qc', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-19 01:53:19'),
(130, 6, 'qc', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-19 01:55:50'),
(131, 8, 'Le Van A', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-19 01:57:22'),
(132, 6, 'qc', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-19 02:05:36'),
(133, 6, 'qc', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-19 02:05:47'),
(134, 5, 'warehouse', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-19 02:05:55'),
(135, 5, 'warehouse', 'logout', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-19 02:12:39'),
(136, 8, 'Le Van A', 'login', 'auth', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-19 02:12:59');

-- --------------------------------------------------------

--
-- Table structure for table `capacity_config`
--

DROP TABLE IF EXISTS `capacity_config`;
CREATE TABLE `capacity_config` (
  `id_config` int(11) NOT NULL,
  `level` tinyint(4) NOT NULL,
  `level_name` varchar(50) NOT NULL,
  `hours_per_shift` int(11) NOT NULL,
  `shifts_per_day` int(11) NOT NULL DEFAULT 2,
  `efficiency_rate` decimal(3,2) DEFAULT 0.80,
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `products_per_shift` int(11) DEFAULT 35
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `capacity_config`
--

INSERT INTO `capacity_config` (`id_config`, `level`, `level_name`, `hours_per_shift`, `shifts_per_day`, `efficiency_rate`, `description`, `is_active`, `created_at`, `updated_at`, `products_per_shift`) VALUES
(1, 1, 'Công suất tiêu chuẩn', 8, 2, 0.80, 'Chuẩn', 1, '2025-12-05 17:00:00', '2025-12-05 17:00:00', 35),
(2, 2, 'Công suất tối đa', 12, 2, 0.85, 'Tối đa', 1, '2025-12-05 17:00:00', '2025-12-13 17:00:00', 50);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
CREATE TABLE `customer` (
  `id_cust` int(25) NOT NULL,
  `cust_name` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL,
  `telp` varchar(20) NOT NULL COMMENT 'Số điện thoại',
  `email` varchar(25) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Hoạt động, 0=Ngừng',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id_cust`, `cust_name`, `address`, `telp`, `email`, `is_active`, `notes`, `created_at`, `updated_at`, `created_by`) VALUES
(1001, 'Tes Customer', 'Indonesia', '21293383', 'tes@mail.com', 1, NULL, '2025-12-18 18:51:41', '2025-12-18 18:51:41', NULL),
(1002, 'danh', 'bb', '0968799898', 'danh@gmail.com', 1, 'vip', '2025-11-28 01:43:33', '2025-12-18 07:46:46', 3);

-- --------------------------------------------------------

--
-- Table structure for table `defect_reasons`
--

DROP TABLE IF EXISTS `defect_reasons`;
CREATE TABLE `defect_reasons` (
  `reason_id` int(11) NOT NULL,
  `reason_code` varchar(50) NOT NULL,
  `reason_name` varchar(255) NOT NULL,
  `category` enum('material','machine','operator','process','other') NOT NULL DEFAULT 'other',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `defect_reasons`
--

INSERT INTO `defect_reasons` (`reason_id`, `reason_code`, `reason_name`, `category`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'DR001', 'Lỗi nguyên liệu đầu vào', 'material', 'Nguyên liệu không đạt chất lượng', 1, '2025-12-18 17:37:01', '2025-12-18 17:37:01'),
(2, 'DR002', 'Lỗi máy móc', 'machine', 'Máy móc hỏng hóc', 1, '2025-12-18 17:37:01', '2025-12-18 17:37:01'),
(3, 'DR003', 'Lỗi vận hành', 'operator', 'Nhân viên thao tác sai', 1, '2025-12-18 17:37:01', '2025-12-18 17:37:01'),
(4, 'DR004', 'Lỗi quy trình', 'process', 'Quy trình chưa tối ưu', 1, '2025-12-18 17:37:01', '2025-12-18 17:37:01'),
(5, 'DR005', 'Lỗi khác', 'other', 'Các lỗi khác', 1, '2025-12-18 17:37:01', '2025-12-18 17:37:01');

-- --------------------------------------------------------

--
-- Table structure for table `downtime_reasons`
--

DROP TABLE IF EXISTS `downtime_reasons`;
CREATE TABLE `downtime_reasons` (
  `id` int(11) NOT NULL,
  `reason_code` varchar(20) NOT NULL COMMENT 'Mã lý do',
  `reason_name` varchar(100) NOT NULL COMMENT 'Tên lý do',
  `category` enum('mechanical','material','quality','operator','other') NOT NULL DEFAULT 'other' COMMENT 'Phân loại',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng danh mục lý do downtime';

--
-- Dumping data for table `downtime_reasons`
--

INSERT INTO `downtime_reasons` (`id`, `reason_code`, `reason_name`, `category`, `is_active`, `created_at`) VALUES
(1, 'MAINT', 'Bảo trì định kỳ', 'mechanical', 1, '2025-12-18 14:53:43'),
(2, 'BREAKDOWN', 'Hỏng hóc máy móc', 'mechanical', 1, '2025-12-18 14:53:43'),
(3, 'MATERIAL_OUT', 'Hết nguyên liệu', 'material', 1, '2025-12-18 14:53:43'),
(4, 'MATERIAL_DEFECT', 'Nguyên liệu lỗi', 'material', 1, '2025-12-18 14:53:43'),
(5, 'QUALITY_ISSUE', 'Vấn đề chất lượng', 'quality', 1, '2025-12-18 14:53:43'),
(6, 'SETUP', 'Thiết lập/chuyển đổi sản phẩm', 'operator', 1, '2025-12-18 14:53:43'),
(7, 'BREAK', 'Giờ nghỉ', 'operator', 1, '2025-12-18 14:53:43'),
(8, 'NO_STAFF', 'Thiếu nhân lực', 'operator', 1, '2025-12-18 14:53:43'),
(9, 'POWER_OUTAGE', 'Mất điện', 'other', 1, '2025-12-18 14:53:43'),
(10, 'OTHER', 'Lý do khác', 'other', 1, '2025-12-18 14:53:43');

-- --------------------------------------------------------

--
-- Table structure for table `finished_issue`
--

DROP TABLE IF EXISTS `finished_issue`;
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
-- Table structure for table `finished_receipt`
--

DROP TABLE IF EXISTS `finished_receipt`;
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
  `status` enum('posted','cancelled') DEFAULT 'posted' COMMENT 'Trạng thái phiếu',
  `qc_verified` tinyint(1) DEFAULT 0 COMMENT '1=QC duyệt, 0=chưa hoặc từ chối',
  `qc_approved_at` datetime DEFAULT NULL COMMENT 'Thời gian QC duyệt',
  `qc_approved_by` varchar(50) DEFAULT NULL COMMENT 'User code QC duyệt',
  `requires_qc_approval` tinyint(1) DEFAULT 1 COMMENT 'Bắt buộc QC duyệt trước khi nhập'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Phiếu nhập thành phẩm';

--
-- Dumping data for table `finished_receipt`
--

INSERT INTO `finished_receipt` (`id_receipt`, `receipt_code`, `id_project`, `id_finished_report`, `quantity_received`, `quantity_planned`, `created_by`, `created_by_name`, `created_date`, `notes`, `status`, `qc_verified`, `qc_approved_at`, `qc_approved_by`, `requires_qc_approval`) VALUES
(1, 'NTP-20231107180000-001', 1001, 1001, 20, 20, 1, 'John Doe', '2023-11-07 18:00:00', 'Nhập từ QC đợt 1', 'posted', 0, NULL, NULL, 1),
(2, 'NTP-20231108090000-002', 1001, 1001, 15, 20, 1, 'John Doe', '2023-11-08 09:00:00', 'Nhập từ QC đợt 2', 'posted', 0, NULL, NULL, 1),
(7, 'NTP-20251214180952-657', 1001, 1, 5000, 5000, 5, 'warehouse', '2025-12-14 18:09:52', '', 'posted', 0, NULL, NULL, 1);

--
-- Triggers `finished_receipt`
--
DROP TRIGGER IF EXISTS `before_finished_receipt_insert`;
DELIMITER $$
CREATE TRIGGER `before_finished_receipt_insert` BEFORE INSERT ON `finished_receipt` FOR EACH ROW BEGIN
  DECLARE v_can_receive_fg TINYINT(1);
  DECLARE v_status VARCHAR(20);
  
  -- If requires_qc_approval is set to true, check QC approval
  IF NEW.requires_qc_approval = 1 AND NEW.id_finished_report IS NOT NULL THEN
    -- Check if shift_closure is QC approved
    SELECT can_receive_fg, status INTO v_can_receive_fg, v_status
    FROM shift_closures
    WHERE id = NEW.id_finished_report
    LIMIT 1;
    
    -- If not approved, raise error
    IF v_can_receive_fg IS NULL OR v_can_receive_fg = 0 THEN
      SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'ERRO_QC_NOT_APPROVED: Shift closure chưa được QC duyệt. Không thể nhập kho!';
    END IF;
    
    IF v_status != 'VERIFIED' THEN
      SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'ERROR_CLOSURE_NOT_VERIFIED: Trạng thái shift closure không hợp lệ';
    END IF;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `finished_report`
--

DROP TABLE IF EXISTS `finished_report`;
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
(1, 1001, 5000, '2025-12-18 18:11:20'),
(2, 1001, 3000, '2025-12-18 18:11:20'),
(1001, 1001, 2000, '2025-11-02 11:39:09');

-- --------------------------------------------------------

--
-- Table structure for table `finished_stock`
--

DROP TABLE IF EXISTS `finished_stock`;
CREATE TABLE `finished_stock` (
  `id_stock` int(11) NOT NULL,
  `id_product` int(11) DEFAULT NULL,
  `quantity_in_stock` int(11) DEFAULT 0,
  `quantity_received` int(11) DEFAULT 0,
  `quantity_issued` int(11) DEFAULT 0,
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `finished_stock`
--

INSERT INTO `finished_stock` (`id_stock`, `id_product`, `quantity_in_stock`, `quantity_received`, `quantity_issued`, `last_updated`) VALUES
(1, 1001, 0, 35, 41000, '2025-12-16 21:33:33'),
(5, 1002, 0, 10, 0, '2025-12-15 04:40:56'),
(6, 1003, 2, 200, 0, '2025-12-18 20:19:30'),
(7, 1004, 0, 50, 0, '2025-12-16 21:36:31');

-- --------------------------------------------------------

--
-- Table structure for table `incident_coordination`
--

DROP TABLE IF EXISTS `incident_coordination`;
CREATE TABLE `incident_coordination` (
  `id` int(10) UNSIGNED NOT NULL,
  `incident_id` int(10) UNSIGNED NOT NULL,
  `leader_id` int(10) UNSIGNED DEFAULT NULL,
  `action_type` varchar(50) NOT NULL,
  `assignee_id` int(10) UNSIGNED DEFAULT NULL,
  `machine_id` varchar(50) DEFAULT NULL,
  `shift_info` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `incident_reports`
--

DROP TABLE IF EXISTS `incident_reports`;
CREATE TABLE `incident_reports` (
  `id` int(11) NOT NULL COMMENT 'ID báo cáo sự cố',
  `user_id` int(11) NOT NULL COMMENT 'Người báo cáo (worker/technical)',
  `id_machine` int(11) DEFAULT NULL COMMENT 'Máy móc cụ thể (optional - NULL nếu sự cố cả dây chuyền)',
  `line_id` int(11) DEFAULT NULL COMMENT 'Dây chuyền liên quan (optional)',
  `shift_id` int(11) DEFAULT NULL COMMENT 'Ca làm việc khi xảy ra sự cố (optional)',
  `id_planshift` int(11) DEFAULT NULL COMMENT 'DEPRECATED - Use line_id instead',
  `category` varchar(50) DEFAULT NULL COMMENT 'Loại sự cố: equipment, quality, safety, other',
  `severity_level` tinyint(4) DEFAULT 1 COMMENT '1:Low, 2:Medium, 3:High, 4:Critical',
  `incident_description` text NOT NULL COMMENT 'Mô tả chi tiết sự cố',
  `media_path` varchar(255) DEFAULT NULL COMMENT 'Ảnh/video chứng minh',
  `status` tinyint(4) DEFAULT 0 COMMENT '0:Pending, 1:Completed, 2:In Progress',
  `assignee_id` int(11) DEFAULT NULL COMMENT 'Người được giao xử lý sự cố',
  `resolution_notes` text DEFAULT NULL COMMENT 'Ghi chú về cách xử lý',
  `resolved_at` datetime DEFAULT NULL COMMENT 'Thời gian hoàn thành',
  `created_at` datetime DEFAULT current_timestamp() COMMENT 'Thời gian tạo báo cáo',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Thời gian cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Báo cáo sự cố sản xuất - Categories: equipment (thiết bị), quality (chất lượng), safety (an toàn), other (khác)';

--
-- Dumping data for table `incident_reports`
--

INSERT INTO `incident_reports` (`id`, `user_id`, `id_machine`, `line_id`, `shift_id`, `id_planshift`, `category`, `severity_level`, `incident_description`, `media_path`, `status`, `assignee_id`, `resolution_notes`, `resolved_at`, `created_at`, `updated_at`) VALUES
(5, 8, 3, 1, NULL, NULL, 'equipment', 1, 'Thiết bị rò rỉ điện', NULL, 0, NULL, NULL, NULL, '2025-12-17 17:27:22', '2025-12-17 17:27:22');

-- --------------------------------------------------------

--
-- Table structure for table `machine`
--

DROP TABLE IF EXISTS `machine`;
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
DROP TRIGGER IF EXISTS `validate_machine_capacity`;
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
-- Table structure for table `machines`
--

DROP TABLE IF EXISTS `machines`;
CREATE TABLE `machines` (
  `id` int(11) NOT NULL,
  `line_id` int(11) DEFAULT NULL,
  `code` varchar(20) NOT NULL COMMENT 'Mã máy/dây chuyền (unique)',
  `name` varchar(100) NOT NULL COMMENT 'Tên máy/dây chuyền',
  `capacity` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Công suất (pieces/hour)',
  `stage_type` enum('molding','assembly','packaging','quality_check','other') NOT NULL DEFAULT 'other' COMMENT 'Loại công đoạn',
  `machine_type` varchar(50) DEFAULT 'production' COMMENT 'production/quality_control/maintenance',
  `equipment_category` varchar(50) DEFAULT 'production' COMMENT 'production, quality_control, maintenance',
  `status` enum('active','maintenance','inactive','broken') NOT NULL DEFAULT 'active' COMMENT 'Trạng thái máy',
  `machine_role` enum('primary','backup') NOT NULL DEFAULT 'primary' COMMENT 'primary=Máy chính, backup=Máy dự phòng',
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
-- Dumping data for table `machines`
--

INSERT INTO `machines` (`id`, `line_id`, `code`, `name`, `capacity`, `stage_type`, `machine_type`, `equipment_category`, `status`, `machine_role`, `description`, `location`, `purchase_date`, `warranty_until`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 1, 'ML001', 'Máy ép nhựa số 1', 1000.00, 'molding', 'production', 'production', 'active', 'primary', 'Máy ép nhựa chính cho sản xuất vỏ bút', 'Khu A - Line 1', NULL, NULL, '2025-12-05 19:12:29', '2025-12-16 14:05:42', 'system', NULL),
(2, 1, 'ML002', 'Máy ép nhựa số 2', 1200.00, 'molding', 'production', 'production', 'active', 'primary', 'Máy ép nhựa dự phòng', 'Khu A - Line 2', NULL, NULL, '2025-12-05 19:12:29', '2025-12-16 14:05:42', 'system', NULL),
(3, 1, 'AS001', 'Dây chuyền lắp ráp 1', 800.00, 'assembly', 'production', 'production', 'active', 'primary', 'Dây chuyền lắp ráp chính', 'Khu B - Line 1', NULL, NULL, '2025-12-05 19:12:29', '2025-12-16 14:05:42', 'system', NULL),
(4, 2, 'AS002', 'Dây chuyền lắp ráp 2', 750.00, 'assembly', 'production', 'production', 'maintenance', 'primary', 'Dây chuyền lắp ráp phụ', 'Khu B - Line 2', NULL, NULL, '2025-12-05 19:12:29', '2025-12-16 14:05:42', 'system', NULL),
(5, 2, 'PK001', 'Máy đóng gói tự động', 2000.00, 'packaging', 'production', 'production', 'active', 'primary', 'Máy đóng gói và dán nhãn tự động', 'Khu C - Line 1', NULL, NULL, '2025-12-05 19:12:29', '2025-12-16 14:05:42', 'system', NULL),
(6, 2, 'QC001', 'Máy kiểm tra chất lượng', 500.00, 'quality_check', 'quality_control', 'quality_control', 'active', 'primary', 'Máy kiểm tra tự động', 'Khu D - QC', NULL, NULL, '2025-12-05 19:12:29', '2025-12-18 12:59:54', 'system', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `machine_breakdown_logs`
--

DROP TABLE IF EXISTS `machine_breakdown_logs`;
CREATE TABLE `machine_breakdown_logs` (
  `log_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `old_machine_id` int(11) NOT NULL COMMENT 'Máy cũ bị breakdown',
  `new_machine_id` int(11) NOT NULL COMMENT 'Máy mới thay thế',
  `breakdown_time` datetime NOT NULL COMMENT 'Thời điểm breakdown',
  `reason` text NOT NULL COMMENT 'Lý do thay máy',
  `downtime_minutes` int(11) DEFAULT 0 COMMENT 'Thời gian chết (phút)',
  `handled_by` int(11) DEFAULT NULL COMMENT 'User ID người xử lý',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Nhật ký thay đổi máy do breakdown';

-- --------------------------------------------------------

--
-- Table structure for table `machine_maintenances`
--

DROP TABLE IF EXISTS `machine_maintenances`;
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
-- Dumping data for table `machine_maintenances`
--

INSERT INTO `machine_maintenances` (`id`, `machine_id`, `title`, `description`, `start_time`, `end_time`, `maintenance_type`, `status`, `estimated_cost`, `actual_cost`, `technician_name`, `created_by_user_id`, `created_by_username`, `created_at`, `updated_at`, `completed_at`, `notes`) VALUES
(1, 4, 'Bảo trì định kỳ hàng tháng', 'Kiểm tra và bảo trì tổng thể dây chuyền lắp ráp 2', '2025-12-01 08:00:00', '2025-12-01 18:00:00', 'preventive', 'planned', 0.00, 0.00, NULL, NULL, 'system', '2025-12-05 19:12:29', '2025-12-05 19:12:29', NULL, NULL),
(2, 2, 'Nâng cấp phần mềm điều khiển', 'Cập nhật firmware và software điều khiển', '2025-12-05 20:00:00', '2025-12-06 06:00:00', 'upgrade', 'planned', 0.00, 0.00, NULL, NULL, 'system', '2025-12-05 19:12:29', '2025-12-05 19:12:29', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `machine_status_logs`
--

DROP TABLE IF EXISTS `machine_status_logs`;
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
-- Dumping data for table `machine_status_logs`
--

INSERT INTO `machine_status_logs` (`id`, `machine_id`, `old_status`, `new_status`, `reason`, `changed_by_user_id`, `changed_by_username`, `changed_at`, `notes`) VALUES
(1, 4, 'active', 'maintenance', 'Bảo trì định kỳ theo lịch', NULL, 'system', '2025-12-05 19:12:29', NULL),
(2, 1, NULL, 'active', 'Khởi tạo máy mới', NULL, 'system', '2025-12-05 19:12:29', NULL),
(3, 2, NULL, 'active', 'Khởi tạo máy mới', NULL, 'system', '2025-12-05 19:12:29', NULL),
(4, 3, NULL, 'active', 'Khởi tạo máy mới', NULL, 'system', '2025-12-05 19:12:29', NULL),
(5, 5, NULL, 'active', 'Khởi tạo máy mới', NULL, 'system', '2025-12-05 19:12:29', NULL),
(6, 6, NULL, 'active', 'Khởi tạo máy mới', NULL, 'system', '2025-12-05 19:12:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `material`
--

DROP TABLE IF EXISTS `material`;
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
-- Dumping data for table `material`
--

INSERT INTO `material` (`id_material`, `material_name`, `stock`, `min_stock`, `uom`, `material_type`, `supplier`, `date_entry`, `attachment`, `created_at`, `updated_at`) VALUES
(1001, 'Test Matereal', 5000, 0, 'g', NULL, NULL, NULL, NULL, NULL, NULL),
(1002, 'Nhựa ABS', 10000, 0, 'g', NULL, NULL, NULL, NULL, NULL, NULL),
(1003, 'Mực gel xanh', 5000, 0, 'g', NULL, NULL, NULL, NULL, NULL, NULL),
(1004, 'Mực gel đen', 5000, 0, 'g', NULL, NULL, NULL, NULL, NULL, NULL),
(1005, 'Bi kim loại 0.5mm', 2000, 0, 'g', NULL, NULL, NULL, NULL, NULL, NULL),
(1006, 'Bi kim loại 0.7mm', 3000, 0, 'g', NULL, NULL, NULL, NULL, NULL, NULL),
(1007, 'Bi kim loại 1.0mm', 2000, 0, 'g', NULL, NULL, NULL, NULL, NULL, NULL),
(1008, 'Lò xo thép', 1000, 0, 'g', NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Triggers `material`
--
DROP TRIGGER IF EXISTS `validate_material_stock`;
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

DROP TABLE IF EXISTS `modules`;
CREATE TABLE `modules` (
  `module_id` int(11) NOT NULL,
  `module_name` varchar(50) NOT NULL,
  `module_display_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `route` varchar(100) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`module_id`, `module_name`, `module_display_name`, `description`, `icon`, `parent_id`, `route`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'customer', 'Quản lý Khách hàng', NULL, 'fa-users', NULL, 'customer/', 1, 1, '2025-11-02 11:41:16'),
(2, 'product', 'Quản lý Sản phẩm', NULL, 'fa-box', NULL, 'product/', 2, 1, '2025-11-02 11:41:16'),
(3, 'order', 'Quản lý Đơn hàng', NULL, 'fa-shopping-cart', NULL, 'order/', 3, 1, '2025-11-02 11:41:16'),
(4, 'project', 'Quản lý Dự án', NULL, 'fa-project-diagram', NULL, 'project/', 4, 1, '2025-11-02 11:41:16'),
(5, 'bom', 'Định mức NVL', NULL, 'fa-list-alt', NULL, 'bom/', 5, 1, '2025-11-02 11:41:16'),
(6, 'planning', 'Kế hoạch Sản xuất', NULL, 'fa-calendar-alt', NULL, 'planning/', 6, 1, '2025-11-02 11:41:16'),
(7, 'shift', 'Quản lý Ca', NULL, 'fa-clock', NULL, 'shift/', 7, 1, '2025-11-02 11:41:16'),
(8, 'production', 'Báo cáo Sản xuất', NULL, 'fa-industry', NULL, 'production/', 8, 1, '2025-11-02 11:41:16'),
(9, 'shift_closing', 'Chốt ca', NULL, 'fa-check-square', NULL, 'shift_closing/', 9, 1, '2025-11-02 11:41:16'),
(10, 'machine', 'Quản lý Máy móc', NULL, 'fa-cogs', NULL, 'machine/', 10, 1, '2025-11-02 11:41:16'),
(11, 'incident', 'Quản lý Sự cố', NULL, 'fa-exclamation-triangle', NULL, 'incident/', 11, 1, '2025-11-02 11:41:16'),
(12, 'material', 'Danh mục NVL', NULL, 'fa-cubes', NULL, 'material/', 12, 1, '2025-11-02 11:41:16'),
(13, 'warehouse', 'Quản lý Kho', NULL, 'fa-warehouse', NULL, 'warehouse/', 13, 1, '2025-11-02 11:41:16'),
(14, 'qc', 'Kiểm soát Chất lượng', NULL, 'fa-check-circle', NULL, 'qc/', 14, 1, '2025-11-02 11:41:16'),
(15, 'staff', 'Quản lý Nhân sự', NULL, 'fa-user-tie', NULL, 'staff/', 15, 1, '2025-11-02 11:41:16'),
(16, 'report', 'Báo cáo & Dashboard', NULL, 'fa-chart-bar', NULL, 'report/', 16, 1, '2025-11-02 11:41:16'),
(17, 'user', 'Quản lý Người dùng', NULL, 'fa-user-cog', NULL, 'user/', 17, 1, '2025-11-02 11:41:16'),
(18, 'system', 'Cài đặt Hệ thống', NULL, 'fa-wrench', NULL, 'system/', 18, 1, '2025-11-02 11:41:16');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `module_id` int(11) DEFAULT NULL,
  `permission_name` varchar(100) NOT NULL,
  `permission_display_name` varchar(200) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `module_id`, `permission_name`, `permission_display_name`, `action`, `description`, `created_at`) VALUES
(1, 1, 'customer.view', 'Xem danh sách khách hàng', 'view', 'Xem danh sách và thông tin chi tiết khách hàng', '2025-11-02 11:41:37'),
(2, 1, 'customer.create', 'Tạo khách hàng mới', 'create', 'Thêm khách hàng mới vào hệ thống', '2025-11-02 11:41:37'),
(3, 1, 'customer.edit', 'Sửa thông tin khách hàng', 'edit', 'Cập nhật thông tin khách hàng', '2025-11-02 11:41:37'),
(4, 1, 'customer.delete', 'Xóa khách hàng', 'delete', 'Xóa khách hàng khỏi hệ thống', '2025-11-02 11:41:37'),
(5, 1, 'customer.export', 'Xuất Excel khách hàng', 'export', 'Xuất danh sách khách hàng ra file Excel', '2025-11-02 11:41:37'),
(6, 2, 'product.view', 'Xem danh sách sản phẩm', 'view', 'Xem danh sách sản phẩm bút bi', '2025-11-02 11:41:37'),
(7, 2, 'product.create', 'Tạo sản phẩm mới', 'create', 'Thêm sản phẩm mới', '2025-11-02 11:41:37'),
(8, 2, 'product.edit', 'Sửa thông tin sản phẩm', 'edit', 'Cập nhật thông tin sản phẩm', '2025-11-02 11:41:37'),
(9, 2, 'product.delete', 'Xóa sản phẩm', 'delete', 'Xóa sản phẩm khỏi danh mục', '2025-11-02 11:41:37'),
(10, 2, 'product_variant.view', 'Xem biến thể sản phẩm', 'view', 'Xem các biến thể (0.5mm, 0.7mm, 1.0mm, màu mực)', '2025-11-02 11:41:37'),
(11, 2, 'product_variant.create', 'Tạo biến thể mới', 'create', 'Thêm biến thể mới cho sản phẩm', '2025-11-02 11:41:37'),
(12, 2, 'product_variant.edit', 'Sửa biến thể', 'edit', 'Cập nhật thông tin biến thể', '2025-11-02 11:41:37'),
(13, 2, 'product_variant.delete', 'Xóa biến thể', 'delete', 'Xóa biến thể sản phẩm', '2025-11-02 11:41:37'),
(14, 2, 'product.export', 'Xuất Excel sản phẩm', 'export', 'Xuất danh sách sản phẩm ra Excel', '2025-11-02 11:41:37'),
(15, 3, 'order.view', 'Xem danh sách đơn hàng', 'view', 'Xem đơn hàng từ khách hàng', '2025-11-02 11:41:37'),
(16, 3, 'order.create', 'Tạo đơn hàng mới', 'create', 'Tiếp nhận đơn hàng mới', '2025-11-02 11:41:37'),
(17, 3, 'order.edit', 'Sửa đơn hàng', 'edit', 'Cập nhật thông tin đơn hàng', '2025-11-02 11:41:37'),
(18, 3, 'order.delete', 'Xóa đơn hàng', 'delete', 'Hủy đơn hàng', '2025-11-02 11:41:37'),
(19, 3, 'order.approve', 'Phê duyệt đơn hàng', 'approve', 'BOD phê duyệt đơn hàng', '2025-11-02 11:41:37'),
(20, 3, 'order.reject', 'Từ chối đơn hàng', 'reject', 'BOD từ chối đơn hàng', '2025-11-02 11:41:37'),
(21, 3, 'order.export', 'Xuất Excel đơn hàng', 'export', 'Xuất danh sách đơn hàng ra Excel', '2025-11-02 11:41:37'),
(22, 4, 'project.view', 'Xem danh sách dự án', 'view', 'Xem dự án sản xuất', '2025-11-02 11:41:37'),
(23, 4, 'project.create', 'Tạo dự án mới', 'create', 'Tạo dự án sản xuất từ đơn hàng', '2025-11-02 11:41:37'),
(24, 4, 'project.edit', 'Sửa thông tin dự án', 'edit', 'Cập nhật thông tin dự án', '2025-11-02 11:41:37'),
(25, 4, 'project.delete', 'Xóa dự án', 'delete', 'Xóa dự án', '2025-11-02 11:41:37'),
(26, 4, 'project.approve', 'Phê duyệt dự án', 'approve', 'BOD phê duyệt dự án', '2025-11-02 11:41:37'),
(27, 4, 'project.export', 'Xuất Excel dự án', 'export', 'Xuất danh sách dự án', '2025-11-02 11:41:37'),
(28, 5, 'bom.view', 'Xem định mức NVL', 'view', 'Xem BOM (Bill of Materials)', '2025-11-02 11:41:37'),
(29, 5, 'bom.create', 'Tạo định mức NVL', 'create', 'Tạo BOM mới cho sản phẩm', '2025-11-02 11:41:37'),
(30, 5, 'bom.edit', 'Sửa định mức NVL', 'edit', 'Cập nhật định mức NVL', '2025-11-02 11:41:37'),
(31, 5, 'bom.delete', 'Xóa định mức NVL', 'delete', 'Xóa BOM', '2025-11-02 11:41:37'),
(32, 5, 'bom.approve', 'Xác nhận định mức NVL', 'approve', 'Kỹ thuật viên xác nhận BOM', '2025-11-02 11:41:37'),
(33, 5, 'bom.export', 'Xuất Excel BOM', 'export', 'Xuất BOM ra Excel', '2025-11-02 11:41:37'),
(34, 6, 'planning.view', 'Xem kế hoạch sản xuất', 'view', 'Xem kế hoạch sản xuất tổng', '2025-11-02 11:41:37'),
(35, 6, 'planning.create', 'Lập kế hoạch sản xuất', 'create', 'Tạo kế hoạch sản xuất mới', '2025-11-02 11:41:37'),
(36, 6, 'planning.edit', 'Sửa kế hoạch sản xuất', 'edit', 'Điều chỉnh kế hoạch', '2025-11-02 11:41:37'),
(37, 6, 'planning.delete', 'Xóa kế hoạch sản xuất', 'delete', 'Xóa kế hoạch', '2025-11-02 11:41:37'),
(38, 6, 'planning.submit', 'Gửi duyệt kế hoạch', 'submit', 'Line Manager gửi kế hoạch lên BOD', '2025-11-02 11:41:37'),
(39, 6, 'planning.approve', 'Phê duyệt kế hoạch', 'approve', 'BOD phê duyệt kế hoạch sản xuất', '2025-11-02 11:41:37'),
(40, 6, 'planning.reject', 'Từ chối kế hoạch', 'reject', 'BOD từ chối kế hoạch', '2025-11-02 11:41:37'),
(41, 6, 'planning_line.view', 'Xem kế hoạch line', 'view', 'Xem kế hoạch từng dây chuyền', '2025-11-02 11:41:37'),
(42, 6, 'planning_line.create', 'Tạo kế hoạch line', 'create', 'Tạo kế hoạch cho line', '2025-11-02 11:41:37'),
(43, 6, 'planning_line.edit', 'Sửa kế hoạch line', 'edit', 'Điều chỉnh kế hoạch line', '2025-11-02 11:41:37'),
(44, 6, 'planning_line.approve', 'Phê duyệt kế hoạch line', 'approve', 'BOD phê duyệt kế hoạch line', '2025-11-02 11:41:37'),
(45, 7, 'shift.view', 'Xem danh sách ca', 'view', 'Xem thông tin ca làm việc', '2025-11-02 11:41:37'),
(46, 7, 'shift.view_own', 'Xem lịch ca của mình', 'view', 'Công nhân xem lịch ca của mình', '2025-11-02 11:41:37'),
(47, 7, 'shift.create', 'Tạo ca làm việc', 'create', 'Tạo ca sản xuất mới', '2025-11-02 11:41:37'),
(48, 7, 'shift.edit', 'Sửa thông tin ca', 'edit', 'Cập nhật thông tin ca', '2025-11-02 11:41:37'),
(49, 7, 'shift.delete', 'Xóa ca làm việc', 'delete', 'Xóa ca', '2025-11-02 11:41:37'),
(50, 7, 'shift_assignment.view', 'Xem phân công ca', 'view', 'Xem phân công nhân sự cho ca', '2025-11-02 11:41:37'),
(51, 7, 'shift_assignment.create', 'Phân công nhân sự', 'create', 'Gán nhân sự vào ca', '2025-11-02 11:41:37'),
(52, 7, 'shift_assignment.edit', 'Sửa phân công ca', 'edit', 'Thay đổi phân công', '2025-11-02 11:41:37'),
(53, 7, 'shift_assignment.delete', 'Xóa phân công', 'delete', 'Hủy phân công nhân sự', '2025-11-02 11:41:37'),
(54, 7, 'shift_assignment.confirm', 'Xác nhận nhận việc', 'confirm', 'Công nhân xác nhận nhận việc', '2025-11-02 11:41:37'),
(55, 7, 'machine_assignment.view', 'Xem gán máy cho ca', 'view', 'Xem máy được gán cho ca', '2025-11-02 11:41:37'),
(56, 7, 'machine_assignment.create', 'Gán máy cho ca', 'create', 'Gán máy móc vào ca sản xuất', '2025-11-02 11:41:37'),
(57, 7, 'machine_assignment.edit', 'Sửa gán máy', 'edit', 'Thay đổi máy cho ca', '2025-11-02 11:41:37'),
(58, 7, 'machine_assignment.delete', 'Xóa gán máy', 'delete', 'Hủy gán máy', '2025-11-02 11:41:37'),
(59, 8, 'production.view', 'Xem báo cáo sản xuất', 'view', 'Xem tổng hợp sản lượng sản xuất', '2025-11-02 11:41:37'),
(60, 8, 'production.view_own', 'Xem sản lượng của mình', 'view', 'Công nhân xem sản lượng của mình', '2025-11-02 11:41:37'),
(61, 8, 'production.create', 'Nhập báo cáo sản xuất', 'create', 'Nhập sản lượng sản xuất', '2025-11-02 11:41:37'),
(62, 8, 'production.edit', 'Sửa báo cáo sản xuất', 'edit', 'Cập nhật sản lượng', '2025-11-02 11:41:37'),
(63, 8, 'production.delete', 'Xóa báo cáo sản xuất', 'delete', 'Xóa báo cáo sản lượng', '2025-11-02 11:41:37'),
(64, 8, 'production_by_machine.view', 'Xem sản lượng theo máy', 'view', 'Theo dõi sản lượng từng máy', '2025-11-02 11:41:37'),
(65, 8, 'production_by_shift.view', 'Xem sản lượng theo ca', 'view', 'Theo dõi sản lượng từng ca', '2025-11-02 11:41:37'),
(66, 8, 'production_by_line.view', 'Xem sản lượng theo line', 'view', 'Theo dõi sản lượng từng dây chuyền', '2025-11-02 11:41:37'),
(67, 8, 'production.export', 'Xuất Excel sản lượng', 'export', 'Xuất báo cáo sản lượng ra Excel', '2025-11-02 11:41:37'),
(68, 9, 'shift_closing.view', 'Xem phiếu chốt ca', 'view', 'Xem phiếu chốt ca sản xuất', '2025-11-02 11:41:37'),
(69, 9, 'shift_closing.create', 'Tạo phiếu chốt ca', 'create', 'Tạo phiếu Finished/Waste sau ca', '2025-11-02 11:41:37'),
(70, 9, 'shift_closing.edit', 'Sửa phiếu chốt ca', 'edit', 'Cập nhật phiếu chốt ca', '2025-11-02 11:41:37'),
(71, 9, 'shift_closing.delete', 'Xóa phiếu chốt ca', 'delete', 'Xóa phiếu chốt ca', '2025-11-02 11:41:37'),
(72, 9, 'shift_closing.submit', 'Gửi phiếu chốt ca', 'submit', 'Line Manager gửi phiếu lên QC', '2025-11-02 11:41:37'),
(73, 9, 'shift_closing.approve', 'Phê duyệt chốt ca', 'approve', 'Line Manager phê duyệt chốt ca', '2025-11-02 11:41:37'),
(74, 9, 'shift_closing.reject', 'Từ chối chốt ca', 'reject', 'Từ chối phiếu chốt ca', '2025-11-02 11:41:37'),
(75, 9, 'waste_reason.create', 'Ghi nhận lý do lỗi', 'create', 'Ghi lý do phế phẩm', '2025-11-02 11:41:37'),
(76, 9, 'waste_reason.view', 'Xem lý do lỗi', 'view', 'Xem nguyên nhân phế phẩm', '2025-11-02 11:41:37'),
(77, 10, 'machine.view', 'Xem danh sách máy móc', 'view', 'Xem thông tin máy móc, dây chuyền', '2025-11-02 11:41:37'),
(78, 10, 'machine.create', 'Thêm máy móc mới', 'create', 'Thêm máy mới vào hệ thống', '2025-11-02 11:41:37'),
(79, 10, 'machine.edit', 'Sửa thông tin máy', 'edit', 'Cập nhật thông tin máy', '2025-11-02 11:41:37'),
(80, 10, 'machine.delete', 'Xóa máy móc', 'delete', 'Xóa máy khỏi hệ thống', '2025-11-02 11:41:37'),
(81, 10, 'machine.update_status', 'Cập nhật trạng thái máy', 'update', 'Kỹ thuật viên cập nhật trạng thái', '2025-11-02 11:41:37'),
(82, 10, 'machine.confirm_ready', 'Xác nhận máy Ready', 'confirm', 'Kỹ thuật viên xác nhận máy sẵn sàng', '2025-11-02 11:41:37'),
(83, 10, 'machine_maintenance.view', 'Xem lịch bảo trì', 'view', 'Xem kế hoạch bảo trì máy', '2025-11-02 11:41:37'),
(84, 10, 'machine_maintenance.create', 'Lập lịch bảo trì', 'create', 'Tạo kế hoạch bảo trì', '2025-11-02 11:41:37'),
(85, 10, 'machine_maintenance.edit', 'Sửa lịch bảo trì', 'edit', 'Cập nhật lịch bảo trì', '2025-11-02 11:41:37'),
(86, 10, 'machine_maintenance.delete', 'Xóa lịch bảo trì', 'delete', 'Hủy lịch bảo trì', '2025-11-02 11:41:37'),
(87, 10, 'machine_maintenance.complete', 'Hoàn thành bảo trì', 'complete', 'Đánh dấu bảo trì hoàn thành', '2025-11-02 11:41:37'),
(88, 11, 'incident.view', 'Xem danh sách sự cố', 'view', 'Xem sự cố máy móc, chất lượng', '2025-11-02 11:41:37'),
(89, 11, 'incident.create', 'Báo cáo sự cố', 'create', 'Công nhân/Line Manager báo cáo sự cố', '2025-11-02 11:41:37'),
(90, 11, 'incident.edit', 'Sửa thông tin sự cố', 'edit', 'Cập nhật thông tin sự cố', '2025-11-02 11:41:37'),
(91, 11, 'incident.delete', 'Xóa sự cố', 'delete', 'Xóa báo cáo sự cố', '2025-11-02 11:41:37'),
(92, 11, 'incident.assign', 'Phân công xử lý sự cố', 'assign', 'Gán kỹ thuật viên xử lý', '2025-11-02 11:41:37'),
(93, 11, 'incident.update', 'Cập nhật xử lý sự cố', 'update', 'Kỹ thuật viên cập nhật tiến độ', '2025-11-02 11:41:37'),
(94, 11, 'incident.resolve', 'Giải quyết sự cố', 'resolve', 'Đánh dấu sự cố đã xử lý', '2025-11-02 11:41:37'),
(95, 11, 'incident.close', 'Đóng sự cố', 'close', 'Đóng sự cố sau khi xử lý xong', '2025-11-02 11:41:37'),
(96, 11, 'incident.export', 'Xuất Excel sự cố', 'export', 'Xuất danh sách sự cố', '2025-11-02 11:41:37'),
(97, 12, 'material.view', 'Xem danh mục NVL', 'view', 'Xem danh sách nguyên vật liệu', '2025-11-02 11:41:37'),
(98, 12, 'material.create', 'Thêm NVL mới', 'create', 'Thêm nguyên vật liệu mới', '2025-11-02 11:41:37'),
(99, 12, 'material.edit', 'Sửa thông tin NVL', 'edit', 'Cập nhật thông tin NVL', '2025-11-02 11:41:37'),
(100, 12, 'material.delete', 'Xóa NVL', 'delete', 'Xóa nguyên vật liệu', '2025-11-02 11:41:37'),
(101, 12, 'material.view_stock', 'Xem tồn kho NVL', 'view', 'Xem số lượng tồn kho', '2025-11-02 11:41:37'),
(102, 12, 'material.export', 'Xuất Excel NVL', 'export', 'Xuất danh sách NVL', '2025-11-02 11:41:37'),
(103, 13, 'warehouse.view', 'Xem tổng quan kho', 'view', 'Xem thông tin kho tổng quát', '2025-11-02 11:41:37'),
(104, 13, 'warehouse_receipt.view', 'Xem phiếu nhập kho NVL', 'view', 'Xem phiếu nhập NVL', '2025-11-02 11:41:37'),
(105, 13, 'warehouse_receipt.create', 'Tạo phiếu nhập NVL', 'create', 'Nhập kho NVL theo PO', '2025-11-02 11:41:37'),
(106, 13, 'warehouse_receipt.edit', 'Sửa phiếu nhập NVL', 'edit', 'Cập nhật phiếu nhập', '2025-11-02 11:41:37'),
(107, 13, 'warehouse_receipt.delete', 'Xóa phiếu nhập NVL', 'delete', 'Hủy phiếu nhập', '2025-11-02 11:41:37'),
(108, 13, 'warehouse_issue.view', 'Xem phiếu xuất kho NVL', 'view', 'Xem phiếu xuất NVL', '2025-11-02 11:41:37'),
(109, 13, 'warehouse_issue.create', 'Tạo phiếu xuất NVL', 'create', 'Xuất NVL cho ca (theo BOM)', '2025-11-02 11:41:37'),
(110, 13, 'warehouse_issue.edit', 'Sửa phiếu xuất NVL', 'edit', 'Cập nhật phiếu xuất', '2025-11-02 11:41:37'),
(111, 13, 'warehouse_issue.delete', 'Xóa phiếu xuất NVL', 'delete', 'Hủy phiếu xuất', '2025-11-02 11:41:37'),
(112, 13, 'finished_goods_receipt.view', 'Xem phiếu nhập TP', 'view', 'Xem phiếu nhập thành phẩm', '2025-11-02 11:41:37'),
(113, 13, 'finished_goods_receipt.create', 'Tạo phiếu nhập TP', 'create', 'Nhập kho TP sau QC', '2025-11-02 11:41:37'),
(114, 13, 'finished_goods_receipt.edit', 'Sửa phiếu nhập TP', 'edit', 'Cập nhật phiếu nhập TP', '2025-11-02 11:41:37'),
(115, 13, 'finished_goods_receipt.delete', 'Xóa phiếu nhập TP', 'delete', 'Hủy phiếu nhập TP', '2025-11-02 11:41:37'),
(116, 13, 'finished_goods_receipt.approve', 'Phê duyệt nhập TP', 'approve', 'QC xác nhận cho nhập TP', '2025-11-02 11:41:37'),
(117, 13, 'finished_goods_issue.view', 'Xem phiếu xuất TP', 'view', 'Xem phiếu xuất thành phẩm', '2025-11-02 11:41:37'),
(118, 13, 'finished_goods_issue.create', 'Tạo phiếu xuất TP', 'create', 'Xuất TP giao hàng', '2025-11-02 11:41:37'),
(119, 13, 'finished_goods_issue.edit', 'Sửa phiếu xuất TP', 'edit', 'Cập nhật phiếu xuất TP', '2025-11-02 11:41:37'),
(120, 13, 'finished_goods_issue.delete', 'Xóa phiếu xuất TP', 'delete', 'Hủy phiếu xuất TP', '2025-11-02 11:41:37'),
(121, 13, 'stock.view', 'Xem tồn kho', 'view', 'Xem tồn kho NVL và TP', '2025-11-02 11:41:37'),
(122, 13, 'stock_report.view', 'Xem báo cáo tồn kho', 'view', 'Báo cáo tồn và luân chuyển', '2025-11-02 11:41:37'),
(123, 13, 'stock_report.export', 'Xuất Excel tồn kho', 'export', 'Xuất báo cáo tồn kho', '2025-11-02 11:41:37'),
(124, 14, 'qc_inspection.view', 'Xem phiếu kiểm tra QC', 'view', 'Xem phiếu QC sau ca', '2025-11-02 11:41:37'),
(125, 14, 'qc_inspection.create', 'Tạo phiếu kiểm tra QC', 'create', 'Tạo phiếu kiểm tra chất lượng', '2025-11-02 11:41:37'),
(126, 14, 'qc_inspection.edit', 'Sửa phiếu QC', 'edit', 'Cập nhật phiếu QC', '2025-11-02 11:41:37'),
(127, 14, 'qc_inspection.delete', 'Xóa phiếu QC', 'delete', 'Xóa phiếu QC', '2025-11-02 11:41:37'),
(128, 14, 'qc_inspection.approve', 'Phê duyệt QC', 'approve', 'QC approve sản phẩm đạt', '2025-11-02 11:41:37'),
(129, 14, 'qc_inspection.reject', 'Từ chối QC', 'reject', 'QC reject sản phẩm lỗi', '2025-11-02 11:41:37'),
(130, 14, 'qc_checklist.view', 'Xem checklist QC', 'view', 'Xem tiêu chuẩn kiểm tra', '2025-11-02 11:41:37'),
(131, 14, 'qc_defect.view', 'Xem lỗi phát hiện', 'view', 'Xem danh sách lỗi', '2025-11-02 11:41:37'),
(132, 14, 'qc_defect.create', 'Ghi nhận lỗi', 'create', 'Ghi lỗi và nguyên nhân', '2025-11-02 11:41:37'),
(133, 14, 'qc_defect.edit', 'Sửa lỗi', 'edit', 'Cập nhật thông tin lỗi', '2025-11-02 11:41:37'),
(134, 14, 'aql_standard.view', 'Xem tiêu chuẩn AQL', 'view', 'Xem Acceptable Quality Level', '2025-11-02 11:41:37'),
(135, 14, 'qc_report.view', 'Xem báo cáo QC', 'view', 'Báo cáo chất lượng tổng hợp', '2025-11-02 11:41:37'),
(136, 14, 'qc_report.export', 'Xuất Excel báo cáo QC', 'export', 'Xuất báo cáo chất lượng', '2025-11-02 11:41:37'),
(137, 15, 'staff.view', 'Xem danh sách nhân sự', 'view', 'Xem thông tin nhân viên, công nhân', '2025-11-02 11:41:37'),
(138, 15, 'staff.create', 'Thêm nhân sự mới', 'create', 'Thêm nhân viên/công nhân', '2025-11-02 11:41:37'),
(139, 15, 'staff.edit', 'Sửa thông tin nhân sự', 'edit', 'Cập nhật thông tin nhân sự', '2025-11-02 11:41:37'),
(140, 15, 'staff.delete', 'Xóa nhân sự', 'delete', 'Xóa nhân sự khỏi hệ thống', '2025-11-02 11:41:37'),
(141, 15, 'staff.export', 'Xuất Excel nhân sự', 'export', 'Xuất danh sách nhân sự', '2025-11-02 11:41:37'),
(142, 16, 'dashboard.view_all', 'Xem dashboard tổng quan', 'view', 'Dashboard cho BOD', '2025-11-02 11:41:37'),
(143, 16, 'dashboard.view_line', 'Xem dashboard dây chuyền', 'view', 'Dashboard cho Line Manager', '2025-11-02 11:41:37'),
(144, 16, 'dashboard.view_warehouse', 'Xem dashboard kho', 'view', 'Dashboard cho Warehouse', '2025-11-02 11:41:37'),
(145, 16, 'report.production_summary', 'Báo cáo tổng hợp sản xuất', 'view', 'Tổng hợp sản lượng, hiệu suất', '2025-11-02 11:41:37'),
(146, 16, 'report.quality_summary', 'Báo cáo tổng hợp chất lượng', 'view', 'Tổng hợp QC, tỷ lệ lỗi', '2025-11-02 11:41:37'),
(147, 16, 'report.inventory', 'Báo cáo tồn kho', 'view', 'Báo cáo tồn NVL và TP', '2025-11-02 11:41:37'),
(148, 16, 'report.material_movement', 'Báo cáo luân chuyển NVL', 'view', 'Nhập/xuất NVL theo thời gian', '2025-11-02 11:41:37'),
(149, 16, 'report.line_performance', 'Báo cáo hiệu suất line', 'view', 'Hiệu suất vận hành dây chuyền', '2025-11-02 11:41:37'),
(150, 16, 'report.shift_summary', 'Báo cáo tổng hợp ca', 'view', 'Tổng hợp sản lượng theo ca', '2025-11-02 11:41:37'),
(151, 16, 'report.efficiency', 'Báo cáo hiệu suất tổng thể', 'view', 'OEE, hiệu suất toàn hệ thống', '2025-11-02 11:41:37'),
(152, 16, 'report.financial', 'Báo cáo tài chính', 'view', 'Doanh thu, chi phí (BOD only)', '2025-11-02 11:41:37'),
(153, 16, 'report.defect_analysis', 'Phân tích lỗi sản phẩm', 'view', 'Phân tích nguyên nhân lỗi', '2025-11-02 11:41:37'),
(154, 16, 'report.maintenance', 'Báo cáo bảo trì', 'view', 'Lịch sử bảo trì máy móc', '2025-11-02 11:41:37'),
(155, 16, 'report.incident_history', 'Báo cáo lịch sử sự cố', 'view', 'Lịch sử sự cố và xử lý', '2025-11-02 11:41:37'),
(156, 16, 'report.export_all', 'Xuất tất cả báo cáo', 'export', 'Quyền xuất Excel tất cả báo cáo', '2025-11-02 11:41:37'),
(157, 17, 'user.view', 'Xem danh sách người dùng', 'view', 'Xem tài khoản user', '2025-11-02 11:41:37'),
(158, 17, 'user.create', 'Tạo người dùng mới', 'create', 'Thêm tài khoản user', '2025-11-02 11:41:37'),
(159, 17, 'user.edit', 'Sửa thông tin người dùng', 'edit', 'Cập nhật thông tin user', '2025-11-02 11:41:37'),
(160, 17, 'user.delete', 'Xóa người dùng', 'delete', 'Xóa tài khoản user', '2025-11-02 11:41:37'),
(161, 17, 'user.reset_password', 'Đặt lại mật khẩu', 'reset', 'Reset password cho user', '2025-11-02 11:41:37'),
(162, 17, 'user.lock', 'Khóa tài khoản', 'lock', 'Khóa user không cho đăng nhập', '2025-11-02 11:41:37'),
(163, 17, 'user.unlock', 'Mở khóa tài khoản', 'unlock', 'Mở khóa tài khoản user', '2025-11-02 11:41:37'),
(164, 17, 'user_role.assign', 'Gán vai trò cho user', 'assign', 'Phân quyền role cho user', '2025-11-02 11:41:37'),
(165, 17, 'audit_log.view', 'Xem nhật ký hoạt động', 'view', 'Xem audit log của user', '2025-11-02 11:41:37'),
(166, 17, 'audit_log.export', 'Xuất Excel nhật ký', 'export', 'Xuất audit log ra Excel', '2025-11-02 11:41:37'),
(167, 18, 'role.view', 'Xem danh sách vai trò', 'view', 'Xem roles trong hệ thống', '2025-11-02 11:41:37'),
(168, 18, 'role.create', 'Tạo vai trò mới', 'create', 'Thêm role mới', '2025-11-02 11:41:37'),
(169, 18, 'role.edit', 'Sửa vai trò', 'edit', 'Cập nhật role', '2025-11-02 11:41:37'),
(170, 18, 'role.delete', 'Xóa vai trò', 'delete', 'Xóa role', '2025-11-02 11:41:37'),
(171, 18, 'permission.view', 'Xem danh sách quyền', 'view', 'Xem permissions', '2025-11-02 11:41:37'),
(172, 18, 'role_permission.assign', 'Gán quyền cho vai trò', 'assign', 'Assign permissions cho role', '2025-11-02 11:41:37'),
(173, 18, 'system_settings.view', 'Xem cài đặt hệ thống', 'view', 'Xem system settings', '2025-11-02 11:41:37'),
(174, 18, 'system_settings.edit', 'Sửa cài đặt hệ thống', 'edit', 'Thay đổi system settings', '2025-11-02 11:41:37');

-- --------------------------------------------------------

--
-- Table structure for table `planning`
--

DROP TABLE IF EXISTS `planning`;
CREATE TABLE `planning` (
  `id_plan` int(15) NOT NULL,
  `plan_name` varchar(25) NOT NULL,
  `id_project` int(15) NOT NULL,
  `qty_target` int(11) NOT NULL,
  `end_date` date NOT NULL,
  `pl_status` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `note` text DEFAULT NULL,
  `suggested_shifts` int(11) DEFAULT NULL,
  `materials` longtext DEFAULT NULL,
  `lines` longtext DEFAULT NULL,
  `needs_review` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng kế hoạch - qty_target: cái/ca (số lượng bút mỗi ca)';

--
-- Dumping data for table `planning`
--

INSERT INTO `planning` (`id_plan`, `plan_name`, `id_project`, `qty_target`, `end_date`, `pl_status`, `start_date`, `created_at`, `updated_at`, `note`, `suggested_shifts`, `materials`, `lines`, `needs_review`) VALUES
(1001, 'Plan-test', 1001, 2000, '2023-11-14', 1, NULL, '2025-12-16 10:25:51', '2025-12-16 10:29:43', NULL, NULL, NULL, NULL, 0),
(1087, 'KH-1001-1765116526', 1001, 9965, '2023-11-06', 1, '2023-11-04', '2025-12-07 21:08:46', '2025-12-07 21:08:46', NULL, 4, '[\"Bi kim loại 0.7mm — Yêu cầu: 9,965 — Thiếu: 6,965\",\"Lò xo thép — Yêu cầu: 9,965 — Thiếu: 8,965\"]', 'Dây chuyền 1 (công suất: 500.00)', 0);

-- --------------------------------------------------------

--
-- Table structure for table `plan_shift`
--

DROP TABLE IF EXISTS `plan_shift`;
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

DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id_product` int(25) NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `summary` text DEFAULT NULL,
  `application` varchar(100) NOT NULL COMMENT 'Màu mực: Xanh, Đen, Đỏ, Nhiều màu',
  `diameter` decimal(3,1) NOT NULL DEFAULT 0.5 COMMENT 'Đường kính bi viết (mm)',
  `bom` longtext DEFAULT NULL COMMENT 'JSON BOM',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id_product`, `product_name`, `summary`, `application`, `diameter`, `bom`, `is_active`, `created_at`, `updated_at`, `created_by`) VALUES
(1001, 'Bút bi TL-079', 'Bút bi mực gel, thân nhựa trong suốt, viết mượt', 'Xanh dương', 0.5, NULL, 1, '2025-12-18 18:51:41', '2025-12-18 18:51:41', NULL),
(1002, 'Bút bi TL-050', 'Bút bi dầu, thân nhựa màu, giá rẻ', 'Đen', 0.5, NULL, 1, '2025-12-18 18:51:41', '2025-12-18 18:51:41', NULL),
(1003, 'Bút bi TL-100', 'Bút bi cao cấp, thân kim loại', 'Đỏ', 0.5, NULL, 1, '2025-12-18 18:51:41', '2025-12-18 18:51:41', NULL),
(1004, 'Bút bi TL-Multi', 'Bút bi 4 màu, đa năng', 'Nhiều màu', 0.5, NULL, 1, '2025-12-18 18:51:41', '2025-12-18 18:51:41', NULL),
(1005, 'bút mực', NULL, 'tím', 0.7, NULL, 1, '2025-12-18 18:51:41', '2025-12-18 18:51:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `production_lines`
--

DROP TABLE IF EXISTS `production_lines`;
CREATE TABLE `production_lines` (
  `id` int(11) NOT NULL,
  `zone_id` int(11) DEFAULT NULL,
  `line_code` varchar(50) NOT NULL COMMENT 'Mã dây chuyền: LINE01, LINE02...',
  `line_name` varchar(100) NOT NULL COMMENT 'Tên dây chuyền',
  `line_type` enum('production_raw','assembly_qc','virtual') NOT NULL DEFAULT 'production_raw' COMMENT 'production_raw=Sản xuất thô, assembly_qc=Lắp ráp-QC, virtual=Line ảo điều phối',
  `is_primary` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=Line chính (cố định), 0=Line phụ/ảo (có thể xóa)',
  `description` text DEFAULT NULL COMMENT 'Mô tả',
  `capacity_per_hour` int(11) DEFAULT 0 COMMENT 'Công suất/giờ',
  `status` tinyint(2) NOT NULL DEFAULT 1 COMMENT '1=Hoạt động, 2=Bảo trì, 3=Ngừng hoạt động',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng dây chuyền sản xuất';

--
-- Dumping data for table `production_lines`
--

INSERT INTO `production_lines` (`id`, `zone_id`, `line_code`, `line_name`, `line_type`, `is_primary`, `description`, `capacity_per_hour`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'LINE01', 'Dây chuyền sản xuất thô', 'production_raw', 1, 'Line chính 1: Sản xuất thô (ép nhựa, đúc...)', 100, 1, '2025-12-16 11:18:29', '2025-12-18 13:54:35'),
(2, 1, 'LINE02', 'Dây chuyền lắp ráp - QC', 'assembly_qc', 1, 'Line chính 2: Lắp ráp và kiểm định chất lượng', 80, 1, '2025-12-16 11:18:29', '2025-12-18 13:54:35'),
(3, 2, 'LINE03', 'Dây chuyền 3', 'production_raw', 0, 'Dây chuyền lắp ráp', 60, 1, '2025-12-16 11:18:29', '2025-12-16 14:05:42'),
(9, 1, 'LINE_VIRTUAL_01', 'Line điều phối ảo', 'virtual', 0, 'Line ảo để điều phối khi ép công suất hoặc sự cố lớn', 0, 1, '2025-12-18 13:54:35', '2025-12-18 13:54:35');

-- --------------------------------------------------------

--
-- Table structure for table `production_records`
--

DROP TABLE IF EXISTS `production_records`;
CREATE TABLE `production_records` (
  `id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL COMMENT 'ID ca làm việc',
  `machine_id` int(11) NOT NULL COMMENT 'ID máy',
  `staff_id` int(11) DEFAULT NULL COMMENT 'ID nhân viên (nếu có)',
  `timestamp` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Thời điểm ghi nhận',
  `good_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Số lượng thành phẩm tốt',
  `defect_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Số lượng phế phẩm',
  `target_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Mục tiêu sản lượng',
  `downtime_minutes` int(11) NOT NULL DEFAULT 0 COMMENT 'Thời gian ngừng máy (phút)',
  `downtime_reason` varchar(255) DEFAULT NULL COMMENT 'Lý do downtime',
  `efficiency_rate` decimal(5,2) DEFAULT NULL COMMENT 'Tỷ lệ hiệu suất (%)',
  `defect_rate` decimal(5,2) DEFAULT NULL COMMENT 'Tỷ lệ phế phẩm (%)',
  `notes` text DEFAULT NULL COMMENT 'Ghi chú',
  `is_simulated` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Dữ liệu giả lập, 0=Dữ liệu thực',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu dữ liệu sản lượng sản xuất theo máy';

--
-- Dumping data for table `production_records`
--

INSERT INTO `production_records` (`id`, `shift_id`, `machine_id`, `staff_id`, `timestamp`, `good_count`, `defect_count`, `target_count`, `downtime_minutes`, `downtime_reason`, `efficiency_rate`, `defect_rate`, `notes`, `is_simulated`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 8, '2025-12-18 23:34:32', 166, 3, 199, 26, 'Nguyên liệu lỗi', 84.92, 2.40, NULL, 1, '2025-12-18 16:34:32', '2025-12-18 16:34:32'),
(2, 1, 2, 8, '2025-12-18 23:34:32', 52, 2, 62, 0, NULL, 87.10, 4.75, NULL, 1, '2025-12-18 16:34:32', '2025-12-18 16:34:32'),
(3, 1, 3, 8, '2025-12-18 23:34:32', 117, 4, 140, 0, NULL, 86.43, 3.70, NULL, 1, '2025-12-18 16:34:32', '2025-12-18 16:34:32'),
(4, 3, 5, 8, '2025-12-19 00:56:37', 188, 9, 225, 13, 'Vấn đề chất lượng', 87.56, 5.13, NULL, 1, '2025-12-18 17:56:37', '2025-12-18 17:56:37'),
(5, 3, 6, 6, '2025-12-19 00:56:37', 120, 4, 144, 0, NULL, 86.11, 3.75, NULL, 1, '2025-12-18 17:56:37', '2025-12-18 17:56:37');

-- --------------------------------------------------------

--
-- Table structure for table `production_shifts`
--

DROP TABLE IF EXISTS `production_shifts`;
CREATE TABLE `production_shifts` (
  `shift_id` int(11) NOT NULL,
  `shift_code` varchar(50) NOT NULL COMMENT 'Mã ca: CA01, CA02...',
  `shift_name` varchar(100) NOT NULL COMMENT 'Tên ca: Ca sáng, Ca chiều...',
  `line_id` int(11) NOT NULL COMMENT 'ID dây chuyền',
  `id_plan` int(11) DEFAULT NULL COMMENT 'ID kế hoạch sản xuất',
  `shift_date` date NOT NULL COMMENT 'Ngày làm việc',
  `start_time` time NOT NULL COMMENT 'Giờ bắt đầu ca',
  `end_time` time NOT NULL COMMENT 'Giờ kết thúc ca',
  `target_quantity` int(11) DEFAULT 0 COMMENT 'Chỉ tiêu sản lượng',
  `actual_quantity` int(11) DEFAULT 0 COMMENT 'Sản lượng thực tế',
  `shift_status` tinyint(2) NOT NULL DEFAULT 1 COMMENT '1=Chưa bắt đầu, 2=Đang chạy, 3=Hoàn thành, 4=Tạm dừng',
  `started_at` datetime DEFAULT NULL COMMENT 'Thời điểm bắt đầu ca thực tế',
  `started_by` int(11) DEFAULT NULL COMMENT 'Người bắt đầu ca',
  `ended_at` datetime DEFAULT NULL COMMENT 'Thời điểm kết thúc ca thực tế',
  `ended_by` int(11) DEFAULT NULL COMMENT 'Người kết thúc ca',
  `is_closed` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = Đã chốt ca',
  `staff_status` varchar(20) DEFAULT 'pending' COMMENT 'pending/sufficient/insufficient/conflict',
  `machine_status` varchar(20) DEFAULT 'unassigned' COMMENT 'unassigned/assigned/maintenance/down',
  `leader_id` int(11) DEFAULT NULL COMMENT 'Leader phụ trách ca này',
  `notes` text DEFAULT NULL COMMENT 'Ghi chú',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng ca sản xuất';

--
-- Dumping data for table `production_shifts`
--

INSERT INTO `production_shifts` (`shift_id`, `shift_code`, `shift_name`, `line_id`, `id_plan`, `shift_date`, `start_time`, `end_time`, `target_quantity`, `actual_quantity`, `shift_status`, `started_at`, `started_by`, `ended_at`, `ended_by`, `is_closed`, `staff_status`, `machine_status`, `leader_id`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'CA-S-001', 'Ca Sáng - Dây chuyền 1', 1, 1001, '2025-12-16', '07:00:00', '15:00:00', 500, 0, 3, '2025-12-19 00:41:23', 2, '2025-12-19 00:44:44', 2, 1, 'insufficient', 'unassigned', NULL, 'Ca sáng hôm nay', 1, '2025-12-16 11:14:22', '2025-12-18 17:44:44'),
(3, 'CA-S-002', 'Ca Sáng - Dây chuyền 2', 2, 1001, '2025-12-16', '07:00:00', '15:00:00', 800, 0, 3, '2025-12-19 00:56:22', 2, '2025-12-19 00:57:02', 2, 1, 'pending', 'unassigned', NULL, NULL, 1, '2025-12-16 11:14:22', '2025-12-18 17:57:02'),
(12, '', 'Ca Sáng', 1, 1087, '2025-12-19', '07:00:00', '09:30:00', 2000, 0, 1, NULL, NULL, NULL, NULL, 0, 'pending', 'unassigned', NULL, '', 2, '2025-12-18 19:13:11', '2025-12-18 19:13:11');

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

DROP TABLE IF EXISTS `project`;
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Thời gian tạo đơn hàng',
  `stock_allocation` longtext DEFAULT NULL COMMENT 'JSON phân bổ kho',
  `cancel_reason` text DEFAULT NULL,
  `warning_flag` tinyint(1) DEFAULT 0,
  `warning_type` varchar(50) DEFAULT NULL,
  `warning_details` longtext DEFAULT NULL,
  `capacity_level_used` tinyint(4) DEFAULT 1,
  `material_shifts_available` int(11) DEFAULT NULL,
  `finished_stock_available` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng dự án - qty_request: cái (số lượng bút), diameter: mm (đường kính bi)';

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`id_project`, `project_name`, `id_cust`, `id_product`, `diameter`, `qty_request`, `entry_date`, `pr_status`, `risk_flag`, `customer_request`, `created_at`, `stock_allocation`, `cancel_reason`, `warning_flag`, `warning_type`, `warning_details`, `capacity_level_used`, `material_shifts_available`, `finished_stock_available`) VALUES
(1001, 'PJ-TEST', 1001, 1001, 7.0, 10000, '2023-11-06', 1, 0, NULL, '2025-11-02 12:20:42', NULL, NULL, 0, NULL, NULL, 1, NULL, 0),
(1002, 'ORD-1001', 1001, 1001, 0.5, 300, '2025-12-30', 1, 0, NULL, '2025-12-13 17:00:00', '{\"from_stock\":0,\"for_production\":300}', NULL, 1, 'ok', '{}', 1, 1, 0),
(1003, 'ORD-1002', 1001, 1001, 0.5, 900, '2025-12-25', 1, 0, NULL, '2025-12-13 17:00:00', '{\"from_stock\":0,\"for_production\":900}', NULL, 1, 'material_shortage', '{}', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `p_machine`
--

DROP TABLE IF EXISTS `p_machine`;
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

DROP TABLE IF EXISTS `p_material`;
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
-- Table structure for table `qc_attachments`
--

DROP TABLE IF EXISTS `qc_attachments`;
CREATE TABLE `qc_attachments` (
  `id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL COMMENT 'FK to qc_sessions.id',
  `filename` varchar(255) NOT NULL COMMENT 'Original filename',
  `path` varchar(500) NOT NULL COMMENT 'Relative path from uploads root',
  `mime_type` varchar(100) DEFAULT NULL COMMENT 'File MIME type',
  `file_size` int(10) UNSIGNED DEFAULT NULL COMMENT 'File size in bytes',
  `uploaded_by` varchar(50) DEFAULT NULL COMMENT 'User code who uploaded',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='QC inspection evidence attachments';

-- --------------------------------------------------------

--
-- Table structure for table `qc_checklist_master`
--

DROP TABLE IF EXISTS `qc_checklist_master`;
CREATE TABLE `qc_checklist_master` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL COMMENT 'Checklist item code',
  `product_code` varchar(50) NOT NULL COMMENT 'Applicable product code',
  `variant` varchar(50) DEFAULT NULL COMMENT 'Specific variant (NULL = all)',
  `item_name` varchar(200) NOT NULL COMMENT 'Checklist item description',
  `criteria` text DEFAULT NULL COMMENT 'Pass/fail criteria',
  `sample_size` int(10) UNSIGNED DEFAULT NULL COMMENT 'Required sample size',
  `aql` decimal(5,2) DEFAULT 2.50 COMMENT 'Acceptance Quality Limit %',
  `category` varchar(50) DEFAULT NULL COMMENT 'Item category (visual, dimensional, functional)',
  `sequence` int(10) UNSIGNED DEFAULT 0 COMMENT 'Display order',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='QC checklist master data';

--
-- Dumping data for table `qc_checklist_master`
--

INSERT INTO `qc_checklist_master` (`id`, `code`, `product_code`, `variant`, `item_name`, `criteria`, `sample_size`, `aql`, `category`, `sequence`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'CHK-BP-001-01', 'PROD-BP-001', NULL, 'Visual Inspection - Body Defects', 'Check for cracks, scratches, discoloration on pen body', 50, 2.50, 'visual', 1, 1, '2025-11-02 14:34:42', '2025-11-02 14:34:42'),
(2, 'CHK-BP-001-02', 'PROD-BP-001', NULL, 'Ink Flow Test', 'Write 10 meters continuously without skipping', 20, 1.50, 'functional', 2, 1, '2025-11-02 14:34:42', '2025-11-02 14:34:42'),
(3, 'CHK-BP-001-03', 'PROD-BP-001', NULL, 'Dimensional Check - Length', 'Length must be 145mm ± 0.5mm', 30, 2.50, 'dimensional', 3, 1, '2025-11-02 14:34:42', '2025-11-02 14:34:42'),
(4, 'CHK-BP-001-04', 'PROD-BP-001', NULL, 'Clip Strength Test', 'Clip must withstand 500g pull force', 15, 1.00, 'functional', 4, 1, '2025-11-02 14:34:42', '2025-11-02 14:34:42'),
(5, 'CHK-BP-001-05', 'PROD-BP-001', NULL, 'Ink Color Consistency', 'Color must match Pantone standard within tolerance', 25, 2.00, 'visual', 5, 1, '2025-11-02 14:34:42', '2025-11-02 14:34:42'),
(6, 'CHK-BP-002-01', 'PROD-BP-002', NULL, 'Visual Inspection - Body Defects', 'Check for cracks, scratches, discoloration on pen body', 50, 2.50, 'visual', 1, 1, '2025-11-02 14:34:42', '2025-11-02 14:34:42'),
(7, 'CHK-BP-002-02', 'PROD-BP-002', NULL, 'Ink Flow Test', 'Write 10 meters continuously without skipping', 20, 1.50, 'functional', 2, 1, '2025-11-02 14:34:42', '2025-11-02 14:34:42'),
(8, 'CHK-BP-002-03', 'PROD-BP-002', NULL, 'Cap Fit Test', 'Cap must fit snugly without wobbling', 30, 2.00, 'functional', 3, 1, '2025-11-02 14:34:42', '2025-11-02 14:34:42'),
(9, 'CHK-BP-002-04', 'PROD-BP-002', NULL, 'Red Ink Color Match', 'Color must match approved red standard', 25, 1.50, 'visual', 4, 1, '2025-11-02 14:34:42', '2025-11-02 14:34:42');

-- --------------------------------------------------------

--
-- Table structure for table `qc_config`
--

DROP TABLE IF EXISTS `qc_config`;
CREATE TABLE `qc_config` (
  `id` int(10) UNSIGNED NOT NULL,
  `config_key` varchar(100) NOT NULL,
  `config_value` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='QC configuration settings';

--
-- Dumping data for table `qc_config`
--

INSERT INTO `qc_config` (`id`, `config_key`, `config_value`, `description`, `updated_at`) VALUES
(1, 'QC_AQL_DEFAULT', '2.5', 'Default Acceptance Quality Limit (%)', '2025-11-02 14:29:41'),
(2, 'QC_NEAR_THRESHOLD_MARGIN', '5', 'Margin for near-threshold warning (%)', '2025-11-02 14:29:41'),
(3, 'QC_MAX_UPLOAD_SIZE', '10485760', 'Max upload file size in bytes (10MB)', '2025-11-02 14:29:41'),
(4, 'QC_ALLOWED_MIME_TYPES', 'image/jpeg,image/png,image/gif,video/mp4,video/quicktime', 'Allowed attachment MIME types', '2025-11-02 14:29:41');

-- --------------------------------------------------------

--
-- Table structure for table `qc_decisions`
--

DROP TABLE IF EXISTS `qc_decisions`;
CREATE TABLE `qc_decisions` (
  `id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL COMMENT 'FK to qc_sessions.id',
  `result` enum('APPROVE','REJECT') NOT NULL COMMENT 'Final decision',
  `aql` decimal(5,2) DEFAULT NULL COMMENT 'Acceptance Quality Limit used',
  `defect_rate` decimal(5,2) DEFAULT NULL COMMENT 'Calculated defect rate %',
  `reason` text DEFAULT NULL COMMENT 'Reason for decision (required for REJECT)',
  `decided_at` datetime NOT NULL,
  `decided_by` varchar(50) DEFAULT NULL COMMENT 'User code who made decision',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='QC final decisions';

-- --------------------------------------------------------

--
-- Table structure for table `qc_items`
--

DROP TABLE IF EXISTS `qc_items`;
CREATE TABLE `qc_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL COMMENT 'FK to qc_sessions.id',
  `checklist_item_code` varchar(50) NOT NULL COMMENT 'Checklist item identifier',
  `checklist_item_name` varchar(200) DEFAULT NULL COMMENT 'Item description',
  `measure_value` decimal(10,2) DEFAULT NULL COMMENT 'Measured value (if applicable)',
  `defect_code` varchar(50) DEFAULT NULL COMMENT 'Defect type code if failed',
  `defect_count` int(10) UNSIGNED DEFAULT 0 COMMENT 'Number of defects found',
  `severity` enum('MINOR','MAJOR','CRITICAL') DEFAULT NULL COMMENT 'Defect severity',
  `result` enum('PASS','FAIL') NOT NULL COMMENT 'Item inspection result',
  `note` text DEFAULT NULL COMMENT 'Inspector notes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='QC checklist item inspection results';

-- --------------------------------------------------------

--
-- Table structure for table `qc_sessions`
--

DROP TABLE IF EXISTS `qc_sessions`;
CREATE TABLE `qc_sessions` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL COMMENT 'Format: QCS-YYYYMMDD-NNNN',
  `closure_id` int(10) UNSIGNED NOT NULL COMMENT 'FK to shift_closures.id',
  `inspector_code` varchar(50) NOT NULL COMMENT 'QC inspector user code',
  `inspector_name` varchar(100) DEFAULT NULL COMMENT 'Cached inspector name',
  `started_at` datetime NOT NULL,
  `status` enum('OPEN','DECIDED') NOT NULL DEFAULT 'OPEN',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='QC inspection session records';

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `role_display_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `level` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `role_display_name`, `description`, `level`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'bod', 'Ban Giám Đốc', 'Quản trị cấp cao, phê duyệt chiến lược', 100, 1, '2025-11-02 11:41:16', '2025-11-02 11:41:16'),
(2, 'line_manager', 'Trưởng dây chuyền', 'Vận hành line, phân ca, điều phối', 70, 1, '2025-11-02 11:41:16', '2025-11-02 11:41:16'),
(3, 'warehouse_staff', 'Nhân viên Kho', 'Quản lý NVL & thành phẩm', 50, 1, '2025-11-02 11:41:16', '2025-11-02 11:41:16'),
(4, 'system_admin', 'Quản trị viên Hệ thống', 'Quản lý tài khoản & phân quyền', 90, 1, '2025-11-02 11:41:16', '2025-11-02 11:41:16'),
(5, 'qc_staff', 'Nhân viên QC', 'Kiểm soát chất lượng', 60, 1, '2025-11-02 11:41:16', '2025-11-02 11:41:16'),
(6, 'technical_staff', 'Nhân viên Kỹ thuật', 'Bảo trì & xử lý sự cố', 60, 1, '2025-11-02 11:41:16', '2025-11-02 11:41:16'),
(7, 'worker', 'Công nhân Sản xuất', 'Thực hiện ca sản xuất', 10, 1, '2025-11-02 11:41:16', '2025-11-02 11:41:16');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `created_at`) VALUES
(1, 1, 33, '2025-11-02 11:41:59'),
(2, 1, 28, '2025-11-02 11:41:59'),
(3, 1, 2, '2025-11-02 11:41:59'),
(4, 1, 4, '2025-11-02 11:41:59'),
(5, 1, 3, '2025-11-02 11:41:59'),
(6, 1, 5, '2025-11-02 11:41:59'),
(7, 1, 1, '2025-11-02 11:41:59'),
(8, 1, 19, '2025-11-02 11:41:59'),
(9, 1, 16, '2025-11-02 11:41:59'),
(10, 1, 18, '2025-11-02 11:41:59'),
(11, 1, 17, '2025-11-02 11:41:59'),
(12, 1, 21, '2025-11-02 11:41:59'),
(13, 1, 20, '2025-11-02 11:41:59'),
(14, 1, 15, '2025-11-02 11:41:59'),
(15, 1, 11, '2025-11-02 11:41:59'),
(16, 1, 13, '2025-11-02 11:41:59'),
(17, 1, 12, '2025-11-02 11:41:59'),
(18, 1, 10, '2025-11-02 11:41:59'),
(19, 1, 7, '2025-11-02 11:41:59'),
(20, 1, 9, '2025-11-02 11:41:59'),
(21, 1, 8, '2025-11-02 11:41:59'),
(22, 1, 14, '2025-11-02 11:41:59'),
(23, 1, 6, '2025-11-02 11:41:59'),
(24, 1, 26, '2025-11-02 11:41:59'),
(25, 1, 23, '2025-11-02 11:41:59'),
(26, 1, 24, '2025-11-02 11:41:59'),
(27, 1, 27, '2025-11-02 11:41:59'),
(28, 1, 22, '2025-11-02 11:41:59'),
(32, 1, 44, '2025-11-02 11:41:59'),
(33, 1, 41, '2025-11-02 11:41:59'),
(34, 1, 39, '2025-11-02 11:41:59'),
(35, 1, 35, '2025-11-02 11:41:59'),
(36, 1, 40, '2025-11-02 11:41:59'),
(37, 1, 34, '2025-11-02 11:41:59'),
(39, 1, 55, '2025-11-02 11:41:59'),
(40, 1, 50, '2025-11-02 11:41:59'),
(41, 1, 45, '2025-11-02 11:41:59'),
(42, 1, 66, '2025-11-02 11:41:59'),
(43, 1, 64, '2025-11-02 11:41:59'),
(44, 1, 65, '2025-11-02 11:41:59'),
(45, 1, 59, '2025-11-02 11:41:59'),
(46, 1, 68, '2025-11-02 11:41:59'),
(47, 1, 76, '2025-11-02 11:41:59'),
(49, 1, 142, '2025-11-02 11:41:59'),
(50, 1, 143, '2025-11-02 11:41:59'),
(51, 1, 144, '2025-11-02 11:41:59'),
(52, 1, 145, '2025-11-02 11:41:59'),
(53, 1, 146, '2025-11-02 11:41:59'),
(54, 1, 147, '2025-11-02 11:41:59'),
(55, 1, 148, '2025-11-02 11:41:59'),
(56, 1, 149, '2025-11-02 11:41:59'),
(57, 1, 150, '2025-11-02 11:41:59'),
(58, 1, 151, '2025-11-02 11:41:59'),
(59, 1, 152, '2025-11-02 11:41:59'),
(60, 1, 153, '2025-11-02 11:41:59'),
(61, 1, 154, '2025-11-02 11:41:59'),
(62, 1, 155, '2025-11-02 11:41:59'),
(63, 1, 156, '2025-11-02 11:41:59'),
(64, 2, 139, '2025-11-02 11:41:59'),
(65, 2, 141, '2025-11-02 11:41:59'),
(66, 2, 137, '2025-11-02 11:41:59'),
(67, 2, 77, '2025-11-02 11:41:59'),
(68, 2, 78, '2025-11-02 11:41:59'),
(69, 2, 79, '2025-11-02 11:41:59'),
(70, 2, 80, '2025-11-02 11:41:59'),
(71, 2, 81, '2025-11-02 11:41:59'),
(72, 2, 82, '2025-11-02 11:41:59'),
(73, 2, 83, '2025-11-02 11:41:59'),
(74, 2, 84, '2025-11-02 11:41:59'),
(75, 2, 85, '2025-11-02 11:41:59'),
(76, 2, 86, '2025-11-02 11:41:59'),
(77, 2, 87, '2025-11-02 11:41:59'),
(82, 2, 42, '2025-11-02 11:41:59'),
(83, 2, 43, '2025-11-02 11:41:59'),
(84, 2, 41, '2025-11-02 11:41:59'),
(85, 2, 35, '2025-11-02 11:41:59'),
(86, 2, 36, '2025-11-02 11:41:59'),
(87, 2, 38, '2025-11-02 11:41:59'),
(88, 2, 34, '2025-11-02 11:41:59'),
(89, 2, 45, '2025-11-02 11:41:59'),
(90, 2, 47, '2025-11-02 11:41:59'),
(91, 2, 48, '2025-11-02 11:41:59'),
(92, 2, 49, '2025-11-02 11:41:59'),
(93, 2, 50, '2025-11-02 11:41:59'),
(94, 2, 51, '2025-11-02 11:41:59'),
(95, 2, 52, '2025-11-02 11:41:59'),
(96, 2, 53, '2025-11-02 11:41:59'),
(97, 2, 54, '2025-11-02 11:41:59'),
(98, 2, 55, '2025-11-02 11:41:59'),
(99, 2, 56, '2025-11-02 11:41:59'),
(100, 2, 57, '2025-11-02 11:41:59'),
(101, 2, 58, '2025-11-02 11:41:59'),
(104, 2, 66, '2025-11-02 11:41:59'),
(105, 2, 64, '2025-11-02 11:41:59'),
(106, 2, 65, '2025-11-02 11:41:59'),
(107, 2, 61, '2025-11-02 11:41:59'),
(108, 2, 62, '2025-11-02 11:41:59'),
(109, 2, 67, '2025-11-02 11:41:59'),
(110, 2, 59, '2025-11-02 11:41:59'),
(111, 2, 88, '2025-11-02 11:41:59'),
(112, 2, 89, '2025-11-02 11:41:59'),
(113, 2, 90, '2025-11-02 11:41:59'),
(114, 2, 91, '2025-11-02 11:41:59'),
(115, 2, 92, '2025-11-02 11:41:59'),
(116, 2, 93, '2025-11-02 11:41:59'),
(117, 2, 94, '2025-11-02 11:41:59'),
(118, 2, 95, '2025-11-02 11:41:59'),
(119, 2, 96, '2025-11-02 11:41:59'),
(126, 2, 68, '2025-11-02 11:41:59'),
(127, 2, 69, '2025-11-02 11:41:59'),
(128, 2, 70, '2025-11-02 11:41:59'),
(129, 2, 71, '2025-11-02 11:41:59'),
(130, 2, 72, '2025-11-02 11:41:59'),
(131, 2, 73, '2025-11-02 11:41:59'),
(132, 2, 74, '2025-11-02 11:41:59'),
(133, 2, 75, '2025-11-02 11:41:59'),
(134, 2, 76, '2025-11-02 11:41:59'),
(141, 2, 143, '2025-11-02 11:41:59'),
(142, 2, 153, '2025-11-02 11:41:59'),
(143, 2, 155, '2025-11-02 11:41:59'),
(144, 2, 149, '2025-11-02 11:41:59'),
(145, 2, 145, '2025-11-02 11:41:59'),
(146, 2, 150, '2025-11-02 11:41:59'),
(148, 2, 28, '2025-11-02 11:41:59'),
(149, 2, 10, '2025-11-02 11:41:59'),
(150, 2, 6, '2025-11-02 11:41:59'),
(151, 2, 22, '2025-11-02 11:41:59'),
(155, 3, 97, '2025-11-02 11:41:59'),
(156, 3, 98, '2025-11-02 11:41:59'),
(157, 3, 99, '2025-11-02 11:41:59'),
(158, 3, 100, '2025-11-02 11:41:59'),
(159, 3, 101, '2025-11-02 11:41:59'),
(160, 3, 102, '2025-11-02 11:41:59'),
(162, 3, 103, '2025-11-02 11:41:59'),
(163, 3, 104, '2025-11-02 11:41:59'),
(164, 3, 105, '2025-11-02 11:41:59'),
(165, 3, 106, '2025-11-02 11:41:59'),
(166, 3, 107, '2025-11-02 11:41:59'),
(167, 3, 108, '2025-11-02 11:41:59'),
(168, 3, 109, '2025-11-02 11:41:59'),
(169, 3, 110, '2025-11-02 11:41:59'),
(170, 3, 111, '2025-11-02 11:41:59'),
(171, 3, 112, '2025-11-02 11:41:59'),
(172, 3, 113, '2025-11-02 11:41:59'),
(173, 3, 114, '2025-11-02 11:41:59'),
(174, 3, 115, '2025-11-02 11:41:59'),
(175, 3, 116, '2025-11-02 11:41:59'),
(176, 3, 117, '2025-11-02 11:41:59'),
(177, 3, 118, '2025-11-02 11:41:59'),
(178, 3, 119, '2025-11-02 11:41:59'),
(179, 3, 120, '2025-11-02 11:41:59'),
(180, 3, 121, '2025-11-02 11:41:59'),
(181, 3, 122, '2025-11-02 11:41:59'),
(182, 3, 123, '2025-11-02 11:41:59'),
(193, 3, 28, '2025-11-02 11:41:59'),
(194, 3, 15, '2025-11-02 11:41:59'),
(195, 3, 22, '2025-11-02 11:41:59'),
(197, 3, 34, '2025-11-02 11:41:59'),
(198, 3, 45, '2025-11-02 11:41:59'),
(200, 3, 144, '2025-11-02 11:41:59'),
(201, 3, 147, '2025-11-02 11:41:59'),
(202, 3, 148, '2025-11-02 11:41:59'),
(207, 4, 157, '2025-11-02 11:41:59'),
(208, 4, 158, '2025-11-02 11:41:59'),
(209, 4, 159, '2025-11-02 11:41:59'),
(210, 4, 160, '2025-11-02 11:41:59'),
(211, 4, 161, '2025-11-02 11:41:59'),
(212, 4, 162, '2025-11-02 11:41:59'),
(213, 4, 163, '2025-11-02 11:41:59'),
(214, 4, 164, '2025-11-02 11:41:59'),
(215, 4, 165, '2025-11-02 11:41:59'),
(216, 4, 166, '2025-11-02 11:41:59'),
(222, 4, 167, '2025-11-02 11:41:59'),
(223, 4, 168, '2025-11-02 11:41:59'),
(224, 4, 169, '2025-11-02 11:41:59'),
(225, 4, 170, '2025-11-02 11:41:59'),
(226, 4, 171, '2025-11-02 11:41:59'),
(227, 4, 172, '2025-11-02 11:41:59'),
(228, 4, 173, '2025-11-02 11:41:59'),
(229, 4, 174, '2025-11-02 11:41:59'),
(237, 4, 1, '2025-11-02 11:41:59'),
(238, 4, 6, '2025-11-02 11:41:59'),
(239, 4, 10, '2025-11-02 11:41:59'),
(240, 4, 15, '2025-11-02 11:41:59'),
(241, 4, 22, '2025-11-02 11:41:59'),
(242, 4, 28, '2025-11-02 11:41:59'),
(243, 4, 34, '2025-11-02 11:41:59'),
(244, 4, 41, '2025-11-02 11:41:59'),
(245, 4, 45, '2025-11-02 11:41:59'),
(246, 4, 46, '2025-11-02 11:41:59'),
(247, 4, 50, '2025-11-02 11:41:59'),
(248, 4, 55, '2025-11-02 11:41:59'),
(249, 4, 59, '2025-11-02 11:41:59'),
(250, 4, 60, '2025-11-02 11:41:59'),
(251, 4, 64, '2025-11-02 11:41:59'),
(252, 4, 65, '2025-11-02 11:41:59'),
(253, 4, 66, '2025-11-02 11:41:59'),
(254, 4, 68, '2025-11-02 11:41:59'),
(255, 4, 76, '2025-11-02 11:41:59'),
(256, 4, 77, '2025-11-02 11:41:59'),
(257, 4, 83, '2025-11-02 11:41:59'),
(258, 4, 88, '2025-11-02 11:41:59'),
(259, 4, 97, '2025-11-02 11:41:59'),
(260, 4, 101, '2025-11-02 11:41:59'),
(261, 4, 103, '2025-11-02 11:41:59'),
(262, 4, 104, '2025-11-02 11:41:59'),
(263, 4, 108, '2025-11-02 11:41:59'),
(264, 4, 112, '2025-11-02 11:41:59'),
(265, 4, 117, '2025-11-02 11:41:59'),
(266, 4, 121, '2025-11-02 11:41:59'),
(267, 4, 122, '2025-11-02 11:41:59'),
(268, 4, 124, '2025-11-02 11:41:59'),
(269, 4, 130, '2025-11-02 11:41:59'),
(270, 4, 131, '2025-11-02 11:41:59'),
(271, 4, 134, '2025-11-02 11:41:59'),
(272, 4, 135, '2025-11-02 11:41:59'),
(273, 4, 137, '2025-11-02 11:41:59'),
(274, 4, 142, '2025-11-02 11:41:59'),
(275, 4, 143, '2025-11-02 11:41:59'),
(276, 4, 144, '2025-11-02 11:41:59'),
(277, 4, 145, '2025-11-02 11:41:59'),
(278, 4, 146, '2025-11-02 11:41:59'),
(279, 4, 147, '2025-11-02 11:41:59'),
(280, 4, 148, '2025-11-02 11:41:59'),
(281, 4, 149, '2025-11-02 11:41:59'),
(282, 4, 150, '2025-11-02 11:41:59'),
(283, 4, 151, '2025-11-02 11:41:59'),
(284, 4, 152, '2025-11-02 11:41:59'),
(285, 4, 153, '2025-11-02 11:41:59'),
(286, 4, 154, '2025-11-02 11:41:59'),
(287, 4, 155, '2025-11-02 11:41:59'),
(300, 5, 124, '2025-11-02 11:41:59'),
(301, 5, 125, '2025-11-02 11:41:59'),
(302, 5, 126, '2025-11-02 11:41:59'),
(303, 5, 127, '2025-11-02 11:41:59'),
(304, 5, 128, '2025-11-02 11:41:59'),
(305, 5, 129, '2025-11-02 11:41:59'),
(306, 5, 130, '2025-11-02 11:41:59'),
(307, 5, 131, '2025-11-02 11:41:59'),
(308, 5, 132, '2025-11-02 11:41:59'),
(309, 5, 133, '2025-11-02 11:41:59'),
(310, 5, 134, '2025-11-02 11:41:59'),
(311, 5, 135, '2025-11-02 11:41:59'),
(312, 5, 136, '2025-11-02 11:41:59'),
(315, 5, 68, '2025-11-02 11:41:59'),
(316, 5, 76, '2025-11-02 11:41:59'),
(318, 5, 116, '2025-11-02 11:41:59'),
(319, 5, 112, '2025-11-02 11:41:59'),
(321, 5, 10, '2025-11-02 11:41:59'),
(322, 5, 6, '2025-11-02 11:41:59'),
(323, 5, 22, '2025-11-02 11:41:59'),
(324, 5, 153, '2025-11-02 11:41:59'),
(325, 5, 146, '2025-11-02 11:41:59'),
(327, 6, 95, '2025-11-02 11:41:59'),
(328, 6, 96, '2025-11-02 11:41:59'),
(329, 6, 94, '2025-11-02 11:41:59'),
(330, 6, 93, '2025-11-02 11:41:59'),
(331, 6, 88, '2025-11-02 11:41:59'),
(334, 6, 87, '2025-11-02 11:41:59'),
(335, 6, 84, '2025-11-02 11:41:59'),
(336, 6, 86, '2025-11-02 11:41:59'),
(337, 6, 85, '2025-11-02 11:41:59'),
(338, 6, 83, '2025-11-02 11:41:59'),
(339, 6, 82, '2025-11-02 11:41:59'),
(340, 6, 81, '2025-11-02 11:41:59'),
(341, 6, 77, '2025-11-02 11:41:59'),
(349, 6, 32, '2025-11-02 11:41:59'),
(350, 6, 28, '2025-11-02 11:41:59'),
(352, 6, 55, '2025-11-02 11:41:59'),
(353, 6, 34, '2025-11-02 11:41:59'),
(354, 6, 45, '2025-11-02 11:41:59'),
(355, 6, 155, '2025-11-02 11:41:59'),
(356, 6, 154, '2025-11-02 11:41:59'),
(358, 7, 54, '2025-11-02 11:41:59'),
(359, 7, 50, '2025-11-02 11:41:59'),
(360, 7, 46, '2025-11-02 11:41:59'),
(361, 7, 60, '2025-11-02 11:41:59'),
(362, 7, 89, '2025-11-02 11:41:59'),
(363, 7, 10, '2025-11-02 11:41:59'),
(364, 7, 6, '2025-11-02 11:41:59'),
(365, 7, 22, '2025-11-02 11:41:59');

-- --------------------------------------------------------

--
-- Table structure for table `shiftment`
--

DROP TABLE IF EXISTS `shiftment`;
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
-- Table structure for table `shift_closures`
--

DROP TABLE IF EXISTS `shift_closures`;
CREATE TABLE `shift_closures` (
  `closure_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `closure_code` varchar(50) NOT NULL,
  `closure_date` datetime NOT NULL DEFAULT current_timestamp(),
  `closed_by` int(11) NOT NULL,
  `total_target` int(11) NOT NULL DEFAULT 0,
  `total_produced` int(11) NOT NULL DEFAULT 0,
  `total_good` int(11) NOT NULL DEFAULT 0,
  `total_defect` int(11) NOT NULL DEFAULT 0,
  `total_downtime` int(11) NOT NULL DEFAULT 0,
  `efficiency_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `defect_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `has_warnings` tinyint(1) NOT NULL DEFAULT 0,
  `warning_details` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `confirmed_quantities` text DEFAULT NULL,
  `status` enum('draft','confirmed','cancelled') NOT NULL DEFAULT 'draft',
  `warehouse_request_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shift_closures`
--

INSERT INTO `shift_closures` (`closure_id`, `shift_id`, `closure_code`, `closure_date`, `closed_by`, `total_target`, `total_produced`, `total_good`, `total_defect`, `total_downtime`, `efficiency_rate`, `defect_rate`, `has_warnings`, `warning_details`, `notes`, `confirmed_quantities`, `status`, `warehouse_request_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'SC-20251219-001', '2025-12-19 00:44:44', 2, 401, 344, 335, 9, 26, 85.79, 2.62, 0, NULL, '', '{\"1\":{\"good\":166,\"defect\":3},\"2\":{\"good\":52,\"defect\":2},\"3\":{\"good\":117,\"defect\":4}}', 'confirmed', 1, '2025-12-18 17:44:44', '2025-12-18 17:44:44'),
(2, 3, 'SC-20251219-002', '2025-12-19 00:57:02', 2, 369, 321, 308, 13, 13, 86.99, 4.05, 1, '[{\"type\":\"warning\",\"machine_id\":\"5\",\"machine_name\":\"M\\u00e1y \\u0111\\u00f3ng g\\u00f3i t\\u1ef1 \\u0111\\u1ed9ng\",\"message\":\"T\\u1ef7 l\\u1ec7 ph\\u1ebf ph\\u1ea9m cao: 5.13% (>= 5%)\",\"defect_rate\":\"5.13\"}]', '', '{\"5\":{\"good\":188,\"defect\":9},\"6\":{\"good\":120,\"defect\":4}}', 'confirmed', 2, '2025-12-18 17:57:02', '2025-12-18 17:57:02');

-- --------------------------------------------------------

--
-- Table structure for table `shift_closure_defects`
--

DROP TABLE IF EXISTS `shift_closure_defects`;
CREATE TABLE `shift_closure_defects` (
  `id` int(11) NOT NULL,
  `closure_machine_id` int(11) NOT NULL,
  `reason_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shift_closure_machines`
--

DROP TABLE IF EXISTS `shift_closure_machines`;
CREATE TABLE `shift_closure_machines` (
  `id` int(11) NOT NULL,
  `closure_id` int(11) NOT NULL,
  `machine_id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `target_count` int(11) NOT NULL DEFAULT 0,
  `produced_count` int(11) NOT NULL DEFAULT 0,
  `good_count` int(11) NOT NULL DEFAULT 0,
  `defect_count` int(11) NOT NULL DEFAULT 0,
  `downtime_minutes` int(11) NOT NULL DEFAULT 0,
  `efficiency_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `defect_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `confirmed_good` int(11) DEFAULT NULL,
  `confirmed_defect` int(11) DEFAULT NULL,
  `defect_details` text DEFAULT NULL,
  `downtime_details` text DEFAULT NULL,
  `is_warning` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shift_closure_machines`
--

INSERT INTO `shift_closure_machines` (`id`, `closure_id`, `machine_id`, `staff_id`, `target_count`, `produced_count`, `good_count`, `defect_count`, `downtime_minutes`, `efficiency_rate`, `defect_rate`, `confirmed_good`, `confirmed_defect`, `defect_details`, `downtime_details`, `is_warning`, `notes`, `created_at`) VALUES
(1, 1, 1, 8, 199, 169, 166, 3, 26, 84.92, 2.40, 166, 3, NULL, '[{\"downtime_minutes\":\"26\",\"downtime_reason\":\"Nguy\\u00ean li\\u1ec7u l\\u1ed7i\",\"timestamp\":\"2025-12-18 23:34:32\"}]', 0, NULL, '2025-12-18 17:44:44'),
(2, 1, 2, 8, 62, 54, 52, 2, 0, 87.10, 4.75, 52, 2, NULL, NULL, 0, NULL, '2025-12-18 17:44:44'),
(3, 1, 3, 8, 140, 121, 117, 4, 0, 86.43, 3.70, 117, 4, NULL, NULL, 0, NULL, '2025-12-18 17:44:44'),
(4, 2, 5, 8, 225, 197, 188, 9, 13, 87.56, 5.13, 188, 9, NULL, '[{\"downtime_minutes\":\"13\",\"downtime_reason\":\"V\\u1ea5n \\u0111\\u1ec1 ch\\u1ea5t l\\u01b0\\u1ee3ng\",\"timestamp\":\"2025-12-19 00:56:37\"}]', 1, NULL, '2025-12-18 17:57:02'),
(5, 2, 6, 6, 144, 124, 120, 4, 0, 86.11, 3.75, 120, 4, NULL, NULL, 0, NULL, '2025-12-18 17:57:02');

-- --------------------------------------------------------

--
-- Table structure for table `shift_machine_staff`
--

DROP TABLE IF EXISTS `shift_machine_staff`;
CREATE TABLE `shift_machine_staff` (
  `id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL COMMENT 'ID ca làm việc',
  `machine_id` int(11) NOT NULL COMMENT 'ID máy (từ bảng machines)',
  `staff_id` int(11) NOT NULL COMMENT 'ID nhân viên',
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Thời điểm phân công',
  `assigned_by` int(11) DEFAULT NULL COMMENT 'User ID người phân công',
  `status` tinyint(2) DEFAULT 1 COMMENT '1=Active, 0=Removed',
  `notes` text DEFAULT NULL COMMENT 'Ghi chú'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Phân công nhân sự vào máy cụ thể trong ca (máy đã cố định theo dây chuyền)';

--
-- Dumping data for table `shift_machine_staff`
--

INSERT INTO `shift_machine_staff` (`id`, `shift_id`, `machine_id`, `staff_id`, `assigned_at`, `assigned_by`, `status`, `notes`) VALUES
(1, 1, 3, 8, '2025-12-18 13:09:30', 2, 1, ''),
(2, 1, 1, 8, '2025-12-18 14:54:27', 2, 1, ''),
(3, 1, 2, 8, '2025-12-18 14:54:33', 2, 1, ''),
(4, 3, 5, 8, '2025-12-18 17:47:28', 2, 1, ''),
(5, 3, 6, 6, '2025-12-18 17:56:13', 2, 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `simulator_settings`
--

DROP TABLE IF EXISTS `simulator_settings`;
CREATE TABLE `simulator_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL COMMENT 'Khóa cấu hình',
  `setting_value` text NOT NULL COMMENT 'Giá trị cấu hình (JSON hoặc text)',
  `setting_type` enum('boolean','integer','float','string','json') NOT NULL DEFAULT 'string' COMMENT 'Kiểu dữ liệu',
  `description` varchar(255) DEFAULT NULL COMMENT 'Mô tả cấu hình',
  `updated_by` varchar(50) DEFAULT NULL COMMENT 'Người cập nhật cuối',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng cấu hình cho production simulator';

--
-- Dumping data for table `simulator_settings`
--

INSERT INTO `simulator_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `updated_by`, `updated_at`) VALUES
(1, 'simulator_enabled', '0', 'boolean', 'Bật/tắt simulator (0=Off, 1=On)', 'leader', '2025-12-18 17:56:42'),
(2, 'simulator_interval', '60', 'integer', 'Khoảng thời gian ghi nhận (giây)', 'leader', '2025-12-18 16:32:58'),
(3, 'good_count_min', '50', 'integer', 'Số lượng thành phẩm tối thiểu mỗi lần ghi', 'leader', '2025-12-18 16:32:58'),
(4, 'good_count_max', '200', 'integer', 'Số lượng thành phẩm tối đa mỗi lần ghi', 'leader', '2025-12-18 16:32:58'),
(5, 'defect_rate_min', '1', 'float', 'Tỷ lệ phế phẩm tối thiểu (%)', 'leader', '2025-12-18 16:32:58'),
(6, 'defect_rate_max', '8', 'float', 'Tỷ lệ phế phẩm tối đa (%)', 'leader', '2025-12-18 16:32:58'),
(7, 'downtime_probability', '0.15', 'float', 'Xác suất xảy ra downtime (0-1)', 'leader', '2025-12-18 16:32:58'),
(8, 'downtime_min', '5', 'integer', 'Thời gian downtime tối thiểu (phút)', 'leader', '2025-12-18 16:32:58'),
(9, 'downtime_max', '30', 'integer', 'Thời gian downtime tối đa (phút)', 'leader', '2025-12-18 16:32:58'),
(10, 'target_multiplier', '1.2', 'float', 'Hệ số nhân cho target (target = good_count * multiplier)', 'leader', '2025-12-18 16:32:58'),
(11, 'simulate_active_shifts_only', '0', 'boolean', 'Chỉ giả lập cho ca đang chạy (1=Yes, 0=No)', 'leader', '2025-12-18 16:32:58');

-- --------------------------------------------------------

--
-- Table structure for table `sorting_report`
--

DROP TABLE IF EXISTS `sorting_report`;
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

DROP TABLE IF EXISTS `staff`;
CREATE TABLE `staff` (
  `id_staff` int(11) NOT NULL,
  `staff_name` varchar(50) NOT NULL,
  `phone` int(15) NOT NULL,
  `email` varchar(25) NOT NULL,
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
(1001, 'Leader1', 8212312, 'leader1@mail.com', 'Sản Xuất', 'Leader', 2, '2025-12-11 15:09:49', '2025-12-12 08:21:14'),
(1002, 'Leader2', 8923321, 'leader2@mail.com', 'Sản Xuất', 'Leader', 1, '2025-12-11 15:09:49', '2025-12-12 08:21:14'),
(1003, 'Administrator', 0, '', 'IT', 'Administrator', 1, '2025-11-01 08:49:53', '2025-12-12 01:21:14'),
(1004, 'Trưởng dây chuyền', 0, '', 'Sản Xuất', 'Trưởng Dây Chuyền', 1, '2025-11-01 08:49:53', '2025-12-12 01:21:14'),
(1005, 'Nguyễn Văn A - Giám Đốc', 0, 'bod@company.com', 'Ban Giám Đốc', 'Giám Đốc', 1, '2025-11-01 08:53:44', '2025-12-12 01:21:14'),
(1006, 'Trần Văn B - Trưởng line 2', 0, 'linemanager@company.com', 'Sản Xuất', 'Trưởng Dây Chuyền', 1, '2025-11-01 08:53:45', '2025-12-12 01:21:14'),
(1007, 'Lê Thị C - Nhân viên kho', 0, 'warehouse@company.com', 'Kho', 'Nhân Viên Kho', 1, '2025-11-01 08:53:45', '2025-12-12 01:21:14'),
(1008, 'Phạm Văn D - Nhân viên QC', 0, 'qc@company.com', 'QC', 'Nhân Viên QC', 1, '2025-11-01 08:53:45', '2025-12-12 01:21:14'),
(1009, 'Hoàng Văn E - Kỹ thuật viên', 0, 'technical@company.com', 'Kỹ Thuật', 'Kỹ Thuật Viên', 1, '2025-11-01 08:53:45', '2025-12-12 01:21:14'),
(1010, 'Nguyễn Thị F - Công nhân', 0, 'worker@company.com', 'Sản Xuất', 'Công Nhân', 1, '2025-11-01 08:53:45', '2025-12-12 01:21:14'),
(1011, 'công ', 2147483647, 'danh12345@gmail.com', 'Chưa Phân Loại', 'Chưa Phân Loại', 1, '2025-12-01 09:02:29', '2025-12-12 01:21:14'),
(1012, 'anh', 2147483647, 'danh@gmail.com', 'Chưa Phân Loại', 'Chưa Phân Loại', 1, '2025-12-03 01:55:13', '2025-12-12 01:21:14'),
(1013, 'cong danh', 0, 'danh66667@gmail.com', 'Chưa Phân Loại', 'Chưa Phân Loại', 1, '2025-12-07 07:38:47', '2025-12-12 01:21:14'),
(1033, 'danh', 9769857, 'danh77656@gmail.com', 'Chưa Phân Loại', 'Chưa Phân Loại', 1, '2025-12-03 01:45:13', '2025-12-12 01:21:14');

-- --------------------------------------------------------

--
-- Table structure for table `system_config`
--

DROP TABLE IF EXISTS `system_config`;
CREATE TABLE `system_config` (
  `config_key` varchar(100) NOT NULL,
  `config_value` text NOT NULL,
  `config_type` enum('string','number','boolean','json') NOT NULL DEFAULT 'string',
  `description` text DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cấu hình hệ thống';

--
-- Dumping data for table `system_config`
--

INSERT INTO `system_config` (`config_key`, `config_value`, `config_type`, `description`, `updated_by`, `updated_at`) VALUES
('closure_auto_save_interval', '300', 'number', 'Tự động lưu nháp phiếu chốt ca sau mỗi X giây', NULL, '2025-12-18 17:00:06'),
('defect_rate_critical_threshold', '10.0', 'number', 'Ngưỡng nghiêm trọng tỷ lệ phế phẩm (%). Nếu vượt sẽ yêu cầu giải trình', NULL, '2025-12-18 17:37:01'),
('defect_rate_warning_threshold', '5.0', 'number', 'Ngưỡng cảnh báo tỷ lệ phế phẩm (%). Nếu vượt sẽ hiển thị cảnh báo khi chốt ca', NULL, '2025-12-18 17:37:01'),
('efficiency_warning_threshold', '70.0', 'number', 'Ngưỡng cảnh báo hiệu suất thấp (%). Dưới mức này sẽ cảnh báo', NULL, '2025-12-18 17:37:01'),
('warehouse_request_auto_generate', 'true', 'boolean', 'Tự động tạo đề nghị nhập kho khi chốt ca', NULL, '2025-12-18 17:00:06');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(11) NOT NULL,
  `password` varchar(11) NOT NULL,
  `temp_password` varchar(50) DEFAULT NULL COMMENT 'Mật khẩu tạm (plaintext) sau reset, NULL khi đã đổi',
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=Bắt buộc đổi password lần đầu, 0=Bình thường',
  `role_id` int(11) NOT NULL COMMENT 'ID vai trò (bắt buộc)',
  `staff_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `temp_password`, `must_change_password`, `role_id`, `staff_id`, `full_name`, `email`, `phone`, `is_active`, `last_login`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin', NULL, 0, 4, 1003, 'Administrator', NULL, NULL, 1, '2025-12-19 01:50:08', NULL, '2025-11-01 15:49:53', '2025-12-19 01:50:08'),
(2, 'leader', 'leader', NULL, 0, 2, 1004, 'Trưởng dây chuyền', NULL, NULL, 1, '2025-12-18 19:07:12', NULL, '2025-11-01 15:49:53', '2025-12-18 19:07:12'),
(3, 'bod', 'bod123', NULL, 0, 1, 1005, 'Nguyễn Văn A - Giám Đốc', 'bod@company.com', NULL, 1, '2025-12-18 19:15:46', NULL, '2025-11-01 15:53:44', '2025-12-18 19:15:46'),
(4, 'line_manage', 'line123', NULL, 0, 2, 1006, 'Trần Văn B - Trưởng line 2', 'linemanager@company.com', NULL, 1, NULL, NULL, '2025-11-01 15:53:45', '2025-12-11 15:22:31'),
(5, 'warehouse', 'wh123', NULL, 0, 3, 1007, 'Lê Thị C - Nhân viên kho', 'warehouse@company.com', NULL, 1, '2025-12-19 02:05:55', NULL, '2025-11-01 15:53:45', '2025-12-19 02:05:55'),
(6, 'qc', 'qc123', NULL, 0, 5, 1008, 'Phạm Văn D - Nhân viên QC', 'qc@company.com', NULL, 1, '2025-12-19 02:05:36', NULL, '2025-11-01 15:53:45', '2025-12-19 02:05:36'),
(7, 'technical', 'tech123', NULL, 0, 6, 1009, 'Hoàng Văn E - Kỹ thuật viên', 'technical@company.com', NULL, 1, NULL, NULL, '2025-11-01 15:53:45', '2025-12-11 15:22:31'),
(8, 'Le Van A', 'worker123', NULL, 0, 7, 1010, 'Nguyễn Thị F - Công nhân', 'worker@company.com', NULL, 1, '2025-12-19 02:12:59', NULL, '2025-11-01 15:53:45', '2025-12-19 02:12:59');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_machine_status`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `v_machine_status`;
CREATE TABLE `v_machine_status` (
`id_machine` int(50)
,`machine_name` varchar(50)
,`capacity_display` varchar(23)
,`status_name` varchar(14)
,`mc_status` int(25)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_material_stock`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `v_material_stock`;
CREATE TABLE `v_material_stock` (
`id_material` int(50)
,`material_name` varchar(50)
,`stock_display` varchar(55)
,`stock` int(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_production_summary`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `v_production_summary`;
CREATE TABLE `v_production_summary` (
`shift_id` int(11)
,`machine_id` int(11)
,`machine_code` varchar(20)
,`machine_name` varchar(100)
,`shift_name` varchar(100)
,`shift_date` date
,`start_time` time
,`end_time` time
,`record_count` bigint(21)
,`total_good` decimal(32,0)
,`total_defect` decimal(32,0)
,`total_produced` decimal(33,0)
,`avg_target` decimal(14,4)
,`total_downtime_minutes` decimal(32,0)
,`avg_efficiency` decimal(9,6)
,`avg_defect_rate` decimal(9,6)
,`first_record_time` datetime
,`last_record_time` datetime
,`has_simulated_data` tinyint(1)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_project_details`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `v_project_details`;
CREATE TABLE `v_project_details` (
`id_project` int(25)
,`project_name` varchar(50)
,`cust_name` varchar(50)
,`product_name` varchar(50)
,`diameter_display` varchar(12)
,`qty_request_display` varchar(19)
,`entry_date` date
,`pr_status` int(5)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_shift_closure_summary`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `v_shift_closure_summary`;
CREATE TABLE `v_shift_closure_summary` (
`closure_id` int(11)
,`closure_code` varchar(50)
,`closure_date` datetime
,`status` enum('draft','confirmed','cancelled')
,`shift_name` varchar(100)
,`shift_date` date
,`closed_by_name` varchar(11)
,`total_target` int(11)
,`total_good` int(11)
,`total_defect` int(11)
,`efficiency_rate` decimal(5,2)
,`defect_rate` decimal(5,2)
,`total_machines` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_import_requests`
--

DROP TABLE IF EXISTS `warehouse_import_requests`;
CREATE TABLE `warehouse_import_requests` (
  `request_id` int(11) NOT NULL,
  `request_code` varchar(50) NOT NULL,
  `closure_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_code` varchar(100) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit` varchar(50) NOT NULL DEFAULT 'cái',
  `status` enum('pending_qc','qc_approved','qc_rejected','imported','cancelled') NOT NULL DEFAULT 'pending_qc',
  `qc_by` int(11) DEFAULT NULL,
  `qc_date` datetime DEFAULT NULL,
  `qc_notes` text DEFAULT NULL,
  `qc_approved_quantity` int(11) DEFAULT NULL,
  `qc_rejected_quantity` int(11) DEFAULT NULL,
  `imported_by` int(11) DEFAULT NULL,
  `imported_date` datetime DEFAULT NULL,
  `warehouse_location` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouse_import_requests`
--

INSERT INTO `warehouse_import_requests` (`request_id`, `request_code`, `closure_id`, `shift_id`, `product_id`, `product_name`, `product_code`, `quantity`, `unit`, `status`, `qc_by`, `qc_date`, `qc_notes`, `qc_approved_quantity`, `qc_rejected_quantity`, `imported_by`, `imported_date`, `warehouse_location`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'WIR-20251219-001', 1, 1, NULL, 'Ca Sáng - Dây chuyền 1', NULL, 335, 'cái', 'pending_qc', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Đề nghị nhập kho tự động từ phiếu chốt ca', 2, '2025-12-18 17:44:44', '2025-12-18 17:44:44'),
(2, 'WIR-20251219-002', 2, 3, NULL, 'Ca Sáng - Dây chuyền 2', NULL, 308, 'cái', 'pending_qc', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Đề nghị nhập kho tự động từ phiếu chốt ca', 2, '2025-12-18 17:57:02', '2025-12-18 17:57:02');

-- --------------------------------------------------------

--
-- Table structure for table `zones`
--

DROP TABLE IF EXISTS `zones`;
CREATE TABLE `zones` (
  `zone_id` int(11) NOT NULL,
  `zone_code` varchar(50) NOT NULL COMMENT 'Mã khu: ZONE_A, ZONE_B...',
  `zone_name` varchar(100) NOT NULL COMMENT 'Tên khu vực: Khu A, Khu B...',
  `description` text DEFAULT NULL COMMENT 'Mô tả khu vực',
  `floor` varchar(50) DEFAULT NULL COMMENT 'Tầng',
  `building` varchar(50) DEFAULT NULL COMMENT 'Tòa nhà',
  `status` tinyint(2) NOT NULL DEFAULT 1 COMMENT '1=Hoạt động, 0=Ngừng hoạt động',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng khu vực sản xuất';

--
-- Dumping data for table `zones`
--

INSERT INTO `zones` (`zone_id`, `zone_code`, `zone_name`, `description`, `floor`, `building`, `status`, `created_at`, `updated_at`) VALUES
(1, 'ZONE_A', 'Khu A', 'Khu sản xuất chính - Đang hoạt động', 'Tầng 1', 'Nhà máy A', 1, '2025-12-16 14:05:41', '2025-12-18 13:54:35'),
(2, 'ZONE_B', 'Khu B', 'Khu dự trữ - Chờ mở rộng sản xuất', 'Tầng 1', 'Nhà máy A', 0, '2025-12-16 14:05:41', '2025-12-18 13:54:35'),
(3, 'ZONE_C', 'Khu C', 'Khu dự trữ - Chờ mở rộng sản xuất', 'Tầng 2', 'Nhà máy A', 0, '2025-12-16 14:05:41', '2025-12-18 13:54:35');

-- --------------------------------------------------------

--
-- Structure for view `v_machine_status`
--
DROP TABLE IF EXISTS `v_machine_status`;

DROP VIEW IF EXISTS `v_machine_status`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_machine_status`  AS SELECT `machine`.`id_machine` AS `id_machine`, `machine`.`machine_name` AS `machine_name`, concat(`machine`.`capacity`,' cái/giờ') AS `capacity_display`, CASE `machine`.`mc_status` WHEN 1 THEN 'Sẵn sàng' WHEN 2 THEN 'Đang sử dụng' WHEN 3 THEN 'Sự cố' WHEN 4 THEN 'Bảo trì' ELSE 'Không xác định' END AS `status_name`, `machine`.`mc_status` AS `mc_status` FROM `machine` ;

-- --------------------------------------------------------

--
-- Structure for view `v_material_stock`
--
DROP TABLE IF EXISTS `v_material_stock`;

DROP VIEW IF EXISTS `v_material_stock`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_material_stock`  AS SELECT `material`.`id_material` AS `id_material`, `material`.`material_name` AS `material_name`, concat(`material`.`stock`,' gram') AS `stock_display`, `material`.`stock` AS `stock` FROM `material` ;

-- --------------------------------------------------------

--
-- Structure for view `v_production_summary`
--
DROP TABLE IF EXISTS `v_production_summary`;

DROP VIEW IF EXISTS `v_production_summary`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_production_summary`  AS SELECT `pr`.`shift_id` AS `shift_id`, `pr`.`machine_id` AS `machine_id`, `m`.`code` AS `machine_code`, `m`.`name` AS `machine_name`, `ps`.`shift_name` AS `shift_name`, `ps`.`shift_date` AS `shift_date`, `ps`.`start_time` AS `start_time`, `ps`.`end_time` AS `end_time`, count(`pr`.`id`) AS `record_count`, sum(`pr`.`good_count`) AS `total_good`, sum(`pr`.`defect_count`) AS `total_defect`, sum(`pr`.`good_count` + `pr`.`defect_count`) AS `total_produced`, avg(`pr`.`target_count`) AS `avg_target`, sum(`pr`.`downtime_minutes`) AS `total_downtime_minutes`, avg(`pr`.`efficiency_rate`) AS `avg_efficiency`, avg(`pr`.`defect_rate`) AS `avg_defect_rate`, min(`pr`.`timestamp`) AS `first_record_time`, max(`pr`.`timestamp`) AS `last_record_time`, max(`pr`.`is_simulated`) AS `has_simulated_data` FROM ((`production_records` `pr` join `machines` `m` on(`pr`.`machine_id` = `m`.`id`)) join `production_shifts` `ps` on(`pr`.`shift_id` = `ps`.`shift_id`)) GROUP BY `pr`.`shift_id`, `pr`.`machine_id` ;

-- --------------------------------------------------------

--
-- Structure for view `v_project_details`
--
DROP TABLE IF EXISTS `v_project_details`;

DROP VIEW IF EXISTS `v_project_details`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_project_details`  AS SELECT `p`.`id_project` AS `id_project`, `p`.`project_name` AS `project_name`, `c`.`cust_name` AS `cust_name`, `pr`.`product_name` AS `product_name`, concat(`p`.`diameter` / 10,' mm') AS `diameter_display`, concat(`p`.`qty_request`,' cái') AS `qty_request_display`, `p`.`entry_date` AS `entry_date`, `p`.`pr_status` AS `pr_status` FROM ((`project` `p` left join `customer` `c` on(`p`.`id_cust` = `c`.`id_cust`)) left join `product` `pr` on(`p`.`id_product` = `pr`.`id_product`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_shift_closure_summary`
--
DROP TABLE IF EXISTS `v_shift_closure_summary`;

DROP VIEW IF EXISTS `v_shift_closure_summary`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_shift_closure_summary`  AS SELECT `sc`.`closure_id` AS `closure_id`, `sc`.`closure_code` AS `closure_code`, `sc`.`closure_date` AS `closure_date`, `sc`.`status` AS `status`, `ps`.`shift_name` AS `shift_name`, `ps`.`shift_date` AS `shift_date`, `u`.`username` AS `closed_by_name`, `sc`.`total_target` AS `total_target`, `sc`.`total_good` AS `total_good`, `sc`.`total_defect` AS `total_defect`, `sc`.`efficiency_rate` AS `efficiency_rate`, `sc`.`defect_rate` AS `defect_rate`, count(`scm`.`id`) AS `total_machines` FROM (((`shift_closures` `sc` join `production_shifts` `ps` on(`sc`.`shift_id` = `ps`.`shift_id`)) join `user` `u` on(`sc`.`closed_by` = `u`.`user_id`)) left join `shift_closure_machines` `scm` on(`sc`.`closure_id` = `scm`.`closure_id`)) GROUP BY `sc`.`closure_id` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adjustment_requests`
--
ALTER TABLE `adjustment_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `closure_id` (`closure_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_assigned_to` (`assigned_to`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `capacity_config`
--
ALTER TABLE `capacity_config`
  ADD PRIMARY KEY (`id_config`),
  ADD UNIQUE KEY `uk_level` (`level`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id_cust`);

--
-- Indexes for table `defect_reasons`
--
ALTER TABLE `defect_reasons`
  ADD PRIMARY KEY (`reason_id`),
  ADD UNIQUE KEY `unique_reason_code` (`reason_code`);

--
-- Indexes for table `downtime_reasons`
--
ALTER TABLE `downtime_reasons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reason_code` (`reason_code`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_is_active` (`is_active`);

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
  ADD UNIQUE KEY `id_product` (`id_product`);

--
-- Indexes for table `incident_coordination`
--
ALTER TABLE `incident_coordination`
  ADD PRIMARY KEY (`id`),
  ADD KEY `incident_id` (`incident_id`),
  ADD KEY `leader_id` (`leader_id`);

--
-- Indexes for table `incident_reports`
--
ALTER TABLE `incident_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_machine` (`id_machine`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_severity` (`severity_level`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_assignee` (`assignee_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_composite` (`status`,`severity_level`,`created_at`),
  ADD KEY `fk_incident_planshift` (`id_planshift`),
  ADD KEY `idx_line_id` (`line_id`),
  ADD KEY `idx_shift_id` (`shift_id`);

--
-- Indexes for table `machine`
--
ALTER TABLE `machine`
  ADD PRIMARY KEY (`id_machine`);

--
-- Indexes for table `machines`
--
ALTER TABLE `machines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_machine_code` (`code`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_stage_type` (`stage_type`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_available_machines` (`status`,`stage_type`,`capacity`),
  ADD KEY `idx_line_id` (`line_id`),
  ADD KEY `idx_equipment_category` (`equipment_category`),
  ADD KEY `idx_machine_role` (`machine_role`);

--
-- Indexes for table `machine_breakdown_logs`
--
ALTER TABLE `machine_breakdown_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_shift_id` (`shift_id`);

--
-- Indexes for table `machine_maintenances`
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
-- Indexes for table `machine_status_logs`
--
ALTER TABLE `machine_status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_machine_id` (`machine_id`),
  ADD KEY `idx_changed_at` (`changed_at`),
  ADD KEY `idx_new_status` (`new_status`),
  ADD KEY `idx_audit_trail` (`machine_id`,`changed_at`,`new_status`);

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
  ADD KEY `idx_module_name` (`module_name`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`),
  ADD KEY `module_id` (`module_id`),
  ADD KEY `idx_permission_name` (`permission_name`);

--
-- Indexes for table `planning`
--
ALTER TABLE `planning`
  ADD PRIMARY KEY (`id_plan`),
  ADD KEY `fk_planning_project` (`id_project`),
  ADD KEY `idx_planning_needs_review` (`needs_review`);

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
-- Indexes for table `production_lines`
--
ALTER TABLE `production_lines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_line_code` (`line_code`),
  ADD KEY `idx_zone_id` (`zone_id`);

--
-- Indexes for table `production_records`
--
ALTER TABLE `production_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_shift_machine` (`shift_id`,`machine_id`),
  ADD KEY `idx_timestamp` (`timestamp`),
  ADD KEY `idx_is_simulated` (`is_simulated`),
  ADD KEY `fk_production_records_machine` (`machine_id`);

--
-- Indexes for table `production_shifts`
--
ALTER TABLE `production_shifts`
  ADD PRIMARY KEY (`shift_id`),
  ADD UNIQUE KEY `unique_shift` (`line_id`,`shift_date`,`start_time`),
  ADD KEY `idx_shift_date` (`shift_date`),
  ADD KEY `idx_line_id` (`line_id`),
  ADD KEY `idx_shift_status` (`shift_status`),
  ADD KEY `idx_id_plan` (`id_plan`),
  ADD KEY `idx_started_by` (`started_by`);

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
-- Indexes for table `qc_attachments`
--
ALTER TABLE `qc_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_session_id` (`session_id`);

--
-- Indexes for table `qc_checklist_master`
--
ALTER TABLE `qc_checklist_master`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_product_variant` (`product_code`,`variant`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `qc_config`
--
ALTER TABLE `qc_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `config_key` (`config_key`);

--
-- Indexes for table `qc_decisions`
--
ALTER TABLE `qc_decisions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_session_decision` (`session_id`),
  ADD KEY `idx_result` (`result`);

--
-- Indexes for table `qc_items`
--
ALTER TABLE `qc_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_session_id` (`session_id`),
  ADD KEY `idx_result` (`result`);

--
-- Indexes for table `qc_sessions`
--
ALTER TABLE `qc_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_closure_id` (`closure_id`),
  ADD KEY `idx_inspector_status` (`inspector_code`,`status`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`),
  ADD KEY `idx_role_name` (`role_name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_permission` (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `shiftment`
--
ALTER TABLE `shiftment`
  ADD PRIMARY KEY (`id_shift`);

--
-- Indexes for table `shift_closures`
--
ALTER TABLE `shift_closures`
  ADD PRIMARY KEY (`closure_id`),
  ADD UNIQUE KEY `unique_closure_code` (`closure_code`),
  ADD KEY `idx_shift` (`shift_id`),
  ADD KEY `idx_closed_by` (`closed_by`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `shift_closure_defects`
--
ALTER TABLE `shift_closure_defects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_closure_machine` (`closure_machine_id`),
  ADD KEY `idx_reason` (`reason_id`);

--
-- Indexes for table `shift_closure_machines`
--
ALTER TABLE `shift_closure_machines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_closure` (`closure_id`),
  ADD KEY `idx_machine` (`machine_id`),
  ADD KEY `idx_staff` (`staff_id`);

--
-- Indexes for table `shift_machine_staff`
--
ALTER TABLE `shift_machine_staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_shift_machine_staff` (`shift_id`,`machine_id`,`staff_id`,`status`),
  ADD KEY `idx_shift_id` (`shift_id`),
  ADD KEY `idx_machine_id` (`machine_id`),
  ADD KEY `idx_staff_id` (`staff_id`);

--
-- Indexes for table `simulator_settings`
--
ALTER TABLE `simulator_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_setting_key` (`setting_key`);

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
-- Indexes for table `system_config`
--
ALTER TABLE `system_config`
  ADD PRIMARY KEY (`config_key`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `warehouse_import_requests`
--
ALTER TABLE `warehouse_import_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD UNIQUE KEY `unique_request_code` (`request_code`),
  ADD UNIQUE KEY `unique_closure` (`closure_id`),
  ADD KEY `idx_shift` (`shift_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_by` (`created_by`);

--
-- Indexes for table `zones`
--
ALTER TABLE `zones`
  ADD PRIMARY KEY (`zone_id`),
  ADD UNIQUE KEY `unique_zone_code` (`zone_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adjustment_requests`
--
ALTER TABLE `adjustment_requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `log_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id_cust` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1003;

--
-- AUTO_INCREMENT for table `defect_reasons`
--
ALTER TABLE `defect_reasons`
  MODIFY `reason_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `downtime_reasons`
--
ALTER TABLE `downtime_reasons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `finished_issue`
--
ALTER TABLE `finished_issue`
  MODIFY `id_issue` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `incident_coordination`
--
ALTER TABLE `incident_coordination`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `incident_reports`
--
ALTER TABLE `incident_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID báo cáo sự cố', AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `machine`
--
ALTER TABLE `machine`
  MODIFY `id_machine` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1003;

--
-- AUTO_INCREMENT for table `machines`
--
ALTER TABLE `machines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `machine_breakdown_logs`
--
ALTER TABLE `machine_breakdown_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `machine_maintenances`
--
ALTER TABLE `machine_maintenances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `machine_status_logs`
--
ALTER TABLE `machine_status_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `material`
--
ALTER TABLE `material`
  MODIFY `id_material` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1009;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `module_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

--
-- AUTO_INCREMENT for table `planning`
--
ALTER TABLE `planning`
  MODIFY `id_plan` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1089;

--
-- AUTO_INCREMENT for table `plan_shift`
--
ALTER TABLE `plan_shift`
  MODIFY `id_planshift` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1003;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id_product` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1006;

--
-- AUTO_INCREMENT for table `production_lines`
--
ALTER TABLE `production_lines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `production_records`
--
ALTER TABLE `production_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `production_shifts`
--
ALTER TABLE `production_shifts`
  MODIFY `shift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `id_project` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1004;

--
-- AUTO_INCREMENT for table `qc_attachments`
--
ALTER TABLE `qc_attachments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `qc_checklist_master`
--
ALTER TABLE `qc_checklist_master`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `qc_config`
--
ALTER TABLE `qc_config`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `qc_decisions`
--
ALTER TABLE `qc_decisions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `qc_items`
--
ALTER TABLE `qc_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `qc_sessions`
--
ALTER TABLE `qc_sessions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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
-- AUTO_INCREMENT for table `shift_closures`
--
ALTER TABLE `shift_closures`
  MODIFY `closure_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `shift_closure_defects`
--
ALTER TABLE `shift_closure_defects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shift_closure_machines`
--
ALTER TABLE `shift_closure_machines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `shift_machine_staff`
--
ALTER TABLE `shift_machine_staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `simulator_settings`
--
ALTER TABLE `simulator_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id_staff` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1034;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `warehouse_import_requests`
--
ALTER TABLE `warehouse_import_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `zones`
--
ALTER TABLE `zones`
  MODIFY `zone_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `finished_issue`
--
ALTER TABLE `finished_issue`
  ADD CONSTRAINT `finished_issue_ibfk_1` FOREIGN KEY (`id_project`) REFERENCES `project` (`id_project`);

--
-- Constraints for table `finished_report`
--
ALTER TABLE `finished_report`
  ADD CONSTRAINT `fk_finished_project` FOREIGN KEY (`id_project`) REFERENCES `project` (`id_project`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `incident_reports`
--
ALTER TABLE `incident_reports`
  ADD CONSTRAINT `fk_incident_assignee` FOREIGN KEY (`assignee_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_incident_line` FOREIGN KEY (`line_id`) REFERENCES `production_lines` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_incident_machine_new` FOREIGN KEY (`id_machine`) REFERENCES `machines` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_incident_planshift` FOREIGN KEY (`id_planshift`) REFERENCES `plan_shift` (`id_planshift`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_incident_shift` FOREIGN KEY (`shift_id`) REFERENCES `production_shifts` (`shift_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_incident_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `machine_maintenances`
--
ALTER TABLE `machine_maintenances`
  ADD CONSTRAINT `fk_machine_maintenances_machine_id` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `machine_status_logs`
--
ALTER TABLE `machine_status_logs`
  ADD CONSTRAINT `fk_machine_logs_machine_id` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `production_records`
--
ALTER TABLE `production_records`
  ADD CONSTRAINT `fk_production_records_machine` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_production_records_shift` FOREIGN KEY (`shift_id`) REFERENCES `production_shifts` (`shift_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `production_shifts`
--
ALTER TABLE `production_shifts`
  ADD CONSTRAINT `fk_production_shifts_planning` FOREIGN KEY (`id_plan`) REFERENCES `planning` (`id_plan`) ON DELETE SET NULL ON UPDATE CASCADE;

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
-- Constraints for table `qc_attachments`
--
ALTER TABLE `qc_attachments`
  ADD CONSTRAINT `qc_attachments_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `qc_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `qc_decisions`
--
ALTER TABLE `qc_decisions`
  ADD CONSTRAINT `qc_decisions_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `qc_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `qc_items`
--
ALTER TABLE `qc_items`
  ADD CONSTRAINT `qc_items_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `qc_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE CASCADE;

--
-- Constraints for table `shift_closures`
--
ALTER TABLE `shift_closures`
  ADD CONSTRAINT `fk_shift_closure_shift` FOREIGN KEY (`shift_id`) REFERENCES `production_shifts` (`shift_id`),
  ADD CONSTRAINT `fk_shift_closure_user` FOREIGN KEY (`closed_by`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `shift_closure_defects`
--
ALTER TABLE `shift_closure_defects`
  ADD CONSTRAINT `fk_closure_defect_machine` FOREIGN KEY (`closure_machine_id`) REFERENCES `shift_closure_machines` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_closure_defect_reason` FOREIGN KEY (`reason_id`) REFERENCES `defect_reasons` (`reason_id`);

--
-- Constraints for table `shift_closure_machines`
--
ALTER TABLE `shift_closure_machines`
  ADD CONSTRAINT `fk_closure_machine_closure` FOREIGN KEY (`closure_id`) REFERENCES `shift_closures` (`closure_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_closure_machine_machine` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`),
  ADD CONSTRAINT `fk_closure_machine_staff` FOREIGN KEY (`staff_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `shift_machine_staff`
--
ALTER TABLE `shift_machine_staff`
  ADD CONSTRAINT `fk_sms_machine` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sms_shift` FOREIGN KEY (`shift_id`) REFERENCES `production_shifts` (`shift_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sms_staff` FOREIGN KEY (`staff_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `sorting_report`
--
ALTER TABLE `sorting_report`
  ADD CONSTRAINT `fk_sorting_planshift` FOREIGN KEY (`id_planshift`) REFERENCES `plan_shift` (`id_planshift`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `warehouse_import_requests`
--
ALTER TABLE `warehouse_import_requests`
  ADD CONSTRAINT `fk_warehouse_request_closure` FOREIGN KEY (`closure_id`) REFERENCES `shift_closures` (`closure_id`),
  ADD CONSTRAINT `fk_warehouse_request_creator` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `fk_warehouse_request_shift` FOREIGN KEY (`shift_id`) REFERENCES `production_shifts` (`shift_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
