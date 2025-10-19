<?php
require_once(__DIR__ . '/../../config/database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_type = 'transport';
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $nic = trim($_POST['nic']);
    $license_number = trim($_POST['license_number']);
    $vehicle_type = $_POST['vehicle_type'] ?? '';
    $vehicle_number = trim($_POST['vehicle_number']);
    $contact_number = trim($_POST['contact_number']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // ------------------- VALIDATION -------------------
    $errors = [];

    if (empty($first_name)) $errors[] = "First name is required.";
    if (empty($last_name)) $errors[] = "Last name is required.";
    if (!preg_match('/^(\d{9}[VvXx]|\d{12})$/', $nic)) $errors[] = "Invalid NIC number format.";
    if (!preg_match('/^[A-Za-z]{2,3}-?\d{4,6}$/', $license_number)) $errors[] = "Invalid license number format.";
    if (empty($vehicle_type)) $errors[] = "Vehicle type is required.";
    if (empty($vehicle_number)) $errors[] = "Vehicle number is required.";
    if (!preg_match('/^\d{10}$/', $contact_number)) $errors[] = "Contact number must be 10 digits.";
    if (empty($email)) $errors[] = "Email is required.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";

    if (!empty($errors)) {
        echo "<h2>Errors:</h2><ul>";
        foreach ($errors as $err) echo "<li>$err</li>";
        echo "</ul>";
        exit;
    }

    // ------------------- FILE UPLOAD -------------------
    $uploadDir = __DIR__ . '/../../public/uploads/';

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $license_file = "";
    if (!empty($_FILES['license_file']['name'])) {
        $license_file = basename($_FILES['license_file']['name']);
        move_uploaded_file($_FILES['license_file']['tmp_name'], $uploadDir . $license_file);
    }

    $vehicle_photo = "";
    if (!empty($_FILES['vehicle_photo']['name'])) {
        $vehicle_photo = basename($_FILES['vehicle_photo']['name']);
        move_uploaded_file($_FILES['vehicle_photo']['tmp_name'], $uploadDir . $vehicle_photo);
    }

    $password_hashed = password_hash($password, PASSWORD_BCRYPT);

    // ------------------- INSERT INTO DATABASE -------------------
    $sql = "INSERT INTO transport_users 
        (user_type, first_name, last_name, nic, license_number, license_file, vehicle_type, vehicle_number, vehicle_photo, contact_number, email, password)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Prepare failed: " . $conn->error);

    $stmt->bind_param(
        "ssssssssssss",
        $user_type,
        $first_name,
        $last_name,
        $nic,
        $license_number,
        $license_file,
        $vehicle_type,
        $vehicle_number,
        $vehicle_photo,
        $contact_number,
        $email,
        $password_hashed
    );

    if ($stmt->execute()) {
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
                <p>Thank you, <?php echo htmlspecialchars($first_name); ?>, you are now registered as a transport provider.</p>
                <a href="transport_register.php">Register Another Transport Provider</a>
            </main>
            <?php include __DIR__ . '/../../views/tourist/footer.php'; ?>
        </body>
        </html>
        <?php
    } else {
        echo "<h2>Registration failed:</h2><p>" . $stmt->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
