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

  // Don't close connection - it's a singleton
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ceylon Go - Admin Dashboard</title>
    <link rel="stylesheet" href="../../public/css/admin/admin_dashboard.css">
  </head>

  <body>
    <aside class="sidebar">
      <div class="sidebar-brand">
        <img src="../../public/images/logo.png" alt="Ceylon Go Logo" class="logo-img">
        <h2>Ceylon Go</h2>
      </div>
      <ul class="sidebar-menu">
        <li><a href="/CeylonGo/public/admin/dashboard" class="active">Home</a></li>
        <li><a href="/CeylonGo/public/admin/users">Users</a></li>
        <li><a href="/CeylonGo/public/admin/bookings">Bookings</a></li>
        <li><a href="/CeylonGo/public/admin/service">Service Providers</a></li>
        <li><a href="/CeylonGo/public/admin/payments">Payments</a></li>
        <li><a href="/CeylonGo/public/admin/reports">Reports</a></li>
        <li><a href="/CeylonGo/public/admin/reviews">Reviews</a></li>
        <li><a href="/CeylonGo/public/admin/inquiries">Inquiries</a></li>
        <li><a href="/CeylonGo/public/admin/settings">System Settings</a></li>
        <li><a href="/CeylonGo/public/admin/promotions">Promotions</a></li>
        <li><a href="/CeylonGo/public/logout">Logout</a></li>
      </ul>
    </aside>
 
    <!-- Main Content -->
    <div class="main-content">
      <header class="navbar">
        <div class="profile-info">
          <div>
            <h2><?= htmlspecialchars($admin_name) ?></h2><br>
            <span class="role"><?= htmlspecialchars($admin_role) ?></span>
          </div>
        </div>
        <div class="profile-buttons">
          <button class="btn-black"><a href="/CeylonGo/public/admin/profile" class="profile-link">View/ Edit/ Delete Profile</a></button>
        </div>
      </header>

      <section class="summary-overview">
        <h3>Summary Overview</h3>
        <div class="stats">
          <div class="stat"><a href="/CeylonGo/public/admin/users" class="stat-link"><h4>Total Users</h4></a><p><?= $totalUsers ?></p></div>
          <div class="stat"><a href="/CeylonGo/public/admin/service" class="stat-link"><h4>Active Providers</h4></a><p>58</p></div>
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
      
      <footer>
        <ul>
          <li><a href="/CeylonGo/public/admin/bookings">View All Bookings</a></li>
          <li><a href="/CeylonGo/public/admin/settings">Update Settings</a></li>
          <li><a href="/CeylonGo/public/admin/reports">Generate Report</a></li>
          <li><a href="/CeylonGo/public/admin/payments">Payments</a></li>
        </ul>
      </footer>
    </div>
  </body>
</html>
