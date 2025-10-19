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
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
    <aside class="sidebar">
      <ul>
        <li><a href="dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Bookings</a></li>
        <li><a href="pending"><i class="fa-regular fa-clock"></i> Pending Bookings</a></li>
        <li><a href="cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Bookings</a></li>
        <li><a href="review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </aside>

    <!-- Main -->
    <main class="main-content">
      <div class="tour-container">

        <!-- Back Button (top right of container) -->
        <div class="back-btn-wrapper">
          <button class="back-btn" onclick="history.back()">
            <i class="fa-solid fa-arrow-left"></i> Back
          </button>
        </div>

        <!-- Trip summary cards -->
        <div class="summary">       
          <div class="summary-card">
              <div class="icon"><i class="fa-regular fa-calendar-days"></i></div>
              <div class="meta">
                  <div class="label">Duration</div>
                  <div class="value">8 Days / 7 Nights</div>
              </div>
          </div>

          <div class="summary-card">
              <div class="icon"><i class="fa-regular fa-calendar"></i></div>
              <div class="meta">
                  <div class="label">Starting Date</div>
                  <div class="value">15th January 2024</div>
              </div>
          </div>    

          <div class="summary-card">
              <div class="icon"><i class="fa-regular fa-clock"></i></div>
              <div class="meta">
                  <div class="label">Pickup Time</div>
                  <div class="value">08:30 am</div>
              </div>
          </div>        

          <div class="summary-card">
              <div class="icon"><i class="fa-regular fa-calendar"></i></div>
              <div class="meta">
                  <div class="label">Ending Date</div>
                  <div class="value">22th January 2024</div>
              </div>
          </div>

          <div class="summary-card">
              <div class="icon"><i class="fa-solid fa-location-dot"></i></div>
              <div class="meta">
                  <div class="label">Pickup Place</div>
                  <div class="value">Hospital Road<br>Dehiwala</div>
              </div>
          </div>

          <div class="summary-card">
              <div class="icon"><i class="fa-solid fa-user-group"></i></div>
              <div class="meta">
                  <div class="label">Passengers</div>
                  <div class="value">2 Passengers</div>
              </div>
          </div>
        </div>

        <!-- Itinerary -->
        <section class="itinerary">
          <h2 class="itinerary-title">Itinerary</h2>
          <ol class="timeline">
            <li class="timeline-item">
              <div class="badge">1</div>
              <div class="content">
                <h3>Day 1–2: Kandy</h3>
                <p>Visit the Temple of the Sacred Tooth Relic, explore the Royal Botanic Gardens, and enjoy a cultural show.</p>
              </div>
            </li>

            <li class="timeline-item">
              <div class="badge">2</div>
              <div class="content">
                <h3>Day 3–4: Ella</h3>
                <p>Hike to Little Adam’s Peak, see the Nine Arch Bridge, and enjoy Ella Gap views.</p>
              </div>
            </li>

            <li class="timeline-item">
              <div class="badge">3</div>
              <div class="content">
                <h3>Day 5–6: Yala</h3>
                <p>Experience a safari in Yala National Park, home to leopards, elephants, and many bird species.</p>
              </div>
            </li>

            <li class="timeline-item">
              <div class="badge">4</div>
              <div class="content">
                <h3>Day 7–8: Galle</h3>
                <p>Explore Galle Fort (UNESCO) and relax on the beaches of Unawatuna.</p>
              </div>
            </li>
          </ol>
        </section>
      </div>
    </main>
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
