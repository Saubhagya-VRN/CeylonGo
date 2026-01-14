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
        <li><a href="/CeylonGo/public/transporter/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <h2 class="page-title"><span class="icon icon-calendar"></span> Upcoming Bookings</h2>

      <!-- Desktop Table View -->
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Booking No</th>
              <th>Date</th>
              <th>Pickup Time</th>
              <th>Pickup Location</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#12345</td>
              <td>2025-03-15</td>
              <td>09:00 AM</td>
              <td>123, Park Road, Dehiwala</td>
              <td><a href="/CeylonGo/public/transporter/info" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a></td>
            </tr>
            <tr>
              <td>#77889</td>
              <td>2025-08-19</td>
              <td>02:30 PM</td>
              <td>202 Cedar Road, Colombo</td>
              <td><a href="/CeylonGo/public/transporter/info" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a></td>
            </tr>
            <tr>
              <td>#VN001</td>
              <td>2026-01-15</td>
              <td>08:30 AM</td>
              <td>Colombo Fort</td>
              <td><a href="/CeylonGo/public/transporter/info" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a></td>
            </tr>
            <tr>
              <td>#VN002</td>
              <td>2026-01-18</td>
              <td>06:00 AM</td>
              <td>Mount Lavinia Hotel</td>
              <td><a href="/CeylonGo/public/transporter/info" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a></td>
            </tr>
            <tr>
              <td>#VN003</td>
              <td>2026-01-22</td>
              <td>10:00 AM</td>
              <td>Bandaranaike Airport</td>
              <td><a href="/CeylonGo/public/transporter/info" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a></td>
            </tr>
            <tr>
              <td>#VN004</td>
              <td>2026-02-01</td>
              <td>07:00 AM</td>
              <td>Kandy City</td>
              <td><a href="/CeylonGo/public/transporter/info" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile Card View -->
      <div class="booking-cards">
        <div class="booking-card-item">
          <div class="card-header">
            <span class="booking-no">#12345</span>
            <span class="status-badge upcoming">Upcoming</span>
          </div>
          <div class="card-body">
            <div class="card-row">
              <span class="icon icon-calendar"></span>
              <span class="label">Date:</span>
              <span>2025-03-15</span>
            </div>
            <div class="card-row">
              <span class="icon icon-clock"></span>
              <span class="label">Time:</span>
              <span>09:00 AM</span>
            </div>
            <div class="card-row">
              <span class="icon icon-location"></span>
              <span class="label">Pickup:</span>
              <span>123, Park Road, Dehiwala</span>
            </div>
          </div>
          <div class="card-actions">
            <a href="/CeylonGo/public/transporter/info" class="see-more-link">View Details <span class="icon icon-arrow-right"></span></a>
          </div>
        </div>

        <div class="booking-card-item">
          <div class="card-header">
            <span class="booking-no">#77889</span>
            <span class="status-badge upcoming">Upcoming</span>
          </div>
          <div class="card-body">
            <div class="card-row">
              <span class="icon icon-calendar"></span>
              <span class="label">Date:</span>
              <span>2025-08-19</span>
            </div>
            <div class="card-row">
              <span class="icon icon-clock"></span>
              <span class="label">Time:</span>
              <span>02:30 PM</span>
            </div>
            <div class="card-row">
              <span class="icon icon-location"></span>
              <span class="label">Pickup:</span>
              <span>202 Cedar Road, Colombo</span>
            </div>
          </div>
          <div class="card-actions">
            <a href="/CeylonGo/public/transporter/info" class="see-more-link">View Details <span class="icon icon-arrow-right"></span></a>
          </div>
        </div>

        <!-- Van Booking 1 -->
        <div class="booking-card-item" style="border-left-color: #2196F3;">
          <div class="card-header">
            <span class="booking-no">#VN001</span>
            <span class="status-badge upcoming">Van Booking</span>
          </div>
          <div class="card-body">
            <div class="card-row">
              <span class="icon icon-calendar"></span>
              <span class="label">Date:</span>
              <span>2026-01-15</span>
            </div>
            <div class="card-row">
              <span class="icon icon-clock"></span>
              <span class="label">Time:</span>
              <span>08:30 AM</span>
            </div>
            <div class="card-row">
              <span class="icon icon-location"></span>
              <span class="label">Pickup:</span>
              <span>Colombo Fort</span>
            </div>
            <div class="card-row">
              <span class="icon icon-users"></span>
              <span class="label">Passengers:</span>
              <span>10</span>
            </div>
          </div>
          <div class="card-actions">
            <a href="/CeylonGo/public/transporter/info" class="see-more-link">View Details <span class="icon icon-arrow-right"></span></a>
          </div>
        </div>

        <!-- Van Booking 2 -->
        <div class="booking-card-item" style="border-left-color: #2196F3;">
          <div class="card-header">
            <span class="booking-no">#VN002</span>
            <span class="status-badge upcoming">Van Booking</span>
          </div>
          <div class="card-body">
            <div class="card-row">
              <span class="icon icon-calendar"></span>
              <span class="label">Date:</span>
              <span>2026-01-18</span>
            </div>
            <div class="card-row">
              <span class="icon icon-clock"></span>
              <span class="label">Time:</span>
              <span>06:00 AM</span>
            </div>
            <div class="card-row">
              <span class="icon icon-location"></span>
              <span class="label">Pickup:</span>
              <span>Mount Lavinia Hotel</span>
            </div>
            <div class="card-row">
              <span class="icon icon-users"></span>
              <span class="label">Passengers:</span>
              <span>12</span>
            </div>
          </div>
          <div class="card-actions">
            <a href="/CeylonGo/public/transporter/info" class="see-more-link">View Details <span class="icon icon-arrow-right"></span></a>
          </div>
        </div>

        <!-- Van Booking 3 -->
        <div class="booking-card-item" style="border-left-color: #2196F3;">
          <div class="card-header">
            <span class="booking-no">#VN003</span>
            <span class="status-badge upcoming">Van Booking</span>
          </div>
          <div class="card-body">
            <div class="card-row">
              <span class="icon icon-calendar"></span>
              <span class="label">Date:</span>
              <span>2026-01-22</span>
            </div>
            <div class="card-row">
              <span class="icon icon-clock"></span>
              <span class="label">Time:</span>
              <span>10:00 AM</span>
            </div>
            <div class="card-row">
              <span class="icon icon-location"></span>
              <span class="label">Pickup:</span>
              <span>Bandaranaike Airport</span>
            </div>
            <div class="card-row">
              <span class="icon icon-users"></span>
              <span class="label">Passengers:</span>
              <span>7</span>
            </div>
          </div>
          <div class="card-actions">
            <a href="/CeylonGo/public/transporter/info" class="see-more-link">View Details <span class="icon icon-arrow-right"></span></a>
          </div>
        </div>

        <!-- Van Booking 4 -->
        <div class="booking-card-item" style="border-left-color: #2196F3;">
          <div class="card-header">
            <span class="booking-no">#VN004</span>
            <span class="status-badge upcoming">Van Booking</span>
          </div>
          <div class="card-body">
            <div class="card-row">
              <span class="icon icon-calendar"></span>
              <span class="label">Date:</span>
              <span>2026-02-01</span>
            </div>
            <div class="card-row">
              <span class="icon icon-clock"></span>
              <span class="label">Time:</span>
              <span>07:00 AM</span>
            </div>
            <div class="card-row">
              <span class="icon icon-location"></span>
              <span class="label">Pickup:</span>
              <span>Kandy City</span>
            </div>
            <div class="card-row">
              <span class="icon icon-users"></span>
              <span class="label">Passengers:</span>
              <span>9</span>
            </div>
          </div>
          <div class="card-actions">
            <a href="/CeylonGo/public/transporter/info" class="see-more-link">View Details <span class="icon icon-arrow-right"></span></a>
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

