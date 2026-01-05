<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('../../config/database.php');

    // Get form values safely
    $hotel_name = trim($_POST['hname']);
    $location = trim($_POST['location']);
    $city = trim($_POST['city']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Basic validation
    if (empty($hotel_name) || empty($location) || empty($city) || empty($contact) || empty($email) || empty($password) || empty($confirm_password)) {
        die("<script>alert('Please fill in all fields.'); window.history.back();</script>");
    }

    // Password match check
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

    // Handle image upload
    $target_dir = "../../public/uploads/hotels/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_name = time() . '_' . basename($_FILES["hotel_image"]["name"]);
    $target_file = $target_dir . $image_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["hotel_image"]["tmp_name"]);
    if($check === false){
        die("<script>alert('File is not an image.'); window.history.back();</script>");
    }

    $allowed_types = ["jpg", "jpeg", "png", "gif"];
    if(!in_array($imageFileType, $allowed_types)){
        die("<script>alert('Only JPG, JPEG, PNG & GIF files are allowed.'); window.history.back();</script>");
    }

    if(!move_uploaded_file($_FILES["hotel_image"]["tmp_name"], $target_file)){
        die("<script>alert('Error uploading the image.'); window.history.back();</script>");
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // ✅ 1. Insert into hotel_users
    $stmt = $conn->prepare("INSERT INTO hotel_users (hotel_name, location, city, hotel_image, contact_number, email, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("SQL prepare failed: " . $conn->error);
    }
    $stmt->bind_param("sssssss", $hotel_name, $location, $city, $image_name, $contact, $email, $hashed_password);

    if ($stmt->execute()) {
        // ✅ 2. Get the hotel user's ID
        $hotel_id = $conn->insert_id;

        // ✅ 3. Insert into main users table
        $role = 'hotel';
        $stmt2 = $conn->prepare("INSERT INTO users (ref_id, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("isss", $hotel_id, $email, $hashed_password, $role);
        $stmt2->execute();
        $stmt2->close();

        echo "<script>alert('Hotel registered successfully!'); window.location.href='../../views/hotel/dashboard.php';</script>";
    } else {
        if ($conn->errno == 1062) {
            echo "<script>alert('This email is already registered!'); window.history.back();</script>";
        } else {
            echo "<script>alert('Error: " . addslashes($conn->error) . "'); window.history.back();</script>";
        }
    }

    $stmt->close();
    $conn->close();
}
?>
