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
        <link rel="stylesheet" href="/CeylonGO/public/css/admin/admin_reviews.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/admin/admin_common.css">

        <!-- Responsive styles (always last) -->
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">

        <title>Reviews Management</title>
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
                <li><a href="/CeylonGo/public/admin/promotions"><i class="fa-solid fa-bullhorn"></i> Packages</a></li>
                <li class="active"><a href="/CeylonGo/public/admin/reviews"><i class="fa-solid fa-star"></i> Reviews</a></li>
                <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports</a></li>
                </ul>
            </div>

            <div class="main-content">
                <div class="reviews-management">

                    <h2 class="page-title">Reviews Management</h2>
                    <br>

                    <div class="filter-buttons">
                        <button class="filter-btn active">User Reviews</button>
                        <button class="filter-btn">Service Feedback</button>
                    </div>
                    <br>

                    <h3>Recent Reviews</h3><br>
                    <p class="sub-text">Latest user comments</p>

                    <div class="users-section">
                        <table class="user-table">
                            <thead>
                            <tr>
                                <th>User</th>
                                <th>Comment</th>
                                <th>Rating</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>John Doe</td>
                                <td>Great service! Highly recommended.</td>
                                <td>⭐⭐⭐⭐⭐</td>
                                <td class="actions">
                                <button class="icon-btn">💬</button>
                                <button class="icon-btn danger">❌</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Jane Smith</td>
                                <td>Could be better, had some issues with delivery.</td>
                                <td>⭐⭐⭐⭐⭐</td>
                                <td class="actions">
                                <button class="icon-btn">💬</button>
                                <button class="icon-btn danger">❌</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <br><br>

                    <h3>Filter by Rating</h3><br>
                    <div class="filter-buttons">
                        <button class="filter-btn">5 Star</button>
                        <button class="filter-btn">4 Star</button>
                        <button class="filter-btn">3 Star</button>
                    </div>
                    <br><br>

                    <h3>Top Rated Services</h3><br>
                    <p class="sub-text">User satisfaction ratings</p>

                    <div class="users-section">
                        <table class="user-table">
                            <thead>
                            <tr>
                                <th>Service</th>
                                <th>Rating</th>
                                <th>Total Reviews</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Service A 🏆</td>
                                <td>4.8</td>
                                <td>200 reviews</td>
                            </tr>
                            <tr>
                                <td>Service B 🔴</td>
                                <td>4.5</td>
                                <td>150 reviews</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <br><br>

                    <h3>Overall Ratings</h3><br>
                    <p class="sub-text">Service Performance Metrics</p>

                    <div class="footer-buttons">
                        <button class="footer-btn">Average Rating: <b>4.6</b></button>
                        <button class="footer-btn">Total Reviews: <b>350 (+20%)</b></button>
                        <button class="footer-btn">Positive Feedback: <b>85% (+5%)</b></button>
                    </div>

                    <div class="footer-buttons">
                        <button class="footer-btn black">Export Reviews</button>
                    </div>
                    <br><br>
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
        </script>
    </body>
</html>
