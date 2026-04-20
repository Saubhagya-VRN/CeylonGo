<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
}
$base = rtrim((string) BASE_URL, '/');
$review = isset($review) && is_array($review) ? $review : null;
if (!$review) {
    header('Location: ' . $base . '/tourist/my-reviews');
    exit;
}
$error_message = isset($error_message) ? (string) $error_message : '';
$user_name = isset($_SESSION['user_name']) ? (string) $_SESSION['user_name'] : 'Tourist';
$tourist_email = isset($_SESSION['user_email']) ? trim((string) $_SESSION['user_email']) : '';
$user_email_sidebar = $tourist_email;
$avatar_initial = $user_name !== '' ? strtoupper(substr($user_name, 0, 1)) : 'T';
$asset_base = $base;
$trip_sidebar_active = 'reviews';
$csrf = isset($_SESSION['csrf_token']) ? (string) $_SESSION['csrf_token'] : '';
$rid = (int) ($review['id'] ?? 0);
$name = htmlspecialchars((string) ($review['name'] ?? ''), ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars((string) ($review['email'] ?? ''), ENT_QUOTES, 'UTF-8');
$rtext = (string) ($review['review_text'] ?? '');
$ratingVal = (int) ($review['rating'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit review - Ceylon Go</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base, ENT_QUOTES, 'UTF-8'); ?>/css/common.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/navbar.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/trip_layout.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/sidebar.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/trip.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/add_review.css">
</head>
<body class="trip-page-body my-reviews-page">
  <?php include __DIR__ . '/header.php'; ?>
  <div class="sidebar-overlay trip-overlay" id="tripSidebarOverlay"></div>
  <div class="trip-page-wrapper">
    <?php include __DIR__ . '/_trip_sidebar.php'; ?>
    <main class="trip-main-content reviews-trip-main">
      <button type="button" class="hamburger-btn trip-hamburger" id="tripHamburgerBtn" aria-label="Toggle menu"><span></span><span></span><span></span></button>
      <div class="trip-breadcrumbs">
        <a href="<?php echo htmlspecialchars($base . '/tourist/my-reviews', ENT_QUOTES, 'UTF-8'); ?>">My reviews</a>
        <span>&gt;</span>
        <span>Edit</span>
      </div>
      <h1 class="trip-page-title trip-title-centered" style="margin-bottom:1rem;">Edit review</h1>

      <section class="review-form-container review-form-container--trip">
        <?php if ($error_message !== ''): ?>
          <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($base . '/tourist/edit-review/' . $rid, ENT_QUOTES, 'UTF-8'); ?>" class="review-form" id="reviewForm">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">

          <div class="form-row">
            <div class="form-group">
              <label for="name">Your Name <span class="required">*</span></label>
              <input type="text" id="name" name="name" value="<?php echo $name; ?>" required>
            </div>
            <div class="form-group">
              <label for="email">Email <span class="required">*</span></label>
              <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label>Your Rating <span class="required">*</span></label>
            <div class="star-rating">
              <?php for ($s = 5; $s >= 1; $s--): ?>
                <input type="radio" id="star<?php echo $s; ?>" name="rating" value="<?php echo $s; ?>" <?php echo $ratingVal === $s ? 'checked' : ''; ?> required>
                <label for="star<?php echo $s; ?>">★</label>
              <?php endfor; ?>
            </div>
          </div>

          <div class="form-group">
            <label for="review">Your review <span class="required">*</span></label>
            <textarea id="review" name="review" rows="6" required><?php echo htmlspecialchars($rtext, ENT_QUOTES, 'UTF-8'); ?></textarea>
          </div>

          <div class="form-actions">
            <a href="<?php echo htmlspecialchars($base . '/tourist/my-reviews', ENT_QUOTES, 'UTF-8'); ?>" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Save changes</button>
          </div>
        </form>
      </section>
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
  });
  </script>
  <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
