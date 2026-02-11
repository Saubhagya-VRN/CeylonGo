<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$p = $package ?? null;
if (!$p) {
    header('Location: /CeylonGo/public/tourist/packages');
    exit;
}
$itinerary = $p['itinerary'] ?? [];
$fullname = $fullname ?? '';
$email = $email ?? '';
$phone = $phone ?? '';
$booking_error = $error ?? '';
$booking_success = !empty($success);
$asset_base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($asset_base === '' || $asset_base === '/') {
    $asset_base = '/CeylonGo/public';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Your Trip - <?php echo htmlspecialchars($p['title']); ?> - Ceylon Go</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/common.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/navbar.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/footer.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/booking_form.css">
</head>
<body class="booking-page">
  <?php include __DIR__ . '/header.php'; ?>

  <main class="booking-main">
    <h1 class="booking-page-title">Book Your Trip</h1>
    <p class="booking-page-sub">Fill in your details and our travel expert will contact you shortly.</p>

    <div class="booking-layout">
      <!-- Left: Trip Summary (expanded Detailed Itinerary) -->
      <section class="booking-trip-summary">
        <h2 class="booking-summary-title">Trip Summary</h2>
        <div class="booking-package-head">
          <p class="booking-package-name"><?php echo htmlspecialchars($p['title']); ?></p>
          <p class="booking-package-meta"><?php echo htmlspecialchars($p['duration_short'] ?? $p['duration']); ?> · <?php echo isset($p['price']) ? 'LKR ' . number_format((int)$p['price']) : 'Price on request'; ?></p>
        </div>
        <?php
        $acc_list = $p['accommodation'] ?? [];
        if (!empty($acc_list)):
          $day_start = 1;
        ?>
        <h3 class="booking-itinerary-heading">Accommodation</h3>
        <ul class="booking-accommodation-list">
          <?php foreach ($acc_list as $acc):
            $nights = (int)($acc['nights'] ?? 1);
            $day_end = $day_start + $nights - 1;
            $day_range = ($day_start === $day_end) ? 'Day ' . $day_start : 'Day ' . $day_start . ' to Day ' . $day_end;
            $day_start = $day_end + 1;
            $hotel = $acc['hotel'] ?? 'Accommodation';
            $loc = $acc['location'] ?? '';
          ?>
          <li class="booking-accommodation-item">
            <strong><?php echo htmlspecialchars($hotel); ?></strong><?php if ($loc !== ''): ?> <span class="booking-acc-location">(<?php echo htmlspecialchars($loc); ?>)</span><?php endif; ?> — <?php echo $day_range; ?>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <h3 class="booking-itinerary-heading">Detailed Itinerary</h3>
        <div class="booking-itinerary-days">
          <?php foreach ($itinerary as $day): ?>
          <div class="booking-day-card" data-day="<?php echo (int)$day['day']; ?>">
            <div class="booking-day-header">
              <span class="booking-day-num"><?php echo (int)$day['day']; ?></span>
              <div class="booking-day-title-wrap">
                <h4 class="booking-day-title">Day <?php echo (int)$day['day']; ?>: <?php echo htmlspecialchars($day['title'] ?? ''); ?></h4>
              </div>
            </div>
            <div class="booking-day-body">
              <ul class="booking-day-activities">
                <?php foreach ($day['activities'] ?? [] as $act): ?>
                <li><?php echo htmlspecialchars($act); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- Right: Booking form -->
      <section class="booking-form-section">
        <h2 class="booking-form-title">Booking Information</h2>
        <?php if ($booking_error): ?>
        <div class="alert alert-error booking-form-error"><?php echo htmlspecialchars($booking_error); ?></div>
        <?php endif; ?>
        <form class="booking-form" method="POST" action="">
          <input type="hidden" name="package_id" value="<?php echo (int)$p['id']; ?>">
          <input type="hidden" name="package_name" value="<?php echo htmlspecialchars($p['title']); ?>">

          <div class="form-group">
            <label>Package Selected</label>
            <input type="text" class="booking-package-selected" value="<?php echo htmlspecialchars($p['title']); ?>" readonly>
          </div>
          <div class="form-group">
            <label for="fullname">Full Name <span class="required">*</span></label>
            <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" placeholder="Enter your full name" required>
          </div>
          <div class="form-group">
            <label for="email">Email Address <span class="required">*</span></label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="your.email@example.com" required>
          </div>
          <div class="form-group">
            <label for="phone">Phone Number <span class="required">*</span></label>
            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" placeholder="e.g. +94 77 123 4567" required>
          </div>
          <div class="form-group" id="travelers-wrap" data-price-per-person="<?php echo (int)($p['price'] ?? 0); ?>">
            <label for="travelers">Number of Travelers <span class="required">*</span></label>
            <select id="travelers" name="travelers" required>
              <option value="">Select</option>
              <?php for ($i = 1; $i <= 10; $i++): ?>
              <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $i === 1 ? 'Traveler' : 'Travelers'; ?></option>
              <?php endfor; ?>
            </select>
            </div>
          <div class="form-group">
            <label for="travel-date">Preferred Travel Date <span class="required">*</span></label>
            <input type="date" id="travel-date" name="travel_date" min="<?php echo date('Y-m-d', strtotime('+21 days')); ?>" required>
            <p class="booking-date-hint">Select a date at least 3 weeks from today.</p>
          </div>
          <div class="form-group">
            <label for="special-requests">Special Requests</label>
            <textarea id="special-requests" name="special_requests" rows="4" placeholder="Any special requirements or preferences..."></textarea>
          </div>
          <div class="booking-form-actions">
            <a href="javascript:history.back()" class="btn-outline">Cancel</a>
            <button type="submit" class="btn-primary">Submit Booking Request</button>
            <span class="booking-total-next-to-buttons" id="booking-total-buttons">LKR 0</span>
          </div>
          <?php if ($booking_success): ?>
          <p class="booking-success-msg">Booking successful. We will contact you within 24 hrs.</p>
          <?php endif; ?>
        </form>
      </section>
    </div>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>
  <script>
    (function() {
      var form = document.querySelector('.booking-form');
      var dateInput = document.getElementById('travel-date');
      if (!form || !dateInput) return;
      var minDate = dateInput.getAttribute('min');
      form.addEventListener('submit', function(e) {
        var val = dateInput.value;
        if (!val) return;
        if (minDate && val < minDate) {
          e.preventDefault();
          alert('Preferred Travel Date must be at least 3 weeks from today.');
          dateInput.focus();
          return false;
        }
      });
    })();
    (function() {
      var wrap = document.getElementById('travelers-wrap');
      var travelersSelect = document.getElementById('travelers');
      var totalByButtons = document.getElementById('booking-total-buttons');
      if (!wrap || !travelersSelect) return;
      var pricePerPerson = parseInt(wrap.getAttribute('data-price-per-person') || '0', 10);
      function formatNum(n) {
        return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
      }
      function updateTotal() {
        var n = parseInt(travelersSelect.value || '0', 10);
        var total = n * pricePerPerson;
        var text = 'LKR ' + formatNum(total);
        if (totalByButtons) totalByButtons.textContent = text;
      }
      travelersSelect.addEventListener('change', updateTotal);
      updateTotal();
    })();
  </script>
</body>
</html>
