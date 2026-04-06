<?php
$scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
$asset_base = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
if ($asset_base === '' || $asset_base === '/') {
  $asset_base = '/CeylonGo/public';
}
$payhere_per_transaction_max_lkr = isset($payhere_per_transaction_max_lkr) ? (int) $payhere_per_transaction_max_lkr : 0;
$bank_transfer_details = isset($bank_transfer_details) ? $bank_transfer_details : '';
$pm_error = isset($_SESSION['payment_error']) ? $_SESSION['payment_error'] : null;
$pm_info = isset($_SESSION['payment_info']) ? $_SESSION['payment_info'] : null;
unset($_SESSION['payment_error'], $_SESSION['payment_info']);
$payhero = htmlspecialchars($asset_base . '/images/pay.jpg', ENT_QUOTES, 'UTF-8');
?>
<div class="trip-payment-page" id="tripPaymentStepRoot" data-payhere-max="<?php echo (int) $payhere_per_transaction_max_lkr; ?>">
  <section class="trip-payment-hero" style="background-image: url('<?php echo $payhero; ?>');">
    <h1 class="trip-payment-hero-title">Complete Your Payment</h1>
    <p class="trip-payment-hero-lead">Secure payment gateway for your Sri Lankan adventure</p>
  </section>

  <section class="trip-payment-customize">
    <div class="payment-container">
      <div class="payment-summary">
        <h3>Booking Summary</h3>
        <div class="summary-item">
          <span>Trip:</span>
          <span id="tripPaymentSummaryDest">—</span>
        </div>
        <div class="summary-item">
          <span>Number of Travelers:</span>
          <span id="tripPaymentSummaryParty">—</span>
        </div>
        <div class="summary-item">
          <span>Travel Date:</span>
          <span id="tripPaymentSummaryDates">—</span>
        </div>
        <div class="summary-item">
          <span>Total Amount:</span>
          <span id="tripPaymentTotalLine">LKR —</span>
        </div>
      </div>

      <div class="payment-methods">
        <h3>Select Payment Method</h3>

        <?php if (!empty($pm_error)): ?>
        <p class="trip-payment-flash trip-payment-flash--error"><?php echo htmlspecialchars($pm_error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php if (!empty($pm_info)): ?>
        <p class="trip-payment-flash trip-payment-flash--info"><?php echo htmlspecialchars($pm_info, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <p id="trip_payhere_limit_banner" class="trip-pay-limit-banner" style="display: none;"></p>

        <p class="trip-payment-hint" id="tripPaymentTripHint">Submit your trip on <strong>Trip Review &amp; Submit</strong> first — your trip reference is required to pay.</p>

        <form method="post" action="<?php echo htmlspecialchars($asset_base, ENT_QUOTES, 'UTF-8'); ?>/tourist/trip-payment-checkout" id="tripPaymentForm" enctype="multipart/form-data" onsubmit="try{sessionStorage.setItem('ceylonTripWizardReturnToReview','1')}catch(e){}">
          <input type="hidden" name="trip_id" id="trip_payment_trip_id" value="">

          <div class="payment-method">
            <input type="radio" id="trip_pay_card" name="payment_method" value="card" checked>
            <label for="trip_pay_card" id="trip_pay_card_label">Credit / Debit Card</label>
          </div>
          <div class="payment-method">
            <input type="radio" id="trip_pay_bank" name="payment_method" value="bank-transfer">
            <label for="trip_pay_bank">Bank Transfer</label>
          </div>

          <div class="card-details" id="trip_payhere_panel">
            <p style="margin:0 0 8px;color:#2c5530;font-weight:600;">Secure checkout</p>
            <p style="margin:0;font-size:0.95rem;color:#374151;">After you click Pay, you’ll go to our payment partner’s secure page to enter your card details.</p>
          </div>

          <div class="card-details" id="trip_bank_panel" style="display:none;">
            <p style="margin:0 0 12px;font-size:0.95rem;color:#374151;font-weight:600;">Pay by bank transfer</p>
            <?php if ($bank_transfer_details !== ''): ?>
            <div style="font-size:0.9rem;color:#374151;line-height:1.6;white-space:pre-wrap;margin-bottom:12px;"><?php echo nl2br(htmlspecialchars($bank_transfer_details, ENT_QUOTES, 'UTF-8')); ?></div>
            <?php else: ?>
            <p style="margin:0 0 12px;font-size:0.95rem;color:#b45309;">Add your company bank details in <code style="font-size:0.8rem;">config/config.php</code> (<strong>BANK_TRANSFER_DETAILS</strong>).</p>
            <?php endif; ?>
            <p style="margin:0 0 8px;font-size:0.95rem;color:#2c5530;"><strong>Amount:</strong> <span id="trip_bank_amount_line">LKR —</span></p>
            <p style="margin:0 0 12px;font-size:0.95rem;color:#2c5530;"><strong>Reference / narration:</strong> <span id="trip_bank_ref_id">—</span></p>
            <div class="form-group" style="margin-top:4px;text-align:left;">
              <label for="trip_bank_transfer_slip">Upload screenshot of bank slip / transfer</label>
              <input type="file" id="trip_bank_transfer_slip" name="bank_transfer_slip" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp">
              <p style="margin:6px 0 0;font-size:0.8rem;color:#6b7280;">JPG, PNG or WebP, max 5 MB. Required before Continue.</p>
            </div>
            <p style="margin:12px 0 0;font-size:0.85rem;color:#6b7280;">After you transfer and upload your slip, click Continue. An admin can mark your trip paid when the money is received.</p>
          </div>

          <div class="trip-payment-btn-row">
            <button type="submit" class="trip-payment-btn trip-payment-btn--primary" id="trip_payment_submit_btn">Pay</button>
            <a href="<?php echo htmlspecialchars($asset_base, ENT_QUOTES, 'UTF-8'); ?>/tourist/my-bookings" class="trip-payment-btn trip-payment-btn--back">Back</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
