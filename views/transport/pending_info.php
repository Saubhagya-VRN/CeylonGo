<?php require_once 'session_init.php'; 
$booking = $booking ?? null;
if (!$booking) {
    header('Location: /CeylonGo/public/transporter/pending');
    exit();
}
// Format date nicely
$dateFormatted = date('F j, Y', strtotime($booking['date']));
$dayOfWeek = date('l', strtotime($booking['date']));
$timeFormatted = date('h:i A', strtotime($booking['pickup_time']));
$customerName = trim(($booking['tourist_first_name'] ?? '') . ' ' . ($booking['tourist_last_name'] ?? ''));
if (empty($customerName)) $customerName = $booking['customer_name'] ?? 'Customer';
$customerEmail = $booking['tourist_email'] ?? '';
$customerPhone = $booking['contact_number'] ?? '';
?>
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
        <li><a href="/CeylonGo/public/transporter/report"><i class="fa-solid fa-chart-line"></i> Performance Report</a></li>
        <li><a href="/CeylonGo/public/transporter/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      
      <!-- Page Header -->
      <div class="page-header">
        <h1><i class="fa-solid fa-clock"></i> Pending Booking Request</h1>
        <button class="back-btn" onclick="window.location.href='/CeylonGo/public/transporter/pending'">
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
        <span class="booking-id">#BK-<?php echo htmlspecialchars($booking['id']); ?></span>
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
              <p><?php echo htmlspecialchars($customerName); ?></p>
            </div>
          </div>
          <div class="customer-detail-item">
            <div class="icon-box"><i class="fa-solid fa-phone"></i></div>
            <div class="detail-text">
              <label>Contact Number</label>
              <p><?php echo htmlspecialchars($customerPhone); ?></p>
            </div>
          </div>
          <?php if (!empty($customerEmail)): ?>
          <div class="customer-detail-item">
            <div class="icon-box"><i class="fa-solid fa-envelope"></i></div>
            <div class="detail-text">
              <label>Email Address</label>
              <p><?php echo htmlspecialchars($customerEmail); ?></p>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Trip Details Grid -->
      <div class="trip-grid">
        <div class="trip-card">
          <div class="card-icon date"><i class="fa-regular fa-calendar"></i></div>
          <h4>Requested Date</h4>
          <p><?php echo htmlspecialchars($dateFormatted); ?></p>
          <p class="small"><?php echo htmlspecialchars($dayOfWeek); ?></p>
        </div>

        <div class="trip-card">
          <div class="card-icon time"><i class="fa-regular fa-clock"></i></div>
          <h4>Pickup Time</h4>
          <p><?php echo htmlspecialchars($timeFormatted); ?></p>
          <p class="small"><?php echo strpos($timeFormatted, 'AM') !== false ? 'Morning pickup' : 'Afternoon pickup'; ?></p>
        </div>

        <div class="trip-card">
          <div class="card-icon location"><i class="fa-solid fa-location-dot"></i></div>
          <h4>Pickup Location</h4>
          <p><?php echo htmlspecialchars($booking['pickup_location']); ?></p>
        </div>

        <div class="trip-card">
          <div class="card-icon location"><i class="fa-solid fa-map-marker-alt"></i></div>
          <h4>Dropoff Location</h4>
          <p><?php echo htmlspecialchars($booking['dropoff_location']); ?></p>
        </div>

        <div class="trip-card">
          <div class="card-icon passengers"><i class="fa-solid fa-user-group"></i></div>
          <h4>Passengers</h4>
          <p><?php echo htmlspecialchars($booking['num_people']); ?> People</p>
        </div>

        <div class="trip-card">
          <div class="card-icon vehicle"><i class="fa-solid fa-car"></i></div>
          <h4>Vehicle Requested</h4>
          <p><?php echo htmlspecialchars($booking['vehicle_type']); ?></p>
          <?php if (!empty($booking['assigned_vehicle_no'])): ?>
          <p class="small">Assigned: <?php echo htmlspecialchars($booking['assigned_vehicle_no']); ?></p>
          <?php endif; ?>
        </div>
      </div>

      <?php if (!empty($booking['estimated_fare'])): ?>
      <div class="trip-grid" style="margin-top: 0;">
        <div class="trip-card">
          <div class="card-icon" style="background: #e8f5e9; color: #2e7d32;"><i class="fa-solid fa-money-bill-wave"></i></div>
          <h4>Estimated Fare</h4>
          <p>LKR <?php echo number_format((float)$booking['estimated_fare'], 2); ?></p>
        </div>
        <?php if (!empty($booking['distance'])): ?>
        <div class="trip-card">
          <div class="card-icon" style="background: #e3f2fd; color: #1565c0;"><i class="fa-solid fa-road"></i></div>
          <h4>Distance</h4>
          <p><?php echo number_format((float)$booking['distance'], 1); ?> km</p>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- Customer Notes -->
      <?php if (!empty($booking['notes'])): ?>
      <div class="notes-section">
        <h4><i class="fa-solid fa-comment-dots"></i> Customer's Message</h4>
        <p><?php echo htmlspecialchars($booking['notes']); ?></p>
      </div>
      <?php endif; ?>

      <!-- Decision Section -->
      <div class="decision-section">
        <h3><i class="fa-solid fa-gavel"></i> Make Your Decision</h3>
        <div class="decision-buttons">
          <button class="decision-btn accept" onclick="acceptBooking(<?php echo $booking['id']; ?>)">
            <i class="fa-solid fa-check-circle"></i>
            Accept Booking
          </button>
          <button class="decision-btn reject" onclick="rejectBooking(<?php echo $booking['id']; ?>)">
            <i class="fa-solid fa-times-circle"></i>
            Reject Booking
          </button>
        </div>
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

    function acceptBooking(bookingId) {
      if (!confirm('Are you sure you want to ACCEPT this booking request?')) return;
      
      fetch('/CeylonGo/public/transporter/accept-booking', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ booking_id: bookingId })
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.success) {
          alert('✅ ' + data.message);
          window.location.href = '/CeylonGo/public/transporter/pending';
        } else {
          alert('Error: ' + (data.message || 'Something went wrong'));
        }
      })
      .catch(function() {
        alert('An error occurred. Please try again.');
      });
    }

    function rejectBooking(bookingId) {
      if (!confirm('Are you sure you want to REJECT this booking request?')) return;
      
      fetch('/CeylonGo/public/transporter/reject-booking', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ booking_id: bookingId })
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.success) {
          alert('❌ ' + data.message);
          window.location.href = '/CeylonGo/public/transporter/pending';
        } else {
          alert('Error: ' + (data.message || 'Something went wrong'));
        }
      })
      .catch(function() {
        alert('An error occurred. Please try again.');
      });
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
