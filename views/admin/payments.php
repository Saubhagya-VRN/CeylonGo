<?php
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        header("Location: /CeylonGo/public/login");
        exit();
    }

    // ── Trip Payments: Server-side filtering (by Payment Status column) ──
    $tripPaySearch         = $_GET['trip_pay_search'] ?? '';
    $tripPaySelectedStatus = $_GET['trip_pay_status'] ?? 'all';
    if (!in_array($tripPaySelectedStatus, ['all', 'awaiting', 'received'], true)) {
        $tripPaySelectedStatus = 'all';
    }
    $tripPayDate           = $_GET['trip_pay_date']   ?? '';

    // Trip rows for the table: status + date only (search is applied in the browser as you type)
    $filteredTripPaymentsBase = array_filter($tripPayments ?? [], function($t) use ($tripPaySelectedStatus, $tripPayDate) {
        if ($tripPaySelectedStatus !== 'all'
            && (($t['payment_status_key'] ?? '') !== $tripPaySelectedStatus)) {
            return false;
        }
        if ($tripPayDate) {
            $checkDate = $t['paid_at'] ?? ($t['bank_transfer_submitted_at'] ?? $t['created_at']);
            if (!$checkDate || date('Y-m-d', strtotime($checkDate)) !== $tripPayDate) return false;
        }
        return true;
    });

    // ── Package Payments: Server-side filtering (by Payment Status column) ──
    $paySearch         = $_GET['pay_search'] ?? '';
    $paySelectedStatus = $_GET['pay_status'] ?? 'all';
    if (!in_array($paySelectedStatus, ['all', 'awaiting', 'received', 'rejected'], true)) {
        $paySelectedStatus = 'all';
    }
    $payDate           = $_GET['pay_date']   ?? '';

    $filteredPaymentsBase = array_filter($payments ?? [], function($p) use ($paySelectedStatus, $payDate) {
        if ($paySelectedStatus !== 'all'
            && (($p['payment_status_key'] ?? '') !== $paySelectedStatus)) {
            return false;
        }
        if ($payDate) {
            $checkDate = $p['paid_at'] ?? ($p['bank_transfer_submitted_at'] ?? $p['created_at']);
            if (!$checkDate || date('Y-m-d', strtotime($checkDate)) !== $payDate) return false;
        }
        return true;
    });

    // ── Helper: Payment column — Received (green), Refunded (red), Awaiting (yellow)
    function getPaymentDisplay(array $row): array {
        $status = strtolower($row['status'] ?? '');

        if (!empty($row['refund_approved_at'])) {
            return ['text' => ucwords('refunded'), 'color' => '#c0392b'];
        }
        if (!empty($row['paid_at'])) {
            return ['text' => ucwords('received'), 'color' => '#198754'];
        }
        if (!empty($row['bank_transfer_submitted_at'])) {
            return ['text' => ucwords('awaiting'), 'color' => '#b8860b'];
        }
        if (in_array($status, ['cancelled', 'rejected'])) {
            return ['text' => '—', 'color' => null];
        }
        return ['text' => ucwords('awaiting'), 'color' => '#b8860b'];
    }

    function renderPaymentCell(array $display): string {
        $text = htmlspecialchars($display['text']);
        if ($display['color'] === null) {
            return '<span style="font-weight:700">' . $text . '</span>';
        }
        $c = htmlspecialchars($display['color']);
        return '<span style="color:' . $c . ';font-weight:700">' . $text . '</span>';
    }

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

        <style>
            /* ── Reject-reason modal overlay ── */
            #rejectReasonModal {
                display: none;
                position: fixed; inset: 0;
                background: rgba(0,0,0,.5);
                z-index: 9999;
                justify-content: center;
                align-items: center;
            }
            #rejectReasonModal.open { display: flex; }
            #rejectReasonBox {
                background: #fff;
                border-radius: 10px;
                padding: 28px 32px;
                width: 420px;
                max-width: 95vw;
                box-shadow: 0 8px 32px rgba(0,0,0,.18);
            }
            #rejectReasonBox h3 {
                margin: 0 0 14px;
                font-size: 17px;
                color: #c0392b;
            }
            #rejectReasonBox textarea {
                width: 100%;
                min-height: 90px;
                border: 1px solid #ccc;
                border-radius: 6px;
                padding: 10px;
                font-size: 14px;
                resize: vertical;
                box-sizing: border-box;
            }
            #rejectReasonBox .modal-actions {
                display: flex;
                gap: 10px;
                margin-top: 16px;
                justify-content: flex-end;
            }
            #rejectReasonBox .btn-cancel-reject {
                padding: 8px 18px;
                border: 1px solid #ccc;
                border-radius: 6px;
                background: #f5f5f5;
                cursor: pointer;
                font-size: 14px;
            }
            #rejectReasonBox .btn-confirm-reject {
                padding: 8px 18px;
                border: none;
                border-radius: 6px;
                background: #c0392b;
                color: #fff;
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
            }
            #rejectReasonBox .btn-confirm-reject:hover { background: #a93226; }
        </style>
    </head>

    <body>
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

                    <h4 class="page-title" style="font-size:16px;">Customized Booking Payments</h4>
                    <form method="GET" action="/CeylonGo/public/admin/payments">
                        <input type="hidden" name="pay_status" value="<?= htmlspecialchars($paySelectedStatus) ?>">
                        <input type="hidden" name="pay_date"   value="<?= htmlspecialchars($payDate) ?>">
                        <div class="toolbar">
                            <div class="search-section">
                                <input type="text" id="tripPaySearchInput"
                                    placeholder="Search by trip ID, customer or destination"
                                    class="search-input" autocomplete="off"
                                    value="<?= htmlspecialchars($tripPaySearch) ?>">
                                <button type="button" class="search-btn" onclick="applyTripPaySearch()" title="Search">🔍</button>
                            </div>
                            <div class="filter-buttons">
                                <?php
                                    $tripPayStatuses = [
                                        'all'       => 'All',
                                        'awaiting'  => 'Awaiting',
                                        'received'  => 'Received',
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

                    <div class="stats-section">
                        <div class="stats-grid">
                            <div class="stat-box">
                                <strong>Total</strong><br>
                                <span><?= (int)($tripPayStats['total'] ?? 0) ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Awaiting</strong><br>
                                <span><?= (int)($tripPayStats['awaiting'] ?? 0) ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Received</strong><br>
                                <span><?= (int)($tripPayStats['received'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="payments-section">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:15px; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
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
                                    <th>Booking Status</th>
                                    <th>Payment Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tripPaymentsTableBody">
                                <?php if (empty($filteredTripPaymentsBase)): ?>
                                    <tr><td colspan="9" style="text-align:center;">No trip payments found.</td></tr>
                                <?php else: ?>
                                    <?php foreach (array_values($filteredTripPaymentsBase) as $t):
                                        $tid = (int)($t['id'] ?? 0);
                                        $tStatus = strtolower((string)($t['status'] ?? ''));
                                        $tMethod = '—';
                                        if (!empty($t['payhere_payment_id'])) {
                                            $tMethod = 'Online';
                                        } elseif (!empty($t['bank_transfer_submitted_at'])) {
                                            $tMethod = 'Bank Transfer';
                                        }
                                        $tHasSlip = !empty($t['bank_transfer_slip_path']);
                                        $tHasRefund = !empty($t['refund_requested_at']);
                                        $tPaid = !empty($t['paid_at']);
                                        $displayDate = $t['paid_at'] ?? ($t['bank_transfer_submitted_at'] ?? ($t['created_at'] ?? null));
                                        $dateStr = $displayDate ? date('Y-m-d', strtotime($displayDate)) : '—';
                                        $budget = (isset($t['budget_lkr']) && $t['budget_lkr'] !== '' && $t['budget_lkr'] !== null)
                                            ? number_format((float)$t['budget_lkr'], 2, '.', ',') : '—';
                                        $st = (string)($t['status'] ?? '');
                                        $statusDisp = $st !== '' ? ucfirst($st) : '';
                                        $slipPath = (string)($t['bank_transfer_slip_path'] ?? '');
                                        $slipEsc = htmlspecialchars($slipPath, ENT_QUOTES, 'UTF-8');
                                        $reasonEsc = htmlspecialchars((string)($t['refund_reason'] ?? ''), ENT_QUOTES, 'UTF-8');
                                    ?>
                                    <tr>
                                        <td><?= $tid ?></td>
                                        <td><?= htmlspecialchars($t['customer_name'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($t['destination'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($budget) ?></td>
                                        <td><?= htmlspecialchars($tMethod) ?><?php if ($tHasSlip): ?><br><small><a href="/CeylonGo/public/uploads/<?= $slipEsc ?>" target="_blank" style="color:#007bff;">View Slip</a></small><?php endif; ?></td>
                                        <td><?= htmlspecialchars($statusDisp) ?><?php if ($tHasRefund): ?><br><span title="Refund requested: <?= $reasonEsc ?>">⚠️ Refund</span><?php endif; ?></td>
                                        <td><?= renderPaymentCell(getPaymentDisplay($t)) ?></td>
                                        <td><?= htmlspecialchars($dateStr) ?></td>
                                        <td class="actions">
                                            <button type="button" class="icon-btn trip-pay-view-btn" data-id="<?= $tid ?>" title="View Details">👁️</button>
                                            <?php if ($tHasSlip && $tStatus === 'pending' && !$tPaid): ?>
                                                <button type="button" class="icon-btn trip-pay-slip-approve-btn" data-id="<?= $tid ?>" data-slip="<?= $slipEsc ?>" title="Approve bank slip (confirm transfer)" aria-label="Approve bank slip and confirm transfer">✅</button>
                                                <button type="button" class="icon-btn trip-pay-slip-reject-btn" data-id="<?= $tid ?>" title="Reject bank slip" aria-label="Reject bank slip">❌</button>
                                            <?php endif; ?>
                                            <?php if ($tHasRefund && empty($t['refund_approved_at']) && empty($t['refund_rejected_at'])): ?>
                                                <button type="button" class="icon-btn trip-pay-refund-approve-btn" data-id="<?= $tid ?>" data-reason="<?= $reasonEsc ?>" title="Approve refund request" aria-label="Approve refund request">💸✅</button>
                                                <button type="button" class="icon-btn trip-pay-refund-reject-btn" data-id="<?= $tid ?>" data-reason="<?= $reasonEsc ?>" title="Reject Refund" aria-label="Reject refund request">💸❌</button>
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
                            Generate Payments Report
                        </a>
                    </div>

                    <br><br>
                    <h4 class="page-title" style="font-size:16px;">Package Booking Payments</h4>

                    <form method="GET" action="/CeylonGo/public/admin/payments">
                        <input type="hidden" name="trip_pay_status" value="<?= htmlspecialchars($tripPaySelectedStatus) ?>">
                        <input type="hidden" name="trip_pay_date"   value="<?= htmlspecialchars($tripPayDate) ?>">
                        <div class="toolbar">
                            <div class="search-section">
                                <input type="text" id="paySearchInput"
                                    placeholder="Search by booking ID, customer or package"
                                    class="search-input" autocomplete="off"
                                    value="<?= htmlspecialchars($paySearch) ?>">
                                <button type="button" class="search-btn" onclick="applyPaySearch()" title="Search">🔍</button>
                            </div>
                            <div class="filter-buttons">
                                <?php
                                    $payStatuses = [
                                        'all'       => 'All',
                                        'awaiting'  => 'Awaiting',
                                        'received'  => 'Received',
                                        'rejected'  => 'Refunded',
                                    ];
                                    foreach ($payStatuses as $val => $label):
                                        $active = $paySelectedStatus === $val ? 'active' : '';
                                        echo "<button type='submit' name='pay_status' value='" . htmlspecialchars($val) . "' class='filter-btn {$active}'>" . htmlspecialchars($label) . "</button>";
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

                    <div class="stats-section">
                        <div class="stats-grid">
                            <div class="stat-box">
                                <strong>Total</strong><br>
                                <span><?= (int)($payStats['total'] ?? 0) ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Awaiting</strong><br>
                                <span><?= (int)($payStats['awaiting'] ?? 0) ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Received</strong><br>
                                <span><?= (int)($payStats['received'] ?? 0) ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Refunded</strong><br>
                                <span><?= (int)($payStats['rejected'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="payments-section">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; flex-wrap:wrap; gap:10px;">
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
                            <div id="payPaginationControls" class="filter-buttons"></div>
                        </div>

                        <table class="payments-table">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Customer</th>
                                    <th>Package</th>
                                    <th>Amount (LKR)</th>
                                    <th>Method</th>
                                    <th>Booking Status</th>
                                    <th>Payment Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="paymentsTableBody">
                                <?php if (empty($filteredPaymentsBase)): ?>
                                    <tr><td colspan="9" style="text-align:center;">No package payments found.</td></tr>
                                <?php else: ?>
                                    <?php foreach (array_values($filteredPaymentsBase) as $p):
                                        $bid = (int)($p['id'] ?? 0);
                                        $pStatus = strtolower((string)($p['status'] ?? ''));
                                        $method = '—';
                                        if (!empty($p['payhere_payment_id'])) {
                                            $method = 'Online';
                                        } elseif (!empty($p['bank_transfer_submitted_at'])) {
                                            $method = 'Bank Transfer';
                                        }
                                        $hasSlip = !empty($p['bank_transfer_slip_path']);
                                        $hasRefund = !empty($p['refund_requested_at']);
                                        $pPaid = !empty($p['paid_at']);
                                        $displayDate = $p['paid_at'] ?? ($p['bank_transfer_submitted_at'] ?? ($p['created_at'] ?? null));
                                        $dateStr = $displayDate ? date('Y-m-d', strtotime($displayDate)) : '—';
                                        $st = (string)($p['status'] ?? '');
                                        $statusDisp = $st !== '' ? ucfirst($st) : '';
                                        $amt = number_format((float)($p['total_amount'] ?? 0), 2, '.', ',');
                                        $slipPath = (string)($p['bank_transfer_slip_path'] ?? '');
                                        $slipEsc = htmlspecialchars($slipPath, ENT_QUOTES, 'UTF-8');
                                        $reasonEsc = htmlspecialchars((string)($p['refund_reason'] ?? ''), ENT_QUOTES, 'UTF-8');
                                    ?>
                                    <tr>
                                        <td><?= $bid ?></td>
                                        <td><?= htmlspecialchars($p['fullname'] ?? '') ?><br><small style="color:#888;"><?= htmlspecialchars($p['email'] ?? '') ?></small></td>
                                        <td><?= htmlspecialchars($p['package_name'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($amt) ?></td>
                                        <td><?= htmlspecialchars($method) ?><?php if ($hasSlip): ?><br><small><a href="/CeylonGo/public/uploads/<?= $slipEsc ?>" target="_blank" style="color:#007bff;">View Slip</a></small><?php endif; ?></td>
                                        <td><?= htmlspecialchars($statusDisp) ?><?php if ($hasRefund): ?><br><span title="Refund requested: <?= $reasonEsc ?>">⚠️ Refund</span><?php endif; ?></td>
                                        <td><?= renderPaymentCell(getPaymentDisplay($p)) ?></td>
                                        <td><?= htmlspecialchars($dateStr) ?></td>
                                        <td class="actions">
                                            <button type="button" class="icon-btn pay-view-btn" data-id="<?= $bid ?>" title="View Details">👁️</button>
                                            <?php if ($hasSlip && ($pStatus === 'pending' || $pStatus === 'approved') && !$pPaid): ?>
                                                <button type="button" class="icon-btn pay-slip-approve-btn" data-id="<?= $bid ?>" data-slip="<?= $slipEsc ?>" title="Approve bank slip (confirm transfer)" aria-label="Approve bank slip and confirm transfer">✅</button>
                                                <button type="button" class="icon-btn pay-slip-reject-btn" data-id="<?= $bid ?>" title="Reject bank slip" aria-label="Reject bank slip">❌</button>
                                            <?php endif; ?>
                                            <?php if ($hasRefund && empty($p['refund_approved_at']) && empty($p['refund_rejected_at'])): ?>
                                                <button type="button" class="icon-btn pay-refund-approve-btn" data-id="<?= $bid ?>" data-reason="<?= $reasonEsc ?>" title="Approve refund request" aria-label="Approve refund request">✅</button>
                                                <button type="button" class="icon-btn pay-refund-reject-btn" data-id="<?= $bid ?>" data-reason="<?= $reasonEsc ?>" title="Reject Refund" aria-label="Reject refund request">❌</button>
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
                            Generate Package Payments Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Package Payment view modal -->
        <div id="paymentModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="pay-modal-close">&times;</span>
                <u><h3>Payment Details</h3></u>
                <div id="paymentDetailsContent">Loading...</div>
            </div>
        </div>

        <!-- Trip Payment view modal -->
        <div id="tripPaymentModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="trip-pay-modal-close">&times;</span>
                <u><h3>Trip Payment Details</h3></u>
                <div id="tripPaymentDetailsContent">Loading...</div>
            </div>
        </div>

        <!-- Reject Reason Modal\ -->
        <div id="rejectReasonModal">
            <div id="rejectReasonBox">
                <h3>❌ Enter Rejection Reason</h3>
                <p style="font-size:13px;color:#555;margin:0 0 10px;">
                    This reason will be sent to the customer via email.
                </p>
                <textarea id="rejectReasonText" placeholder="Enter the reason (shown to the customer)..."></textarea>
                <div class="modal-actions">
                    <button class="btn-cancel-reject" id="cancelRejectBtn">Cancel</button>
                    <button class="btn-confirm-reject" id="confirmRejectBtn">Confirm Rejection</button>
                </div>
            </div>
        </div>

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

            //  DATA — injected from PHP
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
                    'payhere_payment_id'         => $p['payhere_payment_id'] ?? null,
                    'paid_at'                    => $p['paid_at'] ?? null,
                    'bank_transfer_submitted_at' => $p['bank_transfer_submitted_at'] ?? null,
                    'bank_transfer_slip_path'    => $p['bank_transfer_slip_path'] ?? null,
                    'refund_requested_at'        => $p['refund_requested_at'] ?? null,
                    'refund_approved_at'         => $p['refund_approved_at'] ?? null,
                    'refund_rejected_at'         => $p['refund_rejected_at'] ?? null,
                    'refund_reason'              => $p['refund_reason'] ?? null,
                    'admin_notes'                => $p['admin_notes'] ?? null,
                    'created_at'                 => $p['created_at'],
                ];
            }, $payments ?? []))) ?>;

            const tripPaymentsData = <?= json_encode(array_values(array_map(function($t) {
                return [
                    'id'                         => $t['id'],
                    'customer_name'              => $t['customer_name'],
                    'destination'                => $t['destination'],
                    'budget_lkr'                 => $t['budget_lkr'] ?? null,
                    'status'                     => $t['status'],
                    'payhere_payment_id'         => $t['payhere_payment_id'] ?? null,
                    'paid_at'                    => $t['paid_at'] ?? null,
                    'bank_transfer_submitted_at' => $t['bank_transfer_submitted_at'] ?? null,
                    'bank_transfer_slip_path'    => $t['bank_transfer_slip_path'] ?? null,
                    'refund_requested_at'        => $t['refund_requested_at'] ?? null,
                    'refund_approved_at'         => $t['refund_approved_at'] ?? null,
                    'refund_rejected_at'         => $t['refund_rejected_at'] ?? null,
                    'refund_reason'              => $t['refund_reason'] ?? null,
                    'created_at'                 => $t['created_at'],
                ];
            }, $tripPayments ?? []))) ?>;

            // ══════════════════════════════════════════════════════
            //  HELPERS
            // ══════════════════════════════════════════════════════
            function escHtml(str) {
                if (!str) return '';
                return String(str)
                    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
                    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            }
            function fmtDate(dt) {
                if (!dt) return '—';
                return dt.substring(0, 10);
            }

            /** Prev / Page x of x / Next — same pattern as admin/user.php */
            function renderPrevNextPaginationControls(containerId, data, rowsPerPage, currentPage, goToPage) {
                const container = document.getElementById(containerId);
                if (!container) return;
                if (!data.length) {
                    container.innerHTML = '';
                    return;
                }
                const totalPages = Math.max(1, Math.ceil(data.length / rowsPerPage));
                const page = Math.min(Math.max(1, currentPage), totalPages);

                container.innerHTML =
                    '<button type="button" class="filter-btn small-btn"' + (page === 1 ? ' disabled' : '') + '>Prev</button>' +
                    '<span class="page-info">Page ' + page + ' of ' + totalPages + '</span>' +
                    '<button type="button" class="filter-btn small-btn"' + (page === totalPages ? ' disabled' : '') + '>Next</button>';

                const btns = container.querySelectorAll('button.filter-btn.small-btn');
                if (btns[0] && page > 1) {
                    btns[0].addEventListener('click', function() { goToPage(page - 1); });
                }
                if (btns[1] && page < totalPages) {
                    btns[1].addEventListener('click', function() { goToPage(page + 1); });
                }
            }

            /** Table rows are rendered in PHP; JS only paginates and filters by text. */
            function collectDataRows(tbody) {
                return Array.from(tbody.querySelectorAll('tr')).filter(function(tr) {
                    return tr.cells.length === 9;
                });
            }

            function initTripPaymentsPagination() {
                const input = document.getElementById('tripPaySearchInput');
                const tbody = document.getElementById('tripPaymentsTableBody');
                const sel = document.getElementById('tripPayRowsPerPage');
                const pagEl = document.getElementById('tripPayPaginationControls');
                if (!tbody || !sel) return;

                const allRowNodes = collectDataRows(tbody);
                if (!allRowNodes.length) {
                    if (pagEl) pagEl.innerHTML = '';
                    return;
                }

                let currentPage = 1;
                let perPage = parseInt(sel.value, 10) || 10;

                function draw() {
                    const q = (input && input.value) ? input.value.toLowerCase().trim() : '';
                    const activeRows = !q
                        ? allRowNodes.slice()
                        : allRowNodes.filter(function(row) {
                            return row.innerText.toLowerCase().indexOf(q) !== -1;
                        });

                    if (!activeRows.length) {
                        tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;">No matching results.</td></tr>';
                        if (pagEl) pagEl.innerHTML = '';
                        return;
                    }

                    const totalPages = Math.max(1, Math.ceil(activeRows.length / perPage));
                    if (currentPage > totalPages) currentPage = totalPages;
                    if (currentPage < 1) currentPage = 1;

                    tbody.innerHTML = '';
                    const start = (currentPage - 1) * perPage;
                    activeRows.slice(start, start + perPage).forEach(function(row) {
                        tbody.appendChild(row);
                    });

                    renderPrevNextPaginationControls('tripPayPaginationControls', activeRows, perPage, currentPage, function(newPage) {
                        currentPage = newPage;
                        draw();
                    });
                }

                if (input) {
                    input.addEventListener('input', function() {
                        currentPage = 1;
                        draw();
                    });
                    input.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') e.preventDefault();
                    });
                }
                sel.addEventListener('change', function() {
                    perPage = parseInt(sel.value, 10) || 10;
                    currentPage = 1;
                    draw();
                });
                draw();
            }

            function initPackagePaymentsPagination() {
                const input = document.getElementById('paySearchInput');
                const tbody = document.getElementById('paymentsTableBody');
                const sel = document.getElementById('payRowsPerPage');
                const pagEl = document.getElementById('payPaginationControls');
                if (!tbody || !sel) return;

                const allRowNodes = collectDataRows(tbody);
                if (!allRowNodes.length) {
                    if (pagEl) pagEl.innerHTML = '';
                    return;
                }

                let currentPage = 1;
                let perPage = parseInt(sel.value, 10) || 10;

                function draw() {
                    const q = (input && input.value) ? input.value.toLowerCase().trim() : '';
                    const activeRows = !q
                        ? allRowNodes.slice()
                        : allRowNodes.filter(function(row) {
                            return row.innerText.toLowerCase().indexOf(q) !== -1;
                        });

                    if (!activeRows.length) {
                        tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;">No matching results.</td></tr>';
                        if (pagEl) pagEl.innerHTML = '';
                        return;
                    }

                    const totalPages = Math.max(1, Math.ceil(activeRows.length / perPage));
                    if (currentPage > totalPages) currentPage = totalPages;
                    if (currentPage < 1) currentPage = 1;

                    tbody.innerHTML = '';
                    const start = (currentPage - 1) * perPage;
                    activeRows.slice(start, start + perPage).forEach(function(row) {
                        tbody.appendChild(row);
                    });

                    renderPrevNextPaginationControls('payPaginationControls', activeRows, perPage, currentPage, function(newPage) {
                        currentPage = newPage;
                        draw();
                    });
                }

                if (input) {
                    input.addEventListener('input', function() {
                        currentPage = 1;
                        draw();
                    });
                    input.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') e.preventDefault();
                    });
                }
                sel.addEventListener('change', function() {
                    perPage = parseInt(sel.value, 10) || 10;
                    currentPage = 1;
                    draw();
                });
                draw();
            }

            document.addEventListener('DOMContentLoaded', function() {
                initTripPaymentsPagination();
                initPackagePaymentsPagination();
            });

            // ══════════════════════════════════════════════════════
            //  PACKAGE PAYMENT — view modal content builder
            // ══════════════════════════════════════════════════════
            function renderPaymentSection(p) {
                const hasOnline  = !!p.payhere_payment_id;
                const hasSlip    = !!p.bank_transfer_slip_path;
                const hasPaid    = !!p.paid_at;
                const hasBank    = !!p.bank_transfer_submitted_at;
                const hasRefund  = !!p.refund_requested_at;

                let method = '—';
                if (hasOnline)    method = 'Online (PayHere)';
                else if (hasSlip) method = 'Bank Transfer';

                let payLabel = 'Awaiting Payment';
                let payColor = '#b8860b';
                if (p.refund_approved_at)    { payLabel = 'Refunded';  payColor = '#c0392b'; }
                else if (hasPaid)            { payLabel = 'Received';  payColor = '#198754'; }
                else if (hasBank)            { payLabel = 'Awaiting Verification'; payColor = '#b8860b'; }

                let html = `
                    <table style="width:100%;border-collapse:collapse;font-size:14px;">
                        <tr><td style="padding:6px 10px;font-weight:600;width:45%;">Booking ID</td>
                            <td style="padding:6px 10px;">${escHtml(String(p.id))}</td></tr>
                        <tr style="background:#f9f9f9;"><td style="padding:6px 10px;font-weight:600;">Customer</td>
                            <td style="padding:6px 10px;">${escHtml(p.fullname)}</td></tr>
                        <tr><td style="padding:6px 10px;font-weight:600;">Email</td>
                            <td style="padding:6px 10px;">${escHtml(p.email)}</td></tr>
                        <tr style="background:#f9f9f9;"><td style="padding:6px 10px;font-weight:600;">Phone</td>
                            <td style="padding:6px 10px;">${escHtml(p.phone)}</td></tr>
                        <tr><td style="padding:6px 10px;font-weight:600;">Package</td>
                            <td style="padding:6px 10px;">${escHtml(p.package_name)}</td></tr>
                        <tr style="background:#f9f9f9;"><td style="padding:6px 10px;font-weight:600;">Travel Date</td>
                            <td style="padding:6px 10px;">${escHtml(p.travel_date)}</td></tr>
                        <tr><td style="padding:6px 10px;font-weight:600;">Travelers</td>
                            <td style="padding:6px 10px;">${escHtml(String(p.travelers))} 
                                (Adults: ${escHtml(String(p.adults))}, Children: ${escHtml(String(p.children))}, Infants: ${escHtml(String(p.infants))})</td></tr>
                        <tr style="background:#f9f9f9;"><td style="padding:6px 10px;font-weight:600;">Amount (LKR)</td>
                            <td style="padding:6px 10px;">${parseFloat(p.total_amount).toLocaleString('en-US',{minimumFractionDigits:2})}</td></tr>
                        <tr><td style="padding:6px 10px;font-weight:600;">Booking Status</td>
                            <td style="padding:6px 10px;">${escHtml(p.status.charAt(0).toUpperCase()+p.status.slice(1))}</td></tr>
                        <tr style="background:#f9f9f9;"><td style="padding:6px 10px;font-weight:600;">Booked On</td>
                            <td style="padding:6px 10px;">${fmtDate(p.created_at)}</td></tr>
                    </table>
                    <hr style="margin:14px 0;">
                    <h4 style="margin:0 0 8px;font-size:14px;">Payment Information</h4>
                    <table style="width:100%;border-collapse:collapse;font-size:14px;">
                        <tr><td style="padding:6px 10px;font-weight:600;width:45%;">Payment Method</td>
                            <td style="padding:6px 10px;">${escHtml(method)}</td></tr>
                        <tr style="background:#f9f9f9;"><td style="padding:6px 10px;font-weight:600;">Payment Status</td>
                            <td style="padding:6px 10px;font-weight:700;color:${payColor};">${escHtml(payLabel)}</td></tr>`;

                if (!hasOnline && !hasSlip && !hasPaid) {
                    html += `<tr><td colspan="2" style="padding:10px;color:#b8860b;font-style:italic;">No payment has been made yet for this booking.</td></tr>`;
                }
                if (hasOnline) {
                    html += `<tr><td style="padding:6px 10px;font-weight:600;">PayHere ID</td>
                                <td style="padding:6px 10px;">${escHtml(p.payhere_payment_id)}</td></tr>`;
                }
                if (hasPaid) {
                    html += `<tr style="background:#f9f9f9;"><td style="padding:6px 10px;font-weight:600;">Paid At</td>
                                <td style="padding:6px 10px;">${escHtml(p.paid_at)}</td></tr>`;
                }
                if (hasBank) {
                    html += `<tr><td style="padding:6px 10px;font-weight:600;">Bank Slip Submitted</td>
                                <td style="padding:6px 10px;">${escHtml(p.bank_transfer_submitted_at)}</td></tr>`;
                }
                if (hasSlip) {
                    html += `<tr style="background:#f9f9f9;"><td style="padding:6px 10px;font-weight:600;">Bank Slip</td>
                                <td style="padding:6px 10px;">
                                    <a href="/CeylonGo/public/uploads/${escHtml(p.bank_transfer_slip_path)}" target="_blank" style="color:#007bff;">View Slip</a>
                                </td></tr>`;
                }
                if (hasRefund) {
                    html += `<tr><td style="padding:6px 10px;font-weight:600;color:#c0392b;">Refund Requested</td>
                                <td style="padding:6px 10px;">${escHtml(p.refund_requested_at)}</td></tr>`;
                    if (p.refund_reason) {
                        html += `<tr style="background:#f9f9f9;"><td style="padding:6px 10px;font-weight:600;">Refund Reason</td>
                                    <td style="padding:6px 10px;">${escHtml(p.refund_reason)}</td></tr>`;
                    }
                    if (p.refund_approved_at) {
                        html += `<tr><td style="padding:6px 10px;font-weight:600;color:green;">Refund Approved</td>
                                    <td style="padding:6px 10px;">${escHtml(p.refund_approved_at)}</td></tr>`;
                    } else if (p.refund_rejected_at) {
                        html += `<tr><td style="padding:6px 10px;font-weight:600;color:#c0392b;">Refund Rejected</td>
                                    <td style="padding:6px 10px;">${escHtml(p.refund_rejected_at)}</td></tr>`;
                    }
                }
                if (p.admin_notes) {
                    html += `<tr style="background:#f9f9f9;"><td style="padding:6px 10px;font-weight:600;">Admin Notes</td>
                                <td style="padding:6px 10px;">${escHtml(p.admin_notes)}</td></tr>`;
                }
                html += `</table>`;
                return html;
            }

            // ══════════════════════════════════════════════════════
            //  TRIP PAYMENT — view modal content builder
            // ══════════════════════════════════════════════════════
            function renderTripPaymentSection(t) {
                const hasOnline = !!t.payhere_payment_id;
                const hasSlip   = !!t.bank_transfer_slip_path;
                const hasPaid   = !!t.paid_at;
                const hasBank   = !!t.bank_transfer_submitted_at;
                const hasRefund = !!t.refund_requested_at;

                let method = '—';
                if (hasOnline)    method = 'Online (PayHere)';
                else if (hasSlip) method = 'Bank Transfer';

                let payLabel = 'Awaiting Payment';
                let payColor = '#b8860b';
                if (t.refund_approved_at)    { payLabel = 'Refunded';  payColor = '#c0392b'; }
                else if (hasPaid)            { payLabel = 'Received';  payColor = '#198754'; }
                else if (hasBank)            { payLabel = 'Awaiting Verification'; payColor = '#b8860b'; }

                let html = `
                    <table style="width:100%;border-collapse:collapse;font-size:14px;">
                        <tr><td style="padding:6px 10px;font-weight:600;width:45%;">Trip ID</td>
                            <td style="padding:6px 10px;">${escHtml(String(t.id))}</td></tr>
                        <tr style="background:#f9f9f9;"><td style="padding:6px 10px;font-weight:600;">Customer</td>
                            <td style="padding:6px 10px;">${escHtml(t.customer_name)}</td></tr>
                        <tr><td style="padding:6px 10px;font-weight:600;">Destination</td>
                            <td style="padding:6px 10px;">${escHtml(t.destination)}</td></tr>
                        <tr style="background:#f9f9f9;"><td style="padding:6px 10px;font-weight:600;">Amount (LKR)</td>
                            <td style="padding:6px 10px;">${t.budget_lkr ? parseFloat(t.budget_lkr).toLocaleString('en-US',{minimumFractionDigits:2}) : '—'}</td></tr>
                        <tr><td style="padding:6px 10px;font-weight:600;">Booking Status</td>
                            <td style="padding:6px 10px;">${escHtml(t.status.charAt(0).toUpperCase()+t.status.slice(1))}</td></tr>
                        <tr style="background:#f9f9f9;"><td style="padding:6px 10px;font-weight:600;">Booked On</td>
                            <td style="padding:6px 10px;">${fmtDate(t.created_at)}</td></tr>
                    </table>
                    <hr style="margin:14px 0;">
                    <h4 style="margin:0 0 8px;font-size:14px;">Payment Information</h4>
                    <table style="width:100%;border-collapse:collapse;font-size:14px;">
                        <tr><td style="padding:6px 10px;font-weight:600;width:45%;">Payment Method</td>
                            <td style="padding:6px 10px;">${escHtml(method)}</td></tr>
                        <tr style="background:#f9f9f9;"><td style="padding:6px 10px;font-weight:600;">Payment Status</td>
                            <td style="padding:6px 10px;font-weight:700;color:${payColor};">${escHtml(payLabel)}</td></tr>`;

                if (!hasOnline && !hasSlip && !hasPaid) {
                    html += `<tr><td colspan="2" style="padding:10px;color:#b8860b;font-style:italic;">No payment has been made yet for this booking.</td></tr>`;
                }
                if (hasOnline) {
                    html += `<tr><td style="padding:6px 10px;font-weight:600;">PayHere ID</td>
                                <td style="padding:6px 10px;">${escHtml(t.payhere_payment_id)}</td></tr>`;
                }
                if (hasPaid) {
                    html += `<tr style="background:#f9f9f9;"><td style="padding:6px 10px;font-weight:600;">Paid At</td>
                                <td style="padding:6px 10px;">${escHtml(t.paid_at)}</td></tr>`;
                }
                if (hasBank) {
                    html += `<tr><td style="padding:6px 10px;font-weight:600;">Bank Slip Submitted</td>
                                <td style="padding:6px 10px;">${escHtml(t.bank_transfer_submitted_at)}</td></tr>`;
                }
                if (hasSlip) {
                    html += `<tr style="background:#f9f9f9;"><td style="padding:6px 10px;font-weight:600;">Bank Slip</td>
                                <td style="padding:6px 10px;">
                                    <a href="/CeylonGo/public/uploads/${escHtml(t.bank_transfer_slip_path)}" target="_blank" style="color:#007bff;">View Slip</a>
                                </td></tr>`;
                }
                if (hasRefund) {
                    html += `<tr><td style="padding:6px 10px;font-weight:600;color:#c0392b;">Refund Requested</td>
                                <td style="padding:6px 10px;">${escHtml(t.refund_requested_at)}</td></tr>`;
                    if (t.refund_reason) {
                        html += `<tr style="background:#f9f9f9;"><td style="padding:6px 10px;font-weight:600;">Refund Reason</td>
                                    <td style="padding:6px 10px;">${escHtml(t.refund_reason)}</td></tr>`;
                    }
                    if (t.refund_approved_at) {
                        html += `<tr><td style="padding:6px 10px;font-weight:600;color:green;">Refund Approved</td>
                                    <td style="padding:6px 10px;">${escHtml(t.refund_approved_at)}</td></tr>`;
                    } else if (t.refund_rejected_at) {
                        html += `<tr><td style="padding:6px 10px;font-weight:600;color:#c0392b;">Refund Rejected</td>
                                    <td style="padding:6px 10px;">${escHtml(t.refund_rejected_at)}</td></tr>`;
                    }
                }
                html += `</table>`;
                return html;
            }

            // ══════════════════════════════════════════════════════
            //  REJECT REASON MODAL — shared state
            // ══════════════════════════════════════════════════════
            let _rejectCallback = null; // set before opening modal

            function openRejectModal(onConfirm) {
                _rejectCallback = onConfirm;
                document.getElementById('rejectReasonText').value = '';
                document.getElementById('rejectReasonModal').classList.add('open');
            }
            function closeRejectModal() {
                document.getElementById('rejectReasonModal').classList.remove('open');
                _rejectCallback = null;
            }

            document.getElementById('cancelRejectBtn').addEventListener('click', closeRejectModal);

            document.getElementById('confirmRejectBtn').addEventListener('click', function() {
                const note = document.getElementById('rejectReasonText').value.trim();
                if (!note) {
                    alert('Please enter a rejection reason before confirming.');
                    return;
                }
                if (_rejectCallback) _rejectCallback(note);
                closeRejectModal();
            });

            // Close reject modal on outside click
            document.getElementById('rejectReasonModal').addEventListener('click', function(e) {
                if (e.target === this) closeRejectModal();
            });

            // ══════════════════════════════════════════════════════
            //  AJAX helper
            // ══════════════════════════════════════════════════════
            function postAction(url, bodyParams, successMsg, failMsg) {
                const fd = new FormData();
                for (const [k, v] of Object.entries(bodyParams)) fd.append(k, v);
                fetch(url, { method: 'POST', body: fd })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            alert(successMsg || data.message || 'Done.');
                            location.reload();
                        } else {
                            alert(failMsg || data.message || 'Action failed.');
                        }
                    })
                    .catch(() => alert('Network error. Please try again.'));
            }

            // ══════════════════════════════════════════════════════
            //  DELEGATED CLICK HANDLERS — single document listener
            // ══════════════════════════════════════════════════════
            document.addEventListener('click', function(e) {

                // ── Package: view details ─────────────────────────
                const viewBtn = e.target.closest('.pay-view-btn');
                if (viewBtn) {
                    const id = parseInt(viewBtn.dataset.id, 10);
                    const record = paymentsData.find(p => p.id === id);
                    if (!record) return;
                    document.getElementById('paymentDetailsContent').innerHTML = renderPaymentSection(record);
                    document.getElementById('paymentModal').style.display = 'flex';
                    return;
                }

                // ── Package: approve bank slip ────────────────────
                const paySlipApprove = e.target.closest('.pay-slip-approve-btn');
                if (paySlipApprove) {
                    const id = parseInt(paySlipApprove.dataset.id, 10);
                    if (!confirm(`Approve bank slip payment for Booking #${id}?`)) return;
                    postAction(
                        '/CeylonGo/public/admin/payment/approve-slip',
                        { booking_id: id },
                        'Bank slip approved. Booking marked as Paid.',
                        'Could not approve slip — check that the slip exists and status is pending/approved.'
                    );
                    return;
                }

                // ── Package: reject bank slip ─────────────────────
                const paySlipReject = e.target.closest('.pay-slip-reject-btn');
                if (paySlipReject) {
                    const id = parseInt(paySlipReject.dataset.id, 10);
                    openRejectModal(function(note) {
                        postAction(
                            '/CeylonGo/public/admin/payment/reject-slip',
                            { booking_id: id, reject_note: note },
                            'Bank slip rejected. Customer has been notified; they can upload a new slip.',
                            'Could not reject slip — check that a slip exists and payment is not yet confirmed.'
                        );
                    });
                    return;
                }

                // ── Package: approve refund ───────────────────────
                const payRefundApprove = e.target.closest('.pay-refund-approve-btn');
                if (payRefundApprove) {
                    const id = parseInt(payRefundApprove.dataset.id, 10);
                    if (!confirm(`Approve refund for Package Booking #${id}?\n\nAn email will be sent to the customer asking for their bank details.`)) return;
                    postAction(
                        '/CeylonGo/public/admin/payment/approve-refund',
                        { booking_id: id },
                        'Refund approved. Email sent to customer requesting bank details.',
                        'Could not approve refund — check that a refund was requested.'
                    );
                    return;
                }

                // ── Package: reject refund ────────────────────────
                const payRefundReject = e.target.closest('.pay-refund-reject-btn');
                if (payRefundReject) {
                    const id = parseInt(payRefundReject.dataset.id, 10);
                    openRejectModal(function(note) {
                        postAction(
                            '/CeylonGo/public/admin/payment/reject-refund',
                            { booking_id: id, reject_note: note },
                            'Refund rejected. Customer has been notified by email.',
                            'Could not reject refund.'
                        );
                    });
                    return;
                }

                // ── Trip: view details ────────────────────────────
                const tripViewBtn = e.target.closest('.trip-pay-view-btn');
                if (tripViewBtn) {
                    const id = parseInt(tripViewBtn.dataset.id, 10);
                    const record = tripPaymentsData.find(t => t.id === id);
                    if (!record) return;
                    document.getElementById('tripPaymentDetailsContent').innerHTML = renderTripPaymentSection(record);
                    document.getElementById('tripPaymentModal').style.display = 'flex';
                    return;
                }

                // ── Trip: approve bank slip ───────────────────────
                const tripSlipApprove = e.target.closest('.trip-pay-slip-approve-btn');
                if (tripSlipApprove) {
                    const id = parseInt(tripSlipApprove.dataset.id, 10);
                    if (!confirm(`Approve bank slip payment for Trip #${id}?`)) return;
                    postAction(
                        '/CeylonGo/public/admin/trip-payment/approve-slip',
                        { trip_id: id },
                        'Bank slip approved. Trip marked as Paid.',
                        'Could not approve slip.'
                    );
                    return;
                }

                // ── Trip: reject bank slip ────────────────────────
                const tripSlipReject = e.target.closest('.trip-pay-slip-reject-btn');
                if (tripSlipReject) {
                    const id = parseInt(tripSlipReject.dataset.id, 10);
                    openRejectModal(function(note) {
                        postAction(
                            '/CeylonGo/public/admin/trip-payment/reject-slip',
                            { trip_id: id, reject_note: note },
                            'Bank slip rejected. Customer has been notified; they can upload a new slip.',
                            'Could not reject slip — check that a slip exists and payment is not yet confirmed.'
                        );
                    });
                    return;
                }

                // ── Trip: approve refund ──────────────────────────
                const tripRefundApprove = e.target.closest('.trip-pay-refund-approve-btn');
                if (tripRefundApprove) {
                    const id = parseInt(tripRefundApprove.dataset.id, 10);
                    if (!confirm(`Approve refund for Trip #${id}?\n\nAn email will be sent to the customer asking for their bank details.`)) return;
                    postAction(
                        '/CeylonGo/public/admin/trip-payment/approve-refund',
                        { trip_id: id },
                        'Refund approved. Email sent to customer requesting bank details.',
                        'Could not approve refund — check that a refund was requested.'
                    );
                    return;
                }

                // ── Trip: reject refund ───────────────────────────
                const tripRefundReject = e.target.closest('.trip-pay-refund-reject-btn');
                if (tripRefundReject) {
                    const id = parseInt(tripRefundReject.dataset.id, 10);
                    openRejectModal(function(note) {
                        postAction(
                            '/CeylonGo/public/admin/trip-payment/reject-refund',
                            { trip_id: id, reject_note: note },
                            'Refund rejected. Customer has been notified by email.',
                            'Could not reject refund.'
                        );
                    });
                    return;
                }
            });

            // ── Close view modals ─────────────────────────────────
            document.querySelector('.pay-modal-close').addEventListener('click', function() {
                document.getElementById('paymentModal').style.display = 'none';
            });
            document.querySelector('.trip-pay-modal-close').addEventListener('click', function() {
                document.getElementById('tripPaymentModal').style.display = 'none';
            });
            window.addEventListener('click', function(e) {
                const payModal  = document.getElementById('paymentModal');
                const tripModal = document.getElementById('tripPaymentModal');
                if (e.target === payModal)  payModal.style.display  = 'none';
                if (e.target === tripModal) tripModal.style.display = 'none';
            });

            // ── Search buttons (live filter; same as typing) ───────
            function applyTripPaySearch() {
                const input = document.getElementById('tripPaySearchInput');
                if (input) input.dispatchEvent(new Event('input', { bubbles: true }));
            }
            function applyPaySearch() {
                const input = document.getElementById('paySearchInput');
                if (input) input.dispatchEvent(new Event('input', { bubbles: true }));
            }
        </script>
    </body>
</html>