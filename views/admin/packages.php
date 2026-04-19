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
    <?php require_once __DIR__ . '/../partials/app_notify_script.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/admin/packages.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/base.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/navbar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/sidebar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/footer.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/responsive.css">
    <title>Manage Packages</title>
  </head>

  <body>
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
            <a href="/CeylonGo/public/admin/packages/new" class="report-link-btn">+ Add New Package</a>
          </div>
          <br>
          
          <h3 class="page-title" style="font-size:18px;">Tour packages (catalog)</h3>
          <p class="sub-text" style="color:#555;margin:8px 0 12px;">Fixed packages available for booking.</p>

          <form onsubmit="return false;">
            <div class="toolbar">
                <div class="search-section">
                    <input type="text" placeholder="Search by package name..." id="packageSearchInput" class="search-input">
                    <button type="button" class="search-btn" onclick="applyPackageSearch()">🔍</button>
                </div>
            </div>
          </form>

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

          <div class="footer-buttons" style="margin-top: 24px;">
              <a href="/CeylonGo/public/admin/packages/export" class="report-link-btn">
                  Download Package Report
              </a>
          </div>
        </div>
      </div>
    </div>

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

    <footer>
      <ul>
        <li><a href="/CeylonGo/public/admin/bookings">View All Bookings</a></li>
        <li><a href="/CeylonGo/public/admin/reports">Generate Reports</a></li>
        <li><a href="/CeylonGo/public/admin/payments">Payments</a></li>
      </ul>
    </footer>

    <script>
      // ── PAGINATION ─────────────────────────────────────────────────
      const allUserRows = Array.from(document.querySelectorAll("#packagesTableBody tr"))
          .filter(row => row.children.length > 1);

      const rowsPerPageSelect = document.getElementById("rowsPerPage");
      const paginationControls = document.getElementById("paginationControls");

      let currentPage = 1;
      let rowsPerPage = parseInt(rowsPerPageSelect.value);

      // Tracks currently active row set (full list or filtered)
      let activeRows = [...allUserRows];

      function renderTable() {
          const tbody = document.getElementById("packagesTableBody");
          tbody.innerHTML = "";

          const totalPages = Math.ceil(activeRows.length / rowsPerPage);

          // Clamp currentPage
          if (currentPage > totalPages) currentPage = Math.max(1, totalPages);

          const start = (currentPage - 1) * rowsPerPage;
          const end   = start + rowsPerPage;

          if (activeRows.length === 0) {
              const noRow = document.createElement("tr");
              noRow.innerHTML = `<td colspan="9" style="text-align:center; padding:20px; color:#888;">No packages found.</td>`;
              tbody.appendChild(noRow);
              paginationControls.innerHTML = "";
              return;
          }

          activeRows.slice(start, end).forEach(row => tbody.appendChild(row));
          renderPagination();
      }

      function renderPagination() {
          const totalPages = Math.ceil(activeRows.length / rowsPerPage);
          paginationControls.innerHTML = `
              <button class="filter-btn small-btn" ${currentPage === 1 ? "disabled" : ""} onclick="prevPage()">Prev</button>
              <span class="page-info">Page ${currentPage} of ${totalPages}</span>
              <button class="filter-btn small-btn" ${currentPage === totalPages ? "disabled" : ""} onclick="nextPage()">Next</button>
          `;
      }

      function nextPage() {
          const totalPages = Math.ceil(activeRows.length / rowsPerPage);
          if (currentPage < totalPages) { currentPage++; renderTable(); }
      }

      function prevPage() {
          if (currentPage > 1) { currentPage--; renderTable(); }
      }

      rowsPerPageSelect.addEventListener("change", function () {
          rowsPerPage = parseInt(this.value);
          currentPage = 1;
          renderTable();
      });

      // ── SEARCH ─────────────────────────────────────────────────────
      function applyPackageSearch() {
          const searchTerm = document.getElementById("packageSearchInput").value.toLowerCase().trim();

          if (searchTerm === "") {
              activeRows = [...allUserRows];
          } else {
              activeRows = allUserRows.filter(row => {
                  const title    = row.cells[1]?.innerText.toLowerCase() || "";
                  const location = row.cells[2]?.innerText.toLowerCase() || "";
                  const category = row.cells[3]?.innerText.toLowerCase() || "";
                  return title.includes(searchTerm) || location.includes(searchTerm) || category.includes(searchTerm);
              });
          }

          currentPage = 1;
          renderTable();
      }

      document.getElementById("packageSearchInput").addEventListener("keydown", function (e) {
          if (e.key === "Enter") { e.preventDefault(); applyPackageSearch(); }
      });
      document.getElementById("packageSearchInput").addEventListener("input", applyPackageSearch);

      // ── Initialize ─────────────────────────────────────────────────
      renderTable();

      // ── Navbar dropdown ───────────────────────────────────────────
      function toggleProfileDropdown() {
        document.getElementById('profileDropdown').classList.toggle('show');
      }
      document.addEventListener('click', function (event) {
        const dropdown  = document.getElementById('profileDropdown');
        const profilePic = document.querySelector('.profile-pic');
        if (dropdown && !dropdown.contains(event.target) && event.target !== profilePic) {
          dropdown.classList.remove('show');
        }
      });

      // ── Delete modal ──────────────────────────────────────────────
      const deleteModal = document.getElementById('deleteModal');
      function confirmDelete(id, title) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteModalMsg').textContent = 'Delete "' + title + '"? This cannot be undone.';
        deleteModal.style.display = 'flex';
      }
      function closeDeleteModal() { deleteModal.style.display = 'none'; }
      window.addEventListener('click', function (e) { if (e.target === deleteModal) closeDeleteModal(); });
    </script>
  </body>
</html>