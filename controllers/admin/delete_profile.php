<?php
session_start();
include("../../config/database.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Delete admin from database
    $sql = "DELETE FROM admin WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Destroy session so user is logged out
        session_destroy();
        header("Location: admin_login.php?msg=Profile+Deleted");
        exit();
    } else {
        $_SESSION['error'] = "Error deleting profile.";
        header("Location: ../../views/admin/admin_profile.php");
        exit();
    }
}
?>
