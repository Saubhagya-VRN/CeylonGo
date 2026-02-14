<?php
// Apply guide request status migration
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';

try {
    $db = Database::getConnection();
    
    // Read and execute the SQL file
    $sql = file_get_contents(__DIR__ . '/add_guide_request_status.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $db->exec($statement);
            echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
        }
    }
    
    echo "\n✓ Migration completed successfully!\n";
    echo "✓ Status column added to guide_requests table\n";
    echo "✓ All existing requests set to 'pending' status\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
