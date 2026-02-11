<?php require_once 'session_init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Tour Reviews</title>
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/base.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/navbar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/sidebar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/cards.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/buttons.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/footer.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/responsive.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/review.css">
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
        <li><a href="/CeylonGo/public/guide/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </div>

    <div class="main-content">
      <!-- Page Header -->
      <div class="page-header">
        <h1><i class="fa-solid fa-star"></i> Tour Reviews</h1>
      </div>

      <!-- Stats Summary Row -->
      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-icon green">
            <i class="fa-solid fa-star"></i>
          </div>
          <div class="stat-content">
            <h4>4.8</h4>
            <p>Average Rating</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon blue">
            <i class="fa-solid fa-comments"></i>
          </div>
          <div class="stat-content">
            <h4>48</h4>
            <p>Total Reviews</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon amber">
            <i class="fa-solid fa-thumbs-up"></i>
          </div>
          <div class="stat-content">
            <h4>96%</h4>
            <p>Positive Reviews</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon purple">
            <i class="fa-solid fa-reply"></i>
          </div>
          <div class="stat-content">
            <h4>92%</h4>
            <p>Response Rate</p>
          </div>
        </div>
      </div>

      <!-- Overall Rating Card -->
      <div class="overall-rating-card">
        <div class="rating-big-score">
          <div class="score">4.8</div>
          <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
          </div>
          <div class="total">Based on 48 reviews</div>
        </div>

        <div class="rating-distribution">
          <div class="rating-bar">
            <span class="star-num">5 <i class="fas fa-star"></i></span>
            <div class="progress-bar"><div class="progress-fill" style="width: 75%;"></div></div>
            <span class="percentage">75%</span>
          </div>
          <div class="rating-bar">
            <span class="star-num">4 <i class="fas fa-star"></i></span>
            <div class="progress-bar"><div class="progress-fill" style="width: 18%;"></div></div>
            <span class="percentage">18%</span>
          </div>
          <div class="rating-bar">
            <span class="star-num">3 <i class="fas fa-star"></i></span>
            <div class="progress-bar"><div class="progress-fill" style="width: 5%;"></div></div>
            <span class="percentage">5%</span>
          </div>
          <div class="rating-bar">
            <span class="star-num">2 <i class="fas fa-star"></i></span>
            <div class="progress-bar"><div class="progress-fill" style="width: 2%;"></div></div>
            <span class="percentage">2%</span>
          </div>
          <div class="rating-bar">
            <span class="star-num">1 <i class="fas fa-star"></i></span>
            <div class="progress-bar"><div class="progress-fill" style="width: 0%;"></div></div>
            <span class="percentage">0%</span>
          </div>
        </div>
      </div>

      <!-- All Reviews Header -->
      <h3 style="margin: 0 0 25px 0; font-size: 20px; color: #1a1a2e; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-comments" style="color: #3d8b40;"></i> All Reviews
      </h3>

      <!-- Reviews Container -->
      <div class="reviews-container">
        
        <!-- Review 1 -->
        <div class="review-card" data-rating="5">
          <div class="review-header">
            <div class="reviewer-info">
              <div class="reviewer-avatar">JS</div>
              <div class="reviewer-details">
                <h4>John Silva</h4>
                <div class="meta">
                  <span><i class="fa-regular fa-calendar"></i> December 25, 2025</span>
                  <span class="verified-badge"><i class="fa-solid fa-check"></i> Verified Tour</span>
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
            <p>Fantastic guide! Very knowledgeable about Sigiriya's history and made the entire experience memorable. The guide explained everything in great detail and answered all our questions patiently. The pace was perfect, and we had enough time to explore and take photos. Highly recommend!</p>
          </div>
          <div class="review-tour">
            <div class="tour-item">
              <i class="fa-solid fa-location-dot"></i>
              <span>Sigiriya Rock Fortress</span>
            </div>
            <div class="tour-item">
              <i class="fa-solid fa-clock"></i>
              <span>Full Day Tour</span>
            </div>
            <div class="tour-item">
              <i class="fa-solid fa-users"></i>
              <span>4 Travelers</span>
            </div>
            <span class="tour-type-badge"><i class="fa-solid fa-mountain"></i> Cultural Heritage</span>
          </div>
          <div class="guide-response">
            <div class="response-header">
              <i class="fa-solid fa-reply"></i> Guide Response
            </div>
            <p>Thank you so much for the wonderful review, John! It was a pleasure guiding you and your family through Sigiriya. I'm glad you enjoyed the experience. Looking forward to hosting you again on your next adventure in Sri Lanka!</p>
          </div>
          <div class="review-actions">
            <button class="helpful-btn">
              <i class="fa-regular fa-thumbs-up"></i> Helpful
            </button>
            <span class="helpful-count">12 people found this helpful</span>
          </div>
        </div>

        <!-- Review 2 -->
        <div class="review-card" data-rating="4">
          <div class="review-header">
            <div class="reviewer-info">
              <div class="reviewer-avatar" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">SF</div>
              <div class="reviewer-details">
                <h4>Sarah Fernando</h4>
                <div class="meta">
                  <span><i class="fa-regular fa-calendar"></i> December 20, 2025</span>
                  <span class="verified-badge"><i class="fa-solid fa-check"></i> Verified Tour</span>
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
              <span class="rating-text">Very Good</span>
            </div>
          </div>
          <div class="review-content">
            <p>Great tour of Kandy! The guide was very professional and accommodating. We visited the Temple of the Tooth, Peradeniya Gardens, and a gem museum. The guide's knowledge of local history and culture was impressive. Would book again!</p>
          </div>
          <div class="review-tour">
            <div class="tour-item">
              <i class="fa-solid fa-location-dot"></i>
              <span>Kandy City Tour</span>
            </div>
            <div class="tour-item">
              <i class="fa-solid fa-clock"></i>
              <span>Full Day Tour</span>
            </div>
            <div class="tour-item">
              <i class="fa-solid fa-users"></i>
              <span>2 Travelers</span>
            </div>
            <span class="tour-type-badge"><i class="fa-solid fa-landmark"></i> Religious Sites</span>
          </div>
          <div class="review-actions">
            <button class="helpful-btn">
              <i class="fa-regular fa-thumbs-up"></i> Helpful
            </button>
            <span class="helpful-count">8 people found this helpful</span>
          </div>
        </div>

        <!-- Review 3 -->
        <div class="review-card" data-rating="5">
          <div class="review-header">
            <div class="reviewer-info">
              <div class="reviewer-avatar" style="background: linear-gradient(135deg, #e83e8c 0%, #fd7e14 100%);">MW</div>
              <div class="reviewer-details">
                <h4>Michael Williams</h4>
                <div class="meta">
                  <span><i class="fa-regular fa-calendar"></i> December 15, 2025</span>
                  <span class="verified-badge"><i class="fa-solid fa-check"></i> Verified Tour</span>
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
            <p>Absolutely incredible experience! Our wildlife safari in Yala National Park was unforgettable. The guide knew exactly where to spot leopards and elephants. We saw so much wildlife - leopards, elephants, crocodiles, peacocks, and countless birds. The guide was patient and made sure everyone got great photos. Best tour of our entire Sri Lanka trip!</p>
          </div>
          <div class="review-tour">
            <div class="tour-item">
              <i class="fa-solid fa-location-dot"></i>
              <span>Yala National Park Safari</span>
            </div>
            <div class="tour-item">
              <i class="fa-solid fa-clock"></i>
              <span>Half Day Tour</span>
            </div>
            <div class="tour-item">
              <i class="fa-solid fa-users"></i>
              <span>6 Travelers</span>
            </div>
            <span class="tour-type-badge"><i class="fa-solid fa-paw"></i> Nature & Wildlife</span>
          </div>
          <div class="guide-response">
            <div class="response-header">
              <i class="fa-solid fa-reply"></i> Guide Response
            </div>
            <p>Michael, thank you for the amazing review! We were lucky to spot so many animals that day, including the elusive leopard. It was a pleasure having you on the safari. Hope to see you again for more wildlife adventures!</p>
          </div>
          <div class="review-actions">
            <button class="helpful-btn active">
              <i class="fa-solid fa-thumbs-up"></i> Helpful
            </button>
            <span class="helpful-count">24 people found this helpful</span>
          </div>
        </div>

        <!-- Review 4 -->
        <div class="review-card" data-rating="5">
          <div class="review-header">
            <div class="reviewer-info">
              <div class="reviewer-avatar" style="background: linear-gradient(135deg, #6f42c1 0%, #6610f2 100%);">AP</div>
              <div class="reviewer-details">
                <h4>Amanda Perera</h4>
                <div class="meta">
                  <span><i class="fa-regular fa-calendar"></i> December 10, 2025</span>
                  <span class="verified-badge"><i class="fa-solid fa-check"></i> Verified Tour</span>
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
            <p>The culinary tour in Colombo was an amazing experience! We explored local markets, tried authentic street food, and even participated in a cooking class. The guide's passion for Sri Lankan cuisine was infectious. Learned so much about local spices and cooking techniques. A must-do experience!</p>
          </div>
          <div class="review-tour">
            <div class="tour-item">
              <i class="fa-solid fa-location-dot"></i>
              <span>Colombo Food Tour</span>
            </div>
            <div class="tour-item">
              <i class="fa-solid fa-clock"></i>
              <span>Half Day Tour</span>
            </div>
            <div class="tour-item">
              <i class="fa-solid fa-users"></i>
              <span>3 Travelers</span>
            </div>
            <span class="tour-type-badge"><i class="fa-solid fa-utensils"></i> Culinary Tours</span>
          </div>
          <div class="review-actions">
            <button class="helpful-btn">
              <i class="fa-regular fa-thumbs-up"></i> Helpful
            </button>
            <span class="helpful-count">15 people found this helpful</span>
          </div>
        </div>

        <!-- Review 5 -->
        <div class="review-card" data-rating="3">
          <div class="review-header">
            <div class="reviewer-info">
              <div class="reviewer-avatar" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">RJ</div>
              <div class="reviewer-details">
                <h4>Robert Johnson</h4>
                <div class="meta">
                  <span><i class="fa-regular fa-calendar"></i> December 5, 2025</span>
                  <span class="verified-badge"><i class="fa-solid fa-check"></i> Verified Tour</span>
                </div>
              </div>
            </div>
            <div class="review-rating">
              <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="far fa-star empty"></i>
                <i class="far fa-star empty"></i>
              </div>
              <span class="rating-text">Good</span>
            </div>
          </div>
          <div class="review-content">
            <p>The tour was decent overall. The guide was knowledgeable but the pace was a bit fast for our liking. We would have appreciated more time at each location. However, the beach experience in Mirissa was beautiful and the guide helped us find a great spot for whale watching. Minor improvements would make this tour excellent.</p>
          </div>
          <div class="review-tour">
            <div class="tour-item">
              <i class="fa-solid fa-location-dot"></i>
              <span>Southern Beaches Tour</span>
            </div>
            <div class="tour-item">
              <i class="fa-solid fa-clock"></i>
              <span>Full Day Tour</span>
            </div>
            <div class="tour-item">
              <i class="fa-solid fa-users"></i>
              <span>2 Travelers</span>
            </div>
            <span class="tour-type-badge"><i class="fa-solid fa-umbrella-beach"></i> Beach & Coastal</span>
          </div>
          <div class="guide-response">
            <div class="response-header">
              <i class="fa-solid fa-reply"></i> Guide Response
            </div>
            <p>Thank you for your feedback, Robert. I appreciate your honest review and will definitely take note of the pacing for future tours. I'm glad you enjoyed the whale watching experience in Mirissa. Your suggestions are valuable and will help me improve. Hope to provide a better experience next time!</p>
          </div>
          <div class="review-actions">
            <button class="helpful-btn">
              <i class="fa-regular fa-thumbs-up"></i> Helpful
            </button>
            <span class="helpful-count">5 people found this helpful</span>
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
      
      // Close sidebar when clicking on a link (mobile)
      const sidebarLinks = document.querySelectorAll('.sidebar ul li a');
      sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
          if (window.innerWidth <= 768) {
            closeSidebar();
          }
        });
      });
      
      // Close sidebar on window resize
      window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
          closeSidebar();
        }
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

    // Filter button functionality
    document.querySelectorAll('.filter-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        // Add active class to clicked button
        this.classList.add('active');
        
        const filter = this.dataset.filter;
        const reviews = document.querySelectorAll('.review-card');
        
        reviews.forEach(review => {
          if (filter === 'all' || filter === 'recent') {
            review.style.display = 'block';
          } else {
            const rating = review.dataset.rating;
            review.style.display = (rating === filter) ? 'block' : 'none';
          }
        });
      });
    });

    // Helpful button functionality
    document.querySelectorAll('.helpful-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        this.classList.toggle('active');
        const icon = this.querySelector('i');
        if (this.classList.contains('active')) {
          icon.classList.remove('fa-regular');
          icon.classList.add('fa-solid');
        } else {
          icon.classList.remove('fa-solid');
          icon.classList.add('fa-regular');
        }
      });
    });

    // Animate progress bars on scroll
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const progressFills = entry.target.querySelectorAll('.progress-fill');
          progressFills.forEach(fill => {
            const width = fill.style.width;
            fill.style.width = '0';
            setTimeout(() => {
              fill.style.width = width;
            }, 100);
          });
        }
      });
    }, { threshold: 0.5 });

    const ratingCard = document.querySelector('.overall-rating-card');
    if (ratingCard) {
      observer.observe(ratingCard);
    }
  </script>

</body>
</html>
