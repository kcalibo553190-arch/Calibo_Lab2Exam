-- ============================================================
-- SQL Dump for XAMPP phpMyAdmin
-- Database Name: `laravel_crud`
-- ============================================================

CREATE DATABASE IF NOT EXISTS `laravel_crud` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `laravel_crud`;

-- Table structure for `items`
DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample starter data
INSERT INTO `items` (`id`, `name`, `description`, `price`, `created_at`, `updated_at`) VALUES
(1, 'Dell UltraSharp Monitor 27"', '4K UHD IPS display with USB-C hub connectivity', 349.99, NOW(), NOW()),
(2, 'Logitech MX Master 3S', 'Ergonomic wireless mouse with quiet clicks and 8K DPI sensor', 99.00, NOW(), NOW()),
(3, 'Keychron Q1 Pro Keyboard', 'Custom mechanical keyboard with wireless bluetooth and RGB backlighting', 189.50, NOW(), NOW());
