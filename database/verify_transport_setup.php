<?php
/**
 * Transport System Setup & Verification Script
 * Ensures all tables, types, and connections are properly configured
 * Run this by visiting: http://localhost/CeylonGo/database/verify_transport_setup.php
 */

require_once dirname(__DIR__) . '/config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Transport System Verification</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; }
        h1 { color: #2c5530; }
        h2 { color: #4CAF50; border-top: 1px solid #ddd; padding-top: 20px; margin-top: 30px; }
        .success { color: #155724; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: #721c24; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: #004085; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f5f5f5; font-weight: bold; }
        .action-link { color: #2c5530; text-decoration: none; font-weight: bold; }
        .action-link:hover { text-decoration: underline; }
    </style>
</head>
<body>";

echo "<h1>🚗 Transport System Setup & Verification</h1>";

try {
    $db = Database::getConnection();
    echo "<div class='success'>✓ Database connection successful</div>";
    
    // Check transport_requests table
    echo "<h2>1. Transport Requests Table</h2>";
    try {
        $query = "DESCRIBE transport_requests";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<div class='success'>✓ transport_requests table exists with " . count($columns) . " columns</div>";
        
        // Check specific columns
        $columnNames = array_column($columns, 'Field');
        $requiredColumns = ['id', 'user_id', 'customer_name', 'contact_number', 'vehicle_type', 'date', 'pickup_time', 'pickup_location', 'dropoff_location', 'num_people', 'assigned_driver_id', 'assigned_vehicle_no', 'status'];
        
        echo "<table><tr><th>Column</th><th>Status</th></tr>";
        foreach ($requiredColumns as $col) {
            $status = in_array($col, $columnNames) ? '✓' : '✗';
            echo "<tr><td>$col</td><td>" . (in_array($col, $columnNames) ? '<span class=\"success\">Present</span>' : '<span class=\"error\">Missing</span>') . "</td></tr>";
        }
        echo "</table>";
    } catch (Exception $e) {
        echo "<div class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    
    // Check vehicle types
    echo "<h2>2. Vehicle Types</h2>";
    try {
        $query = "SELECT * FROM transport_vehicle_types ORDER BY type_id";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($types) >= 4) {
            echo "<div class='success'>✓ All required vehicle types present</div>";
        } else {
            echo "<div class='info'>⚠ Only " . count($types) . " vehicle types found. Recommend running: <a class='action-link' href='setup_vehicle_types.php'>Setup Vehicle Types</a></div>";
        }
        
        echo "<table><tr><th>Type ID</th><th>Type Name</th></tr>";
        foreach ($types as $type) {
            echo "<tr><td>{$type['type_id']}</td><td>{$type['type_name']}</td></tr>";
        }
        echo "</table>";
    } catch (Exception $e) {
        echo "<div class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }

    // Check transport reviews table
    echo "<h2>3. Transport Reviews Table</h2>";
    try {
        $query = "SELECT COUNT(*) as count FROM transport_reviews";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $reviewCount = $result['count'];
        echo "<div class='success'>✓ transport_reviews table exists with " . $reviewCount . " review(s)</div>";
    } catch (Exception $e) {
        echo "<div class='error'>✗ transport_reviews table is missing or inaccessible. Create it with <code>database/create_transport_reviews.php</code>.</div>";
    }
    
    // Check transport vehicles
    echo "<h2>4. Registered Transport Vehicles</h2>";
    try {
        $query = "SELECT COUNT(*) as count FROM transport_vehicle";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $vehicleCount = $result['count'];
        
        if ($vehicleCount > 0) {
            echo "<div class='success'>✓ " . $vehicleCount . " vehicle(s) registered</div>";
            
            // List vehicles
            $query = "SELECT v.vehicle_no, v.user_id, v.vehicle_type, v.psg_capacity, tu.full_name
                      FROM transport_vehicle v
                      LEFT JOIN transport_users tu ON TRIM(v.user_id) = TRIM(tu.user_id)
                      LIMIT 10";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<table><tr><th>Vehicle #</th><th>Driver</th><th>Type</th><th>Capacity</th></tr>";
            foreach ($vehicles as $v) {
                echo "<tr><td>{$v['vehicle_no']}</td><td>{$v['full_name']}</td><td>{$v['vehicle_type']}</td><td>{$v['psg_capacity']}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='error'>✗ No vehicles registered. Transport providers need to add vehicles.</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    
    // Check transport requests
    echo "<h2>4. Recent Transport Requests</h2>";
    try {
        $query = "SELECT COUNT(*) as count FROM transport_requests";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $requestCount = $result['count'];
        
        echo "<div class='success'>✓ " . $requestCount . " total request(s)</div>";
        
        // List recent requests
        $query = "SELECT id, customer_name, vehicle_type, date, status, assigned_driver_id, created_at
                  FROM transport_requests
                  ORDER BY created_at DESC
                  LIMIT 10";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($requests)) {
            echo "<table><tr><th>ID</th><th>Customer</th><th>Vehicle</th><th>Date</th><th>Status</th><th>Driver Assigned</th></tr>";
            foreach ($requests as $r) {
                $driverStatus = !empty($r['assigned_driver_id']) ? '✓ Yes' : '✗ No';
                $statusBadge = $r['status'] === 'confirmed' ? '<span class=\"success\">' . $r['status'] . '</span>' : '<span class=\"info\">' . $r['status'] . '</span>';
                echo "<tr><td>#{$r['id']}</td><td>{$r['customer_name']}</td><td>{$r['vehicle_type']}</td><td>{$r['date']}</td><td>{$statusBadge}</td><td>{$driverStatus}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='info'>No requests yet. Test by creating a new booking.</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    
    echo "<h2>5. Setup Summary</h2>";
    echo "<div class='success'>";
    echo "<p><strong>What's been fixed:</strong></p>";
    echo "<ul>";
    echo "<li>✓ Created transport_requests table with all required fields</li>";
    echo "<li>✓ Added status field to track request state (pending/confirmed/completed/cancelled)</li>";
    echo "<li>✓ Implemented automatic driver assignment based on:</li>";
    echo "<ul>";
    echo "<li>- Vehicle type and passenger capacity</li>";
    echo "<li>- Driver availability (no conflicting bookings on same date)</li>";
    echo "<li>- Driver rating and completed trips (prioritizes best drivers)</li>";
    echo "</ul>";
    echo "<li>✓ Requests now save with proper assignment status</li>";
    echo "<li>✓ Requests marked as 'confirmed' when driver is assigned</li>";
    echo "<li>✓ Requests marked as 'pending' when no driver available</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<h2>6. Next Steps</h2>";
    echo "<ol>";
    echo "<li><a class='action-link' href='setup_vehicle_types.php'>Setup Vehicle Types</a> - Ensures all vehicle types are configured</li>";
    echo "<li><a class='action-link' href='create_transport_requests_table_php.php'>Create Transport Requests Table</a> - Creates the table if missing</li>";
    echo "<li>Register vehicles as Transport Providers</li>";
    echo "<li>Test booking by creating transport requests as Tourist</li>";
    echo "</ol>";
    
} catch (PDOException $e) {
    echo "<div class='error'>✗ Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "<hr>";
echo "<p><a class='action-link' href='/CeylonGo/public/'>← Back to Home</a></p>";
echo "</body></html>";
?>
