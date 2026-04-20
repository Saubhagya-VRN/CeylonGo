<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
}
$base = rtrim((string) BASE_URL, '/');
$inquiry = isset($inquiry) && is_array($inquiry) ? $inquiry : null;
if (!$inquiry) {
    header('Location: ' . $base . '/tourist/my-inquiries');
    exit;
}
$error_message = isset($error_message) ? (string) $error_message : '';
$user_name = isset($_SESSION['user_name']) ? (string) $_SESSION['user_name'] : 'Tourist';
$tourist_email = isset($_SESSION['user_email']) ? trim((string) $_SESSION['user_email']) : '';
$user_email_sidebar = $tourist_email;
$avatar_initial = $user_name !== '' ? strtoupper(substr($user_name, 0, 1)) : 'T';
$asset_base = $base;
$trip_sidebar_active = '';
$csrf = isset($_SESSION['csrf_token']) ? (string) $_SESSION['csrf_token'] : '';
$iid = (int) ($inquiry['id'] ?? 0);
$subj = (string) ($inquiry['subject'] ?? '');
$msg = (string) ($inquiry['message'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit inquiry - Ceylon Go</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base, ENT_QUOTES, 'UTF-8'); ?>/css/common.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/navbar.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/trip_layout.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/sidebar.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/add_review.css">
</head>
<body class="trip-page-body bg-app">
  <?php include __DIR__ . '/header.php'; ?>
  <div class="sidebar-overlay trip-overlay" id="tripSidebarOverlay"></div>
  <div class="trip-page-wrapper">
    <?php include __DIR__ . '/_trip_sidebar.php'; ?>
    <main class="trip-main-content reviews-trip-main">
      <button type="button" class="hamburger-btn trip-hamburger" id="tripHamburgerBtn" aria-label="Toggle menu"><span></span><span></span><span></span></button>
      <div class="trip-breadcrumbs">
        <a href="<?php echo htmlspecialchars($base . '/tourist/my-inquiries', ENT_QUOTES, 'UTF-8'); ?>">My inquiries</a>
        <span>&gt;</span>
        <span>Edit</span>
      </div>
      <h1 class="trip-page-title trip-title-centered" style="margin-bottom:1rem;">Edit inquiry</h1>

      <section class="review-form-container review-form-container--trip">
        <?php if ($error_message !== ''): ?>
          <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($base . '/tourist/edit-inquiry/' . $iid, ENT_QUOTES, 'UTF-8'); ?>" class="review-form">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">

          <div class="form-group">
            <label for="subject">Subject <span class="required">*</span></label>
            <input type="text" id="subject" name="subject" maxlength="150" value="<?php echo htmlspecialchars($subj, ENT_QUOTES, 'UTF-8'); ?>" required>
          </div>

          <div class="form-group">
            <label for="message">Message <span class="required">*</span></label>
            <textarea id="message" name="message" rows="6" required><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></textarea>
          </div>

          <div class="form-actions">
            <a href="<?php echo htmlspecialchars($base . '/tourist/my-inquiries', ENT_QUOTES, 'UTF-8'); ?>" class="btn-secondary">Cancel</a>
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
