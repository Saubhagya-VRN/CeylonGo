<?php
// views/guide/guide_register.php

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
  <title>Ceylon Go - Tour Guide Registration</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/guide_register.css">
</head>
<body>
  <!-- Navbar -->
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
        <i class="fa-solid fa-person-hiking"></i>
      </div>
      <h1>Become a Tour Guide</h1>
      <p>Join Ceylon Go and share your knowledge of Sri Lanka's rich heritage with tourists from around the world</p>
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
        <span class="step-label">Expertise</span>
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
      <form action="/CeylonGo/public/guide/register" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="user_type" value="guide">
        
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
              <label>First Name <span class="required">*</span></label>
              <div class="input-wrapper">
                <input type="text" name="first_name" placeholder="e.g., Kamal" required>
                <i class="fa-solid fa-user"></i>
              </div>
            </div>

            <div class="form-group">
              <label>Last Name <span class="required">*</span></label>
              <div class="input-wrapper">
                <input type="text" name="last_name" placeholder="e.g., Perera" required>
                <i class="fa-solid fa-user"></i>
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
                <input type="text" name="contact_number" placeholder="e.g., 0771234567" required>
                <i class="fa-solid fa-phone"></i>
              </div>
            </div>

            <div class="form-group full-width">
              <label>Profile Photo <span class="required">*</span></label>
              <div class="file-upload">
                <label class="file-upload-label" for="profile_photo">
                  <i class="fa-solid fa-cloud-arrow-up"></i>
                  <span>Drag and drop or <span class="browse-text">browse</span> to upload</span>
                </label>
                <input type="file" id="profile_photo" name="profile_photo" accept="image/*" required onchange="showFileName(this, 'profile-file-name')">
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
              <div class="section-subtitle">Your tour guide license details</div>
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group full-width">
              <label>Tour Guide License Number <span class="required">*</span></label>
              <div class="input-wrapper">
                <input type="text" name="license_number" placeholder="e.g., TG2024-1234" required>
                <i class="fa-solid fa-id-badge"></i>
              </div>
            </div>

            <div class="form-group full-width">
              <label>License Document <span class="required">*</span></label>
              <div class="file-upload">
                <label class="file-upload-label" for="license_file">
                  <i class="fa-solid fa-cloud-arrow-up"></i>
                  <span>Drag and drop or <span class="browse-text">browse</span> to upload</span>
                </label>
                <input type="file" id="license_file" name="license_file" accept="image/*,.pdf" required onchange="showFileName(this, 'license-file-name')">
                <div class="file-name" id="license-file-name"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Expertise Section -->
        <div class="form-section">
          <div class="section-header">
            <div class="section-icon expertise">
              <i class="fa-solid fa-compass"></i>
            </div>
            <div>
              <div class="section-title">Expertise & Skills</div>
              <div class="section-subtitle">Tell us about your guiding expertise</div>
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group full-width">
              <label>Specialization <span class="required">*</span></label>
              <div class="specialization-grid">
                <div class="specialization-option">
                  <input type="radio" id="cultural" name="specialization" value="Cultural Heritage" required>
                  <label for="cultural">
                    <i class="fa-solid fa-landmark"></i>
                    Cultural Heritage
                  </label>
                </div>
                <div class="specialization-option">
                  <input type="radio" id="historical" name="specialization" value="Historical Sites">
                  <label for="historical">
                    <i class="fa-solid fa-monument"></i>
                    Historical Sites
                  </label>
                </div>
                <div class="specialization-option">
                  <input type="radio" id="religious" name="specialization" value="Religious Sites">
                  <label for="religious">
                    <i class="fa-solid fa-place-of-worship"></i>
                    Religious Sites
                  </label>
                </div>
                <div class="specialization-option">
                  <input type="radio" id="nature" name="specialization" value="Nature & Wildlife">
                  <label for="nature">
                    <i class="fa-solid fa-leaf"></i>
                    Nature & Wildlife
                  </label>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label>Languages Spoken <span class="required">*</span></label>
              <div class="input-wrapper">
                <input type="text" name="languages" placeholder="e.g., English, Sinhala, Tamil" required>
                <i class="fa-solid fa-language"></i>
              </div>
            </div>

            <div class="form-group">
              <label>Years of Experience</label>
              <div class="input-wrapper">
                <input type="number" name="experience" placeholder="e.g., 5" min="0">
                <i class="fa-solid fa-briefcase"></i>
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
                <input type="password" name="password" id="password" placeholder="e.g., ********" required minlength="8"
                       pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                       title="Password must contain at least 8 characters, including uppercase, lowercase, number, and special character">
                <i class="fa-solid fa-lock"></i>
              </div>
              <div class="password-strength">
                <div class="strength-bar"></div>
                <div class="strength-bar"></div>
                <div class="strength-bar"></div>
                <div class="strength-bar"></div>
              </div>
              <div class="password-hint">Must include uppercase, lowercase, number & special character</div>
            </div>

            <div class="form-group">
              <label>Confirm Password <span class="required">*</span></label>
              <div class="input-wrapper">
                <input type="password" name="confirm_password" id="confirm-password" placeholder="e.g., ********" required minlength="8">
                <i class="fa-solid fa-lock"></i>
              </div>
            </div>

            <div class="form-group full-width">
              <div class="terms-group">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">
                  I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>. 
                  I confirm that all information provided is accurate and I am authorized to provide tour guide services.
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
            <i class="fa-solid fa-globe"></i>
          </div>
          <h4>Global Reach</h4>
          <p>Connect with tourists from around the world seeking authentic Sri Lankan experiences</p>
        </div>
        <div class="benefit-card">
          <div class="benefit-icon">
            <i class="fa-solid fa-calendar-check"></i>
          </div>
          <h4>Flexible Schedule</h4>
          <p>Work on your own schedule and accept tours that suit your availability</p>
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

  <!-- Footer -->
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

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm-password').value;
      
      // Check if passwords match
      if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
      }
      
      // Password strength validation
      const hasUpperCase = /[A-Z]/.test(password);
      const hasLowerCase = /[a-z]/.test(password);
      const hasNumber = /\d/.test(password);
      const hasSpecialChar = /[@$!%*?&]/.test(password);
      const hasMinLength = password.length >= 8;
      
      if (!hasMinLength || !hasUpperCase || !hasLowerCase || !hasNumber || !hasSpecialChar) {
        e.preventDefault();
        alert('Password must contain at least 8 characters, including uppercase, lowercase, number, and special character');
        return false;
      }
    });
  </script>
</body>
</html>
