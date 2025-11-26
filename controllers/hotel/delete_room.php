<?php
require_once '../../config/db.php';

$room_id = $_GET['id'] ?? $_POST['room_id'] ?? null;

if ($room_id) {
    $sql = "DELETE FROM hotel_rooms WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_id);
    
    if ($stmt->execute()) {
        header("Location: ../../views/hotel/rooms.php?success=Room deleted successfully");
    } else {
        header("Location: ../../views/hotel/rooms.php?error=" . urlencode($stmt->error));
    }
    exit();
}

header("Location: ../../views/hotel/rooms.php?error=Invalid request");
exit();
?>
