<!DOCTYPE html>
<html lang="en">
<head>
  <?php require_once __DIR__ . '/../partials/app_notify_script.php'; ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Transport Provider Registration</title>

  <!-- CSS Files -->
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/base.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/navbar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/footer.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/buttons.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/forms.css">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <link rel="stylesheet" href="/CeylonGo/public/css/transport/login.css">
</head>
<body>
  <!-- Navbar -->
  <header class="navbar">
    <div class="branding">
      <img src="/CeylonGo/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
      <img src="/CeylonGo/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="#">Home</a>
      <a href="/CeylonGo/public/login"><button class="login-btn">Login</button></a>
    </nav>
  </header>

  <!-- Welcome Section -->
  <section class="wlc-section">
    <h1><span class="icon icon-user-plus"></span> Join as a Transport Provider</h1>
    <p>Welcome! We're excited to have you join our platform. Please fill in your details below to complete your registration and start connecting with travelers.</p>
  </section>

  <!-- Registration Form -->
  <main class="form-container">
    <div class="registration-card">
      <div class="info-box">
        <span class="icon icon-info-circle"></span>
        <p><strong>Important:</strong> Please ensure all information is accurate. You'll need to provide valid documents including your license and vehicle registration.</p>
      </div>

      <form method="POST" action="/CeylonGo/public/registerProvider" enctype="multipart/form-data">
        
        <!-- Personal Information -->
        <h3 class="section-title">
          <span class="icon icon-user"></span>
          Personal Information
        </h3>
        <div class="form-grid">
          <div class="form-group">
            <label><span class="icon icon-id-card"></span> Full Name</label>
            <input type="text" name="full_name" placeholder="Enter your full name" required>
          </div>

          <div class="form-group">
            <label><span class="icon icon-calendar"></span> Date of Birth</label>
            <input type="date" name="dob" required>
          </div>

          <div class="form-group">
            <label><span class="icon icon-id-badge"></span> NIC Number</label>
            <input type="text" name="nic" placeholder="Enter your NIC Number" required>
          </div>

          <div class="form-group">
            <label><span class="icon icon-phone"></span> Contact Number</label>
            <input type="text" name="contact_no" placeholder="Enter your contact number" required>
          </div>

          <div class="form-group full-width">
            <label><span class="icon icon-location"></span> Home Address</label>
            <input type="text" name="address" placeholder="Enter your complete home address" required>
          </div>

          <div class="form-group">
            <label><span class="icon icon-envelope"></span> Email Address</label>
            <input type="email" name="email" placeholder="Enter your email address" required>
          </div>

          <div class="form-group">
            <label><span class="icon icon-image"></span> Profile Picture</label>
            <input type="file" name="profile_image" accept="image/*" required>
          </div>
        </div>

        <!-- License Information -->
        <h3 class="section-title">
          <span class="icon icon-id-card"></span>
          Driving License Information
        </h3>
        <div class="form-grid">
          <div class="form-group">
            <label><span class="icon icon-hashtag"></span> License Number</label>
            <input type="text" name="license_no" placeholder="Enter your license number" required>
          </div>

          <div class="form-group">
            <label><span class="icon icon-calendar-x"></span> License Expiry Date</label>
            <input type="date" name="license_exp_date" id="license_exp_date" required>
          </div>

          <div class="form-group full-width">
            <label><span class="icon icon-file"></span> Upload License Document</label>
            <input type="file" name="license_image" accept="image/*" required>
          </div>
        </div>

        <!-- Vehicle Information -->
        <h3 class="section-title">
          <span class="icon icon-car"></span>
          Vehicle Information
        </h3>
        <div class="form-grid">
          <div class="form-group">
            <label><span class="icon icon-car-side"></span> Vehicle Type</label>
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
          </div>

          <div class="form-group">
            <label><span class="icon icon-hashtag"></span> Vehicle Number</label>
            <input type="text" name="vehicle_no" placeholder="e.g., ABC-1234" required>
          </div>

          <div class="form-group">
            <label><span class="icon icon-users"></span> Passenger Capacity</label>
            <input type="number" name="psg_capacity" min="1" value="1" placeholder="Enter capacity" required>
          </div>

          <div class="form-group">
            <label><span class="icon icon-camera"></span> Vehicle Photo</label>
            <input type="file" name="vehicle_image" accept="image/*" required>
          </div>
        </div>

        <!-- Account Security -->
        <h3 class="section-title">
          <span class="icon icon-lock"></span>
          Account Security
        </h3>
        <div class="form-grid">
          <div class="form-group">
            <label><span class="icon icon-key"></span> Password</label>
            <input type="password" name="password" placeholder="Create a strong password" required>
          </div>

          <div class="form-group">
            <label><span class="icon icon-key"></span> Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="Re-enter your password" required>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="buttons">
          <button type="button" class="back-button" onclick="window.history.back()">
            <span class="icon icon-arrow-left"></span> Back
          </button>
          <button type="submit" class="register-btn">
            <span class="icon icon-user-plus"></span> Complete Registration
          </button>
        </div>

      </form>
    </div>
  </main>

  <!-- Footer -->
  <footer>
    <ul>
      <li><a href="#">About Us</a></li>
      <li><a href="#">Contact Us</a></li>
    </ul>
  </footer>

  <script>
    // Password validation
    const passwordInput = document.querySelector('input[name="password"]');
    const confirmPasswordInput = document.querySelector('input[name="confirm_password"]');
    const form = document.querySelector('form');

    // Add password strength indicator
    const passwordGroup = passwordInput.closest('.form-group');
    const strengthIndicator = document.createElement('div');
    strengthIndicator.style.cssText = 'margin-top: 8px; font-size: 12px;';
    passwordGroup.appendChild(strengthIndicator);

    // Password validation function
    function validatePassword(password) {
      const minLength = password.length >= 8;
      const hasUpperCase = /[A-Z]/.test(password);
      const hasLowerCase = /[a-z]/.test(password);
      const hasNumber = /[0-9]/.test(password);
      const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);

      return {
        valid: minLength && hasUpperCase && hasLowerCase && hasNumber && hasSpecial,
        minLength,
        hasUpperCase,
        hasLowerCase,
        hasNumber,
        hasSpecial
      };
    }

    // Update strength indicator
    passwordInput.addEventListener('input', function() {
      const validation = validatePassword(this.value);
      
      let message = '<div style="color: #666; font-weight: 600; margin-bottom: 5px;">Password must contain:</div>';
      message += `<div style="color: ${validation.minLength ? '#28a745' : '#dc3545'};">✓ At least 8 characters</div>`;
      message += `<div style="color: ${validation.hasUpperCase ? '#28a745' : '#dc3545'};">✓ Uppercase letter (A-Z)</div>`;
      message += `<div style="color: ${validation.hasLowerCase ? '#28a745' : '#dc3545'};">✓ Lowercase letter (a-z)</div>`;
      message += `<div style="color: ${validation.hasNumber ? '#28a745' : '#dc3545'};">✓ Number (0-9)</div>`;
      message += `<div style="color: ${validation.hasSpecial ? '#28a745' : '#dc3545'};">✓ Special character (!@#$%^&*)</div>`;
      
      strengthIndicator.innerHTML = message;
    });

    // Form submission validation
    form.addEventListener('submit', function(e) {
      const password = passwordInput.value;
      const confirmPassword = confirmPasswordInput.value;
      const validation = validatePassword(password);

      if (!validation.valid) {
        e.preventDefault();
        alert('Password must be at least 8 characters and include uppercase, lowercase, numbers, and special characters.');
        return false;
      }

      if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
      }

      // Validate license expiry date
      const licenseExpDate = document.getElementById('license_exp_date').value;
      if (licenseExpDate) {
        const selectedDate = new Date(licenseExpDate);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (selectedDate <= today) {
          e.preventDefault();
          alert('License expiry date must be a future date!');
          return false;
        }
      }
    });

    // Set minimum date for license expiry to tomorrow
    const licenseExpInput = document.getElementById('license_exp_date');
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const minDate = tomorrow.toISOString().split('T')[0];
    licenseExpInput.setAttribute('min', minDate);
  </script>
</body>
</html>
