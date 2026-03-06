<?php
/**
 * Create transport_reviews table
 * Run this script to set up the driver reviews table
 */
require_once dirname(__DIR__) . '/config/database.php';

try {
    $sql = file_get_contents(__DIR__ . '/create_transport_reviews.sql');
    $conn->multi_query($sql);
    
    // Process all results
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    
    if ($conn->error) {
        echo "Error: " . $conn->error;
    } else {
        echo "✅ transport_reviews table created successfully!";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>
