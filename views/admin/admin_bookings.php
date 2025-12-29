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
                <li><a href="/CeylonGo/public/admin/dashboard">Home</a></li>
                <li><a href="/CeylonGo/public/admin/users">Users</a></li>
                <li><a href="/CeylonGo/public/admin/bookings" class="active">Bookings</a></li>
                <li><a href="/CeylonGo/public/admin/service">Service Providers</a></li>
                <li><a href="/CeylonGo/public/admin/payments">Payments</a></li>
                <li><a href="/CeylonGo/public/admin/reports">Reports</a></li>
                <li><a href="/CeylonGo/public/admin/reviews">Reviews</a></li>
                <li><a href="/CeylonGo/public/admin/inquiries">Inquiries</a></li>
                <li><a href="/CeylonGo/public/admin/settings">System Settings</a></li>
                <li><a href="/CeylonGo/public/admin/promotions">Promotions</a></li>
                <li><a href="/CeylonGo/public/logout">Logout</a></li>
            </ul>
        </aside>

        <div class="main-content">
            <div class="booking-management">
                
                <h2 class="page-title">Booking Management</h2>
                <br><br>

                <form method="GET" action="/CeylonGo/public/admin/bookings">
                    <div class="toolbar">
                        <div class="search-section">
                            <input type="text" name="search" placeholder="Search by booking ID" class="search-input" value="<?= htmlspecialchars($searchId ?? '') ?>">
                            <button type="submit" class="search-btn">🔍</button>
                        </div>
                        <div class="filter-buttons">
                            <button type="submit" name="status" value="pending" class="filter-btn <?= ($selectedStatus=='pending')?'active':'' ?>">Pending</button>
                            <button type="submit" name="status" value="completed" class="filter-btn <?= ($selectedStatus=='completed')?'active':'' ?>">Completed</button>
                            <button type="submit" name="status" value="cancelled" class="filter-btn <?= ($selectedStatus=='cancelled')?'active':'' ?>">Cancelled</button>
                            <button type="submit" name="status" value="all" class="filter-btn <?= ($selectedStatus=='all')?'active':'' ?>">All</button>
                        </div>
                        <div class="date-filter">
                            <input type="date" name="date" class="date-input" value="<?= htmlspecialchars($date ?? '') ?>" onchange="this.form.submit()">
                        </div>
                    </div>
                </form>

                <div class="stats-section">
                    <h4>Booking Statistics</h4><br>
                    <p class="subheading">Overview of current bookings</p>
                    <div class="stats-grid">
                        <div class="stat-box">
                            <strong>Total</strong><br>
                            <span><?= $stats['total'] ?? 0 ?></span>
                        </div>
                        <div class="stat-box">
                            <strong>Pending</strong><br>
                            <span><?= $stats['pending'] ?? 0 ?></span>
                        </div>
                        <div class="stat-box">
                            <strong>Completed</strong><br>
                            <span><?= $stats['completed'] ?? 0 ?></span>
                        </div>
                        <div class="stat-box">
                            <strong>Cancelled</strong><br>
                            <span><?= $stats['cancelled'] ?? 0 ?></span>
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
                            <?php foreach($bookings as $booking): ?>
                                <tr>
                                    <td><?= $booking['booking_id'] ?></td>
                                    <td><?= htmlspecialchars($booking['user_name']) ?></td>
                                    <td>
                                        <?php 
                                            $statusClass = '';
                                            switch(strtolower($booking['status'])) {
                                                case 'pending': $statusClass = 'active'; break;
                                                case 'completed': $statusClass = 'completed'; break;
                                                case 'cancelled': $statusClass = 'cancelled'; break;
                                            }
                                        ?>
                                        <span class="status <?= $statusClass ?>"><?= ucfirst($booking['status']) ?></span>
                                    </td>
                                    <td><?= date('Y-m-d', strtotime($booking['created_at'])) ?></td>
                                    <td class="actions">
                                        <button class="icon-btn">👁️</button>
                                        <button class="icon-btn danger">❌</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
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
