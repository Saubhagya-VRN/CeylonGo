<?php
// Connect to database
include('../config/database.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_type = trim($_POST['user-type']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($user_type) && !empty($email) && !empty($password)) {

        // Identify table and redirect path
        switch ($user_type) {
            case 'tourist':
                $table = 'tourist_users';
                $redirect = 'tourist/tourist_dashboard.php';
                break;
            case 'hotel':
                $table = 'hotel_users';
                $redirect = 'hotel/hotel_dashboard.php';
                break;
            case 'guide':
                $table = 'guide_users';
                $redirect = 'guide/guide_dashboard.php';
                break;
            case 'transport':
                $table = 'transport_users';
                $redirect = 'transport/dashboard.php';
                break;
          

            default:
                $error = "Invalid user type selected.";
                $table = '';
        }

        if (!empty($table)) {
            // Query to check credentials
            $sql = "SELECT * FROM $table WHERE email = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                // Compare password directly (since no hash used)
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['user_type'] = $user_type;
                    $_SESSION['user_email'] = $email;
                    header("Location: $redirect");
                    exit();
                } else {
                    $error = "Incorrect password.";
                }
            } else {
                $error = "No account found with that email.";
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Ceylon Go</title>
  <link rel="stylesheet" href="../public/css/common.css">
  <link rel="stylesheet" href="../public/css/tourist/tourist_dashboard.css">
  <link rel="stylesheet" href="../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../public/css/tourist/footer.css">
  <style>
    body {
      background-color: #f0f8f0;
    }
    
    .login-container {
      max-width: 500px;
      margin: 0 auto;
      padding: 40px 20px;
    }
    
    .login-card {
      background: #ffffff;
      border-radius: 16px;
      padding: 40px;
      box-shadow: 0 8px 25px rgba(74, 124, 89, 0.15);
      border: 1px solid rgba(74, 124, 89, 0.1);
    }
    
    .login-form {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    
    .form-group {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    
    .form-group label {
      font-weight: 600;
      color: var(--color-primary);
      font-size: 14px;
    }
    
    .form-group input,
    .form-group select {
      padding: 12px 16px;
      border: 2px solid #d0ddd0;
      border-radius: 8px;
      font-size: 16px;
      transition: all 0.3s ease;
      background: #fff;
    }
    
    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: var(--color-primary-600);
      box-shadow: 0 0 0 3px rgba(74, 124, 89, 0.1);
    }
    
    .form-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 10px 0;
    }
    
    .checkbox-label {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      color: #666;
    }
    
    .checkbox-label input[type="checkbox"] {
      width: auto;
      margin: 0;
    }
    
    .forgot-link {
      color: var(--color-primary);
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
    }
    
    .forgot-link:hover {
      text-decoration: underline;
    }
    
    .login-btn {
      background: var(--color-primary);
      color: #fff;
      border: none;
      padding: 14px 28px;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 10px;
    }
    
    .login-btn:hover {
      background: var(--color-primary-600);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(44, 85, 48, 0.3);
    }
    
    .register-link {
      text-align: center;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #e0e8e0;
    }
    
    .register-link p {
      color: #666;
      margin: 0;
    }
    
    .register-link a {
      color: var(--color-primary);
      text-decoration: none;
      font-weight: 600;
    }
    
    .register-link a:hover {
      text-decoration: underline;
    }
    
    .user-type-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
      gap: 10px;
      margin-top: 10px;
    }
    
    .user-type-option {
      padding: 12px 16px;
      border: 2px solid #d0ddd0;
      border-radius: 8px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
      background: #fff;
    }
    
    .user-type-option:hover {
      border-color: var(--color-primary-600);
      background: #f8f9f8;
    }
    
    .user-type-option.selected {
      border-color: var(--color-primary);
      background: var(--color-primary);
      color: #fff;
    }
    
    @media (max-width: 600px) {
      .login-container {
        padding: 20px 10px;
      }
      
      .login-card {
        padding: 30px 20px;
      }
      
      .form-options {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
      }
    }
  </style>
</head>
<body class="bg-app">
  <?php include 'index_navbar.php'; ?>

  <section class="intro" style="padding: 60px 20px;">
    <h1>Welcome Back</h1>
    <p>Login to your Ceylon Go account to continue planning your perfect trip!</p>
  </section>

  <section style="padding: 0 20px 60px;">
    <div class="login-container">
      <div class="login-card">
        <h2 style="text-align: center; margin-bottom: 30px; color: var(--color-primary);">Login to Your Account</h2>

        <?php if (isset($error)) { ?>
          <div style="color: red; text-align: center; margin-bottom: 15px;">
            <?php echo htmlspecialchars($error); ?>
          </div>
        <?php } ?>

        <form class="login-form" method="POST" action="">
          <div class="form-group">
            <label for="user-type">Login As</label>
            <select id="user-type" name="user-type" required>
              <option value="">Select user type</option>
              <option value="tourist">Tourist</option>
              <option value="hotel">Hotel</option>
              <option value="guide">Tour Guide</option>
              <option value="transport">Transport Provider</option>
              
            </select>
          </div>
          
          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email address" required>
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
          </div>

          

          <button type="submit" class="login-btn">Login</button>
        </form>

        <div class="register-link">
          <p>Don't have an account? <a href="register.php">Register here</a></p>
          <p>Are you an Admin? <a href="admin/admin_login.php">Login here</a></p>
      </div>
    </div>
  </section>

  <?php include 'tourist/footer.php'; ?>
</body>
</html>
