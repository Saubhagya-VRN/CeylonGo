<?php
require_once dirname(__DIR__) . '/config/database.php';

try {
    // Read SQL file
    $sql = file_get_contents(__DIR__ . '/create_transport_requests_table.sql');
    
    // Execute the SQL
    if ($conn->query($sql)) {
        echo "SUCCESS: transport_requests table created successfully!<br>";
        echo "<br>Table Structure:<br>";
        echo "- id (Primary Key)<br>";
        echo "- user_id (Foreign Key to tourist_users)<br>";
        echo "- customer_name<br>";
        echo "- contact_number<br>";
        echo "- date<br>";
        echo "- num_people<br>";
        echo "- vehicle_type<br>";
        echo "- pickup_location<br>";
        echo "- pickup_time<br>";
        echo "- dropoff_location<br>";
        echo "- notes<br>";
        echo "- estimated_fare<br>";
        echo "- distance<br>";
        echo "- status (pending/confirmed/cancelled/completed)<br>";
        echo "- created_at<br>";
        echo "- updated_at<br>";
        echo "<br><a href='../views/tourist/tourist_dashboard.php'>Go to Dashboard</a>";
    } else {
        echo "ERROR: " . $conn->error;
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}

$conn->close();
?>
