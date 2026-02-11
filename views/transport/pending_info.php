<?php require_once 'session_init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Pending Booking Details</title>
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

    <link rel="stylesheet" href="/CeylonGo/public/css/transport/pending_info.css">
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
        <li><a href="/CeylonGo/public/transporter/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Bookings</a></li>
        <li class="active"><a href="/CeylonGo/public/transporter/pending"><i class="fa-regular fa-clock"></i> Pending Bookings</a></li>
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
        <h1><i class="fa-solid fa-clock"></i> Pending Booking Request</h1>
        <button class="back-btn" onclick="history.back()">
          <i class="fa-solid fa-arrow-left"></i> Back to List
        </button>
      </div>

      <!-- Status Banner -->
      <div class="status-banner">
        <div class="status-info">
          <i class="fa-solid fa-hourglass-half"></i>
          <div>
            <h3>Awaiting Your Response</h3>
            <p>Please review and accept or reject this booking request</p>
          </div>
        </div>
        <span class="booking-id">#BK-12345</span>
      </div>

      <!-- Awaiting Response Box -->
      <div class="awaiting-box">
        <i class="fa-solid fa-bell"></i>
        <h3>New Booking Request!</h3>
        <p>A customer is waiting for your response. Please review the details below and make your decision.</p>
      </div>

      <!-- Customer Information -->
      <div class="customer-card">
        <h3><i class="fa-solid fa-user-circle"></i> Customer Information</h3>
        <div class="customer-details">
          <div class="customer-detail-item">
            <div class="icon-box"><i class="fa-solid fa-user"></i></div>
            <div class="detail-text">
              <label>Customer Name</label>
              <p>Sarah Fernando</p>
            </div>
          </div>
          <div class="customer-detail-item">
            <div class="icon-box"><i class="fa-solid fa-phone"></i></div>
            <div class="detail-text">
              <label>Contact Number</label>
              <p>+94 71 456 7890</p>
            </div>
          </div>
          <div class="customer-detail-item">
            <div class="icon-box"><i class="fa-solid fa-envelope"></i></div>
            <div class="detail-text">
              <label>Email Address</label>
              <p>sarah.fernando@email.com</p>
            </div>
          </div>
          <div class="customer-detail-item">
            <div class="icon-box"><i class="fa-solid fa-globe"></i></div>
            <div class="detail-text">
              <label>Country</label>
              <p>Australia</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Trip Details Grid -->
      <div class="trip-grid">
        <div class="trip-card">
          <div class="card-icon date"><i class="fa-regular fa-calendar"></i></div>
          <h4>Requested Date</h4>
          <p>March 15, 2026</p>
          <p class="small">Sunday</p>
        </div>

        <div class="trip-card">
          <div class="card-icon time"><i class="fa-regular fa-clock"></i></div>
          <h4>Pickup Time</h4>
          <p>09:00 AM</p>
          <p class="small">Morning pickup</p>
        </div>

        <div class="trip-card">
          <div class="card-icon duration"><i class="fa-solid fa-hourglass-half"></i></div>
          <h4>Duration</h4>
          <p>4 Days</p>
          <p class="small">Multi-day trip</p>
        </div>

        <div class="trip-card">
          <div class="card-icon location"><i class="fa-solid fa-location-dot"></i></div>
          <h4>Pickup Location</h4>
          <p>123, Park Road</p>
          <p class="small">Dehiwala, Colombo</p>
        </div>

        <div class="trip-card">
          <div class="card-icon passengers"><i class="fa-solid fa-user-group"></i></div>
          <h4>Passengers</h4>
          <p>4 Adults</p>
          <p class="small">Family trip</p>
        </div>

        <div class="trip-card">
          <div class="card-icon vehicle"><i class="fa-solid fa-car"></i></div>
          <h4>Vehicle Requested</h4>
          <p>Van (AC)</p>
          <p class="small">Spacious for luggage</p>
        </div>
      </div>

      <!-- Customer Notes -->
      <div class="notes-section">
        <h4><i class="fa-solid fa-comment-dots"></i> Customer's Message</h4>
        <p>Hi! We are a family of 4 traveling from Australia. We'd like to explore the hill country including Kandy, Nuwara Eliya, and Ella. We have quite a bit of luggage, so a spacious van would be great. Looking forward to hearing from you!</p>
      </div>

      <!-- Requested Itinerary Section -->
      <div class="itinerary-section">
        <h3><i class="fa-solid fa-map-marked-alt"></i> Requested Tour Plan</h3>
        
        <ul class="timeline">
          <li class="timeline-item">
            <div class="timeline-badge">1</div>
            <div class="timeline-content">
              <h4>Day 1: Kandy</h4>
              <p>Pickup from Dehiwala and drive to Kandy. Visit the Temple of the Sacred Tooth Relic and explore the Kandy Lake area. Evening cultural dance show.</p>
            </div>
          </li>

          <li class="timeline-item">
            <div class="timeline-badge">2</div>
            <div class="timeline-content">
              <h4>Day 2: Nuwara Eliya</h4>
              <p>Morning visit to Royal Botanic Gardens, then drive to Nuwara Eliya via tea plantations. Visit a tea factory and explore the "Little England" town.</p>
            </div>
          </li>

          <li class="timeline-item">
            <div class="timeline-badge">3</div>
            <div class="timeline-content">
              <h4>Day 3: Ella</h4>
              <p>Scenic drive to Ella. Visit the Nine Arch Bridge, hike to Little Adam's Peak for panoramic views, and explore Ravana Falls.</p>
            </div>
          </li>

          <li class="timeline-item">
            <div class="timeline-badge">4</div>
            <div class="timeline-content">
              <h4>Day 4: Return</h4>
              <p>Morning leisure time in Ella, then drive back to Colombo. Drop-off at the hotel or airport as per customer preference.</p>
            </div>
          </li>
        </ul>
      </div>

      <!-- Decision Section -->
      <div class="decision-section">
        <h3><i class="fa-solid fa-gavel"></i> Make Your Decision</h3>
        <div class="decision-buttons">
          <button class="decision-btn accept" onclick="acceptBooking()">
            <i class="fa-solid fa-check-circle"></i>
            Accept Booking
          </button>
          <button class="decision-btn reject" onclick="rejectBooking()">
            <i class="fa-solid fa-times-circle"></i>
            Reject Booking
          </button>
        </div>
      </div>

      <!-- Other Action Buttons -->
      <div class="action-buttons">
        <button class="action-btn primary" onclick="alert('Contacting customer...')">
          <i class="fa-solid fa-phone"></i> Contact Customer
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

    function acceptBooking() {
      if (confirm('Are you sure you want to ACCEPT this booking request?')) {
        alert('✅ Booking accepted successfully! The customer will be notified.');
        // TODO: Add AJAX call to update booking status in database
        window.location.href = '/CeylonGo/public/transporter/pending';
      }
    }

    function rejectBooking() {
      if (confirm('Are you sure you want to REJECT this booking request?')) {
        const reason = prompt('Please provide a reason for rejection (optional):');
        alert('❌ Booking rejected. The customer will be notified.');
        // TODO: Add AJAX call to update booking status with reason in database
        window.location.href = '/CeylonGo/public/transporter/pending';
      }
    }
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
