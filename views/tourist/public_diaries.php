<?php
// views/tourist/public_diaries.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$entries = $entries ?? [];
$images = $images ?? [];
$comments = $comments ?? [];
$is_logged_in = $is_logged_in ?? false;
$current_user_id = $current_user_id ?? null;
$current_user_type = $current_user_type ?? null;
$error_message = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Travel Diaries</title>
  <link rel="stylesheet" href="../../public/css/common.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
  <link rel="stylesheet" href="../../public/css/tourist/diary.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .instagram-feed {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 15px;
      margin-top: 30px;
      max-width: 1400px;
      margin-left: auto;
      margin-right: auto;
    }

    .instagram-post {
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      height: 100%;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .instagram-post:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }

    .post-header {
      padding: 6px 12px;
      border-bottom: 1px solid #eee;
      flex-shrink: 0;
    }

    .post-author-info {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .author-avatar {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      background: var(--color-primary);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      flex-shrink: 0;
    }

    .author-details {
      flex: 1;
    }

    .author-details strong {
      display: block;
      color: var(--color-dark-text);
      font-size: 0.8em;
      font-weight: 600;
      line-height: 1.1;
    }

    .post-date {
      font-size: 0.65em;
      color: var(--color-light-text);
      display: flex;
      align-items: center;
      gap: 3px;
      margin-top: 1px;
      flex-wrap: wrap;
    }

    .post-images {
      width: 100%;
      flex-shrink: 0;
    }

    .post-image {
      width: 100%;
      aspect-ratio: 4/3;
      overflow: hidden;
    }

    .post-image:first-child {
      display: block;
    }

    .post-image:not(:first-child) {
      display: none;
    }

    .post-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .post-content {
      padding: 8px 12px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .post-title {
      font-size: 0.9em;
      margin: 0 0 4px 0;
      color: var(--color-dark-text);
      font-weight: 600;
      line-height: 1.1;
    }

    .post-text {
      line-height: 1.3;
      color: #555;
      white-space: pre-wrap;
      overflow: hidden;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      flex: 1;
      font-size: 0.75em;
      margin-bottom: 4px;
    }

    .comments-section {
      border-top: 1px solid #eee;
      padding: 6px 12px;
      flex-shrink: 0;
    }

    .comments-header {
      display: flex;
      align-items: center;
      gap: 4px;
      margin-bottom: 4px;
      color: var(--color-primary);
      font-weight: 600;
      font-size: 0.75em;
    }

    .comments-list {
      max-height: 60px;
      overflow-y: auto;
      margin-bottom: 4px;
      font-size: 0.7em;
    }

    .comment-item {
      padding: 4px 0;
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
      margin-top: 4px;
    }

    .comment-input-wrapper {
      display: flex;
      gap: 6px;
      align-items: center;
    }

    .comment-input {
      flex: 1;
      padding: 6px 10px;
      border: 1px solid #e0e0e0;
      border-radius: 20px;
      font-size: 0.75em;
      outline: none;
      transition: border-color 0.3s;
    }

    .comment-input:focus {
      border-color: var(--color-primary);
    }

    .comment-submit-btn {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: var(--color-primary);
      color: white;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.3s;
      flex-shrink: 0;
      font-size: 0.8em;
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

    .view-trip-link {
      display: inline-block;
      margin-top: 4px;
      color: var(--color-primary);
      text-decoration: none;
      font-weight: 600;
      font-size: 0.75em;
    }

    .view-trip-link:hover {
      text-decoration: underline;
    }

    @media (min-width: 1200px) {
      .instagram-feed {
        grid-template-columns: repeat(4, 1fr);
      }
    }

    @media (min-width: 900px) and (max-width: 1199px) {
      .instagram-feed {
        grid-template-columns: repeat(3, 1fr);
      }
    }

    @media (min-width: 600px) and (max-width: 899px) {
      .instagram-feed {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 599px) {
      .instagram-feed {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body class="bg-app">
  <!-- Navbar include -->
  <?php include 'header.php'; ?>

  <div class="diary-container" style="max-width: 1400px;">
    <div class="diary-header">
      <h1>Travel Diaries</h1>
      <p>Explore complete journeys from fellow travelers</p>
      <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'tourist'): ?>
        <a href="/CeylonGo/public/tourist/my-diary" class="btn btn-primary">My Diary</a>
      <?php endif; ?>
    </div>

    <?php if ($error_message): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <?php if (empty($entries)): ?>
      <div class="empty-state">
        <p>No public travel diaries yet. Be the first to share your journey!</p>
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'tourist'): ?>
          <a href="/CeylonGo/public/tourist/add-diary-entry" class="btn btn-primary">Create Your First Entry</a>
        <?php endif; ?>
      </div>
    <?php else: ?>
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
                  <strong><?= htmlspecialchars($entry['first_name'] . ' ' . $entry['last_name']) ?></strong>
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
              <a href="/CeylonGo/public/tourist/view-trip/<?= $entry['tourist_id'] ?>" class="view-trip-link">
                View Complete Trip →
              </a>
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
    <?php endif; ?>
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


