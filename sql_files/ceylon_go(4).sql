-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2025 at 06:27 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `email`, `phone_number`, `role`, `password`) VALUES
(1, 'admin1', 'admin1@gmail.com', '0712323455', 'Senior Administrator', '$2y$10$Yd6F0.BVibpwJPGV.o1dLegNkZeWWitQXqC3LmOztGk63d5sKetG.'),
(2, 'admin2', 'admin2@gmail.com', '0771122334', 'Manager', '$2y$10$QyNbvufZQTO.M3EEXNyPJ.9bEsXhdyMDxP3oKmdn9L3nMdg6qNHYi'),
(4, 'admin3', 'admin3@gmail.com', '0756354279', 'Junior Admin', '$2y$10$o7srfJw7MK82UatrjHg51OwtEfnSQyPpMFG/iiU3q4uczjuPAWkOi');

-- --------------------------------------------------------

--
-- Table structure for table `guides`
--

CREATE TABLE `guides` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guide_bookings`
--

CREATE TABLE `guide_bookings` (
  `id` int(11) NOT NULL,
  `guide_id` int(11) NOT NULL,
  `place_id` int(11) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `booking_date` datetime DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guide_places`
--

CREATE TABLE `guide_places` (
  `id` int(11) NOT NULL,
  `guide_id` int(11) NOT NULL,
  `place_name` varchar(255) NOT NULL,
  `address` varchar(500) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guide_users`
--

CREATE TABLE `guide_users` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guide_users`
--

INSERT INTO `guide_users` (`id`, `user_type`, `first_name`, `last_name`, `nic`, `license_number`, `specialization`, `languages`, `experience`, `profile_photo`, `license_file`, `contact_number`, `email`, `password`, `created_at`) VALUES
(1, 'guide', 'Sunil', 'Gamage', '200388500751', '845123359874222', 'Historical Sites', 'English, Sinhala, Tamil', 4, '[IS2203] Lecture 7_Operator Overloading.pdf', NULL, '0715896459', 'guide1@gmail.com', '$2y$10$t.B/cgFlzX7Vwiu5QwoM9Oiqn2RHhfKH5b4ZrvMxGkH1Mfvw/sxUm', '2025-10-14 05:49:45'),
(2, 'guide', 'Aravinda', 'Fernando', '200388500757', '845123359874227', 'Religious Sites', 'English, Sinhala, Tamil', 7, '[IS2203] Lecture 7_Operator Overloading.pdf', '7. Exception Handling.pdf', '0715896454', 'guide2@gmail.com', '$2y$10$yO9GPYLScCQ5zuiMSetxROk0WApPQzrZWNlafiXsNnWjuJUzyc/3y', '2025-10-14 05:57:06'),
(3, 'guide', 'Kamala', 'Dahamila', '200388500757', '84546512', 'Historical Sites', 'English, Sinhala, Tamil', 0, '[IS2203] Lecture 7_Operator Overloading.pdf', '7. Exception Handling.pdf', '5548798784', 'guide3@gmail.com', '$2y$10$lpsoC6n9VDMwTAI6/sU4BuREtUZ6MDGx4.zQDXxSnfSCss8NJwW7q', '2025-10-14 07:43:00'),
(5, 'guide', 'Kamala', 'Dahamila', '200277400963', '84546517', 'Historical Sites', 'English, Sinhala, Tamil', 0, '[IS2203] Lecture 7_Operator Overloading.pdf', '7. Exception Handling.pdf', '0715496351', 'guide4@gmail.com', '$2y$10$lcV2K8.ehMojZL1B19rqOOnoGcAM1WgS1udav5duEc0f..nk.5Lny', '2025-10-14 07:47:19'),
(7, 'guide', 'Kamala', 'Udara', '200377400787', '84546517', 'Historical Sites', 'English, Sinhala, Tamil', 4, '[IS2203] Lecture 7_Operator Overloading.pdf', '7. Exception Handling.pdf', '0715496351', 'guide5@gmail.com', '$2y$10$AvMN4PqN7Wp0sxOUrBgQSOjal695i6UeLrIzlCRKPCR2F5PSsR6pW', '2025-10-14 08:12:00'),
(8, 'guide', 'Kamala', 'Denis', '200377400787', '845123359874223', 'Historical Sites', 'English, Sinhala, Tamil', 2, '7. Exception Handling.pdf', '[IS2203] Lecture 6_Constant Classes and Objects.pdf', '0715496351', 'denis@gmail.com', '$2y$10$RiqhlxVTfdAPak59mJ4QsuxgguM6rQBNX5NiD5bdkLh2vnF4naPw2', '2025-10-17 16:08:09'),
(9, 'guide', 'Kamal', 'Gamage', '200388500757', '845123359874223', 'Historical Sites', 'English, Sinhala, Tamil', 5, 'register.drawio.png', 'IS05.pdf', '0715496351', 'kamal@gmail.com', '$2y$10$i6MfIl6nso27Xz3XQt69Le924OR4RQicrZcZCSeArf4GGnoQvgnA.', '2025-10-22 09:06:10'),
(10, 'guide', 'Ajith', 'Perera', '200377400787', '845123359874223', 'Historical Sites', 'English, Sinhala, Tamil', 6, 'IS_05_Interim_Report.pdf', '7. Exception Handling.pdf', '0715896454', 'ajith@gmail.com', '$2y$10$8GDocLtMniK2rCaRqzFeiOiN.JiLIasWEsk6e65l7vpOg2.eSk30K', '2025-10-22 13:00:36'),
(11, 'guide', 'Saman', 'Gayan', '200277400963', '845123359874227', 'Historical Sites', 'English, Sinhala, Tamil', 5, 'Interim Report.pdf', 'IS05.pdf', '5548798784', 'saman@gmail.com', '$2y$10$5KRhfr4.1BXdUeVFjJ.5q.KEm8Yo.zmMQs0rPQNJw6koeUvz8I9Fm', '2025-10-22 13:05:26'),
(13, 'guide', 'Saman', 'Gayan', '200277400963', '845123359874227', 'Historical Sites', 'English, Sinhala, Tamil', 5, 'Interim Report.pdf', 'IS05.pdf', '5548798784', 'samangayan@gmail.com', '$2y$10$u75fvDjGWArmWp.I1s0RvuBrSz1ifbUkmwOsea0UMqKQpjjMExhFC', '2025-10-22 13:10:15'),
(14, 'guide', 'Supun', 'Gayan', '200277400963', '845123359874227', 'Historical Sites', 'English, Sinhala, Tamil', 5, 'Interim Report.pdf', 'IS05.pdf', '5548798784', 'supun@gmail.com', '$2y$10$SmU.NAa9xdO.7RGs1QdRLeXPqdSX/HAQvKs7M6p./l/WFqP56X4ke', '2025-10-22 13:13:51'),
(15, 'guide', 'Sadun', 'Gayan', '200277400963', '845123359874227', 'Historical Sites', 'English, Sinhala, Tamil', 5, 'Interim Report.pdf', 'IS05.pdf', '5548798784', 'sadun@gmail.com', '$2y$10$nBUFZxILo40RH1s8J1Mwd.jygeac1TIaq1BHo.66AodFaeoOzf15q', '2025-10-22 13:15:26'),
(16, 'guide', 'kaveesha', 'Fernando', '200377400787', '845123359874223', 'Historical Sites', 'English, Sinhala, Tamil', 5, 'IS_05.pdf', 'WhatsApp Image 2025-10-21 at 01.39.29_ac57068d.jpg', '0715896459', 'kaveesha@gmail.com', '$2y$10$6nJbtDX5ScpcaqE18PbMX.uhHKlIGqcX1GVs8VGrqN2MGtQ7V1NRS', '2025-10-22 13:17:59'),
(17, 'guide', 'kaveesha', 'Fernando', '200377400787', '845123359874223', 'Historical Sites', 'English, Sinhala, Tamil', 5, 'IS_05.pdf', 'WhatsApp Image 2025-10-21 at 01.39.29_ac57068d.jpg', '0715896459', 'kaveesha1@gmail.com', '$2y$10$XnhkuzIRFRNOeklFvUPPx.QIFxvZdS1L2uDSer1JUeFijyfN/nYRi', '2025-10-22 13:18:24');

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `name`, `email`, `password`, `contact`, `address`, `created_at`) VALUES
(1, 'My Hotel', 'hotel@example.com', 'password123', '0771234567', 'No. 123, Colombo, Sri Lanka', '2025-10-20 17:14:50'),
(3, 'My Hotel', 'hotel2@example.com', 'password123', '0771234567', 'No. 123, Colombo, Sri Lanka', '2025-10-20 17:16:21');

-- --------------------------------------------------------

--
-- Table structure for table `hotel_rooms`
--

CREATE TABLE `hotel_rooms` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `capacity` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `amenities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`amenities`)),
  `status` enum('available','occupied','maintenance') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotel_rooms`
--

INSERT INTO `hotel_rooms` (`id`, `hotel_id`, `room_number`, `room_type`, `rate`, `capacity`, `description`, `amenities`, `status`, `created_at`) VALUES
(50, 1, '7584', 'deluxe', 515151.00, 2, '', NULL, '', '2025-10-20 17:20:35'),
(51, 1, '255', 'double', 7500.00, 2, 'Ac Included', NULL, 'maintenance', '2025-10-20 18:52:13'),
(52, 1, '6985', 'double', 87522.00, 5, 'heyy', NULL, 'maintenance', '2025-10-20 18:52:54'),
(53, 1, '899', 'suite', 875000.00, 6, 'Clean', NULL, 'maintenance', '2025-10-20 18:56:28'),
(54, 1, '542', 'suite', 9855.00, 5, 'Sea View', '[\"wifi\",\"air_conditioning\"]', 'occupied', '2025-10-20 19:21:06'),
(55, 1, '542', 'double', 664.00, 2, '', '[\"wifi\"]', 'maintenance', '2025-10-21 03:05:52'),
(56, 1, '415', 'single', 23.00, 2, 'awetr', '[\"room_service\"]', 'available', '2025-10-21 03:31:50'),
(57, 1, '845', 'double', 4500.00, 2, '', '[\"wifi\"]', 'available', '2025-10-22 14:57:43'),
(58, 1, '123', 'double', 3000.00, 1, '', '[\"air_conditioning\",\"tv\",\"minibar\",\"room_service\"]', '', '2025-10-23 06:08:22'),
(59, 1, '100', 'double', 2000.00, 2, 'sea view', '[\"air_conditioning\",\"tv\",\"minibar\",\"room_service\",\"parking\"]', '', '2025-10-23 06:46:11'),
(60, 1, '101', 'double', 2000.00, 2, 'Sea view', '[\"air_conditioning\",\"tv\",\"minibar\",\"room_service\",\"parking\"]', '', '2025-10-23 07:40:47');

-- --------------------------------------------------------

--
-- Table structure for table `hotel_users`
--

CREATE TABLE `hotel_users` (
  `id` int(11) NOT NULL,
  `hotel_name` varchar(100) NOT NULL,
  `location` varchar(150) NOT NULL,
  `city` varchar(100) NOT NULL,
  `hotel_image` varchar(255) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotel_users`
--

INSERT INTO `hotel_users` (`id`, `hotel_name`, `location`, `city`, `hotel_image`, `contact_number`, `email`, `password`, `created_at`) VALUES
(1, 'Cent', '150/14/4, Lake Road, Kandy', 'Matara', '1760982033_register.drawio.png', '0714254873', 'hhh@gmail.com', '$2y$10$728EWLac3BiX/fZsOz3sUexIq.YAKZf6vAtHDZZRdqHpauzz9x.1K', '2025-10-20 17:40:33'),
(2, 'Ant', '150/14/4, Lake Road, Kandy', 'Matara', '1760982304_login.drawio.png', '0714254871', 'ooo@gmail.com', '$2y$10$FjACe/ZwvE97mSLW0TapU.Z5eREFFQt/yaNhlX4yEGdG76BGEiHAG', '2025-10-20 17:45:04'),
(3, 'Ant', '150/14/4, Lake Road, Kandy', 'Matara', '1760982414_login.drawio.png', '0714254871', 'ttt@gmail.com', '$2y$10$vDhxhC.1Nh0DOcQf9G1nvuQpkS6MmqLcko7yreHA70gH1GMjoP5we', '2025-10-20 17:46:54'),
(4, 'Shangrila', '150/14/4, Lake Road, Colombo', 'Colombo', '1761123454_review.drawio.png', '0714254876', 'shangrila@gmail.com', '$2y$10$9RfzT2QytsuaUmWGgnuPIezteVeYph.6yTaYlkr5APJJCx2T78ACO', '2025-10-22 08:57:34'),
(5, 'Resort Inn', '150/14/4, Lake Road, Jaffna', 'Jaffna', '1761137953_logout.drawio.png', '0714254873', 'resort@gmail.com', '$2y$10$ZxrWHGbt0.kN1x82ZY.eYubNOyKqq5ufHh0uRSU0fGAy2gAdAZNGW', '2025-10-22 12:59:13');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text NOT NULL,
  `destination` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending' COMMENT 'pending, approved, rejected',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Customer reviews and ratings';

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `name`, `email`, `rating`, `review_text`, `destination`, `status`, `created_at`, `updated_at`) VALUES
(1, 6, 'Abhijeeth Kandauda', 'kandauda91@gmail.com', 4, 'good', 'Kandy', 'pending', '2025-10-20 12:21:59', '2025-10-20 12:21:59');

-- --------------------------------------------------------

--
-- Table structure for table `tourist_transport_requests`
--

CREATE TABLE `tourist_transport_requests` (
  `id` int(11) NOT NULL,
  `customerName` varchar(100) NOT NULL,
  `vehicleType` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `pickupTime` time NOT NULL,
  `pickupLocation` varchar(100) NOT NULL,
  `dropoffLocation` varchar(100) NOT NULL,
  `numPeople` int(11) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tourist_transport_requests`
--

INSERT INTO `tourist_transport_requests` (`id`, `customerName`, `vehicleType`, `date`, `pickupTime`, `pickupLocation`, `dropoffLocation`, `numPeople`, `notes`, `created_at`) VALUES
(1, 'Amali', 'Car', '2025-10-23', '20:32:00', 'Bandaranayake Airport', 'Galle Fort', 5, 'Be punctual', '2025-10-17 11:02:17'),
(7, 'Shevin', 'Tuk', '2025-10-11', '05:07:00', 'Jewing Colombo Hotel', 'Colombo Museam', 45, '', '2025-10-20 20:37:18'),
(10, 'Sadun', 'Car', '2025-11-07', '20:24:00', 'UCSC', 'Negomboo Beach', 8, '', '2025-10-22 11:54:21'),
(11, 'Arunod', 'Tuk', '2025-10-24', '21:36:00', 'Jewing Colombo Hotel', 'Colombo Museam', 5, 'Be punctual', '2025-10-22 12:06:31'),
(14, 'Queen', 'Car', '2025-10-29', '14:34:00', 'UCSC', 'Negomboo Beach', 4, 'Tip of 1000', '2025-10-23 06:04:19'),
(15, 'Shevin', 'Tuk', '2025-10-24', '03:12:00', 'Jewing Colombo Hotel', 'Negomboo Beach', 5, '', '2025-10-23 06:42:57');

-- --------------------------------------------------------

--
-- Table structure for table `tourist_users`
--

CREATE TABLE `tourist_users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tourist_users`
--

INSERT INTO `tourist_users` (`id`, `first_name`, `last_name`, `contact_number`, `email`, `password`, `created_at`) VALUES
(1, 'Nethmini', 'Saubhagya', '0714254877', 'tourist1@gmail.com', '$2y$10$NuQqmwBQKWbEmOYuUId.s./vnqSpqCBQv/EW0B0U0bRo1pG6uY9ny', '2025-10-17 08:43:46'),
(2, 'Vinoli', 'Fernando', '0714254876', 'vinoli@gmail.com', '$2y$10$Ya3t1MpzIumAQM/e3NfETul7IugSRTDjm.CtFA7DSsjp4dArkP4/W', '2025-10-17 15:10:06'),
(4, 'Gayan', 'Perera', '0756783422', 'gayan@gmail.com', '$2y$10$YWrrP4WOWJysgnNTeFelguvEol46NHXUcBczlRbs/C2HvQ37inne6', '2025-10-19 07:38:02'),
(5, 'Nethmini', 'Saubhagya', '0714254876', 'hotel1@gmail.com', '$2y$10$UQF.6SP5gWeF09gypxPk/eTMZNEflNc1AKsD9wdGxCBDYsXtTI7bq', '2025-10-20 05:38:42'),
(6, 'abhijeeth', 'kandauda', '0978543216', 'kandauda91@gmail.com', '$2y$10$1xvot.y1R4j3jppZA2aoPuG2sUDbF4tCY48DoDStTIKaXVb8QtkMG', '2025-10-20 06:18:26'),
(8, 'Nirosha', 'Damayanthi', '0714254875', 'nirosha@gmail.com', '$2y$10$b.HKtc3Fr0YVASIdeT2nO.06/Ue30gKpaqdfVUaBgegB6rz4lXtjO', '2025-10-22 04:47:26'),
(9, 'Dewmini', 'Sathsara', '0714254870', 'dew@gmail.co', '$2y$10$oUaDHRPmfGvScPQVmEytv.kkedeDgDXr84R6NIGMNjVBf2AV9d082', '2025-10-22 08:26:56'),
(11, 'Nethmini', 'Saubhagya', '0714254872', 'sau@gmail.com', '$2y$10$E9vQewZvDS5LMwTWbpg2XObV8VuSlDte0fGw.WIyObCkpb5fygBIu', '2025-10-22 08:34:26'),
(12, 'Dee', 'Gagan', '0789145722', 'dee@gmail.com', '$2y$10$2XoYffJgN4Fwn0M33FaJde9sedqDul6JWJW1xVBFMoYIazqPnweIC', '2025-10-22 08:39:00'),
(16, 'Kalum', 'Fernando', '0745698321', 'kalum@gmail.com', '$2y$10$IwPymMHQTHifELowRRa0i.2X2h/9QqQWr68aNRee6uBr5RMZ2ZQei', '2025-11-14 05:56:59'),
(17, 'sandesh', 'Perera', '0714254874', 'sandesh@gmail.com', '$2y$10$F4/83mHHpCZVOdNmQs9LZOCleqr2UVQi96gNG4VVF8Vgr7BaVokcy', '2025-11-14 06:18:36');

-- --------------------------------------------------------

--
-- Table structure for table `transport_license`
--

CREATE TABLE `transport_license` (
  `license_no` varchar(20) NOT NULL,
  `license_exp_date` date NOT NULL,
  `image` varchar(20) NOT NULL,
  `driver_id` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transport_license`
--

INSERT INTO `transport_license` (`license_no`, `license_exp_date`, `image`, `driver_id`) VALUES
('098098', '2025-10-31', 'img_68f2880f03ffc8.0', 'U68f2880eea1'),
('1234JJ', '2026-01-14', 'img_68f4b466ef70f6.5', 'U68f4b466a98'),
('2345678', '2025-11-07', 'img_68f288b3b00bf2.5', 'U68f288b3a20'),
('23457678', '2026-01-01', 'Screenshot 2025-09-0', 'U68f28688787'),
('8763446', '2027-11-08', 'img_68f48c352cdcc7.9', 'U68f48c351e8'),
('A2345767', '2026-12-31', '', ' 68f9cc3ce65'),
('AY-2345', '2025-11-07', 'img_68f9cc3d2967d0.5', ' 68f9cc3ce84');

-- --------------------------------------------------------

--
-- Table structure for table `transport_users`
--

CREATE TABLE `transport_users` (
  `user_id` varchar(12) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `dob` date NOT NULL,
  `nic` varchar(15) NOT NULL,
  `address` varchar(50) NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `profile_image` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `psw` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transport_users`
--

INSERT INTO `transport_users` (`user_id`, `full_name`, `dob`, `nic`, `address`, `contact_no`, `profile_image`, `email`, `psw`) VALUES
(' 68f9cc3ce65', 'Kaveesha', '2025-10-05', '1234', '123, Main Street, Matara', '123456789', '', 'transport1@gmail.com', '$2y$10$6Y8/5HsqQlOSPmTZEj9q8u24uJ368FdZ0d8YG2N34fEnQcetYjuFW'),
(' 68f9cc3ce84', 'Kaveesha', '2003-03-10', '200388500432', 'Dehiwala, Colombo', '0712345431', '', 'kavee@gmail.com', '$2y$10$0M6O83.DOtvP6XURUZ/vZevT33gBP5lymQWGd/HQGHD.kYEXY3IBW'),
('U68f28688787', 'kaveesha', '2025-10-07', '1142356', 'xscdf', '1244534645', '', 'aaa@gmail.com', '$2y$10$hiSLosZ2UBr0SNssqmhwXer9UDjH7Boxl/sqH9zuHRQpxJGTnMeOe');

-- --------------------------------------------------------

--
-- Table structure for table `transport_vehicle`
--

CREATE TABLE `transport_vehicle` (
  `vehicle_no` varchar(15) NOT NULL,
  `user_id` varchar(12) NOT NULL,
  `vehicle_type` varchar(20) NOT NULL,
  `image` varchar(48) NOT NULL,
  `psg_capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transport_vehicle`
--

INSERT INTO `transport_vehicle` (`vehicle_no`, `user_id`, `vehicle_type`, `image`, `psg_capacity`) VALUES
('BA1234', 'U68f4b466a98', '1', 'img_68f4b46700fb02.80913680.png', 3),
('BA1239', ' 68f9cc3ce84', '1', 'img_68f9cc3d37dda9.01415511.png', 3),
('ju8877', 'U68f288b3a20', '1', 'img_68f288b3b09129.8', 3),
('PG-5432', 'U68f48c351e8', '2', 'img_68f48c352dd631.69653958.jpg', 10),
('TY-5341', 'U68f28688787', '1', 'img_68f480b7b36c52.25171860.webp', 3),
('TY-5349', '', '1', 'img_693f97e815d233.43114761.webp', 3);

-- --------------------------------------------------------

--
-- Table structure for table `transport_vehicle_types`
--

CREATE TABLE `transport_vehicle_types` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transport_vehicle_types`
--

INSERT INTO `transport_vehicle_types` (`type_id`, `type_name`) VALUES
(1, 'TUK'),
(2, 'VAN');

-- --------------------------------------------------------

--
-- Table structure for table `trip_bookings`
--

CREATE TABLE `trip_bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `guide_required` varchar(10) DEFAULT 'No',
  `status` varchar(50) DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_bookings`
--

INSERT INTO `trip_bookings` (`id`, `user_id`, `guide_required`, `status`, `created_at`, `updated_at`) VALUES
(1, 6, 'No', 'pending', '2025-10-20 12:02:25', '2025-10-20 12:02:25'),
(2, 6, 'No', 'pending', '2025-10-20 12:03:31', '2025-10-20 12:03:31'),
(3, 6, 'No', 'pending', '2025-10-20 12:08:51', '2025-10-20 12:08:51'),
(4, 6, 'No', 'pending', '2025-10-20 12:16:44', '2025-10-20 12:16:44'),
(5, 12, 'No', 'pending', '2025-10-22 17:02:15', '2025-10-22 17:02:15'),
(6, 12, 'No', 'pending', '2025-10-22 17:04:02', '2025-10-22 17:04:02'),
(7, 12, 'Yes', 'pending', '2025-10-23 12:13:50', '2025-10-23 12:13:50'),
(8, 12, 'No', 'pending', '2025-10-23 13:06:08', '2025-10-23 13:06:08');

-- --------------------------------------------------------

--
-- Table structure for table `trip_destinations`
--

CREATE TABLE `trip_destinations` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `people_count` int(11) NOT NULL,
  `days` int(11) NOT NULL,
  `hotel` varchar(255) DEFAULT '',
  `transport` varchar(255) DEFAULT 'No',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_destinations`
--

INSERT INTO `trip_destinations` (`id`, `booking_id`, `destination`, `people_count`, `days`, `hotel`, `transport`, `created_at`) VALUES
(1, 1, 'Colombo', 3, 3, '', 'No', '2025-10-20 12:02:25'),
(2, 2, 'Colombo', 3, 3, '', 'No', '2025-10-20 12:03:31'),
(3, 3, 'Nuwara Eliya', 9, 54, '', 'No', '2025-10-20 12:08:51'),
(4, 4, 'Colombo', 4, 65, '', 'No', '2025-10-20 12:16:44'),
(5, 5, 'Colombo', 1, 5, '', 'No', '2025-10-22 17:02:15'),
(6, 6, 'Galle', 7, 87, '', 'No', '2025-10-22 17:04:02'),
(7, 7, 'Sigiriya', 4, 2, '', 'No', '2025-10-23 12:13:50'),
(8, 8, 'Kandy', 5, 5, '', 'No', '2025-10-23 13:06:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('tourist','guide','hotel','transport','admin') NOT NULL,
  `ref_id` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `ref_id`, `created_at`) VALUES
(1, 'hhh@gmail.com', '$2y$10$728EWLac3BiX/fZsOz3sUexIq.YAKZf6vAtHDZZRdqHpauzz9x.1K', 'hotel', '1', '2025-10-22 04:19:29'),
(2, 'ooo@gmail.com', '$2y$10$FjACe/ZwvE97mSLW0TapU.Z5eREFFQt/yaNhlX4yEGdG76BGEiHAG', 'hotel', '2', '2025-10-22 04:19:29'),
(3, 'ttt@gmail.com', '$2y$10$vDhxhC.1Nh0DOcQf9G1nvuQpkS6MmqLcko7yreHA70gH1GMjoP5we', 'hotel', '3', '2025-10-22 04:19:29'),
(4, 'tourist1@gmail.com', '$2y$10$NuQqmwBQKWbEmOYuUId.s./vnqSpqCBQv/EW0B0U0bRo1pG6uY9ny', 'tourist', '1', '2025-10-22 04:19:51'),
(5, 'vinoli@gmail.com', '$2y$10$Ya3t1MpzIumAQM/e3NfETul7IugSRTDjm.CtFA7DSsjp4dArkP4/W', 'tourist', '2', '2025-10-22 04:19:51'),
(6, 'gayan@gmail.com', '$2y$10$YWrrP4WOWJysgnNTeFelguvEol46NHXUcBczlRbs/C2HvQ37inne6', 'tourist', '4', '2025-10-22 04:19:51'),
(7, 'hotel1@gmail.com', '$2y$10$UQF.6SP5gWeF09gypxPk/eTMZNEflNc1AKsD9wdGxCBDYsXtTI7bq', 'tourist', '5', '2025-10-22 04:19:51'),
(8, 'kandauda91@gmail.com', '$2y$10$1xvot.y1R4j3jppZA2aoPuG2sUDbF4tCY48DoDStTIKaXVb8QtkMG', 'tourist', '6', '2025-10-22 04:19:51'),
(11, 'transport1@gmail.com', '$2y$10$6Y8/5HsqQlOSPmTZEj9q8u24uJ368FdZ0d8YG2N34fEnQcetYjuFW', 'transport', '', '2025-10-22 04:20:32'),
(12, 'aaa@gmail.com', '$2y$10$hiSLosZ2UBr0SNssqmhwXer9UDjH7Boxl/sqH9zuHRQpxJGTnMeOe', 'transport', 'U68f28688787', '2025-10-22 04:20:32'),
(14, 'guide1@gmail.com', '$2y$10$t.B/cgFlzX7Vwiu5QwoM9Oiqn2RHhfKH5b4ZrvMxGkH1Mfvw/sxUm', 'guide', '1', '2025-10-22 04:20:56'),
(15, 'guide2@gmail.com', '$2y$10$yO9GPYLScCQ5zuiMSetxROk0WApPQzrZWNlafiXsNnWjuJUzyc/3y', 'guide', '2', '2025-10-22 04:20:56'),
(16, 'guide3@gmail.com', '$2y$10$lpsoC6n9VDMwTAI6/sU4BuREtUZ6MDGx4.zQDXxSnfSCss8NJwW7q', 'guide', '3', '2025-10-22 04:20:56'),
(17, 'guide4@gmail.com', '$2y$10$lcV2K8.ehMojZL1B19rqOOnoGcAM1WgS1udav5duEc0f..nk.5Lny', 'guide', '5', '2025-10-22 04:20:56'),
(18, 'guide5@gmail.com', '$2y$10$AvMN4PqN7Wp0sxOUrBgQSOjal695i6UeLrIzlCRKPCR2F5PSsR6pW', 'guide', '7', '2025-10-22 04:20:56'),
(19, 'denis@gmail.com', '$2y$10$RiqhlxVTfdAPak59mJ4QsuxgguM6rQBNX5NiD5bdkLh2vnF4naPw2', 'guide', '8', '2025-10-22 04:20:56'),
(21, 'admin1@gmail.com', '$2y$10$Yd6F0.BVibpwJPGV.o1dLegNkZeWWitQXqC3LmOztGk63d5sKetG.', 'admin', '1', '2025-10-22 04:21:07'),
(22, 'admin2@gmail.com', '$2y$10$QyNbvufZQTO.M3EEXNyPJ.9bEsXhdyMDxP3oKmdn9L3nMdg6qNHYi', 'admin', '2', '2025-10-22 04:21:07'),
(23, 'admin3@gmail.com', '$2y$10$o7srfJw7MK82UatrjHg51OwtEfnSQyPpMFG/iiU3q4uczjuPAWkOi', 'admin', '4', '2025-10-22 04:21:07'),
(24, 'dew@gmail.co', '$2y$10$oUaDHRPmfGvScPQVmEytv.kkedeDgDXr84R6NIGMNjVBf2AV9d082', 'tourist', '9', '2025-10-22 08:26:56'),
(25, 'sau@gmail.com', '$2y$10$E9vQewZvDS5LMwTWbpg2XObV8VuSlDte0fGw.WIyObCkpb5fygBIu', 'tourist', '11', '2025-10-22 08:34:26'),
(26, 'dee@gmail.com', '$2y$10$2XoYffJgN4Fwn0M33FaJde9sedqDul6JWJW1xVBFMoYIazqPnweIC', 'tourist', '12', '2025-10-22 08:39:00'),
(27, 'resort@gmail.com', '$2y$10$ZxrWHGbt0.kN1x82ZY.eYubNOyKqq5ufHh0uRSU0fGAy2gAdAZNGW', 'hotel', '5', '2025-10-22 12:59:13'),
(28, 'sadun@gmail.com', '$2y$10$nBUFZxILo40RH1s8J1Mwd.jygeac1TIaq1BHo.66AodFaeoOzf15q', 'guide', NULL, '2025-10-22 13:15:26'),
(29, 'kaveesha@gmail.com', '$2y$10$6nJbtDX5ScpcaqE18PbMX.uhHKlIGqcX1GVs8VGrqN2MGtQ7V1NRS', 'guide', NULL, '2025-10-22 13:17:59'),
(30, 'kaveesha1@gmail.com', '$2y$10$XnhkuzIRFRNOeklFvUPPx.QIFxvZdS1L2uDSer1JUeFijyfN/nYRi', 'guide', NULL, '2025-10-22 13:18:24'),
(31, 'kalum@gmail.com', '$2y$10$IwPymMHQTHifELowRRa0i.2X2h/9QqQWr68aNRee6uBr5RMZ2ZQei', 'tourist', '16', '2025-11-14 05:56:59'),
(32, 'sandesh@gmail.com', '$2y$10$F4/83mHHpCZVOdNmQs9LZOCleqr2UVQi96gNG4VVF8Vgr7BaVokcy', 'tourist', '17', '2025-11-14 06:18:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `guides`
--
ALTER TABLE `guides`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guide_bookings`
--
ALTER TABLE `guide_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guide_id` (`guide_id`),
  ADD KEY `place_id` (`place_id`);

--
-- Indexes for table `guide_places`
--
ALTER TABLE `guide_places`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guide_id` (`guide_id`);

--
-- Indexes for table `guide_users`
--
ALTER TABLE `guide_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `hotel_rooms`
--
ALTER TABLE `hotel_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `hotel_users`
--
ALTER TABLE `hotel_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `tourist_transport_requests`
--
ALTER TABLE `tourist_transport_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tourist_users`
--
ALTER TABLE `tourist_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `transport_license`
--
ALTER TABLE `transport_license`
  ADD PRIMARY KEY (`license_no`),
  ADD UNIQUE KEY `driver_id` (`driver_id`);

--
-- Indexes for table `transport_users`
--
ALTER TABLE `transport_users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `nic` (`nic`),
  ADD UNIQUE KEY `contact_no` (`contact_no`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `transport_vehicle`
--
ALTER TABLE `transport_vehicle`
  ADD PRIMARY KEY (`vehicle_no`);

--
-- Indexes for table `transport_vehicle_types`
--
ALTER TABLE `transport_vehicle_types`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `trip_bookings`
--
ALTER TABLE `trip_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_trip_bookings_user_id` (`user_id`),
  ADD KEY `idx_trip_bookings_status` (`status`);

--
-- Indexes for table `trip_destinations`
--
ALTER TABLE `trip_destinations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_trip_destinations_booking_id` (`booking_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `guides`
--
ALTER TABLE `guides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guide_bookings`
--
ALTER TABLE `guide_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guide_places`
--
ALTER TABLE `guide_places`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guide_users`
--
ALTER TABLE `guide_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hotel_rooms`
--
ALTER TABLE `hotel_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `hotel_users`
--
ALTER TABLE `hotel_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tourist_transport_requests`
--
ALTER TABLE `tourist_transport_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tourist_users`
--
ALTER TABLE `tourist_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `transport_vehicle_types`
--
ALTER TABLE `transport_vehicle_types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `trip_bookings`
--
ALTER TABLE `trip_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `trip_destinations`
--
ALTER TABLE `trip_destinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `guide_bookings`
--
ALTER TABLE `guide_bookings`
  ADD CONSTRAINT `guide_bookings_ibfk_1` FOREIGN KEY (`guide_id`) REFERENCES `guides` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `guide_bookings_ibfk_2` FOREIGN KEY (`place_id`) REFERENCES `guide_places` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `guide_places`
--
ALTER TABLE `guide_places`
  ADD CONSTRAINT `guide_places_ibfk_1` FOREIGN KEY (`guide_id`) REFERENCES `guides` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hotel_rooms`
--
ALTER TABLE `hotel_rooms`
  ADD CONSTRAINT `hotel_rooms_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tourist_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trip_bookings`
--
ALTER TABLE `trip_bookings`
  ADD CONSTRAINT `trip_bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tourist_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trip_destinations`
--
ALTER TABLE `trip_destinations`
  ADD CONSTRAINT `trip_destinations_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `trip_bookings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
