<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Transport Provider Dashboard</title>
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/base.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/navbar.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/sidebar.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/footer.css">
    
    <!-- Component styles -->
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/cards.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/buttons.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/forms.css">
    
    <!-- Page-specific styles -->
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/timeline.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/tables.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/profile.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/reviews.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/charts.css">

    <!-- Responsive styles (always last) -->
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/responsive.css">       
    <link rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <!-- Navbar -->
  <header class="navbar">
    <div class="branding">
      <img src="/Ceylon_Go/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="#">Home</a>
      <a href="#">Logout</a>
      <img src="/Ceylon_Go/public/images/profile.jpeg" alt="User" class="profile-pic">
    </nav>
  </header>

  <div class="page-wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
      <ul>
        <li><a href="dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Bookings</a></li>
        <li><a href="pending"><i class="fa-regular fa-clock"></i> Pending Bookings</a></li>
        <li><a href="cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Bookings</a></li>
        <li class="active"><a href="review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </div>
 

    <!-- Main Content -->
    <div class="main-content">
      
      <h1>Reviews</h1>
      <div class="rating-summary">
        <span class="rating-score">4.7</span>
        <span class="stars">
          <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
        </span>
        <span>(125 reviews)</span>
        <div class="rating-distribution">
          <div class="rating-bar"><span>5</span><div class="progress"><div class="progress-fill" style="width: 65%;"></div></div> 65%</div>
          <div class="rating-bar"><span>4</span><div class="progress"><div class="progress-fill" style="width: 20%;"></div></div> 20%</div>
          <div class="rating-bar"><span>3</span><div class="progress"><div class="progress-fill" style="width: 7%;"></div></div> 7%</div>
          <div class="rating-bar"><span>2</span><div class="progress"><div class="progress-fill" style="width: 3%;"></div></div> 3%</div>
          <div class="rating-bar"><span>1</span><div class="progress"><div class="progress-fill" style="width: 5%;"></div></div> 5%</div>
        </div>
    
      <div class="review">
        <div class="user-info">
          <span class="user-name">Malsha Nethmini</span>
          <span class="date">May 15, 2025</span>
        </div>
        <div class="stars">
          <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
        </div>
        <p>The transport service was excellent! The driver was punctual, the vehicle was clean and comfortable, and the ride was smooth. Highly recommend this provider for anyone needing reliable transport in the area.</p>
      </div>
      <div class="review">
        <div class="user-info">
          <span class="user-name">Kamal Nishantha</span>
          <span class="date">April 22, 2025</span>
        </div>
        <div class="stars">
          <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
        </div>
        <p>Overall, a good experience. The driver was friendly and the vehicle was in good condition. However, due to traffic, but the driver communicated this effectively.</p>
      </div>
      <div class="review">
        <div class="user-info">
          <span class="user-name">Avindya Himahansi</span>
          <span class="date">March 10, 2025</span>
        </div>
        <div class="stars">
          <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
        </div>
        <p>Fantastic service! The driver was very professional and knowledgeable about the area. The vehicle was well-maintained. I would definitely use this service again.</p>
      </div>
    </div>
  </div>
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