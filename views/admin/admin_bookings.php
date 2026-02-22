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

                <?php
                $bookings = $bookings ?? [];
                $stats = $stats ?? ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0, 'cancelled' => 0];
                ?>
                <div class="stats-section">
                    <h4>Booking Statistics</h4><br>
                    <p class="subheading">Overview of current bookings</p>
                    <div class="stats-grid">
                    <div class="stat-box">
                        <strong>Total</strong><br>
                        <span><?php echo $stats['total']; ?></span>
                    </div>
                    <div class="stat-box">
                        <strong>Pending</strong><br>
                        <span><?php echo $stats['pending']; ?></span>
                    </div>
                    <div class="stat-box">
                        <strong>Approved</strong><br>
                        <span><?php echo $stats['approved']; ?></span>
                    </div>
                    <div class="stat-box">
                        <strong>Rejected</strong><br>
                        <span><?php echo $stats['rejected']; ?></span>
                    </div>
                    </div>
                </div>
                <br>

                <?php if (isset($_GET['success'])): ?>
                <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 16px;">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
                <?php endif; ?>
                <?php if (isset($_GET['error'])): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 16px;">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
                <?php endif; ?>

                <div class="bookings-section">
                    <table class="booking-table">
                    <thead>
                        <tr>
                        <th>Booking ID</th>
                        <th>Package</th>
                        <th>Customer</th>
                        <th>Travel Date</th>
                        <th>Travelers</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px;">No bookings found</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($bookings as $booking): 
                            $status = $booking['status'] ?? 'pending';
                            $userName = trim(($booking['user_first_name'] ?? '') . ' ' . ($booking['user_last_name'] ?? ''));
                            if (empty($userName)) $userName = $booking['fullname'] ?? 'N/A';
                        ?>
                        <tr>
                        <td>#<?php echo htmlspecialchars($booking['id']); ?></td>
                        <td><?php echo htmlspecialchars($booking['package_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($userName); ?><br><small><?php echo htmlspecialchars($booking['email'] ?? ''); ?></small></td>
                        <td><?php echo htmlspecialchars($booking['travel_date'] ?? '-'); ?></td>
                        <td><?php echo (int)($booking['travelers'] ?? 0); ?> (<?php echo (int)($booking['adults'] ?? 0); ?>A, <?php echo (int)($booking['children'] ?? 0); ?>C, <?php echo (int)($booking['infants'] ?? 0); ?>I)</td>
                        <td>LKR <?php echo number_format((float)($booking['total_amount'] ?? 0), 2); ?></td>
                        <td>
                            <?php if ($status === 'pending'): ?>
                            <span class="status pending" style="background: #ffc107; color: #000;">Pending</span>
                            <?php elseif ($status === 'approved'): ?>
                            <span class="status approved" style="background: #28a745; color: #fff;">Approved</span>
                            <?php elseif ($status === 'rejected'): ?>
                            <span class="status rejected" style="background: #dc3545; color: #fff;">Rejected</span>
                            <?php else: ?>
                            <span class="status cancelled" style="background: #6c757d; color: #fff;"><?php echo ucfirst($status); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('Y-m-d', strtotime($booking['created_at'] ?? 'now')); ?></td>
                        <td class="actions">
                            <?php if ($status === 'pending'): ?>
                            <form method="POST" action="/CeylonGo/public/admin/approve-booking" style="display: inline;">
                                <input type="hidden" name="booking_id" value="<?php echo (int)$booking['id']; ?>">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="icon-btn" style="background: #28a745; color: #fff;" title="Approve">✓</button>
                            </form>
                            <form method="POST" action="/CeylonGo/public/admin/approve-booking" style="display: inline;">
                                <input type="hidden" name="booking_id" value="<?php echo (int)$booking['id']; ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="icon-btn danger" title="Reject">✕</button>
                            </form>
                            <?php endif; ?>
                            <button class="icon-btn" onclick="showBookingDetails(<?php echo htmlspecialchars(json_encode($booking)); ?>)" title="View Details">👁️</button>
                        </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    </table>
                </div>

            </div>
        </div>

        <!-- Booking Details Modal -->
        <div id="bookingModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
            <div style="background: white; padding: 24px; border-radius: 8px; max-width: 600px; max-height: 80vh; overflow-y: auto;">
                <h3>Booking Details</h3>
                <div id="bookingDetails"></div>
                <button onclick="document.getElementById('bookingModal').style.display='none'" style="margin-top: 16px; padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">Close</button>
            </div>
        </div>

        <script>
        function showBookingDetails(booking) {
            const modal = document.getElementById('bookingModal');
            const details = document.getElementById('bookingDetails');
            details.innerHTML = `
                <p><strong>Booking ID:</strong> #${booking.id}</p>
                <p><strong>Package:</strong> ${booking.package_name || 'N/A'}</p>
                <p><strong>Customer:</strong> ${booking.fullname || 'N/A'}</p>
                <p><strong>Email:</strong> ${booking.email || 'N/A'}</p>
                <p><strong>Phone:</strong> ${booking.phone || 'N/A'}</p>
                <p><strong>Travel Date:</strong> ${booking.travel_date || '-'}</p>
                <p><strong>Travelers:</strong> ${booking.travelers || 0} (${booking.adults || 0} Adults, ${booking.children || 0} Children, ${booking.infants || 0} Infants)</p>
                <p><strong>Total Amount:</strong> LKR ${parseFloat(booking.total_amount || 0).toLocaleString()}</p>
                <p><strong>Status:</strong> ${booking.status || 'pending'}</p>
                ${booking.special_requests ? '<p><strong>Special Requests:</strong> ' + booking.special_requests + '</p>' : ''}
                ${booking.admin_notes ? '<p><strong>Admin Notes:</strong> ' + booking.admin_notes + '</p>' : ''}
                <p><strong>Created:</strong> ${booking.created_at || '-'}</p>
            `;
            modal.style.display = 'flex';
        }
        </script>
    </body>
</html>
