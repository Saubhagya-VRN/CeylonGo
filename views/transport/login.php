<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Transport Provider Registration</title>

  <!-- CSS Files -->
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/base.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/navbar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/footer.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/buttons.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/forms.css">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #e8f5e9 100%);
      min-height: 100vh;
    }

    .wlc-section {
      text-align: center;
      padding: 40px 20px;
      background: linear-gradient(135deg, #66bb6a 0%, #43a047 100%);
      color: white;
      margin-bottom: 40px;
    }

    .wlc-section h1 {
      font-size: 32px;
      margin-bottom: 10px;
      font-weight: 700;
    }

    .wlc-section p {
      font-size: 16px;
      opacity: 0.95;
      max-width: 600px;
      margin: 0 auto;
      line-height: 1.6;
    }

    .form-container {
      max-width: 900px;
      margin: 0 auto 60px;
      padding: 0 20px;
    }

    .registration-card {
      background: white;
      border-radius: 16px;
      padding: 40px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }

    .section-title {
      font-size: 20px;
      color: #2c5530;
      margin: 30px 0 20px 0;
      padding-bottom: 10px;
      border-bottom: 2px solid #2c5530;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .section-title:first-child {
      margin-top: 0;
    }

    .section-title i {
      font-size: 22px;
    }

    .form-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
      margin-bottom: 20px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
    }

    .form-group.full-width {
      grid-column: 1 / -1;
    }

    .form-group label {
      font-size: 14px;
      font-weight: 600;
      color: #333;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .form-group label i {
      color: #2c5530;
      font-size: 14px;
    }

    .form-group input,
    .form-group select {
      padding: 12px 15px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      font-size: 14px;
      transition: all 0.3s ease;
      background: #fafafa;
    }

    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: #2c5530;
      background: white;
      box-shadow: 0 0 0 3px rgba(44, 85, 48, 0.1);
    }

    .form-group input[type="file"] {
      padding: 10px;
      background: white;
      cursor: pointer;
    }

    .buttons {
      display: flex;
      gap: 15px;
      justify-content: flex-end;
      margin-top: 30px;
      padding-top: 30px;
      border-top: 2px solid #f0f0f0;
    }

    .back-button {
      padding: 12px 30px;
      background: #6c757d;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .back-button:hover {
      background: #5a6268;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .register-btn {
      padding: 12px 40px;
      background: linear-gradient(135deg, #2c5530 0%, #1e3d21 100%);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .register-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(44, 85, 48, 0.4);
    }

    .info-box {
      background: #e8f5e9;
      border-left: 4px solid #2c5530;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 25px;
      display: flex;
      align-items: start;
      gap: 12px;
    }

    .info-box i {
      color: #2c5530;
      font-size: 20px;
      margin-top: 2px;
    }

    .info-box p {
      margin: 0;
      color: #333;
      font-size: 14px;
      line-height: 1.5;
    }

    @media (max-width: 768px) {
      .form-grid {
        grid-template-columns: 1fr;
      }

      .registration-card {
        padding: 25px;
      }

      .buttons {
        flex-direction: column;
      }

      .back-button,
      .register-btn {
        width: 100%;
        justify-content: center;
      }
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <header class="navbar">
    <div class="branding">
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
    <h1><i class="fa-solid fa-user-plus"></i> Join as a Transport Provider</h1>
    <p>Welcome! We're excited to have you join our platform. Please fill in your details below to complete your registration and start connecting with travelers.</p>
  </section>

  <!-- Registration Form -->
  <main class="form-container">
    <div class="registration-card">
      <div class="info-box">
        <i class="fa-solid fa-circle-info"></i>
        <p><strong>Important:</strong> Please ensure all information is accurate. You'll need to provide valid documents including your license and vehicle registration.</p>
      </div>

      <form method="POST" action="/CeylonGo/public/registerProvider" enctype="multipart/form-data">
        
        <!-- Personal Information -->
        <h3 class="section-title">
          <i class="fa-solid fa-user"></i>
          Personal Information
        </h3>
        <div class="form-grid">
          <div class="form-group">
            <label><i class="fa-solid fa-id-card"></i> Full Name</label>
            <input type="text" name="full_name" placeholder="Enter your full name" required>
          </div>

          <div class="form-group">
            <label><i class="fa-solid fa-calendar"></i> Date of Birth</label>
            <input type="date" name="dob" required>
          </div>

          <div class="form-group">
            <label><i class="fa-solid fa-id-badge"></i> NIC Number</label>
            <input type="text" name="nic" placeholder="Enter your NIC Number" required>
          </div>

          <div class="form-group">
            <label><i class="fa-solid fa-phone"></i> Contact Number</label>
            <input type="text" name="contact_no" placeholder="Enter your contact number" required>
          </div>

          <div class="form-group full-width">
            <label><i class="fa-solid fa-location-dot"></i> Home Address</label>
            <input type="text" name="address" placeholder="Enter your complete home address" required>
          </div>

          <div class="form-group">
            <label><i class="fa-solid fa-envelope"></i> Email Address</label>
            <input type="email" name="email" placeholder="Enter your email address" required>
          </div>

          <div class="form-group">
            <label><i class="fa-solid fa-image"></i> Profile Picture</label>
            <input type="file" name="profile_image" accept="image/*" required>
          </div>
        </div>

        <!-- License Information -->
        <h3 class="section-title">
          <i class="fa-solid fa-id-card-clip"></i>
          Driving License Information
        </h3>
        <div class="form-grid">
          <div class="form-group">
            <label><i class="fa-solid fa-hashtag"></i> License Number</label>
            <input type="text" name="license_no" placeholder="Enter your license number" required>
          </div>

          <div class="form-group">
            <label><i class="fa-solid fa-calendar-xmark"></i> License Expiry Date</label>
            <input type="date" name="license_exp_date" id="license_exp_date" required>
          </div>

          <div class="form-group full-width">
            <label><i class="fa-solid fa-file-image"></i> Upload License Document</label>
            <input type="file" name="license_image" accept="image/*" required>
          </div>
        </div>

        <!-- Vehicle Information -->
        <h3 class="section-title">
          <i class="fa-solid fa-car"></i>
          Vehicle Information
        </h3>
        <div class="form-grid">
          <div class="form-group">
            <label><i class="fa-solid fa-car-side"></i> Vehicle Type</label>
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
            <label><i class="fa-solid fa-hashtag"></i> Vehicle Number</label>
            <input type="text" name="vehicle_no" placeholder="e.g., ABC-1234" required>
          </div>

          <div class="form-group">
            <label><i class="fa-solid fa-users"></i> Passenger Capacity</label>
            <input type="number" name="psg_capacity" min="1" value="1" placeholder="Enter capacity" required>
          </div>

          <div class="form-group">
            <label><i class="fa-solid fa-camera"></i> Vehicle Photo</label>
            <input type="file" name="vehicle_image" accept="image/*" required>
          </div>
        </div>

        <!-- Account Security -->
        <h3 class="section-title">
          <i class="fa-solid fa-lock"></i>
          Account Security
        </h3>
        <div class="form-grid">
          <div class="form-group">
            <label><i class="fa-solid fa-key"></i> Password</label>
            <input type="password" name="password" placeholder="Create a strong password" required>
          </div>

          <div class="form-group">
            <label><i class="fa-solid fa-key"></i> Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="Re-enter your password" required>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="buttons">
          <button type="button" class="back-button" onclick="window.history.back()">
            <i class="fa-solid fa-arrow-left"></i> Back
          </button>
          <button type="submit" class="register-btn">
            <i class="fa-solid fa-user-plus"></i> Complete Registration
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
