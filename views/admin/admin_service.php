<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Service Provider Management</title>
        <link rel="stylesheet" href="../../public/css/admin/admin_service.css">
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
                <li><a href="admin_service.php" class="active">Service Providers</a></li>
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
            <div class="provider-management">
            
                <h2 class="page-title">Service Provider Management</h2>
                <br><br>

                <div class="toolbar">
                    <div class="search-section">
                    <input type="text" placeholder="Search by name or status" class="search-input">
                    <button class="search-btn">🔍</button>
                    </div>
                    <div class="filter-buttons">
                    <button class="filter-btn active">All</button>
                    <button class="filter-btn">Pending</button>
                    <button class="filter-btn">Approved</button>
                    <button class="filter-btn">Rejected</button>
                    </div>
                </div>

                <div class="stats-section">
                    <h4>Provider Statistics</h4>
                    <div class="stats-grid">
                    <div class="stat-box">
                        <strong>Total Providers</strong><br>
                        <span>150</span>
                    </div>
                    <div class="stat-box">
                        <strong>Approved</strong><br>
                        <span>100</span>
                    </div>
                    <div class="stat-box">
                        <strong>Pending</strong><br>
                        <span>30</span>
                    </div>
                    <div class="stat-box">
                        <strong>Rejected</strong><br>
                        <span>20</span>
                    </div>
                    </div>
                </div>
                <br>

                <div class="providers-section">
                    <table class="provider-table">
                    <thead>
                        <tr>
                        <th>Provider Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <td>Provider A</td>
                        <td><span class="status pending">Pending Validation</span></td>
                        <td class="actions">
                            <button class="icon-btn">✔️</button>
                            <button class="icon-btn danger">❌</button>
                            <button class="icon-btn">✏️</button>
                        </td>
                        </tr>
                        <tr>
                        <td>Provider B</td>
                        <td><span class="status approved">Approved</span></td>
                        <td class="actions">
                            <button class="icon-btn danger>❌</button>
                            <button class="icon-btn">✏️</button>
                        </td>
                        </tr>
                        <tr>
                        <td>Provider C</td>
                        <td><span class="status rejected">Rejected</span></td>
                        <td class="actions">
                            <button class="icon-btn">✏️</button>
                        </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
                
                <div class="footer-buttons">
                    <button class="footer-btn">Export Providers</button>
                    <button class="footer-btn black">+ Add New Provider</button>
                </div>
            </div>
        </div>
    </body>
</html>