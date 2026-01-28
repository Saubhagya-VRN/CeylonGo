<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting table creation...\n";

// Direct database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=ceylon_go;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "Connected to database\n";
    
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
    
    $pdo->exec($sql);
    echo "✓ Table 'guide_requests' created successfully!\n";
    
    // Verify
    $stmt = $pdo->query("SHOW TABLES LIKE 'guide_requests'");
    $result = $stmt->fetch();
    if ($result) {
        echo "✓ Table verified in database\n";
        
        // Show structure
        $stmt = $pdo->query("DESCRIBE guide_requests");
        echo "\nTable structure:\n";
        while ($row = $stmt->fetch()) {
            echo "  {$row['Field']} - {$row['Type']}\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
