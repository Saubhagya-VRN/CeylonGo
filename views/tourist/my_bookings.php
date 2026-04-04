<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$bookings = $bookings ?? [];
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
  <title>My Bookings - Ceylon Go</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/common.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/navbar.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/footer.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/my_bookings.css">
</head>
<body class="my-bookings-page">
  <?php include __DIR__ . '/header.php'; ?>

  <main class="my-bookings-main">
    <h1 class="my-bookings-title">My Bookings</h1>
    <p class="my-bookings-intro">Your booking requests. We will contact you within 24 hrs.</p>

    <?php
    $payment_message = $payment_message ?? null;
    $payment_error = $payment_error ?? null;
    $payment_info = $payment_info ?? null;
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

    <?php if (empty($bookings)): ?>
      <div class="my-bookings-empty">
        <p>You have no pending bookings.</p>
        <a href="<?php echo htmlspecialchars($asset_base); ?>/tourist/packages" class="btn-primary-pkg">Browse Packages</a>
      </div>
    <?php else: ?>
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
            <span class="my-booking-date"><?php echo htmlspecialchars($b['travel_date'] ?? '-'); ?></span>
          </div>
          <h2 class="my-booking-package"><?php echo htmlspecialchars($b['package_name'] ?? 'Package'); ?></h2>
          <ul class="my-booking-details<?php echo $is_paid ? ' my-booking-details--paid-follow' : ''; ?>">
            <li><strong>Travelers:</strong> <?php echo (int)($b['travelers'] ?? 0); ?><?php if (isset($b['adults']) || isset($b['children']) || isset($b['infants'])): ?> (<?php echo (int)($b['adults'] ?? 0); ?> adult<?php echo ((int)($b['adults'] ?? 0)) !== 1 ? 's' : ''; ?><?php if (!empty($b['children'])): ?>, <?php echo (int)$b['children']; ?> child<?php echo (int)$b['children'] !== 1 ? 'ren' : ''; ?><?php endif; ?><?php if (!empty($b['infants'])): ?>, <?php echo (int)$b['infants']; ?> infant<?php echo (int)$b['infants'] !== 1 ? 's' : ''; ?><?php endif; ?>)<?php endif; ?></li>
            <li><strong>Total:</strong> LKR <?php echo number_format((int)($b['total_amount'] ?? 0)); ?></li>
            <?php if (!$is_paid): ?>
            <li><strong>Contact:</strong> <?php echo htmlspecialchars($b['fullname'] ?? ''); ?> · <?php echo htmlspecialchars($b['email'] ?? ''); ?></li>
            <?php endif; ?>
            <?php if (!empty($b['special_requests'])): ?>
            <li><strong>Requests:</strong> <?php echo htmlspecialchars($b['special_requests']); ?></li>
            <?php endif; ?>
          </ul>
          <?php if ($is_paid):
            $paid_at_raw = $b['paid_at'] ?? null;
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
              $rr = $b['refund_requested_at'] ?? '';
              if ($rr !== ''): ?> on <?php echo htmlspecialchars(date('F j, Y \a\t g:i A', strtotime($rr))); ?><?php endif; ?>. We will email you about the next steps.</p>
            <?php endif; ?>
            <div class="my-booking-paid-grid">
              <div class="my-booking-cell my-booking-cell--contact">
                <ul class="my-booking-details my-booking-details--contact-only">
                  <li><strong>Contact:</strong> <?php echo htmlspecialchars($b['fullname'] ?? ''); ?> · <?php echo htmlspecialchars($b['email'] ?? ''); ?></li>
                </ul>
                <p class="my-booking-note my-booking-paymsg-line">Payment complete. Thank you for choosing Ceylon Go.</p>
              </div>
              <div class="my-booking-cell my-booking-cell--refund">
                <?php if (!$refund_requested && $refund_eligible && $paid_at_raw): ?>
                <button type="button" class="my-booking-btn-refund js-refund-open"
                  data-booking-id="<?php echo htmlspecialchars($bid); ?>"
                  data-paid-label="<?php echo htmlspecialchars(date('F j, Y \a\t g:i A', strtotime($paid_at_raw))); ?>"
                  data-deadline-label="<?php echo htmlspecialchars(date('F j, Y \a\t g:i A', (int) $refund_deadline_ts)); ?>"
                  data-total-lkr="<?php echo (int) round((float) ($b['total_amount'] ?? 0)); ?>"
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
  </main>

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

  <?php include __DIR__ . '/footer.php'; ?>
  <script>
  (function () {
    var base = <?php echo json_encode($asset_base, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    var tripModal = document.getElementById('tripSummaryModal');
    var tripBodyEl = document.getElementById('tripSummaryModalBody');
    var refundModal = document.getElementById('refundModal');
    var refundBodyEl = document.getElementById('refundModalBody');
    var refundState = { bookingId: '', paidLabel: '', deadlineLabel: '', totalLkr: 0 };

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
      if (!tripModal || tripModal.hidden) document.body.style.overflow = '';
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
        '<p class="refund-hint">If you continue, you confirm a refund request for booking #' + esc(refundState.bookingId) + '.</p>' +
        '<div class="refund-actions">' +
        '<button type="button" class="refund-btn refund-btn--ghost js-refund-step1-cancel">Cancel</button>' +
        '<button type="button" class="refund-btn refund-btn--primary js-refund-step1-continue">Continue</button>' +
        '</div></div>';
    }
    function showRefundStep2() {
      if (!refundBodyEl) return;
      refundBodyEl.innerHTML =
        '<form class="refund-step" id="refundSubmitForm">' +
        '<input type="hidden" name="booking_id" value="' + esc(refundState.bookingId) + '">' +
        '<p class="refund-confirm-line">Booking <strong>#' + esc(refundState.bookingId) + '</strong></p>' +
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
          refundState.bookingId = btn.getAttribute('data-booking-id') || '';
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
        fetch(base + '/tourist/booking/refund-request', {
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
      if (!refundModal || refundModal.hidden) document.body.style.overflow = '';
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

      document.querySelectorAll('.js-trip-summary-close').forEach(function (el) {
        el.addEventListener('click', closeTripModal);
      });
    }

    document.addEventListener('keydown', function (e) {
      if (e.key !== 'Escape') return;
      if (refundModal && !refundModal.hidden) {
        closeRefundModal();
        return;
      }
      if (tripModal && !tripModal.hidden) closeTripModal();
    });
  })();
  </script>
</body>
</html>
