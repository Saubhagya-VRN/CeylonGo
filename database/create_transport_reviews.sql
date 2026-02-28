-- Create transport_reviews table for per-driver reviews
CREATE TABLE IF NOT EXISTS `transport_reviews` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `request_id` INT(11) NOT NULL,
  `driver_id` VARCHAR(12) NOT NULL,
  `tourist_id` INT(11) NOT NULL,
  `rating` INT(11) NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
  `review_text` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  KEY `idx_driver_id` (`driver_id`),
  KEY `idx_request_id` (`request_id`),
  KEY `idx_tourist_id` (`tourist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
