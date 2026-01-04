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
        <a href="../login" style="color: #2c5530; font-weight: bold; text-decoration: underline;">login</a> 
        or 
        <a href="../register" style="color: #2c5530; font-weight: bold; text-decoration: underline;">register</a> 
        to complete your booking.
      </div>
    <?php endif; ?>

    <form method="POST" action="" id="customizeTripForm">
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

            <div class="row row-2 three">
              <div class="box">
                <div class="box-title">Where are You Going?</div>
                <div class="input-with-icon autocomplete-wrapper">
                  <svg class="search-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                  </svg>
                  <input 
                    type="text" 
                    name="destination[]" 
                    class="destination-input"
                    placeholder="Type a place..."
                    required
                    autocomplete="off"
                  >
                  <ul class="autocomplete-list">
                    <li data-value="Kandy">Kandy</li>
                    <li data-value="Colombo">Colombo</li>
                    <li data-value="Galle">Galle</li>
                    <li data-value="Nuwara Eliya">Nuwara Eliya</li>
                    <li data-value="Sigiriya">Sigiriya</li>
                    <li data-value="Unawatuna">Unawatuna</li>
                    <li data-value="Ella">Ella</li>
                  </ul>
                </div>
              </div>
              <div class="box">
                <div class="box-title">How Many Days Do You Plan To Stay?</div>
                <div class="input-with-icon">
                  <span class="icon"></span>
                  <input type="number" name="days[]" min="1" max="10" placeholder="Days" required oninput="if(this.value > 10) this.value = 10; if(this.value < 1) this.value = 1;">
                  <span class="nights-text">Nights</span>
                </div>
              </div>
              <div class="box box-hotel-btn">
                <div style="display: flex; align-items: center; justify-content: center; gap: 12px;">
                  <input type="hidden" name="hotel[]" class="hotel-value" value="">
                  <a href="/CeylonGo/public/tourist/choose-hotel" class="btn-black choose-hotel-btn" style="text-decoration: none; display: inline-flex;">
                    <span class="btn-icon"></span>
                    <span>Choose Hotel</span>
                  </a>
                </div>
              </div>
            </div>

            <div class="row row-3">
              <div class="box">
                <div class="inline-control">
                  <div class="box-title">Do You Want Transport?</div>
                  <div class="btn-group">
                    <input type="hidden" name="transport[]" class="transport-value" value="No">
                    <a href="/CeylonGo/public/tourist/transport-providers" class="btn-white transport-yes-btn" style="text-decoration: none;">
                      <span class="btn-icon">✓</span>
                      <span>Yes</span>
                    </a>
                    <button type="button" class="btn-black transport-no-btn active">
                      <span>No</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row row-5">
          <div class="box">
            <div class="inline-control">
              <div class="box-title">Add a Tour Guide?</div>
              <div class="btn-group">
                <input type="hidden" name="guide" id="guideChoice" value="No">
                <a href="/CeylonGo/public/tourist/tour-guide-request" class="btn-white guide-yes-btn" style="text-decoration: none;">
                  <span class="btn-icon">✓</span>
                  <span>Yes</span>
                </a>
                <button type="button" class="btn-black guide-no-btn active">
                  <span>No</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="row row-4 actions-right">
          <button type="button" id="addMore" class="btn-white">
            <span class="btn-icon">+</span>
            <span>Add More +</span>
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

    <script>
    // Autocomplete functionality
    function initializeAutocomplete(wrapper) {
      var input = wrapper.querySelector('.destination-input');
      var list = wrapper.querySelector('.autocomplete-list');
      var items = list.querySelectorAll('li');

      if (!input || !list) return;

      // Show suggestions on focus
      input.addEventListener('focus', function() {
        filterSuggestions();
      });

      // Filter suggestions on input
      input.addEventListener('input', function() {
        filterSuggestions();
      });

      function filterSuggestions() {
        var searchValue = input.value.toLowerCase();
        var visibleItems = 0;

        items.forEach(function(item) {
          var text = item.textContent.toLowerCase();
          var matches = !searchValue || text.includes(searchValue);
          
          if (matches) {
            item.style.display = 'block';
            visibleItems++;
          } else {
            item.style.display = 'none';
          }
          item.classList.remove('active');
        });

        list.classList.toggle('show', visibleItems > 0);
      }

      // Handle item selection
      items.forEach(function(item) {
        item.addEventListener('click', function(e) {
          e.preventDefault();
          input.value = this.getAttribute('data-value');
          list.classList.remove('show');
          input.dispatchEvent(new Event('change'));
        });

        item.addEventListener('mouseover', function() {
          items.forEach(function(i) { i.classList.remove('active'); });
          this.classList.add('active');
        });
      });

      // Close list when clicking outside
      document.addEventListener('click', function(e) {
        if (!wrapper.contains(e.target)) {
          list.classList.remove('show');
        }
      });
    }

    // Initialize autocomplete for first group
    var firstWrapper = document.querySelector('.autocomplete-wrapper');
    if (firstWrapper) {
      initializeAutocomplete(firstWrapper);
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

          // Remove any existing remove button from clone
          var existingRemove = clone.querySelector('.actions-right');
          if (existingRemove) {
            existingRemove.remove();
          }

           // Reset inputs in clone
           var inputs = clone.querySelectorAll('input[type=number], input[type=text], input[type=hidden], select');
           inputs.forEach(function(el){
             if (el.tagName.toLowerCase() === 'select') { 
               el.selectedIndex = 0; 
             } else if (el.closest('.number-control')) {
               // Reset number control to 1
               el.value = '1';
             } else if (!el.classList.contains('hotel-value') && !el.classList.contains('transport-value')) {
               el.value = ''; 
             } else {
               el.value = el.classList.contains('transport-value') ? 'No' : '';
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
            saveFormData();
          });
          removeWrap.appendChild(removeBtn);
          clone.appendChild(removeWrap);

          // Append clone to container
          container.appendChild(clone);
          
          // Attach event listeners to new group
          attachGroupEventListeners(clone);
          
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
          
          localStorage.setItem('tripFormData', JSON.stringify(formData));
        }
        
        // Load form data from localStorage
        function loadFormData() {
          var savedData = localStorage.getItem('tripFormData');
          if (savedData) {
            try {
              var formData = JSON.parse(savedData);
              
              // Restore guide choice
              var guideChoice = document.getElementById('guideChoice');
              if (guideChoice && formData.guide) {
                guideChoice.value = formData.guide;
                if (formData.guide === 'Yes') {
                  var guideYesBtn = document.querySelector('.guide-yes-btn');
                  var guideNoBtn = document.querySelector('.guide-no-btn');
                  if (guideYesBtn) guideYesBtn.classList.add('active');
                  if (guideNoBtn) guideNoBtn.classList.remove('active');
                }
              }
              
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
                  
                  if (peopleInput) peopleInput.value = formData.people[i] || '';
                  if (destinationInput) destinationInput.value = formData.destinations[i] || '';
                  if (daysInput) daysInput.value = formData.days[i] || '';
                  if (hotelValue) hotelValue.value = formData.hotels[i] || '';
                  if (transportValue) transportValue.value = formData.transports[i] || '';
                  
                  // Update display
                  if (formData.hotels[i]) {
                    var selectedHotel = group.querySelector('.selected-hotel');
                    if (selectedHotel) selectedHotel.textContent = 'Selected: ' + formData.hotels[i];
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
          
          // Transport Yes button
          var transportYesBtn = group.querySelector('.transport-yes-btn');
          if (transportYesBtn) {
            transportYesBtn.addEventListener('click', function() {
              saveFormData();
              window.location.href = '/CeylonGo/public/tourist/transport-providers';
            });
          }
          
          // Transport No button
          var transportNoBtn = group.querySelector('.transport-no-btn');
          if (transportNoBtn) {
            transportNoBtn.addEventListener('click', function() {
              var transportValue = group.querySelector('.transport-value');
              if (transportValue) transportValue.value = 'No';
              transportNoBtn.classList.add('active');
              var transportYesBtn = group.querySelector('.transport-yes-btn');
              if (transportYesBtn) transportYesBtn.classList.remove('active');
              saveFormData();
            });
          }
        }
        
        // Wait for DOM to be ready
        function init() {
          // Load saved data on page load
          loadFormData();
          
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
          
          // Guide Yes button
          var guideYesBtn = document.querySelector('.guide-yes-btn');
          if (guideYesBtn) {
            guideYesBtn.addEventListener('click', function() {
              var guideChoice = document.getElementById('guideChoice');
              if (guideChoice) guideChoice.value = 'Yes';
              guideYesBtn.classList.add('active');
              var guideNoBtn = document.querySelector('.guide-no-btn');
              if (guideNoBtn) guideNoBtn.classList.remove('active');
              saveFormData();
            });
          }
          
          // Guide No button
          var guideNoBtn = document.querySelector('.guide-no-btn');
          if (guideNoBtn) {
            guideNoBtn.addEventListener('click', function() {
              var guideChoice = document.getElementById('guideChoice');
              if (guideChoice) guideChoice.value = 'No';
              guideNoBtn.classList.add('active');
              var guideYesBtn = document.querySelector('.guide-yes-btn');
              if (guideYesBtn) guideYesBtn.classList.remove('active');
              saveFormData();
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
        }
        
        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', init);
        } else {
          init();
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
