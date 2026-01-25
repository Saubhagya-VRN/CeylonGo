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
  
  <style>
    /* Reset & Base */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
      min-height: 100vh;
      color: #333;
      line-height: 1.6;
    }

    /* ===== NAVBAR (Same as Transport Provider Pages) ===== */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #2c5530;
      padding: 15px 25px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      position: sticky;
      top: 0;
      z-index: 1000;
      flex-wrap: wrap;
    }

    .branding {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .logo-img {
      height: 42px;
      margin-right: 8px;
      border-radius: 50%;
      border: 2px solid rgba(255, 255, 255, 0.2);
      transition: transform 0.3s ease;
    }

    .logo-img:hover {
      transform: scale(1.05);
    }

    .logo-text {
      font-size: 22px;
      font-weight: 700;
      color: #ffffff;
      letter-spacing: 0.5px;
    }

    .navbar .nav-links {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }

    .navbar .nav-links a {
      margin-left: 10px;
      text-decoration: none;
      color: rgba(255, 255, 255, 0.9);
      font-weight: 500;
      padding: 8px 16px;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .navbar .nav-links a:hover {
      background: rgba(255, 255, 255, 0.15);
      color: #ffffff;
    }

    .navbar .nav-links .login-btn {
      background: rgba(255, 255, 255, 0.2);
      border: 2px solid rgba(255, 255, 255, 0.4);
      padding: 8px 20px;
      border-radius: 25px;
      font-weight: 600;
    }

    .navbar .nav-links .login-btn:hover {
      background: rgba(255, 255, 255, 0.3);
      border-color: rgba(255, 255, 255, 0.6);
    }

    /* Hero Section */
    .hero-section {
      background: #4CAF50;
      padding: 60px 20px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .hero-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
      opacity: 0.5;
    }

    .hero-content {
      position: relative;
      z-index: 1;
      max-width: 700px;
      margin: 0 auto;
    }

    .hero-icon {
      width: 80px;
      height: 80px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 25px;
      backdrop-filter: blur(10px);
    }

    .hero-icon i {
      font-size: 36px;
      color: #fff;
    }

    .hero-section h1 {
      font-size: 42px;
      font-weight: 800;
      color: #fff;
      margin-bottom: 15px;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .hero-section p {
      font-size: 18px;
      color: rgba(255, 255, 255, 0.9);
      max-width: 500px;
      margin: 0 auto;
    }

    /* Progress Indicator */
    .progress-container {
      max-width: 600px;
      margin: -30px auto 0;
      padding: 0 20px;
      position: relative;
      z-index: 10;
    }

    .progress-bar {
      background: #fff;
      border-radius: 50px;
      padding: 20px 30px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .progress-step {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      flex: 1;
      position: relative;
    }

    .progress-step:not(:last-child)::after {
      content: '';
      position: absolute;
      top: 15px;
      left: 60%;
      width: 80%;
      height: 3px;
      background: #e0e0e0;
    }

    .progress-step.active:not(:last-child)::after,
    .progress-step.completed:not(:last-child)::after {
      background: #4CAF50;
    }

    .step-number {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: #e0e0e0;
      color: #999;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 14px;
      position: relative;
      z-index: 1;
      transition: all 0.3s;
    }

    .progress-step.active .step-number,
    .progress-step.completed .step-number {
      background: #4CAF50;
      color: #fff;
    }

    .step-label {
      font-size: 12px;
      font-weight: 600;
      color: #999;
    }

    .progress-step.active .step-label,
    .progress-step.completed .step-label {
      color: #4CAF50;
    }

    /* Main Form Container */
    .main-container {
      max-width: 900px;
      margin: 40px auto;
      padding: 0 20px 40px;
    }

    /* Alert Messages */
    .alert {
      padding: 16px 20px;
      border-radius: 12px;
      margin-bottom: 25px;
      display: flex;
      align-items: flex-start;
      gap: 12px;
    }

    .alert-danger {
      background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
      border: 1px solid #feb2b2;
      color: #c53030;
    }

    .alert-success {
      background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);
      border: 1px solid #9ae6b4;
      color: #276749;
    }

    .alert i {
      font-size: 20px;
      margin-top: 2px;
    }

    .alert-content p {
      margin: 3px 0;
      font-size: 14px;
    }

    /* Form Card */
    .form-card {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }

    .form-section {
      padding: 30px 40px;
      border-bottom: 1px solid #f0f0f0;
    }

    .form-section:last-of-type {
      border-bottom: none;
    }

    .section-header {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 25px;
    }

    .section-icon {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
    }

    .section-icon.personal {
      background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
      color: #1976d2;
    }

    .section-icon.license {
      background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
      color: #f57c00;
    }

    .section-icon.vehicle {
      background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
      color: #388e3c;
    }

    .section-icon.account {
      background: linear-gradient(135deg, #fce4ec 0%, #f8bbd9 100%);
      color: #c2185b;
    }

    .section-title {
      font-size: 20px;
      font-weight: 700;
      color: #333;
    }

    .section-subtitle {
      font-size: 14px;
      color: #888;
      margin-top: 2px;
    }

    /* Form Grid */
    .form-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
    }

    .form-group {
      position: relative;
    }

    .form-group.full-width {
      grid-column: 1 / -1;
    }

    .form-group label {
      display: block;
      font-size: 14px;
      font-weight: 600;
      color: #555;
      margin-bottom: 8px;
    }

    .form-group label .required {
      color: #e53e3e;
      margin-left: 2px;
    }

    .input-wrapper {
      position: relative;
    }

    .input-wrapper i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #aaa;
      font-size: 16px;
      transition: color 0.3s;
    }

    .form-group input,
    .form-group select {
      width: 100%;
      padding: 14px 15px 14px 45px;
      border: 2px solid #e8e8e8;
      border-radius: 12px;
      font-size: 15px;
      font-family: inherit;
      transition: all 0.3s;
      background: #fafafa;
    }

    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: #4CAF50;
      background: #fff;
      box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.1);
    }

    .form-group input:focus + i,
    .form-group select:focus + i {
      color: #4CAF50;
    }

    .form-group select {
      cursor: pointer;
      appearance: none;
    }

    /* File Upload */
    .file-upload {
      position: relative;
    }

    .file-upload-label {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 25px 20px;
      border: 2px dashed #d0d0d0;
      border-radius: 12px;
      background: #fafafa;
      cursor: pointer;
      transition: all 0.3s;
      text-align: center;
    }

    .file-upload-label:hover {
      border-color: #4CAF50;
      background: #f0fff4;
    }

    .file-upload-label i {
      font-size: 32px;
      color: #4CAF50;
      margin-bottom: 10px;
    }

    .file-upload-label span {
      font-size: 14px;
      color: #666;
    }

    .file-upload-label .browse-text {
      color: #4CAF50;
      font-weight: 600;
      text-decoration: underline;
    }

    .file-upload input[type="file"] {
      position: absolute;
      opacity: 0;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      cursor: pointer;
    }

    .file-name {
      margin-top: 8px;
      font-size: 13px;
      color: #4CAF50;
      font-weight: 500;
    }

    /* Password Strength */
    .password-strength {
      display: flex;
      gap: 4px;
      margin-top: 8px;
    }

    .strength-bar {
      flex: 1;
      height: 4px;
      background: #e0e0e0;
      border-radius: 2px;
      transition: background 0.3s;
    }

    .password-hint {
      font-size: 12px;
      color: #888;
      margin-top: 6px;
    }

    /* Terms Checkbox */
    .terms-group {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      padding: 20px;
      background: #f8f9fa;
      border-radius: 12px;
      margin-top: 10px;
    }

    .terms-group input[type="checkbox"] {
      width: 20px;
      height: 20px;
      accent-color: #4CAF50;
      margin-top: 2px;
    }

    .terms-group label {
      font-size: 14px;
      color: #555;
      line-height: 1.5;
    }

    .terms-group a {
      color: #4CAF50;
      text-decoration: none;
      font-weight: 600;
    }

    .terms-group a:hover {
      text-decoration: underline;
    }

    /* Buttons */
    .form-actions {
      display: flex;
      gap: 15px;
      padding: 30px 40px;
      background: #f8f9fa;
      border-top: 1px solid #f0f0f0;
    }

    .btn {
      flex: 1;
      padding: 16px 30px;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .btn-back {
      background: #fff;
      border: 2px solid #d0d0d0;
      color: #666;
    }

    .btn-back:hover {
      border-color: #999;
      color: #333;
      background: #f5f5f5;
    }

    .btn-register {
      background: #4CAF50;
      border: none;
      color: #fff;
      box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4);
    }

    .btn-register:hover {
      background: #45a049;
      transform: translateY(-2px);
      box-shadow: 0 6px 25px rgba(76, 175, 80, 0.5);
    }

    /* Benefits Section */
    .benefits-section {
      margin-top: 40px;
      padding: 40px;
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    }

    .benefits-title {
      text-align: center;
      font-size: 24px;
      font-weight: 700;
      color: #333;
      margin-bottom: 30px;
    }

    .benefits-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 25px;
    }

    .benefit-card {
      text-align: center;
      padding: 25px 20px;
      background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
      border-radius: 16px;
      border: 1px solid #f0f0f0;
      transition: all 0.3s;
    }

    .benefit-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .benefit-icon {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 15px;
    }

    .benefit-icon i {
      font-size: 24px;
      color: #4CAF50;
    }

    .benefit-card h4 {
      font-size: 16px;
      font-weight: 700;
      color: #333;
      margin-bottom: 8px;
    }

    .benefit-card p {
      font-size: 13px;
      color: #888;
      line-height: 1.5;
    }

    /* ===== FOOTER (Same as Transport Provider Pages) ===== */
    footer {
      background: #2c5530;
      color: white;
      padding: 20px 0;
      text-align: center;
      width: 100%;
      margin-top: auto;
    }

    footer ul {
      list-style: none;
      padding: 0;
      margin: 0 auto;
      display: flex;
      justify-content: center;
      gap: 30px;
      flex-wrap: wrap;
      max-width: 100%;
    }

    footer ul li {
      display: inline;
    }

    footer ul li a {
      color: rgba(255, 255, 255, 0.9);
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      transition: all 0.3s ease;
      padding: 8px 16px;
      border-radius: 6px;
    }

    footer ul li a:hover {
      color: #ffffff;
      background: rgba(255, 255, 255, 0.15);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .navbar {
        padding: 12px 15px;
      }

      .logo-text {
        font-size: 18px;
      }

      .logo-img {
        height: 35px;
      }

      .hero-section {
        padding: 40px 20px;
      }

      .hero-section h1 {
        font-size: 28px;
      }

      .hero-section p {
        font-size: 15px;
      }

      .progress-bar {
        padding: 15px 20px;
      }

      .step-label {
        display: none;
      }

      .form-section {
        padding: 25px 20px;
      }

      .form-grid {
        grid-template-columns: 1fr;
      }

      .benefits-grid {
        grid-template-columns: 1fr;
      }

      .form-actions {
        padding: 20px;
        flex-direction: column-reverse;
      }

      .btn {
        width: 100%;
      }

      footer {
        padding: 15px 10px;
      }

      footer ul {
        gap: 15px;
      }

      footer ul li a {
        font-size: 13px;
        padding: 6px 12px;
      }
    }

    @media (max-width: 480px) {
      .navbar {
        flex-direction: column;
        gap: 15px;
      }

      .progress-bar {
        padding: 12px 15px;
      }

      .step-number {
        width: 28px;
        height: 28px;
        font-size: 12px;
      }
    }
  </style>
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
                  <option value="1">TUK TUK (Three Wheeler)</option>
                  <option value="2">VAN</option>
                  <option value="3">CAR (Sedan)</option>
                  <option value="4">SUV</option>
                  <option value="5">MINI BUS</option>
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
  </script>
</body>
</html>
