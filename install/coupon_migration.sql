-- Coupon system migration
-- Run this once against your live database

CREATE TABLE IF NOT EXISTS `coupons` (
  `id`               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `code`             VARCHAR(50)   NOT NULL,
  `description`      VARCHAR(255)  DEFAULT NULL,
  `discount_type`    ENUM('percentage','fixed') NOT NULL DEFAULT 'percentage',
  `discount_value`   DECIMAL(10,2) NOT NULL,
  `applies_to`       VARCHAR(500)  NOT NULL DEFAULT 'all',
  `max_uses`         INT UNSIGNED  DEFAULT NULL,
  `used_count`       INT UNSIGNED  NOT NULL DEFAULT 0,
  `one_per_customer` TINYINT(1)    NOT NULL DEFAULT 1,
  `expires_at`       DATETIME      DEFAULT NULL,
  `is_active`        TINYINT(1)    NOT NULL DEFAULT 1,
  `created_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupons_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `coupon_uses` (
  `id`              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `coupon_id`       INT UNSIGNED  NOT NULL,
  `user_id`         INT UNSIGNED  DEFAULT NULL,
  `email`           VARCHAR(180)  NOT NULL,
  `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `used_at`         DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `coupon_uses_coupon_id` (`coupon_id`),
  KEY `coupon_uses_user_id` (`user_id`),
  CONSTRAINT `fk_coupon_uses_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
