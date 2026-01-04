<?php
// views/tourist/edit_diary_entry.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
    header('Location: /CeylonGo/public/login');
    exit;
}

$entry = $entry ?? null;
$images = $images ?? [];
$error_message = $_GET['error'] ?? '';

if (!$entry) {
    header('Location: /CeylonGo/public/tourist/my-diary');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Edit Diary Entry</title>
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
      <h1>Edit Diary Entry</h1>
      <p>Update your travel experience</p>
    </div>

    <?php if ($error_message): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <form method="POST" action="/CeylonGo/public/tourist/edit-diary-entry/<?= $entry['id'] ?>" enctype="multipart/form-data" class="diary-form">
      <div class="form-group">
        <label for="title">Title *</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($entry['title']) ?>" required>
      </div>

      <div class="form-group">
        <label for="location">Location</label>
        <input type="text" id="location" name="location" value="<?= htmlspecialchars($entry['location'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label for="entry_date">Date *</label>
        <input type="date" id="entry_date" name="entry_date" value="<?= $entry['entry_date'] ?>" required>
      </div>

      <div class="form-group">
        <label for="content">Your Story *</label>
        <textarea id="content" name="content" rows="10" required><?= htmlspecialchars($entry['content']) ?></textarea>
      </div>

      <?php if (!empty($images)): ?>
        <div class="form-group">
          <label>Current Photos</label>
          <div class="image-gallery">
            <?php foreach ($images as $image): ?>
              <div class="image-item">
                <img src="/CeylonGo/public/uploads/<?= htmlspecialchars($image['image_path']) ?>" alt="Diary image">
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <div class="form-group">
        <label for="images">Add More Photos</label>
        <input type="file" id="images" name="images[]" multiple accept="image/*">
        <small>You can select multiple images (JPG, PNG, GIF, WEBP)</small>
      </div>

      <div class="form-group">
        <label class="checkbox-label">
          <input type="checkbox" name="is_public" value="1" <?= $entry['is_public'] ? 'checked' : '' ?>>
          <span>Make this entry public (others can see it)</span>
        </label>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Update Entry</button>
        <a href="/CeylonGo/public/tourist/my-diary" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>

  <!-- Footer include -->
  <?php include 'footer.php'; ?>
</body>
</html>


