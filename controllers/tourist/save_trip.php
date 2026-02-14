<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

// Set JSON response header
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to create a trip.'
    ]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $requiredFields = ['numberOfPeople', 'startDate', 'destination', 'numberOfDays'];
        foreach ($requiredFields as $field) {
            if (empty($input[$field])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required field: ' . $field
                ]);
                exit();
            }
        }
        
        // Get user info
        $userId = $_SESSION['user_id'];
        
        // Fetch customer name from tourist_users
        $userQuery = "SELECT CONCAT(first_name, ' ', last_name) as full_name FROM tourist_users WHERE id = ?";
        $userStmt = $conn->prepare($userQuery);
        $userStmt->bind_param("i", $userId);
        $userStmt->execute();
        $userResult = $userStmt->get_result();
        $userData = $userResult->fetch_assoc();
        $customerName = $userData['full_name'];
        $userStmt->close();
        
        // Collect form data
        $numberOfPeople = intval($input['numberOfPeople']);
        $startDate = trim($input['startDate']);
        $destination = trim($input['destination']);
        $numberOfDays = intval($input['numberOfDays']);
        
        // Insert into database
        $query = "INSERT INTO trips 
                  (user_id, customer_name, number_of_people, start_date, destination, number_of_days, status) 
                  VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("issisi", 
            $userId, 
            $customerName, 
            $numberOfPeople, 
            $startDate, 
            $destination, 
            $numberOfDays
        );
        
        if ($stmt->execute()) {
            $tripId = $stmt->insert_id;
            echo json_encode([
                'success' => true,
                'message' => 'Trip created successfully!',
                'tripId' => $tripId
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create trip: ' . $stmt->error
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
