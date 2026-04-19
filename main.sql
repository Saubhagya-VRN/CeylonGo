-- CeylonGo Main Database Schema
-- Synchronized with WhatsApp Transfer (test.sql) on 2026-04-18
-- Preserves Diary System and Enhanced Refund Logic

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Table structure for table `accommodation_catalog`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `accommodation_catalog`;
CREATE TABLE `accommodation_catalog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hotel_slug` varchar(128) NOT NULL COMMENT 'e.g. sunset-beach',
  `hotel_name` varchar(255) NOT NULL,
  `hotel_user_id` int(11) NOT NULL,
  `room_no` int(11) NOT NULL,
  `location` varchar(255) NOT NULL COMMENT 'e.g. Galle, Southern Province',
  `rating` decimal(2,1) NOT NULL DEFAULT 0.0,
  `review_count` int(11) NOT NULL DEFAULT 0,
  `amenities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '["WiFi","Pool",...]' CHECK (json_valid(`amenities`)),
  `hero_image` varchar(500) DEFAULT NULL,
  `from_price` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Hotel card "from" /night (LKR), usually min room rate',
  `currency` varchar(8) NOT NULL DEFAULT 'LKR',
  `room_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`room_details`)),
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  INDEX `idx_accommodation_catalog_slug` (`hotel_slug`,`sort_order`),
  INDEX `idx_accommodation_catalog_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `accommodation_catalog` (`id`, `hotel_slug`, `hotel_name`, `hotel_user_id`, `room_no`, `location`, `rating`, `review_count`, `amenities`, `hero_image`, `from_price`, `currency`, `room_details`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'sunset-beach', 'Sunset Beach Resort', 27, 1, 'Galle, Southern Province', '5.0', 127, '[\"WiFi\",\"Pool\",\"Spa\",\"Restaurant\"]', '/CeylonGo/public/images/5star.jpg', '12500.00', 'LKR', '[{\"type\":\"Standard Room\",\"description\":\"Cozy double room with garden view, ideal for couples.\",\"price\":\"Rs.12,500\",\"priceValue\":12500,\"image\":\"/img/5star.jpg\"},{\"type\":\"Deluxe Sea View\",\"description\":\"Spacious room with balcony overlooking the ocean.\",\"price\":\"Rs.18,900\",\"priceValue\":18900,\"image\":\"/img/5star.jpg\"},{\"type\":\"new\",\"description\":\"new\",\"price\":\"Rs.15,000\",\"priceValue\":15000,\"image\":\"/img/5star.jpg\"}]', 1, 1, '2026-04-12 12:25:29'),
(2, 'sachith-beach', 'Sachith Beach Resort', 5, 0, 'Galle, Southern Province', '5.0', 127, '[\"WiFi\",\"Pool\",\"Spa\",\"Restaurant\"]', '/CeylonGo/public/images/5star.jpg', '12500.00', 'LKR', '[   {     \"type\": \"Standard Room\",     \"description\": \"Cozy double room with garden view, ideal for couples.\",     \"price\": \"Rs.12,500\",     \"priceValue\": 12500,     \"image\": \"/img/5star.jpg\"   },   {     \"type\": \"Deluxe Sea View\",     \"description\": \"Spacious room with balcony overlooking the ocean.\",     \"price\": \"Rs.18,900\",     \"priceValue\": 18900,     \"image\": \"/img/5star.jpg\"   } ]', 2, 1, '2026-04-12 12:25:29');

-- --------------------------------------------------------
-- Table structure for table `admin`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin` (`id`, `username`, `email`, `phone_number`, `role`, `password`) VALUES
(1, 'admin1', 'admin1@gmail.com', '0712323455', 'Senior Administrator', '$2y$10$Yd6F0.BVibpwJPGV.o1dLegNkZeWWitQXqC3LmOztGk63d5sKetG.'),
(2, 'admin2', 'admin2@gmail.com', '0771122334', 'Manager', '$2y$10$QyNbvufZQTO.M3EEXNyPJ.9bEsXhdyMDxP3oKmdn9L3nMdg6qNHYi'),
(4, 'admin3', 'admin3@gmail.com', '0756354279', 'Junior Admin', '$2y$10$o7srfJw7MK82UatrjHg51OwtEfnSQyPpMFG/iiU3q4uczjuPAWkOi');

-- --------------------------------------------------------
-- Table structure for table `customise_trips`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `customise_trips`;
CREATE TABLE `customise_trips` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `customise_trip_no` varchar(16) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `number_of_people` int(11) DEFAULT NULL,
  `budget_lkr` decimal(12,2) DEFAULT NULL,
  `trip_json` longtext NOT NULL,
  `status` enum('pending','submitted','payment_submitted','completed','cancelled') NOT NULL DEFAULT 'submitted',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_customise_trip_no` (`customise_trip_no`),
  KEY `idx_user_created` (`user_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DELIMITER $$
DROP TRIGGER IF EXISTS `trg_customise_trips_set_no`$$
CREATE TRIGGER `trg_customise_trips_set_no` AFTER INSERT ON `customise_trips` FOR EACH ROW BEGIN
  UPDATE customise_trips
  SET customise_trip_no = CONCAT('CT', LPAD(NEW.id, 6, '0'))
  WHERE id = NEW.id;
END$$
DELIMITER ;

-- --------------------------------------------------------
-- Table structure for table `guides`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `guides`;
CREATE TABLE `guides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `guide_bookings`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `guide_bookings`;
CREATE TABLE `guide_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guide_id` int(11) NOT NULL,
  `place_id` int(11) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `booking_date` datetime DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `guide_id` (`guide_id`),
  KEY `place_id` (`place_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `guide_places`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `guide_places`;
CREATE TABLE `guide_places` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guide_id` int(11) NOT NULL,
  `place_name` varchar(255) NOT NULL,
  `address` varchar(500) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `guide_id` (`guide_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `guide_requests`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `guide_requests`;
CREATE TABLE `guide_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tourist_id` int(11) DEFAULT NULL,
  `customerName` varchar(255) NOT NULL,
  `contactNumber` varchar(20) NOT NULL,
  `location` varchar(255) NOT NULL,
  `language` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `fee` decimal(10,2) DEFAULT 3000.00,
  `guide_id` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_guide_id` (`guide_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `guide_requests` (`id`, `tourist_id`, `customerName`, `contactNumber`, `location`, `language`, `date`, `time`, `notes`, `status`, `fee`, `guide_id`, `approved_at`, `created_at`) VALUES
(1, 12, 'Dee Gagan', '0789145722', 'Kandy', 'Sinhala', '2026-03-03', '10:00:00', '', 'pending', '3000.00', 14, NULL, '2026-03-03 10:48:47'),
(2, 12, 'Dee Gagan', '0789145722', 'anuradapura', 'Sinhala', '2026-03-03', '10:00:00', '', 'approved', '3000.00', 18, '2026-03-03 10:55:06', '2026-03-03 10:53:32'),
(3, 12, 'Dee Gagan', '0789145722', 'jaffna', 'Sinhala', '2026-04-03', '09:00:00', '', 'approved', '3000.00', 18, '2026-03-03 12:13:56', '2026-03-03 12:13:23'),
(4, 18, 'Dee Gagan', '0789145722', 'Trincomalee', 'English', '2026-04-24', '14:00:00', '', 'approved', '3000.00', 18, '2026-03-03 12:26:18', '2026-03-03 12:25:53'),
(5, 12, 'Dee Gagan', '0789145722', 'Kandy', 'Sinhala', '2026-05-27', '13:00:00', '', 'approved', '3000.00', 18, '2026-04-06 06:22:38', '2026-04-06 06:21:49'),
(6, 12, 'Dee Gagan', '0789145722', 'Kandy', 'Sinhala', '2026-04-27', '10:00:00', '', 'approved', '3000.00', 18, '2026-04-07 04:07:45', '2026-04-07 04:07:08'),
(7, NULL, 'Dee Gagan', '0789145722', 'Negombo, Sri Lanka', 'English', '2026-04-28', '14:00:00', '', 'pending', '3000.00', NULL, NULL, '2026-04-07 06:01:15'),
(8, 12, 'Dee Gagan', '0789145722', 'Jaffna International Airport, Palali, Sri Lanka', 'English', '2026-04-28', '14:00:00', '', 'approved', '3000.00', 18, '2026-04-07 06:09:17', '2026-04-07 06:08:35'),
(9, 12, 'Dee Gagan', '0789145722', 'Jabeen\'s Kitchen, Galle Road, Colombo, Sri Lanka', 'English', '2026-04-28', '13:00:00', '', 'approved', '3000.00', 18, '2026-04-07 06:17:34', '2026-04-07 06:16:23'),
(10, 12, 'Dee Gagan', '0789145722', 'Edotco Sri Lanka, Level 6, HNB Towers, T. B. Jayah Mawatha, Colombo, Sri Lanka', 'English', '2026-04-28', '13:00:00', '', 'approved', '3000.00', 18, '2026-04-07 06:39:18', '2026-04-07 06:38:28'),
(11, 12, 'Dee Gagan', '0789145722', 'Jana Jaya City Mall Rajagiriya ( JJC MALL ), Jinadasa Niyathapala Mawatha, Sri Jayawardenepura Kotte, Sri Lanka', 'English', '2026-04-28', '12:00:00', '', 'pending', '3000.00', 18, NULL, '2026-04-07 06:51:39'),
(12, 11, 'Nethmini Saubhagya', '0714254872', 'Temple of the Sacred Tooth Relic, Kandy, Sri Lanka', 'English', '2026-04-28', '14:00:00', '', 'approved', '3000.00', 18, '2026-04-07 07:59:00', '2026-04-07 07:58:05');

-- --------------------------------------------------------
-- Table structure for table `guide_users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `guide_users`;
CREATE TABLE `guide_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_type` enum('tourist','guide','transport') NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `nic` varchar(20) DEFAULT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `languages` varchar(255) DEFAULT NULL,
  `experience` int(11) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `license_file` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `guide_users` (`id`, `user_type`, `first_name`, `last_name`, `nic`, `license_number`, `specialization`, `languages`, `experience`, `profile_photo`, `license_file`, `contact_number`, `email`, `password`, `created_at`, `is_active`) VALUES
(18, 'guide', 'Kamal', 'Nishantha', '195988295v', 'GR-2378645', 'cultural,religious,beach', 'English,sinhala', 4, 'guide_profile_18_1771894166.jpg', 'IS2210_Lab_sheet_11.pdf', '0727355412', 'knishantha@gmail.com', '$2y$10$QsZucy5teAVyH65L4MfNoesZACKcN4ZIyFAHAe5h436Uk0gY1Y8I.', '2026-02-24 00:48:51', 1);

-- --------------------------------------------------------
-- Table structure for table `hotels`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `hotels`;
CREATE TABLE `hotels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `hotels` (`id`, `name`, `email`, `password`, `contact`, `address`, `created_at`) VALUES
(1, 'My Hotel', 'hotel@example.com', 'password123', '0771234567', 'No. 123, Colombo, Sri Lanka', '2025-10-20 17:14:50'),
(3, 'My Hotel', 'hotel2@example.com', 'password123', '0771234567', 'No. 123, Colombo, Sri Lanka', '2025-10-20 17:16:21');

-- --------------------------------------------------------
-- Table structure for table `hotel_bookings`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `hotel_bookings`;
CREATE TABLE `hotel_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `hotel_user_id` int(11) NOT NULL,
  `hotel_slug` varchar(128) NOT NULL COMMENT 'e.g. sunset-beach from data-hotel-id',
  `hotel_name` varchar(255) NOT NULL,
  `guest_name` varchar(255) NOT NULL,
  `contact_number` varchar(50) NOT NULL,
  `guests` int(11) NOT NULL DEFAULT 1,
  `adults` int(11) NOT NULL DEFAULT 0,
  `children` int(11) NOT NULL DEFAULT 0,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `nights` int(11) NOT NULL DEFAULT 1,
  `room_type` varchar(255) NOT NULL,
  `room_count` int(11) NOT NULL DEFAULT 1,
  `total_price` decimal(12,2) NOT NULL,
  `currency` varchar(8) NOT NULL DEFAULT 'LKR',
  `status` varchar(32) NOT NULL DEFAULT 'confirmed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_hotel_bookings_user` (`user_id`),
  KEY `idx_hotel_bookings_slug` (`hotel_slug`),
  KEY `idx_hotel_bookings_dates` (`check_in`,`check_out`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `hotel_bookings` (`id`, `user_id`, `hotel_user_id`, `hotel_slug`, `hotel_name`, `guest_name`, `contact_number`, `guests`, `adults`, `children`, `check_in`, `check_out`, `nights`, `room_type`, `room_count`, `total_price`, `currency`, `status`, `created_at`) VALUES
(1, 12, 1, 'city-center', 'City Center Hotel', 'Dee Gagan', '0789145722', 2, 0, 0, '2026-04-26', '2026-04-26', 1, 'Business Room', 1, '11000.00', 'LKR', 'confirmed', '2026-04-04 22:36:07'),
(2, 12, 1, 'downtown-comfort', 'Downtown Comfort Inn', 'Dee Gagan', '0789145722', 2, 0, 0, '2026-04-26', '2026-04-26', 1, 'Single Room', 1, '7500.00', 'LKR', 'confirmed', '2026-04-04 22:37:07'),
(3, 12, 1, 'budget-stay', 'Budget Stay Hostel', 'Dee Gagan', '0789145722', 2, 0, 0, '2026-04-26', '2026-04-26', 1, 'Shared Dorm', 1, '3500.00', 'LKR', 'confirmed', '2026-04-04 22:43:09'),
(60, 11, 1, 'budget-stay', 'Budget Stay Hostel', 'Nethmini Saubhagya', '0714254872', 2, 0, 0, '2026-04-28', '2026-05-01', 3, 'Shared Dorm', 1, '10500.00', 'LKR', 'pending', '2026-04-07 07:55:41');

-- --------------------------------------------------------
-- Table structure for table `hotel_requests`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `hotel_requests`;
CREATE TABLE `hotel_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `hotel_id` varchar(100) NOT NULL,
  `hotel_name` varchar(255) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `contact_number` varchar(50) NOT NULL,
  `guests` int(11) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `room_type` varchar(150) NOT NULL,
  `room_count` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'LKR',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `hotel_rooms`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `hotel_rooms`;
CREATE TABLE `hotel_rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hotel_id` int(11) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `capacity` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `amenities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`amenities`)),
  `status` enum('available','occupied','maintenance') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `hotel_id` (`hotel_id`),
  CONSTRAINT `hotel_rooms_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `hotel_rooms` (`id`, `hotel_id`, `room_number`, `room_type`, `rate`, `capacity`, `description`, `amenities`, `status`, `created_at`) VALUES
(50, 1, '7584', 'deluxe', '515151.00', 2, '', NULL, '', '2025-10-20 17:20:35'),
(51, 1, '255', 'double', '7500.00', 2, 'Ac Included', NULL, 'maintenance', '2025-10-20 18:52:13'),
(52, 1, '6985', 'double', '87522.00', 5, 'heyy', NULL, 'maintenance', '2025-10-20 18:52:54'),
(53, 1, '899', 'suite', '875000.00', 6, 'Clean', NULL, 'maintenance', '2025-10-20 18:56:28'),
(54, 1, '542', 'suite', '9855.00', 5, 'Sea View', '[\"wifi\",\"air_conditioning\"]', 'occupied', '2025-10-20 19:21:06'),
(55, 1, '542', 'double', '664.00', 2, '', '[\"wifi\"]', 'maintenance', '2025-10-21 03:05:52'),
(56, 1, '415', 'single', '23.00', 2, 'awetr', '[\"room_service\"]', 'available', '2025-10-21 03:31:50'),
(57, 1, '845', 'double', '4500.00', 2, '', '[\"wifi\"]', 'available', '2025-10-22 14:57:43'),
(58, 1, '123', 'double', '3000.00', 1, '', '[\"air_conditioning\",\"tv\",\"minibar\",\"room_service\"]', '', '2025-10-23 06:08:22'),
(59, 1, '100', 'double', '2000.00', 2, 'sea view', '[\"air_conditioning\",\"tv\",\"minibar\",\"room_service\",\"parking\"]', '', '2025-10-23 06:46:11'),
(60, 1, '101', 'double', '2000.00', 2, 'Sea view', '[\"air_conditioning\",\"tv\",\"minibar\",\"room_service\",\"parking\"]', '', '2025-10-23 07:40:47');

-- --------------------------------------------------------
-- Table structure for table `hotel_users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `hotel_users`;
CREATE TABLE `hotel_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hotel_name` varchar(100) NOT NULL,
  `location` varchar(150) NOT NULL,
  `city` varchar(100) NOT NULL,
  `hotel_image` varchar(255) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `hotel_users` (`id`, `hotel_name`, `location`, `city`, `hotel_image`, `contact_number`, `email`, `password`, `created_at`) VALUES
(1, 'Cent', '150/14/4, Lake Road, Kandy', 'Matara', '1760982033_register.drawio.png', '0714254873', 'hhh@gmail.com', '$2y$10$728EWLac3BiX/fZsOz3sUexIq.YAKZf6vAtHDZZRdqHpauzz9x.1K', '2025-10-20 17:40:33'),
(2, 'Ant', '150/14/4, Lake Road, Kandy', 'Matara', '1760982304_login.drawio.png', '0714254871', 'ooo@gmail.com', '$2y$10$FjACe/ZwvE97mSLW0TapU.Z5eREFFQt/yaNhlX4yEGdG76BGEiHAG', '2025-10-20 17:45:04'),
(3, 'Ant', '150/14/4, Lake Road, Kandy', 'Matara', '1760982414_login.drawio.png', '0714254871', 'ttt@gmail.com', '$2y$10$vDhxhC.1Nh0DOcQf9G1nvuQpkS6MmqLcko7yreHA70gH1GMjoP5we', '2025-10-20 17:46:54'),
(4, 'Shangrila', '150/14/4, Lake Road, Colombo', 'Colombo', '1761123454_review.drawio.png', '0714254876', 'shangrila@gmail.com', '$2y$10$9RfzT2QytsuaUmWGgnuPIezteVeYph.6yTaYlkr5APJJCx2T78ACO', '2025-10-22 08:57:34'),
(5, 'Resort Inn', '150/14/4, Lake Road, Jaffna', 'Jaffna', '1761137953_logout.drawio.png', '0714254873', 'resort@gmail.com', '$2y$10$ZxrWHGbt0.kN1x82ZY.eYubNOyKqq5ufHh0uRSU0fGAy2gAdAZNGW', '2025-10-22 12:59:13');

-- --------------------------------------------------------
-- Table structure for table `inquiries`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `inquiries`;
CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `guest_name` varchar(120) DEFAULT NULL,
  `guest_email` varchar(190) DEFAULT NULL,
  `subject` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `admin_reply` text DEFAULT NULL,
  `status` enum('pending','replied') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `replied_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_inquiries_user_id` (`user_id`),
  KEY `idx_inquiries_status` (`status`),
  KEY `idx_inquiries_created_at` (`created_at`),
  KEY `idx_inquiries_guest_email` (`guest_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `inquiries` (`id`, `user_id`, `guest_name`, `guest_email`, `subject`, `message`, `admin_reply`, `status`, `created_at`, `replied_at`) VALUES
(1, 12, NULL, NULL, 'Packages', 'Are food also included in packages?', 'yes', 'replied', '2026-04-06 09:59:25', '2026-04-07 04:01:50'),
(2, NULL, 'sadila perera', 'sadila@gmail.com', 'customising trips', 'how many days will it take to update the status of booking', NULL, 'pending', '2026-04-06 10:07:15', NULL);

-- --------------------------------------------------------
-- Table structure for table `packages`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `packages`;
CREATE TABLE `packages` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `location` varchar(100) NOT NULL COMMENT 'Primary location e.g. Galle',
  `locations` varchar(255) DEFAULT NULL COMMENT 'Comma-separated list for listing e.g. Galle, Mirissa, Unawatuna',
  `duration` varchar(50) DEFAULT NULL COMMENT 'e.g. 5 Days 4 Nights',
  `duration_short` varchar(50) DEFAULT NULL COMMENT 'e.g. 5 Days / 4 Nights',
  `image` varchar(500) DEFAULT NULL COMMENT 'Image path or URL',
  `category` varchar(50) NOT NULL COMMENT 'e.g. cultural, honeymoon, solo, adventure, heritage, safari, family, beach',
  `price` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Price in LKR',
  `price_child_ratio` decimal(3,2) DEFAULT 0.50 COMMENT 'Child price = price * ratio',
  `price_infant_ratio` decimal(3,2) DEFAULT 0.00 COMMENT 'Infant price = price * ratio',
  `rating` decimal(2,1) DEFAULT NULL COMMENT 'e.g. 4.5',
  `reviews` int(10) UNSIGNED DEFAULT 0 COMMENT 'Review count',
  `trending` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=no, 1=yes',
  `overview` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`overview`)),
  `highlights` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`highlights`)),
  `itinerary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`itinerary`)),
  `accommodation` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`accommodation`)),
  `included` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`included`)),
  `excluded` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`excluded`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_trending` (`trending`),
  KEY `idx_price` (`price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `packages` (`id`, `title`, `location`, `locations`, `duration`, `duration_short`, `image`, `category`, `price`, `price_child_ratio`, `price_infant_ratio`, `rating`, `reviews`, `trending`, `overview`, `highlights`, `itinerary`, `accommodation`, `included`, `excluded`, `created_at`, `updated_at`) VALUES
(1, 'Cultural Triangle 4N5D — Kandy, Sigiriya & Dambulla', 'Kandy', 'Kandy, Sigiriya, Dambulla', '5 Days 4 Nights', '5 Days / 4 Nights', '/CeylonGo/public/images/kandy.jpeg', 'cultural', 23999, '0.50', '0.00', '4.5', 203, 1, '[\"Arrival at Colombo and transfer to Kandy, the royal and cultural heart of Sri Lanka.\",\"Explore Kandy: Temple of the Tooth, evening cultural dance show; optional Botanical Gardens.\",\"Full day to Sigiriya Rock Fortress and Dambulla Cave Temple; return to Kandy.\",\"Kandy to Nuwara Eliya \\u2014 scenic hill country, tea factory visit.\",\"Departure to Colombo as per schedule.\"]', '[{\"icon\":\"hotel\",\"title\":\"Accommodation\",\"desc\":\"4 nights stay\"},{\"icon\":\"transfer\",\"title\":\"Transfers\",\"desc\":\"Private cab for all transfers\"},{\"icon\":\"sightseeing\",\"title\":\"Sightseeing\",\"desc\":\"Kandy, Sigiriya, Dambulla covered\"},{\"icon\":\"meals\",\"title\":\"Meals\",\"desc\":\"Daily breakfast & dinner included\"},{\"icon\":\"activities\",\"title\":\"Activities\",\"desc\":\"Temple of the Tooth, cultural show\"},{\"icon\":\"support\",\"title\":\"Support\",\"desc\":\"24x7 travel assistance\"}]', '[{\"day\":1,\"title\":\"Arrival \\u2013 Colombo to Kandy\",\"activities\":[\"Airport pick-up and transfer to Kandy\",\"Check-in and visit Temple of the Tooth\",\"Evening cultural dance show\"]},{\"day\":2,\"title\":\"Sigiriya Rock Fortress (Full Day)\",\"activities\":[\"Full day at Sigiriya Rock and gardens\",\"Dambulla Cave Temple en route\",\"Return to Kandy\"]},{\"day\":3,\"title\":\"Kandy \\u2013 City & Spice Garden\",\"activities\":[\"Botanical Gardens (optional)\",\"Spice garden tour\",\"Leisure in Kandy\"]},{\"day\":4,\"title\":\"Kandy \\u2013 Nuwara Eliya (Scenic)\",\"activities\":[\"Scenic drive to hill country\",\"Tea factory visit\",\"Overnight in Nuwara Eliya\"]},{\"day\":5,\"title\":\"Departure\",\"activities\":[\"Transfer to Colombo airport\",\"Departure\"]}]', '[{\"nights\":3,\"location\":\"Kandy\",\"hotel\":\"Earl\'s Regent Hotel\"},{\"nights\":1,\"location\":\"Nuwara Eliya\",\"hotel\":\"Araliya Green City Hotel\"}]', '[\"4 nights accommodation\",\"Half Board (Breakfast + Dinner)\",\"All entrance fees (Sigiriya, Dambulla, Temple of the Tooth)\",\"Cultural show ticket\",\"Private cab for sightseeing\"]', '[\"International flights\",\"Lunches and beverages unless specified\",\"Personal expenses and tips\",\"Visa (if applicable)\",\"Travel insurance\"]', '2026-02-14 15:31:21', '2026-04-03 20:52:09');

-- --------------------------------------------------------
-- Table structure for table `package_bookings`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `package_bookings`;
CREATE TABLE `package_bookings` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'Tourist user ID',
  `package_id` int(10) UNSIGNED NOT NULL COMMENT 'Package ID',
  `package_name` varchar(255) NOT NULL COMMENT 'Package name at time of booking',
  `travelers` int(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Total number of travelers',
  `adults` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `children` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `infants` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `travel_date` date NOT NULL COMMENT 'Preferred travel date',
  `fullname` varchar(255) NOT NULL COMMENT 'Customer full name',
  `email` varchar(255) NOT NULL COMMENT 'Customer email',
  `phone` varchar(50) NOT NULL COMMENT 'Customer phone number',
  `special_requests` text DEFAULT NULL COMMENT 'Special requests or notes',
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Total booking amount in LKR',
  `status` enum('pending','approved','rejected','cancelled','paid') NOT NULL DEFAULT 'pending',
  `payhere_payment_id` varchar(64) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `bank_transfer_submitted_at` datetime DEFAULT NULL COMMENT 'Tourist clicked Continue after bank transfer; awaiting manual verification',
  `bank_transfer_slip_path` varchar(500) DEFAULT NULL COMMENT 'Relative path under public/uploads (e.g. bank_slips/booking_1_....jpg)',
  `refund_requested_at` datetime DEFAULT NULL COMMENT 'When tourist submitted refund request',
  `refund_approved_at` datetime DEFAULT NULL,
  `refund_rejected_at` datetime DEFAULT NULL,
  `refund_reject_note` varchar(500) DEFAULT NULL,
  `refund_reason` varchar(2000) DEFAULT NULL COMMENT 'Optional reason from tourist',
  `admin_notes` text DEFAULT NULL COMMENT 'Admin notes or comments',
  `approved_at` datetime DEFAULT NULL COMMENT 'When booking was approved',
  `approved_by` int(10) UNSIGNED DEFAULT NULL COMMENT 'Admin user ID who approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `package_id` (`package_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `package_bookings` (`id`, `user_id`, `package_id`, `package_name`, `travelers`, `adults`, `children`, `infants`, `travel_date`, `fullname`, `email`, `phone`, `special_requests`, `total_amount`, `status`, `payhere_payment_id`, `paid_at`, `bank_transfer_submitted_at`, `bank_transfer_slip_path`, `refund_requested_at`, `refund_approved_at`, `refund_rejected_at`, `refund_reject_note`, `refund_reason`, `admin_notes`, `approved_at`, `approved_by`, `created_at`, `updated_at`) VALUES
(1, 12, 5, 'Wildlife Safari', 3, 2, 1, 0, '2026-03-17', 'Dee Gagan', 'dee@gmail.com', '0789145722', '', '412500.00', 'approved', NULL, NULL, '2026-04-04 02:12:28', NULL, NULL, NULL, NULL, NULL, NULL, '', '2026-04-04 01:58:24', 1, '2026-02-21 12:08:49', '2026-04-03 20:42:28'),
(4, 12, 1, 'Cultural Triangle 4N5D — Kandy, Sigiriya & Dambulla', 1, 1, 0, 0, '2026-04-23', 'Dee Gagan', 'dee@gmail.com', '0789145722', '', '50000.00', 'cancelled', 'sandbox-empty-return-275378c070c08a1f', '2026-04-04 01:46:39', NULL, NULL, '2026-04-04 19:21:01', '2026-04-13 05:42:35', NULL, NULL, 'I have other plans on the trip dates', ' | Refund approved by admin on 2026-04-13 05:42:35', '2026-04-02 01:41:45', 2, '2026-04-01 19:54:29', '2026-04-13 00:12:35');

-- --------------------------------------------------------
-- Table structure for table `package_reviews`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `package_reviews`;
CREATE TABLE `package_reviews` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `package_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `review_text` text NOT NULL,
  `destination` varchar(255) DEFAULT NULL COMMENT 'Package title snapshot at submit',
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `admin_reply` text DEFAULT NULL,
  `replied_at` datetime DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_package_id` (`package_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_package_reviews_pkg` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `reviews`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text NOT NULL,
  `destination` varchar(100) DEFAULT NULL,
  `admin_reply` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending' COMMENT 'pending, approved, rejected',
  `created_at` datetime DEFAULT current_timestamp(),
  `replied_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_rating` (`rating`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `tourist_users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tourist_users`;
CREATE TABLE `tourist_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tourist_users` (`id`, `first_name`, `last_name`, `contact_number`, `email`, `password`, `created_at`, `is_active`) VALUES
(1, 'Nethmini', 'Saubhagya', '0714254877', 'tourist1@gmail.com', '$2y$10$NuQqmwBQKWbEmOYuUId.s./vnqSpqCBQv/EW0B0U0bRo1pG6uY9ny', '2025-10-17 08:43:46', 1),
(12, 'Dee', 'Gagan', '0789145722', 'dee@gmail.com', '$2y$10$2XoYffJgN4Fwn0M33FaJde9sedqDul6JWJW1xVBFMoYIazqPnweIC', '2025-10-22 08:39:00', 1);

-- --------------------------------------------------------
-- Table structure for table `transport_users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `transport_users`;
CREATE TABLE `transport_users` (
  `user_id` varchar(12) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `dob` date NOT NULL,
  `nic` varchar(15) NOT NULL,
  `address` varchar(50) NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `profile_image` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `psw` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `nic` (`nic`),
  UNIQUE KEY `contact_no` (`contact_no`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `transport_users` (`user_id`, `full_name`, `dob`, `nic`, `address`, `contact_no`, `profile_image`, `email`, `psw`, `is_active`) VALUES
('TP69d33ed552', 'Sachith Anuranga', '1997-09-09', '199788295v', '347, Niwandama, Ja-ela', '0716944635', 'profile_69d33ed55eba9.jpg', 'sachi.anu@gmail.com', '$2y$10$9YehJqZRpEiXZtsH2AwYFO49/cz58QdDrWQ3PIfZONg97RpbokN5W', 1);

-- --------------------------------------------------------
-- Table structure for table `transport_license`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `transport_license`;
CREATE TABLE `transport_license` (
  `license_no` varchar(20) NOT NULL,
  `license_exp_date` date NOT NULL,
  `image` varchar(20) NOT NULL,
  `driver_id` varchar(12) NOT NULL,
  PRIMARY KEY (`license_no`),
  UNIQUE KEY `driver_id` (`driver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `transport_license` (`license_no`, `license_exp_date`, `image`, `driver_id`) VALUES
('098098', '2025-10-31', 'img_68f2880f03ffc8.0', 'U68f2880eea1'),
('AY-2345', '2025-11-07', 'img_68f9cc3d2967d0.5', ' 68f9cc3ce84');

-- --------------------------------------------------------
-- Table structure for table `transport_requests`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `transport_requests`;
CREATE TABLE `transport_requests` (
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
  `assigned_driver_id` varchar(12) DEFAULT NULL,
  `assigned_vehicle_no` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `date` (`date`),
  KEY `idx_assigned_driver` (`assigned_driver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `transport_requests` (`id`, `user_id`, `customer_name`, `contact_number`, `date`, `num_people`, `vehicle_type`, `pickup_location`, `pickup_time`, `dropoff_location`, `notes`, `estimated_fare`, `distance`, `status`, `assigned_driver_id`, `assigned_vehicle_no`, `created_at`, `updated_at`) VALUES
(1, 12, 'Dee Gagan', '0789145722', '2026-02-01', 1, 'Tuk', 'Jewing Colombo Hotel', '16:23:00', 'Galle Fort', '0', '10756.06', '107.56', 'pending', NULL, NULL, '2026-02-01 04:54:52', '2026-02-01 04:54:52'),
(45, 11, 'Nethmini Saubhagya', '0714254872', '2026-04-28', 2, 'Car', 'Sri Lanka Bureau of Foreign Employment, District Office, Galle, Sri Lanka', '13:26:00', 'Temple of the Sacred Tooth Relic, Kandy, Sri Lanka', NULL, '26568.36', '221.40', 'pending', NULL, NULL, '2026-04-07 07:58:05', '2026-04-07 07:58:05');

-- --------------------------------------------------------
-- Table structure for table `trips`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `trips`;
CREATE TABLE `trips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `number_of_people` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `destination` varchar(255) NOT NULL,
  `number_of_days` int(11) NOT NULL,
  `budget_lkr` decimal(12,2) DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `payhere_payment_id` varchar(64) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `refund_requested_at` datetime DEFAULT NULL COMMENT 'When tourist submitted refund request',
  `refund_reason` varchar(2000) DEFAULT NULL COMMENT 'Optional reason from tourist',
  `bank_transfer_submitted_at` datetime DEFAULT NULL,
  `bank_transfer_slip_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `start_date` (`start_date`),
  CONSTRAINT `trips_user_fk` FOREIGN KEY (`user_id`) REFERENCES `tourist_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `trip_submissions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `trip_submissions`;
CREATE TABLE `trip_submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trip_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `trip_json` longtext NOT NULL,
  `payment_status` enum('pending','payment_submitted','completed','refunded') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_trip` (`trip_id`),
  KEY `idx_user` (`user_id`),
  KEY `payment_status` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('tourist','guide','hotel','transport','admin') NOT NULL,
  `ref_id` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `email`, `password`, `role`, `ref_id`, `created_at`) VALUES
(1, 'hhh@gmail.com', '$2y$10$728EWLac3BiX/fZsOz3sUexIq.YAKZf6vAtHDZZRdqHpauzz9x.1K', 'hotel', '1', '2025-10-22 04:19:29'),
(26, 'dee@gmail.com', '$2y$10$2XoYffJgN4Fwn0M33FaJde9sedqDul6JWJW1xVBFMoYIazqPnweIC', 'tourist', '12', '2025-10-22 08:39:00');

-- --------------------------------------------------------
-- Table structure for table `transport_vehicle`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `transport_vehicle`;
CREATE TABLE `transport_vehicle` (
  `vehicle_no` varchar(15) NOT NULL,
  `user_id` varchar(12) NOT NULL,
  `vehicle_type` varchar(20) NOT NULL,
  `image` varchar(48) NOT NULL,
  `psg_capacity` int(11) NOT NULL,
  PRIMARY KEY (`vehicle_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `transport_vehicle` (`vehicle_no`, `user_id`, `vehicle_type`, `image`, `psg_capacity`) VALUES
('BA1234', 'U68f4b466a98', '1', 'img_68f4b46700fb02.80913680.png', 3),
('BA1239', ' 68f9cc3ce84', '1', 'img_68f9cc3d37dda9.01415511.png', 3),
('ju8877', 'U68f288b3a20', '1', 'img_68f288b3b09129.8', 3),
('PG-5432', 'U68f48c351e8', '2', 'img_68f48c352dd631.69653958.jpg', 10),
('TY-5341', 'U68f28688787', '1', 'img_68f480b7b36c52.25171860.webp', 3);

-- --------------------------------------------------------
-- Table structure for table `transport_vehicle_types`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `transport_vehicle_types`;
CREATE TABLE `transport_vehicle_types` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(20) NOT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `transport_vehicle_types` (`type_id`, `type_name`) VALUES
(1, 'TUK'),
(2, 'VAN');

-- --------------------------------------------------------
-- Table structure for table `transport_provider_acc_details`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `transport_provider_acc_details`;
CREATE TABLE `transport_provider_acc_details` (
  `ref_id` varchar(50) NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `acc_no` varchar(30) NOT NULL,
  `acc_holder_name` varchar(100) NOT NULL,
  `branch_name` varchar(50) NOT NULL,
  PRIMARY KEY (`ref_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `transport_provider_acc_details` (`ref_id`, `bank_name`, `acc_no`, `acc_holder_name`, `branch_name`) VALUES
('68f9cc3ce65', 'people\'s bank', '23432130005643', 'Kaveesha Dulanjani', 'Dehiwala');

-- --------------------------------------------------------
-- Table structure for table `tourist_guide_requests`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tourist_guide_requests`;
CREATE TABLE `tourist_guide_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tourist_name` varchar(100) NOT NULL,
  `requested_location` varchar(255) NOT NULL,
  `language` varchar(50) DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `tourist_transport_requests`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tourist_transport_requests`;
CREATE TABLE `tourist_transport_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tourist_id` int(11) NOT NULL,
  `pickup_location` varchar(255) NOT NULL,
  `dropoff_location` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `vehicle_type` varchar(50) DEFAULT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `tourist_trip_bookings`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tourist_trip_bookings`;
CREATE TABLE `tourist_trip_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `status` enum('draft','submitted','confirmed') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `tourist_trip_destinations`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tourist_trip_destinations`;
CREATE TABLE `tourist_trip_destinations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `days` int(11) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `trip_bookings`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `trip_bookings`;
CREATE TABLE `trip_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `guide_required` varchar(10) DEFAULT 'No',
  `status` varchar(50) DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `trip_destinations`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `trip_destinations`;
CREATE TABLE `trip_destinations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `start_date` date DEFAULT NULL,
  `people_count` int(11) NOT NULL,
  `days` int(11) NOT NULL,
  `hotel` varchar(255) DEFAULT '',
  `transport` varchar(255) DEFAULT 'No',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `transport_request_trip_links`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `transport_request_trip_links`;
CREATE TABLE `transport_request_trip_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trip_id` int(11) NOT NULL,
  `transport_request_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `trip_diary_entries`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `trip_diary_entries`;
CREATE TABLE `trip_diary_entries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tourist_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entry_date` date NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_diary_tourist` FOREIGN KEY (`tourist_id`) REFERENCES `tourist_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `diary_comments`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `diary_comments`;
CREATE TABLE `diary_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `entry_id` int NOT NULL,
  `user_id` int NOT NULL,
  `user_type` ENUM('tourist', 'guide', 'hotel', 'transporter', 'admin') NOT NULL,
  `comment_text` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_comment_entry` FOREIGN KEY (`entry_id`) REFERENCES `trip_diary_entries` (`id`) ON DELETE CASCADE,
  INDEX `idx_entry_id` (`entry_id`),
  INDEX `idx_user` (`user_id`, `user_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
