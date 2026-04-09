<?php
  // views/admin/packages.php
  if (session_status() === PHP_SESSION_NONE) session_start();
  if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
      header("Location: /CeylonGo/public/login");
      exit();
  }
  $packages = $packages ?? [];
  $customTripsWithDestinations = $customTripsWithDestinations ?? [];
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
          <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-regular fa-star"></i> Reviews</a></li>
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
          
          <h3 class="page-title" style="font-size:18px;">Tour packages (catalog)</h3>
          <p class="sub-text" style="color:#555;margin:8px 0 12px;">Fixed packages available for booking.</p>

          <div class="users-section">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:15px; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
                
                <!-- LEFT: Show entries -->
                <div class="filter-buttons" style="align-items:center;">
                    <span style="font-size:14px;">Show</span>

                    <select id="rowsPerPage" class="filter-btn small-btn">
                        <option value="10" selected>10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>

                    <span style="font-size:14px;">entries</span>
                </div>

                <!-- RIGHT: Pagination -->
                <div id="paginationControls" class="filter-buttons"></div>

            </div>
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

          <div class="footer-buttons" style="flex-direction:column;align-items:flex-start;gap:10px;">
            <div class="export-timeline-toolbar" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
              <label for="exportTimelinePresetPkg">Report period:</label>
              <select id="exportTimelinePresetPkg" class="search-input" style="max-width:220px;padding:6px 8px;">
                <option value="all">All time</option>
                <option value="7d">Last 7 days</option>
                <option value="30d">Last 30 days</option>
                <option value="90d">Last 90 days</option>
                <option value="ytd">Year to date</option>
                <option value="custom">Custom range</option>
              </select>
              <span id="exportCustomRangeWrapPkg" class="export-custom-date-range" style="display:none;align-items:center;gap:8px;flex-wrap:wrap;">
                <span class="export-range-label">From</span>
                <div class="date-filter"><input type="date" id="exportDateFromPkg" class="date-input"></div>
                <span class="export-range-label">To</span>
                <div class="date-filter"><input type="date" id="exportDateToPkg" class="date-input"></div>
              </span>
            </div>
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
      const customTripsData = <?= json_encode(array_map(function($b) {
          return [
              'booking_id'   => $b['booking_id'],
              'user_name'    => $b['user_name'],
              'status'       => $b['status'],
              'created_at'   => $b['created_at'],
              'destinations' => $b['destinations'] ?? [],
          ];
      }, $customTripsWithDestinations), JSON_UNESCAPED_UNICODE) ?>;

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
              'created_at'         => $p['created_at'] ?? '',
              'overview'           => $p['overview'] ?? [],
              'highlights'         => $p['highlights'] ?? [],
              'itinerary'          => $p['itinerary'] ?? [],
              'accommodation'      => $p['accommodation'] ?? [],
              'included'           => $p['included'] ?? [],
              'excluded'           => $p['excluded'] ?? [],
          ];
      }, $packages), JSON_UNESCAPED_UNICODE) ?>;

      function pad2(n) { return String(n).padStart(2, '0'); }
      function ymdLocal(d) { return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate()); }

      function bindExportPreset(presetId, wrapId) {
        const presetEl = document.getElementById(presetId);
        const wrap = document.getElementById(wrapId);
        function toggle() {
          if (!presetEl || !wrap) return;
          wrap.style.display = presetEl.value === 'custom' ? 'inline-flex' : 'none';
        }
        if (presetEl) {
          presetEl.addEventListener('change', toggle);
          toggle();
        }
      }
      bindExportPreset('exportTimelinePresetTrip', 'exportCustomRangeWrapTrip');
      bindExportPreset('exportTimelinePresetPkg', 'exportCustomRangeWrapPkg');

      function resolveExportRange(prefix) {
        const presetEl = document.getElementById('exportTimelinePreset' + prefix);
        const v = presetEl ? presetEl.value : 'all';
        if (v === 'custom') {
          const f = document.getElementById('exportDateFrom' + prefix).value;
          const t = document.getElementById('exportDateTo' + prefix).value;
          if (!f || !t) { alert('Please select both From and To dates for a custom range.'); return null; }
          if (f > t) { alert('From date must be before or equal to To date.'); return null; }
          return { start: f, end: t };
        }
        if (v === 'all') return { start: null, end: null };
        const today = new Date();
        const end = new Date(today.getFullYear(), today.getMonth(), today.getDate());
        let start = new Date(end);
        if (v === '7d') start.setDate(start.getDate() - 6);
        else if (v === '30d') start.setDate(start.getDate() - 29);
        else if (v === '90d') start.setDate(start.getDate() - 89);
        else if (v === 'ytd') start = new Date(today.getFullYear(), 0, 1);
        else return { start: null, end: null };
        return { start: ymdLocal(start), end: ymdLocal(end) };
      }

      function inDateRange(dateStr, range) {
        if (!range || (!range.start && !range.end)) return true;
        const d = (dateStr && String(dateStr).trim().slice(0, 10)) || '';
        if (!d) return false;
        if (range.start && d < range.start) return false;
        if (range.end && d > range.end) return false;
        return true;
      }

      function periodLabel(range) {
        if (!range || (!range.start && !range.end)) return 'All time';
        return range.start + ' to ' + range.end;
      }

      // ── Export customized trips (trip bookings) ──────────────
      document.getElementById('exportTripBtn').addEventListener('click', function () {
        if (!customTripsData || customTripsData.length === 0) {
          alert('No customized trips to export!');
          return;
        }
        const range = resolveExportRange('Trip');
        if (range === null) return;
        const list = customTripsData.filter(function (b) {
          return inDateRange((b.created_at || '').slice(0, 10), range);
        });
        if (!list.length) {
          alert('No customized trips in the selected period.');
          return;
        }

        const sep = '='.repeat(70);
        const subSep = '-'.repeat(70);
        const now = new Date();
        const dateStr = now.toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' });
        const timeStr = now.toLocaleTimeString('en-GB');

        let report = '';
        report += sep + '\n';
        report += '     CEYLON GO — CUSTOMIZED TRIP BOOKINGS REPORT\n';
        report += sep + '\n';
        report += '  Generated on   : ' + dateStr + ' at ' + timeStr + '\n';
        report += '  Report period  : ' + periodLabel(range) + '\n';
        report += '  Total Bookings : ' + list.length + '\n';
        report += sep + '\n\n';

        list.forEach(function (b, index) {
          report += 'BOOKING ' + (index + 1) + ' OF ' + list.length + '\n';
          report += sep + '\n';
          report += '  BOOKING DETAILS\n';
          report += '  ' + subSep + '\n';
          report += '  Booking ID   : ' + b.booking_id + '\n';
          report += '  Customer     : ' + b.user_name + '\n';
          report += '  Status       : ' + b.status.charAt(0).toUpperCase() + b.status.slice(1) + '\n';
          report += '  Submitted On : ' + b.created_at + '\n\n';

          if (b.destinations && b.destinations.length > 0) {
            report += '  DESTINATIONS (' + b.destinations.length + ')\n';
            report += '  ' + subSep + '\n';
            b.destinations.forEach(function (d, di) {
              report += '  Destination ' + (di + 1) + '\n';
              report += '    Location   : ' + d.destination + '\n';
              report += '    People     : ' + d.people_count + '\n';
              report += '    Days       : ' + d.days + '\n';
              if (d.hotel && String(d.hotel).trim() !== '') report += '    Hotel      : ' + d.hotel + '\n';
              report += '    Transport  : ' + d.transport + '\n';
            });
          } else {
            report += '  DESTINATIONS\n';
            report += '  ' + subSep + '\n';
            report += '  No destination details recorded.\n';
          }
          report += '\n' + sep + '\n\n';
        });

        report += sep + '\n';
        report += '  END OF REPORT\n';
        report += '  Ceylon Go Admin  |  ' + dateStr + '\n';
        report += sep + '\n';

        const blob = new Blob([report], { type: 'text/plain' });
        const link = document.createElement('a');
        const tag = range.start && range.end ? range.start + '_to_' + range.end : 'all_time';
        link.href = URL.createObjectURL(blob);
        link.download = 'ceylongo_customized_trips_' + tag + '_' + now.toISOString().slice(0, 10) + '.txt';
        link.click();
      });

      // ── Export catalog packages ──────────────────────────────
      document.getElementById('exportBtn').addEventListener('click', function () {
        if (packagesData.length === 0) {
          alert('No packages to export!');
          return;
        }
        const range = resolveExportRange('Pkg');
        if (range === null) return;
        const list = packagesData.filter(function (p) {
          return inDateRange((p.created_at || '').slice(0, 10), range);
        });
        if (!list.length) {
          alert('No packages in the selected period (by catalog created date).');
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
        report += '  Report period : ' + periodLabel(range) + '\n';
        report += '  Total Packages: ' + list.length + '\n';
        report += '='.repeat(70) + '\n\n';

        list.forEach(function (p, index) {

          // ── Package header ──
          report += 'PACKAGE ' + (index + 1) + ' OF ' + list.length + '\n';
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
        const tag = range.start && range.end ? range.start + '_to_' + range.end : 'all_time';
        link.href = URL.createObjectURL(blob);
        link.download = 'ceylongo_packages_' + tag + '_' + fileDate + '.txt';
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

      // ── PAGINATION FOR USERS ───────────────────

      // Get all rows initially rendered by PHP
      const allUserRows = Array.from(document.querySelectorAll("#packagesTableBody tr"))
          .filter(row => row.children.length > 1); // ignore "no data" row

      const rowsPerPageSelect = document.getElementById("rowsPerPage");
      const paginationControls = document.getElementById("paginationControls");

      let currentPage = 1;
      let rowsPerPage = parseInt(rowsPerPageSelect.value);

      // Render table based on page
      function renderTable() {
          const tbody = document.getElementById("packagesTableBody");
          tbody.innerHTML = "";

          const start = (currentPage - 1) * rowsPerPage;
          const end = start + rowsPerPage;

          const pageRows = allUserRows.slice(start, end);

          pageRows.forEach(row => tbody.appendChild(row));

          renderPagination();
      }

      // Pagination buttons
      function renderPagination() {
          const totalPages = Math.ceil(allUserRows.length / rowsPerPage);

          paginationControls.innerHTML = `
              <button class="filter-btn small-btn" ${currentPage === 1 ? "disabled" : ""} onclick="prevPage()">Prev</button>

              <span class="page-info">
                  Page ${currentPage} of ${totalPages}
              </span>

              <button class="filter-btn small-btn" ${currentPage === totalPages ? "disabled" : ""} onclick="nextPage()">Next</button>
          `;
      }

      // Navigation
      function nextPage() {
          const totalPages = Math.ceil(allUserRows.length / rowsPerPage);
          if (currentPage < totalPages) {
              currentPage++;
              renderTable();
          }
      }

      function prevPage() {
          if (currentPage > 1) {
              currentPage--;
              renderTable();
          }
      }

      // Change rows per page
      rowsPerPageSelect.addEventListener("change", function() {
          rowsPerPage = parseInt(this.value);
          currentPage = 1;
          renderTable();
      });

      // Initialize
      renderTable();
    </script>
  </body>
</html>