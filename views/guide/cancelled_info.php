<?php require_once 'session_init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Cancelled Tour Details</title>
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/base.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/navbar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/sidebar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/footer.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/cards.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/buttons.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/responsive.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/cancelled_info.css">
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

  <!-- Sidebar Overlay for Mobile -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="page-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <ul>
        <li><a href="/CeylonGo/public/guide/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="/CeylonGo/public/guide/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Tours</a></li>
        <li><a href="/CeylonGo/public/guide/pending"><i class="fa-regular fa-clock"></i> Pending Requests</a></li>
        <li class="active"><a href="/CeylonGo/public/guide/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Tours</a></li>
        <li><a href="/CeylonGo/public/guide/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="/CeylonGo/public/guide/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/guide/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      
      <!-- Page Header -->
      <div class="page-header">
        <h1><i class="fa-solid fa-ban"></i> Cancelled Tour Details</h1>
        <a href="/CeylonGo/public/guide/cancelled" class="back-btn">
          <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
      </div>

      <!-- Status Banner -->
      <div class="status-banner">
        <div class="status-info">
          <i class="fa-solid fa-times-circle"></i>
          <div>
            <h3>Tour Cancelled</h3>
            <p>This tour has been cancelled and will not proceed</p>
          </div>
        </div>
        <span class="booking-id">#TG005</span>
      </div>

      <!-- Cancellation Details -->
      <div class="cancellation-card">
        <h3><i class="fa-solid fa-info-circle"></i> Cancellation Details</h3>
        <div class="cancellation-details">
          <div class="cancellation-item">
            <label>Cancellation Reason</label>
            <p>Weather conditions - heavy rain expected</p>
          </div>
          <div class="cancellation-item">
            <label>Cancelled By</label>
            <p>Tour Guide</p>
          </div>
          <div class="cancellation-item">
            <label>Cancelled On</label>
            <p>December 28, 2025</p>
          </div>
          <div class="cancellation-item">
            <label>Refund Status</label>
            <p>Full refund processed</p>
          </div>
        </div>
      </div>

      <!-- Tourist Information -->
      <div class="tourist-card">
        <h3><i class="fa-solid fa-user-circle"></i> Tourist Information</h3>
        <div class="tourist-details">
          <div class="tourist-detail-item">
            <div class="icon-box"><i class="fa-solid fa-user"></i></div>
            <div class="detail-text">
              <label>Tourist Name</label>
              <p>Emma Rajapaksa</p>
            </div>
          </div>
          <div class="tourist-detail-item">
            <div class="icon-box"><i class="fa-solid fa-phone"></i></div>
            <div class="detail-text">
              <label>Contact Number</label>
              <p>+94 77 456 7890</p>
            </div>
          </div>
          <div class="tourist-detail-item">
            <div class="icon-box"><i class="fa-solid fa-envelope"></i></div>
            <div class="detail-text">
              <label>Email Address</label>
              <p>emma.r@email.com</p>
            </div>
          </div>
          <div class="tourist-detail-item">
            <div class="icon-box"><i class="fa-solid fa-globe"></i></div>
            <div class="detail-text">
              <label>Country</label>
              <p>Sri Lanka</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tour Details Grid (Greyed Out) -->
      <div class="tour-grid">
        <div class="tour-card">
          <div class="card-icon date"><i class="fa-regular fa-calendar"></i></div>
          <h4>Scheduled Date</h4>
          <p class="cancelled">December 30, 2025</p>
          <p class="small">Wednesday</p>
        </div>

        <div class="tour-card">
          <div class="card-icon time"><i class="fa-regular fa-clock"></i></div>
          <h4>Scheduled Time</h4>
          <p class="cancelled">08:00 AM</p>
          <p class="small">Morning session</p>
        </div>

        <div class="tour-card">
          <div class="card-icon location"><i class="fa-solid fa-location-dot"></i></div>
          <h4>Location</h4>
          <p>Nuwara Eliya</p>
          <p class="small">Hill Country</p>
        </div>

        <div class="tour-card">
          <div class="card-icon tour-type"><i class="fa-solid fa-map"></i></div>
          <h4>Tour Type</h4>
          <p>Nature Tour</p>
          <p class="small">Scenic Experience</p>
        </div>

        <div class="tour-card">
          <div class="card-icon people"><i class="fa-solid fa-user-group"></i></div>
          <h4>Group Size</h4>
          <p>3 People</p>
          <p class="small">Family trip</p>
        </div>
      </div>

      <!-- What Was Planned Section -->
      <div class="planned-section">
        <h3><i class="fa-solid fa-route"></i> What Was Planned (Cancelled)</h3>
        
        <ul class="timeline">
          <li class="timeline-item">
            <div class="timeline-badge">1</div>
            <div class="timeline-content">
              <h4>08:00 AM - Pickup from Nuwara Eliya</h4>
              <p>Hotel pickup and introduction to the day's itinerary.</p>
            </div>
          </li>

          <li class="timeline-item">
            <div class="timeline-badge">2</div>
            <div class="timeline-content">
              <h4>09:00 AM - Gregory Lake</h4>
              <p>Scenic walk around the lake with optional boat ride.</p>
            </div>
          </li>

          <li class="timeline-item">
            <div class="timeline-badge">3</div>
            <div class="timeline-content">
              <h4>11:00 AM - Tea Plantation Visit</h4>
              <p>Tour of Pedro Tea Estate with tea tasting session.</p>
            </div>
          </li>

          <li class="timeline-item">
            <div class="timeline-badge">4</div>
            <div class="timeline-content">
              <h4>01:00 PM - Lunch & Return</h4>
              <p>Traditional lunch at a local restaurant before return.</p>
            </div>
          </li>
        </ul>
      </div>

    </main>
  </div>

  <!-- Footer -->
  <footer>
    <ul>
      <li><a href="#">About Us</a></li>
      <li><a href="#">Contact Us</a></li>
    </ul>
  </footer>

  <!-- Scripts -->
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

</body>
</html>
