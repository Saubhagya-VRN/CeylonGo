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
            color: #ffc107;
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
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        /* Pending Status Banner */
        .status-banner {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: #212529;
            padding: 15px 25px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
        }

        .status-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .status-info i {
            font-size: 24px;
        }

        .status-info h3 {
            margin: 0;
            font-size: 18px;
        }

        .status-info p {
            margin: 5px 0 0 0;
            opacity: 0.8;
            font-size: 14px;
        }

        .booking-id {
            background: rgba(255, 255, 255, 0.4);
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        /* Awaiting Response Box */
        .awaiting-box {
            background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
            border: 2px dashed #ffc107;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            margin-bottom: 30px;
        }

        .awaiting-box i {
            font-size: 48px;
            color: #e0a800;
            margin-bottom: 15px;
        }

        .awaiting-box h3 {
            margin: 0 0 10px 0;
            color: #856404;
            font-size: 20px;
        }

        .awaiting-box p {
            margin: 0 0 20px 0;
            color: #664d03;
        }

        .awaiting-box .timer {
            font-size: 24px;
            font-weight: 700;
            color: #856404;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        /* Customer Info Card */
        .customer-card {
            background: #fff;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
        }

        .customer-card h3 {
            font-size: 18px;
            color: #1a1a2e;
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .customer-card h3 i {
            color: #ffc107;
        }

        .customer-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .customer-detail-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .customer-detail-item .icon-box {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e0a800;
            font-size: 18px;
        }

        .customer-detail-item .detail-text label {
            display: block;
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 3px;
        }

        .customer-detail-item .detail-text p {
            margin: 0;
            font-size: 15px;
            font-weight: 600;
            color: #1a1a2e;
        }

        /* Trip Details Grid */
        .trip-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .trip-card {
            background: #fff;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .trip-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .trip-card .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 18px;
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

        .trip-card .card-icon.duration {
            background: linear-gradient(135deg, #ede7f6 0%, #d1c4e9 100%);
            color: #7b1fa2;
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
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0;
        }

        .trip-card p.small {
            font-size: 14px;
            font-weight: 500;
            color: #495057;
            margin-top: 5px;
        }

        /* Notes Section */
        .notes-section {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #1976d2;
        }

        .notes-section h4 {
            margin: 0 0 10px 0;
            color: #1565c0;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .notes-section p {
            margin: 0;
            color: #0d47a1;
            font-size: 14px;
        }

        /* Floating Decision Panel */
        .decision-section {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 25px 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            z-index: 1000;
            max-width: 400px;
            animation: slideUp 0.4s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .decision-section h3 {
            margin: 0 0 18px 0;
            font-size: 16px;
            color: #1a1a2e;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
        }

        .decision-section h3 i {
            color: #ffc107;
            font-size: 18px;
        }

        .decision-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: nowrap;
        }

        .decision-btn {
            flex: 1;
            padding: 14px 20px;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .decision-btn.accept {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: #fff;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.35);
        }

        .decision-btn.accept:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.45);
        }

        .decision-btn.accept:active {
            transform: translateY(0) scale(0.98);
        }

        .decision-btn.accept i {
            font-size: 16px;
        }

        .decision-btn.reject {
            background: linear-gradient(135deg, #dc3545 0%, #e74c5e 100%);
            color: #fff;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.35);
        }

        .decision-btn.reject:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.45);
        }

        .decision-btn.reject:active {
            transform: translateY(0) scale(0.98);
        }

        .decision-btn.reject i {
            font-size: 16px;
        }

        /* Other Action Buttons */
        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .action-btn.secondary {
            background: #fff;
            color: #495057;
            border: 2px solid #dee2e6;
        }

        .action-btn.secondary:hover {
            background: #f8f9fa;
            border-color: #ced4da;
        }

        .action-btn.primary {
            background: linear-gradient(135deg, #0077b6 0%, #005a8d 100%);
            color: #fff;
            box-shadow: 0 4px 15px rgba(0, 119, 182, 0.3);
        }

        .action-btn.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 119, 182, 0.4);
        }

        /* Itinerary Section */
        .itinerary-section {
            background: #fff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            margin-bottom: 30px;
        }

        .itinerary-section h3 {
            font-size: 20px;
            color: #1a1a2e;
            margin: 0 0 25px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .itinerary-section h3 i {
            color: #ffc107;
        }

        .timeline {
            list-style: none;
            padding: 0;
            margin: 0;
            position: relative;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 24px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, #ffc107, #ff9800);
            border-radius: 3px;
        }

        .timeline-item {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
            position: relative;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-badge {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 16px;
            z-index: 1;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.4);
            flex-shrink: 0;
        }

        .timeline-content {
            flex: 1;
            background: linear-gradient(135deg, #fffbf0 0%, #fff 100%);
            border-radius: 12px;
            padding: 20px;
            border-left: 4px solid #ffc107;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .timeline-content h4 {
            margin: 0 0 10px 0;
            font-size: 17px;
            color: #1a1a2e;
        }

        .timeline-content p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
            line-height: 1.6;
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

            .decision-section {
                left: 15px;
                right: 15px;
                bottom: 15px;
                max-width: none;
            }

            .decision-buttons {
                flex-direction: row;
            }

            .decision-btn {
                padding: 12px 16px;
                font-size: 13px;
            }

            .action-buttons {
                flex-direction: column;
                padding-bottom: 120px;
            }

            .action-btn {
                justify-content: center;
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
