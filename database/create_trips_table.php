<?php
require_once dirname(__DIR__) . '/config/database.php';

try {
    // Read SQL file
    $sql = file_get_contents(__DIR__ . '/create_trips_table.sql');
    
    // Execute the SQL
    if ($conn->query($sql)) {
        echo "SUCCESS: trips table created successfully!<br><br>";
        echo "Table Structure:<br>";
        echo "- id (Primary Key)<br>";
        echo "- user_id (Foreign Key to tourist_users)<br>";
        echo "- customer_name<br>";
        echo "- number_of_people<br>";
        echo "- start_date<br>";
        echo "- destination<br>";
        echo "- number_of_days<br>";
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
