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
$csrf = isset($_SESSION['csrf_token']) ? (string) $_SESSION['csrf_token'] : '';
$iid = (int) (isset($inquiry['id']) ? $inquiry['id'] : 0);
$subj = (string) (isset($inquiry['subject']) ? $inquiry['subject'] : '');
$msg = (string) (isset($inquiry['message']) ? $inquiry['message'] : '');
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
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/trip.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/add_review.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base, ENT_QUOTES, 'UTF-8'); ?>/css/tourist/footer.css">
</head>
<body class="trip-page-body my-inquiries-page">
  <?php include __DIR__ . '/header.php'; ?>

  <main class="trip-main-content reviews-trip-main">
    <div class="my-inquiries-page-inner">
      <div class="trip-header-row" aria-label="Edit inquiry">
        <div class="trip-stepper-prev">
          <a href="<?php echo htmlspecialchars($base . '/tourist/my-inquiries', ENT_QUOTES, 'UTF-8'); ?>" class="btn-secondary review-history-btn"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back</a>
        </div>
        <h1 class="trip-page-title trip-title-centered"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> Edit inquiry</h1>
        <div class="trip-stepper-next"></div>
      </div>

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
    </div>
  </main>
  <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
