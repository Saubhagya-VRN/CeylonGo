<?php
$user_name = $user_name ?? '';
$tourist_data = $tourist_data ?? null;
$user_email = ($tourist_data['email'] ?? $_SESSION['user_email'] ?? '');
$avatar_initial = $user_name ? strtoupper(substr(trim($user_name), 0, 1)) : 'T';
$steps = [
    'Destination & Dates',
    'Travel Group',
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
      <div class="trip-step-card trip-step-card--dest-dates">
        <div class="step-icon step-icon--dest-dates"><i class="fa-solid fa-location-dot"></i></div>
        <h2 class="step-heading">Where do you dream to go next?</h2>
        <p class="step-subheading">Tell us your destination and travel dates.</p>

        <form method="POST" action="#" id="trip-step-form">
          <div class="dest-dates-destination-row">
            <div class="form-group form-group--full">
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
            <div class="form-group">
              <label for="start_date">Start Date</label>
              <div class="input-with-icon input-with-icon--date">
                <i class="fa-regular fa-calendar input-icon input-icon--left"></i>
                <input type="date" id="start_date" name="start_date" value="2026-02-04" required>
                <i class="fa-solid fa-chevron-right input-icon input-icon--right"></i>
              </div>
            </div>
            <div class="form-group">
              <label for="end_date">End Date</label>
              <div class="input-with-icon input-with-icon--date">
                <i class="fa-regular fa-calendar input-icon input-icon--left"></i>
                <input type="date" id="end_date" name="end_date" value="2026-02-09" required>
                <i class="fa-solid fa-chevron-right input-icon input-icon--right"></i>
              </div>
            </div>
          </div>

          <div class="trip-duration-banner" id="tripDuration">5 Nights Trip</div>

          <div class="trip-destinations-heading">Add more destinations (optional)</div>
          <div id="trip-destinations-list" class="trip-destinations-list"></div>
          <template id="trip-destination-template">
            <div class="trip-destination-block" data-index="">
              <div class="trip-destination-row">
                <div class="form-group trip-dest-district">
                  <label>District</label>
                  <div class="input-with-icon">
                    <i class="fa-solid fa-location-dot input-icon"></i>
                    <select name="">
                      <option value="">Select district</option>
                      <?php foreach ($districts as $val => $label): ?>
                        <option value="<?php echo htmlspecialchars($val); ?>"><?php echo htmlspecialchars($label); ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="form-group trip-dest-days">
                  <label>How many days here?</label>
                  <input type="number" class="trip-days-input" value="1" min="1" max="90">
                </div>
                <div class="form-group trip-dest-hotel">
                  <label>&nbsp;</label>
                  <a href="/CeylonGo/public/tourist/choose-hotel" class="btn-choose-hotel" target="_blank">Choose Hotel</a>
                </div>
                <div class="form-group trip-dest-remove">
                  <label>&nbsp;</label>
                  <button type="button" class="btn-remove-dest" title="Remove destination" aria-label="Remove destination"><i class="fa-solid fa-trash-can"></i> Remove</button>
                </div>
              </div>
            </div>
          </template>

          <button type="button" class="trip-add-more-btn" id="tripAddMoreBtn" aria-label="Add another destination"><i class="fa-solid fa-plus"></i> Add More</button>
          <input type="hidden" name="destinations[0][days]" value="1">

          <div class="trip-options-row">
            <div class="trip-option-group">
              <label>Do you want transport?</label>
              <div class="trip-toggle-group">
                <button type="button" class="trip-toggle-btn" data-option="transport" data-value="yes">Yes <i class="fa-solid fa-check"></i></button>
                <button type="button" class="trip-toggle-btn selected" data-option="transport" data-value="no">No</button>
              </div>
              <input type="hidden" name="transport" id="transport" value="no">
            </div>
            <div class="trip-option-group">
              <label>Add a tour guide?</label>
              <div class="trip-toggle-group">
                <button type="button" class="trip-toggle-btn selected" data-option="tour_guide" data-value="yes">Yes <i class="fa-solid fa-check"></i></button>
                <button type="button" class="trip-toggle-btn" data-option="tour_guide" data-value="no">No</button>
              </div>
              <input type="hidden" name="tour_guide" id="tour_guide" value="yes">
            </div>
          </div>
        </form>
      </div>
      </div>

      <div class="trip-step-panel" data-step="2">
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
    var destList = document.getElementById('trip-destinations-list');
    var startDateEl = document.getElementById('start_date');
    var endDateEl = document.getElementById('end_date');
    function updateDurationBanner() {
      if (!durationEl || !startDateEl || !endDateEl) return;
      var start = startDateEl.value ? new Date(startDateEl.value) : null;
      var end = endDateEl.value ? new Date(endDateEl.value) : null;
      if (!start || !end || end < start) return;
      var nights = Math.round((end - start) / (24 * 60 * 60 * 1000));
      if (nights < 0) nights = 0;
      durationEl.textContent = nights + ' Night' + (nights !== 1 ? 's' : '') + ' Trip';
    }
    if (startDateEl) startDateEl.addEventListener('change', updateDurationBanner);
    if (endDateEl) endDateEl.addEventListener('change', updateDurationBanner);
    updateDurationBanner();

    function reindexDestinations() {
      if (!destList) return;
      var blocks = destList.querySelectorAll('.trip-destination-block');
      blocks.forEach(function (block, i) {
        var idx = i + 1;
        block.setAttribute('data-index', idx);
        var sel = block.querySelector('select');
        var daysInp = block.querySelector('.trip-days-input');
        if (sel) sel.name = 'destinations[' + idx + '][district]';
        if (daysInp) daysInp.name = 'destinations[' + idx + '][days]';
        var removeBtn = block.querySelector('.btn-remove-dest');
        if (removeBtn) removeBtn.disabled = false;
      });
    }
    if (destList) {
      destList.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-remove-dest');
        if (!btn) return;
        var block = btn.closest('.trip-destination-block');
        if (block) { block.remove(); reindexDestinations(); }
      });
    }
    var addMoreBtn = document.getElementById('tripAddMoreBtn');
    var templateEl = document.getElementById('trip-destination-template');
    if (addMoreBtn && destList && templateEl) {
      addMoreBtn.addEventListener('click', function () {
        var clone = templateEl.content.cloneNode(true);
        var block = clone.querySelector('.trip-destination-block');
        var blocks = destList.querySelectorAll('.trip-destination-block');
        var idx = blocks.length + 1;
        block.setAttribute('data-index', idx);
        block.querySelector('select').name = 'destinations[' + idx + '][district]';
        block.querySelector('.trip-days-input').name = 'destinations[' + idx + '][days]';
        destList.appendChild(clone);
      });
    }

    document.querySelectorAll('.trip-toggle-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var opt = this.getAttribute('data-option');
        var val = this.getAttribute('data-value');
        var group = this.closest('.trip-option-group');
        if (!group) return;
        group.querySelectorAll('.trip-toggle-btn').forEach(function (b) { b.classList.remove('selected'); });
        this.classList.add('selected');
        var hid = document.getElementById(opt === 'transport' ? 'transport' : 'tour_guide');
        if (hid) hid.value = val;
      });
    });
  });
  </script>
</body>
</html>
