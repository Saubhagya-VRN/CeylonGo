<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
}
$base = rtrim((string) BASE_URL, '/');
$inquiries = isset($inquiries) && is_array($inquiries) ? $inquiries : [];
$flash_ok = isset($flash_ok) ? (string) $flash_ok : '';
$flash_err = isset($flash_err) ? (string) $flash_err : '';
$csrf = isset($_SESSION['csrf_token']) ? (string) $_SESSION['csrf_token'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My inquiries - Ceylon Go</title>
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
      <div class="trip-breadcrumbs">
        <a href="<?php echo htmlspecialchars($base . '/tourist/dashboard-side', ENT_QUOTES, 'UTF-8'); ?>"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span>&gt;</span>
        <span>My inquiries</span>
      </div>
      <div class="trip-header-row" aria-label="My inquiries">
        <div class="trip-stepper-prev">
          <a href="<?php echo htmlspecialchars($base . '/tourist/dashboard#inquiry', ENT_QUOTES, 'UTF-8'); ?>" class="btn-secondary review-history-btn"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back</a>
        </div>
        <h1 class="trip-page-title trip-title-centered"><i class="fa-solid fa-circle-question" aria-hidden="true"></i> My inquiries</h1>
        <div class="trip-stepper-next">
          <a href="<?php echo htmlspecialchars($base . '/tourist/dashboard#inquiry', ENT_QUOTES, 'UTF-8'); ?>" class="btn-secondary review-history-btn"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> Submit a new inquiry</a>
        </div>
      </div>

      <section class="review-form-container review-form-container--trip my-inquiries-section">
        <?php if ($flash_ok !== ''): ?>
          <div class="alert alert-info" role="status"><?php echo htmlspecialchars($flash_ok); ?></div>
        <?php endif; ?>
        <?php if ($flash_err !== ''): ?>
          <div class="alert alert-error" role="alert"><?php echo htmlspecialchars($flash_err); ?></div>
        <?php endif; ?>

        <?php if (empty($inquiries)): ?>
          <p class="my-reviews-empty">No inquiries linked to your account yet.</p>
        <?php else: ?>
          <div class="my-reviews-table-scroll">
            <table class="my-reviews-table">
              <thead>
                <tr>
                  <th class="my-reviews-col-date" scope="col">Date</th>
                  <th class="my-inquiries-col-subject" scope="col">Subject</th>
                  <th class="my-reviews-col-status" scope="col">Status</th>
                  <th class="my-reviews-col-excerpt" scope="col">Message</th>
                  <th class="my-reviews-col-actions" scope="col">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($inquiries as $inq): ?>
                  <?php
                    $st = isset($inq['status']) ? (string) $inq['status'] : 'pending';
                    $pending = ($st === 'pending');
                    $iid = (int) (isset($inq['id']) ? $inq['id'] : 0);
                    $msg = isset($inq['message']) ? trim((string) $inq['message']) : '';
                    $msgEx = strlen($msg) > 100 ? substr($msg, 0, 100) . '…' : $msg;
                    $adminReply = isset($inq['admin_reply']) ? trim((string) $inq['admin_reply']) : '';
                    $stSlug = preg_replace('/[^a-z0-9_-]/', '', strtolower($st));
                    if ($stSlug === '') {
                        $stSlug = 'pending';
                    }
                    if (!in_array($stSlug, array('pending', 'replied', 'approved', 'rejected'), true)) {
                        $stSlug = 'default';
                    }
                    if ($stSlug === 'replied') {
                        $stClass = 'my-reviews-badge--replied';
                    } elseif ($stSlug === 'approved') {
                        $stClass = 'my-reviews-badge--approved';
                    } elseif ($stSlug === 'rejected') {
                        $stClass = 'my-reviews-badge--rejected';
                    } elseif ($stSlug === 'pending') {
                        $stClass = 'my-reviews-badge--pending';
                    } else {
                        $stClass = 'my-reviews-badge--default';
                    }
                  ?>
                  <tr>
                    <td class="my-reviews-col-date"><?php echo htmlspecialchars(isset($inq['created_at']) ? substr((string) $inq['created_at'], 0, 16) : ''); ?></td>
                    <td class="my-inquiries-col-subject"><?php echo htmlspecialchars((string) (isset($inq['subject']) ? $inq['subject'] : '')); ?></td>
                    <td class="my-reviews-col-status"><span class="my-reviews-badge <?php echo htmlspecialchars($stClass, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($st); ?></span></td>
                    <td class="my-reviews-col-excerpt">
                      <?php echo nl2br(htmlspecialchars($msgEx)); ?>
                      <?php if ($stSlug === 'replied' && $adminReply !== ''): ?>
                        <div class="my-inquiries-admin-reply">
                          <strong>Admin reply:</strong>
                          <div><?php echo nl2br(htmlspecialchars($adminReply)); ?></div>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td class="my-reviews-col-actions">
                      <?php if ($pending): ?>
                        <div class="my-reviews-actions">
                          <a class="my-reviews-btn-edit" href="<?php echo htmlspecialchars($base . '/tourist/edit-inquiry/' . $iid, ENT_QUOTES, 'UTF-8'); ?>">Edit</a>
                          <form method="post" action="<?php echo htmlspecialchars($base . '/tourist/delete-inquiry', ENT_QUOTES, 'UTF-8'); ?>" class="my-reviews-delete-form" onsubmit="return confirm('Delete this inquiry?');">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="inquiry_id" value="<?php echo (int) $iid; ?>">
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
          <p class="my-reviews-footnote">Edit or delete only while status is <strong>pending</strong> (before an admin reply).</p>
        <?php endif; ?>
      </section>
    </div>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
