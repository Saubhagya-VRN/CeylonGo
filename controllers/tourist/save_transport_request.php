<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/core/Database.php';
require_once dirname(__DIR__, 2) . '/models/Vehicle.php';

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

        // Map vehicle type string to DB type ID for driver lookup
        $vehicleTypeMap = [
            'Tuk' => '1',
            'Car' => '2',
            'Minivan' => '2',
            'Minivan AC' => '2',
            'Bus' => '2',
            'Bus AC' => '2'
        ];
        $dbTypeId = $vehicleTypeMap[$vehicleType] ?? null;

        // Find the best available driver (ranked by reviews)
        $assignedDriverId = null;
        $assignedVehicleNo = null;
        $driverName = null;
        $driverContact = null;

        if ($dbTypeId) {
            $pdoDb = Database::getConnection();
            $vehicleModel = new Vehicle($pdoDb);
            $bestVehicle = $vehicleModel->findAvailableVehicle($dbTypeId, $date, $numPeople);
            
            if ($bestVehicle) {
                $assignedDriverId = trim($bestVehicle['user_id']);
                $assignedVehicleNo = $bestVehicle['vehicle_no'];
                $driverName = $bestVehicle['driver_name'];
                $driverContact = $bestVehicle['driver_contact'];
            }
        }
        
        // Insert into database with driver assignment
        $query = "INSERT INTO transport_requests 
                  (user_id, customer_name, contact_number, date, num_people, vehicle_type, 
                   pickup_location, pickup_time, dropoff_location, notes, estimated_fare, distance, 
                   status, assigned_driver_id, assigned_vehicle_no) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?)";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isssisssssddss", 
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
            $distance,
            $assignedDriverId,
            $assignedVehicleNo
        );
        
        if ($stmt->execute()) {
            $requestId = $stmt->insert_id;
            
            $responseData = [
                'success' => true,
                'message' => 'Transport request submitted successfully!',
                'requestId' => $requestId
            ];

            if ($assignedDriverId) {
                $responseData['driverAssigned'] = true;
                $responseData['driverName'] = $driverName;
                $responseData['vehicleNo'] = $assignedVehicleNo;
                $responseData['message'] = 'Transport request submitted! A driver has been assigned and will review your request.';
            } else {
                $responseData['driverAssigned'] = false;
                $responseData['message'] = 'Transport request submitted! No drivers are currently available for this vehicle type and date. We will assign one as soon as possible.';
            }
            
            echo json_encode($responseData);
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
