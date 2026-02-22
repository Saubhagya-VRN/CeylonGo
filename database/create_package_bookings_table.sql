-- Create package_bookings table for storing package booking requests
CREATE TABLE IF NOT EXISTS `package_bookings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL COMMENT 'Tourist user ID',
  `package_id` INT UNSIGNED NOT NULL COMMENT 'Package ID',
  `package_name` VARCHAR(255) NOT NULL COMMENT 'Package name at time of booking',
  `travelers` INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Total number of travelers',
  `adults` INT UNSIGNED NOT NULL DEFAULT 1,
  `children` INT UNSIGNED NOT NULL DEFAULT 0,
  `infants` INT UNSIGNED NOT NULL DEFAULT 0,
  `travel_date` DATE NOT NULL COMMENT 'Preferred travel date',
  `fullname` VARCHAR(255) NOT NULL COMMENT 'Customer full name',
  `email` VARCHAR(255) NOT NULL COMMENT 'Customer email',
  `phone` VARCHAR(50) NOT NULL COMMENT 'Customer phone number',
  `special_requests` TEXT DEFAULT NULL COMMENT 'Special requests or notes',
  `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Total booking amount in LKR',
  `status` ENUM('pending', 'approved', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
  `admin_notes` TEXT DEFAULT NULL COMMENT 'Admin notes or comments',
  `approved_at` DATETIME DEFAULT NULL COMMENT 'When booking was approved',
  `approved_by` INT UNSIGNED DEFAULT NULL COMMENT 'Admin user ID who approved',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `package_id` (`package_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Package booking requests from tourists';



