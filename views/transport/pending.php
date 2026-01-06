<?php require_once 'session_init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Pending Bookings</title>
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
    
    <link rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
      <a href="/CeylonGo/public/logout">Logout</a>
      <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="User" class="profile-pic">
    </nav>
  </header>

  <!-- Sidebar Overlay for Mobile -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="page-wrapper">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
      <ul>
        <li><a href="/CeylonGo/public/transporter/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="/CeylonGo/public/transporter/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Bookings</a></li>
        <li class="active"><a href="/CeylonGo/public/transporter/pending"><i class="fa-regular fa-clock"></i> Pending Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="/CeylonGo/public/transporter/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/transporter/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <h2 class="page-title"><i class="fa-regular fa-clock"></i> Pending Bookings</h2>

      <!-- Desktop Table View -->
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Booking No</th>
              <th>Date</th>
              <th>Pickup Time</th>
              <th>Pickup Location</th>
              <th>No. of Days</th>
              <th>Manage Request</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#12345</td>
              <td>2025-03-15</td>
              <td>09:00 AM</td>
              <td>123, Park Road, Dehiwala</td>
              <td>4</td>
              <td>
                <button class="accept-btn">Accept</button>
                <button class="reject-btn">Reject</button>
              </td>
              <td><a href="/CeylonGo/public/transporter/pending_info" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile Card View -->
      <div class="booking-cards">
        <div class="booking-card-item" style="border-left-color: #ff9800;">
          <div class="card-header">
            <span class="booking-no">#12345</span>
            <span class="status-badge pending" style="background: #fff3e0; color: #e65100;">Pending</span>
          </div>
          <div class="card-body">
            <div class="card-row">
              <i class="fa-solid fa-calendar"></i>
              <span class="label">Date:</span>
              <span>2025-03-15</span>
            </div>
            <div class="card-row">
              <i class="fa-solid fa-clock"></i>
              <span class="label">Time:</span>
              <span>09:00 AM</span>
            </div>
            <div class="card-row">
              <i class="fa-solid fa-location-dot"></i>
              <span class="label">Pickup:</span>
              <span>123, Park Road, Dehiwala</span>
            </div>
            <div class="card-row">
              <i class="fa-solid fa-calendar-days"></i>
              <span class="label">Duration:</span>
              <span>4 Days</span>
            </div>
          </div>
          <div class="card-actions" style="flex-direction: column; gap: 12px;">
            <div style="display: flex; gap: 10px;">
              <button class="accept-btn" style="flex: 1;">Accept</button>
              <button class="reject-btn" style="flex: 1;">Reject</button>
            </div>
            <a href="/CeylonGo/public/transporter/pending_info" class="see-more-link">View Details <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
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
</body>
</html>
