<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Booking Management</title>
        <link rel="stylesheet" href="../../public/css/admin/bookings.css">
    </head>
    <body>
        <aside class="sidebar">
            <div class="sidebar-brand">
            <img src="../../public/images/logo.png" alt="Ceylon Go Logo" class="logo-img">
            <h2>Ceylon Go</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="admin_dashboard.php">Home</a></li>
                <li><a href="admin_user.php">Users</a></li>
                <li><a href="admin_bookings.php" class="active">Bookings</a></li>
                <li><a href="admin_service.php">Service Providers</a></li>
                <li><a href="admin_payments.php">Payments</a></li>
                <li><a href="admin_reports.php">Reports</a></li>
                <li><a href="admin_reviews.php">Reviews</a></li>
                <li><a href="admin_inquiries.php">Inquiries</a></li>
                <li><a href="admin_settings.php">System Settings</a></li>
                <li><a href="admin_promotions.php">Promotions</a></li>
                <li><a href="../../controllers/admin/logout.php">Logout</a></li>
            </ul>
        </aside>

        <div class="main-content">
            <div class="booking-management">
                
                <h2 class="page-title">Booking Management</h2>
                <br><br>

                <div class="toolbar">
                    <div class="search-section">
                    <input type="text" placeholder="Search by booking ID" class="search-input">
                    <button class="search-btn">🔍</button>
                    </div>
                    <div class="filter-buttons">
                        <button class="filter-btn active">Active</button>
                        <button class="filter-btn">Completed</button>
                        <button class="filter-btn">Cancelled</button>
                        <button class="filter-btn">All</button>
                    </div>
                    <div class="date-filter">
                        <input type="date" class="date-input">
                    </div>
                </div>

                <div class="stats-section">
                    <h4>Booking Statistics</h4><br>
                    <p class="subheading">Overview of current bookings</p>
                    <div class="stats-grid">
                    <div class="stat-box">
                        <strong>Total</strong><br>
                        <span>50</span>
                    </div>
                    <div class="stat-box">
                        <strong>Active</strong><br>
                        <span>30</span>
                    </div>
                    <div class="stat-box">
                        <strong>Cancelled</strong><br>
                        <span>10</span>
                    </div>
                    <div class="stat-box">
                        <strong>Completed</strong><br>
                        <span>10</span>
                    </div>
                    </div>
                </div>
                <br>

                <div class="bookings-section">
                    <table class="booking-table">
                    <thead>
                        <tr>
                        <th>Booking ID</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <td>#1234</td>
                        <td>John Doe</td>
                        <td><span class="status active">Active</span></td>
                        <td>2023-10-12</td>
                        <td class="actions">
                            <button class="icon-btn">👁️</button>
                            <button class="icon-btn danger">❌</button>
                        </td>
                        </tr>
                        <tr>
                        <td>#5678</td>
                        <td>Jane Smith</td>
                        <td><span class="status completed">Completed</span></td>
                        <td>2023-10-10</td>
                        <td class="actions">
                            <button class="icon-btn">👁️</button>
                            <button class="icon-btn danger">❌</button>
                        </td>
                        </tr>
                        <tr>
                        <td>#91011</td>
                        <td>Emily Johnson</td>
                        <td><span class="status cancelled">Cancelled</span></td>
                        <td>2023-10-01</td>
                        <td class="actions">
                            <button class="icon-btn">👁️</button>
                            <button class="icon-btn danger">❌</button>
                        </td>
                        </tr>
                    </tbody>
                    </table>
                </div>

                <div class="footer-buttons">
                    <button class="footer-btn black">+ New Booking</button>
                </div>
            </div>
        </div>
    </body>
</html>
