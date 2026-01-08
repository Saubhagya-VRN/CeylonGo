<?php
// views/transport/transport_register.php

// Display any error messages
if (isset($_SESSION['register_errors'])) {
    echo '<div class="alert alert-danger">';
    foreach ($_SESSION['register_errors'] as $error) {
        echo '<p>' . htmlspecialchars($error) . '</p>';
    }
    echo '</div>';
    unset($_SESSION['register_errors']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Transport Provider Registration</title>
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/register.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/tourist/navbar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/tourist/footer.css">
  <style>
    body { background-color: #f0f8f0; }
    .alert { padding: 15px; margin: 15px auto; max-width: 800px; border-radius: 5px; }
    .alert-danger { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
    .alert p { margin: 5px 0; }
    h3 { margin-top: 25px; color: #2c5530; border-bottom: 2px solid #4CAF50; padding-bottom: 8px; }
  </style>
</head>
<body>
  <!-- Header -->
  <header class="navbar">
      <div class="branding">
          <img src="/CeylonGo/public/images/logo.png" class="logo-img">
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
    <h1>Welcome, Transport Provider!</h1>
    <p>Thank you for registering as a transport provider — please fill in your details below.</p>
  </section>

  <!-- Registration Form -->
  <main class="form-container">
    <form action="/CeylonGo/public/transporter/register" method="POST" enctype="multipart/form-data">
      
      <h3>Personal Information</h3>
      
      <label>Full Name</label>
      <input type="text" name="full_name" placeholder="Enter your full name" required>

      <label>Date of Birth</label>
      <input type="date" name="dob" required>

      <label>NIC Number</label>
      <input type="text" name="nic" placeholder="Enter your NIC Number" required>

      <label>Address</label>
      <input type="text" name="address" placeholder="Enter your address" required>

      <label>Contact Number</label>
      <input type="text" name="contact_no" placeholder="Enter your contact number (10 digits)" required>

      <label>Upload Profile Photo</label>
      <input type="file" name="profile_image" accept="image/*" required>

      <h3>License Information</h3>

      <label>License Number</label>
      <input type="text" name="license_no" placeholder="Enter your License Number" required>

      <label>License Expiry Date</label>
      <input type="date" name="license_exp_date" required>

      <label>Upload License Image</label>
      <input type="file" name="license_image" accept="image/*" required>

      <h3>Vehicle Information</h3>

      <label>Vehicle Number</label>
      <input type="text" name="vehicle_no" placeholder="CAA-8475" required>

      <label>Vehicle Type</label>
      <select name="vehicle_type" required>
        <option value="">Select Vehicle Type</option>
        <option value="1">TUK</option>
        <option value="2">VAN</option>
      </select>

      <label>Passenger Capacity</label>
      <input type="number" name="psg_capacity" placeholder="Enter passenger capacity" min="1" required>

      <label>Upload Vehicle Photo</label>
      <input type="file" name="vehicle_image" accept="image/*" required>

      <h3>Account Information</h3>

      <label>Email</label>
      <input type="email" name="email" placeholder="Enter your Email Address" required>

      <label>Password</label>
      <input type="password" name="password" placeholder="Create Password" required>

      <label>Confirm Password</label>
      <input type="password" name="confirm_password" placeholder="Enter your password again" required>

      <div class="buttons">
        <button type="button" class="back-btn" onclick="history.back()">Back</button>
        <button type="submit" class="register-btn">Register</button>
      </div>
    </form>
  </main>

   <!-- Footer Links -->
    <?php include __DIR__ . '/../tourist/footer.php'; ?>
</body>
</html>
