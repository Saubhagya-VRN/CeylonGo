-- Create guide_acc_details table for tour guide bank account information
CREATE TABLE IF NOT EXISTS `guide_acc_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_id` int(11) NOT NULL COMMENT 'Foreign key to guide_users.id',
  `bank_name` varchar(100) NOT NULL,
  `acc_no` varchar(50) NOT NULL,
  `acc_holder_name` varchar(100) NOT NULL,
  `branch_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_ref_id` (`ref_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add index for faster lookups
CREATE INDEX IF NOT EXISTS `idx_guide_acc_ref_id` ON `guide_acc_details` (`ref_id`);
