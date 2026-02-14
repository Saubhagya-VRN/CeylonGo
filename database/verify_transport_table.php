<?php
require_once dirname(__DIR__) . '/config/database.php';

echo "Checking transport_requests table...\n\n";

// Check if table exists
$result = $conn->query("SHOW TABLES LIKE 'transport_requests'");
if ($result->num_rows > 0) {
    echo "✓ Table 'transport_requests' exists!\n\n";
    
    // Show table structure
    echo "Table Structure:\n";
    echo str_repeat("-", 80) . "\n";
    $structure = $conn->query("DESCRIBE transport_requests");
    while ($row = $structure->fetch_assoc()) {
        printf("%-20s %-30s %-10s %-10s\n", 
            $row['Field'], 
            $row['Type'], 
            $row['Null'], 
            $row['Key']
        );
    }
    echo str_repeat("-", 80) . "\n\n";
    
    // Count records
    $count = $conn->query("SELECT COUNT(*) as total FROM transport_requests")->fetch_assoc();
    echo "Total records: " . $count['total'] . "\n\n";
    
    echo "Setup completed successfully!\n";
    echo "\nYou can now:\n";
    echo "1. Test the transport request form on the tourist dashboard\n";
    echo "2. View your requests at: views/tourist/my_transport_requests.php\n";
} else {
    echo "✗ Table 'transport_requests' does not exist!\n";
    echo "Please run: database/create_transport_requests.php\n";
}

$conn->close();
?>
