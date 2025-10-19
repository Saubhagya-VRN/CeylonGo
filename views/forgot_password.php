<?php
// forgot_password.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password - Ceylon Go</title>
  <link rel="stylesheet" href="../public/css/common.css">
  <link rel="stylesheet" href="../public/css/tourist/tourist_dashboard.css">
  <link rel="stylesheet" href="../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../public/css/tourist/footer.css">
  <style>
    body {
      background-color: #f0f8f0;
    }
    
    .forgot-container {
      max-width: 500px;
      margin: 0 auto;
      padding: 40px 20px;
    }
    
    .forgot-card {
      background: #ffffff;
      border-radius: 16px;
      padding: 40px;
      box-shadow: 0 8px 25px rgba(74, 124, 89, 0.15);
      border: 1px solid rgba(74, 124, 89, 0.1);
    }
    
    .forgot-form {
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
    
    .submit-btn {
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
    
    .submit-btn:hover {
      background: var(--color-primary-600);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(44, 85, 48, 0.3);
    }
    
    .back-link {
      text-align: center;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #e0e8e0;
    }
    
    .back-link a {
      color: var(--color-primary);
      text-decoration: none;
      font-weight: 600;
    }
    
    .back-link a:hover {
      text-decoration: underline;
    }
    
    .info-text {
      background: #f8f9f8;
      border: 1px solid #d0ddd0;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
      color: #666;
      font-size: 14px;
      line-height: 1.5;
    }
    
    @media (max-width: 600px) {
      .forgot-container {
        padding: 20px 10px;
      }
      
      .forgot-card {
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body class="bg-app">
  <!-- Navbar -->
  <?php include 'index_navbar.php'; ?>

  <!-- Forgot Password Section -->
  <section class="intro" style="padding: 60px 20px;">
    <h1>Forgot Password</h1>
    <p>Enter your email address and we'll send you instructions to reset your password.</p>
  </section>

  <section style="padding: 0 20px 60px;">
    <div class="forgot-container">
      <div class="forgot-card">
        <h2 style="text-align: center; margin-bottom: 30px; color: var(--color-primary);">Reset Your Password</h2>
        
        <div class="info-text">
          <strong>How it works:</strong> Enter your email address and user type below. We'll send you a secure link to reset your password. The link will expire in 24 hours for security.
        </div>
        
        <form class="forgot-form" method="POST" action="">
          <div class="form-group">
            <label for="user-type">Account Type</label>
            <select id="user-type" name="user-type" required>
              <option value="">Select account type</option>
              <option value="tourist">Tourist</option>
              <option value="hotel">Hotel</option>
              <option value="guide">Tour Guide</option>
              <option value="transport">Transport Provider</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your registered email address" required>
          </div>

          <button type="submit" class="submit-btn">Send Reset Instructions</button>
        </form>

        <div class="back-link">
          <a href="login.php">← Back to Login</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include 'tourist/footer.php'; ?>
</body>
</html>
