<?php
/**
 * Script to create the transport_requests table in the database
 * Run this by visiting: http://localhost/CeylonGo/public/database/create_transport_requests_table_php.php
 */

require_once dirname(__DIR__) . '/config/database.php';

try {
    $db = Database::getConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS `transport_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'Tourist user ID',
  `customer_name` varchar(100) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `vehicle_type` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `pickup_time` time NOT NULL,
  `pickup_location` varchar(255) NOT NULL,
  `dropoff_location` varchar(255) NOT NULL,
  `num_people` int(11) NOT NULL DEFAULT 1,
  `notes` varchar(500) DEFAULT NULL,
  `estimated_fare` decimal(10, 2) DEFAULT NULL,
  `distance` decimal(8, 2) DEFAULT NULL,
  `assigned_driver_id` varchar(50) DEFAULT NULL COMMENT 'Transport provider user ID',
  `assigned_vehicle_no` varchar(50) DEFAULT NULL COMMENT 'Vehicle license plate',
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `assigned_driver_id` (`assigned_driver_id`),
  KEY `date_status` (`date`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    $stmt = $db->prepare($sql);
    $stmt->execute();
    
    echo htmlspecialchars("✓ SUCCESS: transport_requests table created/updated successfully!") . "<br>";
    echo "You can now use the transport booking feature.<br>";
    
} catch (PDOException $e) {
    echo htmlspecialchars("✗ ERROR: ") . htmlspecialchars($e->getMessage()) . "<br>";
}

// Also add indexes if they don't exist
try {
    $indexQueries = [
        "ALTER TABLE `transport_requests` ADD KEY `user_id` (`user_id`)" => "user_id index",
        "ALTER TABLE `transport_requests` ADD KEY `assigned_driver_id` (`assigned_driver_id`)" => "assigned_driver_id index",
        "ALTER TABLE `transport_requests` ADD KEY `date_status` (`date`, `status`)" => "date_status index"
    ];
    
    foreach ($indexQueries as $query => $name) {
        try {
            $stmt = $db->prepare($query);
            $stmt->execute();
        } catch (Exception $e) {
            // Index might already exist, that's fine
        }
    }
} catch (Exception $e) {
    // Silent fail for indexes
}
?>

