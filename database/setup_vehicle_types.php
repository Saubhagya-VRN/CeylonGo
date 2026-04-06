<?php
/**
 * Setup script to add/update vehicle types in the database
 * Run this by visiting: http://localhost/CeylonGo/database/setup_vehicle_types.php
 */

require_once dirname(__DIR__) . '/config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Vehicle Types Setup</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; }
        h1 { color: #2c5530; }
        h2 { color: #4CAF50; }
        .success { color: #155724; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: #004085; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: #721c24; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
        ul { background: #f9f9f9; padding: 15px; border-radius: 5px; }
        li { margin: 5px 0; }
        .action-link { color: #2c5530; text-decoration: none; font-weight: bold; }
        .action-link:hover { text-decoration: underline; }
    </style>
</head>
<body>";

echo "<h1>🚗 Vehicle Types Setup</h1>";

try {
    $db = Database::getConnection();
    echo "<div class='success'>✓ Database connection successful</div>";

    // Define vehicle types with their IDs
    $vehicleTypes = [
        1 => 'TUK',
        2 => 'CAR',
        3 => 'MINIVAN',
        4 => 'BUS'
    ];

    echo "<h2>Setting up Vehicle Types</h2>";

    // First, check existing types
    $selectQuery = "SELECT type_id, type_name FROM transport_vehicle_types ORDER BY type_id";
    $stmt = $db->prepare($selectQuery);
    $stmt->execute();
    $existingTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>Current Vehicle Types:</h3>";
    if (!empty($existingTypes)) {
        echo "<ul>";
        foreach ($existingTypes as $type) {
            echo "<li>ID {$type['type_id']}: {$type['type_name']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<div class='info'>No vehicle types found. Will create all required types.</div>";
    }

    // Track what we did
    $created = 0;
    $updated = 0;
    $skipped = 0;

    // Insert or update vehicle types
    foreach ($vehicleTypes as $id => $name) {
        $checkQuery = "SELECT COUNT(*) as cnt, type_name FROM transport_vehicle_types WHERE type_id = :id";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $checkStmt->execute();
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($result['cnt'] == 0) {
            // Insert new type
            $insertQuery = "INSERT INTO transport_vehicle_types (type_id, type_name) VALUES (:id, :name)";
            $insertStmt = $db->prepare($insertQuery);
            $insertStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $insertStmt->bindParam(':name', $name);
            $insertStmt->execute();
            $created++;
        } else if ($result['type_name'] !== $name) {
            // Update existing type if name differs
            $updateQuery = "UPDATE transport_vehicle_types SET type_name = :name WHERE type_id = :id";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $updateStmt->bindParam(':name', $name);
            $updateStmt->execute();
            $updated++;
        } else {
            // Type exists and name matches
            $skipped++;
        }
    }

    // Show summary of actions
    echo "<h3>Setup Summary:</h3>";
    echo "<div class='success'>";
    echo "<ul>";
    if ($created > 0) echo "<li>✓ Created {$created} new vehicle type(s)</li>";
    if ($updated > 0) echo "<li>✓ Updated {$updated} existing vehicle type(s)</li>";
    if ($skipped > 0) echo "<li>✓ {$skipped} vehicle type(s) already correct</li>";
    echo "</ul>";
    echo "</div>";

    echo "<h3>Final Vehicle Types:</h3>";
    $stmt = $db->prepare($selectQuery);
    $stmt->execute();
    $finalTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<ul>";
    foreach ($finalTypes as $type) {
        echo "<li>ID {$type['type_id']}: {$type['type_name']}</li>";
    }
    echo "</ul>";

    echo "<div class='success'>";
    echo "<h3>✓ Vehicle types setup complete!</h3>";
    echo "<p>All required vehicle types are now configured in the database.</p>";
    echo "</div>";

    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li><a class='action-link' href='verify_transport_setup.php'>Verify Transport Setup</a> - Check complete system status</li>";
    echo "<li><a class='action-link' href='/CeylonGo/public/'>Back to Home</a> - Return to main application</li>";
    echo "</ol>";

} catch (PDOException $e) {
    echo "<div class='error'>";
    echo "<h2>✗ Database Error</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Troubleshooting:</strong></p>";
    echo "<ul>";
    echo "<li>Check if the database connection is working</li>";
    echo "<li>Verify that the transport_vehicle_types table exists</li>";
    echo "<li>Check database permissions</li>";
    echo "</ul>";
    echo "</div>";
}

echo "</body></html>";
?>
