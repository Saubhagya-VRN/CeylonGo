<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Payments Management</title>
        <link rel="stylesheet" href="../../public/css/admin/admin_payments.css">
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
                <li><a href="admin_bookings.php">Bookings</a></li>
                <li><a href="admin_service.php">Service Providers</a></li>
                <li><a href="admin_payments.php" class="active">Payments</a></li>
                <li><a href="admin_reports.php">Reports</a></li>
                <li><a href="admin_reviews.php">Reviews</a></li>
                <li><a href="admin_inquiries.php">Inquiries</a></li>
                <li><a href="admin_settings.php">System Settings</a></li>
                <li><a href="admin_promotions.php">Promotions</a></li>
                <li><a href="../../controllers/admin/logout.php">Logout</a></li>
            </ul>
        </aside>

        <div class="main-content">
            <div class="payments-management">
            
                <h2 class="page-title">Payments Management</h2>
                <br><br>

                <div class="toolbar">
                    <div class="search-section">
                    <input type="text" placeholder="Search by payment ID/ user" class="search-input">
                    <button class="search-btn">🔍</button>
                    </div>
                    <div class="filter-buttons">
                    <button class="filter-btn active">All</button>
                    <button class="filter-btn">Pending</button>
                    <button class="filter-btn">Completed</button>
                    <button class="filter-btn">Cancelled</button>
                    </div>
                </div>

                <div class="stats-section">
                    <div class="stat-card">Total Payments <span>200</span></div>
                    <div class="stat-card">Completed <span>189</span></div>
                    <div class="stat-card">Pending <span>16</span></div>
                    <div class="stat-card">Cancelled <span>5</span></div>
                </div>

                <div class="payments-section">
                    <table class="payments-table">
                    <thead>
                        <tr>
                        <th>Payment ID</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <td>#PAY001</td>
                        <td>John Doe</td>
                        <td>$120.00</td>
                        <td><span class="status pending">Pending</span></td>
                        <td class="actions">
                            <button class="icon-btn">✔️</button>
                            <button class="icon-btn danger">❌</button>
                            <button class="icon-btn">👁️</button>
                        </td>
                        </tr>
                        <tr>
                        <td>#PAY002</td>
                        <td>Jane Smith</td>
                        <td>$250.00</td>
                        <td><span class="status completed">Completed</span></td>
                        <td class="actions">
                            <button class="icon-btn">↩️</button>
                            <button class="icon-btn">👁️</button>
                        </td>
                        </tr>
                        <tr>
                        <td>#PAY003</td>
                        <td>Alice Johnson</td>
                        <td>$75.00</td>
                        <td><span class="status canceled">Cancelled</span></td>
                        <td class="actions">
                            <button class="icon-btn">👁️</button>
                        </td>
                        </tr>
                    </tbody>
                    </table>
                </div>

                <div class="footer-buttons">
                    <button class="footer-btn black">Contact the Bank</button>
                </div>
            </div>
        </div>
    </body>
</html>
