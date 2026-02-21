-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 21, 2026 at 08:47 AM
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
-- Database: `rent`
--

-- --------------------------------------------------------

--
-- Table structure for table `agents`
--

CREATE TABLE `agents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `phone` varchar(191) NOT NULL,
  `photo_path` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `listing_id` bigint(20) UNSIGNED NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `total_price` decimal(15,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(191) NOT NULL,
  `owner` varchar(191) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `listing_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(191) NOT NULL,
  `message` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `listing_id`, `user_id`, `name`, `email`, `phone`, `message`, `read_at`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'chisala luckaon', 'chisalaluckyk5@gmail.com', '+260chisalaluckyk5@gmail.com', 'tbttbtbbb', NULL, '2026-02-14 09:41:43', '2026-02-14 09:41:43');

-- --------------------------------------------------------

--
-- Table structure for table `listings`
--

CREATE TABLE `listings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `public_id` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `description` text NOT NULL,
  `type` enum('rent','buy') NOT NULL DEFAULT 'rent',
  `category` varchar(191) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `currency` enum('ZMW','USD') NOT NULL,
  `location` varchar(191) NOT NULL,
  `city` varchar(191) DEFAULT NULL,
  `country` varchar(191) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `video_path` varchar(191) DEFAULT NULL,
  `bedrooms` int(11) DEFAULT NULL,
  `bathrooms` int(11) DEFAULT NULL,
  `area` varchar(191) DEFAULT NULL,
  `year_built` int(11) DEFAULT NULL,
  `previous_renters` int(11) NOT NULL DEFAULT 0,
  `condition` varchar(191) DEFAULT NULL,
  `cuisine` varchar(191) DEFAULT NULL,
  `amenities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`amenities`)),
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','sold','expired') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `agent_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `listings`
--

INSERT INTO `listings` (`id`, `public_id`, `user_id`, `title`, `description`, `type`, `category`, `price`, `currency`, `location`, `city`, `country`, `latitude`, `longitude`, `images`, `video_path`, `bedrooms`, `bathrooms`, `area`, `year_built`, `previous_renters`, `condition`, `cuisine`, `amenities`, `is_featured`, `views`, `status`, `created_at`, `updated_at`, `agent_id`) VALUES
(1, 'b0f1352f-0a8c-433d-8c62-57d7b6f6b0af', 3, 'chalal', 'very clean', 'rent', 'house', 4500.00, 'ZMW', 'matero', NULL, NULL, NULL, NULL, '[\"\\/storage\\/listings\\/AOrgvVRdtCVUX1s2udWcPy2WGpOJbd2Boq0gtkev.webp\",\"\\/storage\\/listings\\/dH5xE6WNSFkuvbtyLMUnNgcdTwYBBWbdxIwAea7Q.webp\"]', NULL, 2, 2, '50', 2014, 2, 'Fair', NULL, NULL, 0, 60, 'active', '2026-02-14 09:01:59', '2026-02-21 03:50:31', NULL),
(2, '471e230c-a51c-44ea-b21e-71518e3d6311', 3, 'house', 'very clean', 'rent', 'house', 2000.00, 'ZMW', 'matero', NULL, NULL, NULL, NULL, '[\"\\/storage\\/listings\\/ceRrzqmZmiAqejA7JTlqOTQxrvV4LO7ADmfi7FKQ.webp\",\"\\/storage\\/listings\\/a0YI3vHsl41yLMNSvt2OLJ8dpUPOkaF92py6xEFQ.webp\",\"\\/storage\\/listings\\/qh3qOrqe1rlZVGWx6uxDxPXHfgcUmmU7FUFqS14F.webp\"]', NULL, 2, 3, '380', 2000, 1, 'Fair', NULL, NULL, 0, 31, 'active', '2026-02-20 14:05:29', '2026-02-21 05:27:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_02_01_153740_create_listings_table', 1),
(5, '2026_02_01_153801_create_payments_table', 1),
(6, '2026_02_14_000001_create_bookings_table', 1),
(7, '2026_02_14_000002_create_reviews_table', 1),
(8, '2026_02_14_110053_add_year_built_to_listings_table', 2),
(9, '2026_02_14_110419_change_category_column_in_listings_table', 3),
(10, '2026_02_14_110630_add_details_to_listings_table', 4),
(11, '2026_02_14_113914_create_leads_table', 5),
(12, '2026_02_14_115302_create_reports_table', 6),
(13, '2026_02_14_120150_add_subscription_fields_to_users_table', 7),
(14, '2026_02_14_120202_add_video_to_listings_table', 7),
(15, '2026_02_14_122233_update_status_enum_in_users_table', 8),
(16, '2026_02_20_103015_make_transaction_id_nullable', 9),
(17, '2026_02_20_103530_add_subscription_to_payments_type', 9),
(18, '2026_02_20_130000_create_settings_table', 10),
(19, '2026_02_20_130010_add_trial_expires_at_to_users_table', 10),
(20, '2026_02_21_000100_add_public_id_to_listings_table', 11),
(21, '2026_02_21_000110_add_public_id_to_payments_table', 11),
(22, '2026_02_21_001000_add_geo_fields_to_listings_table', 12),
(23, '2026_02_21_053239_create_agents_table', 13),
(24, '2026_02_21_053402_add_agent_id_to_listings_table', 13);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('chisalaluckyk5@gmail.com', '$2y$12$6wcYxkbtM.1WAt4clJFKh.WZEtEFldd8FP2969TrLcSEGadlOK.Q6', '2026-02-14 09:55:03');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `public_id` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currency` enum('ZMW','USD') NOT NULL,
  `payment_method` varchar(191) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending',
  `type` enum('dealer_registration','promotion','subscription') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `public_id`, `user_id`, `amount`, `currency`, `payment_method`, `transaction_id`, `status`, `type`, `created_at`, `updated_at`) VALUES
(134, 'a1221186-7615-40c7-a2a4-360cc2e4a2d4', 3, 20.00, 'ZMW', 'airtel_money', 'temp_3f8d290f-9402-462e-bd2e-708119a9abae', 'pending', 'subscription', '2026-02-20 10:36:52', '2026-02-20 10:36:52'),
(135, '7f90d0df-32cc-44cf-b9c0-16a73f8c61d4', 3, 20.00, 'ZMW', 'airtel_money', 'temp_87d30057-7709-41b5-85cd-775e8063075b', 'pending', 'subscription', '2026-02-20 11:58:52', '2026-02-20 11:58:52'),
(136, '3c4f8675-265e-42c8-8152-675f72d5841b', 3, 20.00, 'ZMW', 'airtel_money', 'temp_7fedbd2b-3570-42f2-b4df-51443b752f4b', 'pending', 'subscription', '2026-02-20 12:04:34', '2026-02-20 12:04:34'),
(137, '6674b1ab-6584-44f2-a09a-15c686bfa7a4', 3, 20.00, 'ZMW', 'airtel_money', 'temp_098efbba-94f3-4b0e-9768-2aafae220fd8', 'pending', 'subscription', '2026-02-20 12:16:40', '2026-02-20 12:16:40'),
(138, 'b19fb294-0c52-4628-9c3d-dacda45a889d', 3, 20.00, 'ZMW', 'airtel_money', 'temp_f06b7646-a8f0-4bb5-95e0-19f6586d8e01', 'pending', 'subscription', '2026-02-20 12:21:34', '2026-02-20 12:21:34'),
(139, 'c086e187-533d-4305-ae2f-63fef1f4e6e5', 3, 20.00, 'ZMW', 'airtel_money', 'temp_c2ed913b-0f43-4d34-b116-db74be325b21', 'pending', 'subscription', '2026-02-20 12:24:27', '2026-02-20 12:24:27'),
(140, 'dacb9c38-7717-40ea-a2d7-b7e61ef2c3ae', 3, 20.00, 'ZMW', 'airtel_money', 'temp_51d0dbb7-5fd3-45e5-a09b-52b60d523605', 'pending', 'subscription', '2026-02-20 12:27:31', '2026-02-20 12:27:31'),
(141, '116aaa43-d9fd-4dea-876b-fbfb52be57f6', 3, 20.00, 'ZMW', 'airtel_money', 'temp_698aa93f-2c35-46ce-9f2b-0e645eaedd52', 'pending', 'subscription', '2026-02-20 12:30:10', '2026-02-20 12:30:10'),
(142, '5f166a06-dbea-4f03-a69f-c20f0d99b8c4', 3, 20.00, 'ZMW', 'airtel_money', 'temp_f3c9aa04-597c-4aa2-8eb7-f7fd535e5223', 'pending', 'subscription', '2026-02-20 12:35:33', '2026-02-20 12:35:33'),
(143, '091ea3eb-0fe4-4884-97fe-ed4f0053c226', 3, 20.00, 'ZMW', 'airtel_money', 'temp_693bd109-4ae1-4b5e-90cd-4d35a95f3abc', 'pending', 'subscription', '2026-02-20 12:35:41', '2026-02-20 12:35:41'),
(144, 'bd57dd09-e4fc-42bb-a123-9361fe80ee27', 3, 20.00, 'ZMW', 'airtel_money', 'temp_260c0ac0-544a-442c-a819-622f2261e61a', 'pending', 'subscription', '2026-02-20 12:45:44', '2026-02-20 12:45:44'),
(145, '1ee2c8da-893f-402a-b9ed-d6c4229bae8e', 3, 20.00, 'ZMW', 'airtel_money', 'temp_55e078e3-a514-4f9e-8eb0-a7b33649a126', 'pending', 'subscription', '2026-02-20 12:51:06', '2026-02-20 12:51:06'),
(146, 'c6c2caf2-5ca0-4bb3-927f-ab6c2a090612', 3, 20.00, 'ZMW', 'airtel_money', 'temp_bf78edac-e666-450b-b9a6-6ca177eb1673', 'pending', 'subscription', '2026-02-20 12:56:19', '2026-02-20 12:56:19'),
(147, 'a62d9e22-abf2-4e68-8330-f3631a4fdbc9', 3, 20.00, 'ZMW', 'airtel_money', 'temp_d23167a9-3fa7-47dc-8843-2721b8b4c4a4', 'pending', 'subscription', '2026-02-20 13:03:17', '2026-02-20 13:03:17'),
(148, '0862e764-c03d-447c-8514-aaa75ef1f626', 3, 20.00, 'ZMW', 'airtel_money', 'temp_b17ef98c-8d65-4a10-a0e9-2561608c30d0', 'pending', 'subscription', '2026-02-20 13:04:47', '2026-02-20 13:04:47'),
(149, '50656bdd-a895-409f-bafb-405885bd56bb', 3, 20.00, 'ZMW', 'airtel_money', 'temp_30b3277c-45a6-4c6b-ba51-d73890fdc86c', 'pending', 'subscription', '2026-02-20 13:07:23', '2026-02-20 13:07:23'),
(150, 'b021722d-8d21-4742-bcb7-194fa7451345', 3, 20.00, 'ZMW', 'mtn_money', 'temp_2d0f924d-f314-43f2-838c-76ce35ef65e8', 'pending', 'subscription', '2026-02-20 13:07:27', '2026-02-20 13:07:27'),
(151, '13d9859f-889c-41d0-9f9e-c15c995ac5d9', 3, 20.00, 'ZMW', 'airtel_money', 'temp_0e9fdaf9-30c3-482d-8277-8822c5826da5', 'pending', 'subscription', '2026-02-20 13:07:43', '2026-02-20 13:07:43'),
(152, '3f474515-31a7-4ace-9cc6-d28f7a5ad5ba', 3, 20.00, 'ZMW', 'airtel_money', 'temp_c0f950ae-32b7-4d03-8e26-627fde2210e9', 'pending', 'subscription', '2026-02-20 13:20:36', '2026-02-20 13:20:36'),
(153, '3f34a07a-c8aa-45f7-9a7b-e82e2692dffb', 3, 20.00, 'ZMW', 'airtel_money', 'temp_4950065f-bb6e-4771-8fa3-267c706c1c59', 'pending', 'subscription', '2026-02-20 13:25:38', '2026-02-20 13:25:38'),
(154, '19c66444-5791-47ac-8a6f-a6fb0990dc32', 3, 20.00, 'ZMW', 'airtel_money', 'temp_47f3ccbd-8c5d-4e89-9520-d4968ad540a6', 'pending', 'subscription', '2026-02-20 13:25:39', '2026-02-20 13:25:39'),
(155, '698a0d3f-2336-452c-9a29-3f6926dae6de', 3, 20.00, 'ZMW', 'airtel_money', 'temp_d015db6d-6e20-4930-8365-4bef1dbd7c0f', 'pending', 'subscription', '2026-02-20 13:30:46', '2026-02-20 13:30:46'),
(156, '0dd150d4-c14a-40c4-bc84-4dc202bf7af6', 3, 20.00, 'ZMW', 'airtel_money', 'temp_f2f2f746-96f8-45a2-a745-0708a28e7ab1', 'pending', 'subscription', '2026-02-20 13:33:50', '2026-02-20 13:33:50'),
(157, '31d91b00-1252-47f9-a008-711c792100ef', 3, 20.00, 'ZMW', 'airtel_money', 'temp_dc4ffefb-5a25-469d-ab2c-5e0c6e2c405c', 'pending', 'subscription', '2026-02-20 13:35:52', '2026-02-20 13:35:52'),
(158, '16390cb9-d9ef-4690-a621-7ed3af408574', 3, 20.00, 'ZMW', 'mtn_money', 'temp_f9205a8d-eb0c-4fc2-9438-13984a5996f2', 'pending', 'subscription', '2026-02-20 13:36:54', '2026-02-20 13:36:54'),
(159, 'de0c7489-c563-44d7-ae49-ba9303624e1d', 3, 20.00, 'ZMW', 'mtn_money', 'temp_05d815b5-1f5c-4e9d-9351-a53efa388fd3', 'pending', 'subscription', '2026-02-20 13:39:51', '2026-02-20 13:39:51'),
(160, 'a5330320-1a2c-4c8e-818a-18c0ee4f7421', 3, 20.00, 'ZMW', 'mtn_money', 'temp_7b7920d9-6277-4dd9-8c27-bbf13e6b6b7d', 'pending', 'subscription', '2026-02-20 13:39:53', '2026-02-20 13:39:53'),
(161, '977be27e-f4c6-4bd7-a311-c2f674af5016', 3, 20.00, 'ZMW', 'airtel_money', 'temp_40b72bd0-c076-4b5c-9261-226a9812ea2b', 'pending', 'subscription', '2026-02-20 13:41:33', '2026-02-20 13:41:33'),
(162, 'be5ebdf2-67f4-4fe9-9b85-4ebb7ddedb27', 3, 20.00, 'ZMW', 'airtel_money', 'temp_2810b035-83a4-4eb5-9114-7218e4c6c98e', 'pending', 'subscription', '2026-02-20 13:41:35', '2026-02-20 13:41:35'),
(163, '3e1a6ed7-4cd9-415e-baba-7277c280a7a7', 3, 20.00, 'ZMW', 'airtel_money', 'temp_873e171f-a42d-43ab-8c40-4eb59f2c6d12', 'pending', 'subscription', '2026-02-20 13:42:26', '2026-02-20 13:42:26'),
(164, '6ef8bef4-f29c-4183-8355-55fad2bc425b', 3, 20.00, 'ZMW', 'airtel_money', 'temp_1fe60ae2-d2b1-4156-9dff-9c42891e27e0', 'pending', 'subscription', '2026-02-20 13:52:43', '2026-02-20 13:52:43'),
(165, 'f5b4b142-c67e-43b8-bdb3-62ab09159702', 3, 20.00, 'ZMW', 'visa_mastercard', 'temp_d450f743-8478-443b-90bd-8d7cec33df7d', 'pending', 'subscription', '2026-02-20 13:52:51', '2026-02-20 13:52:51'),
(166, '7fbf6de9-df12-48df-acd7-48b0be20f108', 3, 20.00, 'ZMW', 'airtel_money', 'temp_c0b82570-56bd-4a6b-8581-fdfa605fc7fc', 'pending', 'subscription', '2026-02-20 13:53:32', '2026-02-20 13:53:32'),
(167, '84874b38-d267-4dff-a71f-2d0aaaa9dc3a', 3, 20.00, 'ZMW', 'airtel_money', 'temp_f32abd4b-b082-4c7f-8956-2e3aa14838f4', 'pending', 'subscription', '2026-02-20 14:10:40', '2026-02-20 14:10:40'),
(168, '7281b84e-7aa9-46e8-aa82-4693ca799fde', 36, 20.00, 'ZMW', 'airtel_money', 'payment_168_1771604780583', 'completed', 'subscription', '2026-02-20 14:26:04', '2026-02-20 14:27:06');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reporter_id` bigint(20) UNSIGNED NOT NULL,
  `reportable_type` varchar(191) NOT NULL,
  `reportable_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(191) NOT NULL DEFAULT 'General',
  `reason` text NOT NULL,
  `status` enum('pending','resolved','dismissed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `reporter_id`, `reportable_type`, `reportable_id`, `type`, `reason`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 'App\\Models\\Listing', 1, 'Fake Listing', 'its fake', 'pending', '2026-02-14 10:26:41', '2026-02-14 10:26:41');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `listing_id` bigint(20) UNSIGNED NOT NULL,
  `rating` int(10) UNSIGNED NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(191) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `country` varchar(191) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `color` varchar(191) DEFAULT NULL,
  `role` enum('admin','dealer','user') NOT NULL DEFAULT 'user',
  `subscription_plan` enum('basic','gold') NOT NULL DEFAULT 'basic',
  `subscription_expires_at` timestamp NULL DEFAULT NULL,
  `trial_expires_at` timestamp NULL DEFAULT NULL,
  `status` enum('active','pending','rejected','suspended') NOT NULL DEFAULT 'active',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `country`, `dob`, `color`, `role`, `subscription_plan`, `subscription_expires_at`, `trial_expires_at`, `status`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Vish Eraman', 'payghostwebservices@gmail.com', '+2600771355473', 'ZM', '2026-02-14', 'Blue', 'user', 'basic', NULL, NULL, 'active', NULL, '$2y$12$oenV7F2VQiTyw4D.sdd1A.Q..sKU0RXd51niCP7pYUJm6HmxpN0t2', NULL, '2026-02-14 07:35:20', '2026-02-14 07:35:20'),
(3, 'chisala luckaon', 'chisalaluckyk5@gmail.com', '+260chisalaluckyk5@gmail.com', 'ZM', '2002-09-18', 'Green', 'dealer', 'gold', '2026-03-20 08:32:24', NULL, 'active', NULL, '$2y$12$.4uLcIdeTXv5Un6gbzaeoOTrQW/BvtT0iMXuiypTcec3KaqABBeBG', 'CshgIB4p71myYHm8AZlmE2LeEJZjCAWj6EbqQ6Vmc0Aznv0Eua3PBcXJoHYX', '2026-02-14 08:47:19', '2026-02-20 08:32:24'),
(4, 'Vudo Admin', 'vudo@houseforrent.com', '+260970000000', 'Zambia', NULL, NULL, 'admin', 'basic', NULL, NULL, 'active', '2026-02-14 09:57:09', '$2y$12$rGVxb8DUU4LYab3Iud/iseSX4R.cMCdTOm2/aOyceXdbIbGGOFfLG', '6ZVMkkq80ofou6sp0Xx973sNFA2HN9Xnuo5UTtW1yjmPeIaEpLAm6BfhKChl', '2026-02-14 09:57:09', '2026-02-14 09:57:09'),
(5, 'Test User', 'test@example.com', NULL, NULL, NULL, NULL, 'user', 'basic', NULL, NULL, 'suspended', '2026-02-14 09:57:11', '$2y$12$MKiMp2eOTFCGBa2xhGFjru1dzQ8.bmLnQdMLn2.2PUTUZ5Tsfgrwa', 'lmxDbeqEcA', '2026-02-14 09:57:11', '2026-02-14 10:24:10'),
(6, 'Flo Bogan', 'lizeth.mclaughlin@example.org', NULL, NULL, NULL, NULL, 'dealer', 'gold', '2026-03-14 10:07:49', NULL, 'active', '2026-02-14 10:07:49', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'IrnFTgmL5d', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(7, 'Nels Mayer', 'danielle.kuhic@example.org', NULL, NULL, NULL, NULL, 'dealer', 'gold', '2026-03-14 10:07:49', NULL, 'active', '2026-02-14 10:07:49', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 't3UZ05xIKX', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(8, 'Krystal Jacobson', 'ryan.joel@example.net', NULL, NULL, NULL, NULL, 'dealer', 'gold', '2026-03-14 10:07:49', NULL, 'active', '2026-02-14 10:07:49', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'tJhc8TyVs4', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(9, 'Carmen Vandervort', 'keshawn98@example.com', NULL, NULL, NULL, NULL, 'dealer', 'gold', '2026-03-14 10:07:49', NULL, 'active', '2026-02-14 10:07:49', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'vUAk2iO4Zc', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(10, 'Christophe Powlowski', 'goodwin.whitney@example.com', NULL, NULL, NULL, NULL, 'dealer', 'gold', '2026-03-14 10:07:49', NULL, 'active', '2026-02-14 10:07:49', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'XsJIAVBxyg', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(11, 'Zora Hickle', 'nquigley@example.org', NULL, NULL, NULL, NULL, 'dealer', 'gold', '2026-03-14 10:07:49', NULL, 'active', '2026-02-14 10:07:49', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', '9gj1rSbnpX', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(12, 'Gayle Feest', 'farrell.zoila@example.com', NULL, NULL, NULL, NULL, 'dealer', 'gold', '2026-03-14 10:07:49', NULL, 'suspended', '2026-02-14 10:07:49', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'JInA4GkEk7', '2026-02-14 10:07:50', '2026-02-14 10:23:58'),
(13, 'Dr. Elza Schamberger DDS', 'wilkinson.roselyn@example.net', NULL, NULL, NULL, NULL, 'dealer', 'gold', '2026-03-14 10:07:49', NULL, 'active', '2026-02-14 10:07:49', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'mnd1za4tof', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(14, 'Dr. Charity Witting III', 'lschimmel@example.org', NULL, NULL, NULL, NULL, 'dealer', 'gold', '2026-03-14 10:07:49', NULL, 'active', '2026-02-14 10:07:49', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'BBJr6hDIh0', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(15, 'Vicenta Williamson', 'camren85@example.org', NULL, NULL, NULL, NULL, 'dealer', 'gold', '2026-03-14 10:07:49', NULL, 'active', '2026-02-14 10:07:49', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'gEToumAfiH', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(16, 'Mr. Ethan Fahey', 'stehr.eula@example.com', NULL, NULL, NULL, NULL, 'dealer', 'gold', '2026-03-14 10:07:49', NULL, 'active', '2026-02-14 10:07:49', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'AfaIV2sgp1', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(17, 'Nona Wisoky', 'yvonrueden@example.com', NULL, NULL, NULL, NULL, 'dealer', 'gold', '2026-03-14 10:07:49', NULL, 'active', '2026-02-14 10:07:49', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', '9Y7q7kX51K', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(18, 'Wayne Rice', 'jbraun@example.net', NULL, NULL, NULL, NULL, 'dealer', 'gold', '2026-03-14 10:07:49', NULL, 'active', '2026-02-14 10:07:49', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'yWov9PUebY', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(19, 'Dr. Watson Schaden I', 'awiegand@example.net', NULL, NULL, NULL, NULL, 'dealer', 'gold', '2026-03-14 10:07:49', NULL, 'active', '2026-02-14 10:07:49', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'ibNhLyY9B7', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(20, 'Amari Balistreri', 'nshields@example.com', NULL, NULL, NULL, NULL, 'dealer', 'gold', '2026-03-14 10:07:49', NULL, 'active', '2026-02-14 10:07:49', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'sNIyC3G8b0', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(21, 'Connor Krajcik', 'kilback.felicita@example.com', NULL, NULL, NULL, NULL, 'dealer', 'gold', '2026-03-14 10:07:49', NULL, 'active', '2026-02-14 10:07:49', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', '3tjdxBNiyV', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(22, 'Christina Beer', 'ischroeder@example.net', NULL, NULL, NULL, NULL, 'dealer', 'gold', '2026-03-14 10:07:49', NULL, 'active', '2026-02-14 10:07:49', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'WBUo2zTYhj', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(23, 'Jon Littel', 'zlesch@example.net', NULL, NULL, NULL, NULL, 'dealer', 'gold', '2026-03-14 10:07:49', NULL, 'active', '2026-02-14 10:07:49', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'xJfNUhHgfL', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(24, 'Darius Hintz', 'vita.upton@example.org', NULL, NULL, NULL, NULL, 'dealer', 'gold', '2026-03-14 10:07:49', NULL, 'active', '2026-02-14 10:07:49', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', '9x14pH548j', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(25, 'Jarret Swaniawski', 'dalton70@example.org', NULL, NULL, NULL, NULL, 'dealer', 'gold', '2026-03-14 10:07:49', NULL, 'active', '2026-02-14 10:07:49', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'mJS4J4tAkV', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(26, 'Pat Ferry', 'nitzsche.tommie@example.com', NULL, NULL, NULL, NULL, 'dealer', 'basic', NULL, NULL, 'active', '2026-02-14 10:07:50', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'TqkHOAmqMY', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(27, 'Abigale Hackett I', 'larson.armando@example.net', NULL, NULL, NULL, NULL, 'dealer', 'basic', NULL, NULL, 'active', '2026-02-14 10:07:50', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'MnhTTH05qJ', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(28, 'Juvenal Bayer I', 'conrad67@example.net', NULL, NULL, NULL, NULL, 'dealer', 'basic', NULL, NULL, 'active', '2026-02-14 10:07:50', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'szeKVhJmnb', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(29, 'Mrs. Liana Casper II', 'ondricka.garrison@example.org', NULL, NULL, NULL, NULL, 'dealer', 'basic', NULL, NULL, 'active', '2026-02-14 10:07:50', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'cnL1TQ7Iy9', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(30, 'Alexie Hyatt', 'doris.abshire@example.com', NULL, NULL, NULL, NULL, 'dealer', 'basic', NULL, NULL, 'active', '2026-02-14 10:07:50', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'NZ7axczVqz', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(31, 'Salvador Lynch', 'konopelski.freeda@example.org', NULL, NULL, NULL, NULL, 'dealer', 'basic', NULL, NULL, 'active', '2026-02-14 10:07:50', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'kuQFKetXtb', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(32, 'Dawson Pollich', 'raphael77@example.com', NULL, NULL, NULL, NULL, 'dealer', 'basic', NULL, NULL, 'active', '2026-02-14 10:07:50', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'H4HHB1O2Yp', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(33, 'Santiago Stoltenberg', 'wschultz@example.com', NULL, NULL, NULL, NULL, 'dealer', 'basic', NULL, NULL, 'active', '2026-02-14 10:07:50', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'gjZgRXkecO', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(34, 'Melba Bradtke V', 'zsimonis@example.org', NULL, NULL, NULL, NULL, 'dealer', 'basic', NULL, NULL, 'active', '2026-02-14 10:07:50', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'yFNlFwPYZk', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(35, 'Margarett Krajcik', 'jgusikowski@example.net', NULL, NULL, NULL, NULL, 'dealer', 'basic', NULL, NULL, 'active', '2026-02-14 10:07:50', '$2y$12$VTM5MyKlG0j1mwLaHjn7a.X7jY1C/qlXnADM/CQSFJdsgUvjrJQTm', 'uNsre0roCv', '2026-02-14 10:07:50', '2026-02-14 10:07:50'),
(36, 'johen', 'kiyo@gmail.com', '+2600771355474', 'ZM', '2010-12-20', 'Green', 'dealer', 'gold', '2026-03-20 14:27:06', NULL, 'active', NULL, '$2y$12$574sa/ebuYy1ykVZaUnfNuzHLIENzwurwWDz5ilaru9lJTithiYuG', NULL, '2026-02-20 14:21:02', '2026-02-20 14:27:06'),
(37, 'jjj', 'chisalaluckson70@gmail.com', '+2600770812506', 'ZM', '2000-02-20', 'Green', 'dealer', 'basic', NULL, '2026-03-20 15:42:24', 'active', NULL, '$2y$12$VCGAlQCSp8y4UgYdAU.Ql.IjmftqjmkuHat8wPh2vn4fJphaEaNx6', NULL, '2026-02-20 15:42:24', '2026-02-20 15:42:24'),
(38, 'jjj', 'chisalaluckson27@gmail.com', '+2600770812506', 'ZM', '2000-02-20', NULL, 'dealer', 'basic', NULL, '2026-03-20 15:44:52', 'active', NULL, '$2y$12$MfgbQBfgsbp3KFVdsVnsteJSVJpkPaaNNTkwZXmgqe2PXY63i/.MO', NULL, '2026-02-20 15:44:52', '2026-02-20 15:44:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agents_user_id_foreign` (`user_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_user_id_foreign` (`user_id`),
  ADD KEY `bookings_listing_id_foreign` (`listing_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

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
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leads_listing_id_foreign` (`listing_id`),
  ADD KEY `leads_user_id_foreign` (`user_id`);

--
-- Indexes for table `listings`
--
ALTER TABLE `listings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `listings_public_id_unique` (`public_id`),
  ADD KEY `listings_user_id_foreign` (`user_id`),
  ADD KEY `listings_agent_id_foreign` (`agent_id`);

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
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_transaction_id_unique` (`transaction_id`),
  ADD UNIQUE KEY `payments_public_id_unique` (`public_id`),
  ADD KEY `payments_user_id_foreign` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reports_reporter_id_foreign` (`reporter_id`),
  ADD KEY `reports_reportable_type_reportable_id_index` (`reportable_type`,`reportable_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_user_id_foreign` (`user_id`),
  ADD KEY `reviews_listing_id_foreign` (`listing_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agents`
--
ALTER TABLE `agents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
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
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `listings`
--
ALTER TABLE `listings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `agents`
--
ALTER TABLE `agents`
  ADD CONSTRAINT `agents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_listing_id_foreign` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `leads_listing_id_foreign` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `listings`
--
ALTER TABLE `listings`
  ADD CONSTRAINT `listings_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `listings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_reporter_id_foreign` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_listing_id_foreign` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
