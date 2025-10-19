<?php
// views/guide/guide_register.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ceylon Go - Tour Guide Registration</title>
    <link rel="stylesheet" href="../../public/css/transport/register.css">
    <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
    <link rel="stylesheet" href="../../public/css/tourist/footer.css">
    <style>
      body {
        background-color: #f0f8f0; /* Light greenish background from tourist_dashboard */
      }
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
    <h1>Welcome, Tour Guide!</h1>
    <p>Thank you for registering as a tour guide — we're glad to have you with us!<br>
    Please fill in your details below to register.</p>
  </section>

  <!-- Registration Form -->
  <main class="form-container">
<form action="../../controllers/guide/register_process.php" method="POST" enctype="multipart/form-data">
  <input type="hidden" name="user_type" value="guide">

  <label>First Name</label>
  <input type="text" name="first_name" placeholder="Enter your first name" required>

  <label>Last Name</label>
  <input type="text" name="last_name" placeholder="Enter your last name" required>

  <label>NIC Number</label>
  <input type="text" name="nic" placeholder="Enter your NIC Number" required>

  <label>License Number</label>
  <input type="text" name="license_number" placeholder="Enter your Tour Guide License Number" required>

  <label>Upload License</label>
  <input type="file" name="license_file">

  <label>Specialization</label>
  <div class="vehicle-type">
    <label><input type="radio" name="specialization" value="Cultural Heritage" required> Cultural Heritage</label>
    <label><input type="radio" name="specialization" value="Historical Sites"> Historical Sites</label>
    <label><input type="radio" name="specialization" value="Religious Sites"> Religious Sites</label>
    <label><input type="radio" name="specialization" value="Nature & Wildlife"> Nature & Wildlife</label>
  </div>

  <label>Languages Spoken</label>
  <input type="text" name="languages" placeholder="e.g., English, Sinhala, Tamil" required>

  <label>Years of Experience</label>
  <input type="number" name="experience" placeholder="Enter years of experience" min="0">

  <label>Upload Profile Photo</label>
  <input type="file" name="profile_photo">

  <label>Contact Number</label>
  <input type="text" name="contact_number" placeholder="Enter your contact number" required>

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


    <script>
        function redirectToDashboard(event) {
  event.preventDefault(); // stops the default form submission
  window.location.href = "guide_dashboard.php"; // go to dashboard
}

</script>
  </main>

   <!-- Footer Links -->
    <?php include '../tourist/footer.php'; ?>
</body>
</html>
