-- Packages table for tourist package listing, details, and future admin CRUD.
-- JSON columns require MySQL 5.7.8+ or MariaDB 10.2.7+. On older versions use TEXT and json_encode/json_decode in PHP.

CREATE TABLE IF NOT EXISTS `packages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL COMMENT 'Full package name e.g. Southern Coast Honeymoon: Galle, Mirissa & Unawatuna 4N/5D',
  `location` VARCHAR(100) NOT NULL COMMENT 'Primary location e.g. Galle',
  `locations` VARCHAR(255) DEFAULT NULL COMMENT 'Comma-separated list for listing e.g. Galle, Mirissa, Unawatuna',
  `duration` VARCHAR(50) DEFAULT NULL COMMENT 'e.g. 5 Days 4 Nights',
  `duration_short` VARCHAR(50) DEFAULT NULL COMMENT 'e.g. 5 Days / 4 Nights',
  `image` VARCHAR(500) DEFAULT NULL COMMENT 'Image path or URL',
  `category` VARCHAR(50) NOT NULL COMMENT 'e.g. cultural, honeymoon, solo, adventure, heritage, safari, family, beach',
  `price` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Price in LKR',
  `price_child_ratio` DECIMAL(3,2) DEFAULT 0.50 COMMENT 'Child price = price * ratio',
  `price_infant_ratio` DECIMAL(3,2) DEFAULT 0.00 COMMENT 'Infant price = price * ratio',
  `rating` DECIMAL(2,1) DEFAULT NULL COMMENT 'e.g. 4.5',
  `reviews` INT UNSIGNED DEFAULT 0 COMMENT 'Review count',
  `trending` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=no, 1=yes (can be computed from bookings later)',
  `overview` JSON DEFAULT NULL COMMENT 'Array of overview bullet strings',
  `highlights` JSON DEFAULT NULL COMMENT 'Array of {icon, title, desc}',
  `itinerary` JSON DEFAULT NULL COMMENT 'Array of {day, title, activities[]}',
  `accommodation` JSON DEFAULT NULL COMMENT 'Array of {nights, location, hotel}',
  `included` JSON DEFAULT NULL COMMENT 'Array of included item strings',
  `excluded` JSON DEFAULT NULL COMMENT 'Array of excluded item strings',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_trending` (`trending`),
  KEY `idx_price` (`price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tour packages for listing, detail page, booking, and admin management';
