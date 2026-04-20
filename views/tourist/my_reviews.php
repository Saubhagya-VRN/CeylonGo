<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
}
$base = rtrim((string) BASE_URL, '/');
$reviews = isset($reviews) && is_array($reviews) ? $reviews : [];
$flash_ok = isset($flash_ok) ? (string) $flash_ok : '';
$flash_err = isset($flash_err) ? (string) $flash_err : '';
$user_name = isset($_SESSION['user_name']) ? (string) $_SESSION['user_name'] : 'Tourist';
$tourist_email = isset($_SESSION['user_email']) ? trim((string) $_SESSION['user_email']) : '';
$user_email_sidebar = $tourist_email;
$avatar_initial = $user_name !== '' ? strtoupper(substr($user_name, 0, 1)) : 'T';
$asset_base = $base;
$trip_sidebar_active = 'reviews';
$csrf = isset($_SESSION['csrf_token']) ? (string) $_SESSION['csrf_token'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Reviews - Ceylon Go</title>
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
        <a href="<?php echo htmlspecialchars($base . '/tourist/dashboard-side', ENT_QUOTES, 'UTF-8'); ?>"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span>&gt;</span>
        <span>My reviews</span>
      </div>
      <div class="trip-header-row" aria-label="My reviews">
        <div class="trip-stepper-prev">
          <a href="<?php echo htmlspecialchars($base . '/tourist/add-review', ENT_QUOTES, 'UTF-8'); ?>" class="btn-secondary review-history-btn"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back</a>
        </div>
        <h1 class="trip-page-title trip-title-centered"><i class="fa-solid fa-list" aria-hidden="true"></i> My reviews</h1>
        <div class="trip-stepper-next" aria-hidden="true"></div>
      </div>

      <section class="review-form-container review-form-container--trip my-reviews-section">
        <?php if ($flash_ok !== ''): ?>
          <div class="alert alert-info" role="status"><?php echo htmlspecialchars($flash_ok); ?></div>
        <?php endif; ?>
        <?php if ($flash_err !== ''): ?>
          <div class="alert alert-error" role="alert"><?php echo htmlspecialchars($flash_err); ?></div>
        <?php endif; ?>

        <?php if (empty($reviews)): ?>
          <p class="my-reviews-empty">You have not submitted any reviews yet.</p>
        <?php else: ?>
          <div class="my-reviews-table-scroll">
            <table class="my-reviews-table">
              <thead>
                <tr>
                  <th class="my-reviews-col-date" scope="col">Date</th>
                  <th class="my-reviews-col-rating" scope="col">Rating</th>
                  <th class="my-reviews-col-status" scope="col">Status</th>
                  <th class="my-reviews-col-excerpt" scope="col">Excerpt</th>
                  <th class="my-reviews-col-actions" scope="col">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($reviews as $r): ?>
                  <?php
                    $st = isset($r['status']) ? (string) $r['status'] : 'pending';
                    $pending = ($st === 'pending');
                    $rid = (int) ($r['id'] ?? 0);
                    $rawTxt = isset($r['review_text']) ? trim((string) $r['review_text']) : '';
                    $excerpt = function_exists('mb_substr') ? mb_substr($rawTxt, 0, 160, 'UTF-8') : substr($rawTxt, 0, 160);
                    if (strlen($rawTxt) > 160) {
                        $excerpt .= '…';
                    }
                    $stSlug = preg_replace('/[^a-z0-9_-]/', '', strtolower($st));
                    if ($stSlug === '') {
                        $stSlug = 'pending';
                    }
                    if (!in_array($stSlug, array('pending', 'approved', 'rejected'), true)) {
                        $stSlug = 'default';
                    }
                    $stClass = 'my-reviews-badge--' . $stSlug;
                  ?>
                  <tr>
                    <td class="my-reviews-col-date"><?php echo htmlspecialchars(isset($r['created_at']) ? substr((string) $r['created_at'], 0, 16) : ''); ?></td>
                    <td class="my-reviews-col-rating"><?php echo (int) ($r['rating'] ?? 0); ?> / 5</td>
                    <td class="my-reviews-col-status"><span class="my-reviews-badge <?php echo htmlspecialchars($stClass, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($st); ?></span></td>
                    <td class="my-reviews-col-excerpt"><?php echo htmlspecialchars($excerpt); ?></td>
                    <td class="my-reviews-col-actions">
                      <?php if ($pending): ?>
                        <div class="my-reviews-actions">
                          <a class="my-reviews-btn-edit" href="<?php echo htmlspecialchars($base . '/tourist/edit-review/' . $rid, ENT_QUOTES, 'UTF-8'); ?>">Edit</a>
                          <form method="post" action="<?php echo htmlspecialchars($base . '/tourist/delete-review', ENT_QUOTES, 'UTF-8'); ?>" class="my-reviews-delete-form" onsubmit="return confirm('Delete this review?');">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="review_id" value="<?php echo (int) $rid; ?>">
                            <button type="submit" class="my-reviews-btn-delete">Delete</button>
                          </form>
                        </div>
                      <?php else: ?>
                        <span class="my-reviews-actions-none">—</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <p class="my-reviews-footnote">You can edit or delete only while status is <strong>pending</strong> (before moderation).</p>
        <?php endif; ?>
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
    document.querySelectorAll('#tripSidebar ul li a').forEach(function (link) {
      link.addEventListener('click', function () { if (window.innerWidth <= 768) closeSidebar(); });
    });
  });
  </script>
  <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
