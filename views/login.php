<?php
// Login view - processing is handled by AuthController
$error = $error ?? '';
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
  <link rel="stylesheet" href="../public/css/login.css">
  
    
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

        <?php if (isset($error) && !empty($error)) { ?>
          <div style="color: red; text-align: center; margin-bottom: 15px;">
            <?php echo htmlspecialchars($error); ?>
          </div>
        <?php } ?>

        <form class="login-form" method="POST" action="/CeylonGo/public/login">
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
          <p>Don't have an account? <a href="/CeylonGo/public/register">Register here</a></p>
         <!--<p><a href="forgot_password.php">Forgot Password?</a></p>-->
      </div>
    </div>
  </section>

  <?php include 'tourist/footer.php'; ?>
</body>
</html>
