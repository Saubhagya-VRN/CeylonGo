-- Create transport_requests table
CREATE TABLE IF NOT EXISTS `transport_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `date` date NOT NULL,
  `num_people` int(11) NOT NULL,
  `vehicle_type` varchar(50) NOT NULL,
  `pickup_location` varchar(255) NOT NULL,
  `pickup_time` time NOT NULL,
  `dropoff_location` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `estimated_fare` decimal(10,2) DEFAULT NULL,
  `distance` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
