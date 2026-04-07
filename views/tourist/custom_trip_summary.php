<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$trip = isset($trip) && is_array($trip) ? $trip : array();
$snapshot = isset($trip_snapshot) && is_array($trip_snapshot) ? $trip_snapshot : null;

$asset_base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($asset_base === '' || $asset_base === '/') {
    $asset_base = '/CeylonGo/public';
}

$dest = isset($trip['destination']) ? trim((string) $trip['destination']) : '';
$title = $dest !== '' ? $dest : 'Your custom trip';
$customer = isset($trip['customer_name']) ? trim((string) $trip['customer_name']) : '';
$people = isset($trip['number_of_people']) ? (int) $trip['number_of_people'] : 0;
$days = isset($trip['number_of_days']) ? (int) $trip['number_of_days'] : 0;
$status = isset($trip['status']) ? (string) $trip['status'] : '';
$startRaw = isset($trip['start_date']) ? (string) $trip['start_date'] : '';
$startFmt = $startRaw !== '' && strtotime($startRaw) !== false ? date('F j, Y', strtotime($startRaw)) : '—';

$endRaw = '';
if ($snapshot && !empty($snapshot['end_date'])) {
    $endRaw = (string) $snapshot['end_date'];
}
if ($endRaw === '' && $startRaw !== '' && $days > 0) {
    $ts = strtotime($startRaw);
    if ($ts !== false) {
        $endRaw = date('Y-m-d', $ts + (($days - 1) * 86400));
    }
}
$endFmt = ($endRaw !== '' && strtotime($endRaw) !== false) ? date('F j, Y', strtotime($endRaw)) : '—';

$budget = 0.0;
if (isset($trip['budget_lkr']) && $trip['budget_lkr'] !== '' && $trip['budget_lkr'] !== null) {
    $budget = (float) $trip['budget_lkr'];
}
if ($budget <= 0 && $snapshot && isset($snapshot['budget_lkr'])) {
    $budget = (float) $snapshot['budget_lkr'];
}

$submittedLine = '';
if ($snapshot && !empty($snapshot['submitted_at'])) {
    $st = strtotime((string) $snapshot['submitted_at']);
    if ($st !== false) {
        $submittedLine = date('F j, Y \a\t g:i A', $st);
    }
}
if ($submittedLine === '' && !empty($trip['created_at'])) {
    $ct = strtotime((string) $trip['created_at']);
    if ($ct !== false) {
        $submittedLine = date('F j, Y \a\t g:i A', $ct);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trip summary — <?php echo htmlspecialchars($title); ?> — Ceylon Go</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/common.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/navbar.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/footer.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/package_booking_trip_summary.css">
</head>
<body class="pkg-trip-summary-page">
  <?php include __DIR__ . '/header.php'; ?>

  <main class="pkg-trip-summary-main">
    <p class="pkg-trip-summary-back"><a href="<?php echo htmlspecialchars($asset_base); ?>/tourist/customize-trip">&larr; Back to trip planner</a></p>

    <h1 class="pkg-trip-summary-h1">Trip summary</h1>

    <section class="pkg-trip-summary-card">
      <h2 class="pkg-trip-summary-title"><?php echo htmlspecialchars($title); ?></h2>
      <p class="pkg-trip-summary-meta">
        Trip ID #<?php echo (int) ($trip['id'] ?? 0); ?>
        <?php if ($status !== ''): ?> · <strong><?php echo htmlspecialchars(ucfirst($status)); ?></strong><?php endif; ?>
      </p>
      <?php if ($submittedLine !== ''): ?>
      <p class="pkg-trip-summary-date"><strong>Submitted:</strong> <?php echo htmlspecialchars($submittedLine); ?></p>
      <?php endif; ?>
      <p class="pkg-trip-summary-date"><strong>Travel start:</strong> <?php echo htmlspecialchars($startFmt); ?></p>
      <?php if ($endFmt !== '—'): ?>
      <p class="pkg-trip-summary-date"><strong>Travel end:</strong> <?php echo htmlspecialchars($endFmt); ?></p>
      <?php endif; ?>
      <?php if ($customer !== ''): ?>
      <p class="pkg-trip-summary-travelers"><strong>Lead guest:</strong> <?php echo htmlspecialchars($customer); ?></p>
      <?php endif; ?>
      <p class="pkg-trip-summary-travelers"><strong>Group size:</strong> <?php echo $people > 0 ? (int) $people : '—'; ?> · <strong>Duration:</strong> <?php echo $days > 0 ? (int) $days . ' day' . ($days !== 1 ? 's' : '') : '—'; ?></p>

      <hr class="pkg-trip-summary-hr">

      <p class="pkg-trip-total">Estimated budget: <span>LKR <?php echo number_format(max(0, $budget), $budget != floor($budget) ? 2 : 0); ?></span></p>
    </section>

    <p class="pkg-trip-summary-back" style="margin-top: 1.25rem;">
      <a href="<?php echo htmlspecialchars($asset_base); ?>/tourist/contact">Request refund</a>
      ·
      <a href="<?php echo htmlspecialchars($asset_base); ?>/tourist/dashboard-side">Dashboard</a>
    </p>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
