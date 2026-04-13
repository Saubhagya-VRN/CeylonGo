<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['user_id']) && (isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '') === 'tourist';
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Tourist';
$tourist_data = isset($tourist_data) ? $tourist_data : null;
$user_email = '';
if (is_array($tourist_data) && isset($tourist_data['email'])) {
    $user_email = $tourist_data['email'];
} elseif (isset($_SESSION['user_email'])) {
    $user_email = $_SESSION['user_email'];
}
$avatar_initial = $user_name ? strtoupper(substr(trim($user_name), 0, 1)) : 'T';

$stats = isset($dashboard_stats) && is_array($dashboard_stats) ? $dashboard_stats : array();
$stat_bookings = isset($stats['total_bookings']) ? (int) $stats['total_bookings'] : 0;
$stat_upcoming = isset($stats['upcoming_trips']) ? (int) $stats['upcoming_trips'] : 0;
$stat_spent = isset($stats['total_spent_lkr']) ? (float) $stats['total_spent_lkr'] : 0.0;
$stat_completed = isset($stats['completed_trips']) ? (int) $stats['completed_trips'] : 0;
$dashboard_bookings_arr = isset($dashboard_bookings) && is_array($dashboard_bookings) ? $dashboard_bookings : array();
$dashboard_bookings_upcoming_arr = isset($dashboard_bookings_upcoming) && is_array($dashboard_bookings_upcoming) ? $dashboard_bookings_upcoming : array();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Ceylon Go</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/common.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/navbar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/trip_layout.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/sidebar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/dashboard_sidebar.css">
</head>
<body class="trip-page-body dash-side-body">
  <?php include __DIR__ . '/header.php'; ?>

  <div class="sidebar-overlay trip-overlay" id="tripSidebarOverlay"></div>

  <div class="trip-page-wrapper">
    <aside class="trip-sidebar" id="tripSidebar">
      <div class="trip-sidebar-nav">
        <ul>
          <li class="active"><a href="/CeylonGo/public/tourist/dashboard-side"><i class="fa-solid fa-table-columns"></i> <span class="sidebar-link-text">Dashboard <span class="sidebar-sub">Overview & Stats</span></span></a></li>
          <li><a href="/CeylonGo/public/tourist/customize-trip"><i class="fa-solid fa-wand-magic-sparkles"></i> <span class="sidebar-link-text">Customise Your Trip <span class="sidebar-sub">Plan Custom Trips</span></span></a></li>
          <li id="tripSidebarNavStatusBookings"><a href="/CeylonGo/public/tourist/booking-status"><i class="fa-solid fa-clipboard-list"></i> <span class="sidebar-link-text">Status of Bookings <span class="sidebar-sub">Trip review &amp; submit</span></span></a></li>
          <li><a href="/CeylonGo/public/tourist/customize-trip?step=10"><i class="fa-solid fa-wallet"></i> <span class="sidebar-link-text">Budget Overview <span class="sidebar-sub">Costs &amp; itinerary</span></span></a></li>
          <li><a href="/CeylonGo/public/tourist/customize-trip?step=14"><i class="fa-solid fa-clipboard-check"></i> <span class="sidebar-link-text">Trip Overview <span class="sidebar-sub">Final confirmation</span></span></a></li>
          <li><a href="/CeylonGo/public/tourist/my-bookings?view=custom"><i class="fa-regular fa-calendar-check"></i> <span class="sidebar-link-text">Bookings <span class="sidebar-sub">Customised trips</span></span></a></li>
          <li><a href="/CeylonGo/public/tourist/payment"><i class="fa-solid fa-credit-card"></i> <span class="sidebar-link-text">Payments <span class="sidebar-sub">Invoices & Wallet</span></span></a></li>
          <li><a href="/CeylonGo/public/tourist/profile"><i class="fa-regular fa-user"></i> <span class="sidebar-link-text">Profile <span class="sidebar-sub">Account Settings</span></span></a></li>
        </ul>
      </div>
      <div class="trip-sidebar-footer">
        <div class="trip-sidebar-user">
          <div class="trip-sidebar-user-avatar"><?php echo htmlspecialchars($avatar_initial); ?></div>
          <div class="trip-sidebar-user-info">
            <div class="trip-sidebar-user-name"><?php echo htmlspecialchars($user_name ? $user_name : 'Tourist'); ?></div>
            <div class="trip-sidebar-user-email"><?php echo htmlspecialchars($user_email ? substr($user_email, 0, 20) . (strlen($user_email) > 20 ? '...' : '') : ''); ?></div>
          </div>
        </div>
        <a href="/CeylonGo/public/logout" class="trip-sidebar-signout"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</a>
      </div>
    </aside>

    <main class="trip-main-content dash-side-main">
      <button class="hamburger-btn trip-hamburger" id="tripHamburgerBtn" aria-label="Toggle menu"><span></span><span></span><span></span></button>

      <section class="dash-welcome">
        <div class="dash-welcome-inner">
          <h1 class="dash-welcome-title">Welcome back, <?php echo htmlspecialchars($user_name); ?>!</h1>
          <p class="dash-welcome-sub">Ready for your next adventure? Let’s explore Sri Lanka together!</p>
          <div class="dash-welcome-meta">
            <span class="dash-welcome-chip"><i class="fa-regular fa-clock"></i> <?php echo (int) $stat_upcoming; ?> upcoming trips</span>
            <span class="dash-welcome-chip"><i class="fa-solid fa-coins"></i> LKR <?php echo htmlspecialchars(number_format($stat_spent, 0)); ?> spent</span>
            <span class="dash-welcome-chip"><i class="fa-regular fa-star"></i> <?php echo (int) $stat_completed; ?> trips completed</span>
          </div>
        </div>
      </section>

      <section class="dash-stats" aria-label="Dashboard stats">
        <div class="dash-stats-grid">
          <div class="dash-stat-card dash-stat-card--bookings" id="dashTotalBookingsCard" role="button" tabindex="0" aria-expanded="false" aria-controls="dashBookingsDetail">
            <div class="dash-stat-icon dash-stat-icon--blue"><i class="fa-solid fa-paper-plane"></i></div>
            <div class="dash-stat-value"><?php echo (int) $stat_bookings; ?></div>
            <div class="dash-stat-label">Total Bookings</div>
            <div class="dash-stat-sub"><?php echo (int) $stat_completed; ?> completed</div>
          </div>
          <div class="dash-stat-card dash-stat-card--upcoming" id="dashUpcomingTripsCard" role="button" tabindex="0" aria-expanded="false" aria-controls="dashUpcomingDetail">
            <div class="dash-stat-icon dash-stat-icon--green"><i class="fa-regular fa-calendar"></i></div>
            <div class="dash-stat-value"><?php echo (int) $stat_upcoming; ?></div>
            <div class="dash-stat-label">Upcoming Trips</div>
            <div class="dash-stat-sub">Next adventure awaits</div>
          </div>
          <div class="dash-stat-card">
            <div class="dash-stat-icon dash-stat-icon--purple"><i class="fa-solid fa-chart-line"></i></div>
            <div class="dash-stat-value">LKR <?php echo htmlspecialchars(number_format($stat_spent, 0)); ?></div>
            <div class="dash-stat-label">Total Spent</div>
            <div class="dash-stat-sub">On amazing trips</div>
          </div>
        </div>
      </section>

      <section id="dashBookingsDetail" class="dash-bookings-detail" hidden aria-label="Booking details">
        <div class="dash-bookings-detail-inner">
          <h2 class="dash-bookings-detail-title">Your bookings</h2>
          <p class="dash-bookings-detail-hint">Paid or confirmed customised trips (same count as Total Bookings).</p>
          <div id="dashBookingsDetailBody" class="dash-bookings-detail-body"></div>
          <p class="dash-bookings-detail-footer"><a href="/CeylonGo/public/tourist/my-bookings?view=custom">Open full My Bookings</a></p>
        </div>
      </section>

      <section id="dashUpcomingDetail" class="dash-bookings-detail" hidden aria-label="Upcoming trips">
        <div class="dash-bookings-detail-inner">
          <h2 class="dash-bookings-detail-title">Upcoming trips</h2>
          <p class="dash-bookings-detail-hint">Trips that have not finished yet (same count as Upcoming Trips).</p>
          <div id="dashUpcomingDetailBody" class="dash-bookings-detail-body"></div>
          <p class="dash-bookings-detail-footer"><a href="/CeylonGo/public/tourist/my-bookings?view=custom">Open full My Bookings</a></p>
        </div>
      </section>
    </main>
  </div>

  <script>
  window.__DASHBOARD_BOOKINGS__ = <?php echo json_encode($dashboard_bookings_arr, JSON_UNESCAPED_UNICODE); ?>;
  window.__DASHBOARD_UPCOMING__ = <?php echo json_encode($dashboard_bookings_upcoming_arr, JSON_UNESCAPED_UNICODE); ?>;
  </script>
  <script>
  document.addEventListener('DOMContentLoaded', function () {
    var hamburger = document.getElementById('tripHamburgerBtn');
    var sidebar = document.getElementById('tripSidebar');
    var overlay = document.getElementById('tripSidebarOverlay');
    function toggleSidebar() {
      if (hamburger) hamburger.classList.toggle('active');
      if (sidebar) sidebar.classList.toggle('active');
      if (overlay) overlay.classList.toggle('active');
    }
    if (hamburger) hamburger.addEventListener('click', toggleSidebar);
    if (overlay) overlay.addEventListener('click', function () {
      if (sidebar) sidebar.classList.remove('active');
      if (overlay) overlay.classList.remove('active');
      if (hamburger) hamburger.classList.remove('active');
    });

    var bookingsCard = document.getElementById('dashTotalBookingsCard');
    var bookingsPanel = document.getElementById('dashBookingsDetail');
    var bookingsBody = document.getElementById('dashBookingsDetailBody');
    var list = window.__DASHBOARD_BOOKINGS__ || [];
    var upcomingCard = document.getElementById('dashUpcomingTripsCard');
    var upcomingPanel = document.getElementById('dashUpcomingDetail');
    var upcomingBody = document.getElementById('dashUpcomingDetailBody');
    var upcomingList = window.__DASHBOARD_UPCOMING__ || [];

    function esc(s) {
      if (s === null || s === undefined) return '';
      return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function fmtShortDate(ymd) {
      if (!ymd) return '—';
      var p = String(ymd).split('-');
      if (p.length !== 3) return String(ymd);
      var dt = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
      if (isNaN(dt.getTime())) return String(ymd);
      return dt.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
    }
    function asideSecondaryLine(b) {
      if (b.phase === 'ended') return 'Trip ended';
      if (b.phase !== 'upcoming' || !b.start_date) return 'Upcoming';
      var p = String(b.start_date).split('-');
      if (p.length !== 3) return 'Upcoming';
      var start = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
      if (isNaN(start.getTime())) return 'Upcoming';
      var today = new Date();
      today.setHours(0, 0, 0, 0);
      start.setHours(0, 0, 0, 0);
      var diff = Math.round((start - today) / 86400000);
      if (diff > 0) return diff + ' day' + (diff === 1 ? '' : 's') + ' to go';
      if (diff === 0) return 'Starts today';
      return 'In progress';
    }
    function renderBookingsCardsInto(bodyEl, tripList, idPrefix, emptyMessage) {
      if (!bodyEl) return;
      emptyMessage = emptyMessage || 'No trips to show.';
      if (!tripList.length) {
        bodyEl.innerHTML = '<p class="dash-bookings-empty">' + esc(emptyMessage) + '</p>';
        return;
      }
      var cards = tripList.map(function (b) {
        var variant = (b.badge_variant || 'paid');
        var badgeClass = 'dash-booking-card__tag dash-booking-card__tag--' + variant;
        var note = (b.note_message && String(b.note_message).trim() !== '')
          ? '<p class="dash-booking-card__note">' + esc(b.note_message) + '</p>'
          : '';
        var dest = (b.destination && String(b.destination).trim() !== '') ? b.destination : 'Your trip';
        var nDays = typeof b.number_of_days === 'number' && b.number_of_days > 0 ? b.number_of_days : 0;
        var nTv = typeof b.travelers === 'number' && b.travelers > 0 ? b.travelers : 0;
        var tvPart = nTv > 0 ? (nTv + ' traveler' + (nTv === 1 ? '' : 's')) : '—';
        var locDuration = nDays > 0
          ? (esc(dest) + ' · ' + nDays + ' day' + (nDays === 1 ? '' : 's'))
          : esc(dest);
        var metaBits = ['Trip #' + b.id, tvPart];
        if (b.total_line) metaBits.push(String(b.total_line));
        var metaLine = esc(metaBits.join(' · '));
        var contact = (b.contact_line && String(b.contact_line).trim() !== '')
          ? '<p class="dash-booking-card__contact">' + esc(b.contact_line) + '</p>'
          : '';
        var bid = esc(String(b.id));
        var detId = idPrefix + '-detail-' + bid;
        var trigId = idPrefix + '-trigger-' + bid;
        return '<article class="dash-booking-card" role="article" data-trip-id="' + bid + '">'
          + '<button type="button" class="dash-booking-card__header" aria-expanded="false" aria-controls="' + esc(detId) + '" id="' + esc(trigId) + '">'
          + '<div class="dash-booking-card__header-main">'
          + '<span class="dash-booking-card__title">' + esc(dest) + '</span>'
          + '<span class="dash-booking-card__countdown">' + esc(asideSecondaryLine(b)) + '</span>'
          + '<span class="' + badgeClass + '">' + esc(b.badge_text || '') + '</span>'
          + '</div>'
          + '<div class="dash-booking-card__aside">'
          + '<div class="dash-booking-card__aside-primary">' + esc(fmtShortDate(b.start_date)) + '</div>'
          + '</div>'
          + '<span class="dash-booking-card__chev" aria-hidden="true"><i class="fa-solid fa-chevron-down"></i></span>'
          + '</button>'
          + '<div class="dash-booking-card__detail" id="' + esc(detId) + '" role="region" aria-labelledby="' + esc(trigId) + '" hidden>'
          + '<p class="dash-booking-card__subtitle">' + locDuration + '</p>'
          + '<p class="dash-booking-card__meta-line">' + metaLine + '</p>'
          + contact
          + note
          + '<div class="dash-booking-card__foot">'
          + '<a class="dash-booking-card__link" href="' + esc(b.overview_url || '#') + '">View trip summary</a>'
          + '</div>'
          + '</div>'
          + '</article>';
      }).join('');
      bodyEl.innerHTML = '<div class="dash-booking-cards">' + cards + '</div>';
      bindBookingAccordionsIn(bodyEl);
    }
    function renderBookingsCards() {
      renderBookingsCardsInto(
        bookingsBody,
        list,
        'db-all',
        'No qualifying trips found. Complete payment or submit bank transfer for a customised trip to see it here.'
      );
    }
    function renderUpcomingCards() {
      renderBookingsCardsInto(
        upcomingBody,
        upcomingList,
        'db-up',
        'No upcoming trips in this list. When your paid trip window is still in the future, it appears here.'
      );
    }
    function bindBookingAccordionsIn(rootEl) {
      if (!rootEl) return;
      var headers = rootEl.querySelectorAll('.dash-booking-card__header');
      for (var i = 0; i < headers.length; i++) {
        var btn = headers[i];
        if (btn.dataset.bound === '1') continue;
        btn.dataset.bound = '1';
        btn.addEventListener('click', function () {
          var card = this.closest('.dash-booking-card');
          var detail = card ? card.querySelector('.dash-booking-card__detail') : null;
          if (!detail) return;
          var expanded = this.getAttribute('aria-expanded') === 'true';
          if (expanded) {
            this.setAttribute('aria-expanded', 'false');
            detail.setAttribute('hidden', '');
            if (card) card.classList.remove('dash-booking-card--open');
          } else {
            this.setAttribute('aria-expanded', 'true');
            detail.removeAttribute('hidden');
            if (card) card.classList.add('dash-booking-card--open');
          }
        });
      }
    }
    function closeUpcomingPanel() {
      if (upcomingPanel) upcomingPanel.setAttribute('hidden', '');
      if (upcomingCard) {
        upcomingCard.setAttribute('aria-expanded', 'false');
        upcomingCard.classList.remove('dash-stat-card--open');
      }
    }
    function closeBookingsPanel() {
      if (bookingsPanel) bookingsPanel.setAttribute('hidden', '');
      if (bookingsCard) {
        bookingsCard.setAttribute('aria-expanded', 'false');
        bookingsCard.classList.remove('dash-stat-card--open');
      }
    }
    function toggleBookingsPanel() {
      if (!bookingsPanel || !bookingsCard) return;
      var willOpen = bookingsPanel.hasAttribute('hidden');
      if (willOpen) {
        closeUpcomingPanel();
        bookingsPanel.removeAttribute('hidden');
        bookingsCard.setAttribute('aria-expanded', 'true');
        bookingsCard.classList.add('dash-stat-card--open');
        renderBookingsCards();
      } else {
        bookingsPanel.setAttribute('hidden', '');
        bookingsCard.setAttribute('aria-expanded', 'false');
        bookingsCard.classList.remove('dash-stat-card--open');
      }
    }
    function toggleUpcomingPanel() {
      if (!upcomingPanel || !upcomingCard) return;
      var willOpen = upcomingPanel.hasAttribute('hidden');
      if (willOpen) {
        closeBookingsPanel();
        upcomingPanel.removeAttribute('hidden');
        upcomingCard.setAttribute('aria-expanded', 'true');
        upcomingCard.classList.add('dash-stat-card--open');
        renderUpcomingCards();
      } else {
        upcomingPanel.setAttribute('hidden', '');
        upcomingCard.setAttribute('aria-expanded', 'false');
        upcomingCard.classList.remove('dash-stat-card--open');
      }
    }
    if (bookingsCard && bookingsPanel) {
      bookingsCard.addEventListener('click', function (e) {
        e.preventDefault();
        toggleBookingsPanel();
      });
      bookingsCard.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          toggleBookingsPanel();
        }
      });
    }
    if (upcomingCard && upcomingPanel) {
      upcomingCard.addEventListener('click', function (e) {
        e.preventDefault();
        toggleUpcomingPanel();
      });
      upcomingCard.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          toggleUpcomingPanel();
        }
      });
    }
  });
  </script>
</body>
</html>
