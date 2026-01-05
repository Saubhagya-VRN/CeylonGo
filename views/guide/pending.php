<?php
// views/guide/pending.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Tour Guide Pending Requests</title>
  <!-- Base styles -->
  <link rel="stylesheet" href="../../public/css/guide/base.css">
  <link rel="stylesheet" href="../../public/css/guide/navbar.css">
  <link rel="stylesheet" href="../../public/css/guide/sidebar.css">
  <link rel="stylesheet" href="../../public/css/guide/footer.css">
  
  <!-- Component styles -->
  <link rel="stylesheet" href="../../public/css/guide/cards.css">
  <link rel="stylesheet" href="../../public/css/guide/buttons.css">
  <link rel="stylesheet" href="../../public/css/guide/forms.css">
  
  <!-- Page-specific styles -->
  <link rel="stylesheet" href="../../public/css/guide/tables.css">
  <link rel="stylesheet" href="../../public/css/guide/profile.css">
  <link rel="stylesheet" href="../../public/css/guide/reviews.css">
  <link rel="stylesheet" href="../../public/css/guide/charts.css">

  <!-- Font Awesome for sidebar icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <!-- Navbar -->
  <header class="navbar">
    <div class="branding">
      <img src="../../public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
      <div class="logo-text">Ceylon Go</div>
    </div>
    
    <nav class="nav-links">
      <a href="guide_dashboard.php">Home</a>
      <a href="../tourist/tourist_dashboard.php">Logout</a>
      <img src="../../public/images/user.png" alt="User" class="profile-pic">
    </nav>
  </header>

  <div class="page-wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
      <ul>
        <li><a href="guide_dashboard.php"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="upcoming.php"><i class="fa-regular fa-calendar"></i> Upcoming Tours</a></li>
        <li class="active"><a href="pending.php"><i class="fa-regular fa-clock"></i> Pending Requests</a></li>
        <li><a href="cancelled.php"><i class="fa-solid fa-xmark"></i> Cancelled Tours</a></li>
        <li><a href="review.php"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="profile.php"><i class="fa-regular fa-user"></i> Manage Profile</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Welcome Section -->
      <div class="welcome">
        <h2>Pending Tour Requests</h2>     
      </div>

      <!-- Requests Table -->
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Request No</th>
              <th>Date</th>
              <th>Start Time</th>
              <th>Meeting Location</th>
              <th>Tour Duration</th>
              <th>Tour Type</th>
              <th>Manage Request</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#TR001</td>
              <td>2025-03-20</td>
              <td>09:00 AM</td>
              <td>Colombo Fort Railway Station</td>
              <td>Full Day</td>
              <td>Cultural Heritage</td>
              <td>
                <button class="accept-btn">Accept</button>
                <button class="reject-btn">Reject</button>
              </td>
              <td><a href="#">See More</a></td>
            </tr>
            <tr>
              <td>#TR002</td>
              <td>2025-08-25</td>
              <td>02:30 PM</td>
              <td>Kandy Temple of the Tooth</td>
              <td>Half Day</td>
              <td>Religious Sites</td>
              <td>
                <button class="accept-btn">Accept</button>
                <button class="reject-btn">Reject</button>
              </td>
              <td><a href="#">See More</a></td>
            </tr>
            <tr>
              <td>#TR003</td>
              <td>2025-09-30</td>
              <td>08:00 AM</td>
              <td>Sigiriya Rock Fortress</td>
              <td>Full Day</td>
              <td>Historical Sites</td>
              <td>
                <button class="accept-btn">Accept</button>
                <button class="reject-btn">Reject</button>
              </td>
              <td><a href="#">See More</a></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <ul>
      <li><a href="#">About Us</a></li>
      <li><a href="#">Contact Us</a></li>
    </ul>
  </footer>
</body>
</html>
