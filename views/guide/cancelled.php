<?php require_once 'session_init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Cancelled Tours</title>
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/base.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/navbar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/sidebar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/cards.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/buttons.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/tables.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/footer.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/responsive.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .status-badge.cancelled {
      background: #dc3545;
      color: white;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }
  </style>
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
      <img src="/CeylonGo/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="/CeylonGo/public/guide/dashboard">Home</a>
      <div class="profile-dropdown">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="User" class="profile-pic" onclick="toggleProfileDropdown()">
        <div class="profile-dropdown-menu" id="profileDropdown">
          <a href="/CeylonGo/public/guide/profile"><i class="fa-regular fa-user"></i> My Profile</a>
          <a href="/CeylonGo/public/logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
      </div>
    </nav>
  </header>
  
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="page-wrapper">
    <div class="sidebar" id="sidebar">
      <ul>
        <li><a href="/CeylonGo/public/guide/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="/CeylonGo/public/guide/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Tours</a></li>
        <li><a href="/CeylonGo/public/guide/pending"><i class="fa-regular fa-clock"></i> Pending Requests</a></li>
        <li class="active"><a href="/CeylonGo/public/guide/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Tours</a></li>
        <li><a href="/CeylonGo/public/guide/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="/CeylonGo/public/guide/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/guide/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </div>

    <div class="main-content">
      <h2 class="page-title"><i class="fa-solid fa-xmark"></i> Cancelled Tours</h2>

      <!-- Desktop Table View -->
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Booking No</th>
              <th>Tourist Name</th>
              <th>Date</th>
              <th>Location</th>
              <th>Tour Type</th>
              <th>Status</th>
              <th>Cancellation Reason</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#TG005</td>
              <td>Emma Rajapaksa</td>
              <td>2025-12-30</td>
              <td>Nuwara Eliya</td>
              <td>Nature Tour</td>
              <td><span class="status-badge cancelled">Cancelled</span></td>
              <td>Weather conditions - heavy rain expected</td>
              <td><a href="/CeylonGo/public/guide/cancelled_info" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a></td>
            </tr>
            <tr>
              <td>#TG006</td>
              <td>Michael De Silva</td>
              <td>2026-01-10</td>
              <td>Mirissa Beach</td>
              <td>Beach Tour</td>
              <td><span class="status-badge cancelled">Cancelled</span></td>
              <td>Customer request - change of plans</td>
              <td><a href="/CeylonGo/public/guide/cancelled_info" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a></td>
            </tr>
            <tr>
              <td>#TG007</td>
              <td>Priya Mendis</td>
              <td>2026-01-12</td>
              <td>Yala National Park</td>
              <td>Wildlife Safari</td>
              <td><span class="status-badge cancelled">Cancelled</span></td>
              <td>Park closed for maintenance</td>
              <td><a href="/CeylonGo/public/guide/cancelled_info" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a></td>
            </tr>
            <tr>
              <td>#TG008</td>
              <td>Alex Johnson</td>
              <td>2026-01-15</td>
              <td>Dambulla Cave Temple</td>
              <td>Cultural Tour</td>
              <td><span class="status-badge cancelled">Cancelled</span></td>
              <td>Guide unavailable - medical emergency</td>
              <td><a href="/CeylonGo/public/guide/cancelled_info" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile Card View -->
      <div class="booking-cards">
        <div class="booking-card-item" style="border-left-color: #dc3545;">
          <div class="card-header">
            <span class="booking-no">#TG005</span>
            <span class="status-badge cancelled">Cancelled</span>
          </div>
          <div class="card-body">
            <div class="card-row"><i class="fa-solid fa-user"></i><span class="label">Tourist:</span><span>Emma Rajapaksa</span></div>
            <div class="card-row"><i class="fa-regular fa-calendar"></i><span class="label">Date:</span><span>2025-12-30</span></div>
            <div class="card-row"><i class="fa-solid fa-location-dot"></i><span class="label">Location:</span><span>Nuwara Eliya</span></div>
            <div class="card-row"><i class="fa-solid fa-map"></i><span class="label">Tour:</span><span>Nature Tour</span></div>
            <div class="card-row"><i class="fa-solid fa-info-circle"></i><span class="label">Reason:</span><span>Weather conditions</span></div>
          </div>
          <div class="card-actions">
            <a href="/CeylonGo/public/guide/cancelled_info" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="booking-card-item" style="border-left-color: #dc3545;">
          <div class="card-header">
            <span class="booking-no">#TG006</span>
            <span class="status-badge cancelled">Cancelled</span>
          </div>
          <div class="card-body">
            <div class="card-row"><i class="fa-solid fa-user"></i><span class="label">Tourist:</span><span>Michael De Silva</span></div>
            <div class="card-row"><i class="fa-regular fa-calendar"></i><span class="label">Date:</span><span>2026-01-10</span></div>
            <div class="card-row"><i class="fa-solid fa-location-dot"></i><span class="label">Location:</span><span>Mirissa Beach</span></div>
            <div class="card-row"><i class="fa-solid fa-map"></i><span class="label">Tour:</span><span>Beach Tour</span></div>
            <div class="card-row"><i class="fa-solid fa-info-circle"></i><span class="label">Reason:</span><span>Customer request</span></div>
          </div>
          <div class="card-actions">
            <a href="/CeylonGo/public/guide/cancelled_info" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    hamburgerBtn.addEventListener('click', function() {
      hamburgerBtn.classList.toggle('active');
      sidebar.classList.toggle('active');
      sidebarOverlay.classList.toggle('active');
    });

    sidebarOverlay.addEventListener('click', function() {
      hamburgerBtn.classList.remove('active');
      sidebar.classList.remove('active');
      sidebarOverlay.classList.remove('active');
    });

    function toggleProfileDropdown() {
      document.getElementById('profileDropdown').classList.toggle('show');
    }

    window.onclick = function(event) {
      if (!event.target.matches('.profile-pic')) {
        var dropdowns = document.getElementsByClassName("profile-dropdown-menu");
        for (var i = 0; i < dropdowns.length; i++) {
          if (dropdowns[i].classList.contains('show')) {
            dropdowns[i].classList.remove('show');
          }
        }
      }
    }
  </script>
  <!-- Footer -->
  <footer>
    <ul>
      <li><a href="#">About Us</a></li>
      <li><a href="#">Contact Us</a></li>
    </ul>
  </footer>
</body>
</html>
