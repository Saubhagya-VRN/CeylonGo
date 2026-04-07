-- Inquiries: tourists ask questions, admin replies
CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) DEFAULT NULL,
  `guest_name` VARCHAR(120) DEFAULT NULL,
  `guest_email` VARCHAR(190) DEFAULT NULL,
  `subject` VARCHAR(150) NOT NULL,
  `message` TEXT NOT NULL,
  `admin_reply` TEXT DEFAULT NULL,
  `status` ENUM('pending','replied') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `replied_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_inquiries_user_id` (`user_id`),
  KEY `idx_inquiries_guest_email` (`guest_email`),
  KEY `idx_inquiries_status` (`status`),
  KEY `idx_inquiries_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

