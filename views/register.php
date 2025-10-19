<?php
// header.php (inside views/tourist)
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | Ceylon Go</title>
  <link rel="stylesheet" href="../public/css/register.css">
  <link rel="stylesheet" href="../public/css/tourist/footer.css">
  <style>
    body {
      background-color: #f0f8f0; /* Light greenish background from tourist_dashboard */
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <?php include 'index_navbar.php'; ?>

  <!-- Main Section -->
  <div class="register-container">
    <h1>Register</h1>
    <p class="subtitle">Create Your Account To get Started!</p>
    <p class="small-text">Select How You Would Like to Register</p>

    <div class="register-options">
      <div class="option-header">
        <span>As a:</span>
      </div>

      <div class="option-list">
        <a href="tourist/tourist_register.php" class="option-box">Tourist</a>
        <a href="hotel/hotel_register.php" class="option-box">Hotel</a>
        <a href="guide/guide_register.php" class="option-box">Tour Guide</a>
        <a href="transport/transport_register.php" class="option-box">Transport Provider</a>
      </div>
    </div>

    <div class="buttons">
      <button class="btn-register" onclick="window.location.href='tourist/tourist_dashboard.php'">Back</button>
    </div>
  </div>

  <!-- Footer -->
  <?php include 'tourist/footer.php'; ?>

</body>
</html>

