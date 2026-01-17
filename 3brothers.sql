-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 17, 2026 at 10:30 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `3brothers`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_attendance`
--

CREATE TABLE `admin_attendance` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `login_date` date NOT NULL,
  `login_time` time NOT NULL,
  `status` enum('On Time','Late') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `logout_time` time DEFAULT NULL,
  `is_flagged` tinyint(1) DEFAULT 0,
  `last_activity` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_attendance`
--

INSERT INTO `admin_attendance` (`id`, `admin_id`, `login_date`, `login_time`, `status`, `created_at`, `logout_time`, `is_flagged`, `last_activity`) VALUES
(1, 9, '2025-12-29', '20:17:58', 'Late', '2025-12-29 12:17:58', '22:05:29', 0, '2025-12-29 22:08:58'),
(2, 10, '2025-12-29', '20:51:49', 'Late', '2025-12-29 12:51:49', '22:05:43', 0, NULL),
(3, 9, '2025-12-30', '08:26:43', 'Late', '2025-12-30 00:26:43', NULL, 0, '2025-12-30 08:31:18'),
(4, 9, '2026-01-06', '16:42:16', 'Late', '2026-01-06 08:42:16', NULL, 1, '2026-01-06 19:29:05'),
(5, 9, '2026-01-07', '19:19:19', 'Late', '2026-01-07 11:19:19', NULL, 1, '2026-01-07 21:02:54'),
(6, 9, '2026-01-08', '20:32:42', 'Late', '2026-01-08 12:32:42', NULL, 1, '2026-01-08 21:52:56'),
(7, 9, '2026-01-10', '10:33:34', 'Late', '2026-01-10 02:33:34', NULL, 1, '2026-01-10 19:48:49'),
(8, 9, '2026-01-11', '13:57:15', 'Late', '2026-01-11 05:57:15', NULL, 1, '2026-01-11 16:43:17'),
(9, 9, '2026-01-12', '21:10:44', 'Late', '2026-01-12 13:10:44', NULL, 1, '2026-01-12 21:53:58'),
(10, 9, '2026-01-14', '20:51:13', 'Late', '2026-01-14 12:51:13', NULL, 1, '2026-01-14 20:59:03'),
(11, 9, '2026-01-17', '11:51:40', 'Late', '2026-01-17 03:51:40', '12:29:35', 1, '2026-01-17 12:57:02');

-- --------------------------------------------------------

--
-- Table structure for table `admin_shifts`
--

CREATE TABLE `admin_shifts` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `shift_name` varchar(50) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_shifts`
--

INSERT INTO `admin_shifts` (`id`, `admin_id`, `shift_name`, `start_time`, `end_time`, `created_at`) VALUES
(1, 10, 'Morning Shift', '10:00:00', '22:00:00', '2025-12-29 13:18:03'),
(2, 10, 'Morning Shift', '10:00:00', '22:00:00', '2025-12-29 13:18:08'),
(3, 9, 'xzcxc', '10:30:00', '20:30:00', '2025-12-30 00:30:44'),
(4, 9, 'xzcxcxc', '08:31:00', '20:31:00', '2025-12-30 00:31:16');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `show_home` tinyint(1) DEFAULT 0,
  `show_product` tinyint(1) DEFAULT 0,
  `show_checkout` tinyint(1) DEFAULT 0,
  `show_order` tinyint(1) NOT NULL DEFAULT 0,
  `show_all` tinyint(1) DEFAULT 0,
  `is_enabled` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `message`, `show_home`, `show_product`, `show_checkout`, `show_order`, `show_all`, `is_enabled`, `created_at`) VALUES
(4, 'Order Delivery has been delayed due to storm', 0, 0, 0, 1, 0, 1, '2026-01-10 12:21:32');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `variant_type` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `cat_no` varchar(15) NOT NULL,
  `cat_name` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `cat_no`, `cat_name`, `created_at`) VALUES
(7, '3BP218969', 'Printing', '2025-12-27 13:56:14'),
(8, '3BP551541', 'Educational Supply', '2025-12-27 13:56:27');

-- --------------------------------------------------------

--
-- Table structure for table `checkout`
--

CREATE TABLE `checkout` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `source` enum('buy_now','cart') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `checkout_items`
--

CREATE TABLE `checkout_items` (
  `id` int(11) NOT NULL,
  `checkout_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `variant_type` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flash_sales`
--

CREATE TABLE `flash_sales` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `is_enabled` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `discount_type` enum('percent','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `low_stock_threshold` int(11) DEFAULT 5,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `action` enum('IN','OUT','ADJUST') NOT NULL,
  `quantity` int(11) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logistics_logs`
--

CREATE TABLE `logistics_logs` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `remark` text DEFAULT NULL,
  `scanned_location` varchar(150) DEFAULT NULL,
  `scanned_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logistics_logs`
--

INSERT INTO `logistics_logs` (`id`, `order_id`, `status`, `remark`, `scanned_location`, `scanned_at`) VALUES
(4, 20, 'To Ship', 'Tracking number assigned', NULL, '2025-12-27 21:21:04'),
(5, 21, 'To Ship', 'Tracking number assigned', NULL, '2025-12-27 21:21:04'),
(6, 31, 'To Ship', 'Tracking number assigned', NULL, '2025-12-27 21:24:28'),
(7, 22, 'To Ship', 'Tracking number assigned', NULL, '2025-12-27 21:29:44'),
(8, 23, 'To Ship', 'Tracking number assigned', NULL, '2025-12-27 21:31:30'),
(9, 24, 'To Ship', 'Tracking number assigned', NULL, '2025-12-27 21:43:14'),
(10, 24, 'To Transit', 'Your order has arrived at warehouse', 'Main Warehouse', '2025-12-27 21:46:22'),
(11, 24, 'To Transit', 'Your order has arrived at CEBU DC', 'Main Warehouse', '2025-12-27 21:49:20'),
(12, 24, 'To Transit', 'Your order has arrived at CEBU DC', 'Main Warehouse', '2025-12-27 21:50:31'),
(13, 24, 'To Transit', 'Your order has arrived at \'Mandaue City\'', 'Mandaue City', '2025-12-27 21:54:23'),
(14, 24, 'To Transit', 'Your order has arrived at Makati', 'Makati', '2025-12-27 21:54:52'),
(15, 24, 'To Transit', 'Your order has arrived at Consolacion', 'Consolacion', '2025-12-27 21:58:32'),
(16, 24, 'To Transit', 'Your order has arrived at Consolacion', 'Consolacion', '2025-12-27 21:58:34'),
(17, 24, 'To Transit', 'Your order has arrived at Consolacion', 'Consolacion', '2025-12-27 21:58:35'),
(18, 24, 'To Transit', 'Your order has arrived at Consolacion', 'Consolacion', '2025-12-27 21:58:36'),
(19, 24, 'To Transit', 'Your order has arrived at Consolacion', 'Consolacion', '2025-12-27 21:58:37'),
(20, 24, 'To Transit', 'Your order has arrived at Consolacion', 'Consolacion', '2025-12-27 21:58:51'),
(21, 24, 'To Transit', 'Your order has arrived at Consolacion', 'Consolacion', '2025-12-27 21:58:55'),
(22, 24, 'To Transit', 'Your order has arrived at Consolacion', 'Consolacion', '2025-12-27 21:58:55'),
(23, 24, 'To Transit', 'Your order has arrived at Consolacion', 'Consolacion', '2025-12-27 21:59:03'),
(24, 24, 'To Transit', 'Your order has arrived at Consolacion', 'Consolacion', '2025-12-27 21:59:06'),
(25, 24, 'To Transit', 'Your order has arrived at Consolacion', 'Consolacion', '2025-12-27 21:59:18'),
(26, 24, 'To Transit', 'Your order has departed from Consolacion', 'Consolacion', '2025-12-27 21:59:48'),
(27, 24, 'To Transit', 'Your order has arrived at MANDAUE CITY', 'MANDAUE CITY', '2025-12-27 22:01:51'),
(28, 24, 'To Transit', 'Your order has departed from delivery hub: MANDAUE CITY', 'MANDAUE CITY', '2025-12-27 22:03:05'),
(29, 24, 'To Transit', 'Your order has arrived at delivery hub: CONSOLACION', 'CONSOLACION', '2025-12-27 22:03:23'),
(30, 24, 'To Transit', 'Your order has arrived at delivery hub: CONSOLACION', 'CONSOLACION', '2025-12-27 22:04:56'),
(31, 25, 'To Ship', 'Tracking number assigned', NULL, '2025-12-28 09:52:41'),
(32, 26, 'To Ship', 'Tracking number assigned', NULL, '2025-12-28 09:52:41'),
(33, 27, 'To Ship', 'Tracking number assigned', NULL, '2025-12-28 09:52:41'),
(34, 27, 'To Transit', 'Your order has departed from delivery hub: CONSOLACION', 'CONSOLACION', '2025-12-28 09:53:40'),
(35, 24, 'To Transit', 'Your order has arrived at delivery hub: MANDAUE CITY', 'MANDAUE CITY', '2025-12-28 09:54:06'),
(36, 27, 'To Transit', 'Your order has arrived at delivery hub: MANDAUE CITY', 'MANDAUE CITY', '2025-12-28 09:54:36'),
(37, 27, 'To Transit', 'Your order has departed from delivery hub: MANDAUE CITY', 'MANDAUE CITY', '2025-12-28 09:55:06'),
(38, 28, 'To Ship', 'Tracking number assigned', NULL, '2025-12-28 10:42:58'),
(39, 27, 'To Transit', 'Your order has arrived at delivery hub: CEBU CITY', 'CEBU CITY', '2025-12-28 10:43:24'),
(40, 28, 'To Transit', 'Your order has departed from delivery hub: CONSOLACION', 'CONSOLACION', '2025-12-28 10:43:35'),
(41, 28, 'To Transit', 'Your order has arrived at delivery hub: MANDAUE CITY', 'MANDAUE CITY', '2025-12-28 10:43:57'),
(42, 28, 'To Transit', 'Your order has departed from delivery hub: MANDAUE CITY', 'MANDAUE CITY', '2025-12-28 20:06:20'),
(43, 28, 'To Transit', 'Your order has arrived at delivery hub: CEBU CITY', 'CEBU CITY', '2025-12-28 20:23:11'),
(44, 28, 'To Transit', 'Your order has departed from delivery hub: CEBU CITY', 'CEBU CITY', '2025-12-29 18:24:32'),
(45, 28, 'To Transit', 'Your order has arrived at delivery hub: MANILA', 'MANILA', '2025-12-29 18:25:51'),
(46, 29, 'To Ship', 'Tracking number assigned', NULL, '2025-12-29 22:42:17'),
(47, 30, 'To Ship', 'Tracking number assigned', NULL, '2026-01-06 19:38:03'),
(48, 28, 'To Transit', 'Your order has arrived at delivery hub: CEBU CITY', 'CEBU CITY', '2026-01-07 19:27:48'),
(49, 30, 'To Transit', 'Your order has arrived at delivery hub: CEBU CITY', 'CEBU CITY', '2026-01-07 19:30:19'),
(50, 30, 'To Transit', 'Your order has departed from delivery hub: CEBU CITY', 'CEBU CITY', '2026-01-07 19:31:00'),
(51, 30, 'To Transit', 'Your order has arrived at delivery hub: CEBU CITY', 'CEBU CITY', '2026-01-07 19:37:56'),
(52, 29, 'To Transit', 'Your order has arrived at delivery hub: CEBU CITY', 'CEBU CITY', '2026-01-07 19:38:09'),
(53, 33, 'To Ship', 'Tracking number assigned', NULL, '2026-01-10 19:45:32'),
(54, 34, 'To Ship', 'Tracking number assigned', NULL, '2026-01-10 19:45:32'),
(55, 34, 'To Transit', 'Your order has arrived at delivery hub: CEBU CITY', 'CEBU CITY', '2026-01-11 16:49:01'),
(56, 34, 'To Transit', 'Your order has departed from delivery hub: CEBU CITY', 'CEBU CITY', '2026-01-11 16:49:54'),
(57, 34, 'To Transit', 'Your order has arrived at delivery hub: MANDAUE CITY', 'MANDAUE CITY', '2026-01-11 16:50:42'),
(58, 34, 'To Transit', 'Your order has departed from delivery hub: MANDAUE CITY', 'MANDAUE CITY', '2026-01-17 14:29:48'),
(59, 34, 'To Transit', 'Your order has arrived at delivery hub: CEBU CITY', 'CEBU CITY', '2026-01-17 14:30:33'),
(60, 34, 'To Transit', 'Your order has departed from delivery hub: CEBU CITY', 'CEBU CITY', '2026-01-17 14:31:26');

-- --------------------------------------------------------

--
-- Table structure for table `logistics_shipments`
--

CREATE TABLE `logistics_shipments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `courier` varchar(50) NOT NULL,
  `tracking_number` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logistics_shipments`
--

INSERT INTO `logistics_shipments` (`id`, `order_id`, `courier`, `tracking_number`, `created_at`) VALUES
(4, 20, 'J&T', 'TBP300570629005', '2025-12-27 21:21:04'),
(5, 21, 'J&T', 'TBP150490899990', '2025-12-27 21:21:04'),
(6, 31, 'J&T', 'TBP043585904175', '2025-12-27 21:24:28'),
(7, 22, 'J&T', 'TBP711831209098', '2025-12-27 21:29:44'),
(8, 23, 'J&T', 'TBP852203291015', '2025-12-27 21:31:30'),
(9, 24, 'LBC', 'TBP910635282010', '2025-12-27 21:43:14'),
(10, 25, 'J&T', 'TBP641693044463', '2025-12-28 09:52:41'),
(11, 26, 'J&T', 'TBP976460457348', '2025-12-28 09:52:41'),
(12, 27, 'J&T', 'TBP431389154534', '2025-12-28 09:52:41'),
(13, 28, 'J&T', 'TBP446882618325', '2025-12-28 10:42:58'),
(14, 29, 'J&T', 'TBP620083211827', '2025-12-29 22:42:17'),
(15, 30, 'J&T', '739924070001727', '2026-01-06 19:38:02'),
(16, 33, 'J&T', '596454360030649', '2026-01-10 19:45:32'),
(17, 34, 'J&T', '315612726628194', '2026-01-10 19:45:32');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `is_read`, `created_at`, `link`) VALUES
(10, 8, 'Christmas Sale', 'awedwadasdasdwdwad', 1, '2025-12-28 14:01:26', ''),
(11, NULL, 'ERRRRR', 'dwdwddsdawdaw', 1, '2025-12-28 14:04:33', ''),
(12, 9, 'wwdsd', 'wdasdsdwadsdsdw', 1, '2025-12-28 14:08:28', '');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_no` varchar(20) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `recipient_name` varchar(100) NOT NULL,
  `recipient_phone` varchar(20) NOT NULL,
  `delivery_address` text NOT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `delivery_type` enum('standard','express','same_day') NOT NULL,
  `payment_method` enum('cod','gcash','paymaya') NOT NULL,
  `voucher_type` enum('fixed','shipping') DEFAULT NULL,
  `voucher_value` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `shipping_fee` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('Order Placed','To Ship','To Transit','Out for Delivery','Delivered','Cancelled') DEFAULT 'Order Placed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_no`, `user_id`, `recipient_name`, `recipient_phone`, `delivery_address`, `barangay`, `city`, `province`, `zip_code`, `delivery_type`, `payment_method`, `voucher_type`, `voucher_value`, `subtotal`, `shipping_fee`, `discount`, `total_amount`, `status`, `created_at`) VALUES
(33, '69623AEBECAC2', 8, 'Earl Christian Tagalog', '09940823693', 'ITFGTGGHGHYTFHGYGYF', 'Pulpogan', 'Consolacion', 'Cebu', '', 'standard', 'cod', '', 0.00, 210.00, 38.00, 0.00, 248.00, 'To Transit', '2026-01-10 11:41:31'),
(34, '69623B7498E53', 8, 'Earl Christian Tagalog', '09940823693', 'ITFGTGGHGHYTFHGYGYF', 'Pulpogan', 'Consolacion', 'Cebu', '', 'standard', 'cod', '', 0.00, 210.00, 38.00, 0.00, 248.00, 'To Transit', '2026-01-10 11:43:49');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `variant` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_image`, `variant`, `price`, `quantity`, `subtotal`) VALUES
(21, 15, 10, 'RJ45 FOR CABLE', 'admin/products/uploads/694d4a92ef4d4_download (1).jpg', 'Red', 100.00, 1, 100.00),
(22, 16, 9, 'BOND PAPER', 'admin/products/uploads/694d472827da7_download (1).jpg', 'Long', 178.00, 1, 178.00),
(23, 18, 10, 'RJ45 FOR CABLE', 'admin/products/uploads/694d4a92ef4d4_download (1).jpg', 'Red', 100.00, 10, 1000.00),
(24, 19, 10, 'RJ45 FOR CABLE', 'admin/products/uploads/694d4a92ef4d4_download (1).jpg', 'Red', 100.00, 1, 100.00),
(25, 20, 11, 'One Bond Paper', 'admin/products/uploads/694f7703ca24e_download (1).jpg', 'Short', 179.00, 1, 179.00),
(26, 21, 13, 'Colored Paper Random (100pcs)', 'admin/products/uploads/694f7a1a225a8_download (1).jpg', 'Random', 120.00, 1, 120.00),
(27, 22, 14, 'Sticker Paper', 'admin/products/uploads/694f7af713bcf_download (1).jpg', '100', 100.00, 1, 100.00),
(28, 23, 14, 'Sticker Paper', 'admin/products/uploads/694f7af713bcf_download (1).jpg', '100', 100.00, 1, 100.00),
(29, 24, 14, 'Sticker Paper', 'admin/products/uploads/694f7af713bcf_download (1).jpg', '100', 100.00, 1, 100.00),
(30, 25, 14, 'Sticker Paper', 'admin/products/uploads/694f7af713bcf_download (1).jpg', '100', 100.00, 1, 100.00),
(31, 26, 13, 'Colored Paper Random (100pcs)', 'admin/products/uploads/694f7a1a225a8_download (1).jpg', 'Random', 120.00, 1, 120.00),
(32, 27, 11, 'One Bond Paper', 'admin/products/uploads/<br />\n<b>Warning</b>:  Undefined variable $productImage in <b>C:\\xampp\\htdocs\\Print\\product-details.php</b> on line <b>145</b><br />\n', '{\"Size\":{\"value\":\"Short\",\"price\":179},\"GSM\":{\"value\":\"80\",\"price\":170}}', 499.00, 1, 499.00),
(33, 27, 14, 'Sticker Paper', 'admin/products/uploads/694f7af713bcf_download (1).jpg', '{\"Pcs\":{\"value\":\"50\",\"price\":50}}', 150.00, 1, 150.00),
(34, 28, 15, 'BOND PAPER', 'admin/products/uploads/69508ef1a45bd_download (1).jpg', '{\"Size\":{\"value\":\"Long\",\"price\":178},\"GSM\":{\"value\":\"80\",\"price\":20}}', 198.00, 1, 198.00),
(35, 29, 15, 'BOND PAPER', 'admin/products/uploads/69508ef1a45bd_download (1).jpg', '{\"Size\":{\"value\":\"Long\",\"price\":178},\"GSM\":{\"value\":\"80\",\"price\":20}}', 396.00, 2, 792.00),
(36, 30, 15, 'BOND PAPER', 'admin/products/uploads/69508ef1a45bd_download (1).jpg', '{\"Size\":{\"value\":\"A4\",\"price\":200},\"GSM\":{\"value\":\"80\",\"price\":20}}', 220.00, 1, 220.00),
(39, 34, 19, 'BOND PAPER', 'admin/products/uploads/6961bdfc31178_Gemini_Generated_Image_cz1n8ucz1n8ucz1n.png', '{\"Size\":{\"value\":\"Long\",\"price\":200},\"GSM\":{\"value\":\"80\",\"price\":10}}', 210.00, 1, 210.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_logs`
--

CREATE TABLE `order_logs` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` enum('Order Placed','To Ship','To Transit','Out for Delivery','Delivered','Cancelled') NOT NULL DEFAULT 'Order Placed',
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_logs`
--

INSERT INTO `order_logs` (`id`, `order_id`, `status`, `remarks`, `created_at`) VALUES
(90, 34, 'Order Placed', 'Order successfully created by customer', '2026-01-10 19:43:49'),
(91, 34, 'To Ship', 'Your order has been prepared', '2026-01-10 19:45:17'),
(92, 33, 'To Ship', 'Your order has been prepared', '2026-01-10 19:45:24'),
(93, 33, 'To Transit', 'Order has been picked up by logistics', '2026-01-10 19:45:32'),
(94, 34, 'To Transit', 'Order has been picked up by logistics', '2026-01-10 19:45:32'),
(95, 34, 'To Transit', 'Your order has arrived at delivery hub: CEBU CITY', '2026-01-11 16:49:01'),
(96, 34, 'To Transit', 'Your order has departed from delivery hub: CEBU CITY', '2026-01-11 16:49:54'),
(97, 34, 'To Transit', 'Your order has arrived at delivery hub: MANDAUE CITY', '2026-01-11 16:50:42'),
(98, 34, 'To Transit', 'Your order has departed from delivery hub: MANDAUE CITY', '2026-01-17 14:29:48'),
(99, 34, 'To Transit', 'Your order has arrived at delivery hub: CEBU CITY', '2026-01-17 14:30:33'),
(100, 34, 'To Transit', 'Your order has departed from delivery hub: CEBU CITY', '2026-01-17 14:31:26');

-- --------------------------------------------------------

--
-- Table structure for table `print_requests`
--

CREATE TABLE `print_requests` (
  `id` int(11) NOT NULL,
  `request_no` varchar(20) DEFAULT NULL,
  `user_id` int(15) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `print_type` varchar(50) DEFAULT NULL,
  `paper_size` varchar(20) DEFAULT NULL,
  `copies` int(11) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `status` enum('Pending Payment Verification','Approved','Rejected','Cancelled','Order Placed','Printing','Ready for Pickup','Completed') NOT NULL DEFAULT 'Order Placed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `price` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT 'Unpaid',
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `print_requests`
--

INSERT INTO `print_requests` (`id`, `request_no`, `user_id`, `full_name`, `print_type`, `paper_size`, `copies`, `color`, `notes`, `delivery_address`, `file_name`, `file_path`, `file_size`, `status`, `created_at`, `price`, `payment_method`, `payment_status`, `paid_at`) VALUES
(35, '685400754703619', 8, 'Earl Christian Tagalog', 'Print', 'Letter (Short)', 1, 'Color', '', 'ITFGTGGHGHYTFHGYGYF\r\n            Pulpogan,\r\n            Consolacion\r\n            Cebu\r\n            Central Visayas', NULL, NULL, NULL, 'Completed', '2026-01-10 12:26:04', 55.00, 'PayMaya', 'Paid', '2026-01-10 20:29:12');

-- --------------------------------------------------------

--
-- Table structure for table `print_request_files`
--

CREATE TABLE `print_request_files` (
  `id` int(11) NOT NULL,
  `request_id` int(11) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `print_request_files`
--

INSERT INTO `print_request_files` (`id`, `request_id`, `file_name`, `file_path`, `file_size`, `created_at`) VALUES
(20, 16, 'mike Consent-Letter-Re-enroll-for-Research-2 copy.pdf', 'uploads/1766463531_mike_Consent-Letter-Re-enroll-for-Research-2_copy.pdf', 97307, '2025-12-23 04:18:51'),
(21, 17, 'Document (13).docx', 'uploads/1766471313_Document__13_.docx', 494363, '2025-12-23 06:28:33'),
(66, 35, 'work-immersion-portfolio.docx', 'uploads/1768047964_work-immersion-portfolio.docx', 5149613, '2026-01-10 12:26:04');

-- --------------------------------------------------------

--
-- Table structure for table `print_request_logs`
--

CREATE TABLE `print_request_logs` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `remark` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `print_request_logs`
--

INSERT INTO `print_request_logs` (`id`, `request_id`, `status`, `remark`, `created_at`) VALUES
(71, 35, 'Order Placed', 'Order placed by customer', '2026-01-10 12:26:04'),
(72, 35, 'Pending Payment Verification', 'Pending payment for verification', '2026-01-10 12:27:35'),
(73, 35, 'Pending Payment Verification', 'Pending payment for verification', '2026-01-10 12:28:02'),
(74, 35, 'Approved', 'The payment has been approved and the file has been\n                    downloaded.', '2026-01-10 12:29:12'),
(75, 35, 'Printing', 'The file has now printing.', '2026-01-10 12:30:02'),
(76, 35, 'Ready for Pickup', 'The file has been printed and ready for delivery', '2026-01-10 12:30:28'),
(77, 35, 'Completed', 'The file has been delivered', '2026-01-10 12:31:09');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_no` varchar(15) DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `small_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `barcode` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_no`, `sku`, `name`, `category`, `status`, `description`, `small_description`, `created_at`, `barcode`) VALUES
(21, '255767674392563', '#8KU9AE79PRCB9LM', 'RJ45 FOR CABLE', 'Printing', 'Active', 'wdasdaewdasdzdwewwasfsxfdasd', 'waedasdasdasdwwadasdsad', '2026-01-17 05:17:08', '3BP-000021'),
(22, '670032130839612', '#ISRS7NWW4OHEG8Q', 'BOND PAPER - 70 GSM', 'Printing', 'Active', 'wdadasdsacsdwasdasdasd', 'wdwadwasdszcxassdasdassdasdwad', '2026-01-17 05:20:43', '3BP-000022');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image`, `sort_order`) VALUES
(96, 21, '696b1b54e6bbc_download (1).jpg', 0),
(97, 21, '696b1b54eb337_download (2).jpg', 1),
(98, 21, '696b1b54ee99e_download (3).jpg', 2),
(99, 21, '696b1b54f1131_download (4).jpg', 3),
(100, 21, '696b1b54f2748_download.jpg', 4),
(101, 22, '696b1c2bbebc3_download (1).jpg', 0),
(102, 22, '696b1c2bc4279_download (2).jpg', 1),
(103, 22, '696b1c2bc4b99_download (3).jpg', 2),
(104, 22, '696b1c2bc68f8_download.jpg', 3),
(105, 22, '696b1c2bc7c85_images.jpg', 4);

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `review` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `review_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_specs`
--

CREATE TABLE `product_specs` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `spec_name` varchar(100) DEFAULT NULL,
  `spec_value` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `value` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `barcode` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `type`, `value`, `price`, `stock`, `barcode`) VALUES
(43, 21, 'Type', 'Cat5e', 5.00, 100, '401778300272999'),
(44, 22, 'Size', 'Long', 120.00, 100, '889620340799260'),
(45, 22, 'Size', 'Short', 130.00, 100, '884942875664231'),
(46, 22, 'Size', 'A4', 140.00, 100, '977599455421574');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `account_no` varchar(15) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `verify_code` varchar(6) DEFAULT NULL,
  `is_verified` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `otp_expires` datetime DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `otp_attempts` int(11) DEFAULT 0,
  `otp_last_sent` datetime DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `otp_blocked_until` datetime DEFAULT NULL,
  `is_banned` tinyint(1) DEFAULT 0,
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `account_no`, `full_name`, `email`, `password`, `verify_code`, `is_verified`, `created_at`, `otp_expires`, `reset_token`, `reset_expires`, `otp_attempts`, `otp_last_sent`, `remember_token`, `otp_blocked_until`, `is_banned`, `role`) VALUES
(8, '20756890', 'Earl Christian Tagalog', 'earlchristiantagalog1@gmail.com', '$2y$10$Z9xvOOds2JprkZnbRkDeiOycGYUYpU2tIDs3OG4/VG./AXAfSf31e', NULL, 1, '2025-12-21 23:51:04', NULL, NULL, NULL, 0, NULL, '25a6fdb101837735ce2058c77ccdd6d1be527d1f106348cfae540fc6b988ace1', NULL, 0, 'user'),
(9, '20795588', 'Earl Christian ', 'earlchristianespina@gmail.com', '$2y$10$Izl4trkQwymwrCNPihIDw.HxkFjF2jCCWMW3WEP5eVJwQvaOkG29O', NULL, 1, '2025-12-23 04:03:12', NULL, NULL, NULL, 0, NULL, '726b7b37c96ac3f8079b0a1a308d94c5897c1e97b7900fb2525cd002071ef6d9', NULL, 0, 'admin'),
(10, '20454417', 'Admin', '3brothersprintservices@gmail.com', '$2y$10$wH3bq4mZ8mQ0f3n0p2H0CeJ2m1bG9n8v8H1v8cXz9n3V8n1KxHkK6\n', NULL, 1, '2025-12-29 12:49:26', NULL, NULL, NULL, 0, NULL, 'f50ebcd8a9a32f8cbee150e142a3736ff52ce653b575542e3495a5acbba6b0da', NULL, 0, 'admin'),
(11, '20720792', 'Earl Christian ', 'example@gmail.com', '$2y$10$PllTclkWB6eoM.Mq4nYpaOJUpCYkQctP2WcWZb.o0l2Klu16ygFqG', '426004', 0, '2025-12-29 14:25:37', '2025-12-29 22:30:37', NULL, NULL, 0, NULL, NULL, NULL, 0, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `label` varchar(50) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `region_code` varchar(10) DEFAULT NULL,
  `region_name` varchar(100) DEFAULT NULL,
  `province_code` varchar(10) DEFAULT NULL,
  `province_name` varchar(100) DEFAULT NULL,
  `city_code` varchar(10) DEFAULT NULL,
  `city_name` varchar(100) DEFAULT NULL,
  `barangay_code` varchar(10) DEFAULT NULL,
  `barangay_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_addresses`
--

INSERT INTO `user_addresses` (`id`, `user_id`, `label`, `full_name`, `phone`, `address`, `zip_code`, `is_default`, `created_at`, `region_code`, `region_name`, `province_code`, `province_name`, `city_code`, `city_name`, `barangay_code`, `barangay_name`) VALUES
(1, 9, 'Home', 'Earl Christian Tagalog', '09940823693', 'dasdsadfsadasdsa', '', 1, '2025-12-23 12:30:38', '070000000', 'Central Visayas', '072200000', 'Cebu', '072209000', 'Bantayan', '072209018', 'Patao'),
(2, 9, 'Home', 'Earl Christian Tagalog', '09940823693', 'sdasdasdsadasdsadasdas', '6001', 0, '2025-12-23 12:33:02', '070000000', 'Central Visayas', '072200000', 'Cebu', '072219000', 'Consolacion', '072219016', 'Pulpogan'),
(3, 8, 'Home', 'Earl Christian Tagalog', '09940823693', 'ITFGTGGHGHYTFHGYGYF', '', 1, '2025-12-25 11:04:48', '070000000', 'Central Visayas', '072200000', 'Cebu', '072219000', 'Consolacion', '072219016', 'Pulpogan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_attendance`
--
ALTER TABLE `admin_attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_shifts`
--
ALTER TABLE `admin_shifts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart` (`user_id`,`product_id`,`variant_type`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `checkout`
--
ALTER TABLE `checkout`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `checkout_items`
--
ALTER TABLE `checkout_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `checkout_id` (`checkout_id`);

--
-- Indexes for table `flash_sales`
--
ALTER TABLE `flash_sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`product_id`);

--
-- Indexes for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `logistics_logs`
--
ALTER TABLE `logistics_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logistics_shipments`
--
ALTER TABLE `logistics_shipments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tracking_number` (`tracking_number`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_logs`
--
ALTER TABLE `order_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_logs_order` (`order_id`);

--
-- Indexes for table `print_requests`
--
ALTER TABLE `print_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `print_request_files`
--
ALTER TABLE `print_request_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `print_request_logs`
--
ALTER TABLE `print_request_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `barcode` (`barcode`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_review` (`user_id`,`order_item_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `order_item_id` (`order_item_id`);

--
-- Indexes for table `product_specs`
--
ALTER TABLE `product_specs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `barcode` (`barcode`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_attendance`
--
ALTER TABLE `admin_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `admin_shifts`
--
ALTER TABLE `admin_shifts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `checkout`
--
ALTER TABLE `checkout`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `checkout_items`
--
ALTER TABLE `checkout_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `flash_sales`
--
ALTER TABLE `flash_sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logistics_logs`
--
ALTER TABLE `logistics_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `logistics_shipments`
--
ALTER TABLE `logistics_shipments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `order_logs`
--
ALTER TABLE `order_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `print_requests`
--
ALTER TABLE `print_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `print_request_files`
--
ALTER TABLE `print_request_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `print_request_logs`
--
ALTER TABLE `print_request_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product_specs`
--
ALTER TABLE `product_specs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_shifts`
--
ALTER TABLE `admin_shifts`
  ADD CONSTRAINT `admin_shifts_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `checkout_items`
--
ALTER TABLE `checkout_items`
  ADD CONSTRAINT `checkout_items_ibfk_1` FOREIGN KEY (`checkout_id`) REFERENCES `checkout` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD CONSTRAINT `inventory_logs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_logs`
--
ALTER TABLE `order_logs`
  ADD CONSTRAINT `fk_order_logs_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `print_request_logs`
--
ALTER TABLE `print_request_logs`
  ADD CONSTRAINT `print_request_logs_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `print_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `fk_review_item` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_review_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_review_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_specs`
--
ALTER TABLE `product_specs`
  ADD CONSTRAINT `product_specs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
