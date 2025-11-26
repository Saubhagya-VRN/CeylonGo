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
        $guide = isset($_POST['guide']) ? htmlspecialchars($_POST['guide']) : '';

        // Validate that we have at least one destination
        if (empty($destinations) || empty($destinations[0])) {
            $error_message = "Please select at least one destination.";
        } else {
            // Save to database
            try {
                require_once '../../config/database.php';
                
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

</head>
<body class="bg-app">
  <!-- Navbar include -->
  <?php include 'header.php'; ?>

  <section class="intro">
    <h1>Plan Your Perfect Trip to Sri Lanka</h1>
    <p>Explore the beauty of Sri Lanka with our customizable tour packages.</p>

    <?php if (!$is_logged_in): ?>
      <a href="/CeylonGo/public/register" class="btn">Get Started</a>
    <?php else: ?>
      <a href="#customize" class="btn">Customize Your Trip</a>
    <?php endif; ?>
  </section>

  <section class="recommended-packages">
    <h2>Recommended Packages</h2>
    <a href="/CeylonGo/public/tourist/recommended-packages" class="btn btn-black">See All Packages</a>
    <div class="packages">
      <div class="package">
        <div class="package-image"></div>
        <h3>Unawatuna Package</h3>
        <p>3 Days, 2 Nights</p>
      </div>
      <div class="package">
        <div class="package-image"></div>
        <h3>Hill Country Package</h3>
        <p>4 Days, 3 Nights</p>
      </div>
      <div class="package">
        <div class="package-image"></div>
        <h3>Sigiriya Package</h3>
        <p>5 Days, 4 Nights</p>
      </div>
    </div>
  </section>

  <!-- ✅ NEW CUSTOMIZE YOUR TRIP SECTION STARTS HERE -->
  <section id="customize" class="customize-trip">
    <h2>Customize Your Trip</h2>

    <?php if ($success_message): ?>
      <div class="alert alert-success"><?= $success_message ?></div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
      <div class="alert alert-error"><?= $error_message ?></div>
    <?php endif; ?>

    <?php if (!$is_logged_in): ?>
      <div class="alert alert-info">
        <strong>👋 Welcome Guest!</strong> You can explore and plan your trip, but you'll need to 
        <a href="../login.php" style="color: #2c5530; font-weight: bold; text-decoration: underline;">login</a> 
        or 
        <a href="../register.php" style="color: #2c5530; font-weight: bold; text-decoration: underline;">register</a> 
        to complete your booking.
      </div>
    <?php endif; ?>

    <form method="POST" action="" id="customizeTripForm">
      <?php if ($is_logged_in): ?>
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
      <?php endif; ?>
      
      <div id="trip-group-container">
        <div class="trip-group" data-index="0">
          <div class="row row-1">
            <div class="box">
              <input type="number" name="people[]" min="1" placeholder="No of People" required>
            </div>
          </div>

          <div class="row row-2 three">
            <div class="box">
              <div class="box-title">Where are You Going?</div>
              <select name="destination[]" required>
                <option value="">Select Destination</option>
                <option value="Kandy">Kandy</option>
                <option value="Colombo">Colombo</option>
                <option value="Galle">Galle</option>
                <option value="Nuwara Eliya">Nuwara Eliya</option>
                <option value="Sigiriya">Sigiriya</option>
                <option value="Unawatuna">Unawatuna</option>
                <option value="Ella">Ella</option>
              </select>
            </div>
            <div class="box">
              <div class="box-title">How Many Days Do You Plan To Stay There?</div>
              <input type="number" name="days[]" min="1" placeholder="Days" required>
            </div>
            <div class="box">
              <div class="inline-control">
                <input type="hidden" name="hotel[]" class="hotel-value" value="">
                <a href="choose_hotel.php" class="btn-black choose-hotel-btn" style="text-decoration: none; display: inline-block; padding: 10px 15px; font-size: 14px;">Choose Hotel</a>
                <span class="selected-hotel"></span>
              </div>
            </div>
          </div>

          <div class="row row-3">
            <div class="box">
              <div class="inline-control">
                <div class="box-title" style="margin:0;">Do You Want Transport?</div>
                <div class="btn-group">
                  <input type="hidden" name="transport[]" class="transport-value" value="No">
                  <a href="transport_providers.php" class="btn-black transport-yes-btn" style="text-decoration: none;">Yes</a>
                  <button type="button" class="btn-white transport-no-btn active">No</button>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <div class="row row-4 actions-right">
        <button type="button" id="addMore" class="btn-white">Add More +</button>
      </div>

      <div class="row row-5">
        <div class="box">
          <div class="inline-control">
            <div class="box-title" style="margin:0;">Add a Tour Guide?</div>
            <div class="btn-group">
              <input type="hidden" name="guide" id="guideChoice" value="No">
              <a href="tour_guide_request.php" class="btn-white guide-yes-btn" style="text-decoration: none; border-radius: 20px;">Yes</a>
              <button type="button" class="btn-black guide-no-btn active" style="border-radius: 4px;">No</button>
            </div>
          </div>
        </div>
      </div>

      <div class="center">
        <?php if ($is_logged_in): ?>
          <button type="submit" class="btn-black finish">Finish</button>
        <?php else: ?>
          <a href="../login.php" class="btn-black finish" style="text-decoration: none; display: inline-block;">Finish</a>
          <p style="margin-top: 10px; color: #5a6b5a; font-size: 14px;">
            <a href="../login.php" style="color: #2c5530; font-weight: bold;">Login</a> to complete your booking
          </p>
        <?php endif; ?>
      </div>
    </form>

    <script>
      (function(){
        var groupIndex = 1;
        
        // Save form data to localStorage
        function saveFormData() {
          var formData = {
            people: [],
            destinations: [],
            days: [],
            hotels: [],
            transports: [],
            guide: document.getElementById('guideChoice').value
          };
          
          var groups = document.querySelectorAll('.trip-group');
          groups.forEach(function(group) {
            formData.people.push(group.querySelector('input[name="people[]"]').value);
            formData.destinations.push(group.querySelector('select[name="destination[]"]').value);
            formData.days.push(group.querySelector('input[name="days[]"]').value);
            formData.hotels.push(group.querySelector('.hotel-value').value);
            formData.transports.push(group.querySelector('.transport-value').value);
          });
          
          localStorage.setItem('tripFormData', JSON.stringify(formData));
        }
        
        // Load form data from localStorage
        function loadFormData() {
          var savedData = localStorage.getItem('tripFormData');
          if (savedData) {
            var formData = JSON.parse(savedData);
            
            // Restore guide choice
            if (formData.guide) {
              document.getElementById('guideChoice').value = formData.guide;
              if (formData.guide === 'Yes') {
                document.querySelector('.guide-yes-btn').classList.add('active');
                document.querySelector('.guide-no-btn').classList.remove('active');
              }
            }
            
            // Restore each trip group
            var groups = document.querySelectorAll('.trip-group');
            for (var i = 0; i < formData.destinations.length; i++) {
              if (i > 0 && i >= groups.length) {
                // Add more groups if needed
                document.getElementById('addMore').click();
                groups = document.querySelectorAll('.trip-group');
              }
              
              var group = groups[i];
              if (group) {
                group.querySelector('input[name="people[]"]').value = formData.people[i] || '';
                group.querySelector('select[name="destination[]"]').value = formData.destinations[i] || '';
                group.querySelector('input[name="days[]"]').value = formData.days[i] || '';
                group.querySelector('.hotel-value').value = formData.hotels[i] || '';
                group.querySelector('.transport-value').value = formData.transports[i] || '';
                
                // Update display
                if (formData.hotels[i]) {
                  group.querySelector('.selected-hotel').textContent = 'Selected: ' + formData.hotels[i];
                }
                if (formData.transports[i]) {
                  group.querySelector('.selected-transport').textContent = formData.transports[i];
                }
              }
            }
          }
        }
        
        // Load saved data on page load
        loadFormData();
        
        // Add More button functionality
        var addBtn = document.getElementById('addMore');
        if (addBtn) {
          addBtn.addEventListener('click', function(){
            var container = document.getElementById('trip-group-container');
            var groups = container.getElementsByClassName('trip-group');
            if (!groups.length) return;
            
            var clone = groups[0].cloneNode(true);
            clone.setAttribute('data-index', groupIndex++);

            // Reset inputs in clone
            var inputs = clone.querySelectorAll('input[type=number], input[type=text], input[type=hidden], select');
            inputs.forEach(function(el){
              if (el.tagName.toLowerCase() === 'select') { 
                el.selectedIndex = 0; 
              } else if (!el.classList.contains('hotel-value') && !el.classList.contains('transport-value')) {
                el.value = ''; 
              } else {
                el.value = el.classList.contains('transport-value') ? 'No' : '';
              }
            });
            
            // Reset display spans
            clone.querySelector('.selected-hotel').textContent = '';
            clone.querySelector('.selected-transport').textContent = '';

            // Add remove button for this clone
            var removeWrap = document.createElement('div');
            removeWrap.className = 'actions-right';
            removeWrap.style.marginTop = '10px';
            var removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn-danger';
            removeBtn.textContent = 'Remove';
            removeBtn.addEventListener('click', function(){
              var tg = this.closest('.trip-group');
              if (tg) tg.remove();
            });
            removeWrap.appendChild(removeBtn);
            clone.appendChild(removeWrap);

            container.appendChild(clone);
            
            // Attach event listeners to new group
            attachGroupEventListeners(clone);
          });
        }
        
        // Function to attach event listeners to a group
        function attachGroupEventListeners(group) {
          // Hotel selection button
          var hotelBtn = group.querySelector('.choose-hotel-btn');
          if (hotelBtn) {
            hotelBtn.addEventListener('click', function() {
              saveFormData();
              window.location.href = 'choose_hotel.php';
            });
          }
          
          // Transport Yes button
          var transportYesBtn = group.querySelector('.transport-yes-btn');
          if (transportYesBtn) {
            transportYesBtn.addEventListener('click', function() {
              saveFormData();
              window.location.href = 'transport_providers.php';
            });
          }
          
          // Transport No button
          var transportNoBtn = group.querySelector('.transport-no-btn');
          if (transportNoBtn) {
            transportNoBtn.addEventListener('click', function() {
              group.querySelector('.transport-value').value = 'No';
              transportNoBtn.classList.add('active');
              group.querySelector('.transport-yes-btn').classList.remove('active');
            });
          }
        }
        
        // Attach event listeners to initial group(s)
        document.querySelectorAll('.trip-group').forEach(function(group) {
          attachGroupEventListeners(group);
        });
        
        // Guide Yes button
        var guideYesBtn = document.querySelector('.guide-yes-btn');
        if (guideYesBtn) {
          guideYesBtn.addEventListener('click', function() {
            document.getElementById('guideChoice').value = 'Yes';
            guideYesBtn.classList.add('active');
            document.querySelector('.guide-no-btn').classList.remove('active');
          });
        }
        
        // Guide No button
        var guideNoBtn = document.querySelector('.guide-no-btn');
        if (guideNoBtn) {
          guideNoBtn.addEventListener('click', function() {
            document.getElementById('guideChoice').value = 'No';
            guideNoBtn.classList.add('active');
            document.querySelector('.guide-yes-btn').classList.remove('active');
          });
        }
        
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
              window.location.href = '../login.php';
            }
          });
        }
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

</body>
</html>
