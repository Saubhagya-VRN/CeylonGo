<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Hotel Register</title>
  <link rel="stylesheet" href="../../public/css/transport/register.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
</head>
<body>

  <!-- Header -->
  <header class="navbar">
      <div class="branding">
          <img src="../../public/images/logo.png" class="logo-img">
          <div class="logo-text">Ceylon Go</div>
      </div>
      <nav class="nav-links">
          <a href="/CeylonGo/public/tourist/dashboard">Home</a>
          <a href="/CeylonGo/public/contact">Contact Us</a>
          <a href="/CeylonGo/public/login" class="login-btn">Login</a>
      </nav>
  </header>

  <!-- Welcome Section -->
  <section class="welcome-section">
    <h1>Welcome, Hotel!</h1>
    <p>Thank you for registering your hotel — we’re glad to have you with us! <br>
       Please fill in your details below to register.</p>
  </section>

  <!-- Registration Form -->
  <main class="form-container">
    <form action="/CeylonGo/public/hotel/register" method="POST" enctype="multipart/form-data">

      <label>Hotel Name</label>
      <input type="text" id="hname" name="hname" required placeholder="Enter the hotel name">

      <label>Location</label>
      <input type="text" id="location" name="location" required placeholder="Enter the location">

      <label>City</label>
      <input type="text" id="city" name="city" required placeholder="Enter the city">

      <label>Hotel Picture</label>
      <input type="file" id="hotel_image" name="hotel_image" accept="image/*" required>

      <label>Contact Number</label>
      <input type="tel" id="contact" name="contact" required pattern="[0-9]{10}" placeholder="Enter your 10-digit contact number">

      <label>Email</label>
      <input type="email" id="email" name="email" required placeholder="Enter your Email Address">

      <label>Password</label>
      <input type="password" id="password" name="password" required 
             pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
             placeholder="8+ chars, uppercase, lowercase, number, special char"
             title="Password must contain at least 8 characters, including uppercase, lowercase, number, and special character">
      <small id="password-error" style="color: red; display: none;"></small>

      <label>Confirm Password</label>
      <input type="password" id="confirm-password" name="confirm_password" required placeholder="Enter your password again">

      <div class="buttons">
        <button type="button" class="back-btn" onclick="window.history.back()">Back</button>
        <button type="submit" class="register-btn">Register</button>
      </div>
    </form>
  </div>
</main>

  <!-- Footer -->
  <?php include '../tourist/footer.php'; ?>

  <script>
    document.querySelector('form').addEventListener('submit', function(e) {
      const password = document.getElementById('password').value;
      const passwordError = document.getElementById('password-error');
      
      // Password strength validation
      const hasUpperCase = /[A-Z]/.test(password);
      const hasLowerCase = /[a-z]/.test(password);
      const hasNumber = /\d/.test(password);
      const hasSpecialChar = /[@$!%*?&]/.test(password);
      const hasMinLength = password.length >= 8;
      
      if (!hasMinLength || !hasUpperCase || !hasLowerCase || !hasNumber || !hasSpecialChar) {
        e.preventDefault();
        passwordError.textContent = 'You have to use 8 characters, uppercase, lowercase, number, and special character';
        passwordError.style.display = 'block';
        passwordError.style.color = 'red';
        return false;
      } else {
        passwordError.style.display = 'none';
      }
    });
  </script>
</body>
</html>
