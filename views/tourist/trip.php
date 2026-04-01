<?php
$user_name = $user_name ?? '';
$tourist_data = $tourist_data ?? null;
$places_autocomplete_url = $places_autocomplete_url ?? '/CeylonGo/public/api/places-autocomplete';
$user_email = ($tourist_data['email'] ?? $_SESSION['user_email'] ?? '');
$avatar_initial = $user_name ? strtoupper(substr(trim($user_name), 0, 1)) : 'T';
$steps = [
    'Travel Group',
    'Destination & Dates',
    'Accommodation',
    'Transport & Flights',
    'Budget & Add-ons',
    'Package Type',
    'Contact Details',
    'Review & Submit'
];
$districts = [
    'ampara' => 'Ampara', 'anuradhapura' => 'Anuradhapura', 'badulla' => 'Badulla', 'batticaloa' => 'Batticaloa',
    'colombo' => 'Colombo', 'galle' => 'Galle', 'gampaha' => 'Gampaha', 'hambantota' => 'Hambantota',
    'jaffna' => 'Jaffna', 'kalutara' => 'Kalutara', 'kandy' => 'Kandy', 'kegalle' => 'Kegalle',
    'kilinochchi' => 'Kilinochchi', 'kurunegala' => 'Kurunegala', 'mannar' => 'Mannar', 'matale' => 'Matale',
    'matara' => 'Matara', 'monaragala' => 'Monaragala', 'mullaitivu' => 'Mullaitivu', 'nuwara-eliya' => 'Nuwara Eliya',
    'polonnaruwa' => 'Polonnaruwa', 'puttalam' => 'Puttalam', 'ratnapura' => 'Ratnapura',
    'trincomalee' => 'Trincomalee', 'vavuniya' => 'Vavuniya'
];
// Main cities for destination autocomplete (display name => district key for form/API)
$main_cities = [
    'Colombo' => 'colombo', 'Kandy' => 'kandy', 'Galle' => 'galle', 'Negombo' => 'gampaha', 'Jaffna' => 'jaffna',
    'Trincomalee' => 'trincomalee', 'Batticaloa' => 'batticaloa', 'Matara' => 'matara', 'Anuradhapura' => 'anuradhapura',
    'Nuwara Eliya' => 'nuwara-eliya', 'Bentota' => 'kalutara', 'Ella' => 'badulla', 'Sigiriya' => 'matale',
    'Dambulla' => 'matale', 'Hikkaduwa' => 'galle', 'Mirissa' => 'matara', 'Arugam Bay' => 'ampara',
    'Unawatuna' => 'galle', 'Ratnapura' => 'ratnapura', 'Badulla' => 'badulla', 'Kurunegala' => 'kurunegala',
    'Gampaha' => 'gampaha', 'Kalutara' => 'kalutara', 'Hambantota' => 'hambantota', 'Polonnaruwa' => 'polonnaruwa',
    'Ampara' => 'ampara', 'Matale' => 'matale', 'Kegalle' => 'kegalle', 'Puttalam' => 'puttalam',
    'Vavuniya' => 'vavuniya', 'Mannar' => 'mannar', 'Kilinochchi' => 'kilinochchi', 'Mullaitivu' => 'mullaitivu',
    'Monaragala' => 'monaragala', 'Mount Lavinia' => 'colombo', 'Galle Fort' => 'galle', 'Yala' => 'hambantota'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customise Your Trip - Ceylon Go</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/common.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/trip_layout.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/sidebar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/trip.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/accommodation_content.css">
</head>
<body class="trip-page-body">

  <header class="trip-navbar">
    <div class="branding">
      <button class="hamburger-btn" id="tripHamburgerBtn" aria-label="Toggle menu"><span></span><span></span><span></span></button>
      <a href="/CeylonGo/public/tourist/dashboard" class="trip-branding-link">
        <img src="/CeylonGo/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
        <span class="logo-text">Ceylon Go</span>
      </a>
    </div>
    <nav class="nav-links">
      <a href="/CeylonGo/public/tourist/dashboard">Dashboard</a>
      <a href="/CeylonGo/public/tourist/recommended-packages">Packages</a>
      <a href="/CeylonGo/public/tourist/customize-trip">Customise Trip</a>
      <a href="/CeylonGo/public/tourist/profile">Profile</a>
      <a href="/CeylonGo/public/logout">Logout</a>
    </nav>
  </header>

  <div class="sidebar-overlay trip-overlay" id="tripSidebarOverlay"></div>

  <div class="trip-page-wrapper">
    <aside class="trip-sidebar" id="tripSidebar">
      <div class="trip-sidebar-nav">
        <ul>
          <li><a href="/CeylonGo/public/tourist/dashboard"><i class="fa-solid fa-table-columns"></i> <span class="sidebar-link-text">Dashboard <span class="sidebar-sub">Overview & Stats</span></span></a></li>
          <li class="active"><a href="/CeylonGo/public/tourist/customize-trip"><i class="fa-solid fa-wand-magic-sparkles"></i> <span class="sidebar-link-text">Customise Your Trip <span class="sidebar-sub">Plan Custom Trips</span></span></a></li>
          <li><a href="/CeylonGo/public/tourist/transport-report"><i class="fa-solid fa-car-side"></i> <span class="sidebar-link-text">Transport Requests <span class="sidebar-sub">Request Transport</span></span></a></li>
          <li><a href="#"><i class="fa-regular fa-message"></i> <span class="sidebar-link-text">Queries <span class="sidebar-sub">Active Requests</span></span></a></li>
          <li><a href="/CeylonGo/public/tourist/my-bookings"><i class="fa-regular fa-calendar-check"></i> <span class="sidebar-link-text">Bookings <span class="sidebar-sub">Manage Reservations</span></span></a></li>
          <li><a href="/CeylonGo/public/tourist/payment"><i class="fa-solid fa-credit-card"></i> <span class="sidebar-link-text">Payments <span class="sidebar-sub">Invoices & Wallet</span></span></a></li>
          <li><a href="#"><i class="fa-regular fa-heart"></i> <span class="sidebar-link-text">Wishlist <span class="sidebar-sub">Saved Destinations</span></span></a></li>
          <li><a href="/CeylonGo/public/tourist/profile"><i class="fa-regular fa-user"></i> <span class="sidebar-link-text">Profile <span class="sidebar-sub">Account Settings</span></span></a></li>
        </ul>
      </div>
      <div class="trip-sidebar-footer">
        <div class="trip-sidebar-user">
          <div class="trip-sidebar-user-avatar"><?php echo htmlspecialchars($avatar_initial); ?></div>
          <div class="trip-sidebar-user-info">
            <div class="trip-sidebar-user-name"><?php echo htmlspecialchars($user_name ?: 'Tourist'); ?></div>
            <div class="trip-sidebar-user-email"><?php echo htmlspecialchars($user_email ? substr($user_email, 0, 20) . (strlen($user_email) > 20 ? '...' : '') : ''); ?></div>
          </div>
        </div>
        <a href="/CeylonGo/public/logout" class="trip-sidebar-signout"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</a>
      </div>
    </aside>

    <main class="trip-main-content">
      <div class="trip-breadcrumbs">
        <a href="/CeylonGo/public/tourist/dashboard"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span>&gt;</span>
        <span>customise-your-trip</span>
      </div>

      <div class="trip-header-row">
        <div class="trip-stepper-prev">
          <button type="button" class="btn-prev" disabled><i class="fa-solid fa-chevron-left"></i> Previous</button>
        </div>
        <h1 class="trip-page-title trip-title-centered">
          <i class="fa-solid fa-wand-magic-sparkles"></i> Customise Your Dream Trip
        </h1>
        <div class="trip-stepper-next">
          <button type="button" class="btn-next">Next <i class="fa-solid fa-chevron-right"></i></button>
        </div>
      </div>

      <div class="trip-stepper-steps-wrap">
        <div class="trip-stepper-steps" id="tripStepperSteps">
          <?php foreach ($steps as $i => $label): ?>
            <div class="trip-step" data-step="<?php echo $i; ?>">
              <?php if ($i > 0): ?><div class="trip-step-line"></div><?php endif; ?>
              <span class="trip-step-label"><?php echo htmlspecialchars($label); ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="trip-step-panel active" data-step="1">
      <div class="trip-step-card trip-step-card--travel-group">
        <div class="step-icon step-icon--travel-group"><i class="fa-solid fa-people-group"></i></div>
        <h2 class="step-heading">Who's traveling with you?</h2>
        <p class="step-subheading">Tell us about your travel companions</p>
        <div class="trip-travel-group-form">
          <div class="trip-counters-row">
            <div class="trip-counter-group">
              <label class="trip-counter-label"><i class="trip-counter-icon fa-solid fa-person"></i> Adults (12+ years)</label>
              <div class="trip-counter">
                <button type="button" class="trip-counter-btn" data-target="adults" data-dir="-1" aria-label="Decrease">−</button>
                <input type="number" id="adults" name="adults" value="1" min="1" max="50" readonly class="trip-counter-value">
                <button type="button" class="trip-counter-btn" data-target="adults" data-dir="1" aria-label="Increase">+</button>
              </div>
            </div>
            <div class="trip-counter-group">
              <label class="trip-counter-label"><i class="trip-counter-icon fa-solid fa-child"></i> Children (2–11 years)</label>
              <div class="trip-counter">
                <button type="button" class="trip-counter-btn" data-target="children" data-dir="-1">−</button>
                <input type="number" id="children" name="children" value="0" min="0" max="50" readonly class="trip-counter-value">
                <button type="button" class="trip-counter-btn" data-target="children" data-dir="1">+</button>
              </div>
            </div>
            <div class="trip-counter-group">
              <label class="trip-counter-label"><i class="trip-counter-icon fa-solid fa-baby"></i> Infants (0–2 years)</label>
              <div class="trip-counter">
                <button type="button" class="trip-counter-btn" data-target="infants" data-dir="-1">−</button>
                <input type="number" id="infants" name="infants" value="0" min="0" max="50" readonly class="trip-counter-value">
                <button type="button" class="trip-counter-btn" data-target="infants" data-dir="1">+</button>
              </div>
            </div>
          </div>
          <div class="trip-type-section">
            <h3 class="trip-type-heading">Type of Trip</h3>
            <div class="trip-type-options trip-type-cards">
              <button type="button" class="trip-type-card" data-type="couple">
                <i class="trip-type-card-icon fa-regular fa-heart"></i>
                <span class="trip-type-card-label">Couple</span>
              </button>
              <button type="button" class="trip-type-card" data-type="family">
                <i class="trip-type-card-icon fa-regular fa-heart"></i>
                <span class="trip-type-card-label">Family</span>
              </button>
              <button type="button" class="trip-type-card" data-type="friends">
                <i class="trip-type-card-icon fa-regular fa-heart"></i>
                <span class="trip-type-card-label">Friends</span>
              </button>
              <button type="button" class="trip-type-card" data-type="solo">
                <i class="trip-type-card-icon fa-regular fa-heart"></i>
                <span class="trip-type-card-label">Solo</span>
              </button>
            </div>
            <input type="hidden" name="trip_type" id="trip_type" value="">
            <p class="trip-date-error trip-type-error" id="tripTypeError" role="alert" aria-live="polite"></p>
          </div>
        </div>
      </div>
      </div>

      <div class="trip-step-panel" data-step="2">
      <div class="trip-step-card trip-step-card--dest-dates trip-wireframe-card">
        <div class="step-icon step-icon--dest-dates"><i class="fa-solid fa-location-dot"></i></div>
        <h2 class="trip-section-heading">Select Destination</h2>

        <form method="POST" action="#" id="trip-step-form">
          <div class="form-row trip-dates-row dest-dates-row trip-dest-row-single">
            <div class="form-group trip-date-group trip-dest-group">
              <label for="dest_primary">Destination</label>
              <div class="input-with-icon input-with-icon--dest trip-dest-input-wrap">
                <i class="fa-solid fa-location-dot input-icon input-icon--left"></i>
                <input type="text" id="dest_primary" name="destinations[0][name]" value="" placeholder="Type or select a city" autocomplete="off" aria-autocomplete="list" aria-controls="dest_suggestions" aria-expanded="false">
                <input type="hidden" id="dest_district_hidden" name="destinations[0][district]" value="">
                <i class="fa-solid fa-chevron-down input-icon input-icon--right"></i>
                <div class="trip-dest-suggestions" id="dest_suggestions" role="listbox" aria-hidden="true"></div>
              </div>
            </div>
          </div>

          <div class="form-row trip-dates-row dest-dates-row">
            <div class="form-group trip-date-group">
              <label for="start_date">Start Date</label>
              <div class="input-with-icon input-with-icon--date">
                <i class="fa-regular fa-calendar input-icon input-icon--left"></i>
                <input type="date" id="start_date" name="start_date" value="2026-02-04" required aria-describedby="tripDateError">
              </div>
            </div>
            <div class="form-group trip-date-group">
              <label for="end_date">End Date</label>
              <div class="input-with-icon input-with-icon--date">
                <i class="fa-regular fa-calendar input-icon input-icon--left"></i>
                <input type="date" id="end_date" name="end_date" value="2026-02-09" required aria-describedby="tripDateError">
              </div>
            </div>
          </div>
          <p class="trip-date-error" id="tripDateError" role="alert" aria-live="polite"></p>

          <div class="trip-duration-banner" id="tripDuration">5 Nights</div>

          <div class="trip-stops-section">
            <h3 class="trip-stops-heading" id="tripStopsHeading">Stops in this area</h3>
            <p class="trip-stops-desc">You can add many stops and request transport &amp; a tour guide if needed.</p>
            <div id="tripStopsList" class="trip-stops-list"></div>
            <button type="button" class="btn-add-more-stops" id="btnAddMoreStops"><i class="fa-solid fa-plus"></i> Add More Stops</button>
          </div>

          <input type="hidden" name="destinations[0][days]" value="1">
        </form>
      </div>
      </div>

      <div class="trip-step-panel" data-step="3">
        <div class="trip-step-card trip-step-card--accommodation">
          <?php include __DIR__ . '/_accommodation_content.php'; ?>
        </div>
      </div>
    </main>
  </div>

  <?php include __DIR__ . '/transport_request_modal.php'; ?>
  <?php include __DIR__ . '/tour_guide_request_modal.php'; ?>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
    var hamburger = document.getElementById('tripHamburgerBtn');
    var sidebar = document.getElementById('tripSidebar');
    var overlay = document.getElementById('tripSidebarOverlay');
    function toggleSidebar() {
      if (hamburger) hamburger.classList.toggle('active');
      if (sidebar) sidebar.classList.toggle('active');
      if (overlay) overlay.classList.toggle('active');
      document.body.style.overflow = sidebar && sidebar.classList.contains('active') ? 'hidden' : '';
    }
    function closeSidebar() {
      if (hamburger) hamburger.classList.remove('active');
      if (sidebar) sidebar.classList.remove('active');
      if (overlay) overlay.classList.remove('active');
      document.body.style.overflow = '';
    }
    if (hamburger) hamburger.addEventListener('click', toggleSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);
    var sidebarLinks = document.querySelectorAll('#tripSidebar ul li a');
    sidebarLinks.forEach(function (link) {
      link.addEventListener('click', function () {
        if (window.innerWidth <= 768) closeSidebar();
      });
    });
    window.addEventListener('resize', function () {
      if (window.innerWidth > 768) closeSidebar();
    });

    var currentStep = 1, totalSteps = 8;
    var panels = document.querySelectorAll('.trip-step-panel');
    var stepLabels = document.querySelectorAll('#tripStepperSteps .trip-step');
    var btnPrev = document.querySelector('.btn-prev');
    var btnNext = document.querySelector('.btn-next');
    function showStep(step) {
      currentStep = step;
      panels.forEach(function (p) { p.classList.toggle('active', parseInt(p.getAttribute('data-step'), 10) === step); });
      stepLabels.forEach(function (el, i) {
        var label = el.querySelector('.trip-step-label');
        if (label) label.classList.toggle('active', i === step - 1);
        var line = el.querySelector('.trip-step-line');
        if (line) line.classList.toggle('active', i === step - 1);
      });
      if (btnPrev) btnPrev.disabled = step <= 1;
      if (step === 3) filterAccommodationByDestination();
    }
    var districtToHotelLocation = { 'colombo': 'colombo', 'kandy': 'kandy', 'galle': 'galle', 'nuwara-eliya': 'nuwara' };
    function filterAccommodationByDestination() {
      var destDistrict = (document.getElementById('dest_district_hidden') || {}).value || '';
      var destPrimary = (document.getElementById('dest_primary') || {}).value || '';
      var grid = document.getElementById('tripAccommodationHotelsGrid');
      var locFilter = document.getElementById('tripAccommodationLocationFilter');
      if (!grid) return;
      var targetLocation = districtToHotelLocation[destDistrict] || destDistrict;
      if (destDistrict && targetLocation) {
        if (locFilter) locFilter.value = targetLocation;
        grid.querySelectorAll('.hotel-card').forEach(function (card) {
          card.style.display = (card.dataset.location || '').indexOf(targetLocation) !== -1 ? '' : 'none';
        });
      } else {
        if (locFilter) locFilter.value = '';
        grid.querySelectorAll('.hotel-card').forEach(function (card) { card.style.display = ''; });
      }
      var searchInput = document.getElementById('tripAccommodationSearchInput');
      if (searchInput) searchInput.value = '';
    }
    function validateStep2() {
      var destPrimary = (document.getElementById('dest_primary') || {}).value || '';
      var destDistrict = (document.getElementById('dest_district_hidden') || {}).value || '';
      var tripDateErrorEl = document.getElementById('tripDateError');
      if (!destPrimary.trim() && !destDistrict.trim()) {
        if (tripDateErrorEl) {
          tripDateErrorEl.textContent = 'Please select a destination before continuing.';
          tripDateErrorEl.classList.add('trip-date-error--visible');
        }
        return false;
      }
      if (tripDateErrorEl) {
        tripDateErrorEl.textContent = '';
        tripDateErrorEl.classList.remove('trip-date-error--visible');
      }
      return true;
    }
    function validateStep1() {
      var tripTypeEl = document.getElementById('trip_type');
      var tripTypeErrorEl = document.getElementById('tripTypeError');
      if (!tripTypeEl || !tripTypeEl.value || !tripTypeEl.value.trim()) {
        if (tripTypeErrorEl) {
          tripTypeErrorEl.textContent = 'Please select a type of trip (Couple, Family, Friends, or Solo) before continuing.';
          tripTypeErrorEl.classList.add('trip-date-error--visible');
        }
        return false;
      }
      var type = tripTypeEl.value.trim().toLowerCase();
      if (type === 'family' || type === 'friends') {
        var adults = parseInt(document.getElementById('adults').value, 10) || 0;
        var children = parseInt(document.getElementById('children').value, 10) || 0;
        var infants = parseInt(document.getElementById('infants').value, 10) || 0;
        var total = adults + children + infants;
        if (total < 2) {
          if (tripTypeErrorEl) {
            tripTypeErrorEl.textContent = 'Family and Friends trips require at least 2 travelers. Please add more adults, children, or infants.';
            tripTypeErrorEl.classList.add('trip-date-error--visible');
          }
          return false;
        }
      }
      if (tripTypeErrorEl) {
        tripTypeErrorEl.textContent = '';
        tripTypeErrorEl.classList.remove('trip-date-error--visible');
      }
      return true;
    }
    if (btnNext) btnNext.addEventListener('click', function () {
      if (currentStep === 1 && !validateStep1()) return;
      if (currentStep === 2 && !validateStep2()) return;
      if (currentStep < totalSteps) showStep(currentStep + 1);
    });
    if (btnPrev) btnPrev.addEventListener('click', function () { if (currentStep > 1) showStep(currentStep - 1); });
    showStep(1);

    document.querySelectorAll('.trip-counter-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var targetId = this.getAttribute('data-target');
        var dir = parseInt(this.getAttribute('data-dir'), 10);
        var input = document.getElementById(targetId);
        if (!input) return;
        var min = parseInt(input.getAttribute('min'), 10), max = parseInt(input.getAttribute('max'), 10);
        var val = parseInt(input.value, 10) + dir;
        input.value = Math.max(min, Math.min(max, val));
      });
    });

    var adultsInput = document.getElementById('adults');
    var childrenInput = document.getElementById('children');
    var infantsInput = document.getElementById('infants');
    var tripUserName = <?php echo json_encode($user_name); ?>;
    var tripUserContact = <?php echo json_encode($tourist_data['contact_number'] ?? ''); ?>;
    function applyAdultLimitForTripType(type) {
      if (!adultsInput) return;
      if (type === 'solo') {
        adultsInput.setAttribute('max', '1'); adultsInput.value = '1';
        if (childrenInput) { childrenInput.setAttribute('max', '0'); childrenInput.value = '0'; }
        if (infantsInput) { infantsInput.setAttribute('max', '0'); infantsInput.value = '0'; }
      } else if (type === 'couple') {
        adultsInput.setAttribute('max', '2');
        adultsInput.value = '2';
        if (childrenInput) { childrenInput.setAttribute('max', '0'); childrenInput.value = '0'; }
        if (infantsInput) { infantsInput.setAttribute('max', '0'); infantsInput.value = '0'; }
      } else {
        adultsInput.setAttribute('max', '50');
        if (childrenInput) childrenInput.setAttribute('max', '50');
        if (infantsInput) infantsInput.setAttribute('max', '50');
      }
    }
    document.querySelectorAll('.trip-type-card').forEach(function (btn) {
      btn.addEventListener('click', function () {
        document.querySelectorAll('.trip-type-card').forEach(function (b) {
          b.classList.remove('selected');
          var icon = b.querySelector('.trip-type-card-icon');
          if (icon) { icon.className = 'trip-type-card-icon fa-regular fa-heart'; }
        });
        this.classList.add('selected');
        var icon = this.querySelector('.trip-type-card-icon');
        if (icon) { icon.className = 'trip-type-card-icon fa-solid fa-heart'; }
        var type = this.getAttribute('data-type');
        var hid = document.getElementById('trip_type');
        if (hid) hid.value = type;
        applyAdultLimitForTripType(type);
        var tripTypeErrorEl = document.getElementById('tripTypeError');
        if (tripTypeErrorEl) { tripTypeErrorEl.textContent = ''; tripTypeErrorEl.classList.remove('trip-date-error--visible'); }
      });
    });

    var durationEl = document.getElementById('tripDuration');
    var startDateEl = document.getElementById('start_date');
    var endDateEl = document.getElementById('end_date');
    var dateErrorEl = document.getElementById('tripDateError');

    function todayStr() {
      var d = new Date();
      return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }
    function minStartDateStr() {
      var d = new Date();
      d.setDate(d.getDate() + 21);
      return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    function validateDates() {
      if (!dateErrorEl) return true;
      dateErrorEl.textContent = '';
      dateErrorEl.classList.remove('trip-date-error--visible');
      if (!startDateEl || !endDateEl) return true;
      var startVal = startDateEl.value;
      var endVal = endDateEl.value;
      if (!startVal || !endVal) return true;
      var start = new Date(startVal);
      var end = new Date(endVal);
      if (end < start) {
        dateErrorEl.textContent = 'End date must be on or after start date.';
        dateErrorEl.classList.add('trip-date-error--visible');
        return false;
      }
      return true;
    }

    function updateDurationBanner() {
      if (!durationEl || !startDateEl || !endDateEl) return;
      if (!validateDates()) {
        durationEl.textContent = '—';
        return;
      }
      var start = startDateEl.value ? new Date(startDateEl.value) : null;
      var end = endDateEl.value ? new Date(endDateEl.value) : null;
      if (!start || !end || end < start) {
        durationEl.textContent = '—';
        return;
      }
      var nights = Math.round((end - start) / (24 * 60 * 60 * 1000));
      if (nights < 0) nights = 0;
      durationEl.textContent = nights + ' Night' + (nights !== 1 ? 's' : '');
    }

    if (startDateEl) {
      var minStart = minStartDateStr();
      startDateEl.setAttribute('min', minStart);
      if (startDateEl.value && startDateEl.value < minStart) {
        startDateEl.value = minStart;
        if (endDateEl && endDateEl.value && endDateEl.value < minStart) endDateEl.value = minStart;
      }
      startDateEl.addEventListener('change', function () {
        if (endDateEl && startDateEl.value) endDateEl.setAttribute('min', startDateEl.value);
        if (endDateEl && endDateEl.value && endDateEl.value < startDateEl.value) endDateEl.value = startDateEl.value;
        updateDurationBanner();
      });
    }
    if (endDateEl) {
      if (startDateEl && startDateEl.value) endDateEl.setAttribute('min', startDateEl.value);
      endDateEl.addEventListener('change', updateDurationBanner);
    }
    updateDurationBanner();

    var stopIndex = 0;
    function addStopCard(data) {
      data = data || { location: '', transportNeeded: 'no', tourGuideNeeded: 'no' };
      var idx = stopIndex++;
      var nameLoc = (data.location || '').replace(/"/g, '&quot;').replace(/</g, '&lt;');
      var card = document.createElement('div');
      card.className = 'trip-stop-card';
      card.dataset.stopIndex = idx;
      card.innerHTML =
        '<div class="trip-stop-card-header">' +
        '<h4 class="trip-stop-title">Stop ' + (document.getElementById('tripStopsList').children.length + 1) + '</h4>' +
        '<button type="button" class="btn-remove-stop" aria-label="Remove this stop"><i class="fa-solid fa-trash-can"></i> Remove</button>' +
        '</div>' +
        '<div class="form-group trip-stop-location-group"><label>Stop location / attraction</label><div class="trip-stop-location-input-wrap"><i class="fa-solid fa-location-dot trip-stop-location-icon"></i><input type="text" class="trip-stop-location" placeholder="Stop location / attraction" value="' + nameLoc + '"></div></div>' +
        '<div class="trip-stop-options">' +
        '<div class="trip-stop-option-group">' +
        '<span class="trip-option-label">Transport Needed?</span>' +
        '<div class="trip-toggle-btns">' +
        '<button type="button" class="trip-toggle-btn' + (data.transportNeeded === 'yes' ? ' selected' : '') + '" data-value="yes">Yes</button>' +
        '<button type="button" class="trip-toggle-btn' + (data.transportNeeded !== 'yes' ? ' selected' : '') + '" data-value="no">No</button>' +
        '</div></div>' +
        '<div class="trip-stop-option-group">' +
        '<span class="trip-option-label">Tour Guide Needed?</span>' +
        '<div class="trip-toggle-btns">' +
        '<button type="button" class="trip-toggle-btn' + (data.tourGuideNeeded === 'yes' ? ' selected' : '') + '" data-value="yes">Yes</button>' +
        '<button type="button" class="trip-toggle-btn' + (data.tourGuideNeeded !== 'yes' ? ' selected' : '') + '" data-value="no">No</button>' +
        '</div></div>' +
        '</div>';
      document.getElementById('tripStopsList').appendChild(card);
      renumberStops();
      var locInput = card.querySelector('.trip-stop-location');
      if (locInput) initStopLocationAutocomplete(locInput);
    }
    var placesAutocompleteDebounceTimer, placesAutocompleteAbort;
    function initStopLocationAutocomplete(inputEl) {
      if (!inputEl || inputEl.dataset.placesInit === 'true') return;
      inputEl.dataset.placesInit = 'true';
      var dropdown = document.createElement('div');
      dropdown.className = 'trip-stop-location-suggestions';
      dropdown.setAttribute('role', 'listbox');
      dropdown.setAttribute('aria-hidden', 'true');
      var wrap = inputEl.closest('.trip-stop-location-input-wrap') || inputEl.parentNode;
      wrap.style.position = 'relative';
      wrap.appendChild(dropdown);
      function hideDropdown() {
        dropdown.innerHTML = '';
        dropdown.style.display = 'none';
        dropdown.setAttribute('aria-hidden', 'true');
      }
      function showSuggestions(predictions) {
        dropdown.innerHTML = '';
        if (!predictions || predictions.length === 0) { hideDropdown(); return; }
        predictions.forEach(function (p) {
          var desc = (typeof p === 'object' && p !== null && p.description) ? p.description : (typeof p === 'string' ? p : '');
          if (!desc) return;
          var item = document.createElement('div');
          item.className = 'trip-stop-location-suggestion-item';
          item.setAttribute('role', 'option');
          item.textContent = desc;
          item.dataset.description = desc;
          if (p && p.place_id) item.dataset.placeId = p.place_id;
          item.addEventListener('mousedown', function (e) {
            e.preventDefault();
            inputEl.value = this.dataset.description || this.textContent;
            if (this.dataset.placeId) inputEl.dataset.placeId = this.dataset.placeId; else delete inputEl.dataset.placeId;
            hideDropdown();
          });
          dropdown.appendChild(item);
        });
        dropdown.style.display = 'block';
        dropdown.setAttribute('aria-hidden', 'false');
      }
      function fetchSuggestions(query, done) {
        if (placesAutocompleteAbort) placesAutocompleteAbort.abort();
        if (!query || query.length < 2) { done([]); return; }
        var url = '<?php echo htmlspecialchars($places_autocomplete_url, ENT_QUOTES, 'UTF-8'); ?>?input=' + encodeURIComponent(query);
        var destHidden = document.getElementById('dest_district_hidden');
        if (destHidden && destHidden.value) {
          url += '&district=' + encodeURIComponent(destHidden.value);
        }
        var xhr = new XMLHttpRequest();
        placesAutocompleteAbort = xhr;
        xhr.onreadystatechange = function () {
          if (xhr.readyState !== 4) return;
          if (placesAutocompleteAbort !== xhr) return;
          if (xhr.status === 200) {
            try {
              var data = JSON.parse(xhr.responseText);
              var list = Array.isArray(data.predictions) ? data.predictions : [];
              done(list);
            } catch (e) { done([]); }
          } else { done([]); }
        };
        xhr.open('GET', url, true);
        xhr.send();
      }
      inputEl.addEventListener('input', function () {
        delete inputEl.dataset.placeId;
        clearTimeout(placesAutocompleteDebounceTimer);
        var q = inputEl.value.trim();
        if (q.length < 2) { hideDropdown(); return; }
        placesAutocompleteDebounceTimer = setTimeout(function () { fetchSuggestions(q, showSuggestions); }, 280);
      });
      inputEl.addEventListener('focus', function () {
        var q = inputEl.value.trim();
        if (q.length >= 2) fetchSuggestions(q, showSuggestions);
      });
      inputEl.addEventListener('blur', function () { setTimeout(hideDropdown, 220); });
    }
    function renumberStops() {
      var list = document.getElementById('tripStopsList');
      if (!list) return;
      var cards = list.querySelectorAll('.trip-stop-card');
      cards.forEach(function (card, i) {
        var title = card.querySelector('.trip-stop-title');
        if (title) title.textContent = 'Stop ' + (i + 1);
      });
    }
    function updateStopsHeading() {
      var heading = document.getElementById('tripStopsHeading');
      var destInput = document.getElementById('dest_primary');
      if (!heading || !destInput) return;
      var name = (destInput.value || '').trim();
      heading.textContent = name ? 'Stops in ' + name : 'Stops in this area';
    }
    var destCities = <?php echo json_encode(array_keys($main_cities)); ?>;
    var destCityToDistrict = <?php echo json_encode($main_cities); ?>;
    (function initDestAutocomplete() {
      var input = document.getElementById('dest_primary');
      var hidden = document.getElementById('dest_district_hidden');
      var dropdown = document.getElementById('dest_suggestions');
      if (!input || !hidden || !dropdown) return;
      function hideDestDropdown() {
        dropdown.innerHTML = '';
        dropdown.style.display = 'none';
        dropdown.setAttribute('aria-hidden', 'true');
        input.setAttribute('aria-expanded', 'false');
      }
      function showDestSuggestions(list) {
        dropdown.innerHTML = '';
        if (!list || list.length === 0) { hideDestDropdown(); return; }
        list.forEach(function (city) {
          var district = destCityToDistrict[city];
          var item = document.createElement('div');
          item.className = 'trip-dest-suggestion-item';
          item.setAttribute('role', 'option');
          item.textContent = city;
          item.addEventListener('mousedown', function (e) {
            e.preventDefault();
            input.value = city;
            hidden.value = district || '';
            hideDestDropdown();
            updateStopsHeading();
          });
          dropdown.appendChild(item);
        });
        dropdown.style.display = 'block';
        dropdown.setAttribute('aria-hidden', 'false');
        input.setAttribute('aria-expanded', 'true');
      }
      function filterCities(q) {
        if (!q) return destCities.slice();
        q = q.toLowerCase();
        return destCities.filter(function (c) { return c.toLowerCase().indexOf(q) !== -1; });
      }
      input.addEventListener('input', function () {
        var q = input.value.trim();
        showDestSuggestions(filterCities(q));
      });
      input.addEventListener('focus', function () {
        showDestSuggestions(filterCities(input.value.trim()));
      });
      input.addEventListener('blur', function () {
        setTimeout(function () {
          hideDestDropdown();
          var v = input.value.trim();
          if (v && destCityToDistrict[v]) hidden.value = destCityToDistrict[v];
        }, 220);
      });
      updateStopsHeading();
    })();
    document.getElementById('btnAddMoreStops').addEventListener('click', function () {
      addStopCard();
    });
    document.getElementById('tripStopsList').addEventListener('click', function (e) {
      var removeBtn = e.target.closest('.btn-remove-stop');
      if (removeBtn) {
        var card = removeBtn.closest('.trip-stop-card');
        if (card) { card.remove(); renumberStops(); }
        return;
      }
      var toggleBtn = e.target.closest('.trip-toggle-btn');
      if (toggleBtn) {
        var group = toggleBtn.closest('.trip-stop-option-group');
        if (group) {
          group.querySelectorAll('.trip-toggle-btn').forEach(function (b) { b.classList.remove('selected'); });
          toggleBtn.classList.add('selected');
          var labelEl = group.querySelector('.trip-option-label');
          var labelText = labelEl ? labelEl.textContent : '';
            if (toggleBtn.getAttribute('data-value') === 'yes' && labelText) {
            if (labelText.indexOf('Transport') !== -1) {
              var overlay = document.getElementById('transportRequestModalOverlay');
              var startEl = document.getElementById('start_date');
              var endEl = document.getElementById('end_date');
              var trDateEl = document.getElementById('tr_date');
              if (trDateEl && startEl && endEl && startEl.value && endEl.value) {
                trDateEl.setAttribute('min', startEl.value);
                trDateEl.setAttribute('max', endEl.value);
                var v = trDateEl.value;
                if (v && (v < startEl.value || v > endEl.value)) trDateEl.value = '';
              }
              var trNumPeopleEl = document.getElementById('tr_numPeople');
              var adultsEl = document.getElementById('adults');
              var childrenEl = document.getElementById('children');
              var infantsEl = document.getElementById('infants');
              if (trNumPeopleEl && adultsEl && childrenEl && infantsEl) {
                var total = (parseInt(adultsEl.value, 10) || 0) + (parseInt(childrenEl.value, 10) || 0) + (parseInt(infantsEl.value, 10) || 0);
                trNumPeopleEl.value = Math.max(1, total);
              }
              if (overlay) {
                overlay.classList.add('trip-modal-open');
                overlay.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
              }
            } else if (labelText.indexOf('Tour Guide') !== -1) {
              var guideOverlay = document.getElementById('tourGuideRequestModalOverlay');
              if (guideOverlay) {
                guideOverlay.classList.add('trip-modal-open');
                guideOverlay.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
              }
            }
          }
        }
      }
    });

    var transportModalOverlay = document.getElementById('transportRequestModalOverlay');
    function closeTransportModal() {
      if (transportModalOverlay) {
        transportModalOverlay.classList.remove('trip-modal-open');
        transportModalOverlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
      }
      if (typeof showTransportForm === 'function') showTransportForm();
    }
    if (document.getElementById('transportRequestModalClose')) {
      document.getElementById('transportRequestModalClose').addEventListener('click', closeTransportModal);
    }
    if (document.getElementById('transportRequestModalCancel')) {
      document.getElementById('transportRequestModalCancel').addEventListener('click', closeTransportModal);
    }
    if (transportModalOverlay) {
      transportModalOverlay.addEventListener('click', function (e) {
        if (e.target === transportModalOverlay) closeTransportModal();
      });
    }
    var vehicleCapacities = { 'Tuk': 3, 'Car': 4, 'Minivan': 7, 'Minivan AC': 7, 'Bus': 20, 'Bus AC': 20 };
    var transportForm = document.getElementById('transportRequestForm');
    var trFormWrap = document.getElementById('tr_formWrap');
    var trSuccessState = document.getElementById('tr_successState');
    var transportSubmitUrl = '/CeylonGo/public/tourist/transport-services';
    var lastSubmittedNumPeople = 0;
    function showTransportForm() {
      if (trFormWrap) trFormWrap.style.display = '';
      if (trSuccessState) trSuccessState.style.display = 'none';
    }
    function showTransportSuccess() {
      if (trFormWrap) trFormWrap.style.display = 'none';
      if (trSuccessState) trSuccessState.style.display = 'block';
    }
    function resetTransportFormForAnother() {
      var vehicleSelect = document.getElementById('tr_vehicleType');
      var numPeopleEl = document.getElementById('tr_numPeople');
      var estimatedFare = document.getElementById('tr_estimatedFare');
      var fareValEl = document.getElementById('tr_estimatedFareValue');
      var distValEl = document.getElementById('tr_distanceValue');
      var breakdown = document.getElementById('tr_fareBreakdown');
      var errEl = document.getElementById('tr_vehicleError');
      if (vehicleSelect) vehicleSelect.value = '';
      if (numPeopleEl) {
        var adultsEl = document.getElementById('adults');
        var childrenEl = document.getElementById('children');
        var infantsEl = document.getElementById('infants');
        var total = (parseInt(adultsEl && adultsEl.value, 10) || 0) + (parseInt(childrenEl && childrenEl.value, 10) || 0) + (parseInt(infantsEl && infantsEl.value, 10) || 0);
        var remaining = total - lastSubmittedNumPeople;
        numPeopleEl.value = Math.max(1, remaining);
      }
      if (estimatedFare) estimatedFare.value = 'LKR 0.00';
      if (fareValEl) fareValEl.value = '';
      if (distValEl) distValEl.value = '';
      if (breakdown) breakdown.style.display = 'none';
      if (errEl) { errEl.style.display = 'none'; errEl.textContent = ''; }
      var confirmBtn = document.getElementById('tr_btnConfirm');
      if (confirmBtn) confirmBtn.disabled = true;
    }
    if (document.getElementById('tr_btnAddAnother')) {
      document.getElementById('tr_btnAddAnother').addEventListener('click', function () {
        resetTransportFormForAnother();
        showTransportForm();
      });
    }
    if (document.getElementById('tr_btnDone')) {
      document.getElementById('tr_btnDone').addEventListener('click', function () {
        window.location.href = '/CeylonGo/public/tourist/transport-report';
      });
    }
    if (transportForm) {
      transportForm.addEventListener('submit', function (e) {
        e.preventDefault();
        var vehicleSelect = document.getElementById('tr_vehicleType');
        var numPeopleEl = document.getElementById('tr_numPeople');
        var errorEl = document.getElementById('tr_vehicleError');
        if (!vehicleSelect || !numPeopleEl || !errorEl) return;
        var vehicle = vehicleSelect.value;
        var numPeople = parseInt(numPeopleEl.value, 10) || 0;
        var capacity = vehicleCapacities[vehicle];
        errorEl.style.display = 'none';
        errorEl.textContent = '';
        if (vehicle && capacity !== undefined && numPeople > capacity) {
          errorEl.textContent = 'This vehicle accommodates up to ' + capacity + ' people. Please choose a larger vehicle or reduce the number of people.';
          errorEl.style.display = 'block';
          return;
        }
        var submitBtn = transportForm.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;
        var formData = new FormData(transportForm);
        fetch(transportSubmitUrl, {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
          .then(function (r) { return r.json(); })
          .then(function (data) {
            if (submitBtn) submitBtn.disabled = false;
            if (data && data.success) {
              lastSubmittedNumPeople = parseInt(numPeopleEl ? numPeopleEl.value : 0, 10) || 0;
              showTransportSuccess();
            } else {
              alert(data && data.error ? data.error : 'Failed to submit transport request.');
            }
          })
          .catch(function () {
            if (submitBtn) submitBtn.disabled = false;
            alert('An error occurred. Please try again.');
          });
      });
      document.getElementById('tr_vehicleType').addEventListener('change', function () {
        var err = document.getElementById('tr_vehicleError');
        if (err) { err.style.display = 'none'; err.textContent = ''; }
      });
    }
    var trPickupInput = document.getElementById('tr_pickupLocation');
    var trDropoffInput = document.getElementById('tr_dropoffLocation');
    if (trPickupInput) initStopLocationAutocomplete(trPickupInput);
    if (trDropoffInput) initStopLocationAutocomplete(trDropoffInput);

    var trBtnCalculate = document.getElementById('tr_btnCalculate');
    var trEstimatedFare = document.getElementById('tr_estimatedFare');
    var placesAutocompleteUrl = '<?php echo htmlspecialchars($places_autocomplete_url ?? '/CeylonGo/public/api/places-autocomplete', ENT_QUOTES, 'UTF-8'); ?>';
    var calculateFareUrl = (placesAutocompleteUrl.split('/api/')[0] || '') + '/api/calculate-fare';
    if (trBtnCalculate && trEstimatedFare) {
      trBtnCalculate.addEventListener('click', function () {
        var pickup = (document.getElementById('tr_pickupLocation') || {}).value.trim();
        var dropoff = (document.getElementById('tr_dropoffLocation') || {}).value.trim();
        var vehicleType = (document.getElementById('tr_vehicleType') || {}).value;
        if (!pickup || !dropoff) {
          alert('Please enter both pickup and dropoff locations');
          return;
        }
        if (!vehicleType) {
          alert('Please select a vehicle type');
          return;
        }
        trEstimatedFare.value = 'Calculating...';
        trBtnCalculate.disabled = true;
        var params = 'pickup=' + encodeURIComponent(pickup) + '&dropoff=' + encodeURIComponent(dropoff) + '&vehicleType=' + encodeURIComponent(vehicleType);
        var pickupPlaceId = (trPickupInput && trPickupInput.dataset && trPickupInput.dataset.placeId) ? trPickupInput.dataset.placeId : '';
        var dropoffPlaceId = (trDropoffInput && trDropoffInput.dataset && trDropoffInput.dataset.placeId) ? trDropoffInput.dataset.placeId : '';
        if (pickupPlaceId) params += '&pickup_place_id=' + encodeURIComponent(pickupPlaceId);
        if (dropoffPlaceId) params += '&dropoff_place_id=' + encodeURIComponent(dropoffPlaceId);
        fetch(calculateFareUrl + '?' + params)
          .then(function (r) { return r.json(); })
          .then(function (data) {
            trBtnCalculate.disabled = false;
            if (data && data.success) {
              trEstimatedFare.value = 'LKR ' + Number(data.totalFare).toFixed(2);
              var fareValEl = document.getElementById('tr_estimatedFareValue');
              var distValEl = document.getElementById('tr_distanceValue');
              if (fareValEl) fareValEl.value = String(data.totalFare || '');
              if (distValEl) distValEl.value = String(data.distance || '');
              var confirmBtn = document.getElementById('tr_btnConfirm');
              if (confirmBtn) confirmBtn.disabled = false;
              var breakdown = document.getElementById('tr_fareBreakdown');
              var distEl = document.getElementById('tr_fareDistance');
              var rateEl = document.getElementById('tr_fareBaseRate');
              var totalEl = document.getElementById('tr_fareTotal');
              if (breakdown && distEl && rateEl && totalEl) {
                distEl.textContent = (data.distance || 0) + ' km';
                rateEl.textContent = 'LKR ' + (data.baseRate || 0) + '/km';
                totalEl.textContent = 'LKR ' + Number(data.totalFare).toFixed(2);
                breakdown.style.display = 'block';
              }
            } else {
              trEstimatedFare.value = 'LKR 0.00';
              var fareValEl = document.getElementById('tr_estimatedFareValue');
              var distValEl = document.getElementById('tr_distanceValue');
              if (fareValEl) fareValEl.value = '';
              if (distValEl) distValEl.value = '';
              var breakdown = document.getElementById('tr_fareBreakdown');
              if (breakdown) breakdown.style.display = 'none';
              alert(data.error || 'Could not calculate fare. Please check the locations.');
            }
          })
          .catch(function () {
            trBtnCalculate.disabled = false;
            trEstimatedFare.value = 'LKR 0.00';
            var fareValEl = document.getElementById('tr_estimatedFareValue');
            var distValEl = document.getElementById('tr_distanceValue');
            if (fareValEl) fareValEl.value = '';
            if (distValEl) distValEl.value = '';
            var breakdown = document.getElementById('tr_fareBreakdown');
            if (breakdown) breakdown.style.display = 'none';
            alert('An error occurred. Please try again.');
          });
      });
    }

    var tourGuideModalOverlay = document.getElementById('tourGuideRequestModalOverlay');
    function closeTourGuideModal() {
      if (tourGuideModalOverlay) {
        tourGuideModalOverlay.classList.remove('trip-modal-open');
        tourGuideModalOverlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
      }
    }
    if (document.getElementById('tourGuideRequestModalClose')) {
      document.getElementById('tourGuideRequestModalClose').addEventListener('click', closeTourGuideModal);
    }
    if (document.getElementById('tourGuideRequestModalCancel')) {
      document.getElementById('tourGuideRequestModalCancel').addEventListener('click', closeTourGuideModal);
    }
    if (tourGuideModalOverlay) {
      tourGuideModalOverlay.addEventListener('click', function (e) {
        if (e.target === tourGuideModalOverlay) closeTourGuideModal();
      });
    }

    function tripAccommodationSearch(e) {
      e.preventDefault();
      var term = (document.getElementById('tripAccommodationSearchInput') || {}).value.toLowerCase();
      var grid = document.getElementById('tripAccommodationHotelsGrid');
      if (!grid) return;
      grid.querySelectorAll('.hotel-card').forEach(function (card) {
        var name = (card.querySelector('.hotel-name') || {}).textContent || '';
        var loc = (card.querySelector('.hotel-location') || {}).textContent || '';
        card.style.display = (name.toLowerCase().indexOf(term) !== -1 || loc.toLowerCase().indexOf(term) !== -1) ? '' : 'none';
      });
    }
    function tripAccommodationApplyFilters() {
      var price = (document.getElementById('tripAccommodationPriceFilter') || {}).value;
      var rating = (document.getElementById('tripAccommodationRatingFilter') || {}).value;
      var location = (document.getElementById('tripAccommodationLocationFilter') || {}).value;
      var grid = document.getElementById('tripAccommodationHotelsGrid');
      if (!grid) return;
      grid.querySelectorAll('.hotel-card').forEach(function (card) {
        var show = true;
        if (price && card.dataset.price !== price) show = false;
        if (rating && card.dataset.rating !== rating) show = false;
        if (location && card.dataset.location.indexOf(location) === -1) show = false;
        card.style.display = show ? '' : 'none';
      });
    }
    function tripAccommodationCloseModal() {
      var modal = document.getElementById('tripAccommodationBookingModal');
      if (modal) { modal.classList.remove('active'); document.body.style.overflow = ''; }
    }
    var tripDetailsModalHotelName = '';
    function tripAccommodationOpenDetailsModal(card) {
      if (!card) return;
      var imgEl = card.querySelector('.hotel-image');
      var nameEl = card.querySelector('.hotel-name');
      var locEl = card.querySelector('.hotel-location');
      var ratingEl = card.querySelector('.hotel-rating');
      var amenitiesEl = card.querySelector('.hotel-amenities');
      var priceEl = card.querySelector('.hotel-price');
      var imgStyle = imgEl ? imgEl.getAttribute('style') || '' : '';
      var imgUrl = imgStyle.match(/url\(['"]?([^'"]+)['"]?\)/);
      document.getElementById('tripDetailsModalImage').style.backgroundImage = imgUrl ? 'url(' + imgUrl[1] + ')' : 'none';
      document.getElementById('tripDetailsModalName').textContent = nameEl ? nameEl.textContent.trim() : '';
      document.getElementById('tripDetailsModalLocation').textContent = locEl ? locEl.textContent.trim() : '';
      document.getElementById('tripDetailsModalRating').innerHTML = ratingEl ? ratingEl.innerHTML : '';
      document.getElementById('tripDetailsModalAmenities').innerHTML = amenitiesEl ? amenitiesEl.innerHTML : '';
      document.getElementById('tripDetailsModalPrice').innerHTML = priceEl ? priceEl.innerHTML : '';
      tripDetailsModalHotelName = nameEl ? nameEl.textContent.trim() : '';
      var roomsRoot = document.getElementById('tripDetailsModalRooms');
      if (roomsRoot) {
        roomsRoot.innerHTML = '';
        var hotelId = card.dataset.hotelId || '';
        var roomDataMap = window.tripAccommodationRoomOptions || {};
        var rooms = roomDataMap[hotelId] || [];
        if (rooms.length) {
          rooms.forEach(function (room) {
            var wrapper = document.createElement('div');
            wrapper.className = 'modal-room-card';
            var img = document.createElement('div');
            img.className = 'modal-room-image';
            if (room.image) img.style.backgroundImage = 'url(' + room.image + ')';
            var info = document.createElement('div');
            info.className = 'modal-room-info';
            var typeEl = document.createElement('div');
            typeEl.className = 'modal-room-type';
            typeEl.textContent = room.type || '';
            var descEl = document.createElement('div');
            descEl.className = 'modal-room-desc';
            descEl.textContent = room.description || '';
            var priceEl2 = document.createElement('div');
            priceEl2.className = 'modal-room-price';
            priceEl2.textContent = room.price || '';
            info.appendChild(typeEl);
            info.appendChild(descEl);
            info.appendChild(priceEl2);
            wrapper.appendChild(img);
            wrapper.appendChild(info);
            roomsRoot.appendChild(wrapper);
          });
        }
      }
      var modal = document.getElementById('tripAccommodationDetailsModal');
      if (modal) { modal.classList.add('active'); document.body.style.overflow = 'hidden'; }
    }
    function tripAccommodationCloseDetailsModal() {
      var modal = document.getElementById('tripAccommodationDetailsModal');
      if (modal) { modal.classList.remove('active'); document.body.style.overflow = ''; }
    }
    function tripAccommodationPopulateBookingForm(hotelId, hotelName) {
      var hidName = document.getElementById('tripAccommodationHotelName');
      var hidId = document.getElementById('tripAccommodationHotelId');
      if (hidName) hidName.value = hotelName || '';
      if (hidId) hidId.value = hotelId || '';

      var nameInput = document.getElementById('tripAccommodationCustomerName');
      var contactInput = document.getElementById('tripAccommodationContact');
      var guestsInput = document.getElementById('tripAccommodationGuests');
      if (nameInput) nameInput.value = tripUserName || '';
      if (contactInput) contactInput.value = tripUserContact || '';
      if (guestsInput) {
        var a = parseInt(adultsInput ? adultsInput.value : '0', 10) || 0;
        var c = parseInt(childrenInput ? childrenInput.value : '0', 10) || 0;
        var i = parseInt(infantsInput ? infantsInput.value : '0', 10) || 0;
        var total = a + c + i;
        guestsInput.value = total || 1;
      }

      var startEl = document.getElementById('start_date');
      var endEl = document.getElementById('end_date');
      var checkInEl = document.getElementById('tripAccommodationCheckIn');
      var checkOutEl = document.getElementById('tripAccommodationCheckOut');
      var nightsHidden = document.getElementById('tripAccommodationNights');
      var nights = 1;
      if (startEl && endEl && startEl.value && endEl.value) {
        var s = new Date(startEl.value);
        var e = new Date(endEl.value);
        var diff = Math.round((e - s) / (24 * 60 * 60 * 1000));
        if (diff > 0) nights = diff;
        if (checkInEl) { checkInEl.value = startEl.value; checkInEl.min = startEl.value; }
        if (checkOutEl) { checkOutEl.value = endEl.value; checkOutEl.min = startEl.value; }
      }
      if (nightsHidden) nightsHidden.value = nights;

      var roomSelect = document.getElementById('tripAccommodationRoomType');
      var roomCountInput = document.getElementById('tripAccommodationRoomCount');
      var totalPriceInput = document.getElementById('tripAccommodationTotalPrice');

      if (roomSelect) {
        while (roomSelect.firstChild) roomSelect.removeChild(roomSelect.firstChild);
        var opt = document.createElement('option');
        opt.value = '';
        opt.textContent = 'Select Room Type';
        roomSelect.appendChild(opt);
        var roomMap = window.tripAccommodationRoomOptions || {};
        var rooms = roomMap[hotelId] || [];
        rooms.forEach(function (room) {
          var o = document.createElement('option');
          o.value = room.type || '';
          o.textContent = (room.type || '') + (room.price ? ' – ' + room.price + ' / night' : '');
          if (room.priceValue) o.dataset.pricePerNight = room.priceValue;
          roomSelect.appendChild(o);
        });
      }
      if (roomCountInput) roomCountInput.value = 1;
      if (totalPriceInput) totalPriceInput.value = 'Rs.0.00';

      updateAccommodationTotalPrice();
    }

    function updateAccommodationTotalPrice() {
      var roomSelect = document.getElementById('tripAccommodationRoomType');
      var roomCountInput = document.getElementById('tripAccommodationRoomCount');
      var nightsHidden = document.getElementById('tripAccommodationNights');
      var totalPriceInput = document.getElementById('tripAccommodationTotalPrice');
      if (!roomSelect || !roomCountInput || !nightsHidden || !totalPriceInput) return;
      var nights = parseInt(nightsHidden.value || '1', 10) || 1;
      var count = parseInt(roomCountInput.value || '1', 10) || 1;
      var pricePerNight = 0;
      var selected = roomSelect.options[roomSelect.selectedIndex];
      if (selected && selected.dataset.pricePerNight) {
        pricePerNight = parseFloat(selected.dataset.pricePerNight) || 0;
      }
      var total = pricePerNight * nights * count;
      if (total <= 0) {
        totalPriceInput.value = 'Rs.0.00';
      } else {
        totalPriceInput.value = 'Rs.' + total.toLocaleString('en-LK', { minimumFractionDigits: 0 });
      }
      var totalNumericInput = document.getElementById('tripAccommodationTotalPriceNumeric');
      if (totalNumericInput) totalNumericInput.value = total > 0 ? total : 0;
    }

    function tripAccommodationOpenBookingFromDetails() {
      tripAccommodationCloseDetailsModal();
      var hid = document.getElementById('tripAccommodationHotelName');
      var hotelIdInput = document.getElementById('tripAccommodationHotelId');
      var hotelName = tripDetailsModalHotelName || (hid ? hid.value : '');
      var hotelId = hotelIdInput ? hotelIdInput.value : '';
      tripAccommodationPopulateBookingForm(hotelId, hotelName);
      var bookingModal = document.getElementById('tripAccommodationBookingModal');
      if (bookingModal) { bookingModal.classList.add('active'); }
    }
    function tripAccommodationConfirmBooking() {
      var checkIn = document.getElementById('tripAccommodationCheckIn');
      var checkOut = document.getElementById('tripAccommodationCheckOut');
      var roomType = document.getElementById('tripAccommodationRoomType');
      var totalPriceNumeric = document.getElementById('tripAccommodationTotalPriceNumeric');
      if (!checkIn || !checkIn.value || !checkOut || !checkOut.value || !roomType || !roomType.value) {
        alert('Please fill in all required fields.');
        return false;
      }
      if (totalPriceNumeric && (!totalPriceNumeric.value || parseFloat(totalPriceNumeric.value) <= 0)) {
        alert('Please select a room type so we can calculate the total price.');
        return false;
      }
      return true; // allow form submit
    }
    var accContent = document.querySelector('.trip-accommodation-content');
    if (accContent) {
      ['tripAccommodationPriceFilter', 'tripAccommodationRatingFilter', 'tripAccommodationLocationFilter'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('change', tripAccommodationApplyFilters);
      });
      var roomSelectEl = document.getElementById('tripAccommodationRoomType');
      var roomCountEl = document.getElementById('tripAccommodationRoomCount');
      if (roomSelectEl) roomSelectEl.addEventListener('change', updateAccommodationTotalPrice);
      if (roomCountEl) roomCountEl.addEventListener('input', updateAccommodationTotalPrice);
      accContent.addEventListener('click', function (e) {
        var detailsBtn = e.target.closest('.btn-details[data-view-details]');
        if (detailsBtn) {
          e.preventDefault();
          var card = detailsBtn.closest('.hotel-card');
          if (card) tripAccommodationOpenDetailsModal(card);
          return;
        }
        var btn = e.target.closest('.btn-book');
        if (btn && btn.href) {
          e.preventDefault();
          var card = btn.closest('.hotel-card');
          var nameEl = card ? card.querySelector('.hotel-name') : null;
          var hotelName = nameEl ? nameEl.textContent.trim() : '';
          var hotelId = card ? (card.dataset.hotelId || '') : '';
          tripAccommodationPopulateBookingForm(hotelId, hotelName);
          var modal = document.getElementById('tripAccommodationBookingModal');
          if (modal) { modal.classList.add('active'); }
        }
      });
      var detailsModal = document.getElementById('tripAccommodationDetailsModal');
      if (detailsModal) detailsModal.addEventListener('click', function (e) { if (e.target === detailsModal) tripAccommodationCloseDetailsModal(); });
      var accModal = document.getElementById('tripAccommodationBookingModal');
      if (accModal) accModal.addEventListener('click', function (e) { if (e.target === accModal) tripAccommodationCloseModal(); });
    }

    addStopCard();
  });
  </script>
</body>
</html>
