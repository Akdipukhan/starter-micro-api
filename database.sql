-- Radio FM Cumilla Database Schema
-- Compatible with MySQL 8.x

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+06:00"; -- Bangladesh Time

CREATE DATABASE IF NOT EXISTS `radio_fm_cumilla` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `radio_fm_cumilla`;

-- 1. Users Table (Admins, Editors, RJs)
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','editor','rj') DEFAULT 'editor',
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Admin (Password: admin123 - CHANGE IMMEDIATELY)
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Super Admin', 'admin@radiofmcumilla.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- 2. Categories Table
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_bn` varchar(100) NOT NULL,
  `name_en` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL UNIQUE,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`name_bn`, `name_en`, `slug`) VALUES
('জাতীয়', 'National', 'national'),
('আন্তর্জাতিক', 'International', 'international'),
('রাজনীতি', 'Politics', 'politics'),
('খেলাধুলা', 'Sports', 'sports'),
('বিনোদন', 'Entertainment', 'entertainment'),
('প্রযুক্তি', 'Technology', 'technology'),
('কুমিল্লা', 'Cumilla', 'cumilla');

-- 3. Posts (News) Table
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title_bn` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL UNIQUE,
  `content_bn` text NOT NULL,
  `content_en` text NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `status` enum('draft','published','scheduled') DEFAULT 'draft',
  `is_breaking` tinyint(1) DEFAULT 0,
  `views` int(11) DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `author_id` (`author_id`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Comments Table
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `comment` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Radio Programs Table
CREATE TABLE `radio_programs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `audio_file` varchar(255) DEFAULT NULL,
  `stream_url` varchar(255) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `is_live` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `radio_programs_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Site Settings Table
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_title` varchar(100) DEFAULT 'Radio FM Cumilla',
  `site_logo` varchar(255) DEFAULT NULL,
  `stream_url` varchar(255) DEFAULT '',
  `facebook` varchar(255) DEFAULT '#',
  `twitter` varchar(255) DEFAULT '#',
  `youtube` varchar(255) DEFAULT '#',
  `contact_phone` varchar(20) DEFAULT '+8801XXX',
  `contact_email` varchar(100) DEFAULT 'info@radiofmcumilla.com',
  `address` text DEFAULT 'Cumilla, Bangladesh',
  `footer_text` text DEFAULT '© 2024 Radio FM Cumilla. All rights reserved.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`site_title`, `stream_url`) VALUES ('Radio FM Cumilla', 'https://stream.zeno.fm/your_stream_key_here');

COMMIT;
