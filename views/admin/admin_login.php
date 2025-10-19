<?php
  session_start();
  include("../../config/database.php");

  // Handle login form
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $adminUser = $_POST['username'];
      $adminPass = $_POST['password'];

      $sql = "SELECT * FROM admin WHERE username = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("s", $adminUser);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result && $result->num_rows > 0) {
          $row = $result->fetch_assoc();
          if (password_verify($adminPass, $row['password'])) {
              $_SESSION['admin_id'] = $row['id'];
              $_SESSION['admin_username'] = $row['username'];
              header("Location: admin_dashboard.php");
              exit();
          } else {
              echo "<script>alert('Invalid password!');</script>";
          }
      } else {
          echo "<script>alert('Invalid username!');</script>";
      }

      $stmt->close();
      $conn->close();
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Ceylon Go</title>
    <link rel="stylesheet" href="../../public/css/admin/admin_login.css">
  </head>

  <body>
    <div class="login-container">
      <div class="login-box">
        <div class="brand">
          <img src="../../public/images/logo.png" alt="Ceylon Go Logo" class="logo-img">
          <h2>Admin Login</h2>
        </div>

        <!-- Form -->
        <form action="admin_login.php" method="POST" class="login-form">
          <div class="form-group">
            <label for="username">Admin Username</label>
            <input type="text" id="username" name="username" placeholder="Enter admin username" required>
          </div>

          <div class="form-group">
            <label for="password">Admin Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required>
          </div>

          <button type="submit" class="login-btn">Login</button>
        </form>

        <div class="extras">
          <a href="admin_forgot_pwd.php">Forgot Password?</a>
        </div>
      </div>
    </div>
  </body>
</html>