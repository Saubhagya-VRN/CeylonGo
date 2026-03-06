-- Create tour_guide_acc_details table for tour guide bank account information
CREATE TABLE IF NOT EXISTS `tour_guide_acc_details` (
  `id` int(11) NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `acc_no` varchar(50) NOT NULL,
  `acc_holder_name` varchar(100) NOT NULL,
  `branch_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
