<?php require_once 'session_init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Booking Details</title>
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/base.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/navbar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/sidebar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/footer.css">
    
    <!-- Component styles -->
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/cards.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/buttons.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/forms.css">

    <!-- Responsive styles (always last) -->
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/responsive.css">  
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="/CeylonGo/public/css/transport/info.css">
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
    <aside class="sidebar" id="sidebar">
      <ul>
        <li><a href="/CeylonGo/public/transporter/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li class="active"><a href="/CeylonGo/public/transporter/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/pending"><i class="fa-regular fa-clock"></i> Pending Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="/CeylonGo/public/transporter/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/transporter/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      
      <!-- Page Header -->
      <div class="page-header">
        <h1><i class="fa-solid fa-route"></i> Booking Details</h1>
        <button class="back-btn" onclick="history.back()">
          <i class="fa-solid fa-arrow-left"></i> Back to List
        </button>
      </div>

      <!-- Status Banner -->
      <div class="status-banner">
        <div class="status-info">
          <i class="fa-solid fa-check-circle"></i>
          <div>
            <h3>Booking Confirmed</h3>
            <p>This booking has been confirmed and is ready for pickup</p>
          </div>
        </div>
        <span class="booking-id">#BK-12345</span>
      </div>

      <!-- Customer Information -->
      <div class="customer-card">
        <h3><i class="fa-solid fa-user-circle"></i> Customer Information</h3>
        <div class="customer-details">
          <div class="customer-detail-item">
            <div class="icon-box"><i class="fa-solid fa-user"></i></div>
            <div class="detail-text">
              <label>Customer Name</label>
              <p>John Silva</p>
            </div>
          </div>
          <div class="customer-detail-item">
            <div class="icon-box"><i class="fa-solid fa-phone"></i></div>
            <div class="detail-text">
              <label>Contact Number</label>
              <p>+94 77 123 4567</p>
            </div>
          </div>
          <div class="customer-detail-item">
            <div class="icon-box"><i class="fa-solid fa-envelope"></i></div>
            <div class="detail-text">
              <label>Email Address</label>
              <p>john.silva@email.com</p>
            </div>
          </div>
          <div class="customer-detail-item">
            <div class="icon-box"><i class="fa-solid fa-globe"></i></div>
            <div class="detail-text">
              <label>Country</label>
              <p>United Kingdom</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Trip Details Grid -->
      <div class="trip-grid">
        <div class="trip-card">
          <div class="card-icon date"><i class="fa-regular fa-calendar"></i></div>
          <h4>Starting Date</h4>
          <p>January 15, 2026</p>
          <p class="small">Monday</p>
        </div>

        <div class="trip-card">
          <div class="card-icon date"><i class="fa-regular fa-calendar-check"></i></div>
          <h4>Ending Date</h4>
          <p>January 22, 2026</p>
          <p class="small">Monday</p>
        </div>

        <div class="trip-card">
          <div class="card-icon time"><i class="fa-regular fa-clock"></i></div>
          <h4>Pickup Time</h4>
          <p>08:30 AM</p>
          <p class="small">Be ready 15 mins early</p>
        </div>

        <div class="trip-card">
          <div class="card-icon duration"><i class="fa-solid fa-hourglass-half"></i></div>
          <h4>Duration</h4>
          <p>8 Days / 7 Nights</p>
          <p class="small">Full package tour</p>
        </div>

        <div class="trip-card">
          <div class="card-icon location"><i class="fa-solid fa-location-dot"></i></div>
          <h4>Pickup Location</h4>
          <p>Hospital Road, Dehiwala</p>
          <p class="small">Near the main junction</p>
        </div>

        <div class="trip-card">
          <div class="card-icon passengers"><i class="fa-solid fa-user-group"></i></div>
          <h4>Passengers</h4>
          <p>2 Adults</p>
          <p class="small">No children</p>
        </div>

        <div class="trip-card">
          <div class="card-icon vehicle"><i class="fa-solid fa-car"></i></div>
          <h4>Vehicle Type</h4>
          <p>Sedan (AC)</p>
          <p class="small">Comfortable for 2-3 passengers</p>
        </div>

        <div class="trip-card">
          <div class="card-icon location"><i class="fa-solid fa-flag-checkered"></i></div>
          <h4>Drop-off Location</h4>
          <p>Bandaranaike Airport</p>
          <p class="small">International Terminal</p>
        </div>
      </div>

      <!-- Special Notes -->
      <div class="notes-section">
        <h4><i class="fa-solid fa-sticky-note"></i> Special Notes from Customer</h4>
        <p>Please be punctual. We have an elderly person traveling with us, so kindly drive carefully. We prefer a non-smoking vehicle. Thank you!</p>
      </div>

      <!-- Itinerary Section -->
      <div class="itinerary-section">
        <h3><i class="fa-solid fa-map-marked-alt"></i> Trip Itinerary</h3>
        
        <ul class="timeline">
          <li class="timeline-item">
            <div class="timeline-badge">1</div>
            <div class="timeline-content">
              <h4>Day 1–2: Kandy</h4>
              <p>Visit the Temple of the Sacred Tooth Relic, explore the Royal Botanic Gardens at Peradeniya, and enjoy an evening cultural dance show featuring traditional Kandyan dancers.</p>
            </div>
          </li>

          <li class="timeline-item">
            <div class="timeline-badge">2</div>
            <div class="timeline-content">
              <h4>Day 3–4: Ella</h4>
              <p>Hike to Little Adam's Peak for stunning views, visit the iconic Nine Arch Bridge, and enjoy the scenic Ella Gap views. Optional visit to Ravana Falls.</p>
            </div>
          </li>

          <li class="timeline-item">
            <div class="timeline-badge">3</div>
            <div class="timeline-content">
              <h4>Day 5–6: Yala</h4>
              <p>Experience an exciting safari in Yala National Park, home to leopards, elephants, crocodiles, and many exotic bird species. Morning and evening game drives included.</p>
            </div>
          </li>

          <li class="timeline-item">
            <div class="timeline-badge">4</div>
            <div class="timeline-content">
              <h4>Day 7–8: Galle</h4>
              <p>Explore the historic Galle Fort (UNESCO World Heritage Site), walk along the ramparts, and relax on the beautiful beaches of Unawatuna before airport drop-off.</p>
            </div>
          </li>
        </ul>
      </div>

      <!-- Action Buttons -->
      <div class="action-buttons">
        <button class="action-btn success" onclick="alert('Contacting customer...')">
          <i class="fa-solid fa-phone"></i> Contact Customer
        </button>
        <button class="action-btn danger" onclick="if(confirm('Are you sure you want to cancel this booking?')) alert('Booking cancelled')">
          <i class="fa-solid fa-ban"></i> Cancel Booking
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
