<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ceylon Go | Hotel Portal – Availability</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,600;14..32,700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
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
      <a href="../tourist/tourist_dashboard.php" class="btn-login">Logout</a>
    </nav>
  </header>

  <aside class="sidebar">
    <div class="brand">
      <div class="brand-text">Ceylon Go</div>
    </div>
    <nav class="nav">
      <a class="nav-link" href="dashboard.php">Dashboard</a>
      <a class="nav-link active" href="availability.php">Availability</a>
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
        <h1 class="page-title">Availability</h1>
        <div class="hotel-name" id="hotelName">Ocean Breeze Hotel</div>
      </div>
      <div class="right">
        <div class="datetime" id="currentDateTime">--</div>
      </div>
    </header>

    <section class="content">
      <div class="profile-actions">
        <div style="margin-right:auto" class="muted">Manage room availability by date</div>
        <input type="date" id="availStartDate" class="form-control" />
        <select id="availRoomType" class="form-control">
          <option>Single</option>
          <option>Double</option>
          <option>Suite</option>
          <option>Deluxe</option>
        </select>
        <button id="toggleAvailabilityBtn" class="btn btn-primary">Toggle Selected</button>
      </div>

      <div id="availSaving" class="success-banner" style="display:none;">Saving changes...</div>

      <div class="panel">
        <div class="panel-header">
          <h2>Next 14 Days</h2>
        </div>
        <div class="panel-body">
          <div class="table-wrap">
            <table class="table" id="availabilityTable">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Single</th>
                  <th>Double</th>
                  <th>Suite</th>
                  <th>Deluxe</th>
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


