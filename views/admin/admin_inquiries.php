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
    <link rel="stylesheet" href="/CeylonGO/public/css/admin/admin_inquiries.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/admin/admin_common.css">

    <!-- Responsive styles (always last) -->
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">

    <title>Inquiry Management</title>
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
          <li class="active"><a href="/CeylonGo/public/admin/inquiries"><i class="fa-solid fa-circle-question"></i> Inquiries</a></li>
          <li><a href="/CeylonGo/public/admin/promotions"><i class="fa-solid fa-bullhorn"></i> Promotions</a></li>
          <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-solid fa-star"></i> Reviews</a></li>
          <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports</a></li>
          <li><a href="/CeylonGo/public/admin/settings"><i class="fa-solid fa-gear"></i> Settings</a></li>
        </ul>
      </div>

      <div class="main-content">
          <div class="inquiry-management">

              <h2 class="page-title">Inquiry Management</h2>
              <br>

              <div class="toolbar">
                  <div class="search-section">
                    <input type="text" placeholder="Search by user or subject" class="search-input">
                    <button class="search-btn">🔍</button>
                  </div>
                  <div class="filter-buttons">
                    <button class="filter-btn active">All</button>
                    <button class="filter-btn">Pending</button>
                    <button class="filter-btn">Resolved</button>
                  </div>
              </div>

              <div class="stats-section">
                  <h4>Inquiry Statistics</h4>
                  <div class="stats-grid">
                    <div class="stat-box">
                        <strong>Total Inquiries</strong><br>
                        <span>350</span>
                    </div>
                    <div class="stat-box">
                        <strong>Pending</strong><br>
                        <span>120</span>
                    </div>
                    <div class="stat-box">
                        <strong>Resolved</strong><br>
                        <span>200</span>
                    </div>
                    <div class="stat-box">
                        <strong>Escalated</strong><br>
                        <span>30</span>
                    </div>
                  </div>
              </div>
              <br>

              <div class="inquiries-section">
                  <table class="inquiry-table">
                    <thead>
                        <tr>
                          <th>User</th>
                          <th>Subject</th>
                          <th>Status</th>
                          <th>Date</th>
                          <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                          <td>John Doe</td>
                          <td>Did not receive refund</td>
                          <td><span class="status pending">Pending</span></td>
                          <td>2025-08-20</td>
                          <td class="actions">
                              <button class="icon-btn">👁️</button>
                              <button class="icon-btn">✏️</button>
                              <button class="icon-btn">✅</button>
                          </td>
                        </tr>
                        <tr>
                          <td>Jane Smith</td>
                          <td>Issue with delivery</td>
                          <td><span class="status resolved">Resolved</span></td>
                          <td>2025-08-19</td>
                          <td class="actions">
                              <button class="icon-btn">👁️</button>
                              <button class="icon-btn">✏️</button>
                          </td>
                        </tr>
                        <tr>
                          <td>Mark Lee</td>
                          <td>Complaint not addressed</td>
                          <td><span class="status resolved">Resolved</span></td>
                          <td>2025-08-18</td>
                          <td class="actions">
                              <button class="icon-btn">👁️</button>
                              <button class="icon-btn">✏️</button>
                          </td>
                        </tr>
                    </tbody>
                  </table>
              </div>

              <div class="footer-buttons">
                  <button class="footer-btn black">Export Inquiries</button>
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
