<?php
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'] ?? null;
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

    $sql = "UPDATE hotel_rooms SET room_number=?, room_type=?, rate=?, capacity=?, status=?, description=?, amenities=? WHERE id=?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdiisss", $room_number, $room_type, $rate, $capacity, $status, $description, $amenities_json, $room_id);
    
    if ($stmt->execute()) {
        header("Location: ../../views/hotel/rooms.php?success=Room updated successfully");
    } else {
        header("Location: ../../views/hotel/edit_room.php?id=$room_id&error=" . urlencode($stmt->error));
    }
    exit();
}
?>
