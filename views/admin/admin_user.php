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

        <!-- Shared Transport Layout -->
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/base.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/navbar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/sidebar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/footer.css">

        <!-- Optional admin-only overrides -->
        <link rel="stylesheet" href="/CeylonGO/public/css/admin/admin_overrides.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/admin/admin_user.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/admin/admin_common.css">

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
                <li><a href="/CeylonGo/public/admin/promotions"><i class="fa-solid fa-bullhorn"></i> Promotions</a></li>
                <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-solid fa-star"></i> Reviews</a></li>
                <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports</a></li>
                <li><a href="/CeylonGo/public/admin/settings"><i class="fa-solid fa-gear"></i> Settings</a></li>
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
                                    <button type="submit" name="status" value="all" class="filter-btn <?= ($selectedStatus=='all')?'active':'' ?>">All</button>
                                    <button type="submit" name="status" value="active" class="filter-btn <?= ($selectedStatus=='active')?'active':'' ?>">Active</button>
                                    <button type="submit" name="status" value="inactive" class="filter-btn <?= ($selectedStatus=='inactive')?'active':'' ?>">Inactive</button>
                            </div>
                        </div>
                    </form>

                    <div class="stats-section">
                        <h4>User Statistics</h4><br>
                        <p class="subheading">Overview of registered users</p>
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
                                    <tr data-id="<?= $user['id'] ?>">
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

                    <div class="footer-buttons">
                        <button class="footer-btn black" id="exportBtn">Export Users</button>
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
                <li><a href="/CeylonGo/public/admin/settings">Update Settings</a></li>
                <li><a href="/CeylonGo/public/admin/reports">Generate Report</a></li>
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
            function applySearch(){
                const searchTerm = document.getElementById("searchInput").value.toLowerCase();
                document.querySelectorAll("#userTableBody tr").forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? "" : "none";
                });
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

            // Export Users
            document.getElementById("exportBtn").addEventListener("click", () => {
                const rows = document.querySelectorAll("#userTableBody tr");
                if(rows.length === 0) { alert("No users to export!"); return; }

                let txtContent = "First Name\tLast Name\tContact Number\tEmail\n";
                rows.forEach(row => {
                    if(row.style.display !== "none"){
                        const fn = row.cells[0].innerText;
                        const ln = row.cells[1].innerText;
                        const cn = row.cells[2].innerText;
                        const em = row.cells[3].innerText;
                        txtContent += `${fn}\t${ln}\t${cn}\t${em}\n`;
                    }
                });

                const blob = new Blob([txtContent], { type: "text/plain" });
                const link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = "tourist_users.txt";
                link.click();
            });
        </script>
    </body>
</html>