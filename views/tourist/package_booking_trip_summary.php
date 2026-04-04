<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$booking = $booking ?? [];
$package = $package ?? null;
$price_adult_unit = isset($price_adult_unit) ? (int) $price_adult_unit : 0;
$price_child_unit = isset($price_child_unit) ? (int) $price_child_unit : 0;
$price_infant_unit = isset($price_infant_unit) ? (int) $price_infant_unit : 0;
$accommodation = $accommodation ?? [];
$itinerary = $itinerary ?? [];
$asset_base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($asset_base === '' || $asset_base === '/') {
    $asset_base = '/CeylonGo/public';
}
$pkg_title = $package['title'] ?? ($booking['package_name'] ?? 'Your trip');
$duration_line = $package['duration'] ?? ($package['duration_short'] ?? '');
$travel_date = isset($booking['travel_date']) ? $booking['travel_date'] : '';
$travel_date_fmt = $travel_date !== '' ? date('F j, Y', strtotime($travel_date)) : '—';
$total_lkr = (int) round((float) ($booking['total_amount'] ?? 0));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trip Summary — <?php echo htmlspecialchars($pkg_title); ?> — Ceylon Go</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/common.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/navbar.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/footer.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base); ?>/css/tourist/package_booking_trip_summary.css">
</head>
<body class="pkg-trip-summary-page">
  <?php include __DIR__ . '/header.php'; ?>

  <main class="pkg-trip-summary-main">
    <p class="pkg-trip-summary-back"><a href="<?php echo htmlspecialchars($asset_base); ?>/tourist/my-bookings">&larr; Back to My Bookings</a></p>

    <h1 class="pkg-trip-summary-h1">Trip Summary</h1>

    <section class="pkg-trip-summary-card">
      <h2 class="pkg-trip-summary-title"><?php echo htmlspecialchars($pkg_title); ?></h2>
      <p class="pkg-trip-summary-meta">
        <?php if ($duration_line !== ''): ?><?php echo htmlspecialchars($duration_line); ?> · <?php endif; ?>
        LKR <?php echo number_format($price_adult_unit); ?> (adult)
      </p>
      <p class="pkg-trip-summary-date"><strong>Travel start:</strong> <?php echo htmlspecialchars($travel_date_fmt); ?></p>
      <?php
        $ta = (int) ($booking['travelers'] ?? 0);
        $ad = (int) ($booking['adults'] ?? 0);
        $ch = (int) ($booking['children'] ?? 0);
        $inf = (int) ($booking['infants'] ?? 0);
      ?>
      <p class="pkg-trip-summary-travelers"><strong>Travelers:</strong> <?php echo $ta; ?> (<?php echo $ad; ?> adult<?php echo $ad !== 1 ? 's' : ''; ?><?php if ($ch > 0): ?>, <?php echo $ch; ?> child<?php echo $ch !== 1 ? 'ren' : ''; ?><?php endif; ?><?php if ($inf > 0): ?>, <?php echo $inf; ?> infant<?php echo $inf !== 1 ? 's' : ''; ?><?php endif; ?>)</p>

      <hr class="pkg-trip-summary-hr">

      <p class="pkg-trip-total">Total: <span>LKR <?php echo number_format($total_lkr); ?></span></p>
    </section>

    <?php if (!empty($accommodation)): ?>
    <section class="pkg-trip-summary-card">
      <h2 class="pkg-trip-section-h">Accommodation</h2>
      <ul class="pkg-trip-accom-list">
        <?php foreach ($accommodation as $row): ?>
        <li class="pkg-trip-accom-item">
          <strong><?php echo htmlspecialchars($row['hotel']); ?></strong>
          <?php if ($row['location'] !== ''): ?>
          <span class="pkg-trip-accom-loc">(<?php echo htmlspecialchars($row['location']); ?>)</span>
          <?php endif; ?>
          <span class="pkg-trip-accom-days"> — <?php echo htmlspecialchars($row['range_label']); ?></span>
        </li>
        <?php endforeach; ?>
      </ul>
    </section>
    <?php endif; ?>

    <section class="pkg-trip-summary-card">
      <h2 class="pkg-trip-section-h">Detailed itinerary</h2>
      <?php if (empty($itinerary)): ?>
      <p class="pkg-trip-empty">Itinerary details for this package will be sent with your confirmation email.</p>
      <?php else: ?>
      <div class="pkg-trip-itin-list">
        <?php foreach ($itinerary as $day): ?>
        <?php
          $dnum = isset($day['day']) ? (int) $day['day'] : 0;
          $dtitle = isset($day['title']) ? (string) $day['title'] : '';
          $activities = isset($day['activities']) && is_array($day['activities']) ? $day['activities'] : [];
        ?>
        <article class="pkg-trip-day-card">
          <div class="pkg-trip-day-head">
            <span class="pkg-trip-day-badge"><?php echo $dnum > 0 ? (int) $dnum : '—'; ?></span>
            <h3 class="pkg-trip-day-title"><?php echo htmlspecialchars($dtitle !== '' ? ('Day ' . $dnum . ': ' . $dtitle) : ('Day ' . max(1, $dnum))); ?></h3>
          </div>
          <?php if (!empty($activities)): ?>
          <ul class="pkg-trip-day-acts">
            <?php foreach ($activities as $act): ?>
            <li><?php echo htmlspecialchars((string) $act); ?></li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
        </article>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </section>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
