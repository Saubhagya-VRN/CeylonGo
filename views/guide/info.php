<?php require_once 'session_init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Tour Details</title>
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/base.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/navbar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/sidebar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/footer.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/cards.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/buttons.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/responsive.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/info.css">
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
        <li class="active"><a href="/CeylonGo/public/guide/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Tours</a></li>
        <li><a href="/CeylonGo/public/guide/pending"><i class="fa-regular fa-clock"></i> Pending Requests</a></li>
        <li><a href="/CeylonGo/public/guide/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Tours</a></li>
        <li><a href="/CeylonGo/public/guide/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="/CeylonGo/public/guide/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/guide/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      
      <!-- Page Header -->
      <div class="page-header">
        <h1><i class="fa-solid fa-route"></i> Tour Details</h1>
        <a href="/CeylonGo/public/guide/upcoming" class="back-btn">
          <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
      </div>

      <!-- Status Banner -->
      <div class="status-banner">
        <div class="status-info">
          <i class="fa-solid fa-check-circle"></i>
          <div>
            <h3>Tour Confirmed</h3>
            <p>This tour has been confirmed and is scheduled</p>
          </div>
        </div>
        <span class="booking-id">#TG001</span>
      </div>

      <!-- Tourist Information -->
      <div class="tourist-card">
        <h3><i class="fa-solid fa-user-circle"></i> Tourist Information</h3>
        <div class="tourist-details">
          <div class="tourist-detail-item">
            <div class="icon-box"><i class="fa-solid fa-user"></i></div>
            <div class="detail-text">
              <label>Tourist Name</label>
              <p>John Silva</p>
            </div>
          </div>
          <div class="tourist-detail-item">
            <div class="icon-box"><i class="fa-solid fa-phone"></i></div>
            <div class="detail-text">
              <label>Contact Number</label>
              <p>+94 77 123 4567</p>
            </div>
          </div>
          <div class="tourist-detail-item">
            <div class="icon-box"><i class="fa-solid fa-envelope"></i></div>
            <div class="detail-text">
              <label>Email Address</label>
              <p>john.silva@email.com</p>
            </div>
          </div>
          <div class="tourist-detail-item">
            <div class="icon-box"><i class="fa-solid fa-globe"></i></div>
            <div class="detail-text">
              <label>Country</label>
              <p>United Kingdom</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tour Details Grid -->
      <div class="tour-grid">
        <div class="tour-card">
          <div class="card-icon date"><i class="fa-regular fa-calendar"></i></div>
          <h4>Tour Date</h4>
          <p>January 20, 2026</p>
          <p class="small">Monday</p>
        </div>

        <div class="tour-card">
          <div class="card-icon time"><i class="fa-regular fa-clock"></i></div>
          <h4>Start Time</h4>
          <p>09:00 AM</p>
          <p class="small">Be ready 15 mins early</p>
        </div>

        <div class="tour-card">
          <div class="card-icon location"><i class="fa-solid fa-location-dot"></i></div>
          <h4>Location</h4>
          <p>Sigiriya Rock Fortress</p>
          <p class="small">UNESCO World Heritage Site</p>
        </div>

        <div class="tour-card">
          <div class="card-icon tour-type"><i class="fa-solid fa-map"></i></div>
          <h4>Tour Type</h4>
          <p>Historical Tour</p>
          <p class="small">Cultural Heritage</p>
        </div>

        <div class="tour-card">
          <div class="card-icon people"><i class="fa-solid fa-user-group"></i></div>
          <h4>Group Size</h4>
          <p>4 People</p>
          <p class="small">2 Adults, 2 Children</p>
        </div>

        <div class="tour-card">
          <div class="card-icon duration"><i class="fa-solid fa-hourglass-half"></i></div>
          <h4>Duration</h4>
          <p>6 Hours</p>
          <p class="small">Full day tour</p>
        </div>
      </div>

      <!-- Special Notes -->
      <div class="notes-section">
        <h4><i class="fa-solid fa-sticky-note"></i> Special Notes from Tourist</h4>
        <p>We would appreciate a detailed explanation of the historical significance of each site. One person in our group has limited mobility, so please plan for accessible routes where possible. We're very interested in photography opportunities!</p>
      </div>

      <!-- Itinerary Section -->
      <div class="itinerary-section">
        <h3><i class="fa-solid fa-map-marked-alt"></i> Tour Itinerary</h3>
        
        <ul class="timeline">
          <li class="timeline-item">
            <div class="timeline-badge">1</div>
            <div class="timeline-content">
              <h4>09:00 AM - Meet & Greet</h4>
              <p>Meet the group at the Sigiriya main entrance. Brief introduction about the day's plan and safety guidelines.</p>
            </div>
          </li>

          <li class="timeline-item">
            <div class="timeline-badge">2</div>
            <div class="timeline-content">
              <h4>09:30 AM - Water Gardens</h4>
              <p>Explore the ancient water gardens and learn about the sophisticated hydraulic systems built by King Kashyapa.</p>
            </div>
          </li>

          <li class="timeline-item">
            <div class="timeline-badge">3</div>
            <div class="timeline-content">
              <h4>10:30 AM - Mirror Wall & Frescoes</h4>
              <p>Visit the famous mirror wall and view the ancient Sigiriya frescoes depicting celestial maidens.</p>
            </div>
          </li>

          <li class="timeline-item">
            <div class="timeline-badge">4</div>
            <div class="timeline-content">
              <h4>12:00 PM - Lion's Gate & Summit</h4>
              <p>Climb to the Lion's Gate and reach the summit to explore the palace ruins with panoramic views.</p>
            </div>
          </li>

          <li class="timeline-item">
            <div class="timeline-badge">5</div>
            <div class="timeline-content">
              <h4>02:00 PM - Lunch Break</h4>
              <p>Enjoy a traditional Sri Lankan lunch at a local restaurant (included in the tour).</p>
            </div>
          </li>
        </ul>
      </div>

      <!-- Action Buttons -->
      <div class="action-buttons">
        <button class="action-btn primary" onclick="alert('Contacting tourist...')">
          <i class="fa-solid fa-phone"></i> Contact Tourist
        </button>
        <button class="action-btn danger" onclick="if(confirm('Are you sure you want to cancel this tour?')) alert('Tour cancelled')">
          <i class="fa-solid fa-ban"></i> Cancel Tour
        </button>
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
