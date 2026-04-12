<?php 
    // Session is already started in public/index.php
    require_once(__DIR__ . '/../../config/config.php');

    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        header("Location: /CeylonGo/public/login");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Font Awesome (REQUIRED) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <!-- Optional admin-only overrides -->
        <link rel="stylesheet" href="/CeylonGO/public/css/admin/user.css">

        <!-- Shared Transport Layout -->
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/base.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/navbar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/sidebar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/footer.css">

        <!-- Responsive styles (always last) -->
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">
        <title>User Management</title>
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
                    <li class="active"><a href="/CeylonGo/public/admin/users"><i class="fa-solid fa-users"></i> Users</a></li>
                    <li><a href="/CeylonGo/public/admin/bookings"><i class="fa-regular fa-calendar"></i> Bookings</a></li>
                    <li><a href="/CeylonGo/public/admin/service"><i class="fa-solid fa-van-shuttle"></i> Service Providers</a></li>
                    <li><a href="/CeylonGo/public/admin/payments"><i class="fa-solid fa-credit-card"></i> Payments</a></li>
                    <li><a href="/CeylonGo/public/admin/inquiries"><i class="fa-solid fa-circle-question"></i> Inquiries</a></li>
                    <li><a href="/CeylonGo/public/admin/packages"><i class="fa-solid fa-bullhorn"></i> Packages</a></li>
                    <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-regular fa-star"></i> Reviews</a></li>
                    <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports & Analysis</a></li>
                </ul>
            </div>

            <div class="main-content">
                <div class="user-management">
                    <h2 class="page-title">User Management</h2>
                    <br>

                    <form method="GET" action="/CeylonGo/public/admin/users">
                        <div class="toolbar">
                            <div class="search-section">
                                <input type="text" placeholder="Search by name or email" id="searchInput" class="search-input">
                                <button type="button" class="search-btn" onclick="applySearch()">🔍</button>
                            </div>

                            <div class="filter-buttons">
                                <button type="submit" name="status" value="all"
                                    class="filter-btn <?= ($selectedStatus=='all')?'active':'' ?>">All</button>

                                <button type="submit" name="status" value="active"
                                    class="filter-btn <?= ($selectedStatus=='active')?'active':'' ?>">Active</button>

                                <button type="submit" name="status" value="inactive"
                                    class="filter-btn <?= ($selectedStatus=='inactive')?'active':'' ?>">Inactive</button>
                            </div>
                        </div>
                    </form>

                    <div class="stats-section">
                        <div class="stats-grid">
                            <div class="stat-box">
                                <strong>Total</strong><br>
                                <span><?= $stats['total'] ?? 0 ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Active</strong><br>
                                <span><?= $stats['active'] ?? 0 ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Inactive</strong><br>
                                <span><?= $stats['inactive'] ?? 0 ?></span>
                            </div>
                        </div>
                    </div>
                    <br>

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
                        <table class="user-table">
                            <thead>
                                <tr>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Contact Number</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="userTableBody">
                            <?php if(count($users)>0): ?>
                                <?php foreach($users as $user): ?>
                                    <tr data-id="<?= $user['id'] ?>" data-created-at="<?= htmlspecialchars(substr($user['created_at'] ?? '', 0, 10)) ?>">
                                        <td><?= htmlspecialchars($user['first_name']) ?></td>
                                        <td><?= htmlspecialchars($user['last_name']) ?></td>
                                        <td><?= htmlspecialchars($user['contact_number']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td>
                                            <?= $user['is_active']
                                                ? "<span style='color:green;font-weight:bold'>Active</span>"
                                                : "<span style='color:red;font-weight:bold'>Inactive</span>" ?>
                                        </td>
                                        <td class="actions">
                                            <button class="icon-btn edit-btn">✏️</button>
                                            <?php if ($user['is_active']): ?>
                                                <button class="icon-btn danger deactivate-btn">🚩</button>
                                            <?php else: ?>
                                                <button class="icon-btn activate-btn">✅</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" style="text-align:center;">No users found.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="footer-buttons" style="margin-top: 24px;">
                        <a href="/CeylonGo/public/admin/reports?type=users" class="report-link-btn">
                            <i class="fa-solid fa-file-arrow-down"></i>
                            Generate User Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Add/Edit Modal -->
            <div class="modal" id="userModal">
                <div class="modal-content">
                    <h3 id="modalTitle">Edit User</h3>
                    <form method="POST" action="/CeylonGo/public/admin/users" id="userForm">
                        <input type="hidden" name="user_id" id="user_id">

                        <input type="text" name="first_name" id="first_name" placeholder="Enter first name" required><br>
                        <input type="text" name="last_name" id="last_name" placeholder="Enter last name" required><br>

                        <!-- Phone number validation: exactly 10 digits -->
                        <input type="text" name="contact" id="contact" placeholder="Enter contact number" required
                            pattern="\d{10}" title="Phone number must be exactly 10 digits"><br>

                        <!-- Email validation: format name@gmail.com -->
                        <input type="email" name="email" id="email" placeholder="Enter email address" required
                            pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                            title="Please enter a valid email, e.g., name@gmail.com"><br>

                        <button type="submit" name="edit_user" id="submitBtn" class="submit-btn">Save Changes</button>
                        <button type="button" class="cancel-btn" id="closeModalBtn">Cancel</button>
                    </form>
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

            const modal = document.getElementById("userModal");
            const modalTitle = document.getElementById("modalTitle");
            const submitBtn = document.getElementById("submitBtn");

            document.getElementById("closeModalBtn").addEventListener("click", () => modal.style.display="none");
            window.onclick = (e) => { if(e.target === modal) modal.style.display="none"; };

            // Edit user
            document.querySelectorAll(".edit-btn").forEach(btn => {
                btn.addEventListener("click", function(){
                    const row = this.closest("tr");

                    const id = row.dataset.id;
                    const first_name = row.cells[0].innerText;
                    const last_name = row.cells[1].innerText;
                    const contact = row.cells[2].innerText;
                    const email = row.cells[3].innerText;

                    modal.style.display="flex";
                    modalTitle.innerText = "Edit User";
                    submitBtn.name = "edit_user";
                    submitBtn.innerText = "Save Changes";

                    document.getElementById("user_id").value = id;
                    document.getElementById("first_name").value = first_name;
                    document.getElementById("last_name").value = last_name;
                    document.getElementById("contact").value = contact;
                    document.getElementById("email").value = email;
                });
            });

            // Activate / Deactivate user
            document.getElementById("userTableBody").addEventListener("click", function(e) {

                const button = e.target.closest("button");
                if (!button) return;

                if (!button.classList.contains("deactivate-btn") &&
                    !button.classList.contains("activate-btn")) return;

                const row = button.closest("tr");
                const userId = row.dataset.id;
                const status = button.classList.contains("deactivate-btn") ? 0 : 1;

                if (!confirm("Are you sure?")) return;

                fetch("/CeylonGo/public/admin/user/status", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `user_id=${userId}&status=${status}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert("Failed to update user status");
                    }
                })
                .catch(() => alert("Server error"));
            });

            // Search
            function applySearch() {
                const searchTerm = document.getElementById("searchInput").value.toLowerCase();
                const tbody = document.getElementById("userTableBody");
                let visibleCount = 0;

                document.querySelectorAll("#userTableBody tr").forEach(row => {
                    // Skip the "no users found" message row itself
                    if (row.id === "noResultsRow") return;

                    const text = row.innerText.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = "";
                        visibleCount++;
                    } else {
                        row.style.display = "none";
                    }
                });

                // Remove existing "no results" row if present
                const existing = document.getElementById("noResultsRow");
                if (existing) existing.remove();

                // Insert "No users found" row if nothing matches
                if (visibleCount === 0) {
                    const noResultsRow = document.createElement("tr");
                    noResultsRow.id = "noResultsRow";
                    noResultsRow.innerHTML = `<td colspan="6" style="text-align:center; padding:20px; color:#888;">No users found.</td>`;
                    tbody.appendChild(noResultsRow);
                }
            }

            //enter key search
            document.getElementById("searchInput").addEventListener("keydown", function (e) {
                if (e.key === "Enter") {
                    e.preventDefault(); // prevent accidental form submit
                    applySearch();
                }
            });

            //auto search
            document.getElementById("searchInput").addEventListener("input", applySearch);

            // ── PAGINATION FOR USERS ───────────────────

            // Get all rows initially rendered by PHP
            const allUserRows = Array.from(document.querySelectorAll("#userTableBody tr"))
                .filter(row => row.children.length > 1); // ignore "no data" row

            const rowsPerPageSelect = document.getElementById("rowsPerPage");
            const paginationControls = document.getElementById("paginationControls");

            let currentPage = 1;
            let rowsPerPage = parseInt(rowsPerPageSelect.value);

            // Render table based on page
            function renderTable() {
                const tbody = document.getElementById("userTableBody");
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