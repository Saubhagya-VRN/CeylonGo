<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$bookings = isset($bookings) ? $bookings : array();
$custom_trips = isset($custom_trips) ? $custom_trips : array();
$tourist_email = isset($tourist_email) ? (string) $tourist_email : '';
$bookings_custom_only = !empty($bookings_custom_only);
$scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
$asset_base = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
if ($asset_base === '' || $asset_base === '/') {
    $asset_base = '/CeylonGo/public';
}
$user_name = isset($_SESSION['user_name']) ? trim((string) $_SESSION['user_name']) : 'Tourist';
$user_email_sidebar = $tourist_email !== '' ? $tourist_email : (isset($_SESSION['user_email']) ? trim((string) $_SESSION['user_email']) : '');
$avatar_initial = $user_name !== '' ? strtoupper(substr($user_name, 0, 1)) : 'T';
$trip_sidebar_active = 'bookings';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $bookings_custom_only ? 'Customised trips — Bookings - Ceylon Go' : 'My Bookings - Ceylon Go'; ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/common.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/navbar.css">
  <?php if ($bookings_custom_only): ?>
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/trip_layout.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/sidebar.css">
  <?php endif; ?>
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/footer.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/my_bookings.css">
  <?php if ($bookings_custom_only): ?>
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/trip.css">
  <?php endif; ?>
</head>
<body class="<?php echo $bookings_custom_only ? 'trip-page-body ' : ''; ?>my-bookings-page">
  <?php include __DIR__ . '/header.php'; ?>

  <?php if ($bookings_custom_only): ?>
  <div class="sidebar-overlay trip-overlay" id="tripSidebarOverlay"></div>

  <div class="trip-page-wrapper">
    <?php include __DIR__ . '/_trip_sidebar.php'; ?>

    <main class="trip-main-content">
      <button type="button" class="hamburger-btn trip-hamburger" id="tripHamburgerBtn" aria-label="Toggle menu"><span></span><span></span><span></span></button>
      <div class="trip-breadcrumbs">
        <a href="<?php echo htmlspecialchars($asset_base); ?>/tourist/dashboard-side"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span>&gt;</span>
        <span>my-bookings</span>
      </div>

      <div class="trip-header-row my-bookings-custom-header" aria-label="Bookings">
        <div class="trip-stepper-prev" aria-hidden="true"></div>
        <h1 class="trip-page-title trip-title-centered">
          <i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i> Bookings
        </h1>
        <div class="trip-stepper-next" aria-hidden="true"></div>
      </div>

  <?php endif; ?>

  <div class="my-bookings-main">
    <?php if (!$bookings_custom_only): ?>
    <h1 class="my-bookings-title">My Bookings</h1>
    <p class="my-bookings-intro">Your booking requests. We will contact you within 24 hrs.</p>
    <?php endif; ?>

    <?php
    $payment_message = isset($payment_message) ? $payment_message : null;
    $payment_error = isset($payment_error) ? $payment_error : null;
    $payment_info = isset($payment_info) ? $payment_info : null;
    ?>
    <?php if (!empty($payment_message)): ?>
    <div class="my-bookings-flash my-bookings-flash--ok"><?php echo htmlspecialchars($payment_message); ?></div>
    <?php endif; ?>
    <?php if (!empty($payment_error)): ?>
    <div class="my-bookings-flash my-bookings-flash--err"><?php echo htmlspecialchars($payment_error); ?></div>
    <?php endif; ?>
    <?php if (!empty($payment_info)): ?>
    <div class="my-bookings-flash my-bookings-flash--info"><?php echo htmlspecialchars($payment_info); ?></div>
    <?php endif; ?>

    <?php
    $has_any_listing = $bookings_custom_only ? !empty($custom_trips) : !empty($bookings);
    ?>
    <?php if (!$has_any_listing): ?>
      <div class="my-bookings-empty">
        <?php if ($bookings_custom_only): ?>
        <p>You have no customised trips here yet. Complete a trip in Customise Your Trip and pay (or submit a bank transfer) to see it listed.</p>
        <a href="<?php echo htmlspecialchars($asset_base); ?>/tourist/customize-trip" class="btn-primary-pkg">Customise Your Trip</a>
        <?php else: ?>
        <p>You have no bookings yet.</p>
        <a href="<?php echo htmlspecialchars($asset_base); ?>/tourist/packages" class="btn-primary-pkg">Browse Packages</a>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <?php if ($bookings_custom_only && !empty($custom_trips)): ?>
      <div class="my-bookings-list my-bookings-list--custom">
        <?php foreach ($custom_trips as $ct):
          $ctid = (int) (isset($ct['id']) ? $ct['id'] : 0);
          $overview_href = htmlspecialchars($asset_base) . '/tourist/customize-trip?step=14&trip_id=' . urlencode((string) $ctid);
          $badge_submitted = !empty($ct['is_bank_submitted']);
          $badge_text = $badge_submitted ? 'Payment submitted' : 'Completed';
          $travelers = (int) (isset($ct['travelers']) ? $ct['travelers'] : 0);
          $budget = (float) (isset($ct['budget_lkr']) ? $ct['budget_lkr'] : 0);
          $total_line = $budget > 0 ? ('LKR ' . number_format((int) round($budget))) : 'LKR —';
          $cust = trim((string) (isset($ct['customer_name']) ? $ct['customer_name'] : ''));
          $em = trim($tourist_email);
          $contact_line = ($cust === '' && $em === '') ? '' : ($cust . ($em !== '' ? ' · ' . $em : ''));
          $refund_requested = !empty($ct['refund_requested']);
          $refund_eligible = !empty($ct['refund_eligible']) && !$badge_submitted;
          $paid_raw = (string) (isset($ct['paid_at_raw']) ? $ct['paid_at_raw'] : '');
          $paid_label = '';
          if ($paid_raw !== '') {
            $pts = strtotime($paid_raw);
            $paid_label = $pts !== false ? date('F j, Y \a\t g:i A', $pts) : $paid_raw;
          }
          $deadline_label = '';
          if (!empty($ct['refund_deadline_ts'])) {
            $deadline_label = date('F j, Y \a\t g:i A', (int) $ct['refund_deadline_ts']);
          }
          $total_lkr_int = (int) round($budget);
          $refund_rr = trim((string) (isset($ct['refund_requested_at']) ? $ct['refund_requested_at'] : ''));
        ?>
        <div class="my-booking-card" role="region" aria-label="Custom trip booking">
          <div class="my-booking-header">
            <?php if ($badge_submitted): ?>
            <span class="my-booking-status my-booking-status--awaiting-bank"><?php echo htmlspecialchars($badge_text); ?></span>
            <?php else: ?>
            <span class="my-booking-status my-booking-status--paid"><?php echo htmlspecialchars($badge_text); ?></span>
            <?php endif; ?>
            <span class="my-booking-date"><?php echo htmlspecialchars(isset($ct['date_label']) ? $ct['date_label'] : '—'); ?></span>
          </div>
          <h2 class="my-booking-package"><?php echo htmlspecialchars($ct['destination'] ?: 'Your trip'); ?></h2>
          <?php if ($badge_submitted): ?>
          <ul class="my-booking-details">
            <li><strong>Trip No:</strong> <?php echo $ctid > 0 ? (int) $ctid : '—'; ?></li>
            <li><strong>Travelers:</strong> <?php echo $travelers > 0 ? $travelers : '—'; ?></li>
            <li><strong>Total:</strong> <?php echo htmlspecialchars($total_line); ?></li>
            <li><strong>Contact:</strong> <?php echo htmlspecialchars($contact_line !== '' ? $contact_line : '—'); ?></li>
          </ul>
          <p class="my-booking-note">We have recorded your bank transfer. Your booking stays approved while we verify the payment (usually within 1–2 business days).</p>
          <div class="my-booking-custom-actions my-booking-custom-actions--row">
            <button type="button" class="my-booking-btn-provider js-custom-trip-provider-open" data-trip-id="<?php echo (int) $ctid; ?>">Service provider details</button>
            <button type="button" class="my-booking-btn-trip-summary js-custom-trip-summary-open" data-trip-id="<?php echo (int) $ctid; ?>">View trip summary</button>
          </div>
          <?php elseif ($refund_requested): ?>
          <ul class="my-booking-details my-booking-details--paid-follow">
            <li><strong>Trip No:</strong> <?php echo $ctid > 0 ? (int) $ctid : '—'; ?></li>
            <li><strong>Travelers:</strong> <?php echo $travelers > 0 ? $travelers : '—'; ?></li>
            <li><strong>Total:</strong> <?php echo htmlspecialchars($total_line); ?></li>
          </ul>
          <div class="my-booking-paid-footer">
            <p class="my-booking-refund-status--above">Refund request submitted<?php
              if ($refund_rr !== '' && strtotime($refund_rr) !== false): ?> on <?php echo htmlspecialchars(date('F j, Y \a\t g:i A', strtotime($refund_rr))); ?><?php endif; ?>. We will email you about the next steps.</p>
            <div class="my-booking-paid-grid my-booking-paid-grid--custom">
              <div class="my-booking-cell my-booking-cell--contact">
                <ul class="my-booking-details my-booking-details--contact-only">
                  <li><strong>Contact:</strong> <?php echo htmlspecialchars($contact_line !== '' ? $contact_line : '—'); ?></li>
                </ul>
                <p class="my-booking-note my-booking-paymsg-line">Payment complete. Thank you for choosing Ceylon Go.</p>
              </div>
              <div class="my-booking-cell my-booking-cell--refund">
              </div>
              <div class="my-booking-cell my-booking-cell--provider">
                <button type="button" class="my-booking-btn-provider js-custom-trip-provider-open" data-trip-id="<?php echo (int) $ctid; ?>">Service provider details</button>
              </div>
              <div class="my-booking-cell my-booking-cell--trip">
                <button type="button" class="my-booking-btn-trip-summary js-custom-trip-summary-open" data-trip-id="<?php echo (int) $ctid; ?>">View trip summary</button>
              </div>
            </div>
          </div>
          <?php else: ?>
          <ul class="my-booking-details my-booking-details--paid-follow">
            <li><strong>Trip No:</strong> <?php echo $ctid > 0 ? (int) $ctid : '—'; ?></li>
            <li><strong>Travelers:</strong> <?php echo $travelers > 0 ? $travelers : '—'; ?></li>
            <li><strong>Total:</strong> <?php echo htmlspecialchars($total_line); ?></li>
          </ul>
          <div class="my-booking-paid-footer">
            <div class="my-booking-paid-grid my-booking-paid-grid--custom">
              <div class="my-booking-cell my-booking-cell--contact">
                <ul class="my-booking-details my-booking-details--contact-only">
                  <li><strong>Contact:</strong> <?php echo htmlspecialchars($contact_line !== '' ? $contact_line : '—'); ?></li>
                </ul>
                <p class="my-booking-note my-booking-paymsg-line">Payment complete. Thank you for choosing Ceylon Go.</p>
              </div>
              <div class="my-booking-cell my-booking-cell--refund">
                <?php if ($refund_eligible && $paid_label !== ''): ?>
                <button type="button" class="my-booking-btn-refund js-custom-trip-refund-open"
                  data-trip-id="<?php echo (int) $ctid; ?>"
                  data-paid-label="<?php echo htmlspecialchars($paid_label, ENT_QUOTES, 'UTF-8'); ?>"
                  data-deadline-label="<?php echo htmlspecialchars($deadline_label, ENT_QUOTES, 'UTF-8'); ?>"
                  data-total-lkr="<?php echo (int) $total_lkr_int; ?>"
                >Request refund</button>
                <?php endif; ?>
              </div>
              <div class="my-booking-cell my-booking-cell--provider">
                <button type="button" class="my-booking-btn-provider js-custom-trip-provider-open" data-trip-id="<?php echo (int) $ctid; ?>">Service provider details</button>
              </div>
              <div class="my-booking-cell my-booking-cell--trip">
                <button type="button" class="my-booking-btn-trip-summary js-custom-trip-summary-open" data-trip-id="<?php echo (int) $ctid; ?>">View trip summary</button>
              </div>
            </div>
            <?php if ($paid_raw !== '' && !$refund_eligible): ?>
            <p class="my-booking-refund-note--below">Refunds can only be requested within 3 days of payment. This booking is outside that window.</p>
            <?php elseif ($paid_raw === ''): ?>
            <p class="my-booking-refund-note--below">Refund requests need a confirmed payment time on file. Please contact us with your trip number.</p>
            <?php endif; ?>
          </div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if (!$bookings_custom_only && !empty($bookings)): ?>
      <div class="my-bookings-list">
        <?php foreach (array_reverse($bookings) as $b):
          $is_paid = (isset($b['status']) && $b['status'] === 'paid');
          $is_approved = (isset($b['status']) && $b['status'] === 'approved');
          $bank_transfer_waiting = $is_approved && !empty($b['bank_transfer_submitted_at']);
          $bid = isset($b['id']) ? $b['id'] : '';
        ?>
        <div class="my-booking-card">
          <div class="my-booking-header">
            <?php if ($is_paid): ?>
            <span class="my-booking-status my-booking-status--paid">Completed</span>
            <?php elseif ($bank_transfer_waiting): ?>
            <span class="my-booking-status my-booking-status--awaiting-bank">Payment submitted</span>
            <?php elseif ($is_approved): ?>
            <span class="my-booking-status my-booking-status--approved">Approved</span>
            <?php else: ?>
            <span class="my-booking-status my-booking-status--pending">Pending</span>
            <?php endif; ?>
            <span class="my-booking-date"><?php echo htmlspecialchars(isset($b['travel_date']) ? $b['travel_date'] : '-'); ?></span>
          </div>
          <h2 class="my-booking-package"><?php echo htmlspecialchars(isset($b['package_name']) ? $b['package_name'] : 'Package'); ?></h2>
          <ul class="my-booking-details<?php echo $is_paid ? ' my-booking-details--paid-follow' : ''; ?>">
            <li><strong>Travelers:</strong> <?php echo (int)(isset($b['travelers']) ? $b['travelers'] : 0); ?><?php if (isset($b['adults']) || isset($b['children']) || isset($b['infants'])): ?> (<?php echo (int)(isset($b['adults']) ? $b['adults'] : 0); ?> adult<?php echo ((int)(isset($b['adults']) ? $b['adults'] : 0)) !== 1 ? 's' : ''; ?><?php if (!empty($b['children'])): ?>, <?php echo (int)$b['children']; ?> child<?php echo (int)$b['children'] !== 1 ? 'ren' : ''; ?><?php endif; ?><?php if (!empty($b['infants'])): ?>, <?php echo (int)$b['infants']; ?> infant<?php echo (int)$b['infants'] !== 1 ? 's' : ''; ?><?php endif; ?>)<?php endif; ?></li>
            <li><strong>Total:</strong> LKR <?php echo number_format((int)(isset($b['total_amount']) ? $b['total_amount'] : 0)); ?></li>
            <?php if (!$is_paid): ?>
            <li><strong>Contact:</strong> <?php echo htmlspecialchars(isset($b['fullname']) ? $b['fullname'] : ''); ?> · <?php echo htmlspecialchars(isset($b['email']) ? $b['email'] : ''); ?></li>
            <?php endif; ?>
            <?php if (!empty($b['special_requests'])): ?>
            <li><strong>Requests:</strong> <?php echo htmlspecialchars($b['special_requests']); ?></li>
            <?php endif; ?>
          </ul>
          <?php if ($is_paid):
            $paid_at_raw = isset($b['paid_at']) ? $b['paid_at'] : null;
            $refund_requested = !empty($b['refund_requested_at']);
            $refund_eligible = false;
            $refund_deadline_ts = null;
            if ($paid_at_raw && !$refund_requested) {
              $pt = strtotime($paid_at_raw);
              if ($pt !== false) {
                $refund_deadline_ts = $pt + (3 * 86400);
                $refund_eligible = time() <= $refund_deadline_ts;
              }
            }
          ?>
          <div class="my-booking-paid-footer">
            <?php if ($refund_requested): ?>
            <p class="my-booking-refund-status--above">Refund request submitted<?php
              $rr = isset($b['refund_requested_at']) ? $b['refund_requested_at'] : '';
              if ($rr !== ''): ?> on <?php echo htmlspecialchars(date('F j, Y \a\t g:i A', strtotime($rr))); ?><?php endif; ?>. We will email you about the next steps.</p>
            <?php endif; ?>
            <div class="my-booking-paid-grid">
              <div class="my-booking-cell my-booking-cell--contact">
                <ul class="my-booking-details my-booking-details--contact-only">
                  <li><strong>Contact:</strong> <?php echo htmlspecialchars(isset($b['fullname']) ? $b['fullname'] : ''); ?> · <?php echo htmlspecialchars(isset($b['email']) ? $b['email'] : ''); ?></li>
                </ul>
                <p class="my-booking-note my-booking-paymsg-line">Payment complete. Thank you for choosing Ceylon Go.</p>
              </div>
              <div class="my-booking-cell my-booking-cell--refund">
                <?php if (!$refund_requested && $refund_eligible && $paid_at_raw): ?>
                <button type="button" class="my-booking-btn-refund js-refund-open"
                  data-booking-id="<?php echo htmlspecialchars($bid); ?>"
                  data-paid-label="<?php echo htmlspecialchars(date('F j, Y \a\t g:i A', strtotime($paid_at_raw))); ?>"
                  data-deadline-label="<?php echo htmlspecialchars(date('F j, Y \a\t g:i A', (int) $refund_deadline_ts)); ?>"
                  data-total-lkr="<?php echo (int) round((float) (isset($b['total_amount']) ? $b['total_amount'] : 0)); ?>"
                >Request refund</button>
                <?php endif; ?>
              </div>
              <div class="my-booking-cell my-booking-cell--trip">
                <button type="button" class="my-booking-btn-trip-summary js-trip-summary-open" data-booking-id="<?php echo htmlspecialchars($bid); ?>">View trip summary</button>
              </div>
            </div>
            <?php if (!$refund_requested && $paid_at_raw && !$refund_eligible): ?>
            <p class="my-booking-refund-note--below">Refunds can only be requested within 3 days of payment. This booking is outside that window.</p>
            <?php elseif (!$refund_requested && !$paid_at_raw): ?>
            <p class="my-booking-refund-note--below">Refund requests need a confirmed payment time on file. Please contact us with your booking number.</p>
            <?php endif; ?>
          </div>
          <?php elseif ($bank_transfer_waiting): ?>
          <p class="my-booking-note">We have recorded your bank transfer. Your booking stays approved while we verify the payment (usually within 1–2 business days).</p>
          <?php elseif ($is_approved): ?>
          <a href="<?php echo htmlspecialchars($asset_base); ?>/tourist/payment?booking_id=<?php echo htmlspecialchars(urlencode($bid)); ?>" class="my-booking-btn-payment">Proceed to payment</a>
          <?php else: ?>
          <p class="my-booking-note">We will contact you within 24 hrs. Your booking will be reviewed by our team.</p>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <?php if ($bookings_custom_only): ?>
    </main>
  </div>
  <?php endif; ?>

  <div id="refundModal" class="refund-modal" hidden aria-hidden="true">
    <div class="refund-modal__backdrop js-refund-close" tabindex="-1"></div>
    <div class="refund-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="refundModalTitle">
      <div class="refund-modal__head">
        <h2 id="refundModalTitle" class="refund-modal__title">Refund</h2>
        <button type="button" class="refund-modal__close js-refund-close" aria-label="Close">&times;</button>
      </div>
      <div class="refund-modal__body" id="refundModalBody"></div>
    </div>
  </div>

  <div id="tripSummaryModal" class="trip-summary-modal" hidden aria-hidden="true">
    <div class="trip-summary-modal__backdrop js-trip-summary-close" tabindex="-1"></div>
    <div class="trip-summary-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="tripSummaryModalTitle">
      <div class="trip-summary-modal__head">
        <h2 id="tripSummaryModalTitle" class="trip-summary-modal__title">Trip summary</h2>
        <button type="button" class="trip-summary-modal__close js-trip-summary-close" aria-label="Close">&times;</button>
      </div>
      <div class="trip-summary-modal__body" id="tripSummaryModalBody">
        <p class="trip-summary-modal__loading">Loading…</p>
      </div>
    </div>
  </div>

  <?php if ($bookings_custom_only): ?>
  <?php include __DIR__ . '/_trip_service_provider_modal.php'; ?>
  <?php endif; ?>

  <?php include __DIR__ . '/footer.php'; ?>
  <script>
  (function () {
    var base = <?php echo json_encode($asset_base, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    var tripModal = document.getElementById('tripSummaryModal');
    var tripBodyEl = document.getElementById('tripSummaryModalBody');
    var refundModal = document.getElementById('refundModal');
    var refundBodyEl = document.getElementById('refundModalBody');
    var providerOverlay = document.getElementById('tripServiceProviderModalOverlay');
    var providerMount = document.getElementById('tripServiceProviderModalMount');
    var refundState = { mode: 'package', bookingId: '', tripId: '', paidLabel: '', deadlineLabel: '', totalLkr: 0 };

    function esc(s) {
      if (s == null) return '';
      var d = document.createElement('div');
      d.textContent = String(s);
      return d.innerHTML;
    }
    function nf(n) {
      return Number(n || 0).toLocaleString('en-LK');
    }

    function openRefundModal() {
      if (!refundModal) return;
      refundModal.hidden = false;
      refundModal.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    }
    function closeRefundModal() {
      if (!refundModal) return;
      refundModal.hidden = true;
      refundModal.setAttribute('aria-hidden', 'true');
      var providerOpen = providerOverlay && providerOverlay.classList.contains('trip-modal-open');
      if ((!tripModal || tripModal.hidden) && !providerOpen) document.body.style.overflow = '';
    }
    function showRefundStep1() {
      if (!refundBodyEl) return;
      refundBodyEl.innerHTML =
        '<div class="refund-step">' +
        '<p class="refund-policy">Refunds are only possible within <strong>3 days</strong> of your payment (not your travel date).</p>' +
        '<ul class="refund-facts">' +
        '<li><strong>Payment received:</strong> ' + esc(refundState.paidLabel) + '</li>' +
        '<li><strong>Request refund by:</strong> ' + esc(refundState.deadlineLabel) + '</li>' +
        '</ul>' +
        '<p class="refund-hint">If you continue, you confirm a refund request for ' +
        (refundState.mode === 'custom_trip' ? ('trip No. ' + esc(refundState.tripId)) : ('booking #' + esc(refundState.bookingId))) +
        '.</p>' +
        '<div class="refund-actions">' +
        '<button type="button" class="refund-btn refund-btn--ghost js-refund-step1-cancel">Cancel</button>' +
        '<button type="button" class="refund-btn refund-btn--primary js-refund-step1-continue">Continue</button>' +
        '</div></div>';
    }
    function showRefundStep2() {
      if (!refundBodyEl) return;
      var idLine = '';
      var hiddenFields = '';
      if (refundState.mode === 'custom_trip') {
        hiddenFields = '<input type="hidden" name="trip_id" value="' + esc(refundState.tripId) + '">';
        idLine = '<p class="refund-confirm-line">Trip No. <strong>' + esc(refundState.tripId) + '</strong></p>';
      } else {
        hiddenFields = '<input type="hidden" name="booking_id" value="' + esc(refundState.bookingId) + '">';
        idLine = '<p class="refund-confirm-line">Booking <strong>#' + esc(refundState.bookingId) + '</strong></p>';
      }
      refundBodyEl.innerHTML =
        '<form class="refund-step" id="refundSubmitForm">' +
        hiddenFields +
        idLine +
        '<p class="refund-confirm-line">Total paid: <strong>LKR ' + nf(refundState.totalLkr) + '</strong></p>' +
        '<label class="refund-label">Reason (optional)' +
        '<textarea name="reason" class="refund-textarea" rows="3" maxlength="2000" placeholder="Tell us why you need a refund"></textarea></label>' +
        '<div class="refund-actions">' +
        '<button type="button" class="refund-btn refund-btn--ghost js-refund-step2-back">Back</button>' +
        '<button type="submit" class="refund-btn refund-btn--primary">Submit refund request</button>' +
        '</div></form>';
    }
    function showRefundSuccess(msg) {
      if (!refundBodyEl) return;
      refundBodyEl.innerHTML =
        '<div class="refund-step refund-step--success">' +
        '<p class="refund-success-msg">' + esc(msg) + '</p>' +
        '<div class="refund-actions">' +
        '<button type="button" class="refund-btn refund-btn--primary js-refund-done">OK</button>' +
        '</div></div>';
    }

    if (refundModal && refundBodyEl) {
      document.querySelectorAll('.js-refund-open').forEach(function (btn) {
        btn.addEventListener('click', function () {
          refundState.mode = 'package';
          refundState.bookingId = btn.getAttribute('data-booking-id') || '';
          refundState.tripId = '';
          refundState.paidLabel = btn.getAttribute('data-paid-label') || '';
          refundState.deadlineLabel = btn.getAttribute('data-deadline-label') || '';
          refundState.totalLkr = parseInt(btn.getAttribute('data-total-lkr') || '0', 10) || 0;
          showRefundStep1();
          openRefundModal();
        });
      });
      document.querySelectorAll('.js-custom-trip-refund-open').forEach(function (btn) {
        btn.addEventListener('click', function () {
          refundState.mode = 'custom_trip';
          refundState.tripId = btn.getAttribute('data-trip-id') || '';
          refundState.bookingId = '';
          refundState.paidLabel = btn.getAttribute('data-paid-label') || '';
          refundState.deadlineLabel = btn.getAttribute('data-deadline-label') || '';
          refundState.totalLkr = parseInt(btn.getAttribute('data-total-lkr') || '0', 10) || 0;
          showRefundStep1();
          openRefundModal();
        });
      });
      refundModal.addEventListener('click', function (e) {
        var t = e.target;
        if (t.classList.contains('js-refund-close')) {
          closeRefundModal();
          return;
        }
        if (t.classList.contains('js-refund-step1-continue')) {
          e.preventDefault();
          showRefundStep2();
          return;
        }
        if (t.classList.contains('js-refund-step1-cancel')) {
          closeRefundModal();
          return;
        }
        if (t.classList.contains('js-refund-step2-back')) {
          showRefundStep1();
          return;
        }
        if (t.classList.contains('js-refund-done')) {
          window.location.reload();
        }
      });
      refundModal.addEventListener('submit', function (e) {
        var form = e.target;
        if (form.id !== 'refundSubmitForm') return;
        e.preventDefault();
        var fd = new FormData(form);
        var submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;
        var refundUrl = refundState.mode === 'custom_trip'
          ? base + '/tourist/trip/refund-request'
          : base + '/tourist/booking/refund-request';
        fetch(refundUrl, {
          method: 'POST',
          body: fd,
          credentials: 'same-origin',
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function (r) { return r.json(); }).then(function (data) {
          if (data && data.ok) {
            showRefundSuccess(data.message || 'Your refund request has been submitted.');
            return;
          }
          alert(data && data.error ? data.error : 'Could not submit refund request.');
        }).catch(function () {
          alert('Could not submit refund request. Please try again.');
        }).then(function () {
          if (submitBtn) submitBtn.disabled = false;
        });
      });
    }

    function renderSummary(d) {
      var html = '';
      html += '<section class="trip-sum-card">';
      html += '<h3 class="trip-sum-h3">' + esc(d.package_title) + '</h3>';
      html += '<p class="trip-sum-meta">' + esc(d.duration_line) + (d.duration_line ? ' · ' : '') + 'LKR ' + nf(d.price_adult_unit) + ' (adult)</p>';
      html += '<p class="trip-sum-line"><strong>Travel start:</strong> ' + esc(d.travel_date_formatted) + '</p>';
      html += '<p class="trip-sum-line"><strong>Travelers:</strong> ' + esc(d.travelers_text) + '</p>';
      html += '<hr class="trip-sum-hr">';
      html += '<p class="trip-sum-total">Total: <span>LKR ' + nf(d.total_lkr) + '</span></p>';
      html += '</section>';

      if (d.accommodation && d.accommodation.length) {
        html += '<section class="trip-sum-card"><h4 class="trip-sum-h4">Accommodation</h4><ul class="trip-sum-ul">';
        d.accommodation.forEach(function (a) {
          html += '<li class="trip-sum-li"><strong>' + esc(a.hotel) + '</strong>';
          if (a.location) html += ' <span class="trip-sum-muted">(' + esc(a.location) + ')</span>';
          html += ' <span class="trip-sum-muted"> — ' + esc(a.range_label) + '</span></li>';
        });
        html += '</ul></section>';
      }

      html += '<section class="trip-sum-card"><h4 class="trip-sum-h4">Detailed itinerary</h4>';
      if (!d.itinerary || !d.itinerary.length) {
        html += '<p class="trip-sum-muted">Itinerary details will be sent with your confirmation email.</p>';
      } else {
        html += '<div class="trip-sum-days">';
        d.itinerary.forEach(function (day) {
          var dn = parseInt(day.day, 10) || 0;
          var tit = day.title ? String(day.title) : '';
          var head = tit ? ('Day ' + dn + ': ' + tit) : ('Day ' + dn);
          html += '<article class="trip-sum-day"><div class="trip-sum-day-head">';
          html += '<span class="trip-sum-badge">' + (dn || '—') + '</span>';
          html += '<h5 class="trip-sum-day-title">' + esc(head) + '</h5></div>';
          if (day.activities && day.activities.length) {
            html += '<ul class="trip-sum-acts">';
            day.activities.forEach(function (act) {
              html += '<li>' + esc(act) + '</li>';
            });
            html += '</ul>';
          }
          html += '</article>';
        });
        html += '</div>';
      }
      html += '</section>';

      return html;
    }

    function openTripModal() {
      if (!tripModal) return;
      tripModal.hidden = false;
      tripModal.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    }
    function closeTripModal() {
      if (!tripModal) return;
      tripModal.hidden = true;
      tripModal.setAttribute('aria-hidden', 'true');
      var providerOpen = providerOverlay && providerOverlay.classList.contains('trip-modal-open');
      if ((!refundModal || refundModal.hidden) && !providerOpen) document.body.style.overflow = '';
    }

    function buildServiceProviderModalInner(sb) {
      if (!providerMount) return;
      sb = sb || { transport: [], guide: [] };
      var tr = sb.transport || [];
      var gu = sb.guide || [];
      if (!tr.length && !gu.length) {
        providerMount.innerHTML = '<p class="trip-provider-empty">No transport or tour guide bookings are linked to this trip yet.</p>';
        return;
      }
      var html = [];
      if (tr.length) {
        html.push('<p class="trip-provider-section-title">Transport providers</p>');
        tr.forEach(function (row) {
          var name = (row.driver_name && String(row.driver_name).trim()) ? String(row.driver_name).trim() : 'Pending assignment';
          var phone = (row.driver_contact && String(row.driver_contact).trim()) ? String(row.driver_contact).trim() : '—';
          var veh = (row.assigned_vehicle_no != null && String(row.assigned_vehicle_no).trim() !== '') ? String(row.assigned_vehicle_no).trim() : '—';
          html.push(
            '<div class="trip-provider-card">' +
            '<div class="trip-provider-card__name">' + esc(name) + '</div>' +
            '<p class="trip-provider-card__row"><strong>Contact:</strong> ' + esc(phone) + '</p>' +
            '<p class="trip-provider-card__row"><strong>Vehicle No:</strong> ' + esc(veh) + '</p>' +
            '</div>'
          );
        });
      }
      if (gu.length) {
        html.push('<p class="trip-provider-section-title" style="margin-top:16px;">Tour guides</p>');
        gu.forEach(function (row) {
          var fn = (row.first_name || '').trim();
          var ln = (row.last_name || '').trim();
          var name = (fn + ' ' + ln).trim() || 'Pending assignment';
          var phone = (row.guide_contact && String(row.guide_contact).trim()) ? String(row.guide_contact).trim() : '—';
          html.push(
            '<div class="trip-provider-card">' +
            '<div class="trip-provider-card__name">' + esc(name) + '</div>' +
            '<p class="trip-provider-card__row"><strong>Contact:</strong> ' + esc(phone) + '</p>' +
            '</div>'
          );
        });
      }
      providerMount.innerHTML = html.join('');
    }

    function openServiceProviderModal() {
      if (!providerOverlay) return;
      providerOverlay.classList.add('trip-modal-open');
      providerOverlay.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    }

    function closeServiceProviderModal() {
      if (!providerOverlay) return;
      providerOverlay.classList.remove('trip-modal-open');
      providerOverlay.setAttribute('aria-hidden', 'true');
      if ((!tripModal || tripModal.hidden) && (!refundModal || refundModal.hidden)) {
        document.body.style.overflow = '';
      }
    }

    if (tripModal && tripBodyEl) {
      document.querySelectorAll('.js-trip-summary-open').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var id = btn.getAttribute('data-booking-id');
          if (!id) return;
          tripBodyEl.innerHTML = '<p class="trip-summary-modal__loading">Loading…</p>';
          openTripModal();
          fetch(base + '/tourist/booking/trip-summary-json?booking_id=' + encodeURIComponent(id), {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
          }).then(function (r) { return r.json(); }).then(function (data) {
            if (!data || !data.ok) {
              tripBodyEl.innerHTML = '<p class="trip-sum-err">' + esc(data && data.error ? data.error : 'Could not load trip summary.') + '</p>';
              return;
            }
            tripBodyEl.innerHTML = renderSummary(data);
          }).catch(function () {
            tripBodyEl.innerHTML = '<p class="trip-sum-err">Could not load trip summary. Please try again.</p>';
          });
        });
      });

      document.querySelectorAll('.js-custom-trip-summary-open').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var id = btn.getAttribute('data-trip-id');
          if (!id) return;
          tripBodyEl.innerHTML =
            '<iframe class="trip-summary-modal__frame" title="Trip summary" src="' +
            esc(base + '/tourist/custom-trip-summary?trip_id=' + encodeURIComponent(id)) +
            '" loading="lazy"></iframe>';
          openTripModal();
        });
      });

      document.querySelectorAll('.js-trip-summary-close').forEach(function (el) {
        el.addEventListener('click', closeTripModal);
      });
    }

    if (providerOverlay) {
      var providerCloseBtn = document.getElementById('tripServiceProviderModalClose');
      if (providerCloseBtn) providerCloseBtn.addEventListener('click', closeServiceProviderModal);
      providerOverlay.addEventListener('click', function (e) {
        if (e.target === providerOverlay) closeServiceProviderModal();
      });
      document.querySelectorAll('.js-custom-trip-provider-open').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var id = btn.getAttribute('data-trip-id');
          if (!id) return;
          if (providerMount) providerMount.innerHTML = '<p class="trip-provider-empty">Loading…</p>';
          openServiceProviderModal();
          fetch(base + '/tourist/trip-payment-status/' + encodeURIComponent(id), {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
          })
            .then(function (r) { return r.json(); })
            .then(function (data) {
              if (!data || !data.success) {
                if (providerMount) {
                  providerMount.innerHTML =
                    '<p class="trip-provider-empty">' +
                    esc(data && data.error ? data.error : 'Could not load provider details.') +
                    '</p>';
                }
                return;
              }
              buildServiceProviderModalInner(data.subBookings || { transport: [], guide: [] });
            })
            .catch(function () {
              if (providerMount) {
                providerMount.innerHTML =
                  '<p class="trip-provider-empty">Could not load provider details. Please try again.</p>';
              }
            });
        });
      });
    }

    document.addEventListener('keydown', function (e) {
      if (e.key !== 'Escape') return;
      if (refundModal && !refundModal.hidden) {
        closeRefundModal();
        return;
      }
      if (providerOverlay && providerOverlay.classList.contains('trip-modal-open')) {
        closeServiceProviderModal();
        return;
      }
      if (tripModal && !tripModal.hidden) closeTripModal();
    });
  })();
  </script>
  <?php if ($bookings_custom_only): ?>
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
  <?php endif; ?>
</body>
</html>
