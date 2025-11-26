<?php
// views/transport/transport_register.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Transport Provider Registration</title>
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
    <h1>Welcome, Transport Provider!</h1>
    <p>Thank you for registering as a transport provider — please fill in your details below.</p>
  </section>

  <!-- Registration Form -->
  <main class="form-container">
    <form action="../../controllers/transport/register_process.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="user_type" value="transport">

      <label>First Name</label>
      <input type="text" name="first_name" placeholder="Enter your first name" required>

      <label>Last Name</label>
      <input type="text" name="last_name" placeholder="Enter your last name" required>

      <label>NIC Number</label>
      <input type="text" name="nic" placeholder="Enter your NIC Number" required>

      <label>License Number</label>
      <input type="text" name="license_number" placeholder="Enter your License Number" required>

      <label>Upload License</label>
      <input type="file" name="license_file" required>

      <label>Vehicle Type</label>
      <div class="vehicle-type">
        <label><input type="radio" name="vehicle_type" value="Car" required> Car</label>
        <label><input type="radio" name="vehicle_type" value="Motor Cycle"> Motor Cycle</label>
        <label><input type="radio" name="vehicle_type" value="Van"> Van</label>
        <label><input type="radio" name="vehicle_type" value="Tuk"> Tuk</label>
      </div>

      <label>Vehicle Number</label>
      <input type="text" name="vehicle_number" placeholder="CAA-8475" required>

      <label>Upload Vehicle Photo</label>
      <input type="file" name="vehicle_photo" required>

      <label>Contact Number</label>
      <input type="text" name="contact_number" placeholder="Enter your contact number" required>

      <label>Email</label>
      <input type="email" name="email" placeholder="Enter your Email Address" required>

      <label>Password</label>
      <input type="password" id="password" name="password" required 
             pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
             placeholder="8+ chars, uppercase, lowercase, number, special char"
             title="Password must contain at least 8 characters, including uppercase, lowercase, number, and special character">
      <small id="password-error" style="color: red; display: none;"></small>

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
