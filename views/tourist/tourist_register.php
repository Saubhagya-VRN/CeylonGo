<?php
// views/tourist/tourist_register.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Register</title>
  <link rel="stylesheet" href="../../public/css/transport/register.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
  <style>
    body { background-color: #f0f8f0; }
  </style>
</head>
<body>

  <!-- Header -->
  <header class="navbar">
      <div class="branding">
          <img src="../../public/images/logo.png" class="logo-img">
          <div class="logo-text">Ceylon Go</div>
          </div>
      <nav class="nav-links">
      <a href="../tourist/tourist_dashboard.php">Home</a>
      <a href="../contact.php">Contact Us</a>
      <a href="../login.php" class="login-btn">Login</a>
    </nav>
  </header>

  <!-- Welcome Section -->
  <section class="welcome-section">
    <h1>Welcome, Traveler!</h1>
    <p>Thank you for registering — we're glad to help you!<br>
    Please fill in your details below to register.</p>
  </section>

  <!-- Registration Form -->
  <main class="form-container">
    <form action="../../controllers/tourist/register_process.php" method="POST">
      <label>First Name</label>
      <input type="text" id="fname" name="fname" required placeholder="Enter your first name">

      <label>Last Name</label>
      <input type="text" id="lname" name="lname" required placeholder="Enter your last name">

      <label>Contact Number</label>
      <input type="tel" id="contact" name="contact" required pattern="[0-9]{10}" placeholder="Enter your 10-digit contact number">

      <label>Email</label>
      <input type="email" id="email" name="email" required placeholder="Enter your Email Address">

      <label>Password</label>
      <input type="password" id="password" name="password" required minlength="6" placeholder="Create Password">

      <label>Confirm Password</label>
      <input type="password" id="confirm-password" name="confirm_password" required placeholder="Enter your password again">

      <div class="buttons">
        <button type="button" class="back-btn" onclick="history.back()">Back</button>
        <button type="submit" class="register-btn">Register</button>
      </div>
    </form>
  </main>

  <!-- Footer Links -->
  <?php include '../tourist/footer.php'; ?>

</body>
</html>
