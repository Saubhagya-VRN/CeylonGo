<?php
session_start();
include("../../config/database.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_ref_id = $_SESSION['user_ref_id'];

    // Delete from admin table
    $sql = "DELETE FROM admin WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_ref_id);
    $stmt->execute();

    // Delete from users table
    $sql2 = "DELETE FROM users WHERE ref_id=? AND role='admin'";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $admin_ref_id);
    $stmt2->execute();

    session_destroy();
    header("Location: ../../views/login.php?msg=Profile+Deleted");
    exit();
}
?>
