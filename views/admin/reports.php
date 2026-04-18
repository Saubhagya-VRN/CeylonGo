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
        <?php require_once __DIR__ . '/../partials/app_notify_script.php'; ?>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Font Awesome (REQUIRED) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <!-- Optional admin-only overrides -->
        <link rel="stylesheet" href="/CeylonGO/public/css/admin/reports.css">

        <!-- Shared Transport Layout -->
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/base.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/navbar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/sidebar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/footer.css">

        <!-- Responsive styles (always last) -->
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">

        <title>Reports and Analysis</title>
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
                <li><a href="/CeylonGo/public/admin/inquiries"><i class="fa-solid fa-circle-question"></i> Inquiries</a></li>
                <li><a href="/CeylonGo/public/admin/packages"><i class="fa-solid fa-bullhorn"></i> Packages</a></li>
                <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-regular fa-star"></i> Reviews</a></li>
                <li class="active"><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports and Analysis</a></li>
                </ul>
            </div>

            <div class="main-content">
                <div class="reports-management">
                
                    <h2 class="page-title">Reports and Analysis</h2>
                    <br>

                    <div class="filter-buttons">
                        <button class="filter-btn <?= ($period === 'daily') ? 'active' : '' ?>" data-period="daily">Daily</button>
                        <button class="filter-btn <?= ($period === 'monthly') ? 'active' : '' ?>" data-period="monthly">Monthly</button>
                        <button class="filter-btn <?= ($period === 'yearly') ? 'active' : '' ?>" data-period="yearly">Yearly</button>
                    </div>
                    <p class="sub-text">Choose the timeline.</p>

                    <div class="stats-section">
                        <h4>Key Metrics</h4><br>
                        <div class="stats-grid">
                            <div class="stat-box">
                                <strong>Total Bookings</strong><br>
                                <span><?= $totalBookings ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Total Revenue</strong><br>
                                <span>$ 100,000</span>
                            </div>
                            <div class="stat-box">
                                <strong>Cancellations</strong><br>
                                <span><?= $totalCancellations ?></span>
                            </div>
                        </div>
                    </div>
                    <br><br>

                    <div class="chart-section">
                        <h3 class="section-title">Number of Bookings</h3>
                        <canvas id="bookingsChart" 
                                data-labels='<?= json_encode($labels) ?>' 
                                data-values='<?= json_encode($bookings) ?>'>
                        </canvas>
                    </div>

                    <div class="chart-section">
                        <h3 class="section-title">Cancellations</h3>
                        <canvas id="cancellationsChart" 
                                data-labels='<?= json_encode($labels) ?>' 
                                data-values='<?= json_encode($cancellations) ?>'>
                        </canvas>
                    </div>

                    <div class="footer-buttons">
                        <button class="footer-btn black">Download PDF</button>
                        <button class="footer-btn black">Download Excel</button>
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

        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="/CeylonGo/public/js/reports_charts.js"></script>
    </body>
</html>
