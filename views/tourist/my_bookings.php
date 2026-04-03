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
          $bid = isset($b['id']) ? $b['id'] : '';
        ?>
        <div class="my-booking-card">
          <div class="my-booking-header">
            <?php if ($is_paid): ?>
            <span class="my-booking-status my-booking-status--paid">Completed</span>
            <?php elseif ($is_approved): ?>
            <span class="my-booking-status my-booking-status--approved">Approved</span>
            <?php else: ?>
            <span class="my-booking-status my-booking-status--pending">Pending</span>
            <?php endif; ?>
            <span class="my-booking-date"><?php echo htmlspecialchars($b['travel_date'] ?? '-'); ?></span>
          </div>
          <h2 class="my-booking-package"><?php echo htmlspecialchars($b['package_name'] ?? 'Package'); ?></h2>
          <ul class="my-booking-details">
            <li><strong>Travelers:</strong> <?php echo (int)($b['travelers'] ?? 0); ?><?php if (isset($b['adults']) || isset($b['children']) || isset($b['infants'])): ?> (<?php echo (int)($b['adults'] ?? 0); ?> adult<?php echo ((int)($b['adults'] ?? 0)) !== 1 ? 's' : ''; ?><?php if (!empty($b['children'])): ?>, <?php echo (int)$b['children']; ?> child<?php echo (int)$b['children'] !== 1 ? 'ren' : ''; ?><?php endif; ?><?php if (!empty($b['infants'])): ?>, <?php echo (int)$b['infants']; ?> infant<?php echo (int)$b['infants'] !== 1 ? 's' : ''; ?><?php endif; ?>)<?php endif; ?></li>
            <li><strong>Total:</strong> LKR <?php echo number_format((int)($b['total_amount'] ?? 0)); ?></li>
            <li><strong>Contact:</strong> <?php echo htmlspecialchars($b['fullname'] ?? ''); ?> · <?php echo htmlspecialchars($b['email'] ?? ''); ?></li>
            <?php if (!empty($b['special_requests'])): ?>
            <li><strong>Requests:</strong> <?php echo htmlspecialchars($b['special_requests']); ?></li>
            <?php endif; ?>
          </ul>
          <?php if ($is_approved): ?>
          <a href="<?php echo htmlspecialchars($asset_base); ?>/tourist/payment?booking_id=<?php echo htmlspecialchars(urlencode($bid)); ?>" class="my-booking-btn-payment">Proceed to payment</a>
          <?php elseif ($is_paid): ?>
          <p class="my-booking-note">Payment complete. Thank you for choosing Ceylon Go.</p>
          <?php else: ?>
          <p class="my-booking-note">We will contact you within 24 hrs. Your booking will be reviewed by our team.</p>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
