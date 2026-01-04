<?php
// views/tourist/profile.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
    header('Location: /CeylonGo/public/login');
    exit;
}

$tourist = $tourist ?? null;
$success_message = $_SESSION['success'] ?? '';
$error_message = $_GET['error'] ?? $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - My Profile</title>
  <link rel="stylesheet" href="../../public/css/common.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
  <link rel="stylesheet" href="../../public/css/tourist/profile.css">
</head>
<body class="bg-app">
  <!-- Navbar include -->
  <?php include 'header.php'; ?>

  <div class="profile-container">
    <div class="profile-header">
      <h1>My Profile</h1>
      <p>Manage your account information</p>
    </div>

    <?php if ($success_message): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <div class="profile-content">
      <form method="POST" action="/CeylonGo/public/tourist/profile" class="profile-form">
        <div class="form-section">
          <h2>Personal Information</h2>
          
          <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" 
                   value="<?= htmlspecialchars($tourist['first_name'] ?? '') ?>" required>
          </div>

          <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" 
                   value="<?= htmlspecialchars($tourist['last_name'] ?? '') ?>" required>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" 
                   value="<?= htmlspecialchars($tourist['email'] ?? '') ?>" required>
          </div>

          <div class="form-group">
            <label for="contact_number">Contact Number</label>
            <input type="tel" id="contact_number" name="contact_number" 
                   value="<?= htmlspecialchars($tourist['contact_number'] ?? '') ?>" required>
          </div>
        </div>

        <div class="form-section">
          <h2>Change Password</h2>
          <p class="form-note">Leave blank if you don't want to change your password</p>
          
          <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password" 
                   placeholder="Enter new password">
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary">Save Changes</button>
          <a href="/CeylonGo/public/tourist/dashboard" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Footer include -->
  <?php include 'footer.php'; ?>
</body>
</html>


