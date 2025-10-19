<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('../../config/database.php');

    // Get form values safely
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validation: Check if all fields are filled
    if (empty($fname) || empty($lname) || empty($contact) || empty($email) || empty($password) || empty($confirm_password)) {
        die("<script>alert('Please fill in all fields.'); window.history.back();</script>");
    }

    // Validation: Password match
    if ($password !== $confirm_password) {
        die("<script>alert('Passwords do not match.'); window.history.back();</script>");
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare & insert
    $stmt = $conn->prepare("INSERT INTO tourist_users (first_name, last_name, contact_number, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $fname, $lname, $contact, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='../../views/tourist/tourist_dashboard.php';</script>";
    } else {
        if ($conn->errno === 1062) {
            echo "<script>alert('This email is already registered!'); window.history.back();</script>";
        } else {
            echo "<script>alert('Error: " . addslashes($conn->error) . "'); window.history.back();</script>";
        }
    }

    $stmt->close();
    $conn->close();
}
?>
