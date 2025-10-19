<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiry Management</title>
    <link rel="stylesheet" href="../../public/css/admin/admin_inquiries.css">
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
          <li><a href="admin_payments.php">Payments</a></li>
          <li><a href="admin_reports.php">Reports</a></li>
          <li><a href="admin_reviews.php">Reviews</a></li>
          <li><a href="admin_inquiries.php" class="active">Inquiries</a></li>
          <li><a href="admin_settings.php">System Settings</a></li>
          <li><a href="admin_promotions.php">Promotions</a></li>
          <li><a href="../../controllers/admin/logout.php">Logout</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <div class="inquiry-management">

            <h2 class="page-title">Inquiry Management</h2>
            <br><br>

            <div class="toolbar">
                <div class="search-section">
                  <input type="text" placeholder="Search by user or subject" class="search-input">
                  <button class="search-btn">🔍</button>
                </div>
                <div class="filter-buttons">
                  <button class="filter-btn active">All</button>
                  <button class="filter-btn">Pending</button>
                  <button class="filter-btn">Resolved</button>
                </div>
            </div>

            <div class="stats-section">
                <h4>Inquiry Statistics</h4>
                <div class="stats-grid">
                  <div class="stat-box">
                      <strong>Total Inquiries</strong><br>
                      <span>350</span>
                  </div>
                  <div class="stat-box">
                      <strong>Pending</strong><br>
                      <span>120</span>
                  </div>
                  <div class="stat-box">
                      <strong>Resolved</strong><br>
                      <span>200</span>
                  </div>
                  <div class="stat-box">
                      <strong>Escalated</strong><br>
                      <span>30</span>
                  </div>
                </div>
            </div>
            <br>

            <div class="inquiries-section">
                <table class="inquiry-table">
                  <thead>
                      <tr>
                        <th>User</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                      </tr>
                  </thead>
                  <tbody>
                      <tr>
                        <td>John Doe</td>
                        <td>Did not receive refund</td>
                        <td><span class="status pending">Pending</span></td>
                        <td>2025-08-20</td>
                        <td class="actions">
                            <button class="icon-btn">👁️</button>
                            <button class="icon-btn">✏️</button>
                            <button class="icon-btn">✅</button>
                        </td>
                      </tr>
                      <tr>
                        <td>Jane Smith</td>
                        <td>Issue with delivery</td>
                        <td><span class="status resolved">Resolved</span></td>
                        <td>2025-08-19</td>
                        <td class="actions">
                            <button class="icon-btn">👁️</button>
                            <button class="icon-btn">✏️</button>
                        </td>
                      </tr>
                      <tr>
                        <td>Mark Lee</td>
                        <td>Complaint not addressed</td>
                        <td><span class="status resolved">Resolved</span></td>
                        <td>2025-08-18</td>
                        <td class="actions">
                            <button class="icon-btn">👁️</button>
                            <button class="icon-btn">✏️</button>
                        </td>
                      </tr>
                  </tbody>
                </table>
            </div>

            <div class="footer-buttons">
                <button class="footer-btn black">Export Inquiries</button>
            </div>
        </div>
    </div>
  </body>
</html>
