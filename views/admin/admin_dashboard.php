<?php
  session_start();
  include("../../config/database.php");

  // If not logged in - redirect to login
  if (!isset($_SESSION['admin_id'])) {
      header("Location: admin_login.php");
      exit();
  }

  $admin_id = $_SESSION['admin_id'];

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
  $sqlUsers = "SELECT COUNT(*) AS total FROM admin_user";
  $resultUsers = $conn->query($sqlUsers);
  if ($resultUsers && $row = $resultUsers->fetch_assoc()) {
      $totalUsers = $row['total'];
  }

  $conn->close();
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
        <li><a href="admin_dashboard.php" class="active">Home</a></li>
        <li><a href="admin_user.php">Users</a></li>
        <li><a href="admin_bookings.php">Bookings</a></li>
        <li><a href="admin_service.php">Service Providers</a></li>
        <li><a href="admin_payments.php">Payments</a></li>
        <li><a href="admin_reports.php">Reports</a></li>
        <li><a href="admin_reviews.php">Reviews</a></li>
        <li><a href="admin_inquiries.php">Inquiries</a></li>
        <li><a href="admin_settings.php">System Settings</a></li>
        <li><a href="admin_promotions.php">Promotions</a></li>
        <li><a href="../../controllers/admin/logout.php">Logout</a></li>
      </ul>
    </aside>
 
    <!-- Main Content -->
    <div class="main-content">
      <header class="navbar">
        <div class="profile-info">
          <div>
            <h2><?= htmlspecialchars($admin_name) ?></h2>
            <span class="role"><?= htmlspecialchars($admin_role) ?></span>
          </div>
        </div>
        <div class="profile-buttons">
          <button class="btn-black"><a href="admin_profile.php">View/ Edit/ Delete Profile</a></button>
        </div>
      </header>

      <section class="summary-overview">
        <h3>Summary Overview</h3>
        <div class="stats">
          <div class="stat"><li><a href="admin_user.php"><h4>Total Users</h4></a></li><p><?= $totalUsers ?></p></div>
          <div class="stat"><h4>Active Providers</h4><p>58</p></div>
          <div class="stat"><h4>New Bookings</h4><p>150</p></div>
          <div class="stat"><h4>Total Payments</h4><p>LKR 10,250</p></div>
          <div class="stat"><h4>Refund Requests</h4><p>5</p></div>
          <div class="stat"><h4>Pending Validations</h4><p>12</p></div>
        </div>
      </section>

      <section class="recent">
        <h3>Recent Inquiries</h3>
        <div class="list">
          <div class="item">Inquiry #123 - Fathima Zara <span>Pending</span></div>
          <div class="item">Inquiry #124 - Jane Roe <span>Resolved</span></div>
          <div class="item">Inquiry #125 - Mark Smith <span>Pending</span></div>
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
          <li><a href="bookings.html">View All Bookings</a></li>
          <li><a href="admin_settings.html">Update Settings</a></li>
          <li><a href="admin_reports.html">Generate Report</a></li>
          <li><a href="admin_payments.html">Payments</a></li>
        </ul>
      </footer>
    </div>
  </body>
</html>
