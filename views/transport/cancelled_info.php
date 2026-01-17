<?php require_once 'session_init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Cancelled Booking Details</title>
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

    <style>
        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 28px;
            color: #1a1a2e;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-header h1 i {
            color: #dc3545;
        }

        .back-btn {
            padding: 10px 20px;
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            color: #fff;
        }

        /* Cancelled Status Banner */
        .status-banner {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: #fff;
            padding: 20px 25px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .status-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .status-info i {
            font-size: 28px;
        }

        .status-info h3 {
            margin: 0;
            font-size: 20px;
        }

        .status-info p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .booking-id {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        /* Cancellation Reason Card */
        .cancellation-card {
            background: linear-gradient(135deg, #fff5f5 0%, #ffe6e6 100%);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            border-left: 5px solid #dc3545;
        }

        .cancellation-card h4 {
            margin: 0 0 12px 0;
            color: #c82333;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cancellation-card p {
            margin: 0;
            color: #721c24;
            font-size: 15px;
            line-height: 1.6;
        }

        .cancellation-card .cancelled-date {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(220, 53, 69, 0.2);
            font-size: 13px;
            color: #856404;
        }

        /* Info Card */
        .info-card {
            background: #fff;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
        }

        .info-card h3 {
            font-size: 18px;
            color: #1a1a2e;
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .info-card h3 i {
            color: #0077b6;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .detail-item .icon-box {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #e8f4f8 0%, #d4edda 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0077b6;
            font-size: 18px;
        }

        .detail-item .detail-text label {
            display: block;
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 3px;
        }

        .detail-item .detail-text p {
            margin: 0;
            font-size: 15px;
            font-weight: 600;
            color: #1a1a2e;
        }

        /* Trip Details Grid */
        .trip-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .trip-card {
            background: #fff;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            opacity: 0.85;
        }

        .trip-card .card-icon {
            width: 55px;
            height: 55px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .trip-card .card-icon.date {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            color: #1976d2;
        }

        .trip-card .card-icon.time {
            background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
            color: #f57c00;
        }

        .trip-card .card-icon.location {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            color: #388e3c;
        }

        .trip-card .card-icon.passengers {
            background: linear-gradient(135deg, #fce4ec 0%, #f8bbd9 100%);
            color: #c2185b;
        }

        .trip-card .card-icon.vehicle {
            background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
            color: #0097a7;
        }

        .trip-card h4 {
            font-size: 13px;
            color: #6c757d;
            margin: 0 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .trip-card p {
            font-size: 17px;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0;
        }

        .trip-card p.small {
            font-size: 13px;
            font-weight: 500;
            color: #495057;
            margin-top: 5px;
        }

        /* Refund Status */
        .refund-card {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            border-left: 5px solid #28a745;
        }

        .refund-card.pending {
            background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
            border-left-color: #ff9800;
        }

        .refund-card h4 {
            margin: 0 0 10px 0;
            color: #155724;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .refund-card.pending h4 {
            color: #856404;
        }

        .refund-card p {
            margin: 0;
            color: #155724;
            font-size: 14px;
        }

        .refund-card.pending p {
            color: #856404;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .status-banner {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .trip-grid {
                grid-template-columns: 1fr;
            }
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
        <li><a href="/CeylonGo/public/transporter/pending"><i class="fa-regular fa-clock"></i> Pending Bookings</a></li>
        <li class="active"><a href="/CeylonGo/public/transporter/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="/CeylonGo/public/transporter/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/transporter/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      
      <!-- Page Header -->
      <div class="page-header">
        <h1><i class="fa-solid fa-ban"></i> Cancelled Booking Details</h1>
        <a href="/CeylonGo/public/transporter/cancelled" class="back-btn">
          <i class="fa-solid fa-arrow-left"></i> Back to Cancelled
        </a>
      </div>

      <!-- Status Banner -->
      <div class="status-banner">
        <div class="status-info">
          <i class="fa-solid fa-circle-xmark"></i>
          <div>
            <h3>Booking Cancelled</h3>
            <p>This booking has been cancelled and is no longer active</p>
          </div>
        </div>
        <span class="booking-id">#BK-54321</span>
      </div>

      <!-- Cancellation Reason -->
      <div class="cancellation-card">
        <h4><i class="fa-solid fa-comment-dots"></i> Cancellation Reason</h4>
        <p>Customer cancelled due to change in travel plans. They mentioned that their flight was rescheduled and they will be arriving on a different date. They apologize for any inconvenience caused.</p>
        <div class="cancelled-date">
          <i class="fa-regular fa-calendar"></i> Cancelled on: January 5, 2026 at 02:30 PM
        </div>
      </div>

      <!-- Customer Information -->
      <div class="info-card">
        <h3><i class="fa-solid fa-user-circle"></i> Customer Information</h3>
        <div class="details-grid">
          <div class="detail-item">
            <div class="icon-box"><i class="fa-solid fa-user"></i></div>
            <div class="detail-text">
              <label>Customer Name</label>
              <p>Emma Rajapaksa</p>
            </div>
          </div>
          <div class="detail-item">
            <div class="icon-box"><i class="fa-solid fa-phone"></i></div>
            <div class="detail-text">
              <label>Contact Number</label>
              <p>+94 77 456 7890</p>
            </div>
          </div>
          <div class="detail-item">
            <div class="icon-box"><i class="fa-solid fa-envelope"></i></div>
            <div class="detail-text">
              <label>Email Address</label>
              <p>emma.r@email.com</p>
            </div>
          </div>
          <div class="detail-item">
            <div class="icon-box"><i class="fa-solid fa-globe"></i></div>
            <div class="detail-text">
              <label>Country</label>
              <p>Australia</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Original Trip Details -->
      <div class="info-card">
        <h3><i class="fa-solid fa-route"></i> Original Trip Details</h3>
      </div>

      <div class="trip-grid">
        <div class="trip-card">
          <div class="card-icon date"><i class="fa-regular fa-calendar"></i></div>
          <h4>Scheduled Date</h4>
          <p>January 10, 2026</p>
          <p class="small">Saturday</p>
        </div>

        <div class="trip-card">
          <div class="card-icon time"><i class="fa-regular fa-clock"></i></div>
          <h4>Pickup Time</h4>
          <p>09:00 AM</p>
          <p class="small">Morning pickup</p>
        </div>

        <div class="trip-card">
          <div class="card-icon location"><i class="fa-solid fa-location-dot"></i></div>
          <h4>Pickup Location</h4>
          <p>Galle Face Hotel, Colombo</p>
          <p class="small">Main entrance</p>
        </div>

        <div class="trip-card">
          <div class="card-icon location"><i class="fa-solid fa-flag-checkered"></i></div>
          <h4>Drop-off Location</h4>
          <p>Yala National Park</p>
          <p class="small">Safari entrance</p>
        </div>

        <div class="trip-card">
          <div class="card-icon passengers"><i class="fa-solid fa-user-group"></i></div>
          <h4>Passengers</h4>
          <p>4 Adults</p>
          <p class="small">No children</p>
        </div>

        <div class="trip-card">
          <div class="card-icon vehicle"><i class="fa-solid fa-van-shuttle"></i></div>
          <h4>Vehicle Type</h4>
          <p>Van (AC)</p>
          <p class="small">8 seater capacity</p>
        </div>
      </div>

      <!-- Refund Status -->
      <div class="refund-card pending">
        <h4><i class="fa-solid fa-clock"></i> Refund Status: Processing</h4>
        <p>The refund is currently being processed. Customer will receive their refund within 5-7 business days. Refund amount: LKR 15,000</p>
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
