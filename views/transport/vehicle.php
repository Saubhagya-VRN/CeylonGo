<?php
// Vehicle registration page with database connection
require_once "../config/config.php";
require_once "../core/Database.php";
require_once "../models/Vehicle.php";
require_once "../models/VehicleType.php";

$user_id = "U68f28688787"; // Current user ID
$message = "";
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $db = Database::getConnection();
        
        // Get form data
        $vehicle_no = $_POST['vehicle_no'] ?? '';
        $vehicle_type = $_POST['vehicle_type'] ?? '';
        $psg_capacity = $_POST['psg_capacity'] ?? '';
        
        // Handle file upload
        $image = '';
        if (isset($_FILES['vehicle_image']) && $_FILES['vehicle_image']['error'] == 0) {
            $uploadDir = '../../uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileInfo = pathinfo($_FILES['vehicle_image']['name']);
            $extension = $fileInfo['extension'];
            $newFileName = uniqid('img_', true) . '.' . $extension;
            $targetPath = $uploadDir . $newFileName;
            
            if (move_uploaded_file($_FILES['vehicle_image']['tmp_name'], $targetPath)) {
                $image = $newFileName;
            }
        }
        
        // Create vehicle object and save
        $vehicle = new Vehicle($db);
        $vehicle->vehicle_no = $vehicle_no;
        $vehicle->user_id = $user_id;
        $vehicle->vehicle_type = $vehicle_type;
        $vehicle->psg_capacity = $psg_capacity;
        $vehicle->image = $image;
        
        if ($vehicle->addVehicle()) {
            $message = "Vehicle added successfully!";
            // Redirect to profile page after 2 seconds
            header("refresh:2;url=profile.php");
        } else {
            $error = "Failed to add vehicle. Please try again.";
        }
        
      } catch (Exception $e) {
          $error = "Error: " . $e->getMessage();
      }
}

      // Get vehicle types for dropdown
      try {
          $db = Database::getConnection();
          $vehicleTypeModel = new VehicleType($db);
          $vehicleTypes = $vehicleTypeModel->getAllTypes()->fetchAll(PDO::FETCH_ASSOC);
      } catch (Exception $e) {
          $vehicleTypes = [];
      }
      ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Add Vehicle</title>
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/base.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/navbar.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/sidebar.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/footer.css">
    
    <!-- Component styles -->
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/cards.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/buttons.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/forms.css">
    
    <!-- Page-specific styles -->
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/timeline.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/tables.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/profile.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/reviews.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/charts.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/vehicle.css">

    <!-- Responsive styles (always last) -->
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/responsive.css">   
    
    <link rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <!-- Navbar -->
  <header class="navbar">
    <div class="branding">
      <img src="/Ceylon_Go/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="#">Home</a>
      <a href="#">Logout</a>
      <img src="/Ceylon_Go/public/images/profile.jpeg" alt="User" class="profile-pic">
    </nav>
  </header>

  <div class="page-wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
      <ul>
        <li><a href="dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Bookings</a></li>
        <li><a href="pending"><i class="fa-regular fa-clock"></i> Pending Bookings</a></li>
        <li><a href="cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Bookings</a></li>
        <li><a href="review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </div>

    <div class="main-content">
        <h2>Please fill in your vehicle details below to register.</h2>

        <!-- Success/Error Messages -->
        <?php if ($message): ?>
            <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0;">
                <?= $message ?>
                <br><small>Redirecting to profile page...</small>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px 0;">
                <?= $error ?>
            </div>
        <?php endif; ?>

  <!-- Registration Form -->
  <main class="form-container">
    <form method="POST" enctype="multipart/form-data">

      <label>Vehicle Type</label>
      <select name="vehicle_type" required>
        <option value="">Select Vehicle Type</option>
        <?php foreach($vehicleTypes as $type): ?>
          <option value="<?= $type['type_id'] ?>"><?= $type['type_name'] ?></option>
        <?php endforeach; ?>
      </select>

        <script>
    // Handle successful form submission
    <?php if ($message): ?>
      setTimeout(function() {
        window.location.href = 'profile';
      }, 2000);
    <?php endif; ?>
  </script>

      <label>Vehicle Number</label>
      <input type="text" name="vehicle_no" placeholder="Enter your Vehicle Number" required>

      <label>Upload Vehicle Photo</label>
      <input type="file" name="vehicle_image" accept="image/*">

      <label>Passenger Capacity</label>
      <input type="number" name="psg_capacity" placeholder="Enter your Vehicle's Passenger Capacity" required>

      <div class="buttons">
        <button type="submit" class="register-btn">Add Vehicle</button>
      </div>

    </form>
  </main>

  
  <!-- Footer -->
  <footer>
    <ul>
      <li><a href="#">About Us</a></li>
      <li><a href="#">Contact Us</a></li>
    </ul>
  </footer>


</body>
</html>