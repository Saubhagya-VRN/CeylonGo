<?php
// views/tourist/add_review.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'tourist';

// Get user name and email for logged-in tourists
$user_name = '';
$user_email = '';
if ($is_logged_in) {
    // Get email from session
    $user_email = $_SESSION['user_email'] ?? '';
    
    // Get name from session if available, otherwise fetch from database
    if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
        $user_name = $_SESSION['user_name'];
    } else {
        // Fetch name from database
        try {
            require_once '../../config/database.php';
            $stmt = $conn->prepare("SELECT first_name, last_name FROM tourist_users WHERE id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $user_name = trim($row['first_name'] . ' ' . $row['last_name']);
                // Store in session for future use
                $_SESSION['user_name'] = $user_name;
            }
            $stmt->close();
            $conn->close();
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }
}

// Generate CSRF token
if ($is_logged_in && empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success_message = '';
$error_message = '';

// Initialize form variables
$name = '';
$email = '';
$rating = 0;
$review_text = '';
$destination = '';

// If user is logged in, pre-populate with their info
if ($is_logged_in && $_SERVER["REQUEST_METHOD"] != "POST") {
    $name = $user_name;
    $email = $user_email;
}

// Process review submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user is logged in
    if (!$is_logged_in) {
        $error_message = "Please login to submit a review. <a href='../login.php' style='color: #2c5530; font-weight: bold; text-decoration: underline;'>Login here</a>";
    }
    // Verify CSRF token
    elseif (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message = "Invalid request. Please try again.";
    } else {
        // Validate and sanitize input
        $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
        $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
        $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
        $review_text = isset($_POST['review']) ? htmlspecialchars(trim($_POST['review'])) : '';
        $destination = isset($_POST['destination']) ? htmlspecialchars(trim($_POST['destination'])) : '';
        
        // Validation
        if (empty($name) || empty($email) || empty($review_text)) {
            $error_message = "Please fill in all required fields.";
        } elseif ($rating < 1 || $rating > 5) {
            $error_message = "Please select a valid rating (1-5 stars).";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Please enter a valid email address.";
        } else {
            // Save to database
            try {
                require_once '../../config/database.php';
                
                $stmt = $conn->prepare("INSERT INTO reviews (user_id, name, email, rating, review_text, destination, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
                $stmt->bind_param("ississ", $_SESSION['user_id'], $name, $email, $rating, $review_text, $destination);
                
                if ($stmt->execute()) {
                    $success_message = "Thank you for your review! It will be published after moderation.";
                    // Clear form
                    $name = $email = $review_text = $destination = '';
                    $rating = 0;
                } else {
                    $error_message = "An error occurred while submitting your review. Please try again.";
                }
                
                $stmt->close();
                $conn->close();
                
            } catch (Exception $e) {
                $error_message = "An error occurred while submitting your review. Please try again.";
                error_log($e->getMessage());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Share Your Experience - Ceylon Go</title>
  <link rel="stylesheet" href="../../public/css/common.css">
  <link rel="stylesheet" href="../../public/css/tourist/add_review.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
</head>
<body class="bg-app">
  <!-- Navbar include -->
  <?php include 'header.php'; ?>

  <section class="review-form-container">
    <div class="review-header">
      <h1>Share Your Experience</h1>
      <p>Help others discover the beauty of Sri Lanka through your experiences</p>
    </div>

    <?php if ($success_message): ?>
      <div class="alert alert-success"><?= $success_message ?></div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
      <div class="alert alert-error"><?= $error_message ?></div>
    <?php endif; ?>

    <?php if (!$is_logged_in): ?>
      <div class="alert alert-info">
        <strong>👋 Welcome Guest!</strong> Please 
        <a href="../login.php" style="color: #2c5530; font-weight: bold; text-decoration: underline;">login</a> 
        or 
        <a href="../register.php" style="color: #2c5530; font-weight: bold; text-decoration: underline;">register</a> 
        to submit a review.
      </div>
    <?php endif; ?>

    <form method="POST" action="" id="reviewForm" class="review-form">
      <?php if ($is_logged_in): ?>
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
      <?php endif; ?>

      <div class="form-row">
        <div class="form-group">
          <label for="name">Your Name <span class="required">*</span></label>
          <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" placeholder="Enter your full name" required <?= !$is_logged_in ? 'disabled' : '' ?>>
        </div>

        <div class="form-group">
          <label for="email">Email Address <span class="required">*</span></label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="your.email@example.com" required <?= !$is_logged_in ? 'disabled' : '' ?>>
        </div>
      </div>

      <div class="form-group">
        <label for="destination">Destination Visited</label>
        <select id="destination" name="destination" <?= !$is_logged_in ? 'disabled' : '' ?>>
          <option value="">Select a destination</option>
          <option value="Colombo" <?= isset($destination) && $destination === 'Colombo' ? 'selected' : '' ?>>Colombo</option>
          <option value="Kandy" <?= isset($destination) && $destination === 'Kandy' ? 'selected' : '' ?>>Kandy</option>
          <option value="Galle" <?= isset($destination) && $destination === 'Galle' ? 'selected' : '' ?>>Galle</option>
          <option value="Nuwara Eliya" <?= isset($destination) && $destination === 'Nuwara Eliya' ? 'selected' : '' ?>>Nuwara Eliya</option>
          <option value="Sigiriya" <?= isset($destination) && $destination === 'Sigiriya' ? 'selected' : '' ?>>Sigiriya</option>
          <option value="Unawatuna" <?= isset($destination) && $destination === 'Unawatuna' ? 'selected' : '' ?>>Unawatuna</option>
          <option value="Ella" <?= isset($destination) && $destination === 'Ella' ? 'selected' : '' ?>>Ella</option>
          <option value="Mirissa" <?= isset($destination) && $destination === 'Mirissa' ? 'selected' : '' ?>>Mirissa</option>
          <option value="Bentota" <?= isset($destination) && $destination === 'Bentota' ? 'selected' : '' ?>>Bentota</option>
          <option value="Anuradhapura" <?= isset($destination) && $destination === 'Anuradhapura' ? 'selected' : '' ?>>Anuradhapura</option>
          <option value="Other" <?= isset($destination) && $destination === 'Other' ? 'selected' : '' ?>>Other</option>
        </select>
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
        <a href="tourist_dashboard.php" class="btn-secondary">Cancel</a>
        <?php if ($is_logged_in): ?>
          <button type="submit" class="btn-primary">Submit Review</button>
        <?php else: ?>
          <a href="../login.php" class="btn-primary">Login to Submit</a>
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

  <!-- Footer include -->
  <?php include 'footer.php'; ?>

  <script>
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
