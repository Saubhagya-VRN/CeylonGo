<?php
session_start();
include("../../config/database.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = $_POST['id'];
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $phone    = $_POST['phone'];
    $role     = $_POST['role'];
    $password = $_POST['password'];

    if (!empty($password)) {
        // hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE admin SET username=?, Email=?, Phone_Number=?, Role=?, password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $username, $email, $phone, $role, $hashedPassword, $id);
    } else {
        $sql = "UPDATE admin SET username=?, Email=?, Phone_Number=?, Role=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $username, $email, $phone, $role, $id);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Profile updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating profile.";
    }
    header("Location: ../../views/admin/admin_profile.php");
    exit();
}
?>
