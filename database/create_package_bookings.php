<?php
/**
 * Script to create package_bookings table
 * Run this file in your browser: http://localhost/CeylonGo/database/create_package_bookings.php
 */

require_once dirname(__DIR__) . '/config/database.php';

$sql = file_get_contents(__DIR__ . '/create_package_bookings_table.sql');

// Split by semicolon to handle multiple statements
$statements = array_filter(array_map('trim', explode(';', $sql)));

$success = true;
$errors = [];

foreach ($statements as $statement) {
    if (empty($statement)) continue;
    
    try {
        if ($conn->query($statement)) {
            echo "<p style='color: green;'>✓ Executed successfully</p>";
        } else {
            $errors[] = $conn->error;
            echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($conn->error) . "</p>";
            $success = false;
        }
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        echo "<p style='color: red;'>✗ Exception: " . htmlspecialchars($e->getMessage()) . "</p>";
        $success = false;
    }
}

if ($success && empty($errors)) {
    echo "<h2 style='color: green;'>✓ Table 'package_bookings' created successfully!</h2>";
} else {
    echo "<h2 style='color: orange;'>⚠ Some errors occurred. Check the messages above.</h2>";
    if (!empty($errors)) {
        echo "<h3>Errors:</h3><ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
    }
}

$conn->close();
?>



