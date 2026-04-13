<?php
$user_name = isset($user_name) ? trim((string) $user_name) : 'Tourist';
$tourist_data = isset($tourist_data) ? $tourist_data : null;
$user_email_sidebar = '';
if (is_array($tourist_data) && isset($tourist_data['email'])) {
    $user_email_sidebar = trim((string) $tourist_data['email']);
} elseif (isset($_SESSION['user_email'])) {
    $user_email_sidebar = trim((string) $_SESSION['user_email']);
}
$avatar_initial = $user_name !== '' ? strtoupper(substr($user_name, 0, 1)) : 'T';
$asset_base = isset($asset_base) ? rtrim((string) $asset_base, '/') : '/CeylonGo/public';
$trip_sidebar_active = 'status';
$pending_trips = isset($pending_trips) && is_array($pending_trips) ? $pending_trips : array();

function booking_hub_fmt_date($iso) {
    $s = trim((string) $iso);
    if ($s === '') {
        return '—';
    }
    $ts = strtotime($s . (strpos($s, 'T') === false ? 'T12:00:00' : ''));
    if ($ts === false) {
        return $s;
    }
    return date('M j, Y', (int) $ts);
}

function booking_hub_party_label($n) {
    $n = (int) $n;
    if ($n <= 0) {
        return '—';
    }
    if ($n === 1) {
        return '1 person';
    }
    return $n . ' people';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Status of Bookings - Ceylon Go</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base, ENT_QUOTES, 'UTF-8'); ?>/css/common.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/navbar.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/trip_layout.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/sidebar.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/trip.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/booking_status_hub.css">
</head>
<body class="trip-page-body booking-status-hub-page">
  <?php include __DIR__ . '/header.php'; ?>

  <div class="sidebar-overlay trip-overlay" id="tripSidebarOverlay"></div>

  <div class="trip-page-wrapper">
    <?php include __DIR__ . '/_trip_sidebar.php'; ?>

    <main class="trip-main-content">
      <button type="button" class="hamburger-btn trip-hamburger" id="tripHamburgerBtn" aria-label="Toggle menu"><span></span><span></span><span></span></button>
      <div class="trip-breadcrumbs">
        <a href="<?php echo htmlspecialchars($asset_base, ENT_QUOTES, 'UTF-8'); ?>/tourist/dashboard-side"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span>&gt;</span>
        <span>status-of-bookings</span>
      </div>

      <div class="trip-header-row booking-hub-header" aria-label="Status of Bookings">
        <div class="trip-stepper-prev" aria-hidden="true"></div>
        <h1 class="trip-page-title trip-title-centered">
          <i class="fa-solid fa-clipboard-list" aria-hidden="true"></i> Status of Bookings
        </h1>
        <div class="trip-stepper-next" aria-hidden="true"></div>
      </div>

      <p class="booking-hub-lead">Submitted trips that still need payment or booking follow-up. Choose a trip to open its status and continue to payment.</p>

      <?php if (empty($pending_trips)): ?>
      <div class="booking-hub-empty">
        <p>You have no trips waiting for payment right now. Start or continue planning in Customise Your Trip.</p>
        <a class="booking-hub-btn booking-hub-btn--primary" href="<?php echo htmlspecialchars($asset_base, ENT_QUOTES, 'UTF-8'); ?>/tourist/customize-trip">Customise Your Trip</a>
      </div>
      <?php else: ?>
      <div class="booking-hub-grid" role="list">
        <?php foreach ($pending_trips as $t):
          $tid = (int) ($t['id'] ?? 0);
          $href = $asset_base . '/tourist/customize-trip?step=11&trip_id=' . $tid;
          $dest = isset($t['destination']) ? (string) $t['destination'] : '';
          $sd = isset($t['start_date']) ? (string) $t['start_date'] : '';
          $bud = isset($t['budget_lkr']) ? (float) $t['budget_lkr'] : 0.0;
          $np = isset($t['number_of_people']) ? (int) $t['number_of_people'] : 0;
          $bank = !empty($t['has_bank_pending']);
          $badge = $bank ? 'Bank transfer submitted' : 'Payment pending';
          $summary = $bank
            ? 'Awaiting payment confirmation (1–2 business days).'
            : 'Complete payment to confirm your trip.';
          ?>
        <a class="booking-hub-card trip-sum-budget-card" role="listitem" href="<?php echo htmlspecialchars($href, ENT_QUOTES, 'UTF-8'); ?>" aria-label="Open status for trip <?php echo (int) $tid; ?>, <?php echo htmlspecialchars($dest, ENT_QUOTES, 'UTF-8'); ?>">
          <div class="trip-sum-budget-card-head">Trip #<?php echo (int) $tid; ?></div>
          <ul class="trip-sum-budget-lines">
            <li>
              <span>Destination</span>
              <strong><?php echo htmlspecialchars($dest, ENT_QUOTES, 'UTF-8'); ?></strong>
            </li>
            <li>
              <span>Starts</span>
              <strong><?php echo htmlspecialchars(booking_hub_fmt_date($sd), ENT_QUOTES, 'UTF-8'); ?></strong>
            </li>
            <li>
              <span>Travel party</span>
              <strong><?php echo htmlspecialchars(booking_hub_party_label($np), ENT_QUOTES, 'UTF-8'); ?></strong>
            </li>
            <li>
              <span>Estimated budget</span>
              <strong><?php echo $bud > 0 ? ('LKR ' . number_format((int) round($bud))) : '—'; ?></strong>
            </li>
            <li>
              <span>Payment</span>
              <strong class="booking-hub-card__pay <?php echo $bank ? 'booking-hub-card__pay--bank' : ''; ?>"><?php echo htmlspecialchars($badge, ENT_QUOTES, 'UTF-8'); ?></strong>
            </li>
          </ul>
          <div class="trip-sum-budget-total trip-sum-booking-status-total booking-hub-card__summary">
            <span>Summary</span>
            <strong class="trip-sum-booking-status-total-value booking-hub-card__summary-val"><?php echo htmlspecialchars($summary, ENT_QUOTES, 'UTF-8'); ?></strong>
          </div>
          <p class="trip-sum-budget-footnote booking-hub-card__hint">Open this trip to review transport, hotels, and proceed to payment.</p>
          <div class="booking-hub-card__cta">
            <span class="booking-hub-card__cta-btn">Open trip status</span>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
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
    document.querySelectorAll('#tripSidebar ul li a').forEach(function (link) {
      link.addEventListener('click', function () {
        if (window.innerWidth <= 768) closeSidebar();
      });
    });
    window.addEventListener('resize', function () {
      if (window.innerWidth > 768) closeSidebar();
    });
  });
  </script>
</body>
</html>
