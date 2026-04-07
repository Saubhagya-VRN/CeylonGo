<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$inquiries = isset($inquiries) ? $inquiries : array();
$status = isset($status) ? $status : 'all';
$flashSuccess = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$flashError = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success'], $_SESSION['error']);

$pendingCount = 0;
$repliedCount = 0;
foreach ($inquiries as $it) {
    $st = isset($it['status']) ? $it['status'] : '';
    if ($st === 'pending') $pendingCount++;
    else if ($st === 'replied') $repliedCount++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/base.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/navbar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/sidebar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/transport/footer.css">
  <title>Inquiry Management</title>
  <style>
    .inq-wrap{max-width:1200px;margin:24px auto;padding:0 16px;}
    .inq-card{background:#fff;border-radius:14px;border:1px solid rgba(0,0,0,.08);box-shadow:0 8px 25px rgba(74,124,89,0.10);padding:18px;}
    .inq-stats{display:flex;gap:12px;flex-wrap:wrap;margin:12px 0 18px;}
    .inq-stat{flex:1;min-width:160px;background:#f6fbf6;border:1px solid rgba(74,124,89,.15);border-radius:12px;padding:12px;}
    .inq-table{width:100%;border-collapse:collapse;}
    .inq-table th,.inq-table td{padding:12px 10px;border-top:1px solid #eef2f7;vertical-align:top;}
    .inq-table th{text-align:left;color:#2c5530;}
    .inq-status{font-size:12px;font-weight:800;padding:6px 10px;border-radius:999px;display:inline-block}
    .inq-status--pending{background:#fffbeb;border:1px solid #f59e0b;color:#92400e;}
    .inq-status--replied{background:#ecfdf5;border:1px solid #10b981;color:#065f46;}
    .inq-reply{width:100%;border:1px solid #d1d5db;border-radius:12px;padding:10px;}
    .inq-btn{background:#111;color:#fff;border:none;border-radius:999px;padding:10px 14px;font-weight:800;cursor:pointer;margin-top:8px;}
    .flash{padding:10px 12px;border-radius:12px;margin:10px 0;}
    .flash--ok{background:#ecfdf5;border:1px solid #10b981;color:#065f46;}
    .flash--err{background:#fef2f2;border:1px solid #ef4444;color:#991b1b;}
  </style>
</head>
<body>
  <header class="navbar">
    <div class="branding">
      <img src="/CeylonGo/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="/CeylonGo/public/admin/dashboard">Home</a>
      <a href="/CeylonGo/public/logout">Logout</a>
    </nav>
  </header>

  <div class="inq-wrap">
    <div class="inq-card">
      <h2 style="margin:0 0 6px;color:#2c5530;">Inquiry Management</h2>
      <p style="margin:0 0 10px;color:#6b7280;">Reply to tourist questions. Replies will appear on the tourist dashboard.</p>

      <?php if ($flashSuccess): ?><div class="flash flash--ok"><?php echo htmlspecialchars($flashSuccess); ?></div><?php endif; ?>
      <?php if ($flashError): ?><div class="flash flash--err"><?php echo htmlspecialchars($flashError); ?></div><?php endif; ?>

      <div class="inq-stats">
        <div class="inq-stat"><strong>Total</strong><div style="font-size:22px;font-weight:900;"><?php echo (int)count($inquiries); ?></div></div>
        <div class="inq-stat"><strong>Pending</strong><div style="font-size:22px;font-weight:900;"><?php echo (int)$pendingCount; ?></div></div>
        <div class="inq-stat"><strong>Replied</strong><div style="font-size:22px;font-weight:900;"><?php echo (int)$repliedCount; ?></div></div>
      </div>

      <table class="inq-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Inquiry</th>
            <th>Status</th>
            <th>Reply</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($inquiries)): ?>
          <tr><td colspan="4">No inquiries yet.</td></tr>
        <?php else: ?>
          <?php foreach ($inquiries as $inq): ?>
            <?php
              $name = trim((isset($inq['first_name']) ? $inq['first_name'] : '') . ' ' . (isset($inq['last_name']) ? $inq['last_name'] : ''));
              $email = isset($inq['email']) ? $inq['email'] : '';
              $uid = isset($inq['user_id']) ? (int)$inq['user_id'] : 0;
              if ($name === '') $name = ($email !== '' ? $email : ('User #' . $uid));
              $st = isset($inq['status']) ? $inq['status'] : 'pending';
              $stCls = ($st === 'replied') ? 'inq-status--replied' : 'inq-status--pending';
            ?>
            <tr>
              <td>
                <strong><?php echo htmlspecialchars($name); ?></strong>
                <div style="font-size:12px;color:#6b7280;"><?php echo htmlspecialchars($email); ?></div>
                <div style="font-size:12px;color:#6b7280;">#<?php echo (int)(isset($inq['id']) ? $inq['id'] : 0); ?></div>
              </td>
              <td>
                <strong><?php echo htmlspecialchars(isset($inq['subject']) ? $inq['subject'] : ''); ?></strong>
                <div style="margin-top:6px;white-space:pre-wrap;color:#374151;"><?php echo htmlspecialchars(isset($inq['message']) ? $inq['message'] : ''); ?></div>
              </td>
              <td><span class="inq-status <?php echo $stCls; ?>"><?php echo htmlspecialchars(ucfirst($st)); ?></span></td>
              <td style="min-width:280px;">
                <form method="post" action="/CeylonGo/public/admin/inquiries/reply">
                  <input type="hidden" name="inquiry_id" value="<?php echo (int)(isset($inq['id']) ? $inq['id'] : 0); ?>">
                  <textarea class="inq-reply" name="admin_reply" rows="3" placeholder="Type reply..."><?php echo htmlspecialchars(isset($inq['admin_reply']) ? $inq['admin_reply'] : ''); ?></textarea>
                  <button class="inq-btn" type="submit">Save reply</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Font Awesome (REQUIRED) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Optional admin-only overrides -->
    <link rel="stylesheet" href="/CeylonGO/public/css/admin/admin_inquiries.css">
    
    <!-- Shared Transport Layout -->
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/base.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/navbar.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/sidebar.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/footer.css">

    <!-- Responsive styles (always last) -->
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">

    <title>Inquiry Management</title>
  </head>

  <body>
    <!-- Navbar -->
    <header class="navbar">
      <div class="branding">
        <img src="/CeylonGo/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
        <div class="logo-text">Ceylon Go</div>
      </div>

      <nav class="nav-links">
        <a href="/CeylonGo/public/admin/dashboard">Home</a>
        <div class="profile-dropdown">
          <img src="/CeylonGo/public/images/profile.jpg" alt="User" class="profile-pic" onclick="toggleProfileDropdown()">
          <div class="profile-dropdown-menu" id="profileDropdown">
            <a href="/CeylonGo/public/admin/profile"><i class="fa-regular fa-user"></i> My Profile</a>
            <a href="/CeylonGo/public/logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
          </div>
        </div>
      </nav>
    </header>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="page-wrapper">

      <!-- Sidebar -->
      <div class="sidebar">
        <ul>
          <li><a href="/CeylonGo/public/admin/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
          <li><a href="/CeylonGo/public/admin/users"><i class="fa-solid fa-users"></i> Users</a></li>
          <li><a href="/CeylonGo/public/admin/bookings"><i class="fa-regular fa-calendar"></i> Bookings</a></li>
          <li><a href="/CeylonGo/public/admin/service"><i class="fa-solid fa-van-shuttle"></i> Service Providers</a></li>
          <li><a href="/CeylonGo/public/admin/payments"><i class="fa-solid fa-credit-card"></i> Payments</a></li>
          <li class="active"><a href="/CeylonGo/public/admin/inquiries"><i class="fa-solid fa-circle-question"></i> Inquiries</a></li>
          <li><a href="/CeylonGo/public/admin/promotions"><i class="fa-solid fa-bullhorn"></i> Packages</a></li>
          <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-solid fa-star"></i> Reviews</a></li>
          <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports & Analysis</a></li>
        </ul>
      </div>

      <div class="main-content">
          <div class="inquiry-management">

              <h2 class="page-title">Inquiry Management</h2>
              <br>

              <div class="toolbar">
                  <div class="search-section">
                    <input type="text" placeholder="Search by user or subject" class="search-input">
                    <button class="search-btn">🔍</button>
                  </div>
                  <div class="filter-buttons">
                    <button class="filter-btn active">All</button>
                    <button class="filter-btn">Pending</button>
                    <button class="filter-btn">Resolved</button>
                  </div>
              </div>

              <div class="stats-section">
                  <h4>Inquiry Statistics</h4><br>
                  <div class="stats-grid">
                    <div class="stat-box">
                        <strong>Total Inquiries</strong><br>
                        <span>350</span>
                    </div>
                    <div class="stat-box">
                        <strong>Pending</strong><br>
                        <span>120</span>
                    </div>
                    <div class="stat-box">
                        <strong>Resolved</strong><br>
                        <span>200</span>
                    </div>
                    <div class="stat-box">
                        <strong>Escalated</strong><br>
                        <span>30</span>
                    </div>
                  </div>
              </div>
              <br>

              <div class="inquiries-section">
                  <table class="inquiry-table">
                    <thead>
                        <tr>
                          <th>User</th>
                          <th>Subject</th>
                          <th>Status</th>
                          <th>Date</th>
                          <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                          <td>John Doe</td>
                          <td>Did not receive refund</td>
                          <td><span class="status pending">Pending</span></td>
                          <td>2025-08-20</td>
                          <td class="actions">
                              <button class="icon-btn">👁️</button>
                              <button class="icon-btn">✏️</button>
                              <button class="icon-btn">✅</button>
                          </td>
                        </tr>
                        <tr>
                          <td>Jane Smith</td>
                          <td>Issue with delivery</td>
                          <td><span class="status resolved">Resolved</span></td>
                          <td>2025-08-19</td>
                          <td class="actions">
                              <button class="icon-btn">👁️</button>
                              <button class="icon-btn">✏️</button>
                          </td>
                        </tr>
                        <tr>
                          <td>Mark Lee</td>
                          <td>Complaint not addressed</td>
                          <td><span class="status resolved">Resolved</span></td>
                          <td>2025-08-18</td>
                          <td class="actions">
                              <button class="icon-btn">👁️</button>
                              <button class="icon-btn">✏️</button>
                          </td>
                        </tr>
                    </tbody>
                  </table>
              </div>

              <div class="footer-buttons">
                  <button class="footer-btn black">Export Inquiries</button>
              </div>
          </div>
      </div>
    </div>

    <!-- Footer -->
    <footer>
      <ul>
        <li><a href="/CeylonGo/public/admin/bookings">View All Bookings</a></li>
        <li><a href="/CeylonGo/public/admin/reports">Generate Reports</a></li>
        <li><a href="/CeylonGo/public/admin/payments">Payments</a></li>
      </ul>
    </footer>

    <script>
      function toggleProfileDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        dropdown.classList.toggle('show');
      }

      // Close dropdown when clicking outside
      document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('profileDropdown');
        const profilePic = document.querySelector('.profile-pic');
        
        if (dropdown && !dropdown.contains(event.target) && event.target !== profilePic) {
          dropdown.classList.remove('show');
        }
      });
    </script>
  </body>
</html>
