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
      color: #3d8b40;
    }

    .back-btn {
      padding: 10px 20px;
      background: #6c757d;
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
      background: #5a6268;
    }

    /* Status Banner */
    .status-banner {
      background: #28a745;
      color: #fff;
      padding: 15px 25px;
      border-radius: 12px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
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

    /* Tourist Info Card */
    .tourist-card {
      background: #fff;
      border-radius: 16px;
      padding: 25px;
      margin-bottom: 25px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      border: 1px solid #e9ecef;
    }

    .tourist-card h3 {
      font-size: 18px;
      color: #1a1a2e;
      margin: 0 0 20px 0;
      display: flex;
      align-items: center;
      gap: 10px;
      padding-bottom: 15px;
      border-bottom: 2px solid #f0f0f0;
    }

    .tourist-card h3 i {
      color: #3d8b40;
    }

    .tourist-details {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
    }

    .tourist-detail-item {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .tourist-detail-item .icon-box {
      width: 45px;
      height: 45px;
      background: rgba(61, 139, 64, 0.1);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #3d8b40;
      font-size: 18px;
    }

    .tourist-detail-item .detail-text label {
      display: block;
      font-size: 12px;
      color: #6c757d;
      margin-bottom: 3px;
    }

    .tourist-detail-item .detail-text p {
      margin: 0;
      font-size: 15px;
      font-weight: 600;
      color: #1a1a2e;
    }

    /* Tour Details Grid */
    .tour-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .tour-card {
      background: #fff;
      border-radius: 16px;
      padding: 25px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      border: 1px solid #e9ecef;
      transition: all 0.3s ease;
    }

    .tour-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .tour-card .card-icon {
      width: 60px;
      height: 60px;
      border-radius: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 26px;
      margin-bottom: 18px;
    }

    .tour-card .card-icon.date {
      background: rgba(25, 118, 210, 0.1);
      color: #1976d2;
    }

    .tour-card .card-icon.time {
      background: rgba(245, 124, 0, 0.1);
      color: #f57c00;
    }

    .tour-card .card-icon.location {
      background: rgba(56, 142, 60, 0.1);
      color: #388e3c;
    }

    .tour-card .card-icon.people {
      background: rgba(194, 24, 91, 0.1);
      color: #c2185b;
    }

    .tour-card .card-icon.tour-type {
      background: rgba(123, 31, 162, 0.1);
      color: #7b1fa2;
    }

    .tour-card .card-icon.duration {
      background: rgba(0, 151, 167, 0.1);
      color: #0097a7;
    }

    .tour-card h4 {
      font-size: 13px;
      color: #6c757d;
      margin: 0 0 8px 0;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .tour-card p {
      font-size: 18px;
      font-weight: 700;
      color: #1a1a2e;
      margin: 0;
    }

    .tour-card p.small {
      font-size: 14px;
      font-weight: 500;
      color: #495057;
      margin-top: 5px;
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
      color: #3d8b40;
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
      background: linear-gradient(to bottom, #3d8b40, #66bb6a);
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
      background: #3d8b40;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-weight: 700;
      font-size: 16px;
      z-index: 1;
      box-shadow: 0 4px 15px rgba(61, 139, 64, 0.4);
      flex-shrink: 0;
    }

    .timeline-content {
      flex: 1;
      background: #f8f9fa;
      border-radius: 12px;
      padding: 20px;
      border-left: 4px solid #3d8b40;
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

    /* Notes Section */
    .notes-section {
      background: #fff9e6;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 30px;
      border-left: 4px solid #ffc107;
    }

    .notes-section h4 {
      margin: 0 0 10px 0;
      color: #856404;
      font-size: 15px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .notes-section p {
      margin: 0;
      color: #664d03;
      font-size: 14px;
    }

    /* Action Buttons */
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

    .action-btn.primary {
      background: #3d8b40;
      color: #fff;
      box-shadow: 0 4px 15px rgba(61, 139, 64, 0.3);
    }

    .action-btn.primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(61, 139, 64, 0.4);
    }

    .action-btn.danger {
      background: #dc3545;
      color: #fff;
      box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    }

    .action-btn.danger:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
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

      .tour-grid {
        grid-template-columns: 1fr;
      }

      .timeline::before {
        left: 20px;
      }

      .timeline-badge {
        width: 42px;
        height: 42px;
        font-size: 14px;
      }

      .action-buttons {
        flex-direction: column;
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
