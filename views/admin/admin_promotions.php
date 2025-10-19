<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotions & Discounts</title>
    <link rel="stylesheet" href="../../public/css/admin/admin_promotions.css">
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
          <li><a href="admin_inquiries.php">Inquiries</a></li>
          <li><a href="admin_settings.php">System Settings</a></li>
          <li><a href="admin_promotions.php" class="active">Promotions</a></li>
          <li><a href="../../controllers/admin/logout.php">Logout</a></li>
        </ul>
    </aside>

    <div class="main-content">
        <div class="user-management">

            <h2 class="page-title">Promotions & Discounts</h2>
            <br><br>

            <div class="users-section">
                <table class="user-table promo-table">
                  <thead>
                    <tr>
                      <th>Promotion</th>
                      <th>Details</th>
                      <th>Status</th>
                      <th>End Date</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>🔥 Summer Sale</td>
                      <td>20% Off All Products</td>
                      <td><span class="promo-status expired">Ended</span></td>
                      <td>08/31/2023</td>
                      <td class="actions">
                        <button class="icon-btn">✏️</button>
                        <button class="icon-btn danger">❌</button>
                      </td>
                    </tr>
                    <tr>
                      <td>🎉 Buy One Get One Free</td>
                      <td>On selected items</td>
                      <td><span class="promo-status expired">Ended</span></td>
                      <td>09/15/2023</td>
                      <td class="actions">
                        <button class="icon-btn">✏️</button>
                        <button class="icon-btn danger">❌</button>
                      </td>
                    </tr>
                    <tr>
                      <td>🚚 Free Shipping</td>
                      <td>On orders over $50</td>
                      <td><span class="promo-status ongoing">Ongoing</span></td>
                      <td>-</td>
                      <td class="actions">
                        <button class="icon-btn">✏️</button>
                        <button class="icon-btn danger">❌</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
            </div>

            <div class="form-section">
              <h3>Add New Promotion</h3><br>
              <div class="form-group">
                <label for="promo-title">Title</label>
                <input type="text" id="promo-title" placeholder="Enter promotion title">
              </div>
              <div class="form-group">
                <label for="discount">Discount %</label>
                <input type="number" id="discount" placeholder="Enter discount percentage">
              </div>
              <div class="form-group">
                <label for="start-date">Start Date</label>
                <input type="date" id="start-date">
              </div>
              <div class="form-group">
                <label for="end-date">End Date</label>
                <input type="date" id="end-date">
              </div>

              <label>Conditions</label>
              <div class="conditions">
                <button class="condition-btn">Minimum Purchase Amount</button>
                <button class="condition-btn">Selected Products Only</button>
                <button class="condition-btn">New Customers Only</button>
                <button class="condition-btn">All Customers</button>
              </div>
            </div>

            <div class="footer-buttons">
                <button class="footer-btn">Cancel</button>
                <button class="footer-btn black">Save Promotion</button>
            </div>
        </div>
    </div>
  </body>
</html>
