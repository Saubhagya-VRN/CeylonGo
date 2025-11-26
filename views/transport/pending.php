<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Transport Provider Pending Bookings</title>
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
      <a href="#">Logout</a>
      <img src="/CeylonGO/public/images/profile.jpg" alt="User" class="profile-pic">
    <link rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    </nav>
  </header>

  <div class="page-wrapper">
    
    <!-- Sidebar -->
    <div class="sidebar">
      <ul>
        <li><a href="/CeylonGo/public/transporter/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="/CeylonGo/public/transporter/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Bookings</a></li>
        <li class="active"><a href="/CeylonGo/public/transporter/pending"><i class="fa-regular fa-clock"></i> Pending Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="/CeylonGo/public/transporter/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/transporter/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2>Pending Bookings</h2>     

      <!-- Bookings Table -->
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Booking No</th>
              <th>Date</th>
              <th>Pickup Time</th>
              <th>Pickup Location</th>
              <th>No. of Days</th>
              <th>Manage Request</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#12345</td>
              <td>2025-03-15</td>
              <td>09:00 AM</td>
              <td>123, Park Road, Dehiwala</td>
              <td>4</td>
              <td>
                <button class="accept-btn">Accept</button>
                <button class="reject-btn">Reject</button>
              </td>
              <td><a href="CeylonGo/public/info">See More</a></td>
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
