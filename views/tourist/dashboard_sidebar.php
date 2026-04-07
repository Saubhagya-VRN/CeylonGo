<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['user_id']) && (isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '') === 'tourist';
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Tourist';
$tourist_data = isset($tourist_data) ? $tourist_data : null;
$user_email = '';
if (is_array($tourist_data) && isset($tourist_data['email'])) {
    $user_email = $tourist_data['email'];
} elseif (isset($_SESSION['user_email'])) {
    $user_email = $_SESSION['user_email'];
}
$avatar_initial = $user_name ? strtoupper(substr(trim($user_name), 0, 1)) : 'T';

$stats = isset($dashboard_stats) && is_array($dashboard_stats) ? $dashboard_stats : array();
$stat_bookings = isset($stats['total_bookings']) ? (int) $stats['total_bookings'] : 0;
$stat_upcoming = isset($stats['upcoming_trips']) ? (int) $stats['upcoming_trips'] : 0;
$stat_spent = isset($stats['total_spent_lkr']) ? (float) $stats['total_spent_lkr'] : 0.0;
$stat_completed = isset($stats['completed_trips']) ? (int) $stats['completed_trips'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Ceylon Go</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/common.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/navbar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/trip_layout.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/sidebar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/dashboard_sidebar.css">
</head>
<body class="trip-page-body dash-side-body">
  <?php include __DIR__ . '/header.php'; ?>

  <div class="sidebar-overlay trip-overlay" id="tripSidebarOverlay"></div>

  <div class="trip-page-wrapper">
    <aside class="trip-sidebar" id="tripSidebar">
      <div class="trip-sidebar-nav">
        <ul>
          <li class="active"><a href="/CeylonGo/public/tourist/dashboard-side"><i class="fa-solid fa-table-columns"></i> <span class="sidebar-link-text">Dashboard <span class="sidebar-sub">Overview & Stats</span></span></a></li>
          <li><a href="/CeylonGo/public/tourist/customize-trip"><i class="fa-solid fa-wand-magic-sparkles"></i> <span class="sidebar-link-text">Customise Your Trip <span class="sidebar-sub">Plan Custom Trips</span></span></a></li>
          <li><a href="/CeylonGo/public/tourist/customize-trip?step=11"><i class="fa-solid fa-clipboard-list"></i> <span class="sidebar-link-text">Status of Bookings <span class="sidebar-sub">Trip review &amp; submit</span></span></a></li>
          <li><a href="/CeylonGo/public/tourist/customize-trip?step=10"><i class="fa-solid fa-wallet"></i> <span class="sidebar-link-text">Budget Overview <span class="sidebar-sub">Costs &amp; itinerary</span></span></a></li>
          <li><a href="/CeylonGo/public/tourist/customize-trip?step=14"><i class="fa-solid fa-clipboard-check"></i> <span class="sidebar-link-text">Trip Overview <span class="sidebar-sub">Final confirmation</span></span></a></li>
          <li><a href="/CeylonGo/public/tourist/my-bookings?view=custom"><i class="fa-regular fa-calendar-check"></i> <span class="sidebar-link-text">Bookings <span class="sidebar-sub">Customised trips</span></span></a></li>
          <li><a href="/CeylonGo/public/tourist/payment"><i class="fa-solid fa-credit-card"></i> <span class="sidebar-link-text">Payments <span class="sidebar-sub">Invoices & Wallet</span></span></a></li>
          <li><a href="/CeylonGo/public/tourist/profile"><i class="fa-regular fa-user"></i> <span class="sidebar-link-text">Profile <span class="sidebar-sub">Account Settings</span></span></a></li>
        </ul>
      </div>
      <div class="trip-sidebar-footer">
        <div class="trip-sidebar-user">
          <div class="trip-sidebar-user-avatar"><?php echo htmlspecialchars($avatar_initial); ?></div>
          <div class="trip-sidebar-user-info">
            <div class="trip-sidebar-user-name"><?php echo htmlspecialchars($user_name ? $user_name : 'Tourist'); ?></div>
            <div class="trip-sidebar-user-email"><?php echo htmlspecialchars($user_email ? substr($user_email, 0, 20) . (strlen($user_email) > 20 ? '...' : '') : ''); ?></div>
          </div>
        </div>
        <a href="/CeylonGo/public/logout" class="trip-sidebar-signout"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</a>
      </div>
    </aside>

    <main class="trip-main-content dash-side-main">
      <button class="hamburger-btn trip-hamburger" id="tripHamburgerBtn" aria-label="Toggle menu"><span></span><span></span><span></span></button>

      <section class="dash-welcome">
        <div class="dash-welcome-inner">
          <h1 class="dash-welcome-title">Welcome back, <?php echo htmlspecialchars($user_name); ?>!</h1>
          <p class="dash-welcome-sub">Ready for your next adventure? Let’s explore Sri Lanka together!</p>
          <div class="dash-welcome-meta">
            <span class="dash-welcome-chip"><i class="fa-regular fa-clock"></i> <?php echo (int) $stat_upcoming; ?> upcoming trips</span>
            <span class="dash-welcome-chip"><i class="fa-solid fa-coins"></i> LKR <?php echo htmlspecialchars(number_format($stat_spent, 0)); ?> spent</span>
            <span class="dash-welcome-chip"><i class="fa-regular fa-star"></i> <?php echo (int) $stat_completed; ?> trips completed</span>
          </div>
        </div>
      </section>

      <section class="dash-stats" aria-label="Dashboard stats">
        <div class="dash-stats-grid">
          <div class="dash-stat-card">
            <div class="dash-stat-icon dash-stat-icon--blue"><i class="fa-solid fa-paper-plane"></i></div>
            <div class="dash-stat-value"><?php echo (int) $stat_bookings; ?></div>
            <div class="dash-stat-label">Total Bookings</div>
            <div class="dash-stat-sub"><?php echo (int) $stat_completed; ?> completed</div>
          </div>
          <div class="dash-stat-card">
            <div class="dash-stat-icon dash-stat-icon--green"><i class="fa-regular fa-calendar"></i></div>
            <div class="dash-stat-value"><?php echo (int) $stat_upcoming; ?></div>
            <div class="dash-stat-label">Upcoming Trips</div>
            <div class="dash-stat-sub">Next adventure awaits</div>
          </div>
          <div class="dash-stat-card">
            <div class="dash-stat-icon dash-stat-icon--purple"><i class="fa-solid fa-chart-line"></i></div>
            <div class="dash-stat-value">LKR <?php echo htmlspecialchars(number_format($stat_spent, 0)); ?></div>
            <div class="dash-stat-label">Total Spent</div>
            <div class="dash-stat-sub">On amazing trips</div>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
    var hamburger = document.getElementById('tripHamburgerBtn');
    var sidebar = document.getElementById('tripSidebar');
    var overlay = document.getElementById('tripSidebarOverlay');
    function toggleSidebar() {
      if (hamburger) hamburger.classList.toggle('active');
      if (sidebar) sidebar.classList.toggle('active');
      if (overlay) overlay.classList.toggle('active');
    }
    if (hamburger) hamburger.addEventListener('click', toggleSidebar);
    if (overlay) overlay.addEventListener('click', function () {
      if (sidebar) sidebar.classList.remove('active');
      if (overlay) overlay.classList.remove('active');
      if (hamburger) hamburger.classList.remove('active');
    });
  });
  </script>
</body>
</html>
