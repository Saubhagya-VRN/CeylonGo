<?php
    // Session already started in public/index.php
    require_once(__DIR__ . '/../../config/config.php');
    require_once(__DIR__ . '/../../core/Database.php');

    // Admin-only access
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        header("Location: /CeylonGo/public/login");
        exit();
    }

    $conn = Database::getMysqliConnection();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/admin/service.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/base.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/navbar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/sidebar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/footer.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">
        <title>Service Provider Management</title>
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
                    <li class="active"><a href="/CeylonGo/public/admin/service"><i class="fa-solid fa-van-shuttle"></i> Service Providers</a></li>
                    <li><a href="/CeylonGo/public/admin/payments"><i class="fa-solid fa-credit-card"></i> Payments</a></li>
                    <li><a href="/CeylonGo/public/admin/inquiries"><i class="fa-solid fa-circle-question"></i> Inquiries</a></li>
                    <li><a href="/CeylonGo/public/admin/packages"><i class="fa-solid fa-bullhorn"></i> Packages</a></li>
                    <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-regular fa-star"></i> Reviews</a></li>
                    <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports & Analysis</a></li>
                </ul>
            </div>

            <div class="main-content">
                <div class="provider-management">
                    <h2 class="page-title">Service Provider Management</h2>
                    <br>
                    <form method="GET" action="/CeylonGo/public/admin/service">
                        <div class="toolbar">
                            <div class="search-section">
                                <input type="text" placeholder="Search by role/ name/ email" id="searchInput" class="search-input">
                                <button type="button" class="search-btn" onclick="applySearch()">🔍</button>
                            </div>

                            <div class="filter-buttons">
                                <button type="submit" name="status" value="all"
                                    class="filter-btn <?= ($selectedStatus=='all')?'active':'' ?>">All
                                </button>

                                <button type="submit" name="status" value="active"
                                    class="filter-btn <?= ($selectedStatus=='active')?'active':'' ?>">Active
                                </button>

                                <button type="submit" name="status" value="inactive"
                                    class="filter-btn <?= ($selectedStatus=='inactive')?'active':'' ?>">Inactive
                                </button>

                                <button type="button" class="filter-btn" onclick="filterProviders('guide')">Tour Guides</button>
                                <button type="button" class="filter-btn" onclick="filterProviders('hotel')">Hotels</button>
                                <button type="button" class="filter-btn" onclick="filterProviders('transport')">Transport Providers</button>
                            </div>
                        </div>
                    </form>

                    <div class="stats-section">
                        <div class="stats-grid">
                            <div class="stat-box">
                                <strong>Total Providers</strong><br>
                                <span><?= $stats['total'] ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Tour Guides</strong><br>
                                <span><?= $stats['guide'] ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Hotels</strong><br>
                                <span><?= $stats['hotel'] ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Transport Providers</strong><br>
                                <span><?= $stats['transport'] ?></span>
                            </div>
                        </div>
                    </div>
                    <br>

                    <div class="providers-section">
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
                        <table class="provider-table">
                            <thead>
                                <tr>
                                    <th>Service Provider Role</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="providerTableBody">
                                <?php if (!empty($providers)): ?>
                                    <?php foreach ($providers as $provider): ?>
                                        <tr data-id="<?= htmlspecialchars($provider['id']) ?>"
                                            data-registered-at="<?= htmlspecialchars(substr($provider['registered_at'] ?? '', 0, 10)) ?>">
                                            <td><?= $roleLabels[$provider['role']] ?? ucfirst($provider['role']) ?></td>
                                            <td><?= htmlspecialchars($provider['provider_name']) ?></td>
                                            <td><?= htmlspecialchars($provider['email']) ?></td>
                                            <td>
                                                <?= $provider['is_active']
                                                    ? "<span style='color:green;font-weight:bold'>Active</span>"
                                                    : "<span style='color:red;font-weight:bold'>Inactive</span>" ?>
                                            </td>
                                            <td class="actions">
                                                <?php if ($provider['is_active']): ?>
                                                    <button class="icon-btn danger deactivate-btn">🚩</button>
                                                <?php else: ?>
                                                    <button class="icon-btn activate-btn">✅</button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" style="text-align:center;">No service providers found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="footer-buttons" style="margin-top: 24px;">
                        <a href="/CeylonGo/public/admin/reports?type=providers" class="report-link-btn">
                            Generate Service Provider Report
                        </a>
                    </div>
                </div>
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

            function applySearch() {
                const searchTerm = document.getElementById("searchInput").value.toLowerCase();
                allUserRows.forEach(row => {
                    row.style.display = row.innerText.toLowerCase().includes(searchTerm) ? "" : "none";
                });
                currentPage = 1;
                renderTable();
            }

            // Enter key search
            document.getElementById("searchInput").addEventListener("keydown", function (e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    applySearch();
                }
            });

            // Auto-search while typing
            document.getElementById("searchInput").addEventListener("input", applySearch);

            function filterProviders(role) {
                const buttons = document.querySelectorAll(".filter-btn");
                buttons.forEach(btn => btn.classList.remove("active"));
                event.target.classList.add("active");

                allUserRows.forEach(row => {
                    const roleCell = row.cells[0].innerText.toLowerCase();
                    if (role === "all") {
                        row.style.display = "";
                    } else if (role === "guide" && roleCell.includes("guide")) {
                        row.style.display = "";
                    } else if (role === "hotel" && roleCell.includes("hotel")) {
                        row.style.display = "";
                    } else if (role === "transport" && roleCell.includes("transport")) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                });
                currentPage = 1;
                renderTable();
            }

            // Activate / Deactivate providers
            document.getElementById("providerTableBody").addEventListener("click", function(e) {
                const button = e.target.closest("button");
                if (!button) return;

                if (!button.classList.contains("deactivate-btn") &&
                    !button.classList.contains("activate-btn")) return;

                const row = button.closest("tr");
                const providerId = row.dataset.id;
                const status = button.classList.contains("deactivate-btn") ? 0 : 1;

                if (!confirm("Are you sure you want to change this provider's status?")) return;

                fetch("/CeylonGo/public/admin/provider/status", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `provider_id=${providerId}&status=${status}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // simple way to refresh table
                    } else {
                        alert("Failed to update provider status");
                    }
                })
                .catch(() => alert("Server error"));
            });

            // ── PAGINATION FOR USERS ───────────────────

            // Get all rows initially rendered by PHP
            const allUserRows = Array.from(document.querySelectorAll("#providerTableBody tr"))
                .filter(row => row.children.length > 1); // ignore "no data" row

            const rowsPerPageSelect = document.getElementById("rowsPerPage");
            const paginationControls = document.getElementById("paginationControls");

            let currentPage = 1;
            let rowsPerPage = parseInt(rowsPerPageSelect.value);

            // Render table based on page
            function renderTable() {
                const tbody = document.getElementById("providerTableBody");
                tbody.innerHTML = "";

                const visibleRows = allUserRows.filter(row => row.style.display !== "none");
                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                const pageRows = visibleRows.slice(start, end);

                if (visibleRows.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:20px;color:#888;">No service providers found.</td></tr>';
                    paginationControls.innerHTML = '';
                    return;
                }

                pageRows.forEach(row => tbody.appendChild(row));
                renderPagination(visibleRows.length);
            }

            // Pagination buttons
            function renderPagination(totalVisible) {
                const totalPages = Math.ceil(totalVisible / rowsPerPage);

                paginationControls.innerHTML = `
                    <button class="filter-btn small-btn" ${currentPage === 1 ? "disabled" : ""} onclick="prevPage()">Prev</button>
                    <span class="page-info">Page ${currentPage} of ${totalPages || 1}</span>
                    <button class="filter-btn small-btn" ${currentPage >= totalPages ? "disabled" : ""} onclick="nextPage()">Next</button>
                `;
            }

            // Navigation
            function nextPage() {
                const visibleRows = allUserRows.filter(row => row.style.display !== "none");
                const totalPages = Math.ceil(visibleRows.length / rowsPerPage);
                if (currentPage < totalPages) { currentPage++; renderTable(); }
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