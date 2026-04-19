<?php require_once 'session_init.php'; 
$pending_bookings = $pending_bookings ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php require_once __DIR__ . '/../partials/app_notify_script.php'; ?>
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
    
    <!-- Font Awesome -->
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
        <li><a href="/CeylonGo/public/transporter/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Bookings</a></li>
        <li class="active"><a href="/CeylonGo/public/transporter/pending"><i class="fa-regular fa-clock"></i> Pending Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="/CeylonGo/public/transporter/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/transporter/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
        <li><a href="/CeylonGo/public/transporter/report"><i class="fa-solid fa-chart-line"></i> Performance Report</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <h2 class="page-title"><i class="fa-regular fa-clock"></i> Pending Bookings</h2>

      <?php if (empty($pending_bookings)): ?>
        <div style="text-align: center; padding: 60px 20px; color: #888;">
          <i class="fa-regular fa-clock" style="font-size: 48px; margin-bottom: 16px; display: block; color: #ccc;"></i>
          <h3 style="margin: 0 0 8px; color: #666;">No Pending Bookings</h3>
          <p style="margin: 0; font-size: 14px;">You don't have any pending booking requests at the moment.</p>
        </div>
      <?php else: ?>

      <!-- Desktop Table View -->
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Booking No</th>
              <th>Date</th>
              <th>Pickup Time</th>
              <th class="col-location">Pickup Location</th>
              <th>Passengers</th>
              <th>Manage Request</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pending_bookings as $booking): ?>
            <tr id="booking-row-<?php echo $booking['id']; ?>">
              <td>#<?php echo htmlspecialchars($booking['id']); ?></td>
              <td><?php echo htmlspecialchars($booking['date']); ?></td>
              <td><?php echo date('h:i A', strtotime($booking['pickup_time'])); ?></td>
              <td class="col-location"><?php echo htmlspecialchars($booking['pickup_location']); ?></td>
              <td><?php echo htmlspecialchars($booking['num_people']); ?></td>
              <td>
                <button class="accept-btn" onclick="handleBooking(<?php echo $booking['id']; ?>, 'accept')">Accept</button>
                <button class="reject-btn" onclick="handleBooking(<?php echo $booking['id']; ?>, 'reject')">Reject</button>
              </td>
              <td><a href="/CeylonGo/public/transporter/pending_info?id=<?php echo $booking['id']; ?>" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Mobile Card View -->
      <div class="booking-cards">
        <?php foreach ($pending_bookings as $booking): ?>
        <div class="booking-card-item" id="booking-card-<?php echo $booking['id']; ?>" style="border-left-color: #ff9800;">
          <div class="card-header">
            <span class="booking-no">#<?php echo htmlspecialchars($booking['id']); ?></span>
            <span class="status-badge pending" style="background: #fff3e0; color: #e65100;">
              <?php echo htmlspecialchars($booking['vehicle_type']); ?> - Pending
            </span>
          </div>
          <div class="card-body">
            <div class="card-row">
              <i class="fa-solid fa-calendar"></i>
              <span class="label">Date:</span>
              <span><?php echo htmlspecialchars($booking['date']); ?></span>
            </div>
            <div class="card-row">
              <i class="fa-solid fa-clock"></i>
              <span class="label">Time:</span>
              <span><?php echo date('h:i A', strtotime($booking['pickup_time'])); ?></span>
            </div>
            <div class="card-row">
              <i class="fa-solid fa-location-dot"></i>
              <span class="label">Pickup:</span>
              <span><?php echo htmlspecialchars($booking['pickup_location']); ?></span>
            </div>
            <div class="card-row">
              <i class="fa-solid fa-users"></i>
              <span class="label">Passengers:</span>
              <span><?php echo htmlspecialchars($booking['num_people']); ?></span>
            </div>
            <div class="card-row">
              <i class="fa-solid fa-user"></i>
              <span class="label">Customer:</span>
              <span><?php echo htmlspecialchars($booking['customer_name']); ?></span>
            </div>
          </div>
          <div class="card-actions" style="flex-direction: column; gap: 12px;">
            <div style="display: flex; gap: 10px;">
              <button class="accept-btn" style="flex: 1;" onclick="handleBooking(<?php echo $booking['id']; ?>, 'accept')">Accept</button>
              <button class="reject-btn" style="flex: 1;" onclick="handleBooking(<?php echo $booking['id']; ?>, 'reject')">Reject</button>
            </div>
            <a href="/CeylonGo/public/transporter/pending_info?id=<?php echo $booking['id']; ?>" class="see-more-link">View Details <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
        <?php endforeach; ?>
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

  <!-- Accept/Reject Booking Script -->
  <script>
    function handleBooking(bookingId, action) {
      var actionText = action === 'accept' ? 'ACCEPT' : 'REJECT';
      if (!confirm('Are you sure you want to ' + actionText + ' this booking request?')) return;

      var url = action === 'accept' 
        ? '/CeylonGo/public/transporter/accept-booking' 
        : '/CeylonGo/public/transporter/reject-booking';

      fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ booking_id: bookingId })
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.success) {
          alert((action === 'accept' ? '✅ ' : '❌ ') + data.message);
          // Remove the row/card from the page
          var row = document.getElementById('booking-row-' + bookingId);
          var card = document.getElementById('booking-card-' + bookingId);
          if (row) row.remove();
          if (card) card.remove();
          // If no more bookings, reload to show empty state
          var tbody = document.querySelector('tbody');
          if (tbody && tbody.children.length === 0) location.reload();
          var cards = document.querySelector('.booking-cards');
          if (cards && cards.children.length === 0) location.reload();
        } else {
          alert('Error: ' + (data.message || 'Something went wrong'));
        }
      })
      .catch(function() {
        alert('An error occurred. Please try again.');
      });
    }
  </script>

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
