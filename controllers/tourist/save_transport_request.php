<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

// Set JSON response header
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to make a transport request.'
    ]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $requiredFields = ['customerName', 'contactNumber', 'date', 'numPeople', 'vehicleType', 'pickupLocation', 'pickupTime', 'dropoffLocation'];
        foreach ($requiredFields as $field) {
            if (empty($input[$field])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required field: ' . $field
                ]);
                exit();
            }
        }
        
        // Collect form data
        $userId = $_SESSION['user_id'];
        $customerName = trim($input['customerName']);
        $contactNumber = trim($input['contactNumber']);
        $date = trim($input['date']);
        $numPeople = intval($input['numPeople']);
        $vehicleType = trim($input['vehicleType']);
        $pickupLocation = trim($input['pickupLocation']);
        $pickupTime = trim($input['pickupTime']);
        $dropoffLocation = trim($input['dropoffLocation']);
        $notes = isset($input['notes']) ? trim($input['notes']) : null;
        $estimatedFare = isset($input['estimatedFare']) ? floatval(str_replace(['LKR', ',', ' '], '', $input['estimatedFare'])) : null;
        $distance = isset($input['distance']) ? floatval($input['distance']) : null;
        
        // Insert into database
        $query = "INSERT INTO transport_requests 
                  (user_id, customer_name, contact_number, date, num_people, vehicle_type, 
                   pickup_location, pickup_time, dropoff_location, notes, estimated_fare, distance, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isssisssssdd", 
            $userId, 
            $customerName, 
            $contactNumber, 
            $date, 
            $numPeople, 
            $vehicleType, 
            $pickupLocation, 
            $pickupTime, 
            $dropoffLocation, 
            $notes, 
            $estimatedFare, 
            $distance
        );
        
        if ($stmt->execute()) {
            $requestId = $stmt->insert_id;
            echo json_encode([
                'success' => true,
                'message' => 'Transport request submitted successfully!',
                'requestId' => $requestId
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to save request: ' . $stmt->error
            ]);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}

$conn->close();
?>
