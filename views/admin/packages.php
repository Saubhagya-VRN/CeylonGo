<?php
  // views/admin/packages.php
  if (session_status() === PHP_SESSION_NONE) session_start();
  if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
      header("Location: /CeylonGo/public/login");
      exit();
  }
  $packages = $packages ?? [];
  $success  = $success  ?? null;
  $error    = $error    ?? null;
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font Awesome (REQUIRED) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Optional admin-only overrides -->
    <link rel="stylesheet" href="/CeylonGo/public/css/admin/packages.css">
    
    <!-- Shared Transport Layout -->
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/base.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/navbar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/sidebar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/footer.css">
    
    <!-- Responsive styles (always last) -->
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/responsive.css">

    <title>Manage Packages</title>
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
          <li><a href="/CeylonGo/public/admin/inquiries"><i class="fa-solid fa-circle-question"></i> Inquiries</a></li>
          <li class="active"><a href="/CeylonGo/public/admin/packages"><i class="fa-solid fa-box-open"></i> Packages</a></li>
          <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-solid fa-star"></i> Reviews</a></li>
          <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports & Analysis</a></li>
        </ul>
      </div>

      <div class="main-content">
        <div class="user-management">

          <h2 class="page-title">Manage Tour Packages</h2>

          <?php if ($success): ?>
            <div class="pkg-alert pkg-alert--success"><?= htmlspecialchars($success) ?></div>
          <?php endif; ?>
          <?php if ($error): ?>
            <div class="pkg-alert pkg-alert--error"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <div class="footer-buttons">
            <a href="/CeylonGo/public/admin/packages/new" class="footer-btn black">+ Add New Package</a>
          </div>
          <br>

          <div class="users-section">
            <table class="user-table" id="packagesTable">
              <thead>
                <tr>
                  <th>Image</th>
                  <th>Title</th>
                  <th>Location</th>
                  <th>Category</th>
                  <th>Duration</th>
                  <th>Price (LKR)</th>
                  <th>Rating</th>
                  <th>Trending</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="packagesTableBody">
                <?php if (empty($packages)): ?>
                  <tr><td colspan="9" style="text-align:center;">No packages found.</td></tr>
                <?php else: ?>
                  <?php foreach ($packages as $p): ?>
                  <tr>
                    <td>
                      <?php if (!empty($p['image'])): ?>
                        <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['title']) ?>" style="width:72px;height:50px;object-fit:cover;border-radius:4px;">
                      <?php else: ?>
                        <span style="color:#aaa;font-size:12px;">No image</span>
                      <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($p['title']) ?></td>
                    <td><?= htmlspecialchars($p['location']) ?></td>
                    <td style="text-transform:capitalize"><?= htmlspecialchars($p['category']) ?></td>
                    <td><?= htmlspecialchars($p['duration_short'] ?? $p['duration'] ?? '—') ?></td>
                    <td><?= number_format($p['price']) ?></td>
                    <td><?= $p['rating'] !== null ? number_format((float)$p['rating'], 1) : '—' ?></td>
                    <td><?php if (!empty($p['trending'])): ?><span style="color:green;font-weight:bold">Yes</span><?php else: ?>No<?php endif; ?></td>
                    <td class="actions">
                      <a href="/CeylonGo/public/admin/packages/edit?id=<?= (int)$p['id'] ?>" class="icon-btn">✏️</a>
                      <button class="icon-btn danger" onclick="confirmDelete(<?= (int)$p['id'] ?>, '<?= htmlspecialchars(addslashes($p['title'])) ?>')">🗑️</button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <div class="footer-buttons">
            <button class="footer-btn black" id="exportBtn">Export Packages</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete confirmation modal -->
    <div class="modal" id="deleteModal">
      <div class="modal-content">
        <h3>Delete Package?</h3>
        <p id="deleteModalMsg" style="color:#555;margin-bottom:20px;">This action cannot be undone.</p>
        <form method="POST" action="/CeylonGo/public/admin/packages/delete">
          <input type="hidden" name="id" id="deleteId">
          <button type="submit" class="submit-btn">Yes, Delete</button>
          <button type="button" class="cancel-btn" onclick="closeDeleteModal()">Cancel</button>
        </form>
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
      const packagesData = <?= json_encode(array_map(function($p) {
          return [
              'id'                 => $p['id'],
              'title'              => $p['title'],
              'location'           => $p['location'],
              'locations'          => $p['locations'] ?? '',
              'category'           => ucfirst($p['category']),
              'duration'           => $p['duration'] ?? '',
              'duration_short'     => $p['duration_short'] ?? '',
              'price'              => $p['price'],
              'price_child_ratio'  => $p['price_child_ratio'] ?? 0.50,
              'price_infant_ratio' => $p['price_infant_ratio'] ?? 0.00,
              'rating'             => $p['rating'],
              'reviews'            => $p['reviews'] ?? 0,
              'trending'           => !empty($p['trending']) ? 'Yes' : 'No',
              'overview'           => $p['overview'] ?? [],
              'highlights'         => $p['highlights'] ?? [],
              'itinerary'          => $p['itinerary'] ?? [],
              'accommodation'      => $p['accommodation'] ?? [],
              'included'           => $p['included'] ?? [],
              'excluded'           => $p['excluded'] ?? [],
          ];
      }, $packages), JSON_UNESCAPED_UNICODE) ?>;

      // ── Export Packages Report ─────────────────────────────────
      document.getElementById('exportBtn').addEventListener('click', function () {
        if (packagesData.length === 0) {
          alert('No packages to export!');
          return;
        }

        const sep    = '='.repeat(70);
        const subSep = '-'.repeat(70);
        const now    = new Date();
        const dateStr = now.toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' });
        const timeStr = now.toLocaleTimeString('en-GB');

        let report = '';

        // ── Report header ──
        report += '='.repeat(70) + '\n';
        report += '          CEYLON GO — TOUR PACKAGES REPORT\n';
        report += '='.repeat(70) + '\n';
        report += '  Generated on  : ' + dateStr + ' at ' + timeStr + '\n';
        report += '  Total Packages: ' + packagesData.length + '\n';
        report += '='.repeat(70) + '\n\n';

        packagesData.forEach(function (p, index) {

          // ── Package header ──
          report += 'PACKAGE ' + (index + 1) + ' OF ' + packagesData.length + '\n';
          report += sep + '\n';
          report += '  Title    : ' + p.title + '\n';
          report += '  Category : ' + p.category + '\n';
          report += sep + '\n\n';

          // ── Basic details ──
          report += '  BASIC DETAILS\n';
          report += '  ' + subSep + '\n';
          report += '  Primary Location : ' + p.location + '\n';
          if (p.locations) {
            report += '  All Locations    : ' + p.locations + '\n';
          }
          report += '  Duration         : ' + (p.duration || p.duration_short || '—') + '\n';
          report += '  Trending         : ' + p.trending + '\n';
          report += '  Rating           : ' + (p.rating !== null && p.rating !== '' ? p.rating + ' / 5.0' : 'N/A') + '\n';
          report += '  Reviews          : ' + p.reviews + '\n\n';

          // ── Pricing ──
          report += '  PRICING\n';
          report += '  ' + subSep + '\n';
          report += '  Adult Price  : LKR ' + Number(p.price).toLocaleString() + '\n';
          report += '  Child Price  : LKR ' + Math.round(p.price * p.price_child_ratio).toLocaleString()
                  + '  (' + Math.round(p.price_child_ratio * 100) + '% of adult price)\n';
          report += '  Infant Price : LKR ' + Math.round(p.price * p.price_infant_ratio).toLocaleString()
                  + '  (' + Math.round(p.price_infant_ratio * 100) + '% of adult price)\n\n';

          // ── Trip Overview ──
          if (p.overview && p.overview.length > 0) {
            report += '  TRIP OVERVIEW\n';
            report += '  ' + subSep + '\n';
            p.overview.forEach(function (line, i) {
              report += '  ' + (i + 1) + '. ' + line + '\n';
            });
            report += '\n';
          }

          // ── Highlights ──
          if (p.highlights && p.highlights.length > 0) {
            report += '  PACKAGE HIGHLIGHTS\n';
            report += '  ' + subSep + '\n';
            p.highlights.forEach(function (h) {
              report += '  [' + (h.icon || '').toUpperCase() + ']  '
                      + (h.title || '') + '\n';
              report += '         ' + (h.desc || '') + '\n';
            });
            report += '\n';
          }

          // ── Itinerary ──
          if (p.itinerary && p.itinerary.length > 0) {
            report += '  ITINERARY (' + p.itinerary.length + ' Days)\n';
            report += '  ' + subSep + '\n';
            p.itinerary.forEach(function (day) {
              report += '  Day ' + day.day + ' : ' + (day.title || '') + '\n';
              if (day.activities && day.activities.length > 0) {
                day.activities.forEach(function (act) {
                  report += '           - ' + act + '\n';
                });
              }
              report += '\n';
            });
          }

          // ── Accommodation ──
          if (p.accommodation && p.accommodation.length > 0) {
            report += '  ACCOMMODATION\n';
            report += '  ' + subSep + '\n';
            p.accommodation.forEach(function (acc) {
              report += '  ' + acc.nights + ' Night(s)  |  ' + acc.hotel
                      + '  (' + acc.location + ')\n';
            });
            report += '\n';
          }

          // ── Included ──
          if (p.included && p.included.length > 0) {
            report += '  WHAT\'S INCLUDED\n';
            report += '  ' + subSep + '\n';
            p.included.forEach(function (item) {
              report += '  [+]  ' + item + '\n';
            });
            report += '\n';
          }

          // ── Excluded ──
          if (p.excluded && p.excluded.length > 0) {
            report += '  WHAT\'S NOT INCLUDED\n';
            report += '  ' + subSep + '\n';
            p.excluded.forEach(function (item) {
              report += '  [-]  ' + item + '\n';
            });
            report += '\n';
          }

          report += sep + '\n\n\n';
        });

        // ── Report footer ──
        report += '='.repeat(70) + '\n';
        report += '  END OF REPORT\n';
        report += '  Ceylon Go Admin  |  ' + dateStr + '\n';
        report += '='.repeat(70) + '\n';

        // Trigger download
        const blob = new Blob([report], { type: 'text/plain' });
        const link = document.createElement('a');
        const fileDate = now.toISOString().slice(0, 10);
        link.href = URL.createObjectURL(blob);
        link.download = 'ceylongo_packages_' + fileDate + '.txt';
        link.click();
      });

      // ── Navbar dropdown ───────────────────────────────────────
      function toggleProfileDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        dropdown.classList.toggle('show');
      }
      document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('profileDropdown');
        const profilePic = document.querySelector('.profile-pic');
        if (dropdown && !dropdown.contains(event.target) && event.target !== profilePic) {
          dropdown.classList.remove('show');
        }
      });

      // ── Delete modal ──────────────────────────────────────────
      const deleteModal = document.getElementById('deleteModal');

      function confirmDelete(id, title) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteModalMsg').textContent = 'Delete "' + title + '"? This cannot be undone.';
        deleteModal.style.display = 'flex';
      }
      function closeDeleteModal() {
        deleteModal.style.display = 'none';
      }
      window.addEventListener('click', function(e) {
        if (e.target === deleteModal) closeDeleteModal();
      });
    </script>
  </body>
</html>