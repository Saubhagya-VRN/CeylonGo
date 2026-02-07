<?php
$user_name = $user_name ?? '';
$tourist_data = $tourist_data ?? null;
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
      <ul>
        <li><a href="/CeylonGo/public/tourist/dashboard"><i class="fa-solid fa-table-columns"></i> <span class="sidebar-link-text">Dashboard</span></a></li>
        <li><a href="#"><i class="fa-regular fa-message"></i> <span class="sidebar-link-text">Queries <span class="sidebar-sub">Active Requests</span></span></a></li>
        <li><a href="#"><i class="fa-regular fa-file-lines"></i> <span class="sidebar-link-text">Itineraries <span class="sidebar-sub">Shared Quotes</span></span></a></li>
        <li><a href="#"><i class="fa-regular fa-calendar-check"></i> <span class="sidebar-link-text">Bookings <span class="sidebar-sub">Manage Reservations</span></span></a></li>
        <li><a href="#"><i class="fa-solid fa-credit-card"></i> <span class="sidebar-link-text">Payments <span class="sidebar-sub">Invoices & Wallet</span></span></a></li>
        <li><a href="#"><i class="fa-regular fa-heart"></i> <span class="sidebar-link-text">Wishlist <span class="sidebar-sub">Saved Destinations</span></span></a></li>
        <li class="active"><a href="/CeylonGo/public/tourist/customize-trip"><i class="fa-solid fa-wand-magic-sparkles"></i> <span class="sidebar-link-text">Customise Your Trip <span class="sidebar-sub">Plan Custom Trips</span></span></a></li>
        <li><a href="/CeylonGo/public/tourist/profile"><i class="fa-regular fa-user"></i> <span class="sidebar-link-text">Profile <span class="sidebar-sub">Account Settings</span></span></a></li>
      </ul>
      <div class="trip-sidebar-divider"></div>
      <div class="trip-sidebar-user">
        <div class="trip-sidebar-user-avatar"><?php echo htmlspecialchars($avatar_initial); ?></div>
        <div class="trip-sidebar-user-info">
          <div class="trip-sidebar-user-name"><?php echo htmlspecialchars($user_name ?: 'Tourist'); ?></div>
          <div class="trip-sidebar-user-email"><?php echo htmlspecialchars($user_email ? substr($user_email, 0, 20) . (strlen($user_email) > 20 ? '...' : '') : ''); ?></div>
        </div>
      </div>
      <a href="/CeylonGo/public/logout" class="trip-sidebar-signout"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</a>
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
              <div class="input-with-icon input-with-icon--dest">
                <i class="fa-solid fa-location-dot input-icon input-icon--left"></i>
                <select id="dest_primary" name="destinations[0][district]">
                  <option value="">Select district</option>
                  <?php foreach ($districts as $val => $label): ?>
                    <option value="<?php echo htmlspecialchars($val); ?>"><?php echo htmlspecialchars($label); ?></option>
                  <?php endforeach; ?>
                </select>
                <i class="fa-solid fa-chevron-down input-icon input-icon--right"></i>
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

          <div class="trip-duration-banner" id="tripDuration">5 Nights Trip</div>

          <div class="trip-stops-section">
            <h3 class="trip-stops-heading">Stops in this area</h3>
            <p class="trip-stops-desc">You can add many stops and request transport &amp; a tour guide if needed.</p>
            <div id="tripStopsList" class="trip-stops-list"></div>
            <button type="button" class="btn-add-more-stops" id="btnAddMoreStops"><i class="fa-solid fa-plus"></i> Add More Stops</button>
          </div>

          <div class="trip-dest-actions">
            <button type="button" class="btn-add-another-dest" id="btnAddAnotherDest"><i class="fa-solid fa-plus"></i> Add Another Destination</button>
            <button type="button" class="btn-end-trip" id="btnEndTrip">End Trip <i class="fa-solid fa-flag-checkered"></i></button>
          </div>

          <input type="hidden" name="destinations[0][days]" value="1">
        </form>
      </div>
      </div>
    </main>
  </div>

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
    }
    if (btnNext) btnNext.addEventListener('click', function () { if (currentStep < totalSteps) showStep(currentStep + 1); });
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
    function applyAdultLimitForTripType(type) {
      if (!adultsInput) return;
      if (type === 'solo') {
        adultsInput.setAttribute('max', '1'); adultsInput.value = '1';
        if (childrenInput) { childrenInput.setAttribute('max', '0'); childrenInput.value = '0'; }
        if (infantsInput) { infantsInput.setAttribute('max', '0'); infantsInput.value = '0'; }
      } else if (type === 'couple') {
        adultsInput.setAttribute('max', '2');
        if (parseInt(adultsInput.value, 10) > 2) adultsInput.value = '2';
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
      durationEl.textContent = nights + ' Night' + (nights !== 1 ? 's' : '') + ' Trip';
    }

    if (startDateEl) {
      startDateEl.setAttribute('min', todayStr());
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
        '<div class="form-group trip-stop-location-group"><label>Stop location / attraction</label><input type="text" class="trip-stop-location" placeholder="Stop location / attraction" value="' + nameLoc + '"></div>' +
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
        }
      }
    });
    addStopCard();
  });
  </script>
</body>
</html>
