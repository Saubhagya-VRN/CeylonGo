<?php require_once 'session_init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Pending Requests</title>
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/base.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/navbar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/sidebar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/cards.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/buttons.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/tables.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/footer.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/responsive.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .btn-accept, .btn-reject {
      padding: 8px 12px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
      margin-right: 5px;
      transition: all 0.3s ease;
    }
    .btn-accept {
      background: #28a745;
      color: white;
    }
    .btn-accept:hover {
      background: #218838;
    }
    .btn-reject {
      background: #dc3545;
      color: white;
    }
    .btn-reject:hover {
      background: #c82333;
    }
    .status-badge.pending {
      background: #ffc107;
      color: #333;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
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
  
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="page-wrapper">
    <div class="sidebar" id="sidebar">
      <ul>
        <li><a href="/CeylonGo/public/guide/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="/CeylonGo/public/guide/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Tours</a></li>
        <li class="active"><a href="/CeylonGo/public/guide/pending"><i class="fa-regular fa-clock"></i> Pending Requests</a></li>
        <li><a href="/CeylonGo/public/guide/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Tours</a></li>
        <li><a href="/CeylonGo/public/guide/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="/CeylonGo/public/guide/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/guide/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </div>

    <div class="main-content">
      <h2 class="page-title"><i class="fa-regular fa-clock"></i> Pending Requests</h2>

      <!-- Desktop Table View -->
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Booking No</th>
              <th>Tourist Name</th>
              <th>Date</th>
              <th>Time</th>
              <th>Location</th>
              <th>Tour Type</th>
              <th>People</th>
              <th>Status</th>
              <th>Actions</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#TG010</td>
              <td>Robert Anderson</td>
              <td>2026-02-15</td>
              <td>09:00 AM</td>
              <td>Ella Nine Arches Bridge</td>
              <td>Nature Tour</td>
              <td>4</td>
              <td><span class="status-badge pending">Pending</span></td>
              <td>
                <button class="btn-accept" title="Accept"><i class="fa-solid fa-check"></i> Accept</button>
                <button class="btn-reject" title="Reject"><i class="fa-solid fa-xmark"></i> Reject</button>
              </td>
              <td><a href="/CeylonGo/public/guide/pending_info" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a></td>
            </tr>
            <tr>
              <td>#TG011</td>
              <td>Linda Thompson</td>
              <td>2026-02-18</td>
              <td>06:00 AM</td>
              <td>Horton Plains</td>
              <td>Adventure Tour</td>
              <td>6</td>
              <td><span class="status-badge pending">Pending</span></td>
              <td>
                <button class="btn-accept" title="Accept"><i class="fa-solid fa-check"></i> Accept</button>
                <button class="btn-reject" title="Reject"><i class="fa-solid fa-xmark"></i> Reject</button>
              </td>
              <td><a href="/CeylonGo/public/guide/pending_info" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a></td>
            </tr>
            <tr>
              <td>#TG012</td>
              <td>James Wilson</td>
              <td>2026-02-20</td>
              <td>08:00 AM</td>
              <td>Polonnaruwa Ancient City</td>
              <td>Historical Tour</td>
              <td>3</td>
              <td><span class="status-badge pending">Pending</span></td>
              <td>
                <button class="btn-accept" title="Accept"><i class="fa-solid fa-check"></i> Accept</button>
                <button class="btn-reject" title="Reject"><i class="fa-solid fa-xmark"></i> Reject</button>
              </td>
              <td><a href="/CeylonGo/public/guide/pending_info" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile Card View -->
      <div class="booking-cards">
        <div class="booking-card-item" style="border-left-color: #ffc107;">
          <div class="card-header">
            <span class="booking-no">#TG010</span>
            <span class="status-badge pending">Pending</span>
          </div>
          <div class="card-body">
            <div class="card-row"><i class="fa-solid fa-user"></i><span class="label">Tourist:</span><span>Robert Anderson</span></div>
            <div class="card-row"><i class="fa-regular fa-calendar"></i><span class="label">Date:</span><span>2026-02-15</span></div>
            <div class="card-row"><i class="fa-regular fa-clock"></i><span class="label">Time:</span><span>09:00 AM</span></div>
            <div class="card-row"><i class="fa-solid fa-location-dot"></i><span class="label">Location:</span><span>Ella Nine Arches Bridge</span></div>
            <div class="card-row"><i class="fa-solid fa-map"></i><span class="label">Tour:</span><span>Nature Tour</span></div>
          </div>
          <div class="card-actions">
            <button class="btn-accept"><i class="fa-solid fa-check"></i> Accept</button>
            <button class="btn-reject"><i class="fa-solid fa-xmark"></i> Reject</button>
            <a href="/CeylonGo/public/guide/pending_info" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="booking-card-item" style="border-left-color: #ffc107;">
          <div class="card-header">
            <span class="booking-no">#TG011</span>
            <span class="status-badge pending">Pending</span>
          </div>
          <div class="card-body">
            <div class="card-row"><i class="fa-solid fa-user"></i><span class="label">Tourist:</span><span>Linda Thompson</span></div>
            <div class="card-row"><i class="fa-regular fa-calendar"></i><span class="label">Date:</span><span>2026-02-18</span></div>
            <div class="card-row"><i class="fa-regular fa-clock"></i><span class="label">Time:</span><span>06:00 AM</span></div>
            <div class="card-row"><i class="fa-solid fa-location-dot"></i><span class="label">Location:</span><span>Horton Plains</span></div>
            <div class="card-row"><i class="fa-solid fa-map"></i><span class="label">Tour:</span><span>Adventure Tour</span></div>
          </div>
          <div class="card-actions">
            <button class="btn-accept"><i class="fa-solid fa-check"></i> Accept</button>
            <button class="btn-reject"><i class="fa-solid fa-xmark"></i> Reject</button>
            <a href="/CeylonGo/public/guide/pending_info" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    hamburgerBtn.addEventListener('click', function() {
      hamburgerBtn.classList.toggle('active');
      sidebar.classList.toggle('active');
      sidebarOverlay.classList.toggle('active');
    });

    sidebarOverlay.addEventListener('click', function() {
      hamburgerBtn.classList.remove('active');
      sidebar.classList.remove('active');
      sidebarOverlay.classList.remove('active');
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
  <!-- Footer -->
  <footer>
    <ul>
      <li><a href="#">About Us</a></li>
      <li><a href="#">Contact Us</a></li>
    </ul>
  </footer>
</body>
</html>
