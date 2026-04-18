-- Standalone package reviews: all text, rating, moderation live here (no `reviews` table).
-- Backup your DB first. Run in phpMyAdmin or: mysql -u root ceylon_go < database/migrate_package_reviews.sql
--
-- After verifying the app works, you may drop the old table:
--   DROP TABLE IF EXISTS `reviews`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `package_reviews`;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `package_reviews` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `package_id` INT UNSIGNED DEFAULT NULL COMMENT 'NULL = general review',
  `user_id` INT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `rating` TINYINT NOT NULL,
  `review_text` TEXT NOT NULL,
  `destination` VARCHAR(255) DEFAULT NULL COMMENT 'Package title snapshot at submit',
  `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  `admin_reply` TEXT NULL,
  `replied_at` DATETIME NULL,
  `approved_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_package_id` (`package_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_package_reviews_pkg` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_package_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `tourist_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
