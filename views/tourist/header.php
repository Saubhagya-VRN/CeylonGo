<?php
// header.php (inside views/tourist)
// Check if user is logged in
$is_user_logged_in = isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'tourist';
?>
<header class="navbar">
  <div class="branding">
      <img src="../../public/images/logo.png" alt="Ceylon Go Logo" class="logo-img">
      <div class="logo-text">Ceylon Go</div>
  </div>
  <nav class="nav-links">
    <a href="/CeylonGo/public/tourist/dashboard">Home</a>
    <a href="/CeylonGo/public/tourist/recommended-packages">Packages</a>
    <a href="/CeylonGo/public/tourist/dashboard#customize">Customize Trip</a>
    <a href="/CeylonGo/public/contact">Contact Us</a>
    
    <?php if ($is_user_logged_in): ?>
      <!-- Logged in user - show logout -->
      <a href="/CeylonGo/public/logout" class="btn-login">Logout</a>
    <?php else: ?>
      <!-- Guest user - show register and login -->
      <a href="/CeylonGo/public/register" class="btn-register">Register</a>
      <a href="/CeylonGo/public/login" class="btn-login">Login</a>
    <?php endif; ?>
  </nav>
</header>
