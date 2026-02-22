-- Same as create_packages_table.sql but uses TEXT instead of JSON for MySQL < 5.7.8 / MariaDB < 10.2.7.
-- Store JSON strings; use json_encode()/json_decode() in PHP.

CREATE TABLE IF NOT EXISTS `packages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `location` VARCHAR(100) NOT NULL,
  `locations` VARCHAR(255) DEFAULT NULL,
  `duration` VARCHAR(50) DEFAULT NULL,
  `duration_short` VARCHAR(50) DEFAULT NULL,
  `image` VARCHAR(500) DEFAULT NULL,
  `category` VARCHAR(50) NOT NULL,
  `price` INT UNSIGNED NOT NULL DEFAULT 0,
  `price_child_ratio` DECIMAL(3,2) DEFAULT 0.50,
  `price_infant_ratio` DECIMAL(3,2) DEFAULT 0.00,
  `rating` DECIMAL(2,1) DEFAULT NULL,
  `reviews` INT UNSIGNED DEFAULT 0,
  `trending` TINYINT(1) NOT NULL DEFAULT 0,
  `overview` TEXT DEFAULT NULL,
  `highlights` TEXT DEFAULT NULL,
  `itinerary` TEXT DEFAULT NULL,
  `accommodation` TEXT DEFAULT NULL,
  `included` TEXT DEFAULT NULL,
  `excluded` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_trending` (`trending`),
  KEY `idx_price` (`price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
