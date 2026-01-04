<?php
// views/tourist/my_diary.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
    header('Location: /CeylonGo/public/login');
    exit;
}

$entries = $entries ?? [];
$images = $images ?? [];
$success_message = $_SESSION['success'] ?? '';
$error_message = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - My Travel Diary</title>
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
      <h1>My Travel Diary</h1>
      <p>Document your journey through Sri Lanka</p>
      <a href="/CeylonGo/public/tourist/add-diary-entry" class="btn btn-primary">+ Add New Entry</a>
    </div>

    <?php if ($success_message): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <?php if (empty($entries)): ?>
      <div class="empty-state">
        <p>You haven't created any diary entries yet.</p>
        <a href="/CeylonGo/public/tourist/add-diary-entry" class="btn btn-primary">Create Your First Entry</a>
      </div>
    <?php else: ?>
      <div class="diary-entries">
        <?php foreach ($entries as $entry): ?>
          <div class="diary-entry-card">
            <div class="entry-header">
              <h3><?= htmlspecialchars($entry['title']) ?></h3>
              <span class="entry-date"><?= date('F j, Y', strtotime($entry['entry_date'])) ?></span>
            </div>
            
            <?php if ($entry['location']): ?>
              <div class="entry-location">
                <i class="fa fa-map-marker-alt"></i> <?= htmlspecialchars($entry['location']) ?>
              </div>
            <?php endif; ?>

            <?php if (isset($images[$entry['id']]) && !empty($images[$entry['id']])): ?>
              <div class="entry-images-preview">
                <?php foreach (array_slice($images[$entry['id']], 0, 3) as $image): ?>
                  <img src="/CeylonGo/public/uploads/<?= htmlspecialchars($image['image_path']) ?>" 
                       alt="Diary image">
                <?php endforeach; ?>
                <?php if (count($images[$entry['id']]) > 3): ?>
                  <span style="color: var(--color-light-text); align-self: center;">+<?= count($images[$entry['id']]) - 3 ?> more</span>
                <?php endif; ?>
              </div>
            <?php else: ?>
              <div class="entry-images-preview">
                <span style="color: var(--color-light-text); font-style: italic;">No images</span>
              </div>
            <?php endif; ?>

            <div class="entry-content">
              <?= nl2br(htmlspecialchars(substr($entry['content'], 0, 200))) ?>
              <?= strlen($entry['content']) > 200 ? '...' : '' ?>
            </div>

            <div class="entry-footer">
              <span class="entry-visibility">
                <?php if ($entry['is_public']): ?>
                  <i class="fa fa-globe"></i> Public
                <?php else: ?>
                  <i class="fa fa-lock"></i> Private
                <?php endif; ?>
              </span>
              <div class="entry-actions">
                <a href="/CeylonGo/public/tourist/view-diary-entry/<?= $entry['id'] ?>" class="btn btn-sm btn-view">View</a>
                <a href="/CeylonGo/public/tourist/edit-diary-entry/<?= $entry['id'] ?>" class="btn btn-sm btn-edit">Edit</a>
                <a href="/CeylonGo/public/tourist/delete-diary-entry/<?= $entry['id'] ?>" 
                   class="btn btn-sm btn-delete" 
                   onclick="return confirm('Are you sure you want to delete this entry?')">Delete</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Footer include -->
  <?php include 'footer.php'; ?>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>


