<?php 
  // Session is already started in public/index.php
  require_once(__DIR__ . '/../../config/config.php');
  require_once(__DIR__ . '/../../core/Database.php');

  // Only allow logged-in admins
  if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
      header("Location: /CeylonGo/public/login");
      exit();
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Profile - Ceylon Go</title>
<link rel="stylesheet" href="../../public/css/admin/admin_profile.css">
</head>
<body>
<div class="profile-container">
  <div class="profile-card">
    <div class="profile-header">
      <h2>Admin Profile</h2>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-msg">
            <?= $_SESSION['success']; ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if(isset($_SESSION['error'])): ?>
        <div class="error-msg">
          <?= $_SESSION['error']; ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="profile-info">
      <br>
      <h3>Profile Details</h3>
      <p><strong>Username:</strong> <?= htmlspecialchars($admin['username']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($admin['email']) ?></p>
      <p><strong>Phone:</strong> <?= htmlspecialchars($admin['phone_number']) ?></p>
      <p><strong>Role:</strong> <?= htmlspecialchars($admin['role']) ?></p>
    </div>

    <!-- Edit Profile Form -->
    <div class="edit-profile">
      <h3>Edit Profile</h3>
      <form action="/CeylonGo/public/admin/profile" method="POST">
        <input type="hidden" name="id" value="<?= $admin['id'] ?>">

        <div class="form-group">
          <label for="username">Admin Username</label>
          <input type="text" id="username" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required>
        </div>

        <div class="form-group">
          <label for="email">Admin Email</label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" 
                required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" 
                title="Please enter a valid email, e.g., name@gmail.com">
        </div>

        <div class="form-group">
          <label for="phone">Phone Number</label>
          <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($admin['phone_number']) ?>" 
                required pattern="\d{10}" 
                title="Phone number must be exactly 10 digits">
        </div>

        <div class="form-group">
          <label for="role">Role</label>
          <select id="role" name="role" required>
            <option value="Senior Administrator" <?= $admin['role']=='Senior Administrator'?'selected':'' ?>>Senior Administrator</option>
            <option value="Junior Administrator" <?= $admin['role']=='Junior Administrator'?'selected':'' ?>>Junior Administrator</option>
            <option value="Content Manager" <?= $admin['role']=='Content Manager'?'selected':'' ?>>Content Manager</option>
            <option value="Customer Support" <?= $admin['role']=='Customer Support'?'selected':'' ?>>Customer Support</option>
            <option value="Finance Officer" <?= $admin['role']=='Finance Officer'?'selected':'' ?>>Finance Officer</option>
          </select>
        </div>

        <div class="form-group">
          <label for="password">New Password</label>
          <input type="password" id="password" name="password" placeholder="Enter new password">
        </div>

        <button type="submit" class="save-btn">Save Changes</button>
      </form>

      <!-- Delete Profile -->
      <form action="/CeylonGo/public/admin/delete-profile" method="POST" onsubmit="return confirm('Are you sure you want to delete your profile?');">
        <input type="hidden" name="id" value="<?= $admin['id'] ?>">
        <button type="submit" class="delete-btn">Delete Profile</button>
        <br><br>
        <a href="/CeylonGo/public/admin/dashboard" class="dashboard-btn">Go to Dashboard</a>
      </form>
    </div>
  </div>
</div>
</body>
</html>
