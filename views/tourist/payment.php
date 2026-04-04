<?php
// payment.php
$booking = $booking ?? null;
$payhere_per_transaction_max_lkr = isset($payhere_per_transaction_max_lkr) ? (int) $payhere_per_transaction_max_lkr : 0;
$bank_transfer_details = $bank_transfer_details ?? '';
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
  <title>Payment - Ceylon Go</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/tourist_dashboard.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/navbar.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/footer.css">
  <style>
    body {
      background-color: #f0f8f0; /* Light greenish background from tourist_dashboard */
    }
    
    .payment-container {
      max-width: 600px;
      margin: 0 auto;
      padding: 20px;
    }
    
    .payment-summary {
      background: white;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(74, 124, 89, 0.15);
      margin-bottom: 30px;
    }
    
    .payment-summary h3 {
      color: #2c5530;
      margin-bottom: 20px;
      font-size: 20px;
    }
    
    .summary-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
      padding: 8px 0;
      border-bottom: 1px solid #e0e8e0;
    }
    
    .summary-item:last-child {
      border-bottom: none;
      font-weight: bold;
      font-size: 18px;
      color: #2c5530;
    }
    
    .payment-methods {
      background: white;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(74, 124, 89, 0.15);
    }
    
    .payment-method {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
      padding: 15px;
      border: 2px solid #e0e8e0;
      border-radius: 10px;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .payment-method:hover {
      border-color: #4a7c59;
      background-color: #f8fcf8;
    }
    
    .payment-method input[type="radio"] {
      margin-right: 15px;
    }
    
    .payment-method label {
      font-weight: 600;
      color: #2c5530;
      cursor: pointer;
    }
    
    .card-details {
      margin-top: 20px;
      padding: 20px;
      background: #f8fcf8;
      border-radius: 10px;
      border: 1px solid #e0e8e0;
    }
    
    .card-row {
      display: flex;
      gap: 15px;
      margin-bottom: 15px;
    }
    
    .card-row .form-group {
      flex: 1;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: 600;
      color: #2c5530;
    }
    
    .form-group input {
      width: 100%;
      padding: 12px;
      border: 2px solid #e0e8e0;
      border-radius: 8px;
      font-size: 16px;
      transition: all 0.3s ease;
    }
    
    .form-group input:focus {
      outline: none;
      border-color: #4a7c59;
      box-shadow: 0 0 0 3px rgba(74, 124, 89, 0.1);
    }

    .form-group input[type="file"] {
      width: 100%;
      max-width: 100%;
      padding: 10px;
      border: 2px solid #e0e8e0;
      border-radius: 8px;
      font-size: 14px;
      background: #fff;
      box-sizing: border-box;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <?php include 'header.php'; ?>

  <!-- Payment Section -->
  <section class="pay" style="background-image: url('../../public/images/pay.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; color: #fff; padding: 80px 20px; text-align: center;">
    <h1>Complete Your Payment</h1>
    <p>Secure payment gateway for your Sri Lankan adventure</p>
  </section>

  <section class="customize-trip">
    <div class="payment-container">
      <!-- Payment Summary -->
      <div class="payment-summary">
        <h3>Booking Summary</h3>
        <?php if ($booking): ?>
        <div class="summary-item">
          <span>Package:</span>
          <span><?php echo htmlspecialchars($booking['package_name'] ?? 'Package'); ?></span>
        </div>
        <div class="summary-item">
          <span>Number of Travelers:</span>
          <span><?php echo (int)($booking['travelers'] ?? 0); ?><?php if (isset($booking['adults']) || isset($booking['children']) || isset($booking['infants'])): ?> (<?php echo (int)($booking['adults'] ?? 0); ?> adult<?php echo ((int)($booking['adults'] ?? 0)) !== 1 ? 's' : ''; ?><?php if (!empty($booking['children'])): ?>, <?php echo (int)$booking['children']; ?> child<?php echo (int)$booking['children'] !== 1 ? 'ren' : ''; ?><?php endif; ?><?php if (!empty($booking['infants'])): ?>, <?php echo (int)$booking['infants']; ?> infant<?php echo (int)$booking['infants'] !== 1 ? 's' : ''; ?><?php endif; ?>)<?php endif; ?></span>
        </div>
        <div class="summary-item">
          <span>Travel Date:</span>
          <span><?php echo htmlspecialchars($booking['travel_date'] ?? '-'); ?></span>
        </div>
        <div class="summary-item">
          <span>Total Amount:</span>
          <span>LKR <?php echo number_format((int)($booking['total_amount'] ?? 0)); ?></span>
        </div>
        <?php else: ?>
        <div class="summary-item">
          <span>Package:</span>
          <span>Cultural Experience in Sri Lanka</span>
        </div>
        <div class="summary-item">
          <span>Number of Persons:</span>
          <span>2</span>
        </div>
        <div class="summary-item">
          <span>Travel Dates:</span>
          <span>Dec 15 - Dec 19, 2024</span>
        </div>
        <div class="summary-item">
          <span>Total Amount:</span>
          <span>Rs. 50,000</span>
        </div>
        <?php endif; ?>
      </div>

      <!-- Payment Methods -->
      <div class="payment-methods">
        <h3>Select Payment Method</h3>

        <?php
        $pm_error = $_SESSION['payment_error'] ?? null;
        $pm_info = $_SESSION['payment_info'] ?? null;
        unset($_SESSION['payment_error'], $_SESSION['payment_info']);
        ?>
        <?php if (!empty($pm_error)): ?>
        <p style="color:#b45309;background:#fef3c7;padding:12px;border-radius:8px;margin-bottom:16px;"><?php echo htmlspecialchars($pm_error); ?></p>
        <?php endif; ?>
        <?php if (!empty($pm_info)): ?>
        <p style="color:#166534;background:#dcfce7;padding:12px;border-radius:8px;margin-bottom:16px;"><?php echo htmlspecialchars($pm_info); ?></p>
        <?php endif; ?>

        <?php
        $booking_total = $booking ? (float) ($booking['total_amount'] ?? 0) : 0;
        if ($booking && ($booking['status'] ?? '') === 'approved' && $payhere_per_transaction_max_lkr > 0 && $booking_total > $payhere_per_transaction_max_lkr + 0.001):
        ?>
        <p style="color:#1e40af;background:#dbeafe;padding:12px;border-radius:8px;margin-bottom:16px;font-size:0.95rem;line-height:1.5;">
          This total (LKR <?php echo number_format($booking_total, 2, '.', ','); ?>) is above the online card limit for this payment account (LKR <?php echo number_format($payhere_per_transaction_max_lkr); ?> per payment).
          Choose <strong>Bank transfer</strong>, or your merchant must raise the limit in <strong>PayHere</strong> (plan / settings) to accept card payments this large.
        </p>
        <?php endif; ?>

        <?php if ($booking && isset($booking['status']) && $booking['status'] === 'paid'): ?>
        <p style="color:#166534;font-weight:600;">This booking is already paid. Thank you.</p>
        <a href="<?php echo htmlspecialchars($asset_base); ?>/tourist/my-bookings" class="btn" style="display:inline-block;margin-top:16px;text-decoration:none;text-align:center;">Back to My Bookings</a>
        <?php elseif ($booking && ($booking['status'] ?? '') === 'approved' && !empty($booking['bank_transfer_submitted_at'])): ?>
        <p style="color:#1e40af;font-weight:600;">We have recorded your bank transfer. Check <strong>My Bookings</strong> for status — we usually confirm within 1–2 business days.</p>
        <a href="<?php echo htmlspecialchars($asset_base); ?>/tourist/my-bookings" class="btn" style="display:inline-block;margin-top:16px;text-decoration:none;text-align:center;">My Bookings</a>
        <?php elseif ($booking): ?>
        <form method="post" action="<?php echo htmlspecialchars($asset_base); ?>/tourist/payment/checkout" id="payment-form" enctype="multipart/form-data">
          <input type="hidden" name="booking_id" value="<?php echo (int)($booking['id'] ?? 0); ?>">
          <?php
          $card_blocked = $payhere_per_transaction_max_lkr > 0 && $booking_total > $payhere_per_transaction_max_lkr + 0.001;
          ?>
        <div class="payment-method">
          <input type="radio" id="credit-card" name="payment_method" value="card" <?php echo $card_blocked ? '' : 'checked'; ?> <?php echo $card_blocked ? 'disabled' : ''; ?>>
          <label for="credit-card" style="<?php echo $card_blocked ? 'opacity:0.65;' : ''; ?>">Credit / Debit Card<?php echo $card_blocked ? ' (over per-payment limit)' : ''; ?></label>
        </div>
        
        <div class="payment-method">
          <input type="radio" id="bank-transfer" name="payment_method" value="bank-transfer" <?php echo $card_blocked ? 'checked' : ''; ?>>
          <label for="bank-transfer">Bank Transfer</label>
        </div>

        <div class="card-details" id="payhere-panel" style="<?php echo $card_blocked ? 'display:none;' : ''; ?>">
          <p style="margin:0 0 8px;color:#2c5530;font-weight:600;">Secure checkout</p>
          <p style="margin:0;font-size:0.95rem;color:#374151;">After you click Pay, you’ll go to our payment partner’s secure page to enter your card details.</p>
        </div>

        <div class="card-details" id="bank-panel" style="<?php echo $card_blocked ? '' : 'display:none;'; ?>">
          <p style="margin:0 0 12px;font-size:0.95rem;color:#374151;font-weight:600;">Pay by bank transfer</p>
          <?php if ($bank_transfer_details !== ''): ?>
          <div style="font-size:0.9rem;color:#374151;line-height:1.6;white-space:pre-wrap;margin-bottom:12px;"><?php echo nl2br(htmlspecialchars($bank_transfer_details)); ?></div>
          <?php else: ?>
          <p style="margin:0 0 12px;font-size:0.9rem;color:#b45309;">Add your company bank details in <code style="font-size:0.8rem;">config/config.php</code> (<strong>BANK_TRANSFER_DETAILS</strong>).</p>
          <?php endif; ?>
          <p style="margin:0 0 8px;font-size:0.95rem;color:#2c5530;"><strong>Amount:</strong> LKR <?php echo number_format((int)($booking['total_amount'] ?? 0)); ?></p>
          <p style="margin:0 0 12px;font-size:0.95rem;color:#2c5530;"><strong>Reference / narration:</strong> <?php echo (int)($booking['id'] ?? 0); ?></p>
          <div class="form-group" style="margin-top:4px;text-align:left;">
            <label for="bank_transfer_slip">Upload screenshot of bank slip / transfer</label>
            <input type="file" id="bank_transfer_slip" name="bank_transfer_slip" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp">
            <p style="margin:6px 0 0;font-size:0.8rem;color:#6b7280;">JPG, PNG or WebP, max 5 MB. Required before Continue.</p>
          </div>
          <p style="margin:12px 0 0;font-size:0.85rem;color:#6b7280;">After you transfer and upload your slip, click Continue. An admin can mark the booking paid when the money is received.</p>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 20px;">
          <button type="submit" class="btn" id="pay-submit" style="flex: 1;"><?php echo $card_blocked ? 'Continue' : ('Pay LKR ' . number_format((int)($booking['total_amount'] ?? 0))); ?></button>
          <a href="<?php echo htmlspecialchars($asset_base); ?>/tourist/my-bookings" class="btn" style="flex: 1; background: #000; text-decoration: none; text-align: center; display: flex; align-items: center; justify-content: center;">Back</a>
        </div>
        </form>
        <script>
        (function () {
 var cardBlocked = <?php echo $card_blocked ? 'true' : 'false'; ?>;
          var card = document.getElementById('credit-card');
          var bank = document.getElementById('bank-transfer');
          var pPay = document.getElementById('payhere-panel');
          var pBank = document.getElementById('bank-panel');
          var btn = document.getElementById('pay-submit');
          var form = document.getElementById('payment-form');
          var slipInput = document.getElementById('bank_transfer_slip');
          var payLabel = 'Pay LKR <?php echo number_format((int)($booking['total_amount'] ?? 0)); ?>';
          var maxBytes = 5 * 1024 * 1024;
          function sync() {
            if (cardBlocked) return;
            var isBank = bank && bank.checked;
            if (pPay) pPay.style.display = isBank ? 'none' : 'block';
            if (pBank) pBank.style.display = isBank ? 'block' : 'none';
            if (btn) btn.textContent = isBank ? 'Continue' : payLabel;
          }
          if (card) card.addEventListener('change', sync);
          if (bank) bank.addEventListener('change', sync);
          sync();
          if (form && slipInput && bank) {
            form.addEventListener('submit', function (e) {
              if (!bank.checked) return;
              if (!slipInput.files || slipInput.files.length === 0) {
                e.preventDefault();
                alert('Please upload a screenshot of your bank transfer slip.');
                return;
              }
              if (slipInput.files[0].size > maxBytes) {
                e.preventDefault();
                alert('File is too large. Maximum size is 5 MB.');
              }
            });
          }
        })();
        </script>
        <?php else: ?>
        <p style="color:#b45309;">No approved booking selected. Open this page from My Bookings after approval.</p>
        <a href="<?php echo htmlspecialchars($asset_base); ?>/tourist/my-bookings" class="btn" style="display:inline-block;margin-top:16px;text-decoration:none;">My Bookings</a>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include 'footer.php'; ?>
</body>
</html>
