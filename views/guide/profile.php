<?php
// Tour Guide Profile page with database connection
require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../core/Database.php";
require_once __DIR__ . "/../../models/Guide.php";

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

// Check if user is logged in as guide
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'guide') {
    header('Location: /CeylonGo/public/login');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle all POST operations
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    try {
        $db = Database::getConnection();
        $guideModel = new Guide($db);
        
        if ($_POST['action'] == 'update_profile') {
            // Handle profile update
            $first_name = trim($_POST['first_name']);
            $last_name = trim($_POST['last_name']);
            $contact_number = trim($_POST['contact_number']);
            $email = trim($_POST['email']);
            $specialization = isset($_POST['specialization']) ? implode(',', $_POST['specialization']) : '';
            $languages = trim($_POST['languages']);
            $experience = intval($_POST['experience']);
            $bio = trim($_POST['bio'] ?? '');
            
            $query = "UPDATE guide_users SET 
                      first_name = :first_name,
                      last_name = :last_name,
                      contact_number = :contact_number,
                      email = :email,
                      specialization = :specialization,
                      languages = :languages,
                      experience = :experience
                      WHERE id = :id";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':contact_number', $contact_number);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':specialization', $specialization);
            $stmt->bindParam(':languages', $languages);
            $stmt->bindParam(':experience', $experience);
            $stmt->bindParam(':id', $user_id);
            
            if ($stmt->execute()) {
                $_SESSION['profile_message'] = "Profile updated successfully!";
            } else {
                $_SESSION['profile_error'] = "Failed to update profile.";
            }
            
            header("Location: /CeylonGo/public/guide/profile");
            exit();
            
        } elseif ($_POST['action'] == 'update_license') {
            // Handle license update
            $license_number = trim($_POST['license_number']);
            
            $query = "UPDATE guide_users SET license_number = :license_number WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':license_number', $license_number);
            $stmt->bindParam(':id', $user_id);
            
            if ($stmt->execute()) {
                $_SESSION['profile_message'] = "License information updated successfully!";
            } else {
                $_SESSION['profile_error'] = "Failed to update license information.";
            }
            
            header("Location: /CeylonGo/public/guide/profile");
            exit();
            
        } elseif ($_POST['action'] == 'update_profile_image') {
            // Handle profile image upload
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                $uploadDir = dirname(__DIR__, 2) . "/public/uploads/guide/";
                
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
                    // Generate unique filename
                    $fileInfo = pathinfo($_FILES['profile_image']['name']);
                    $extension = strtolower($fileInfo['extension']);
                    $newFileName = 'guide_profile_' . $user_id . '_' . time() . '.' . $extension;
                    $targetPath = $uploadDir . $newFileName;
                    
                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
                        // Get current profile image to delete old one
                        $currentGuide = $guideModel->getGuideById($user_id);
                        $oldImage = $currentGuide['profile_photo'] ?? '';
                        
                        // Delete old image if exists
                        if (!empty($oldImage) && file_exists($uploadDir . $oldImage)) {
                            unlink($uploadDir . $oldImage);
                        }
                        
                        // Update database
                        $query = "UPDATE guide_users SET profile_photo = :profile_photo WHERE id = :id";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':profile_photo', $newFileName);
                        $stmt->bindParam(':id', $user_id);
                        
                        if ($stmt->execute()) {
                            $_SESSION['profile_message'] = "Profile image updated successfully!";
                        } else {
                            $_SESSION['profile_error'] = "Database update failed.";
                        }
                    } else {
                        $_SESSION['profile_error'] = "Failed to upload file.";
                    }
                }
            } else {
                $_SESSION['profile_error'] = "File upload error.";
            }
            
            header("Location: /CeylonGo/public/guide/profile");
            exit();
            
        } elseif ($_POST['action'] == 'change_password') {
            // Handle password change
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            
            // Validate inputs
            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $_SESSION['profile_error'] = "All password fields are required.";
            } elseif ($new_password !== $confirm_password) {
                $_SESSION['profile_error'] = "New password and confirm password do not match.";
            } elseif (strlen($new_password) < 8) {
                $_SESSION['profile_error'] = "New password must be at least 8 characters long.";
            } else {
                // Get current guide data to get email
                $guide = $guideModel->getGuideById($user_id);
                $userEmail = $guide['email'] ?? '';
                
                // Verify password from users table (where login authenticates)
                $passwordValid = false;
                if (!empty($userEmail)) {
                    $verifyQuery = "SELECT password FROM users WHERE email = :email AND role = 'guide'";
                    $verifyStmt = $db->prepare($verifyQuery);
                    $verifyStmt->bindParam(':email', $userEmail);
                    $verifyStmt->execute();
                    $authUser = $verifyStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($authUser && password_verify($current_password, $authUser['password'])) {
                        $passwordValid = true;
                    }
                }
                
                if ($passwordValid) {
                    // Update password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    
                    // Update password in guide_users table
                    $query = "UPDATE guide_users SET password = :password WHERE id = :id";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':password', $hashed_password);
                    $stmt->bindParam(':id', $user_id);
                    $guideUpdated = $stmt->execute();
                    
                    // Update password in users table
                    $usersQuery = "UPDATE users SET password = :password WHERE email = :email AND role = 'guide'";
                    $usersStmt = $db->prepare($usersQuery);
                    $usersStmt->bindParam(':password', $hashed_password);
                    $usersStmt->bindParam(':email', $userEmail);
                    $usersUpdated = $usersStmt->execute();
                    
                    if ($guideUpdated || $usersUpdated) {
                        $_SESSION['profile_message'] = "Password changed successfully!";
                    } else {
                        $_SESSION['profile_error'] = "Failed to update password.";
                    }
                } else {
                    $_SESSION['profile_error'] = "Current password is incorrect.";
                }
            }
            
            header("Location: /CeylonGo/public/guide/profile");
            exit();
        }
        
    } catch (Exception $e) {
        $_SESSION['profile_error'] = "Error: " . $e->getMessage();
        header("Location: /CeylonGo/public/guide/profile");
        exit();
    }
}

try {
    // Get database connection
    $db = Database::getConnection();
    
    // Fetch user data
    $guideModel = new Guide($db);
    $user = $guideModel->getGuideById($user_id);
    
    // Set profile picture - use saved image or default
    if (!empty($user['profile_photo'])) {
        $profile_picture = '/CeylonGo/public/uploads/guide/' . $user['profile_photo'];
    } else {
        $profile_picture = '/CeylonGo/public/images/profile.jpg';
    }
    
    // Store user name for display
    $user_name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
    if (empty($user_name)) {
        $user_name = 'Guide';
    }
    
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

// Define specialization options
$specializations = [
    'cultural' => 'Cultural Heritage',
    'historical' => 'Historical Sites',
    'religious' => 'Religious Sites',
    'nature' => 'Nature & Wildlife',
    'adventure' => 'Adventure Tours',
    'beach' => 'Beach & Coastal',
    'culinary' => 'Culinary Tours'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - My Profile</title>
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/base.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/navbar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/sidebar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/footer.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/cards.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/buttons.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/forms.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/responsive.css">
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
      flex-shrink: 0;
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
    
    .profile-banner-info {
      flex: 1;
      z-index: 1;
    }
    
    .profile-banner-info h2 {
      margin: 0 0 5px 0;
      font-size: 1.8em;
    }
    
    .profile-banner-info p {
      margin: 0 0 5px 0;
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
    
    .status-badge.verified {
      background: rgba(76, 175, 80, 0.3);
    }
    
    .profile-stats {
      display: flex;
      gap: 20px;
      margin-top: 15px;
    }
    
    .profile-stat {
      text-align: center;
    }
    
    .profile-stat-value {
      font-size: 1.5em;
      font-weight: 700;
    }
    
    .profile-stat-label {
      font-size: 12px;
      opacity: 0.8;
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
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 15px;
      transition: border-color 0.3s, box-shadow 0.3s;
      font-family: inherit;
    }
    
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #4CAF50;
      box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
    }
    
    .form-group.full-width {
      grid-column: 1 / -1;
    }
    
    .form-group textarea {
      resize: vertical;
      min-height: 100px;
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
      display: inline-flex;
      align-items: center;
      gap: 8px;
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
    
    /* Skills/Languages Tags */
    .skills-container {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 15px;
    }
    
    .skill-tag {
      background: rgba(44, 85, 48, 0.1);
      color: #2c5530;
      padding: 8px 16px;
      border-radius: 20px;
      font-size: 14px;
      font-weight: 500;
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
    
    .form-group.half-width {
      min-width: 280px;
      flex: 1;
    }
    
    /* Experience Badge */
    .experience-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: #ffd700;
      color: #333;
      padding: 8px 16px;
      border-radius: 20px;
      font-size: 14px;
      font-weight: 600;
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
      max-width: 600px;
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
      display: flex;
      align-items: center;
      gap: 10px;
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
    
    .btn-cancel {
      background: #f5f5f5;
      color: #666;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 500;
      transition: background 0.3s;
    }
    
    .btn-cancel:hover {
      background: #e0e0e0;
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
      
      .profile-stats {
        justify-content: center;
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
      <a href="/CeylonGo/public/guide/dashboard">Home</a>
      <div class="profile-dropdown">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="User" class="profile-pic" onclick="toggleProfileDropdown()">
        <div class="profile-dropdown-menu" id="profileDropdown">
          <a href="/CeylonGo/public/guide/profile"><i class="fa-regular fa-user"></i> My Profile</a>
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
        <li><a href="/CeylonGo/public/guide/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="/CeylonGo/public/guide/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Tours</a></li>
        <li><a href="/CeylonGo/public/guide/pending"><i class="fa-regular fa-clock"></i> Pending Requests</a></li>
        <li><a href="/CeylonGo/public/guide/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Tours</a></li>
        <li><a href="/CeylonGo/public/guide/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li class="active"><a href="/CeylonGo/public/guide/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/guide/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
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
            <input type="file" id="profileImageInput" name="profile_image" accept="image/*" onchange="openImageCropper(this)">
          </form>
        </div>
        <div class="profile-banner-info">
          <h2><?= htmlspecialchars($user_name) ?></h2>
          <p><i class="fa-solid fa-id-badge"></i> Guide ID: <?= $user['id'] ?? 'N/A' ?></p>
          <p><i class="fa-solid fa-map-marker-alt"></i> <?= htmlspecialchars($specializations[$user['specialization'] ?? ''] ?? 'Tour Guide') ?></p>
          <div class="profile-stats">
            <div class="profile-stat">
              <div class="profile-stat-value"><?= $user['experience'] ?? '0' ?></div>
              <div class="profile-stat-label">Years Exp.</div>
            </div>
            <div class="profile-stat">
              <div class="profile-stat-value">4.8</div>
              <div class="profile-stat-label">Rating</div>
            </div>
            <div class="profile-stat">
              <div class="profile-stat-value">125</div>
              <div class="profile-stat-label">Tours</div>
            </div>
          </div>
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
              <label>First Name</label>
              <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
            </div>
            <div class="form-group">
              <label>Last Name</label>
              <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
            </div>
            <div class="form-group">
              <label>Contact Number</label>
              <input type="text" name="contact_number" value="<?= htmlspecialchars($user['contact_number'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label>Email Address</label>
              <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
            </div>
            <div class="form-group full-width">
              <label>Specialization Areas (Select multiple)</label>
              <div class="specialization-checkboxes" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; padding: 15px; background: #f8f9fa; border-radius: 8px; border: 1px solid #ddd;">
                <?php 
                $userSpecializations = explode(',', $user['specialization'] ?? '');
                foreach ($specializations as $key => $value): 
                ?>
                  <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 8px 12px; background: white; border-radius: 6px; border: 1px solid #e0e0e0; transition: all 0.2s;" class="spec-checkbox-label">
                    <input type="checkbox" name="specialization[]" value="<?= $key ?>" <?= in_array($key, $userSpecializations) ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: #3d8b40;">
                    <span style="font-size: 14px; color: #333;"><?= $value ?></span>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="form-group">
              <label>Years of Experience</label>
              <input type="number" name="experience" min="0" max="50" value="<?= htmlspecialchars($user['experience'] ?? '0') ?>" required>
            </div>
            <div class="form-group full-width">
              <label>Languages Spoken</label>
              <input type="text" name="languages" value="<?= htmlspecialchars($user['languages'] ?? '') ?>" placeholder="e.g., English, Sinhala, Tamil">
            </div>
          </div>
          <button type="submit" class="btn-save"><i class="fa-solid fa-save"></i> Save Changes</button>
        </form>
      </div>

      <!-- Tour Guide License Card -->
      <div class="profile-card">
        <div class="profile-card-header">
          <i class="fa-solid fa-id-card"></i>
          <h3>Tour Guide License</h3>
        </div>
        
        <!-- Current License Info -->
        <div class="license-info-grid">
          <div class="license-info-item">
            <label>License Number</label>
            <span><?= !empty($user['license_number']) ? htmlspecialchars($user['license_number']) : 'Not set' ?></span>
          </div>
          <div class="license-info-item">
            <label>NIC Number</label>
            <span><?= !empty($user['nic']) ? htmlspecialchars($user['nic']) : 'Not set' ?></span>
          </div>
          <div class="license-info-item">
            <label>Status</label>
            <span class="status-badge verified"><i class="fa-solid fa-check-circle"></i> Verified</span>
          </div>
        </div>
        
        <!-- Update License Form -->
        <form method="POST" action="">
          <input type="hidden" name="action" value="update_license">
          <div class="form-grid">
            <div class="form-group">
              <label>License Number</label>
              <input type="text" name="license_number" value="<?= htmlspecialchars($user['license_number'] ?? '') ?>" placeholder="Enter your SLTDA license number" required>
            </div>
          </div>
          <button type="submit" class="btn-save"><i class="fa-solid fa-save"></i> Update License</button>
        </form>
      </div>

      <!-- Languages & Skills Card -->
      <div class="profile-card">
        <div class="profile-card-header">
          <i class="fa-solid fa-language"></i>
          <h3>Languages & Skills</h3>
        </div>
        
        <h4 style="color: #555; margin-bottom: 10px; font-size: 14px;">Languages You Speak</h4>
        <div class="skills-container">
          <?php 
          $languages = explode(',', $user['languages'] ?? 'English');
          foreach ($languages as $lang): 
            $lang = trim($lang);
            if (!empty($lang)):
          ?>
            <span class="skill-tag"><i class="fa-solid fa-globe"></i> <?= htmlspecialchars($lang) ?></span>
          <?php 
            endif;
          endforeach; 
          ?>
        </div>
        
        <h4 style="color: #555; margin: 20px 0 10px 0; font-size: 14px;">Tour Specializations</h4>
        <div class="skills-container">
          <?php 
          $userSpecs = explode(',', $user['specialization'] ?? '');
          foreach ($userSpecs as $spec):
            $spec = trim($spec);
            if (!empty($spec) && isset($specializations[$spec])):
          ?>
            <span class="skill-tag"><i class="fa-solid fa-map-location-dot"></i> <?= htmlspecialchars($specializations[$spec]) ?></span>
          <?php 
            endif;
          endforeach;
          if (empty(trim($user['specialization'] ?? ''))):
          ?>
            <span class="skill-tag"><i class="fa-solid fa-map-location-dot"></i> Not specified</span>
          <?php endif; ?>
        </div>
        
        <h4 style="color: #555; margin: 20px 0 10px 0; font-size: 14px;">Experience</h4>
        <div class="experience-badge">
          <i class="fa-solid fa-award"></i>
          <?= htmlspecialchars($user['experience'] ?? '0') ?> Years of Experience
        </div>
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
        <button type="button" class="btn-cancel" onclick="closeCropperModal()">Cancel</button>
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
