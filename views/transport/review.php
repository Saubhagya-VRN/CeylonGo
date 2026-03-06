<?php require_once 'session_init.php'; ?>
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
          <div class="score">4.7</div>
          <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
          </div>
          <div class="total">Based on 125 reviews</div>
        </div>

        <div class="rating-distribution">
          <div class="rating-bar">
            <span class="star-num">5 <i class="fas fa-star"></i></span>
            <div class="progress-bar"><div class="progress-fill" style="width: 65%;"></div></div>
            <span class="percentage">65%</span>
          </div>
          <div class="rating-bar">
            <span class="star-num">4 <i class="fas fa-star"></i></span>
            <div class="progress-bar"><div class="progress-fill" style="width: 20%;"></div></div>
            <span class="percentage">20%</span>
          </div>
          <div class="rating-bar">
            <span class="star-num">3 <i class="fas fa-star"></i></span>
            <div class="progress-bar"><div class="progress-fill" style="width: 7%;"></div></div>
            <span class="percentage">7%</span>
          </div>
          <div class="rating-bar">
            <span class="star-num">2 <i class="fas fa-star"></i></span>
            <div class="progress-bar"><div class="progress-fill" style="width: 3%;"></div></div>
            <span class="percentage">3%</span>
          </div>
          <div class="rating-bar">
            <span class="star-num">1 <i class="fas fa-star"></i></span>
            <div class="progress-bar"><div class="progress-fill" style="width: 5%;"></div></div>
            <span class="percentage">5%</span>
          </div>
        </div>
      </div>

      <!-- Reviews Section Header -->
      <h3 style="margin: 30px 0 20px 0; font-size: 20px; color: #1a1a2e;">All Reviews</h3>

      <!-- Reviews Container -->
      <div class="reviews-container">
        
        <!-- Review 1 -->
        <div class="review-card">
          <div class="review-header">
            <div class="reviewer-info">
              <div class="reviewer-avatar">MN</div>
              <div class="reviewer-details">
                <h4>Malsha Nethmini</h4>
                <div class="meta">
                  <span><i class="fa-regular fa-calendar"></i> May 15, 2025</span>
                  <span class="verified-badge"><i class="fa-solid fa-check"></i> Verified Trip</span>
                </div>
              </div>
            </div>
            <div class="review-rating">
              <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
              </div>
              <span class="rating-text">Excellent</span>
            </div>
          </div>
          <div class="review-content">
            <p>The transport service was excellent! The driver was punctual, the vehicle was clean and comfortable, and the ride was smooth. Highly recommend this provider for anyone needing reliable transport in the area. Will definitely book again for my next trip!</p>
          </div>
          <div class="review-trip">
            <div class="trip-item">
              <i class="fa-solid fa-route"></i>
              <span>Kandy → Ella</span>
            </div>
            <div class="trip-item">
              <i class="fa-solid fa-car"></i>
              <span>Sedan (AC)</span>
            </div>
            <div class="trip-item">
              <i class="fa-solid fa-calendar-days"></i>
              <span>3 Day Trip</span>
            </div>
          </div>
        </div>

        <!-- Review 2 -->
        <div class="review-card">
          <div class="review-header">
            <div class="reviewer-info">
              <div class="reviewer-avatar" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">KN</div>
              <div class="reviewer-details">
                <h4>Kamal Nishantha</h4>
                <div class="meta">
                  <span><i class="fa-regular fa-calendar"></i> April 22, 2025</span>
                  <span class="verified-badge"><i class="fa-solid fa-check"></i> Verified Trip</span>
                </div>
              </div>
            </div>
            <div class="review-rating">
              <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
              </div>
              <span class="rating-text">Very Good</span>
            </div>
          </div>
          <div class="review-content">
            <p>Overall, a great experience. The driver was friendly and knowledgeable about the local attractions. The vehicle was in good condition. There was a slight delay due to traffic, but the driver communicated this effectively and made sure we reached safely.</p>
          </div>
          <div class="review-trip">
            <div class="trip-item">
              <i class="fa-solid fa-route"></i>
              <span>Colombo → Galle</span>
            </div>
            <div class="trip-item">
              <i class="fa-solid fa-car"></i>
              <span>Van (AC)</span>
            </div>
            <div class="trip-item">
              <i class="fa-solid fa-calendar-days"></i>
              <span>1 Day Trip</span>
            </div>
          </div>
        </div>

        <!-- Review 3 -->
        <div class="review-card">
          <div class="review-header">
            <div class="reviewer-info">
              <div class="reviewer-avatar" style="background: linear-gradient(135deg, #e83e8c 0%, #fd7e14 100%);">AH</div>
              <div class="reviewer-details">
                <h4>Avindya Himahansi</h4>
                <div class="meta">
                  <span><i class="fa-regular fa-calendar"></i> March 10, 2025</span>
                  <span class="verified-badge"><i class="fa-solid fa-check"></i> Verified Trip</span>
                </div>
              </div>
            </div>
            <div class="review-rating">
              <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
              </div>
              <span class="rating-text">Excellent</span>
            </div>
          </div>
          <div class="review-content">
            <p>Fantastic service! The driver was very professional and knowledgeable about the area. He gave us great recommendations for restaurants and photo spots. The vehicle was well-maintained and super comfortable. I would definitely use this service again for my future travels in Sri Lanka!</p>
          </div>
          <div class="review-trip">
            <div class="trip-item">
              <i class="fa-solid fa-route"></i>
              <span>Negombo → Sigiriya</span>
            </div>
            <div class="trip-item">
              <i class="fa-solid fa-car"></i>
              <span>SUV (AC)</span>
            </div>
            <div class="trip-item">
              <i class="fa-solid fa-calendar-days"></i>
              <span>5 Day Trip</span>
            </div>
          </div>
        </div>

        <!-- Review 4 -->
        <div class="review-card">
          <div class="review-header">
            <div class="reviewer-info">
              <div class="reviewer-avatar" style="background: linear-gradient(135deg, #6f42c1 0%, #6610f2 100%);">JS</div>
              <div class="reviewer-details">
                <h4>John Smith</h4>
                <div class="meta">
                  <span><i class="fa-regular fa-calendar"></i> February 28, 2025</span>
                  <span class="verified-badge"><i class="fa-solid fa-check"></i> Verified Trip</span>
                </div>
              </div>
            </div>
            <div class="review-rating">
              <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="far fa-star empty"></i>
              </div>
              <span class="rating-text">Good</span>
            </div>
          </div>
          <div class="review-content">
            <p>Good overall experience. The driver was polite and the vehicle was comfortable. The air conditioning worked perfectly which was important for us traveling with elderly family members. Minor suggestion: it would be nice to have phone chargers available in the vehicle.</p>
          </div>
          <div class="review-trip">
            <div class="trip-item">
              <i class="fa-solid fa-route"></i>
              <span>Colombo → Nuwara Eliya</span>
            </div>
            <div class="trip-item">
              <i class="fa-solid fa-car"></i>
              <span>Van (AC)</span>
            </div>
            <div class="trip-item">
              <i class="fa-solid fa-calendar-days"></i>
              <span>2 Day Trip</span>
            </div>
          </div>
        </div>

      </div>

      <!-- Load More Button -->
      <div class="load-more">
        <button class="load-more-btn">
          <i class="fa-solid fa-plus"></i> Load More Reviews
        </button>
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
    // Filter button functionality
    document.querySelectorAll('.filter-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
      });
    });

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