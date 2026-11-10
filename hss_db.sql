-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2026 at 08:58 PM
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
-- Database: `hss_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `model_type`, `model_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'login', 'App\\Models\\User', 1, '2026-11-07 18:27:36', '2026-11-07 18:27:36'),
(2, 1, 'updated', 'App\\Models\\PublicService', 1, '2026-11-07 18:34:34', '2026-11-07 18:34:34'),
(3, 1, 'updated', 'App\\Models\\PublicService', 2, '2026-11-07 18:34:54', '2026-11-07 18:34:54'),
(4, 1, 'updated', 'App\\Models\\PublicService', 3, '2026-11-07 18:35:22', '2026-11-07 18:35:22'),
(5, 1, 'updated', 'App\\Models\\PublicService', 4, '2026-11-07 18:35:28', '2026-11-07 18:35:28'),
(6, 1, 'updated', 'App\\Models\\PublicService', 4, '2026-11-07 18:35:39', '2026-11-07 18:35:39'),
(7, 1, 'updated', 'App\\Models\\PublicService', 5, '2026-11-07 18:35:52', '2026-11-07 18:35:52'),
(8, 1, 'updated', 'App\\Models\\PublicSoftware', 1, '2026-11-07 18:36:39', '2026-11-07 18:36:39'),
(9, 1, 'updated', 'App\\Models\\PublicAchievement', 1, '2026-11-07 18:37:57', '2026-11-07 18:37:57'),
(10, 1, 'updated', 'App\\Models\\PublicAchievement', 2, '2026-11-07 18:38:14', '2026-11-07 18:38:14'),
(11, 1, 'created', 'App\\Models\\CustomerDemo', 1, '2026-11-07 18:40:20', '2026-11-07 18:40:20'),
(12, 1, 'updated', 'App\\Models\\CustomerDemo', 1, '2026-11-07 18:41:49', '2026-11-07 18:41:49'),
(13, 1, 'created', 'App\\Models\\User', 3, '2026-11-07 18:44:14', '2026-11-07 18:44:14'),
(14, 3, 'login', 'App\\Models\\User', 3, '2026-11-07 18:44:47', '2026-11-07 18:44:47'),
(15, 1, 'created', 'App\\Models\\CustomerDemo', 2, '2026-11-07 18:49:31', '2026-11-07 18:49:31'),
(16, 1, 'created', 'App\\Models\\CustomerDemo', 3, '2026-11-07 18:49:58', '2026-11-07 18:49:58'),
(17, 3, 'created', 'App\\Models\\ChatMessage', 1, '2026-11-07 18:50:43', '2026-11-07 18:50:43'),
(18, 1, 'updated', 'App\\Models\\PublicSoftware', 1, '2026-11-07 18:54:37', '2026-11-07 18:54:37'),
(19, 1, 'updated', 'App\\Models\\PublicSoftware', 1, '2026-11-07 18:55:36', '2026-11-07 18:55:36');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('4b1f4c22a3ac7b715a6376fc23f4b574', 'i:1;', 1794081103),
('4b1f4c22a3ac7b715a6376fc23f4b574:timer', 'i:1794081103;', 1794081103),
('94d92f976fd06fd3e8cf53ec4e03d646', 'i:1;', 1794080746),
('94d92f976fd06fd3e8cf53ec4e03d646:timer', 'i:1794080746;', 1794080746);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sender_id` bigint(20) UNSIGNED DEFAULT NULL,
  `receiver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sender_name` varchar(255) DEFAULT NULL,
  `receiver_name` varchar(255) DEFAULT NULL,
  `sender_type` enum('customer','admin') NOT NULL DEFAULT 'customer',
  `message` text NOT NULL,
  `is_seen` tinyint(1) NOT NULL DEFAULT 0,
  `link` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `voice_message` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `user_id`, `sender_id`, `receiver_id`, `customer_group_id`, `sender_name`, `receiver_name`, `sender_type`, `message`, `is_seen`, `link`, `image`, `voice_message`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 3, 3, NULL, NULL, 'Said Bacha', 'Administration', 'customer', 'J\'ai un probleme ...', 0, NULL, NULL, NULL, NULL, '2026-11-07 18:50:43', '2026-11-07 18:50:43');

-- --------------------------------------------------------

--
-- Table structure for table `customer_demos`
--

CREATE TABLE `customer_demos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `public_software_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `demo_link` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `admin_note` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_demos`
--

INSERT INTO `customer_demos` (`id`, `public_software_id`, `name`, `demo_link`, `is_active`, `admin_note`, `image`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'خضر وفواكه', NULL, 1, NULL, 'software_demos/1794080420_33TUTgZ4vrWk.webp', NULL, '2026-11-07 18:40:20', '2026-11-07 18:41:49'),
(2, 1, 'Wili Quincaillerie', NULL, 1, NULL, 'software_demos/1794080971_cZW20uF9wO44.webp', NULL, '2026-11-07 18:49:31', '2026-11-07 18:49:31'),
(3, 1, 'Wili Alimentation', NULL, 1, NULL, 'software_demos/1794080998_LEnNUryMnKYb.webp', NULL, '2026-11-07 18:49:58', '2026-11-07 18:49:58');

-- --------------------------------------------------------

--
-- Table structure for table `customer_groups`
--

CREATE TABLE `customer_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_group_user`
--

CREATE TABLE `customer_group_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_group_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(25, '0001_01_01_000000_create_users_table', 1),
(26, '0001_01_01_000001_create_cache_table', 1),
(27, '0001_01_01_000002_create_jobs_table', 1),
(28, '2026_05_21_142449_create_public_messages_table', 1),
(29, '2026_05_21_142451_create_public_achievements_table', 1),
(30, '2026_05_21_142454_create_public_services_table', 1),
(31, '2026_05_21_142459_create_public_software_table', 1),
(32, '2026_06_04_074717_create_store_subscriptions_table', 1),
(33, '2026_06_04_074720_create_store_requests_table', 1),
(34, '2026_06_13_174549_create_customer_demos_table', 1),
(35, '2026_06_16_114616_create_public_heroes_table', 1),
(36, '2026_06_17_080805_create_activity_logs_table', 1),
(37, '2026_06_22_081537_create_customer_groups_table', 1),
(38, '2026_06_22_081622_create_customer_group_user_table', 1),
(39, '2026_06_22_081629_create_chat_messages_table', 1),
(40, '2026_06_24_090648_update_links_columns_to_text', 1),
(41, '2026_06_27_074625_add_priority_to_store_subscriptions_table', 1),
(42, '2026_06_27_080810_add_is_published_to_store_subscriptions_table', 1),
(43, '2026_06_27_084814_add_voice_message_to_chat_messages_table', 1),
(44, '2026_06_27_123622_update_users_make_email_nullable_add_username', 1),
(45, '2026_06_28_080217_replace_user_id_with_public_software_id_on_customer_demos_table', 1),
(46, '2026_07_07_151646_update_store_subscriptions_type_invitation_to_activation_link', 1),
(47, '2026_07_22_083303_add_slugs_to_store_subscriptions_public_services_and_public_software_tables', 1),
(48, '2026_07_22_092043_fill_null_slugs_with_fake_values', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `public_achievements`
--

CREATE TABLE `public_achievements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ar_title` varchar(255) NOT NULL,
  `fr_title` varchar(255) NOT NULL,
  `en_title` varchar(255) NOT NULL,
  `ar_type` varchar(255) NOT NULL,
  `fr_type` varchar(255) NOT NULL,
  `en_type` varchar(255) NOT NULL,
  `ar_short_description` varchar(255) NOT NULL,
  `fr_short_description` varchar(255) NOT NULL,
  `en_short_description` varchar(255) NOT NULL,
  `ar_description` text DEFAULT NULL,
  `fr_description` text DEFAULT NULL,
  `en_description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `project_url` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `public_achievements`
--

INSERT INTO `public_achievements` (`id`, `ar_title`, `fr_title`, `en_title`, `ar_type`, `fr_type`, `en_type`, `ar_short_description`, `fr_short_description`, `en_short_description`, `ar_description`, `fr_description`, `en_description`, `image`, `project_url`, `is_published`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'ويلي', 'Wili', 'Wili', 'برمجية', 'Logiciel', 'Software', 'نظام ذكي لإدارة المخزون والأعمال يساعد الشركات على إدارة المنتجات والمخزون والمبيعات والعمليات اليومية بكفاءة.', 'Un système intelligent de gestion de stock et d\'entreprise conçu pour aider les entreprises à gérer les produits, les stocks, les ventes et les opérations quotidiennes efficacement.', 'A smart stock and business management system built to help companies manage products, inventory, sales, and daily operations efficiently.', 'ويلي هو نظام إدارة احترافي مصمم للشركات التي تحتاج إلى تحكم أفضل في المخزون والمنتجات والمبيعات والعمليات الداخلية. يوفر واجهة نظيفة وعمليات منظمة وأدوات عملية لتبسيط المهام اليومية.', 'Wili est un système de gestion professionnel conçu pour les entreprises qui ont besoin d\'un meilleur contrôle de leurs stocks, produits, ventes et opérations internes. Il offre une interface claire, des flux de travail organisés et des outils pratiques pour simplifier les tâches quotidiennes.', 'Wili is a professional management system designed for businesses that need better control over their stock, products, sales, and internal operations. It provides a clean interface, organized workflows, and practical tools to simplify daily management tasks.', 'achievements/IUeGJHoBQRgWY9juhOJh1k7Sbph7CoGWuafratXX.webp', NULL, 1, NULL, '2026-11-07 18:14:35', '2026-11-07 18:37:57'),
(2, 'هوريزون بي سي', 'HORIZON PC', 'HORIZON PC', 'متجر إلكتروني', 'E-commerce', 'Ecommerce', 'موقع تجارة إلكترونية عصري لـ HORIZON PC مصمم لعرض المنتجات الرقمية والاشتراكات والبرمجيات وخدمات الألعاب عبر الإنترنت.', 'Un site e-commerce moderne pour HORIZON PC, conçu pour présenter des produits numériques, des abonnements, des logiciels et des services de jeux en ligne.', 'A modern ecommerce website for HORIZON PC, designed to showcase digital products, subscriptions, software, and gaming services online.', 'موقع HORIZON PC هو متجر إلكتروني نظيف ومتجاوب يعرض منتجات مثل مفاتيح الألعاب وبطاقات الهدايا والبرمجيات والاشتراكات والخدمات الرقمية. يساعد العملاء على تصفح المنتجات بسهولة وإجراء الطلبات عبر تجربة شراء سلسة واحترافية.', 'Le site e-commerce HORIZON PC est une boutique en ligne moderne et responsive présentant des produits tels que des clés de jeux, cartes cadeaux, logiciels, abonnements et services numériques. Il permet aux clients de naviguer facilement et de passer commande via une expérience fluide et professionnelle.', 'The HORIZON PC ecommerce website is a clean and responsive online store built to present products such as game keys, gift cards, software, subscriptions, and digital services. It helps customers browse products easily and place orders through a smooth and professional shopping experience.', 'achievements/86ngn0WyLTLKPNKyQQAGRr2k3pxhKDaVjSpyVHxd.webp', 'https://horizon-pc.com', 1, NULL, '2026-11-07 18:14:35', '2026-11-07 18:38:14');

-- --------------------------------------------------------

--
-- Table structure for table `public_heroes`
--

CREATE TABLE `public_heroes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ar_label` varchar(255) DEFAULT NULL,
  `fr_label` varchar(255) DEFAULT NULL,
  `en_label` varchar(255) DEFAULT NULL,
  `ar_title` varchar(255) NOT NULL,
  `fr_title` varchar(255) NOT NULL,
  `en_title` varchar(255) NOT NULL,
  `ar_description` text DEFAULT NULL,
  `fr_description` text DEFAULT NULL,
  `en_description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `facebook_url` text DEFAULT NULL,
  `instagram_url` text DEFAULT NULL,
  `whatsapp_url` text DEFAULT NULL,
  `linked_in_url` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `public_heroes`
--

INSERT INTO `public_heroes` (`id`, `ar_label`, `fr_label`, `en_label`, `ar_title`, `fr_title`, `en_title`, `ar_description`, `fr_description`, `en_description`, `image`, `facebook_url`, `instagram_url`, `whatsapp_url`, `linked_in_url`, `created_at`, `updated_at`) VALUES
(1, 'Horizon Smart Systems', 'Horizon Smart Systems', 'Horizon Smart Systems', 'إنشاء مواقع إلكترونية، برمجيات، أنظمة ERP وحلول معلوماتية مصممة للمؤسسات الجزائرية', 'Développement de sites web, de logiciels, de systèmes ERP et de solutions informatiques conçus pour les entreprises algériennes', 'Development of websites, software, ERP systems, and IT solutions tailored for Algerian businesses.', 'نقدم حلولاً رقمية متكاملة تشمل تطوير المواقع الإلكترونية، البرمجيات المخصصة، أنظمة تسيير المؤسسات، والدعم التقني لمرافقة نمو أعمالكم.', 'Nous proposons des solutions digitales complètes incluant la création de sites web, le développement de logiciels sur mesure, les systèmes ERP et l\'assistance informatique pour accompagner la croissance de votre entreprise.', 'We provide comprehensive digital solutions including website development, custom software, ERP systems, and IT support to empower your business growth.', 'hero/iAIR71tFOVo6f0wYVJ0e9HMdARf5sztBFpvq3fyR.webp', 'https://www.facebook.com/HORIZON.INFORMATIQUE.BISKRA', NULL, 'https://wa.me/+213561655100', NULL, '2026-11-07 18:14:35', '2026-11-07 18:28:02');

-- --------------------------------------------------------

--
-- Table structure for table `public_messages`
--

CREATE TABLE `public_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `is_seen` tinyint(1) NOT NULL DEFAULT 0,
  `admin_note` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `public_services`
--

CREATE TABLE `public_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ar_name` varchar(255) NOT NULL,
  `fr_name` varchar(255) NOT NULL,
  `en_name` varchar(255) NOT NULL,
  `ar_slug` varchar(255) DEFAULT NULL,
  `fr_slug` varchar(255) DEFAULT NULL,
  `en_slug` varchar(255) DEFAULT NULL,
  `ar_short_description` varchar(255) NOT NULL,
  `fr_short_description` varchar(255) NOT NULL,
  `en_short_description` varchar(255) NOT NULL,
  `ar_description` text NOT NULL,
  `fr_description` text NOT NULL,
  `en_description` text NOT NULL,
  `type` enum('main','other') NOT NULL DEFAULT 'main',
  `fa_icon` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `image` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `public_services`
--

INSERT INTO `public_services` (`id`, `ar_name`, `fr_name`, `en_name`, `ar_slug`, `fr_slug`, `en_slug`, `ar_short_description`, `fr_short_description`, `en_short_description`, `ar_description`, `fr_description`, `en_description`, `type`, `fa_icon`, `is_published`, `image`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'إنشاء مواقع ويب', 'Création de sites web', 'Website Creation', 'إنشاء-مواقع-ويب', 'creation-de-sites-web', 'website-creation', 'مواقع ويب حديثة وسريعة ومتجاوبة.', 'Sites web modernes, rapides et responsive.', 'Modern, fast and responsive websites.', 'نقدم خدمة إنشاء مواقع ويب احترافية وعصرية مصممة بعناية لتلبية احتياجات نشاطك التجاري وتعزيز حضورك الرقمي. نقوم بتطوير مواقع سريعة، متجاوبة مع جميع الأجهزة (الهاتف، الحاسوب، اللوحيات)، وسهلة الاستخدام لضمان أفضل تجربة للمستخدم. نركز على تصميم واجهات جذابة وتجربة مستخدم متقدمة، بالإضافة إلى تحسين محركات البحث (SEO) لضمان ظهور موقعك في نتائج البحث. سواء كنت تحتاج إلى موقع تعريفي، متجر إلكتروني، منصة خدمات أو نظام مخصص، فإننا نوفر حلولًا متكاملة من التصميم إلى الإطلاق والدعم المستمر.', 'Nous proposons des services complets de création de sites web modernes et professionnels, conçus pour répondre parfaitement aux besoins de votre activité et renforcer votre présence en ligne. Nos sites sont rapides, sécurisés, entièrement responsives et optimisés pour offrir une expérience utilisateur fluide sur tous les appareils. Nous accordons une grande importance au design, à l’ergonomie ainsi qu’au référencement naturel (SEO) afin d’améliorer votre visibilité sur les moteurs de recherche. Que ce soit pour un site vitrine, un e-commerce ou une plateforme personnalisée, nous vous accompagnons de la conception jusqu’au déploiement et au suivi technique.', 'We provide comprehensive website creation services tailored to elevate your business presence online. Our websites are modern, fast, secure, and fully responsive, ensuring seamless performance across all devices including smartphones, tablets, and desktops. We focus on clean design, intuitive user experience, and SEO optimization to help your business stand out in search results. Whether you need a corporate website, an e-commerce platform, or a custom web solution, we guide you through every stage—from planning and design to development, launch, and ongoing support.', 'main', 'fa-solid fa-globe', 1, 'services/G9tB9x5Qy1u0nqURBFpTUzDaAJlYFh485XLTVUol.webp', NULL, '2026-11-07 18:14:35', '2026-11-07 18:34:34'),
(2, 'تطوير تطبيقات مخصصة', 'Création d\'application sur mesure', 'Custom Application Development', 'تطوير-تطبيقات-مخصصة', 'creation-d-application-sur-mesure', 'custom-application-development', 'تطبيقات مخصصة حسب احتياجاتك.', 'Applications sur mesure adaptées à vos besoins.', 'Tailor-made applications.', 'نقوم بتطوير تطبيقات مخصصة تلبي احتياجاتك الفريدة بدقة، سواء كانت تطبيقات ويب، تطبيقات مكتبية أو أنظمة داخلية. نحلل متطلباتك بعناية ونصمم حلولًا تقنية فعالة تساعدك على تحسين الأداء، تقليل التكاليف، وزيادة الإنتاجية. نعتمد على أحدث التقنيات لضمان الأمان، السرعة، وقابلية التوسع، مع توفير تجربة استخدام سلسة وواجهة سهلة. خدماتنا تشمل التصميم، التطوير، الاختبار، النشر، والدعم المستمر.', 'Nous développons des applications sur mesure parfaitement adaptées à vos besoins spécifiques, qu’il s’agisse d’applications web, de logiciels métiers ou de systèmes internes. Nous analysons vos exigences en profondeur afin de concevoir des solutions efficaces qui optimisent vos processus et améliorent votre productivité. Nos applications sont sécurisées, évolutives et conçues avec des technologies modernes, tout en offrant une expérience utilisateur fluide et intuitive.', 'We specialize in developing custom applications tailored precisely to your business requirements. Whether you need web apps, internal systems, or specialized tools, we design and build scalable, secure, and high-performance solutions. Our process includes deep requirement analysis, user-focused design, robust development, and continuous improvement. The goal is to streamline your operations, enhance efficiency, and provide long-term value through technology.', 'main', 'fa-solid fa-code', 1, 'services/5TFNt0bjzqYxMAOQb9V2cHqiZikpw3Q9G9YAwzqN.webp', NULL, '2026-11-07 18:14:35', '2026-11-07 18:34:54'),
(3, 'الإدارة التجارية والرواتب وحلول أخرى', 'Gestion commerciale, paie et autres solutions', 'Business Management & ERP Solutions', 'الإدارة-التجارية-والرواتب-وحلول-أخرى', 'gestion-commerciale-paie-et-autres-solutions', 'business-management-and-erp-solutions', 'حلول لإدارة وتحسين نشاطك.', 'Solutions pour gérer et optimiser votre activité.', 'Integrated business management solutions.', 'نوفر حلولًا متكاملة لإدارة الأعمال تشمل إدارة المبيعات، المخزون، الموارد البشرية، والرواتب، مما يساعدك على التحكم الكامل في نشاطك التجاري. تتيح لك هذه الأنظمة تتبع العمليات بدقة، تحسين الكفاءة، واتخاذ قرارات مبنية على بيانات دقيقة. حلولنا مرنة وقابلة للتخصيص لتناسب مختلف أنواع الشركات، مع واجهات سهلة الاستخدام وتقارير مفصلة.', 'Nous proposons des solutions complètes de gestion d’entreprise incluant la gestion commerciale, la paie, les ressources humaines et le suivi des opérations. Ces systèmes vous permettent de centraliser vos données, améliorer votre productivité et prendre des décisions stratégiques basées sur des informations fiables. Nos solutions sont flexibles, évolutives et adaptées à tous types d’entreprises.', 'We deliver integrated business management solutions including sales tracking, inventory management, payroll systems, and HR tools. These systems help you gain full control over your operations, improve efficiency, and make data-driven decisions. Our ERP solutions are customizable, scalable, and designed to simplify complex business processes while providing powerful reporting capabilities.', 'main', 'fa-solid fa-briefcase', 1, 'services/vM9h4SBpsQlk4QB2rOPcFJ00hbKLLRDfEtpFQhw7.webp', NULL, '2026-11-07 18:14:35', '2026-11-07 18:35:22'),
(4, 'أنظمة إدارة مخصصة', 'Systèmes de gestion personnalisés', 'Custom Management Systems', 'أنظمة-إدارة-مخصصة', 'systemes-de-gestion-personnalises', 'custom-management-systems', 'برامج لتتبع وإدارة أفضل.', 'Logiciels pour un meilleur suivi.', 'Custom management software.', 'نقوم بتصميم وتطوير أنظمة إدارة مخصصة تساعدك على تنظيم أعمالك وتتبع كل تفاصيل نشاطك بدقة. سواء كنت بحاجة إلى نظام لإدارة العملاء، المشاريع، الطلبات أو العمليات الداخلية، فإننا نقدم حلولًا فعالة وسهلة الاستخدام تعزز إنتاجيتك وتقلل الأخطاء البشرية.', 'Nous développons des systèmes de gestion personnalisés pour vous aider à structurer et suivre efficacement votre activité. Nos solutions sont conçues pour améliorer la productivité, automatiser les tâches et offrir une meilleure visibilité sur vos opérations.', 'We build custom management systems that help you track, organize, and optimize your operations. Whether it’s CRM, project tracking, or workflow automation, our solutions are designed to simplify your work and improve productivity with intuitive interfaces and reliable performance.', 'main', 'fa-solid fa-gears', 1, 'services/2B2elnQq8LbQd1aGJaymTVH8maTUimr83PKy8VLC.webp', NULL, '2026-11-07 18:14:35', '2026-11-07 18:35:39'),
(5, 'الدعم البرمجي والتقني', 'Assistance logicielle et informatique', 'Software & IT Support', 'الدعم-البرمجي-والتقني', 'assistance-logicielle-et-informatique', 'software-and-it-support', 'دعم وحلول تقنية.', 'Support et dépannage informatique.', 'Technical support and troubleshooting.', 'نقدم خدمات دعم تقني متكاملة تشمل صيانة الأنظمة، إصلاح الأعطال، حل مشاكل الشبكات والاتصال، وضمان استمرارية عملك دون انقطاع. فريقنا جاهز للتدخل السريع وتقديم حلول فعالة للحفاظ على استقرار أنظمتك.', 'Nous assurons un support informatique complet incluant la maintenance, le dépannage, la gestion des réseaux et la résolution des problèmes techniques afin de garantir la continuité de votre activité.', 'We provide reliable IT support services including troubleshooting, system maintenance, network management, and performance optimization to ensure your systems run smoothly and efficiently at all times.', 'main', 'fa-solid fa-screwdriver-wrench', 1, 'services/OFCuZdsSk49IWIP5vqrXLnIlrMPoV8GWP6TbR9Ca.webp', NULL, '2026-11-07 18:14:35', '2026-11-07 18:35:52'),
(6, 'توفير البرمجيات الدولية', 'Acquisition de logiciels internationaux', 'International Software Procurement', 'توفير-البرمجيات-الدولية', 'acquisition-de-logiciels-internationaux', 'international-software-procurement', 'توفير البرمجيات الدولية حسب احتياجاتك.', 'Fourniture de logiciels internationaux selon vos besoins.', 'International software procurement according to your needs.', 'نوفر البرمجيات والحلول الدولية الأصلية وفق احتياجاتك وميزانيتك، مع المرافقة في الاختيار والتفعيل.', 'Fourniture de logiciels internationaux et de solutions professionnelles selon vos besoins et votre budget, avec accompagnement dans le choix et l’activation.', 'Supply of international software and professional solutions according to your needs and budget, including guidance for selection and activation.', 'other', 'fa-solid fa-boxes-stacked', 1, NULL, NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(7, 'توفير الاشتراكات', 'Fourniture d\'abonnements', 'Subscription Services', 'توفير-الاشتراكات', 'fourniture-d-abonnements', 'subscription-services', 'اشتراكات رقمية وبرمجيات عالمية.', 'Abonnements numériques et logiciels internationaux.', 'Digital subscriptions and software services.', 'توفير وإدارة اشتراكات الذكاء الاصطناعي والخدمات الرقمية مثل ChatGPT وGemini وAdobe وMicrosoft وSteam Cards وغيرها.', 'Fourniture et gestion d’abonnements IA et de services numériques tels que ChatGPT, Gemini, Adobe, Microsoft, Steam Cards et bien d’autres.', 'Provision and management of AI subscriptions and digital services such as ChatGPT, Gemini, Adobe, Microsoft, Steam Cards and more.', 'other', 'fa-solid fa-credit-card', 1, NULL, NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(8, 'استضافة المواقع الإلكترونية', 'Hébergement de sites web', 'Website Hosting', 'استضافة-المواقع-الإلكترونية', 'hebergement-de-sites-web', 'website-hosting', 'استضافة آمنة وسريعة لمواقعك.', 'Hébergement sécurisé et performant.', 'Secure and high-performance hosting.', 'استضافة احترافية وآمنة وعالية الأداء لمواقعك وتطبيقاتك مع قابلية التوسع والدعم التقني.', 'Hébergement professionnel, sécurisé et performant pour vos sites web et applications avec évolutivité et assistance technique.', 'Professional, secure and scalable hosting for your websites and applications with technical support.', 'other', 'fa-solid fa-server', 1, NULL, NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(9, 'الاستشارة والمرافقة', 'Conseil et accompagnement', 'Consulting & Support', 'الاستشارة-والمرافقة', 'conseil-et-accompagnement', 'consulting-and-support', 'مرافقة استراتيجية وتقنية لمشاريعك.', 'Conseil stratégique et accompagnement.', 'Strategic consulting and project support.', 'نقدم الاستشارة التقنية والاستراتيجية ونرافقك في جميع مراحل تنفيذ وتطوير مشاريعك الرقمية.', 'Nous vous accompagnons à chaque étape de vos projets grâce à un conseil stratégique et technique adapté à vos objectifs.', 'We provide strategic and technical consulting and support throughout every stage of your digital projects.', 'other', 'fa-solid fa-handshake-angle', 1, NULL, NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35');

-- --------------------------------------------------------

--
-- Table structure for table `public_software`
--

CREATE TABLE `public_software` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ar_name` varchar(255) NOT NULL,
  `fr_name` varchar(255) NOT NULL,
  `en_name` varchar(255) NOT NULL,
  `ar_slug` varchar(255) DEFAULT NULL,
  `fr_slug` varchar(255) DEFAULT NULL,
  `en_slug` varchar(255) DEFAULT NULL,
  `ar_short_description` varchar(255) NOT NULL,
  `fr_short_description` varchar(255) NOT NULL,
  `en_short_description` varchar(255) NOT NULL,
  `ar_description` text NOT NULL,
  `fr_description` text NOT NULL,
  `en_description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `public_software`
--

INSERT INTO `public_software` (`id`, `ar_name`, `fr_name`, `en_name`, `ar_slug`, `fr_slug`, `en_slug`, `ar_short_description`, `fr_short_description`, `en_short_description`, `ar_description`, `fr_description`, `en_description`, `image`, `icon`, `is_published`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'ويلي', 'Wili', 'Wili', 'ويلي', 'wili', 'wili', 'نظام تسيير المخزون', 'Système de gestion de stock', 'Stock Management System', 'Wili – نظام متكامل لتسيير وإدارة المخزون\r\nWili هو نظام معلوماتي متكامل ومتخصص في تسيير المخزون وإدارة العمليات التجارية واللوجستية داخل المؤسسات والشركات بمختلف أحجامها وأنشطتها. يهدف البرنامج إلى توفير بيئة عمل رقمية موحدة تساعد المؤسسات على تنظيم مواردها، ومتابعة منتجاتها، والتحكم في مخزونها، وإدارة مبيعاتها وحركات السلع بكفاءة واحترافية، مع تقليل الاعتماد على الطرق التقليدية والعمليات اليدوية التي قد تؤدي إلى الأخطاء وصعوبة تتبع المعلومات.\r\n\r\nيأتي Wili كحل عملي لتبسيط عمليات إدارة المخزون، من خلال توفير مجموعة من الأدوات والوظائف التي تسمح للمستخدمين بالتحكم في مختلف مراحل دورة حياة المنتج، بداية من تسجيل المنتجات وإدخالها إلى النظام، مروراً بتخزينها ومتابعة كمياتها، وصولاً إلى بيعها وإخراجها من المخزون. كما يتيح النظام إمكانية تتبع جميع الحركات التي تتم على السلع، مما يمنح المؤسسة رؤية واضحة ومستمرة حول وضعية مخزونها والعمليات التي تتم داخله.\r\nإدارة المنتجات وتنظيم البيانات\r\nيوفر Wili نظاماً منظماً لإدارة المنتجات، يسمح للمؤسسة بإنشاء قاعدة بيانات مركزية تحتوي على مختلف المعلومات المتعلقة بالمنتجات التي تتعامل معها. يمكن من خلال هذه الوظيفة تسجيل المنتجات الجديدة، وتحديد أسمائها، وأوصافها، وتصنيفاتها، ورموزها التعريفية، بالإضافة إلى المعلومات الأخرى التي تساعد على تمييز كل منتج عن غيره.\r\n\r\nيساعد هذا التنظيم على تسهيل عملية البحث عن المنتجات والوصول إلى المعلومات الخاصة بها بسرعة، كما يساهم في توحيد البيانات وتقليل التكرار والأخطاء الناتجة عن إدخال المعلومات بشكل عشوائي أو الاحتفاظ بها في ملفات منفصلة.\r\n\r\nومن خلال واجهة استخدام واضحة ومنظمة، يمكن للمستخدمين الاطلاع على قائمة المنتجات، ومراجعة بياناتها، وتحديث المعلومات عند الحاجة، مما يجعل عملية إدارة الكتالوج التجاري أكثر سهولة ومرونة.\r\nإدارة المخزون ومراقبة الكميات\r\nتُعد إدارة المخزون من أهم الوظائف الأساسية التي يوفرها نظام Wili، حيث يتيح للمؤسسات متابعة الكميات المتوفرة من المنتجات والتحكم في مستويات المخزون بشكل مستمر.\r\n\r\nيساعد النظام على معرفة الكمية الحالية لكل منتج، ومتابعة التغيرات التي تطرأ عليها نتيجة عمليات الإدخال والإخراج والبيع وغيرها من الحركات. ويساهم ذلك في الحصول على معلومات دقيقة حول الموارد المتاحة داخل المؤسسة، وتجنب المشكلات الناتجة عن عدم معرفة الكميات الحقيقية للمنتجات المخزنة.\r\n\r\nكما تساعد إدارة المخزون على تحسين عملية اتخاذ القرارات المتعلقة بتوفير المنتجات وإعادة التزويد، من خلال الاعتماد على بيانات منظمة ومحدثة بدلاً من التقديرات العشوائية. ويمنح هذا الأمر المؤسسة قدرة أفضل على التخطيط والتحكم في مواردها، والحد من حالات نفاد المنتجات أو تراكم كميات غير ضرورية منها.\r\nإدارة المبيعات والعمليات التجارية\r\nيتضمن Wili وظائف مخصصة لإدارة المبيعات وتنظيم العمليات التجارية المرتبطة بالمنتجات. فمن خلال النظام، يمكن تسجيل عمليات البيع ومتابعة تفاصيلها، بما في ذلك المنتجات المباعة والكميات المرتبطة بكل عملية.\r\n\r\nوتسمح هذه الوظيفة بربط النشاط التجاري بإدارة المخزون، بحيث تنعكس عمليات البيع على الكميات المتوفرة من المنتجات، مما يساعد على المحافظة على توافق بيانات المبيعات مع الوضعية الفعلية للمخزون.\r\n\r\nكما يساهم النظام في الاحتفاظ بسجل منظم للعمليات التجارية، الأمر الذي يسهل الرجوع إلى المعلومات السابقة ومراجعة المعاملات عند الحاجة. ويساعد هذا التنظيم المؤسسات على تحسين متابعة نشاطها التجاري والحصول على تصور أوضح حول حركة منتجاتها.\r\nتتبع حركات السلع\r\nيوفر Wili إمكانية تنظيم ومتابعة حركات السلع داخل المؤسسة، وهي من الوظائف المهمة لضمان التحكم الجيد في المخزون.\r\n\r\nتشمل حركات السلع مختلف العمليات التي تؤثر على الكميات المتوفرة، مثل إدخال المنتجات إلى المخزون، وإخراجها منه، ونقلها بين مواقع التخزين، أو تسجيل التغييرات المرتبطة بها.\r\n\r\nيساعد تسجيل هذه الحركات على معرفة تاريخ التغيرات التي طرأت على كل منتج، وتحديد طبيعة العمليات التي أدت إلى زيادة أو انخفاض كميته. كما يوفر ذلك مستوى أفضل من الشفافية في إدارة الموارد، ويسهل عملية مراجعة العمليات واكتشاف الاختلافات المحتملة بين الكميات المسجلة والكميات الفعلية.\r\n\r\nومن خلال الاحتفاظ بسجل للحركات، تستطيع المؤسسة الحصول على صورة أكثر وضوحاً حول دورة انتقال المنتجات داخل نظامها، مما يعزز إمكانية المراقبة والتنظيم.\r\nتنظيم المعلومات وتحسين كفاءة العمل\r\nيهدف Wili إلى جمع مختلف المعلومات المتعلقة بالمنتجات والمخزون والمبيعات وحركات السلع في نظام واحد، بدلاً من توزيعها على ملفات ورقية أو جداول إلكترونية متعددة.\r\n\r\nيساعد هذا التوحيد على تسهيل الوصول إلى المعلومات، وتحسين التنسيق بين مختلف العمليات، وتقليل الوقت والجهد اللازمين لإنجاز المهام اليومية.\r\n\r\nكما يساهم النظام في الحد من الأخطاء البشرية الناتجة عن المعالجة اليدوية للبيانات، ويوفر بيئة أكثر تنظيماً تسمح للمؤسسة بالتركيز على نشاطها الأساسي بدلاً من استهلاك الوقت في العمليات الإدارية المتكررة.\r\n\r\nويمنح اعتماد نظام مركزي لتسيير المخزون إمكانية الحصول على بيانات أكثر اتساقاً، مما يساعد المسؤولين والعاملين على التعامل مع المعلومات بثقة أكبر.\r\nواجهة استخدام سهلة ومنظمة\r\nيراعي Wili أهمية تجربة المستخدم في تصميم أنظمة التسيير، لذلك يهدف إلى توفير واجهة استخدام بسيطة وواضحة تسمح بالتعامل مع مختلف وظائف البرنامج دون تعقيد.\r\n\r\nتساعد الواجهة المنظمة على تسهيل التنقل بين أقسام النظام، والوصول إلى الوظائف المختلفة، وإدارة البيانات والعمليات بطريقة مباشرة.\r\n\r\nكما يساهم تنظيم المعلومات وعرضها بشكل واضح في تحسين تجربة المستخدم، سواء كان مسؤولاً عن إدارة المخزون أو موظفاً مكلفاً بتسجيل المنتجات والمبيعات والحركات اليومية.\r\nأهداف نظام Wili\r\nيسعى Wili إلى تحقيق مجموعة من الأهداف التي تساعد المؤسسات على تطوير أساليب إدارة مخزونها وتحسين كفاءة عملياتها، ومن أبرزها:\r\n1. رقمنة إدارة المخزون: الانتقال من الطرق التقليدية واليدوية إلى نظام معلوماتي منظم لإدارة المنتجات والكميات.\r\n2. تحسين تنظيم المنتجات: توفير قاعدة بيانات مركزية تسهل تسجيل المنتجات وتصنيفها والبحث عنها.\r\n3. مراقبة الكميات المتوفرة: متابعة الوضعية الحالية للمخزون ومعرفة التغيرات التي تطرأ عليه.\r\n4. تسهيل إدارة المبيعات: تنظيم عمليات البيع وربطها بالمخزون لضمان تحديث البيانات.\r\n5. تتبع حركات السلع: تسجيل عمليات إدخال وإخراج المنتجات ومتابعة تاريخ حركتها.\r\n6. تقليل الأخطاء: الحد من الأخطاء الناتجة عن الإدخال اليدوي والتعامل مع البيانات بطريقة غير منظمة.\r\n7. توفير الوقت والجهد: تسريع العمليات اليومية وتسهيل الوصول إلى المعلومات.\r\n8. تحسين اتخاذ القرارات: توفير معلومات دقيقة تساعد المسؤولين على التخطيط وإدارة الموارد بكفاءة.\r\n9. تعزيز الرقابة والشفافية: تسهيل مراجعة العمليات ومعرفة التغييرات التي تحدث على المخزون.\r\n10. رفع كفاءة المؤسسة: تحسين الأداء الإداري والتجاري من خلال توحيد عمليات التسيير داخل نظام واحد.\r\nأهمية Wili بالنسبة للمؤسسات\r\nفي ظل التطور المتزايد في مجال الرقمنة والتحول الرقمي، أصبحت أنظمة إدارة المخزون من الأدوات الضرورية للمؤسسات التي تسعى إلى تحسين أدائها وتنظيم عملياتها الداخلية.\r\n\r\nفالإدارة الجيدة للمخزون لا تقتصر على معرفة عدد المنتجات المتوفرة فحسب، بل تشمل أيضاً التحكم في تدفق السلع، ومتابعة المبيعات، وتنظيم البيانات، وضمان توفر المعلومات اللازمة لاتخاذ القرارات المناسبة.\r\n\r\nومن هذا المنطلق، يقدم Wili حلاً يهدف إلى مساعدة المؤسسات على بناء بيئة عمل أكثر تنظيماً وفعالية، من خلال توفير الأدوات اللازمة لمتابعة مواردها التجارية وإدارة مخزونها بطريقة رقمية.\r\n\r\nيساهم النظام في تحسين التنسيق بين العمليات المختلفة، ويمنح المستخدمين إمكانية الوصول إلى المعلومات الضرورية دون الحاجة إلى الرجوع إلى مصادر متعددة. كما يساعد على تطوير أساليب العمل وتعزيز الاعتماد على البيانات في تسيير الأنشطة اليومية.\r\nالخلاصة\r\nيمكن اعتبار Wili نظاماً متكاملاً لتسيير المخزون وإدارة العمليات المرتبطة بالمنتجات والمبيعات وحركات السلع، حيث يجمع بين سهولة الاستخدام وتنظيم المعلومات وتبسيط الإجراءات الإدارية والتجارية.\r\n\r\nومن خلال توفير بيئة رقمية موحدة، يساعد النظام المؤسسات على التحكم بشكل أفضل في مخزونها، ومتابعة منتجاتها، وتنظيم عمليات البيع والإدخال والإخراج، مع إمكانية الرجوع إلى سجلات الحركات ومراجعة البيانات عند الحاجة.\r\n\r\nإن الهدف الأساسي من Wili هو جعل عملية تسيير المخزون أكثر سهولة ودقة وفعالية، ومساعدة المؤسسات على تقليل التعقيدات الإدارية وتحسين استغلال مواردها، بما يساهم في رفع مستوى التنظيم والإنتاجية ودعم مسار التحول الرقمي.\r\n\r\nWili – إدارة ذكية، مخزون منظم، وتحكم أفضل في العمليات التجارية.', '# Wili – Un système intégré de gestion des stocks\r\n\r\n**Wili** est un système d\'information intégré, spécialisé dans la gestion des stocks et le pilotage des opérations commerciales et logistiques au sein des entreprises et des organisations de différentes tailles et de différents secteurs d\'activité.\r\n\r\nIl a pour objectif de fournir un environnement de travail numérique unifié permettant aux entreprises d\'organiser leurs ressources, de suivre leurs produits, de contrôler leurs stocks et de gérer leurs ventes ainsi que les mouvements de marchandises de manière efficace et professionnelle. Il contribue également à réduire la dépendance aux méthodes traditionnelles et aux opérations manuelles susceptibles d\'entraîner des erreurs et de compliquer le suivi des informations.\r\n\r\nWili constitue une solution pratique destinée à simplifier les opérations de gestion des stocks grâce à un ensemble d\'outils et de fonctionnalités permettant de contrôler les différentes étapes du cycle de vie d\'un produit : depuis son enregistrement dans le système et son entrée en stock, jusqu\'à sa gestion, son suivi, sa vente et sa sortie du stock.\r\n\r\nLe système permet également de suivre l\'ensemble des mouvements effectués sur les marchandises, offrant ainsi à l\'entreprise une vision claire et actualisée de la situation de ses stocks et des opérations réalisées.\r\n\r\n## Gestion des produits et organisation des données\r\n\r\nWili propose un système structuré de gestion des produits permettant à l\'entreprise de créer une base de données centralisée regroupant les différentes informations relatives aux articles qu\'elle commercialise ou utilise.\r\n\r\nCette fonctionnalité permet d\'enregistrer de nouveaux produits, de définir leurs noms, descriptions, catégories et références, ainsi que d\'autres informations nécessaires à leur identification.\r\n\r\nCette organisation facilite la recherche des produits et l\'accès rapide à leurs informations. Elle contribue également à centraliser les données, à éviter les doublons et à réduire les erreurs liées à la saisie désorganisée ou à la conservation des informations dans plusieurs fichiers distincts.\r\n\r\nGrâce à une interface claire et structurée, les utilisateurs peuvent consulter la liste des produits, examiner leurs informations et les mettre à jour lorsque cela est nécessaire.\r\n\r\n## Gestion et suivi des stocks\r\n\r\nLa gestion des stocks constitue l\'une des fonctionnalités fondamentales de Wili. Le système permet aux entreprises de suivre les quantités disponibles et de contrôler en permanence les niveaux de stock.\r\n\r\nIl permet de connaître la quantité actuelle de chaque produit et de suivre les variations résultant des opérations d\'entrée, de sortie, de vente et des autres mouvements de marchandises.\r\n\r\nCette fonctionnalité offre une vision précise des ressources disponibles au sein de l\'entreprise et permet d\'éviter les problèmes liés au manque de visibilité sur les quantités réellement stockées.\r\n\r\nLa gestion des stocks facilite également la prise de décision concernant le réapprovisionnement et la disponibilité des produits, en s\'appuyant sur des données structurées et actualisées plutôt que sur des estimations approximatives.\r\n\r\nAinsi, l\'entreprise bénéficie d\'une meilleure capacité de planification et de contrôle de ses ressources, tout en limitant les ruptures de stock et l\'accumulation de quantités inutiles.\r\n\r\n## Gestion des ventes et des opérations commerciales\r\n\r\nWili intègre des fonctionnalités dédiées à la gestion des ventes et à l\'organisation des opérations commerciales liées aux produits.\r\n\r\nLe système permet d\'enregistrer les transactions commerciales et d\'en suivre les détails, notamment les produits vendus et les quantités associées à chaque opération.\r\n\r\nCette fonctionnalité assure une liaison entre l\'activité commerciale et la gestion des stocks. Ainsi, les opérations de vente peuvent être répercutées sur les quantités disponibles, contribuant à maintenir la cohérence entre les données commerciales et la situation réelle du stock.\r\n\r\nLe système permet également de conserver un historique structuré des opérations commerciales, facilitant la consultation des informations précédentes et la vérification des transactions lorsque cela est nécessaire.\r\n\r\nCette organisation aide les entreprises à améliorer le suivi de leur activité commerciale et à obtenir une vision plus claire de la circulation de leurs produits.\r\n\r\n## Suivi des mouvements de marchandises\r\n\r\nWili offre la possibilité d\'organiser et de suivre les mouvements de marchandises au sein de l\'entreprise, une fonctionnalité essentielle pour assurer un contrôle efficace des stocks.\r\n\r\nLes mouvements de marchandises comprennent les différentes opérations ayant un impact sur les quantités disponibles, telles que les entrées en stock, les sorties de stock, les transferts entre différents lieux de stockage et les autres modifications liées aux produits.\r\n\r\nL\'enregistrement de ces mouvements permet de connaître l\'historique des changements affectant chaque produit et d\'identifier les opérations responsables de l\'augmentation ou de la diminution de ses quantités.\r\n\r\nCette fonctionnalité améliore la transparence dans la gestion des ressources et facilite la vérification des opérations ainsi que la détection d\'éventuels écarts entre les quantités enregistrées et les quantités physiques.\r\n\r\nGrâce à la conservation d\'un historique des mouvements, l\'entreprise dispose d\'une vision plus précise du cycle de circulation des produits au sein de son organisation.\r\n\r\n## Organisation des informations et amélioration de l\'efficacité opérationnelle\r\n\r\nWili vise à centraliser les différentes informations relatives aux produits, aux stocks, aux ventes et aux mouvements de marchandises dans un seul système, au lieu de les disperser dans des documents papier ou des feuilles de calcul distinctes.\r\n\r\nCette centralisation facilite l\'accès aux informations, améliore la coordination entre les différentes opérations et réduit le temps et les efforts nécessaires à l\'exécution des tâches quotidiennes.\r\n\r\nLe système contribue également à limiter les erreurs humaines liées au traitement manuel des données et offre un environnement de travail mieux organisé.\r\n\r\nIl permet ainsi aux entreprises de consacrer davantage de temps à leurs activités principales plutôt qu\'à des tâches administratives répétitives.\r\n\r\nL\'utilisation d\'un système centralisé garantit une meilleure cohérence des données et permet aux responsables et aux employés de travailler avec des informations plus fiables.\r\n\r\n## Une interface utilisateur simple et organisée\r\n\r\nWili accorde une importance particulière à l\'expérience utilisateur dans la conception des systèmes de gestion. Il vise ainsi à proposer une interface simple, intuitive et claire permettant d\'accéder facilement aux différentes fonctionnalités du logiciel.\r\n\r\nL\'organisation de l\'interface facilite la navigation entre les différentes sections du système, l\'accès aux outils disponibles ainsi que la gestion des données et des opérations.\r\n\r\nLa présentation structurée des informations améliore également l\'expérience utilisateur, qu\'il s\'agisse d\'un responsable de stock ou d\'un employé chargé de l\'enregistrement des produits, des ventes et des mouvements quotidiens.\r\n\r\n## Les objectifs du système Wili\r\n\r\nWili poursuit plusieurs objectifs destinés à aider les entreprises à moderniser leurs méthodes de gestion des stocks et à améliorer l\'efficacité de leurs opérations :\r\n\r\n1. **Digitalisation de la gestion des stocks :** remplacer les méthodes traditionnelles et manuelles par un système informatique structuré.\r\n2. **Amélioration de l\'organisation des produits :** mettre à disposition une base de données centralisée facilitant l\'enregistrement, la classification et la recherche des produits.\r\n3. **Contrôle des quantités disponibles :** suivre la situation actuelle des stocks et les variations qui les affectent.\r\n4. **Simplification de la gestion des ventes :** organiser les opérations commerciales et les relier aux stocks.\r\n5. **Suivi des mouvements de marchandises :** enregistrer les entrées et les sorties des produits et consulter leur historique.\r\n6. **Réduction des erreurs :** limiter les erreurs liées à la saisie manuelle et à la mauvaise organisation des données.\r\n7. **Gain de temps et d\'efforts :** accélérer les opérations quotidiennes et faciliter l\'accès aux informations.\r\n8. **Amélioration de la prise de décision :** fournir des informations précises permettant aux responsables de mieux planifier et gérer leurs ressources.\r\n9. **Renforcement du contrôle et de la transparence :** faciliter la vérification des opérations et le suivi des modifications apportées aux stocks.\r\n10. **Amélioration de la performance de l\'entreprise :** optimiser les performances administratives et commerciales grâce à la centralisation des opérations dans un seul système.\r\n\r\n## L\'importance de Wili pour les entreprises\r\n\r\nDans un contexte marqué par le développement continu des technologies numériques et de la transformation digitale, les systèmes de gestion des stocks sont devenus des outils essentiels pour les entreprises souhaitant améliorer leurs performances et organiser leurs opérations internes.\r\n\r\nUne bonne gestion des stocks ne consiste pas uniquement à connaître le nombre de produits disponibles. Elle implique également le contrôle des flux de marchandises, le suivi des ventes, l\'organisation des données et la disponibilité des informations nécessaires à une prise de décision pertinente.\r\n\r\nDans cette perspective, Wili propose une solution destinée à accompagner les entreprises dans la mise en place d\'un environnement de travail plus organisé et plus efficace, en mettant à leur disposition les outils nécessaires au suivi de leurs ressources commerciales et à la gestion numérique de leurs stocks.\r\n\r\nLe système contribue à améliorer la coordination entre les différentes opérations et permet aux utilisateurs d\'accéder aux informations essentielles sans devoir consulter plusieurs sources distinctes.\r\n\r\nIl favorise également la modernisation des méthodes de travail et encourage une gestion davantage fondée sur les données.\r\n\r\n## Conclusion\r\n\r\nWili peut être considéré comme un **système intégré de gestion des stocks et de pilotage des opérations liées aux produits, aux ventes et aux mouvements de marchandises**.\r\n\r\nEn réunissant les différentes fonctionnalités dans un environnement numérique unifié, le système aide les entreprises à mieux contrôler leurs stocks, à suivre leurs produits, à organiser les opérations de vente, d\'entrée et de sortie, ainsi qu\'à consulter l\'historique des mouvements et à vérifier les données lorsque cela est nécessaire.\r\n\r\nL\'objectif principal de Wili est de rendre la gestion des stocks plus simple, plus précise et plus efficace, tout en aidant les entreprises à réduire les complexités administratives et à optimiser l\'utilisation de leurs ressources.\r\n\r\nCette approche contribue à améliorer l\'organisation, la productivité et à accompagner les entreprises dans leur processus de transformation numérique.\r\n\r\n**Wili – Une gestion intelligente, des stocks organisés et un meilleur contrôle des opérations commerciales.**', '# Wili – An Integrated Inventory Management System\r\n\r\n**Wili** is an integrated information system specialized in inventory management and the administration of commercial and logistical operations within businesses and organizations of different sizes and industries.\r\n\r\nIts main objective is to provide a unified digital working environment that enables businesses to organize their resources, track their products, control their inventory, and manage sales and goods movements efficiently and professionally. It also aims to reduce reliance on traditional methods and manual processes that may lead to errors and make information tracking more difficult.\r\n\r\nWili is a practical solution designed to simplify inventory management operations through a comprehensive set of tools and features that allow users to control the different stages of a product\'s lifecycle, starting with product registration and stock entry, followed by storage and quantity monitoring, and ending with sales and stock removal.\r\n\r\nThe system also enables businesses to track all movements performed on their goods, providing a clear and continuously updated overview of their inventory status and the operations taking place within it.\r\n\r\n## Product Management and Data Organization\r\n\r\nWili provides a structured product management system that allows businesses to create a centralized database containing all relevant information about the products they handle.\r\n\r\nThis functionality makes it possible to register new products, define their names, descriptions, categories, identification codes, and other information required to distinguish each product from others.\r\n\r\nThis organization makes it easier to search for products and quickly access their information. It also helps centralize data, prevent duplication, and reduce errors caused by disorganized data entry or storing information across multiple separate files.\r\n\r\nThrough a clear and well-organized user interface, users can browse the product catalog, review product details, and update information whenever necessary.\r\n\r\n## Inventory Management and Stock Monitoring\r\n\r\nInventory management is one of the core functionalities offered by Wili. The system enables businesses to monitor available product quantities and maintain continuous control over their stock levels.\r\n\r\nIt allows users to identify the current quantity of each product and monitor changes resulting from stock entries, stock exits, sales, and other inventory movements.\r\n\r\nThis functionality provides businesses with accurate information about their available resources and helps prevent problems caused by a lack of visibility into actual stock quantities.\r\n\r\nInventory management also supports better decision-making regarding product availability and replenishment by relying on structured and up-to-date information rather than rough estimates.\r\n\r\nAs a result, businesses can improve resource planning and inventory control while reducing the risk of stock shortages and unnecessary inventory accumulation.\r\n\r\n## Sales Management and Commercial Operations\r\n\r\nWili includes dedicated features for managing sales and organizing commercial operations related to products.\r\n\r\nThrough the system, users can record sales transactions and monitor their details, including the products sold and the quantities associated with each transaction.\r\n\r\nThis functionality connects commercial activities with inventory management. Consequently, sales operations can be reflected in the available stock quantities, helping maintain consistency between sales records and actual inventory levels.\r\n\r\nThe system also maintains an organized history of commercial transactions, making it easier to retrieve previous information and review transactions whenever necessary.\r\n\r\nThis organization helps businesses improve the monitoring of their commercial activities and gain a clearer understanding of product movements.\r\n\r\n## Goods Movement Tracking\r\n\r\nWili provides tools for organizing and tracking goods movements within a business, which is an essential functionality for maintaining effective inventory control.\r\n\r\nGoods movements include all operations that affect available stock quantities, such as receiving products into inventory, removing products from inventory, transferring goods between storage locations, and recording other inventory-related changes.\r\n\r\nRecording these movements makes it possible to track the history of changes affecting each product and identify the operations responsible for increasing or decreasing its quantity.\r\n\r\nThis functionality improves transparency in resource management and makes it easier to review operations and detect potential discrepancies between recorded quantities and actual physical stock.\r\n\r\nBy maintaining a history of inventory movements, businesses can obtain a clearer understanding of how products circulate throughout their operations.\r\n\r\n## Information Organization and Operational Efficiency\r\n\r\nWili aims to centralize all information related to products, inventory, sales, and goods movements within a single system, rather than distributing it across paper documents or multiple spreadsheets.\r\n\r\nThis centralization simplifies access to information, improves coordination between different operations, and reduces the time and effort required to complete daily tasks.\r\n\r\nThe system also helps minimize human errors associated with manual data processing and provides a more organized working environment.\r\n\r\nAs a result, businesses can dedicate more time and resources to their core activities instead of spending excessive time on repetitive administrative tasks.\r\n\r\nUsing a centralized management system also improves data consistency, allowing managers and employees to work with more reliable information.\r\n\r\n## A Simple and Well-Organized User Interface\r\n\r\nWili recognizes the importance of user experience in the design of management systems. Therefore, it aims to provide a simple, intuitive, and well-structured interface that allows users to access the software\'s different functionalities without unnecessary complexity.\r\n\r\nThe organized interface makes it easier to navigate between different sections of the system, access available features, and manage data and operations efficiently.\r\n\r\nPresenting information in a clear and structured manner also improves the user experience, whether the user is an inventory manager or an employee responsible for recording products, sales, and daily stock movements.\r\n\r\n## Objectives of the Wili System\r\n\r\nWili aims to achieve several objectives that help businesses modernize their inventory management practices and improve operational efficiency:\r\n\r\n1. **Digitalizing inventory management:** Replacing traditional and manual methods with a structured digital management system.\r\n2. **Improving product organization:** Providing a centralized database that simplifies product registration, classification, and searching.\r\n3. **Monitoring available quantities:** Tracking current inventory levels and identifying changes affecting stock.\r\n4. **Simplifying sales management:** Organizing commercial transactions and connecting them with inventory records.\r\n5. **Tracking goods movements:** Recording product entries and exits while maintaining a history of inventory movements.\r\n6. **Reducing errors:** Minimizing mistakes caused by manual data entry and poorly organized information.\r\n7. **Saving time and effort:** Accelerating daily operations and simplifying access to information.\r\n8. **Improving decision-making:** Providing accurate information that helps managers plan and manage resources more effectively.\r\n9. **Enhancing control and transparency:** Making it easier to review operations and monitor inventory changes.\r\n10. **Improving business performance:** Optimizing administrative and commercial operations by centralizing management activities within a single system.\r\n\r\n## The Importance of Wili for Businesses\r\n\r\nIn an environment characterized by continuous technological development and digital transformation, inventory management systems have become essential tools for businesses seeking to improve performance and organize their internal operations.\r\n\r\nEffective inventory management involves much more than simply knowing how many products are available. It also includes controlling goods flows, monitoring sales, organizing information, and ensuring the availability of the data required to make appropriate decisions.\r\n\r\nFrom this perspective, Wili provides a solution designed to help businesses establish a more organized and efficient working environment by offering the tools needed to monitor commercial resources and manage inventory digitally.\r\n\r\nThe system contributes to better coordination between different business operations and allows users to access essential information without having to consult multiple separate sources.\r\n\r\nIt also supports the modernization of working methods and encourages a more data-driven approach to business management.\r\n\r\n## Conclusion\r\n\r\nWili can be described as an **integrated inventory management system designed to manage products, sales, and goods movements**.\r\n\r\nBy bringing different management functionalities together within a unified digital environment, the system helps businesses maintain better control over their inventory, track their products, organize sales and stock entry and exit operations, and review movement histories whenever necessary.\r\n\r\nThe primary goal of Wili is to make inventory management simpler, more accurate, and more efficient while helping businesses reduce administrative complexity and optimize the use of their resources.\r\n\r\nThis approach contributes to improved organization, increased productivity, and the advancement of digital transformation within businesses.\r\n\r\n**Wili – Smart Management, Organized Inventory, and Better Control Over Business Operations.**', 'software/1TZq1hGDo7gKJ8zlRO3d3zjSiw9tD7cPKuLoXhHg.webp', 'software_icons/MVKSIRaeVdjmGyjAtdr4GqxW2Fi3U3aflkClc1l2.webp', 1, NULL, '2026-11-07 18:14:35', '2026-11-07 18:55:36');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('MbZAogiiUNfxvWnxbf1I2aAkzWQ15XhTQGTPn2qK', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36 Edg/157.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiVnAzTWtXcFhtb0lMVERkMTRDTnZMN2tRN3hTcDE1Z2h0cEl2QWRaeSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jdXN0b21lci9wb3J0YWwiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjY6ImxvY2FsZSI7czoyOiJlbiI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mzt9', 1794081106),
('yEJzbIOxBABFjZcmKt7uimjUry6I7iYFIryFzmJe', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiQzhoQlJKckZ2T1JQTTI0RWJjbEh5TnNzR2pRSjQ1dGtGUjB5cnM3diI7czo2OiJsb2NhbGUiO3M6MjoiZW4iO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQwOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vYWNoaWV2ZW1lbnRzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1794081388);

-- --------------------------------------------------------

--
-- Table structure for table `store_requests`
--

CREATE TABLE `store_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `store_subscription_id` bigint(20) UNSIGNED NOT NULL,
  `customer_full_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_address` varchar(255) DEFAULT NULL,
  `customer_contact` varchar(255) NOT NULL,
  `quantity` varchar(255) NOT NULL,
  `unit_price` int(11) NOT NULL,
  `status` enum('pending','processing','available','cancelled','postponed','paid','delivered') NOT NULL DEFAULT 'pending',
  `admin_note` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `store_subscriptions`
--

CREATE TABLE `store_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `ar_slug` varchar(255) DEFAULT NULL,
  `fr_slug` varchar(255) DEFAULT NULL,
  `en_slug` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` int(11) NOT NULL,
  `discount` int(11) DEFAULT NULL,
  `priority` int(11) NOT NULL DEFAULT 0,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `image` varchar(255) DEFAULT NULL,
  `type` enum('key','account','activation_link') DEFAULT NULL,
  `category` enum('games','gift_cards','software','subscriptions','in_game_currency','other') NOT NULL DEFAULT 'other',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `store_subscriptions`
--

INSERT INTO `store_subscriptions` (`id`, `name`, `ar_slug`, `fr_slug`, `en_slug`, `description`, `price`, `discount`, `priority`, `is_published`, `image`, `type`, `category`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'ChatGPT Plus Subscription 1 Month', 'اشتراك-شات-جي-بي-تي-بلس-شهر-واحد', 'abonnement-chatgpt-plus-1-mois', 'chatgpt-plus-subscription-1-month', 'Monthly ChatGPT Plus subscription account for AI writing, coding, research, and productivity tasks.', 6500, NULL, 0, 1, NULL, 'account', 'subscriptions', NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(2, 'Gemini Pro Subscription 1 Month', 'اشتراك-جيميني-برو-شهر-واحد', 'abonnement-gemini-pro-1-mois', 'gemini-pro-subscription-1-month', 'Google Gemini Pro subscription account for AI assistance, productivity, and creative work.', 6000, NULL, 0, 1, NULL, 'account', 'subscriptions', NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(3, 'Adobe Creative Cloud 1 Month', 'اشتراك-أدوبي-كرييتف-كلاود-شهر-واحد', 'adobe-creative-cloud-1-mois', 'adobe-creative-cloud-1-month', 'Adobe Creative Cloud subscription for Photoshop, Illustrator, Premiere Pro, and other creative tools.', 7500, NULL, 0, 1, NULL, 'account', 'subscriptions', NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(4, 'Netflix Premium 1 Month', 'اشتراك-نتفليكس-بريميوم-شهر-واحد', 'netflix-premium-1-mois', 'netflix-premium-1-month', 'Netflix Premium account access for movies, series, and entertainment streaming.', 2200, NULL, 0, 1, NULL, 'account', 'subscriptions', NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(5, 'Spotify Premium 1 Month', 'اشتراك-سبوتيفاي-بريميوم-شهر-واحد', 'spotify-premium-1-mois', 'spotify-premium-1-month', 'Spotify Premium subscription for ad-free music, offline listening, and high-quality audio.', 1800, NULL, 0, 1, NULL, 'account', 'subscriptions', NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(6, 'YouTube Premium 1 Month', 'اشتراك-يوتيوب-بريميوم-شهر-واحد', 'youtube-premium-1-mois', 'youtube-premium-1-month', 'YouTube Premium account access with ad-free videos, background play, and YouTube Music.', 2500, NULL, 0, 1, NULL, 'account', 'subscriptions', NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(7, 'Microsoft Office 2021 Professional Plus', 'مايكروسوفت-أوفيس-2021-بروفيشنال-بلس', 'microsoft-office-2021-professional-plus', 'microsoft-office-2021-professional-plus', 'Microsoft Office 2021 Professional Plus activation key for Word, Excel, PowerPoint, Outlook, and more.', 4500, NULL, 0, 1, NULL, 'key', 'software', NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(8, 'Windows 11 Pro Key', 'مفتاح-تفعيل-ويندوز-11-برو', 'cle-windows-11-pro', 'windows-11-pro-key', 'Windows 11 Pro activation key for professional use, security features, and modern productivity.', 3500, NULL, 0, 1, NULL, 'key', 'software', NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(9, 'Windows 10 Pro Key', 'مفتاح-تفعيل-ويندوز-10-برو', 'cle-windows-10-pro', 'windows-10-pro-key', 'Windows 10 Pro activation key for business, gaming, and daily computer use.', 3000, NULL, 0, 1, NULL, 'key', 'software', NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(10, 'Steam Gift Card 10 USD', 'بطاقة-هدية-ستيم-10-دولار', 'carte-cadeau-steam-10-usd', 'steam-gift-card-10-usd', 'Steam wallet gift card for buying PC games, software, and in-game content on Steam.', 2700, NULL, 0, 1, NULL, 'key', 'gift_cards', NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(11, 'Steam Gift Card 20 USD', 'بطاقة-هدية-ستيم-20-دولار', 'carte-cadeau-steam-20-usd', 'steam-gift-card-20-usd', 'Steam wallet gift card for purchasing games, DLCs, and digital content.', 5200, NULL, 0, 1, NULL, 'key', 'gift_cards', NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(12, 'PlayStation Store Gift Card 10 USD', 'بطاقة-هدية-بلايستيشن-ستور-10-دولار', 'carte-cadeau-playstation-store-10-usd', 'playstation-store-gift-card-10-usd', 'PlayStation Store gift card for games, add-ons, subscriptions, and digital content.', 2800, NULL, 0, 1, NULL, 'key', 'gift_cards', NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(13, 'Xbox Gift Card 10 USD', 'بطاقة-هدية-إكس-بوكس-10-دولار', 'carte-cadeau-xbox-10-usd', 'xbox-gift-card-10-usd', 'Xbox gift card for buying games, subscriptions, and digital content from the Microsoft Store.', 2700, NULL, 0, 1, NULL, 'key', 'gift_cards', NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(14, 'EA Sports FC 25', 'لعبة-إي-أيه-سبورتس-إف-سي-25', 'ea-sports-fc-25', 'ea-sports-fc-25', 'EA Sports FC 25 game key for football fans with clubs, tournaments, and online matches.', 9500, NULL, 0, 1, NULL, 'key', 'games', NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(15, 'Grand Theft Auto V', 'لعبة-جراند-ثيفت-أوتو-5', 'grand-theft-auto-v', 'grand-theft-auto-v', 'GTA V game key with story mode, online multiplayer, missions, vehicles, and open-world gameplay.', 4500, NULL, 0, 1, NULL, 'key', 'games', NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(16, 'Minecraft Java & Bedrock Edition', 'ماينكرافت-جافا-وبيدروك-إيديشن', 'minecraft-java-et-bedrock-edition', 'minecraft-java-and-bedrock-edition', 'Minecraft Java and Bedrock Edition account/key for creative building, survival, and multiplayer.', 6200, NULL, 0, 1, NULL, 'key', 'games', NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(17, 'Red Dead Redemption 2', 'لعبة-ريد-ديد-ريدمبشن-2', 'red-dead-redemption-2', 'red-dead-redemption-2', 'Red Dead Redemption 2 game key with an immersive open-world western adventure.', 6800, NULL, 0, 1, NULL, 'key', 'games', NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(18, 'Fortnite V-Bucks 1000', 'فورتنايت-1000-في-باكس', 'fortnite-v-bucks-1000', 'fortnite-v-bucks-1000', 'Fortnite V-Bucks digital code for skins, battle passes, emotes, and in-game items.', 2500, NULL, 0, 1, NULL, 'key', 'in_game_currency', NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(19, 'PUBG Mobile UC Pack', 'شدات-ببجي-موبايل-يو-سي', 'pack-uc-pubg-mobile', 'pubg-mobile-uc-pack', 'PUBG Mobile UC pack for buying outfits, crates, royal pass, and in-game items.', 2200, NULL, 0, 1, NULL, 'key', 'in_game_currency', NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(20, 'Discord Nitro 1 Month', 'اشتراك-ديسكورد-نيترو-شهر-واحد', 'discord-nitro-1-mois', 'discord-nitro-1-month', 'Discord Nitro subscription for profile customization, server boosts, HD streaming, and bigger uploads.', 3000, NULL, 0, 1, NULL, 'account', 'subscriptions', NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(1024) DEFAULT NULL,
  `role` enum('user','admin','operator','customer') NOT NULL DEFAULT 'user',
  `phone` varchar(10) DEFAULT NULL,
  `company` varchar(50) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(1024) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `role`, `phone`, `company`, `email_verified_at`, `password`, `remember_token`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Sohaib KHIAF', 'sohaibkhiaf', 'sohaibkhiaf@gmail.com', 'admin', NULL, 'Horizon Smart Systems', '2026-11-07 18:14:34', '$2y$12$mQ49ujNCnpJa2U5nQEmlJuWu3SDchbvxCfN7KzRkK4HdjV0tNnorS', 'xvtpyKizGE99T9zNYhwzii7dmANuhBbpTB34mLE9kd85wjIKelk0Ugkhy1jY', NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(2, 'Mohamed Amine SALEM', 'aminesalem45670', 'aminesalem45670@gmail.com', 'admin', NULL, 'Horizon Smart Systems', '2026-11-07 18:14:35', '$2y$12$3AbN1oKNSjp9vNK69EAHb.s4WpCjlCkyBoifYEXJ6I3zqYe3ddx9.', NULL, NULL, '2026-11-07 18:14:35', '2026-11-07 18:14:35'),
(3, 'Said Bacha', 'saidbacha', NULL, 'customer', '0550123777', 'HSS', NULL, '$2y$12$oFsN.vt1bh.YpV9Y9mYqy.9j.gKbenHwUSW90z5w6G.iQqctdH3T2', 'eljtcsb5Jgj4aSFi8VywOpueP9P7sdVGfJ8YerlPygBjHJcKYYpOXGZXmSlJ', NULL, '2026-11-07 18:44:14', '2026-11-07 18:44:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_messages_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `chat_messages_sender_id_created_at_index` (`sender_id`,`created_at`),
  ADD KEY `chat_messages_receiver_id_created_at_index` (`receiver_id`,`created_at`),
  ADD KEY `chat_messages_customer_group_id_created_at_index` (`customer_group_id`,`created_at`),
  ADD KEY `chat_messages_sender_type_is_seen_index` (`sender_type`,`is_seen`);

--
-- Indexes for table `customer_demos`
--
ALTER TABLE `customer_demos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_demos_public_software_id_foreign` (`public_software_id`);

--
-- Indexes for table `customer_groups`
--
ALTER TABLE `customer_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_groups_name_unique` (`name`),
  ADD KEY `customer_groups_created_by_foreign` (`created_by`);

--
-- Indexes for table `customer_group_user`
--
ALTER TABLE `customer_group_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_group_user_customer_group_id_user_id_unique` (`customer_group_id`,`user_id`),
  ADD KEY `customer_group_user_user_id_foreign` (`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `public_achievements`
--
ALTER TABLE `public_achievements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `public_heroes`
--
ALTER TABLE `public_heroes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `public_messages`
--
ALTER TABLE `public_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `public_services`
--
ALTER TABLE `public_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `public_services_ar_slug_unique` (`ar_slug`),
  ADD UNIQUE KEY `public_services_fr_slug_unique` (`fr_slug`),
  ADD UNIQUE KEY `public_services_en_slug_unique` (`en_slug`);

--
-- Indexes for table `public_software`
--
ALTER TABLE `public_software`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `public_software_ar_slug_unique` (`ar_slug`),
  ADD UNIQUE KEY `public_software_fr_slug_unique` (`fr_slug`),
  ADD UNIQUE KEY `public_software_en_slug_unique` (`en_slug`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `store_requests`
--
ALTER TABLE `store_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `store_requests_store_subscription_id_foreign` (`store_subscription_id`);

--
-- Indexes for table `store_subscriptions`
--
ALTER TABLE `store_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `store_subscriptions_ar_slug_unique` (`ar_slug`),
  ADD UNIQUE KEY `store_subscriptions_fr_slug_unique` (`fr_slug`),
  ADD UNIQUE KEY `store_subscriptions_en_slug_unique` (`en_slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`(768));

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customer_demos`
--
ALTER TABLE `customer_demos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customer_groups`
--
ALTER TABLE `customer_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_group_user`
--
ALTER TABLE `customer_group_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `public_achievements`
--
ALTER TABLE `public_achievements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `public_heroes`
--
ALTER TABLE `public_heroes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `public_messages`
--
ALTER TABLE `public_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `public_services`
--
ALTER TABLE `public_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `public_software`
--
ALTER TABLE `public_software`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `store_requests`
--
ALTER TABLE `store_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `store_subscriptions`
--
ALTER TABLE `store_subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_customer_group_id_foreign` FOREIGN KEY (`customer_group_id`) REFERENCES `customer_groups` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chat_messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chat_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chat_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_demos`
--
ALTER TABLE `customer_demos`
  ADD CONSTRAINT `customer_demos_public_software_id_foreign` FOREIGN KEY (`public_software_id`) REFERENCES `public_software` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_groups`
--
ALTER TABLE `customer_groups`
  ADD CONSTRAINT `customer_groups_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customer_group_user`
--
ALTER TABLE `customer_group_user`
  ADD CONSTRAINT `customer_group_user_customer_group_id_foreign` FOREIGN KEY (`customer_group_id`) REFERENCES `customer_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_group_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `store_requests`
--
ALTER TABLE `store_requests`
  ADD CONSTRAINT `store_requests_store_subscription_id_foreign` FOREIGN KEY (`store_subscription_id`) REFERENCES `store_subscriptions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
