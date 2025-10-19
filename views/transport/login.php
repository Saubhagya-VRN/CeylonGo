<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Transport Provider Registration</title>

  <!-- CSS Files -->
  <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/base.css">
  <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/navbar.css">
  <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/sidebar.css">
  <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/footer.css">

  <!-- Components -->
  <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/cards.css">
  <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/buttons.css">
  <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/forms.css">

  <!-- Page-specific -->
  <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/timeline.css">
  <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/tables.css">
  <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/profile.css">
  <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/reviews.css">
  <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/charts.css">

  <!-- Responsive -->
  <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/responsive.css">
</head>
<body>
  <!-- Navbar -->
  <header class="navbar">
    <div class="branding">
      <img src="/Ceylon_Go/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="#">Home</a>
      <button class="login-btn">Login</button>
    </nav>
  </header>

  <!-- Welcome Section -->
  <section class="wlc-section">
    <h1>Welcome, Transport Provider!</h1>
    <p>Thank you for registering as a transport provider — we’re glad to have you with us!<br>
    Please fill in your details below to register.</p>
  </section>

  <!-- Registration Form -->
  <main class="form-container">
    <form method="POST" action="/Ceylon_Go/public/registerProvider" enctype="multipart/form-data">
      
      <label>Full Name</label>
      <input type="text" name="full_name" placeholder="Enter your full name" required>

      <label>Date of Birth</label>
      <input type="date" name="dob" required>

      <label>NIC Number</label>
      <input type="text" name="nic" placeholder="Enter your NIC Number" required>

      <label>Home Address</label>
      <input type="text" name="address" placeholder="Enter your Home Address" required>

      <label>Contact Number</label>
      <input type="text" name="contact_no" placeholder="Enter your contact number" required>

      <label>Upload Your Picture</label>
      <input type="file" name="profile_image" accept="image/*" required>

      <label>Email</label>
      <input type="email" name="email" placeholder="Enter your Email Address" required>

      <label>License Number</label>
      <input type="text" name="license_no" placeholder="Enter your License Number" required>

      <label>License Expiry Date</label>
      <input type="date" name="license_exp_date" required>

      <label>Upload License</label>
      <input type="file" name="license_image" accept="image/*" required>

      <label>Vehicle Type</label>
      <select name="vehicle_type" required>
        <?php if (!empty($vehicleTypes)): ?>
          <?php foreach ($vehicleTypes as $type): ?>
            <option value="<?= htmlspecialchars($type['type_id']) ?>">
              <?= htmlspecialchars($type['type_name']) ?>
            </option>
          <?php endforeach; ?>
        <?php else: ?>
          <option value="">No vehicle types available</option>
        <?php endif; ?>
      </select>

      <label>Vehicle Number</label>
      <input type="text" name="vehicle_no" placeholder="Enter your Vehicle Number" required>

      <label>Upload Vehicle Photo</label>
      <input type="file" name="vehicle_image" accept="image/*" required>

      <label>Passenger Capacity</label>
      <input type="number" name="psg_capacity" placeholder="Enter capacity" required>

      <label>Password</label>
      <input type="password" name="password" placeholder="Create Password" required>

      <label>Confirm Password</label>
      <input type="password" name="confirm_password" placeholder="Confirm Password" required>

      <div class="buttons">
        <button type="button" class="back-button">Back</button>
        <button type="submit" class="register-btn">Register</button>
      </div>

    </form>
  </main>

  <!-- Footer -->
  <footer>
    <ul>
      <li><a href="#">About Us</a></li>
      <li><a href="#">Contact Us</a></li>
    </ul>
  </footer>
</body>
</html>
