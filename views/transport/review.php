<?php require_once 'session_init.php';

// Ensure data from controller
if (!isset($reviews)) $reviews = [];
if (!isset($stats)) $stats = [
    'total_reviews' => 0, 'avg_rating' => 0,
    'star_5' => 0, 'star_4' => 0, 'star_3' => 0, 'star_2' => 0, 'star_1' => 0,
    'pct_5' => 0, 'pct_4' => 0, 'pct_3' => 0, 'pct_2' => 0, 'pct_1' => 0,
    'positive_pct' => 0,
];
$avgRating = floatval($stats['avg_rating']);
$totalReviews = intval($stats['total_reviews']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Customer Reviews</title>
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/base.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/navbar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/sidebar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/footer.css">
    
    <!-- Component styles -->
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/cards.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/buttons.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/forms.css">

    <!-- Responsive styles (always last) -->
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/responsive.css">       
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="/CeylonGo/public/css/transport/review.css">
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
      <a href="/CeylonGo/public/transporter/dashboard">Home</a>
      <div class="profile-dropdown">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="User" class="profile-pic" onclick="toggleProfileDropdown()">
        <div class="profile-dropdown-menu" id="profileDropdown">
          <a href="/CeylonGo/public/transporter/profile"><i class="fa-regular fa-user"></i> My Profile</a>
          <a href="/CeylonGo/public/logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
      </div>
    </nav>
  </header>

  <!-- Sidebar Overlay for Mobile -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="page-wrapper">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
      <ul>
        <li><a href="/CeylonGo/public/transporter/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="/CeylonGo/public/transporter/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/pending"><i class="fa-regular fa-clock"></i> Pending Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Bookings</a></li>
        <li class="active"><a href="/CeylonGo/public/transporter/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="/CeylonGo/public/transporter/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/transporter/report"><i class="fa-solid fa-chart-line"></i> Performance Report</a></li>
        <li><a href="/CeylonGo/public/transporter/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      
      <!-- Page Header -->
      <div class="page-header">
        <h1><i class="fa-solid fa-star"></i> Customer Reviews</h1>
      </div>

      <!-- Overall Rating Card -->
      <div class="overall-rating-card">
        <div class="rating-big-score">
          <div class="score"><?= $avgRating > 0 ? number_format($avgRating, 1) : '0.0' ?></div>
          <div class="stars">
            <?php
              $fullStars = floor($avgRating);
              $halfStar = ($avgRating - $fullStars) >= 0.3;
              $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
              for ($i = 0; $i < $fullStars; $i++) echo '<i class="fas fa-star"></i>';
              if ($halfStar) echo '<i class="fas fa-star-half-alt"></i>';
              for ($i = 0; $i < $emptyStars; $i++) echo '<i class="far fa-star empty"></i>';
            ?>
          </div>
          <div class="total">Based on <?= $totalReviews ?> review<?= $totalReviews !== 1 ? 's' : '' ?></div>
        </div>

        <div class="rating-distribution">
          <?php for ($star = 5; $star >= 1; $star--): ?>
          <div class="rating-bar">
            <span class="star-num"><?= $star ?> <i class="fas fa-star"></i></span>
            <div class="progress-bar"><div class="progress-fill" style="width: <?= $stats['pct_' . $star] ?>%;"></div></div>
            <span class="percentage"><?= $stats['pct_' . $star] ?>%</span>
          </div>
          <?php endfor; ?>
        </div>
      </div>

      <!-- Reviews Section Header -->
      <h3 style="margin: 30px 0 20px 0; font-size: 20px; color: #1a1a2e;">All Reviews</h3>

      <!-- Reviews Container -->
      <div class="reviews-container">
        <?php if (!empty($reviews)): ?>
          <?php
            $avatarColors = [
              'linear-gradient(135deg, #4e73df 0%, #224abe 100%)',
              'linear-gradient(135deg, #28a745 0%, #20c997 100%)',
              'linear-gradient(135deg, #e83e8c 0%, #fd7e14 100%)',
              'linear-gradient(135deg, #6f42c1 0%, #6610f2 100%)',
              'linear-gradient(135deg, #17a2b8 0%, #138496 100%)',
            ];
          ?>
          <?php foreach ($reviews as $index => $review): ?>
            <?php
              $rating = intval($review['rating']);
              $touristInfo = htmlspecialchars($review['tourist_info']);
              $reviewText = htmlspecialchars($review['review_text']);
              $words = explode(' ', $touristInfo);
              $initials = '';
              foreach (array_slice($words, 0, 2) as $w) {
                  $initials .= strtoupper(mb_substr($w, 0, 1));
              }
              $avatarColor = $avatarColors[$index % count($avatarColors)];
              if ($rating >= 5) $ratingLabel = 'Excellent';
              elseif ($rating >= 4) $ratingLabel = 'Very Good';
              elseif ($rating >= 3) $ratingLabel = 'Good';
              elseif ($rating >= 2) $ratingLabel = 'Fair';
              else $ratingLabel = 'Poor';
            ?>
        <div class="review-card">
          <div class="review-header">
            <div class="reviewer-info">
              <div class="reviewer-avatar" style="background: <?= $avatarColor ?>;"><?= $initials ?></div>
              <div class="reviewer-details">
                <h4><?= $touristInfo ?></h4>
                <div class="meta">
                  <span class="verified-badge"><i class="fa-solid fa-check"></i> Verified Trip</span>
                </div>
              </div>
            </div>
            <div class="review-rating">
              <div class="stars">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <?php if ($i <= $rating): ?>
                    <i class="fas fa-star"></i>
                  <?php else: ?>
                    <i class="far fa-star empty"></i>
                  <?php endif; ?>
                <?php endfor; ?>
              </div>
              <span class="rating-text"><?= $ratingLabel ?></span>
            </div>
          </div>
          <div class="review-content">
            <p><?= $reviewText ?></p>
          </div>
        </div>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- No Reviews Empty State -->
          <div style="text-align: center; padding: 60px 20px; color: #888;">
            <i class="fa-regular fa-star" style="font-size: 3em; margin-bottom: 15px; display: block; opacity: 0.4;"></i>
            <h3 style="color: #666; margin-bottom: 10px;">No Reviews Yet</h3>
            <p>You haven't received any reviews yet. Customer reviews will appear here after they rate your service.</p>
          </div>
        <?php endif; ?>
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

  <script>
    // Profile dropdown functionality
    function toggleProfileDropdown() {
      const dropdown = document.getElementById('profileDropdown');
      dropdown.classList.toggle('show');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      const dropdown = document.getElementById('profileDropdown');
      const profilePic = document.querySelector('.profile-pic');
      
      if (dropdown && !dropdown.contains(event.target) && event.target !== profilePic) {
        dropdown.classList.remove('show');
      }
    });

    // Hamburger Menu Toggle
    document.addEventListener('DOMContentLoaded', function() {
      const hamburgerBtn = document.getElementById('hamburgerBtn');
      const sidebar = document.getElementById('sidebar');
      const sidebarOverlay = document.getElementById('sidebarOverlay');
      
      function toggleSidebar() {
        hamburgerBtn.classList.toggle('active');
        sidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
      }
      
      function closeSidebar() {
        hamburgerBtn.classList.remove('active');
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
      }
      
      if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', toggleSidebar);
      }
      
      if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
      }
      
      const sidebarLinks = document.querySelectorAll('.sidebar ul li a');
      sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
          if (window.innerWidth <= 768) {
            closeSidebar();
          }
        });
      });
      
      window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
          closeSidebar();
        }
      });
    });
  </script>

</body>
</html>