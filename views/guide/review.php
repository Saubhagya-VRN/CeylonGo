<?php require_once 'session_init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Reviews</title>
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/base.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/navbar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/sidebar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/cards.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/buttons.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/footer.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/responsive.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .reviews-container { max-width: 900px; margin: 0 auto; }
    .rating-summary { background: #fff; border-radius: 16px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 40px; }
    .rating-score { text-align: center; }
    .rating-score h2 { font-size: 48px; color: #2c5530; margin: 0; }
    .rating-score .stars { color: #ffc107; font-size: 20px; margin: 10px 0; }
    .rating-score p { color: #666; margin: 0; }
    .rating-bars { flex: 1; }
    .rating-bar { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
    .rating-bar span { width: 30px; font-size: 14px; color: #666; }
    .bar-container { flex: 1; height: 8px; background: #e9ecef; border-radius: 4px; overflow: hidden; }
    .bar-fill { height: 100%; background: #ffc107; border-radius: 4px; }
    .review-card { background: #fff; border-radius: 12px; padding: 20px; margin-bottom: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .review-header { display: flex; align-items: center; gap: 15px; margin-bottom: 12px; }
    .reviewer-avatar { width: 50px; height: 50px; border-radius: 50%; background: #e0e0e0; object-fit: cover; }
    .reviewer-info h4 { margin: 0 0 4px 0; color: #333; }
    .reviewer-info .date { font-size: 13px; color: #999; }
    .review-stars { color: #ffc107; margin-left: auto; }
    .review-text { color: #555; line-height: 1.6; }
  </style>
</head>
<body>
  <!-- Navbar -->
  <header class="navbar">
    <div class="branding">
      <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle menu">
        <span></span>
        <span></span>
        <span></span>
      </button>
      <img src="/CeylonGo/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="/CeylonGo/public/guide/dashboard">Home</a>
      <div class="profile-dropdown">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="User" class="profile-pic" onclick="toggleProfileDropdown()">
        <div class="profile-dropdown-menu" id="profileDropdown">
          <a href="/CeylonGo/public/guide/profile"><i class="fa-regular fa-user"></i> My Profile</a>
          <a href="/CeylonGo/public/logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
      </div>
    </nav>
  </header>
  
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="page-wrapper">
    <div class="sidebar" id="sidebar">
      <ul>
        <li><a href="/CeylonGo/public/guide/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="/CeylonGo/public/guide/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Tours</a></li>
        <li><a href="/CeylonGo/public/guide/pending"><i class="fa-regular fa-clock"></i> Pending Requests</a></li>
        <li><a href="/CeylonGo/public/guide/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Tours</a></li>
        <li class="active"><a href="/CeylonGo/public/guide/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="/CeylonGo/public/guide/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/guide/places"><i class="fa-solid fa-map-location-dot"></i> My Places</a></li>
      </ul>
    </div>

    <div class="main-content">
      <h2 class="page-title"><i class="fa-regular fa-star"></i> Reviews</h2>

      <div class="reviews-container">
        <!-- Rating Summary -->
        <div class="rating-summary">
          <div class="rating-score">
            <h2>4.8</h2>
            <div class="stars">
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star-half-stroke"></i>
            </div>
            <p>Based on 24 reviews</p>
          </div>
          <div class="rating-bars">
            <div class="rating-bar"><span>5</span><div class="bar-container"><div class="bar-fill" style="width: 75%;"></div></div></div>
            <div class="rating-bar"><span>4</span><div class="bar-container"><div class="bar-fill" style="width: 15%;"></div></div></div>
            <div class="rating-bar"><span>3</span><div class="bar-container"><div class="bar-fill" style="width: 8%;"></div></div></div>
            <div class="rating-bar"><span>2</span><div class="bar-container"><div class="bar-fill" style="width: 2%;"></div></div></div>
            <div class="rating-bar"><span>1</span><div class="bar-container"><div class="bar-fill" style="width: 0%;"></div></div></div>
          </div>
        </div>

        <!-- Review Cards -->
        <div class="review-card">
          <div class="review-header">
            <img src="/CeylonGo/public/images/profile.jpg" alt="Reviewer" class="reviewer-avatar">
            <div class="reviewer-info">
              <h4>John Silva</h4>
              <span class="date">December 25, 2025</span>
            </div>
            <div class="review-stars">
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
            </div>
          </div>
          <p class="review-text">Fantastic guide! Very knowledgeable about Sigiriya's history and made the entire experience memorable. Highly recommend!</p>
        </div>

        <div class="review-card">
          <div class="review-header">
            <img src="/CeylonGo/public/images/profile.jpg" alt="Reviewer" class="reviewer-avatar">
            <div class="reviewer-info">
              <h4>Sarah Fernando</h4>
              <span class="date">December 20, 2025</span>
            </div>
            <div class="review-stars">
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
            </div>
          </div>
          <p class="review-text">Great tour of Kandy! The guide was very professional and accommodating. Would book again.</p>
        </div>
      </div>
    </div>
  </div>

  <script>
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    hamburgerBtn.addEventListener('click', function() {
      hamburgerBtn.classList.toggle('active');
      sidebar.classList.toggle('active');
      sidebarOverlay.classList.toggle('active');
    });

    sidebarOverlay.addEventListener('click', function() {
      hamburgerBtn.classList.remove('active');
      sidebar.classList.remove('active');
      sidebarOverlay.classList.remove('active');
    });

    function toggleProfileDropdown() {
      document.getElementById('profileDropdown').classList.toggle('show');
    }

    window.onclick = function(event) {
      if (!event.target.matches('.profile-pic')) {
        var dropdowns = document.getElementsByClassName("profile-dropdown-menu");
        for (var i = 0; i < dropdowns.length; i++) {
          if (dropdowns[i].classList.contains('show')) {
            dropdowns[i].classList.remove('show');
          }
        }
      }
    }
  </script>
  <!-- Footer -->
  <footer>
    <ul>
      <li><a href="#">About Us</a></li>
      <li><a href="#">Contact Us</a></li>
    </ul>
  </footer>
</body>
</html>
