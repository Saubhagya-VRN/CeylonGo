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

    <style>
        /* Page Header */
        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 28px;
            color: #1a1a2e;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-header h1 i {
            color: #ffc107;
        }

        /* Overall Rating Card */
        .overall-rating-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border-radius: 20px;
            padding: 35px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
            display: flex;
            gap: 40px;
            align-items: center;
            flex-wrap: wrap;
        }

        .rating-big-score {
            text-align: center;
            min-width: 180px;
        }

        .rating-big-score .score {
            font-size: 72px;
            font-weight: 800;
            color: #1a1a2e;
            line-height: 1;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .rating-big-score .stars {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-bottom: 10px;
        }

        .rating-big-score .stars i {
            font-size: 24px;
            color: #ffc107;
        }

        .rating-big-score .total {
            font-size: 16px;
            color: #6c757d;
        }

        /* Rating Distribution */
        .rating-distribution {
            flex: 1;
            min-width: 250px;
        }

        .rating-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .rating-bar .star-num {
            display: flex;
            align-items: center;
            gap: 4px;
            min-width: 40px;
            font-weight: 600;
            color: #495057;
        }

        .rating-bar .star-num i {
            color: #ffc107;
            font-size: 14px;
        }

        .rating-bar .progress-bar {
            flex: 1;
            height: 12px;
            background: #e9ecef;
            border-radius: 20px;
            overflow: hidden;
        }

        .rating-bar .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #ffc107 0%, #ff9800 100%);
            border-radius: 20px;
            transition: width 0.5s ease;
        }

        .rating-bar .percentage {
            min-width: 45px;
            text-align: right;
            font-weight: 600;
            color: #495057;
            font-size: 14px;
        }

        /* Rating Stats */
        .rating-stats {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .stat-box {
            background: linear-gradient(135deg, #e8f4f8 0%, #d4edda 100%);
            padding: 20px 30px;
            border-radius: 12px;
            text-align: center;
            min-width: 120px;
        }

        .stat-box.positive {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        }

        .stat-box h4 {
            font-size: 28px;
            color: #28a745;
            margin: 0 0 5px 0;
        }

        .stat-box p {
            margin: 0;
            color: #495057;
            font-size: 13px;
        }

        /* Filter Section */
        .filter-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .filter-section h3 {
            margin: 0;
            font-size: 20px;
            color: #1a1a2e;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-section h3 i {
            color: #0077b6;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 8px 20px;
            border: 2px solid #e9ecef;
            background: #fff;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 500;
            color: #495057;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn:hover {
            border-color: #0077b6;
            color: #0077b6;
        }

        .filter-btn.active {
            background: linear-gradient(135deg, #0077b6 0%, #005a8d 100%);
            color: #fff;
            border-color: transparent;
        }

        /* Review Cards */
        .reviews-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .review-card {
            background: #fff;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .review-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .reviewer-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .reviewer-avatar {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0077b6 0%, #00b4d8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 22px;
            font-weight: 700;
        }

        .reviewer-details h4 {
            margin: 0 0 5px 0;
            font-size: 17px;
            color: #1a1a2e;
        }

        .reviewer-details .meta {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            color: #6c757d;
        }

        .reviewer-details .meta i {
            font-size: 12px;
        }

        .review-rating {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 5px;
        }

        .review-rating .stars {
            display: flex;
            gap: 3px;
        }

        .review-rating .stars i {
            font-size: 16px;
            color: #ffc107;
        }

        .review-rating .stars i.empty {
            color: #e0e0e0;
        }

        .review-rating .rating-text {
            font-size: 13px;
            color: #6c757d;
        }

        .review-content {
            margin-bottom: 15px;
        }

        .review-content p {
            margin: 0;
            color: #495057;
            font-size: 15px;
            line-height: 1.7;
        }

        .review-trip {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 12px 15px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            flex-wrap: wrap;
        }

        .review-trip .trip-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #495057;
        }

        .review-trip .trip-item i {
            color: #0077b6;
        }

        /* Verified Badge */
        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
        }

        .verified-badge i {
            font-size: 10px;
        }

        /* Load More Button */
        .load-more {
            text-align: center;
            margin-top: 30px;
        }

        .load-more-btn {
            padding: 14px 40px;
            background: linear-gradient(135deg, #0077b6 0%, #005a8d 100%);
            color: #fff;
            border: none;
            border-radius: 30px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 119, 182, 0.3);
        }

        .load-more-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 119, 182, 0.4);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .overall-rating-card {
                flex-direction: column;
                text-align: center;
            }

            .rating-distribution {
                width: 100%;
            }

            .rating-stats {
                justify-content: center;
            }

            .filter-section {
                flex-direction: column;
                align-items: flex-start;
            }

            .review-header {
                flex-direction: column;
                gap: 15px;
            }

            .review-rating {
                align-items: flex-start;
            }

            .review-trip {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
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