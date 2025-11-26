<?php
require_once(__DIR__ . '/../../config/database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_type = 'guide';
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $nic = trim($_POST['nic']);
    $license_number = trim($_POST['license_number']);
    $specialization = $_POST['specialization'] ?? '';
    $languages = trim($_POST['languages']);
    $experience = intval($_POST['experience']);
    $contact_number = trim($_POST['contact_number']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // ------------------- VALIDATION -------------------
    $errors = [];

    // Required fields
    if (empty($first_name)) $errors[] = "First name is required.";
    if (empty($last_name)) $errors[] = "Last name is required.";
    if (empty($nic)) $errors[] = "NIC number is required.";
    if (empty($license_number)) $errors[] = "License number is required.";
    if (empty($specialization)) $errors[] = "Specialization is required.";
    if (empty($languages)) $errors[] = "Languages are required.";
    if (empty($contact_number)) $errors[] = "Contact number is required.";
    if (empty($email)) $errors[] = "Email is required.";
    if (empty($password)) $errors[] = "Password is required.";
    if (empty($confirm_password)) $errors[] = "Confirm password is required.";

    // Password match
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Password strength validation (8+ chars, uppercase, lowercase, number, special char)
    $hasUpperCase = preg_match('/[A-Z]/', $password);
    $hasLowerCase = preg_match('/[a-z]/', $password);
    $hasNumber = preg_match('/\d/', $password);
    $hasSpecialChar = preg_match('/[@$!%*?&]/', $password);
    $hasMinLength = strlen($password) >= 8;
    
    if (!$hasMinLength || !$hasUpperCase || !$hasLowerCase || !$hasNumber || !$hasSpecialChar) {
        $errors[] = "You have to use 8 characters, uppercase, lowercase, number, and special character";
    }

    // NIC validation (Sri Lankan format)
    if (!preg_match('/^(\d{9}[VvXx]|\d{12})$/', $nic)) {
        $errors[] = "Invalid NIC number format.";
    }

   
    // Contact number validation (10 digits)
    if (!preg_match('/^\d{10}$/', $contact_number)) {
        $errors[] = "Contact number must be 10 digits.";
    }

    // If there are errors, show them and stop
    if (!empty($errors)) {
        echo "<h2>Registration Errors:</h2><ul>";
        foreach ($errors as $err) {
            echo "<li>$err</li>";
        }
        echo "</ul>";
        exit;
    }

    // ------------------- FILE UPLOADS -------------------
    $profile_photo = "";
    if (!empty($_FILES["profile_photo"]["name"])) {
        $targetDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $profile_photo = basename($_FILES["profile_photo"]["name"]);
        move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $targetDir . $profile_photo);
    }

    $license_file = "";
    if (!empty($_FILES["license_file"]["name"])) {
        $targetDir = __DIR__ . '/../../public/uploads/';
        $license_file = basename($_FILES["license_file"]["name"]);
        move_uploaded_file($_FILES["license_file"]["tmp_name"], $targetDir . $license_file);
    }

    // ------------------- DATABASE INSERT -------------------
    $password_hashed = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO guide_users 
        (user_type, first_name, last_name, nic, license_number, specialization, languages, experience, profile_photo, license_file, contact_number, email, password)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Prepare failed: " . $conn->error);

    $stmt->bind_param(
        "sssssssssssss",
        $user_type,
        $first_name,
        $last_name,
        $nic,
        $license_number,
        $specialization,
        $languages,
        $experience,
        $profile_photo,
        $license_file,
        $contact_number,
        $email,
        $password_hashed
    );

    if ($stmt->execute()) {
    // Insert into shared users table
    $sql2 = "INSERT INTO users (email, password, role) VALUES (?, ?, ?)";
    $stmt2 = $conn->prepare($sql2);
    
    if (!$stmt2) {
        die("Error preparing users insert: " . $conn->error);
    }
    
    $stmt2->bind_param("sss", $email, $password_hashed, $user_type);
    
    if ($stmt2->execute()) {
        // Redirect to login after successful registration
        header("Location: ../../views/guide/guide_dashboard.php");
        exit;
    } else {
        die("Error inserting into users table: " . $stmt2->error);
    }

    $stmt2->close();
} else {
    echo "<h2>Registration failed:</h2>";
    echo "<p>" . $stmt->error . "</p>";
}
}
?>
