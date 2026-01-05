<?php
// views/guide/guide_dashboard.php

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Tour Guide Dashboard</title>
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

  <!-- Font Awesome for icons -->
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
        <li><a href="pending.php"><i class="fa-regular fa-clock"></i> Pending Requests</a></li>
        <li><a href="cancelled.php"><i class="fa-solid fa-xmark"></i> Cancelled Tours</a></li>
        <li><a href="review.php"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="profile.php"><i class="fa-regular fa-user"></i>Profile Management</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Welcome Section -->
      <div class="welcome">
        <div class="profile-box">
          <h1>Welcome, Priya Silva !</h1>
          <h5>Overview of your tour guide services</h2>
        </div>
      </div>

      <!-- Summary Overview -->
       <div class="summary">
        <div class="summary-card">
          <h2>25</h2>
          <p>Total Tours</p>
        </div>
        </div>
      <div class="summary">
        <div class="summary-card">
          <h2>8</h2>
          <p>Upcoming Tours</p>
        </div>
        <div class="summary-card">
          <h2>3</h2>
          <p>Pending Requests</p>
        </div>
        <div class="summary-card">
          <h2>2</h2>
          <p>Cancelled Tours</p>
        </div>
        <div class="summary-card">
          <h2>12</h2>
          <p>Completed Tours</p>
        </div>
      </div>

      <!-- Performance Chart -->
      <div class="chart-container">
        <h3>Performance Overview</h3>
        <canvas id="performanceChart"></canvas>
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

  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    // Performance Chart
    const ctx = document.getElementById('performanceChart').getContext('2d');
    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Successful', 'Cancelled'],
        datasets: [{
          label: 'Success Rate',
          data: [12, 2],
          backgroundColor: ['#4caf50', '#f44336'],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });
  </script>
</body>
</html>
