<?php
/**
 * Reports & Analysis — expects variables from AdminReportController@index.
 * @var bool $generated
 * @var string $reportType
 * @var array $filters
 * @var string $search
 * @var string $sort
 * @var string $dir
 * @var int $page
 * @var int $perPage
 * @var int $totalRows
 * @var int $totalPages
 * @var array $reportData
 * @var array $summary
 * @var array|null $reportChart  Single filter-aware chart payload for this page (not PDF).
 */
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /CeylonGo/public/login');
    exit();
}

$preserved = array_merge($_GET, [
    'generated'         => '1',
    'type'                => $reportType,
    'date_from'           => $filters['date_from'] ?? '',
    'date_to'             => $filters['date_to'] ?? '',
    'booking_status'      => $filters['booking_status'] ?? 'all',
    'pay_method'          => $filters['pay_method'] ?? 'all',
    'pay_status'          => $filters['pay_status'] ?? 'all',
    'pay_source'          => $filters['pay_source'] ?? 'all',
    'user_role'           => $filters['user_role'] ?? 'all',
    'user_status'         => $filters['user_status'] ?? 'all',
    'provider_category'   => $filters['provider_category'] ?? 'all',
    'provider_status'     => $filters['provider_status'] ?? 'all',
    'q'                   => $search,
    'sort'                => $sort,
    'dir'                 => $dir,
]);

$baseQs = static function (array $extra = []) use ($preserved): string {
    return http_build_query(array_merge($preserved, $extra));
};

$exportQs = $baseQs();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php require_once __DIR__ . '/../partials/app_notify_script.php'; ?>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="/CeylonGo/public/css/admin/reports.css">
        <link rel="stylesheet" href="/CeylonGo/public/css/transport/base.css">
        <link rel="stylesheet" href="/CeylonGo/public/css/transport/navbar.css">
        <link rel="stylesheet" href="/CeylonGo/public/css/transport/sidebar.css">
        <link rel="stylesheet" href="/CeylonGo/public/css/transport/footer.css">
        <link rel="stylesheet" href="/CeylonGo/public/css/transport/responsive.css">
        <title>Reports &amp; Analysis — Admin</title>
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
                    <img src="/CeylonGo/public/images/profile.jpg" alt="User" class="profile-pic" onclick="document.getElementById('profileDropdown').classList.toggle('show')">
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
                    <li><a href="/CeylonGo/public/admin/payments"><i class="fa-solid fa-credit-card"></i> Payments</a></li>
                    <li><a href="/CeylonGo/public/admin/inquiries"><i class="fa-solid fa-circle-question"></i> Inquiries</a></li>
                    <li><a href="/CeylonGo/public/admin/packages"><i class="fa-solid fa-bullhorn"></i> Packages</a></li>
                    <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-regular fa-star"></i> Reviews</a></li>
                    <li class="active"><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports &amp; Analysis</a></li>
                </ul>
            </div>

            <div class="main-content">
                <div class="reports-management">
                    <h2 class="page-title">Reports &amp; Analysis</h2>

                    <form class="report-form" method="get" action="/CeylonGo/public/admin/reports" id="reportForm">
                        <input type="hidden" name="generated" value="1">
                        <div class="report-form-grid">
                            <div>
                                <label for="type">Report type</label>
                                <select name="type" id="type">
                                    <option value="users" <?= $reportType === 'users' ? 'selected' : '' ?>>Users</option>
                                    <option value="bookings" <?= $reportType === 'bookings' ? 'selected' : '' ?>>Bookings</option>
                                    <option value="payments" <?= $reportType === 'payments' ? 'selected' : '' ?>>Payments</option>
                                    <option value="providers" <?= $reportType === 'providers' ? 'selected' : '' ?>>Service Providers</option>
                                </select>
                            </div>
                            <div>
                                <label for="date_from">From date</label>
                                <input type="date" name="date_from" id="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
                            </div>
                            <div>
                                <label for="date_to">To date</label>
                                <input type="date" name="date_to" id="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="filter-dynamic" id="dynamicFilters">
                            <div data-for="bookings" style="display:<?= $reportType === 'bookings' ? 'block' : 'none' ?>;">
                                <label for="booking_status">Booking status</label>
                                <select name="booking_status" id="booking_status" class="w-full">
                                    <option value="all" <?= ($filters['booking_status'] ?? '') === 'all' ? 'selected' : '' ?>>All</option>
                                    <option value="pending" <?= ($filters['booking_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="confirmed" <?= ($filters['booking_status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                    <option value="cancelled" <?= ($filters['booking_status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                                <p class="sub-text" style="margin-top:6px;font-size:12px;color:#888;">Package “approved” and custom trips “confirmed/completed” count as confirmed.</p>
                            </div>
                            <div data-for="payments" style="display:<?= $reportType === 'payments' ? 'block' : 'none' ?>;">
                                <div class="report-form-grid">
                                    <div>
                                        <label for="pay_source">Payment scope</label>
                                        <select name="pay_source" id="pay_source">
                                            <option value="all" <?= ($filters['pay_source'] ?? 'all') === 'all' ? 'selected' : '' ?>>Package bookings + custom trips</option>
                                            <option value="package" <?= ($filters['pay_source'] ?? '') === 'package' ? 'selected' : '' ?>>Package bookings only</option>
                                            <option value="trip" <?= ($filters['pay_source'] ?? '') === 'trip' ? 'selected' : '' ?>>Custom trips only</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="pay_method">Payment method</label>
                                        <select name="pay_method" id="pay_method">
                                            <option value="all" <?= ($filters['pay_method'] ?? '') === 'all' ? 'selected' : '' ?>>All</option>
                                            <option value="bank_transfer" <?= ($filters['pay_method'] ?? '') === 'bank_transfer' ? 'selected' : '' ?>>Bank transfer</option>
                                            <option value="online" <?= ($filters['pay_method'] ?? '') === 'online' ? 'selected' : '' ?>>Online / other</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="pay_status">Payment status</label>
                                        <?php
                                            $_paySel = strtolower((string) ($filters['pay_status'] ?? 'all'));
                                            if ($_paySel === 'paid') {
                                                $_paySel = 'received';
                                            }
                                        ?>
                                        <select name="pay_status" id="pay_status">
                                            <option value="all" <?= $_paySel === 'all' ? 'selected' : '' ?>>All</option>
                                            <option value="awaiting" <?= $_paySel === 'awaiting' ? 'selected' : '' ?>>Awaiting</option>
                                            <option value="received" <?= $_paySel === 'received' ? 'selected' : '' ?>>Received</option>
                                            <option value="refunded" <?= $_paySel === 'refunded' ? 'selected' : '' ?>>Refunded</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div data-for="users" style="display:<?= $reportType === 'users' ? 'block' : 'none' ?>;">
                                <div class="report-form-grid">
                                    <div>
                                        <label for="user_status">Account status</label>
                                        <select name="user_status" id="user_status">
                                            <option value="all" <?= ($filters['user_status'] ?? '') === 'all' ? 'selected' : '' ?>>All</option>
                                            <option value="active" <?= ($filters['user_status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                                            <option value="inactive" <?= ($filters['user_status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div data-for="providers" style="display:<?= $reportType === 'providers' ? 'block' : 'none' ?>;">
                                <div class="report-form-grid">
                                    <div>
                                        <label for="provider_category">Category</label>
                                        <select name="provider_category" id="provider_category">
                                            <option value="all" <?= ($filters['provider_category'] ?? '') === 'all' ? 'selected' : '' ?>>All</option>
                                            <option value="guide" <?= ($filters['provider_category'] ?? '') === 'guide' ? 'selected' : '' ?>>Tour guides</option>
                                            <option value="hotel" <?= ($filters['provider_category'] ?? '') === 'hotel' ? 'selected' : '' ?>>Hotels</option>
                                            <option value="transport" <?= ($filters['provider_category'] ?? '') === 'transport' ? 'selected' : '' ?>>Transport Providers</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="provider_status">Status</label>
                                        <select name="provider_status" id="provider_status">
                                            <option value="all" <?= ($filters['provider_status'] ?? '') === 'all' ? 'selected' : '' ?>>All</option>
                                            <option value="active" <?= ($filters['provider_status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                                            <option value="inactive" <?= ($filters['provider_status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="footer-buttons" style="margin-top: 24px;">
                            <button type="submit" class="report-link-btn"><i class="fa-solid fa-play"></i> Generate report</button>
                            <a class="report-link-btn report-link-btn-secondary" href="/CeylonGo/public/admin/reports/export-pdf?<?= htmlspecialchars($exportQs) ?>"><i class="fa-solid fa-file-pdf"></i> Download PDF</a>
                        </div>
                    </form>

                    <?php /* Report results always shown — no empty placeholder needed */ ?>

                    <form method="get" class="report-form" id="reportSearchForm" style="padding:12px 16px;">
                        <input type="hidden" name="generated" value="1">
                        <input type="hidden" name="type" value="<?= htmlspecialchars($reportType) ?>">
                        <input type="hidden" name="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
                        <input type="hidden" name="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
                        <input type="hidden" name="booking_status" value="<?= htmlspecialchars($filters['booking_status'] ?? '') ?>">
                        <input type="hidden" name="pay_method" value="<?= htmlspecialchars($filters['pay_method'] ?? '') ?>">
                        <input type="hidden" name="pay_status" value="<?= htmlspecialchars($filters['pay_status'] ?? '') ?>">
                        <input type="hidden" name="pay_source" value="<?= htmlspecialchars($filters['pay_source'] ?? 'all') ?>">
                        <input type="hidden" name="user_role" value="<?= htmlspecialchars($filters['user_role'] ?? '') ?>">
                        <input type="hidden" name="user_status" value="<?= htmlspecialchars($filters['user_status'] ?? '') ?>">
                        <input type="hidden" name="provider_category" value="<?= htmlspecialchars($filters['provider_category'] ?? '') ?>">
                        <input type="hidden" name="provider_status" value="<?= htmlspecialchars($filters['provider_status'] ?? '') ?>">
                        <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                        <input type="hidden" name="dir" value="<?= htmlspecialchars($dir) ?>">
                        <label for="q" class="search-inline" style="display:inline-block;width:100%;max-width:320px;">
                            <span style="font-size:12px;font-weight:600;color:#666;">Search table</span>
                            <input type="search" name="q" id="q" value="<?= htmlspecialchars($search) ?>" autocomplete="off" placeholder="Type to filter" style="width:100%;margin-top:4px;padding:8px;border:1px solid #ccc;border-radius:6px;">
                        </label>
                        <span id="reportSearchStatus" style="display:inline-block;margin-left:12px;font-size:12px;color:#888;vertical-align:bottom;padding-top:22px;" aria-live="polite"></span>
                    </form>

                    <h3 class="section-title" style="margin-top:8px;">Summary</h3>
                    <div class="summary-cards">
                        <?php if ($reportType === 'users'): ?>
                            <div class="summary-card"><strong>Registered Customers</strong><span><?= (int) ($summary['total'] ?? 0) ?></span></div>
                            <div class="summary-card" style="border-left-color:#0d6efd;"><strong>Active</strong><span><?= (int) ($summary['active'] ?? 0) ?></span></div>
                            <div class="summary-card" style="border-left-color:#dc3545;"><strong>Inactive</strong><span><?= (int) ($summary['inactive'] ?? 0) ?></span></div>
                        <?php elseif ($reportType === 'bookings'): ?>
                            <div class="summary-card"><strong>Total Bookings</strong><span><?= (int) ($summary['total'] ?? 0) ?></span></div>
                            <div class="summary-card" style="border-left-color:#ffc107;"><strong>Pending</strong><span><?= (int) ($summary['pending'] ?? 0) ?></span></div>
                            <div class="summary-card" style="border-left-color:#0d6efd;"><strong>Confirmed</strong><span><?= (int) ($summary['confirmed'] ?? 0) ?></span></div>
                            <div class="summary-card" style="border-left-color:#dc3545;"><strong>Cancelled</strong><span><?= (int) ($summary['cancelled'] ?? 0) ?></span></div>
                            <div class="summary-card" style="border-left-color:#198754;"><strong>Revenue (LKR)</strong><span><?= number_format((float) ($summary['total_revenue'] ?? 0), 2) ?></span></div>
                        <?php elseif ($reportType === 'payments'): ?>
                            <div class="summary-card summary-card-revenue-primary" title="For this date range and filters: package paid totals, plus custom-trip budget for confirmed/completed (combined when both scopes are included).">
                                <strong>Total revenue (LKR)</strong>
                                <span><?= number_format((float) ($summary['total_revenue'] ?? 0), 2) ?></span>
                            </div>
                            <?php
                            $psScope = $summary['pay_source'] ?? 'all';
                            if ($psScope === 'all'):
                                $ps = $summary['package_summary'] ?? [];
                                $ts = $summary['trip_summary'] ?? [];
                            ?>
                            <div class="summary-card"><strong>No of Bookings - Packages</strong><span><?= (int) ($ps['total'] ?? 0) ?></span></div>
                            <div class="summary-card" style="border-left-color:#198754;"><strong>Total Revenue (LKR) - Packages</strong><span><?= number_format((float) ($ps['total_revenue'] ?? 0), 2) ?></span></div>
                            <div class="summary-card"><strong>Paid Count - Packages</strong><span><?= (int) ($ps['paid'] ?? 0) ?></span></div>
                            <div class="summary-card" style="border-left-color:#ffc107;"><strong>No of Pending Payments - Packages</strong><span><?= (int) ($ps['pending'] ?? 0) ?></span></div>
                            <div class="summary-card"><strong>No of Bookings - Customized Trips</strong><span><?= (int) ($ts['total'] ?? 0) ?></span></div>
                            <div class="summary-card" style="border-left-color:#0d6efd;"><strong>Total Revenue (LKR) - Customized Trips</strong><span><?= number_format((float) ($ts['completed_value'] ?? 0), 2) ?></span></div>
                            <div class="summary-card" style="border-left-color:#198754;"><strong>Paid Count - Customized Trips</strong><span><?= (int) ($ts['paid'] ?? 0) ?></span></div>
                            <div class="summary-card"><strong>No of Pending Payments - Customized Trips</strong><span><?= (int) ($ts['pending'] ?? 0) ?></span></div>
                            <?php elseif ($psScope === 'trip'):
                                $ts = $summary['trip_summary'] ?? [];
                            ?>
                            <div class="summary-card"><strong>No of Bookings</strong><span><?= (int) ($ts['total'] ?? 0) ?></span></div>
                            <div class="summary-card" style="border-left-color:#198754;"><strong>Total Revenue (LKR)</strong><span><?= number_format((float) ($ts['completed_value'] ?? 0), 2) ?></span></div>
                            <div class="summary-card" style="border-left-color:#198754;"><strong>Paid bookings</strong><span><?= (int) ($ts['paid'] ?? 0) ?></span></div>
                            <div class="summary-card"><strong>No of Pending Payments</strong><span><?= (int) ($ts['pending'] ?? 0) ?></span></div>
                            <?php else:
                                $ps = $summary['package_summary'] ?? [];
                            ?>
                            <div class="summary-card"><strong>No of Bookings</strong><span><?= (int) ($ps['total'] ?? 0) ?></span></div>
                            <div class="summary-card" style="border-left-color:#198754;"><strong>Total Revenue (LKR)</strong><span><?= number_format((float) ($ps['total_revenue'] ?? 0), 2) ?></span></div>
                            <div class="summary-card"><strong>Paid bookings</strong><span><?= (int) ($ps['paid'] ?? 0) ?></span></div>
                            <div class="summary-card" style="border-left-color:#ffc107;"><strong>No of Pending Payments</strong><span><?= (int) ($ps['pending'] ?? 0) ?></span></div>
                            <?php endif; ?>
                        <?php elseif ($reportType === 'providers'): ?>
                            <div class="summary-card"><strong>Total providers</strong><span><?= (int) ($summary['total'] ?? 0) ?></span></div>
                            <div class="summary-card" style="border-left-color:#198754;"><strong>Active</strong><span><?= (int) ($summary['active'] ?? 0) ?></span></div>
                            <div class="summary-card" style="border-left-color:#dc3545;"><strong>Inactive</strong><span><?= (int) ($summary['inactive'] ?? 0) ?></span></div>
                            <div class="summary-card"><strong>Guides</strong><span><?= (int) ($summary['guide'] ?? 0) ?></span></div>
                            <div class="summary-card"><strong>Hotels</strong><span><?= (int) ($summary['hotel'] ?? 0) ?></span></div>
                            <div class="summary-card"><strong>Transport Providers</strong><span><?= (int) ($summary['transport'] ?? 0) ?></span></div>
                        <?php endif; ?>
                    </div>

                    <h3 class="section-title">Data</h3>
                    <div class="data-table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <?php if ($reportType === 'users'): ?>
                                        <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'email', 'dir' => ($sort === 'email' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Email</a></th>
                                        <th>Ref ID</th>
                                        <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'created_at', 'dir' => ($sort === 'created_at' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Registered</a></th>
                                        <th>Active</th>
                                    <?php elseif ($reportType === 'bookings'): ?>
                                        <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'type', 'dir' => ($sort === 'type' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Type</a></th>
                                        <th>ID</th>
                                        <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'customer', 'dir' => ($sort === 'customer' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Customer</a></th>
                                        <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'status', 'dir' => ($sort === 'status' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Status</a></th>
                                        <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'created_at', 'dir' => ($sort === 'created_at' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Created</a></th>
                                        <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'amount', 'dir' => ($sort === 'amount' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Amount</a></th>
                                        <th>Detail</th>
                                    <?php elseif ($reportType === 'payments'): ?>
                                        <?php if (($filters['pay_source'] ?? 'all') === 'all'): ?>
                                            <th>Source</th>
                                        <?php endif; ?>
                                        <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'id', 'dir' => ($sort === 'id' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">ID</a></th>
                                        <th>Customer</th>
                                        <th>Email</th>
                                        <th>Package / destination</th>
                                        <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'amount', 'dir' => ($sort === 'amount' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Amount</a></th>
                                        <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'status', 'dir' => ($sort === 'status' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Status</a></th>
                                        <th>Method</th>
                                        <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'created_at', 'dir' => ($sort === 'created_at' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Created</a></th>
                                        <th>Notes</th>
                                    <?php elseif ($reportType === 'providers'): ?>
                                        <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'provider_name', 'dir' => ($sort === 'provider_name' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Name</a></th>
                                        <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'email', 'dir' => ($sort === 'email' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Email</a></th>
                                        <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'role', 'dir' => ($sort === 'role' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Category</a></th>
                                        <th>Active</th>
                                        <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'registered_at', 'dir' => ($sort === 'registered_at' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Registered</a></th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($reportType === 'users'): ?>
                                    <?php foreach ($reportData as $row): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                                            <td><?= (int) ($row['ref_id'] ?? 0) ?></td>
                                            <td><?= htmlspecialchars($row['created_at'] ?? '') ?></td>
                                            <td><?= !empty($row['account_active']) ? 'Yes' : 'No' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php elseif ($reportType === 'bookings'): ?>
                                    <?php foreach ($reportData as $row): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['booking_type'] ?? '') ?></td>
                                            <td><?= (int) ($row['row_id'] ?? 0) ?></td>
                                            <td><?= htmlspecialchars($row['customer'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($row['created_at'] ?? '') ?></td>
                                            <td>LKR <?= number_format((float) ($row['amount'] ?? 0), 2) ?></td>
                                            <td><?php $d = (string)($row['detail'] ?? ''); echo htmlspecialchars(strlen($d) > 80 ? substr($d, 0, 77) . '…' : $d); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php elseif ($reportType === 'payments'): ?>
                                    <?php foreach ($reportData as $row): ?>
                                        <tr>
                                            <?php if (($filters['pay_source'] ?? 'all') === 'all'): ?>
                                                <td><?= (($row['payment_source'] ?? '') === 'trip') ? 'Custom trip' : 'Package' ?></td>
                                            <?php endif; ?>
                                            <td><?= (int) ($row['id'] ?? 0) ?></td>
                                            <td><?= htmlspecialchars($row['customer'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                                            <td><?php $pn = (string)($row['detail'] ?? ''); echo htmlspecialchars(strlen($pn) > 40 ? substr($pn, 0, 37) . '…' : $pn); ?></td>
                                            <td>LKR <?= number_format((float) ($row['amount'] ?? 0), 2) ?></td>
                                            <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($row['pay_method'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($row['created_at'] ?? '') ?></td>
                                            <td><?php $n = (string)($row['notes'] ?? ''); echo htmlspecialchars(strlen($n) > 48 ? substr($n, 0, 45) . '…' : $n); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php elseif ($reportType === 'providers'): ?>
                                    <?php foreach ($reportData as $row): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['provider_name'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($row['role'] ?? '') ?></td>
                                            <td><?= !empty($row['is_active']) ? 'Yes' : 'No' ?></td>
                                            <td><?= htmlspecialchars($row['registered_at'] ?? '') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <?php if (empty($reportData)): ?>
                                    <tr><td colspan="12" style="text-align:center;padding:24px;color:#888;">
                                        <?php if ($reportType === 'users'): ?>
                                            No customers in this period. Try clearing the from/to dates to see all tourist registrations, or choose a wider date range that includes when accounts were created.
                                        <?php else: ?>
                                            No rows match your filters.
                                        <?php endif; ?>
                                    </td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <?php if ($p === $page): ?>
                                <span class="current"><?= $p ?></span>
                            <?php else: ?>
                                <a href="?<?= htmlspecialchars($baseQs(['page' => $p])) ?>"><?= $p ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <span style="border:none;"><?= (int) $totalRows ?> rows</span>
                    </div>
                    <?php endif; ?>

                    <h3 class="section-title" style="margin-top:28px;">Chart</h3>
                    <p class="sub-text">Based on the report type, date range, and filters above (not included in PDF export).</p>
                    <?php if (!empty($reportChart)): ?>
                    <div class="chart-box chart-box-report">
                        <h4><?= htmlspecialchars($reportChart['title'] ?? 'Trend') ?></h4>
                        <canvas id="reportChartCanvas"></canvas>
                    </div>
                    <?php else: ?>
                    <div class="chart-empty">
                        <p style="margin:0;color:#888;font-size:14px;">No chart data for the selected period and filters. Try widening the date range or clearing filters.</p>
                    </div>
                    <?php endif; ?>

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

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        <script>
            window.REPORT_PAGE_CHART = <?= ($generated && !empty($reportChart)) ? json_encode([
                'title'      => $reportChart['title'] ?? '',
                'labels'     => $reportChart['labels'] ?? [],
                'values'     => $reportChart['values'] ?? [],
                'valueKind'  => $reportChart['value_kind'] ?? 'count',
                'chartKind'  => $reportChart['chart_kind'] ?? 'bar',
            ]) : 'null' ?>;

            document.getElementById('type').addEventListener('change', function () {
                var v = this.value;
                // Show the relevant dynamic filter section
                document.querySelectorAll('#dynamicFilters [data-for]').forEach(function (el) {
                    el.style.display = el.getAttribute('data-for') === v ? 'block' : 'none';
                });
                // Auto-submit to load data for the newly selected report type
                document.getElementById('reportForm').submit();
            });

            (function () {
                var form = document.getElementById('reportSearchForm');
                var input = document.getElementById('q');
                var statusEl = document.getElementById('reportSearchStatus');
                if (!form || !input) return;

                var debounceMs = 380;
                var t = null;
                var lastSubmitted = input.value;

                function setStatus(msg) {
                    if (statusEl) statusEl.textContent = msg || '';
                }

                input.addEventListener('input', function () {
                    clearTimeout(t);
                    setStatus('Searching…');
                    t = setTimeout(function () {
                        if (input.value === lastSubmitted) {
                            setStatus('');
                            return;
                        }
                        lastSubmitted = input.value;
                        form.submit();
                    }, debounceMs);
                });
            })();
        </script>

        <script src="/CeylonGo/public/js/reports_charts.js"></script>
    </body>
</html>
