<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reviews Management</title>
        <link rel="stylesheet" href="../../public/css/admin/admin_reviews.css">
    </head>

    <body>
        <aside class="sidebar">
            <div class="sidebar-brand">
                <img src="../../public/images/logo.png" alt="Ceylon Go Logo" class="logo-img">
                <h2>Ceylon Go</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="/CeylonGo/public/admin/dashboard">Home</a></li>
                <li><a href="/CeylonGo/public/admin/users">Users</a></li>
                <li><a href="/CeylonGo/public/admin/bookings">Bookings</a></li>
                <li><a href="/CeylonGo/public/admin/service">Service Providers</a></li>
                <li><a href="/CeylonGo/public/admin/payments">Payments</a></li>
                <li><a href="/CeylonGo/public/admin/reports">Reports</a></li>
                <li><a href="/CeylonGo/public/admin/reviews" class="active">Reviews</a></li>
                <li><a href="/CeylonGo/public/admin/inquiries">Inquiries</a></li>
                <li><a href="/CeylonGo/public/admin/settings">System Settings</a></li>
                <li><a href="/CeylonGo/public/admin/promotions">Promotions</a></li>
                <li><a href="/CeylonGo/public/logout">Logout</a></li>
            </ul>
        </aside>

        <div class="main-content">
            <div class="reviews-management">

                <h2 class="page-title">Reviews Management</h2>
                <br><br>

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
    </body>
</html>
