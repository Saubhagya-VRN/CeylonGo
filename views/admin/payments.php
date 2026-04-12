<?php
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        header("Location: /CeylonGo/public/login");
        exit();
    }

    // ── Package Payments: Server-side filtering ────────────────
    $paySearch         = $_GET['pay_search'] ?? '';
    $paySelectedStatus = $_GET['pay_status'] ?? 'all';
    $payDate           = $_GET['pay_date']   ?? '';

    $filteredPayments = array_filter($payments ?? [], function($p) use ($paySearch, $paySelectedStatus, $payDate) {
        if ($paySelectedStatus !== 'all' && strtolower($p['status']) !== $paySelectedStatus) return false;
        if ($payDate) {
            $checkDate = $p['paid_at'] ?? ($p['bank_transfer_submitted_at'] ?? $p['created_at']);
            if (!$checkDate || date('Y-m-d', strtotime($checkDate)) !== $payDate) return false;
        }
        if ($paySearch) {
            $q = strtolower($paySearch);
            $haystack = strtolower(
                $p['fullname'] . ' ' . $p['email'] . ' ' . $p['package_name']
            );
            if (strpos($haystack, $q) === false) return false;
        }
        return true;
    });

    // ── Trip Payments: Server-side filtering ───────────────────
    $tripPaySearch         = $_GET['trip_pay_search'] ?? '';
    $tripPaySelectedStatus = $_GET['trip_pay_status'] ?? 'all';
    $tripPayDate           = $_GET['trip_pay_date']   ?? '';

    $filteredTripPayments = array_filter($tripPayments ?? [], function($t) use ($tripPaySearch, $tripPaySelectedStatus, $tripPayDate) {
        if ($tripPaySelectedStatus !== 'all' && strtolower($t['status']) !== $tripPaySelectedStatus) return false;
        if ($tripPayDate) {
            $checkDate = $t['paid_at'] ?? ($t['bank_transfer_submitted_at'] ?? $t['created_at']);
            if (!$checkDate || date('Y-m-d', strtotime($checkDate)) !== $tripPayDate) return false;
        }
        if ($tripPaySearch) {
            $q = strtolower($tripPaySearch);
            $haystack = strtolower(
                $t['id'] . ' ' . $t['customer_name'] . ' ' . $t['destination']
            );
            if (strpos($haystack, $q) === false) return false;
        }
        return true;
    });
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/admin/payments.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/base.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/navbar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/sidebar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/footer.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">

        <title>Payments Management</title>
    </head>

    <body>
        <!-- Navbar -->
        <header class="navbar">
            <div class="branding">
                <img src="/CeylonGo/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
                <div class="logo-text">Ceylon Go</div>
            </div>
            <nav class="nav-links">
                <a href="/CeylonGo/public/admin/dashboard">Home</a>
                <div class="profile-dropdown">
                    <img src="/CeylonGo/public/images/profile.jpg" alt="User" class="profile-pic" onclick="toggleProfileDropdown()">
                    <div class="profile-dropdown-menu" id="profileDropdown">
                        <a href="/CeylonGo/public/admin/profile"><i class="fa-regular fa-user"></i> My Profile</a>
                        <a href="/CeylonGo/public/logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                    </div>
                </div>
            </nav>
        </header>

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <div class="page-wrapper">

            <!-- Sidebar -->
            <div class="sidebar">
                <ul>
                    <li><a href="/CeylonGo/public/admin/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
                    <li><a href="/CeylonGo/public/admin/users"><i class="fa-solid fa-users"></i> Users</a></li>
                    <li><a href="/CeylonGo/public/admin/bookings"><i class="fa-regular fa-calendar"></i> Bookings</a></li>
                    <li><a href="/CeylonGo/public/admin/service"><i class="fa-solid fa-van-shuttle"></i> Service Providers</a></li>
                    <li class="active"><a href="/CeylonGo/public/admin/payments"><i class="fa-solid fa-credit-card"></i> Payments</a></li>
                    <li><a href="/CeylonGo/public/admin/inquiries"><i class="fa-solid fa-circle-question"></i> Inquiries</a></li>
                    <li><a href="/CeylonGo/public/admin/packages"><i class="fa-solid fa-box-open"></i> Packages</a></li>
                    <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-regular fa-star"></i> Reviews</a></li>
                    <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports & Analysis</a></li>
                </ul>
            </div>

            <div class="main-content">
                <div class="payments-management">

                    <h2 class="page-title">Payments Management</h2>
                    <br>

                    <h4 class="page-title" style="font-size:16px;">Customized Booking Payments</h4>

                    <!-- Search, Filter & Date toolbar (trip payments) -->
                    <form method="GET" action="/CeylonGo/public/admin/payments">
                        <!-- Preserve package-payments filters across submits -->
                        <input type="hidden" name="pay_search" value="<?= htmlspecialchars($paySearch) ?>">
                        <input type="hidden" name="pay_status" value="<?= htmlspecialchars($paySelectedStatus) ?>">
                        <input type="hidden" name="pay_date"   value="<?= htmlspecialchars($payDate) ?>">
                        <div class="toolbar">
                            <div class="search-section">
                                <input type="text" name="trip_pay_search"
                                    placeholder="Search by Trip ID, customer or destination"
                                    class="search-input"
                                    value="<?= htmlspecialchars($tripPaySearch) ?>">
                                <button type="submit" class="search-btn">🔍</button>
                            </div>
                            <div class="filter-buttons">
                                <?php
                                    $tripPayStatuses = [
                                        'all'       => 'All',
                                        'completed' => 'Completed',
                                        'pending'   => 'Pending',
                                        'cancelled' => 'Cancelled',
                                    ];
                                    foreach ($tripPayStatuses as $val => $label):
                                        $active = $tripPaySelectedStatus === $val ? 'active' : '';
                                        echo "<button type='submit' name='trip_pay_status' value='{$val}' class='filter-btn {$active}'>{$label}</button>";
                                    endforeach;
                                ?>
                            </div>
                            <div class="date-filter">
                                <input type="date" name="trip_pay_date" class="date-input"
                                    value="<?= htmlspecialchars($tripPayDate) ?>"
                                    onchange="this.form.submit()">
                            </div>
                        </div>
                    </form>

                    <!-- Trip Payment Stats -->
                    <div class="stats-section">
                        <div class="stats-grid">
                            <div class="stat-box">
                                <strong>Total</strong><br>
                                <span><?= $tripPayStats['total'] ?? 0 ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Completed</strong><br>
                                <span><?= $tripPayStats['completed'] ?? 0 ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Pending</strong><br>
                                <span><?= $tripPayStats['pending'] ?? 0 ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Cancelled</strong><br>
                                <span><?= $tripPayStats['cancelled'] ?? 0 ?></span>
                            </div>
                            <?php if (($tripPayStats['refund_requested'] ?? 0) > 0): ?>
                                <div class="stat-box refund-card">
                                    <strong>Refund Requests</strong><br>
                                    <span><?= $tripPayStats['refund_requested'] ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Trip Payments Table -->
                    <div class="payments-section">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:15px; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
                            <!-- LEFT: Show entries -->
                            <div class="filter-buttons" style="align-items:center;">
                                <span style="font-size:14px;">Show</span>
                                <select id="tripPayRowsPerPage" class="filter-btn small-btn">
                                    <option value="10" selected>10</option>
                                    <option value="15">15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                                <span style="font-size:14px;">entries</span>
                            </div>
                            <!-- RIGHT: Pagination -->
                            <div id="tripPayPaginationControls" class="filter-buttons"></div>
                        </div>

                        <table class="payments-table">
                            <thead>
                                <tr>
                                    <th>Trip ID</th>
                                    <th>Customer</th>
                                    <th>Destination</th>
                                    <th>Amount (LKR)</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tripPaymentsTableBody">
                                <?php if (empty($filteredTripPayments)): ?>
                                    <tr><td colspan="8" style="text-align:center;">No trip payments found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($filteredTripPayments as $t):
                                        $tStatus = strtolower($t['status']);
                                        switch ($tStatus) {
                                            case 'completed': $tsc = 'completed'; break;
                                            case 'confirmed': $tsc = 'approved';  break;
                                            case 'pending':   $tsc = 'pending';   break;
                                            case 'cancelled': $tsc = 'canceled';  break;
                                            default:          $tsc = '';
                                        }
                                        if (!empty($t['payhere_payment_id'])) {
                                            $tMethod = 'Online';
                                        } elseif (!empty($t['bank_transfer_submitted_at'])) {
                                            $tMethod = 'Bank Transfer';
                                        } else {
                                            $tMethod = '—';
                                        }
                                        $tDisplayDate = $t['paid_at']
                                            ?? ($t['bank_transfer_submitted_at']
                                            ?? $t['created_at']);
                                        $tHasRefund = !empty($t['refund_requested_at']);
                                        $tHasSlip   = !empty($t['bank_transfer_slip_path']);
                                    ?>
                                    <tr>
                                        <td>#<?= (int)$t['id'] ?></td>
                                        <td><?= htmlspecialchars($t['customer_name']) ?></td>
                                        <td><?= htmlspecialchars($t['destination']) ?></td>
                                        <td>
                                            <?= $t['budget_lkr'] ? number_format((float)$t['budget_lkr'], 2) : '—' ?>
                                        </td>
                                        <td>
                                            <?= $tMethod ?>
                                            <?php if ($tHasSlip): ?>
                                                <br><small><a href="/CeylonGo/public/uploads/<?= htmlspecialchars($t['bank_transfer_slip_path']) ?>"
                                                    target="_blank" style="color:#007bff;">View Slip</a></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status <?= $tsc ?>"><?= ucfirst($t['status']) ?></span>
                                            <?php if ($tHasRefund): ?>
                                                <br><span class="refund-badge" title="Refund requested: <?= htmlspecialchars($t['refund_reason'] ?? '') ?>">⚠️ Refund</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $tDisplayDate ? date('Y-m-d', strtotime($tDisplayDate)) : '—' ?></td>
                                        <td class="actions">
                                            <!-- View details — always shown -->
                                            <button class="icon-btn trip-pay-view-btn"
                                                    data-id="<?= (int)$t['id'] ?>"
                                                    title="View Details">👁️</button>

                                            <?php if ($tStatus === 'pending' && !$tHasSlip): ?>
                                                <!-- Pending with no slip: admin manually marks paid -->
                                                <button class="icon-btn trip-pay-verify-btn"
                                                        data-id="<?= (int)$t['id'] ?>"
                                                        title="Mark as Paid">✅</button>

                                            <?php elseif ($tHasSlip && $tStatus === 'pending' && empty($t['paid_at'])): ?>
                                                <!-- Bank slip submitted, awaiting admin approval -->
                                                <button class="icon-btn trip-pay-slip-approve-btn"
                                                        data-id="<?= (int)$t['id'] ?>"
                                                        data-slip="<?= htmlspecialchars($t['bank_transfer_slip_path']) ?>"
                                                        title="Approve Bank Transfer">🏦✅</button>
                                            <?php endif; ?>

                                            <?php if ($tHasRefund && empty($t['refund_approved_at']) && empty($t['refund_rejected_at'])): ?>
                                                <!-- Refund requested, awaiting admin decision -->
                                                <button class="icon-btn trip-pay-refund-approve-btn"
                                                        data-id="<?= (int)$t['id'] ?>"
                                                        data-reason="<?= htmlspecialchars($t['refund_reason'] ?? '') ?>"
                                                        title="Approve Refund">↩️✅</button>
                                                <button class="icon-btn trip-pay-refund-reject-btn"
                                                        data-id="<?= (int)$t['id'] ?>"
                                                        data-reason="<?= htmlspecialchars($t['refund_reason'] ?? '') ?>"
                                                        title="Reject Refund">↩️❌</button>
                                            <?php elseif (!empty($t['refund_approved_at'])): ?>
                                                <span style="font-size:11px;color:green;font-weight:bold;">✅ Refund Approved</span>
                                            <?php elseif (!empty($t['refund_rejected_at'])): ?>
                                                <span style="font-size:11px;color:#c0392b;font-weight:bold;">❌ Refund Rejected</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="footer-buttons" style="margin-top: 24px;">
                        <a href="/CeylonGo/public/admin/reports?type=payments" class="report-link-btn">
                            <i class="fa-solid fa-file-arrow-down"></i>
                            Generate Payments Report
                        </a>
                    </div>

                    <br><br>
                    <h4 class="page-title" style="font-size:16px;">Package Booking Payments</h4>

                    <!-- Search, Filter & Date toolbar -->
                    <form method="GET" action="/CeylonGo/public/admin/payments">
                        <!-- Preserve trip-payments filters across submits -->
                        <input type="hidden" name="trip_pay_search" value="<?= htmlspecialchars($tripPaySearch) ?>">
                        <input type="hidden" name="trip_pay_status" value="<?= htmlspecialchars($tripPaySelectedStatus) ?>">
                        <input type="hidden" name="trip_pay_date"   value="<?= htmlspecialchars($tripPayDate) ?>">
                        <div class="toolbar">
                            <div class="search-section">
                                <input type="text" name="pay_search"
                                    placeholder="Search by customer or package"
                                    class="search-input"
                                    value="<?= htmlspecialchars($paySearch) ?>">
                                <button type="submit" class="search-btn">🔍</button>
                            </div>
                            <div class="filter-buttons">
                                <?php
                                    $payStatuses = [
                                        'all'       => 'All',
                                        'paid'      => 'Paid',
                                        'approved'  => 'Approved',
                                        'pending'   => 'Pending',
                                        'rejected'  => 'Rejected',
                                        'cancelled' => 'Cancelled',
                                    ];
                                    foreach ($payStatuses as $val => $label):
                                        $active = $paySelectedStatus === $val ? 'active' : '';
                                        echo "<button type='submit' name='pay_status' value='{$val}' class='filter-btn {$active}'>{$label}</button>";
                                    endforeach;
                                ?>
                            </div>
                            <div class="date-filter">
                                <input type="date" name="pay_date" class="date-input"
                                    value="<?= htmlspecialchars($payDate) ?>"
                                    onchange="this.form.submit()">
                            </div>
                        </div>
                    </form>

                    <!-- Stats -->
                    <div class="stats-section">
                        <div class="stats-grid">
                            <div class="stat-box">
                                <strong>Total Payments</strong><br>
                                <span><?= $payStats['total'] ?? 0 ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Paid</strong><br>
                                <span><?= $payStats['paid'] ?? 0 ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Approved</strong><br>
                                <span><?= $payStats['approved'] ?? 0 ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Pending</strong><br>
                                <span><?= $payStats['pending'] ?? 0 ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Rejected</strong><br>
                                <span><?= $payStats['rejected'] ?? 0 ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Cancelled</strong><br>
                                <span><?= $payStats['cancelled'] ?? 0 ?></span>
                            </div>
                            <?php if (($payStats['refund_requested'] ?? 0) > 0): ?>
                                <div class="stat-box refund-card">
                                    <strong>Refund Requests</strong><br>
                                    <span><?= $payStats['refund_requested'] ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Package Payments Table -->
                    <div class="payments-section">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; flex-wrap:wrap; gap:10px;">
                            <!-- LEFT: Show entries -->
                            <div class="filter-buttons" style="align-items:center;">
                                <span style="font-size:14px;">Show</span>
                                <select id="payRowsPerPage" class="filter-btn small-btn">
                                    <option value="10" selected>10</option>
                                    <option value="15">15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                                <span style="font-size:14px;">entries</span>
                            </div>
                            <!-- RIGHT: Pagination -->
                            <div id="payPaginationControls" class="filter-buttons"></div>
                        </div>

                        <table class="payments-table">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Package</th>
                                    <th>Amount (LKR)</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="paymentsTableBody">
                                <?php if (empty($filteredPayments)): ?>
                                    <tr><td colspan="7" style="text-align:center;">No payments found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($filteredPayments as $p):
                                        $status = strtolower($p['status']);
                                        switch ($status) {
                                            case 'paid':      $sc = 'completed'; break;
                                            case 'approved':  $sc = 'approved';  break;
                                            case 'pending':   $sc = 'pending';   break;
                                            case 'rejected':  $sc = 'canceled';  break;
                                            case 'cancelled': $sc = 'canceled';  break;
                                            default:          $sc = '';
                                        }
                                        if (!empty($p['payhere_payment_id'])) {
                                            $method = 'Online';
                                        } elseif (!empty($p['bank_transfer_submitted_at'])) {
                                            $method = 'Bank Transfer';
                                        } else {
                                            $method = '—';
                                        }
                                        $displayDate = $p['paid_at']
                                            ?? ($p['bank_transfer_submitted_at']
                                            ?? $p['created_at']);
                                        $hasRefund   = !empty($p['refund_requested_at']);
                                        $hasSlip     = !empty($p['bank_transfer_slip_path']);
                                    ?>
                                    <tr>
                                        <td>
                                            <?= htmlspecialchars($p['fullname']) ?><br>
                                            <small style="color:#888;"><?= htmlspecialchars($p['email']) ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($p['package_name']) ?></td>
                                        <td><?= number_format((float)$p['total_amount'], 2) ?></td>
                                        <td>
                                            <?= $method ?>
                                            <?php if ($hasSlip): ?>
                                                <br><small><a href="/CeylonGo/public/uploads/<?= htmlspecialchars($p['bank_transfer_slip_path']) ?>"
                                                    target="_blank" style="color:#007bff;">View Slip</a></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status <?= $sc ?>"><?= ucfirst($p['status']) ?></span>
                                            <?php if ($hasRefund): ?>
                                                <br><span class="refund-badge" title="Refund requested: <?= htmlspecialchars($p['refund_reason'] ?? '') ?>">⚠️ Refund</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $displayDate ? date('Y-m-d', strtotime($displayDate)) : '—' ?></td>
                                        <td class="actions">
                                            <!-- View details — always shown -->
                                            <button class="icon-btn pay-view-btn"
                                                    data-id="<?= (int)$p['id'] ?>"
                                                    title="View Details">👁️</button>

                                            <?php if ($status === 'pending' && !$hasSlip): ?>
                                                <!-- Pending with no slip: admin manually marks paid -->
                                                <button class="icon-btn pay-verify-btn"
                                                        data-id="<?= (int)$p['id'] ?>"
                                                        title="Mark as Paid">✅</button>

                                            <?php elseif ($hasSlip && in_array($status, ['pending','approved']) && empty($p['paid_at'])): ?>
                                                <!-- Bank slip submitted, awaiting admin approval -->
                                                <button class="icon-btn pay-slip-approve-btn"
                                                        data-id="<?= (int)$p['id'] ?>"
                                                        data-slip="<?= htmlspecialchars($p['bank_transfer_slip_path']) ?>"
                                                        title="Approve Bank Transfer">🏦✅</button>
                                            <?php endif; ?>

                                            <?php if ($hasRefund && empty($p['refund_approved_at']) && empty($p['refund_rejected_at'])): ?>
                                                <!-- Refund requested, awaiting admin decision -->
                                                <button class="icon-btn pay-refund-approve-btn"
                                                        data-id="<?= (int)$p['id'] ?>"
                                                        data-reason="<?= htmlspecialchars($p['refund_reason'] ?? '') ?>"
                                                        title="Approve Refund">↩️✅</button>
                                                <button class="icon-btn pay-refund-reject-btn"
                                                        data-id="<?= (int)$p['id'] ?>"
                                                        data-reason="<?= htmlspecialchars($p['refund_reason'] ?? '') ?>"
                                                        title="Reject Refund">↩️❌</button>
                                            <?php elseif (!empty($p['refund_approved_at'])): ?>
                                                <span style="font-size:11px;color:green;font-weight:bold;">✅ Refund Approved</span>
                                            <?php elseif (!empty($p['refund_rejected_at'])): ?>
                                                <span style="font-size:11px;color:#c0392b;font-weight:bold;">❌ Refund Rejected</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="footer-buttons" style="margin-top: 24px;">
                        <a href="/CeylonGo/public/admin/reports?type=payments" class="report-link-btn">
                            <i class="fa-solid fa-file-arrow-down"></i>
                            Generate Package Payments Report
                        </a>
                    </div>

                </div><!-- /payments-management -->
            </div><!-- /main-content -->

        </div><!-- /page-wrapper -->

        <!-- Package Payment Details Modal -->
        <div id="paymentModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="pay-modal-close">&times;</span>
                <u><h3>Payment Details</h3></u>
                <div id="paymentDetailsContent">Loading...</div>
            </div>
        </div>

        <!-- Trip Payment Details Modal -->
        <div id="tripPaymentModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="trip-pay-modal-close">&times;</span>
                <u><h3>Trip Payment Details</h3></u>
                <div id="tripPaymentDetailsContent">Loading...</div>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            <ul>
                <li><a href="/CeylonGo/public/admin/bookings">View All Bookings</a></li>
                <li><a href="/CeylonGo/public/admin/reports">Generate Reports</a></li>
                <li><a href="/CeylonGo/public/admin/payments">Payments</a></li>
            </ul>
        </footer>

        <script>
            // ── Navbar dropdown ───────────────────────────────────
            function toggleProfileDropdown() {
                const dropdown = document.getElementById('profileDropdown');
                dropdown.classList.toggle('show');
            }
            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('profileDropdown');
                const profilePic = document.querySelector('.profile-pic');
                if (dropdown && !dropdown.contains(event.target) && event.target !== profilePic) {
                    dropdown.classList.remove('show');
                }
            });

            // ══════════════════════════════════════════════════════
            //  PACKAGE PAYMENTS DATA & LOGIC
            // ══════════════════════════════════════════════════════

            // ── Embed payment data ────────────────────────────────
            const paymentsData = <?= json_encode(array_values(array_map(function($p) {
                return [
                    'id'                         => $p['id'],
                    'fullname'                   => $p['fullname'],
                    'email'                      => $p['email'],
                    'phone'                      => $p['phone'],
                    'package_name'               => $p['package_name'],
                    'travelers'                  => $p['travelers'],
                    'adults'                     => $p['adults'],
                    'children'                   => $p['children'],
                    'infants'                    => $p['infants'],
                    'travel_date'                => $p['travel_date'],
                    'total_amount'               => $p['total_amount'],
                    'status'                     => $p['status'],
                    'payhere_payment_id'         => $p['payhere_payment_id'] ?? '',
                    'paid_at'                    => $p['paid_at'] ?? '',
                    'bank_transfer_submitted_at' => $p['bank_transfer_submitted_at'] ?? '',
                    'bank_transfer_slip_path'    => $p['bank_transfer_slip_path'] ?? '',
                    'refund_requested_at'        => $p['refund_requested_at'] ?? '',
                    'refund_reason'              => $p['refund_reason'] ?? '',
                    'approved_at'                => $p['approved_at'] ?? '',
                    'created_at'                 => $p['created_at'],
                ];
            }, $filteredPayments)), JSON_UNESCAPED_UNICODE) ?>;

            // ── Embed trip payments data ──────────────────────────
            const tripPaymentsData = <?= json_encode(array_values(array_map(function($t) {
                return [
                    'id'                         => $t['id'],
                    'customer_name'              => $t['customer_name'],
                    'destination'                => $t['destination'],
                    'number_of_people'           => $t['number_of_people'],
                    'number_of_days'             => $t['number_of_days'],
                    'start_date'                 => $t['start_date'],
                    'budget_lkr'                 => $t['budget_lkr'] ?? '',
                    'status'                     => $t['status'],
                    'payhere_payment_id'         => $t['payhere_payment_id'] ?? '',
                    'paid_at'                    => $t['paid_at'] ?? '',
                    'bank_transfer_submitted_at' => $t['bank_transfer_submitted_at'] ?? '',
                    'bank_transfer_slip_path'    => $t['bank_transfer_slip_path'] ?? '',
                    'refund_requested_at'        => $t['refund_requested_at'] ?? '',
                    'refund_reason'              => $t['refund_reason'] ?? '',
                    'created_at'                 => $t['created_at'],
                ];
            }, $filteredTripPayments)), JSON_UNESCAPED_UNICODE) ?>;

            // ── Package payment modal ─────────────────────────────
            const paymentModal   = document.getElementById('paymentModal');
            const paymentContent = document.getElementById('paymentDetailsContent');
            const payModalClose  = paymentModal.querySelector('.pay-modal-close');

            payModalClose.onclick = () => paymentModal.style.display = 'none';

            document.querySelectorAll('.pay-view-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = parseInt(this.dataset.id);
                    const p  = paymentsData.find(x => x.id === id);
                    if (!p) return;

                    let method = '—';
                    if (p.payhere_payment_id)              method = 'Online (PayHere)';
                    else if (p.bank_transfer_submitted_at) method = 'Bank Transfer';

                    let html = '';
                    html += `<p><strong>Booking ID:</strong> #${p.id}</p>`;
                    html += `<p><strong>Customer:</strong> ${p.fullname}</p>`;
                    html += `<p><strong>Status:</strong> ${p.status.charAt(0).toUpperCase() + p.status.slice(1)}</p>`;
                    html += `<p><strong>Submitted:</strong> ${p.created_at}</p>`;

                    if (p.refund_requested_at) {
                        html += `<div style="background:#fff3cd;border:1px solid #ffc107;border-radius:6px;padding:10px;margin:10px 0;">
                            <strong>⚠️ Refund Requested</strong><br>
                            <small>On: ${p.refund_requested_at}</small><br>
                            ${p.refund_reason ? `<em>"${p.refund_reason}"</em>` : ''}
                        </div>`;
                    }

                    html += `<h4>Payment Details</h4>
                        <table>
                            <tr><th>Field</th><th>Details</th></tr>
                            <tr><td>Package</td><td>${p.package_name}</td></tr>
                            <tr><td>Email</td><td>${p.email}</td></tr>
                            <tr><td>Phone</td><td>${p.phone}</td></tr>
                            <tr><td>Travel Date</td><td>${p.travel_date}</td></tr>
                            <tr><td>Travelers</td><td>
                                ${p.travelers} total
                                (${p.adults} Adult${p.adults != 1 ? 's' : ''}
                                ${p.children > 0 ? ' / ' + p.children + ' Child' + (p.children != 1 ? 'ren' : '') : ''}
                                ${p.infants  > 0 ? ' / ' + p.infants  + ' Infant' + (p.infants != 1 ? 's' : '') : ''})
                            </td></tr>
                            <tr><td>Total Amount</td><td>LKR ${Number(p.total_amount).toLocaleString()}</td></tr>
                            <tr><td>Payment Method</td><td>${method}</td></tr>
                            ${p.payhere_payment_id ? `<tr><td>PayHere ID</td><td>${p.payhere_payment_id}</td></tr>` : ''}
                            ${p.paid_at ? `<tr><td>Paid At</td><td>${p.paid_at}</td></tr>` : ''}
                            ${p.bank_transfer_submitted_at ? `<tr><td>Bank Transfer Submitted</td><td>${p.bank_transfer_submitted_at}</td></tr>` : ''}
                            ${p.bank_transfer_slip_path ? `<tr><td>Bank Slip</td><td><a href="/CeylonGo/public/uploads/${p.bank_transfer_slip_path}" target="_blank" style="color:#007bff;">View Slip</a></td></tr>` : ''}
                            ${p.approved_at ? `<tr><td>Approved At</td><td>${p.approved_at}</td></tr>` : ''}
                            ${p.refund_requested_at ? `<tr><td style="color:#e65100;font-weight:bold;">Refund Requested</td><td>${p.refund_requested_at}</td></tr>` : ''}
                            ${p.refund_reason ? `<tr><td style="color:#e65100;">Refund Reason</td><td>${p.refund_reason}</td></tr>` : ''}
                        </table>`;

                    paymentContent.innerHTML = html;
                    paymentModal.style.display = 'block';
                });
            });

            // ── Trip payment modal ────────────────────────────────
            const tripPaymentModal   = document.getElementById('tripPaymentModal');
            const tripPaymentContent = document.getElementById('tripPaymentDetailsContent');
            const tripPayModalClose  = tripPaymentModal.querySelector('.trip-pay-modal-close');

            tripPayModalClose.onclick = () => tripPaymentModal.style.display = 'none';

            document.querySelectorAll('.trip-pay-view-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = parseInt(this.dataset.id);
                    const t  = tripPaymentsData.find(x => x.id === id);
                    if (!t) return;

                    let method = '—';
                    if (t.payhere_payment_id)              method = 'Online (PayHere)';
                    else if (t.bank_transfer_submitted_at) method = 'Bank Transfer';

                    let html = '';
                    html += `<p><strong>Trip ID:</strong> #${t.id}</p>`;
                    html += `<p><strong>Customer:</strong> ${t.customer_name}</p>`;
                    html += `<p><strong>Status:</strong> ${t.status.charAt(0).toUpperCase() + t.status.slice(1)}</p>`;
                    html += `<p><strong>Submitted:</strong> ${t.created_at}</p>`;

                    if (t.refund_requested_at) {
                        html += `<div style="background:#fff3cd;border:1px solid #ffc107;border-radius:6px;padding:10px;margin:10px 0;">
                            <strong>⚠️ Refund Requested</strong><br>
                            <small>On: ${t.refund_requested_at}</small><br>
                            ${t.refund_reason ? `<em>"${t.refund_reason}"</em>` : ''}
                        </div>`;
                    }

                    html += `<h4>Trip Payment Details</h4>
                        <table>
                            <tr><th>Field</th><th>Details</th></tr>
                            <tr><td>Destination</td><td>${t.destination}</td></tr>
                            <tr><td>Start Date</td><td>${t.start_date}</td></tr>
                            <tr><td>People</td><td>${t.number_of_people}</td></tr>
                            <tr><td>Days</td><td>${t.number_of_days}</td></tr>
                            <tr><td>Amount (Budget)</td><td>${t.budget_lkr ? 'LKR ' + Number(t.budget_lkr).toLocaleString() : '—'}</td></tr>
                            <tr><td>Payment Method</td><td>${method}</td></tr>
                            ${t.payhere_payment_id ? `<tr><td>PayHere ID</td><td>${t.payhere_payment_id}</td></tr>` : ''}
                            ${t.paid_at ? `<tr><td>Paid At</td><td>${t.paid_at}</td></tr>` : ''}
                            ${t.bank_transfer_submitted_at ? `<tr><td>Bank Transfer Submitted</td><td>${t.bank_transfer_submitted_at}</td></tr>` : ''}
                            ${t.bank_transfer_slip_path ? `<tr><td>Bank Slip</td><td><a href="/CeylonGo/public/uploads/${t.bank_transfer_slip_path}" target="_blank" style="color:#007bff;">View Slip</a></td></tr>` : ''}
                            ${t.refund_requested_at ? `<tr><td style="color:#e65100;font-weight:bold;">Refund Requested</td><td>${t.refund_requested_at}</td></tr>` : ''}
                            ${t.refund_reason ? `<tr><td style="color:#e65100;">Refund Reason</td><td>${t.refund_reason}</td></tr>` : ''}
                        </table>`;

                    tripPaymentContent.innerHTML = html;
                    tripPaymentModal.style.display = 'block';
                });
            });

            // ── Approve bank transfer slip (package) ──────────────
            document.querySelectorAll('.pay-slip-approve-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id      = parseInt(this.dataset.id);
                    const slip    = this.dataset.slip;
                    const slipUrl = '/CeylonGo/public/uploads/' + slip;

                    if (!confirm(
                        'Approve bank transfer payment for booking #' + id + '?\n\n' +
                        'Make sure you have reviewed the slip before approving.\n' +
                        'Slip: ' + slipUrl
                    )) return;

                    fetch('/CeylonGo/public/admin/payment/approve-slip', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `booking_id=${id}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) location.reload();
                        else alert('Failed: ' + (data.message || 'Unknown error'));
                    })
                    .catch(() => alert('Server error. Please try again.'));
                });
            });

            // ── Approve bank transfer slip (trip) ─────────────────
            document.querySelectorAll('.trip-pay-slip-approve-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id      = parseInt(this.dataset.id);
                    const slip    = this.dataset.slip;
                    const slipUrl = '/CeylonGo/public/uploads/' + slip;

                    if (!confirm(
                        'Approve bank transfer payment for trip #' + id + '?\n\n' +
                        'Make sure you have reviewed the slip before approving.\n' +
                        'Slip: ' + slipUrl
                    )) return;

                    fetch('/CeylonGo/public/admin/trip-payment/approve-slip', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `trip_id=${id}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) location.reload();
                        else alert('Failed: ' + (data.message || 'Unknown error'));
                    })
                    .catch(() => alert('Server error. Please try again.'));
                });
            });

            // ── Approve refund (package) ──────────────────────────
            document.querySelectorAll('.pay-refund-approve-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id     = parseInt(this.dataset.id);
                    const reason = this.dataset.reason;

                    if (!confirm(
                        'Approve refund for booking #' + id + '?\n\n' +
                        'Customer reason: "' + (reason || 'No reason given') + '"\n\n' +
                        'This will mark the booking as Cancelled.'
                    )) return;

                    fetch('/CeylonGo/public/admin/payment/approve-refund', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `booking_id=${id}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) location.reload();
                        else alert('Failed: ' + (data.message || 'Unknown error'));
                    })
                    .catch(() => alert('Server error. Please try again.'));
                });
            });

            // ── Reject refund (package) ───────────────────────────
            document.querySelectorAll('.pay-refund-reject-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id     = parseInt(this.dataset.id);
                    const reason = this.dataset.reason;
                    const note   = prompt(
                        'Reject refund for booking #' + id + '?\n\n' +
                        'Customer reason: "' + (reason || 'No reason given') + '"\n\n' +
                        'Enter a note to send to the customer (required):'
                    );
                    if (note === null) return; // cancelled
                    if (!note.trim()) { alert('A rejection note is required.'); return; }

                    fetch('/CeylonGo/public/admin/payment/reject-refund', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `booking_id=${id}&reject_note=${encodeURIComponent(note.trim())}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) location.reload();
                        else alert('Failed: ' + (data.message || 'Unknown error'));
                    })
                    .catch(() => alert('Server error. Please try again.'));
                });
            });

            // ── Approve refund (trip) ─────────────────────────────
            document.querySelectorAll('.trip-pay-refund-approve-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id     = parseInt(this.dataset.id);
                    const reason = this.dataset.reason;

                    if (!confirm(
                        'Approve refund for trip #' + id + '?\n\n' +
                        'Customer reason: "' + (reason || 'No reason given') + '"\n\n' +
                        'This will mark the trip as Cancelled.'
                    )) return;

                    fetch('/CeylonGo/public/admin/trip-payment/approve-refund', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `trip_id=${id}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) location.reload();
                        else alert('Failed: ' + (data.message || 'Unknown error'));
                    })
                    .catch(() => alert('Server error. Please try again.'));
                });
            });

            // ── Reject refund (trip) ──────────────────────────────
            document.querySelectorAll('.trip-pay-refund-reject-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id     = parseInt(this.dataset.id);
                    const reason = this.dataset.reason;
                    const note   = prompt(
                        'Reject refund for trip #' + id + '?\n\n' +
                        'Customer reason: "' + (reason || 'No reason given') + '"\n\n' +
                        'Enter a note to send to the customer (required):'
                    );
                    if (note === null) return; // cancelled
                    if (!note.trim()) { alert('A rejection note is required.'); return; }

                    fetch('/CeylonGo/public/admin/trip-payment/reject-refund', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `trip_id=${id}&reject_note=${encodeURIComponent(note.trim())}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) location.reload();
                        else alert('Failed: ' + (data.message || 'Unknown error'));
                    })
                    .catch(() => alert('Server error. Please try again.'));
                });
            });

            // ── Mark as paid — package ────────────────────────────
            document.querySelectorAll('.pay-verify-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = parseInt(this.dataset.id);
                    if (!confirm('Mark this payment as Paid? This confirms the bank transfer was received.')) return;

                    fetch('/CeylonGo/public/admin/payment/verify', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `booking_id=${id}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) location.reload();
                        else alert('Failed to update: ' + (data.message || 'Unknown error'));
                    })
                    .catch(() => alert('Server error. Please try again.'));
                });
            });

            // ── Mark as paid — trip ───────────────────────────────
            document.querySelectorAll('.trip-pay-verify-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = parseInt(this.dataset.id);
                    if (!confirm('Mark this trip payment as Paid? This confirms the bank transfer was received.')) return;

                    fetch('/CeylonGo/public/admin/trip-payment/verify', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `trip_id=${id}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) location.reload();
                        else alert('Failed to update: ' + (data.message || 'Unknown error'));
                    })
                    .catch(() => alert('Server error. Please try again.'));
                });
            });

            // ── Close modals on outside click ─────────────────────
            window.onclick = e => {
                if (e.target == paymentModal)     paymentModal.style.display     = 'none';
                if (e.target == tripPaymentModal) tripPaymentModal.style.display = 'none';
            };

            // ══════════════════════════════════════════════════════
            //  PAGINATION — PACKAGE PAYMENTS
            // ══════════════════════════════════════════════════════

            const allPayRows       = Array.from(document.querySelectorAll('#paymentsTableBody tr'))
                                         .filter(r => r.children.length > 1);
            const payRPSel         = document.getElementById('payRowsPerPage');
            const payPagCtrl       = document.getElementById('payPaginationControls');
            let   payPage          = 1;
            let   payRPP           = parseInt(payRPSel.value);

            function renderPayTable() {
                const tbody = document.getElementById('paymentsTableBody');
                tbody.innerHTML = '';
                if (allPayRows.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:20px;color:#888;">No payments found.</td></tr>';
                    renderPayPagination();
                    return;
                }
                const start = (payPage - 1) * payRPP;
                allPayRows.slice(start, start + payRPP).forEach(r => tbody.appendChild(r));
                renderPayPagination();
            }
            function renderPayPagination() {
                const total = Math.ceil(allPayRows.length / payRPP);
                payPagCtrl.innerHTML =
                    `<button class="filter-btn small-btn" ${payPage === 1     ? 'disabled' : ''} onclick="payPrev()">Prev</button>` +
                    `<span class="page-info">Page ${payPage} of ${total || 1}</span>` +
                    `<button class="filter-btn small-btn" ${payPage === total || total === 0 ? 'disabled' : ''} onclick="payNext()">Next</button>`;
            }
            function payNext() { const t = Math.ceil(allPayRows.length / payRPP); if (payPage < t) { payPage++; renderPayTable(); } }
            function payPrev() { if (payPage > 1) { payPage--; renderPayTable(); } }
            payRPSel.addEventListener('change', function() { payRPP = parseInt(this.value); payPage = 1; renderPayTable(); });
            renderPayTable();

            // ══════════════════════════════════════════════════════
            //  PAGINATION — TRIP PAYMENTS
            // ══════════════════════════════════════════════════════

            const allTripPayRows   = Array.from(document.querySelectorAll('#tripPaymentsTableBody tr'))
                                         .filter(r => r.children.length > 1);
            const tripPayRPSel     = document.getElementById('tripPayRowsPerPage');
            const tripPayPagCtrl   = document.getElementById('tripPayPaginationControls');
            let   tripPayPage      = 1;
            let   tripPayRPP       = parseInt(tripPayRPSel.value);

            function renderTripPayTable() {
                const tbody = document.getElementById('tripPaymentsTableBody');
                tbody.innerHTML = '';
                if (allTripPayRows.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:20px;color:#888;">No payments found.</td></tr>';
                    renderTripPayPagination();
                    return;
                }
                const start = (tripPayPage - 1) * tripPayRPP;
                allTripPayRows.slice(start, start + tripPayRPP).forEach(r => tbody.appendChild(r));
                renderTripPayPagination();
            }
            function renderTripPayPagination() {
                const total = Math.ceil(allTripPayRows.length / tripPayRPP);
                tripPayPagCtrl.innerHTML =
                    `<button class="filter-btn small-btn" ${tripPayPage === 1     ? 'disabled' : ''} onclick="tripPayPrev()">Prev</button>` +
                    `<span class="page-info">Page ${tripPayPage} of ${total || 1}</span>` +
                    `<button class="filter-btn small-btn" ${tripPayPage === total || total === 0 ? 'disabled' : ''} onclick="tripPayNext()">Next</button>`;
            }
            function tripPayNext() { const t = Math.ceil(allTripPayRows.length / tripPayRPP); if (tripPayPage < t) { tripPayPage++; renderTripPayTable(); } }
            function tripPayPrev() { if (tripPayPage > 1) { tripPayPage--; renderTripPayTable(); } }
            tripPayRPSel.addEventListener('change', function() { tripPayRPP = parseInt(this.value); tripPayPage = 1; renderTripPayTable(); });
            renderTripPayTable();
        </script>
    </body>
</html>