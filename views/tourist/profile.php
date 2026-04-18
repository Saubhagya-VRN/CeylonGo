<?php
// views/tourist/profile.php — account details (controller loads $tourist)
$tourist = isset($tourist) && is_array($tourist) ? $tourist : array();
$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error_message = isset($_GET['error']) ? $_GET['error'] : (isset($_SESSION['error']) ? $_SESSION['error'] : '');
unset($_SESSION['success'], $_SESSION['error']);

$scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
$asset_base = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
if ($asset_base === '' || $asset_base === '/') {
    $asset_base = '/CeylonGo/public';
}

$user_name = isset($_SESSION['user_name']) ? trim((string) $_SESSION['user_name']) : 'Tourist';
$user_email_sidebar = isset($_SESSION['user_email']) ? trim((string) $_SESSION['user_email']) : '';
if ($user_email_sidebar === '' && !empty($tourist['email'])) {
    $user_email_sidebar = trim((string) $tourist['email']);
}
$avatar_initial = $user_name !== '' ? strtoupper(substr($user_name, 0, 1)) : 'T';
$trip_sidebar_active = 'profile';

$fn = htmlspecialchars((string) (isset($tourist['first_name']) ? $tourist['first_name'] : ''), ENT_QUOTES, 'UTF-8');
$ln = htmlspecialchars((string) (isset($tourist['last_name']) ? $tourist['last_name'] : ''), ENT_QUOTES, 'UTF-8');
$contact = htmlspecialchars((string) (isset($tourist['contact_number']) ? $tourist['contact_number'] : ''), ENT_QUOTES, 'UTF-8');
$em = htmlspecialchars((string) (isset($tourist['email']) ? $tourist['email'] : ''), ENT_QUOTES, 'UTF-8');
$form_action = htmlspecialchars($asset_base . '/tourist/profile', ENT_QUOTES, 'UTF-8');
$full_page = isset($_GET['full']) && (string) $_GET['full'] === '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile — Ceylon Go</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base, ENT_QUOTES, 'UTF-8'); ?>/css/common.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/navbar.css">
  <?php if (!$full_page): ?>
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/trip_layout.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/sidebar.css">
  <?php endif; ?>
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/footer.css">
  <?php if (!$full_page): ?>
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/trip.css">
  <?php endif; ?>
  <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/profile.css">
</head>
<body class="<?php echo $full_page ? 'profile-full-page' : 'trip-page-body'; ?>">
  <?php include __DIR__ . '/header.php'; ?>

  <?php if (!$full_page): ?>
  <div class="sidebar-overlay trip-overlay" id="tripSidebarOverlay"></div>

  <div class="trip-page-wrapper">
    <?php include __DIR__ . '/_trip_sidebar.php'; ?>

    <main class="trip-main-content profile-trip-main">
      <button type="button" class="hamburger-btn trip-hamburger" id="tripHamburgerBtn" aria-label="Toggle menu"><span></span><span></span><span></span></button>
      <div class="trip-breadcrumbs">
        <a href="<?php echo htmlspecialchars($asset_base . '/tourist/dashboard-side', ENT_QUOTES, 'UTF-8'); ?>"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span>&gt;</span>
        <span>Profile</span>
      </div>
  <?php else: ?>
    <main class="profile-trip-main" style="max-width: 1100px; margin: 0 auto; padding: 28px 18px;">
  <?php endif; ?>

      <div class="trip-header-row" aria-label="Profile">
        <div class="trip-stepper-prev" aria-hidden="true"></div>
        <h1 class="trip-page-title trip-title-centered">
          <i class="fa-regular fa-user" aria-hidden="true"></i> My profile
        </h1>
        <div class="trip-stepper-next" aria-hidden="true"></div>
      </div>

      <div class="profile-container">
        <div class="profile-topcard" aria-label="Profile summary">
          <div class="profile-avatar-wrap">
            <div class="profile-avatar" aria-hidden="true"><?php echo htmlspecialchars($avatar_initial, ENT_QUOTES, 'UTF-8'); ?></div>
            <button type="button" class="profile-avatar-edit" title="Photo upload not available yet" aria-label="Edit profile photo" disabled>
              <i class="fa-solid fa-camera"></i>
            </button>
          </div>
          <div class="profile-topcard-text">
            <div class="profile-name"><?php echo htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="profile-subtitle">Update your name, contact details, and password.</div>
          </div>
        </div>

        <?php if ($success_message !== ''): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if ($error_message !== ''): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <div class="profile-content">
          <form method="post" action="<?php echo $form_action; ?>" class="profile-form" autocomplete="off">
            <?php if ($full_page): ?>
            <input type="hidden" name="full" value="1">
            <?php endif; ?>
            <div class="form-section">
              <h2><i class="fa-solid fa-gear"></i> Personal information</h2>

              <div class="profile-grid">
                <div class="profile-field">
                  <div class="profile-field-label"><i class="fa-regular fa-user"></i> Full name</div>
                  <div class="profile-field-inputs">
                    <input type="text" id="first_name" name="first_name" value="<?php echo $fn; ?>" required maxlength="120" placeholder="First name">
                    <input type="text" id="last_name" name="last_name" value="<?php echo $ln; ?>" required maxlength="120" placeholder="Last name">
                  </div>
                </div>

                <div class="profile-field">
                  <div class="profile-field-label"><i class="fa-regular fa-envelope"></i> Email</div>
                  <div class="profile-field-inputs">
                    <div class="profile-input-icon-wrap">
                      <span class="profile-input-icon" aria-hidden="true"><i class="fa-regular fa-envelope"></i></span>
                      <input type="email" id="email" name="email" value="<?php echo $em; ?>" required maxlength="190" placeholder="Email address" class="profile-input--icon">
                    </div>
                  </div>
                </div>

                <div class="profile-field">
                  <div class="profile-field-label"><i class="fa-solid fa-phone"></i> Phone</div>
                  <div class="profile-field-inputs">
                    <div class="profile-input-icon-wrap">
                      <span class="profile-input-icon" aria-hidden="true"><i class="fa-solid fa-phone"></i></span>
                      <input type="text" id="contact_number" name="contact_number" value="<?php echo $contact; ?>" maxlength="30" placeholder="e.g. +94 77 123 4567" class="profile-input--icon">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-section">
              <h2><i class="fa-solid fa-lock"></i> Change password</h2>
              <p class="form-note">Leave new fields blank to keep your current password. To set a new password, enter your current password first.</p>
              <div class="profile-grid">
                <div class="profile-field">
                  <div class="profile-field-label"><i class="fa-solid fa-unlock-keyhole"></i> Current password</div>
                  <div class="profile-field-inputs">
                    <div class="profile-input-icon-wrap">
                      <span class="profile-input-icon" aria-hidden="true"><i class="fa-solid fa-unlock-keyhole"></i></span>
                      <input type="password" id="current_password" name="current_password" autocomplete="current-password" placeholder="Current Password" class="profile-input--icon">
                    </div>
                  </div>
                </div>
                <div class="profile-field">
                  <div class="profile-field-label"><i class="fa-solid fa-key"></i> New password</div>
                  <div class="profile-field-inputs">
                    <div class="profile-input-icon-wrap">
                      <span class="profile-input-icon" aria-hidden="true"><i class="fa-solid fa-key"></i></span>
                      <input type="password" id="password" name="password" autocomplete="new-password" minlength="6" placeholder="New password" class="profile-input--icon">
                    </div>
                  </div>
                </div>
                <div class="profile-field">
                  <div class="profile-field-label"><i class="fa-solid fa-check"></i> Confirm</div>
                  <div class="profile-field-inputs">
                    <div class="profile-input-icon-wrap">
                      <span class="profile-input-icon" aria-hidden="true"><i class="fa-solid fa-check"></i></span>
                      <input type="password" id="password_confirm" name="password_confirm" autocomplete="new-password" minlength="6" placeholder="Confirm password" class="profile-input--icon">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
          </form>
        </div>
      </div>
    </main>
  <?php if (!$full_page): ?>
  </div>
  <?php endif; ?>

  <?php include __DIR__ . '/footer.php'; ?>

  <?php if (!$full_page): ?>
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
