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

        <!-- Font Awesome (REQUIRED) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <!-- Shared Transport Layout -->
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/base.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/navbar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/sidebar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/footer.css">

        <!-- Optional admin-only overrides -->
        <link rel="stylesheet" href="/CeylonGO/public/css/admin/admin_overrides.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/admin/admin_service.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/admin/admin_common.css">

        <!-- Responsive styles (always last) -->
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">

        <title>Service Provider Management</title>
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
                <li class="active"><a href="/CeylonGo/public/admin/service"><i class="fa-solid fa-van-shuttle"></i> Service Providers</a></li>
                <li><a href="/CeylonGo/public/admin/payments"><i class="fa-solid fa-credit-card"></i> Payments</a></li>
                <li><a href="/CeylonGo/public/admin/inquiries"><i class="fa-solid fa-circle-question"></i> Inquiries</a></li>
                <li><a href="/CeylonGo/public/admin/promotions"><i class="fa-solid fa-bullhorn"></i> Packages</a></li>
                <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-solid fa-star"></i> Reviews</a></li>
                <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports</a></li>
                <li><a href="/CeylonGo/public/admin/settings"><i class="fa-solid fa-gear"></i> Settings</a></li>
                </ul>
            </div>

            <div class="main-content">
                <div class="provider-management">
                
                    <h2 class="page-title">Service Provider Management</h2>
                    <br>
                    
                    <div class="toolbar">
                        <div class="search-section">
                            <input type="text" placeholder="Search by role, name or email" id="searchInput" class="search-input">
                            <button type="button" class="search-btn" onclick="applySearch()">🔍</button>
                        </div>
                        <div class="filter-buttons">
                            <button class="filter-btn active" onclick="filterProviders('all')">All</button>
                            <button class="filter-btn" onclick="filterProviders('guide')">Tour Guides</button>
                            <button class="filter-btn" onclick="filterProviders('hotel')">Hotels</button>
                            <button class="filter-btn" onclick="filterProviders('transport')">Transport Providers</button>
                        </div>
                    </div>

                    <div class="stats-section">
                        <h4>Provider Statistics</h4>
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
                        <table class="provider-table">
                            <thead>
                                <tr>
                                    <th>Service Provider Role</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody id="providerTableBody">
                                <?php if (!empty($providers)): ?>
                                    <?php foreach ($providers as $provider): ?>
                                        <tr>
                                            <td><?= $roleLabels[$provider['role']] ?? ucfirst($provider['role']) ?></td>
                                            <td><?= htmlspecialchars($provider['provider_name']) ?></td>
                                            <td><?= htmlspecialchars($provider['email']) ?></td>
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
                    
                    <div class="footer-buttons">
                        <button class="footer-btn black" id="exportBtn">Export Details</button>
                    </div>
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

            function applySearch() {
                const searchTerm = document
                    .getElementById("searchInput")
                    .value
                    .toLowerCase();

                document.querySelectorAll("#providerTableBody tr").forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? "" : "none";
                });
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
                const rows = document.querySelectorAll("#providerTableBody tr");
                const buttons = document.querySelectorAll(".filter-btn");

                // Toggle active class
                buttons.forEach(btn => btn.classList.remove("active"));
                event.target.classList.add("active");

                rows.forEach(row => {
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
            }
        </script>
    </body>
</html>