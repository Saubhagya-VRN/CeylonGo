<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ceylon Go | Hotel Portal – Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/hotel/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <header class="app-header">
    <div class="app-brand">
      <div class="logo">CG</div>
      <div class="brand-text">Ceylon Go | Hotel Portal</div>
    </div>
    <nav class="app-nav">
      <a href="login.php">Login</a>
      <a href="register.php" class="active">Register</a>
      <a href="dashboard.php">Dashboard</a>
      <a href="profile.php">Profile</a>
      <a href="availability.php">Availability</a>
      <a href="bookings.php">Bookings</a>
      <a href="payments.php">Payments</a>
      <a href="reviews.php">Reviews</a>
      <a href="inquiries.php">Inquiries</a>
      <a href="report_issue.php">Report Issue</a>
      <a href="notifications.php">Notifications</a>
    </nav>
  </header>

  <main class="auth-wrapper">
    <section class="auth-card">
      <h1 class="auth-title">Register your Hotel</h1>
      <p class="auth-subtitle">Create your hotel account to get started</p>

      <form id="hotelRegisterForm" novalidate>
        <div class="grid-2-col">
          <div class="form-group">
            <label for="hotelName">Hotel Name</label>
            <input type="text" id="hotelName" name="hotelName" class="form-control" required />
            <small class="error-text" id="err-hotelName"></small>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required />
            <small class="error-text" id="err-email"></small>
          </div>
        </div>

        <div class="grid-2-col">
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required 
                   pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                   placeholder="8+ chars, uppercase, lowercase, number, special char"
                   title="Password must contain at least 8 characters, including uppercase, lowercase, number, and special character" />
            <small class="error-text" id="err-password"></small>
            <small id="password-error" style="color: red; display: none;"></small>
          </div>

          <div class="form-group">
            <label for="confirmPassword">Confirm Password</label>
            <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required />
            <small class="error-text" id="err-confirmPassword"></small>
          </div>
        </div>

        <div class="grid-2-col">
          <div class="form-group">
            <label for="contactNumber">Contact Number</label>
            <input type="tel" id="contactNumber" name="contactNumber" class="form-control" required />
            <small class="error-text" id="err-contactNumber"></small>
          </div>

          <div class="form-group">
            <label for="address">Address</label>
            <input type="text" id="address" name="address" class="form-control" required />
            <small class="error-text" id="err-address"></small>
          </div>
        </div>

        <div class="grid-2-col">
          <div class="form-group">
            <label for="roomTypes">Room Types</label>
            <select id="roomTypes" name="roomTypes" class="form-control" required>
              <option value="">Select room type</option>
              <option>Single</option>
              <option>Double</option>
              <option>Suite</option>
              <option>Deluxe</option>
            </select>
            <small class="error-text" id="err-roomTypes"></small>
          </div>

          <div class="form-group">
            <label for="pricePerNight">Price per Night (USD)</label>
            <input type="number" id="pricePerNight" name="pricePerNight" class="form-control" min="0" step="0.01" required />
            <small class="error-text" id="err-pricePerNight"></small>
          </div>
        </div>

        <div class="form-group">
          <label for="description">Description</label>
          <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
          <small class="error-text" id="err-description"></small>
        </div>

        <div class="form-group">
          <label for="hotelImage">Upload Hotel Image</label>
          <input type="file" id="hotelImage" name="hotelImage" class="form-control" accept="image/*" />
          <div class="image-preview" id="registerImagePreview">No image selected</div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Create Account</button>
        <p class="muted mt-12">Already have an account? <a href="login.php">Login</a></p>
        <div id="registerSuccess" class="success-banner" style="display:none;">Registration successful! Redirecting to login...</div>
      </form>
    </section>
  </main>

  <script src="assets/js/hotel.js"></script>
  <script>
    // Password strength validation for registration form
    document.getElementById('hotelRegisterForm').addEventListener('submit', function(e) {
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


