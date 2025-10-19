<?php
  session_start();
  include("../../config/database.php");

  if (!isset($_SESSION['admin_id'])) {
      header("Location: admin_login.php");
      exit();
  }

  $admin_id = $_SESSION['admin_id'];
  $message = "";

  // Update profile
  if (isset($_POST['update_profile'])) {
      $username = $_POST['username'];
      $email    = $_POST['email'];
      $phone    = $_POST['phone'];
      $role     = $_POST['role'];
      $password = $_POST['password'];

      if (!empty($password)) {
          // hash new password
          $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
          $sql = "UPDATE admin SET username=?, email=?, phone_number=?, role=?, password=? WHERE id=?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("sssssi", $username, $email, $phone, $role, $hashedPassword, $admin_id);
      } else {
          $sql = "UPDATE admin SET username=?, email=?, phone_number=?, role=? WHERE id=?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("ssssi", $username, $email, $phone, $role, $admin_id);
      }

      if ($stmt->execute()) {
          $message = "✅ Profile updated successfully!";
      } else {
          $message = "❌ Error updating profile.";
      }
  }

  //  Delete profile
  if (isset($_POST['delete_profile'])) {
      $deleteQuery = "DELETE FROM admin WHERE id = ?";
      $stmt = $conn->prepare($deleteQuery);
      $stmt->bind_param("i", $admin_id);

      if ($stmt->execute()) {
          session_destroy();
          header("Location: admin_login.php?msg=profile_deleted");
          exit();
      } else {
          $message = "❌ Error deleting profile.";
      }
  }

  $sql = "SELECT * FROM admin WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $admin_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $admin = $result->fetch_assoc();
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

        <div class="profile-info">
          <h3>Profile Details</h3>
          <p><strong>Username:</strong> <?= htmlspecialchars($admin['username']) ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($admin['email']) ?></p>
          <p><strong>Phone:</strong> <?= htmlspecialchars($admin['phone_number']) ?></p>
          <p><strong>Role:</strong> <?= htmlspecialchars($admin['role']) ?></p>
        </div>

        <!-- Edit Profile Form -->
        <div class="edit-profile">
          <h3>Edit Profile</h3>
          <form action="../../controllers/admin/update_profile.php" method="POST">
            <input type="hidden" name="id" value="<?= $admin['id'] ?>">

            <div class="form-group">
              <label for="username">Admin Username</label>
              <input type="text" id="username" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required>
            </div>

            <div class="form-group">
              <label for="email">Admin Email</label>
              <input type="email" id="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>
            </div>

            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($admin['phone_number']) ?>" required>
            </div>

            <div class="form-group">
              <label for="role">Role</label>
              <input type="text" id="role" name="role" value="<?= htmlspecialchars($admin['role']) ?>" required>
            </div>

            <div class="form-group">
              <label for="password">New Password</label>
              <input type="password" id="password" name="password" placeholder="Enter new password">
            </div>

            <button type="submit" class="save-btn">Save Changes</button>
          </form>

          <!-- Delete Profile -->
          <form action="../../controllers/admin/delete_profile.php" method="POST" onsubmit="return confirm('Are you sure you want to delete your profile? This action cannot be undone.');">
            <input type="hidden" name="id" value="<?= $admin['id'] ?>">
            <button type="submit" class="delete-btn">Delete Profile</button>
            <br><br>
            <a href="admin_dashboard.php" class="dashboard-btn">Go to Dashboard</a>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
