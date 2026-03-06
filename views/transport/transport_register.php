<?php
// views/transport/transport_register.php

// Check if session already started before calling session_start()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Display any error messages
$errors = [];
$success = null;

if (isset($_SESSION['register_errors'])) {
    $errors = $_SESSION['register_errors'];
    unset($_SESSION['register_errors']);
}

if (isset($_SESSION['register_success'])) {
    $success = $_SESSION['register_success'];
    unset($_SESSION['register_success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Transport Provider Registration</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/transport_register.css">
</head>
<body>
  <!-- Navbar (Same style as Transport Provider Pages) -->
  <header class="navbar">
    <div class="branding">
      <img src="/CeylonGo/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="/CeylonGo/public/tourist/dashboard">Home</a>
      <a href="/CeylonGo/public/contact">Contact Us</a>
      <a href="/CeylonGo/public/login" class="login-btn">Login</a>
    </nav>
  </header>

  <!-- Hero Section -->
  <section class="hero-section">
    <div class="hero-content">
      <div class="hero-icon">
        <i class="fa-solid fa-car-side"></i>
      </div>
      <h1>Become a Transport Provider</h1>
      <p>Join Ceylon Go and start earning by providing transport services to tourists exploring beautiful Sri Lanka</p>
    </div>
  </section>

  <!-- Progress Indicator -->
  <div class="progress-container">
    <div class="progress-bar">
      <div class="progress-step active">
        <div class="step-number">1</div>
        <span class="step-label">Personal</span>
      </div>
      <div class="progress-step">
        <div class="step-number">2</div>
        <span class="step-label">License</span>
      </div>
      <div class="progress-step">
        <div class="step-number">3</div>
        <span class="step-label">Vehicle</span>
      </div>
      <div class="progress-step">
        <div class="step-number">4</div>
        <span class="step-label">Account</span>
      </div>
    </div>
  </div>

  <!-- Main Container -->
  <main class="main-container">
    
    <!-- Error Messages -->
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <i class="fa-solid fa-circle-exclamation"></i>
        <div class="alert-content">
          <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <div class="alert-content">
          <p><?= htmlspecialchars($success) ?></p>
        </div>
      </div>
    <?php endif; ?>

    <!-- Registration Form -->
    <div class="form-card">
      <form action="/CeylonGo/public/transporter/register" method="POST" enctype="multipart/form-data">
        
        <!-- Personal Information Section -->
        <div class="form-section">
          <div class="section-header">
            <div class="section-icon personal">
              <i class="fa-solid fa-user"></i>
            </div>
            <div>
              <div class="section-title">Personal Information</div>
              <div class="section-subtitle">Tell us about yourself</div>
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label>Full Name <span class="required">*</span></label>
              <div class="input-wrapper">
                <input type="text" name="full_name" placeholder="e.g., Kamal Perera" required>
                <i class="fa-solid fa-user"></i>
              </div>
            </div>

            <div class="form-group">
              <label>Date of Birth <span class="required">*</span></label>
              <div class="input-wrapper">
                <input type="date" name="dob" required>
                <i class="fa-solid fa-calendar"></i>
              </div>
            </div>

            <div class="form-group">
              <label>NIC Number <span class="required">*</span></label>
              <div class="input-wrapper">
                <input type="text" name="nic" placeholder="e.g., 199012345678" required>
                <i class="fa-solid fa-id-card"></i>
              </div>
            </div>

            <div class="form-group">
              <label>Contact Number <span class="required">*</span></label>
              <div class="input-wrapper">
                <input type="text" name="contact_no" placeholder="e.g., 0771234567" required>
                <i class="fa-solid fa-phone"></i>
              </div>
            </div>

            <div class="form-group full-width">
              <label>Address <span class="required">*</span></label>
              <div class="input-wrapper">
                <input type="text" name="address" placeholder="e.g., 123, Main Street, Colombo" required>
                <i class="fa-solid fa-location-dot"></i>
              </div>
            </div>

            <div class="form-group full-width">
              <label>Profile Photo <span class="required">*</span></label>
              <div class="file-upload">
                <label class="file-upload-label" for="profile_image">
                  <i class="fa-solid fa-cloud-arrow-up"></i>
                  <span>Drag and drop or <span class="browse-text">browse</span> to upload</span>
                </label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*" required onchange="showFileName(this, 'profile-file-name')">
                <div class="file-name" id="profile-file-name"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- License Information Section -->
        <div class="form-section">
          <div class="section-header">
            <div class="section-icon license">
              <i class="fa-solid fa-id-badge"></i>
            </div>
            <div>
              <div class="section-title">License Information</div>
              <div class="section-subtitle">Your driving license details</div>
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label>License Number <span class="required">*</span></label>
              <div class="input-wrapper">
                <input type="text" name="license_no" placeholder="e.g., B1234567" required>
                <i class="fa-solid fa-id-badge"></i>
              </div>
            </div>

            <div class="form-group">
              <label>License Expiry Date <span class="required">*</span></label>
              <div class="input-wrapper">
                <input type="date" name="license_exp_date" required>
                <i class="fa-solid fa-calendar-xmark"></i>
              </div>
            </div>

            <div class="form-group full-width">
              <label>License Image <span class="required">*</span></label>
              <div class="file-upload">
                <label class="file-upload-label" for="license_image">
                  <i class="fa-solid fa-cloud-arrow-up"></i>
                  <span>Drag and drop or <span class="browse-text">browse</span> to upload</span>
                </label>
                <input type="file" id="license_image" name="license_image" accept="image/*" required onchange="showFileName(this, 'license-file-name')">
                <div class="file-name" id="license-file-name"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Vehicle Information Section -->
        <div class="form-section">
          <div class="section-header">
            <div class="section-icon vehicle">
              <i class="fa-solid fa-car"></i>
            </div>
            <div>
              <div class="section-title">Vehicle Information</div>
              <div class="section-subtitle">Details about your vehicle</div>
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label>Vehicle Number <span class="required">*</span></label>
              <div class="input-wrapper">
                <input type="text" name="vehicle_no" placeholder="e.g., CAA-1234" required>
                <i class="fa-solid fa-car-side"></i>
              </div>
            </div>

            <div class="form-group">
              <label>Passenger Capacity <span class="required">*</span></label>
              <div class="input-wrapper">
                <input type="number" name="psg_capacity" placeholder="e.g., 4" min="1" required>
                <i class="fa-solid fa-users"></i>
              </div>
            </div>

            <div class="form-group full-width">
              <label>Vehicle Type <span class="required">*</span></label>
              <div class="input-wrapper">
                <select name="vehicle_type" required>
                  <option value="">Select Vehicle Type</option>
                  <option value="1">TUK</option>
                  <option value="2">Car</option>
                  <option value="3">Minivan</option>
                  <option value="4">Minivan AC</option>
                  <option value="5">Bus</option>
                  <option value="6">Bus AC</option>
                </select>
                <i class="fa-solid fa-car"></i>
              </div>
            </div>

            <div class="form-group full-width">
              <label>Vehicle Photo <span class="required">*</span></label>
              <div class="file-upload">
                <label class="file-upload-label" for="vehicle_image">
                  <i class="fa-solid fa-cloud-arrow-up"></i>
                  <span>Drag and drop or <span class="browse-text">browse</span> to upload</span>
                </label>
                <input type="file" id="vehicle_image" name="vehicle_image" accept="image/*" required onchange="showFileName(this, 'vehicle-file-name')">
                <div class="file-name" id="vehicle-file-name"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Account Information Section -->
        <div class="form-section">
          <div class="section-header">
            <div class="section-icon account">
              <i class="fa-solid fa-lock"></i>
            </div>
            <div>
              <div class="section-title">Account Information</div>
              <div class="section-subtitle">Create your login credentials</div>
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group full-width">
              <label>Email Address <span class="required">*</span></label>
              <div class="input-wrapper">
                <input type="email" name="email" placeholder="e.g., kamal@example.com" required>
                <i class="fa-solid fa-envelope"></i>
              </div>
            </div>

            <div class="form-group">
              <label>Password <span class="required">*</span></label>
              <div class="input-wrapper">
                <input type="password" name="password" id="password" placeholder="e.g., ********" required minlength="8">
                <i class="fa-solid fa-lock"></i>
              </div>
              <div class="password-strength">
                <div class="strength-bar"></div>
                <div class="strength-bar"></div>
                <div class="strength-bar"></div>
                <div class="strength-bar"></div>
              </div>
              <div class="password-hint">Minimum 8 characters</div>
            </div>

            <div class="form-group">
              <label>Confirm Password <span class="required">*</span></label>
              <div class="input-wrapper">
                <input type="password" name="confirm_password" placeholder="e.g., ********" required minlength="8">
                <i class="fa-solid fa-lock"></i>
              </div>
            </div>

            <div class="form-group full-width">
              <div class="terms-group">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">
                  I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>. 
                  I confirm that all information provided is accurate and I am authorized to provide transport services.
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
          <button type="button" class="btn btn-back" onclick="history.back()">
            <i class="fa-solid fa-arrow-left"></i> Back
          </button>
          <button type="submit" class="btn btn-register">
            <i class="fa-solid fa-user-plus"></i> Create Account
          </button>
        </div>
      </form>
    </div>

    <!-- Benefits Section -->
    <div class="benefits-section">
      <h3 class="benefits-title">Why Join Ceylon Go?</h3>
      <div class="benefits-grid">
        <div class="benefit-card">
          <div class="benefit-icon">
            <i class="fa-solid fa-money-bill-wave"></i>
          </div>
          <h4>Earn More</h4>
          <p>Get access to thousands of tourists looking for reliable transport services</p>
        </div>
        <div class="benefit-card">
          <div class="benefit-icon">
            <i class="fa-solid fa-calendar-check"></i>
          </div>
          <h4>Flexible Schedule</h4>
          <p>Work on your own schedule and accept bookings that suit your availability</p>
        </div>
        <div class="benefit-card">
          <div class="benefit-icon">
            <i class="fa-solid fa-shield-halved"></i>
          </div>
          <h4>Secure Payments</h4>
          <p>Get paid directly to your bank account with our secure payment system</p>
        </div>
      </div>
    </div>

  </main>

  <!-- Footer (Same style as Transport Provider Pages) -->
  <footer>
    <ul>
      <li><a href="#">About Us</a></li>
      <li><a href="#">Contact Us</a></li>
      <li><a href="#">Privacy Policy</a></li>
      <li><a href="#">Terms of Service</a></li>
    </ul>
  </footer>

  <script>
    // Show file name when file is selected
    function showFileName(input, displayId) {
      const display = document.getElementById(displayId);
      if (input.files && input.files[0]) {
        display.textContent = '✓ ' + input.files[0].name;
      } else {
        display.textContent = '';
      }
    }

    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const strengthBars = document.querySelectorAll('.strength-bar');

    passwordInput.addEventListener('input', function() {
      const password = this.value;
      let strength = 0;

      if (password.length >= 8) strength++;
      if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
      if (password.match(/[0-9]/)) strength++;
      if (password.match(/[^a-zA-Z0-9]/)) strength++;

      strengthBars.forEach((bar, index) => {
        if (index < strength) {
          const colors = ['#ff5252', '#ff9800', '#ffc107', '#4CAF50'];
          bar.style.background = colors[strength - 1];
        } else {
          bar.style.background = '#e0e0e0';
        }
      });
    });

    // Update progress steps based on form section focus
    const formSections = document.querySelectorAll('.form-section');
    const progressSteps = document.querySelectorAll('.progress-step');

    function updateProgress(index) {
      progressSteps.forEach((step, i) => {
        step.classList.remove('active', 'completed');
        if (i < index) {
          step.classList.add('completed');
        } else if (i === index) {
          step.classList.add('active');
        }
      });
    }

    formSections.forEach((section, index) => {
      section.addEventListener('focusin', () => updateProgress(index));
    });

    // Passenger capacity limits per vehicle type
    const capacityLimits = { '1': 3, '2': 4, '3': 7, '4': 7, '5': 20, '6': 20 };
    const capacityNames = { '1': 'TUK', '2': 'Car', '3': 'Minivan', '4': 'Minivan AC', '5': 'Bus', '6': 'Bus AC' };
    const vehicleTypeSelect = document.querySelector('select[name="vehicle_type"]');
    const psgCapacityInput = document.querySelector('input[name="psg_capacity"]');

    // Create error message element
    const capacityError = document.createElement('div');
    capacityError.style.cssText = 'color: #c62828; font-size: 13px; margin-top: 5px; display: none;';
    psgCapacityInput.closest('.form-group').appendChild(capacityError);

    function validateCapacity() {
      const type = vehicleTypeSelect.value;
      const capacity = parseInt(psgCapacityInput.value) || 0;
      const maxCapacity = capacityLimits[type];

      if (type && maxCapacity && capacity > maxCapacity) {
        capacityError.textContent = `Maximum passenger capacity for ${capacityNames[type]} is ${maxCapacity}.`;
        capacityError.style.display = 'block';
        psgCapacityInput.style.borderColor = '#c62828';
        return false;
      } else {
        capacityError.style.display = 'none';
        psgCapacityInput.style.borderColor = '';
        return true;
      }
    }

    vehicleTypeSelect.addEventListener('change', function() {
      const type = this.value;
      if (capacityLimits[type]) {
        psgCapacityInput.setAttribute('max', capacityLimits[type]);
        psgCapacityInput.setAttribute('placeholder', `Max: ${capacityLimits[type]} passengers`);
      }
      validateCapacity();
    });

    psgCapacityInput.addEventListener('input', validateCapacity);

    document.querySelector('form').addEventListener('submit', function(e) {
      if (!validateCapacity()) {
        e.preventDefault();
        psgCapacityInput.focus();
      }
    });
  </script>
</body>
</html>
