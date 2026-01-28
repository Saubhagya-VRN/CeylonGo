<?php
// Create guide_requests table
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';

try {
    $db = Database::getConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS `guide_requests` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $db->exec($sql);
    echo "✓ Table 'guide_requests' created successfully!\n";
    
    // Verify table was created
    $result = $db->query("SHOW TABLES LIKE 'guide_requests'")->fetch();
    if ($result) {
        echo "✓ Table verified in database\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
