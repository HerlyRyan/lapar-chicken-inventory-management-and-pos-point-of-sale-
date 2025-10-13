-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 24, 2025 at 09:49 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lapar_chicken_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `activity` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_id` bigint UNSIGNED DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `type` enum('production','branch') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'branch',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `code`, `address`, `phone`, `email`, `is_active`, `type`, `created_at`, `updated_at`) VALUES
(1, 'LC Panglima Batur', 'LC01', 'Jl. Panglima Batur (Seberang SDN 3 Komet Banjarbaru)', '6287819409503', 'pusat@laparchicken.com', 1, 'branch', '2025-07-22 15:10:31', '2025-07-31 07:21:30'),
(3, 'LC Bundaran Simpang Empat Banjarbaru', 'LC02', 'Jl. H. Mistar Cukrokusomo', '082210302117', 'barat@laparchicken.com', 1, 'branch', '2025-07-22 15:10:31', '2025-08-03 14:13:45'),
(5, 'Pusat Produksi', 'LC00', 'Jl. H. Mistar Cukrokusomo (Belakang LC02)', '089513515040', NULL, 1, 'production', '2025-07-23 09:57:29', '2025-07-23 13:23:31'),
(6, 'LC Sekumpul', 'LC03', 'Jl. Sungai Kacang', '089513515040', NULL, 1, 'branch', '2025-07-23 13:14:55', '2025-07-23 14:32:01');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `is_active`, `code`, `description`, `created_at`, `updated_at`) VALUES
(2, 'Bumbu dan Rempah', 1, 'BUM2', 'Bumbu dan rempah-rempah', '2025-07-22 15:10:31', '2025-07-29 13:15:02'),
(3, 'Kemasan', 1, 'KEM3', 'Bahan kemasan produk', '2025-07-22 15:10:31', '2025-07-29 09:54:44'),
(12, 'Cleaning Supply', 1, 'CLE', 'Auto-created for Cleaning Supply', '2025-07-30 06:31:27', '2025-07-30 06:31:27'),
(13, 'Ala Carte', 1, 'ALA', 'Auto-created for Ala Carte', '2025-07-30 06:31:28', '2025-08-03 14:16:22'),
(14, 'Drink', 1, 'DRI', 'Auto-created for Drink', '2025-07-30 06:31:28', '2025-07-30 06:31:28'),
(15, 'Geprek Ala Carte', 1, 'GEP', 'Auto-created for Geprek Ala Carte', '2025-07-30 06:31:28', '2025-07-30 06:31:28'),
(16, 'Sauce', 1, 'SAU', 'Auto-created for Sauce', '2025-07-30 06:31:28', '2025-07-30 06:31:28'),
(17, 'Snack', 1, 'SNA', 'Auto-created for Snack', '2025-07-30 06:31:28', '2025-07-30 06:31:28');

-- --------------------------------------------------------

--
-- Table structure for table `destruction_reports`


-- --------------------------------------------------------

--
-- Table structure for table `finished_branch_stocks`
--

CREATE TABLE `finished_branch_stocks` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `finished_product_id` bigint UNSIGNED NOT NULL,
  `quantity` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `finished_branch_stocks`
--

INSERT INTO `finished_branch_stocks` (`id`, `branch_id`, `finished_product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(29, 3, 18, 5, '2025-07-30 08:56:17', '2025-07-30 11:21:53'),
(30, 3, 14, 9, '2025-07-30 08:56:17', '2025-08-19 22:57:07'),
(31, 3, 26, 5, '2025-07-30 08:56:17', '2025-07-30 12:10:31'),
(32, 3, 20, 10, '2025-07-30 08:56:17', '2025-08-07 16:46:19'),
(33, 3, 21, 10, '2025-07-30 08:56:17', '2025-08-07 16:46:19'),
(34, 3, 19, 10, '2025-07-30 08:56:17', '2025-08-07 16:46:19'),
(35, 3, 22, 10, '2025-07-30 08:56:17', '2025-08-07 16:46:19'),
(36, 3, 24, 10, '2025-07-30 08:56:17', '2025-08-07 16:46:19'),
(37, 3, 23, 10, '2025-07-30 08:56:17', '2025-08-07 16:46:19'),
(38, 3, 34, 10, '2025-07-30 08:56:17', '2025-08-07 16:46:19'),
(39, 3, 35, 10, '2025-07-30 08:56:17', '2025-08-07 16:46:19'),
(40, 3, 36, 10, '2025-07-30 08:56:17', '2025-08-07 16:46:19'),
(41, 3, 15, 10, '2025-07-30 08:56:17', '2025-08-07 16:46:19'),
(42, 3, 27, 10, '2025-07-30 08:56:17', '2025-08-07 16:46:19'),
(43, 3, 16, 10, '2025-07-30 08:56:17', '2025-08-07 16:46:19'),
(44, 1, 18, 8, '2025-07-30 08:58:14', '2025-08-07 22:55:31'),
(45, 1, 14, 12, '2025-07-30 08:58:14', '2025-08-20 21:54:46'),
(46, 1, 26, 10, '2025-07-30 08:58:14', '2025-08-07 16:46:19'),
(47, 1, 20, 10, '2025-07-30 08:58:14', '2025-07-30 12:12:39'),
(48, 1, 21, 10, '2025-07-30 08:58:14', '2025-08-07 16:46:19'),
(49, 1, 19, 10, '2025-07-30 08:58:14', '2025-08-07 16:46:19'),
(50, 1, 22, 10, '2025-07-30 08:58:14', '2025-08-07 16:46:19'),
(51, 1, 24, 10, '2025-07-30 08:58:14', '2025-08-07 16:46:19'),
(52, 1, 23, 10, '2025-07-30 08:58:14', '2025-08-07 16:46:19'),
(53, 1, 34, 10, '2025-07-30 08:58:14', '2025-08-07 16:46:19'),
(54, 1, 35, 10, '2025-07-30 08:58:14', '2025-08-07 16:46:19'),
(55, 1, 36, 10, '2025-07-30 08:58:14', '2025-08-07 16:46:19'),
(56, 1, 15, 10, '2025-07-30 08:58:14', '2025-08-07 16:46:19'),
(57, 1, 27, 8, '2025-07-30 08:58:14', '2025-08-20 21:54:46'),
(58, 1, 16, 10, '2025-07-30 08:58:14', '2025-08-07 16:46:19'),
(59, 3, 28, 10, '2025-07-30 09:41:27', '2025-08-07 16:46:19'),
(60, 3, 30, 10, '2025-07-30 09:41:28', '2025-08-07 16:46:19'),
(61, 3, 31, 10, '2025-07-30 09:41:28', '2025-08-07 16:46:19'),
(62, 3, 32, 10, '2025-07-30 09:41:28', '2025-08-07 16:46:19'),
(63, 3, 33, 10, '2025-07-30 09:41:28', '2025-08-07 16:46:19'),
(64, 3, 17, 10, '2025-07-30 09:41:28', '2025-08-07 16:46:19'),
(65, 3, 29, 10, '2025-07-30 09:41:28', '2025-08-07 16:46:19'),
(66, 3, 25, 5, '2025-07-30 09:41:28', '2025-08-05 13:13:14'),
(70, 6, 18, 0, '2025-07-30 11:23:17', '2025-08-07 20:49:47'),
(71, 6, 14, 24, '2025-07-30 11:23:17', '2025-08-20 02:27:02'),
(72, 6, 26, 18, '2025-07-30 11:23:17', '2025-08-07 15:43:40'),
(73, 6, 20, 27, '2025-07-30 11:23:17', '2025-08-07 15:43:40'),
(74, 6, 21, 31, '2025-07-30 11:23:17', '2025-08-07 15:43:40'),
(75, 6, 19, 12, '2025-07-30 11:23:17', '2025-08-07 15:43:40'),
(76, 6, 22, 35, '2025-07-30 11:23:17', '2025-08-07 15:43:40'),
(77, 6, 24, 30, '2025-07-30 11:23:17', '2025-08-07 15:43:40'),
(78, 6, 23, 11, '2025-07-30 11:23:17', '2025-08-07 15:43:40'),
(79, 6, 34, 21, '2025-07-30 11:23:17', '2025-08-07 15:43:40'),
(80, 6, 35, 16, '2025-07-30 11:23:17', '2025-08-07 15:43:40'),
(81, 6, 36, 34, '2025-07-30 11:23:17', '2025-08-07 15:43:40'),
(82, 6, 15, 19, '2025-07-30 11:23:17', '2025-08-07 15:43:40'),
(83, 6, 27, 26, '2025-07-30 11:23:17', '2025-08-07 15:43:40'),
(84, 6, 16, 17, '2025-07-30 11:23:17', '2025-08-07 15:43:40'),
(85, 1, 28, 9, '2025-08-05 13:53:52', '2025-08-20 02:25:41'),
(86, 1, 30, 10, '2025-08-05 13:53:52', '2025-08-07 16:46:19'),
(87, 1, 31, 10, '2025-08-05 13:53:52', '2025-08-07 16:46:19'),
(88, 1, 32, 10, '2025-08-05 13:53:52', '2025-08-07 16:46:19'),
(89, 1, 33, 10, '2025-08-05 13:53:52', '2025-08-07 16:46:19'),
(90, 1, 17, 10, '2025-08-05 13:53:52', '2025-08-07 16:46:19'),
(91, 1, 29, 10, '2025-08-05 13:53:52', '2025-08-07 16:46:19'),
(92, 1, 25, 10, '2025-08-05 13:53:52', '2025-08-07 16:46:19'),
(93, 6, 28, 39, '2025-08-05 15:04:13', '2025-08-20 02:26:53'),
(94, 6, 30, 17, '2025-08-05 15:04:13', '2025-08-07 15:43:40'),
(95, 6, 31, 14, '2025-08-05 15:04:13', '2025-08-07 15:43:40'),
(96, 6, 32, 37, '2025-08-05 15:04:13', '2025-08-07 15:43:40'),
(97, 6, 33, 17, '2025-08-05 15:04:13', '2025-08-07 15:43:40'),
(98, 6, 17, 18, '2025-08-05 15:04:13', '2025-08-07 15:43:40'),
(99, 6, 29, 40, '2025-08-05 15:04:13', '2025-08-07 15:43:40'),
(100, 6, 25, 18, '2025-08-05 15:04:13', '2025-08-07 15:43:40'),
(101, 5, 14, 10, '2025-08-07 16:46:19', '2025-08-07 16:46:19'),
(102, 5, 15, 10, '2025-08-07 16:46:19', '2025-08-07 16:46:19'),
(103, 5, 16, 9, '2025-08-07 16:46:19', '2025-08-20 03:04:09'),
(104, 5, 17, 10, '2025-08-07 16:46:19', '2025-08-07 16:46:19'),
(105, 5, 18, 10, '2025-08-07 16:46:19', '2025-08-07 16:46:19'),
(106, 5, 19, 10, '2025-08-07 16:46:19', '2025-08-07 16:46:19'),
(107, 5, 20, 10, '2025-08-07 16:46:19', '2025-08-07 16:46:19'),
(108, 5, 21, 10, '2025-08-07 16:46:19', '2025-08-07 16:46:19'),
(109, 5, 22, 10, '2025-08-07 16:46:19', '2025-08-07 16:46:19'),
(110, 5, 23, 10, '2025-08-07 16:46:19', '2025-08-07 16:46:19'),
(111, 5, 24, 10, '2025-08-07 16:46:19', '2025-08-07 16:46:19'),
(112, 5, 25, 10, '2025-08-07 16:46:19', '2025-08-07 16:46:19'),
(113, 5, 26, 10, '2025-08-07 16:46:19', '2025-08-07 16:46:19'),
(114, 5, 27, 10, '2025-08-07 16:46:19', '2025-08-07 16:46:19'),
(115, 5, 28, 10, '2025-08-07 16:46:19', '2025-08-07 16:46:19'),
(116, 5, 29, 10, '2025-08-07 16:46:19', '2025-08-07 16:46:19'),
(117, 5, 30, 10, '2025-08-07 16:46:19', '2025-08-07 16:46:19'),
(118, 5, 31, 10, '2025-08-07 16:46:19', '2025-08-07 16:46:19'),
(119, 5, 32, 10, '2025-08-07 16:46:19', '2025-08-07 16:46:19'),
(120, 5, 33, 10, '2025-08-07 16:46:19', '2025-08-07 16:46:19'),
(121, 5, 34, 10, '2025-08-07 16:46:19', '2025-08-07 16:46:19'),
(122, 5, 35, 0, '2025-08-07 16:46:19', '2025-08-20 03:18:40'),
(123, 5, 36, 10, '2025-08-07 16:46:19', '2025-08-07 16:46:19');

-- --------------------------------------------------------

--
-- Table structure for table `finished_products`
--

CREATE TABLE `finished_products` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `category_id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED NOT NULL,
  `price` int UNSIGNED NOT NULL DEFAULT '0',
  `minimum_stock` int UNSIGNED DEFAULT NULL,
  `stock` int UNSIGNED NOT NULL DEFAULT '0',
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `production_cost` int UNSIGNED DEFAULT NULL,
  `base_cost` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Modal dasar produk - tidak boleh melebihi harga jual',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `finished_products`
--

INSERT INTO `finished_products` (`id`, `name`, `code`, `description`, `category_id`, `unit_id`, `price`, `minimum_stock`, `stock`, `photo`, `production_cost`, `base_cost`, `is_active`, `created_at`, `updated_at`) VALUES
(14, 'Dada Crispy', 'HL9', 'Imported from spreadsheet data', 13, 1, 13000, 5, 52, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-08 14:07:30'),
(15, 'Paha Atas Crispy', 'HL10', 'Imported from spreadsheet data', 13, 1, 13000, 0, 49, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-08 14:07:30'),
(16, 'Paha Bawah Crispy', 'HL11', 'Imported from spreadsheet data', 13, 1, 11000, 5, 47, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-20 03:19:20'),
(17, 'Sayap Crispy', 'HL12', 'Imported from spreadsheet data', 13, 1, 9000, 0, 48, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-08 14:07:30'),
(18, 'Air Mineral', 'HL13', 'Imported from spreadsheet data', 14, 1, 5000, 5, 23, 'products/finished/1754953494_689a77167faea.jfif', 1, 0, 1, '2025-07-30 07:24:41', '2025-08-24 17:54:15'),
(19, 'Es Choco Lavar', 'HL14', 'Imported from spreadsheet data', 14, 1, 11000, 0, 42, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-08 14:07:30'),
(20, 'Es Batu Cup', 'HL15', 'Imported from spreadsheet data', 14, 1, 2000, 0, 57, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-08 14:07:30'),
(21, 'Es Batu Plastik Small', 'HL16', 'Imported from spreadsheet data', 14, 1, 1000, 0, 61, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-08 14:07:30'),
(22, 'Es Milo', 'HL19', 'Imported from spreadsheet data', 14, 1, 10000, 0, 65, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-08 14:07:30'),
(23, 'Es Teh', 'HL20', 'Imported from spreadsheet data', 14, 1, 5000, 0, 41, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-08 14:07:30'),
(24, 'Es Pink Lavar', 'HL21', 'Imported from spreadsheet data', 14, 1, 11000, 0, 60, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-08 14:07:30'),
(25, 'Teh Kotak', 'HL22', 'Imported from spreadsheet data', 14, 1, 6000, 0, 43, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-08 14:07:30'),
(26, 'Dada Geprek', 'HL23', 'Imported from spreadsheet data', 15, 1, 16000, 0, 43, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-08 14:07:30'),
(27, 'Paha Atas Geprek', 'HL24', 'Imported from spreadsheet data', 15, 1, 16000, 0, 56, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-08 14:07:30'),
(28, 'Paha Bawah Geprek', 'HL25', 'Imported from spreadsheet data', 15, 1, 14000, 0, 68, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-08 14:07:30'),
(29, 'Sayap Geprek', 'HL26', 'Imported from spreadsheet data', 15, 1, 12000, 0, 70, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-08 14:07:30'),
(30, 'Sambal Geprek Cup', 'HL56', 'Imported from spreadsheet data', 16, 1, 4000, 0, 47, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-08 14:07:30'),
(31, 'Saos Barbeque Cup', 'HL57', 'Imported from spreadsheet data', 16, 1, 4000, 0, 44, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-08 14:07:30'),
(32, 'Saos Cheese Cup', 'HL58', 'Imported from spreadsheet data', 16, 1, 4000, 0, 67, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-08 14:07:30'),
(33, 'Saos Hot Lavar Cup', 'HL59', 'Imported from spreadsheet data', 16, 1, 4000, 0, 47, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-08 14:07:30'),
(34, 'Kentang Crispy', 'HL60', 'Imported from spreadsheet data', 17, 1, 10000, 0, 51, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-08 14:07:30'),
(35, 'Kulit Crispy', 'HL61', 'Imported from spreadsheet data', 17, 1, 9000, 0, 46, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-08 14:07:30'),
(36, 'Nasi', 'HL63', 'Imported from spreadsheet data', 17, 1, 5000, 0, 64, NULL, 0, 0, 1, '2025-07-30 07:24:41', '2025-08-08 14:07:30');

-- --------------------------------------------------------

--
-- Table structure for table `material_supplier`
--

CREATE TABLE `material_supplier` (
  `id` bigint UNSIGNED NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `raw_material_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2024_12_15_000000_create_complete_database_structure', 1),
(3, '2024_12_16_000000_create_user_roles_and_permissions_tables', 1),
(4, '2024_12_16_000001_update_users_table_remove_role_id', 1),
(5, '2024_12_17_000000_create_additional_tables', 1),
(6, '2025_07_22_231112_create_inventory_tables', 2),
(7, '2025_07_22_231947_add_group_to_permissions_table', 3),
(8, '2025_07_22_234200_add_is_active_to_units_table', 4),
(9, '2025_07_22_235000_rename_units_columns', 5),
(10, '2025_07_22_235500_create_production_requests_table', 6),
(11, '2025_07_23_000000_create_distributions_table', 7),
(12, '2025_07_23_002411_add_avatar_to_users_table', 8),
(13, '2025_07_23_170400_add_type_to_branches_table', 9),
(14, '2025_07_25_000000_create_material_supplier_table', 10),
(15, '2025_07_26_043100_add_category_id_to_raw_materials_table', 11),
(16, '2025_07_26_074700_add_is_centralized_to_raw_materials_table', 12),
(17, '2025_07_26_112000_add_supplier_id_to_raw_materials_table', 13),
(18, '2025_07_26_170000_update_semi_finished_products_add_fields', 14),
(19, '2025_07_26_182200_create_semi_finished_branch_stocks_table', 15),
(20, '2025_07_26_183000_update_semi_finished_branch_stocks_add_columns', 16),
(21, '2025_07_26_231926_add_image_to_semi_finished_products_table', 17),
(22, '2025_07_28_000000_remove_minimum_stock_from_branch_stocks', 18),
(23, '2025_07_28_052539_add_image_to_raw_materials_table', 19),
(24, '2025_07_28_173755_add_current_stock_to_raw_materials_table', 20),
(25, '2025_07_28_203229_add_material_type_to_categories_table', 21),
(26, '2025_07_28_212114_add_category_id_to_semi_finished_products_table', 22),
(27, '2025_07_28_213000_add_category_id_to_semi_finished_products', 22),
(28, '2025_07_28_213454_add_code_to_categories_table', 23),
(29, '2025_07_28_213806_add_code_column_to_categories_table', 23),
(30, '2025_07_28_214753_add_code_column_to_categories_table_fix', 24),
(31, '2025_07_28_214849_fix_categories_code_column', 24),
(32, '2025_07_28_221226_fix_categories_code_column_final', 25),
(33, '2025_07_29_000001_add_is_active_to_categories_table', 26),
(34, '2025_07_29_100000_create_finished_branch_stocks_table', 27),
(37, '2025_07_29_180533_add_missing_fields_to_finished_products_table', 28),
(38, '2025_07_29_183215_add_minimum_stock_to_finished_branch_stocks_table', 29),
(39, '2025_07_29_183454_add_minimum_stock_column_to_finished_branch_stocks', 29),
(40, '2025_07_29_183934_remove_minimum_stock_from_finished_branch_stocks', 29),
(41, '2025_07_29_184704_rename_min_stock_to_minimum_stock_in_finished_products_table', 30),
(42, '2025_07_29_200604_add_is_active_to_raw_materials_table', 31),
(43, '2025_07_29_205042_create_purchase_orders_table', 31),
(44, '2025_07_29_205151_create_purchase_order_items_table', 31),
(45, '2025_07_30_200000_create_purchase_receipts_table', 32),
(46, '2025_07_30_200001_create_purchase_receipt_items_table', 32),
(47, '2025_07_31_000000_restructure_purchase_system', 33),
(48, '2025_07_31_111622_fix_purchase_orders_structure', 33),
(49, '2025_07_31_111915_fix_purchase_order_items_structure', 33),
(50, '2025_07_31_151444_add_phone_to_users_table', 34),
(51, '2024_12_31_160000_simplify_purchase_tables', 35),
(52, '2025_07_31_194500_update_purchase_orders_status_enum', 36),
(53, '2025_08_01_054124_add_requested_by_to_production_requests_table', 37),
(54, '2025_08_01_124622_update_requested_quantity_in_production_requests_table', 38),
(55, '2025_08_01_131700_create_production_request_items_table', 39),
(56, '2025_08_01_131701_create_production_request_outputs_table', 39),
(57, '2025_08_01_150205_add_branch_id_to_production_requests_table', 40),
(58, '2025_08_01_150553_add_branch_id_column_to_production_requests', 40),
(59, '2025_08_01_160709_add_approval_fields_to_production_requests_table', 41),
(60, '2025_08_01_164817_add_production_process_fields_to_production_requests_table', 42),
(61, '2025_08_01_182210_create_purchase_receipt_additional_costs_table', 43),
(62, '2025_08_02_140138_add_unit_id_to_purchase_order_items_table', 44),
(63, '2025_08_03_221929_add_is_active_to_suppliers_table', 45),
(64, '2025_01_04_101500_create_sales_packages_table', 46),
(65, '2025_01_04_101501_create_sales_package_items_table', 46),
(66, '2025_08_05_175746_add_category_id_to_sales_packages_table', 47),
(67, '2025_08_07_155516_create_sales_table', 48),
(68, '2025_08_07_155554_create_sale_items_table', 49),
(69, '2025_08_07_053218_create_sale_items_table', 50),
(70, '2025_08_07_103000_create_new_sales_system_tables', 51),
(71, '2025_08_07_220609_create_sales_table', 52),
(72, '2025_08_07_220619_create_sale_items_table', 53),
(73, '2025_08_04_000001_create_purchase_system_tables', 54),
(74, '2025_08_04_192139_add_category_field_to_sales_packages_table', 54),
(75, '2025_08_07_223000_add_stock_column_to_finished_products_table', 54),
(76, '2025_08_08_120000_alter_order_code_length_in_purchase_orders_table', 54),
(77, '2025_08_10_055600_add_requested_delivery_date_to_purchase_orders_table', 55),
(78, '2025_08_11_100000_add_totals_columns_to_purchase_receipts_table', 56),
(79, '2025_08_12_010950_drop_conversion_recipes_tables', 57),
(80, '2025_08_12_031300_alter_requested_quantity_to_decimal_on_production_requests', 58),
(81, '2025_08_12_120000_add_production_process_columns_to_production_requests', 59),
(82, '2025_08_12_130500_create_production_request_histories_table', 60),
(83, '2025_08_12_140500_add_semi_finished_product_id_to_distributions_table', 61),
(84, '2025_08_12_163901_drop_production_and_distribution_tables', 62),
(85, '2025_08_12_172224_create_production_requests_table', 62),
(86, '2025_08_12_172338_create_production_request_items_table', 63),
(87, '2025_08_12_172400_create_semi_finished_distributions_table', 63),
(88, '2025_08_12_120000_create_production_request_outputs_table', 64),
(89, '2025_08_13_062255_add_evidence_path_to_production_requests', 65),
(90, '2025_08_13_070600_add_last_updated_to_semi_finished_branch_stocks', 66),
(91, '2025_08_13_095600_add_branch_id_to_production_requests_table', 67),
(92, '2025_08_15_064100_add_estimated_output_quantity_to_production_requests_table', 68),
(93, '2025_08_16_140000_create_material_usage_requests_table', 69),
(94, '2025_08_16_142942_add_is_centralized_to_semi_finished_products_table', 69),
(95, '2025_08_16_162800_rename_material_usage_tables_to_semi_finished', 69),
(96, '2025_08_16_173500_update_semi_finished_usage_requests_schema', 69),
(97, '2025_08_16_000000_create_semi_finished_usage_request_targets_table', 70),
(98, '2025_08_19_000001_create_stock_movements_table', 71),
(99, '2025_08_19_000001_rename_material_usage_request_fk_to_semi_finished_request', 72),
(100, '2025_08_19_000002_create_stock_transfers_table', 73),
(101, '2025_08_23_195100_add_base_cost_to_finished_products_table', 74);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `code`, `group`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Manage Users', 'MANAGE_USERS', NULL, 'Can manage users', 1, '2025-07-22 15:10:31', '2025-07-22 15:10:31'),
(2, 'Manage Inventory', 'MANAGE_INVENTORY', NULL, 'Can manage inventory', 1, '2025-07-22 15:10:31', '2025-07-22 15:10:31'),
(3, 'Manage Sales', 'MANAGE_SALES', NULL, 'Can manage sales', 1, '2025-07-22 15:10:31', '2025-07-22 15:10:31'),
(4, 'View Reports', 'VIEW_REPORTS', NULL, 'Can view reports', 1, '2025-07-22 15:10:31', '2025-07-22 15:10:31'),
(5, 'Manage Branches', 'MANAGE_BRANCHES', NULL, 'Can manage branches', 1, '2025-07-22 15:10:31', '2025-07-22 15:10:31');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `production_requests`
--

CREATE TABLE `production_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `request_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `requested_by` bigint UNSIGNED NOT NULL,
  `purpose` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_raw_material_cost` int UNSIGNED NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected','in_progress','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_notes` text COLLATE utf8mb4_unicode_ci,
  `production_started_by` bigint UNSIGNED DEFAULT NULL,
  `production_started_at` timestamp NULL DEFAULT NULL,
  `production_completed_by` bigint UNSIGNED DEFAULT NULL,
  `production_completed_at` timestamp NULL DEFAULT NULL,
  `production_notes` text COLLATE utf8mb4_unicode_ci,
  `evidence_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `production_requests`
--

INSERT INTO `production_requests` (`id`, `request_code`, `branch_id`, `requested_by`, `purpose`, `total_raw_material_cost`, `notes`, `status`, `approved_by`, `approved_at`, `approval_notes`, `production_started_by`, `production_started_at`, `production_completed_by`, `production_completed_at`, `production_notes`, `evidence_path`, `created_at`, `updated_at`) VALUES
(7, 'PR-20250813-001', NULL, 1, '', 40000, NULL, 'completed', 1, '2025-08-12 21:55:54', 'laksanaken', 1, '2025-08-12 21:56:13', 1, '2025-08-12 23:08:17', 'ssdaf', 'production-evidence/production_7_1755040097.jfif', '2025-08-12 21:46:35', '2025-08-12 23:08:17'),
(8, 'PR-20250815-001', NULL, 1, 'asd', 1000, NULL, 'rejected', 1, '2025-08-15 12:10:14', 'seff', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-15 09:36:22', '2025-08-15 12:10:14'),
(9, 'PR-20250815-002', NULL, 1, 'fdsgdfg', 13000, NULL, 'rejected', 1, '2025-08-15 12:10:14', 'seff', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-15 12:09:52', '2025-08-15 12:10:14');

-- --------------------------------------------------------

--
-- Table structure for table `production_request_items`
--

CREATE TABLE `production_request_items` (
  `id` bigint UNSIGNED NOT NULL,
  `production_request_id` bigint UNSIGNED NOT NULL,
  `raw_material_id` bigint UNSIGNED NOT NULL,
  `requested_quantity` int UNSIGNED NOT NULL,
  `unit_cost` int UNSIGNED NOT NULL,
  `total_cost` int UNSIGNED NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `production_request_items`
--

INSERT INTO `production_request_items` (`id`, `production_request_id`, `raw_material_id`, `requested_quantity`, `unit_cost`, `total_cost`, `notes`, `created_at`, `updated_at`) VALUES
(7, 7, 20, 1, 40000, 40000, NULL, '2025-08-12 21:46:35', '2025-08-12 21:46:35'),
(8, 8, 30, 1, 1000, 1000, 'sad', '2025-08-15 09:36:22', '2025-08-15 09:36:22'),
(9, 9, 32, 1, 13000, 13000, NULL, '2025-08-15 12:09:52', '2025-08-15 12:09:52');

-- --------------------------------------------------------

--
-- Table structure for table `production_request_outputs`
--

CREATE TABLE `production_request_outputs` (
  `id` bigint UNSIGNED NOT NULL,
  `production_request_id` bigint UNSIGNED NOT NULL,
  `semi_finished_product_id` bigint UNSIGNED NOT NULL,
  `planned_quantity` int UNSIGNED NOT NULL,
  `actual_quantity` int UNSIGNED DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `production_request_outputs`
--

INSERT INTO `production_request_outputs` (`id`, `production_request_id`, `semi_finished_product_id`, `planned_quantity`, `actual_quantity`, `notes`, `created_at`, `updated_at`) VALUES
(7, 7, 74, 1, 1, 'd', '2025-08-12 21:46:35', '2025-08-12 23:08:17'),
(8, 8, 74, 1, NULL, 'sdaf', '2025-08-15 09:36:22', '2025-08-15 09:36:22'),
(9, 9, 74, 1, NULL, 'dfg', '2025-08-15 12:09:52', '2025-08-15 12:09:52');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` bigint UNSIGNED NOT NULL,
  `order_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `order_date` date NOT NULL,
  `requested_delivery_date` date DEFAULT NULL,
  `status` enum('draft','ordered','received','partially_received','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `total_amount` int UNSIGNED NOT NULL DEFAULT '0',
  `ordered_at` timestamp NULL DEFAULT NULL,
  `whatsapp_sent` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `order_number`, `order_code`, `supplier_id`, `created_by`, `order_date`, `requested_delivery_date`, `status`, `notes`, `total_amount`, `ordered_at`, `whatsapp_sent`, `created_at`, `updated_at`) VALUES
(12, 'PO-20250808-0001', 'PO2508080001', 20, 10, '2025-08-08', NULL, 'ordered', NULL, 40000, '2025-08-08 15:37:24', 1, '2025-08-08 14:11:00', '2025-08-09 09:24:27'),
(13, 'PO-20250808-0013', 'PO2508080013', 20, 10, '2025-08-08', NULL, 'ordered', NULL, 40000, '2025-08-08 15:38:28', 1, '2025-08-08 15:38:28', '2025-08-08 15:38:28'),
(16, 'PO-20250809-0015', 'PO2508090015', 20, 1, '2025-08-09', NULL, 'ordered', 'tyfgh', 140000, '2025-08-08 22:00:21', 1, '2025-08-08 22:00:21', '2025-08-11 14:22:35'),
(19, 'PO-20250809-0017', 'PO2508090017', 9, 1, '2025-08-09', NULL, 'received', NULL, 53000, '2025-08-08 23:44:18', 0, '2025-08-08 23:44:18', '2025-08-11 12:28:17'),
(20, 'PO-20250809-0020', 'PO2508090020', 20, 1, '2025-08-09', NULL, 'ordered', 'dfg', 13000, '2025-08-08 23:45:30', 1, '2025-08-08 23:45:16', '2025-08-11 14:22:56'),
(21, 'PO-20250809-0021', 'PO2508090021', 20, 1, '2025-08-09', NULL, 'rejected', 'sdfgdfg', 93000, '2025-08-08 23:49:07', 1, '2025-08-08 23:49:07', '2025-08-11 12:48:29'),
(22, 'PO-20250809-0022', 'PO2508090022', 20, 1, '2025-08-09', NULL, 'ordered', 'htgh', 180000, '2025-08-08 23:50:01', 1, '2025-08-08 23:50:01', '2025-08-08 23:50:01'),
(23, 'PO-20250809-0023', 'PO2508090023', 20, 1, '2025-08-09', NULL, 'ordered', 'dfgfdg', 13000, '2025-08-09 01:35:20', 1, '2025-08-08 23:50:46', '2025-08-09 01:35:23'),
(26, 'PO-20250810-0024', 'PO2508100024', 20, 1, '2025-08-10', '2025-08-13', 'rejected', 'rfgfdg', 40000, '2025-08-09 22:25:05', 1, '2025-08-09 22:25:05', '2025-08-11 15:05:44'),
(27, 'PO-20250811-0027', 'PO2508110027', 20, 1, '2025-08-11', '2025-08-14', 'partially_received', NULL, 66000, '2025-08-11 14:00:24', 1, '2025-08-11 13:58:48', '2025-08-11 14:13:00'),
(28, 'PO-20250811-0028', 'PO2508110028', 20, 1, '2025-08-11', '2025-08-14', 'ordered', NULL, 320000, '2025-08-11 14:05:54', 1, '2025-08-11 14:05:20', '2025-08-11 14:05:54'),
(29, 'PO-20250811-0029', 'PO2508110029', 20, 1, '2025-08-11', '2025-08-14', 'ordered', NULL, 106000, '2025-08-11 14:08:42', 1, '2025-08-11 14:08:37', '2025-08-11 14:08:42'),
(30, 'PO-20250811-0030', 'PO2508110030', 20, 1, '2025-08-11', '2025-08-14', 'received', NULL, 306000, '2025-08-11 14:09:20', 1, '2025-08-11 14:09:20', '2025-08-20 05:16:57'),
(31, 'PO-20250813-0031', 'PO2508130031', 20, 1, '2025-08-13', '2025-08-16', 'draft', NULL, 13000, NULL, 0, '2025-08-12 23:43:51', '2025-08-12 23:43:51');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_order_id` bigint UNSIGNED NOT NULL,
  `raw_material_id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `unit_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `unit_price` int UNSIGNED NOT NULL,
  `total_price` int UNSIGNED NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `raw_material_id`, `unit_id`, `unit_name`, `quantity`, `unit_price`, `total_price`, `notes`, `created_at`, `updated_at`) VALUES
(14, 12, 20, 14, 'Unit', 1, 40000, 40000, NULL, '2025-08-08 14:11:00', '2025-08-08 14:11:00'),
(15, 13, 20, 14, 'Unit', 1, 40000, 40000, NULL, '2025-08-08 15:38:28', '2025-08-08 15:38:28'),
(19, 16, 6, 13, 'Unit', 1, 140000, 140000, 'dwferg', '2025-08-08 22:00:21', '2025-08-08 22:00:21'),
(22, 19, 33, 2, 'Gram', 1, 53000, 53000, 'rtgh', '2025-08-08 23:44:18', '2025-08-08 23:44:18'),
(23, 20, 32, 19, 'bonggol', 1, 13000, 13000, 'sdf', '2025-08-08 23:45:16', '2025-08-08 23:45:16'),
(24, 21, 32, 19, 'bonggol', 1, 13000, 13000, 'dfgfdg', '2025-08-08 23:49:07', '2025-08-08 23:49:07'),
(25, 21, 20, 14, 'Ekor', 2, 40000, 80000, 'dfgfdg', '2025-08-08 23:49:07', '2025-08-08 23:49:07'),
(26, 22, 20, 14, 'Ekor', 1, 40000, 40000, 'aaa', '2025-08-08 23:50:01', '2025-08-08 23:50:01'),
(27, 22, 6, 13, 'Karung', 1, 140000, 140000, 'bbb', '2025-08-08 23:50:01', '2025-08-08 23:50:01'),
(29, 23, 32, 19, 'bonggol', 1, 13000, 13000, 'dfgfdg', '2025-08-09 00:35:36', '2025-08-09 00:35:36'),
(32, 26, 20, 14, 'Ekor', 1, 40000, 40000, 'dsf', '2025-08-09 22:25:05', '2025-08-09 22:25:05'),
(33, 27, 20, 14, 'Ekor', 1, 40000, 40000, '1', '2025-08-11 13:58:48', '2025-08-11 13:58:48'),
(34, 27, 32, 19, 'bonggol', 2, 13000, 26000, '2', '2025-08-11 13:58:48', '2025-08-11 13:58:48'),
(37, 28, 20, 14, 'Ekor', 1, 40000, 40000, 'x', '2025-08-11 14:05:46', '2025-08-11 14:05:46'),
(38, 28, 6, 13, 'Karung', 2, 140000, 280000, NULL, '2025-08-11 14:05:46', '2025-08-11 14:05:46'),
(39, 29, 20, 14, 'Ekor', 2, 40000, 80000, NULL, '2025-08-11 14:08:37', '2025-08-11 14:08:37'),
(40, 29, 32, 19, 'bonggol', 2, 13000, 26000, NULL, '2025-08-11 14:08:37', '2025-08-11 14:08:37'),
(41, 30, 32, 19, 'bonggol', 2, 13000, 26000, NULL, '2025-08-11 14:09:20', '2025-08-11 14:09:20'),
(42, 30, 6, 13, 'Karung', 2, 140000, 280000, NULL, '2025-08-11 14:09:20', '2025-08-11 14:09:20'),
(43, 31, 32, 19, 'bonggol', 1, 13000, 13000, NULL, '2025-08-12 23:43:51', '2025-08-12 23:43:51');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_receipts`
--

CREATE TABLE `purchase_receipts` (
  `id` bigint UNSIGNED NOT NULL,
  `receipt_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_order_id` bigint UNSIGNED NOT NULL,
  `received_by` bigint UNSIGNED NOT NULL,
  `receipt_date` date NOT NULL,
  `status` enum('accepted','rejected','partial') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'accepted',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `receipt_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal_items` int UNSIGNED NOT NULL DEFAULT '0',
  `additional_cost_total` int UNSIGNED NOT NULL DEFAULT '0',
  `discount_amount` int UNSIGNED NOT NULL DEFAULT '0',
  `tax_amount` int UNSIGNED NOT NULL DEFAULT '0',
  `total_amount` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_receipts`
--

INSERT INTO `purchase_receipts` (`id`, `receipt_number`, `purchase_order_id`, `received_by`, `receipt_date`, `status`, `notes`, `receipt_photo`, `subtotal_items`, `additional_cost_total`, `discount_amount`, `tax_amount`, `total_amount`, `created_at`, `updated_at`) VALUES
(29, 'PR-2025-08-003', 26, 1, '2025-08-11', 'rejected', NULL, 'receipts/tlSwxS05dPAQpDkLk9RfIQgBxqODGF5A9H113JHd.jpg', 0, 5000, 0, 0, 5000, '2025-08-11 12:10:15', '2025-08-11 15:05:44'),
(30, 'PR-2025-08-004', 19, 1, '2025-08-11', 'accepted', NULL, NULL, 53000, 0, 5000, 5000, 53000, '2025-08-11 12:28:17', '2025-08-11 12:28:17'),
(32, 'PR-2025-08-006', 21, 1, '2025-08-11', 'rejected', NULL, NULL, 0, 0, 0, 0, 0, '2025-08-11 12:48:29', '2025-08-11 12:48:29'),
(33, 'PR-2025-08-007', 27, 1, '2025-08-11', 'partial', 'awawaw', 'receipts/zNzzZzUhh5QFq1zK44MXZXH49nXbK8wn22cu35BZ.jpg', 13000, 5000, 2000, 2500, 18500, '2025-08-11 14:13:00', '2025-08-11 15:07:45'),
(34, 'PR-2025-08-008', 30, 1, '2025-08-20', 'accepted', NULL, 'receipts/2K7fxL8kjiohur1UiQ7UagqJDjKSFLteHxuerUoz.jpg', 306000, 0, 0, 0, 306000, '2025-08-20 05:16:57', '2025-08-20 05:16:57');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_receipt_additional_costs`
--

CREATE TABLE `purchase_receipt_additional_costs` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_receipt_id` bigint UNSIGNED NOT NULL,
  `cost_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` int UNSIGNED NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_receipt_additional_costs`
--

INSERT INTO `purchase_receipt_additional_costs` (`id`, `purchase_receipt_id`, `cost_name`, `amount`, `notes`, `created_at`, `updated_at`) VALUES
(7, 29, 'ddfgdfg', 5000, 'dfgdfg', '2025-08-11 15:05:44', '2025-08-11 15:05:44'),
(8, 33, 'zzz', 5000, 'tyuytu', '2025-08-11 15:07:45', '2025-08-11 15:07:45');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_receipt_items`
--

CREATE TABLE `purchase_receipt_items` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_receipt_id` bigint UNSIGNED NOT NULL,
  `purchase_order_item_id` bigint UNSIGNED NOT NULL,
  `raw_material_id` bigint UNSIGNED NOT NULL,
  `ordered_quantity` int UNSIGNED NOT NULL,
  `received_quantity` int UNSIGNED NOT NULL,
  `rejected_quantity` int UNSIGNED NOT NULL DEFAULT '0',
  `unit_price` int UNSIGNED NOT NULL,
  `item_status` enum('accepted','rejected','partial') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'accepted',
  `condition_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_receipt_items`
--

INSERT INTO `purchase_receipt_items` (`id`, `purchase_receipt_id`, `purchase_order_item_id`, `raw_material_id`, `ordered_quantity`, `received_quantity`, `rejected_quantity`, `unit_price`, `item_status`, `condition_photo`, `notes`, `created_at`, `updated_at`) VALUES
(29, 29, 32, 20, 1, 0, 1, 40000, 'rejected', 'receipt-items/Bh6HRmZjP7EW7HZApuvlw8h7ihhLgz5TUXL2c2o6.jpg', NULL, '2025-08-11 12:10:15', '2025-08-11 15:05:44'),
(30, 30, 22, 33, 1, 1, 0, 53000, 'accepted', NULL, NULL, '2025-08-11 12:28:17', '2025-08-11 12:28:17'),
(32, 32, 24, 32, 1, 0, 1, 13000, 'rejected', NULL, NULL, '2025-08-11 12:48:29', '2025-08-11 12:48:29'),
(33, 32, 25, 20, 2, 0, 2, 40000, 'rejected', NULL, NULL, '2025-08-11 12:48:29', '2025-08-11 12:48:29'),
(34, 33, 33, 20, 1, 0, 1, 40000, 'rejected', 'receipt-items/6aBLEBzN3pqmfTLlawdYAH6ZEeChhyaLw6tRm0qm.jpg', 'zz', '2025-08-11 14:13:00', '2025-08-11 15:07:45'),
(35, 33, 34, 32, 2, 1, 1, 13000, 'partial', 'receipt-items/ROgluT6YOiUPsTe8bEKACGqMJasJJ9xyW20lZKjb.jpg', 'zzz', '2025-08-11 14:13:00', '2025-08-11 14:13:00'),
(36, 34, 41, 32, 2, 2, 0, 13000, 'accepted', 'receipt-items/3A1vmfYcAJf1igBn6cDstmM2gTA2a8aHtfhzHVZV.jpg', NULL, '2025-08-20 05:16:57', '2025-08-20 05:16:57'),
(37, 34, 42, 6, 2, 2, 0, 140000, 'accepted', 'receipt-items/bXNpdtWG8zM2N2JmAqq4mahG2x1eLus9Ql3Z1une.jpg', NULL, '2025-08-20 05:16:57', '2025-08-20 05:16:57');

-- --------------------------------------------------------

--
-- Table structure for table `raw_materials`
--

CREATE TABLE `raw_materials` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_id` bigint UNSIGNED NOT NULL,
  `minimum_stock` int UNSIGNED DEFAULT NULL,
  `current_stock` int UNSIGNED NOT NULL DEFAULT '0',
  `unit_price` int UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `supplier_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `raw_materials`
--

INSERT INTO `raw_materials` (`id`, `name`, `code`, `category_id`, `description`, `image`, `unit_id`, `minimum_stock`, `current_stock`, `unit_price`, `is_active`, `supplier_id`, `created_at`, `updated_at`) VALUES
(6, 'Bogasari Segitiga Biru 25 KG', 'S1', NULL, 'Imported from spreadsheet data', 'storage/materials/1754074855_omah 1.jfif', 13, 0, 4, 140000, 1, 20, '2025-07-30 06:31:26', '2025-08-20 05:16:57'),
(7, 'Bubuk Lada Putih', 'S2', NULL, 'Imported from spreadsheet data', NULL, 6, 2, 4, 134500, 1, NULL, '2025-07-30 06:31:26', '2025-07-30 06:31:26'),
(8, 'Bubuk Jinten', 'S3', NULL, 'Imported from spreadsheet data', NULL, 6, 2, 2, 12500, 1, 24, '2025-07-30 06:31:26', '2025-08-01 23:48:18'),
(9, 'Baking Powder', 'S4', NULL, 'Imported from spreadsheet data', NULL, 6, 2, 0, 64000, 1, 25, '2025-07-30 06:31:26', '2025-08-02 10:09:03'),
(10, 'Kapal Laut Garam 500 g', 'S5', NULL, 'Imported from spreadsheet data', NULL, 6, 5, 27, 5000, 1, NULL, '2025-07-30 06:31:26', '2025-07-30 06:31:26'),
(11, 'Bubuk Ketumbar', 'S6', NULL, 'Imported from spreadsheet data', NULL, 6, 2, 2, 68000, 1, NULL, '2025-07-30 06:31:26', '2025-07-30 06:31:26'),
(12, 'Bubuk Cabai', 'S7', NULL, 'Imported from spreadsheet data', NULL, 6, 2, 4, 82500, 1, NULL, '2025-07-30 06:31:26', '2025-07-30 06:31:26'),
(13, 'Bubuk Marinasi', 'S9', NULL, 'Imported from spreadsheet data', NULL, 2, 3500, 2394, 653051, 1, NULL, '2025-07-30 06:31:26', '2025-07-30 06:31:26'),
(14, 'Bubuk Bawang Bombai', 'S10', NULL, 'Imported from spreadsheet data', NULL, 6, 2, 5, 91500, 1, 24, '2025-07-30 06:31:26', '2025-08-02 10:08:37'),
(15, 'Bubuk Bawang Putih', 'S11', 2, 'Imported from spreadsheet data', NULL, 6, 2, 8, 77185, 1, 24, '2025-07-30 06:31:26', '2025-08-02 10:09:51'),
(16, 'Bubuk Jahe', 'S12', NULL, 'Imported from spreadsheet data', NULL, 6, 2, 4, 85000, 1, NULL, '2025-07-30 06:31:26', '2025-07-30 06:31:26'),
(17, 'Bubuk Lada Hitam', 'S13', NULL, 'Imported from spreadsheet data', NULL, 6, 2, 3, 89000, 1, NULL, '2025-07-30 06:31:26', '2025-07-30 06:31:26'),
(18, 'Bubuk Cengkeh', 'S14', NULL, 'Imported from spreadsheet data', NULL, 6, 2, 2, 125050, 1, NULL, '2025-07-30 06:31:27', '2025-07-30 06:31:27'),
(19, 'Daun Oregano', 'S15', NULL, 'Imported from spreadsheet data', NULL, 6, 2, 6, 177500, 1, NULL, '2025-07-30 06:31:27', '2025-07-30 06:31:27'),
(20, 'Ayam Pedaging 1,1 KG', 'S16', 2, 'Imported from spreadsheet data', NULL, 14, 15, 0, 40000, 1, 20, '2025-07-30 06:31:27', '2025-08-12 21:55:54'),
(23, 'Sarana Pangan Kulit Ayam 1 pack @500 gr', 'S28', NULL, 'Imported from spreadsheet data', NULL, 6, 6, 8, 24000, 1, NULL, '2025-07-30 06:31:27', '2025-07-30 06:31:27'),
(25, 'Plastik Marinasi M', 'S32', NULL, 'Imported from spreadsheet data', NULL, 6, 0, 10, 860000, 1, NULL, '2025-07-30 06:31:27', '2025-07-30 06:31:27'),
(27, 'Plastik Marinasi L', 'S43', NULL, 'Imported from spreadsheet data', NULL, 6, 0, 9, 38500, 1, NULL, '2025-07-30 06:31:27', '2025-07-30 06:31:27'),
(28, 'Plastik Marinasi XL', 'S49', NULL, 'Imported from spreadsheet data', NULL, 16, 0, 2, 156000, 1, NULL, '2025-07-30 06:31:27', '2025-07-30 06:31:27'),
(30, 'Lemon', 'S55', NULL, 'Imported from spreadsheet data', NULL, 17, 20, 24, 1000, 1, NULL, '2025-07-30 06:31:27', '2025-07-30 06:31:27'),
(31, 'Silica Gel', 'S66', NULL, 'Imported from spreadsheet data', NULL, 17, 0, 21, 10000, 1, NULL, '2025-07-30 06:31:27', '2025-07-30 06:31:27'),
(32, 'Bawang Putih', 'S70', 2, 'Imported from spreadsheet data', 'storage/materials/1754074868_kulit 25 pack.jfif', 19, 2, 20, 13000, 1, 20, '2025-07-30 06:31:27', '2025-08-20 05:16:57'),
(33, 'Cabai Tiung', 'S71', NULL, 'Imported from spreadsheet data', NULL, 2, 250, 253, 53000, 1, 9, '2025-07-30 06:31:27', '2025-08-11 12:28:17'),
(34, 'Royco Ayam', 'S72', NULL, 'Imported from spreadsheet data', NULL, 20, 0, 0, 37000, 1, NULL, '2025-07-30 06:31:27', '2025-07-30 06:31:27'),
(35, 'Minyak Goreng Cair 2L', 'S62', NULL, 'Imported from spreadsheet data', NULL, 1, 500, 1200, 34000, 1, NULL, '2025-07-30 06:34:01', '2025-07-30 06:34:01');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `code`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'SUPER_ADMIN', 'Administrator dengan akses penuh', 1, '2025-07-22 15:10:31', '2025-07-22 15:10:31'),
(3, 'Kepala Produksi', 'KEPALA_PRODUKSI', 'Kepala Produksi memimpin proses Produksi di Pusat Produksi', 1, '2025-07-22 15:10:31', '2025-07-24 14:45:42'),
(4, 'Kepala Toko', 'KEPALA_TOKO', 'Kepala Toko bertugas memimpin operasional cabang', 1, '2025-07-24 13:02:02', '2025-07-24 13:02:29'),
(8, 'Kru Produksi', 'KRU_PRODUKSI', 'Kru produksi memproses pengolahan bahan mentah menjadi bahan setengah jadi', 1, '2025-08-02 12:05:56', '2025-08-02 12:05:56'),
(9, 'Kru Toko', 'KRU_TOKO', 'Kru Toko mengolah bahan setengah jadi ke produk siap jual dan berjualan kasir', 1, '2025-08-02 22:43:11', '2025-08-02 22:43:11'),
(10, 'Manajer', 'MANAGER', 'Manajer Melakukan Pembelian Bahan, Persetujuan Produksi, Mengelola Laporan', 1, '2025-08-02 22:44:26', '2025-08-02 22:44:26');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `permission_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `created_at`, `updated_at`) VALUES
(6, 1, 5, NULL, NULL),
(7, 1, 2, NULL, NULL),
(8, 1, 3, NULL, NULL),
(9, 1, 1, NULL, NULL),
(10, 1, 4, NULL, NULL),
(11, 3, 2, NULL, NULL),
(12, 4, 5, NULL, NULL),
(13, 4, 3, NULL, NULL),
(14, 4, 4, NULL, NULL),
(30, 9, 3, NULL, NULL),
(31, 10, 5, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` bigint UNSIGNED NOT NULL,
  `sale_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal_amount` int UNSIGNED NOT NULL,
  `discount_type` enum('none','percentage','nominal') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `discount_value` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount_amount` int UNSIGNED NOT NULL,
  `final_amount` int UNSIGNED NOT NULL,
  `payment_method` enum('cash','qris') COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid_amount` int UNSIGNED NOT NULL,
  `change_amount` int UNSIGNED NOT NULL DEFAULT '0',
  `status` enum('pending','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `sale_number`, `sale_code`, `branch_id`, `user_id`, `customer_name`, `customer_phone`, `subtotal_amount`, `discount_type`, `discount_value`, `discount_amount`, `final_amount`, `payment_method`, `paid_amount`, `change_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 'TRX-20250808-0001', 'SL2508085130', 6, 10, NULL, NULL, 15000, 'nominal', '0.00', 0, 15000, 'qris', 15000, 0, 'completed', '2025-08-07 22:37:30', '2025-08-07 22:37:30'),
(2, 'TRX-20250808-0002', 'SL2508088888', 6, 10, NULL, NULL, 15000, 'nominal', '0.00', 0, 15000, 'qris', 15000, 0, 'completed', '2025-08-07 22:37:40', '2025-08-07 22:37:40'),
(3, 'TRX-20250808-0003', 'SL2508083159', 1, 10, NULL, NULL, 5000, 'nominal', '0.00', 0, 5000, 'qris', 5000, 0, 'completed', '2025-08-07 23:17:59', '2025-08-07 23:17:59'),
(4, 'TRX-20250808-0004', 'SL2508089095', 1, 10, NULL, NULL, 2000, 'nominal', '0.00', 0, 2000, 'qris', 2000, 0, 'completed', '2025-08-07 23:23:32', '2025-08-07 23:23:32'),
(5, 'TRX-20250812-0001', 'SL2508121027', 3, 1, NULL, NULL, 5000, 'nominal', '0.00', 0, 5000, 'cash', 50000, 45000, 'completed', '2025-08-11 21:42:04', '2025-08-11 21:42:04'),
(6, 'TRX-20250823-0001', 'SL2508232344', 6, 1, NULL, NULL, 101000, 'nominal', '0.00', 0, 101000, 'qris', 101000, 0, 'completed', '2025-08-22 22:24:11', '2025-08-22 22:24:11'),
(7, 'TRX-20250823-0002', 'SL2508238081', 6, 1, NULL, NULL, 101000, 'nominal', '0.00', 0, 101000, 'qris', 101000, 0, 'completed', '2025-08-22 22:24:13', '2025-08-22 22:24:13');

-- --------------------------------------------------------

--
-- Table structure for table `sales_packages`
--

CREATE TABLE `sales_packages` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `base_price` int UNSIGNED NOT NULL,
  `discount_amount` int UNSIGNED NOT NULL DEFAULT '0',
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `additional_charge` int UNSIGNED NOT NULL DEFAULT '0',
  `final_price` int UNSIGNED NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales_packages`
--

INSERT INTO `sales_packages` (`id`, `name`, `code`, `description`, `category_id`, `base_price`, `discount_amount`, `discount_percentage`, `additional_charge`, `final_price`, `image`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Paket Lapar Aja', 'PKG-20250804-238', 'asfdsg', 14, 11000, 2000, '0.00', 5000, 14000, 'sales-packages/aBXyJ17xH2l9fgmPLkTyltfnRk491DcIR8EEqlLq.jpg', 1, 1, '2025-08-04 11:05:13', '2025-08-23 00:48:00'),
(5, 'dsdgfg', 'PKG-20250823-167', 'fdgdfg', 2, 18000, 0, '0.00', 0, 18000, 'sales-packages/bi7z4vSXq83ZcyME5Hzf73IsWIEruQBjL8idY1WH.jpg', 1, 1, '2025-08-23 00:48:21', '2025-08-23 00:57:17');

-- --------------------------------------------------------

--
-- Table structure for table `sales_package_items`
--

CREATE TABLE `sales_package_items` (
  `id` bigint UNSIGNED NOT NULL,
  `sales_package_id` bigint UNSIGNED NOT NULL,
  `finished_product_id` bigint UNSIGNED NOT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `unit_price` int UNSIGNED NOT NULL,
  `total_price` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales_package_items`
--

INSERT INTO `sales_package_items` (`id`, `sales_package_id`, `finished_product_id`, `quantity`, `unit_price`, `total_price`, `created_at`, `updated_at`) VALUES
(32, 1, 18, 1, 5000, 5000, '2025-08-23 00:56:30', '2025-08-23 00:56:30'),
(33, 1, 25, 1, 6000, 6000, '2025-08-23 00:56:30', '2025-08-23 00:56:30'),
(34, 5, 28, 1, 14000, 14000, '2025-08-23 00:57:17', '2025-08-23 00:57:17'),
(35, 5, 30, 1, 4000, 4000, '2025-08-23 00:57:17', '2025-08-23 00:57:17');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` bigint UNSIGNED NOT NULL,
  `sale_id` bigint UNSIGNED NOT NULL,
  `item_type` enum('product','package') COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` bigint UNSIGNED NOT NULL,
  `item_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` int UNSIGNED NOT NULL,
  `subtotal` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `item_type`, `item_id`, `item_name`, `quantity`, `unit_price`, `subtotal`, `created_at`, `updated_at`) VALUES
(1, 1, 'product', 25, 'Teh Kotak', 1, 6000, 6000, '2025-08-07 22:37:30', '2025-08-07 22:37:30'),
(2, 1, 'product', 17, 'Sayap Crispy', 1, 9000, 9000, '2025-08-07 22:37:31', '2025-08-07 22:37:31'),
(3, 2, 'product', 25, 'Teh Kotak', 1, 6000, 6000, '2025-08-07 22:37:40', '2025-08-07 22:37:40'),
(4, 2, 'product', 17, 'Sayap Crispy', 1, 9000, 9000, '2025-08-07 22:37:40', '2025-08-07 22:37:40'),
(5, 3, 'product', 36, 'Nasi', 1, 5000, 5000, '2025-08-07 23:17:59', '2025-08-07 23:17:59'),
(6, 4, 'product', 20, 'Es Batu Cup', 1, 2000, 2000, '2025-08-07 23:23:32', '2025-08-07 23:23:32'),
(7, 5, 'product', 18, 'Air Mineral', 1, 5000, 5000, '2025-08-11 21:42:04', '2025-08-11 21:42:04'),
(8, 6, 'product', 19, 'Es Choco Lavar', 6, 11000, 66000, '2025-08-22 22:24:11', '2025-08-22 22:24:11'),
(9, 6, 'product', 23, 'Es Teh', 7, 5000, 35000, '2025-08-22 22:24:11', '2025-08-22 22:24:11'),
(10, 7, 'product', 19, 'Es Choco Lavar', 6, 11000, 66000, '2025-08-22 22:24:13', '2025-08-22 22:24:13'),
(11, 7, 'product', 23, 'Es Teh', 7, 5000, 35000, '2025-08-22 22:24:13', '2025-08-22 22:24:13');

-- --------------------------------------------------------

--
-- Table structure for table `semi_finished_branch_stocks`
--

CREATE TABLE `semi_finished_branch_stocks` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `semi_finished_product_id` bigint UNSIGNED NOT NULL,
  `quantity` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_updated` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `semi_finished_branch_stocks`
--

INSERT INTO `semi_finished_branch_stocks` (`id`, `branch_id`, `semi_finished_product_id`, `quantity`, `created_at`, `updated_at`, `last_updated`) VALUES
(34, 1, 74, 0, '2025-08-02 10:40:55', '2025-08-02 10:40:55', NULL),
(35, 3, 74, 7, '2025-08-02 10:40:55', '2025-08-19 22:26:53', '2025-08-19 22:26:53'),
(36, 5, 74, 14, '2025-08-02 10:40:55', '2025-08-19 22:25:50', '2025-08-19 22:25:50'),
(37, 6, 74, 1, '2025-08-02 10:40:55', '2025-08-16 05:03:26', '2025-08-16 05:03:26');

-- --------------------------------------------------------

--
-- Table structure for table `semi_finished_distributions`
--

CREATE TABLE `semi_finished_distributions` (
  `id` bigint UNSIGNED NOT NULL,
  `distribution_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sent_by` bigint UNSIGNED NOT NULL,
  `target_branch_id` bigint UNSIGNED NOT NULL,
  `semi_finished_product_id` bigint UNSIGNED NOT NULL,
  `quantity_sent` int UNSIGNED NOT NULL,
  `unit_cost` int UNSIGNED NOT NULL,
  `total_cost` int UNSIGNED NOT NULL,
  `distribution_notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('sent','accepted','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `handled_by` bigint UNSIGNED DEFAULT NULL,
  `handled_at` timestamp NULL DEFAULT NULL,
  `response_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `semi_finished_distributions`
--

INSERT INTO `semi_finished_distributions` (`id`, `distribution_code`, `sent_by`, `target_branch_id`, `semi_finished_product_id`, `quantity_sent`, `unit_cost`, `total_cost`, `distribution_notes`, `status`, `handled_by`, `handled_at`, `response_notes`, `created_at`, `updated_at`) VALUES
(1, 'SF-20250816-001', 1, 3, 74, 10, 50000, 500000, 'asfsdf', 'accepted', 1, '2025-08-16 03:17:16', 'asadad', '2025-08-15 23:20:37', '2025-08-16 03:17:16'),
(2, 'SF-20250816-002', 1, 6, 74, 1, 50000, 50000, 'dsf', 'rejected', 1, '2025-08-16 03:19:17', 'fdsf', '2025-08-16 03:18:16', '2025-08-16 03:19:17'),
(3, 'SF-20250816-003', 1, 6, 74, 1, 50000, 50000, 'sdf', 'accepted', 1, '2025-08-16 05:03:26', 'aaa', '2025-08-16 04:54:04', '2025-08-16 05:03:26'),
(4, 'SF-20250820-001', 1, 3, 74, 1, 50000, 50000, 'dserg', 'accepted', 1, '2025-08-19 22:26:53', 'trrrr', '2025-08-19 22:25:50', '2025-08-19 22:26:53');

-- --------------------------------------------------------

--
-- Table structure for table `semi_finished_products`
--

CREATE TABLE `semi_finished_products` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `unit_id` bigint UNSIGNED NOT NULL,
  `minimum_stock` int UNSIGNED DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` int UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_centralized` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `semi_finished_products`
--

INSERT INTO `semi_finished_products` (`id`, `name`, `code`, `description`, `unit_id`, `minimum_stock`, `image`, `unit_price`, `is_active`, `is_centralized`, `created_at`, `updated_at`, `category_id`) VALUES
(74, 'Ayam Marinasi', 'SF-AYA-001', 'Ayam Marinasi 1 Ekor untuk 9 potong ayam krispi (3 dada, 2 paha atas, 2 sayap, 2 paha bawah)', 14, 10, 'products/semi-finished/1754131255_688deb37c5b94.jfif', 50000, 1, 1, '2025-08-02 10:40:55', '2025-08-03 14:16:50', 13);

-- --------------------------------------------------------

--
-- Table structure for table `semi_finished_usage_requests`
--

CREATE TABLE `semi_finished_usage_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `request_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requesting_branch_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `purpose` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_material_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `requested_date` date DEFAULT NULL,
  `required_date` date DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_notes` text COLLATE utf8mb4_unicode_ci,
  `process_started_by` bigint UNSIGNED DEFAULT NULL,
  `process_started_at` timestamp NULL DEFAULT NULL,
  `process_completed_by` bigint UNSIGNED DEFAULT NULL,
  `process_completed_at` timestamp NULL DEFAULT NULL,
  `process_notes` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `semi_finished_usage_requests`
--

INSERT INTO `semi_finished_usage_requests` (`id`, `request_number`, `requesting_branch_id`, `user_id`, `purpose`, `total_material_cost`, `status`, `requested_date`, `required_date`, `approved_by`, `approved_at`, `approval_notes`, `process_started_by`, `process_started_at`, `process_completed_by`, `process_completed_at`, `process_notes`, `notes`, `rejection_reason`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'MUR202508170001', 3, 1, 'fdgdfg', '0.00', 'completed', '2025-08-17', '2025-08-17', 1, '2025-08-19 07:57:15', NULL, NULL, NULL, NULL, NULL, NULL, 'dfgdf', NULL, '2025-08-16 22:05:47', '2025-08-19 09:16:45', NULL),
(3, 'MUR202508180001', 3, 1, 'sdfsdf', '0.00', 'completed', '2025-08-18', '2025-08-18', 1, '2025-08-18 20:41:26', NULL, NULL, NULL, NULL, NULL, NULL, 'sefsef', NULL, '2025-08-17 22:45:05', '2025-08-19 09:15:46', NULL),
(4, 'MUR202508180002', 3, 1, 'asf', '0.00', 'rejected', '2025-08-18', '2025-08-18', 1, '2025-08-18 15:38:30', NULL, NULL, NULL, NULL, NULL, NULL, 'asf', 'sadfsadf', '2025-08-17 22:50:25', '2025-08-18 15:38:30', NULL),
(5, 'MUR202508180003', 3, 1, 'rey', '0.00', 'rejected', '2025-08-18', '2025-08-18', 1, '2025-08-18 15:15:18', NULL, NULL, NULL, NULL, NULL, NULL, 'fg', 'skdfmklsdjfkl', '2025-08-17 23:06:58', '2025-08-18 15:15:18', NULL),
(6, 'MUR202508190001', 6, 1, 'sdfsdf', '0.00', 'pending', '2025-08-19', '2025-08-19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'awawaw', NULL, '2025-08-19 07:48:59', '2025-08-19 07:48:59', NULL),
(11, 'MUR202508190002', 3, 1, 'sdgg', '0.00', 'completed', '2025-08-19', '2025-08-19', 1, '2025-08-19 13:17:48', 'sdgdfg', NULL, NULL, NULL, NULL, NULL, 'sdgsdg', NULL, '2025-08-19 13:06:52', '2025-08-19 13:34:28', NULL),
(13, 'SFR202508190001', 3, 1, 'fdgdfg', '0.00', 'rejected', '2025-08-19', '2025-08-19', 1, '2025-08-19 15:23:14', NULL, NULL, NULL, NULL, NULL, NULL, 'dfg', 'sdfdsf', '2025-08-19 15:22:49', '2025-08-19 15:23:14', NULL),
(14, 'SFR202508190002', 3, 1, 'fghfgh', '0.00', 'completed', '2025-08-19', '2025-08-19', 1, '2025-08-19 15:23:52', 'setujuuu', NULL, NULL, NULL, NULL, NULL, 'dfhgfh', NULL, '2025-08-19 15:23:42', '2025-08-19 15:24:15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `semi_finished_usage_request_items`
--

CREATE TABLE `semi_finished_usage_request_items` (
  `id` bigint UNSIGNED NOT NULL,
  `semi_finished_request_id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `semi_finished_product_id` bigint UNSIGNED NOT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `unit_price` int UNSIGNED NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `semi_finished_usage_request_items`
--

INSERT INTO `semi_finished_usage_request_items` (`id`, `semi_finished_request_id`, `unit_id`, `semi_finished_product_id`, `quantity`, `unit_price`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, 14, 74, 1, 50000, 'aa', '2025-08-16 22:05:47', '2025-08-16 22:05:47'),
(2, 3, 14, 74, 1, 50000, 'aaa', '2025-08-17 22:45:05', '2025-08-17 22:45:05'),
(3, 4, 14, 74, 1, 50000, '1asd', '2025-08-17 22:50:26', '2025-08-17 22:50:26'),
(4, 5, 14, 74, 1, 50000, 'sdf', '2025-08-17 23:06:58', '2025-08-17 23:06:58'),
(5, 6, 14, 74, 1, 50000, 'dfgaa', '2025-08-19 07:48:59', '2025-08-19 07:48:59'),
(10, 11, 14, 74, 1, 50000, 'sdfsdf', '2025-08-19 13:06:52', '2025-08-19 13:06:52'),
(11, 13, 14, 74, 1, 50000, 'fgh', '2025-08-19 15:22:49', '2025-08-19 15:22:49'),
(12, 14, 14, 74, 1, 50000, 'fh', '2025-08-19 15:23:42', '2025-08-19 15:23:42');

-- --------------------------------------------------------

--
-- Table structure for table `semi_finished_usage_request_outputs`
--

CREATE TABLE `semi_finished_usage_request_outputs` (
  `id` bigint UNSIGNED NOT NULL,
  `semi_finished_request_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `planned_quantity` int UNSIGNED NOT NULL,
  `actual_quantity` int UNSIGNED DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `semi_finished_usage_request_outputs`
--

INSERT INTO `semi_finished_usage_request_outputs` (`id`, `semi_finished_request_id`, `product_id`, `planned_quantity`, `actual_quantity`, `notes`, `created_at`, `updated_at`) VALUES
(1, 11, 14, 3, 3, 'sdfgdfg', '2025-08-19 13:06:52', '2025-08-19 13:18:58');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint UNSIGNED NOT NULL,
  `item_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_id` bigint UNSIGNED DEFAULT NULL,
  `material_id` bigint UNSIGNED DEFAULT NULL,
  `finished_product_id` bigint UNSIGNED DEFAULT NULL,
  `semi_finished_product_id` bigint UNSIGNED DEFAULT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `movement_category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int UNSIGNED NOT NULL DEFAULT '0',
  `quantity_before` int UNSIGNED DEFAULT NULL,
  `quantity_moved` int UNSIGNED DEFAULT NULL,
  `quantity_after` int UNSIGNED DEFAULT NULL,
  `unit_cost` int UNSIGNED DEFAULT NULL,
  `total_cost` int UNSIGNED DEFAULT NULL,
  `reference_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `processed_by` bigint UNSIGNED DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `item_type`, `item_id`, `material_id`, `finished_product_id`, `semi_finished_product_id`, `branch_id`, `type`, `movement_category`, `quantity`, `quantity_before`, `quantity_moved`, `quantity_after`, `unit_cost`, `total_cost`, `reference_type`, `reference_id`, `notes`, `created_by`, `processed_by`, `processed_at`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, NULL, NULL, 74, 3, 'out', 'usage', 1, NULL, NULL, NULL, NULL, NULL, 'semi_finished_usage_request', 3, 'Persetujuan Permintaan Penggunaan Bahan #MUR202508180001', 1, NULL, NULL, '2025-08-18 20:41:26', '2025-08-18 20:41:26'),
(2, NULL, NULL, NULL, NULL, 74, 3, 'out', 'usage', 1, NULL, NULL, NULL, NULL, NULL, 'semi_finished_usage_request', 2, 'Persetujuan Permintaan Penggunaan Bahan #MUR202508170001 | Catatan: sadasd', 1, NULL, NULL, '2025-08-19 07:57:15', '2025-08-19 07:57:15'),
(3, NULL, NULL, NULL, NULL, 74, 3, 'out', 'usage', 1, NULL, NULL, NULL, NULL, NULL, 'semi_finished_usage_request', 11, 'Persetujuan Permintaan Penggunaan Bahan #MUR202508190002', 1, NULL, NULL, '2025-08-19 13:17:48', '2025-08-19 13:17:48'),
(4, NULL, NULL, NULL, NULL, NULL, 3, 'in', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Hasil produksi dari MUR #MUR202508190002', 1, NULL, NULL, '2025-08-19 13:34:28', '2025-08-19 13:34:28'),
(5, NULL, NULL, NULL, NULL, 74, 3, 'out', 'usage', 1, NULL, NULL, NULL, NULL, NULL, 'semi_finished_usage_request', 14, 'Persetujuan Permintaan Penggunaan Bahan #SFR202508190002', 1, NULL, NULL, '2025-08-19 15:23:52', '2025-08-19 15:23:52'),
(6, NULL, NULL, NULL, NULL, NULL, 3, 'in', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'inventory_correction - fgtyuj', 1, NULL, NULL, '2025-08-19 15:31:52', '2025-08-19 15:31:52'),
(7, NULL, NULL, NULL, NULL, NULL, 3, 'in', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'production_output - dsf', 1, NULL, NULL, '2025-08-19 15:32:26', '2025-08-19 15:32:26'),
(8, NULL, NULL, NULL, NULL, NULL, 3, 'out', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'production_output - dfgf', 1, NULL, NULL, '2025-08-19 15:32:42', '2025-08-19 15:32:42'),
(9, 'finished_product', 14, NULL, NULL, NULL, 3, 'transfer_out', NULL, 5, NULL, NULL, NULL, NULL, NULL, 'stock_transfer', NULL, 'Transfer ke LC Panglima Batur - 50wadsad', 1, NULL, NULL, '2025-08-19 22:57:07', '2025-08-19 22:57:07'),
(10, 'finished_product', 14, NULL, NULL, NULL, 1, 'transfer_in', NULL, 5, NULL, NULL, NULL, NULL, NULL, 'stock_transfer', 1, 'Transfer dari LC Bundaran Simpang Empat Banjarbaru - dsfsdf', 1, NULL, NULL, '2025-08-19 22:57:40', '2025-08-19 22:57:40'),
(11, 'finished_product', 28, NULL, NULL, NULL, 1, 'transfer_out', NULL, 1, NULL, NULL, NULL, NULL, NULL, 'stock_transfer', NULL, 'Transfer ke LC Sekumpul - dgrg', 1, NULL, NULL, '2025-08-20 02:25:41', '2025-08-20 02:25:41'),
(12, 'finished_product', 14, NULL, NULL, NULL, 1, 'transfer_out', NULL, 1, NULL, NULL, NULL, NULL, NULL, 'stock_transfer', NULL, 'Transfer ke LC Sekumpul - sdre', 1, NULL, NULL, '2025-08-20 02:25:41', '2025-08-20 02:25:41'),
(13, 'finished_product', 28, NULL, NULL, NULL, 6, 'transfer_in', NULL, 1, NULL, NULL, NULL, NULL, NULL, 'stock_transfer', 2, 'Transfer dari LC Panglima Batur - dfgsf', 1, NULL, NULL, '2025-08-20 02:26:53', '2025-08-20 02:26:53'),
(14, 'finished_product', 14, NULL, NULL, NULL, 6, 'transfer_in', NULL, 1, NULL, NULL, NULL, NULL, NULL, 'stock_transfer', 3, 'Transfer dari LC Panglima Batur - gdfg', 1, NULL, NULL, '2025-08-20 02:27:02', '2025-08-20 02:27:02'),
(15, 'finished_product', 16, NULL, NULL, NULL, 5, 'transfer_out', NULL, 1, NULL, NULL, NULL, NULL, NULL, 'stock_transfer', NULL, 'Transfer ke LC Bundaran Simpang Empat Banjarbaru - sadrfdfrf', 1, NULL, NULL, '2025-08-20 03:04:09', '2025-08-20 03:04:09'),
(16, 'finished_product', 14, NULL, NULL, NULL, 1, 'transfer_out', NULL, 1, NULL, NULL, NULL, NULL, NULL, 'stock_transfer', NULL, 'Transfer ke LC Bundaran Simpang Empat Banjarbaru - sdfsdf', 1, NULL, NULL, '2025-08-20 21:54:46', '2025-08-20 21:54:46'),
(17, 'finished_product', 27, NULL, NULL, NULL, 1, 'transfer_out', NULL, 2, NULL, NULL, NULL, NULL, NULL, 'stock_transfer', NULL, 'Transfer ke LC Bundaran Simpang Empat Banjarbaru - fghgfh', 1, NULL, NULL, '2025-08-20 21:54:46', '2025-08-20 21:54:46');

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfers`
--

CREATE TABLE `stock_transfers` (
  `id` bigint UNSIGNED NOT NULL,
  `item_type` enum('finished','semi-finished') COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` bigint UNSIGNED NOT NULL,
  `from_branch_id` bigint UNSIGNED NOT NULL,
  `to_branch_id` bigint UNSIGNED NOT NULL,
  `quantity` decimal(12,3) NOT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('sent','accepted','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `sent_by` bigint UNSIGNED DEFAULT NULL,
  `handled_by` bigint UNSIGNED DEFAULT NULL,
  `handled_at` timestamp NULL DEFAULT NULL,
  `response_notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_transfers`
--

INSERT INTO `stock_transfers` (`id`, `item_type`, `item_id`, `from_branch_id`, `to_branch_id`, `quantity`, `notes`, `status`, `sent_by`, `handled_by`, `handled_at`, `response_notes`, `created_at`, `updated_at`) VALUES
(1, 'finished', 14, 3, 1, '5.000', '50wadsad', 'accepted', 1, 1, '2025-08-19 22:57:40', 'dsfsdf', '2025-08-19 22:57:07', '2025-08-19 22:57:40'),
(2, 'finished', 28, 1, 6, '1.000', 'dgrg', 'accepted', 1, 1, '2025-08-20 02:26:53', 'dfgsf', '2025-08-20 02:25:41', '2025-08-20 02:26:53'),
(3, 'finished', 14, 1, 6, '1.000', 'sdre', 'accepted', 1, 1, '2025-08-20 02:27:02', 'gdfg', '2025-08-20 02:25:41', '2025-08-20 02:27:02'),
(4, 'finished', 16, 5, 3, '1.000', 'sadrfdfrf', 'sent', 1, NULL, NULL, NULL, '2025-08-20 03:04:09', '2025-08-20 03:04:09'),
(5, 'finished', 14, 1, 3, '1.000', 'sdfsdf', 'sent', 1, NULL, NULL, NULL, '2025-08-20 21:54:46', '2025-08-20 21:54:46'),
(6, 'finished', 27, 1, 3, '2.000', 'fghgfh', 'sent', 1, NULL, NULL, NULL, '2025-08-20 21:54:46', '2025-08-20 21:54:46');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `code`, `address`, `phone`, `email`, `is_active`, `created_at`, `updated_at`) VALUES
(8, 'Toko Plastik Arkana', 'TOKOP', '', '082251187059', NULL, 1, '2025-07-30 06:34:53', '2025-07-30 06:34:53'),
(9, 'Agen Jeruk Nipis (Depan Toko merah)', 'AGENJ', NULL, '6282255647148', NULL, 0, '2025-07-30 06:34:53', '2025-08-11 17:31:03'),
(10, 'Al Yasmin Market (Zahra Mtp)', 'ALYAS', 'Jl. Karang Anyar 1 2', '', NULL, 1, '2025-07-30 06:34:53', '2025-07-30 06:34:53'),
(14, 'Toko Bahan Kue Nabil', 'TOKOB', 'https://goo.gl/maps/irqsqiATWrT9pDmFA', '081220091969', NULL, 1, '2025-07-30 06:34:53', '2025-07-30 06:34:53'),
(15, 'Es Kristal Omah', 'ESKRI', '', '08111833535', NULL, 1, '2025-07-30 06:34:53', '2025-07-30 06:34:53'),
(16, 'DBWood Shoppe', 'DBWOO', 'Surabaya', '-', NULL, 1, '2025-07-30 06:34:53', '2025-07-30 06:34:53'),
(18, 'PT. Puji Surya Indah', 'PTPUJ', '', '085100252286', NULL, 1, '2025-07-30 06:34:53', '2025-07-30 06:34:53'),
(20, 'Agen Tepung Pasar Sekumpul', 'AGENT', 'https://goo.gl/maps/iLcJybhKuQ5Fm1K18', '6282255647148', NULL, 1, '2025-07-30 06:34:53', '2025-07-31 09:26:10'),
(21, 'Kojobox', 'KOJOB', '', '081213311587', NULL, 1, '2025-07-30 06:34:53', '2025-07-30 06:34:53'),
(22, 'Ibu Aisyah (Agen Lemon)', 'IBUAI', 'https://goo.gl/maps/KwF8LywrTUGuzi328', '', NULL, 1, '2025-07-30 06:34:53', '2025-07-30 06:34:53'),
(24, 'Pusat Bumbu Surabaya Yutakachi', 'PUSAT', 'Surabaya', '-', NULL, 1, '2025-07-30 06:34:53', '2025-07-30 06:34:53'),
(25, 'Breadmart', 'BREAD', 'https://goo.gl/maps/3eJhLDbmEuqY8xGz9', '089530593335', NULL, 1, '2025-07-30 06:34:53', '2025-07-30 06:34:53'),
(26, 'Bosgil Marinasi', 'BOSGI', 'https://goo.gl/maps/LmwWneL1QKqTcngS6', '085692371999', NULL, 1, '2025-07-30 06:34:53', '2025-07-30 06:34:53'),
(29, 'Chazone Red cup', 'CHAZO', 'https://goo.gl/maps/Dx798GgsWDYeyYtq7', '', NULL, 1, '2025-07-30 06:34:53', '2025-07-30 06:34:53'),
(30, 'Delmapack', 'DELMA', 'https://goo.gl/maps/FsmatJViAGqDUPoM7', '082261231717', NULL, 1, '2025-07-30 06:34:53', '2025-07-30 06:34:53'),
(32, 'Gemilang Jaya Plastik (Rice Paper)', 'GEMIL', 'https://goo.gl/maps/6WRau8xR9zggpbkL6', '088901103509', NULL, 1, '2025-07-30 06:34:53', '2025-07-30 06:34:53'),
(33, 'Mawan Toserba KLIR', 'MAWAN', 'https://goo.gl/maps/CiPUzW82kphjhJ6F6', '0895330546032', NULL, 1, '2025-07-30 06:34:53', '2025-07-30 06:34:53'),
(35, 'ATK Matahari Kertas Struk', 'ATKMA', 'https://goo.gl/maps/kEQh65F7Xs2mXAHZ6', '081219090939', NULL, 1, '2025-07-30 06:34:53', '2025-07-30 06:34:53'),
(37, 'Supplierone French Fries Paper bag', 'SUPPL', 'https://goo.gl/maps/AECjAQz8S3W4ihZv6', '081281014419', NULL, 1, '2025-07-30 06:34:53', '2025-07-30 06:34:53'),
(40, 'Alka Azqua Drinking Water Refill', 'ALKAA', 'https://goo.gl/maps/TKE2YRJ5V3ScCRXLA', '', NULL, 1, '2025-07-30 06:34:53', '2025-07-30 06:34:53'),
(42, 'Phokpand ayam', 'PHOKP', NULL, '085258171151', NULL, 1, '2025-07-30 06:34:53', '2025-07-30 06:34:53');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint UNSIGNED NOT NULL,
  `unit_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abbreviation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `unit_name`, `abbreviation`, `description`, `created_at`, `updated_at`, `is_active`) VALUES
(1, 'Kilogram', 'kg', 'Unit berat dalam kilogram', '2025-07-22 15:10:31', '2025-07-22 15:10:31', 1),
(2, 'Gram', 'g', 'Unit berat dalam gram', '2025-07-22 15:10:31', '2025-07-22 15:10:31', 1),
(3, 'Liter', 'L', 'Unit volume dalam liter', '2025-07-22 15:10:31', '2025-07-22 15:10:31', 1),
(4, 'Mililiter', 'ml', 'Unit volume dalam mililiter', '2025-07-22 15:10:31', '2025-07-22 15:10:31', 1),
(5, 'Pieces', 'pcs', 'Unit satuan dalam pieces', '2025-07-22 15:10:31', '2025-07-22 15:10:31', 1),
(6, 'Pack', 'pack', 'Unit kemasan dalam pack', '2025-07-22 15:10:31', '2025-07-22 15:10:31', 1),
(9, 'Karton', 'ktn', 'Satuan kardus/karton', '2025-07-27 13:31:14', '2025-07-27 13:31:14', 1),
(10, 'Sak', 'sak', 'Satuan karung/sak', '2025-07-27 13:31:14', '2025-07-27 13:31:14', 1),
(13, 'Karung', 'KAR', 'Unit for Karung', '2025-07-30 06:31:26', '2025-07-30 06:31:26', 1),
(14, 'Ekor', 'EKO', 'Unit for Ekor', '2025-07-30 06:31:27', '2025-07-30 06:31:27', 1),
(15, 'toples', 'TOP', 'Unit for toples', '2025-07-30 06:31:27', '2025-07-30 06:31:27', 1),
(16, 'bungkus', 'BUN', 'Unit for bungkus', '2025-07-30 06:31:27', '2025-07-30 06:31:27', 1),
(17, 'buah', 'BUA', 'Unit for buah', '2025-07-30 06:31:27', '2025-07-30 06:31:27', 1),
(19, 'bonggol', 'BON', 'Unit for bonggol', '2025-07-30 06:31:27', '2025-08-03 14:33:06', 1),
(20, 'Pouch', 'POU', 'Unit for Pouch', '2025-07-30 06:31:27', '2025-07-30 06:31:27', 1),
(22, 'Teng', 'TEN', 'Unit for Teng', '2025-07-30 06:31:27', '2025-07-30 06:31:27', 1),
(25, 'slop', 'SLO', 'Unit for slop', '2025-07-30 06:31:27', '2025-07-30 06:31:27', 1),
(26, 'rim', 'RIM', 'Unit for rim', '2025-07-30 06:31:27', '2025-07-30 06:31:27', 1),
(27, 'Ikat', 'IKA', 'Unit for Ikat', '2025-07-30 06:31:27', '2025-07-30 06:31:27', 1),
(28, 'Kotak', 'KOT', 'Unit for kotak', '2025-07-30 06:31:27', '2025-08-02 12:28:30', 1),
(29, 'Galon', 'GAL', 'Unit for Galon', '2025-07-30 06:31:27', '2025-07-30 06:31:27', 1),
(30, 'botol', 'BOT', 'Unit for botol', '2025-07-30 06:31:27', '2025-07-30 06:31:27', 1),
(31, 'dus', 'DUS', 'Unit for dus', '2025-07-30 06:31:27', '2025-07-30 06:31:27', 1),
(32, 'Roll', 'ROL', 'Unit for Roll', '2025-07-30 06:31:27', '2025-07-30 06:31:27', 1),
(34, 'tabung', 'TAB', 'Unit for tabung', '2025-07-30 06:31:28', '2025-07-30 06:31:28', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Phone number in 62 format for WhatsApp',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `avatar`, `branch_id`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'm.hbb.works@gmail.com', '6282255647148', NULL, '$2y$10$sem0avDzXch4Rdv0X81n8OVd7dkXi5e/pmVY4NkjZjNSQFQS8Jq0W', NULL, NULL, 1, NULL, '2025-07-22 15:10:32', '2025-08-04 09:25:23'),
(5, 'Muhammad Habibi', 'habibi3525@gmail.com', '6282255647148', NULL, '$2y$10$mBC5UDm4rmW.pWyTR94KaOXsD9am7SSGyh0KxGckS4.geBYz0.blK', 'avatars/avatar_1754216698.jpg', NULL, 1, NULL, '2025-08-03 09:16:14', '2025-08-03 10:24:58'),
(10, 'Super Administrator', 'admin@laparchicken.com', NULL, NULL, '$2y$10$VtuA2RyAvQj/A98yGy4FeuotrFYmNd/gR.C/TFekWGIUviAbM.GbC', NULL, 1, 1, NULL, '2025-08-05 09:42:48', '2025-08-05 09:42:48');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-07-22 15:10:32', '2025-07-22 15:10:32'),
(8, 5, 10, NULL, NULL),
(12, 10, 1, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activity_logs_user_date` (`user_id`,`created_at`),
  ADD KEY `idx_activity_logs_model` (`model_type`,`model_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branches_code_unique` (`code`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_code_unique` (`code`);

--
-- Indexes for table `destruction_reports`
--
ALTER TABLE `destruction_reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `destruction_reports_report_number_unique` (`report_number`),
  ADD KEY `destruction_reports_reported_by_foreign` (`reported_by`),
  ADD KEY `destruction_reports_approved_by_foreign` (`approved_by`),
  ADD KEY `idx_destruction_branch_date` (`branch_id`,`destruction_date`);

--
-- Indexes for table `destruction_report_items`
--
ALTER TABLE `destruction_report_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `destruction_report_items_destruction_report_id_foreign` (`destruction_report_id`),
  ADD KEY `idx_destruction_items_item` (`item_type`,`item_id`);

--
-- Indexes for table `finished_branch_stocks`
--
ALTER TABLE `finished_branch_stocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_branch_finished_product` (`branch_id`,`finished_product_id`),
  ADD KEY `finished_branch_stocks_finished_product_id_foreign` (`finished_product_id`),
  ADD KEY `finished_branch_stocks_branch_id_finished_product_id_index` (`branch_id`,`finished_product_id`),
  ADD KEY `finished_branch_stocks_quantity_index` (`quantity`);

--
-- Indexes for table `finished_products`
--
ALTER TABLE `finished_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `finished_products_code_unique` (`code`),
  ADD KEY `finished_products_category_id_foreign` (`category_id`),
  ADD KEY `finished_products_unit_id_foreign` (`unit_id`);

--
-- Indexes for table `material_supplier`
--
ALTER TABLE `material_supplier`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `material_supplier_supplier_id_raw_material_id_unique` (`supplier_id`,`raw_material_id`),
  ADD KEY `material_supplier_raw_material_id_foreign` (`raw_material_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_code_unique` (`code`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `production_requests`
--
ALTER TABLE `production_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `production_requests_request_code_unique` (`request_code`),
  ADD KEY `production_requests_approved_by_foreign` (`approved_by`),
  ADD KEY `production_requests_production_started_by_foreign` (`production_started_by`),
  ADD KEY `production_requests_production_completed_by_foreign` (`production_completed_by`),
  ADD KEY `production_requests_status_created_at_index` (`status`,`created_at`),
  ADD KEY `production_requests_requested_by_index` (`requested_by`),
  ADD KEY `production_requests_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `production_request_items`
--
ALTER TABLE `production_request_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `prod_req_items_unique` (`production_request_id`,`raw_material_id`),
  ADD KEY `production_request_items_production_request_id_index` (`production_request_id`),
  ADD KEY `production_request_items_raw_material_id_index` (`raw_material_id`);

--
-- Indexes for table `production_request_outputs`
--
ALTER TABLE `production_request_outputs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pr_output_unique` (`production_request_id`,`semi_finished_product_id`),
  ADD KEY `production_request_outputs_semi_finished_product_id_index` (`semi_finished_product_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_orders_order_number_unique` (`order_number`),
  ADD UNIQUE KEY `purchase_orders_order_code_unique` (`order_code`),
  ADD KEY `purchase_orders_supplier_id_index` (`supplier_id`),
  ADD KEY `purchase_orders_created_by_index` (`created_by`),
  ADD KEY `purchase_orders_status_index` (`status`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_items_purchase_order_id_foreign` (`purchase_order_id`),
  ADD KEY `purchase_order_items_raw_material_id_foreign` (`raw_material_id`),
  ADD KEY `purchase_order_items_unit_id_foreign` (`unit_id`);

--
-- Indexes for table `purchase_receipts`
--
ALTER TABLE `purchase_receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_receipts_receipt_number_unique` (`receipt_number`),
  ADD KEY `purchase_receipts_purchase_order_id_foreign` (`purchase_order_id`),
  ADD KEY `purchase_receipts_received_by_foreign` (`received_by`);

--
-- Indexes for table `purchase_receipt_additional_costs`
--
ALTER TABLE `purchase_receipt_additional_costs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_receipt_additional_costs_purchase_receipt_id_index` (`purchase_receipt_id`);

--
-- Indexes for table `purchase_receipt_items`
--
ALTER TABLE `purchase_receipt_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_receipt_items_purchase_receipt_id_foreign` (`purchase_receipt_id`),
  ADD KEY `purchase_receipt_items_purchase_order_item_id_foreign` (`purchase_order_item_id`),
  ADD KEY `purchase_receipt_items_raw_material_id_foreign` (`raw_material_id`);

--
-- Indexes for table `raw_materials`
--
ALTER TABLE `raw_materials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_raw_materials_category` (`category_id`),
  ADD KEY `fk_raw_materials_unit` (`unit_id`),
  ADD KEY `raw_materials_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_permissions_role_id_permission_id_unique` (`role_id`,`permission_id`),
  ADD KEY `role_permissions_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_sale_number_unique` (`sale_number`),
  ADD UNIQUE KEY `sales_sale_code_unique` (`sale_code`),
  ADD KEY `sales_branch_id_foreign` (`branch_id`),
  ADD KEY `sales_user_id_foreign` (`user_id`);

--
-- Indexes for table `sales_packages`
--
ALTER TABLE `sales_packages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_packages_code_unique` (`code`),
  ADD KEY `sales_packages_created_by_foreign` (`created_by`),
  ADD KEY `sales_packages_is_active_name_index` (`is_active`,`name`),
  ADD KEY `sales_packages_category_id_foreign` (`category_id`);

--
-- Indexes for table `sales_package_items`
--
ALTER TABLE `sales_package_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_package_items_sales_package_id_finished_product_id_unique` (`sales_package_id`,`finished_product_id`),
  ADD KEY `sales_package_items_finished_product_id_foreign` (`finished_product_id`),
  ADD KEY `sales_package_items_sales_package_id_index` (`sales_package_id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_items_sale_id_foreign` (`sale_id`);

--
-- Indexes for table `semi_finished_branch_stocks`
--
ALTER TABLE `semi_finished_branch_stocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sfbs_branch_prod_unique` (`branch_id`,`semi_finished_product_id`),
  ADD KEY `semi_finished_branch_stocks_semi_finished_product_id_foreign` (`semi_finished_product_id`);

--
-- Indexes for table `semi_finished_distributions`
--
ALTER TABLE `semi_finished_distributions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `semi_finished_distributions_distribution_code_unique` (`distribution_code`),
  ADD KEY `semi_finished_distributions_semi_finished_product_id_foreign` (`semi_finished_product_id`),
  ADD KEY `semi_finished_distributions_handled_by_foreign` (`handled_by`),
  ADD KEY `semi_finished_distributions_status_created_at_index` (`status`,`created_at`),
  ADD KEY `semi_finished_distributions_target_branch_id_index` (`target_branch_id`),
  ADD KEY `semi_finished_distributions_sent_by_index` (`sent_by`);

--
-- Indexes for table `semi_finished_products`
--
ALTER TABLE `semi_finished_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `semi_finished_products_code_unique` (`code`),
  ADD KEY `semi_finished_products_unit_id_foreign` (`unit_id`),
  ADD KEY `semi_finished_products_category_id_foreign` (`category_id`);

--
-- Indexes for table `semi_finished_usage_requests`
--
ALTER TABLE `semi_finished_usage_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `material_usage_requests_request_code_unique` (`request_number`),
  ADD KEY `material_usage_requests_branch_id_foreign` (`requesting_branch_id`),
  ADD KEY `material_usage_requests_requested_by_foreign` (`user_id`),
  ADD KEY `material_usage_requests_approved_by_foreign` (`approved_by`),
  ADD KEY `material_usage_requests_process_started_by_foreign` (`process_started_by`),
  ADD KEY `material_usage_requests_process_completed_by_foreign` (`process_completed_by`);

--
-- Indexes for table `semi_finished_usage_request_items`
--
ALTER TABLE `semi_finished_usage_request_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_usage_request_items_semi_finished_product_id_foreign` (`semi_finished_product_id`),
  ADD KEY `semi_finished_usage_request_items_semi_finished_request_id_index` (`semi_finished_request_id`);

--
-- Indexes for table `semi_finished_usage_request_outputs`
--
ALTER TABLE `semi_finished_usage_request_outputs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_movements_created_by_foreign` (`created_by`),
  ADD KEY `stock_movements_branch_id_index` (`branch_id`),
  ADD KEY `stock_movements_semi_finished_product_id_index` (`semi_finished_product_id`),
  ADD KEY `stock_movements_material_id_index` (`material_id`),
  ADD KEY `stock_movements_finished_product_id_index` (`finished_product_id`),
  ADD KEY `stock_movements_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  ADD KEY `stock_movements_type_index` (`type`),
  ADD KEY `stock_movements_movement_category_index` (`movement_category`),
  ADD KEY `stock_movements_created_at_index` (`created_at`);

--
-- Indexes for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_transfers_from_branch_id_foreign` (`from_branch_id`),
  ADD KEY `stock_transfers_sent_by_foreign` (`sent_by`),
  ADD KEY `stock_transfers_handled_by_foreign` (`handled_by`),
  ADD KEY `stock_transfers_item_type_item_id_index` (`item_type`,`item_id`),
  ADD KEY `stock_transfers_to_branch_id_status_index` (`to_branch_id`,`status`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `suppliers_code_unique` (`code`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `units_symbol_unique` (`abbreviation`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_roles_user_id_role_id_unique` (`user_id`,`role_id`),
  ADD KEY `user_roles_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `destruction_reports`
--
ALTER TABLE `destruction_reports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `destruction_report_items`
--
ALTER TABLE `destruction_report_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `finished_branch_stocks`
--
ALTER TABLE `finished_branch_stocks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT for table `finished_products`
--
ALTER TABLE `finished_products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `material_supplier`
--
ALTER TABLE `material_supplier`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `production_requests`
--
ALTER TABLE `production_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `production_request_items`
--
ALTER TABLE `production_request_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `production_request_outputs`
--
ALTER TABLE `production_request_outputs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `purchase_receipts`
--
ALTER TABLE `purchase_receipts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `purchase_receipt_additional_costs`
--
ALTER TABLE `purchase_receipt_additional_costs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `purchase_receipt_items`
--
ALTER TABLE `purchase_receipt_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `raw_materials`
--
ALTER TABLE `raw_materials`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sales_packages`
--
ALTER TABLE `sales_packages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sales_package_items`
--
ALTER TABLE `sales_package_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `semi_finished_branch_stocks`
--
ALTER TABLE `semi_finished_branch_stocks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `semi_finished_distributions`
--
ALTER TABLE `semi_finished_distributions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `semi_finished_products`
--
ALTER TABLE `semi_finished_products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `semi_finished_usage_requests`
--
ALTER TABLE `semi_finished_usage_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `semi_finished_usage_request_items`
--
ALTER TABLE `semi_finished_usage_request_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `semi_finished_usage_request_outputs`
--
ALTER TABLE `semi_finished_usage_request_outputs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `destruction_reports`
--
ALTER TABLE `destruction_reports`
  ADD CONSTRAINT `destruction_reports_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `destruction_reports_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `destruction_reports_reported_by_foreign` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `destruction_report_items`
--
ALTER TABLE `destruction_report_items`
  ADD CONSTRAINT `destruction_report_items_destruction_report_id_foreign` FOREIGN KEY (`destruction_report_id`) REFERENCES `destruction_reports` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `finished_branch_stocks`
--
ALTER TABLE `finished_branch_stocks`
  ADD CONSTRAINT `finished_branch_stocks_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `finished_branch_stocks_finished_product_id_foreign` FOREIGN KEY (`finished_product_id`) REFERENCES `finished_products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `finished_products`
--
ALTER TABLE `finished_products`
  ADD CONSTRAINT `finished_products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `finished_products_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `material_supplier`
--
ALTER TABLE `material_supplier`
  ADD CONSTRAINT `material_supplier_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `material_supplier_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `production_requests`
--
ALTER TABLE `production_requests`
  ADD CONSTRAINT `production_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `production_requests_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `production_requests_production_completed_by_foreign` FOREIGN KEY (`production_completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `production_requests_production_started_by_foreign` FOREIGN KEY (`production_started_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `production_requests_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `production_request_items`
--
ALTER TABLE `production_request_items`
  ADD CONSTRAINT `production_request_items_production_request_id_foreign` FOREIGN KEY (`production_request_id`) REFERENCES `production_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `production_request_items_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`);

--
-- Constraints for table `production_request_outputs`
--
ALTER TABLE `production_request_outputs`
  ADD CONSTRAINT `production_request_outputs_production_request_id_foreign` FOREIGN KEY (`production_request_id`) REFERENCES `production_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `production_request_outputs_semi_finished_product_id_foreign` FOREIGN KEY (`semi_finished_product_id`) REFERENCES `semi_finished_products` (`id`);

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_order_items_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_order_items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_receipts`
--
ALTER TABLE `purchase_receipts`
  ADD CONSTRAINT `purchase_receipts_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_receipts_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_receipt_additional_costs`
--
ALTER TABLE `purchase_receipt_additional_costs`
  ADD CONSTRAINT `purchase_receipt_additional_costs_purchase_receipt_id_foreign` FOREIGN KEY (`purchase_receipt_id`) REFERENCES `purchase_receipts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_receipt_items`
--
ALTER TABLE `purchase_receipt_items`
  ADD CONSTRAINT `purchase_receipt_items_purchase_order_item_id_foreign` FOREIGN KEY (`purchase_order_item_id`) REFERENCES `purchase_order_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_receipt_items_purchase_receipt_id_foreign` FOREIGN KEY (`purchase_receipt_id`) REFERENCES `purchase_receipts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_receipt_items_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `raw_materials`
--
ALTER TABLE `raw_materials`
  ADD CONSTRAINT `fk_raw_materials_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_raw_materials_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`),
  ADD CONSTRAINT `raw_materials_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `sales_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `sales_packages`
--
ALTER TABLE `sales_packages`
  ADD CONSTRAINT `sales_packages_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_packages_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sales_package_items`
--
ALTER TABLE `sales_package_items`
  ADD CONSTRAINT `sales_package_items_finished_product_id_foreign` FOREIGN KEY (`finished_product_id`) REFERENCES `finished_products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_package_items_sales_package_id_foreign` FOREIGN KEY (`sales_package_id`) REFERENCES `sales_packages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `semi_finished_branch_stocks`
--
ALTER TABLE `semi_finished_branch_stocks`
  ADD CONSTRAINT `semi_finished_branch_stocks_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `semi_finished_branch_stocks_semi_finished_product_id_foreign` FOREIGN KEY (`semi_finished_product_id`) REFERENCES `semi_finished_products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `semi_finished_distributions`
--
ALTER TABLE `semi_finished_distributions`
  ADD CONSTRAINT `semi_finished_distributions_handled_by_foreign` FOREIGN KEY (`handled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `semi_finished_distributions_semi_finished_product_id_foreign` FOREIGN KEY (`semi_finished_product_id`) REFERENCES `semi_finished_products` (`id`),
  ADD CONSTRAINT `semi_finished_distributions_sent_by_foreign` FOREIGN KEY (`sent_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `semi_finished_distributions_target_branch_id_foreign` FOREIGN KEY (`target_branch_id`) REFERENCES `branches` (`id`);

--
-- Constraints for table `semi_finished_products`
--
ALTER TABLE `semi_finished_products`
  ADD CONSTRAINT `semi_finished_products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `semi_finished_products_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `semi_finished_usage_requests`
--
ALTER TABLE `semi_finished_usage_requests`
  ADD CONSTRAINT `material_usage_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `material_usage_requests_branch_id_foreign` FOREIGN KEY (`requesting_branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `material_usage_requests_process_completed_by_foreign` FOREIGN KEY (`process_completed_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `material_usage_requests_process_started_by_foreign` FOREIGN KEY (`process_started_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `material_usage_requests_requested_by_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `semi_finished_usage_request_items`
--
ALTER TABLE `semi_finished_usage_request_items`
  ADD CONSTRAINT `material_usage_request_items_semi_finished_product_id_foreign` FOREIGN KEY (`semi_finished_product_id`) REFERENCES `semi_finished_products` (`id`);

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_movements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_movements_semi_finished_product_id_foreign` FOREIGN KEY (`semi_finished_product_id`) REFERENCES `semi_finished_products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD CONSTRAINT `stock_transfers_from_branch_id_foreign` FOREIGN KEY (`from_branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `stock_transfers_handled_by_foreign` FOREIGN KEY (`handled_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `stock_transfers_sent_by_foreign` FOREIGN KEY (`sent_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `stock_transfers_to_branch_id_foreign` FOREIGN KEY (`to_branch_id`) REFERENCES `branches` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
