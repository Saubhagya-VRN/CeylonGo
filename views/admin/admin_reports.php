<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reports Page</title>
        <link rel="stylesheet" href="../../public/css/admin/admin_reports.css">
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
                <li><a href="/CeylonGo/public/admin/reports" class="active">Reports</a></li>
                <li><a href="/CeylonGo/public/admin/reviews">Reviews</a></li>
                <li><a href="/CeylonGo/public/admin/inquiries">Inquiries</a></li>
                <li><a href="/CeylonGo/public/admin/settings">System Settings</a></li>
                <li><a href="/CeylonGo/public/admin/promotions">Promotions</a></li>
                <li><a href="/CeylonGo/public/logout">Logout</a></li>
            </ul>
        </aside>

        <div class="main-content">
            <div class="reports-management">
            
                <h2 class="page-title">Reports Page</h2>
                <br><br>

                <div class="filter-buttons">
                    <button class="filter-btn active">Daily</button>
                    <button class="filter-btn">Monthly</button>
                    <button class="filter-btn">Custom</button>
                </div>
                <p class="sub-text">Choose the report type you want to generate.</p>

                <h3 class="section-title">Key Metrics</h3>
                <p class="sub-text">Overview of Performance</p>
                <div class="metrics-grid">
                    <div class="metric-card">
                        <h4>Total Bookings</h4>
                        <p class="metric-value">1500</p>
                        <span class="metric-change positive">+20%</span>
                    </div>
                    <div class="metric-card">
                        <h4>Total Revenue</h4>
                        <p class="metric-value">$30,000</p>
                        <span class="metric-change positive">+15%</span>
                    </div>
                    <div class="metric-card">
                        <h4>Cancellations</h4>
                        <p class="metric-value">50</p>
                        <span class="metric-change negative">-5%</span>
                    </div>
                </div>

                <div class="chart-section">
                    <h3 class="section-title">Bookings Over Time</h3>
                    <canvas id="bookingsChart"></canvas>
                </div>

                <div class="chart-section">
                    <h3 class="section-title">Revenue Trends</h3>
                    <canvas id="revenueChart"></canvas>
                </div>

                <div class="chart-section">
                    <h3 class="section-title">Cancellations Distribution</h3>
                    <canvas id="cancellationsChart"></canvas>
                </div>

                <div class="footer-buttons">
                    <button class="footer-btn">Download PDF</button>
                    <button class="footer-btn">Download Excel</button>
                    <button class="footer-btn black">Export Report</button>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="../../public/js/reports_charts.js"></script>
    </body>
</html>
