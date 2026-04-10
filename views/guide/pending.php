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
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/pending.css">
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
        <li><a href="/CeylonGo/public/guide/report"><i class="fa-solid fa-chart-line"></i> Performance Report</a></li>
        <li><a href="/CeylonGo/public/guide/places"><i class="fa-solid fa-map-location-dot"></i> My Places</a></li>
        <li><a href="/CeylonGo/public/guide/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </div>

    <div class="main-content">
      <?php if (isset($_GET['success'])): ?>
        <div class="alert-success" style="background:#d4edda;color:#155724;padding:12px 20px;border-radius:8px;margin-bottom:16px;border:1px solid #c3e6cb;">
          <?= htmlspecialchars($_GET['success']) ?>
        </div>
      <?php endif; ?>
      <?php if (isset($_GET['error'])): ?>
        <div class="alert-error" style="background:#f8d7da;color:#721c24;padding:12px 20px;border-radius:8px;margin-bottom:16px;border:1px solid #f5c6cb;">
          <?= htmlspecialchars($_GET['error']) ?>
        </div>
      <?php endif; ?>
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
              <th>Language</th>
              <th>Status</th>
              <th>Actions</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($bookings)): ?>
              <?php foreach ($bookings as $booking): ?>
              <tr>
                <td>#TG<?= str_pad($booking['id'], 3, '0', STR_PAD_LEFT) ?></td>
                <td><?= htmlspecialchars($booking['customerName']) ?></td>
                <td><?= htmlspecialchars($booking['date']) ?></td>
                <td><?= date('h:i A', strtotime($booking['time'])) ?></td>
                <td><?= htmlspecialchars($booking['location']) ?></td>
                <td><?= htmlspecialchars($booking['language']) ?></td>
                <td><span class="status-badge pending">Pending</span></td>
                <td>
                  <form method="POST" action="/CeylonGo/public/guide/accept-booking" style="display:inline;">
                    <input type="hidden" name="request_id" value="<?= $booking['id'] ?>">
                    <button type="submit" class="btn-accept" title="Accept"><i class="fa-solid fa-check"></i> Accept</button>
                  </form>
                  <form method="POST" action="/CeylonGo/public/guide/reject-booking" style="display:inline;">
                    <input type="hidden" name="request_id" value="<?= $booking['id'] ?>">
                    <button type="submit" class="btn-reject" title="Reject"><i class="fa-solid fa-xmark"></i> Reject</button>
                  </form>
                </td>
                <td><a href="/CeylonGo/public/guide/pending_info?id=<?= $booking['id'] ?>" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a></td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="9" style="text-align: center; padding: 30px; color: #888;">No pending requests found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Mobile Card View -->
      <div class="booking-cards">
        <?php if (!empty($bookings)): ?>
          <?php foreach ($bookings as $booking): ?>
          <div class="booking-card-item" style="border-left-color: #ffc107;">
            <div class="card-header">
              <span class="booking-no">#TG<?= str_pad($booking['id'], 3, '0', STR_PAD_LEFT) ?></span>
              <span class="status-badge pending">Pending</span>
            </div>
            <div class="card-body">
              <div class="card-row"><i class="fa-solid fa-user"></i><span class="label">Tourist:</span><span><?= htmlspecialchars($booking['customerName']) ?></span></div>
              <div class="card-row"><i class="fa-regular fa-calendar"></i><span class="label">Date:</span><span><?= htmlspecialchars($booking['date']) ?></span></div>
              <div class="card-row"><i class="fa-regular fa-clock"></i><span class="label">Time:</span><span><?= date('h:i A', strtotime($booking['time'])) ?></span></div>
              <div class="card-row"><i class="fa-solid fa-location-dot"></i><span class="label">Location:</span><span><?= htmlspecialchars($booking['location']) ?></span></div>
              <div class="card-row"><i class="fa-solid fa-language"></i><span class="label">Language:</span><span><?= htmlspecialchars($booking['language']) ?></span></div>
            </div>
            <div class="card-actions">
              <form method="POST" action="/CeylonGo/public/guide/accept-booking" style="display:inline;">
                <input type="hidden" name="request_id" value="<?= $booking['id'] ?>">
                <button type="submit" class="btn-accept"><i class="fa-solid fa-check"></i> Accept</button>
              </form>
              <form method="POST" action="/CeylonGo/public/guide/reject-booking" style="display:inline;">
                <input type="hidden" name="request_id" value="<?= $booking['id'] ?>">
                <button type="submit" class="btn-reject"><i class="fa-solid fa-xmark"></i> Reject</button>
              </form>
              <a href="/CeylonGo/public/guide/pending_info?id=<?= $booking['id'] ?>" class="see-more-link">See More <i class="fa-solid fa-arrow-right"></i></a>
            </div>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="booking-card-item" style="text-align: center; padding: 30px; color: #888;">
            <p>No pending requests found.</p>
          </div>
        <?php endif; ?>
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
