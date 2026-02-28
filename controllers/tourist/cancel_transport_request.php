<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = json_decode(file_get_contents('php://input'), true);
    $requestId = isset($input['request_id']) ? (int) $input['request_id'] : 0;

    if ($requestId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid request ID']);
        exit();
    }

    // Verify this request belongs to the logged-in user and is pending
    $checkQuery = "SELECT id, status FROM transport_requests WHERE id = ? AND user_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ii", $requestId, $_SESSION['user_id']);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $request = $result->fetch_assoc();

    if (!$request) {
        echo json_encode(['success' => false, 'message' => 'Request not found']);
        exit();
    }

    if ($request['status'] !== 'pending') {
        echo json_encode(['success' => false, 'message' => 'Only pending requests can be cancelled']);
        exit();
    }

    // Cancel the request
    $updateQuery = "UPDATE transport_requests SET status = 'cancelled', assigned_driver_id = NULL, assigned_vehicle_no = NULL WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("i", $requestId);

    if ($updateStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Request cancelled successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to cancel request']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
