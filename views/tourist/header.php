<?php
// header.php (inside views/tourist)
// Check if user is logged in
$is_user_logged_in = isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'tourist';
require_once dirname(__DIR__) . '/partials/app_notify_script.php';
?>
<header class="navbar">
  <div class="branding">
      <a href="/CeylonGo/public/tourist/dashboard" class="branding-link">
        <img src="/CeylonGo/public/images/logo.png" alt="Ceylon Go Logo" class="logo-img">
        <span class="logo-text">Ceylon Go</span>
      </a>
  </div>
  <nav class="nav-links">
    <a href="/CeylonGo/public/tourist/dashboard">Home</a>
    <a href="/CeylonGo/public/tourist/packages">Packages</a>
    <?php if ($is_user_logged_in): ?>
      <a href="/CeylonGo/public/tourist/customize-trip">Customize Trip</a>
    <?php else: ?>
      <a href="/CeylonGo/public/tourist/dashboard?openLogin=1">Customize Trip</a>
    <?php endif; ?>
    <?php if ($is_user_logged_in): ?>
      <a href="/CeylonGo/public/tourist/my-bookings">My Bookings</a>
      <a href="/CeylonGo/public/tourist/profile?full=1">My profile</a>
      <a href="/CeylonGo/public/logout" class="btn-login">Logout</a>
    <?php else: ?>
      <!-- Guest user - show register and login -->
      <a href="/CeylonGo/public/register" class="btn-register">Register</a>
      <a href="/CeylonGo/public/login" class="btn-login">Login</a>
    <?php endif; ?>
  </nav>
</header>
