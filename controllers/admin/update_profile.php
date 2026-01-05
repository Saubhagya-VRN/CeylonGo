<?php
session_start();
include("../../config/database.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_ref_id = $_SESSION['user_ref_id']; // admin.id
    $username     = $_POST['username'];
    $email        = $_POST['email'];
    $phone        = $_POST['phone'];    
    $role         = $_POST['role'];
    $password     = $_POST['password'];

    // Update admin table
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE admin SET username=?, email=?, phone_number=?, role=?, password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $username, $email, $phone, $role, $hashedPassword, $admin_ref_id);
    } else {
        $sql = "UPDATE admin SET username=?, email=?, phone_number=?, role=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $username, $email, $phone, $role, $admin_ref_id);
    }
    $stmt->execute();

    // Update users table to keep login in sync
    if (!empty($password)) {
        $sql2 = "UPDATE users SET email=?, password=? WHERE ref_id=? AND role='admin'";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("sss", $email, $hashedPassword, $admin_ref_id);
    } else {
        $sql2 = "UPDATE users SET email=? WHERE ref_id=? AND role='admin'";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("ss", $email, $admin_ref_id);
    }
    $stmt2->execute();

    $_SESSION['success'] = "Profile updated successfully!";
    header("Location: ../../views/admin/admin_profile.php");
    exit();
}
?>
