<?php
// header.php (inside views/hotel/includes)
// Check if user is logged in
$is_hotel_logged_in = isset($_SESSION['hotel_id']) && $_SESSION['user_type'] === 'hotel';
?>
<header class="navbar">
  <div class="branding">
      <img src="../../public/images/logo.png" alt="Ceylon Go Logo" class="logo-img">
      <div class="logo-text">Ceylon Go</div>
  </div>
  <nav class="nav-links">
    <a href="dashboard.php">Home</a>
    <a href="profile.php">Profile</a>
    <a href="availability.php">Availability</a>
    <a href="bookings.php">Bookings</a>
    <a href="payments.php">Payments</a>
    <a href="reviews.php">Reviews</a>
    <a href="inquiries.php">Inquiries</a>
    <a href="report_issue.php">Report Issue</a>
    <a href="notifications.php">Notifications</a>
    
    <?php if ($is_hotel_logged_in): ?>
      <!-- Logged in hotel - show logout -->
      <a href="tourist_dashboard.php" class="btn-login">Logout</a>
    <?php else: ?>
      <!-- Guest hotel - show register and login -->
      <a href="hotel_register.php" class="btn-register">Register</a>
      <a href="login.php" class="btn-login">Login</a>
    <?php endif; ?>
  </nav>
</header>