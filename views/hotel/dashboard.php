<?php
  // Optional: include session/auth and shared header/footer if available
  // @example
  // require_once __DIR__ . '/../includes/session.php';
  // include __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ceylon Go • Hotel Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../public/css/hotel/style.css" />
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <header class="navbar">
    <div class="branding">
      <img src="../../public/images/logo.png" alt="Ceylon Go Logo" class="logo-img">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="dashboard.php">Home</a>
      <a href="../tourist/tourist_dashboard.php" class="btn-login">Logout</a>
    </nav>
  </header>

  <aside class="sidebar">
    <div class="brand">
      <div class="brand-text">Ceylon Go</div>
    </div>
    <nav class="nav">
      <a class="nav-link active" href="dashboard.php">Dashboard</a>
      <a class="nav-link" href="availability.php">Availability</a>
      <a class="nav-link" href="bookings.php">Bookings</a>
      <a class="nav-link" href="add_room.php">Booking Management</a>
      <a class="nav-link" href="payments.php">Payments</a>
      <a class="nav-link" href="reviews.php">Reviews</a>
      <a class="nav-link" href="inquiries.php">Inquiries</a>
      <a class="nav-link" href="report_issue.php">Report Issue</a>
      <a class="nav-link" href="notifications.php">Notifications</a>
    </nav>
  </aside>

  <div class="main">
    <header class="topbar">
      <div class="left">
        <h1 class="page-title">Dashboard</h1>
        <div class="hotel-name" id="hotelName">Ocean Breeze Hotel</div>
      </div>
      <div class="right">
        <div class="datetime" id="currentDateTime">--</div>
      </div>
    </header>

    <section class="content">
      <div class="cards-grid">
        <div class="card stat-card" id="statTotalBookings">
          <div class="stat-label">Total Bookings</div>
          <div class="stat-value" data-key="totalBookings">20</div>
        </div>
        <div class="card stat-card" id="statPendingRequests">
          <div class="stat-label">Pending Requests</div>
          <div class="stat-value" data-key="pendingRequests">6</div>
        </div>
        <div class="card stat-card" id="statTotalReviews">
          <div class="stat-label">Total Reviews</div>
          <div class="stat-value" data-key="totalReviews">16</div>
        </div>
        <div class="card stat-card" id="statTotalEarnings">
          <div class="stat-label">Total Earnings</div>
          <div class="stat-value" data-key="totalEarnings">256987.67</div>
        </div>
      </div>

      <div class="grid-2">
        <div class="panel">
          <div class="panel-header">
            <h2>Recent Bookings</h2>
          </div>
          <div class="panel-body table-wrap">
            <table class="table" id="bookingsTable">
              <thead>
                <tr>
                  <th>Booking ID</th>
                  <th>Guest</th>
                  <th>Check-in</th>
                  <th>Check-out</th>
                  <th>Amount</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <!-- Rows injected by JS -->
              </tbody>
            </table>
          </div>
        </div>

        <div class="panel">
          <div class="panel-header">
            <h2>Notifications</h2>
          </div>
          <div class="panel-body notifications" id="notificationsList">
            <!-- Notifications injected by JS -->
          </div>
        </div>
      </div>

      <div class="panel">
        <div class="panel-header">
          <h2>Monthly Revenue</h2>
        </div>
        <div class="panel-body chart-wrap">
          <canvas id="revenueChart" height="110"></canvas>
        </div>
      </div>
    </section>
  </div>

  <script src="../../public/js/hotel.js"></script>
<?php
  // Optional: include shared footer if available
  // include __DIR__ . '/../includes/footer.php';
?>
</body>
</html>


