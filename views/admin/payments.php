<?php
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        header("Location: /CeylonGo/public/login");
        exit();
    }
 
    // ── Server-side filtering ──────────────────────────────────
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
                    <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-solid fa-star"></i> Reviews</a></li>
                    <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports & Analysis</a></li>
                </ul>
            </div>
 
            <div class="main-content">
                <div class="payments-management">
 
                    <h2 class="page-title">Payments Management</h2>
                    <br>
 
                    <!-- Search, Filter & Date toolbar -->
                    <form method="GET" action="/CeylonGo/public/admin/payments">
                        <div class="toolbar">
                            <div class="search-section">
                                <input type="text" name="pay_search"
                                    placeholder="Search by customer/ package"
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
                        <h4>Payment Statistics</h4><br>
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
 
                    <!-- Payments Table -->
                    <div class="payments-section">
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
 
                                            <?php if ($hasRefund && empty($p['refund_approved_at'])): ?>
                                                <!-- Refund requested, awaiting admin approval -->
                                                <button class="icon-btn pay-refund-approve-btn"
                                                        data-id="<?= (int)$p['id'] ?>"
                                                        data-reason="<?= htmlspecialchars($p['refund_reason'] ?? '') ?>"
                                                        title="Approve Refund">↩️✅</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
 
                    <div class="footer-buttons">
                        <button class="footer-btn black" id="exportPayBtn">Export Payments</button>
                    </div>
 
                </div><!-- /payments-management -->
            </div><!-- /main-content -->
 
        </div><!-- /page-wrapper -->
 
        <!-- Payment Details Modal — outside page-wrapper so it isn't caught in flex layout -->
        <div id="paymentModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="pay-modal-close">&times;</span>
                <u><h3>Payment Details</h3></u>
                <div id="paymentDetailsContent">Loading...</div>
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
 
            // ── Modal setup ───────────────────────────────────────
            const paymentModal   = document.getElementById('paymentModal');
            const paymentContent = document.getElementById('paymentDetailsContent');
            const payModalClose  = paymentModal.querySelector('.pay-modal-close');
 
            payModalClose.onclick = () => paymentModal.style.display = 'none';
            window.onclick = e => { if (e.target == paymentModal) paymentModal.style.display = 'none'; };
 
            // ── View payment details ──────────────────────────────
            document.querySelectorAll('.pay-view-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = parseInt(this.dataset.id);
                    const p  = paymentsData.find(x => x.id === id);
                    if (!p) return;
 
                    let method = '—';
                    if (p.payhere_payment_id)          method = 'Online (PayHere)';
                    else if (p.bank_transfer_submitted_at) method = 'Bank Transfer';
 
                    let html = '';
                    html += `<p><strong>Booking ID:</strong> #${p.id}</p>`;
                    html += `<p><strong>Customer:</strong> ${p.fullname}</p>`;
                    html += `<p><strong>Status:</strong> ${p.status.charAt(0).toUpperCase() + p.status.slice(1)}</p>`;
                    html += `<p><strong>Submitted:</strong> ${p.created_at}</p>`;
 
                    // Refund alert banner
                    if (p.refund_requested_at) {
                        html += `<div style="background:#fff3cd;border:1px solid #ffc107;border-radius:6px;padding:10px;margin:10px 0;">
                            <strong>⚠️ Refund Requested</strong><br>
                            <small>On: ${p.refund_requested_at}</small><br>
                            ${p.refund_reason ? `<em>"${p.refund_reason}"</em>` : ''}
                        </div>`;
                    }
 
                    html += `<h4>Payment Details</h4>`;
                    html += `<table>
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
 
            // ── Approve bank transfer slip ────────────────────────
            document.querySelectorAll('.pay-slip-approve-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id   = parseInt(this.dataset.id);
                    const slip = this.dataset.slip;
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
 
            // ── Approve refund request ────────────────────────────
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
 
            // ── Mark as paid (manual, no slip) ────────────────────
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
 
            // ── Export payments report ────────────────────────────
            document.getElementById('exportPayBtn').addEventListener('click', function() {
                if (!paymentsData || paymentsData.length === 0) {
                    alert('No payments to export!');
                    return;
                }
 
                const sep    = '='.repeat(70);
                const subSep = '-'.repeat(70);
                const now    = new Date();
                const dateStr = now.toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' });
                const timeStr = now.toLocaleTimeString('en-GB');
 
                let report = '';
                report += sep + '\n';
                report += '        CEYLON GO — PAYMENTS REPORT\n';
                report += sep + '\n';
                report += '  Generated on   : ' + dateStr + ' at ' + timeStr + '\n';
                report += '  Total Records  : ' + paymentsData.length + '\n';
                report += sep + '\n\n';
 
                paymentsData.forEach(function(p, index) {
                    let method = '—';
                    if (p.payhere_payment_id)          method = 'Online (PayHere)';
                    else if (p.bank_transfer_submitted_at) method = 'Bank Transfer';
 
                    report += 'PAYMENT ' + (index + 1) + ' OF ' + paymentsData.length + '\n';
                    report += sep + '\n';
 
                    report += '  CUSTOMER\n';
                    report += '  ' + subSep + '\n';
                    report += '  Name         : ' + p.fullname + '\n';
                    report += '  Email        : ' + p.email + '\n';
                    report += '  Phone        : ' + p.phone + '\n\n';
 
                    report += '  BOOKING\n';
                    report += '  ' + subSep + '\n';
                    report += '  Booking ID   : #' + p.id + '\n';
                    report += '  Package      : ' + p.package_name + '\n';
                    report += '  Travel Date  : ' + p.travel_date + '\n';
                    report += '  Travelers    : ' + p.travelers + ' (' + p.adults + ' Adult' + (p.adults != 1 ? 's' : '');
                    if (p.children > 0) report += ' / ' + p.children + ' Child' + (p.children != 1 ? 'ren' : '');
                    if (p.infants  > 0) report += ' / ' + p.infants + ' Infant' + (p.infants != 1 ? 's' : '');
                    report += ')\n\n';
 
                    report += '  PAYMENT\n';
                    report += '  ' + subSep + '\n';
                    report += '  Total Amount : LKR ' + Number(p.total_amount).toLocaleString() + '\n';
                    report += '  Status       : ' + p.status.charAt(0).toUpperCase() + p.status.slice(1) + '\n';
                    report += '  Method       : ' + method + '\n';
                    if (p.payhere_payment_id)         report += '  PayHere ID   : ' + p.payhere_payment_id + '\n';
                    if (p.paid_at)                    report += '  Paid At      : ' + p.paid_at + '\n';
                    if (p.bank_transfer_submitted_at) report += '  Bank Transfer Submitted : ' + p.bank_transfer_submitted_at + '\n';
                    if (p.bank_transfer_slip_path)    report += '  Bank Slip    : /uploads/' + p.bank_transfer_slip_path + '\n';
                    if (p.approved_at)                report += '  Approved At  : ' + p.approved_at + '\n';
                    report += '  Submitted On : ' + p.created_at + '\n';
 
                    if (p.refund_requested_at) {
                        report += '\n  REFUND REQUEST\n';
                        report += '  ' + subSep + '\n';
                        report += '  Requested At : ' + p.refund_requested_at + '\n';
                        if (p.refund_reason) report += '  Reason       : ' + p.refund_reason + '\n';
                    }
 
                    report += '\n' + sep + '\n\n';
                });
 
                report += sep + '\n';
                report += '  END OF REPORT\n';
                report += '  Ceylon Go Admin  |  ' + dateStr + '\n';
                report += sep + '\n';
 
                const blob = new Blob([report], { type: 'text/plain' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'ceylongo_payments_' + now.toISOString().slice(0, 10) + '.txt';
                link.click();
            });
        </script>
    </body>
</html>