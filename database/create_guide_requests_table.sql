-- Create guide_requests table in ceylon_go database
-- This table stores tour guide requests from tourists

CREATE TABLE IF NOT EXISTS `guide_requests` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `customerName` VARCHAR(255) NOT NULL,
  `contactNumber` VARCHAR(20) NOT NULL,
  `location` VARCHAR(255) NOT NULL,
  `language` VARCHAR(100) NOT NULL,
  `date` DATE NOT NULL,
  `time` TIME NOT NULL,
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_customer (`customerName`),
  INDEX idx_date (`date`),
  INDEX idx_location (`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
