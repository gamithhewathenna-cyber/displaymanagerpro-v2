-- Blog Articles Migration
-- Run this after the main schema.sql has been applied

CREATE TABLE IF NOT EXISTS `blog_articles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `excerpt` VARCHAR(600) NULL DEFAULT NULL,
  `body` LONGTEXT NOT NULL,
  `featured_image_url` VARCHAR(500) NULL DEFAULT NULL,
  `category` VARCHAR(100) NULL DEFAULT NULL,
  `author_name` VARCHAR(100) NULL DEFAULT NULL,
  `status` ENUM('draft','published') NOT NULL DEFAULT 'draft',
  `published_at` TIMESTAMP NULL DEFAULT NULL,
  `seo_title` VARCHAR(255) NULL DEFAULT NULL,
  `seo_description` VARCHAR(500) NULL DEFAULT NULL,
  `focus_keyword` VARCHAR(100) NULL DEFAULT NULL,
  `view_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_articles_slug_unique` (`slug`),
  KEY `blog_articles_status_idx` (`status`),
  KEY `blog_articles_published_at_idx` (`published_at`),
  KEY `blog_articles_user_id_idx` (`user_id`),
  CONSTRAINT `fk_blog_articles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
