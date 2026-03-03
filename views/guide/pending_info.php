<?php require_once 'session_init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Pending Tour Request</title>
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/base.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/navbar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/sidebar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/footer.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/cards.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/buttons.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/responsive.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/pending_info.css">
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
        <li class="active"><a href="/CeylonGo/public/guide/pending"><i class="fa-regular fa-clock"></i> Pending Requests</a></li>
        <li><a href="/CeylonGo/public/guide/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Tours</a></li>
        <li><a href="/CeylonGo/public/guide/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="/CeylonGo/public/guide/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/guide/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      
      <?php if (!$booking): ?>
        <div class="page-header">
          <h1><i class="fa-regular fa-clock"></i> Pending Tour Request</h1>
          <a href="/CeylonGo/public/guide/pending" class="back-btn">
            <i class="fa-solid fa-arrow-left"></i> Back to List
          </a>
        </div>
        <p style="text-align:center;padding:40px;color:#888;">Booking not found.</p>
      <?php else: ?>

      <!-- Page Header -->
      <div class="page-header">
        <h1><i class="fa-regular fa-clock"></i> Pending Tour Request</h1>
        <a href="/CeylonGo/public/guide/pending" class="back-btn">
          <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
      </div>

      <!-- Status Banner -->
      <div class="status-banner">
        <div class="status-info">
          <i class="fa-solid fa-hourglass-half"></i>
          <div>
            <h3>Awaiting Your Response</h3>
            <p>Please review this request and accept or reject</p>
          </div>
        </div>
        <span class="booking-id">#TG<?= str_pad($booking['id'], 3, '0', STR_PAD_LEFT) ?></span>
      </div>

      <!-- Tourist Information -->
      <div class="tourist-card">
        <h3><i class="fa-solid fa-user-circle"></i> Tourist Information</h3>
        <div class="tourist-details">
          <div class="tourist-detail-item">
            <div class="icon-box"><i class="fa-solid fa-user"></i></div>
            <div class="detail-text">
              <label>Tourist Name</label>
              <p><?= htmlspecialchars($booking['customerName']) ?></p>
            </div>
          </div>
          <div class="tourist-detail-item">
            <div class="icon-box"><i class="fa-solid fa-phone"></i></div>
            <div class="detail-text">
              <label>Contact Number</label>
              <p><?= htmlspecialchars($booking['contactNumber']) ?></p>
            </div>
          </div>
          <div class="tourist-detail-item">
            <div class="icon-box"><i class="fa-solid fa-language"></i></div>
            <div class="detail-text">
              <label>Language</label>
              <p><?= htmlspecialchars($booking['language']) ?></p>
            </div>
          </div>
          <div class="tourist-detail-item">
            <div class="icon-box"><i class="fa-solid fa-calendar-plus"></i></div>
            <div class="detail-text">
              <label>Requested On</label>
              <p><?= date('M d, Y h:i A', strtotime($booking['created_at'])) ?></p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tour Details Grid -->
      <div class="tour-grid">
        <div class="tour-card">
          <div class="card-icon date"><i class="fa-regular fa-calendar"></i></div>
          <h4>Requested Date</h4>
          <p><?= date('F d, Y', strtotime($booking['date'])) ?></p>
          <p class="small"><?= date('l', strtotime($booking['date'])) ?></p>
        </div>

        <div class="tour-card">
          <div class="card-icon time"><i class="fa-regular fa-clock"></i></div>
          <h4>Preferred Time</h4>
          <p><?= date('h:i A', strtotime($booking['time'])) ?></p>
        </div>

        <div class="tour-card">
          <div class="card-icon location"><i class="fa-solid fa-location-dot"></i></div>
          <h4>Location</h4>
          <p><?= htmlspecialchars($booking['location']) ?></p>
        </div>

        <div class="tour-card">
          <div class="card-icon tour-type"><i class="fa-solid fa-language"></i></div>
          <h4>Language</h4>
          <p><?= htmlspecialchars($booking['language']) ?> Tour</p>
        </div>
      </div>

      <!-- Message from Tourist -->
      <?php if (!empty($booking['notes'])): ?>
      <div class="message-section">
        <h3><i class="fa-solid fa-message"></i> Message from Tourist</h3>
        <div class="message-content">
          <p><?= htmlspecialchars($booking['notes']) ?></p>
        </div>
      </div>
      <?php endif; ?>

      <!-- Action Buttons -->
      <div class="action-buttons">
        <a href="tel:<?= htmlspecialchars($booking['contactNumber']) ?>" class="action-btn contact">
          <i class="fa-solid fa-phone"></i> Contact Tourist
        </a>
      </div>

      <?php endif; ?>

    </main>
  </div>

  <!-- Floating Decision Panel -->
  <?php if ($booking && $booking['status'] === 'pending'): ?>
  <div class="decision-section">
    <h3><i class="fa-solid fa-gavel"></i> Make Your Decision</h3>
    <div class="decision-buttons">
      <form method="POST" action="/CeylonGo/public/guide/accept-booking" style="display:inline;">
        <input type="hidden" name="request_id" value="<?= $booking['id'] ?>">
        <button type="submit" class="decision-btn accept" onclick="return confirm('Accept this tour request?')">
          <i class="fa-solid fa-check-circle"></i>
          Accept
        </button>
      </form>
      <form method="POST" action="/CeylonGo/public/guide/reject-booking" style="display:inline;">
        <input type="hidden" name="request_id" value="<?= $booking['id'] ?>">
        <button type="submit" class="decision-btn reject" onclick="return confirm('Are you sure you want to reject this request?')">
          <i class="fa-solid fa-times-circle"></i>
          Reject
        </button>
      </form>
    </div>
  </div>
  <?php endif; ?>

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
