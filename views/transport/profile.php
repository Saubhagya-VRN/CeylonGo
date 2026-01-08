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
      background: linear-gradient(135deg, #2c5530 0%, #4CAF50 100%);
      border-radius: 12px;
      padding: 30px;
      color: white;
      display: flex;
      align-items: center;
      gap: 25px;
      margin-bottom: 25px;
    }
    
    .profile-banner img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      border: 4px solid rgba(255, 255, 255, 0.3);
      object-fit: cover;
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
      background: linear-gradient(135deg, #2c5530 0%, #4CAF50 100%);
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
      background: linear-gradient(135deg, #2c5530 0%, #4CAF50 100%);
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
      
      .form-grid {
        grid-template-columns: 1fr;
      }
      
      .vehicles-grid {
        grid-template-columns: 1fr;
      }
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
      <a href="/CeylonGo/public/logout">Logout</a>
      <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="User" class="profile-pic">
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
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
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
              <input type="text" name="full_name" value="<?= $user['full_name'] ?? '' ?>" required>
            </div>
            <div class="form-group">
              <label>Date of Birth</label>
              <input type="date" name="dob" value="<?= $user['dob'] ?? '' ?>" required>
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
          <div class="form-grid">
            <div class="form-group">
              <label>License Number</label>
              <input type="text" name="license_no" value="<?= $license['license_no'] ?? '' ?>" required>
            </div>
            <div class="form-group">
              <label>Expiry Date</label>
              <input type="date" name="license_exp_date" value="<?= $license['license_exp_date'] ?? '' ?>" required>
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
          <input type="hidden" name="action" value="edit_vehicle">
          
          <div class="form-group" style="margin-bottom: 15px;">
            <label>Vehicle Number</label>
            <input type="text" id="edit_vehicle_no" name="vehicle_no" required>
          </div>
          
          <div class="form-group" style="margin-bottom: 15px;">
            <label>Vehicle Type</label>
            <select id="edit_vehicle_type" name="vehicle_type" required>
              <option value="1">TUK</option>
              <option value="2">VAN</option>
              <option value="3">CAR</option>
              <option value="4">BUS</option>
            </select>
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
      document.getElementById('edit_vehicle_type').value = vehicleType;
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

</body>
</html>