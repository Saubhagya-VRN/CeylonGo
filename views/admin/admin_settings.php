<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>System Settings</title>
        <link rel="stylesheet" href="../../public/css/admin/admin_settings.css">
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
                <li><a href="/CeylonGo/public/admin/reviews">Reviews</a></li>
                <li><a href="/CeylonGo/public/admin/inquiries">Inquiries</a></li>
                <li><a href="/CeylonGo/public/admin/settings" class="active">System Settings</a></li>
                <li><a href="/CeylonGo/public/admin/promotions">Promotions</a></li>
                <li><a href="/CeylonGo/public/logout">Logout</a></li>
            </ul>
        </aside>

        <div class="main-content">
            
            <h2 class="page-title">System Settings</h2>
            <br>

            <div class="form-section">
                <div class="form-group">
                    <label for="cancellation">Cancellation Policies</label>
                    <textarea id="cancellation" placeholder="Enter cancellation policies here..." rows="3"></textarea>
                    <small>Specify your cancellation terms clearly.</small>
                </div>

                <div class="form-group">
                    <label for="fees">Platform Fees</label>
                    <input type="text" id="fees" placeholder="Enter platform fee percentages...">
                    <small>Include any additional fees as necessary.</small>
                </div>

                <div class="form-group">
                    <label>Default Language/Currency</label>
                    <div class="filter-buttons">
                        <button class="filter-btn">English (USD)</button>
                        <button class="filter-btn">Spanish (EUR)</button>
                        <button class="filter-btn">French (GBP)</button>
                        <button class="filter-btn">German (CAD)</button>
                    </div>
                    <small>Choose your preferred language and currency.</small>
                </div>
            </div>

            <div class="footer-buttons">
                <button class="footer-btn">Cancel</button>
                <button class="footer-btn black">Save Changes</button>
            </div>
        </div>
    </body>
</html>
