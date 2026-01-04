<?php
// views/tourist/add_diary_entry.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
    header('Location: /CeylonGo/public/login');
    exit;
}

$error_message = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Add Diary Entry</title>
  <link rel="stylesheet" href="../../public/css/common.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
  <link rel="stylesheet" href="../../public/css/tourist/diary.css">
</head>
<body class="bg-app">
  <!-- Navbar include -->
  <?php include 'header.php'; ?>

  <div class="diary-container">
    <div class="diary-header">
      <h1>Add New Diary Entry</h1>
      <p>Share your travel experiences</p>
    </div>

    <?php if ($error_message): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <form method="POST" action="/CeylonGo/public/tourist/add-diary-entry" enctype="multipart/form-data" class="diary-form">
      <div class="form-group">
        <label for="title">Title *</label>
        <input type="text" id="title" name="title" required placeholder="Give your entry a title">
      </div>

      <div class="form-group">
        <label for="location">Location</label>
        <input type="text" id="location" name="location" placeholder="Where did you visit?">
      </div>

      <div class="form-group">
        <label for="entry_date">Date *</label>
        <input type="date" id="entry_date" name="entry_date" value="<?= date('Y-m-d') ?>" required>
      </div>

      <div class="form-group">
        <label for="content">Your Story *</label>
        <textarea id="content" name="content" rows="10" required placeholder="Write about your experience..."></textarea>
      </div>

      <div class="form-group">
        <label for="images">Add Photos</label>
        <input type="file" id="images" name="images[]" multiple accept="image/*">
        <small>You can select multiple images (JPG, PNG, GIF, WEBP)</small>
      </div>

      <div class="form-group">
        <label class="checkbox-label">
          <input type="checkbox" name="is_public" value="1">
          <span>Make this entry public (others can see it)</span>
        </label>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save Entry</button>
        <a href="/CeylonGo/public/tourist/my-diary" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>

  <!-- Footer include -->
  <?php include 'footer.php'; ?>
</body>
</html>


