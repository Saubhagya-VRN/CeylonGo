-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 19, 2026 at 05:09 AM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ceylon_go`
--

-- --------------------------------------------------------

--
-- Table structure for table `accommodation_catalog`
--

DROP TABLE IF EXISTS `accommodation_catalog`;
CREATE TABLE IF NOT EXISTS `accommodation_catalog` (
  `amenities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '["WiFi","Pool",...]',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `currency` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'LKR',
  `from_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Hotel card "from" /night (LKR), usually min room rate',
  `hero_image` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hotel_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hotel_slug` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g. sunset-beach',
  `hotel_user_id` int NOT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g. Galle, Southern Province',
  `rating` decimal(2,1) NOT NULL DEFAULT '0.0',
  `review_count` int NOT NULL DEFAULT '0',
  `room_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `room_no` int NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ;

--
-- Dumping data for table `accommodation_catalog`
--

INSERT INTO `accommodation_catalog` (`amenities`, `created_at`, `currency`, `from_price`, `hero_image`, `hotel_name`, `hotel_slug`, `hotel_user_id`, `id`, `is_active`, `location`, `rating`, `review_count`, `room_details`, `room_no`, `sort_order`) VALUES
('[\"WiFi\",\"Pool\",\"Spa\",\"Restaurant\"]', '2026-04-12 12:25:29', 'LKR', 12500.00, '/CeylonGo/public/images/5star.jpg', 'Sunset Beach Resort', 'sunset-beach', 27, 1, 1, 'Galle, Southern Province', 5.0, 127, '[{\"type\":\"Standard Room\",\"description\":\"Cozy double room with garden view, ideal for couples.\",\"price\":\"Rs.12,500\",\"priceValue\":12500,\"image\":\"/img/5star.jpg\"},{\"type\":\"Deluxe Sea View\",\"description\":\"Spacious room with balcony overlooking the ocean.\",\"price\":\"Rs.18,900\",\"priceValue\":18900,\"image\":\"/img/5star.jpg\"},{\"type\":\"new\",\"description\":\"new\",\"price\":\"Rs.15,000\",\"priceValue\":15000,\"image\":\"/img/5star.jpg\"}]', 1, 1),
('[\"WiFi\",\"Pool\",\"Spa\",\"Restaurant\"]', '2026-04-12 12:25:29', 'LKR', 12500.00, '/CeylonGo/public/images/5star.jpg', 'Sachith Beach Resort', 'sachith-beach', 5, 2, 1, 'Galle, Southern Province', 5.0, 127, '[   {     \"type\": \"Standard Room\",     \"description\": \"Cozy double room with garden view, ideal for couples.\",     \"price\": \"Rs.12,500\",     \"priceValue\": 12500,     \"image\": \"/img/5star.jpg\"   },   {     \"type\": \"Deluxe Sea View\",     \"description\": \"Spacious room with balcony overlooking the ocean.\",     \"price\": \"Rs.18,900\",     \"priceValue\": 18900,     \"image\": \"/img/5star.jpg\"   } ]', 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`email`, `id`, `password`, `phone_number`, `role`, `username`) VALUES
('admin1@gmail.com', 1, '$2y$10$Yd6F0.BVibpwJPGV.o1dLegNkZeWWitQXqC3LmOztGk63d5sKetG.', '0712323455', 'Senior Administrator', 'admin1'),
('admin2@gmail.com', 2, '$2y$10$QyNbvufZQTO.M3EEXNyPJ.9bEsXhdyMDxP3oKmdn9L3nMdg6qNHYi', '0771122334', 'Manager', 'admin2'),
('admin3@gmail.com', 4, '$2y$10$o7srfJw7MK82UatrjHg51OwtEfnSQyPpMFG/iiU3q4uczjuPAWkOi', '0756354279', 'Junior Admin', 'admin3');

-- --------------------------------------------------------

--
-- Table structure for table `customise_trips`
--

DROP TABLE IF EXISTS `customise_trips`;
CREATE TABLE IF NOT EXISTS `customise_trips` (
  `budget_lkr` decimal(12,2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `customise_trip_no` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destination` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `number_of_people` int DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `status` enum('pending','submitted','payment_submitted','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'submitted',
  `trip_json` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `diary_comments`
--

DROP TABLE IF EXISTS `diary_comments`;
CREATE TABLE IF NOT EXISTS `diary_comments` (
  `comment_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `entry_id` int NOT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `user_type` enum('tourist','guide','hotel','transporter','admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guides`
--

DROP TABLE IF EXISTS `guides`;
CREATE TABLE IF NOT EXISTS `guides` (
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `description` text COLLATE utf8mb4_unicode_ci,
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guide_bookings`
--

DROP TABLE IF EXISTS `guide_bookings`;
CREATE TABLE IF NOT EXISTS `guide_bookings` (
  `booking_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guide_id` int NOT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `place_id` int DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guide_places`
--

DROP TABLE IF EXISTS `guide_places`;
CREATE TABLE IF NOT EXISTS `guide_places` (
  `address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guide_id` int NOT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `place_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guide_requests`
--

DROP TABLE IF EXISTS `guide_requests`;
CREATE TABLE IF NOT EXISTS `guide_requests` (
  `approved_at` timestamp NULL DEFAULT NULL,
  `contactNumber` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `customerName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `fee` decimal(10,2) DEFAULT '3000.00',
  `guide_id` int DEFAULT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `language` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `time` time NOT NULL,
  `tourist_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guide_requests`
--

INSERT INTO `guide_requests` (`approved_at`, `contactNumber`, `created_at`, `customerName`, `date`, `fee`, `guide_id`, `id`, `language`, `location`, `notes`, `status`, `time`, `tourist_id`) VALUES
(NULL, '0789145722', '2026-03-03 10:48:47', 'Dee Gagan', '2026-03-03', 3000.00, 14, 1, 'Sinhala', 'Kandy', '', 'pending', '10:00:00', 12),
('2026-03-03 10:55:06', '0789145722', '2026-03-03 10:53:32', 'Dee Gagan', '2026-03-03', 3000.00, 18, 2, 'Sinhala', 'anuradapura', '', 'approved', '10:00:00', 12),
('2026-03-03 12:13:56', '0789145722', '2026-03-03 12:13:23', 'Dee Gagan', '2026-04-03', 3000.00, 18, 3, 'Sinhala', 'jaffna', '', 'approved', '09:00:00', 12),
('2026-03-03 12:26:18', '0789145722', '2026-03-03 12:25:53', 'Dee Gagan', '2026-04-24', 3000.00, 18, 4, 'English', 'Trincomalee', '', 'approved', '14:00:00', 18),
('2026-04-06 06:22:38', '0789145722', '2026-04-06 06:21:49', 'Dee Gagan', '2026-05-27', 3000.00, 18, 5, 'Sinhala', 'Kandy', '', 'approved', '13:00:00', 12),
('2026-04-07 04:07:45', '0789145722', '2026-04-07 04:07:08', 'Dee Gagan', '2026-04-27', 3000.00, 18, 6, 'Sinhala', 'Kandy', '', 'approved', '10:00:00', 12),
('2026-04-19 05:03:53', '0789145722', '2026-04-07 06:01:15', 'Dee Gagan', '2026-04-28', 3000.00, 18, 7, 'English', 'Negombo, Sri Lanka', '', 'approved', '14:00:00', NULL),
('2026-04-07 06:09:17', '0789145722', '2026-04-07 06:08:35', 'Dee Gagan', '2026-04-28', 3000.00, 18, 8, 'English', 'Jaffna International Airport, Palali, Sri Lanka', '', 'approved', '14:00:00', 12),
('2026-04-07 06:17:34', '0789145722', '2026-04-07 06:16:23', 'Dee Gagan', '2026-04-28', 3000.00, 18, 9, 'English', 'Jabeen\'s Kitchen, Galle Road, Colombo, Sri Lanka', '', 'approved', '13:00:00', 12),
('2026-04-07 06:39:18', '0789145722', '2026-04-07 06:38:28', 'Dee Gagan', '2026-04-28', 3000.00, 18, 10, 'English', 'Edotco Sri Lanka, Level 6, HNB Towers, T. B. Jayah Mawatha, Colombo, Sri Lanka', '', 'approved', '13:00:00', 12),
('2026-04-07 07:59:00', '0714254872', '2026-04-07 07:58:05', 'Nethmini Saubhagya', '2026-04-28', 3000.00, 18, 12, 'English', 'Temple of the Sacred Tooth Relic, Kandy, Sri Lanka', '', 'approved', '14:00:00', 11),
('2026-04-19 05:04:17', '0789145722', '2026-04-13 20:49:58', 'Dee Gagan', '2026-05-05', 3000.00, 18, 13, 'Sinhala', 'BVSK_BOPITIYA, Weligepola Road, Sri Lanka', '', 'approved', '17:00:00', NULL),
('2026-04-19 05:04:24', '0789145722', '2026-04-13 20:56:41', 'Dee Gagan', '2026-05-05', 3000.00, 18, 14, 'English', 'J.N.S.Stores, Bandarawela, Sri Lanka', '', 'approved', '18:00:00', NULL),
('2026-04-19 05:04:21', '0789145722', '2026-04-13 21:16:36', 'Dee Gagan', '2026-05-05', 3000.00, 18, 15, 'English', 'Y F Enterprise, Siri Dhamma Mawatha, Colombo, Sri Lanka', '', 'approved', '16:00:00', NULL),
(NULL, '0789145722', '2026-04-13 21:31:38', 'Dee Gagan', '2026-05-05', 3000.00, NULL, 16, 'English', 'JB Fashion, Sri Lanka', '', 'pending', '18:00:00', NULL),
(NULL, '0789145722', '2026-04-19 04:10:26', 'Dee Gagan', '2026-05-12', 2500.00, 18, 17, 'English', 'Temple of the Sacred Tooth Relic, Kandy, Sri Lanka', '', 'rejected', '10:00:00', 12),
('2026-04-19 05:04:01', '0789145722', '2026-04-19 04:44:20', 'Dee Gagan', '2026-05-22', 2500.00, 18, 18, 'English', 'Arugam Bay Beach, Sri Lanka', '', 'approved', '11:00:00', 12);

-- --------------------------------------------------------

--
-- Table structure for table `guide_users`
--

DROP TABLE IF EXISTS `guide_users`;
CREATE TABLE IF NOT EXISTS `guide_users` (
  `contact_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `experience` int DEFAULT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `languages` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nic` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specialization` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_type` enum('tourist','guide','transport') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guide_users`
--

INSERT INTO `guide_users` (`contact_number`, `created_at`, `email`, `experience`, `first_name`, `id`, `is_active`, `languages`, `last_name`, `license_file`, `license_number`, `nic`, `password`, `profile_photo`, `specialization`, `user_type`) VALUES
('0727355412', '2026-02-24 00:48:51', 'knishantha@gmail.com', 4, 'Kamal', 18, 1, 'English,sinhala', 'Nishantha', 'IS2210_Lab_sheet_11.pdf', 'GR-2378645', '195988295v', '$2y$10$QsZucy5teAVyH65L4MfNoesZACKcN4ZIyFAHAe5h436Uk0gY1Y8I.', 'guide_profile_18_1771894166.jpg', 'cultural,religious,beach', 'guide');

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

DROP TABLE IF EXISTS `hotels`;
CREATE TABLE IF NOT EXISTS `hotels` (
  `address` text COLLATE utf8mb4_unicode_ci,
  `contact` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`address`, `contact`, `created_at`, `email`, `id`, `name`, `password`) VALUES
('No. 123, Colombo, Sri Lanka', '0771234567', '2025-10-20 17:14:50', 'hotel@example.com', 1, 'My Hotel', 'password123'),
('No. 123, Colombo, Sri Lanka', '0771234567', '2025-10-20 17:16:21', 'hotel2@example.com', 3, 'My Hotel', 'password123');

-- --------------------------------------------------------

--
-- Table structure for table `hotel_bookings`
--

DROP TABLE IF EXISTS `hotel_bookings`;
CREATE TABLE IF NOT EXISTS `hotel_bookings` (
  `adults` int NOT NULL DEFAULT '0',
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `children` int NOT NULL DEFAULT '0',
  `contact_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `currency` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'LKR',
  `guest_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guests` int NOT NULL DEFAULT '1',
  `hotel_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hotel_slug` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g. sunset-beach from data-hotel-id',
  `hotel_user_id` int NOT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `nights` int NOT NULL DEFAULT '1',
  `room_count` int NOT NULL DEFAULT '1',
  `room_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'confirmed',
  `total_price` decimal(12,2) NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hotel_bookings`
--

INSERT INTO `hotel_bookings` (`adults`, `check_in`, `check_out`, `children`, `contact_number`, `created_at`, `currency`, `guest_name`, `guests`, `hotel_name`, `hotel_slug`, `hotel_user_id`, `id`, `nights`, `room_count`, `room_type`, `status`, `total_price`, `user_id`) VALUES
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-04 22:36:07', 'LKR', 'Dee Gagan', 2, 'City Center Hotel', 'city-center', 1, 1, 1, 1, 'Business Room', 'confirmed', 11000.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-04 22:37:07', 'LKR', 'Dee Gagan', 2, 'Downtown Comfort Inn', 'downtown-comfort', 1, 2, 1, 1, 'Single Room', 'confirmed', 7500.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-04 22:43:09', 'LKR', 'Dee Gagan', 2, 'Budget Stay Hostel', 'budget-stay', 1, 3, 1, 1, 'Shared Dorm', 'confirmed', 3500.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-04 22:46:28', 'LKR', 'Dee Gagan', 2, 'Budget Stay Hostel', 'budget-stay', 0, 4, 1, 1, 'Shared Dorm', 'confirmed', 3500.00, 12),
(0, '2026-04-26', '2026-04-27', 0, '0789145722', '2026-04-04 22:47:12', 'LKR', 'Dee Gagan', 2, 'Downtown Comfort Inn', 'downtown-comfort', 0, 5, 2, 1, 'Single Room', 'confirmed', 15000.00, 12),
(0, '2026-04-27', '2026-04-28', 0, '0789145722', '2026-04-04 22:47:34', 'LKR', 'Dee Gagan', 2, 'City Center Hotel', 'city-center', 0, 6, 2, 1, 'Business Room', 'confirmed', 22000.00, 12),
(0, '2026-04-26', '2026-04-27', 0, '0789145722', '2026-04-04 22:49:43', 'LKR', 'Dee Gagan', 2, 'Downtown Comfort Inn', 'downtown-comfort', 0, 7, 2, 1, 'Single Room', 'confirmed', 15000.00, 12),
(0, '2026-04-27', '2026-04-28', 0, '0789145722', '2026-04-04 22:50:12', 'LKR', 'Dee Gagan', 2, 'City Center Hotel', 'city-center', 0, 8, 2, 1, 'Business Room', 'confirmed', 22000.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-04 22:52:13', 'LKR', 'Dee Gagan', 2, 'Downtown Comfort Inn', 'downtown-comfort', 0, 9, 1, 1, 'Single Room', 'confirmed', 7500.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-04 22:52:16', 'LKR', 'Dee Gagan', 2, 'City Center Hotel', 'city-center', 0, 10, 1, 1, 'Business Room', 'confirmed', 11000.00, 12),
(0, '2026-04-27', '2026-04-27', 0, '0789145722', '2026-04-05 10:10:18', 'LKR', 'Dee Gagan', 2, 'Budget Stay Hostel', 'budget-stay', 0, 11, 1, 1, 'Shared Dorm', 'confirmed', 3500.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-05 10:22:16', 'LKR', 'Dee Gagan', 2, 'Downtown Comfort Inn', 'downtown-comfort', 0, 12, 1, 1, 'Single Room', 'confirmed', 7500.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-05 10:36:22', 'LKR', 'Dee Gagan', 2, 'Downtown Comfort Inn', 'downtown-comfort', 0, 13, 1, 1, 'Single Room', 'confirmed', 7500.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-05 10:43:16', 'LKR', 'Dee Gagan', 2, 'Budget Stay Hostel', 'budget-stay', 0, 14, 1, 1, 'Shared Dorm', 'confirmed', 3500.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-05 10:54:45', 'LKR', 'Dee Gagan', 2, 'Backpacker\'s Paradise', 'backpackers-paradise', 0, 15, 1, 1, 'Mixed Dorm', 'confirmed', 2800.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-05 10:58:40', 'LKR', 'Dee Gagan', 2, 'City Center Hotel', 'city-center', 0, 16, 1, 1, 'Business Room', 'confirmed', 11000.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-05 11:04:48', 'LKR', 'Dee Gagan', 2, 'City Center Hotel', 'city-center', 0, 17, 1, 1, 'Business Room', 'confirmed', 11000.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-05 11:09:20', 'LKR', 'Dee Gagan', 2, 'City Center Hotel', 'city-center', 0, 18, 1, 1, 'Business Room', 'confirmed', 11000.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-05 11:14:15', 'LKR', 'Dee Gagan', 1, 'Downtown Comfort Inn', 'downtown-comfort', 0, 19, 1, 1, 'Single Room', 'confirmed', 7500.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-05 11:20:40', 'LKR', 'Dee Gagan', 2, 'City Center Hotel', 'city-center', 0, 20, 1, 1, 'Business Room', 'confirmed', 11000.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-05 11:36:18', 'LKR', 'Dee Gagan', 2, 'Downtown Comfort Inn', 'downtown-comfort', 0, 21, 1, 1, 'Single Room', 'confirmed', 7500.00, 12),
(0, '2026-04-27', '2026-04-27', 0, '0789145722', '2026-04-05 11:37:04', 'LKR', 'Dee Gagan', 2, 'Backpacker\'s Paradise', 'backpackers-paradise', 0, 22, 1, 1, 'Mixed Dorm', 'confirmed', 2800.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-05 11:57:06', 'LKR', 'Dee Gagan', 2, 'City Center Hotel', 'city-center', 0, 23, 1, 1, 'Business Room', 'confirmed', 11000.00, 12),
(0, '2026-04-27', '2026-04-27', 0, '0789145722', '2026-04-05 11:57:28', 'LKR', 'Dee Gagan', 2, 'Budget Stay Hostel', 'budget-stay', 0, 24, 1, 1, 'Shared Dorm', 'confirmed', 3500.00, 12),
(0, '2026-04-28', '2026-04-28', 0, '0789145722', '2026-04-05 11:58:02', 'LKR', 'Dee Gagan', 2, 'Backpacker\'s Paradise', 'backpackers-paradise', 0, 25, 1, 1, 'Mixed Dorm', 'confirmed', 2800.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-05 12:10:35', 'LKR', 'Dee Gagan', 2, 'City Center Hotel', 'city-center', 0, 26, 1, 1, 'Business Room', 'confirmed', 11000.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-05 12:40:43', 'LKR', 'Dee Gagan', 2, 'City Center Hotel', 'city-center', 0, 27, 1, 1, 'Business Room', 'confirmed', 11000.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-05 12:46:49', 'LKR', 'Dee Gagan', 2, 'City Center Hotel', 'city-center', 0, 28, 1, 1, 'Business Room', 'confirmed', 11000.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-05 12:51:22', 'LKR', 'Dee Gagan', 2, 'Budget Stay Hostel', 'budget-stay', 0, 29, 1, 1, 'Shared Dorm', 'pending', 3500.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-05 12:56:56', 'LKR', 'Dee Gagan', 2, 'Downtown Comfort Inn', 'downtown-comfort', 0, 30, 1, 1, 'Single Room', 'pending', 7500.00, 12),
(0, '2026-04-26', '2026-04-26', 0, '0789145722', '2026-04-05 13:00:01', 'LKR', 'Dee Gagan', 1, 'City Center Hotel', 'city-center', 0, 31, 1, 1, 'Business Room', 'pending', 11000.00, 12),
(0, '2026-04-27', '2026-04-28', 0, '0789145722', '2026-04-06 07:28:53', 'LKR', 'Dee Gagan', 1, 'Downtown Comfort Inn', 'downtown-comfort', 0, 32, 1, 1, 'Single Room', 'pending', 7500.00, 12),
(0, '2026-04-27', '2026-04-27', 0, '0789145722', '2026-04-06 07:34:03', 'LKR', 'Dee Gagan', 1, 'City Center Hotel', 'city-center', 0, 33, 1, 1, 'Business Room', 'pending', 11000.00, 12),
(0, '2026-04-27', '2026-04-27', 0, '0789145722', '2026-04-06 07:37:22', 'LKR', 'Dee Gagan', 1, 'Downtown Comfort Inn', 'downtown-comfort', 0, 34, 1, 1, 'Single Room', 'pending', 7500.00, 12),
(0, '2026-04-27', '2026-04-27', 0, '0789145722', '2026-04-06 08:26:27', 'LKR', 'Dee Gagan', 1, 'City Center Hotel', 'city-center', 0, 35, 1, 1, 'Business Room', 'pending', 11000.00, 12),
(0, '2026-04-27', '2026-04-27', 0, '0789145722', '2026-04-06 08:30:11', 'LKR', 'Dee Gagan', 1, 'City Center Hotel', 'city-center', 0, 36, 1, 1, 'Business Room', 'pending', 11000.00, 12),
(0, '2026-04-27', '2026-04-27', 0, '0789145722', '2026-04-06 08:44:34', 'LKR', 'Dee Gagan', 1, 'City Center Hotel', 'city-center', 0, 37, 1, 1, 'Business Room', 'pending', 11000.00, 12),
(0, '2026-04-27', '2026-04-27', 0, '0789145722', '2026-04-06 08:46:17', 'LKR', 'Dee Gagan', 1, 'Budget Stay Hostel', 'budget-stay', 0, 38, 1, 1, 'Shared Dorm', 'pending', 3500.00, 12),
(0, '2026-04-27', '2026-04-27', 0, '0789145722', '2026-04-06 08:48:40', 'LKR', 'Dee Gagan', 1, 'Budget Stay Hostel', 'budget-stay', 0, 39, 1, 1, 'Shared Dorm', 'pending', 3500.00, 12),
(0, '2026-04-27', '2026-04-27', 0, '0789145722', '2026-04-06 08:50:58', 'LKR', 'Dee Gagan', 1, 'City Center Hotel', 'city-center', 0, 40, 1, 1, 'Business Room', 'pending', 11000.00, 12),
(0, '2026-04-27', '2026-04-27', 0, '0789145722', '2026-04-06 08:55:01', 'LKR', 'Dee Gagan', 2, 'Downtown Comfort Inn', 'downtown-comfort', 0, 41, 1, 1, 'Single Room', 'pending', 7500.00, 12),
(0, '2026-04-27', '2026-04-27', 0, '0789145722', '2026-04-06 09:10:03', 'LKR', 'Dee Gagan', 2, 'City Center Hotel', 'city-center', 0, 42, 1, 1, 'Business Room', 'pending', 11000.00, 12),
(0, '2026-04-27', '2026-04-27', 0, '0745698321', '2026-04-06 10:27:39', 'LKR', 'Kalum Fernando', 2, 'City Center Hotel', 'city-center', 0, 43, 1, 1, 'Business Room', 'pending', 11000.00, 16),
(0, '2026-04-27', '2026-04-27', 0, '0789145722', '2026-04-06 12:25:41', 'LKR', 'Dee Gagan', 2, 'City Center Hotel', 'city-center', 0, 44, 1, 1, 'Business Room', 'pending', 11000.00, 12),
(0, '2026-04-27', '2026-04-27', 0, '0789145722', '2026-04-06 13:38:41', 'LKR', 'Dee Gagan', 1, 'City Center Hotel', 'city-center', 0, 45, 1, 1, 'Business Room', 'pending', 11000.00, 12),
(0, '2026-04-27', '2026-04-27', 0, '0714254872', '2026-04-06 17:03:38', 'LKR', 'Nethmini Saubhagya', 2, 'Downtown Comfort Inn', 'downtown-comfort', 0, 46, 1, 1, 'Single Room', 'pending', 7500.00, 11),
(0, '2026-04-27', '2026-04-27', 0, '0714254872', '2026-04-06 17:10:16', 'LKR', 'Nethmini Saubhagya', 2, 'City Center Hotel', 'city-center', 0, 47, 1, 1, 'Business Room', 'pending', 11000.00, 11),
(0, '2026-04-28', '2026-04-28', 0, '0789145722', '2026-04-06 19:34:26', 'LKR', 'Dee Gagan', 2, 'City Center Hotel', 'city-center', 0, 48, 1, 1, 'Business Room', 'pending', 11000.00, 12),
(0, '2026-04-28', '2026-04-28', 0, '0789145722', '2026-04-06 19:35:04', 'LKR', 'Dee Gagan', 2, 'Downtown Comfort Inn', 'downtown-comfort', 0, 49, 1, 1, 'Single Room', 'pending', 7500.00, 12),
(0, '2026-04-28', '2026-04-28', 0, '0789145722', '2026-04-06 19:38:44', 'LKR', 'Dee Gagan', 2, 'Budget Stay Hostel', 'budget-stay', 0, 50, 1, 1, 'Shared Dorm', 'pending', 3500.00, 12),
(0, '2026-04-28', '2026-04-28', 0, '0789145722', '2026-04-06 19:39:26', 'LKR', 'Dee Gagan', 2, 'Budget Stay Hostel', 'budget-stay', 0, 51, 1, 1, 'Shared Dorm', 'pending', 3500.00, 12),
(0, '2026-04-28', '2026-04-28', 0, '0789145722', '2026-04-06 19:59:38', 'LKR', 'Dee Gagan', 2, 'Backpacker\'s Paradise', 'backpackers-paradise', 0, 52, 1, 1, 'Mixed Dorm', 'pending', 2800.00, 12),
(0, '2026-04-28', '2026-04-28', 0, '0789145722', '2026-04-06 20:07:35', 'LKR', 'Dee Gagan', 2, 'Sunset Beach Resort', 'sunset-beach', 0, 53, 1, 1, 'Standard Room', 'pending', 12500.00, 12),
(0, '2026-04-28', '2026-04-28', 0, '0789145722', '2026-04-06 20:10:42', 'LKR', 'Dee Gagan', 1, 'Budget Stay Hostel', 'budget-stay', 0, 54, 1, 1, 'Shared Dorm', 'pending', 3500.00, 12),
(0, '2026-04-28', '2026-04-28', 0, '0789145722', '2026-04-06 20:15:50', 'LKR', 'Dee Gagan', 1, 'Budget Stay Hostel', 'budget-stay', 0, 55, 1, 1, 'Shared Dorm', 'pending', 3500.00, 12),
(0, '2026-04-28', '2026-04-28', 0, '0789145722', '2026-04-06 20:25:34', 'LKR', 'Dee Gagan', 2, 'City Center Hotel', 'city-center', 0, 56, 1, 1, 'Business Room', 'pending', 11000.00, 12),
(0, '2026-04-28', '2026-04-28', 0, '0789145722', '2026-04-06 20:36:35', 'LKR', 'Dee Gagan', 2, 'Backpacker\'s Paradise', 'backpackers-paradise', 0, 57, 1, 1, 'Mixed Dorm', 'pending', 2800.00, 12),
(0, '2026-04-28', '2026-04-29', 0, '0714254872', '2026-04-07 03:54:29', 'LKR', 'Nethmini Saubhagya', 1, 'Downtown Comfort Inn', 'downtown-comfort', 0, 58, 1, 1, 'Single Room', 'pending', 7500.00, 11),
(0, '2026-04-28', '2026-04-28', 0, '0789145722', '2026-04-07 06:38:15', 'LKR', 'Dee Gagan', 1, 'Downtown Comfort Inn', 'downtown-comfort', 0, 59, 1, 1, 'Single Room', 'pending', 7500.00, 12),
(0, '2026-04-28', '2026-05-01', 0, '0714254872', '2026-04-07 07:55:41', 'LKR', 'Nethmini Saubhagya', 2, 'Budget Stay Hostel', 'budget-stay', 1, 60, 3, 1, 'Shared Dorm', 'pending', 10500.00, 11),
(0, '2026-05-05', '2026-05-05', 0, '0789145722', '2026-04-13 20:49:31', 'LKR', 'Dee Gagan', 1, 'Backpacker\'s Paradise', 'backpackers-paradise', 0, 61, 1, 1, 'Mixed Dorm', 'pending', 2800.00, 12),
(0, '2026-05-05', '2026-05-05', 0, '0789145722', '2026-04-13 21:20:47', 'LKR', 'Dee Gagan', 1, 'City Center Hotel', 'city-center', 0, 62, 1, 1, 'Business Room', 'pending', 11000.00, 12),
(0, '2026-05-05', '2026-05-05', 0, '0789145722', '2026-04-13 21:22:29', 'LKR', 'Dee Gagan', 2, 'Sunset Beach Resort', 'sunset-beach', 0, 63, 1, 1, 'Standard Room', 'pending', 12500.00, 12),
(0, '2026-05-05', '2026-05-05', 0, '0789145722', '2026-04-17 05:02:13', 'LKR', 'Dee Gagan', 1, 'City Center Hotel', 'city-center', 0, 64, 1, 1, 'Business Room', 'pending', 11000.00, 12);

-- --------------------------------------------------------

--
-- Table structure for table `hotel_requests`
--

DROP TABLE IF EXISTS `hotel_requests`;
CREATE TABLE IF NOT EXISTS `hotel_requests` (
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `contact_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'LKR',
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guests` int NOT NULL,
  `hotel_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hotel_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `room_count` int NOT NULL,
  `room_type` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hotel_rooms`
--

DROP TABLE IF EXISTS `hotel_rooms`;
CREATE TABLE IF NOT EXISTS `hotel_rooms` (
  `amenities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `capacity` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `description` text COLLATE utf8mb4_unicode_ci,
  `hotel_id` int NOT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `rate` decimal(10,2) NOT NULL,
  `room_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `room_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('available','occupied','maintenance') COLLATE utf8mb4_unicode_ci DEFAULT 'available',
  PRIMARY KEY (`id`)
) ;

--
-- Dumping data for table `hotel_rooms`
--

INSERT INTO `hotel_rooms` (`amenities`, `capacity`, `created_at`, `description`, `hotel_id`, `id`, `rate`, `room_number`, `room_type`, `status`) VALUES
(NULL, 2, '2025-10-20 17:20:35', '', 1, 50, 515151.00, '7584', 'deluxe', ''),
(NULL, 2, '2025-10-20 18:52:13', 'Ac Included', 1, 51, 7500.00, '255', 'double', 'maintenance'),
(NULL, 5, '2025-10-20 18:52:54', 'heyy', 1, 52, 87522.00, '6985', 'double', 'maintenance'),
(NULL, 6, '2025-10-20 18:56:28', 'Clean', 1, 53, 875000.00, '899', 'suite', 'maintenance'),
('[\"wifi\",\"air_conditioning\"]', 5, '2025-10-20 19:21:06', 'Sea View', 1, 54, 9855.00, '542', 'suite', 'occupied'),
('[\"wifi\"]', 2, '2025-10-21 03:05:52', '', 1, 55, 664.00, '542', 'double', 'maintenance'),
('[\"room_service\"]', 2, '2025-10-21 03:31:50', 'awetr', 1, 56, 23.00, '415', 'single', 'available'),
('[\"wifi\"]', 2, '2025-10-22 14:57:43', '', 1, 57, 4500.00, '845', 'double', 'available'),
('[\"air_conditioning\",\"tv\",\"minibar\",\"room_service\"]', 1, '2025-10-23 06:08:22', '', 1, 58, 3000.00, '123', 'double', ''),
('[\"air_conditioning\",\"tv\",\"minibar\",\"room_service\",\"parking\"]', 2, '2025-10-23 06:46:11', 'sea view', 1, 59, 2000.00, '100', 'double', ''),
('[\"air_conditioning\",\"tv\",\"minibar\",\"room_service\",\"parking\"]', 2, '2025-10-23 07:40:47', 'Sea view', 1, 60, 2000.00, '101', 'double', '');

-- --------------------------------------------------------

--
-- Table structure for table `hotel_users`
--

DROP TABLE IF EXISTS `hotel_users`;
CREATE TABLE IF NOT EXISTS `hotel_users` (
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_number` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hotel_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hotel_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `location` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hotel_users`
--

INSERT INTO `hotel_users` (`city`, `contact_number`, `created_at`, `is_active`, `email`, `hotel_image`, `hotel_name`, `id`, `location`, `password`) VALUES
('Matara', '0714254873', '2025-10-20 17:40:33', 1, 'hhh@gmail.com', '1760982033_register.drawio.png', 'Cent', 1, '150/14/4, Lake Road, Kandy', '$2y$10$728EWLac3BiX/fZsOz3sUexIq.YAKZf6vAtHDZZRdqHpauzz9x.1K'),
('Matara', '0714254871', '2025-10-20 17:45:04', 1, 'ooo@gmail.com', '1760982304_login.drawio.png', 'Ant', 2, '150/14/4, Lake Road, Kandy', '$2y$10$FjACe/ZwvE97mSLW0TapU.Z5eREFFQt/yaNhlX4yEGdG76BGEiHAG'),
('Matara', '0714254871', '2025-10-20 17:46:54', 1, 'ttt@gmail.com', '1760982414_login.drawio.png', 'Ant', 3, '150/14/4, Lake Road, Kandy', '$2y$10$vDhxhC.1Nh0DOcQf9G1nvuQpkS6MmqLcko7yreHA70gH1GMjoP5we'),
('Colombo', '0714254876', '2025-10-22 08:57:34', 1, 'shangrila@gmail.com', '1761123454_review.drawio.png', 'Shangrila', 4, '150/14/4, Lake Road, Colombo', '$2y$10$9RfzT2QytsuaUmWGgnuPIezteVeYph.6yTaYlkr5APJJCx2T78ACO'),
('Jaffna', '0714254873', '2025-10-22 12:59:13', 1, 'resort@gmail.com', '1761137953_logout.drawio.png', 'Resort Inn', 5, '150/14/4, Lake Road, Jaffna', '$2y$10$ZxrWHGbt0.kN1x82ZY.eYubNOyKqq5ufHh0uRSU0fGAy2gAdAZNGW');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

DROP TABLE IF EXISTS `inquiries`;
CREATE TABLE IF NOT EXISTS `inquiries` (
  `admin_reply` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guest_email` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','replied') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `subject` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`admin_reply`, `created_at`, `guest_email`, `guest_name`, `id`, `message`, `replied_at`, `status`, `subject`, `user_id`) VALUES
('yes', '2026-04-06 09:59:25', NULL, NULL, 1, 'Are food also included in packages?', '2026-04-07 04:01:50', 'replied', 'Packages', 12),
(NULL, '2026-04-06 10:07:15', 'sadila@gmail.com', 'sadila perera', 2, 'how many days will it take to update the status of booking', NULL, 'pending', 'customising trips', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

DROP TABLE IF EXISTS `packages`;
CREATE TABLE IF NOT EXISTS `packages` (
  `accommodation` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g. cultural, honeymoon, solo, adventure, heritage, safari, family, beach',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `duration` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'e.g. 5 Days 4 Nights',
  `duration_short` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'e.g. 5 Days / 4 Nights',
  `excluded` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `highlights` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `image` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Image path or URL',
  `included` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `itinerary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `location` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Primary location e.g. Galle',
  `locations` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Comma-separated list for listing e.g. Galle, Mirissa, Unawatuna',
  `overview` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `price` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Price in LKR',
  `price_child_ratio` decimal(3,2) DEFAULT '0.50' COMMENT 'Child price = price * ratio',
  `price_infant_ratio` decimal(3,2) DEFAULT '0.00' COMMENT 'Infant price = price * ratio',
  `rating` decimal(2,1) DEFAULT NULL COMMENT 'e.g. 4.5',
  `reviews` int UNSIGNED DEFAULT '0' COMMENT 'Review count',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trending` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=no, 1=yes',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`accommodation`, `category`, `created_at`, `duration`, `duration_short`, `excluded`, `highlights`, `id`, `image`, `included`, `itinerary`, `location`, `locations`, `overview`, `price`, `price_child_ratio`, `price_infant_ratio`, `rating`, `reviews`, `title`, `trending`, `updated_at`) VALUES
('[{\"nights\":3,\"location\":\"Kandy\",\"hotel\":\"Earl\'s Regent Hotel\"},{\"nights\":1,\"location\":\"Nuwara Eliya\",\"hotel\":\"Araliya Green City Hotel\"}]', 'cultural', '2026-02-14 15:31:21', '5 Days 4 Nights', '5 Days / 4 Nights', '[\"International flights\",\"Lunches and beverages unless specified\",\"Personal expenses and tips\",\"Visa (if applicable)\",\"Travel insurance\"]', '[{\"icon\":\"hotel\",\"title\":\"Accommodation\",\"desc\":\"4 nights stay\"},{\"icon\":\"transfer\",\"title\":\"Transfers\",\"desc\":\"Private cab for all transfers\"},{\"icon\":\"sightseeing\",\"title\":\"Sightseeing\",\"desc\":\"Kandy, Sigiriya, Dambulla covered\"},{\"icon\":\"meals\",\"title\":\"Meals\",\"desc\":\"Daily breakfast & dinner included\"},{\"icon\":\"activities\",\"title\":\"Activities\",\"desc\":\"Temple of the Tooth, cultural show\"},{\"icon\":\"support\",\"title\":\"Support\",\"desc\":\"24x7 travel assistance\"}]', 1, '/CeylonGo/public/images/kandy.jpeg', '[\"4 nights accommodation\",\"Half Board (Breakfast + Dinner)\",\"All entrance fees (Sigiriya, Dambulla, Temple of the Tooth)\",\"Cultural show ticket\",\"Private cab for sightseeing\"]', '[{\"day\":1,\"title\":\"Arrival \\u2013 Colombo to Kandy\",\"activities\":[\"Airport pick-up and transfer to Kandy\",\"Check-in and visit Temple of the Tooth\",\"Evening cultural dance show\"]},{\"day\":2,\"title\":\"Sigiriya Rock Fortress (Full Day)\",\"activities\":[\"Full day at Sigiriya Rock and gardens\",\"Dambulla Cave Temple en route\",\"Return to Kandy\"]},{\"day\":3,\"title\":\"Kandy \\u2013 City & Spice Garden\",\"activities\":[\"Botanical Gardens (optional)\",\"Spice garden tour\",\"Leisure in Kandy\"]},{\"day\":4,\"title\":\"Kandy \\u2013 Nuwara Eliya (Scenic)\",\"activities\":[\"Scenic drive to hill country\",\"Tea factory visit\",\"Overnight in Nuwara Eliya\"]},{\"day\":5,\"title\":\"Departure\",\"activities\":[\"Transfer to Colombo airport\",\"Departure\"]}]', 'Kandy', 'Kandy, Sigiriya, Dambulla', '[\"Arrival at Colombo and transfer to Kandy, the royal and cultural heart of Sri Lanka.\",\"Explore Kandy: Temple of the Tooth, evening cultural dance show; optional Botanical Gardens.\",\"Full day to Sigiriya Rock Fortress and Dambulla Cave Temple; return to Kandy.\",\"Kandy to Nuwara Eliya \\u2014 scenic hill country, tea factory visit.\",\"Departure to Colombo as per schedule.\"]', 23999, 0.50, 0.00, 4.5, 203, 'Cultural Triangle 4N5D — Kandy, Sigiriya & Dambulla', 1, '2026-04-03 20:52:09'),
('[{\"nights\":4,\"location\":\"Galle \\/ Unawatuna\",\"hotel\":\"Jetwing Lighthouse, Galle\"}]', 'honeymoon', '2026-02-14 15:31:21', '5 Days 4 Nights', '5 Days / 4 Nights', '[\"International flights\",\"Lunches and beverages\",\"Water sports (optional)\",\"Personal expenses and tips\",\"Visa\",\"Travel insurance\"]', '[{\"icon\":\"hotel\",\"title\":\"Accommodation\",\"desc\":\"4 nights beachfront stay\"},{\"icon\":\"transfer\",\"title\":\"Transfers\",\"desc\":\"Private cab for all transfers\"},{\"icon\":\"sightseeing\",\"title\":\"Sightseeing\",\"desc\":\"Galle Fort, Mirissa, Unawatuna\"},{\"icon\":\"meals\",\"title\":\"Meals\",\"desc\":\"Breakfast & dinner included\"},{\"icon\":\"activities\",\"title\":\"Activities\",\"desc\":\"Whale watching, beach experiences\"},{\"icon\":\"support\",\"title\":\"Support\",\"desc\":\"24x7 travel assistance\"}]', 2, '/CeylonGo/public/images/beach.jpg', '[\"Airport transfers\",\"4 nights beachfront accommodation\",\"Half Board\",\"Whale watching (seasonal)\",\"Honeymoon freebies as listed\"]', '[{\"day\":1,\"title\":\"Arrival \\u2013 Airport to Galle\",\"activities\":[\"Airport pick-up and transfer to Galle\",\"Check-in and leisure at resort\"]},{\"day\":2,\"title\":\"Galle Fort & Unawatuna\",\"activities\":[\"Galle Fort walking tour\",\"Unawatuna beach\",\"Sunset at the fort\"]},{\"day\":3,\"title\":\"Mirissa Whale Watching\",\"activities\":[\"Whale watching tour (seasonal)\",\"Beach time at Mirissa\"]},{\"day\":4,\"title\":\"Leisure & Spa\",\"activities\":[\"Beach relaxation\",\"Optional spa (discount for honeymooners)\",\"Candlelit dinner\"]},{\"day\":5,\"title\":\"Departure\",\"activities\":[\"Transfer to Colombo airport\",\"Departure\"]}]', 'Galle', 'Galle, Mirissa, Unawatuna', '[\"Arrival at Colombo and transfer to Galle, the historic fort city by the sea.\",\"Explore Galle Fort & Unawatuna \\u2014 walking tour, beach time, sunset at the fort.\",\"Head to Mirissa \\u2014 whale watching (seasonal), beach time.\",\"Leisure & spa \\u2014 beach relaxation, optional spa; candlelit dinner.\",\"Departure to Colombo as per schedule.\"]', 43999, 0.50, 0.00, 4.5, 65, 'Southern Coast Honeymoon: Galle, Mirissa & Unawatuna 4N/5D', 1, '2026-04-03 20:52:09'),
('[{\"nights\":2,\"location\":\"Nuwara Eliya\",\"hotel\":\"St. Andrew\'s Hotel, Nuwara Eliya\"},{\"nights\":3,\"location\":\"Ella\",\"hotel\":\"98 Acres Resort & Spa, Ella\"}]', 'adventure', '2026-02-14 15:31:21', '6 Days 5 Nights', '6 Days / 5 Nights', '[\"International flights\",\"Lunches and beverages\",\"Horton Plains entry (optional)\",\"Personal expenses\",\"Visa\",\"Travel insurance\"]', '[{\"icon\":\"hotel\",\"title\":\"Accommodation\",\"desc\":\"5 nights in hill country\"},{\"icon\":\"transfer\",\"title\":\"Transfers\",\"desc\":\"Private cab for all transfers\"},{\"icon\":\"sightseeing\",\"title\":\"Sightseeing\",\"desc\":\"Nuwara Eliya, Ella, Horton Plains\"},{\"icon\":\"meals\",\"title\":\"Meals\",\"desc\":\"Breakfast & dinner included\"},{\"icon\":\"activities\",\"title\":\"Activities\",\"desc\":\"Tea factory, Nine Arch Bridge, Little Adam\'s Peak\"},{\"icon\":\"support\",\"title\":\"Support\",\"desc\":\"24x7 travel assistance\"}]', 3, '/CeylonGo/public/images/greenary.jpg', '[\"Airport transfers\",\"5 nights accommodation\",\"Half Board\",\"Tea factory visit\",\"Private cab for sightseeing\"]', '[{\"day\":1,\"title\":\"Arrival \\u2013 Colombo to Nuwara Eliya\",\"activities\":[\"Airport pick-up and transfer to Nuwara Eliya\",\"Check-in\",\"Evening at Gregory Lake\"]},{\"day\":2,\"title\":\"Nuwara Eliya \\u2013 Horton Plains (optional)\",\"activities\":[\"Horton Plains National Park (optional)\",\"Tea estate visit\",\"Leisure in Nuwara Eliya\"]},{\"day\":3,\"title\":\"Nuwara Eliya to Ella\",\"activities\":[\"Scenic drive to Ella\",\"Nine Arch Bridge\",\"Check-in at Ella\"]},{\"day\":4,\"title\":\"Ella \\u2013 Little Adam\'s Peak & viewpoints\",\"activities\":[\"Little Adam\'s Peak hike\",\"Ella Rock or viewpoints\",\"Relax in Ella\"]},{\"day\":5,\"title\":\"Ella \\u2013 Tea country & train experience\",\"activities\":[\"Tea factory tour\",\"Scenic train stretch (optional)\",\"Leisure in Ella\"]},{\"day\":6,\"title\":\"Departure\",\"activities\":[\"Transfer to Colombo airport\",\"Departure\"]}]', 'Nuwara Eliya', 'Nuwara Eliya, Ella, Horton Plains', '[\"Arrival at Colombo and transfer to Nuwara Eliya, the misty tea-country highlands.\",\"Explore Nuwara Eliya: tea estates, Gregory Lake; optional Horton Plains.\",\"Drive to Ella \\u2014 Nine Arch Bridge, Little Adam\'s Peak, scenic train stretch.\",\"Hill country exploration \\u2014 tea factory, cool climate, mountain views.\",\"Departure to Colombo as per schedule.\"]', 41999, 0.50, 0.00, 4.8, 142, 'Hill Country Escape', 0, '2026-04-03 20:52:09'),
('[{\"nights\":2,\"location\":\"Anuradhapura\",\"hotel\":\"Rajarata Hotel, Anuradhapura\"},{\"nights\":1,\"location\":\"Dambulla \\/ Sigiriya\",\"hotel\":\"Heritage Hotel, Sigiriya\"}]', 'heritage', '2026-02-14 15:31:21', '4 Days 3 Nights', '4 Days / 3 Nights', '[\"International flights\",\"Lunches and beverages\",\"Personal expenses\",\"Visa\",\"Travel insurance\"]', '[{\"icon\":\"hotel\",\"title\":\"Accommodation\",\"desc\":\"3 nights stay\"},{\"icon\":\"transfer\",\"title\":\"Transfers\",\"desc\":\"Private cab for all transfers\"},{\"icon\":\"sightseeing\",\"title\":\"Sightseeing\",\"desc\":\"Anuradhapura, Dambulla, Sigiriya\\/Polonnaruwa\"},{\"icon\":\"meals\",\"title\":\"Meals\",\"desc\":\"Breakfast & dinner included\"},{\"icon\":\"activities\",\"title\":\"Activities\",\"desc\":\"Temples, stupas, heritage sites\"},{\"icon\":\"support\",\"title\":\"Support\",\"desc\":\"24x7 travel assistance\"}]', 4, '/CeylonGo/public/images/perehara.jpeg', '[\"Airport transfers\",\"3 nights accommodation\",\"Half Board\",\"Entrance fees to heritage sites\",\"Private cab\"]', '[{\"day\":1,\"title\":\"Arrival \\u2013 Colombo to Anuradhapura\",\"activities\":[\"Airport pick-up and transfer to Anuradhapura\",\"Check-in\",\"Evening visit to sacred city or rest\"]},{\"day\":2,\"title\":\"Anuradhapura \\u2013 Ancient capital\",\"activities\":[\"Anuradhapura sacred city \\u2013 temples and stupas\",\"Sri Maha Bodhi, Ruwanwelisaya\",\"Return to hotel\"]},{\"day\":3,\"title\":\"Dambulla & Sigiriya or Polonnaruwa\",\"activities\":[\"Dambulla Cave Temple\",\"Sigiriya Rock Fortress or Polonnaruwa ancient city\",\"Return to Anuradhapura\"]},{\"day\":4,\"title\":\"Departure\",\"activities\":[\"Transfer to Colombo airport\",\"Departure\"]}]', 'Anuradhapura', 'Anuradhapura, Dambulla, Sigiriya', '[\"Arrival at Colombo and transfer to Anuradhapura or the Cultural Triangle.\",\"Explore ancient capitals \\u2014 temples, stupas, and sacred sites.\",\"Dambulla Cave Temple and Sigiriya or Polonnaruwa.\",\"Departure to Colombo as per schedule.\"]', 34999, 0.50, 0.00, 4.6, 98, 'Ancient Heritage Trail', 1, '2026-04-03 20:52:09'),
('[{\"nights\":2,\"location\":\"Yala \\/ Tissamaharama\",\"hotel\":\"Cinnamon Wild Yala\"},{\"nights\":1,\"location\":\"Udawalawe\",\"hotel\":\"Elephant Reach, Udawalawe\"}]', 'safari', '2026-02-14 15:31:21', '4 Days 3 Nights', '4 Days / 3 Nights', '[\"International flights\",\"Lunches and beverages\",\"Personal expenses\",\"Visa\",\"Travel insurance\"]', '[{\"icon\":\"hotel\",\"title\":\"Accommodation\",\"desc\":\"3 nights near parks\"},{\"icon\":\"transfer\",\"title\":\"Transfers\",\"desc\":\"Private cab for all transfers\"},{\"icon\":\"sightseeing\",\"title\":\"Safari\",\"desc\":\"Yala and Udawalawe National Parks\"},{\"icon\":\"meals\",\"title\":\"Meals\",\"desc\":\"Breakfast & dinner included\"},{\"icon\":\"activities\",\"title\":\"Activities\",\"desc\":\"Game drives, wildlife viewing\"},{\"icon\":\"support\",\"title\":\"Support\",\"desc\":\"24x7 travel assistance\"}]', 5, '/CeylonGo/public/images/elephant.jpg', '[\"Airport transfers\",\"3 nights accommodation\",\"Half Board\",\"Safari jeep and park fees (as per itinerary)\",\"Private cab\"]', '[{\"day\":1,\"title\":\"Arrival \\u2013 Colombo to Yala \\/ Tissamaharama\",\"activities\":[\"Airport pick-up and transfer to Yala or Tissamaharama\",\"Check-in\",\"Evening at leisure\"]},{\"day\":2,\"title\":\"Yala National Park \\u2013 Safari\",\"activities\":[\"Early morning or afternoon game drive in Yala\",\"Leopards, elephants, birdlife\",\"Return to hotel\"]},{\"day\":3,\"title\":\"Udawalawe National Park \\u2013 Safari\",\"activities\":[\"Transfer to Udawalawe\",\"Safari in Udawalawe \\u2013 elephants and wildlife\",\"Overnight near Udawalawe or return to Yala\"]},{\"day\":4,\"title\":\"Departure\",\"activities\":[\"Transfer to Colombo airport\",\"Departure\"]}]', 'Yala', 'Yala, Udawalawe', '[\"Arrival at Colombo and transfer to Yala or the safari region.\",\"Safari in Yala National Park \\u2014 leopards, elephants, birdlife.\",\"Udawalawe or second safari \\u2014 wildlife and nature.\",\"Departure to Colombo as per schedule.\"]', 42999, 0.50, 0.00, 4.7, 176, 'Wildlife Safari', 0, '2026-04-03 20:52:09'),
('[{\"nights\":2,\"location\":\"Nuwara Eliya\",\"hotel\":\"Tea Factory Hotel, Nuwara Eliya\"},{\"nights\":3,\"location\":\"Ella\",\"hotel\":\"Mountain Heavens, Ella\"}]', 'solo', '2026-02-14 15:31:21', '6 Days 5 Nights', '6 Days / 5 Nights', '[\"International flights\",\"Lunches and beverages\",\"Personal expenses\",\"Visa\",\"Travel insurance\"]', '[{\"icon\":\"hotel\",\"title\":\"Accommodation\",\"desc\":\"5 nights in hill country\"},{\"icon\":\"transfer\",\"title\":\"Transfers\",\"desc\":\"Train + cab as per itinerary\"},{\"icon\":\"sightseeing\",\"title\":\"Sightseeing\",\"desc\":\"Ella, Nine Arch Bridge, Little Adam\'s Peak\"},{\"icon\":\"meals\",\"title\":\"Meals\",\"desc\":\"Breakfast & dinner included\"},{\"icon\":\"activities\",\"title\":\"Activities\",\"desc\":\"Scenic train, hiking, tea country\"},{\"icon\":\"support\",\"title\":\"Support\",\"desc\":\"24x7 travel assistance\"}]', 6, '/CeylonGo/public/images/train.jpg', '[\"Airport transfers\",\"5 nights accommodation\",\"Half Board\",\"Train ticket\",\"Private cab where needed\"]', '[{\"day\":1,\"title\":\"Arrival \\u2013 Colombo to Kandy \\/ Nuwara Eliya by train or road\",\"activities\":[\"Airport pick-up\",\"Scenic train from Colombo to Kandy or transfer by road\",\"Check-in at Kandy or Nuwara Eliya\"]},{\"day\":2,\"title\":\"Train \\/ road to Ella\",\"activities\":[\"Scenic train to Ella (or drive)\",\"Check-in at Ella\",\"Evening at leisure\"]},{\"day\":3,\"title\":\"Ella \\u2013 Nine Arch Bridge & Little Adam\'s Peak\",\"activities\":[\"Nine Arch Bridge\",\"Little Adam\'s Peak hike\",\"Relax in Ella\"]},{\"day\":4,\"title\":\"Ella \\u2013 Tea country & viewpoints\",\"activities\":[\"Tea factory or plantation visit\",\"Ella Rock or viewpoints\",\"Leisure in Ella\"]},{\"day\":5,\"title\":\"Ella \\u2013 Extra day or Nuwara Eliya\",\"activities\":[\"Optional day trip to Nuwara Eliya or rest in Ella\",\"Last evening in hill country\"]},{\"day\":6,\"title\":\"Departure\",\"activities\":[\"Transfer to Colombo airport\",\"Departure\"]}]', 'Ella', 'Colombo, Nuwara Eliya, Ella', '[\"Arrival at Colombo; begin journey to hill country by train or road.\",\"Scenic train to Ella \\u2014 tea country, Nine Arch Bridge, Little Adam\'s Peak.\",\"Explore Ella and surrounds \\u2014 hiking, viewpoints, relaxed pace.\",\"Hill country discovery \\u2014 Nuwara Eliya or Kandy option.\",\"Departure to Colombo as per schedule.\"]', 32999, 0.50, 0.00, 4.4, 89, 'Solo Explorer', 1, '2026-04-13 17:43:02'),
('[{\"nights\":3,\"location\":\"Bentota\",\"hotel\":\"Avani Bentota Resort & Spa\"},{\"nights\":1,\"location\":\"Colombo\",\"hotel\":\"Marino Beach Colombo\"}]', 'family', '2026-02-14 15:31:21', '5 Days 4 Nights', '5 Days / 4 Nights', '[\"International flights\",\"Lunches and beverages\",\"Water sports (optional)\",\"Personal expenses\",\"Visa\",\"Travel insurance\"]', '[{\"icon\":\"hotel\",\"title\":\"Accommodation\",\"desc\":\"4 nights (Bentota + Colombo)\"},{\"icon\":\"transfer\",\"title\":\"Transfers\",\"desc\":\"Private cab for all transfers\"},{\"icon\":\"sightseeing\",\"title\":\"Sightseeing\",\"desc\":\"Bentota beach, Colombo city\"},{\"icon\":\"meals\",\"title\":\"Meals\",\"desc\":\"Breakfast & dinner included\"},{\"icon\":\"activities\",\"title\":\"Activities\",\"desc\":\"Beach, turtle hatchery, family activities\"},{\"icon\":\"support\",\"title\":\"Support\",\"desc\":\"24x7 travel assistance\"}]', 7, '/CeylonGo/public/images/resort.jpg', '[\"Airport transfers\",\"4 nights accommodation\",\"Half Board\",\"Turtle hatchery visit\",\"Private cab\"]', '[{\"day\":1,\"title\":\"Arrival \\u2013 Colombo to Bentota\",\"activities\":[\"Airport pick-up and transfer to Bentota\",\"Check-in at resort\",\"Beach time\"]},{\"day\":2,\"title\":\"Bentota \\u2013 Beach & turtle hatchery\",\"activities\":[\"Beach activities\",\"Turtle hatchery visit\",\"Optional water sports\",\"Resort leisure\"]},{\"day\":3,\"title\":\"Bentota \\u2013 Family day\",\"activities\":[\"Resort activities\",\"Beach or pool\",\"Optional boat ride on Madu Ganga\"]},{\"day\":4,\"title\":\"Bentota to Colombo\",\"activities\":[\"Transfer to Colombo\",\"Colombo city tour \\u2013 markets, Galle Face, or shopping\",\"Overnight in Colombo\"]},{\"day\":5,\"title\":\"Departure\",\"activities\":[\"Transfer to Colombo airport\",\"Departure\"]}]', 'Bentota', 'Bentota, Colombo', '[\"Arrival at Colombo and transfer to Bentota, the family-friendly beach strip.\",\"Explore Bentota \\u2014 beach, water sports (optional), turtle hatchery.\",\"Colombo day \\u2014 city sights, markets, or leisure.\",\"Family time \\u2014 beach and resort activities.\",\"Departure to Colombo as per schedule.\"]', 44999, 0.50, 0.00, 4.5, 124, 'Family Fun', 0, '2026-04-03 20:52:09'),
('[{\"nights\":2,\"location\":\"Hikkaduwa\",\"hotel\":\"Coral Sands Hotel, Hikkaduwa\"}]', 'beach', '2026-02-14 15:31:21', '3 Days 2 Nights', '3 Days / 2 Nights', '[\"International flights\",\"Lunches and beverages\",\"Water sports and diving (optional)\",\"Personal expenses\",\"Visa\",\"Travel insurance\"]', '[{\"icon\":\"hotel\",\"title\":\"Accommodation\",\"desc\":\"2 nights beach stay\"},{\"icon\":\"transfer\",\"title\":\"Transfers\",\"desc\":\"Private cab for all transfers\"},{\"icon\":\"sightseeing\",\"title\":\"Sightseeing\",\"desc\":\"Hikkaduwa, Unawatuna beaches\"},{\"icon\":\"meals\",\"title\":\"Meals\",\"desc\":\"Breakfast & dinner included\"},{\"icon\":\"activities\",\"title\":\"Activities\",\"desc\":\"Beach, snorkelling (optional)\"},{\"icon\":\"support\",\"title\":\"Support\",\"desc\":\"24x7 travel assistance\"}]', 8, '/CeylonGo/public/images/sunset.jpg', '[\"Airport transfers\",\"2 nights accommodation\",\"Half Board\",\"Private cab\"]', '[{\"day\":1,\"title\":\"Arrival \\u2013 Colombo to Hikkaduwa\",\"activities\":[\"Airport pick-up and transfer to Hikkaduwa\",\"Check-in\",\"Beach time and coral viewing\"]},{\"day\":2,\"title\":\"Hikkaduwa to Unawatuna (or stay Hikkaduwa)\",\"activities\":[\"Transfer to Unawatuna or full day at Hikkaduwa\",\"Beach, optional snorkelling or diving\",\"Sunset at beach\"]},{\"day\":3,\"title\":\"Departure\",\"activities\":[\"Transfer to Colombo airport\",\"Departure\"]}]', 'Hikkaduwa', 'Hikkaduwa, Unawatuna', '[\"Arrival at Colombo and transfer to Hikkaduwa or Unawatuna.\",\"Beach time at Hikkaduwa \\u2014 coral, beach, optional water sports.\",\"Unawatuna \\u2014 beach, diving or relaxation. Departure to Colombo.\"]', 29999, 0.50, 0.00, 4.3, 67, 'Beach Getaway', 0, '2026-04-03 20:52:09');

-- --------------------------------------------------------

--
-- Table structure for table `package_bookings`
--

DROP TABLE IF EXISTS `package_bookings`;
CREATE TABLE IF NOT EXISTS `package_bookings` (
  `admin_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Admin notes or comments',
  `adults` int UNSIGNED NOT NULL DEFAULT '1',
  `approved_at` datetime DEFAULT NULL COMMENT 'When booking was approved',
  `approved_by` int UNSIGNED DEFAULT NULL COMMENT 'Admin user ID who approved',
  `bank_transfer_slip_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Relative path under public/uploads (e.g. bank_slips/booking_1_....jpg)',
  `bank_transfer_submitted_at` datetime DEFAULT NULL COMMENT 'Tourist clicked Continue after bank transfer; awaiting manual verification',
  `children` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Customer email',
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Customer full name',
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `infants` int UNSIGNED NOT NULL DEFAULT '0',
  `package_id` int UNSIGNED NOT NULL COMMENT 'Package ID',
  `package_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Package name at time of booking',
  `paid_at` datetime DEFAULT NULL,
  `payhere_payment_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Customer phone number',
  `refund_approved_at` datetime DEFAULT NULL,
  `refund_reason` varchar(2000) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Optional reason from tourist',
  `refund_reject_note` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refund_rejected_at` datetime DEFAULT NULL,
  `refund_requested_at` datetime DEFAULT NULL COMMENT 'When tourist submitted refund request',
  `special_requests` text COLLATE utf8mb4_unicode_ci COMMENT 'Special requests or notes',
  `status` enum('pending','approved','rejected','cancelled','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Total booking amount in LKR',
  `travel_date` date NOT NULL COMMENT 'Preferred travel date',
  `travelers` int UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Total number of travelers',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int UNSIGNED NOT NULL COMMENT 'Tourist user ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `package_bookings`
--

INSERT INTO `package_bookings` (`admin_notes`, `adults`, `approved_at`, `approved_by`, `bank_transfer_slip_path`, `bank_transfer_submitted_at`, `children`, `created_at`, `email`, `fullname`, `id`, `infants`, `package_id`, `package_name`, `paid_at`, `payhere_payment_id`, `phone`, `refund_approved_at`, `refund_reason`, `refund_reject_note`, `refund_rejected_at`, `refund_requested_at`, `special_requests`, `status`, `total_amount`, `travel_date`, `travelers`, `updated_at`, `user_id`) VALUES
('', 2, '2026-04-04 01:58:24', 1, NULL, '2026-04-04 02:12:28', 1, '2026-02-21 12:08:49', 'dee@gmail.com', 'Dee Gagan', 1, 0, 5, 'Wildlife Safari', NULL, NULL, '0789145722', NULL, NULL, NULL, NULL, NULL, '', 'approved', 412500.00, '2026-03-17', 3, '2026-04-03 20:42:28', 12),
('', 2, '2026-02-24 11:59:48', 2, NULL, NULL, 0, '2026-02-21 12:10:14', 'kalum@gmail.com', 'Kalum Fernando', 2, 0, 2, 'Southern Coast Honeymoon: Galle, Mirissa & Unawatuna 4N/5D', NULL, NULL, '0745698321', NULL, NULL, NULL, NULL, NULL, '', 'approved', 370000.00, '2026-03-25', 2, '2026-02-24 06:29:48', 16),
('', 4, '2026-04-04 01:58:12', 1, NULL, NULL, 0, '2026-02-24 06:17:55', 'dee@gmail.com', 'Dee Gagan', 3, 0, 1, 'Cultural Triangle 4N5D — Kandy, Sigiriya & Dambulla', '2026-04-06 18:53:16', 'sandbox-empty-return-b3ae773a039b9a5b', '0789145722', NULL, NULL, NULL, NULL, NULL, '', 'approved', 500000.00, '2026-03-25', 4, '2026-04-03 20:28:12', 12),
(' | Refund approved by admin on 2026-04-13 05:42:35', 1, '2026-04-02 01:41:45', 2, NULL, NULL, 0, '2026-04-01 19:54:29', 'dee@gmail.com', 'Dee Gagan', 4, 0, 1, 'Cultural Triangle 4N5D — Kandy, Sigiriya & Dambulla', '2026-04-04 01:46:39', 'sandbox-empty-return-275378c070c08a1f', '0789145722', '2026-04-13 05:42:35', 'I have other plans on the trip dates', NULL, NULL, '2026-04-04 19:21:01', '', 'cancelled', 50000.00, '2026-04-23', 1, '2026-04-13 00:12:35', 12),
('', 1, '2026-04-04 01:56:40', 2, 'bank_slips/booking_5_ada6d4f934c15517.webp', '2026-04-04 20:09:43', 0, '2026-04-01 21:46:59', 'dee@gmail.com', 'Dee Gagan', 5, 0, 1, 'Cultural Triangle 4N5D — Kandy, Sigiriya & Dambulla', '2026-04-07 08:17:45', NULL, '0789145722', NULL, NULL, NULL, NULL, NULL, '', 'paid', 50000.00, '2026-04-23', 1, '2026-04-07 02:47:45', 12),
('', 2, '2026-04-04 01:56:28', 1, NULL, NULL, 0, '2026-04-03 20:22:07', 'dee@gmail.com', 'Dee Gagan', 6, 0, 2, 'Southern Coast Honeymoon: Galle, Mirissa & Unawatuna 4N/5D', NULL, NULL, '0789145722', NULL, NULL, NULL, NULL, NULL, '', 'approved', 370000.00, '2026-05-01', 2, '2026-04-03 20:26:28', 12),
(NULL, 1, NULL, NULL, NULL, NULL, 0, '2026-04-03 20:52:53', 'dee@gmail.com', 'Dee Gagan', 7, 0, 1, 'Cultural Triangle 4N5D — Kandy, Sigiriya & Dambulla', NULL, NULL, '0789145722', NULL, NULL, NULL, NULL, NULL, '', 'pending', 23999.00, '2026-04-30', 1, '2026-04-03 20:52:53', 12),
('', 2, '2026-04-07 08:16:32', 2, NULL, NULL, 0, '2026-04-03 20:58:49', 'kalum@gmail.com', 'Kalum Fernando', 8, 0, 3, 'Hill Country Escape', NULL, NULL, '0745698321', NULL, NULL, NULL, NULL, NULL, '', 'approved', 83998.00, '2026-05-01', 2, '2026-04-07 02:46:32', 16),
(' | Refund approved by admin on 2026-04-17 21:59:45 | Refund rejected by admin on 2026-04-17 23:00:39: Unavoidable reason', 1, '2026-04-17 21:57:47', 1, NULL, NULL, 0, '2026-04-17 16:27:21', 'saubhagyaanethmini@gmail.com', 'Sadun Ekala', 9, 0, 6, 'Solo Explorer', '2026-04-17 21:59:00', 'sandbox-empty-return-a05f2fc45cf25bd9', '0789145725', NULL, 'Floods', 'Unavoidable reason', '2026-04-17 23:00:39', '2026-04-17 21:59:17', '', 'cancelled', 32999.00, '2026-05-28', 1, '2026-04-17 17:30:39', 21),
(' | Refund approved by admin on 2026-04-17 23:00:02', 1, '2026-04-17 22:01:06', 2, NULL, NULL, 0, '2026-04-17 16:30:41', 'saubhagyaanethmini@gmail.com', 'Sadun Ekala', 10, 0, 6, 'Solo Explorer', '2026-04-17 22:01:35', 'sandbox-empty-return-2765e0df04fd470a', '0789145725', '2026-04-17 23:00:02', 'emergency', NULL, NULL, '2026-04-17 22:01:47', 'Looking forward for a nice trip!', 'cancelled', 32999.00, '2026-05-13', 1, '2026-04-17 17:30:02', 21),
('', 1, '2026-04-06 21:21:25', 1, NULL, NULL, 0, '2026-04-06 15:50:09', 'kalum@gmail.com', 'Kalum Fernando', 11, 0, 1, 'Cultural Triangle 4N5D — Kandy, Sigiriya & Dambulla', NULL, NULL, '0745698321', NULL, NULL, NULL, NULL, NULL, 'hoping for a safe trip!', 'approved', 23999.00, '2026-04-29', 1, '2026-04-06 15:51:25', 16),
(NULL, 1, NULL, NULL, NULL, NULL, 0, '2026-04-06 19:29:30', 'kalum@gmail.com', 'Kalum Fernando', 12, 0, 6, 'Solo Explorer', NULL, NULL, '0745698321', NULL, NULL, NULL, NULL, NULL, 'No', 'pending', 32999.00, '2026-05-01', 1, '2026-04-06 19:29:30', 16),
('', 1, '2026-04-07 08:43:01', 1, NULL, NULL, 0, '2026-04-07 03:12:18', 'dee@gmail.com', 'Dee Gagan', 13, 0, 7, 'Family Fun', '2026-04-07 08:43:52', 'sandbox-empty-return-0bd523ca1c7300bc', '0789145722', NULL, NULL, NULL, NULL, NULL, '', 'paid', 44999.00, '2026-04-28', 1, '2026-04-07 03:13:52', 12),
('', 1, '2026-04-07 08:46:23', 1, 'bank_slips/booking_14_1f12f5b62fe1648a.jpg', '2026-04-07 08:46:59', 0, '2026-04-07 03:15:23', 'dee@gmail.com', 'Dee Gagan', 14, 0, 3, 'Hill Country Escape', NULL, NULL, '0789145722', NULL, NULL, NULL, NULL, NULL, '', 'approved', 41999.00, '2026-04-29', 1, '2026-04-07 03:16:59', 12),
('', 2, '2026-04-07 09:14:07', 1, NULL, NULL, 0, '2026-04-07 03:43:37', 'sau@gmail.com', 'Nethmini Saubhagya', 15, 0, 1, 'Cultural Triangle 4N5D — Kandy, Sigiriya & Dambulla', '2026-04-07 09:15:20', 'sandbox-empty-return-abf2f04f2fd1de09', '0714254872', NULL, 'im not free those days', NULL, NULL, '2026-04-07 09:15:41', '', 'paid', 47998.00, '2026-04-30', 2, '2026-04-07 03:45:41', 11),
('', 2, '2026-04-07 13:20:02', 1, NULL, NULL, 0, '2026-04-07 07:49:44', 'sau@gmail.com', 'Nethmini Saubhagya', 16, 0, 1, 'Cultural Triangle 4N5D — Kandy, Sigiriya & Dambulla', '2026-04-07 13:20:41', 'sandbox-empty-return-fa420f520ea0feb8', '0714254872', NULL, 'im not free this day', NULL, NULL, '2026-04-07 13:21:09', '', 'paid', 47998.00, '2026-04-30', 2, '2026-04-07 07:51:09', 11);

-- --------------------------------------------------------

--
-- Table structure for table `package_reviews`
--

DROP TABLE IF EXISTS `package_reviews`;
CREATE TABLE IF NOT EXISTS `package_reviews` (
  `admin_reply` text COLLATE utf8mb4_unicode_ci,
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `destination` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Package title snapshot at submit',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `package_id` int UNSIGNED NOT NULL,
  `rating` tinyint NOT NULL,
  `replied_at` datetime DEFAULT NULL,
  `review_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `package_reviews`
--

INSERT INTO `package_reviews` (`admin_reply`, `approved_at`, `created_at`, `destination`, `email`, `id`, `name`, `package_id`, `rating`, `replied_at`, `review_text`, `status`, `updated_at`, `user_id`) VALUES
(NULL, NULL, '2026-04-04 19:41:52', 'Cultural Triangle 4N5D — Kandy, Sigiriya & Dambulla', 'dee@gmail.com', 1, 'Dee Gagan', 1, 4, NULL, 'good trip recommended!', 'pending', '2026-04-04 19:41:52', 12),
(NULL, NULL, '2026-04-04 19:45:02', 'Cultural Triangle 4N5D — Kandy, Sigiriya & Dambulla', 'dee@gmail.com', 2, 'Dee Gagan', 1, 4, NULL, 'nice one', 'pending', '2026-04-04 19:45:02', 12),
(NULL, NULL, '2026-04-04 19:49:08', 'Cultural Triangle 4N5D — Kandy, Sigiriya & Dambulla', 'dee@gmail.com', 3, 'Dee Gagan', 1, 3, NULL, 'wooow', 'pending', '2026-04-04 19:49:08', 12),
(NULL, NULL, '2026-04-04 19:49:54', 'Cultural Triangle 4N5D — Kandy, Sigiriya & Dambulla', 'dee@gmail.com', 4, 'Dee Gagan', 1, 2, NULL, 'need improvement', 'pending', '2026-04-04 19:49:54', 12),
(NULL, NULL, '2026-04-04 19:52:23', 'Cultural Triangle 4N5D — Kandy, Sigiriya & Dambulla', 'dee@gmail.com', 5, 'Dee Gagan', 1, 5, NULL, 'trip was well planned', 'pending', '2026-04-04 19:52:23', 12);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `destination` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` int NOT NULL,
  `review_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_reply` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'pending' COMMENT 'pending, approved, rejected',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `replied_at` datetime DEFAULT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`created_at`, `destination`, `email`, `id`, `name`, `rating`, `review_text`, `admin_reply`, `status`, `updated_at`, `replied_at`, `user_id`) VALUES
('2025-10-20 12:21:59', 'Kandy', 'kandauda91@gmail.com', 1, 'Abhijeeth Kandauda', 4, 'good', NULL, 'pending', '2025-10-20 12:21:59', NULL, 6),
('2026-04-04 19:31:43', 'Cultural Triangle 4N5D — Kandy, Sigiriya & Dambulla', 'dee@gmail.com', 2, 'Dee Gagan', 4, 'it was greatbad', NULL, 'pending', '2026-04-04 19:31:43', NULL, 12),
('2026-04-04 19:32:18', 'Cultural Triangle 4N5D — Kandy, Sigiriya & Dambulla', 'dee@gmail.com', 3, 'Dee Gagan', 3, 'nice trip', NULL, 'pending', '2026-04-04 19:32:18', NULL, 12),
('2026-04-04 19:35:34', 'Cultural Triangle 4N5D — Kandy, Sigiriya & Dambulla', 'dee@gmail.com', 4, 'Dee Gagan', 5, 'my friends really enjoyed this', NULL, 'pending', '2026-04-04 19:35:34', NULL, 12);

-- --------------------------------------------------------

--
-- Table structure for table `tourist_guide_requests`
--

DROP TABLE IF EXISTS `tourist_guide_requests`;
CREATE TABLE IF NOT EXISTS `tourist_guide_requests` (
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `customerName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `language` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `requested_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','accepted','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `time` time NOT NULL,
  `tourist_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tourist_guide_requests`
--

INSERT INTO `tourist_guide_requests` (`created_at`, `customerName`, `date`, `id`, `language`, `location`, `notes`, `requested_location`, `status`, `time`, `tourist_name`) VALUES
('2026-01-05 04:22:21', 'Dee Gagan', '2026-01-24', 1, 'Sinhala', 'Nuwara Eliya', '', '', NULL, '00:00:00', ''),
('2026-01-05 04:22:30', 'Dee Gagan', '2026-01-22', 2, 'English', 'Nuwara Eliya', '', '', NULL, '00:00:00', ''),
('2026-01-09 05:34:25', 'Dee Gagan', '2026-01-22', 3, 'Sinhala', 'Colombo', '', '', NULL, '00:00:00', ''),
('2026-01-09 08:44:10', 'Dee Gagan', '2026-01-15', 4, 'Sinhala', 'Nuwara Eliya', '', '', NULL, '00:00:00', ''),
('2026-01-09 08:44:19', 'Dee Gagan', '2026-01-14', 5, 'Sinhala', 'Nuwara Eliya', '', '', NULL, '00:00:00', '');

-- --------------------------------------------------------

--
-- Table structure for table `tourist_transport_requests`
--

DROP TABLE IF EXISTS `tourist_transport_requests`;
CREATE TABLE IF NOT EXISTS `tourist_transport_requests` (
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `customerName` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `dropoff_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dropoffLocation` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numPeople` int NOT NULL,
  `pickup_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pickupLocation` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pickupTime` time NOT NULL,
  `status` enum('pending','accepted','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `time` time NOT NULL,
  `tourist_id` int NOT NULL,
  `vehicle_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicleType` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tourist_transport_requests`
--

INSERT INTO `tourist_transport_requests` (`created_at`, `customerName`, `date`, `dropoff_location`, `dropoffLocation`, `id`, `notes`, `numPeople`, `pickup_location`, `pickupLocation`, `pickupTime`, `status`, `time`, `tourist_id`, `vehicle_type`, `vehicleType`) VALUES
('2025-10-17 11:02:17', 'Amali', '2025-10-23', '', 'Galle Fort', 1, 'Be punctual', 5, '', 'Bandaranayake Airport', '20:32:00', NULL, '00:00:00', 0, NULL, 'Car'),
('2025-10-20 20:37:18', 'Shevin', '2025-10-11', '', 'Colombo Museam', 7, '', 45, '', 'Jewing Colombo Hotel', '05:07:00', NULL, '00:00:00', 0, NULL, 'Tuk'),
('2025-10-22 11:54:21', 'Sadun', '2025-11-07', '', 'Negomboo Beach', 10, '', 8, '', 'UCSC', '20:24:00', NULL, '00:00:00', 0, NULL, 'Car'),
('2025-10-22 12:06:31', 'Arunod', '2025-10-24', '', 'Colombo Museam', 11, 'Be punctual', 5, '', 'Jewing Colombo Hotel', '21:36:00', NULL, '00:00:00', 0, NULL, 'Tuk'),
('2025-10-23 06:04:19', 'Queen', '2025-10-29', '', 'Negomboo Beach', 14, 'Tip of 1000', 4, '', 'UCSC', '14:34:00', NULL, '00:00:00', 0, NULL, 'Car'),
('2025-10-23 06:42:57', 'Shevin', '2025-10-24', '', 'Negomboo Beach', 15, '', 5, '', 'Jewing Colombo Hotel', '03:12:00', NULL, '00:00:00', 0, NULL, 'Tuk'),
('2026-01-04 20:33:57', 'Shevin', '2026-01-15', '', 'Galle Fort', 17, '', 10, '', 'Jewing Colombo Hotel', '06:07:00', NULL, '00:00:00', 0, NULL, 'Car'),
('2026-01-04 20:44:19', 'Binali', '2026-01-16', '', 'Colombo Museam', 18, '', 4, '', 'Jewing Colombo Hotel', '06:14:00', NULL, '00:00:00', 0, NULL, 'Tuk'),
('2026-01-05 03:41:16', 'Dee Gagan', '2026-01-16', '', 'Negomboo Beach', 19, '', 1, '', 'Jewing Colombo Hotel', '00:14:00', NULL, '00:00:00', 0, NULL, 'Tuk'),
('2026-01-05 03:54:36', 'Dee Gagan', '2026-01-14', '', 'Colombo Port', 20, '', 1, '', 'Jewing Colombo Hotel', '00:24:00', NULL, '00:00:00', 0, NULL, 'Car'),
('2026-01-05 04:00:26', 'Dee Gagan', '2026-01-20', '', 'Colombo Museam', 21, '', 1, '', 'Bandaranayake Airport', '01:30:00', NULL, '00:00:00', 0, NULL, 'Car'),
('2026-01-05 04:03:03', 'Dee Gagan', '2026-01-21', '', 'galle', 22, '', 1, '', 'Jewing Colombo Hotel', '11:32:00', NULL, '00:00:00', 0, NULL, 'Tuk'),
('2026-01-05 04:09:39', 'Dee Gagan', '2026-01-15', '', 'galle', 23, '', 1, '', 'Bandaranayake Airport', '01:39:00', NULL, '00:00:00', 0, NULL, 'Car'),
('2026-01-09 06:06:30', 'Dee Gagan', '2026-01-21', '', 'Negomboo Beach', 24, '', 1, '', 'Jewing Colombo Hotel', '02:36:00', NULL, '00:00:00', 0, NULL, 'Car'),
('2026-01-09 08:27:49', 'Dee Gagan', '2026-01-20', '', 'Colombo Museam', 25, '', 1, '', 'Bandaranayake Airport', '16:57:00', NULL, '00:00:00', 0, NULL, 'Tuk');

-- --------------------------------------------------------

--
-- Table structure for table `tourist_trip_bookings`
--

DROP TABLE IF EXISTS `tourist_trip_bookings`;
CREATE TABLE IF NOT EXISTS `tourist_trip_bookings` (
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guide_required` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `id` int NOT NULL AUTO_INCREMENT,
  `status` enum('draft','submitted','confirmed') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tourist_trip_destinations`
--

DROP TABLE IF EXISTS `tourist_trip_destinations`;
CREATE TABLE IF NOT EXISTS `tourist_trip_destinations` (
  `booking_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `days` int DEFAULT '1',
  `destination` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hotel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `id` int NOT NULL AUTO_INCREMENT,
  `people_count` int DEFAULT '1',
  `transport` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tourist_users`
--

DROP TABLE IF EXISTS `tourist_users`;
CREATE TABLE IF NOT EXISTS `tourist_users` (
  `contact_number` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tourist_users`
--

INSERT INTO `tourist_users` (`contact_number`, `created_at`, `email`, `first_name`, `id`, `is_active`, `last_name`, `password`) VALUES
('0714254877', '2025-10-17 08:43:46', 'tourist1@gmail.com', 'Nethmini', 1, 1, 'Saubhagya', '$2y$10$NuQqmwBQKWbEmOYuUId.s./vnqSpqCBQv/EW0B0U0bRo1pG6uY9ny'),
('0714254876', '2025-10-17 15:10:06', 'vinoli@gmail.com', 'Vinoli', 2, 1, 'Fernando', '$2y$10$Ya3t1MpzIumAQM/e3NfETul7IugSRTDjm.CtFA7DSsjp4dArkP4/W'),
('0756783422', '2025-10-19 07:38:02', 'gayan@gmail.com', 'Gayan', 4, 1, 'Perera', '$2y$10$YWrrP4WOWJysgnNTeFelguvEol46NHXUcBczlRbs/C2HvQ37inne6'),
('0714254876', '2025-10-20 05:38:42', 'hotel1@gmail.com', 'Nethmini', 5, 1, 'Saubhagya', '$2y$10$UQF.6SP5gWeF09gypxPk/eTMZNEflNc1AKsD9wdGxCBDYsXtTI7bq'),
('0978543216', '2025-10-20 06:18:26', 'kandauda91@gmail.com', 'abhijeeth', 6, 1, 'kandauda', '$2y$10$1xvot.y1R4j3jppZA2aoPuG2sUDbF4tCY48DoDStTIKaXVb8QtkMG'),
('0714254875', '2025-10-22 04:47:26', 'nirosha@gmail.com', 'Nirosha', 8, 1, 'Damayanthi', '$2y$10$b.HKtc3Fr0YVASIdeT2nO.06/Ue30gKpaqdfVUaBgegB6rz4lXtjO'),
('0714254870', '2025-10-22 08:26:56', 'dew@gmail.co', 'Dewmini', 9, 1, 'Sathsara', '$2y$10$oUaDHRPmfGvScPQVmEytv.kkedeDgDXr84R6NIGMNjVBf2AV9d082'),
('0714254872', '2025-10-22 08:34:26', 'sau@gmail.com', 'Nethmini', 11, 1, 'Saubhagya', '$2y$10$E9vQewZvDS5LMwTWbpg2XObV8VuSlDte0fGw.WIyObCkpb5fygBIu'),
('0789145722', '2025-10-22 08:39:00', 'dee@gmail.com', 'Dee', 12, 1, 'Gagan', '$2y$10$2XoYffJgN4Fwn0M33FaJde9sedqDul6JWJW1xVBFMoYIazqPnweIC'),
('0745698321', '2025-11-14 05:56:59', 'kalum@gmail.com', 'Kalum', 16, 1, 'Fernando', '$2y$10$IwPymMHQTHifELowRRa0i.2X2h/9QqQWr68aNRee6uBr5RMZ2ZQei'),
('0714254874', '2025-11-14 06:18:36', 'sandesh@gmail.com', 'sandesh', 17, 1, 'Perera', '$2y$10$F4/83mHHpCZVOdNmQs9LZOCleqr2UVQi96gNG4VVF8Vgr7BaVokcy'),
('0771234567', '2026-01-18 06:51:58', 'gagana@gmail.com', 'Gagana', 18, 1, 'User', '$2y$10$2blPFuKc3Gri69ua3LCKJOuW7RGsMVjftaDbTUQObedAaUNZ6o53i'),
('0789645231', '2026-01-18 07:19:20', 'abhi@gmail.com', 'Abhi', 19, 1, 'Kanda', '$2y$10$cFspHt/YdMVJk.UOa0g5XOJnSzPs8H9LYHIOHY2xFfGnvILRbR.Ie'),
('0714596351', '2026-04-17 16:05:20', 'saubhagyanethmini35@gmail.com', 'Nethmini', 20, 1, 'Saubhagya', '$2y$10$Nkm6GzYZy4ikwTJJTiWURuL6nY3DZ6rK0ov2ODSBC7lfI4HHjWJ7q'),
('0789145725', '2026-04-17 16:12:40', 'saubhagyaanethmini@gmail.com', 'Sadun', 21, 1, 'Ekala', '$2y$10$3X8KwEpSchzxRWszqeY.AuCpQktFjGDU3VQT.gg7ujH8x2WrBb7be'),
('0759632581', '2026-04-18 00:09:38', 'vinudilanya16@gmail.com', 'Vinuji', 22, 1, 'Dilanya', '$2y$10$XK/LwuHUe03/SkXI0kXlu.xzn236h3byYjdu8//iq4Wp/vb5hlRWm');

-- --------------------------------------------------------

--
-- Table structure for table `transport_license`
--

DROP TABLE IF EXISTS `transport_license`;
CREATE TABLE IF NOT EXISTS `transport_license` (
  `driver_id` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `license_exp_date` date NOT NULL,
  `license_no` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`license_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transport_license`
--

INSERT INTO `transport_license` (`driver_id`, `image`, `license_exp_date`, `license_no`) VALUES
('U68f2880eea1', 'img_68f2880f03ffc8.0', '2025-10-31', '098098'),
('U68f4b466a98', 'img_68f4b466ef70f6.5', '2026-01-14', '1234JJ'),
('U68f288b3a20', 'img_68f288b3b00bf2.5', '2025-11-07', '2345678'),
('U68f28688787', 'Screenshot 2025-09-0', '2025-10-31', '23457678'),
('U68f48c351e8', 'img_68f48c352cdcc7.9', '2027-11-08', '8763446'),
(' 68f9cc3ce84', 'img_68f9cc3d2967d0.5', '2025-11-07', 'AY-2345'),
('TP69d33ed552', '', '2029-02-04', 'B984453');

-- --------------------------------------------------------

--
-- Table structure for table `transport_provider_acc_details`
--

DROP TABLE IF EXISTS `transport_provider_acc_details`;
CREATE TABLE IF NOT EXISTS `transport_provider_acc_details` (
  `acc_holder_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `acc_no` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ref_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`ref_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transport_provider_acc_details`
--

INSERT INTO `transport_provider_acc_details` (`acc_holder_name`, `acc_no`, `bank_name`, `branch_name`, `ref_id`) VALUES
('Kaveesha Dulanjani', '23432130005643', 'people\'s bank', 'Dehiwala', '68f9cc3ce65'),
('Sachith Anuranga', '8873648729', 'commercial bank', 'Ja ela ', 'TP69d33ed552');

-- --------------------------------------------------------

--
-- Table structure for table `transport_requests`
--

DROP TABLE IF EXISTS `transport_requests`;
CREATE TABLE IF NOT EXISTS `transport_requests` (
  `assigned_driver_id` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_vehicle_no` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `customer_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `distance` decimal(10,2) DEFAULT NULL,
  `dropoff_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estimated_fare` decimal(10,2) DEFAULT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `num_people` int NOT NULL,
  `pickup_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pickup_time` time NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int DEFAULT NULL,
  `vehicle_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transport_requests`
--

INSERT INTO `transport_requests` (`assigned_driver_id`, `assigned_vehicle_no`, `contact_number`, `created_at`, `customer_name`, `date`, `distance`, `dropoff_location`, `estimated_fare`, `id`, `notes`, `num_people`, `pickup_location`, `pickup_time`, `status`, `updated_at`, `user_id`, `vehicle_type`) VALUES
(NULL, NULL, '0789145722', '2026-02-01 04:54:52', 'Dee Gagan', '2026-02-01', 107.56, 'Galle Fort', 10756.06, 1, '0', 1, 'Jewing Colombo Hotel', '16:23:00', 'pending', '2026-02-01 04:54:52', 12, 'Tuk'),
(NULL, NULL, '0789145722', '2026-02-01 04:59:09', 'Dee Gagan', '2026-02-03', 107.56, 'Galle Fort', 10756.06, 2, '0', 1, 'Jewing Colombo Hotel', '16:28:00', 'pending', '2026-02-01 04:59:09', 12, 'Tuk'),
(NULL, NULL, '0789145722', '2026-02-01 05:03:11', 'Dee Gagan', '2026-02-13', 104.96, 'Galle', 10495.86, 3, '0', 1, 'Colombo', '10:36:00', 'pending', '2026-02-01 05:03:11', 12, 'Tuk'),
(NULL, NULL, '0789145722', '2026-02-01 05:05:53', 'Dee Gagan', '2026-02-13', 5.77, 'Negomboo Beach', 577.39, 4, 'Need a safe driver', 1, 'Bandaranayake Airport', '22:38:00', 'pending', '2026-02-01 05:05:53', 12, 'Minivan'),
(NULL, NULL, '0789145722', '2026-02-01 05:10:49', 'Dee Gagan', '2026-02-05', 104.96, 'galle', 10495.86, 5, '', 1, 'Jewing Colombo Hotel', '22:45:00', 'pending', '2026-02-01 05:10:49', 12, 'Tuk'),
(NULL, NULL, '0789145722', '2026-02-22 00:45:52', 'Dee Gagan', '2026-03-15', 6.57, 'Boralesgamuwa, Sri Lanka', 788.76, 6, NULL, 4, 'Havelock City Mall, Havelock Road, Colombo, Sri Lanka', '07:15:00', 'pending', '2026-02-22 00:45:52', 12, 'Car'),
(NULL, NULL, '0745698321', '2026-02-22 07:02:18', 'Kalum Fernando', '2026-03-15', 34.73, 'Galle Face, Colombo, Sri Lanka', 4167.00, 7, NULL, 4, 'Bandaranayake Airport Transit Guest House, Katunayake, Sri Lanka', '12:34:00', 'pending', '2026-02-22 07:02:18', 16, 'Car'),
(NULL, NULL, '0745698321', '2026-02-22 07:02:50', 'Kalum Fernando', '2026-03-15', NULL, 'Galle Face, Colombo, Sri Lanka', NULL, 8, NULL, 3, 'Bandaranayake Airport Transit Guest House, Katunayake, Sri Lanka', '12:34:00', 'pending', '2026-02-22 07:02:50', 16, 'Car'),
(NULL, NULL, '0745698321', '2026-02-22 07:10:54', 'Kalum Fernando', '2026-03-15', 93.21, 'H S Fabric Collection - Dehiwala, Galle Road, Dehiwala-Mount Lavinia, Sri Lanka', 7456.96, 9, NULL, 3, 'Nvshop, New Road, Ambalangoda, Sri Lanka', '14:39:00', 'pending', '2026-02-22 07:10:54', 16, 'Tuk'),
(NULL, NULL, '0745698321', '2026-02-22 07:13:44', 'Kalum Fernando', '2026-03-15', 225.43, 'Havelock City Mall, Havelock Road, Colombo, Sri Lanka', 18034.08, 10, NULL, 3, 'Hettipola, Sri Lanka', '14:43:00', 'pending', '2026-02-22 07:13:44', 16, 'Tuk'),
(NULL, NULL, '0745698321', '2026-02-22 07:14:03', 'Kalum Fernando', '2026-03-15', 225.43, 'Havelock City Mall, Havelock Road, Colombo, Sri Lanka', 27051.12, 11, NULL, 4, 'Hettipola, Sri Lanka', '14:43:00', 'pending', '2026-02-22 07:14:03', 16, 'Car'),
(NULL, NULL, '0789145722', '2026-03-02 10:36:52', 'Dee Gagan', '2026-03-23', 7.01, 'mshopping, Avissawella Road, Kotikawatta, Sri Lanka', 560.80, 13, NULL, 2, 'BZL Elevators & Escalator (Pvt) Ltd, Maradana Road, Colombo, Sri Lanka', '16:08:00', 'pending', '2026-03-02 10:36:52', 12, 'Tuk'),
(NULL, NULL, '0789145722', '2026-04-04 21:35:10', 'Dee Gagan', '2026-04-26', 40.60, 'Mirissa Beach, Mirissa, Sri Lanka', 3247.84, 15, NULL, 2, 'Galle, Sri Lanka', '03:08:00', 'pending', '2026-04-04 21:35:10', 12, 'Tuk'),
(NULL, NULL, '0789145722', '2026-04-04 21:37:55', 'Dee Gagan', '2026-04-26', 40.69, 'Mirissa Beach, Mirissa, Sri Lanka', 6103.05, 16, NULL, 2, 'Galle Fort, Galle, Sri Lanka', '03:09:00', 'pending', '2026-04-04 21:37:55', 12, 'Minivan'),
(NULL, NULL, '0789145722', '2026-04-04 21:40:35', 'Dee Gagan', '2026-04-26', 525.92, 'Mirissa Beach, Mirissa, Sri Lanka', 78887.85, 17, NULL, 2, 'Jaffna, Sri Lanka', '03:12:00', 'pending', '2026-04-04 21:40:35', 12, 'Minivan'),
(NULL, NULL, '0789145722', '2026-04-04 21:45:44', 'Dee Gagan', '2026-04-26', 2.00, 'Kandy City Centre, Sri Wickrama Rajasinghe Mawatha, Kandy, Sri Lanka', 239.88, 18, NULL, 2, 'Banana Bunks Kandy, Aniwatta Circular Road, Kandy, Sri Lanka', '07:15:00', 'pending', '2026-04-04 21:45:44', 12, 'Car'),
(NULL, NULL, '0789145722', '2026-04-04 21:49:21', 'Dee Gagan', '2026-04-26', 105.12, 'Kandy City Centre, Sri Wickrama Rajasinghe Mawatha, Kandy, Sri Lanka', 12614.04, 19, NULL, 2, 'Hanwella, Sri Lanka', '03:21:00', 'pending', '2026-04-04 21:49:21', 12, 'Car'),
(NULL, NULL, '0789145722', '2026-04-04 21:52:06', 'Dee Gagan', '2026-04-26', 183.26, 'Mirissa Beach, Mirissa, Sri Lanka', 14660.48, 20, NULL, 2, 'Bandarawela, Sri Lanka', '03:23:00', 'pending', '2026-04-04 21:52:06', 12, 'Tuk'),
(NULL, NULL, '0789145722', '2026-04-04 21:55:29', 'Dee Gagan', '2026-04-26', 239.44, 'Kandy City Centre, Sri Wickrama Rajasinghe Mawatha, Kandy, Sri Lanka', 28732.20, 21, NULL, 2, 'Jaburagoda - Koledanda Road, Weligama, Sri Lanka', '03:28:00', 'pending', '2026-04-04 21:55:29', 12, 'Car'),
(NULL, NULL, '0789145722', '2026-04-04 22:02:16', 'Dee Gagan', '2026-04-26', 81.69, 'Jacobi carbons Lanka (PVT) Ltd.- Manufacturing Plant, Panangoda, Sri Lanka', 9802.44, 23, NULL, 8, 'Hemas Hospital - Thalawathugoda, Pannipitiya Road, Thalawathugoda, Sri Lanka', '06:31:00', 'pending', '2026-04-04 22:02:16', 12, 'Bus'),
(NULL, NULL, '0789145722', '2026-04-04 22:05:34', 'Dee Gagan', '2026-04-26', 70.01, 'Negombo Beach, Negombo, Sri Lanka', 10500.75, 24, NULL, 2, 'JNK Motors, Jaliyagoda, Piliyandala, Sri Lanka', '03:37:00', 'pending', '2026-04-04 22:05:34', 12, 'Minivan'),
(NULL, NULL, '0789145722', '2026-04-04 22:41:14', 'Dee Gagan', '2026-04-26', 129.65, 'Sri Lanka Navy Whale Watching, Galle, Sri Lanka', 10371.92, 25, NULL, 2, 'NBRO, Jawatta Road, Colombo, Sri Lanka', '04:14:00', 'pending', '2026-04-04 22:41:14', 12, 'Tuk'),
(NULL, NULL, '0789145722', '2026-04-04 22:42:49', 'Dee Gagan', '2026-04-26', 59.64, 'Sandares Floral Deco, Pannala Road, Dankotuwa, Sri Lanka', 8945.55, 26, NULL, 2, 'CSTH, Hospital Road, Dehiwala-Mount Lavinia, Sri Lanka', '04:14:00', 'pending', '2026-04-04 22:42:49', 12, 'Minivan'),
(NULL, NULL, '0789145722', '2026-04-05 10:35:51', 'Dee Gagan', '2026-04-26', 166.34, 'NZ Avissawela Land, Eheliyagoda-Dehiowita Road, Sri Lanka', 13307.12, 27, NULL, 2, 'Mzion Hotel Weligama, Samaraweera Place, Weligama, Sri Lanka', '16:07:00', 'pending', '2026-04-05 10:35:51', 12, 'Tuk'),
(NULL, NULL, '0789145722', '2026-04-05 10:42:55', 'Dee Gagan', '2026-04-26', 48.20, 'Kandy, Sri Lanka', 3855.84, 28, NULL, 2, 'Js home center, Matale, Sri Lanka', '16:15:00', 'pending', '2026-04-05 10:42:55', 12, 'Tuk'),
(NULL, NULL, '0789145722', '2026-04-05 11:13:09', 'Dee Gagan', '2026-04-26', 27.53, 'Kadawatha, Sri Lanka', 2202.16, 29, 'Be punctual', 1, 'Japan Karate Association of Sri Lanka, Ratmalana - Attidiya Road, Dehiwala-Mount Lavinia, Sri Lanka', '16:44:00', 'pending', '2026-04-05 11:13:09', 12, 'Tuk'),
(NULL, NULL, '0789145722', '2026-04-05 12:51:01', 'Dee Gagan', '2026-04-26', 284.38, 'M.A. Stores, Kalutara, Sri Lanka', 22750.32, 30, NULL, 2, 'M.M.A Stores Silmiyapura, Peradeniya-Badulla-Chenkaladi Highway, Boragasketiya, Sri Lanka', '18:22:00', 'pending', '2026-04-05 12:51:01', 12, 'Tuk'),
(NULL, NULL, '0789145722', '2026-04-06 07:28:04', 'Dee Gagan', '2026-04-27', 38.15, 'Negombo Beach, Negombo, Sri Lanka', 3052.08, 32, NULL, 1, 'Colombo Fort Station, Colombo, Sri Lanka', '12:59:00', 'pending', '2026-04-06 07:28:04', 12, 'Tuk'),
(NULL, NULL, '0789145722', '2026-04-06 07:37:12', 'Dee Gagan', '2026-04-27', 88.21, 'Nakalagamuwa land, Dampelessa, Sri Lanka', 10585.20, 33, NULL, 1, 'BBQ Station, Bernard Soysa Mawatha, Colombo, Sri Lanka', '13:09:00', 'pending', '2026-04-06 07:37:12', 12, 'Car'),
(NULL, NULL, '0789145722', '2026-04-06 08:30:43', 'Dee Gagan', '2026-04-27', 101.43, 'Negombo, Sri Lanka', 8114.24, 35, NULL, 1, 'Ceypetco Filling Station - සිපෙට්කෝ පිරවුම්හළ, B157, Neboda, Sri Lanka', '14:01:00', 'pending', '2026-04-06 08:30:43', 12, 'Tuk'),
(NULL, NULL, '0789145722', '2026-04-06 08:38:44', 'Dee Gagan', '2026-04-27', 140.83, 'Galle, Sri Lanka', 11266.32, 36, NULL, 1, 'B.Ariyadasa Mawatha, Wattala, Sri Lanka', '14:10:00', 'pending', '2026-04-06 08:38:44', 12, 'Tuk'),
(NULL, NULL, '0789145722', '2026-04-06 11:27:15', 'Dee Gagan', '2026-04-27', 264.62, 'Visakha Vidyalaya, Vajira Road, Colombo, Sri Lanka', 21169.68, 38, NULL, 2, 'Vavuniya, Sri Lanka', '19:57:00', 'pending', '2026-04-06 11:27:15', 12, 'Tuk'),
(NULL, NULL, '0789145722', '2026-04-06 11:34:31', 'Dee Gagan', '2026-04-27', 371.50, 'Sri Lanka Air force Trade Training School, Ja-Ela, Gampaha, Sri Lanka', 29719.76, 39, NULL, 2, 'Jaffna, Sri Lanka', '17:07:00', 'pending', '2026-04-06 11:34:31', 12, 'Tuk'),
(NULL, NULL, '0714254872', '2026-04-07 03:53:42', 'Nethmini Saubhagya', '2026-04-28', 128.95, 'Colombo Fort Station, Colombo, Sri Lanka', 10316.08, 40, NULL, 1, 'Sri Lanka Navy Whale Watching, Galle, Sri Lanka', '09:25:00', 'pending', '2026-04-07 03:53:42', 11, 'Tuk'),
(NULL, NULL, '0789145722', '2026-04-07 04:41:43', 'Dee Gagan', '2026-04-28', 5.02, 'Dehiwala, B94, Dehiwala-Mount Lavinia, Sri Lanka', 401.44, 41, NULL, 2, 'Kingsbridge American College, Abeyrathne Mawatha, Boralesgamuwa, Sri Lanka', '10:14:00', 'pending', '2026-04-07 04:41:43', 12, 'Tuk'),
(NULL, NULL, '0789145722', '2026-04-07 07:00:28', 'Dee Gagan', '2026-04-28', 221.30, 'Trinity College Kandy, A9, Kandy, Sri Lanka', 26556.48, 43, NULL, 2, 'JB VILLA, c bonavista, Unawatuna, Sri Lanka', '12:32:00', 'pending', '2026-04-07 07:00:28', 12, 'Car'),
(NULL, NULL, '0789145722', '2026-04-07 07:07:07', 'Dee Gagan', '2026-04-28', 85.85, 'Bentara - Uragaha - Elpitiya Road, Uragasmanhandiya, Sri Lanka', 10301.40, 44, NULL, 2, 'G W J Perera, Wijaya Rd, Piliyandala, Sri Lanka', '16:36:00', 'pending', '2026-04-07 07:07:07', 12, 'Car'),
(NULL, NULL, '0714254872', '2026-04-07 07:58:05', 'Nethmini Saubhagya', '2026-04-28', 221.40, 'Temple of the Sacred Tooth Relic, Kandy, Sri Lanka', 26568.36, 45, NULL, 2, 'Sri Lanka Bureau of Foreign Employment, District Office, Galle, Sri Lanka', '13:26:00', 'pending', '2026-04-07 07:58:05', 11, 'Car'),
(NULL, NULL, '0789145722', '2026-04-12 16:34:24', 'Dee Gagan', '2026-05-03', 117.02, 'HALEON, Galle Road, Moratuwa, Sri Lanka', 9361.28, 46, NULL, 1, 'KNAS Assocites, Kaduwela, Sri Lanka', '22:06:00', 'pending', '2026-04-12 16:34:24', 12, 'Tuk'),
(NULL, NULL, '0789145722', '2026-04-19 04:10:03', 'Dee Gagan', '2026-05-12', 133.15, 'Temple of the Sacred Tooth Relic, Kandy, Sri Lanka', 15978.48, 47, NULL, 1, 'Dehiwala, Dehiwala-Mount Lavinia, Sri Lanka', '09:39:00', 'pending', '2026-04-19 04:10:03', 12, 'Car'),
(NULL, NULL, '0789145722', '2026-04-19 04:10:26', 'Dee Gagan', '2026-05-12', 0.00, 'Temple of the Sacred Tooth Relic, Kandy, Sri Lanka', 15978.48, 48, 'Added via trip wizard submission (Trip #77)', 1, 'Dehiwala, Dehiwala-Mount Lavinia, Sri Lanka', '09:39:00', 'pending', '2026-04-19 04:10:26', 12, 'Car'),
(NULL, NULL, '0789145722', '2026-04-19 04:29:02', 'Dee Gagan', '2026-05-31', 115.13, 'Galle Fort, Galle, Sri Lanka', 13815.24, 49, NULL, 1, 'Dehiwala, Dehiwala-Mount Lavinia, Sri Lanka', '06:00:00', 'pending', '2026-04-19 04:29:02', 12, 'Car'),
('TP69d33ed552', 'KP-8872', '0789145722', '2026-04-19 04:29:13', 'Dee Gagan', '2026-05-31', 0.00, 'Galle Fort, Galle, Sri Lanka', 13815.24, 50, 'Added via trip wizard submission (Trip #78)', 1, 'Dehiwala, Dehiwala-Mount Lavinia, Sri Lanka', '06:00:00', 'confirmed', '2026-04-19 04:30:09', 12, 'Car'),
(NULL, NULL, '0789145722', '2026-04-19 04:43:35', 'Dee Gagan', '2026-05-22', 382.40, 'Arugam Bay Beach, Sri Lanka', 45887.64, 51, NULL, 1, 'Nugegoda Flyover, Nugegoda, Sri Lanka', '10:13:00', 'pending', '2026-04-19 04:43:35', 12, 'Car'),
('TP69d33ed552', 'KP-8872', '0789145722', '2026-04-19 04:44:20', 'Dee Gagan', '2026-05-22', 0.00, 'Arugam Bay Beach, Sri Lanka', 45887.64, 52, 'Added via trip wizard submission (Trip #79)', 1, 'Nugegoda Flyover, Nugegoda, Sri Lanka', '10:13:00', 'confirmed', '2026-04-19 04:45:38', 12, 'Car');

-- --------------------------------------------------------

--
-- Table structure for table `transport_request_trip_links`
--

DROP TABLE IF EXISTS `transport_request_trip_links`;
CREATE TABLE IF NOT EXISTS `transport_request_trip_links` (
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id` int NOT NULL AUTO_INCREMENT,
  `transport_request_id` int NOT NULL,
  `trip_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transport_request_trip_links`
--

INSERT INTO `transport_request_trip_links` (`created_at`, `id`, `transport_request_id`, `trip_id`, `user_id`) VALUES
('2026-04-06 11:34:54', 1, 39, 25, 12);

-- --------------------------------------------------------

--
-- Table structure for table `transport_users`
--

DROP TABLE IF EXISTS `transport_users`;
CREATE TABLE IF NOT EXISTS `transport_users` (
  `address` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_no` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dob` date NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `nic` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_image` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `psw` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transport_users`
--

INSERT INTO `transport_users` (`address`, `contact_no`, `dob`, `email`, `full_name`, `is_active`, `nic`, `profile_image`, `psw`, `user_id`) VALUES
('347, Niwandama, Ja-ela', '0716944635', '1997-09-09', 'sachi.anu@gmail.com', 'Sachith Anuranga', 1, '199788295v', 'profile_TP69d33ed552_1776572843.jpg', '$2y$10$9YehJqZRpEiXZtsH2AwYFO49/cz58QdDrWQ3PIfZONg97RpbokN5W', 'TP69d33ed552');

-- --------------------------------------------------------

--
-- Table structure for table `transport_vehicle`
--

DROP TABLE IF EXISTS `transport_vehicle`;
CREATE TABLE IF NOT EXISTS `transport_vehicle` (
  `image` varchar(48) COLLATE utf8mb4_unicode_ci NOT NULL,
  `psg_capacity` int NOT NULL,
  `user_id` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_no` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`vehicle_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transport_vehicle`
--

INSERT INTO `transport_vehicle` (`image`, `psg_capacity`, `user_id`, `vehicle_no`, `vehicle_type`) VALUES
('img_68f4b46700fb02.80913680.png', 3, 'U68f4b466a98', 'BA1234', '1'),
('img_68f9cc3d37dda9.01415511.png', 3, ' 68f9cc3ce84', 'BA1239', '1'),
('img_68f288b3b09129.8', 3, 'U68f288b3a20', 'ju8877', '1'),
('img_69e459937177a2.51823851.jfif', 4, 'TP69d33ed552', 'KP-8872', '2'),
('img_68f48c352dd631.69653958.jpg', 10, 'U68f48c351e8', 'PG-5432', '2'),
('img_68f480b7b36c52.25171860.webp', 3, 'U68f28688787', 'TY-5341', '1');

-- --------------------------------------------------------

--
-- Table structure for table `transport_vehicle_types`
--

DROP TABLE IF EXISTS `transport_vehicle_types`;
CREATE TABLE IF NOT EXISTS `transport_vehicle_types` (
  `type_id` int NOT NULL AUTO_INCREMENT,
  `type_name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transport_vehicle_types`
--

INSERT INTO `transport_vehicle_types` (`type_id`, `type_name`) VALUES
(1, 'TUK'),
(2, 'Car'),
(3, 'Minivan'),
(4, 'Minivan AC'),
(5, 'Bus'),
(6, 'Bus AC');

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

DROP TABLE IF EXISTS `trips`;
CREATE TABLE IF NOT EXISTS `trips` (
  `bank_transfer_slip_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_transfer_submitted_at` datetime DEFAULT NULL,
  `budget_lkr` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `customer_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destination` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `number_of_days` int NOT NULL,
  `number_of_people` int NOT NULL,
  `paid_at` datetime DEFAULT NULL,
  `payhere_payment_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refund_approved_at` datetime DEFAULT NULL,
  `refund_reason` varchar(2000) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Optional reason from tourist',
  `refund_reject_note` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refund_rejected_at` datetime DEFAULT NULL,
  `refund_requested_at` datetime DEFAULT NULL COMMENT 'When tourist submitted refund request',
  `start_date` date NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trips`
--

INSERT INTO `trips` (`bank_transfer_slip_path`, `bank_transfer_submitted_at`, `budget_lkr`, `created_at`, `customer_name`, `destination`, `id`, `number_of_days`, `number_of_people`, `paid_at`, `payhere_payment_id`, `refund_approved_at`, `refund_reason`, `refund_reject_note`, `refund_rejected_at`, `refund_requested_at`, `start_date`, `status`, `updated_at`, `user_id`) VALUES
(NULL, NULL, NULL, '2026-04-05 12:57:17', 'Dee Gagan', 'Colombo', 1, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-26', 'pending', '2026-04-05 12:57:17', 12),
(NULL, NULL, NULL, '2026-04-05 13:00:20', 'Dee Gagan', 'Colombo', 2, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-26', 'pending', '2026-04-05 13:00:20', 12),
(NULL, NULL, NULL, '2026-04-06 07:34:58', 'Dee Gagan', 'Colombo', 3, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 07:34:58', 12),
(NULL, NULL, NULL, '2026-04-06 08:37:21', 'Dee Gagan', 'Colombo', 4, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 08:37:21', 12),
(NULL, NULL, NULL, '2026-04-06 08:39:06', 'Dee Gagan', 'Colombo', 5, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 08:39:06', 12),
(NULL, NULL, NULL, '2026-04-06 08:41:08', 'Dee Gagan', 'Colombo', 6, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 08:41:08', 12),
(NULL, NULL, NULL, '2026-04-06 08:44:41', 'Dee Gagan', 'Colombo', 7, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 08:44:41', 12),
(NULL, NULL, NULL, '2026-04-06 08:46:23', 'Dee Gagan', 'Kandy', 8, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 08:46:23', 12),
(NULL, NULL, NULL, '2026-04-06 08:48:48', 'Dee Gagan', 'Kandy', 9, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 08:48:48', 12),
(NULL, NULL, NULL, '2026-04-06 08:51:15', 'Dee Gagan', 'Colombo', 10, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 08:51:15', 12),
(NULL, NULL, NULL, '2026-04-06 08:55:13', 'Dee Gagan', 'Colombo', 11, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 08:55:13', 12),
(NULL, NULL, NULL, '2026-04-06 09:07:19', 'Dee Gagan', 'Kandy', 12, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 09:07:19', 12),
(NULL, NULL, NULL, '2026-04-06 09:10:15', 'Dee Gagan', 'Colombo', 13, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 09:10:15', 12),
(NULL, NULL, NULL, '2026-04-06 10:28:05', 'Kalum Fernando', 'Colombo', 14, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 10:28:05', 16),
(NULL, NULL, NULL, '2026-04-06 10:36:27', 'Kalum Fernando', 'Colombo', 15, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 10:36:27', 16),
(NULL, NULL, NULL, '2026-04-06 10:39:15', 'Kalum Fernando', 'Colombo', 16, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 10:39:15', 16),
(NULL, NULL, NULL, '2026-04-06 10:57:25', 'Kalum Fernando', 'Colombo', 17, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 10:57:25', 16),
(NULL, NULL, NULL, '2026-04-06 11:00:58', 'Kalum Fernando', 'Colombo', 18, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 11:00:58', 16),
(NULL, NULL, NULL, '2026-04-06 11:07:33', 'Kalum Fernando', 'Colombo', 19, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 11:07:33', 16),
(NULL, NULL, NULL, '2026-04-06 11:09:51', 'Kalum Fernando', 'Colombo', 20, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 11:09:51', 16),
(NULL, NULL, NULL, '2026-04-06 11:13:36', 'Dee Gagan', 'Kandy', 21, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 11:13:36', 12),
(NULL, NULL, NULL, '2026-04-06 11:15:39', 'Dee Gagan', 'Colombo', 22, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 11:15:39', 12),
(NULL, NULL, NULL, '2026-04-06 11:23:11', 'Dee Gagan', 'Kandy', 23, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 11:23:11', 12),
(NULL, NULL, NULL, '2026-04-06 11:27:49', 'Dee Gagan', 'Kandy', 24, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 11:27:49', 12),
(NULL, NULL, NULL, '2026-04-06 11:34:54', 'Dee Gagan', 'Colombo', 25, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 11:34:54', 12),
(NULL, NULL, NULL, '2026-04-06 11:43:48', 'Dee Gagan', 'Kandy', 26, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 11:43:48', 12),
(NULL, NULL, NULL, '2026-04-06 11:47:29', 'Dee Gagan', 'Galle', 27, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 11:47:29', 12),
(NULL, NULL, NULL, '2026-04-06 12:25:49', 'Dee Gagan', 'Colombo', 28, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 12:25:49', 12),
(NULL, NULL, NULL, '2026-04-06 12:29:16', 'Dee Gagan', 'Jaffna', 29, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 12:29:16', 12),
(NULL, NULL, NULL, '2026-04-06 12:33:29', 'Dee Gagan', 'Colombo', 30, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 12:33:29', 12),
(NULL, NULL, NULL, '2026-04-06 12:41:19', 'Dee Gagan', 'Colombom', 31, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 12:41:19', 12),
(NULL, NULL, NULL, '2026-04-06 12:47:20', 'Dee Gagan', 'Kandy', 32, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 12:47:20', 12),
(NULL, NULL, NULL, '2026-04-06 12:51:47', 'Dee Gagan', 'Kandy', 33, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 12:51:47', 12),
(NULL, NULL, NULL, '2026-04-06 12:59:03', 'Dee Gagan', 'Colombo', 34, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 12:59:03', 12),
(NULL, NULL, NULL, '2026-04-06 13:08:23', 'Dee Gagan', 'Kandy', 35, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 13:08:23', 12),
(NULL, NULL, NULL, '2026-04-06 13:12:01', 'Dee Gagan', 'Colombo', 36, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 13:12:01', 12),
(NULL, NULL, NULL, '2026-04-06 13:15:01', 'Dee Gagan', 'Colombo', 37, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 13:15:01', 12),
(NULL, NULL, NULL, '2026-04-06 13:19:28', 'Dee Gagan', 'Kandy', 38, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 13:19:28', 12),
(NULL, NULL, NULL, '2026-04-06 13:22:26', 'Dee Gagan', 'Colombo', 39, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 13:22:26', 12),
(NULL, NULL, NULL, '2026-04-06 13:26:42', 'Dee Gagan', 'Galle', 40, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 13:26:42', 12),
(NULL, NULL, NULL, '2026-04-06 13:32:01', 'Dee Gagan', 'Colombo', 41, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 13:32:01', 12),
(NULL, NULL, NULL, '2026-04-06 13:35:27', 'Dee Gagan', 'Negombo', 42, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 13:35:27', 12),
(NULL, NULL, NULL, '2026-04-06 13:38:50', 'Dee Gagan', 'Colombo', 43, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 13:38:50', 12),
(NULL, NULL, NULL, '2026-04-06 13:43:23', 'Dee Gagan', 'Kandy', 44, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 13:43:23', 12),
(NULL, NULL, NULL, '2026-04-06 17:03:51', 'Nethmini Saubhagya', 'Colombo', 45, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 17:03:51', 11),
(NULL, NULL, NULL, '2026-04-06 17:10:23', 'Nethmini Saubhagya', 'Colombo', 46, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 17:10:23', 11),
(NULL, NULL, NULL, '2026-04-06 17:23:10', 'Dee Gagan', 'Colombo', 47, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27', 'pending', '2026-04-06 17:23:10', 12),
(NULL, NULL, NULL, '2026-04-06 19:34:35', 'Dee Gagan', 'Colombo', 48, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-06 19:34:35', 12),
(NULL, NULL, NULL, '2026-04-06 19:35:12', 'Dee Gagan', 'Colombo', 49, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-06 19:35:12', 12),
(NULL, NULL, NULL, '2026-04-06 19:38:50', 'Dee Gagan', 'Kandy', 50, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-06 19:38:50', 12),
(NULL, NULL, NULL, '2026-04-06 19:39:33', 'Dee Gagan', 'Kandy', 51, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-06 19:39:33', 12),
(NULL, NULL, NULL, '2026-04-06 19:40:33', 'Dee Gagan', 'Hikkaduwa', 52, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-06 19:40:33', 12),
(NULL, NULL, NULL, '2026-04-06 19:44:23', 'Dee Gagan', 'Jaffna', 53, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-06 19:44:23', 12),
(NULL, NULL, NULL, '2026-04-06 19:46:43', 'Dee Gagan', 'Batticaloa', 54, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-06 19:46:43', 12),
(NULL, NULL, NULL, '2026-04-06 19:47:34', 'Dee Gagan', 'Colombo', 55, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-06 19:47:34', 12),
(NULL, NULL, NULL, '2026-04-06 19:53:25', 'Dee Gagan', 'Negombo', 56, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-06 19:53:25', 12),
(NULL, NULL, NULL, '2026-04-06 19:59:48', 'Dee Gagan', 'Galle', 57, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-06 19:59:48', 12),
(NULL, NULL, 5000.00, '2026-04-06 20:01:59', 'Dee Gagan', 'Anuradhapura', 58, 1, 2, '2026-04-07 01:32:23', 'sandbox-empty-return-5b084340d056f690', NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'completed', '2026-04-06 20:02:23', 12),
(NULL, NULL, 3500.00, '2026-04-06 20:10:50', 'Dee Gagan', 'Kandy', 59, 1, 1, '2026-04-07 01:41:13', 'sandbox-empty-return-a3fb434f0f7367be', NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'completed', '2026-04-06 20:11:13', 12),
(NULL, NULL, 5000.00, '2026-04-06 20:12:17', 'Dee Gagan', 'Bentota', 60, 1, 1, '2026-04-07 01:42:45', 'sandbox-empty-return-656fcf3190c1f08b', NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'completed', '2026-04-06 20:12:45', 12),
(NULL, NULL, 3500.00, '2026-04-06 20:16:01', 'Dee Gagan', 'Kandy', 61, 1, 1, '2026-04-07 01:46:29', 'sandbox-empty-return-2eb126229e42576c', NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'completed', '2026-04-06 20:16:29', 12),
(NULL, NULL, 11000.00, '2026-04-06 20:25:44', 'Dee Gagan', 'Colombo', 62, 1, 2, '2026-04-07 01:56:07', 'sandbox-empty-return-efeba231b8a0cf90', NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'completed', '2026-04-06 20:26:07', 12),
('bank_slips/trip_63_e44176182823774d.jpg', '2026-04-07 01:57:58', 5000.00, '2026-04-06 20:27:48', 'Dee Gagan', 'Colombo', 63, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-06 20:27:58', 12),
('bank_slips/trip_64_2766cfbb89644b38.jpg', '2026-04-07 02:07:02', 2800.00, '2026-04-06 20:36:50', 'Dee Gagan', 'Galle', 64, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-06 20:37:02', 12),
(NULL, NULL, 2500.00, '2026-04-07 03:05:01', 'Dee Gagan', 'Kandy', 65, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-07 03:05:01', 12),
('bank_slips/trip_66_85d12b136cec6c17.jpg', '2026-04-07 09:27:20', 20316.00, '2026-04-07 03:55:58', 'Nethmini Saubhagya', 'Colombo', 66, 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-07 03:57:20', 11),
('bank_slips/trip_67_d168d36df08703e3.jpg', '2026-04-07 11:21:13', 401.00, '2026-04-07 05:50:33', 'Dee Gagan', 'Colombo', 67, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-07 05:51:13', 12),
(NULL, NULL, 2500.00, '2026-04-07 05:54:12', 'Dee Gagan', 'Kandy', 68, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-07 05:54:12', 12),
(NULL, NULL, 2500.00, '2026-04-07 06:01:15', 'Dee Gagan', 'Negombo', 69, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-07 06:01:15', 12),
(NULL, NULL, 2500.00, '2026-04-07 06:08:35', 'Dee Gagan', 'Jaffna', 70, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-07 06:08:35', 12),
(NULL, NULL, 2500.00, '2026-04-07 06:16:22', 'Dee Gagan', 'Negombo', 71, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-07 06:16:22', 12),
('bank_slips/trip_72_380da64e69867ad3.jpg', '2026-04-07 12:10:02', 10000.00, '2026-04-07 06:38:28', 'Dee Gagan', 'Colombo', 72, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-07 06:40:02', 12),
(NULL, NULL, 16891.00, '2026-04-07 06:51:39', 'Dee Gagan', 'Colombo', 73, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-07 06:51:39', 12),
(NULL, NULL, 26556.00, '2026-04-07 07:00:28', 'Dee Gagan', 'Colombo', 74, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-07 07:00:28', 12),
(NULL, NULL, 10301.00, '2026-04-07 07:07:07', 'Dee Gagan', 'Bentota', 75, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-07 07:07:07', 12),
('bank_slips/trip_76_36bb6f315276fd58.jpg', '2026-04-07 13:30:01', 39568.00, '2026-04-07 07:58:04', 'Nethmini Saubhagya', 'Kandy', 76, 4, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-28', 'pending', '2026-04-07 08:00:01', 11),
(NULL, NULL, 18478.00, '2026-04-19 04:10:26', 'Dee Gagan', 'Kandy', 77, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-12', 'pending', '2026-04-19 04:10:26', 12),
(NULL, NULL, 13815.00, '2026-04-19 04:29:13', 'Dee Gagan', 'Galle', 78, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-31', 'pending', '2026-04-19 04:29:13', 12),
(NULL, NULL, 48388.00, '2026-04-19 04:44:20', 'Dee Gagan', 'Arugam Bay', 79, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-22', 'pending', '2026-04-19 04:44:20', 12);

-- --------------------------------------------------------

--
-- Table structure for table `trip_bookings`
--

DROP TABLE IF EXISTS `trip_bookings`;
CREATE TABLE IF NOT EXISTS `trip_bookings` (
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `guide_required` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `id` int NOT NULL AUTO_INCREMENT,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trip_bookings`
--

INSERT INTO `trip_bookings` (`created_at`, `guide_required`, `id`, `status`, `updated_at`, `user_id`) VALUES
('2025-10-20 12:02:25', 'No', 1, 'pending', '2025-10-20 12:02:25', 6),
('2025-10-20 12:03:31', 'No', 2, 'pending', '2025-10-20 12:03:31', 6),
('2025-10-20 12:08:51', 'No', 3, 'pending', '2025-10-20 12:08:51', 6),
('2025-10-20 12:16:44', 'No', 4, 'pending', '2025-10-20 12:16:44', 6),
('2025-10-22 17:02:15', 'No', 5, 'pending', '2025-10-22 17:02:15', 12),
('2025-10-22 17:04:02', 'No', 6, 'pending', '2025-10-22 17:04:02', 12),
('2025-10-23 12:13:50', 'Yes', 7, 'pending', '2025-10-23 12:13:50', 12),
('2025-10-23 13:06:08', 'No', 8, 'pending', '2025-10-23 13:06:08', 12),
('2026-01-05 02:08:21', 'No', 9, 'pending', '2026-01-05 02:08:21', 12),
('2026-01-05 02:22:46', 'No', 10, 'pending', '2026-01-05 02:22:46', 12),
('2026-01-05 02:22:56', 'No', 11, 'pending', '2026-01-05 02:22:56', 12),
('2026-01-05 09:20:03', 'Yes', 12, 'pending', '2026-01-05 09:20:03', 12),
('2026-01-05 09:24:06', 'No', 13, 'pending', '2026-01-05 09:24:06', 12),
('2026-01-05 09:52:40', 'Yes', 14, 'pending', '2026-01-05 09:52:40', 12),
('2026-01-09 11:04:54', 'Yes', 15, 'pending', '2026-01-09 11:04:54', 12),
('2026-01-09 11:34:36', 'No', 16, 'pending', '2026-01-09 11:34:36', 12),
('2026-01-09 14:01:31', 'Yes', 17, 'pending', '2026-01-09 14:01:31', 12),
('2026-01-09 14:14:31', 'Yes', 18, 'pending', '2026-01-09 14:14:31', 12),
('2026-01-09 14:47:59', 'Yes', 19, 'pending', '2026-01-09 14:47:59', 12),
('2026-01-13 09:48:44', 'No', 20, 'pending', '2026-01-13 09:48:44', 12),
('2026-01-13 15:23:13', 'Yes', 21, 'pending', '2026-01-13 15:23:13', 12);

-- --------------------------------------------------------

--
-- Table structure for table `trip_destinations`
--

DROP TABLE IF EXISTS `trip_destinations`;
CREATE TABLE IF NOT EXISTS `trip_destinations` (
  `booking_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `days` int NOT NULL,
  `destination` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hotel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `id` int NOT NULL AUTO_INCREMENT,
  `people_count` int NOT NULL,
  `start_date` date DEFAULT NULL,
  `transport` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trip_destinations`
--

INSERT INTO `trip_destinations` (`booking_id`, `created_at`, `days`, `destination`, `hotel`, `id`, `people_count`, `start_date`, `transport`) VALUES
(1, '2025-10-20 12:02:25', 3, 'Colombo', '', 1, 3, NULL, 'No'),
(2, '2025-10-20 12:03:31', 3, 'Colombo', '', 2, 3, NULL, 'No'),
(3, '2025-10-20 12:08:51', 54, 'Nuwara Eliya', '', 3, 9, NULL, 'No'),
(4, '2025-10-20 12:16:44', 65, 'Colombo', '', 4, 4, NULL, 'No'),
(5, '2025-10-22 17:02:15', 5, 'Colombo', '', 5, 1, NULL, 'No'),
(6, '2025-10-22 17:04:02', 87, 'Galle', '', 6, 7, NULL, 'No'),
(7, '2025-10-23 12:13:50', 2, 'Sigiriya', '', 7, 4, NULL, 'No'),
(8, '2025-10-23 13:06:08', 5, 'Kandy', '', 8, 5, NULL, 'No'),
(9, '2026-01-05 02:08:21', 7, 'Colombo', 'Budget Stay Hostel', 9, 24, NULL, 'No'),
(10, '2026-01-05 02:22:46', 10, 'Nuwara Eliya', 'Downtown Comfort Inn', 10, 1, NULL, 'No'),
(11, '2026-01-05 02:22:56', 10, 'Nuwara Eliya', 'Downtown Comfort Inn', 11, 1, NULL, 'No'),
(12, '2026-01-05 09:20:03', 1, 'Colombo', 'Sunset Beach Resort', 12, 1, NULL, 'No'),
(13, '2026-01-05 09:24:06', 1, 'Nuwara Eliya', 'Downtown Comfort Inn', 13, 1, NULL, 'No'),
(14, '2026-01-05 09:52:40', 2, 'Nuwara Eliya', 'Downtown Comfort Inn', 14, 1, NULL, 'No'),
(15, '2026-01-09 11:04:54', 10, 'Colombo', 'Downtown Comfort Inn', 15, 1, NULL, 'No'),
(15, '2026-01-09 11:04:54', 10, 'Colombo', 'Sunset Beach Resort', 16, 1, NULL, 'No'),
(16, '2026-01-09 11:34:36', 10, 'Colombo', '', 17, 1, NULL, 'No'),
(17, '2026-01-09 14:01:31', 5, 'Nuwara Eliya', 'Sunset Beach Resort', 18, 1, NULL, 'Yes'),
(18, '2026-01-09 14:14:31', 3, 'Nuwara Eliya', '', 19, 1, NULL, 'No'),
(19, '2026-01-09 14:47:59', 1, 'Galle', '', 20, 4, NULL, 'Yes'),
(20, '2026-01-13 09:48:44', 2, 'Jaffna', 'Sunset Beach Resort', 21, 3, NULL, 'No'),
(21, '2026-01-13 15:23:13', 3, 'Mirissa', 'Sunset Beach Resort', 22, 12, NULL, 'Yes');

-- --------------------------------------------------------

--
-- Table structure for table `trip_diary_entries`
--

DROP TABLE IF EXISTS `trip_diary_entries`;
CREATE TABLE IF NOT EXISTS `trip_diary_entries` (
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `entry_date` date NOT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tourist_id` int NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trip_submissions`
--

DROP TABLE IF EXISTS `trip_submissions`;
CREATE TABLE IF NOT EXISTS `trip_submissions` (
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id` int NOT NULL AUTO_INCREMENT,
  `payment_status` enum('pending','payment_submitted','completed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `trip_id` int NOT NULL,
  `trip_json` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trip_submissions`
--

INSERT INTO `trip_submissions` (`created_at`, `id`, `payment_status`, `trip_id`, `trip_json`, `updated_at`, `user_id`) VALUES
('2026-04-06 12:41:19', 1, 'pending', 31, '{\"trip_id\":31,\"user_id\":12,\"destination\":\"Colombom\",\"start_date\":\"2026-04-27\",\"end_date\":\"2026-04-27\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-06T18:11:19+05:30\"}', '2026-04-06 12:41:19', 12),
('2026-04-06 12:47:20', 2, 'pending', 32, '{\"trip_id\":32,\"user_id\":12,\"destination\":\"Kandy\",\"start_date\":\"2026-04-27\",\"end_date\":\"2026-04-27\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-06T18:17:20+05:30\"}', '2026-04-06 12:47:20', 12),
('2026-04-06 12:51:47', 3, 'pending', 33, '{\"trip_id\":33,\"user_id\":12,\"destination\":\"Kandy\",\"start_date\":\"2026-04-27\",\"end_date\":\"2026-04-27\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-06T18:21:47+05:30\"}', '2026-04-06 12:51:47', 12),
('2026-04-06 12:59:03', 4, 'pending', 34, '{\"trip_id\":34,\"user_id\":12,\"destination\":\"Colombo\",\"start_date\":\"2026-04-27\",\"end_date\":\"2026-04-27\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-06T18:29:03+05:30\"}', '2026-04-06 12:59:03', 12),
('2026-04-06 13:08:23', 5, 'pending', 35, '{\"trip_id\":35,\"user_id\":12,\"destination\":\"Kandy\",\"start_date\":\"2026-04-27\",\"end_date\":\"2026-04-27\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-06T18:38:23+05:30\"}', '2026-04-06 13:08:23', 12),
('2026-04-06 13:12:01', 6, 'pending', 36, '{\"trip_id\":36,\"user_id\":12,\"destination\":\"Colombo\",\"start_date\":\"2026-04-27\",\"end_date\":\"2026-04-27\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-06T18:42:01+05:30\"}', '2026-04-06 13:12:01', 12),
('2026-04-06 13:15:01', 7, 'pending', 37, '{\"trip_id\":37,\"user_id\":12,\"destination\":\"Colombo\",\"start_date\":\"2026-04-27\",\"end_date\":\"2026-04-27\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-06T18:45:01+05:30\"}', '2026-04-06 13:15:01', 12),
('2026-04-06 13:19:29', 8, 'pending', 38, '{\"trip_id\":38,\"user_id\":12,\"destination\":\"Kandy\",\"start_date\":\"2026-04-27\",\"end_date\":\"2026-04-27\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-06T18:49:29+05:30\"}', '2026-04-06 13:19:29', 12),
('2026-04-06 13:22:26', 9, 'pending', 39, '{\"trip_id\":39,\"user_id\":12,\"destination\":\"Colombo\",\"start_date\":\"2026-04-27\",\"end_date\":\"2026-04-27\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-06T18:52:26+05:30\"}', '2026-04-06 13:22:26', 12),
('2026-04-06 13:26:42', 10, 'pending', 40, '{\"trip_id\":40,\"user_id\":12,\"destination\":\"Galle\",\"start_date\":\"2026-04-27\",\"end_date\":\"2026-04-27\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-06T18:56:42+05:30\"}', '2026-04-06 13:26:42', 12),
('2026-04-06 13:32:01', 11, 'pending', 41, '{\"trip_id\":41,\"user_id\":12,\"destination\":\"Colombo\",\"start_date\":\"2026-04-27\",\"end_date\":\"2026-04-27\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-06T19:02:01+05:30\"}', '2026-04-06 13:32:01', 12),
('2026-04-06 13:35:28', 12, 'pending', 42, '{\"trip_id\":42,\"user_id\":12,\"destination\":\"Negombo\",\"start_date\":\"2026-04-27\",\"end_date\":\"2026-04-27\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-06T19:05:28+05:30\"}', '2026-04-06 13:35:28', 12),
('2026-04-06 13:38:50', 13, 'pending', 43, '{\"trip_id\":43,\"user_id\":12,\"destination\":\"Colombo\",\"start_date\":\"2026-04-27\",\"end_date\":\"2026-04-27\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":11000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-06T19:08:50+05:30\"}', '2026-04-06 13:38:50', 12),
('2026-04-06 13:43:23', 14, 'pending', 44, '{\"trip_id\":44,\"user_id\":12,\"destination\":\"Kandy\",\"start_date\":\"2026-04-27\",\"end_date\":\"2026-04-27\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-06T19:13:23+05:30\"}', '2026-04-06 13:43:23', 12),
('2026-04-06 17:03:51', 15, 'pending', 45, '{\"trip_id\":45,\"user_id\":11,\"destination\":\"Colombo\",\"start_date\":\"2026-04-27\",\"end_date\":\"2026-04-27\",\"customer_name\":\"Nethmini Saubhagya\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":7500,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-06T22:33:51+05:30\"}', '2026-04-06 17:03:51', 11),
('2026-04-06 17:10:23', 16, 'pending', 46, '{\"trip_id\":46,\"user_id\":11,\"destination\":\"Colombo\",\"start_date\":\"2026-04-27\",\"end_date\":\"2026-04-27\",\"customer_name\":\"Nethmini Saubhagya\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":11000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-06T22:40:23+05:30\"}', '2026-04-06 17:10:23', 11),
('2026-04-06 17:23:10', 17, 'pending', 47, '{\"trip_id\":47,\"user_id\":12,\"destination\":\"Colombo\",\"start_date\":\"2026-04-27\",\"end_date\":\"2026-04-27\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-06T22:53:10+05:30\"}', '2026-04-06 17:23:10', 12),
('2026-04-06 19:34:35', 18, 'pending', 48, '{\"trip_id\":48,\"user_id\":12,\"destination\":\"Colombo\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":11000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-07T01:04:35+05:30\"}', '2026-04-06 19:34:35', 12),
('2026-04-06 19:35:12', 19, 'pending', 49, '{\"trip_id\":49,\"user_id\":12,\"destination\":\"Colombo\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":7500,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-07T01:05:12+05:30\"}', '2026-04-06 19:35:12', 12),
('2026-04-06 19:38:50', 20, 'pending', 50, '{\"trip_id\":50,\"user_id\":12,\"destination\":\"Kandy\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":3500,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-07T01:08:50+05:30\"}', '2026-04-06 19:38:50', 12),
('2026-04-06 19:39:33', 21, 'pending', 51, '{\"trip_id\":51,\"user_id\":12,\"destination\":\"Kandy\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":3500,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-07T01:09:33+05:30\"}', '2026-04-06 19:39:33', 12),
('2026-04-06 19:40:33', 22, 'pending', 52, '{\"trip_id\":52,\"user_id\":12,\"destination\":\"Hikkaduwa\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-07T01:10:33+05:30\"}', '2026-04-06 19:40:33', 12),
('2026-04-06 19:44:23', 23, 'pending', 53, '{\"trip_id\":53,\"user_id\":12,\"destination\":\"Jaffna\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-07T01:14:23+05:30\"}', '2026-04-06 19:44:23', 12),
('2026-04-06 19:46:43', 24, 'pending', 54, '{\"trip_id\":54,\"user_id\":12,\"destination\":\"Batticaloa\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-07T01:16:43+05:30\"}', '2026-04-06 19:46:43', 12),
('2026-04-06 19:47:34', 25, 'pending', 55, '{\"trip_id\":55,\"user_id\":12,\"destination\":\"Colombo\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-07T01:17:34+05:30\"}', '2026-04-06 19:47:34', 12),
('2026-04-06 19:53:25', 26, 'pending', 56, '{\"trip_id\":56,\"user_id\":12,\"destination\":\"Negombo\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-07T01:23:25+05:30\"}', '2026-04-06 19:53:25', 12),
('2026-04-06 19:59:48', 27, 'pending', 57, '{\"trip_id\":57,\"user_id\":12,\"destination\":\"Galle\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":2800,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-07T01:29:48+05:30\"}', '2026-04-06 19:59:48', 12),
('2026-04-06 20:01:59', 28, 'completed', 58, '{\"trip_id\":58,\"user_id\":12,\"destination\":\"Anuradhapura\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"completed\",\"submitted_at\":\"2026-04-07T01:31:59+05:30\",\"payment_status_updated_at\":\"2026-04-07T01:32:23+05:30\"}', '2026-04-06 20:02:23', 12),
('2026-04-06 20:10:50', 30, 'completed', 59, '{\"trip_id\":59,\"user_id\":12,\"destination\":\"Kandy\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":3500,\"payment_status\":\"completed\",\"submitted_at\":\"2026-04-07T01:40:50+05:30\",\"payment_status_updated_at\":\"2026-04-07T01:41:13+05:30\"}', '2026-04-06 20:11:13', 12),
('2026-04-06 20:12:17', 32, 'completed', 60, '{\"trip_id\":60,\"user_id\":12,\"destination\":\"Bentota\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"completed\",\"submitted_at\":\"2026-04-07T01:42:17+05:30\",\"payment_status_updated_at\":\"2026-04-07T01:42:45+05:30\"}', '2026-04-06 20:12:45', 12),
('2026-04-06 20:16:01', 34, 'completed', 61, '{\"trip_id\":61,\"user_id\":12,\"destination\":\"Kandy\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":3500,\"payment_status\":\"completed\",\"submitted_at\":\"2026-04-07T01:46:01+05:30\",\"payment_status_updated_at\":\"2026-04-07T01:46:29+05:30\"}', '2026-04-06 20:16:29', 12),
('2026-04-06 20:25:44', 36, 'completed', 62, '{\"trip_id\":62,\"user_id\":12,\"destination\":\"Colombo\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":11000,\"payment_status\":\"completed\",\"submitted_at\":\"2026-04-07T01:55:44+05:30\",\"payment_status_updated_at\":\"2026-04-07T01:56:07+05:30\"}', '2026-04-06 20:26:07', 12),
('2026-04-06 20:27:48', 38, 'payment_submitted', 63, '{\"trip_id\":63,\"user_id\":12,\"destination\":\"Colombo\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"payment_submitted\",\"submitted_at\":\"2026-04-07T01:57:48+05:30\",\"payment_status_updated_at\":\"2026-04-07T01:57:58+05:30\"}', '2026-04-06 20:27:58', 12),
('2026-04-06 20:36:50', 40, 'payment_submitted', 64, '{\"trip_id\":64,\"user_id\":12,\"destination\":\"Galle\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":2800,\"payment_status\":\"payment_submitted\",\"submitted_at\":\"2026-04-07T02:06:50+05:30\",\"wizard_snapshot\":{\"trip_type\":\"couple\",\"adults\":2,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Galle\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"stops\":[{\"location\":\"MSK COMPUTERS, Ragama, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"notRequested\":true}}]}]},\"payment_status_updated_at\":\"2026-04-07T02:07:02+05:30\"}', '2026-04-06 20:37:02', 12),
('2026-04-07 03:05:01', 42, 'pending', 65, '{\"trip_id\":65,\"user_id\":12,\"destination\":\"Kandy\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":2500,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-07T08:35:01+05:30\",\"wizard_snapshot\":{\"trip_type\":\"solo\",\"adults\":1,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Kandy\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"stops\":[{\"location\":\"Fahim\'s pet house, Panaliya, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"location\":\"Fahim\'s pet house, Panaliya, Sri Lanka\",\"date\":\"2026-04-28\",\"language\":\"English\",\"time\":\"17:00\",\"notes\":\"\"}}]}]}}', '2026-04-07 03:05:01', 12),
('2026-04-07 03:55:58', 43, 'payment_submitted', 66, '{\"trip_id\":66,\"user_id\":11,\"destination\":\"Colombo\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-29\",\"customer_name\":\"Nethmini Saubhagya\",\"number_of_people\":1,\"number_of_days\":2,\"budget_lkr\":20316,\"payment_status\":\"payment_submitted\",\"submitted_at\":\"2026-04-07T09:25:58+05:30\",\"wizard_snapshot\":{\"trip_type\":\"solo\",\"adults\":1,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Colombo\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-29\",\"stops\":[{\"location\":\"Colombo Fort Station, Colombo, Sri Lanka\",\"transport\":{\"pickup\":\"Sri Lanka Navy Whale Watching, Galle, Sri Lanka\",\"dropoff\":\"Colombo Fort Station, Colombo, Sri Lanka\",\"date\":\"2026-04-28\",\"vehicle\":\"Tuk\",\"time\":\"09:25\",\"people\":\"1\",\"fare\":10316.08},\"guide\":{\"location\":\"Colombo Fort Station, Colombo, Sri Lanka\",\"date\":\"2026-04-28\",\"language\":\"Sinhala\",\"time\":\"15:00\",\"notes\":\"\"}}]},{\"leg\":2,\"destination\":\"Galle\",\"start_date\":\"2026-04-30\",\"end_date\":\"2026-05-22\",\"stops\":[{\"location\":\"Galle Face, Colombo, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"notRequested\":true}}]}]},\"payment_status_updated_at\":\"2026-04-07T09:27:20+05:30\"}', '2026-04-07 03:57:20', 11),
('2026-04-07 05:50:33', 46, 'payment_submitted', 67, '{\"trip_id\":67,\"user_id\":12,\"destination\":\"Colombo\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":401,\"payment_status\":\"payment_submitted\",\"submitted_at\":\"2026-04-07T11:20:33+05:30\",\"wizard_snapshot\":{\"trip_type\":\"solo\",\"adults\":1,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Colombo\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"stops\":[{\"location\":\"Dehiwala, B94, Dehiwala-Mount Lavinia, Sri Lanka\",\"transport\":{\"pickup\":\"Kingsbridge American College, Abeyrathne Mawatha, Boralesgamuwa, Sri Lanka\",\"dropoff\":\"Dehiwala, B94, Dehiwala-Mount Lavinia, Sri Lanka\",\"date\":\"2026-04-28\",\"vehicle\":\"Tuk\",\"time\":\"10:14\",\"people\":\"2\",\"notes\":\"\",\"distance\":null,\"fare\":401.44},\"guide\":{\"notRequested\":true}},{\"location\":\"Viharamahadevi Park, Colombo, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"notRequested\":true}},{\"location\":\"Sapugaskanda, Gonawala, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"notRequested\":true}},{\"location\":\"Dandugama, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"notRequested\":true}},{\"location\":\"PizzaHut- Havelock City Mall, Havelock Road, Colombo, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"notRequested\":true}}]}]},\"payment_status_updated_at\":\"2026-04-07T11:21:13+05:30\"}', '2026-04-07 05:51:13', 12),
('2026-04-07 05:54:12', 48, 'pending', 68, '{\"trip_id\":68,\"user_id\":12,\"destination\":\"Kandy\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":2500,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-07T11:24:12+05:30\",\"wizard_snapshot\":{\"trip_type\":\"couple\",\"adults\":2,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Kandy\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"stops\":[{\"location\":\"Temple of the Sacred Tooth Relic, Kandy, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"location\":\"Temple of the Sacred Tooth Relic, Kandy, Sri Lanka\",\"date\":\"2026-04-28\",\"language\":\"English\",\"time\":\"15:00\",\"notes\":\"\"}}]}]}}', '2026-04-07 05:54:12', 12),
('2026-04-07 06:01:15', 49, 'pending', 69, '{\"trip_id\":69,\"user_id\":12,\"destination\":\"Negombo\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":2500,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-07T11:31:15+05:30\",\"wizard_snapshot\":{\"trip_type\":\"couple\",\"adults\":2,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Negombo\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"stops\":[{\"location\":\"Negombo, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"location\":\"Negombo, Sri Lanka\",\"date\":\"2026-04-28\",\"language\":\"English\",\"time\":\"14:00\",\"notes\":\"\"}}]}]}}', '2026-04-07 06:01:15', 12),
('2026-04-07 06:08:35', 50, 'pending', 70, '{\"trip_id\":70,\"user_id\":12,\"destination\":\"Jaffna\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":2500,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-07T11:38:35+05:30\",\"wizard_snapshot\":{\"trip_type\":\"couple\",\"adults\":2,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Jaffna\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"stops\":[{\"location\":\"Jaffna International Airport, Palali, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"location\":\"Jaffna International Airport, Palali, Sri Lanka\",\"date\":\"2026-04-28\",\"language\":\"English\",\"time\":\"14:00\",\"notes\":\"\"}}]}]}}', '2026-04-07 06:08:35', 12),
('2026-04-07 06:16:22', 51, 'pending', 71, '{\"trip_id\":71,\"user_id\":12,\"destination\":\"Negombo\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":2500,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-07T11:46:22+05:30\",\"wizard_snapshot\":{\"trip_type\":\"\",\"adults\":1,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Negombo\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"stops\":[{\"location\":\"Jabeen\'s Kitchen, Galle Road, Colombo, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"location\":\"Jabeen\'s Kitchen, Galle Road, Colombo, Sri Lanka\",\"date\":\"2026-04-28\",\"language\":\"English\",\"time\":\"13:00\",\"notes\":\"\"}}]}]}}', '2026-04-07 06:16:22', 12),
('2026-04-07 06:38:28', 52, 'payment_submitted', 72, '{\"trip_id\":72,\"user_id\":12,\"destination\":\"Colombo\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":10000,\"payment_status\":\"payment_submitted\",\"submitted_at\":\"2026-04-07T12:08:28+05:30\",\"wizard_snapshot\":{\"trip_type\":\"solo\",\"adults\":1,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Colombo\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"stops\":[{\"location\":\"Edotco Sri Lanka, Level 6, HNB Towers, T. B. Jayah Mawatha, Colombo, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"location\":\"Edotco Sri Lanka, Level 6, HNB Towers, T. B. Jayah Mawatha, Colombo, Sri Lanka\",\"date\":\"2026-04-28\",\"language\":\"English\",\"time\":\"13:00\",\"notes\":\"\"}}]}]},\"payment_status_updated_at\":\"2026-04-07T12:10:02+05:30\"}', '2026-04-07 06:40:02', 12),
('2026-04-07 07:00:28', 55, 'pending', 74, '{\"trip_id\":74,\"user_id\":12,\"destination\":\"Colombo\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":26556,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-07T12:30:28+05:30\",\"wizard_snapshot\":{\"trip_type\":\"couple\",\"adults\":2,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Colombo\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"stops\":[{\"location\":\"Trinity College Kandy, A9, Kandy, Sri Lanka\",\"transport\":{\"pickup\":\"JB VILLA, c bonavista, Unawatuna, Sri Lanka\",\"dropoff\":\"Trinity College Kandy, A9, Kandy, Sri Lanka\",\"date\":\"2026-04-28\",\"vehicle\":\"Car\",\"time\":\"12:32\",\"people\":\"2\",\"notes\":\"\",\"distance\":221.3,\"fare\":26556.48},\"guide\":{\"notRequested\":true}}]}]}}', '2026-04-07 07:00:28', 12),
('2026-04-07 07:07:07', 56, 'pending', 75, '{\"trip_id\":75,\"user_id\":12,\"destination\":\"Bentota\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":10301,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-07T12:37:07+05:30\",\"wizard_snapshot\":{\"trip_type\":\"family\",\"adults\":2,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Bentota\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-04-28\",\"stops\":[{\"location\":\"Bentara - Uragaha - Elpitiya Road, Uragasmanhandiya, Sri Lanka\",\"transport\":{\"pickup\":\"G W J Perera, Wijaya Rd, Piliyandala, Sri Lanka\",\"dropoff\":\"Bentara - Uragaha - Elpitiya Road, Uragasmanhandiya, Sri Lanka\",\"date\":\"2026-04-28\",\"vehicle\":\"Car\",\"time\":\"16:36\",\"people\":\"2\",\"notes\":\"\",\"distance\":85.85,\"fare\":10301.4},\"guide\":{\"notRequested\":true}}]}]}}', '2026-04-07 07:07:07', 12),
('2026-04-07 07:58:04', 57, 'payment_submitted', 76, '{\"trip_id\":76,\"user_id\":11,\"destination\":\"Kandy\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-05-01\",\"customer_name\":\"Nethmini Saubhagya\",\"number_of_people\":2,\"number_of_days\":4,\"budget_lkr\":39568,\"payment_status\":\"payment_submitted\",\"submitted_at\":\"2026-04-07T13:28:04+05:30\",\"wizard_snapshot\":{\"trip_type\":\"couple\",\"adults\":2,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Kandy\",\"start_date\":\"2026-04-28\",\"end_date\":\"2026-05-01\",\"stops\":[{\"location\":\"Temple of the Sacred Tooth Relic, Kandy, Sri Lanka\",\"transport\":{\"pickup\":\"Sri Lanka Bureau of Foreign Employment, District Office, Galle, Sri Lanka\",\"dropoff\":\"Temple of the Sacred Tooth Relic, Kandy, Sri Lanka\",\"date\":\"2026-04-28\",\"vehicle\":\"Car\",\"time\":\"13:26\",\"people\":\"2\",\"notes\":\"\",\"distance\":221.4,\"fare\":26568.36},\"guide\":{\"location\":\"Temple of the Sacred Tooth Relic, Kandy, Sri Lanka\",\"date\":\"2026-04-28\",\"language\":\"English\",\"time\":\"14:00\",\"notes\":\"\"}}]},{\"leg\":2,\"destination\":\"Negombo\",\"start_date\":\"2026-05-02\",\"end_date\":\"2026-05-27\",\"stops\":[{\"location\":\"Negombo Beach, Negombo, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"notRequested\":true}}]}]},\"payment_status_updated_at\":\"2026-04-07T13:30:01+05:30\"}', '2026-04-07 08:00:01', 11),
('2026-04-12 22:35:21', 59, 'payment_submitted', 77, '{\"trip_id\":77,\"user_id\":12,\"destination\":\"Colombo\",\"start_date\":\"2026-05-04\",\"end_date\":\"2026-05-04\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"payment_submitted\",\"submitted_at\":\"2026-04-13T04:05:21+05:30\",\"wizard_snapshot\":{\"trip_type\":\"solo\",\"adults\":1,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Colombo\",\"start_date\":\"2026-05-04\",\"end_date\":\"2026-05-04\",\"stops\":[{\"location\":\"JBA-Sipsi Kegalle, Kegalle Palladeniya Road, Kegalle, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"notRequested\":true}}]}]},\"payment_status_updated_at\":\"2026-04-13T04:05:36+05:30\"}', '2026-04-12 22:35:36', 12),
('2026-04-13 20:49:58', 61, 'pending', 78, '{\"trip_id\":78,\"user_id\":12,\"destination\":\"Galle\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":5300,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-14T02:19:58+05:30\",\"wizard_snapshot\":{\"trip_type\":\"solo\",\"adults\":1,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Galle\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"stops\":[{\"location\":\"BVSK_BOPITIYA, Weligepola Road, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"location\":\"BVSK_BOPITIYA, Weligepola Road, Sri Lanka\",\"date\":\"2026-05-05\",\"language\":\"Sinhala\",\"time\":\"17:00\",\"notes\":\"\"}}]}]}}', '2026-04-13 20:49:58', 12),
('2026-04-13 20:55:07', 62, 'pending', 79, '{\"trip_id\":79,\"user_id\":12,\"destination\":\"Galle\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-14T02:25:07+05:30\",\"wizard_snapshot\":{\"trip_type\":\"couple\",\"adults\":2,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Galle\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"stops\":[{\"location\":\"Sri Lanka Air force Trade Training School, Ja-Ela, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"notRequested\":true}}]}]}}', '2026-04-13 20:55:07', 12),
('2026-04-13 20:56:41', 63, 'pending', 80, '{\"trip_id\":80,\"user_id\":12,\"destination\":\"Kandy\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":2500,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-14T02:26:41+05:30\",\"wizard_snapshot\":{\"trip_type\":\"solo\",\"adults\":1,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Kandy\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"stops\":[{\"location\":\"J.N.S.Stores, Bandarawela, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"location\":\"J.N.S.Stores, Bandarawela, Sri Lanka\",\"date\":\"2026-05-05\",\"language\":\"English\",\"time\":\"18:00\",\"notes\":\"\"}}]}]}}', '2026-04-13 20:56:41', 12),
('2026-04-13 21:02:06', 64, 'pending', 81, '{\"trip_id\":81,\"user_id\":12,\"destination\":\"Kandy\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-14T02:32:06+05:30\",\"wizard_snapshot\":{\"trip_type\":\"couple\",\"adults\":2,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Kandy\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"stops\":[{\"location\":\"J S R Consultant & Traders (Pvt) Ltd, Wariyapola Sri Sumangala Mawatha, Kandy, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"notRequested\":true}}]}]}}', '2026-04-13 21:02:06', 12),
('2026-04-13 21:08:02', 65, 'pending', 82, '{\"trip_id\":82,\"user_id\":12,\"destination\":\"Kandy\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-14T02:38:02+05:30\",\"wizard_snapshot\":{\"trip_type\":\"couple\",\"adults\":2,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Kandy\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"stops\":[{\"location\":\"JZER Salon, Dehiwala Road, Pepiliyana, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"notRequested\":true}}]}]}}', '2026-04-13 21:08:02', 12),
('2026-04-13 21:15:11', 66, 'pending', 83, '{\"trip_id\":83,\"user_id\":12,\"destination\":\"Colombo\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-14T02:45:11+05:30\",\"wizard_snapshot\":{\"trip_type\":\"solo\",\"adults\":1,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Colombo\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"stops\":[{\"location\":\"JB Fashion, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"notRequested\":true}}]}]}}', '2026-04-13 21:15:11', 12),
('2026-04-13 21:16:36', 67, 'pending', 84, '{\"trip_id\":84,\"user_id\":12,\"destination\":\"Colombo\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":2500,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-14T02:46:36+05:30\",\"wizard_snapshot\":{\"trip_type\":\"solo\",\"adults\":1,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Colombo\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"stops\":[{\"location\":\"Y F Enterprise, Siri Dhamma Mawatha, Colombo, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"location\":\"Y F Enterprise, Siri Dhamma Mawatha, Colombo, Sri Lanka\",\"date\":\"2026-05-05\",\"language\":\"English\",\"time\":\"16:00\",\"notes\":\"\"}}]}]}}', '2026-04-13 21:16:36', 12),
('2026-04-13 21:20:56', 68, 'pending', 85, '{\"trip_id\":85,\"user_id\":12,\"destination\":\"Colombo\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":11000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-14T02:50:56+05:30\",\"wizard_snapshot\":{\"trip_type\":\"solo\",\"adults\":1,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Colombo\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"stops\":[{\"location\":\"Narammala, Narammala, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"notRequested\":true}}]}]}}', '2026-04-13 21:20:56', 12),
('2026-04-13 21:22:41', 69, 'pending', 86, '{\"trip_id\":86,\"user_id\":12,\"destination\":\"Galle\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":2,\"number_of_days\":1,\"budget_lkr\":12500,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-14T02:52:41+05:30\",\"wizard_snapshot\":{\"trip_type\":\"friends\",\"adults\":2,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Galle\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"stops\":[{\"location\":\"Mzion Hotel Weligama, Samaraweera Place, Weligama, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"notRequested\":true}}]}]}}', '2026-04-13 21:22:41', 12),
('2026-04-13 21:25:08', 70, 'pending', 87, '{\"trip_id\":87,\"user_id\":12,\"destination\":\"Kandy\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-14T02:55:08+05:30\",\"wizard_snapshot\":{\"trip_type\":\"solo\",\"adults\":1,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Kandy\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"stops\":[{\"location\":\"Ja-Ela, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"notRequested\":true}}]}]}}', '2026-04-13 21:25:08', 12),
('2026-04-13 21:26:23', 71, 'pending', 88, '{\"trip_id\":88,\"user_id\":12,\"destination\":\"Colombo\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-14T02:56:23+05:30\",\"wizard_snapshot\":{\"trip_type\":\"solo\",\"adults\":1,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Colombo\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"stops\":[{\"location\":\"DN “ AUDIO RAJAGIRIYA JBLPARTYBOX Renting, School Lane, Colombo, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"notRequested\":true}}]}]}}', '2026-04-13 21:26:23', 12),
('2026-04-13 21:31:38', 72, 'pending', 89, '{\"trip_id\":89,\"user_id\":12,\"destination\":\"Negombo\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":2500,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-14T03:01:38+05:30\",\"wizard_snapshot\":{\"trip_type\":\"solo\",\"adults\":1,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Negombo\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"stops\":[{\"location\":\"JB Fashion, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"location\":\"JB Fashion, Sri Lanka\",\"date\":\"2026-05-05\",\"language\":\"English\",\"time\":\"18:00\",\"notes\":\"\"}}]}]}}', '2026-04-13 21:31:38', 12),
('2026-04-17 05:02:25', 73, 'pending', 90, '{\"trip_id\":90,\"user_id\":12,\"destination\":\"Colombo\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":11000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-17T10:32:25+05:30\",\"wizard_snapshot\":{\"trip_type\":\"solo\",\"adults\":1,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Colombo\",\"start_date\":\"2026-05-05\",\"end_date\":\"2026-05-05\",\"stops\":[{\"location\":\"JB Fashion, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"notRequested\":true}}]}]}}', '2026-04-17 05:02:25', 12),
('2026-04-17 13:29:22', 74, 'pending', 91, '{\"trip_id\":91,\"user_id\":12,\"destination\":\"Kandy\",\"start_date\":\"2026-05-08\",\"end_date\":\"2026-05-08\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-17T18:59:22+05:30\",\"wizard_snapshot\":{\"trip_type\":\"solo\",\"adults\":1,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Kandy\",\"start_date\":\"2026-05-08\",\"end_date\":\"2026-05-08\",\"stops\":[{\"location\":\"Maharagama, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"notRequested\":true}}]}]}}', '2026-04-17 13:29:22', 12),
('2026-04-17 13:40:26', 75, 'pending', 92, '{\"trip_id\":92,\"user_id\":12,\"destination\":\"Kandy\",\"start_date\":\"2026-05-08\",\"end_date\":\"2026-05-08\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":5000,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-17T19:10:26+05:30\",\"wizard_snapshot\":{\"trip_type\":\"solo\",\"adults\":1,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Kandy\",\"start_date\":\"2026-05-08\",\"end_date\":\"2026-05-08\",\"stops\":[{\"location\":\"N.Bandara Motors, Sri Lanka\",\"transport\":{\"notRequested\":true},\"guide\":{\"notRequested\":true}}]}]}}', '2026-04-17 13:40:26', 12),
('2026-04-19 04:10:26', 76, 'pending', 77, '{\"trip_id\":77,\"user_id\":12,\"destination\":\"Kandy\",\"start_date\":\"2026-05-12\",\"end_date\":\"2026-05-12\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":18478,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-19T09:40:26+05:30\",\"wizard_snapshot\":{\"trip_type\":\"solo\",\"adults\":1,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Kandy\",\"start_date\":\"2026-05-12\",\"end_date\":\"2026-05-12\",\"stops\":[{\"location\":\"Temple of the Sacred Tooth Relic, Kandy, Sri Lanka\",\"transport\":{\"pickup\":\"Dehiwala, Dehiwala-Mount Lavinia, Sri Lanka\",\"dropoff\":\"Temple of the Sacred Tooth Relic, Kandy, Sri Lanka\",\"date\":\"2026-05-12\",\"vehicle\":\"Car\",\"time\":\"09:39\",\"people\":\"1\",\"fare\":15978.48},\"guide\":{\"location\":\"Temple of the Sacred Tooth Relic, Kandy, Sri Lanka\",\"date\":\"2026-05-12\",\"language\":\"English\",\"time\":\"10:00\",\"notes\":\"\"}}]}]}}', '2026-04-19 04:10:26', 12),
('2026-04-19 04:29:13', 77, 'pending', 78, '{\"trip_id\":78,\"user_id\":12,\"destination\":\"Galle\",\"start_date\":\"2026-05-31\",\"end_date\":\"2026-05-31\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":13815,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-19T09:59:13+05:30\",\"wizard_snapshot\":{\"trip_type\":\"solo\",\"adults\":1,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Galle\",\"start_date\":\"2026-05-31\",\"end_date\":\"2026-05-31\",\"stops\":[{\"location\":\"Galle Fort, Galle, Sri Lanka\",\"transport\":{\"pickup\":\"Dehiwala, Dehiwala-Mount Lavinia, Sri Lanka\",\"dropoff\":\"Galle Fort, Galle, Sri Lanka\",\"date\":\"2026-05-31\",\"vehicle\":\"Car\",\"time\":\"06:00\",\"people\":\"1\",\"fare\":13815.24},\"guide\":{\"notRequested\":true}}]}]}}', '2026-04-19 04:29:13', 12),
('2026-04-19 04:44:20', 78, 'pending', 79, '{\"trip_id\":79,\"user_id\":12,\"destination\":\"Arugam Bay\",\"start_date\":\"2026-05-22\",\"end_date\":\"2026-05-22\",\"customer_name\":\"Dee Gagan\",\"number_of_people\":1,\"number_of_days\":1,\"budget_lkr\":48388,\"payment_status\":\"pending\",\"submitted_at\":\"2026-04-19T10:14:20+05:30\",\"wizard_snapshot\":{\"trip_type\":\"solo\",\"adults\":1,\"children\":0,\"infants\":0,\"legs\":[{\"leg\":1,\"destination\":\"Arugam Bay\",\"start_date\":\"2026-05-22\",\"end_date\":\"2026-05-22\",\"stops\":[{\"location\":\"Arugam Bay Beach, Sri Lanka\",\"transport\":{\"pickup\":\"Nugegoda Flyover, Nugegoda, Sri Lanka\",\"dropoff\":\"Arugam Bay Beach, Sri Lanka\",\"date\":\"2026-05-22\",\"vehicle\":\"Car\",\"time\":\"10:13\",\"people\":\"1\",\"fare\":45887.64},\"guide\":{\"location\":\"Arugam Bay Beach, Sri Lanka\",\"date\":\"2026-05-22\",\"language\":\"English\",\"time\":\"11:00\",\"notes\":\"\"}}]}]}}', '2026-04-19 04:44:20', 12);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ref_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('tourist','guide','hotel','transport','admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`created_at`, `email`, `id`, `password`, `ref_id`, `role`) VALUES
('2025-10-22 04:19:29', 'hhh@gmail.com', 1, '$2y$10$728EWLac3BiX/fZsOz3sUexIq.YAKZf6vAtHDZZRdqHpauzz9x.1K', '1', 'hotel'),
('2025-10-22 04:19:29', 'ooo@gmail.com', 2, '$2y$10$FjACe/ZwvE97mSLW0TapU.Z5eREFFQt/yaNhlX4yEGdG76BGEiHAG', '2', 'hotel'),
('2025-10-22 04:19:29', 'ttt@gmail.com', 3, '$2y$10$vDhxhC.1Nh0DOcQf9G1nvuQpkS6MmqLcko7yreHA70gH1GMjoP5we', '3', 'hotel'),
('2025-10-22 04:19:51', 'tourist1@gmail.com', 4, '$2y$10$NuQqmwBQKWbEmOYuUId.s./vnqSpqCBQv/EW0B0U0bRo1pG6uY9ny', '1', 'tourist'),
('2025-10-22 04:19:51', 'vinoli@gmail.com', 5, '$2y$10$Ya3t1MpzIumAQM/e3NfETul7IugSRTDjm.CtFA7DSsjp4dArkP4/W', '2', 'tourist'),
('2025-10-22 04:19:51', 'gayan@gmail.com', 6, '$2y$10$YWrrP4WOWJysgnNTeFelguvEol46NHXUcBczlRbs/C2HvQ37inne6', '4', 'tourist'),
('2025-10-22 04:19:51', 'hotel1@gmail.com', 7, '$2y$10$UQF.6SP5gWeF09gypxPk/eTMZNEflNc1AKsD9wdGxCBDYsXtTI7bq', '5', 'tourist'),
('2025-10-22 04:19:51', 'kandauda91@gmail.com', 8, '$2y$10$1xvot.y1R4j3jppZA2aoPuG2sUDbF4tCY48DoDStTIKaXVb8QtkMG', '6', 'tourist'),
('2025-10-22 04:20:32', 'transport1@gmail.com', 11, '$2y$10$6Y8/5HsqQlOSPmTZEj9q8u24uJ368FdZ0d8YG2N34fEnQcetYjuFW', '', 'transport'),
('2025-10-22 04:20:32', 'aaa@gmail.com', 12, '$2y$10$hiSLosZ2UBr0SNssqmhwXer9UDjH7Boxl/sqH9zuHRQpxJGTnMeOe', 'U68f28688787', 'transport'),
('2025-10-22 04:20:56', 'guide1@gmail.com', 14, '$2y$10$t.B/cgFlzX7Vwiu5QwoM9Oiqn2RHhfKH5b4ZrvMxGkH1Mfvw/sxUm', '1', 'guide'),
('2025-10-22 04:20:56', 'guide2@gmail.com', 15, '$2y$10$yO9GPYLScCQ5zuiMSetxROk0WApPQzrZWNlafiXsNnWjuJUzyc/3y', '2', 'guide'),
('2025-10-22 04:20:56', 'guide3@gmail.com', 16, '$2y$10$lpsoC6n9VDMwTAI6/sU4BuREtUZ6MDGx4.zQDXxSnfSCss8NJwW7q', '3', 'guide'),
('2025-10-22 04:20:56', 'guide4@gmail.com', 17, '$2y$10$lcV2K8.ehMojZL1B19rqOOnoGcAM1WgS1udav5duEc0f..nk.5Lny', '5', 'guide'),
('2025-10-22 04:20:56', 'guide5@gmail.com', 18, '$2y$10$AvMN4PqN7Wp0sxOUrBgQSOjal695i6UeLrIzlCRKPCR2F5PSsR6pW', '7', 'guide'),
('2025-10-22 04:20:56', 'denis@gmail.com', 19, '$2y$10$RiqhlxVTfdAPak59mJ4QsuxgguM6rQBNX5NiD5bdkLh2vnF4naPw2', '8', 'guide'),
('2025-10-22 04:21:07', 'admin1@gmail.com', 21, '$2y$10$Yd6F0.BVibpwJPGV.o1dLegNkZeWWitQXqC3LmOztGk63d5sKetG.', '1', 'admin'),
('2025-10-22 04:21:07', 'admin2@gmail.com', 22, '$2y$10$QyNbvufZQTO.M3EEXNyPJ.9bEsXhdyMDxP3oKmdn9L3nMdg6qNHYi', '2', 'admin'),
('2025-10-22 04:21:07', 'admin3@gmail.com', 23, '$2y$10$o7srfJw7MK82UatrjHg51OwtEfnSQyPpMFG/iiU3q4uczjuPAWkOi', '4', 'admin'),
('2025-10-22 08:26:56', 'dew@gmail.co', 24, '$2y$10$oUaDHRPmfGvScPQVmEytv.kkedeDgDXr84R6NIGMNjVBf2AV9d082', '9', 'tourist'),
('2025-10-22 08:34:26', 'sau@gmail.com', 25, '$2y$10$E9vQewZvDS5LMwTWbpg2XObV8VuSlDte0fGw.WIyObCkpb5fygBIu', '11', 'tourist'),
('2025-10-22 08:39:00', 'dee@gmail.com', 26, '$2y$10$2XoYffJgN4Fwn0M33FaJde9sedqDul6JWJW1xVBFMoYIazqPnweIC', '12', 'tourist'),
('2025-10-22 12:59:13', 'resort@gmail.com', 27, '$2y$10$ZxrWHGbt0.kN1x82ZY.eYubNOyKqq5ufHh0uRSU0fGAy2gAdAZNGW', '5', 'hotel'),
('2025-10-22 13:15:26', 'sadun@gmail.com', 28, '$2y$10$nBUFZxILo40RH1s8J1Mwd.jygeac1TIaq1BHo.66AodFaeoOzf15q', NULL, 'guide'),
('2025-10-22 13:17:59', 'kaveesha@gmail.com', 29, '$2y$10$6nJbtDX5ScpcaqE18PbMX.uhHKlIGqcX1GVs8VGrqN2MGtQ7V1NRS', NULL, 'guide'),
('2025-10-22 13:18:24', 'kaveesha1@gmail.com', 30, '$2y$10$XnhkuzIRFRNOeklFvUPPx.QIFxvZdS1L2uDSer1JUeFijyfN/nYRi', NULL, 'guide'),
('2025-11-14 05:56:59', 'kalum@gmail.com', 31, '$2y$10$IwPymMHQTHifELowRRa0i.2X2h/9QqQWr68aNRee6uBr5RMZ2ZQei', '16', 'tourist'),
('2025-11-14 06:18:36', 'sandesh@gmail.com', 32, '$2y$10$F4/83mHHpCZVOdNmQs9LZOCleqr2UVQi96gNG4VVF8Vgr7BaVokcy', '17', 'tourist'),
('2025-10-22 04:47:26', 'nirosha@gmail.com', 33, '$2y$10$b.HKtc3Fr0YVASIdeT2nO.06/Ue30gKpaqdfVUaBgegB6rz4lXtjO', '8', 'tourist'),
('2026-01-18 06:51:58', 'gagana@gmail.com', 34, '$2y$10$2blPFuKc3Gri69ua3LCKJOuW7RGsMVjftaDbTUQObedAaUNZ6o53i', '18', 'tourist'),
('2026-01-18 07:19:20', 'abhi@gmail.com', 36, '$2y$10$cFspHt/YdMVJk.UOa0g5XOJnSzPs8H9LYHIOHY2xFfGnvILRbR.Ie', '19', 'tourist'),
('2026-04-06 17:13:49', 'sachi.anu@gmail.com', 37, '$2y$10$9YehJqZRpEiXZtsH2AwYFO49/cz58QdDrWQ3PIfZONg97RpbokN5W', 'TP69d33ed552', 'transport'),
('2026-04-06 17:13:49', 'knishantha@gmail.com', 38, '$2y$10$QsZucy5teAVyH65L4MfNoesZACKcN4ZIyFAHAe5h436Uk0gY1Y8I.', '18', 'guide'),
('2026-04-17 16:05:20', 'saubhagyanethmini35@gmail.com', 39, '$2y$10$Nkm6GzYZy4ikwTJJTiWURuL6nY3DZ6rK0ov2ODSBC7lfI4HHjWJ7q', '20', 'tourist'),
('2026-04-17 16:12:40', 'saubhagyaanethmini@gmail.com', 40, '$2y$10$3X8KwEpSchzxRWszqeY.AuCpQktFjGDU3VQT.gg7ujH8x2WrBb7be', '21', 'tourist'),
('2026-04-18 05:39:38', 'vinudilanya16@gmail.com', 41, '$2y$10$XK/LwuHUe03/SkXI0kXlu.xzn236h3byYjdu8//iq4Wp/vb5hlRWm', '22', 'tourist'),
('2026-04-19 10:50:00', 'shangrila@gmail.com', 42, '$2y$10$ZxrWHGbt0.kN1x82ZY.eYubNOyKqq5ufHh0uRSU0fGAy2gAdAZNGW', '4', 'hotel');

-- Populate Hotel Test Data for Shangrila (User 42) and Sachith Beach (User 27)

-- 1. Ensure entries in accommodation_catalog
INSERT INTO `accommodation_catalog` (`amenities`, `created_at`, `currency`, `from_price`, `hero_image`, `hotel_name`, `hotel_slug`, `hotel_user_id`, `is_active`, `location`, `rating`, `review_count`, `room_details`, `room_no`, `sort_order`) VALUES
('["WiFi","Pool","Spa","Fine Dining","Gym"]', '2026-01-10 10:00:00', 'LKR', 15000.00, '/CeylonGo/public/images/shangrila.jpg', 'Shangrila Colombo', 'shangrila-colombo', 42, 1, 'Colombo, Western Province', 4.8, 45, '[{"type":"Deluxe Room","description":"City view with premium amenities.","price":"Rs.15,000","priceValue":15000,"image":"/img/shangrila_deluxe.jpg"},{"type":"Family Suite","description":"Spacious suite for families with living area.","price":"Rs.25,000","priceValue":25000,"image":"/img/shangrila_suite.jpg"}]', 0, 3);

-- 2. Populate hotel_rooms
INSERT INTO `hotel_rooms` (`amenities`, `capacity`, `created_at`, `description`, `hotel_id`, `rate`, `room_number`, `room_type`, `status`) VALUES
-- Shangrila (User 42)
('["wifi","ac","tv","minibar"]', 2, '2026-01-10 10:00:00', 'Elegant room with city view', 42, 15000.00, '301', 'Deluxe', 'available'),
('["wifi","ac","tv","minibar"]', 2, '2026-01-10 10:00:00', 'Elegant room with city view', 42, 15000.00, '302', 'Deluxe', 'occupied'),
('["wifi","ac","tv","minibar","kitchenette"]', 4, '2026-01-10 10:00:00', 'Perfect for families', 42, 25000.00, '501', 'Family Suite', 'available'),
-- Sachith Beach Resort (User 27)
('["wifi","fan"]', 2, '2026-01-10 10:00:00', 'Budget friendly double room', 27, 8000.00, '101', 'Standard Double', 'available'),
('["wifi","ac","tv"]', 2, '2026-01-10 10:00:00', 'Beautiful ocean view balcony', 27, 12000.00, '201', 'Ocean View', 'available'),
('["wifi","ac","tv"]', 2, '2026-01-10 10:00:00', 'Beautiful ocean view balcony', 27, 12000.00, '202', 'Ocean View', 'maintenance');

-- 3. Populate hotel_bookings (Trend Data)
INSERT INTO `hotel_bookings` (`adults`, `check_in`, `check_out`, `children`, `contact_number`, `created_at`, `currency`, `guest_name`, `guests`, `hotel_name`, `hotel_slug`, `hotel_user_id`, `nights`, `room_count`, `room_type`, `status`, `total_price`, `user_id`) VALUES
-- Shangrila (User 42)
(2, '2026-01-05', '2026-01-07', 0, '0789145722', '2026-01-01 09:00:00', 'LKR', 'Dee Gagan', 2, 'Shangrila Colombo', 'shangrila-colombo', 42, 2, 1, 'Deluxe', 'confirmed', 30000.00, 12),
(2, '2026-01-15', '2026-01-18', 1, '0714254872', '2026-01-10 14:00:00', 'LKR', 'Nethmini Saubhagya', 3, 'Shangrila Colombo', 'shangrila-colombo', 42, 3, 1, 'Family Suite', 'confirmed', 75000.00, 11),
(2, '2026-02-02', '2026-02-05', 0, '0745698321', '2026-01-25 11:00:00', 'LKR', 'Kalum Fernando', 2, 'Shangrila Colombo', 'shangrila-colombo', 42, 3, 1, 'Deluxe', 'confirmed', 45000.00, 16),
(2, '2026-02-14', '2026-02-16', 0, '0789145725', '2026-02-01 10:00:00', 'LKR', 'Sadun Ekala', 2, 'Shangrila Colombo', 'shangrila-colombo', 42, 2, 1, 'Deluxe', 'confirmed', 30000.00, 21),
(1, '2026-03-10', '2026-03-12', 0, '0789145722', '2026-03-01 08:30:00', 'LKR', 'Dee Gagan', 1, 'Shangrila Colombo', 'shangrila-colombo', 42, 2, 1, 'Deluxe', 'confirmed', 30000.00, 12),
(2, '2026-03-20', '2026-03-25', 2, '0714254872', '2026-03-12 16:00:00', 'LKR', 'Nethmini Saubhagya', 4, 'Shangrila Colombo', 'shangrila-colombo', 42, 5, 1, 'Family Suite', 'confirmed', 125000.00, 11),
(2, '2026-04-01', '2026-04-03', 0, '0745698321', '2026-03-25 09:15:00', 'LKR', 'Kalum Fernando', 2, 'Shangrila Colombo', 'shangrila-colombo', 42, 2, 1, 'Deluxe', 'confirmed', 30000.00, 16),
(2, '2026-04-10', '2026-04-12', 0, '0789145722', '2026-04-05 12:00:00', 'LKR', 'Dee Gagan', 2, 'Shangrila Colombo', 'shangrila-colombo', 42, 2, 1, 'Deluxe', 'confirmed', 30000.00, 12),
(2, '2026-04-20', '2026-04-22', 0, '0714254872', '2026-04-15 15:00:00', 'LKR', 'Nethmini Saubhagya', 2, 'Shangrila Colombo', 'shangrila-colombo', 42, 2, 1, 'Deluxe', 'confirmed', 30000.00, 11),
(2, '2026-05-01', '2026-05-04', 0, '0745698321', '2026-04-18 10:00:00', 'LKR', 'Kalum Fernando', 2, 'Shangrila Colombo', 'shangrila-colombo', 42, 3, 1, 'Deluxe', 'confirmed', 45000.00, 16),

-- Sachith Beach Resort (User 27)
(2, '2026-01-08', '2026-01-10', 0, '0789145722', '2026-01-02 10:00:00', 'LKR', 'Dee Gagan', 2, 'Sachith Beach Resort', 'sachith-beach', 27, 2, 1, 'Standard Double', 'confirmed', 16000.00, 12),
(2, '2026-01-20', '2026-01-22', 0, '0745698321', '2026-01-15 11:00:00', 'LKR', 'Kalum Fernando', 2, 'Sachith Beach Resort', 'sachith-beach', 27, 2, 1, 'Ocean View', 'confirmed', 24000.00, 16),
(2, '2026-02-05', '2026-02-07', 0, '0714254872', '2026-01-28 09:00:00', 'LKR', 'Nethmini Saubhagya', 2, 'Sachith Beach Resort', 'sachith-beach', 27, 2, 1, 'Standard Double', 'confirmed', 16000.00, 11),
(2, '2026-02-14', '2026-02-16', 0, '0789145725', '2026-02-01 08:00:00', 'LKR', 'Sadun Ekala', 2, 'Sachith Beach Resort', 'sachith-beach', 27, 2, 1, 'Ocean View', 'confirmed', 24000.00, 21),
(1, '2026-03-05', '2026-03-07', 0, '0789145722', '2026-03-01 10:00:00', 'LKR', 'Dee Gagan', 1, 'Sachith Beach Resort', 'sachith-beach', 27, 2, 1, 'Standard Double', 'confirmed', 16000.00, 12),
(2, '2026-03-15', '2026-03-18', 0, '0745698321', '2026-03-10 11:00:00', 'LKR', 'Kalum Fernando', 2, 'Sachith Beach Resort', 'sachith-beach', 27, 3, 1, 'Ocean View', 'confirmed', 36000.00, 16),
(2, '2026-04-05', '2026-04-07', 0, '0714254872', '2026-03-28 09:00:00', 'LKR', 'Nethmini Saubhagya', 2, 'Sachith Beach Resort', 'sachith-beach', 27, 2, 1, 'Standard Double', 'confirmed', 16000.00, 11),
(2, '2026-04-15', '2026-04-17', 0, '0789145722', '2026-04-05 10:00:00', 'LKR', 'Dee Gagan', 2, 'Sachith Beach Resort', 'sachith-beach', 27, 2, 1, 'Ocean View', 'confirmed', 24000.00, 12),
(2, '2026-04-25', '2026-04-27', 0, '0745698321', '2026-04-15 11:00:00', 'LKR', 'Kalum Fernando', 2, 'Sachith Beach Resort', 'sachith-beach', 27, 2, 1, 'Standard Double', 'confirmed', 16000.00, 16),
(2, '2026-05-10', '2026-05-12', 0, '0714254872', '2026-04-18 09:00:00', 'LKR', 'Nethmini Saubhagya', 2, 'Sachith Beach Resort', 'sachith-beach', 27, 2, 1, 'Ocean View', 'confirmed', 24000.00, 11);

-- 4. Populate hotel_requests
-- Future Bookings for Next 14 Days (Availability Table Demo)
INSERT INTO `hotel_rooms` (`amenities`, `capacity`, `created_at`, `description`, `hotel_id`, `rate`, `room_number`, `room_type`, `status`) VALUES
-- Shangrila Extra Rooms
('["wifi","ac"]', 1, CURRENT_TIMESTAMP, 'Compact single room', 42, 10000.00, '101', 'Single', 'available'),
('["wifi","ac"]', 2, CURRENT_TIMESTAMP, 'Classic double room', 42, 18000.00, '201', 'Double', 'available'),
-- Sachith Extra Rooms
('["wifi","fan"]', 1, CURRENT_TIMESTAMP, 'Budget single', 27, 4000.00, 'S1', 'Single', 'available'),
('["wifi","fan"]', 2, CURRENT_TIMESTAMP, 'Budget suite', 27, 15000.00, 'SU1', 'Suite', 'available');

INSERT INTO `hotel_bookings` (`adults`, `check_in`, `check_out`, `children`, `contact_number`, `created_at`, `currency`, `guest_name`, `guests`, `hotel_name`, `hotel_slug`, `hotel_user_id`, `nights`, `room_count`, `room_type`, `status`, `total_price`, `user_id`) VALUES
-- Future bookings for Shangrila
(1, '2026-04-22', '2026-04-25', 0, '0789145667', CURRENT_TIMESTAMP, 'LKR', 'Future Guest 1', 1, 'Shangrila Colombo', 'shangrila-colombo', 42, 3, 1, 'Single', 'confirmed', 30000.00, 11),
(2, '2026-04-20', '2026-04-21', 0, '0789145668', CURRENT_TIMESTAMP, 'LKR', 'Future Guest 2', 2, 'Shangrila Colombo', 'shangrila-colombo', 42, 1, 1, 'Deluxe', 'confirmed', 15000.00, 12),
(2, '2026-04-25', '2026-04-28', 2, '0789145669', CURRENT_TIMESTAMP, 'LKR', 'Future Guest 3', 4, 'Shangrila Colombo', 'shangrila-colombo', 42, 3, 1, 'Family Suite', 'confirmed', 75000.00, 16),
-- Future bookings for Sachith
(2, '2026-04-20', '2026-04-22', 0, '0714254000', CURRENT_TIMESTAMP, 'LKR', 'Beach Guest 1', 2, 'Sachith Beach Resort', 'sachith-beach', 27, 2, 1, 'Standard Double', 'confirmed', 16000.00, 11),
(1, '2026-04-21', '2026-04-23', 0, '0714254001', CURRENT_TIMESTAMP, 'LKR', 'Beach Guest 2', 1, 'Sachith Beach Resort', 'sachith-beach', 27, 2, 1, 'Single', 'confirmed', 8000.00, 12);

-- Standardize Hotel Names across all tables
UPDATE `hotel_users` SET `hotel_name` = 'Sachith Beach Resort' WHERE `email` = 'resort@gmail.com';
UPDATE `hotel_users` SET `hotel_name` = 'Shangrila Colombo' WHERE `email` = 'shangrila@gmail.com';

UPDATE `accommodation_catalog` SET `hotel_name` = 'Sachith Beach Resort' WHERE `hotel_user_id` = 27 OR `hotel_slug` = 'sachith-beach' OR `hotel_slug` = 'sunset-beach';
UPDATE `accommodation_catalog` SET `hotel_name` = 'Shangrila Colombo' WHERE `hotel_user_id` = 42 OR `hotel_slug` = 'shangrila-colombo';

UPDATE `hotel_bookings` SET `hotel_name` = 'Sachith Beach Resort' WHERE `hotel_user_id` = 27;
UPDATE `hotel_bookings` SET `hotel_name` = 'Shangrila Colombo' WHERE `hotel_user_id` = 42;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
