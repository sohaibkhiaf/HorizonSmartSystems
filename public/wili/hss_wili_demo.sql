-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2026 at 02:46 PM
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
-- Database: `hss_wili_demo`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_events`
--

CREATE TABLE `audit_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(120) NOT NULL,
  `entity_type` varchar(80) NOT NULL DEFAULT '',
  `entity_id` varchar(80) NOT NULL DEFAULT '',
  `detail` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_events`
--

INSERT INTO `audit_events` (`id`, `workspace_id`, `user_id`, `action`, `entity_type`, `entity_id`, `detail`, `created_at`) VALUES
(1, 13, 1, 'sale.create', 'sale', '1', '', '2026-06-27 13:09:19'),
(2, 14, 2, 'sale.create', 'sale', '2', '', '2026-06-27 13:16:07'),
(3, 14, 2, 'sale.create', 'sale', '3', '', '2026-06-27 13:17:07'),
(4, 14, 2, 'purchase.create', 'purchase', '1', '', '2026-06-27 13:17:07'),
(5, 14, 2, 'sale.create', 'sale', '4', '', '2026-06-27 13:17:31');

-- --------------------------------------------------------

--
-- Table structure for table `counters`
--

CREATE TABLE `counters` (
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `counter_key` varchar(80) NOT NULL,
  `counter_value` bigint(20) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `counters`
--

INSERT INTO `counters` (`workspace_id`, `counter_key`, `counter_value`) VALUES
(13, 'BL', 1),
(14, 'BL', 1),
(14, 'BR', 1),
(14, 'VCC', 2);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `reference` varchar(120) NOT NULL DEFAULT '',
  `phone` varchar(120) NOT NULL DEFAULT '',
  `address` text DEFAULT NULL,
  `nif` varchar(120) NOT NULL DEFAULT '',
  `nis` varchar(120) NOT NULL DEFAULT '',
  `rc` varchar(120) NOT NULL DEFAULT '',
  `ai` varchar(120) NOT NULL DEFAULT '',
  `activity` varchar(190) NOT NULL DEFAULT '',
  `commerce_name` varchar(190) NOT NULL DEFAULT '',
  `balance_cents` bigint(20) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `finance_entries`
--

CREATE TABLE `finance_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `entry_type` varchar(30) NOT NULL,
  `source` varchar(120) NOT NULL,
  `ref` varchar(80) NOT NULL DEFAULT '',
  `ref_id` varchar(80) NOT NULL DEFAULT '',
  `party_key` varchar(80) NOT NULL DEFAULT '',
  `party_name` varchar(255) NOT NULL DEFAULT '',
  `amount_cents` bigint(20) NOT NULL DEFAULT 0,
  `status` varchar(60) NOT NULL DEFAULT 'Regle',
  `pdf_kind` varchar(80) NOT NULL DEFAULT '',
  `pdf_id` varchar(80) NOT NULL DEFAULT '',
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) NOT NULL DEFAULT '',
  `qty` decimal(14,2) NOT NULL DEFAULT 0.00,
  `note` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `finance_entries`
--

INSERT INTO `finance_entries` (`id`, `workspace_id`, `date`, `entry_type`, `source`, `ref`, `ref_id`, `party_key`, `party_name`, `amount_cents`, `status`, `pdf_kind`, `pdf_id`, `product_id`, `product_name`, `qty`, `note`, `created_at`) VALUES
(1, 13, '2026-06-27 13:09:19', 'income', 'Encaissement BL', 'BL/1/2026', 'BL/1/2026', '', '', 15000, 'Regle', 'sale_invoice', '1', NULL, '', 0.00, '', '2026-06-27 13:09:19'),
(2, 14, '2026-06-27 13:16:07', 'income', 'Encaissement BL', 'BL/1/2026', 'BL/1/2026', '', '', 15500, 'Regle', 'sale_invoice', '2', NULL, '', 0.00, '', '2026-06-27 13:16:07'),
(3, 14, '2026-06-27 13:17:07', 'income', 'Encaissement VCC', 'VCC/1/2026', 'VCC/1/2026', '', '', 15500, 'Regle', 'sale_invoice', '3', NULL, '', 0.00, '', '2026-06-27 13:17:07'),
(4, 14, '2026-06-27 13:17:07', 'outcome', 'Paiement BR', 'BR/1/2026', 'BR/1/2026', '', '', 10000, 'Regle', 'purchase_invoice', '1', NULL, '', 0.00, '', '2026-06-27 13:17:07'),
(5, 14, '2026-06-27 13:17:31', 'income', 'Encaissement VCC', 'VCC/2/2026', 'VCC/2/2026', '', '', 15500, 'Regle', 'sale_invoice', '4', NULL, '', 0.00, '', '2026-06-27 13:17:31');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `barcode` varchar(120) NOT NULL DEFAULT '',
  `reference` varchar(120) NOT NULL DEFAULT '',
  `sku` varchar(120) NOT NULL DEFAULT '',
  `product_type` varchar(30) NOT NULL DEFAULT 'product',
  `stock_qty` decimal(14,2) NOT NULL DEFAULT 0.00,
  `cost_cents` bigint(20) NOT NULL DEFAULT 0,
  `avg_cost_cents` bigint(20) NOT NULL DEFAULT 0,
  `last_cost_cents` bigint(20) NOT NULL DEFAULT 0,
  `wholesale_price_cents` bigint(20) NOT NULL DEFAULT 0,
  `retail_price_cents` bigint(20) NOT NULL DEFAULT 0,
  `assisted_price_cents` bigint(20) NOT NULL DEFAULT 0,
  `discount_price_cents` bigint(20) NOT NULL DEFAULT 0,
  `expiry_date` date DEFAULT NULL,
  `image_path` varchar(255) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `workspace_id`, `name`, `barcode`, `reference`, `sku`, `product_type`, `stock_qty`, `cost_cents`, `avg_cost_cents`, `last_cost_cents`, `wholesale_price_cents`, `retail_price_cents`, `assisted_price_cents`, `discount_price_cents`, `expiry_date`, `image_path`, `created_at`, `updated_at`) VALUES
(1, 1, 'LED 05W SPOT COB BLANC', 'QG33', 'LED3', 'LED3', 'product', 45.00, 28000, 28000, 28000, 40500, 45500, 45500, 45500, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30'),
(2, 1, 'CAR SPOT 1P CARRE NOIR BLANC', '613000000001', 'MAX1', 'MAX1', 'product', 42.00, 24000, 24000, 24000, 40000, 45000, 45000, 45000, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30'),
(3, 1, '2GE INTERPT SA-APPARENT-NIVO', '402-010000-200', '402-010000-200', '402-010000-200', 'product', 75.00, 10000, 10000, 10000, 10500, 15500, 15500, 15500, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30'),
(4, 2, 'Pizza Margherita', 'PIZ001', 'PIZ001', 'PIZ001', 'product', 100.00, 35000, 35000, 35000, 65000, 70000, 70000, 70000, NULL, '', '2026-06-24 17:37:09', '2026-06-24 17:37:09'),
(5, 2, 'Pizza Thon', 'PIZ002', 'PIZ002', 'PIZ002', 'product', 80.00, 45000, 45000, 45000, 85000, 90000, 90000, 90000, NULL, '', '2026-06-24 17:37:09', '2026-06-24 17:37:09'),
(6, 2, 'Boisson 1L', 'B001', 'B001', 'B001', 'product', 60.00, 8000, 8000, 8000, 10000, 15000, 15000, 15000, NULL, '', '2026-06-24 17:37:09', '2026-06-24 17:37:09'),
(7, 3, 'Lait 1L', '613000000001', 'AL001', 'AL001', 'product', 120.00, 9000, 9000, 9000, 9000, 12000, 12000, 12000, NULL, '', '2026-06-24 17:37:09', '2026-06-24 17:37:09'),
(8, 3, 'Sucre 1KG', '613000000002', 'AL002', 'AL002', 'product', 90.00, 11000, 11000, 11000, 11000, 15000, 15000, 15000, NULL, '', '2026-06-24 17:37:09', '2026-06-24 17:37:09'),
(9, 3, 'Cafe 250G', '613000000003', 'AL003', 'AL003', 'product', 40.00, 26000, 26000, 26000, 31000, 36000, 36000, 36000, NULL, '', '2026-06-24 17:37:09', '2026-06-24 17:37:09'),
(13, 6, 'Boisson 1L', 'B001', 'B001', 'B001', 'product', 60.00, 8000, 8000, 8000, 10000, 15000, 15000, 15000, NULL, '', '2026-06-24 17:37:09', '2026-06-24 17:37:09'),
(14, 6, 'Pizza Margherita', 'PIZ001', 'PIZ001', 'PIZ001', 'product', 100.00, 35000, 35000, 35000, 65000, 70000, 70000, 70000, NULL, '', '2026-06-24 17:37:09', '2026-06-24 17:37:09'),
(15, 6, 'Pizza Thon', 'PIZ002', 'PIZ002', 'PIZ002', 'product', 80.00, 45000, 45000, 45000, 85000, 90000, 90000, 90000, NULL, '', '2026-06-24 17:37:09', '2026-06-24 17:37:09'),
(19, 7, 'Cafe 250G', '613000000003', 'AL003', 'AL003', 'product', 40.00, 26000, 26000, 26000, 31000, 36000, 36000, 36000, NULL, '', '2026-06-24 17:37:09', '2026-06-24 17:37:09'),
(20, 7, 'Lait 1L', '613000000001', 'AL001', 'AL001', 'product', 120.00, 9000, 9000, 9000, 9000, 12000, 12000, 12000, NULL, '', '2026-06-24 17:37:09', '2026-06-24 17:37:09'),
(21, 7, 'Sucre 1KG', '613000000002', 'AL002', 'AL002', 'product', 90.00, 11000, 11000, 11000, 11000, 15000, 15000, 15000, NULL, '', '2026-06-24 17:37:09', '2026-06-24 17:37:09'),
(22, 5, '2GE INTERPT SA-APPARENT-NIVO', '402-010000-200', '402-010000-200', '402-010000-200', 'product', 74.00, 10000, 10000, 10000, 10500, 15500, 15500, 15500, NULL, '', '2026-06-24 17:36:30', '2026-06-24 20:58:56'),
(23, 5, 'CAR SPOT 1P CARRE NOIR BLANC', '613000000001', 'MAX1', 'MAX1', 'product', 42.00, 24000, 24000, 24000, 40000, 45000, 45000, 45000, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30'),
(24, 5, 'LED 05W SPOT COB BLANC', 'QG33', 'LED3', 'LED3', 'product', 45.00, 28000, 28000, 28000, 40500, 45500, 45500, 45500, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30'),
(25, 8, '2GE INTERPT SA-APPARENT-NIVO', '402-010000-200', '402-010000-200', '402-010000-200', 'product', 75.00, 10000, 10000, 10000, 10500, 15500, 15500, 15500, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30'),
(26, 8, 'CAR SPOT 1P CARRE NOIR BLANC', '613000000001', 'MAX1', 'MAX1', 'product', 42.00, 24000, 24000, 24000, 40000, 45000, 45000, 45000, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30'),
(27, 8, 'LED 05W SPOT COB BLANC', 'QG33', 'LED3', 'LED3', 'product', 45.00, 28000, 28000, 28000, 40500, 45500, 45500, 45500, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30'),
(28, 9, '2GE INTERPT SA-APPARENT-NIVO', '402-010000-200', '402-010000-200', '402-010000-200', 'product', 75.00, 10000, 10000, 10000, 10500, 15500, 15500, 15500, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30'),
(29, 9, 'CAR SPOT 1P CARRE NOIR BLANC', '613000000001', 'MAX1', 'MAX1', 'product', 42.00, 24000, 24000, 24000, 40000, 45000, 45000, 45000, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30'),
(30, 9, 'LED 05W SPOT COB BLANC', 'QG33', 'LED3', 'LED3', 'product', 45.00, 28000, 28000, 28000, 40500, 45500, 45500, 45500, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30'),
(31, 10, '2GE INTERPT SA-APPARENT-NIVO', '402-010000-200', '402-010000-200', '402-010000-200', 'product', 75.00, 10000, 10000, 10000, 10500, 15500, 15500, 15500, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30'),
(32, 10, 'CAR SPOT 1P CARRE NOIR BLANC', '613000000001', 'MAX1', 'MAX1', 'product', 42.00, 24000, 24000, 24000, 40000, 45000, 45000, 45000, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30'),
(33, 10, 'LED 05W SPOT COB BLANC', 'QG33', 'LED3', 'LED3', 'product', 45.00, 28000, 28000, 28000, 40500, 45500, 45500, 45500, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30'),
(34, 11, '2GE INTERPT SA-APPARENT-NIVO', '402-010000-200', '402-010000-200', '402-010000-200', 'product', 75.00, 10000, 10000, 10000, 10500, 15500, 15500, 15500, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30'),
(35, 11, 'CAR SPOT 1P CARRE NOIR BLANC', '613000000001', 'MAX1', 'MAX1', 'product', 42.00, 24000, 24000, 24000, 40000, 45000, 45000, 45000, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30'),
(36, 11, 'LED 05W SPOT COB BLANC', 'QG33', 'LED3', 'LED3', 'product', 45.00, 28000, 28000, 28000, 40500, 45500, 45500, 45500, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30'),
(37, 12, '2GE INTERPT SA-APPARENT-NIVO', '402-010000-200', '402-010000-200', '402-010000-200', 'product', 75.00, 10000, 10000, 10000, 10500, 15500, 15500, 15500, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30'),
(38, 12, 'CAR SPOT 1P CARRE NOIR BLANC', '613000000001', 'MAX1', 'MAX1', 'product', 42.00, 24000, 24000, 24000, 40000, 45000, 45000, 45000, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30'),
(39, 12, 'LED 05W SPOT COB BLANC', 'QG33', 'LED3', 'LED3', 'product', 45.00, 28000, 28000, 28000, 40500, 45500, 45500, 45500, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30'),
(40, 13, 'Boisson 1L', 'B001', 'B001', 'B001', 'product', 59.00, 8000, 8000, 8000, 10000, 15000, 15000, 15000, NULL, '', '2026-06-24 17:37:09', '2026-06-27 13:09:19'),
(41, 13, 'Pizza Margherita', 'PIZ001', 'PIZ001', 'PIZ001', 'product', 100.00, 35000, 35000, 35000, 65000, 70000, 70000, 70000, NULL, '', '2026-06-24 17:37:09', '2026-06-24 17:37:09'),
(42, 13, 'Pizza Thon', 'PIZ002', 'PIZ002', 'PIZ002', 'product', 80.00, 45000, 45000, 45000, 85000, 90000, 90000, 90000, NULL, '', '2026-06-24 17:37:09', '2026-06-24 17:37:09'),
(43, 14, '2GE INTERPT SA-APPARENT-NIVO', '402-010000-200', '402-010000-200', '402-010000-200', 'product', 73.00, 10000, 10000, 10000, 10500, 15500, 15500, 15500, NULL, '', '2026-06-24 17:36:30', '2026-06-27 13:17:31'),
(44, 14, 'CAR SPOT 1P CARRE NOIR BLANC', '613000000001', 'MAX1', 'MAX1', 'product', 42.00, 24000, 24000, 24000, 40000, 45000, 45000, 45000, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30'),
(45, 14, 'LED 05W SPOT COB BLANC', 'QG33', 'LED3', 'LED3', 'product', 45.00, 28000, 28000, 28000, 40500, 45500, 45500, 45500, NULL, '', '2026-06-24 17:36:30', '2026-06-24 17:36:30');

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `ref` varchar(80) NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `total_cents` bigint(20) NOT NULL DEFAULT 0,
  `paid_cents` bigint(20) NOT NULL DEFAULT 0,
  `due_cents` bigint(20) NOT NULL DEFAULT 0,
  `status` varchar(40) NOT NULL DEFAULT 'Regle',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `workspace_id`, `ref`, `customer_id`, `customer_name`, `date`, `total_cents`, `paid_cents`, `due_cents`, `status`, `created_by`, `created_at`) VALUES
(1, 14, 'BR/1/2026', NULL, '', '2026-06-27 13:17:07', 10000, 10000, 0, 'Regle', 2, '2026-06-27 13:17:07');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_lines`
--

CREATE TABLE `purchase_lines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `qty` decimal(14,2) NOT NULL DEFAULT 0.00,
  `unit_cost_cents` bigint(20) NOT NULL DEFAULT 0,
  `total_cents` bigint(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_lines`
--

INSERT INTO `purchase_lines` (`id`, `workspace_id`, `purchase_id`, `product_id`, `product_name`, `qty`, `unit_cost_cents`, `total_cents`) VALUES
(1, 14, 1, 43, '2GE INTERPT SA-APPARENT-NIVO', 1.00, 10000, 10000);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `ref` varchar(80) NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `total_cents` bigint(20) NOT NULL DEFAULT 0,
  `paid_cents` bigint(20) NOT NULL DEFAULT 0,
  `due_cents` bigint(20) NOT NULL DEFAULT 0,
  `status` varchar(40) NOT NULL DEFAULT 'Regle',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `workspace_id`, `ref`, `customer_id`, `customer_name`, `date`, `total_cents`, `paid_cents`, `due_cents`, `status`, `created_by`, `created_at`) VALUES
(1, 13, 'BL/1/2026', NULL, '', '2026-06-27 13:09:19', 15000, 15000, 0, 'Regle', 1, '2026-06-27 13:09:19'),
(2, 14, 'BL/1/2026', NULL, '', '2026-06-27 13:16:07', 15500, 15500, 0, 'Regle', 2, '2026-06-27 13:16:07'),
(3, 14, 'VCC/1/2026', NULL, '', '2026-06-27 13:17:07', 15500, 15500, 0, 'Regle', 2, '2026-06-27 13:17:07'),
(4, 14, 'VCC/2/2026', NULL, '', '2026-06-27 13:17:31', 15500, 15500, 0, 'Regle', 2, '2026-06-27 13:17:31');

-- --------------------------------------------------------

--
-- Table structure for table `sale_lines`
--

CREATE TABLE `sale_lines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `qty` decimal(14,2) NOT NULL DEFAULT 0.00,
  `unit_price_cents` bigint(20) NOT NULL DEFAULT 0,
  `discount_cents` bigint(20) NOT NULL DEFAULT 0,
  `total_cents` bigint(20) NOT NULL DEFAULT 0,
  `cost_cents` bigint(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_lines`
--

INSERT INTO `sale_lines` (`id`, `workspace_id`, `sale_id`, `product_id`, `product_name`, `qty`, `unit_price_cents`, `discount_cents`, `total_cents`, `cost_cents`) VALUES
(1, 13, 1, 40, 'Boisson 1L', 1.00, 15000, 0, 15000, 8000),
(2, 14, 2, 43, '2GE INTERPT SA-APPARENT-NIVO', 1.00, 15500, 0, 15500, 10000),
(3, 14, 3, 43, '2GE INTERPT SA-APPARENT-NIVO', 1.00, 15500, 0, 15500, 10000),
(4, 14, 4, 43, '2GE INTERPT SA-APPARENT-NIVO', 1.00, 15500, 0, 15500, 10000);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `setting_key` varchar(120) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`workspace_id`, `setting_key`, `setting_value`) VALUES
(1, 'allow_negative_stock', '1'),
(1, 'business_name', 'WILI Electric'),
(1, 'commerce_name', 'WILI Electric'),
(1, 'comptoir_layout', 'classic_no_images'),
(1, 'language', 'fr'),
(1, 'purchase_paid_auto_full', '1'),
(2, 'allow_negative_stock', '1'),
(2, 'business_name', 'WILI Pizzeria'),
(2, 'commerce_name', 'WILI Pizzeria'),
(2, 'comptoir_layout', 'classic_no_images'),
(2, 'language', 'fr'),
(2, 'purchase_paid_auto_full', '1'),
(3, 'allow_negative_stock', '1'),
(3, 'business_name', 'WILI Alimentation'),
(3, 'commerce_name', 'WILI Alimentation'),
(3, 'comptoir_layout', 'classic_no_images'),
(3, 'language', 'fr'),
(3, 'purchase_paid_auto_full', '1'),
(11, 'allow_negative_stock', '1'),
(11, 'business_name', 'WILI Electric'),
(11, 'commerce_name', 'WILI Electric'),
(11, 'comptoir_layout', 'classic_no_images'),
(11, 'language', 'fr'),
(11, 'purchase_paid_auto_full', '1'),
(12, 'allow_negative_stock', '1'),
(12, 'business_name', 'WILI Electric'),
(12, 'commerce_name', 'WILI Electric'),
(12, 'comptoir_layout', 'classic_no_images'),
(12, 'language', 'fr'),
(12, 'purchase_paid_auto_full', '1'),
(13, 'allow_negative_stock', '1'),
(13, 'business_name', 'WILI Pizzeria'),
(13, 'commerce_name', 'WILI Pizzeria'),
(13, 'comptoir_layout', 'classic_no_images'),
(13, 'language', 'fr'),
(13, 'purchase_paid_auto_full', '1'),
(14, 'allow_negative_stock', '1'),
(14, 'business_name', 'WILI Electric'),
(14, 'commerce_name', 'WILI Electric'),
(14, 'comptoir_layout', 'classic_no_images'),
(14, 'language', 'fr'),
(14, 'purchase_paid_auto_full', '1');

-- --------------------------------------------------------

--
-- Table structure for table `stock_losses`
--

CREATE TABLE `stock_losses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `qty` decimal(14,2) NOT NULL DEFAULT 0.00,
  `reason` varchar(120) NOT NULL DEFAULT '',
  `note` text DEFAULT NULL,
  `cost_cents` bigint(20) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `reference` varchar(120) NOT NULL DEFAULT '',
  `phone` varchar(120) NOT NULL DEFAULT '',
  `address` text DEFAULT NULL,
  `nif` varchar(120) NOT NULL DEFAULT '',
  `nis` varchar(120) NOT NULL DEFAULT '',
  `rc` varchar(120) NOT NULL DEFAULT '',
  `ai` varchar(120) NOT NULL DEFAULT '',
  `activity` varchar(190) NOT NULL DEFAULT '',
  `commerce_name` varchar(190) NOT NULL DEFAULT '',
  `balance_cents` bigint(20) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `name`, `created_at`) VALUES
(1, 'test', '$2y$10$g9OIMYZAGikfenjpdzgNIe5S/tdoU7PBu.sAEXUpraTN3syjH0e46', 'test', '2026-06-27 13:07:36'),
(2, 'test2', '$2y$10$8RVkzBY6OuCMiXZoaCVkxuBceS4Zixf28DsrnyRbO0seCckfNJSq.', 'test2', '2026-06-27 13:16:07');

-- --------------------------------------------------------

--
-- Table structure for table `wili_workspaces`
--

CREATE TABLE `wili_workspaces` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `owner_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(190) NOT NULL,
  `template_key` varchar(80) NOT NULL DEFAULT '',
  `type` enum('template','user') NOT NULL DEFAULT 'user',
  `status` enum('active','archived','deleted') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_used_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wili_workspaces`
--

INSERT INTO `wili_workspaces` (`id`, `owner_user_id`, `name`, `template_key`, `type`, `status`, `created_at`, `updated_at`, `last_used_at`) VALUES
(1, NULL, 'WILI Electric Template', 'electric', 'template', 'active', '2026-06-24 17:36:30', '2026-06-24 17:36:30', NULL),
(2, NULL, 'WILI Pizzeria Template', 'pizzeria', 'template', 'active', '2026-06-24 17:37:09', '2026-06-24 17:37:09', NULL),
(3, NULL, 'WILI Alimentation Template', 'alimentation', 'template', 'active', '2026-06-24 17:37:09', '2026-06-24 17:37:09', NULL),
(12, 1, 'Demo Electric', 'electric', 'user', 'active', '2026-06-27 13:07:36', '2026-06-27 13:07:36', NULL),
(13, 1, 'Demo Pizzeria', 'pizzeria', 'user', 'active', '2026-06-27 13:09:11', '2026-06-27 13:09:11', NULL),
(14, 2, 'Demo Electric', 'electric', 'user', 'active', '2026-06-27 13:16:07', '2026-06-27 13:16:07', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_events`
--
ALTER TABLE `audit_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_workspace_date` (`workspace_id`,`created_at`);

--
-- Indexes for table `counters`
--
ALTER TABLE `counters`
  ADD PRIMARY KEY (`workspace_id`,`counter_key`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customers_workspace_name` (`workspace_id`,`name`),
  ADD KEY `idx_customers_workspace_phone` (`workspace_id`,`phone`),
  ADD KEY `idx_customers_workspace_reference` (`workspace_id`,`reference`);

--
-- Indexes for table `finance_entries`
--
ALTER TABLE `finance_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_finance_workspace_date` (`workspace_id`,`date`),
  ADD KEY `idx_finance_workspace_ref` (`workspace_id`,`ref`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_products_workspace_name` (`workspace_id`,`name`),
  ADD KEY `idx_products_workspace_barcode` (`workspace_id`,`barcode`),
  ADD KEY `idx_products_workspace_reference` (`workspace_id`,`reference`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sales_workspace_date` (`workspace_id`,`date`),
  ADD KEY `idx_sales_workspace_ref` (`workspace_id`,`ref`);

--
-- Indexes for table `purchase_lines`
--
ALTER TABLE `purchase_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_purchase_lines_workspace_purchase` (`workspace_id`,`purchase_id`),
  ADD KEY `idx_purchase_lines_workspace_product` (`workspace_id`,`product_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sales_workspace_date` (`workspace_id`,`date`),
  ADD KEY `idx_sales_workspace_ref` (`workspace_id`,`ref`);

--
-- Indexes for table `sale_lines`
--
ALTER TABLE `sale_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sale_lines_workspace_sale` (`workspace_id`,`sale_id`),
  ADD KEY `idx_sale_lines_workspace_product` (`workspace_id`,`product_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`workspace_id`,`setting_key`);

--
-- Indexes for table `stock_losses`
--
ALTER TABLE `stock_losses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_stock_losses_workspace_date` (`workspace_id`,`created_at`),
  ADD KEY `idx_stock_losses_workspace_product` (`workspace_id`,`product_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customers_workspace_name` (`workspace_id`,`name`),
  ADD KEY `idx_customers_workspace_phone` (`workspace_id`,`phone`),
  ADD KEY `idx_suppliers_workspace_reference` (`workspace_id`,`reference`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wili_workspaces`
--
ALTER TABLE `wili_workspaces`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_workspaces_owner` (`owner_user_id`,`status`),
  ADD KEY `idx_workspaces_template` (`template_key`,`type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_events`
--
ALTER TABLE `audit_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `finance_entries`
--
ALTER TABLE `finance_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_lines`
--
ALTER TABLE `purchase_lines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sale_lines`
--
ALTER TABLE `sale_lines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stock_losses`
--
ALTER TABLE `stock_losses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wili_workspaces`
--
ALTER TABLE `wili_workspaces`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
