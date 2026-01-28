<?php
  // Session is already started in public/index.php
  // Use Database class instead of direct include
  require_once(__DIR__ . '/../../config/config.php');
  require_once(__DIR__ . '/../../core/Database.php');

  // ✅ Make sure only logged-in admins can view this
  if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
      header("Location: /CeylonGo/public/login");
      exit();
  }

  // Get database connection
  $conn = Database::getMysqliConnection();

  // ✅ Get the corresponding admin ref_id
  $admin_id = $_SESSION['user_ref_id']; // from users.ref_id

  $sql = "SELECT username, role FROM admin WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $admin_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $admin = $result->fetch_assoc();

  $admin_name = $admin['username'];
  $admin_role = $admin['role'];

  $stmt->close();

  // Total number of users
  $totalUsers = 0;
  $sqlUsers = "SELECT COUNT(*) AS total FROM tourist_users";
  $resultUsers = $conn->query($sqlUsers);
  if ($resultUsers && $row = $resultUsers->fetch_assoc()) {
      $totalUsers = $row['total'];
  }

  // Total number of pending bookings
  $totalPendingBookings = 0;
  $sqlPending = "SELECT COUNT(*) AS total FROM trip_bookings WHERE status = 'pending'";
  $resultPending = $conn->query($sqlPending);
  if ($resultPending && $row = $resultPending->fetch_assoc()) {
      $totalPendingBookings = $row['total'];
  }

  // Total number of active service providers
  $totalProviders = 0;
  $sqlProviders = "SELECT COUNT(*) AS total FROM users WHERE role IN ('guide', 'hotel', 'transport')";
  $resultProviders = $conn->query($sqlProviders);
  if ($resultProviders && $row = $resultProviders->fetch_assoc()) {
      $totalProviders = $row['total'];
  }
?>

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
    <link rel="stylesheet" href="/CeylonGO/public/css/admin/admin_dashboard.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/admin/admin_common.css">

    <!-- Responsive styles (always last) -->
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">

    <title>Ceylon Go - Admin Dashboard</title>
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
          <li class="active"><a href="/CeylonGo/public/admin/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
          <li><a href="/CeylonGo/public/admin/users"><i class="fa-solid fa-users"></i> Users</a></li>
          <li><a href="/CeylonGo/public/admin/bookings"><i class="fa-regular fa-calendar"></i> Bookings</a></li>
          <li><a href="/CeylonGo/public/admin/service"><i class="fa-solid fa-van-shuttle"></i> Service Providers</a></li>
          <li><a href="/CeylonGo/public/admin/payments"><i class="fa-solid fa-credit-card"></i> Payments</a></li>
          <li><a href="/CeylonGo/public/admin/inquiries"><i class="fa-solid fa-circle-question"></i> Inquiries</a></li>
          <li><a href="/CeylonGo/public/admin/promotions"><i class="fa-solid fa-bullhorn"></i> Packages</a></li>
          <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-solid fa-star"></i> Reviews</a></li>
          <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports</a></li>
        </ul>
      </div>
 
      <!-- Main Content -->
      <div class="main-content">
        <div class="dashboard-header">
          <h2 class="header-title">Admin Dashboard</h2>
          <div class="header-info">
            <h3><?= htmlspecialchars($admin_name) ?></h3>
            <h5><span class="role"><?= htmlspecialchars($admin_role) ?></span></h5>
          </div>
        </div>

        <section class="summary-overview">
          <h3>Summary Overview</h3>
          <div class="stats">
            <div class="stat"><a href="/CeylonGo/public/admin/users" class="stat-link"><h4>Total Users</h4></a><p><?= $totalUsers ?></p></div>
            <div class="stat"><a href="/CeylonGo/public/admin/service" class="stat-link"><h4>Active Service Providers</h4></a><p><?= $totalProviders ?></p></div>
            <div class="stat"><a href="/CeylonGo/public/admin/bookings" class="stat-link"><h4>Pending Bookings</h4></a><p><?= $totalPendingBookings ?></p></div>
            <div class="stat"><a href="/CeylonGo/public/admin/payments" class="stat-link"><h4>Total Payments</h4></a><p>LKR 10,250</p></div>
            <div class="stat"><a href="/CeylonGo/public/admin/users" class="stat-link"><h4>Refund Requests</h4><p>5</p></div>
            <div class="stat"><h4>Pending Validations</h4><p>12</p></div>
          </div>
        </section>

        <section class="recent">
          <h3>Recent Inquiries</h3>
          <div class="list">
            <div class="item">Inquiry 123 - Fathima Zara <span>Pending</span></div>
            <div class="item">Inquiry 124 - Jane Roe <span>Resolved</span></div>
            <div class="item">Inquiry 125 - Mark Smith <span>Pending</span></div>
          </div>
        </section>

        <section class="reviews">
          <h3>Latest Reviews</h3>
          <div class="review">Alice - Great service! ⭐⭐⭐⭐⭐</div>
          <div class="review">Bob - Very helpful support! ⭐⭐⭐⭐⭐</div>
          <div class="review">Jane - Good customer service! ⭐⭐⭐⭐⭐</div>
        </section>
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
