<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Font Awesome (REQUIRED) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Shared Transport Layout -->
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/base.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/navbar.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/sidebar.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/footer.css">

    <!-- Optional admin-only overrides -->
    <link rel="stylesheet" href="/CeylonGO/public/css/admin/admin_overrides.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/admin/admin_promotions.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/admin/admin_common.css">

    <!-- Responsive styles (always last) -->
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">

    <title>Promotions & Discounts</title>
  </head>

  <body>
    <!-- Navbar -->
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

      <!-- Sidebar -->
      <div class="sidebar">
        <ul>
          <li><a href="/CeylonGo/public/admin/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
          <li><a href="/CeylonGo/public/admin/users"><i class="fa-solid fa-users"></i> Users</a></li>
          <li><a href="/CeylonGo/public/admin/bookings"><i class="fa-regular fa-calendar"></i> Bookings</a></li>
          <li><a href="/CeylonGo/public/admin/service"><i class="fa-solid fa-van-shuttle"></i> Service Providers</a></li>
          <li><a href="/CeylonGo/public/admin/payments"><i class="fa-solid fa-credit-card"></i> Payments</a></li>
          <li><a href="/CeylonGo/public/admin/inquiries"><i class="fa-solid fa-circle-question"></i> Inquiries</a></li>
          <li class="active"><a href="/CeylonGo/public/admin/promotions"><i class="fa-solid fa-bullhorn"></i> Promotions</a></li>
          <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-solid fa-star"></i> Reviews</a></li>
          <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports</a></li>
          <li><a href="/CeylonGo/public/admin/settings"><i class="fa-solid fa-gear"></i> Settings</a></li>
        </ul>
      </div>

      <div class="main-content">
          <div class="user-management">

              <h2 class="page-title">Promotions & Discounts</h2>
              <br>

              <div class="users-section">
                  <table class="user-table promo-table">
                    <thead>
                      <tr>
                        <th>Promotion</th>
                        <th>Details</th>
                        <th>Status</th>
                        <th>End Date</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>🔥 Summer Sale</td>
                        <td>20% Off All Products</td>
                        <td><span class="promo-status expired">Ended</span></td>
                        <td>08/31/2023</td>
                        <td class="actions">
                          <button class="icon-btn">✏️</button>
                          <button class="icon-btn danger">❌</button>
                        </td>
                      </tr>
                      <tr>
                        <td>🎉 Buy One Get One Free</td>
                        <td>On selected items</td>
                        <td><span class="promo-status expired">Ended</span></td>
                        <td>09/15/2023</td>
                        <td class="actions">
                          <button class="icon-btn">✏️</button>
                          <button class="icon-btn danger">❌</button>
                        </td>
                      </tr>
                      <tr>
                        <td>🚚 Free Shipping</td>
                        <td>On orders over $50</td>
                        <td><span class="promo-status ongoing">Ongoing</span></td>
                        <td>-</td>
                        <td class="actions">
                          <button class="icon-btn">✏️</button>
                          <button class="icon-btn danger">❌</button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
              </div>

              <div class="form-section">
                <h3>Add New Promotion</h3><br>
                <div class="form-group">
                  <label for="promo-title">Title</label>
                  <input type="text" id="promo-title" placeholder="Enter promotion title">
                </div>
                <div class="form-group">
                  <label for="discount">Discount %</label>
                  <input type="number" id="discount" placeholder="Enter discount percentage">
                </div>
                <div class="form-group">
                  <label for="start-date">Start Date</label>
                  <input type="date" id="start-date">
                </div>
                <div class="form-group">
                  <label for="end-date">End Date</label>
                  <input type="date" id="end-date">
                </div>

                <label>Conditions</label>
                <div class="conditions">
                  <button class="condition-btn">Minimum Purchase Amount</button>
                  <button class="condition-btn">Selected Products Only</button>
                  <button class="condition-btn">New Customers Only</button>
                  <button class="condition-btn">All Customers</button>
                </div>
              </div>

              <div class="footer-buttons">
                  <button class="footer-btn">Cancel</button>
                  <button class="footer-btn black">Save Promotion</button>
              </div>
          </div>
      </div>
    </div>

    <!-- Footer -->
    <footer>
      <ul>
        <li><a href="/CeylonGo/public/admin/bookings">View All Bookings</a></li>
        <li><a href="/CeylonGo/public/admin/settings">Update Settings</a></li>
        <li><a href="/CeylonGo/public/admin/reports">Generate Report</a></li>
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
