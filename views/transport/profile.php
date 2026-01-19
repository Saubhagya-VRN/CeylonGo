<?php
// Profile page with database connection
require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../core/Database.php";
require_once __DIR__ . "/../../models/User.php";
require_once __DIR__ . "/../../models/License.php";
require_once __DIR__ . "/../../models/Vehicle.php";

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check for session messages (from redirect after update)
$message = null;
$error = null;

if (isset($_SESSION['profile_message'])) {
    $message = $_SESSION['profile_message'];
    unset($_SESSION['profile_message']);
}

if (isset($_SESSION['profile_error'])) {
    $error = $_SESSION['profile_error'];
    unset($_SESSION['profile_error']);
}

// Check if user is logged in
if(isset($_SESSION['transporter_id'])){
  $user_id = $_SESSION['transporter_id'];
}
else{
  header('Location: /CeylonGo/views/transport/login.php');
  exit();
}


// Handle all POST operations
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    try {
        $db = Database::getConnection();
        $vehicleModel = new Vehicle($db);
        
        if ($_POST['action'] == 'edit_vehicle') {
            // Handle vehicle edit
            $old_vehicle_no = $_POST['vehicle_no_old'];
            $new_vehicle_no = $_POST['vehicle_no'];
            $vehicle_type = $_POST['vehicle_type'];
            $psg_capacity = $_POST['psg_capacity'];
            
            // Validate passenger capacity
            if ($psg_capacity < 1) {
                $error = "Passenger capacity must be at least 1.";
            } else {
                // Handle file upload
                $image = '';
if (isset($_FILES['vehicle_image']) && $_FILES['vehicle_image']['error'] == 0) {
$uploadDir = dirname(__DIR__, 2) . "/uploads/";

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileInfo = pathinfo($_FILES['vehicle_image']['name']);
    $extension = $fileInfo['extension'];
    $newFileName = uniqid('img_', true) . '.' . $extension;
    $targetPath = $uploadDir . $newFileName;

    if (move_uploaded_file($_FILES['vehicle_image']['tmp_name'], $targetPath)) {
        $image = $newFileName;
    } else {
        echo "<script>alert('Failed to move uploaded file');</script>";
    }
}

                
                // Update vehicle
                $vehicleModel->vehicle_no = $new_vehicle_no;
                $vehicleModel->user_id = trim($user_id);
                $vehicleModel->vehicle_type = $vehicle_type;
                $vehicleModel->psg_capacity = $psg_capacity;
                if ($image) {
                    $vehicleModel->image = $image;
                }
                
                if ($vehicleModel->updateVehicle($old_vehicle_no)) {
                    $message = "Vehicle updated successfully!";
                } else {
                    $error = "Failed to update vehicle.";
                }
            }
            
        } elseif ($_POST['action'] == 'update_profile') {
            // Handle profile update
            $userModel = new User($db);
            $userModel->user_id = trim($user_id);
            $userModel->full_name = $_POST['full_name'];
            $userModel->dob = $_POST['dob'];
            $userModel->address = $_POST['address'];
            $userModel->contact_no = $_POST['contact_no'];
            $userModel->email = $_POST['email'];
            
            try {
                if ($userModel->updateUser()) {
                    $_SESSION['profile_message'] = "Profile updated successfully!";
                } else {
                    $_SESSION['profile_error'] = "Failed to update profile. No rows affected.";
                }
            } catch (PDOException $e) {
                $_SESSION['profile_error'] = "Database error: " . $e->getMessage();
            }
            // Redirect to refresh page with updated data
            header("Location: /CeylonGo/public/transporter/profile");
            exit();
            
        } elseif ($_POST['action'] == 'update_license') {
            // Handle license update
            $licenseModel = new License($db);
            $licenseModel->driver_id = trim($user_id);
            $licenseModel->license_no = $_POST['license_no'];
            $licenseModel->license_exp_date = $_POST['license_exp_date'];
            $licenseModel->image = ''; // Empty image for now
            
            try {
                if ($licenseModel->updateLicense()) {
                    $_SESSION['profile_message'] = "License information updated successfully!";
                } else {
                    $_SESSION['profile_error'] = "Failed to update license information.";
                }
            } catch (PDOException $e) {
                $_SESSION['profile_error'] = "Database error: " . $e->getMessage();
            }
            // Redirect to refresh page with updated data
            header("Location: /CeylonGo/public/transporter/profile");
            exit();
            
        } elseif ($_POST['action'] == 'delete_vehicle') {
            // Handle vehicle delete
            $vehicle_no = $_POST['vehicle_no'];
            
            if ($vehicleModel->deleteVehicle($vehicle_no, trim($user_id))) {
                $message = "Vehicle deleted successfully!";
            } else {
                $error = "Failed to delete vehicle.";
            }
        } elseif ($_POST['action'] == 'update_profile_image') {
            // Handle profile image upload
            
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                $uploadDir = dirname(__DIR__, 2) . "/public/uploads/transport/";
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $fileType = $_FILES['profile_image']['type'];
                
                if (!in_array($fileType, $allowedTypes)) {
                    $_SESSION['profile_error'] = "Invalid file type. Please upload a JPEG, PNG, GIF, or WebP image.";
                } else {
                    // Generate unique filename - sanitize user_id for filename
                    $fileInfo = pathinfo($_FILES['profile_image']['name']);
                    $extension = strtolower($fileInfo['extension']);
                    $sanitizedUserId = preg_replace('/[^a-zA-Z0-9]/', '', $user_id);
                    $newFileName = 'profile_' . $sanitizedUserId . '_' . time() . '.' . $extension;
                    $targetPath = $uploadDir . $newFileName;
                    
                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
                        // File uploaded successfully, now update database
                        $userModel = new User($db);
                        
                        // Get current profile image to delete old one
                        $currentUser = $userModel->getUserById($user_id);
                        $oldImage = $currentUser['profile_image'] ?? '';
                        
                        // Delete old image if exists
                        if (!empty($oldImage) && file_exists($uploadDir . $oldImage)) {
                            unlink($uploadDir . $oldImage);
                        }
                        
                        // Update database
                        $updateResult = $userModel->updateProfileImage($user_id, $newFileName);
                        
                        if ($updateResult) {
                            $_SESSION['profile_message'] = "Profile image updated successfully!";
                        } else {
                            // Debug: Check if rowCount was 0 - means user_id didn't match
                            $_SESSION['profile_error'] = "Database update failed. File saved as: $newFileName. Check if user_id matches.";
                        }
                    } else {
                        $_SESSION['profile_error'] = "Failed to upload file to: $targetPath";
                    }
                }
            } else {
                $errorCode = isset($_FILES['profile_image']) ? $_FILES['profile_image']['error'] : 'No file';
                $_SESSION['profile_error'] = "File upload error. Code: $errorCode";
            }
            // Redirect to refresh page
            header("Location: /CeylonGo/public/transporter/profile");
            exit();
        } elseif ($_POST['action'] == 'change_password') {
            // Handle password change
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            
            $userModel = new User($db);
            $userModel->user_id = trim($user_id);
            
            // Validate inputs
            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $_SESSION['profile_error'] = "All password fields are required.";
            } elseif ($new_password !== $confirm_password) {
                $_SESSION['profile_error'] = "New password and confirm password do not match.";
            } elseif (strlen($new_password) < 8) {
                $_SESSION['profile_error'] = "New password must be at least 8 characters long.";
            } elseif (!$userModel->verifyPassword($user_id, $current_password)) {
                $_SESSION['profile_error'] = "Current password is incorrect.";
            } else {
                // Password validation passed, update the password
                try {
                    if ($userModel->updatePassword($new_password)) {
                        $_SESSION['profile_message'] = "Password changed successfully!";
                    } else {
                        $_SESSION['profile_error'] = "Failed to update password. Please try again.";
                    }
                } catch (PDOException $e) {
                    $_SESSION['profile_error'] = "Database error: " . $e->getMessage();
                }
            }
            // Redirect to refresh page
            header("Location: /CeylonGo/public/transporter/profile");
            exit();
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

try {
    // Get database connection
    $db = Database::getConnection();
    
    // Trim user_id to remove any leading/trailing spaces
    $user_id = trim($user_id);
    
    // Debug: Show what user_id we're querying with
    // Uncomment the next line to debug
    // echo "<!-- DEBUG: user_id = '" . htmlspecialchars($user_id) . "' (length: " . strlen($user_id) . ") -->";
    
    // Fetch user data
    $userModel = new User($db);
    $user = $userModel->getUserById($user_id);
    
    // If user not found, try to check what's in the database
    if (!$user) {
        // Debug: Try raw query to see what's there
        $debugQuery = "SELECT user_id, full_name, email FROM transport_users LIMIT 5";
        $debugStmt = $db->query($debugQuery);
        $allUsers = $debugStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Check if any user_id contains our session id (possibly with whitespace)
        foreach ($allUsers as $u) {
            if (trim($u['user_id']) === $user_id || strpos($u['user_id'], substr($user_id, 0, 10)) !== false) {
                // Found a match - use this user_id
                $user = $userModel->getUserById(trim($u['user_id']));
                if ($user) {
                    // Update session with correct user_id
                    $_SESSION['transporter_id'] = trim($u['user_id']);
                    break;
                }
            }
        }
    }
    
    // Fetch license data
    $licenseModel = new License($db);
    $license = $licenseModel->getLicenseByDriverId($user_id);
    
    // Fetch vehicles data
    $vehicleModel = new Vehicle($db);
    $vehicles = $vehicleModel->getVehiclesByUser($user_id);
    
    // Set profile picture - use saved image or default
    if (!empty($user['profile_image'])) {
        $profile_picture = '/CeylonGo/public/uploads/transport/' . $user['profile_image'];
    } else {
        $profile_picture = '/CeylonGo/public/images/profile.jpg';
    }
    
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - My Profile</title>
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/base.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/navbar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/sidebar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/footer.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/cards.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/buttons.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/forms.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/tables.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/responsive.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <style>
    /* Profile Page Specific Styles */
    .profile-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      padding: 25px;
      margin-bottom: 25px;
    }
    
    .profile-card-header {
      display: flex;
      align-items: center;
      gap: 15px;
      padding-bottom: 15px;
      border-bottom: 1px solid #eee;
      margin-bottom: 20px;
    }
    
    .profile-card-header i {
      font-size: 24px;
      color: #2c5530;
      background: rgba(44, 85, 48, 0.1);
      padding: 12px;
      border-radius: 10px;
    }
    
    .profile-card-header h3 {
      margin: 0;
      font-size: 1.2em;
      color: #333;
    }
    
    .profile-banner {
      background: #3d8b40;
      border-radius: 12px;
      padding: 30px;
      color: white;
      display: flex;
      align-items: center;
      gap: 25px;
      margin-bottom: 25px;
    }
    
    .profile-image-container {
      position: relative;
      width: 120px;
      height: 120px;
    }
    
    .profile-banner img.profile-avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      border: 4px solid rgba(255, 255, 255, 0.3);
      object-fit: cover;
      transition: opacity 0.3s;
    }
    
    .profile-image-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background: rgba(0, 0, 0, 0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: opacity 0.3s;
      cursor: pointer;
    }
    
    .profile-image-container:hover .profile-image-overlay {
      opacity: 1;
    }
    
    .profile-image-overlay i {
      font-size: 24px;
      color: white;
    }
    
    .profile-image-overlay span {
      color: white;
      font-size: 12px;
      margin-top: 5px;
    }
    
    .profile-image-overlay-content {
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    
    .profile-banner-info h2 {
      margin: 0 0 5px 0;
      font-size: 1.8em;
    }
    
    .profile-banner-info p {
      margin: 0;
      opacity: 0.9;
    }
    
    .status-badge {
      display: inline-block;
      background: rgba(255, 255, 255, 0.2);
      padding: 5px 15px;
      border-radius: 20px;
      font-size: 14px;
      margin-top: 10px;
    }
    
    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
    }
    
    .form-group {
      margin-bottom: 0;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #555;
      font-size: 14px;
    }
    
    .form-group input,
    .form-group select {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 15px;
      transition: border-color 0.3s, box-shadow 0.3s;
    }
    
    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: #4CAF50;
      box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
    }
    
    .form-group.full-width {
      grid-column: 1 / -1;
    }
    
    .btn-save {
      background: #3d8b40;
      color: white;
      border: none;
      padding: 12px 30px;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
      margin-top: 20px;
    }
    
    .btn-save:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(44, 85, 48, 0.3);
    }
    
    .license-info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      background: #f8f9fa;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 25px;
    }
    
    .license-info-item label {
      display: block;
      font-size: 13px;
      color: #666;
      margin-bottom: 5px;
    }
    
    .license-info-item span {
      font-size: 16px;
      font-weight: 600;
      color: #333;
    }
    
    /* Vehicle Cards */
    .vehicles-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
    }
    
    .vehicle-card {
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      transition: transform 0.2s, box-shadow 0.2s;
      border: 1px solid #eee;
    }
    
    .vehicle-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    }
    
    .vehicle-card-image {
      height: 180px;
      overflow: hidden;
      background: #f5f5f5;
    }
    
    .vehicle-card-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .vehicle-card-body {
      padding: 20px;
    }
    
    .vehicle-card-body h4 {
      margin: 0 0 15px 0;
      color: #2c5530;
      font-size: 1.3em;
    }
    
    .vehicle-detail {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 10px;
      color: #555;
    }
    
    .vehicle-detail i {
      color: #4CAF50;
      width: 20px;
    }
    
    .vehicle-card-actions {
      display: flex;
      gap: 10px;
      padding: 15px 20px;
      background: #f8f9fa;
      border-top: 1px solid #eee;
    }
    
    .btn-edit, .btn-delete {
      flex: 1;
      padding: 10px;
      border: none;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: all 0.2s;
    }
    
    .btn-edit {
      background: #e3f2fd;
      color: #1976d2;
    }
    
    .btn-edit:hover {
      background: #1976d2;
      color: white;
    }
    
    .btn-delete {
      background: #ffebee;
      color: #c62828;
    }
    
    .btn-delete:hover {
      background: #c62828;
      color: white;
    }
    
    .btn-add-vehicle {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: #3d8b40;
      color: white;
      padding: 12px 25px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: transform 0.2s, box-shadow 0.2s;
      margin-bottom: 20px;
    }
    
    .btn-add-vehicle:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(44, 85, 48, 0.3);
      color: white;
    }
    
    .no-vehicles {
      text-align: center;
      padding: 40px;
      background: #f8f9fa;
      border-radius: 10px;
      color: #666;
    }
    
    .no-vehicles i {
      font-size: 48px;
      color: #ddd;
      margin-bottom: 15px;
    }
    
    .alert {
      padding: 15px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .alert-success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    
    .alert-error {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    
    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      align-items: center;
      justify-content: center;
    }
    
    .modal-content {
      background: #fff;
      border-radius: 12px;
      width: 90%;
      max-width: 500px;
      max-height: 90vh;
      overflow-y: auto;
    }
    
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px;
      border-bottom: 1px solid #eee;
    }
    
    .modal-header h3 {
      margin: 0;
    }
    
    .modal-header .close {
      font-size: 28px;
      cursor: pointer;
      color: #999;
    }
    
    .modal-header .close:hover {
      color: #333;
    }
    
    .modal-body {
      padding: 20px;
    }
    
    .modal-footer {
      display: flex;
      gap: 10px;
      padding: 20px;
      border-top: 1px solid #eee;
      justify-content: flex-end;
    }
    
    @media (max-width: 768px) {
      .profile-banner {
        flex-direction: column;
        text-align: center;
        padding: 25px;
      }
      
      .profile-image-container {
        margin: 0 auto 15px;
      }
      
      .profile-image-overlay {
        opacity: 0.7;
      }
      
      .form-grid {
        grid-template-columns: 1fr;
      }
      
      .vehicles-grid {
        grid-template-columns: 1fr;
      }
    }
    
    /* Password Input Styles */
    .password-input-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }
    
    .password-input-wrapper input {
      width: 100%;
      padding-right: 45px;
    }
    
    .password-toggle {
      position: absolute;
      right: 12px;
      background: none;
      border: none;
      cursor: pointer;
      color: #666;
      padding: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: color 0.3s;
    }
    
    .password-toggle:hover {
      color: #3d8b40;
    }
    
    .password-hint {
      display: block;
      margin-top: 5px;
      font-size: 12px;
      color: #888;
    }
    
    .password-strength {
      display: flex;
      gap: 4px;
      margin-top: 8px;
    }
    
    .password-strength-bar {
      flex: 1;
      height: 4px;
      background: #e0e0e0;
      border-radius: 2px;
      transition: background 0.3s;
    }
    
    .password-strength-bar.weak { background: #f44336; }
    .password-strength-bar.medium { background: #ff9800; }
    .password-strength-bar.strong { background: #4caf50; }
    
    .form-group.half-width {
      min-width: 350px;
      flex: 1;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <header class="navbar">
    <div class="branding">
      <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle menu">
        <span></span>
        <span></span>
        <span></span>
      </button>
      <img src="/CeylonGo/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="/CeylonGo/public/transporter/dashboard">Home</a>
      <div class="profile-dropdown">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="User" class="profile-pic" onclick="toggleProfileDropdown()">
        <div class="profile-dropdown-menu" id="profileDropdown">
          <a href="/CeylonGo/public/transporter/profile"><i class="fa-regular fa-user"></i> My Profile</a>
          <a href="/CeylonGo/public/logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
      </div>
    </nav>
  </header>

  <!-- Sidebar Overlay for Mobile -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="page-wrapper">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
      <ul>
        <li><a href="/CeylonGo/public/transporter/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="/CeylonGo/public/transporter/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/pending"><i class="fa-regular fa-clock"></i> Pending Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li class="active"><a href="/CeylonGo/public/transporter/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/transporter/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <h2 class="page-title"><i class="fa-regular fa-user"></i> My Profile</h2>

      <!-- Success/Error Messages -->
      <?php if ($message): ?>
        <div class="alert alert-success">
          <i class="fa-solid fa-check-circle"></i>
          <?= $message ?>
        </div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="alert alert-error">
          <i class="fa-solid fa-exclamation-circle"></i>
          <?= $error ?>
        </div>
      <?php endif; ?>

      <!-- Profile Banner -->
      <div class="profile-banner">
        <div class="profile-image-container">
          <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="profile-avatar">
          <div class="profile-image-overlay" onclick="document.getElementById('profileImageInput').click()">
            <div class="profile-image-overlay-content">
              <i class="fa-solid fa-camera"></i>
              <span>Change Photo</span>
            </div>
          </div>
          <form id="profileImageForm" method="POST" enctype="multipart/form-data" style="display: none;">
            <input type="hidden" name="action" value="update_profile_image">
            <input type="hidden" id="croppedImageData" name="cropped_image_data">
            <input type="file" id="profileImageInput" name="profile_image" accept="image/*" onchange="openImageCropper(this)">
          </form>
        </div>
        <div class="profile-banner-info">
          <h2><?= $user['full_name'] ?? 'N/A' ?></h2>
          <p>Driver ID: <?= $user['user_id'] ?? 'N/A' ?></p>
          <span class="status-badge">● Active</span>
        </div>
      </div>

      <!-- Personal Information Card -->
      <div class="profile-card">
        <div class="profile-card-header">
          <i class="fa-solid fa-user-pen"></i>
          <h3>Personal Information</h3>
        </div>
        <form method="POST" action="">
          <input type="hidden" name="action" value="update_profile">
          <div class="form-grid">
            <div class="form-group">
              <label>Full Name</label>
              <input type="text" name="full_name" value="<?= $user['full_name'] ?? '' ?>" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
            </div>
            <div class="form-group">
              <label>Date of Birth</label>
              <input type="date" name="dob" value="<?= $user['dob'] ?? '' ?>" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
            </div>
            <div class="form-group full-width">
              <label>Home Address</label>
              <input type="text" name="address" value="<?= $user['address'] ?? '' ?>" required>
            </div>
            <div class="form-group">
              <label>Contact Number</label>
              <input type="text" name="contact_no" value="<?= $user['contact_no'] ?? '' ?>" required>
            </div>
            <div class="form-group">
              <label>Email Address</label>
              <input type="email" name="email" value="<?= $user['email'] ?? '' ?>" required>
            </div>
          </div>
          <button type="submit" class="btn-save"><i class="fa-solid fa-save"></i> Save Changes</button>
        </form>
      </div>

      <!-- Driving License Card -->
      <div class="profile-card">
        <div class="profile-card-header">
          <i class="fa-solid fa-id-card"></i>
          <h3>Driving License</h3>
        </div>
        
        <!-- Current License Info -->
        <div class="license-info-grid">
          <div class="license-info-item">
            <label>License Number</label>
            <span><?= !empty($license['license_no']) ? $license['license_no'] : 'Not set' ?></span>
          </div>
          <div class="license-info-item">
            <label>Expiry Date</label>
            <span><?= !empty($license['license_exp_date']) ? date('F d, Y', strtotime($license['license_exp_date'])) : 'Not set' ?></span>
          </div>
        </div>
        
        <!-- Update License Form -->
        <form method="POST" action="">
          <input type="hidden" name="action" value="update_license">
          <input type="hidden" name="license_no" value="<?= $license['license_no'] ?? '' ?>">
          <div class="form-grid">
            <div class="form-group">
              <label>License Number</label>
              <input type="text" value="<?= $license['license_no'] ?? '' ?>" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
            </div>
            <div class="form-group">
              <label>Expiry Date</label>
              <input type="date" name="license_exp_date" value="<?= $license['license_exp_date'] ?? '' ?>" min="<?= date('Y-m-d') ?>" required>
            </div>
          </div>
          <button type="submit" class="btn-save"><i class="fa-solid fa-save"></i> Update License</button>
        </form>
      </div>

      <!-- My Vehicles Card -->
      <div class="profile-card">
        <div class="profile-card-header">
          <i class="fa-solid fa-car"></i>
          <h3>My Vehicles</h3>
        </div>
        
        <a href="vehicle" class="btn-add-vehicle">
          <i class="fa-solid fa-plus"></i> Add New Vehicle
        </a>
        
        <?php if(isset($vehicles) && !empty($vehicles)): ?>
          <div class="vehicles-grid">
            <?php foreach($vehicles as $v): ?>
              <div class="vehicle-card">
                <div class="vehicle-card-image">
                  <?php if(isset($v['image']) && !empty($v['image'])): ?>
                    <img src="/CeylonGo/uploads/<?= $v['image'] ?>" alt="<?= $v['vehicle_no'] ?>">
                  <?php else: ?>
                    <img src="/CeylonGo/public/images/logo.png" alt="No Image">
                  <?php endif; ?>
                </div>
                <div class="vehicle-card-body">
                  <h4>
                    <?= 
                      $v['vehicle_type'] == '1' ? 'TUK' :
                      ($v['vehicle_type'] == '2' ? 'VAN' :
                      ($v['vehicle_type'] == '3' ? 'CAR' :
                      ($v['vehicle_type'] == '4' ? 'BUS' : $v['vehicle_type'])))
                    ?>
                  </h4>
                  <div class="vehicle-detail">
                    <i class="fa-solid fa-hashtag"></i>
                    <span>License Plate: <?= $v['vehicle_no'] ?? 'N/A' ?></span>
                  </div>
                  <div class="vehicle-detail">
                    <i class="fa-solid fa-users"></i>
                    <span>Capacity: <?= $v['psg_capacity'] ?? 'N/A' ?> passengers</span>
                  </div>
                </div>
                <div class="vehicle-card-actions">
                  <button class="btn-edit" onclick="openEditModal('<?= $v['vehicle_no'] ?>', '<?= $v['vehicle_type'] ?>', '<?= $v['psg_capacity'] ?>', '<?= $v['image'] ?? '' ?>')">
                    <i class="fa-solid fa-edit"></i> Edit
                  </button>
                  <button class="btn-delete" onclick="deleteVehicle('<?= $v['vehicle_no'] ?>')">
                    <i class="fa-solid fa-trash"></i> Delete
                  </button>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="no-vehicles">
            <i class="fa-solid fa-car"></i>
            <p>No vehicles added yet.</p>
            <a href="vehicle" class="btn-add-vehicle" style="margin-top: 15px;">
              <i class="fa-solid fa-plus"></i> Add Your First Vehicle
            </a>
          </div>
        <?php endif; ?>
      </div>

      <!-- Change Password Card -->
      <div class="profile-card">
        <div class="profile-card-header">
          <i class="fa-solid fa-key"></i>
          <h3>Change Password</h3>
        </div>
        <form method="POST" action="" id="changePasswordForm">
          <input type="hidden" name="action" value="change_password">
          <div class="form-grid">
            <div class="form-group half-width">
              <label>Current Password</label>
              <div class="password-input-wrapper">
                <input type="password" name="current_password" id="current_password" placeholder="Enter your current password" required>
                <button type="button" class="password-toggle" onclick="togglePasswordVisibility('current_password', this)">
                  <i class="fa-solid fa-eye"></i>
                </button>
              </div>
            </div>
            <div class="form-group half-width">
              <label>New Password</label>
              <div class="password-input-wrapper">
                <input type="password" name="new_password" id="new_password" placeholder="Enter new password" required minlength="8">
                <button type="button" class="password-toggle" onclick="togglePasswordVisibility('new_password', this)">
                  <i class="fa-solid fa-eye"></i>
                </button>
              </div>
              <small class="password-hint">Minimum 8 characters</small>
            </div>
            <div class="form-group half-width">
              <label>Confirm New Password</label>
              <div class="password-input-wrapper">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required minlength="8">
                <button type="button" class="password-toggle" onclick="togglePasswordVisibility('confirm_password', this)">
                  <i class="fa-solid fa-eye"></i>
                </button>
              </div>
            </div>
          </div>
          <div id="password-match-error" style="color: #c62828; font-size: 14px; margin-top: 10px; display: none;">
            <i class="fa-solid fa-exclamation-circle"></i> Passwords do not match
          </div>
          <button type="submit" class="btn-save"><i class="fa-solid fa-lock"></i> Change Password</button>
        </form>
      </div>

    </div>
  </div>

  <!-- Edit Vehicle Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Edit Vehicle</h3>
        <span class="close" onclick="closeEditModal()">&times;</span>
      </div>
      <form id="editVehicleForm" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" id="edit_vehicle_no_old" name="vehicle_no_old">
          <input type="hidden" id="edit_vehicle_no" name="vehicle_no">
          <input type="hidden" id="edit_vehicle_type" name="vehicle_type">
          <input type="hidden" name="action" value="edit_vehicle">
          
          <div class="form-group" style="margin-bottom: 15px;">
            <label>Vehicle Number</label>
            <input type="text" id="edit_vehicle_no_display" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
          </div>
          
          <div class="form-group" style="margin-bottom: 15px;">
            <label>Vehicle Type</label>
            <input type="text" id="edit_vehicle_type_display" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
          </div>
          
          <div class="form-group" style="margin-bottom: 15px;">
            <label>Passenger Capacity</label>
            <input type="number" id="edit_psg_capacity" name="psg_capacity" min="1" value="1" required>
          </div>
          
          <div class="form-group" style="margin-bottom: 15px;">
            <label>Vehicle Image</label>
            <input type="file" id="edit_vehicle_image" name="vehicle_image" accept="image/*">
            <div id="current_image_preview" style="margin-top: 10px;"></div>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn-delete" onclick="closeEditModal()" style="flex: none; padding: 10px 20px;">Cancel</button>
          <button type="submit" class="btn-save" style="margin-top: 0;"><i class="fa-solid fa-save"></i> Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Open edit modal
    function openEditModal(vehicleNo, vehicleType, psgCapacity, currentImage) {
      document.getElementById('edit_vehicle_no_old').value = vehicleNo;
      document.getElementById('edit_vehicle_no').value = vehicleNo;
      document.getElementById('edit_vehicle_no_display').value = vehicleNo;
      document.getElementById('edit_vehicle_type').value = vehicleType;
      
      // Set display value for vehicle type
      const vehicleTypeNames = { '1': 'TUK', '2': 'VAN', '3': 'CAR', '4': 'BUS' };
      document.getElementById('edit_vehicle_type_display').value = vehicleTypeNames[vehicleType] || vehicleType;
      
      document.getElementById('edit_psg_capacity').value = psgCapacity;
      
      // Show current image if exists
      const preview = document.getElementById('current_image_preview');
      if (currentImage) {
        preview.innerHTML = `<p style="color: #666; font-size: 13px;">Current Image:</p><img src="/CeylonGo/uploads/${currentImage}" style="max-width: 150px; max-height: 100px; border-radius: 8px; margin-top: 5px;">`;
      } else {
        preview.innerHTML = '<p style="color: #999; font-size: 13px;">No current image</p>';
      }
      
      document.getElementById('editModal').style.display = 'flex';
    }
    
    // Close edit modal
    function closeEditModal() {
      document.getElementById('editModal').style.display = 'none';
    }
    
    // Delete vehicle
    function deleteVehicle(vehicleNo) {
      if (confirm('Are you sure you want to delete this vehicle?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
          <input type="hidden" name="action" value="delete_vehicle">
          <input type="hidden" name="vehicle_no" value="${vehicleNo}">
        `;
        document.body.appendChild(form);
        form.submit();
      }
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
      const modal = document.getElementById('editModal');
      if (event.target == modal) {
        closeEditModal();
      }
    }
  </script>

  <!-- Footer -->
  <footer>
    <ul>
      <li><a href="#">About Us</a></li>
      <li><a href="#">Contact Us</a></li>
    </ul>
  </footer>

  <!-- Hamburger Menu Toggle Script -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const hamburgerBtn = document.getElementById('hamburgerBtn');
      const sidebar = document.getElementById('sidebar');
      const sidebarOverlay = document.getElementById('sidebarOverlay');
      
      function toggleSidebar() {
        hamburgerBtn.classList.toggle('active');
        sidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
      }
      
      function closeSidebar() {
        hamburgerBtn.classList.remove('active');
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
      }
      
      if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', toggleSidebar);
      }
      
      if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
      }
      
      const sidebarLinks = document.querySelectorAll('.sidebar ul li a');
      sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
          if (window.innerWidth <= 768) {
            closeSidebar();
          }
        });
      });
      
      window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
          closeSidebar();
        }
      });
    });
  </script>

  <!-- Image Cropper Modal -->
  <div id="imageCropperModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 600px; width: 95%;">
      <div class="modal-header">
        <h3><i class="fa-solid fa-crop"></i> Crop Profile Picture</h3>
        <span class="close" onclick="closeCropperModal()">&times;</span>
      </div>
      <div class="modal-body" style="padding: 20px;">
        <p style="color: #666; margin-bottom: 15px; font-size: 14px;">Drag to position, scroll to zoom. The circular area will be your profile picture.</p>
        <div id="cropperContainer" style="position: relative; width: 100%; height: 350px; background: #1a1a1a; border-radius: 10px; overflow: hidden; cursor: move;">
          <img id="cropperImage" src="" alt="Crop Image" style="position: absolute; max-width: none;">
          <div id="cropOverlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none;">
            <svg width="100%" height="100%" style="position: absolute;">
              <defs>
                <mask id="cropMask">
                  <rect width="100%" height="100%" fill="white"/>
                  <circle id="cropCircle" cx="50%" cy="50%" r="120" fill="black"/>
                </mask>
              </defs>
              <rect width="100%" height="100%" fill="rgba(0,0,0,0.6)" mask="url(#cropMask)"/>
              <circle id="cropBorder" cx="50%" cy="50%" r="120" fill="none" stroke="#fff" stroke-width="3" stroke-dasharray="8,4"/>
            </svg>
          </div>
        </div>
        <div style="display: flex; align-items: center; gap: 15px; margin-top: 15px;">
          <span style="font-size: 14px; color: #666;"><i class="fa-solid fa-magnifying-glass-minus"></i></span>
          <input type="range" id="cropperZoom" min="0.5" max="3" step="0.1" value="1" style="flex: 1; cursor: pointer;">
          <span style="font-size: 14px; color: #666;"><i class="fa-solid fa-magnifying-glass-plus"></i></span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-delete" onclick="closeCropperModal()" style="flex: none; padding: 10px 20px;">Cancel</button>
        <button type="button" class="btn-save" onclick="saveCroppedImage()" style="margin-top: 0;"><i class="fa-solid fa-check"></i> Save Photo</button>
      </div>
    </div>
  </div>

  <script>
    // Image Cropper Variables
    let cropperImage = null;
    let cropperZoom = 1;
    let cropperX = 0;
    let cropperY = 0;
    let isDragging = false;
    let dragStartX = 0;
    let dragStartY = 0;
    let originalFile = null;
    let imageWidth = 0;
    let imageHeight = 0;

    function openImageCropper(input) {
      if (input.files && input.files[0]) {
        originalFile = input.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
          const img = document.getElementById('cropperImage');
          img.src = e.target.result;
          
          img.onload = function() {
            imageWidth = img.naturalWidth;
            imageHeight = img.naturalHeight;
            
            // Reset position and zoom
            cropperZoom = 1;
            document.getElementById('cropperZoom').value = 1;
            
            // Center the image
            const container = document.getElementById('cropperContainer');
            const containerWidth = container.offsetWidth;
            const containerHeight = container.offsetHeight;
            
            // Scale image to fit container initially
            const scale = Math.max(240 / imageWidth, 240 / imageHeight, 0.5);
            cropperZoom = scale;
            document.getElementById('cropperZoom').value = scale;
            
            cropperX = (containerWidth - imageWidth * cropperZoom) / 2;
            cropperY = (containerHeight - imageHeight * cropperZoom) / 2;
            
            updateCropperPosition();
            document.getElementById('imageCropperModal').style.display = 'flex';
          };
        };
        
        reader.readAsDataURL(originalFile);
      }
    }

    function updateCropperPosition() {
      const img = document.getElementById('cropperImage');
      img.style.width = (imageWidth * cropperZoom) + 'px';
      img.style.height = (imageHeight * cropperZoom) + 'px';
      img.style.left = cropperX + 'px';
      img.style.top = cropperY + 'px';
    }

    function closeCropperModal() {
      document.getElementById('imageCropperModal').style.display = 'none';
      document.getElementById('profileImageInput').value = '';
    }

    function saveCroppedImage() {
      const container = document.getElementById('cropperContainer');
      const img = document.getElementById('cropperImage');
      const containerWidth = container.offsetWidth;
      const containerHeight = container.offsetHeight;
      
      // Calculate crop area (center circle)
      const circleRadius = 120;
      const centerX = containerWidth / 2;
      const centerY = containerHeight / 2;
      
      // Calculate source coordinates on the original image
      const sourceX = (centerX - circleRadius - cropperX) / cropperZoom;
      const sourceY = (centerY - circleRadius - cropperY) / cropperZoom;
      const sourceSize = (circleRadius * 2) / cropperZoom;
      
      // Create canvas for cropped image
      const canvas = document.createElement('canvas');
      const outputSize = 300; // Output image size
      canvas.width = outputSize;
      canvas.height = outputSize;
      const ctx = canvas.getContext('2d');
      
      // Create circular clip
      ctx.beginPath();
      ctx.arc(outputSize / 2, outputSize / 2, outputSize / 2, 0, Math.PI * 2);
      ctx.closePath();
      ctx.clip();
      
      // Draw cropped portion of image
      ctx.drawImage(
        img,
        sourceX, sourceY, sourceSize, sourceSize,
        0, 0, outputSize, outputSize
      );
      
      // Convert to blob and submit
      canvas.toBlob(function(blob) {
        // Create a new form data with the cropped image
        const formData = new FormData();
        formData.append('action', 'update_profile_image');
        formData.append('profile_image', blob, 'profile.jpg');
        
        // Submit via fetch
        fetch(window.location.href, {
          method: 'POST',
          body: formData
        }).then(response => {
          window.location.reload();
        }).catch(error => {
          alert('Error uploading image. Please try again.');
          closeCropperModal();
        });
      }, 'image/jpeg', 0.9);
    }

    // Set up cropper event listeners
    document.addEventListener('DOMContentLoaded', function() {
      const container = document.getElementById('cropperContainer');
      const zoomSlider = document.getElementById('cropperZoom');
      
      // Mouse drag
      container.addEventListener('mousedown', function(e) {
        isDragging = true;
        dragStartX = e.clientX - cropperX;
        dragStartY = e.clientY - cropperY;
        e.preventDefault();
      });
      
      document.addEventListener('mousemove', function(e) {
        if (isDragging) {
          cropperX = e.clientX - dragStartX;
          cropperY = e.clientY - dragStartY;
          updateCropperPosition();
        }
      });
      
      document.addEventListener('mouseup', function() {
        isDragging = false;
      });
      
      // Touch drag for mobile
      container.addEventListener('touchstart', function(e) {
        if (e.touches.length === 1) {
          isDragging = true;
          dragStartX = e.touches[0].clientX - cropperX;
          dragStartY = e.touches[0].clientY - cropperY;
          e.preventDefault();
        }
      });
      
      document.addEventListener('touchmove', function(e) {
        if (isDragging && e.touches.length === 1) {
          cropperX = e.touches[0].clientX - dragStartX;
          cropperY = e.touches[0].clientY - dragStartY;
          updateCropperPosition();
        }
      });
      
      document.addEventListener('touchend', function() {
        isDragging = false;
      });
      
      // Zoom slider
      zoomSlider.addEventListener('input', function() {
        const oldZoom = cropperZoom;
        cropperZoom = parseFloat(this.value);
        
        // Zoom towards center
        const container = document.getElementById('cropperContainer');
        const centerX = container.offsetWidth / 2;
        const centerY = container.offsetHeight / 2;
        
        cropperX = centerX - (centerX - cropperX) * (cropperZoom / oldZoom);
        cropperY = centerY - (centerY - cropperY) * (cropperZoom / oldZoom);
        
        updateCropperPosition();
      });
      
      // Mouse wheel zoom
      container.addEventListener('wheel', function(e) {
        e.preventDefault();
        const delta = e.deltaY > 0 ? -0.1 : 0.1;
        const newZoom = Math.max(0.5, Math.min(3, cropperZoom + delta));
        
        if (newZoom !== cropperZoom) {
          const oldZoom = cropperZoom;
          cropperZoom = newZoom;
          zoomSlider.value = cropperZoom;
          
          // Zoom towards mouse position
          const rect = container.getBoundingClientRect();
          const mouseX = e.clientX - rect.left;
          const mouseY = e.clientY - rect.top;
          
          cropperX = mouseX - (mouseX - cropperX) * (cropperZoom / oldZoom);
          cropperY = mouseY - (mouseY - cropperY) * (cropperZoom / oldZoom);
          
          updateCropperPosition();
        }
      });
    });
  </script>

  <!-- Profile Dropdown Script -->
  <script>
    function toggleProfileDropdown() {
      const dropdown = document.getElementById('profileDropdown');
      dropdown.classList.toggle('show');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      const dropdown = document.getElementById('profileDropdown');
      const profilePic = document.querySelector('.profile-pic');
      
      if (dropdown && !dropdown.contains(event.target) && event.target !== profilePic) {
        dropdown.classList.remove('show');
      }
    });
  </script>

  <!-- Password Change Scripts -->
  <script>
    // Toggle password visibility
    function togglePasswordVisibility(inputId, button) {
      const input = document.getElementById(inputId);
      const icon = button.querySelector('i');
      
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }

    // Password change form validation
    document.addEventListener('DOMContentLoaded', function() {
      const changePasswordForm = document.getElementById('changePasswordForm');
      const newPassword = document.getElementById('new_password');
      const confirmPassword = document.getElementById('confirm_password');
      const matchError = document.getElementById('password-match-error');

      // Check password match on input
      function checkPasswordMatch() {
        if (confirmPassword.value && newPassword.value !== confirmPassword.value) {
          matchError.style.display = 'block';
          confirmPassword.style.borderColor = '#c62828';
        } else {
          matchError.style.display = 'none';
          confirmPassword.style.borderColor = '';
        }
      }

      if (newPassword && confirmPassword) {
        newPassword.addEventListener('input', checkPasswordMatch);
        confirmPassword.addEventListener('input', checkPasswordMatch);
      }

      // Form submission validation
      if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', function(e) {
          if (newPassword.value !== confirmPassword.value) {
            e.preventDefault();
            matchError.style.display = 'block';
            confirmPassword.focus();
            return false;
          }
          
          if (newPassword.value.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters long.');
            newPassword.focus();
            return false;
          }
        });
      }
    });
  </script>

</body>
</html>