-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Jul 11, 2023 at 07:41 AM
-- Server version: 5.7.22
-- PHP Version: 8.0.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jobtron`
--

-- --------------------------------------------------------

--
-- Table structure for table `domains`
--

CREATE TABLE `domains` (
  `id` int(10) UNSIGNED NOT NULL,
  `domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenant_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `domains`
--

INSERT INTO `domains` (`id`, `domain`, `tenant_id`, `created_at`, `updated_at`) VALUES
(5, 'bp', 'bp', '2022-05-27 11:52:13', '2022-05-27 11:52:13'),
(6, 'test', 'bp', '2022-05-27 11:52:13', '2022-05-27 11:52:13'),
(21, 'admin', 'admin', '2022-12-07 12:10:57', '2022-12-07 12:10:57'),
(30, 'ky6lku', 'ky6lku', '2023-02-07 12:17:16', '2023-02-07 12:17:16');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `manager_has_owner`
--

CREATE TABLE `manager_has_owner` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `manager_id` bigint(20) UNSIGNED NOT NULL,
  `owner_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `manager_has_owner`
--

INSERT INTO `manager_has_owner` (`id`, `manager_id`, `owner_id`, `created_at`, `updated_at`) VALUES
(6, 8, 38, '2023-03-07 08:42:23', '2023-03-07 08:42:23'),
(7, 8, 1, '2023-03-07 08:42:37', '2023-03-07 08:42:37');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2022_05_19_164107_create_check_lists_table', 1),
(2, '2022_05_24_031026_create_check_users_table', 1),
(3, '2022_05_25_064934_create_check_reports_table', 1),
(4, '2022_05_29_121046_create_tenant_user_table', 2),
(5, '2022_05_29_123629_add_global_id_to_tenants', 2),
(6, '2022_11_30_071802_alter_users.php', 3),
(7, '2022_12_29_190613_create_tenant_pivot_table', 4);

-- --------------------------------------------------------

--
-- Table structure for table `portals`
--

CREATE TABLE `portals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_id` bigint(20) UNSIGNED NOT NULL,
  `currency` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kzt',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `main_page_video` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `main_page_video_show_days_amount` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `kpi_backlight` json DEFAULT NULL COMMENT 'Цвет ячеек kpi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `portals`
--

INSERT INTO `portals` (`id`, `tenant_id`, `owner_id`, `currency`, `created_at`, `updated_at`, `main_page_video`, `main_page_video_show_days_amount`, `kpi_backlight`) VALUES
(1, 'bp', 1, 'kzt', '2019-03-24 06:49:00', '2023-04-04 05:49:43', NULL, 0, NULL),
(2, '62mbs8', 29, 'kzt', '2023-03-24 06:49:00', '2023-03-24 06:49:00', NULL, 0, NULL),
(3, 'arq5xa', 30, 'kzt', '2023-03-24 06:49:00', '2023-03-24 06:49:00', NULL, 0, NULL),
(4, '4uyld3', 31, 'kzt', '2023-03-24 06:49:00', '2023-03-24 06:49:00', NULL, 0, NULL),
(5, '3hj21t', 32, 'kzt', '2023-03-24 06:49:00', '2023-03-24 06:49:00', NULL, 0, NULL),
(6, 'w85iax', 33, 'kzt', '2023-03-24 06:49:00', '2023-03-24 06:49:00', NULL, 0, NULL),
(7, 'mw2xr2', 34, 'kzt', '2023-03-24 06:49:00', '2023-03-24 06:49:00', NULL, 0, NULL),
(8, 'h6r8zr', 35, 'kzt', '2023-03-24 06:49:00', '2023-03-24 06:49:00', NULL, 0, NULL),
(9, 'tcep78', 36, 'kzt', '2023-03-24 06:49:00', '2023-03-24 06:49:00', NULL, 0, NULL),
(10, 'ruybd7', 1, 'kzt', '2023-03-24 06:49:00', '2023-03-24 06:49:00', NULL, 0, NULL),
(11, 'ky6lku', 38, 'kzt', '2023-03-24 06:49:00', '2023-03-24 06:49:00', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `profile_groups`
--

CREATE TABLE `profile_groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `users` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `work_start` time DEFAULT NULL,
  `work_end` time DEFAULT NULL,
  `workdays` tinyint(3) UNSIGNED NOT NULL DEFAULT '6',
  `editors_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `book_groups` text COLLATE utf8mb4_unicode_ci,
  `required` int(11) NOT NULL DEFAULT '0',
  `provided` int(11) NOT NULL DEFAULT '0',
  `head_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '[]',
  `zoom_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bp_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `corp_books` text COLLATE utf8mb4_unicode_ci,
  `checktime` timestamp NULL DEFAULT NULL,
  `checktime_users` text COLLATE utf8mb4_unicode_ci,
  `payment_terms` text COLLATE utf8mb4_unicode_ci,
  `salary_approved` int(11) DEFAULT '0',
  `salary_approved_by` int(11) DEFAULT '0',
  `salary_approved_date` timestamp NULL DEFAULT NULL,
  `has_analytics` tinyint(4) NOT NULL DEFAULT '0',
  `quality` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ucalls',
  `editable_time` tinyint(4) NOT NULL DEFAULT '0',
  `time_address` int(11) NOT NULL DEFAULT '0',
  `time_exceptions` text COLLATE utf8mb4_unicode_ci,
  `paid_internship` tinyint(4) NOT NULL DEFAULT '0',
  `rentability_max` int(11) NOT NULL DEFAULT '120',
  `show_payment_terms` tinyint(4) NOT NULL DEFAULT '1',
  `archived_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `telescope_entries`
--

CREATE TABLE `telescope_entries` (
  `sequence` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `family_hash` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `should_display_on_index` tinyint(1) NOT NULL DEFAULT '1',
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `telescope_entries_tags`
--

CREATE TABLE `telescope_entries_tags` (
  `entry_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `telescope_monitoring`
--

CREATE TABLE `telescope_monitoring` (
  `tag` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `global_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tenants`
--

INSERT INTO `tenants` (`id`, `created_at`, `updated_at`, `data`, `global_id`) VALUES
('admin', '2022-12-07 12:10:29', '2022-12-07 12:10:29', '{\"tenancy_db_name\":\"tenantadmin\"}', 0),
('bp', '2022-05-27 11:49:57', '2022-05-27 11:49:57', '{\"tenancy_db_name\":\"tenantbp\"}', 1),
('ky6lku', '2023-02-07 11:47:46', '2023-02-07 11:47:46', '{\"tenancy_db_name\":\"tenantky6lku\"}', 0),
('test', '2022-05-27 11:49:57', '2022-05-27 11:49:57', '{\"tenancy_db_name\":\"tenanttest\"}', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tenant_pivot`
--

CREATE TABLE `tenant_pivot` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `owner` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tenant_pivot`
--

INSERT INTO `tenant_pivot` (`id`, `tenant_id`, `user_id`, `owner`) VALUES
(1, 'bp', 37, 0),
(2, 'admin', 39, 0),
(3, 'admin', 40, 0),
(4, 'admin', 41, 0),
(5, 'admin', 42, 0),
(6, 'bp', 1, 0),
(7, 'bp', 38, 0),
(8, 'bp', 39, 0),
(9, 'bp', 40, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tenant_user`
--

CREATE TABLE `tenant_user` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tenant_user`
--

INSERT INTO `tenant_user` (`id`, `tenant_id`, `user_id`) VALUES
(3, 'bp', 1),
(14, '62mbs8', 29),
(15, 'arq5xa', 30),
(16, '4uyld3', 31),
(17, '3hj21t', 32),
(18, 'w85iax', 33),
(19, 'mw2xr2', 34),
(20, 'h6r8zr', 35),
(21, 'tcep78', 36),
(22, 'ruybd7', 1),
(23, 'ky6lku', 38);

-- --------------------------------------------------------

--
-- Table structure for table `tenant_user_impersonation_tokens`
--

CREATE TABLE `tenant_user_impersonation_tokens` (
  `token` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenant_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auth_guard` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect_url` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tenant_user_impersonation_tokens`
--

INSERT INTO `tenant_user_impersonation_tokens` (`token`, `tenant_id`, `user_id`, `auth_guard`, `redirect_url`, `created_at`) VALUES
('08OnPy3iFa6ro1F4vUongkhdZsaqI2YFkS0GqeN1Tag4dtzMlhYSeU0n7qwqNjze52xNdgh6CZA8zAUXNQSJqnFkXY3nXGq14mOGa17vZg55IBEniiklzmSoGmdv46ku', 'uozkch', '1', 'web', '/profile', '2022-12-01 13:53:40'),
('18jh0oe5LsTVzx5ZJALxwwPGCxnSLi3VDJns0UePQAchIUau6HDxF4J6XtXiQt6qArLZA3EUJ1k1HMP4ySjEbAf26lVAbI0NQWWJSH31E0DU0VNuZ7YB2n4LTr58Q2Ey', '4uyld3', '1', 'web', '/profile', '2022-12-08 10:17:49'),
('5nW5o5WNBb7CFm6O2jOIe7KL7PJJviJ0bitH6cquGTLllYrssSVHYFDZ24ObLZAx3fm4TqUP0VCxpHAVC7NxzH6PXBsVnmTgtgaTU0eRQgSecm16ONDKEoDySqY5AB8C', 'p9bthm', '1', 'web', '/profile', '2022-12-01 13:20:28'),
('7SAARBGAUrB7PkZDPqX10Ql2MzXpUDfNdNXjw4ripiWkdR3Jb4FRFn0blHK3I9OLywAjlHu2KixzkDjPb1u78PwIgYxBJU7T8ycXM50Gc2BtMQBhYvYhZxXHaMS8MoOv', 'h6r8zr', '1', 'web', '/profile', '2022-12-08 11:04:29'),
('861GTKnjY6VBZe70GrrFcRegeTzBxa0DIno8BHTJKXaRBX0ZBD7jPTBwTQewfQMkLev5kyvcQc5vR3MyyGRKOWyk0qRspvrpa6rZLvibOP71n7zRaAUtUyFlcFgI3HCK', 'bp', '1', '', '/', '2022-11-27 19:16:11'),
('erwikv9ID17H7aDejeI2Kfz4yVpDMuvYIIW0WbwnzMaGb9j9Zd8YHSYusfmITzhtR7TgGrHq5OUanK5Cpq84tdbhwQwDV15bK5S9QLyOLM18kgn4hweVWjeKx06KigZo', 'w85iax', '1', 'web', '/profile', '2022-12-08 10:37:42'),
('fFX0OxDKeBLYUp9NOqNbuunSOXeSvv8VJSHz06Ht7EB4ncEOETeVQVTgMQe5NusLGWCcv0RtNwmOtbsjwJf7TKK9hmjFByubo3IXPeovgboZy73STXpdVBQHCtR7PnUC', 'mw2xr2', '1', 'web', '/profile', '2022-12-08 10:41:16'),
('HFdJpuwGE7CjCc4ulraG7ucF7HNZGFmp0YQ3e72HBlgtZ4KHiBuyRXwALOchs4WfcPqHqhn76yaFHMyk2ZXAMt7fCSqY14ADipre5RfW0nv6M6NjZxXVoU2xYY2kytw3', 'jbpyo1', '1', 'web', '/profile', '2022-12-01 13:55:21'),
('ixPvht1Duu8iqIDZM4u1cBqUw1BvTOgbcvNiCApknk1r1M8oD5mIsvCpkWzjmepYE3x9J6JRQuNKNOVNB1Dxu8O8EwtwrSmXuF96HhKraiGehbBVrvW8t72Yg5pbp0UH', 'oUaIFow7byqx', '1', 'web', '/profile', '2022-11-28 05:04:25'),
('L94qBHebKqzZfCILd2krhmEoGHb7qpxn69IglYLZQPIE3DxcR0RkPgQBQH3wcRhJA8AWpaQd0xvQyNo0zHE6GRrJRm1dUHRi3tqNFP9VwjBtf005keskAwEZVK9xQyT7', 'ruybd7', '1', 'web', '/profile', '2022-12-08 12:22:18'),
('lu5k6TSufIXuDQKMBPU8qN0DOqtkLgtr6i6FkJRXyNTFc4qhN7YLw2IXOiYbSMqizkGXtVwZGWuV672uxB0SBXIjo5rn3Wu5kqGGCedLIyFzqaTofGB7nmjkGRd4BLtL', 'arq5xa', '1', 'web', '/profile', '2022-12-08 10:15:47'),
('NcTt91R0iqWvVE46oPINDb3C34wDdgGg9molKaXR4PVfy1ENDjupg1FQoA9Iv4OADhIRl79uN5LfoGHf2jygU2wsDGlOQBxtfvS2zQeOohy2NjmL3EzW2w9e6gfLDCJE', 'tcep78', '1', 'web', '/profile', '2022-12-08 11:12:08'),
('uaHMOrCwV9EfT7jMaWutVNuZbyuiBOGtuHOXEe4pTPNTV1MU75lxcR6q1HFxxogzwsOD4Nr2npMaCdv6uwxyU2gw3veubB9C1n8gxIzCyE0WYDYRJERf0nbHZoaneUXa', '3hj21t', '1', 'web', '/profile', '2022-12-08 10:29:14'),
('xBvcSihUZXZg42hefBGRaZjriOgXTI4AL5bTpPJ0h73aGTLvhIbiTbeE4CO3ddVYtYqT8eTwkZKmVOk3nctg3IJqRKjv8xHF3TJTbT1Pk3225Cw5NslUbhALMkN1zn8B', '62mbs8', '1', 'web', '/profile', '2022-12-07 12:10:57');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthday` timestamp NULL DEFAULT NULL,
  `login_at` timestamp NULL DEFAULT NULL,
  `city` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lead` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `balance` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `phone`, `name`, `last_name`, `password`, `remember_token`, `birthday`, `login_at`, `city`, `lead`, `country`, `balance`, `created_at`, `updated_at`, `deleted_at`, `currency`) VALUES
(1, 'ali.akpanov@yandex.ru', '870778202000', 'Али', 'Акпанов', '$2y$10$rhmLIHmpg4ZPd3pb0T3VCOT7bmFlpruwu6Znbv4vdw2GFnx617kzm', NULL, '1993-12-12 00:00:00', '2022-11-27 17:36:16', NULL, 'https://infinitys.bitrix24.kz/crm/lead/details/562872', 'Страна:  Казахстана Город:  Шымкент', 100, '2022-11-27 17:36:16', '2023-04-12 03:13:57', NULL, ''),
(29, 'test@mail.ru', '01234567890', 'test', 'test', '$2y$10$zTRCPtzx/aer5xalYKdpR.rwUqRTO9qOmJr1P2Xic12my/sZmuUcO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-07 12:10:29', '2022-12-07 12:10:29', NULL, ''),
(30, 'test1@mail.ru', '01234567891', '1', '1', '$2y$10$tTuk0b1wXo39K9AvSBBVs.CMsy6o0BnJACmwAtDD7qx4BZSWlObe.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-08 10:15:17', '2022-12-08 10:15:17', NULL, ''),
(31, 'test2@mail.ru', '01234567892', '1', '1', '$2y$10$hvmyU.l9czkz5dV50rKFpeIkhwBvdeL.56enIR5HciXYwUSEV/Y9q', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-08 10:17:23', '2022-12-08 10:17:23', NULL, ''),
(32, 'test3@mail.ru', '01234567893', '1', '1', '$2y$10$infR3waFp6jWVQvFd1KWBeAS0W7Fyb.7KIc2yfDNqzvUwct7RO/EK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-08 10:28:49', '2022-12-08 10:28:49', NULL, 'rub'),
(33, 'test4@mail.ru', '01234567894', '1', '1', '$2y$10$ZjvfUvbmsxP22CXCPrBql.lusmTEk7u8AIP2.ZlsAqnCDKspDib2y', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-08 10:37:14', '2022-12-08 10:37:14', NULL, 'usd'),
(34, 'test5@mail.ru', '01234567895', '1', '1', '$2y$10$rgwc4uNBH3BwE9DUnEKGDOjnsOL81xyB5r8uQje1MdJN48GBFR01G', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-08 10:40:49', '2022-12-08 10:40:49', NULL, 'usd'),
(35, 'test6@mail.ru', '01234567896', '1', '1', '$2y$10$ecVzS7oIpnZpSNnCWN9F5uJchCro/VsDPc9Dpa1MbMjQIXjjjzsze', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-08 11:04:04', '2022-12-08 11:04:04', NULL, 'usd'),
(36, 'test8@mail.ru', '01234567898', '1', '1', '$2y$10$PZp8hcEpiKjJ2kAz1.mMnOdfbxtnq0GA3djVb/N4C.95TRcnGou4G', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-08 11:11:42', '2022-12-08 11:11:42', NULL, 'usd'),
(37, 'user625548@bpartners.kz', '77751575668', 'Султан', 'Жанылхан', '$2y$10$PXu0z7JsFIaxB5Ek82CtIeFO/JW.e0hGm.DJicchv0cU6PO84u1iK', NULL, '2022-12-25 00:00:00', NULL, NULL, NULL, 'казахстан', NULL, '2023-01-20 03:05:54', '2023-01-20 03:05:54', NULL, ''),
(38, 'test@ya.ru', '01234567890', 'test', 'test', '$2y$10$rhmLIHmpg4ZPd3pb0T3VCOT7bmFlpruwu6Znbv4vdw2GFnx617kzm', NULL, NULL, '2023-02-07 11:58:34', NULL, NULL, 'казахстан', 100, '2023-02-07 11:58:34', '2023-02-07 11:58:34', NULL, 'usd'),
(39, 'ariche.a@yandex.ru', '87077753155', 'Адиль', 'Каримов', '$2y$10$JTWGEwJkCRJoXtnrYzYmbO7YOQtrlrdCpxpV4c1RvWMkLYir/F3qy', NULL, NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, 'KZT'),
(40, 'tashmetov03@mail.ru', '', 'Руслан', '', '$2y$10$rhmLIHmpg4ZPd3pb0T3VCOT7bmFlpruwu6Znbv4vdw2GFnx617kzm', NULL, NULL, NULL, '', NULL, '', NULL, NULL, NULL, NULL, 'KZT'),
(41, '', '', '', '', '$2y$10$YZS/ZM4yRUJ/BmGYaoE4b.HhbkNY1BLkUGW3pzk67XDflTQQxPHJe', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, 'KZT'),
(42, 'ters@fsd.ftsde', '1231541515', 'нкывн', 'нкыва', '$2y$10$9OEUxVFfeNAlLf/3OiXDrunWv.Kh.NnZAnsdX/u.9YRMApekMq/4m', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, 'KZT');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `domains`
--
ALTER TABLE `domains`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `domains_domain_unique` (`domain`),
  ADD KEY `domains_tenant_id_foreign` (`tenant_id`);

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
-- Indexes for table `manager_has_owner`
--
ALTER TABLE `manager_has_owner`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `manager_has_owner_owner_id_unique` (`owner_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `portals`
--
ALTER TABLE `portals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profile_groups`
--
ALTER TABLE `profile_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `telescope_entries`
--
ALTER TABLE `telescope_entries`
  ADD PRIMARY KEY (`sequence`),
  ADD UNIQUE KEY `telescope_entries_uuid_unique` (`uuid`),
  ADD KEY `telescope_entries_batch_id_index` (`batch_id`),
  ADD KEY `telescope_entries_family_hash_index` (`family_hash`),
  ADD KEY `telescope_entries_created_at_index` (`created_at`),
  ADD KEY `telescope_entries_type_should_display_on_index_index` (`type`,`should_display_on_index`);

--
-- Indexes for table `telescope_entries_tags`
--
ALTER TABLE `telescope_entries_tags`
  ADD KEY `telescope_entries_tags_entry_uuid_tag_index` (`entry_uuid`,`tag`),
  ADD KEY `telescope_entries_tags_tag_index` (`tag`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tenant_pivot`
--
ALTER TABLE `tenant_pivot`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tenant_user`
--
ALTER TABLE `tenant_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tenant_user_impersonation_tokens`
--
ALTER TABLE `tenant_user_impersonation_tokens`
  ADD PRIMARY KEY (`token`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD UNIQUE KEY `users_pk` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `domains`
--
ALTER TABLE `domains`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

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
-- AUTO_INCREMENT for table `manager_has_owner`
--
ALTER TABLE `manager_has_owner`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `portals`
--
ALTER TABLE `portals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `profile_groups`
--
ALTER TABLE `profile_groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `telescope_entries`
--
ALTER TABLE `telescope_entries`
  MODIFY `sequence` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenant_pivot`
--
ALTER TABLE `tenant_pivot`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tenant_user`
--
ALTER TABLE `tenant_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `domains`
--
ALTER TABLE `domains`
  ADD CONSTRAINT `domains_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `telescope_entries_tags`
--
ALTER TABLE `telescope_entries_tags`
  ADD CONSTRAINT `telescope_entries_tags_entry_uuid_foreign` FOREIGN KEY (`entry_uuid`) REFERENCES `telescope_entries` (`uuid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
