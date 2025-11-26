<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Transport Provider Dashboard</title>
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/base.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/navbar.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/sidebar.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/footer.css">
    
    <!-- Component styles -->
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/cards.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/buttons.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/forms.css">
    
    <!-- Page-specific styles -->
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/timeline.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/tables.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/profile.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/reviews.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/charts.css">

    <!-- Responsive styles (always last) -->
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">  
    
    <!-- Responsive styles (always last) -->
    <link rel="stylesheet" href="/CeylonGO/public/css/responsive.css">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <!-- Navbar -->
  <header class="navbar">
    <div class="branding">
      <img src="/CeylonGO/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="#">Home</a>
      <a href="/CeylonGo/views/tourist/tourist_dashboard.php">Logout</a>
     <img src="/CeylonGO/public/images/profile.jpg" alt="User" class="profile-pic">
    </nav>
  </header>
  
  <div class="page-wrapper">

    <!-- Sidebar -->
    <div class="sidebar">
      <ul>
        <li class="active"><a href="/CeylonGo/public/transporter/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="/CeylonGo/public/transporter/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/pending"><i class="fa-regular fa-clock"></i> Pending Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="/CeylonGo/public/transporter/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/transporter/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Welcome Section -->
      <div class="welcome">
        <div class="profile-box">
          <h1>Welcome, Kaveesha Dulanjani !</h1>
          <h5>Overview of your transport operators</h2>
        </div>
      </div>

      <!-- Summary Overview -->
       <div class="summary">
        <div class="summary-card">
          <h2>40</h2>
          <p>Total Bookings</p>
        </div>
        </div>
      <div class="summary">
        <div class="summary-card">
          <h2>12</h2>
          <p>Upcoming Bookings</p>
        </div>
        <div class="summary-card">
          <h2>5</h2>
          <p>Pending Bookings</p>
        </div>
        <div class="summary-card">
          <h2>3</h2>
          <p>Cancelled Bookings</p>
        </div>
        <div class="summary-card">
          <h2>20</h2>
          <p>Completed Bookings</p>
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
          data: [20, 6],
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
