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
        // --- Styled success page ---
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
                    max-width: 600px; 
                    margin: 50px auto; 
                    background: #fff; 
                    padding: 30px; 
                    border-radius: 10px; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.1); 
                    text-align: center; 
                }
                .success-container h2 { color: #4CAF50; margin-bottom: 15px; }
                .success-container p { font-size: 18px; }
                .success-container a { 
                    display: inline-block; 
                    margin-top: 20px; 
                    padding: 10px 20px; 
                    background: #4CAF50; 
                    color: white; 
                    text-decoration: none; 
                    border-radius: 5px; 
                }
                .success-container a:hover { background: #45a049; }
            </style>
        </head>
        <body>
            <?php include '../tourist/navbar.php'; ?>
            <main class="success-container">
                <h2>Registration Successful!</h2>
                <p>Thank you, <?php echo htmlspecialchars($first_name); ?>, you are now registered as a tour guide.</p>
                <a href="guide_register.php">Register Another Guide</a>
            </main>
            <?php include '../tourist/footer.php'; ?>
        </body>
        </html>
        <?php
    } else {
        echo "<h2>Registration failed:</h2>";
        echo "<p>" . $stmt->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
