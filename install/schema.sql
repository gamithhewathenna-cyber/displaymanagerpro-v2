-- SignageCloud Database Schema v1.0
-- Compatible with MySQL 8.0+

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Drop existing tables to ensure clean install
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `support_replies`;
DROP TABLE IF EXISTS `support_tickets`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `slides`;
DROP TABLE IF EXISTS `media`;
DROP TABLE IF EXISTS `channels`;
DROP TABLE IF EXISTS `subscriptions`;
DROP TABLE IF EXISTS `email_verifications`;
DROP TABLE IF EXISTS `password_resets`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `plans`;
DROP TABLE IF EXISTS `users`;


-- -----------------------------------------------
-- Table: users
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(180) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','customer') NOT NULL DEFAULT 'customer',
  `status` ENUM('active','suspended','pending') NOT NULL DEFAULT 'pending',
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `avatar` VARCHAR(255) NULL DEFAULT NULL,
  `timezone` VARCHAR(60) NOT NULL DEFAULT 'UTC',
  `last_login_at` TIMESTAMP NULL DEFAULT NULL,
  `last_login_ip` VARCHAR(45) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_status_idx` (`role`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Table: email_verifications
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `email_verifications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `token` VARCHAR(64) NOT NULL,
  `expires_at` TIMESTAMP NOT NULL,
  `used_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ev_token_unique` (`token`),
  KEY `ev_user_id_idx` (`user_id`),
  CONSTRAINT `fk_ev_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Table: password_resets
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `token` VARCHAR(64) NOT NULL,
  `expires_at` TIMESTAMP NOT NULL,
  `used_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pr_token_unique` (`token`),
  KEY `pr_user_id_idx` (`user_id`),
  CONSTRAINT `fk_pr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Table: plans
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `plans` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(80) NOT NULL,
  `slug` VARCHAR(80) NOT NULL,
  `description` TEXT NULL,
  `price_monthly` DECIMAL(10,2) NOT NULL,
  `price_annual` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `currency` VARCHAR(3) NOT NULL DEFAULT 'AUD',
  `max_screens` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `max_slides` TINYINT UNSIGNED NOT NULL DEFAULT 15,
  `max_storage_mb` INT UNSIGNED NOT NULL DEFAULT 1024,
  `stripe_price_id_monthly` VARCHAR(100) NULL DEFAULT NULL,
  `stripe_price_id_annual` VARCHAR(100) NULL DEFAULT NULL,
  `features` JSON NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plans_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Table: subscriptions
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `plan_id` INT UNSIGNED NOT NULL,
  `stripe_customer_id` VARCHAR(100) NULL DEFAULT NULL,
  `stripe_subscription_id` VARCHAR(100) NULL DEFAULT NULL,
  `stripe_price_id` VARCHAR(100) NULL DEFAULT NULL,
  `billing_cycle` ENUM('monthly','annual') NOT NULL DEFAULT 'monthly',
  `status` ENUM('trialing','active','past_due','canceled','incomplete','paused') NOT NULL DEFAULT 'trialing',
  `trial_ends_at` TIMESTAMP NULL DEFAULT NULL,
  `current_period_start` TIMESTAMP NULL DEFAULT NULL,
  `current_period_end` TIMESTAMP NULL DEFAULT NULL,
  `canceled_at` TIMESTAMP NULL DEFAULT NULL,
  `ends_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `subs_user_id_idx` (`user_id`),
  KEY `subs_plan_id_idx` (`plan_id`),
  KEY `subs_stripe_sub_idx` (`stripe_subscription_id`),
  KEY `subs_stripe_cust_idx` (`stripe_customer_id`),
  CONSTRAINT `fk_subs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_subs_plan` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Table: channels
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `channels` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(20) NOT NULL COMMENT 'Unique display URL token',
  `description` TEXT NULL,
  `orientation` ENUM('landscape','portrait') NOT NULL DEFAULT 'landscape',
  `slide_duration` SMALLINT UNSIGNED NOT NULL DEFAULT 8 COMMENT 'Seconds per slide',
  `transition_effect` ENUM('fade','slide-left','slide-right','zoom','crossfade') NOT NULL DEFAULT 'fade',
  `auto_refresh_minutes` TINYINT UNSIGNED NOT NULL DEFAULT 15,
  `background_color` VARCHAR(7) NOT NULL DEFAULT '#000000',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `last_displayed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `channels_slug_unique` (`slug`),
  KEY `channels_user_id_idx` (`user_id`),
  CONSTRAINT `fk_channels_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Table: media
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `media` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `stored_name` VARCHAR(255) NOT NULL,
  `storage_path` VARCHAR(500) NOT NULL,
  `storage_driver` ENUM('local','s3','r2') NOT NULL DEFAULT 'local',
  `mime_type` VARCHAR(80) NOT NULL,
  `file_size` INT UNSIGNED NOT NULL COMMENT 'Size in bytes',
  `width` SMALLINT UNSIGNED NULL DEFAULT NULL,
  `height` SMALLINT UNSIGNED NULL DEFAULT NULL,
  `public_url` VARCHAR(500) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `media_user_id_idx` (`user_id`),
  CONSTRAINT `fk_media_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Table: slides
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `slides` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `channel_id` INT UNSIGNED NOT NULL,
  `media_id` INT UNSIGNED NOT NULL,
  `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `duration_override` SMALLINT UNSIGNED NULL DEFAULT NULL COMMENT 'Override channel default (seconds)',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `slides_channel_id_idx` (`channel_id`),
  KEY `slides_media_id_idx` (`media_id`),
  KEY `slides_sort_idx` (`channel_id`, `sort_order`),
  CONSTRAINT `fk_slides_channel` FOREIGN KEY (`channel_id`) REFERENCES `channels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_slides_media` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Table: payments
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `subscription_id` INT UNSIGNED NULL DEFAULT NULL,
  `stripe_invoice_id` VARCHAR(100) NULL DEFAULT NULL,
  `stripe_payment_intent_id` VARCHAR(100) NULL DEFAULT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(3) NOT NULL DEFAULT 'AUD',
  `status` ENUM('succeeded','failed','pending','refunded') NOT NULL,
  `description` VARCHAR(255) NULL DEFAULT NULL,
  `invoice_url` VARCHAR(500) NULL DEFAULT NULL,
  `invoice_pdf` VARCHAR(500) NULL DEFAULT NULL,
  `paid_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `payments_user_id_idx` (`user_id`),
  KEY `payments_sub_id_idx` (`subscription_id`),
  KEY `payments_stripe_inv_idx` (`stripe_invoice_id`),
  CONSTRAINT `fk_payments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_payments_sub` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Table: support_tickets
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `support_tickets` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `ticket_number` VARCHAR(12) NOT NULL,
  `subject` VARCHAR(200) NOT NULL,
  `status` ENUM('open','pending','closed') NOT NULL DEFAULT 'open',
  `priority` ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
  `closed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tickets_number_unique` (`ticket_number`),
  KEY `tickets_user_id_idx` (`user_id`),
  KEY `tickets_status_idx` (`status`),
  CONSTRAINT `fk_tickets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Table: support_replies
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `support_replies` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `message` TEXT NOT NULL,
  `is_admin_reply` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `replies_ticket_id_idx` (`ticket_id`),
  KEY `replies_user_id_idx` (`user_id`),
  CONSTRAINT `fk_replies_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_replies_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Table: settings
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(100) NOT NULL,
  `value` TEXT NULL,
  `group` VARCHAR(60) NOT NULL DEFAULT 'general',
  `is_encrypted` TINYINT(1) NOT NULL DEFAULT 0,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Table: activity_logs
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL DEFAULT NULL,
  `action` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `logs_user_id_idx` (`user_id`),
  KEY `logs_action_idx` (`action`),
  KEY `logs_created_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------
-- Seed: Default Plans
-- -----------------------------------------------
INSERT INTO `plans` (`name`, `slug`, `description`, `price_monthly`, `price_annual`, `currency`, `max_screens`, `max_slides`, `max_storage_mb`, `features`, `is_active`, `sort_order`) VALUES
('Starter', 'starter', 'Perfect for single-screen setups', 75.00, 750.00, 'AUD', 1, 15, 512, '["14-Day Free Trial","1 TV Screen","Up to 15 Slides","500 MB Storage","Auto Refresh","Cloud Management","Secure Hosting","Software Updates"]', 1, 1),
('Growth', 'growth', 'Great for small venues with multiple screens', 99.00, 990.00, 'AUD', 2, 15, 1024, '["14-Day Free Trial","Up to 2 TV Screens","Up to 15 Slides","1 GB Storage","Auto Refresh","Cloud Management","Secure Hosting","Software Updates"]', 1, 2),
('Pro', 'pro', 'For businesses with multiple display zones', 119.00, 1190.00, 'AUD', 3, 15, 2048, '["14-Day Free Trial","Up to 3 TV Screens","Up to 15 Slides","2 GB Storage","Auto Refresh","Cloud Management","Secure Hosting","Software Updates","Priority Support"]', 1, 3);

-- -----------------------------------------------
-- Seed: Default Settings
-- -----------------------------------------------
INSERT INTO `settings` (`key`, `value`, `group`) VALUES
('company_name', 'SignageCloud', 'general'),
('company_logo', '', 'general'),
('company_url', 'https://yourdomain.com', 'general'),
('app_env', 'production', 'general'),
('stripe_mode', 'test', 'stripe'),
('stripe_test_pk', '', 'stripe'),
('stripe_test_sk', '', 'stripe'),
('stripe_live_pk', '', 'stripe'),
('stripe_live_sk', '', 'stripe'),
('stripe_webhook_secret', '', 'stripe'),
('smtp_host', '', 'mail'),
('smtp_port', '587', 'mail'),
('smtp_user', '', 'mail'),
('smtp_pass', '', 'mail'),
('smtp_from_email', 'noreply@yourdomain.com', 'mail'),
('smtp_from_name', 'SignageCloud', 'mail'),
('smtp_encryption', 'tls', 'mail'),
('storage_driver', 'local', 'storage'),
('s3_bucket', '', 'storage'),
('s3_region', '', 'storage'),
('s3_access_key', '', 'storage'),
('s3_secret_key', '', 'storage'),
('s3_url', '', 'storage'),
('r2_bucket', '', 'storage'),
('r2_account_id', '', 'storage'),
('r2_access_key', '', 'storage'),
('r2_secret_key', '', 'storage'),
('r2_url', '', 'storage'),
('max_upload_size_kb', '500', 'media'),
('allowed_mime_types', 'image/jpeg,image/png,image/webp', 'media'),
('ga_measurement_id', '', 'seo'),
('gsc_verification', '', 'seo');
