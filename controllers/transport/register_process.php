<?php
require_once(__DIR__ . '/../../config/database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ------------------- GET FORM DATA -------------------
    // Personal Information
    $full_name = trim($_POST['full_name']);
    $dob = trim($_POST['dob']);
    $nic = trim($_POST['nic']);
    $address = trim($_POST['address']);
    $contact_no = trim($_POST['contact_no']);
    
    // License Information
    $license_no = trim($_POST['license_no']);
    $license_exp_date = trim($_POST['license_exp_date']);
    
    // Vehicle Information
    $vehicle_no = trim($_POST['vehicle_no']);
    $vehicle_type = $_POST['vehicle_type'] ?? '';
    $psg_capacity = intval($_POST['psg_capacity']);
    
    // Account Information
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // ------------------- VALIDATION -------------------
    $errors = [];

    if (empty($full_name)) $errors[] = "Full name is required.";
    if (empty($dob)) $errors[] = "Date of birth is required.";
    if (!preg_match('/^(\d{9}[VvXx]|\d{12})$/', $nic)) $errors[] = "Invalid NIC number format.";
    if (empty($address)) $errors[] = "Address is required.";
    if (!preg_match('/^\d{10}$/', $contact_no)) $errors[] = "Contact number must be 10 digits.";
    if (empty($license_no)) $errors[] = "License number is required.";
    if (empty($license_exp_date)) $errors[] = "License expiry date is required.";
    if (empty($vehicle_no)) $errors[] = "Vehicle number is required.";
    if (empty($vehicle_type)) $errors[] = "Vehicle type is required.";
    if ($psg_capacity < 1) $errors[] = "Passenger capacity must be at least 1.";
    if (empty($email)) $errors[] = "Email is required.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";

    // Password strength validation (8+ chars, uppercase, lowercase, number, special char)
    $hasUpperCase = preg_match('/[A-Z]/', $password);
    $hasLowerCase = preg_match('/[a-z]/', $password);
    $hasNumber = preg_match('/\d/', $password);
    $hasSpecialChar = preg_match('/[@$!%*?&]/', $password);
    $hasMinLength = strlen($password) >= 8;
    
    if (!$hasMinLength || !$hasUpperCase || !$hasLowerCase || !$hasNumber || !$hasSpecialChar) {
        $errors[] = "Password must have 8+ characters, uppercase, lowercase, number, and special character.";
    }

    // Check if email already exists in transport_users
    $check_email = $conn->prepare("SELECT user_id FROM transport_users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    if ($check_email->get_result()->num_rows > 0) {
        $errors[] = "Email already exists. Please use a different email.";
    }
    $check_email->close();

    // Check if email exists in central users table
    $check_users_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_users_email->bind_param("s", $email);
    $check_users_email->execute();
    if ($check_users_email->get_result()->num_rows > 0) {
        $errors[] = "Email already registered. Please use a different email.";
    }
    $check_users_email->close();

    if (!empty($errors)) {
        echo "<h2>Errors:</h2><ul>";
        foreach ($errors as $err) echo "<li>$err</li>";
        echo "</ul>";
        echo "<a href='javascript:history.back()'>Go Back</a>";
        exit;
    }

    // ------------------- FILE UPLOAD -------------------
    $uploadDir = __DIR__ . '/../../public/uploads/transport/';

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    // Profile Image Upload
    $profile_image = "";
    if (!empty($_FILES['profile_image']['name']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $fileExtension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $profile_image = 'profile_' . uniqid() . '.' . $fileExtension;
        if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadDir . $profile_image)) {
            $errors[] = "Failed to upload profile image.";
        }
    }

    // License Image Upload
    $license_image = "";
    if (!empty($_FILES['license_image']['name']) && $_FILES['license_image']['error'] === UPLOAD_ERR_OK) {
        $fileExtension = pathinfo($_FILES['license_image']['name'], PATHINFO_EXTENSION);
        $license_image = 'img_' . uniqid() . '.' . $fileExtension;
        if (!move_uploaded_file($_FILES['license_image']['tmp_name'], $uploadDir . $license_image)) {
            $errors[] = "Failed to upload license image.";
        }
    }

    // Vehicle Image Upload
    $vehicle_image = "";
    if (!empty($_FILES['vehicle_image']['name']) && $_FILES['vehicle_image']['error'] === UPLOAD_ERR_OK) {
        $fileExtension = pathinfo($_FILES['vehicle_image']['name'], PATHINFO_EXTENSION);
        $vehicle_image = 'img_' . uniqid() . '.' . $fileExtension;
        if (!move_uploaded_file($_FILES['vehicle_image']['tmp_name'], $uploadDir . $vehicle_image)) {
            $errors[] = "Failed to upload vehicle image.";
        }
    }

    // Check for file upload errors after processing
    if (!empty($errors)) {
        echo "<h2>Errors:</h2><ul>";
        foreach ($errors as $err) echo "<li>$err</li>";
        echo "</ul>";
        echo "<a href='javascript:history.back()'>Go Back</a>";
        exit;
    }

    // Generate unique user ID
    $user_id = 'TP' . uniqid();
    $password_hashed = password_hash($password, PASSWORD_BCRYPT);

    // ------------------- INSERT INTO DATABASE -------------------
    // Enable mysqli exceptions for better error handling
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    // Disable autocommit and start transaction for data integrity
    $conn->autocommit(false);
    $conn->begin_transaction();

    try {
        // 1. Insert into transport_users table
        $sql = "INSERT INTO transport_users 
            (user_id, full_name, dob, nic, address, contact_no, profile_image, email, psw)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param(
            "sssssssss",
            $user_id,
            $full_name,
            $dob,
            $nic,
            $address,
            $contact_no,
            $profile_image,
            $email,
            $password_hashed
        );

        if (!$stmt->execute()) {
            throw new Exception("Insert into transport_users failed: " . $stmt->error);
        }
        $stmt->close();

        // 2. Insert into transport_license table
        $license_sql = "INSERT INTO transport_license 
            (license_no, license_exp_date, image, driver_id)
            VALUES (?, ?, ?, ?)";

        $license_stmt = $conn->prepare($license_sql);
        if (!$license_stmt) {
            throw new Exception("Prepare license insert failed: " . $conn->error);
        }

        $license_stmt->bind_param("ssss", $license_no, $license_exp_date, $license_image, $user_id);
        
        if (!$license_stmt->execute()) {
            throw new Exception("Insert into transport_license failed: " . $license_stmt->error);
        }
        $license_stmt->close();

        // 3. Insert into transport_vehicle table
        $vehicle_sql = "INSERT INTO transport_vehicle 
            (vehicle_no, user_id, vehicle_type, image, psg_capacity)
            VALUES (?, ?, ?, ?, ?)";

        $vehicle_stmt = $conn->prepare($vehicle_sql);
        if (!$vehicle_stmt) {
            throw new Exception("Prepare vehicle insert failed: " . $conn->error);
        }

        $vehicle_stmt->bind_param("ssssi", $vehicle_no, $user_id, $vehicle_type, $vehicle_image, $psg_capacity);
        
        if (!$vehicle_stmt->execute()) {
            throw new Exception("Insert into transport_vehicle failed: " . $vehicle_stmt->error);
        }
        $vehicle_stmt->close();

        // 4. Insert into users table for authentication
        $role = 'transport';
        $users_sql = "INSERT INTO users (ref_id, email, password, role) VALUES (?, ?, ?, ?)";
        $users_stmt = $conn->prepare($users_sql);
        
        if (!$users_stmt) {
            throw new Exception("Prepare users insert failed: " . $conn->error);
        }

        $users_stmt->bind_param("ssss", $user_id, $email, $password_hashed, $role);
        
        if (!$users_stmt->execute()) {
            throw new Exception("Insert into users table failed: " . $users_stmt->error);
        }
        
        // Verify the insert was successful
        if ($users_stmt->affected_rows <= 0) {
            throw new Exception("Users table insert did not affect any rows. User ID: " . $user_id);
        }
        
        $users_stmt->close();
        
        // Debug log - can be removed after verification
        error_log("Transport registration successful - User ID: $user_id, Email: $email inserted into users table");

        // Commit transaction
        $conn->commit();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Registration Successful</title>
            <link rel="stylesheet" href="../../public/css/transport/register.css">
            <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
            <link rel="stylesheet" href="../../public/css/tourist/footer.css">
            <style>
                body { background-color: #f0f8f0; }
                .success-container { 
                    max-width: 600px; margin: 50px auto; 
                    background: #fff; padding: 30px; border-radius: 10px; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.1); text-align: center; 
                }
                .success-container h2 { color: #4CAF50; margin-bottom: 15px; }
                .success-container p { font-size: 18px; }
                .success-container a { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; }
                .success-container a:hover { background: #45a049; }
            </style>
        </head>
        <body>
            <?php include __DIR__ . '/../../views/index_navbar.php'; ?>
            <main class="success-container">
                <h2>Registration Successful!</h2>
                <p>Thank you, <?php echo htmlspecialchars($full_name); ?>, you are now registered as a transport provider.</p>
                <a href="/CeylonGo/public/login">Login to your account</a>
            </main>
            <?php include __DIR__ . '/../../views/tourist/footer.php'; ?>
        </body>
        </html>
        <?php
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $conn->autocommit(true); // Re-enable autocommit
        
        echo "<h2>Registration failed:</h2>";
        echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>User ID:</strong> " . htmlspecialchars($user_id) . "</p>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
        error_log("Transport registration failed: " . $e->getMessage() . " | User ID: $user_id | Email: $email");
        echo "<a href='javascript:history.back()'>Go Back</a>";
    }

    $conn->autocommit(true); // Ensure autocommit is re-enabled
    $conn->close();
}
?>
