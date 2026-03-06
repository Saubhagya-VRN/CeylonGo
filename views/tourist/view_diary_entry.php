<?php
// views/tourist/view_diary_entry.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$entry = $entry ?? null;
$images = $images ?? [];

if (!$entry) {
    header('Location: /CeylonGo/public/tourist/public-diaries');
    exit;
}

$is_owner = isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'tourist' && $entry['tourist_id'] == $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - <?= htmlspecialchars($entry['title']) ?></title>
  <link rel="stylesheet" href="../../public/css/common.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
  <link rel="stylesheet" href="../../public/css/tourist/diary.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-app">
  <!-- Navbar include -->
  <?php include 'header.php'; ?>

  <div class="diary-container">
    <div class="entry-view">
      <div class="entry-header">
        <h1><?= htmlspecialchars($entry['title']) ?></h1>
        <div class="entry-meta">
          <span class="entry-author">
            <i class="fa fa-user"></i> 
            <?= htmlspecialchars($entry['first_name'] . ' ' . $entry['last_name']) ?>
          </span>
          <span class="entry-date">
            <i class="fa fa-calendar"></i> 
            <?= date('F j, Y', strtotime($entry['entry_date'])) ?>
          </span>
          <?php if ($entry['location']): ?>
            <span class="entry-location">
              <i class="fa fa-map-marker-alt"></i> 
              <?= htmlspecialchars($entry['location']) ?>
            </span>
          <?php endif; ?>
        </div>
      </div>

      <?php if (!empty($images)): ?>
        <div class="entry-images">
          <?php foreach ($images as $image): ?>
            <div class="image-item">
              <img src="/CeylonGo/public/uploads/<?= htmlspecialchars($image['image_path']) ?>" alt="Diary image">
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="entry-content">
        <?= nl2br(htmlspecialchars($entry['content'])) ?>
      </div>

      <?php if ($is_owner): ?>
        <div class="entry-actions">
          <a href="/CeylonGo/public/tourist/edit-diary-entry/<?= $entry['id'] ?>" class="btn btn-edit">Edit Entry</a>
          <a href="/CeylonGo/public/tourist/my-diary" class="btn btn-secondary">Back to My Diary</a>
        </div>
      <?php else: ?>
        <div class="entry-actions">
          <a href="/CeylonGo/public/tourist/public-diaries" class="btn btn-secondary">Back to Public Diaries</a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Footer include -->
  <?php include 'footer.php'; ?>
</body>
</html>

