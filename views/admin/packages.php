<?php
  // Session check
  if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
      header("Location: /CeylonGo/public/login");
      exit();
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- KEEP EXISTING CSS -->
    <link rel="stylesheet" href="/CeylonGO/public/css/admin/packages.css">

    <link rel="stylesheet" href="/CeylonGO/public/css/transport/base.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/navbar.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/sidebar.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/footer.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">

    <title>Manage Packages</title>
  </head>

  <body>
    <!-- NAVBAR -->
    <header class="navbar">
      <div class="branding">
        <img src="/CeylonGo/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
        <div class="logo-text">Ceylon Go</div>
      </div>

      <nav class="nav-links">
        <a href="/CeylonGo/public/admin/dashboard">Home</a>
        <div class="profile-dropdown">
          <img src="/CeylonGo/public/images/profile.jpg" alt="User" class="profile-pic" onclick="toggleProfileDropdown()">
          <div class="profile-dropdown-menu" id="profileDropdown">
            <a href="/CeylonGo/public/admin/profile"><i class="fa-regular fa-user"></i> My Profile</a>
            <a href="/CeylonGo/public/logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
          </div>
        </div>
      </nav>
    </header>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="page-wrapper">
      <!-- SIDEBAR -->
      <div class="sidebar">
        <ul>
          <li><a href="/CeylonGo/public/admin/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
          <li><a href="/CeylonGo/public/admin/users"><i class="fa-solid fa-users"></i> Users</a></li>
          <li><a href="/CeylonGo/public/admin/bookings"><i class="fa-regular fa-calendar"></i> Bookings</a></li>
          <li><a href="/CeylonGo/public/admin/service"><i class="fa-solid fa-van-shuttle"></i> Service Providers</a></li>
          <li><a href="/CeylonGo/public/admin/payments"><i class="fa-solid fa-credit-card"></i> Payments</a></li>
          <li><a href="/CeylonGo/public/admin/inquiries"><i class="fa-solid fa-circle-question"></i> Inquiries</a></li>
          <li class="active"><a href="/CeylonGo/public/admin/packages"><i class="fa-solid fa-bullhorn"></i> Packages</a></li>
          <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-solid fa-star"></i> Reviews</a></li>
          <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports & Analysis</a></li>
        </ul>
      </div>

      <div class="main-content">
        <div class="user-management">

          <h2 class="page-title">Manage Tour Packages</h2>

          <!-- ================= TABLE ================= -->
          <div class="users-section">
            <table class="user-table promo-table">
              <thead>
                <tr>
                  <th>Image</th>
                  <th>Title</th>
                  <th>Location</th>
                  <th>Category</th>
                  <th>Price</th>
                  <th>Actions</th>
                </tr>
              </thead>

              <tbody>
                <?php foreach ($packages as $p): ?>
                <tr>
                <td><img src="<?= $p['image'] ?>" width="80"></td>
                <td><?= htmlspecialchars($p['title']) ?></td>
                <td><?= $p['location'] ?></td>
                <td><?= $p['category'] ?></td>
                <td>LKR <?= number_format($p['price']) ?></td>
                <td>
                <button class="icon-btn">✏️</button>
                <button class="icon-btn danger">❌</button>
                </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <!-- ================= ADD FORM ================= -->
          <div class="form-section">
            <h3>Add New Package</h3>

            <form method="POST" action="/CeylonGo/public/admin/packages">
              <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" required>
              </div>

              <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" required>
              </div>

              <div class="form-group">
                <label>Locations (comma separated)</label>
                <input type="text" name="locations">
              </div>

              <div class="form-group">
                <label>Duration</label>
                <input type="text" name="duration">
              </div>

              <div class="form-group">
                <label>Image URL</label>
                <input type="text" name="image">
              </div>

              <div class="form-group">
                <label>Category</label>
                <select name="category">
                  <option value="cultural">Cultural</option>
                  <option value="honeymoon">Honeymoon</option>
                  <option value="family">Family</option>
                  <option value="adventure">Adventure</option>
                  <option value="safari">Safari</option>
                  <option value="beach">Beach</option>
                </select>
              </div>

              <div class="form-group">
                <label>Price (LKR)</label>
                <input type="number" name="price">
              </div>

              <div class="form-group">
                <label>Overview</label>
                <textarea name="overview" placeholder="One per line"></textarea>
              </div>

              <div class="footer-buttons">
                <button class="footer-btn black">Save Package</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer>
      <ul>
        <li><a href="/CeylonGo/public/admin/bookings">View All Bookings</a></li>
        <li><a href="/CeylonGo/public/admin/reports">Generate Reports</a></li>
        <li><a href="/CeylonGo/public/admin/payments">Payments</a></li>
      </ul>
    </footer>
    
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
