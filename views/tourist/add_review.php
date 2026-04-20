<?php
/**
 * Add review — presentation only. POST is handled by TouristController@addReview + models/Review.php.
 *
 * Expected variables (from controller via view()):
 * is_logged_in, user_name, user_email, error_message, success_modal_message, reviewer_name, email, rating, review_text
 * (Do not use key "name" — it collides with view()'s $name path parameter after extract().)
 */
$is_logged_in = !empty($is_logged_in);
$user_name = isset($user_name) ? (string) $user_name : '';
$user_email = isset($user_email) ? (string) $user_email : '';
$error_message = isset($error_message) ? (string) $error_message : '';
$success_modal_message = isset($success_modal_message) ? (string) $success_modal_message : '';
$reviewer_name = isset($reviewer_name) ? (string) $reviewer_name : '';
$email = isset($email) ? (string) $email : '';
$rating = isset($rating) ? (int) $rating : 0;
$review_text = isset($review_text) ? (string) $review_text : '';

$login_base = defined('BASE_URL') ? rtrim((string) BASE_URL, '/') : '/CeylonGo/public';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Share Your Experience - Ceylon Go</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../../public/css/common.css">
  <link rel="stylesheet" href="../../public/css/tourist/add_review.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
  <?php if ($is_logged_in): ?>
  <link rel="stylesheet" href="../../public/css/tourist/trip_layout.css">
  <link rel="stylesheet" href="../../public/css/tourist/sidebar.css">
  <link rel="stylesheet" href="../../public/css/tourist/trip.css">
  <?php endif; ?>
</head>
<body class="<?php echo $is_logged_in ? 'trip-page-body ' : ''; ?>bg-app">
  <!-- Navbar include -->
  <?php include 'header.php'; ?>

  <?php if ($is_logged_in):
    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    $asset_base = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    if ($asset_base === '' || $asset_base === '/') { $asset_base = '/CeylonGo/public'; }
    $tourist_email = isset($_SESSION['user_email']) ? trim((string) $_SESSION['user_email']) : '';
    $user_email_sidebar = $tourist_email;
    $avatar_initial = $user_name !== '' ? strtoupper(substr($user_name, 0, 1)) : 'T';
    $trip_sidebar_active = 'reviews';
  ?>
  <div class="sidebar-overlay trip-overlay" id="tripSidebarOverlay"></div>
  <div class="trip-page-wrapper">
    <?php include __DIR__ . '/_trip_sidebar.php'; ?>
    <main class="trip-main-content reviews-trip-main">
      <button type="button" class="hamburger-btn trip-hamburger" id="tripHamburgerBtn" aria-label="Toggle menu"><span></span><span></span><span></span></button>
      <div class="trip-breadcrumbs">
        <a href="<?php echo htmlspecialchars($asset_base . '/tourist/dashboard-side', ENT_QUOTES, 'UTF-8'); ?>"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span>&gt;</span>
        <span>Write a review</span>
      </div>
      <div class="trip-header-row" aria-label="Reviews">
        <div class="trip-stepper-prev" aria-hidden="true"></div>
        <h1 class="trip-page-title trip-title-centered"><i class="fa-solid fa-star" aria-hidden="true"></i> Reviews</h1>
        <div class="trip-stepper-next">
          <a href="<?php echo htmlspecialchars($asset_base . '/tourist/my-reviews', ENT_QUOTES, 'UTF-8'); ?>" class="btn-secondary review-history-btn"><i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i> View review history</a>
        </div>
      </div>
  <?php endif; ?>

  <section class="review-form-container<?php echo $is_logged_in ? ' review-form-container--trip' : ''; ?>">
    <?php if (!$is_logged_in): ?>
      <div class="review-header">
        <h1>Share Your Experience</h1>
        <p>Help others discover the beauty of Sri Lanka through your experiences</p>
      </div>
    <?php endif; ?>

    <div id="reviewAjaxError" class="alert alert-error" style="display:none;" role="alert"></div>

    <?php if ($error_message): ?>
      <div class="alert alert-error"><?= $error_message ?></div>
    <?php endif; ?>

    <?php if (!$is_logged_in): ?>
      <div class="alert alert-info">
        <strong>👋 Welcome Guest!</strong> Please 
        <a href="<?php echo htmlspecialchars($login_base . '/login', ENT_QUOTES, 'UTF-8'); ?>" style="color: #2c5530; font-weight: bold; text-decoration: underline;">login</a> 
        or 
        <a href="<?php echo htmlspecialchars($login_base . '/register', ENT_QUOTES, 'UTF-8'); ?>" style="color: #2c5530; font-weight: bold; text-decoration: underline;">register</a> 
        to submit a review.
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= htmlspecialchars(defined('BASE_URL') ? (BASE_URL . '/tourist/add-review') : '/CeylonGo/public/tourist/add-review') ?>" id="reviewForm" class="review-form">
      <?php if ($is_logged_in): ?>
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
      <?php endif; ?>

      <div class="form-row">
        <div class="form-group">
          <label for="name">Your Name <span class="required">*</span></label>
          <input type="text" id="name" name="name" value="<?= htmlspecialchars($reviewer_name) ?>" placeholder="Enter your full name" required <?= !$is_logged_in ? 'disabled' : '' ?>>
        </div>

        <div class="form-group">
          <label for="email">Email Address <span class="required">*</span></label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="your.email@example.com" required <?= !$is_logged_in ? 'disabled' : '' ?>>
        </div>
      </div>

      <div class="form-group">
        <label>Your Rating <span class="required">*</span></label>
        <div class="star-rating">
          <input type="radio" id="star5" name="rating" value="5" <?= !$is_logged_in ? 'disabled' : '' ?> required>
          <label for="star5" title="5 stars">★</label>
          
          <input type="radio" id="star4" name="rating" value="4" <?= !$is_logged_in ? 'disabled' : '' ?>>
          <label for="star4" title="4 stars">★</label>
          
          <input type="radio" id="star3" name="rating" value="3" <?= !$is_logged_in ? 'disabled' : '' ?>>
          <label for="star3" title="3 stars">★</label>
          
          <input type="radio" id="star2" name="rating" value="2" <?= !$is_logged_in ? 'disabled' : '' ?>>
          <label for="star2" title="2 stars">★</label>
          
          <input type="radio" id="star1" name="rating" value="1" <?= !$is_logged_in ? 'disabled' : '' ?>>
          <label for="star1" title="1 star">★</label>
        </div>
        <p class="rating-hint">Click on the stars to rate your experience</p>
      </div>

      <div class="form-group">
        <label for="review">Your Review <span class="required">*</span></label>
        <textarea id="review" name="review" rows="6" placeholder="Share your experience with Ceylon Go... What did you enjoy most? What made your trip memorable?" required <?= !$is_logged_in ? 'disabled' : '' ?>><?= isset($review_text) ? $review_text : '' ?></textarea>
        <p class="char-count"><span id="charCount">0</span> / 500 characters</p>
      </div>

      <div class="form-actions">
        <a href="javascript:history.back()" class="btn-secondary">Cancel</a>
        <?php if ($is_logged_in): ?>
          <button type="submit" class="btn-primary">Submit Review</button>
        <?php else: ?>
          <a href="../login" class="btn-primary">Login to Submit</a>
        <?php endif; ?>
      </div>
    </form>

    <div class="review-guidelines">
      <h3>Review Guidelines</h3>
      <ul>
        <li>✓ Be honest and specific about your experience</li>
        <li>✓ Mention highlights of your trip</li>
        <li>✓ Include details about services, guides, or accommodations</li>
        <li>✓ Keep it respectful and constructive</li>
        <li>✓ Reviews are moderated and will be published within 24-48 hours</li>
      </ul>
    </div>
  </section>

  <?php if ($is_logged_in): ?>
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

  <!-- Footer include -->
  <?php include 'footer.php'; ?>

  <?php if ($success_modal_message !== ''): ?>
  <div id="reviewSuccessModal" class="review-success-modal" role="dialog" aria-modal="true" aria-labelledby="reviewSuccessModalTitle">
    <div class="review-success-modal__backdrop js-review-success-close" tabindex="-1"></div>
    <div class="review-success-modal__box">
      <h2 id="reviewSuccessModalTitle" class="review-success-modal__title">Thank you</h2>
      <p class="review-success-modal__text"><?= htmlspecialchars($success_modal_message) ?></p>
      <button type="button" class="review-success-modal__ok btn-primary js-review-success-ok">OK</button>
    </div>
  </div>
  <?php endif; ?>

  <script>
    <?php if ($is_logged_in): ?>
    (function () {
      var form = document.getElementById('reviewForm');
      var errBox = document.getElementById('reviewAjaxError');
      var prefName = <?= json_encode($user_name, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
      var prefEmail = <?= json_encode($user_email, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

      function showReviewSubmittedModal(message) {
        var wrap = document.createElement('div');
        wrap.className = 'review-success-modal';
        wrap.setAttribute('role', 'dialog');
        wrap.setAttribute('aria-modal', 'true');
        wrap.innerHTML =
          '<div class="review-success-modal__backdrop js-ajax-success-close" tabindex="-1"></div>' +
          '<div class="review-success-modal__box">' +
          '<h2 class="review-success-modal__title">Review submitted</h2>' +
          '<p class="review-success-modal__text"></p>' +
          '<button type="button" class="review-success-modal__ok btn-primary js-ajax-success-ok">OK</button>' +
          '</div>';
        wrap.querySelector('.review-success-modal__text').textContent = message;
        document.body.appendChild(wrap);
        document.body.style.overflow = 'hidden';
        function closeOnly() {
          wrap.remove();
          document.body.style.overflow = '';
        }
        function closeAndGoBack() {
          closeOnly();
          window.history.back();
        }
        wrap.querySelector('.review-success-modal__backdrop').addEventListener('click', closeOnly);
        wrap.querySelector('.js-ajax-success-ok').addEventListener('click', closeAndGoBack);
        document.addEventListener('keydown', function esc(e) {
          if (e.key === 'Escape') {
            closeOnly();
            document.removeEventListener('keydown', esc);
          }
        });
      }

      if (!form) return;
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (errBox) {
          errBox.style.display = 'none';
          errBox.textContent = '';
        }
        var fd = new FormData(form);
        fd.set('ajax', '1');
        var actionUrl = form.getAttribute('action');
        if (!actionUrl || actionUrl === '') {
          actionUrl = window.location.pathname + window.location.search;
        }
        fetch(actionUrl, {
          method: 'POST',
          body: fd,
          credentials: 'include',
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
          .then(function (r) {
            return r.text().then(function (text) {
              var data;
              try {
                data = JSON.parse(text);
              } catch (e) {
                console.error('add-review: expected JSON, got:', text.substring(0, 600));
                throw new Error('Server did not return JSON (status ' + r.status + ').');
              }
              return data;
            });
          })
          .then(function (data) {
            if (data.ok) {
              showReviewSubmittedModal(data.message || 'Review submitted successfully.');
              form.reset();
              var nameEl = document.getElementById('name');
              var emailEl = document.getElementById('email');
              if (nameEl) nameEl.value = prefName;
              if (emailEl) emailEl.value = prefEmail;
              var ta = document.getElementById('review');
              var cc = document.getElementById('charCount');
              if (ta && cc) cc.textContent = String(ta.value.length);
            } else {
              if (errBox) {
                errBox.textContent = data.message || 'Something went wrong.';
                errBox.style.display = 'block';
                errBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
              } else {
                alert(data.message || 'Something went wrong.');
              }
            }
          })
          .catch(function (err) {
            var msg = (err && err.message) ? err.message : 'Could not submit. Check your connection and try again.';
            if (errBox) {
              errBox.textContent = msg;
              errBox.style.display = 'block';
            } else {
              alert(msg);
            }
          });
      });
    })();
    <?php endif; ?>

    // Character counter
    const reviewTextarea = document.getElementById('review');
    const charCount = document.getElementById('charCount');
    
    if (reviewTextarea && charCount) {
      reviewTextarea.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = count;
        
        if (count > 500) {
          charCount.style.color = '#721c24';
        } else {
          charCount.style.color = '#5a6b5a';
        }
      });
      
      // Set maxlength
      reviewTextarea.setAttribute('maxlength', '500');
    }
    
    (function () {
      var modal = document.getElementById('reviewSuccessModal');
      if (!modal) return;
      function closeOnly() {
        modal.remove();
        document.body.style.overflow = '';
      }
      function closeAndGoBack() {
        closeOnly();
        window.history.back();
      }
      var backdrop = modal.querySelector('.js-review-success-close');
      if (backdrop) backdrop.addEventListener('click', closeOnly);
      var okBtn = modal.querySelector('.js-review-success-ok');
      if (okBtn) okBtn.addEventListener('click', closeAndGoBack);
      document.addEventListener('keydown', function esc(e) {
        if (e.key === 'Escape') {
          closeOnly();
          document.removeEventListener('keydown', esc);
        }
      });
      document.body.style.overflow = 'hidden';
    })();

    // Star rating hover effect
    const stars = document.querySelectorAll('.star-rating label');
    stars.forEach((star, index) => {
      star.addEventListener('mouseenter', function() {
        for (let i = stars.length - 1; i >= index; i--) {
          stars[i].style.color = '#ffc107';
        }
      });
      
      star.addEventListener('mouseleave', function() {
        stars.forEach(s => s.style.color = '');
      });
    });
  </script>
</body>
</html>
