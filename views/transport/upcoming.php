<?php require_once 'session_init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Upcoming Bookings</title>
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/base.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/navbar.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/sidebar.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/footer.css">
    
    <!-- Component styles -->
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/cards.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/buttons.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/forms.css">
    
    <!-- Page-specific styles -->
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/tables.css">

    <!-- Responsive styles (always last) -->
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <!-- Navbar -->
  <header class="navbar">
    <div class="branding">
      <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle menu">
        <span></span>
        <span></span>
        <span></span>
      </button>
      <img src="/CeylonGO/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
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

  <!-- Sidebar Overlay for Mobile -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="page-wrapper">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
      <ul>
        <li><a href="/CeylonGo/public/transporter/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li class="active"><a href="/CeylonGo/public/transporter/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/pending"><i class="fa-regular fa-clock"></i> Pending Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="/CeylonGo/public/transporter/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/transporter/report"><i class="fa-solid fa-chart-line"></i> Performance Report</a></li>
        <li><a href="/CeylonGo/public/transporter/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <h2 class="page-title"><span class="icon icon-calendar"></span> Upcoming Bookings</h2>

      <?php if (!empty($confirmed_bookings) && count($confirmed_bookings) > 0): ?>
      <!-- Desktop Table View -->
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Booking No</th>
              <th>Customer</th>
              <th>Date</th>
              <th>Pickup Time</th>
              <th>Pickup Location</th>
              <th>Vehicle Type</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($confirmed_bookings as $booking): ?>
            <tr>
              <td>#<?= htmlspecialchars($booking['id']) ?></td>
              <td><?= htmlspecialchars($booking['customer_name']) ?></td>
              <td><?= date('Y-m-d', strtotime($booking['date'])) ?></td>
              <td><?= date('h:i A', strtotime($booking['pickup_time'])) ?></td>
              <td><?= htmlspecialchars($booking['pickup_location']) ?></td>
              <td><?= htmlspecialchars($booking['vehicle_type']) ?></td>
              <td><a href="/CeylonGo/public/transporter/info?id=<?= $booking['id'] ?>" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Mobile Card View -->
      <div class="booking-cards">
        <?php foreach ($confirmed_bookings as $booking): ?>
        <div class="booking-card-item">
          <div class="card-header">
            <span class="booking-no">#<?= htmlspecialchars($booking['id']) ?></span>
            <span class="status-badge upcoming">Confirmed</span>
          </div>
          <div class="card-body">
            <div class="card-row">
              <span class="icon icon-user"></span>
              <span class="label">Customer:</span>
              <span><?= htmlspecialchars($booking['customer_name']) ?></span>
            </div>
            <div class="card-row">
              <span class="icon icon-calendar"></span>
              <span class="label">Date:</span>
              <span><?= date('Y-m-d', strtotime($booking['date'])) ?></span>
            </div>
            <div class="card-row">
              <span class="icon icon-clock"></span>
              <span class="label">Time:</span>
              <span><?= date('h:i A', strtotime($booking['pickup_time'])) ?></span>
            </div>
            <div class="card-row">
              <span class="icon icon-location"></span>
              <span class="label">Pickup:</span>
              <span><?= htmlspecialchars($booking['pickup_location']) ?></span>
            </div>
            <div class="card-row">
              <span class="icon icon-location"></span>
              <span class="label">Dropoff:</span>
              <span><?= htmlspecialchars($booking['dropoff_location']) ?></span>
            </div>
            <div class="card-row">
              <span class="icon icon-users"></span>
              <span class="label">Passengers:</span>
              <span><?= htmlspecialchars($booking['num_people']) ?></span>
            </div>
          </div>
          <div class="card-actions">
            <a href="/CeylonGo/public/transporter/info?id=<?= $booking['id'] ?>" class="see-more-link">View Details <span class="icon icon-arrow-right"></span></a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <?php else: ?>
      <!-- No bookings message -->
      <div style="text-align: center; padding: 60px 20px; color: #888;">
        <i class="fa-regular fa-calendar-xmark" style="font-size: 3em; margin-bottom: 15px; display: block; opacity: 0.4;"></i>
        <h3 style="color: #666; margin-bottom: 10px;">No Upcoming Bookings</h3>
        <p>You don't have any confirmed bookings yet. Once you accept a pending booking, it will appear here.</p>
        <a href="/CeylonGo/public/transporter/pending" style="display: inline-block; margin-top: 15px; padding: 10px 24px; background: #2563eb; color: white; border-radius: 8px; text-decoration: none; font-weight: 500;">
          <i class="fa-regular fa-clock"></i> View Pending Bookings
        </a>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <ul>
      <li><a href="#">About Us</a></li>
      <li><a href="#">Contact Us</a></li>
    </ul>
  </footer>

  <!-- Hamburger Menu Toggle Script -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const hamburgerBtn = document.getElementById('hamburgerBtn');
      const sidebar = document.getElementById('sidebar');
      const sidebarOverlay = document.getElementById('sidebarOverlay');
      
      function toggleSidebar() {
        hamburgerBtn.classList.toggle('active');
        sidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
      }
      
      function closeSidebar() {
        hamburgerBtn.classList.remove('active');
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
      }
      
      if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', toggleSidebar);
      }
      
      if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
      }
      
      const sidebarLinks = document.querySelectorAll('.sidebar ul li a');
      sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
          if (window.innerWidth <= 768) {
            closeSidebar();
          }
        });
      });
      
      window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
          closeSidebar();
        }
      });
    });
  </script>

  <!-- Profile Dropdown Script -->
  <script>
    function toggleProfileDropdown() {
      const dropdown = document.getElementById('profileDropdown');
      dropdown.classList.toggle('show');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      const dropdown = document.getElementById('profileDropdown');
      const profilePic = document.querySelector('.profile-pic');
      
      if (dropdown && !dropdown.contains(event.target) && event.target !== profilePic) {
        dropdown.classList.remove('show');
      }
    });
  </script>
</body>
</html>
