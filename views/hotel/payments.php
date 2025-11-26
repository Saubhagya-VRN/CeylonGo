<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ceylon Go | Hotel Portal – Payments</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../public/css/hotel/style.css" />
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css" />
</head>
<body>
  <header class="navbar">
    <div class="branding">
      <img src="../../public/images/logo.png" alt="Ceylon Go Logo" class="logo-img">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="dashboard.php">Home</a>
      <a href="tourist_dashboard.php" class="btn-login">Logout</a>
    </nav>
  </header>

  <aside class="sidebar">
    <div class="brand">
      <div class="brand-text">Ceylon Go</div>
    </div>
    <nav class="nav">
      <a class="nav-link" href="dashboard.php">Dashboard</a>
      <a class="nav-link" href="availability.php">Availability</a>
      <a class="nav-link" href="bookings.php">Bookings</a>
      <a class="nav-link" href="add_room.php">Booking Management</a>
      <a class="nav-link active" href="payments.php">Payments</a>
      <a class="nav-link" href="reviews.php">Reviews</a>
      <a class="nav-link" href="inquiries.php">Inquiries</a>
      <a class="nav-link" href="report_issue.php">Report Issue</a>
      <a class="nav-link" href="notifications.php">Notifications</a>
    </nav>
  </aside>

  <div class="main">
    <header class="topbar">
      <div class="left">
        <h1 class="page-title">Payments</h1>
        <div class="hotel-name" id="hotelName">Ocean Breeze Hotel</div>
      </div>
      <div class="right">
        <div class="datetime" id="currentDateTime">--</div>
      </div>
    </header>

    <section class="content">
      <div class="cards-grid">
        <div class="card stat-card">
          <div class="stat-label">Gross Revenue</div>
          <div class="stat-value" id="payGross">$0.00</div>
        </div>
        <div class="card stat-card">
          <div class="stat-label">Commission</div>
          <div class="stat-value" id="payCommission">$0.00</div>
        </div>
        <div class="card stat-card">
          <div class="stat-label">Net Income</div>
          <div class="stat-value" id="payNet">$0.00</div>
        </div>
      </div>

      <div class="panel">
        <div class="panel-header">
          <h2>Payment History</h2>
        </div>
        <div class="panel-body">
          <div class="table-wrap">
            <table class="table" id="paymentsTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Date</th>
                  <th>Amount</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </div>

  <script src="../../public/js/hotel.js"></script>
</body>
</html>


