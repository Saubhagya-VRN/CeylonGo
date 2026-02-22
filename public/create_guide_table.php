<!DOCTYPE html>
<html>
<head>
    <title>Create Guide Requests Table</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .success { color: green; padding: 10px; background: #e8f5e9; border-radius: 4px; margin: 10px 0; }
        .error { color: red; padding: 10px; background: #ffebee; border-radius: 4px; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Guide Requests Table</h1>
        
        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=ceylon_go;charset=utf8mb4', 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            
            echo '<div class="success">✓ Connected to database</div>';
            
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
            echo '<div class="success">✓ Table "guide_requests" created successfully!</div>';
            
            // Verify
            $stmt = $pdo->query("SHOW TABLES LIKE 'guide_requests'");
            $result = $stmt->fetch();
            
            if ($result) {
                echo '<div class="success">✓ Table verified in database</div>';
                
                // Show structure
                $stmt = $pdo->query("DESCRIBE guide_requests");
                echo '<h3>Table Structure:</h3>';
                echo '<pre>';
                echo str_pad('Field', 20) . str_pad('Type', 20) . str_pad('Null', 10) . str_pad('Key', 10) . "Default\n";
                echo str_repeat('-', 80) . "\n";
                while ($row = $stmt->fetch()) {
                    echo str_pad($row['Field'], 20) . 
                         str_pad($row['Type'], 20) . 
                         str_pad($row['Null'], 10) . 
                         str_pad($row['Key'], 10) . 
                         ($row['Default'] ?? 'NULL') . "\n";
                }
                echo '</pre>';
            }
            
        } catch (PDOException $e) {
            echo '<div class="error">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
        
        <p><a href="/CeylonGo/public/tourist/dashboard">← Back to Tourist Dashboard</a></p>
    </div>
</body>
</html>
