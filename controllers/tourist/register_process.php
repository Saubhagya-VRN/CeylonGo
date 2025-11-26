<?php
session_start();
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

    // Validation: Email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/\.[a-zA-Z]{2,6}$/', $email)) {
    die("<script>alert('Please enter a valid email address.'); window.history.back();</script>");
    }


    // Validation: Contact number (digits only, min 7, max 15)
    if (!preg_match('/^\d{7,15}$/', $contact)) {
        die("<script>alert('Please enter a valid contact number.'); window.history.back();</script>");
    }

    // Validation: Password match
    if ($password !== $confirm_password) {
        die("<script>alert('Passwords do not match.'); window.history.back();</script>");
    }

    // Validation: Password strength (8+ chars, uppercase, lowercase, number, special char)
    $hasUpperCase = preg_match('/[A-Z]/', $password);
    $hasLowerCase = preg_match('/[a-z]/', $password);
    $hasNumber = preg_match('/\d/', $password);
    $hasSpecialChar = preg_match('/[@$!%*?&]/', $password);
    $hasMinLength = strlen($password) >= 8;
    
    if (!$hasMinLength || !$hasUpperCase || !$hasLowerCase || !$hasNumber || !$hasSpecialChar) {
        die("<script>alert('You have to use 8 characters, uppercase, lowercase, number, and special character'); window.history.back();</script>");
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into tourist_users
    $stmt = $conn->prepare("INSERT INTO tourist_users (first_name, last_name, contact_number, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $fname, $lname, $contact, $email, $hashed_password);

    if ($stmt->execute()) {
        // Get the ID of the newly inserted tourist
        $ref_id = $stmt->insert_id;

        // Insert into users table
        $stmt2 = $conn->prepare("INSERT INTO users (email, password, role, ref_id) VALUES (?, ?, 'tourist', ?)");
        $stmt2->bind_param("sss", $email, $hashed_password, $ref_id);

        if ($stmt2->execute()) {
            // Set session variables for the newly registered user
            $_SESSION['user_id'] = $ref_id;
            $_SESSION['user_role'] = 'tourist';
            $_SESSION['user_type'] = 'tourist';
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $fname . ' ' . $lname;
            
            // Redirect to dashboard
            header("Location: ../../views/tourist/tourist_dashboard.php");
            exit();
        } else {
            // Rollback tourist_users insert if users insert fails
            $conn->query("DELETE FROM tourist_users WHERE id = $ref_id");
            if ($conn->errno === 1062) {
                echo "<script>alert('This email is already registered!'); window.history.back();</script>";
            } else {
                echo "<script>alert('Error adding user to users table: " . addslashes($conn->error) . "'); window.history.back();</script>";
            }
        }

        $stmt2->close();
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
