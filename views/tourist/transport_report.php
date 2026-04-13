<?php
$requests = $requests ?? [];
$user_name = $user_name ?? '';
$tourist_data = $tourist_data ?? null;
$user_email = ($tourist_data['email'] ?? $_SESSION['user_email'] ?? '');
$avatar_initial = $user_name ? strtoupper(substr(trim($user_name), 0, 1)) : 'T';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transport Requests - Ceylon Go</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/common.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/tourist/trip_layout.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/tourist/sidebar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/tourist/tourist_dashboard.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/tourist/footer.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/tourist/transport_report.css">
</head>
<body class="trip-page-body">
  <header class="trip-navbar">
    <div class="branding">
      <button class="hamburger-btn" id="tripHamburgerBtn" aria-label="Toggle menu"><span></span><span></span><span></span></button>
      <a href="/CeylonGo/public/tourist/dashboard-side" class="trip-branding-link">
        <img src="/CeylonGo/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
        <span class="logo-text">Ceylon Go</span>
      </a>
    </div>
    <nav class="nav-links">
      <a href="/CeylonGo/public/tourist/dashboard-side">Dashboard</a>
      <a href="/CeylonGo/public/tourist/recommended-packages">Packages</a>
      <a href="/CeylonGo/public/tourist/customize-trip">Customise Trip</a>
      <a href="/CeylonGo/public/tourist/profile">Profile</a>
      <a href="/CeylonGo/public/logout">Logout</a>
    </nav>
  </header>

  <div class="sidebar-overlay trip-overlay" id="tripSidebarOverlay"></div>

  <div class="trip-page-wrapper">
    <aside class="trip-sidebar" id="tripSidebar">
      <div class="trip-sidebar-nav">
        <ul>
          <li><a href="/CeylonGo/public/tourist/dashboard-side"><i class="fa-solid fa-table-columns"></i> <span class="sidebar-link-text">Dashboard <span class="sidebar-sub">Overview & Stats</span></span></a></li>
          <li><a href="/CeylonGo/public/tourist/customize-trip"><i class="fa-solid fa-wand-magic-sparkles"></i> <span class="sidebar-link-text">Customise Your Trip <span class="sidebar-sub">Plan Custom Trips</span></span></a></li>
          <li id="tripSidebarNavStatusBookings"><a href="/CeylonGo/public/tourist/booking-status"><i class="fa-solid fa-clipboard-list"></i> <span class="sidebar-link-text">Status of Bookings <span class="sidebar-sub">Trip review &amp; submit</span></span></a></li>
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
            <div class="trip-sidebar-user-name"><?php echo htmlspecialchars($user_name ?: 'Tourist'); ?></div>
            <div class="trip-sidebar-user-email"><?php echo htmlspecialchars($user_email ? substr($user_email, 0, 20) . (strlen($user_email) > 20 ? '...' : '') : ''); ?></div>
          </div>
        </div>
        <a href="/CeylonGo/public/logout" class="trip-sidebar-signout"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</a>
      </div>
    </aside>

    <main class="trip-main-content">
  <section class="transport-report-hero">
    <div class="transport-report-hero-bar"></div>
    <h1>Transport Requests</h1>
    <p>Review, edit, or delete your submitted transport requests.</p>
  </section>

  <section style="padding: 40px 20px;">
    <div style="max-width: 1100px; margin: 0 auto;">
      <div style="overflow-x:auto; background:#ffffff; border-radius: 12px; box-shadow: 0 8px 25px rgba(74,124,89,0.15); border: 1px solid rgba(74,124,89,0.1);">
        <table style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr style="background: linear-gradient(135deg, #4a7c59, #5a8c69); color: #fff;">
              <th style="text-align:left; padding: 14px 16px;">ID</th>
              <th style="text-align:left; padding: 14px 16px;">Customer</th>
              <th style="text-align:left; padding: 14px 16px;">Contact</th>
              <th style="text-align:left; padding: 14px 16px;">Vehicle</th>
              <th style="text-align:left; padding: 14px 16px;">Date</th>
              <th style="text-align:left; padding: 14px 16px;">Time</th>
              <th style="text-align:left; padding: 14px 16px;">Pickup</th>
              <th style="text-align:left; padding: 14px 16px;">Dropoff</th>
              <th style="text-align:left; padding: 14px 16px;">People</th>
              <th style="text-align:left; padding: 14px 16px;">Distance</th>
              <th style="text-align:left; padding: 14px 16px;">Est. Fare</th>
              <th style="text-align:left; padding: 14px 16px;">Status</th>
              <th style="text-align:left; padding: 14px 16px;">Notes</th>
              <th style="text-align:left; padding: 14px 16px;">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($requests as $row): ?>
            <?php
              $estFare = isset($row['estimatedFare']) && $row['estimatedFare'] !== null && $row['estimatedFare'] !== '' 
                ? 'LKR ' . number_format((float) $row['estimatedFare'], 2) 
                : '-';
              $dist = isset($row['distance']) && $row['distance'] !== null && $row['distance'] !== '' 
                ? number_format((float) $row['distance'], 1) . ' km' 
                : '-';
            ?>
            <tr style="border-top: 1px solid #e0e8e0;">
              <td style="padding: 12px 16px;"><?php echo (int) $row['id']; ?></td>
              <td style="padding: 12px 16px;"><?php echo htmlspecialchars($row['customerName'] ?? ''); ?></td>
              <td style="padding: 12px 16px;"><?php echo htmlspecialchars($row['contactNumber'] ?? ''); ?></td>
              <td style="padding: 12px 16px;"><?php echo htmlspecialchars($row['vehicleType'] ?? ''); ?></td>
              <td style="padding: 12px 16px;"><?php echo htmlspecialchars($row['date'] ?? ''); ?></td>
              <td style="padding: 12px 16px;"><?php echo htmlspecialchars($row['pickupTime'] ?? ''); ?></td>
              <td style="padding: 12px 16px;"><?php echo htmlspecialchars($row['pickupLocation'] ?? ''); ?></td>
              <td style="padding: 12px 16px;"><?php echo htmlspecialchars($row['dropoffLocation'] ?? ''); ?></td>
              <td style="padding: 12px 16px;"><?php echo (int) ($row['numPeople'] ?? 0); ?></td>
              <td style="padding: 12px 16px;"><?php echo $dist; ?></td>
              <td style="padding: 12px 16px;"><?php echo $estFare; ?></td>
              <td style="padding: 12px 16px;"><span style="background:#f0e68c; color:#5a4a00; padding:4px 10px; border-radius:6px; font-weight:600; text-transform:uppercase;"><?php echo htmlspecialchars($row['status'] ?? ''); ?></span></td>
              <td style="padding: 12px 16px;"><?php echo htmlspecialchars($row['notes'] ?? '-'); ?></td>
              <td style="padding: 12px 16px; white-space: nowrap;">
                <a class="btn btn-black" href="/CeylonGo/public/tourist/transport-edit/<?php echo (int) $row['id']; ?>">Edit</a>
                <a class="btn" href="/CeylonGo/public/tourist/transport-delete/<?php echo (int) $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this request?')" style="background:#a33; border:2px solid #a33; color:#fff; margin-left:8px;">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div style="margin-top: 20px; display:flex; gap: 12px;">
        <a href="/CeylonGo/public/tourist/customize-trip" class="btn">Submit Another Request</a>
        <a href="/CeylonGo/public/tourist/dashboard-side" class="btn btn-black">Back to Dashboard</a>
      </div>
    </div>
  </section>

  <footer class="footer-spacer"></footer>
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
      document.body.style.overflow = sidebar && sidebar.classList.contains('active') ? 'hidden' : '';
    }
    function closeSidebar() {
      if (hamburger) hamburger.classList.remove('active');
      if (sidebar) sidebar.classList.remove('active');
      if (overlay) overlay.classList.remove('active');
      document.body.style.overflow = '';
    }
    if (hamburger) hamburger.addEventListener('click', toggleSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);
    var sidebarLinks = document.querySelectorAll('#tripSidebar ul li a');
    sidebarLinks.forEach(function (link) {
      link.addEventListener('click', function () {
        if (window.innerWidth <= 768) closeSidebar();
      });
    });
    window.addEventListener('resize', function () {
      if (window.innerWidth > 768) closeSidebar();
    });
  });
  </script>
</body>
</html>
