<?php require_once 'session_init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Cancelled Booking Details</title>
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/base.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/navbar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/sidebar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/footer.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/cards.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/buttons.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/forms.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/responsive.css">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/cancelled_info.css">
</head>
<body>

  <header class="navbar">
    <div class="branding">
      <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle menu">
        <span></span><span></span><span></span>
      </button>
      <img src="/CeylonGo/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="/CeylonGo/public/transporter/dashboard">Home</a>
      <div class="profile-dropdown">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="User" class="profile-pic" onclick="toggleProfileDropdown()">
        <div class="profile-dropdown-menu" id="profileDropdown">
          <a href="/CeylonGo/public/transporter/profile"><i class="fa-regular fa-user"></i> My Profile</a>
          <a href="/CeylonGo/public/logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
      </div>
    </nav>
  </header>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="page-wrapper">
    <aside class="sidebar" id="sidebar">
      <ul>
        <li><a href="/CeylonGo/public/transporter/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="/CeylonGo/public/transporter/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/pending"><i class="fa-regular fa-clock"></i> Pending Bookings</a></li>
        <li class="active"><a href="/CeylonGo/public/transporter/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="/CeylonGo/public/transporter/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/transporter/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </aside>

    <main class="main-content">
      
      <?php
        $bookingDate = date('F d, Y', strtotime($booking['date']));
        $bookingDay = date('l', strtotime($booking['date']));
        $pickupTime = date('h:i A', strtotime($booking['pickup_time']));
      ?>

      <!-- Page Header -->
      <div class="page-header">
        <h1><i class="fa-solid fa-ban"></i> Cancelled Booking Details</h1>
        <a href="/CeylonGo/public/transporter/cancelled" class="back-btn">
          <i class="fa-solid fa-arrow-left"></i> Back to Cancelled
        </a>
      </div>

      <!-- Status Banner -->
      <div class="status-banner">
        <div class="status-info">
          <i class="fa-solid fa-circle-xmark"></i>
          <div>
            <h3>Booking Cancelled</h3>
            <p>This booking has been cancelled and is no longer active</p>
          </div>
        </div>
        <span class="booking-id">#BK-<?= htmlspecialchars($booking['id']) ?></span>
      </div>

      <?php if (!empty($booking['notes'])): ?>
      <!-- Cancellation Notes -->
      <div class="cancellation-card">
        <h4><i class="fa-solid fa-comment-dots"></i> Notes</h4>
        <p><?= htmlspecialchars($booking['notes']) ?></p>
        <?php if (!empty($booking['updated_at'])): ?>
        <div class="cancelled-date">
          <i class="fa-regular fa-calendar"></i> Updated on: <?= date('F d, Y \a\t h:i A', strtotime($booking['updated_at'])) ?>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- Customer Information -->
      <div class="info-card">
        <h3><i class="fa-solid fa-user-circle"></i> Customer Information</h3>
        <div class="details-grid">
          <div class="detail-item">
            <div class="icon-box"><i class="fa-solid fa-user"></i></div>
            <div class="detail-text">
              <label>Customer Name</label>
              <p><?= htmlspecialchars($booking['customer_name']) ?></p>
            </div>
          </div>
          <div class="detail-item">
            <div class="icon-box"><i class="fa-solid fa-phone"></i></div>
            <div class="detail-text">
              <label>Contact Number</label>
              <p><?= htmlspecialchars($booking['contact_number'] ?? 'N/A') ?></p>
            </div>
          </div>
          <?php if (!empty($booking['tourist_email'])): ?>
          <div class="detail-item">
            <div class="icon-box"><i class="fa-solid fa-envelope"></i></div>
            <div class="detail-text">
              <label>Email Address</label>
              <p><?= htmlspecialchars($booking['tourist_email']) ?></p>
            </div>
          </div>
          <?php endif; ?>
          <?php if (!empty($booking['tourist_first_name'])): ?>
          <div class="detail-item">
            <div class="icon-box"><i class="fa-solid fa-id-card"></i></div>
            <div class="detail-text">
              <label>Account Name</label>
              <p><?= htmlspecialchars($booking['tourist_first_name'] . ' ' . ($booking['tourist_last_name'] ?? '')) ?></p>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Original Trip Details -->
      <div class="info-card">
        <h3><i class="fa-solid fa-route"></i> Original Trip Details</h3>
      </div>

      <div class="trip-grid">
        <div class="trip-card">
          <div class="card-icon date"><i class="fa-regular fa-calendar"></i></div>
          <h4>Scheduled Date</h4>
          <p><?= $bookingDate ?></p>
          <p class="small"><?= $bookingDay ?></p>
        </div>

        <div class="trip-card">
          <div class="card-icon time"><i class="fa-regular fa-clock"></i></div>
          <h4>Pickup Time</h4>
          <p><?= $pickupTime ?></p>
        </div>

        <div class="trip-card">
          <div class="card-icon location"><i class="fa-solid fa-location-dot"></i></div>
          <h4>Pickup Location</h4>
          <p><?= htmlspecialchars($booking['pickup_location']) ?></p>
        </div>

        <div class="trip-card">
          <div class="card-icon location"><i class="fa-solid fa-flag-checkered"></i></div>
          <h4>Drop-off Location</h4>
          <p><?= htmlspecialchars($booking['dropoff_location']) ?></p>
        </div>

        <div class="trip-card">
          <div class="card-icon passengers"><i class="fa-solid fa-user-group"></i></div>
          <h4>Passengers</h4>
          <p><?= htmlspecialchars($booking['num_people']) ?> People</p>
        </div>

        <div class="trip-card">
          <div class="card-icon vehicle"><i class="fa-solid fa-car"></i></div>
          <h4>Vehicle Type</h4>
          <p><?= htmlspecialchars($booking['vehicle_type_name'] ?? $booking['vehicle_type']) ?></p>
        </div>
      </div>

      <?php if (!empty($booking['estimated_fare'])): ?>
      <div class="info-card" style="background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 50%, #ede9fe 100%); color: #1e293b; border: 1px solid #bfdbfe;">
        <h3 style="color: #3b82f6;"><i class="fa-solid fa-money-bill-wave"></i> Estimated Fare: LKR <?= number_format($booking['estimated_fare'], 2) ?></h3>
      </div>
      <?php endif; ?>

    </main>
  </div>

  <footer>
    <ul>
      <li><a href="#">About Us</a></li>
      <li><a href="#">Contact Us</a></li>
    </ul>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const hamburgerBtn = document.getElementById('hamburgerBtn');
      const sidebar = document.getElementById('sidebar');
      const sidebarOverlay = document.getElementById('sidebarOverlay');
      function toggleSidebar() {
        hamburgerBtn.classList.toggle('active'); sidebar.classList.toggle('active'); sidebarOverlay.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
      }
      function closeSidebar() {
        hamburgerBtn.classList.remove('active'); sidebar.classList.remove('active'); sidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
      }
      if (hamburgerBtn) hamburgerBtn.addEventListener('click', toggleSidebar);
      if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);
      document.querySelectorAll('.sidebar ul li a').forEach(link => {
        link.addEventListener('click', function() { if (window.innerWidth <= 768) closeSidebar(); });
      });
      window.addEventListener('resize', function() { if (window.innerWidth > 768) closeSidebar(); });
    });
  </script>
  <script>
    function toggleProfileDropdown() { document.getElementById('profileDropdown').classList.toggle('show'); }
    document.addEventListener('click', function(event) {
      const dropdown = document.getElementById('profileDropdown');
      const profilePic = document.querySelector('.profile-pic');
      if (dropdown && !dropdown.contains(event.target) && event.target !== profilePic) dropdown.classList.remove('show');
    });
  </script>

</body>
</html>
