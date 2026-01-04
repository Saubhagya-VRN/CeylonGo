<?php
// views/tourist/view_trip.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$tourist = $tourist ?? null;
$entries = $entries ?? [];
$locations = $locations ?? [];
$images = $images ?? [];
$comments = $comments ?? [];
$is_owner = $is_owner ?? false;
$is_logged_in = isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
$current_user_id = $_SESSION['user_id'] ?? null;
$current_user_type = $_SESSION['user_role'] ?? null;

if (!$tourist || empty($entries)) {
    header('Location: /CeylonGo/public/tourist/public-diaries');
    exit;
}

// Calculate trip duration
$trip_start = null;
$trip_end = null;
if (!empty($entries)) {
    $dates = array_column($entries, 'entry_date');
    if (!empty($dates)) {
        $trip_start = min($dates);
        $trip_end = max($dates);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - <?= htmlspecialchars($tourist['first_name'] . ' ' . $tourist['last_name']) ?>'s Trip</title>
  <link rel="stylesheet" href="../../public/css/common.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
  <link rel="stylesheet" href="../../public/css/tourist/diary.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .trip-header {
      background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-600) 100%);
      color: white;
      padding: 40px 20px;
      border-radius: 12px;
      margin-bottom: 30px;
      text-align: center;
    }
    
    .trip-header h1 {
      margin: 0 0 10px 0;
      font-size: 2.5em;
      font-weight: 700;
    }
    
    .trip-header .trip-meta {
      display: flex;
      justify-content: center;
      gap: 30px;
      margin-top: 20px;
      flex-wrap: wrap;
    }
    
    .trip-header .meta-item {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 1.1em;
    }
    
    .locations-section {
      background: white;
      padding: 25px;
      border-radius: 12px;
      margin-bottom: 30px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .locations-section h2 {
      margin: 0 0 20px 0;
      color: var(--color-primary);
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .locations-list {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
    }
    
    .location-badge {
      background: var(--color-primary);
      color: white;
      padding: 10px 20px;
      border-radius: 25px;
      font-size: 0.95em;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .timeline {
      position: relative;
      padding: 20px 0;
    }
    
    .timeline::before {
      content: '';
      position: absolute;
      left: 30px;
      top: 0;
      bottom: 0;
      width: 3px;
      background: var(--color-primary);
      opacity: 0.3;
    }
    
    .timeline-entry {
      position: relative;
      margin-bottom: 40px;
      padding-left: 80px;
    }
    
    .timeline-entry::before {
      content: '';
      position: absolute;
      left: 22px;
      top: 10px;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      background: var(--color-primary);
      border: 4px solid white;
      box-shadow: 0 0 0 3px var(--color-primary);
    }
    
    .timeline-entry-card {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .timeline-entry-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .entry-date-header {
      font-size: 0.9em;
      color: var(--color-primary);
      font-weight: 600;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .entry-title {
      font-size: 1.4em;
      margin: 10px 0;
      color: var(--color-dark-text);
    }
    
    .entry-location {
      color: var(--color-light-text);
      margin: 10px 0;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .entry-content {
      margin: 15px 0;
      line-height: 1.8;
      color: #555;
    }
    
    .entry-images {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 15px;
      margin-top: 20px;
    }
    
    .entry-images .image-item {
      border-radius: 8px;
      overflow: hidden;
      aspect-ratio: 1;
    }
    
    .entry-images .image-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .trip-actions {
      text-align: center;
      margin-top: 30px;
    }

    /* Instagram-like Feed Styles */
    .instagram-feed {
      max-width: 600px;
      margin: 0 auto;
    }

    .instagram-post {
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      margin-bottom: 30px;
      overflow: hidden;
    }

    .post-header {
      padding: 15px 20px;
      border-bottom: 1px solid #eee;
    }

    .post-author-info {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .author-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--color-primary);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
    }

    .author-details {
      flex: 1;
    }

    .author-details strong {
      display: block;
      color: var(--color-dark-text);
      font-size: 1.1em;
    }

    .post-date {
      font-size: 0.85em;
      color: var(--color-light-text);
      display: flex;
      align-items: center;
      gap: 8px;
      margin-top: 4px;
    }

    .post-images {
      width: 100%;
    }

    .post-image {
      width: 100%;
      aspect-ratio: 1;
      overflow: hidden;
    }

    .post-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .post-content {
      padding: 20px;
    }

    .post-title {
      font-size: 1.3em;
      margin: 0 0 10px 0;
      color: var(--color-dark-text);
    }

    .post-text {
      line-height: 1.6;
      color: #555;
      white-space: pre-wrap;
    }

    .comments-section {
      border-top: 1px solid #eee;
      padding: 15px 20px;
    }

    .comments-header {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 15px;
      color: var(--color-primary);
      font-weight: 600;
    }

    .comments-list {
      max-height: 400px;
      overflow-y: auto;
      margin-bottom: 15px;
    }

    .comment-item {
      padding: 12px 0;
      border-bottom: 1px solid #f0f0f0;
    }

    .comment-item:last-child {
      border-bottom: none;
    }

    .comment-author {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 5px;
    }

    .comment-author strong {
      color: var(--color-dark-text);
    }

    .comment-type {
      font-size: 0.75em;
      background: var(--color-primary);
      color: white;
      padding: 2px 8px;
      border-radius: 10px;
    }

    .comment-text {
      color: #555;
      line-height: 1.5;
      margin-bottom: 5px;
      white-space: pre-wrap;
    }

    .comment-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .comment-time {
      font-size: 0.8em;
      color: var(--color-light-text);
    }

    .delete-comment-btn {
      background: none;
      border: none;
      color: #dc3545;
      cursor: pointer;
      padding: 5px;
      font-size: 0.9em;
    }

    .delete-comment-btn:hover {
      color: #c82333;
    }

    .add-comment-form {
      margin-top: 15px;
    }

    .comment-input-wrapper {
      display: flex;
      gap: 10px;
      align-items: center;
    }

    .comment-input {
      flex: 1;
      padding: 12px 15px;
      border: 2px solid #e0e0e0;
      border-radius: 25px;
      font-size: 0.95em;
      outline: none;
      transition: border-color 0.3s;
    }

    .comment-input:focus {
      border-color: var(--color-primary);
    }

    .comment-submit-btn {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      background: var(--color-primary);
      color: white;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.3s;
    }

    .comment-submit-btn:hover {
      background: var(--color-primary-600);
    }

    .login-prompt {
      text-align: center;
      padding: 15px;
      color: var(--color-light-text);
    }

    .login-prompt a {
      color: var(--color-primary);
      text-decoration: none;
    }

    .login-prompt a:hover {
      text-decoration: underline;
    }
    
    @media (max-width: 768px) {
      .trip-header h1 {
        font-size: 1.8em;
      }
      
      .trip-header .trip-meta {
        flex-direction: column;
        gap: 15px;
      }
      
      .timeline-entry {
        padding-left: 60px;
      }
      
      .timeline::before {
        left: 20px;
      }
      
      .timeline-entry::before {
        left: 12px;
      }
    }
  </style>
</head>
<body class="bg-app">
  <!-- Navbar include -->
  <?php include 'header.php'; ?>

  <div class="diary-container">
    <div class="trip-header">
      <h1><?= htmlspecialchars($tourist['first_name'] . ' ' . $tourist['last_name']) ?>'s Journey</h1>
      <div class="trip-meta">
        <?php if ($trip_start): ?>
          <div class="meta-item">
            <i class="fa fa-calendar-alt"></i>
            <span><?= date('M j, Y', strtotime($trip_start)) ?></span>
            <?php if ($trip_end && $trip_end != $trip_start): ?>
              <span> - <?= date('M j, Y', strtotime($trip_end)) ?></span>
            <?php endif; ?>
          </div>
        <?php endif; ?>
        <div class="meta-item">
          <i class="fa fa-map-marker-alt"></i>
          <span><?= count($locations) ?> Locations</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-book"></i>
          <span><?= count($entries) ?> Entries</span>
        </div>
      </div>
    </div>

    <?php if (!empty($locations)): ?>
      <div class="locations-section">
        <h2><i class="fa fa-map"></i> Places Visited</h2>
        <div class="locations-list">
          <?php foreach ($locations as $loc): ?>
            <?php if (!empty($loc['location'])): ?>
              <div class="location-badge">
                <i class="fa fa-map-marker-alt"></i>
                <?= htmlspecialchars($loc['location']) ?>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <div class="instagram-feed">
      <?php foreach ($entries as $entry): 
        $entryComments = isset($comments[$entry['id']]) ? $comments[$entry['id']] : [];
      ?>
        <div class="instagram-post" data-entry-id="<?= $entry['id'] ?>">
          <!-- Post Header -->
          <div class="post-header">
            <div class="post-author-info">
              <div class="author-avatar">
                <i class="fa fa-user"></i>
              </div>
              <div class="author-details">
                <strong><?= htmlspecialchars($tourist['first_name'] . ' ' . $tourist['last_name']) ?></strong>
                <span class="post-date">
                  <i class="fa fa-calendar"></i>
                  <?= date('M j, Y', strtotime($entry['entry_date'])) ?>
                  <?php if ($entry['location']): ?>
                    <i class="fa fa-map-marker-alt"></i> <?= htmlspecialchars($entry['location']) ?>
                  <?php endif; ?>
                </span>
              </div>
            </div>
          </div>

          <!-- Post Images -->
          <?php if (isset($images[$entry['id']]) && !empty($images[$entry['id']])): ?>
            <div class="post-images">
              <?php foreach ($images[$entry['id']] as $image): ?>
                <div class="post-image">
                  <img src="/CeylonGo/public/uploads/<?= htmlspecialchars($image['image_path']) ?>" alt="Trip photo">
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <!-- Post Content -->
          <div class="post-content">
            <h3 class="post-title"><?= htmlspecialchars($entry['title']) ?></h3>
            <div class="post-text">
              <?= nl2br(htmlspecialchars($entry['content'])) ?>
            </div>
          </div>

          <!-- Comments Section -->
          <div class="comments-section">
            <div class="comments-header">
              <i class="fa fa-comments"></i>
              <span><?= count($entryComments) ?> Comment<?= count($entryComments) != 1 ? 's' : '' ?></span>
            </div>
            
            <div class="comments-list" id="comments-<?= $entry['id'] ?>">
              <?php foreach ($entryComments as $comment): ?>
                <div class="comment-item" data-comment-id="<?= $comment['id'] ?>">
                  <div class="comment-author">
                    <strong><?= htmlspecialchars($comment['user_name'] ?? 'Unknown') ?></strong>
                    <span class="comment-type"><?= ucfirst($comment['user_type']) ?></span>
                  </div>
                  <div class="comment-text"><?= nl2br(htmlspecialchars($comment['comment_text'])) ?></div>
                  <div class="comment-meta">
                    <span class="comment-time"><?= date('M j, Y g:i A', strtotime($comment['created_at'])) ?></span>
                    <?php if ($is_logged_in && $comment['user_id'] == $current_user_id && $comment['user_type'] == $current_user_type): ?>
                      <button class="delete-comment-btn" onclick="deleteComment(<?= $comment['id'] ?>, <?= $entry['id'] ?>)">
                        <i class="fa fa-trash"></i>
                      </button>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

            <!-- Add Comment Form -->
            <?php if ($is_logged_in): ?>
              <div class="add-comment-form">
                <div class="comment-input-wrapper">
                  <input type="text" 
                         class="comment-input" 
                         id="comment-input-<?= $entry['id'] ?>" 
                         placeholder="Add a comment..." 
                         data-entry-id="<?= $entry['id'] ?>">
                  <button class="comment-submit-btn" onclick="addComment(<?= $entry['id'] ?>)">
                    <i class="fa fa-paper-plane"></i>
                  </button>
                </div>
              </div>
            <?php else: ?>
              <div class="login-prompt">
                <a href="/CeylonGo/public/login">Login to comment</a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="trip-actions">
      <?php if ($is_owner): ?>
        <a href="/CeylonGo/public/tourist/my-diary" class="btn btn-secondary">Back to My Diary</a>
      <?php else: ?>
        <a href="/CeylonGo/public/tourist/public-diaries" class="btn btn-secondary">Back to Public Diaries</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Footer include -->
  <?php include 'footer.php'; ?>

  <script>
    // Add comment function
    function addComment(entryId) {
      const input = document.getElementById('comment-input-' + entryId);
      const commentText = input.value.trim();
      
      if (!commentText) {
        alert('Please enter a comment');
        return;
      }

      fetch('/CeylonGo/public/tourist/add-comment', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          entry_id: entryId,
          comment_text: commentText
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Add comment to the list
          const commentsList = document.getElementById('comments-' + entryId);
          const commentDiv = document.createElement('div');
          commentDiv.className = 'comment-item';
          commentDiv.setAttribute('data-comment-id', data.comment.id);
          
          const now = new Date();
          const timeStr = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + 
                         ' ' + now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
          
          commentDiv.innerHTML = `
            <div class="comment-author">
              <strong>${data.comment.user_name}</strong>
              <span class="comment-type">${data.comment.user_type.charAt(0).toUpperCase() + data.comment.user_type.slice(1)}</span>
            </div>
            <div class="comment-text">${commentText.replace(/\n/g, '<br>')}</div>
            <div class="comment-meta">
              <span class="comment-time">${timeStr}</span>
              <button class="delete-comment-btn" onclick="deleteComment(${data.comment.id}, ${entryId})">
                <i class="fa fa-trash"></i>
              </button>
            </div>
          `;
          
          commentsList.appendChild(commentDiv);
          
          // Update comment count
          const commentsHeader = commentsList.previousElementSibling;
          const currentCount = parseInt(commentsHeader.querySelector('span').textContent.match(/\d+/)[0]);
          commentsHeader.querySelector('span').textContent = (currentCount + 1) + ' Comment' + (currentCount + 1 != 1 ? 's' : '');
          
          // Clear input
          input.value = '';
        } else {
          alert(data.message || 'Failed to add comment');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
      });
    }

    // Delete comment function
    function deleteComment(commentId, entryId) {
      if (!confirm('Are you sure you want to delete this comment?')) {
        return;
      }

      fetch('/CeylonGo/public/tourist/delete-comment/' + commentId, {
        method: 'GET',
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Remove comment from DOM
          const commentItem = document.querySelector(`[data-comment-id="${commentId}"]`);
          if (commentItem) {
            commentItem.remove();
            
            // Update comment count
            const commentsHeader = document.querySelector(`#comments-${entryId}`).previousElementSibling;
            const currentCount = parseInt(commentsHeader.querySelector('span').textContent.match(/\d+/)[0]);
            commentsHeader.querySelector('span').textContent = (currentCount - 1) + ' Comment' + (currentCount - 1 != 1 ? 's' : '');
          }
        } else {
          alert(data.message || 'Failed to delete comment');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
      });
    }

    // Allow Enter key to submit comment
    document.querySelectorAll('.comment-input').forEach(input => {
      input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          const entryId = this.getAttribute('data-entry-id');
          addComment(entryId);
        }
      });
    });
  </script>
</body>
</html>

