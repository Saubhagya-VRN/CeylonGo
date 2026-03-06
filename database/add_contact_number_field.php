<?php
// Run this script to add contact_number field to tourist_transport_requests table
require_once('../config/database.php');

try {
    $sql = "ALTER TABLE `tourist_transport_requests` 
            ADD COLUMN `contactNumber` VARCHAR(15) NOT NULL AFTER `customerName`";
    
    if ($conn->query($sql) === TRUE) {
        echo "Success! Column 'contactNumber' has been added to 'tourist_transport_requests' table.\n";
    } else {
        echo "Error: " . $conn->error . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$conn->close();
?>
