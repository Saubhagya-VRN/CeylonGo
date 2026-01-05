<?php
require_once '../../config/db.php';

// Hardcode valid hotel ID
$hotel_id = 1;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_number = $_POST['room_number'] ?? '';
    $room_type = $_POST['room_type'] ?? '';
    $rate = $_POST['rate'] ?? 0;
    $capacity = $_POST['capacity'] ?? 1;
    $status = $_POST['status'] ?? 'available';
    $description = $_POST['description'] ?? null;
    
    // Handle amenities
    $amenities = [];
    if (isset($_POST['amenities']) && is_array($_POST['amenities'])) {
        $amenities = $_POST['amenities'];
    }
    $amenities_json = json_encode($amenities);

    $sql = "INSERT INTO hotel_rooms (hotel_id, room_number, room_type, rate, capacity, description, amenities, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("issdisss", $hotel_id, $room_number, $room_type, $rate, $capacity, $description, $amenities_json, $status);

    if ($stmt->execute()) {
        header("Location: ../../views/hotel/rooms.php?success=" . urlencode("Room added successfully"));
        exit();
    } else {
        header("Location: ../../views/hotel/add_room.php?error=" . urlencode($stmt->error));
        exit();
    }
} else {
    header("Location: ../../views/hotel/add_room.php");
    exit();
}
?>
