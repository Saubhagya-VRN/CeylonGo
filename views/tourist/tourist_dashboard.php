<?php
// views/tourist/tourist_dashboard.php
// Session is already started in public/index.php

// Check if user is logged in (for features that require authentication)
$is_logged_in = isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'tourist';

// Generate CSRF token for logged-in users
if ($is_logged_in && empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize variables
$people = array();
$destinations = array();
$days = array();
$hotels = array();
$transports = array();
$guide = '';
$success_message = '';
$error_message = '';
$upcoming_trip = null;
$days_until_trip = null;
$tourist_data = null;

// Fetch tourist data for logged-in users
if ($is_logged_in) {
    try {
        require_once dirname(__DIR__, 2) . '/core/Database.php';
        require_once dirname(__DIR__, 2) . '/models/Tourist.php';
        $db = Database::getConnection();
        $touristModel = new Tourist($db);
        $tourist_data = $touristModel->getTouristById($_SESSION['user_id']);
    } catch (Exception $e) {
        error_log("Error fetching tourist data: " . $e->getMessage());
    }
}

// Initialize form data in session if not exists
if (!isset($_SESSION['trip_form_data'])) {
    $_SESSION['trip_form_data'] = array(
        'people' => array(),
        'destinations' => array(),
        'days' => array(),
        'hotels' => array(),
        'transports' => array(),
        'guide' => 'No'
    );
}

// Fetch the next upcoming trip for logged-in tourists
if ($is_logged_in) {
    try {
        require_once dirname(__DIR__, 2) . '/config/database.php';
        
        // Fetch the next upcoming trip
        $stmt = $conn->prepare("
            SELECT 
                tb.id as booking_id,
                tb.status,
                tb.created_at,
                GROUP_CONCAT(td.destination SEPARATOR ', ') as destinations,
                SUM(td.days) as total_days,
                MAX(td.people_count) as people_count
            FROM trip_bookings tb
            LEFT JOIN trip_destinations td ON tb.id = td.booking_id
            WHERE tb.user_id = ?
            GROUP BY tb.id
            ORDER BY tb.created_at DESC
            LIMIT 1
        ");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $upcoming_trip = $result->fetch_assoc();
            // Calculate days until trip (using created_at as reference)
            $trip_date = strtotime($upcoming_trip['created_at']);
            $today = strtotime('today');
            $days_until_trip = ceil(($trip_date - $today) / 86400) + 11; // Adding 11 days as example
        }
        $stmt->close();
    } catch (Exception $e) {
        error_log("Error fetching upcoming trip: " . $e->getMessage());
    }
}

// Process customize trip form submission (ONLY for logged-in users)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user is logged in before processing booking
    if (!$is_logged_in) {
        $error_message = "Please login to book a trip. <a href='/CeylonGo/public/login' style='color: #2c5530; font-weight: bold; text-decoration: underline;'>Login here</a>";
    }
    // Verify CSRF token
    elseif (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message = "Invalid request. Please try again.";
    } else {
        // Validate and sanitize input
        $people = isset($_POST['people']) ? array_map('intval', $_POST['people']) : array();
        $destinations = isset($_POST['destination']) ? array_map('htmlspecialchars', $_POST['destination']) : array();
        $days = isset($_POST['days']) ? array_map('intval', $_POST['days']) : array();
        $hotels = isset($_POST['hotel']) ? array_map('htmlspecialchars', $_POST['hotel']) : array();
        $transports = isset($_POST['transport']) ? array_map('htmlspecialchars', $_POST['transport']) : array();
        $guides = isset($_POST['guide']) ? (is_array($_POST['guide']) ? array_map('htmlspecialchars', $_POST['guide']) : array(htmlspecialchars($_POST['guide']))) : array();
        // For backward compatibility, use "Yes" if any destination requires a guide
        $guide = in_array('Yes', $guides) ? 'Yes' : 'No';

        // Validate that we have at least one destination
        if (empty($destinations) || empty($destinations[0])) {
            $error_message = "Please select at least one destination.";
        } else {
            // Save to database
            try {
                require_once dirname(__DIR__, 2) . '/config/database.php';
                
                // Insert trip booking
                $stmt = $conn->prepare("INSERT INTO trip_bookings (user_id, guide_required, created_at, status) VALUES (?, ?, NOW(), 'pending')");
                $stmt->bind_param("is", $_SESSION['user_id'], $guide);
                $stmt->execute();
                $booking_id = $conn->insert_id;
                
                // Insert each destination
                $stmt = $conn->prepare("INSERT INTO trip_destinations (booking_id, destination, people_count, days, hotel, transport) VALUES (?, ?, ?, ?, ?, ?)");
                
                for ($i = 0; $i < count($destinations); $i++) {
                    $dest = $destinations[$i];
                    $ppl = isset($people[$i]) ? $people[$i] : 0;
                    $dy = isset($days[$i]) ? $days[$i] : 0;
                    $htl = isset($hotels[$i]) ? $hotels[$i] : '';
                    $trn = isset($transports[$i]) ? $transports[$i] : '';
                    
                    $stmt->bind_param("isiiss", $booking_id, $dest, $ppl, $dy, $htl, $trn);
                    $stmt->execute();
                }
                
                $stmt->close();
                $conn->close();
                
                // Redirect to trip summary page
                header("Location: /CeylonGo/public/tourist/trip-summary?booking_id=" . $booking_id);
                exit;
                
            } catch (Exception $e) {
                $error_message = "An error occurred while saving your trip. Please try again.";
                error_log($e->getMessage());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Plan Your Trip</title>
  <link rel="stylesheet" href="../../public/css/common.css">
  <link rel="stylesheet" href="../../public/css/tourist/tourist_dashboard.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
  <style>
    .autocomplete-suggestions {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: white;
      border: 1px solid #d0d7d0;
      border-top: none;
      border-radius: 0 0 6px 6px;
      max-height: 200px;
      overflow-y: auto;
      z-index: 1000;
      display: none;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .autocomplete-suggestions.active {
      display: block;
    }
    .suggestion-item {
      padding: 10px 14px;
      cursor: pointer;
      border-bottom: 1px solid #f0f0f0;
      font-size: 14px;
      color: #333;
    }
    .suggestion-item:last-child {
      border-bottom: none;
    }
    .suggestion-item:hover,
    .suggestion-item.active {
      background-color: #f5f8f5;
      color: #2d5016;
    }
    .suggestion-loading {
      padding: 10px 14px;
      text-align: center;
      color: #666;
      font-size: 13px;
    }
  </style>

</head>
<body class="bg-app">
  <!-- Navbar include -->
  <?php include 'header.php'; ?>

  <!-- ✅ UPCOMING TRIP NOTIFICATION -->
  <?php if ($is_logged_in && $upcoming_trip): ?>
  <div class="upcoming-trip-notification" id="tripNotification">
    <div class="notification-content">
      <span class="notification-icon">✈️</span>
      <div class="notification-text">
        <strong>View Your Upcoming Trip!</strong>
        <p><?= htmlspecialchars($upcoming_trip['destinations']) ?> • Trip in <?= $days_until_trip ?> days</p>
      </div>
      <a href="/CeylonGo/public/tourist/trip-summary?booking_id=<?= $upcoming_trip['booking_id'] ?>" class="notification-btn">View Details</a>
      <button class="notification-close" onclick="closeNotification()" aria-label="Close notification">&times;</button>
    </div>
  </div>
  <?php endif; ?>

  <section class="intro">
    <div class="carousel-container">
      <!-- Carousel Slides -->
      <div class="carousel-slide active"></div>
      <div class="carousel-slide"></div>
      <div class="carousel-slide"></div>
      <div class="carousel-slide"></div>
      
      <!-- Carousel Content (overlays the slides) -->
      <div class="carousel-content">
        <h1>Plan Your Perfect Trip to Sri Lanka</h1>
        <p>Explore the beauty of Sri Lanka with our customizable tour packages.</p>

        <?php if (!$is_logged_in): ?>
          <a href="/CeylonGo/public/register" class="btn">Get Started</a>
        <?php else: ?>
          <a href="#customize" class="btn">Customize Your Trip</a>
        <?php endif; ?>
      </div>
      
      <!-- Carousel Navigation Arrows -->
      <button class="carousel-btn prev" onclick="changeSlide(-1)">‹</button>
      <button class="carousel-btn next" onclick="changeSlide(1)">›</button>
      
      <!-- Carousel Indicators -->
      <div class="carousel-indicators">
        <span class="carousel-indicator active" onclick="goToSlide(0)"></span>
        <span class="carousel-indicator" onclick="goToSlide(1)"></span>
        <span class="carousel-indicator" onclick="goToSlide(2)"></span>
        <span class="carousel-indicator" onclick="goToSlide(3)"></span>
      </div>
    </div>
  </section>

  <script>
    // Close notification function
    function closeNotification() {
      const notification = document.getElementById('tripNotification');
      if (notification) {
        notification.style.animation = 'slideOut 0.3s ease forwards';
        setTimeout(() => {
          notification.style.display = 'none';
        }, 300);
      }
    }

    // Carousel functionality
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-slide');
    const indicators = document.querySelectorAll('.carousel-indicator');
    const totalSlides = slides.length;
    let autoSlideInterval;

    function showSlide(index) {
      // Remove active class from all slides and indicators
      slides.forEach(slide => slide.classList.remove('active'));
      indicators.forEach(indicator => indicator.classList.remove('active'));
      
      // Add active class to current slide and indicator
      slides[index].classList.add('active');
      indicators[index].classList.add('active');
    }

    function changeSlide(direction) {
      currentSlide += direction;
      
      // Loop around
      if (currentSlide >= totalSlides) {
        currentSlide = 0;
      } else if (currentSlide < 0) {
        currentSlide = totalSlides - 1;
      }
      
      showSlide(currentSlide);
      resetAutoSlide();
    }

    function goToSlide(index) {
      currentSlide = index;
      showSlide(currentSlide);
      resetAutoSlide();
    }

    function autoSlide() {
      currentSlide++;
      if (currentSlide >= totalSlides) {
        currentSlide = 0;
      }
      showSlide(currentSlide);
    }

    function resetAutoSlide() {
      clearInterval(autoSlideInterval);
      autoSlideInterval = setInterval(autoSlide, 5000);
    }

    // Start auto-sliding
    autoSlideInterval = setInterval(autoSlide, 5000);

    // Pause auto-slide on hover
    const carouselContainer = document.querySelector('.carousel-container');
    carouselContainer.addEventListener('mouseenter', () => {
      clearInterval(autoSlideInterval);
    });
    
    carouselContainer.addEventListener('mouseleave', () => {
      autoSlideInterval = setInterval(autoSlide, 5000);
    });
  </script>

  <section class="recommended-packages">
    <h2>Discover Your Perfect Journey</h2>
    <p class="section-subtitle">Find the perfect package for your Sri Lankan adventure</p>
    
    <!-- Filter Tabs -->
    <div class="package-filters">
      <button class="filter-btn active" data-filter="all">
        All Packages
      </button>
      <button class="filter-btn" data-filter="region">
        By Region
      </button>
      <button class="filter-btn" data-filter="duration">
        By Duration
      </button>
      <button class="filter-btn" data-filter="experience">
        By Experience
      </button>
      <button class="filter-btn" data-filter="group">
        By Group
      </button>
    </div>

    <!-- Packages Grid -->
    <div class="packages-grid">
      <!-- Region-based Packages -->
      <a href="/CeylonGo/public/tourist/package-details/1" class="package-card" data-category="region" data-tags="central cultural heritage">
        <div class="package-image" style="background-image: url('../../public/images/kandy.jpeg');">
        </div>
        <div class="package-content">
          <h3>Cultural Triangle Tour</h3>
          <p class="package-description">Kandy, Sigiriya & Dambulla</p>
          <div class="package-meta">
            <span class="meta-item">4-5 Days</span>
            <span class="meta-item">Central</span>
            <span class="meta-item">Cultural</span>
          </div>
        </div>
      </a>

      <a href="/CeylonGo/public/tourist/package-details/2" class="package-card" data-category="region" data-tags="south beach relaxation">
        <div class="package-image" style="background-image: url('../../public/images/beach.jpg');">
        </div>
        <div class="package-content">
          <h3>South Coast Beaches</h3>
          <p class="package-description">Galle, Mirissa & Unawatuna</p>
          <div class="package-meta">
            <span class="meta-item">3 Days</span>
            <span class="meta-item">South Coast</span>
            <span class="meta-item">Beach</span>
          </div>
        </div>
      </a>

      <a href="/CeylonGo/public/tourist/package-details/3" class="package-card" data-category="region" data-tags="central nature adventure">
        <div class="package-image" style="background-image: url('../../public/images/greenary.jpg');">
        </div>
        <div class="package-content">
          <h3>Hill Country Experience</h3>
          <p class="package-description">Nuwara Eliya, Ella & Horton Plains</p>
          <div class="package-meta">
            <span class="meta-item">4 Days</span>
            <span class="meta-item">Hill Country</span>
            <span class="meta-item">Nature</span>
          </div>
        </div>
      </a>

    </div>

    <a href="/CeylonGo/public/tourist/recommended-packages" class="btn btn-view-all">
      View All Packages
      <span class="btn-arrow">→</span>
    </a>
  </section>

  <script>
    // Package filtering functionality
    const filterBtns = document.querySelectorAll('.filter-btn');
    const packageCards = document.querySelectorAll('.package-card');

    filterBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        // Remove active class from all buttons
        filterBtns.forEach(b => b.classList.remove('active'));
        // Add active class to clicked button
        btn.classList.add('active');

        const filter = btn.getAttribute('data-filter');

        packageCards.forEach(card => {
          if (filter === 'all') {
            card.style.display = 'block';
            setTimeout(() => card.classList.add('show'), 10);
          } else {
            const category = card.getAttribute('data-category');
            if (category === filter) {
              card.style.display = 'block';
              setTimeout(() => card.classList.add('show'), 10);
            } else {
              card.classList.remove('show');
              setTimeout(() => card.style.display = 'none', 300);
            }
          }
        });
      });
    });

    // Initialize all cards as visible
    packageCards.forEach(card => {
      card.classList.add('show');
    });
  </script>
  </section>

  <!-- ✅ NEW CUSTOMIZE YOUR TRIP SECTION STARTS HERE -->
  <section id="customize" class="customize-trip">
    <div style="margin-bottom: 40px;">
      <h2 style="margin: 0;">Customize Your Trip</h2>
    </div>

    <?php if ($success_message): ?>
      <div class="alert alert-success"><?= $success_message ?></div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
      <div class="alert alert-error"><?= $error_message ?></div>
    <?php endif; ?>

    <?php if (!$is_logged_in): ?>
      <div class="alert alert-info">
        <strong>👋 Welcome Guest!</strong> You can explore and plan your trip, but you'll need to 
        <a href="../login" style="color: #2c5530; font-weight: bold; text-decoration: underline;">login</a> 
        or 
        <a href="../register" style="color: #2c5530; font-weight: bold; text-decoration: underline;">register</a> 
        to complete your booking.
      </div>
    <?php endif; ?>

    <div class="customize-layout">
      <!-- Task Checklist Sidebar -->
      <div class="trip-checklist">
        <h3>📋 Booking Steps</h3>
        <p class="checklist-subtitle">Complete each step</p>
        <ul class="checklist">
          <li class="checklist-item" data-step="people">
            <input type="checkbox" id="step-people" disabled>
            <label for="step-people">
              <span class="step-number">1</span>
              <span class="step-text">Select number of people</span>
            </label>
          </li>
          <li class="checklist-item" data-step="date">
            <input type="checkbox" id="step-date" disabled>
            <label for="step-date">
              <span class="step-number">2</span>
              <span class="step-text">Choose start date</span>
            </label>
          </li>
          <li class="checklist-item" data-step="destination">
            <input type="checkbox" id="step-destination" disabled>
            <label for="step-destination">
              <span class="step-number">3</span>
              <span class="step-text">Select destination</span>
            </label>
          </li>
          <li class="checklist-item" data-step="days">
            <input type="checkbox" id="step-days" disabled>
            <label for="step-days">
              <span class="step-number">4</span>
              <span class="step-text">Specify days staying</span>
            </label>
          </li>
          <li class="checklist-item" data-step="hotel">
            <input type="checkbox" id="step-hotel" disabled>
            <label for="step-hotel">
              <span class="step-number">5</span>
              <span class="step-text">Choose hotel</span>
            </label>
          </li>
          <li class="checklist-item" data-step="transport">
            <input type="checkbox" id="step-transport" disabled>
            <label for="step-transport">
              <span class="step-number">6</span>
              <span class="step-text">Select transport</span>
            </label>
          </li>
          <li class="checklist-item" data-step="guide">
            <input type="checkbox" id="step-guide" disabled>
            <label for="step-guide">
              <span class="step-number">7</span>
              <span class="step-text">Need tour guide?</span>
            </label>
          </li>
        </ul>
      </div>

      <!-- Main Form Content -->
      <div class="form-content">
        <form method="POST" action="/CeylonGo/public/tourist/dashboard" id="customizeTripForm">
          <?php if ($is_logged_in): ?>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
          <?php endif; ?>
          
          <div class="customize-trip-box">
        <div id="trip-group-container">
          <div class="trip-group" data-index="0">
            <div class="row row-1">
              <div class="box box-people">
                <div style="display: flex; align-items: center; gap: 16px; justify-content: space-between;">
                  <div class="box-title">Number of People</div>
                  <div class="number-control">
                    <button type="button" class="decrease-btn" onclick="decreasePeople(this)">−</button>
                    <input
                        type="number"
                        name="people[]"
                        min="1"
                        max="50"
                        value="1"
                        required
                        oninput="if(this.value > 50) this.value = 50; if(this.value < 1) this.value = 1;"
                      >
                    <button type="button" class="increase-btn" onclick="increasePeople(this)">+</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="row row-trip-details">
              <div class="box">
                <div class="form-row">
                  <div class="form-group">
                    <label for="start_date">Start Date of the Trip</label>
                    <input type="date" name="start_date[]" class="trip-start-date" required>
                  </div>
                  <div class="form-group" style="position: relative;">
                    <label for="destination">Where are You Going?</label>
                    <input 
                      type="text" 
                      name="destination[]" 
                      class="destination-input"
                      placeholder="Type a place..."
                      required
                      autocomplete="off"
                    >
                    <div class="destination-autocomplete"></div>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label for="days">How Many Days Do You Plan To Stay?</label>
                    <input type="number" name="days[]" min="1" max="10" placeholder="Number of nights" required oninput="if(this.value > 10) this.value = 10; if(this.value < 1) this.value = 1;">
                  </div>
                  <div class="form-group">
                    <label for="hotel" style="margin-bottom: 8px; visibility: hidden;">Hotel</label>
                    <input type="hidden" name="hotel[]" class="hotel-value" value="">
                    <div style="display: flex; gap: 12px; align-items: center;">
                      <span class="selected-hotel" style="color: #2c5530; font-weight: 600; display: none; flex: 1;"></span>
                      <a href="/CeylonGo/public/tourist/choose-hotel" class="btn-modal-primary choose-hotel-btn" style="text-decoration: none; display: inline-flex; padding: 10px 20px; font-size: 14px;" onclick="return saveFormData();">
                        Choose Hotel
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row row-3">
              <div class="box">
                <div class="inline-control">
                  <div class="box-title">Do You Want Transport?</div>
                  <div class="btn-group">
                    <input type="hidden" name="transport[]" class="transport-value" value="No">
                    <button type="button" class="btn-white transport-yes-btn" onclick="openTransportModal(this)">
                      <span class="btn-icon">✓</span>
                      <span>Yes</span>
                    </button>
                    <button type="button" class="btn-black transport-no-btn active" onclick="selectNoTransport(this)">
                      <span>No</span>
                    </button>
                  </div>
                  <div class="selected-transport-info" style="margin-top: 10px; color: #2c5530; font-weight: 600; display: none;"></div>
                </div>
              </div>
            </div>

            <div class="row row-4">
              <div class="box">
                <div class="inline-control">
                  <div class="box-title">Add a Tour Guide?</div>
                  <div class="btn-group">
                    <input type="hidden" name="guide[]" class="guide-value" value="No">
                    <button type="button" class="btn-white guide-yes-btn" onclick="goToGuideRequestForm(this)">
                      <span class="btn-icon">✓</span>
                      <span>Yes</span>
                    </button>
                    <button type="button" class="btn-black guide-no-btn active" onclick="selectNoGuide(this)">
                      <span>No</span>
                    </button>
                  </div>
                  <div class="selected-guide-info" style="margin-top: 10px; color: #2c5530; font-weight: 600; display: none;"></div>
                </div>
              </div>
            </div>

            <!-- Remove button for this destination (only shown if not first group) -->
            <div class="row row-remove actions-right" style="display: none;">
              <button type="button" class="btn-danger remove-destination-btn">
                <span class="btn-icon">×</span>
                <span>Remove This Destination</span>
              </button>
            </div>

            <!-- Section Separator -->
            <div class="destination-separator"></div>
          </div>
        </div>

        <div class="row row-4 actions-right">
          <button type="button" id="addMore" class="btn-white">
            <span class="btn-icon">+</span>
            <span>Add More</span>
          </button>
        </div>
      </div>

      <div class="center">
        <?php if ($is_logged_in): ?>
          <button type="submit" class="btn-black finish">Finish</button>
        <?php else: ?>
          <a href="../login" class="btn-black finish" style="text-decoration: none; display: inline-block;">Finish</a>
          <p style="margin-top: 10px; color: #5a6b5a; font-size: 14px;">
            <a href="../login" style="color: #2c5530; font-weight: bold;">Login</a> to complete your booking
          </p>
        <?php endif; ?>
      </div>
    </form>
      </div><!-- End form-content -->
    </div><!-- End customize-layout -->

    <script>
    // Local location autocomplete functionality (no external libraries)
    let debounceTimer;
    
    function initializeAutocomplete(wrapper) {
      var input = wrapper.querySelector('.destination-input');
      var list = wrapper.querySelector('.autocomplete-list');

      if (!input || !list) return;

      // Fetch location suggestions from local API
      input.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length < 2) {
          list.innerHTML = '';
          list.classList.remove('show');
          return;
        }

        debounceTimer = setTimeout(async () => {
          try {
            const response = await fetch(`/CeylonGo/api/locations.php?input=${encodeURIComponent(query)}`);
            const data = await response.json();

            list.innerHTML = '';

            if (data.predictions && data.predictions.length > 0) {
              data.predictions.forEach(prediction => {
                const li = document.createElement('li');
                li.textContent = prediction.description;
                li.setAttribute('data-value', prediction.description);
                
                li.addEventListener('click', function(e) {
                  e.preventDefault();
                  input.value = this.getAttribute('data-value');
                  list.innerHTML = '';
                  list.classList.remove('show');
                  input.dispatchEvent(new Event('change'));
                });

                li.addEventListener('mouseover', function() {
                  list.querySelectorAll('li').forEach(item => item.classList.remove('active'));
                  this.classList.add('active');
                });

                list.appendChild(li);
              });
              list.classList.add('show');
            } else {
              list.innerHTML = '<li style="color: #999; cursor: default;">No locations found</li>';
              list.classList.add('show');
            }
          } catch (error) {
            console.error('Location search error:', error);
          }
        }, 300);
      });

      // Close list when clicking outside
      document.addEventListener('click', function(e) {
        if (!wrapper.contains(e.target)) {
          list.classList.remove('show');
        }
      });

      // Keyboard navigation
      input.addEventListener('keydown', function(e) {
        const items = list.querySelectorAll('li');
        const active = list.querySelector('li.active');
        let index = Array.from(items).indexOf(active);

        if (e.key === 'ArrowDown') {
          e.preventDefault();
          index = (index + 1) % items.length;
          items.forEach(item => item.classList.remove('active'));
          items[index]?.classList.add('active');
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          index = index <= 0 ? items.length - 1 : index - 1;
          items.forEach(item => item.classList.remove('active'));
          items[index]?.classList.add('active');
        } else if (e.key === 'Enter' && active) {
          e.preventDefault();
          active.click();
        } else if (e.key === 'Escape') {
          list.classList.remove('show');
        }
      });
    }

    // Initialize autocomplete for first group
    var firstWrapper = document.querySelector('.autocomplete-wrapper');
    if (firstWrapper) {
      initializeAutocomplete(firstWrapper);
    }

    // Handle returning from hotel selection page
    function handleHotelSelection() {
      var selectedHotel = sessionStorage.getItem('selectedHotel');
      if (selectedHotel) {
        // Find the last trip group's hotel input
        var groups = document.querySelectorAll('.trip-group');
        if (groups.length > 0) {
          var lastGroup = groups[groups.length - 1];
          var hotelInput = lastGroup.querySelector('.hotel-value');
          var hotelDisplay = lastGroup.querySelector('.selected-hotel');
          if (hotelInput) {
            hotelInput.value = selectedHotel;
            if (hotelDisplay) {
              hotelDisplay.textContent = '✓ ' + selectedHotel;
            }
          }
        }
        sessionStorage.removeItem('selectedHotel');
      }
    }
    
    // Call this when page loads
    window.addEventListener('load', handleHotelSelection);

    function saveFormData() {
      // Save form data before navigating
      var formData = {
        people: [],
        destinations: [],
        days: [],
        hotels: [],
        transports: [],
        guide: document.getElementById('guideChoice') ? document.getElementById('guideChoice').value : 'No'
      };
      
      var groups = document.querySelectorAll('.trip-group');
      groups.forEach(function(group) {
        var peopleInput = group.querySelector('input[name="people[]"]');
        var destinationInput = group.querySelector('.destination-input');
        var daysInput = group.querySelector('input[name="days[]"]');
        var hotelValue = group.querySelector('.hotel-value');
        var transportValue = group.querySelector('.transport-value');
        
        if (peopleInput) formData.people.push(peopleInput.value);
        if (destinationInput) formData.destinations.push(destinationInput.value);
        if (daysInput) formData.days.push(daysInput.value);
        if (hotelValue) formData.hotels.push(hotelValue.value);
        if (transportValue) formData.transports.push(transportValue.value);
      });
      
      sessionStorage.setItem('tripFormData', JSON.stringify(formData));
      return true;
    }

    function increasePeople(btn) {
      var input = btn.previousElementSibling;
      var value = parseInt(input.value) || 1;

      if (value < 50) {
        input.value = value + 1;
      }
    }

    function decreasePeople(btn) {
      var input = btn.nextElementSibling;
      var value = parseInt(input.value) || 1;

      if (value > 1) {
        input.value = value - 1;
      }
    }
   </script>

    <script>
      (function(){
        var groupIndex = 1;
        
        // Function to update remove buttons visibility and separators
        function updateRemoveButtons() {
          var container = document.getElementById('trip-group-container');
          if (!container) return;
          
          var groups = container.getElementsByClassName('trip-group');
          
          // Update visibility for each group
          for (var i = 0; i < groups.length; i++) {
            var group = groups[i];
            var removeRow = group.querySelector('.row-remove');
            var separator = group.querySelector('.destination-separator');
            
            if (i === 0) {
              // First group - hide remove button, show separator if more than one group
              if (removeRow) removeRow.style.display = 'none';
              if (separator) {
                separator.style.display = groups.length > 1 ? 'block' : 'none';
              }
            } else {
              // Other groups - show remove button
              if (removeRow) removeRow.style.display = 'flex';
              
              // Show separator after each group except the last one
              if (separator) {
                separator.style.display = i < groups.length - 1 ? 'block' : 'none';
              }
            }
          }
        }
        
        // Function to duplicate trip group
        function duplicateTripGroup() {
          var container = document.getElementById('trip-group-container');
          if (!container) {
            console.error('trip-group-container not found');
            return;
          }
          
          var groups = container.getElementsByClassName('trip-group');
          if (!groups.length) {
            console.error('No trip-group found to clone');
            return;
          }
          
          // Clone the first group
          var clone = groups[0].cloneNode(true);
          clone.setAttribute('data-index', groupIndex++);

           // Reset inputs in clone
           var inputs = clone.querySelectorAll('input[type=number], input[type=text], input[type=hidden], select');
           inputs.forEach(function(el){
             if (el.tagName.toLowerCase() === 'select') { 
               el.selectedIndex = 0; 
             } else if (el.closest('.number-control')) {
               // Reset number control to 1
               el.value = '1';
             } else if (!el.classList.contains('hotel-value') && !el.classList.contains('transport-value') && !el.classList.contains('guide-value')) {
               el.value = ''; 
             } else if (el.classList.contains('transport-value')) {
               el.value = 'No';
             } else if (el.classList.contains('guide-value')) {
               el.value = 'No';
             }
           });
          
          // Reset display spans
          var selectedHotel = clone.querySelector('.selected-hotel');
          if (selectedHotel) selectedHotel.textContent = '';
          var selectedTransport = clone.querySelector('.selected-transport');
          if (selectedTransport) selectedTransport.textContent = '';
          
          // Reset transport button states
          var transportNoBtn = clone.querySelector('.transport-no-btn');
          var transportYesBtn = clone.querySelector('.transport-yes-btn');
          if (transportNoBtn) {
            transportNoBtn.classList.add('active');
            transportNoBtn.classList.remove('btn-black');
            transportNoBtn.classList.add('btn-white');
          }
          if (transportYesBtn) {
            transportYesBtn.classList.remove('active');
            transportYesBtn.classList.remove('btn-white');
            transportYesBtn.classList.add('btn-black');
          }

          // Reset guide button states
          var guideNoBtn = clone.querySelector('.guide-no-btn');
          var guideYesBtn = clone.querySelector('.guide-yes-btn');
          if (guideNoBtn) {
            guideNoBtn.classList.add('active');
            guideNoBtn.classList.add('btn-black');
            guideNoBtn.classList.remove('btn-white');
          }
          if (guideYesBtn) {
            guideYesBtn.classList.remove('active');
            guideYesBtn.classList.add('btn-white');
            guideYesBtn.classList.remove('btn-black');
          }

          // Append clone to container
          container.appendChild(clone);
          
          // Attach event listeners to new group
          attachGroupEventListeners(clone);
          
          // Initialize autocomplete for new destination input
          initDestinationAutocomplete();
          
          // Update remove buttons visibility after adding
          updateRemoveButtons();
          
          // Save form data after adding
          saveFormData();
        }
        
        // Save form data to localStorage
        function saveFormData() {
          var formData = {
            people: [],
            destinations: [],
            days: [],
            hotels: [],
            transports: [],
            guides: []
          };
          
          var groups = document.querySelectorAll('.trip-group');
          groups.forEach(function(group) {
            var peopleInput = group.querySelector('input[name="people[]"]');
            var destinationInput = group.querySelector('.destination-input');
            var daysInput = group.querySelector('input[name="days[]"]');
            var hotelValue = group.querySelector('.hotel-value');
            var transportValue = group.querySelector('.transport-value');
            var guideValue = group.querySelector('.guide-value');
            
            if (peopleInput) formData.people.push(peopleInput.value);
            if (destinationInput) formData.destinations.push(destinationInput.value);
            if (daysInput) formData.days.push(daysInput.value);
            if (hotelValue) formData.hotels.push(hotelValue.value);
            if (transportValue) formData.transports.push(transportValue.value);
            if (guideValue) formData.guides.push(guideValue.value);
          });
          
          localStorage.setItem('tripFormData', JSON.stringify(formData));
        }
        
        // Load form data from localStorage
        function loadFormData() {
          var savedData = localStorage.getItem('tripFormData');
          if (savedData) {
            try {
              var formData = JSON.parse(savedData);
              
              // Restore each trip group
              var groups = document.querySelectorAll('.trip-group');
              for (var i = 0; i < formData.destinations.length; i++) {
                if (i > 0 && i >= groups.length) {
                  // Add more groups if needed
                  duplicateTripGroup();
                  groups = document.querySelectorAll('.trip-group');
                }
                
                var group = groups[i];
                if (group) {
                  var peopleInput = group.querySelector('input[name="people[]"]');
                  var destinationInput = group.querySelector('.destination-input');
                  var daysInput = group.querySelector('input[name="days[]"]');
                  var hotelValue = group.querySelector('.hotel-value');
                  var transportValue = group.querySelector('.transport-value');
                  var guideValue = group.querySelector('.guide-value');
                  
                  if (peopleInput) peopleInput.value = formData.people[i] || '';
                  if (destinationInput) destinationInput.value = formData.destinations[i] || '';
                  if (daysInput) daysInput.value = formData.days[i] || '';
                  if (hotelValue) hotelValue.value = formData.hotels[i] || '';
                  if (transportValue) transportValue.value = formData.transports[i] || '';
                  if (guideValue) {
                    guideValue.value = formData.guides && formData.guides[i] ? formData.guides[i] : (formData.guide || 'No');
                    // Update guide button states
                    var guideYesBtn = group.querySelector('.guide-yes-btn');
                    var guideNoBtn = group.querySelector('.guide-no-btn');
                    if (guideValue.value === 'Yes') {
                      if (guideYesBtn) {
                        guideYesBtn.classList.add('active');
                        guideYesBtn.classList.remove('btn-white');
                        guideYesBtn.classList.add('btn-black');
                      }
                      if (guideNoBtn) {
                        guideNoBtn.classList.remove('active');
                        guideNoBtn.classList.remove('btn-black');
                        guideNoBtn.classList.add('btn-white');
                      }
                    }
                  }
                  
                  // Update display
                  if (formData.hotels[i]) {
                    var selectedHotel = group.querySelector('.selected-hotel');
                    if (selectedHotel) selectedHotel.textContent = '✓ ' + formData.hotels[i];
                  }
                  if (formData.transports[i]) {
                    var selectedTransport = group.querySelector('.selected-transport');
                    if (selectedTransport) selectedTransport.textContent = formData.transports[i];
                  }
                }
              }
            } catch(e) {
              console.error('Error loading form data:', e);
            }
          }
        }
        
        // Function to attach event listeners to a group
        function attachGroupEventListeners(group) {
          // Initialize autocomplete for this group
          var wrapper = group.querySelector('.autocomplete-wrapper');
          if (wrapper) {
            initializeAutocomplete(wrapper);
          }

          // Hotel selection button
          var hotelBtn = group.querySelector('.choose-hotel-btn');
          if (hotelBtn) {
            hotelBtn.addEventListener('click', function() {
              saveFormData();
              window.location.href = '/CeylonGo/public/tourist/choose-hotel';
            });
          }
          
          // Note: Transport and Guide buttons now use onclick attributes with modal functions
          // No need to add event listeners here

          // Remove destination button
          var removeBtn = group.querySelector('.remove-destination-btn');
          if (removeBtn) {
            removeBtn.addEventListener('click', function() {
              if (confirm('Are you sure you want to remove this destination?')) {
                group.remove();
                saveFormData();
                updateRemoveButtons();
              }
            });
          }
        }
        
        // Wait for DOM to be ready
        function init() {
          // Load saved data on page load
          loadFormData();
          
          // Update remove buttons on page load (in case there are multiple groups from saved data)
          setTimeout(function() {
            updateRemoveButtons();
          }, 100);
          
          // Add More button functionality
          var addBtn = document.getElementById('addMore');
          if (addBtn) {
            addBtn.addEventListener('click', function(e){
              e.preventDefault();
              duplicateTripGroup();
            });
          } else {
            console.error('addMore button not found');
          }
          
          // Attach event listeners to initial group(s)
          document.querySelectorAll('.trip-group').forEach(function(group) {
            attachGroupEventListeners(group);
          });
          
          // Save data before form submit
          var form = document.getElementById('customizeTripForm');
          if (form) {
            form.addEventListener('submit', function() {
              saveFormData();
            });
          }
          
          // Auto-save on input change
          document.addEventListener('change', function(e) {
            if (e.target.closest('#customizeTripForm')) {
              saveFormData();
            }
          });
          
          // Handle guest user finish button
          var guestFinishBtn = document.getElementById('guestFinishBtn');
          if (guestFinishBtn) {
            guestFinishBtn.addEventListener('click', function() {
              // Save form data first
              saveFormData();
              
              // Show confirmation dialog
              if (confirm('You need to login to complete your booking. Your trip details will be saved. Redirect to login page?')) {
                window.location.href = '../login';
              }
            });
          }
        }
        
        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', init);
        } else {
          init();
        }

        // Function to update booking steps checklist
        function updateBookingSteps() {
          const firstTripGroup = document.querySelector('.trip-group[data-index="0"]');
          if (!firstTripGroup) return;

          // Check people count
          const peopleInput = firstTripGroup.querySelector('input[name="people[]"]');
          const stepPeople = document.getElementById('step-people');
          if (peopleInput && parseInt(peopleInput.value) > 0) {
            stepPeople.checked = true;
            stepPeople.parentElement.classList.add('completed');
          }

          // Check start date
          const dateInput = firstTripGroup.querySelector('input[name="start_date[]"]');
          const stepDate = document.getElementById('step-date');
          if (dateInput && dateInput.value) {
            stepDate.checked = true;
            stepDate.parentElement.classList.add('completed');
          } else {
            stepDate.checked = false;
            stepDate.parentElement.classList.remove('completed');
          }

          // Check destination
          const destinationInput = firstTripGroup.querySelector('input[name="destination[]"]');
          const stepDestination = document.getElementById('step-destination');
          if (destinationInput && destinationInput.value.trim()) {
            stepDestination.checked = true;
            stepDestination.parentElement.classList.add('completed');
          } else {
            stepDestination.checked = false;
            stepDestination.parentElement.classList.remove('completed');
          }

          // Check days
          const daysInput = firstTripGroup.querySelector('input[name="days[]"]');
          const stepDays = document.getElementById('step-days');
          if (daysInput && parseInt(daysInput.value) > 0) {
            stepDays.checked = true;
            stepDays.parentElement.classList.add('completed');
          } else {
            stepDays.checked = false;
            stepDays.parentElement.classList.remove('completed');
          }

          // Check hotel
          const hotelInput = firstTripGroup.querySelector('input[name="hotel[]"]');
          const stepHotel = document.getElementById('step-hotel');
          if (hotelInput && hotelInput.value) {
            stepHotel.checked = true;
            stepHotel.parentElement.classList.add('completed');
          } else {
            stepHotel.checked = false;
            stepHotel.parentElement.classList.remove('completed');
          }

          // Check transport
          const transportInput = firstTripGroup.querySelector('input[name="transport[]"]');
          const stepTransport = document.getElementById('step-transport');
          if (transportInput && transportInput.value && transportInput.value !== 'No') {
            stepTransport.checked = true;
            stepTransport.parentElement.classList.add('completed');
          } else {
            stepTransport.checked = false;
            stepTransport.parentElement.classList.remove('completed');
          }

          // Check guide
          const guideInput = firstTripGroup.querySelector('input[name="guide[]"]');
          const stepGuide = document.getElementById('step-guide');
          if (guideInput && guideInput.value) {
            stepGuide.checked = true;
            stepGuide.parentElement.classList.add('completed');
          } else {
            stepGuide.checked = false;
            stepGuide.parentElement.classList.remove('completed');
          }
        }

        // Add event listeners to form inputs
        function attachBookingStepListeners() {
          const firstTripGroup = document.querySelector('.trip-group[data-index="0"]');
          if (!firstTripGroup) return;

          // People input
          const peopleInput = firstTripGroup.querySelector('input[name="people[]"]');
          if (peopleInput) {
            peopleInput.addEventListener('input', updateBookingSteps);
          }

          // Date input
          const dateInput = firstTripGroup.querySelector('input[name="start_date[]"]');
          if (dateInput) {
            dateInput.addEventListener('change', updateBookingSteps);
          }

          // Destination input
          const destinationInput = firstTripGroup.querySelector('input[name="destination[]"]');
          if (destinationInput) {
            destinationInput.addEventListener('input', updateBookingSteps);
          }

          // Days input
          const daysInput = firstTripGroup.querySelector('input[name="days[]"]');
          if (daysInput) {
            daysInput.addEventListener('input', updateBookingSteps);
          }

          // Hotel input (hidden field)
          const hotelInput = firstTripGroup.querySelector('input[name="hotel[]"]');
          if (hotelInput) {
            const observer = new MutationObserver(updateBookingSteps);
            observer.observe(hotelInput, { attributes: true, attributeFilter: ['value'] });
          }

          // Transport input (hidden field)
          const transportInput = firstTripGroup.querySelector('input[name="transport[]"]');
          if (transportInput) {
            const observer = new MutationObserver(updateBookingSteps);
            observer.observe(transportInput, { attributes: true, attributeFilter: ['value'] });
          }

          // Guide input (hidden field)
          const guideInput = firstTripGroup.querySelector('input[name="guide[]"]');
          if (guideInput) {
            const observer = new MutationObserver(updateBookingSteps);
            observer.observe(guideInput, { attributes: true, attributeFilter: ['value'] });
          }

          // Initial check
          updateBookingSteps();
        }

        // Call this after DOM is loaded
        setTimeout(attachBookingStepListeners, 500);

        // Destination Autocomplete Functionality
        function initDestinationAutocomplete() {
          const destinationInputs = document.querySelectorAll('.destination-input');
          
          destinationInputs.forEach(input => {
            const autocompleteDiv = input.nextElementSibling;
            if (!autocompleteDiv || !autocompleteDiv.classList.contains('destination-autocomplete')) return;
            
            let selectedIndex = -1;
            let debounceTimer;

            input.addEventListener('input', function() {
              const value = this.value.trim();
              autocompleteDiv.innerHTML = '';
              selectedIndex = -1;

              // Clear previous timer
              clearTimeout(debounceTimer);

              if (value.length < 2) {
                autocompleteDiv.classList.remove('active');
                return;
              }

              // Debounce for local suggestions only (no external APIs)
              debounceTimer = setTimeout(async () => {
                try {
                  // Fetch from local locations API
                  const response = await fetch(`/CeylonGo/api/locations.php?input=${encodeURIComponent(value)}`);
                  const data = await response.json();

                  if (data.predictions && data.predictions.length > 0) {
                    data.predictions.forEach((prediction, index) => {
                      const item = document.createElement('div');
                      item.className = 'destination-suggestion-item';
                      item.innerHTML = `
                        <span class="name">${prediction.description}</span>
                      `;
                      item.addEventListener('click', function() {
                        input.value = prediction.description;
                        autocompleteDiv.classList.remove('active');
                        autocompleteDiv.innerHTML = '';
                        // Trigger input event to update booking steps
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                      });
                      autocompleteDiv.appendChild(item);
                    });
                    autocompleteDiv.classList.add('active');
                  } else {
                    const noResults = document.createElement('div');
                    noResults.className = 'destination-no-suggestions';
                    noResults.textContent = 'No locations found. Try: Colombo, Kandy, Galle...';
                    autocompleteDiv.appendChild(noResults);
                    autocompleteDiv.classList.add('active');
                  }
                } catch (error) {
                  console.error('Autocomplete error:', error);
                  // Fallback to empty state
                  autocompleteDiv.classList.remove('active');
                }
              }, 300); // 300ms debounce
            });

            // Keyboard navigation
            input.addEventListener('keydown', function(e) {
              const items = autocompleteDiv.querySelectorAll('.destination-suggestion-item');
              
              if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                updateSelection(items);
              } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = Math.max(selectedIndex - 1, -1);
                updateSelection(items);
              } else if (e.key === 'Enter' && selectedIndex >= 0) {
                e.preventDefault();
                items[selectedIndex].click();
              } else if (e.key === 'Escape') {
                autocompleteDiv.classList.remove('active');
              }
            });

            function updateSelection(items) {
              items.forEach((item, index) => {
                if (index === selectedIndex) {
                  item.classList.add('selected');
                  item.scrollIntoView({ block: 'nearest' });
                } else {
                  item.classList.remove('selected');
                }
              });
            }

            // Close suggestions when clicking outside
            document.addEventListener('click', function(e) {
              if (!input.contains(e.target) && !autocompleteDiv.contains(e.target)) {
                autocompleteDiv.classList.remove('active');
              }
            });
          });
        }

        // Initialize destination autocomplete on page load and after adding new trip groups
        setTimeout(initDestinationAutocomplete, 500);

        // Set minimum date for all trip start date inputs to today
        function setMinDateForTripStartDates() {
          const today = new Date().toISOString().split('T')[0];
          const dateInputs = document.querySelectorAll('.trip-start-date');
          dateInputs.forEach(input => {
            input.setAttribute('min', today);
          });
        }
        
        // Call on load
        setMinDateForTripStartDates();
        
        // Also set min date when new trip groups are added
        const observer = new MutationObserver(function(mutations) {
          mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
              setMinDateForTripStartDates();
            }
          });
        });
        
        const container = document.getElementById('trip-group-container');
        if (container) {
          observer.observe(container, { childList: true, subtree: true });
        }
      })();

      // Checklist auto-update functionality
      (function() {
        function updateChecklist() {
          const firstGroup = document.querySelector('.trip-group[data-index="0"]');
          if (!firstGroup) return;

          // Step 1: Number of people
          const peopleInput = firstGroup.querySelector('input[name="people[]"]');
          const peopleCheckbox = document.getElementById('step-people');
          if (peopleInput && peopleCheckbox) {
            peopleCheckbox.checked = peopleInput.value && parseInt(peopleInput.value) > 0;
          }

          // Step 2: Start date
          const dateInput = firstGroup.querySelector('input[name="start_dates[]"]');
          const dateCheckbox = document.getElementById('step-date');
          if (dateInput && dateCheckbox) {
            dateCheckbox.checked = dateInput.value && dateInput.value.trim() !== '';
          }

          // Step 3: Destination
          const destinationSelect = firstGroup.querySelector('select[name="destinations[]"]');
          const destinationCheckbox = document.getElementById('step-destination');
          if (destinationSelect && destinationCheckbox) {
            destinationCheckbox.checked = destinationSelect.value && destinationSelect.value !== '';
          }

          // Step 4: Number of days
          const daysInput = firstGroup.querySelector('input[name="days[]"]');
          const daysCheckbox = document.getElementById('step-days');
          if (daysInput && daysCheckbox) {
            daysCheckbox.checked = daysInput.value && parseInt(daysInput.value) > 0;
          }

          // Step 5: Hotel
          const hotelSelect = firstGroup.querySelector('select[name="hotels[]"]');
          const hotelCheckbox = document.getElementById('step-hotel');
          if (hotelSelect && hotelCheckbox) {
            hotelCheckbox.checked = hotelSelect.value && hotelSelect.value !== '';
          }

          // Step 6: Transport
          const transportRadios = firstGroup.querySelectorAll('input[name="transports[]"]');
          const transportCheckbox = document.getElementById('step-transport');
          if (transportRadios && transportCheckbox) {
            let transportSelected = false;
            transportRadios.forEach(radio => {
              if (radio.checked) transportSelected = true;
            });
            transportCheckbox.checked = transportSelected;
          }

          // Step 7: Guide
          const guideRadios = document.querySelectorAll('input[name="guide"]');
          const guideCheckbox = document.getElementById('step-guide');
          if (guideRadios && guideCheckbox) {
            let guideSelected = false;
            guideRadios.forEach(radio => {
              if (radio.checked) guideSelected = true;
            });
            guideCheckbox.checked = guideSelected;
          }
        }

        // Initial check
        setTimeout(updateChecklist, 500);

        // Add event listeners to form inputs
        document.addEventListener('change', updateChecklist);
        document.addEventListener('input', updateChecklist);
        document.addEventListener('click', function(e) {
          if (e.target.classList.contains('increase-btn') || 
              e.target.classList.contains('decrease-btn')) {
            setTimeout(updateChecklist, 100);
          }
        });
      })();
    </script>

    <?php if (!empty($destinations) && !empty($destinations[0])): ?>
      <div class="summary">
        <h3>Trip Summary</h3>
        <?php for ($i = 0; $i < count($destinations); $i++): ?>
          <div class="summary-item">
            <h4>Destination <?= $i + 1 ?></h4>
            <p><strong>Destination:</strong> <?= htmlspecialchars($destinations[$i]) ?></p>
            <p><strong>No of People:</strong> <?= isset($people[$i]) ? htmlspecialchars($people[$i]) : 'N/A' ?></p>
            <p><strong>Days:</strong> <?= isset($days[$i]) ? htmlspecialchars($days[$i]) : 'N/A' ?></p>
            <?php if (isset($hotels[$i]) && $hotels[$i]): ?>
              <p><strong>Hotel:</strong> <?= htmlspecialchars($hotels[$i]) ?></p>
            <?php endif; ?>
            <p><strong>Transport:</strong> <?= isset($transports[$i]) ? htmlspecialchars($transports[$i]) : 'No' ?></p>
          </div>
          <?php if ($i < count($destinations) - 1): ?>
            <hr>
          <?php endif; ?>
        <?php endfor; ?>
        <p><strong>Tour Guide:</strong> <?= htmlspecialchars($guide) ?></p>
      </div>
    <?php endif; ?>
  </section>
  <!-- ✅ END CUSTOMIZE YOUR TRIP SECTION -->


  <section class="recommended-destinations">
    <h2>Recommended Destinations</h2>
    <div class="destinations">
      <div class="destination">
        <div class="destination-image"></div>
        <p>Heritance Tea Factory</p>
        <p>Nuwara Eliya</p>
      </div>
      <div class="destination">
        <div class="destination-image"></div>
        <p>Cinnamon Resort</p>
        <p>Unawatuna</p>
      </div>
      <div class="destination">
        <div class="destination-image"></div>
        <p>Hotel Sigiriya</p>
        <p>Sigiriya</p>
      </div>
    </div>
  </section>

  <section class="client-reviews">
    <h2>What Our Clients Say</h2>
    <div class="reviews">
      <div class="review">
        <p>Alice</p>
        <div class="stars">⭐⭐⭐⭐</div>
        <p>An unforgettable experience!</p>
      </div>
      <div class="review">
        <p>Sara</p>
        <div class="stars">⭐⭐⭐⭐⭐</div>
        <p>Everything was well-organized and exciting!</p>
      </div>
      <div class="review">
        <p>John</p>
        <div class="stars">⭐⭐⭐⭐⭐</div>
        <p>Highly recommend customizing your trip.</p>
      </div>
    </div>
    <div class="review-action">
      <a href="/CeylonGo/public/tourist/add-review" class="btn-review">
        <span>✍️</span> Share Your Experience
      </a>
    </div>
  </section>

  <!-- Navbar include -->
  <?php include 'footer.php'; ?>

  <!-- Transport Request Modal -->
  <div id="transportModal" class="service-modal">
    <div class="service-modal-content">
      <div class="service-modal-header">
        <h3>Request Transport Service</h3>
        <button type="button" class="service-modal-close" onclick="closeTransportModal()">&times;</button>
      </div>
      <div class="service-modal-body">
        <form id="transportForm" class="modal-form">
          <!-- Customer Name and Contact Number -->
          <div class="form-row">
            <div class="form-group">
              <label for="transportCustomerName">Customer Name</label>
              <input type="text" id="transportCustomerName" name="customerName" placeholder="Enter your full name" value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>" required>
            </div>
            <div class="form-group">
              <label for="transportContactNumber">Contact Number</label>
              <input type="tel" id="transportContactNumber" name="contactNumber" placeholder="e.g., 07X XXX XXXX" value="<?php echo isset($tourist_data['contact_number']) ? htmlspecialchars($tourist_data['contact_number']) : ''; ?>" required pattern="[0-9]{10}">
            </div>
          </div>

          <!-- Date and No. of People -->
          <div class="form-row">
            <div class="form-group">
              <label for="transportDate">Date</label>
              <input type="date" id="transportDate" name="date" required>
            </div>
            <div class="form-group">
              <label for="transportNumPeople">No. of People</label>
              <input type="number" id="transportNumPeople" name="numPeople" min="1" placeholder="Number of passengers" required>
            </div>
          </div>

          <!-- Vehicle Type and Pickup Location -->
          <div class="form-row">
            <div class="form-group">
              <label for="transportVehicleType">Vehicle Type</label>
              <select id="transportVehicleType" name="vehicleType" required>
                <option value="">Select a vehicle</option>
                <option value="Tuk">Tuk (3 People)</option>
                <option value="Car">Car (4 People)</option>
                <option value="SUV">SUV (4 People)</option>
                <option value="Minivan">Minivan (5 People)</option>
                <option value="Bus">Bus (20 People)</option>
              </select>
            </div>
            <div class="form-group">
              <label for="transportPickup">Pickup Location</label>
              <input type="text" id="transportPickup" name="pickupLocation" placeholder="e.g., Bandaranaike Airport" required>
            </div>
          </div>

          <!-- Pickup Time and Dropoff Location -->
          <div class="form-row">
            <div class="form-group">
              <label for="transportTime">Pickup Time</label>
              <input type="time" id="transportTime" name="pickupTime" required>
            </div>
            <div class="form-group">
              <label for="transportDropoff">Dropoff Location</label>
              <input type="text" id="transportDropoff" name="dropoffLocation" placeholder="e.g., Galle Fort" required>
            </div>
          </div>

          <!-- Notes and Fare Estimation -->
          <div class="form-row">
            <div class="form-group">
              <label for="transportNotes">Notes (optional)</label>
              <input type="text" id="transportNotes" name="notes" placeholder="Any extra details">
            </div>
            <div class="form-group">
              <label for="estimatedFare">Estimated Fare</label>
              <div style="display: flex; gap: 10px; align-items: center;">
                <input type="text" id="estimatedFare" name="estimatedFare" placeholder="LKR 0.00" readonly style="flex: 1;">
                <button type="button" class="btn-calculate-fare" onclick="calculateFare()" title="Calculate fare based on distance and vehicle type">Calculate</button>
              </div>
            </div>
          </div>

          <div id="fareBreakdown" style="display: none; padding: 12px; background: #f0f7f0; border-radius: 6px; margin-bottom: 16px; font-size: 13px; color: #2d4a2d;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
              <span>Distance:</span>
              <span id="fareDistance">0 km</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
              <span>Base Rate:</span>
              <span id="fareBaseRate">LKR 0.00</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-weight: 600;">
              <span>Total Fare:</span>
              <span id="fareTotalAmount">LKR 0.00</span>
            </div>
          </div>

          <div class="modal-actions">
            <button type="button" class="btn-modal-primary" onclick="confirmTransport()">Confirm Selection</button>
            <button type="button" class="btn-modal-outline" onclick="closeTransportModal()">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Tour Guide Request Modal -->
  <div id="guideModal" class="service-modal">
    <div class="service-modal-content">
      <div class="service-modal-header">
        <h3>Tour Guide Request</h3>
        <button type="button" class="service-modal-close" onclick="closeGuideModal()">&times;</button>
      </div>
      <div class="service-modal-body">
        <p class="modal-subtitle">Provide your preferences to request a tour guide</p>
        <form id="guideRequestForm" class="modal-form">
          <div class="form-row">
            <div class="form-group">
              <label for="guideCustomerName">Customer Name</label>
              <input type="text" id="guideCustomerName" name="customerName" placeholder="Enter your full name" value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>" required>
            </div>
            <div class="form-group">
              <label for="guideContact">Contact Number</label>
              <input type="tel" id="guideContact" name="contact" placeholder="e.g., +94 77 123 4567" value="<?php echo isset($tourist_data['contact_number']) ? htmlspecialchars($tourist_data['contact_number']) : ''; ?>" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group" style="position: relative;">
              <label for="guideLocation">Location</label>
              <input type="text" id="guideLocation" name="location" placeholder="e.g., Kandy" autocomplete="off" required>
              <div id="locationSuggestions" class="autocomplete-suggestions"></div>
            </div>
            <div class="form-group">
              <label for="guideLanguage">Preferred Language</label>
              <select id="guideLanguage" name="language" required>
                <option value="">Select language</option>
                <option value="English">English</option>
                <option value="Sinhala">Sinhala</option>
                <option value="Tamil">Tamil</option>
                <option value="Hindi">Hindi</option>
                <option value="French">French</option>
                <option value="Spanish">Spanish</option>
                <option value="Chinese">Chinese (Mandarin)</option>
                <option value="German">German</option>
                <option value="Japanese">Japanese</option>
                <option value="Arabic">Arabic</option>
                <option value="Russian">Russian</option>
                <option value="Italian">Italian</option>
                <option value="Portuguese">Portuguese</option>
                <option value="Korean">Korean</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="guideDate">Preferred Date</label>
              <input type="date" id="guideDate" name="date" required>
            </div>
            <div class="form-group">
              <label for="guideTime">Preferred Time</label>
              <select id="guideTime" name="time" required style="padding: 12px 14px; border: 1.5px solid #d0d7d0; border-radius: 6px; font-size: 14px; width: 100%; background: #fff;">
                <option value="">Select time</option>
                <option value="06:00">06:00 AM</option>
                <option value="06:30">06:30 AM</option>
                <option value="07:00">07:00 AM</option>
                <option value="07:30">07:30 AM</option>
                <option value="08:00">08:00 AM</option>
                <option value="08:30">08:30 AM</option>
                <option value="09:00">09:00 AM</option>
                <option value="09:30">09:30 AM</option>
                <option value="10:00">10:00 AM</option>
                <option value="10:30">10:30 AM</option>
                <option value="11:00">11:00 AM</option>
                <option value="11:30">11:30 AM</option>
                <option value="12:00">12:00 PM</option>
                <option value="12:30">12:30 PM</option>
                <option value="13:00">01:00 PM</option>
                <option value="13:30">01:30 PM</option>
                <option value="14:00">02:00 PM</option>
                <option value="14:30">02:30 PM</option>
                <option value="15:00">03:00 PM</option>
                <option value="15:30">03:30 PM</option>
                <option value="16:00">04:00 PM</option>
                <option value="16:30">04:30 PM</option>
                <option value="17:00">05:00 PM</option>
                <option value="17:30">05:30 PM</option>
                <option value="18:00">06:00 PM</option>
                <option value="18:30">06:30 PM</option>
                <option value="19:00">07:00 PM</option>
                <option value="19:30">07:30 PM</option>
                <option value="20:00">08:00 PM</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group full-width">
              <label for="guideNotes">Notes (optional)</label>
              <input type="text" id="guideNotes" name="notes" placeholder="Any special requests">
            </div>
          </div>

          <div class="modal-actions">
            <button type="submit" class="btn-modal-primary">Submit Request</button>
            <button type="button" class="btn-modal-outline" onclick="closeGuideModal()">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Transport Modal Functions
    let currentTransportGroup = null;

    function openTransportModal(button) {
      currentTransportGroup = button.closest('.trip-group');
      
      // Pre-fill number of people from the group
      const peopleInput = currentTransportGroup.querySelector('input[name="people[]"]');
      if (peopleInput && peopleInput.value) {
        document.getElementById('transportNumPeople').value = peopleInput.value;
      }
      
      // Set minimum date to today
      const today = new Date().toISOString().split('T')[0];
      document.getElementById('transportDate').min = today;
      
      // Initialize time restrictions
      setTimeout(updateTimeRestrictions, 100);
      
      document.getElementById('transportModal').style.display = 'block';
      document.body.style.overflow = 'hidden';
    }

    function openTransportModalStandalone() {
      currentTransportGroup = null;
      const today = new Date().toISOString().split('T')[0];
      document.getElementById('transportDate').min = today;
      setTimeout(updateTimeRestrictions, 100);
      document.getElementById('transportModal').style.display = 'block';
      document.body.style.overflow = 'hidden';
    }

    function closeTransportModal() {
      document.getElementById('transportModal').style.display = 'none';
      document.body.style.overflow = 'auto';
      document.getElementById('transportForm').reset();
      currentTransportGroup = null;
    }

    async function calculateFare() {
      const pickup = document.getElementById('transportPickup').value.trim();
      const dropoff = document.getElementById('transportDropoff').value.trim();
      const vehicleType = document.getElementById('transportVehicleType').value;

      if (!pickup || !dropoff) {
        alert('Please enter both pickup and dropoff locations');
        return;
      }

      if (!vehicleType) {
        alert('Please select a vehicle type');
        return;
      }

      // Show loading state
      const fareInput = document.getElementById('estimatedFare');
      fareInput.value = 'Calculating...';

      try {
        // Call our backend API to calculate fare
        const params = new URLSearchParams({
          pickup: pickup,
          dropoff: dropoff,
          vehicleType: vehicleType
        });
        
        const response = await fetch(`/CeylonGo/public/api/calculate-fare?${params}`);
        const data = await response.json();

        if (!response.ok || !data.success) {
          fareInput.value = 'Location not found';
          alert(data.error || 'Failed to calculate fare. Please check your locations.');
          return;
        }

        // Display fare
        fareInput.value = 'LKR ' + data.totalFare;

        // Show breakdown
        document.getElementById('fareDistance').textContent = data.distance + ' km';
        document.getElementById('fareBaseRate').textContent = 'LKR ' + data.baseRate + '/km';
        document.getElementById('fareTotalAmount').textContent = 'LKR ' + data.totalFare;
        document.getElementById('fareBreakdown').style.display = 'block';

        console.log('Fare calculated:', data);

      } catch (error) {
        console.error('Fare calculation error:', error);
        fareInput.value = 'Error calculating fare';
        alert('An error occurred. Please try again.');
      }
    }

    async function geocodeLocation(location) {
      try {
        const params = new URLSearchParams({ location: location });
        const response = await fetch(`/CeylonGo/public/api/geocode?${params}`);
        const data = await response.json();
        
        if (data && data.success) {
          return {
            lat: data.lat,
            lon: data.lon,
            name: data.name
          };
        }
        return null;
      } catch (error) {
        console.error('Geocoding error:', error);
        return null;
      }
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
      // Haversine formula to calculate distance between two points
      const R = 6371; // Earth's radius in kilometers
      const dLat = toRad(lat2 - lat1);
      const dLon = toRad(lon2 - lon1);
      
      const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
      
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      const distance = R * c;
      
      return distance;
    }

    function toRad(degrees) {
      return degrees * (Math.PI / 180);
    }

    // Validate date and time
    function validateDateTime(dateStr, timeStr) {
      if (!dateStr || !timeStr) {
        alert('Please select both date and time for pickup.');
        return false;
      }

      const now = new Date();
      const selectedDateTime = new Date(dateStr + 'T' + timeStr);
      
      // Check if date/time is in the past
      if (selectedDateTime < now) {
        alert('Pickup time cannot be in the past. Please select a future date and time.');
        document.getElementById('transportDate').focus();
        return false;
      }

      // Check if booking is at least 2 hours in advance
      const twoHoursFromNow = new Date(now.getTime() + (2 * 60 * 60 * 1000));
      if (selectedDateTime < twoHoursFromNow) {
        alert('Please book at least 2 hours in advance.');
        document.getElementById('transportTime').focus();
        return false;
      }

      // Check if time is within operating hours (5 AM - 11 PM)
      const selectedHour = selectedDateTime.getHours();
      if (selectedHour < 5 || selectedHour >= 23) {
        alert('Service is available between 5:00 AM and 11:00 PM only.');
        document.getElementById('transportTime').focus();
        return false;
      }

      return true;
    }

    // Add real-time validation on date and time change
    document.addEventListener('DOMContentLoaded', function() {
      const dateInput = document.getElementById('transportDate');
      const timeInput = document.getElementById('transportTime');
      
      if (dateInput) {
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;
        
        dateInput.addEventListener('change', function() {
          updateTimeRestrictions();
          if (timeInput.value) {
            validateDateTimeFields();
          }
        });
      }
      
      if (timeInput) {
        timeInput.addEventListener('change', function() {
          if (dateInput.value) {
            validateDateTimeFields();
          }
        });
      }
      
      // Initial time restrictions setup
      updateTimeRestrictions();
    });

    function updateTimeRestrictions() {
      const dateInput = document.getElementById('transportDate');
      const timeInput = document.getElementById('transportTime');
      
      if (!dateInput || !timeInput) return;
      
      const selectedDate = dateInput.value;
      const today = new Date().toISOString().split('T')[0];
      
      if (selectedDate === today) {
        // For today, set minimum time to 2 hours from now
        const now = new Date();
        const twoHoursLater = new Date(now.getTime() + (2 * 60 * 60 * 1000));
        
        // Format time as HH:MM
        const hours = String(twoHoursLater.getHours()).padStart(2, '0');
        const minutes = String(twoHoursLater.getMinutes()).padStart(2, '0');
        const minTime = `${hours}:${minutes}`;
        
        // Set min time, but also respect operating hours (5 AM - 11 PM)
        if (twoHoursLater.getHours() >= 23) {
          // Too late today, show message
          timeInput.disabled = true;
          timeInput.value = '';
          alert('Too late to book for today. Service ends at 11:00 PM and requires 2 hours advance booking. Please select a future date.');
          dateInput.value = '';
          timeInput.disabled = false;
          return;
        }
        
        timeInput.min = minTime;
        timeInput.max = '23:00';
        
        // Clear the time if it's now invalid
        if (timeInput.value && timeInput.value < minTime) {
          timeInput.value = '';
        }
      } else {
        // For future dates, only apply operating hours
        timeInput.min = '05:00';
        timeInput.max = '23:00';
      }
    }

    function validateDateTimeFields() {
      const dateStr = document.getElementById('transportDate').value;
      const timeStr = document.getElementById('transportTime').value;
      
      if (!dateStr || !timeStr) return;
      
      const now = new Date();
      const selectedDateTime = new Date(dateStr + 'T' + timeStr);
      const twoHoursFromNow = new Date(now.getTime() + (2 * 60 * 60 * 1000));
      const selectedHour = selectedDateTime.getHours();
      
      // Clear previous custom validity
      document.getElementById('transportDate').setCustomValidity('');
      document.getElementById('transportTime').setCustomValidity('');
      
      if (selectedDateTime < now) {
        document.getElementById('transportTime').setCustomValidity('Pickup time cannot be in the past');
      } else if (selectedDateTime < twoHoursFromNow) {
        document.getElementById('transportTime').setCustomValidity('Please book at least 2 hours in advance');
      } else if (selectedHour < 5 || selectedHour >= 23) {
        document.getElementById('transportTime').setCustomValidity('Service available between 5:00 AM and 11:00 PM only');
      }
    }

    function confirmTransport() {
      const form = document.getElementById('transportForm');
      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }
      
      // Validate date and time
      const selectedDate = document.getElementById('transportDate').value;
      const selectedTime = document.getElementById('transportTime').value;
      
      if (!validateDateTime(selectedDate, selectedTime)) {
        return;
      }
      
      // Collect form data
      const formData = {
        customerName: document.getElementById('transportCustomerName').value,
        contactNumber: document.getElementById('transportContactNumber').value,
        date: selectedDate,
        numPeople: document.getElementById('transportNumPeople').value,
        vehicleType: document.getElementById('transportVehicleType').value,
        pickupLocation: document.getElementById('transportPickup').value,
        pickupTime: selectedTime,
        dropoffLocation: document.getElementById('transportDropoff').value,
        notes: document.getElementById('transportNotes').value,
        estimatedFare: document.getElementById('estimatedFare').value,
        distance: document.getElementById('fareDistance') ? document.getElementById('fareDistance').textContent.replace(' km', '') : null
      };
      
      // Save to database via AJAX
      fetch('/CeylonGo/controllers/tourist/save_transport_request.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const hadTripGroup = currentTransportGroup;
          closeTransportModal();
          if (hadTripGroup) {
            const vehicleType = formData.vehicleType;
            const transportValue = hadTripGroup.querySelector('.transport-value');
            const yesBtn = hadTripGroup.querySelector('.transport-yes-btn');
            const noBtn = hadTripGroup.querySelector('.transport-no-btn');
            const infoDiv = hadTripGroup.querySelector('.selected-transport-info');
            if (transportValue) transportValue.value = vehicleType;
            if (yesBtn) yesBtn.classList.add('active');
            if (noBtn) noBtn.classList.remove('active');
            if (infoDiv) {
              infoDiv.textContent = '✓ ' + vehicleType + ' booked (Request #' + (data.requestId || '') + ')';
              infoDiv.style.display = 'block';
            }
            if (typeof updateBookingSteps === 'function') updateBookingSteps();
          }
          window.location.href = '/CeylonGo/public/tourist/transport-report';
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting your request. Please try again.');
      });
    }

    function selectNoTransport(button) {
      const group = button.closest('.trip-group');
      const transportValue = group.querySelector('.transport-value');
      const yesBtn = group.querySelector('.transport-yes-btn');
      const noBtn = group.querySelector('.transport-no-btn');
      const infoDiv = group.querySelector('.selected-transport-info');
      
      transportValue.value = 'No';
      yesBtn.classList.remove('active');
      noBtn.classList.add('active');
      infoDiv.style.display = 'none';
      infoDiv.textContent = '';
      
      // Update booking steps
      if (typeof updateBookingSteps === 'function') {
        updateBookingSteps();
      }
    }

    // Guide Modal Functions
    let currentGuideGroup = null;

    function goToGuideRequestForm(button) {
      if (button && button.closest) {
        currentGuideGroup = button.closest('.trip-group');
      } else if (typeof event !== 'undefined' && event.target) {
        currentGuideGroup = event.target.closest('.trip-group');
      }
      
      // Set minimum date to today
      const today = new Date().toISOString().split('T')[0];
      document.getElementById('guideDate').min = today;
      
      document.getElementById('guideModal').style.display = 'block';
      document.body.style.overflow = 'hidden';
    }

    function closeGuideModal() {
      document.getElementById('guideModal').style.display = 'none';
      document.body.style.overflow = 'auto';
      document.getElementById('guideRequestForm').reset();
      currentGuideGroup = null;
    }

    function selectNoGuide(button) {
      const group = button.closest('.trip-group');
      const guideValue = group.querySelector('.guide-value');
      const yesBtn = group.querySelector('.guide-yes-btn');
      const noBtn = group.querySelector('.guide-no-btn');
      const infoDiv = group.querySelector('.selected-guide-info');
      
      guideValue.value = 'No';
      yesBtn.classList.remove('active');
      noBtn.classList.add('active');
      infoDiv.style.display = 'none';
      infoDiv.textContent = '';
      
      // Update booking steps
      if (typeof updateBookingSteps === 'function') {
        updateBookingSteps();
      }
    }

    // Location autocomplete for guide request modal
    let locationDebounceTimer;
    const locationInput = document.getElementById('guideLocation');
    const locationSuggestions = document.getElementById('locationSuggestions');
    
    if (locationInput && locationSuggestions) {
      let selectedLocationIndex = -1;
      
      locationInput.addEventListener('input', function() {
        clearTimeout(locationDebounceTimer);
        const query = this.value.trim();
        
        if (query.length < 2) {
          locationSuggestions.innerHTML = '';
          locationSuggestions.classList.remove('active');
          return;
        }
        
        locationDebounceTimer = setTimeout(async () => {
          try {
            const response = await fetch(`/CeylonGo/api/locations.php?input=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            locationSuggestions.innerHTML = '';
            
            if (data.predictions && data.predictions.length > 0) {
              data.predictions.forEach((prediction, index) => {
                const item = document.createElement('div');
                item.className = 'suggestion-item';
                item.textContent = prediction.description;
                item.addEventListener('click', function() {
                  locationInput.value = prediction.description;
                  locationSuggestions.classList.remove('active');
                  locationSuggestions.innerHTML = '';
                });
                locationSuggestions.appendChild(item);
              });
              locationSuggestions.classList.add('active');
            } else {
              locationSuggestions.innerHTML = '<div class="suggestion-loading">No locations found</div>';
              locationSuggestions.classList.add('active');
            }
          } catch (error) {
            console.error('Location autocomplete error:', error);
          }
        }, 300);
      });
      
      // Keyboard navigation
      locationInput.addEventListener('keydown', function(e) {
        const items = locationSuggestions.querySelectorAll('.suggestion-item');
        
        if (e.key === 'ArrowDown') {
          e.preventDefault();
          selectedLocationIndex = Math.min(selectedLocationIndex + 1, items.length - 1);
          updateLocationSelection(items);
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          selectedLocationIndex = Math.max(selectedLocationIndex - 1, -1);
          updateLocationSelection(items);
        } else if (e.key === 'Enter' && selectedLocationIndex >= 0 && items[selectedLocationIndex]) {
          e.preventDefault();
          items[selectedLocationIndex].click();
        } else if (e.key === 'Escape') {
          locationSuggestions.classList.remove('active');
        }
      });
      
      function updateLocationSelection(items) {
        items.forEach((item, index) => {
          if (index === selectedLocationIndex) {
            item.classList.add('active');
          } else {
            item.classList.remove('active');
          }
        });
      }
      
      // Close when clicking outside
      document.addEventListener('click', function(e) {
        if (!locationInput.contains(e.target) && !locationSuggestions.contains(e.target)) {
          locationSuggestions.classList.remove('active');
        }
      });
    }

    // Guide request form submission
    document.addEventListener('DOMContentLoaded', function() {
      // Update available times based on selected date
      const dateInput = document.getElementById('guideDate');
      const timeSelect = document.getElementById('guideTime');
      
      function updateAvailableTimes() {
        if (!dateInput.value) return;
        
        const selectedDate = new Date(dateInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        selectedDate.setHours(0, 0, 0, 0);
        
        const isToday = selectedDate.getTime() === today.getTime();
        const now = new Date();
        const currentHour = now.getHours();
        const currentMinute = now.getMinutes();
        
        // Enable/disable time options based on current time
        Array.from(timeSelect.options).forEach(option => {
          if (option.value === '') return; // Skip placeholder
          
          if (isToday) {
            const [hour, minute] = option.value.split(':').map(Number);
            const isPast = hour < currentHour || (hour === currentHour && minute <= currentMinute);
            option.disabled = isPast;
            if (isPast) {
              option.style.color = '#ccc';
            } else {
              option.style.color = '';
            }
          } else {
            option.disabled = false;
            option.style.color = '';
          }
        });
        
        // If currently selected time is now disabled, clear selection
        if (timeSelect.value && timeSelect.options[timeSelect.selectedIndex].disabled) {
          timeSelect.value = '';
        }
      }
      
      if (dateInput && timeSelect) {
        dateInput.addEventListener('change', updateAvailableTimes);
        // Check on load if date is already set
        if (dateInput.value) {
          updateAvailableTimes();
        }
      }
      
      const guideForm = document.getElementById('guideRequestForm');
      if (guideForm) {
        guideForm.addEventListener('submit', function(e) {
          e.preventDefault();
          
          if (!currentGuideGroup) return;
          
          // Validate time is selected
          if (!timeSelect.value || timeSelect.value === '') {
            alert('Please select a preferred time');
            timeSelect.focus();
            return;
          }
          
          const formData = new FormData(guideForm);
          
          // Submit to server via fetch
          fetch('/CeylonGo/public/tourist/tour-guide-submit', {
            method: 'POST',
            body: formData
          })
          .then(response => {
            if (response.redirected) {
              window.location.href = response.url;
            } else {
              return response.text();
            }
          })
          .then(data => {
            if (data) {
              // Handle any returned data
              const customerName = document.getElementById('guideCustomerName').value;
              const location = document.getElementById('guideLocation').value;
              const date = document.getElementById('guideDate').value;
              const time = document.getElementById('guideTime').value;
              
              // Update the guide value and display
              const guideValue = currentGuideGroup.querySelector('.guide-value');
              const yesBtn = currentGuideGroup.querySelector('.guide-yes-btn');
              const noBtn = currentGuideGroup.querySelector('.guide-no-btn');
              const infoDiv = currentGuideGroup.querySelector('.selected-guide-info');
              
              guideValue.value = 'Yes';
              yesBtn.classList.add('active');
              noBtn.classList.remove('active');
              infoDiv.textContent = `✓ Tour guide requested for ${location} on ${date} at ${time}`;
              infoDiv.style.display = 'block';
              
              // Update booking steps
              if (typeof updateBookingSteps === 'function') {
                updateBookingSteps();
              }
              
              closeGuideModal();
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Failed to submit guide request. Please try again.');
          });
        });
      }

      // Close modals when clicking outside
      window.onclick = function(event) {
        const transportModal = document.getElementById('transportModal');
        const guideModal = document.getElementById('guideModal');
        
        if (event.target == transportModal) {
          closeTransportModal();
        }
        if (event.target == guideModal) {
          closeGuideModal();
        }
      }

      // Close modals with Escape key
      document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
          closeTransportModal();
          closeGuideModal();
        }
      });

      // Open transport modal when coming from "Submit Another Request" link
      if (new URLSearchParams(window.location.search).get('open_transport') === '1') {
        openTransportModalStandalone();
      }

      // Custom Places Autocomplete (Vanilla JS)
      const locationInput = document.getElementById('guideLocation');
      const suggestionsContainer = document.getElementById('locationSuggestions');
      let debounceTimer;
      let selectedIndex = -1;
      let suggestions = [];

      locationInput.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(debounceTimer);

        if (query.length < 2) {
          suggestionsContainer.classList.remove('active');
          return;
        }

        // Show loading state
        suggestionsContainer.innerHTML = '<div class="suggestion-loading">Loading...</div>';
        suggestionsContainer.classList.add('active');

        // Use local fallback suggestions (no external API)
        debounceTimer = setTimeout(() => {
          useFallbackSuggestions(query);
        }, 300);
      });

      // Fallback suggestions for common Sri Lankan locations
      const sriLankanCities = [
        'Colombo, Sri Lanka',
        'Kandy, Sri Lanka',
        'Galle, Sri Lanka',
        'Jaffna, Sri Lanka',
        'Negombo, Sri Lanka',
        'Trincomalee, Sri Lanka',
        'Anuradhapura, Sri Lanka',
        'Polonnaruwa, Sri Lanka',
        'Nuwara Eliya, Sri Lanka',
        'Ella, Sri Lanka',
        'Sigiriya, Sri Lanka',
        'Mirissa, Sri Lanka',
        'Bentota, Sri Lanka',
        'Hikkaduwa, Sri Lanka',
        'Arugam Bay, Sri Lanka',
        'Dambulla, Sri Lanka',
        'Matara, Sri Lanka',
        'Batticaloa, Sri Lanka',
        'Ratnapura, Sri Lanka',
        'Badulla, Sri Lanka'
      ];

      function useFallbackSuggestions(query) {
        const matches = sriLankanCities.filter(city => 
          city.toLowerCase().includes(query.toLowerCase())
        ).slice(0, 5);

        if (matches.length > 0) {
          suggestions = matches.map(city => ({ description: city }));
          displaySuggestions(suggestions);
        } else {
          suggestionsContainer.innerHTML = '<div class="suggestion-loading">No matches found</div>';
        }
      }

      function displaySuggestions(predictions) {
        if (predictions.length === 0) {
          suggestionsContainer.classList.remove('active');
          return;
        }

        suggestionsContainer.innerHTML = '';
        predictions.forEach((prediction, index) => {
          const div = document.createElement('div');
          div.className = 'suggestion-item';
          div.textContent = prediction.description;
          div.dataset.index = index;
          
          div.addEventListener('click', function() {
            locationInput.value = prediction.description;
            suggestionsContainer.classList.remove('active');
            selectedIndex = -1;
          });
          
          suggestionsContainer.appendChild(div);
        });
        
        suggestionsContainer.classList.add('active');
      }

      // Keyboard navigation
      locationInput.addEventListener('keydown', function(e) {
        const items = suggestionsContainer.querySelectorAll('.suggestion-item');
        
        if (e.key === 'ArrowDown') {
          e.preventDefault();
          selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
          updateSelection(items);
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          selectedIndex = Math.max(selectedIndex - 1, -1);
          updateSelection(items);
        } else if (e.key === 'Enter' && selectedIndex >= 0) {
          e.preventDefault();
          items[selectedIndex].click();
        } else if (e.key === 'Escape') {
          suggestionsContainer.classList.remove('active');
          selectedIndex = -1;
        }
      });

      function updateSelection(items) {
        items.forEach((item, index) => {
          item.classList.toggle('active', index === selectedIndex);
        });
      }

      // Close suggestions when clicking outside
      document.addEventListener('click', function(e) {
        if (!locationInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
          suggestionsContainer.classList.remove('active');
          selectedIndex = -1;
        }
      });
    });
  </script>

</body>
</html>

