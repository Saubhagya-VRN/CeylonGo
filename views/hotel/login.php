<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ceylon Go | Hotel Portal – Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,600;14..32,700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../public/css/hotel/style.css" />
</head>
<body>
  <header class="app-header">
    <div class="app-brand">
      <div class="logo">CG</div>
      <div class="brand-text">Ceylon Go | Hotel Portal</div>
    </div>
    <nav class="app-nav">
      <a href="login.php" class="active">Login</a>
      <a href="register.php">Register</a>
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
      <h1 class="auth-title">Welcome back</h1>
      <p class="auth-subtitle">Login to manage your hotel</p>

      <form id="hotelLoginForm" novalidate>
        <div class="form-group">
          <label for="loginEmail">Email</label>
          <input type="email" id="loginEmail" name="loginEmail" class="form-control" required />
        </div>

        <div class="form-group">
          <label for="loginPassword">Password</label>
          <input type="password" id="loginPassword" name="loginPassword" class="form-control" required />
        </div>

        <div class="form-group-inline">
          <label class="checkbox">
            <input type="checkbox" id="rememberMe" />
            <span>Remember me</span>
          </label>
          <a href="register.php" class="muted">Create an account</a>
        </div>

        <div id="loginError" class="error-banner" style="display:none;">Invalid email or password</div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>
    </section>
  </main>

  <script src="../../public/js/hotel.js"></script>
  </body>
  </html>


