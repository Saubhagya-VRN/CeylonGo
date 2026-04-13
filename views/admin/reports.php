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
 * @var array $charts
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
    <style>
        .report-form { background:#fff; padding:20px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:24px; }
        .report-form-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap:14px; align-items:end; }
        .report-form label { display:block; font-size:12px; color:#666; margin-bottom:4px; font-weight:600; }
        .report-form input, .report-form select { width:100%; padding:8px 10px; border:1px solid #ccc; border-radius:6px; font-size:14px; }
        .filter-dynamic { margin-top:12px; padding-top:12px; border-top:1px solid #eee; }
        .btn-row { display:flex; flex-wrap:wrap; gap:10px; margin-top:16px; }
        .btn-gen { background:#198754; color:#fff; border:none; padding:10px 18px; border-radius:6px; cursor:pointer; font-weight:600; }
        .btn-export { background:#212529; color:#fff; border:none; padding:10px 18px; border-radius:6px; cursor:pointer; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
        .btn-export.secondary { background:#6c757d; }
        .empty-preview { text-align:center; padding:48px 20px; color:#888; background:#fafafa; border-radius:8px; border:1px dashed #ccc; }
        .summary-cards { display:grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap:14px; margin-bottom:20px; }
        .summary-card { background:#fff; padding:16px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,.06); border-left:4px solid #198754; }
        .summary-card strong { display:block; font-size:12px; color:#666; text-transform:uppercase; letter-spacing:.04em; }
        .summary-card span { font-size:22px; font-weight:700; color:#222; }
        .data-table-wrap { overflow-x:auto; background:#fff; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,.06); }
        .data-table { width:100%; border-collapse:collapse; font-size:14px; }
        .data-table th, .data-table td { padding:10px 12px; border-bottom:1px solid #eee; text-align:left; }
        .data-table th a { color:inherit; text-decoration:none; }
        .data-table th a:hover { text-decoration:underline; }
        .pagination { display:flex; gap:8px; align-items:center; flex-wrap:wrap; margin-top:16px; }
        .pagination a, .pagination span { padding:6px 12px; border-radius:6px; border:1px solid #ddd; text-decoration:none; color:#333; font-size:13px; }
        .pagination a:hover { background:#f0f0f0; }
        .pagination .current { background:#198754; color:#fff; border-color:#198754; }
        .chart-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:20px; margin-top:20px; }
        .chart-box { background:#fff; padding:16px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,.06); }
        .chart-box h4 { margin:0 0 12px; font-size:15px; }
        .chart-box canvas { max-height:260px; }
        .search-inline { max-width:280px; }
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
                                <option value="packages" <?= $reportType === 'packages' ? 'selected' : '' ?>>Tour packages</option>
                                <option value="trip_payments" <?= $reportType === 'trip_payments' ? 'selected' : '' ?>>Custom trip payments</option>
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
                                    <label for="pay_method">Payment method</label>
                                    <select name="pay_method" id="pay_method">
                                        <option value="all" <?= ($filters['pay_method'] ?? '') === 'all' ? 'selected' : '' ?>>All</option>
                                        <option value="bank_transfer" <?= ($filters['pay_method'] ?? '') === 'bank_transfer' ? 'selected' : '' ?>>Bank transfer</option>
                                        <option value="online" <?= ($filters['pay_method'] ?? '') === 'online' ? 'selected' : '' ?>>Online / other</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="pay_status">Payment status</label>
                                    <select name="pay_status" id="pay_status">
                                        <option value="all" <?= ($filters['pay_status'] ?? '') === 'all' ? 'selected' : '' ?>>All</option>
                                        <option value="pending" <?= ($filters['pay_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="approved" <?= ($filters['pay_status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                                        <option value="paid" <?= ($filters['pay_status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                                        <option value="rejected" <?= ($filters['pay_status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                        <option value="cancelled" <?= ($filters['pay_status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <p class="sub-text" style="margin-top:6px;font-size:12px;color:#888;">Based on package booking payment records.</p>
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
                                        <option value="guide" <?= ($filters['provider_category'] ?? '') === 'guide' ? 'selected' : '' ?>>Tour guide</option>
                                        <option value="hotel" <?= ($filters['provider_category'] ?? '') === 'hotel' ? 'selected' : '' ?>>Hotel</option>
                                        <option value="transport" <?= ($filters['provider_category'] ?? '') === 'transport' ? 'selected' : '' ?>>Transport</option>
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
                        <div data-for="packages" style="display:<?= $reportType === 'packages' ? 'block' : 'none' ?>;">
                            <p class="sub-text" style="margin:0;font-size:13px;color:#555;">Lists tour packages in the catalog. Filter by <strong>created</strong> date range.</p>
                        </div>
                        <div data-for="trip_payments" style="display:<?= $reportType === 'trip_payments' ? 'block' : 'none' ?>;">
                            <p class="sub-text" style="margin:0;font-size:13px;color:#555;">Customized trip bookings (trips). Filter by trip <strong>created</strong> date.</p>
                        </div>
                    </div>

                    <div class="btn-row">
                        <button type="submit" class="btn-gen"><i class="fa-solid fa-play"></i> Generate report</button>
                        <a class="btn-export secondary" href="/CeylonGo/public/admin/reports/export-pdf?<?= htmlspecialchars($exportQs) ?>"><i class="fa-solid fa-file-pdf"></i> Download PDF</a>
                    </div>
                </form>

                <?php if (!$generated): ?>
                    <div class="empty-preview">
                        <i class="fa-solid fa-file-lines" style="font-size:40px;margin-bottom:12px;opacity:.4;"></i>
                        <p>No report generated yet. Choose filters and click <strong>Generate report</strong>.</p>
                    </div>
                <?php else: ?>

                <form method="get" class="report-form" id="reportSearchForm" style="padding:12px 16px;">
                    <input type="hidden" name="generated" value="1">
                    <input type="hidden" name="type" value="<?= htmlspecialchars($reportType) ?>">
                    <input type="hidden" name="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
                    <input type="hidden" name="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
                    <input type="hidden" name="booking_status" value="<?= htmlspecialchars($filters['booking_status'] ?? '') ?>">
                    <input type="hidden" name="pay_method" value="<?= htmlspecialchars($filters['pay_method'] ?? '') ?>">
                    <input type="hidden" name="pay_status" value="<?= htmlspecialchars($filters['pay_status'] ?? '') ?>">
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
                        <div class="summary-card"><strong>Registered customers</strong><span><?= (int) ($summary['total'] ?? 0) ?></span></div>
                        <div class="summary-card" style="border-left-color:#0d6efd;"><strong>Active</strong><span><?= (int) ($summary['active'] ?? 0) ?></span></div>
                        <div class="summary-card" style="border-left-color:#dc3545;"><strong>Inactive</strong><span><?= (int) ($summary['inactive'] ?? 0) ?></span></div>
                    <?php elseif ($reportType === 'bookings'): ?>
                        <div class="summary-card"><strong>Total records</strong><span><?= (int) ($summary['total'] ?? 0) ?></span></div>
                        <div class="summary-card" style="border-left-color:#ffc107;"><strong>Pending</strong><span><?= (int) ($summary['pending'] ?? 0) ?></span></div>
                        <div class="summary-card" style="border-left-color:#0d6efd;"><strong>Confirmed</strong><span><?= (int) ($summary['confirmed'] ?? 0) ?></span></div>
                        <div class="summary-card" style="border-left-color:#dc3545;"><strong>Cancelled</strong><span><?= (int) ($summary['cancelled'] ?? 0) ?></span></div>
                        <div class="summary-card" style="border-left-color:#198754;"><strong>Revenue (LKR)</strong><span><?= number_format((float) ($summary['total_revenue'] ?? 0), 2) ?></span></div>
                    <?php elseif ($reportType === 'payments'): ?>
                        <div class="summary-card"><strong>Total rows</strong><span><?= (int) ($summary['total'] ?? 0) ?></span></div>
                        <div class="summary-card" style="border-left-color:#198754;"><strong>Paid amount (LKR)</strong><span><?= number_format((float) ($summary['total_revenue'] ?? 0), 2) ?></span></div>
                        <div class="summary-card"><strong>Paid bookings</strong><span><?= (int) ($summary['paid'] ?? 0) ?></span></div>
                        <div class="summary-card" style="border-left-color:#ffc107;"><strong>Pending / approved</strong><span><?= (int) ($summary['pending'] ?? 0) ?></span></div>
                    <?php elseif ($reportType === 'providers'): ?>
                        <div class="summary-card"><strong>Total providers</strong><span><?= (int) ($summary['total'] ?? 0) ?></span></div>
                        <div class="summary-card" style="border-left-color:#198754;"><strong>Active</strong><span><?= (int) ($summary['active'] ?? 0) ?></span></div>
                        <div class="summary-card"><strong>Guides</strong><span><?= (int) ($summary['guide'] ?? 0) ?></span></div>
                        <div class="summary-card"><strong>Hotels</strong><span><?= (int) ($summary['hotel'] ?? 0) ?></span></div>
                        <div class="summary-card"><strong>Transport</strong><span><?= (int) ($summary['transport'] ?? 0) ?></span></div>
                    <?php elseif ($reportType === 'packages'): ?>
                        <div class="summary-card"><strong>Total packages</strong><span><?= (int) ($summary['total'] ?? 0) ?></span></div>
                        <div class="summary-card" style="border-left-color:#198754;"><strong>Average price (LKR)</strong><span><?= number_format((float) ($summary['avg_price'] ?? 0), 2) ?></span></div>
                        <div class="summary-card"><strong>Trending</strong><span><?= (int) ($summary['trending'] ?? 0) ?></span></div>
                    <?php elseif ($reportType === 'trip_payments'): ?>
                        <div class="summary-card"><strong>Total trips</strong><span><?= (int) ($summary['total'] ?? 0) ?></span></div>
                        <div class="summary-card" style="border-left-color:#198754;"><strong>Budget confirmed/completed (LKR)</strong><span><?= number_format((float) ($summary['completed_value'] ?? 0), 2) ?></span></div>
                        <div class="summary-card"><strong>Pending</strong><span><?= (int) ($summary['pending'] ?? 0) ?></span></div>
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
                                    <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'id', 'dir' => ($sort === 'id' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">ID</a></th>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Package</th>
                                    <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'amount', 'dir' => ($sort === 'amount' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Amount</a></th>
                                    <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'status', 'dir' => ($sort === 'status' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Status</a></th>
                                    <th>Method</th>
                                    <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'created_at', 'dir' => ($sort === 'created_at' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Created</a></th>
                                <?php elseif ($reportType === 'providers'): ?>
                                    <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'provider_name', 'dir' => ($sort === 'provider_name' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Name</a></th>
                                    <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'email', 'dir' => ($sort === 'email' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Email</a></th>
                                    <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'role', 'dir' => ($sort === 'role' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Category</a></th>
                                    <th>Active</th>
                                    <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'registered_at', 'dir' => ($sort === 'registered_at' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Registered</a></th>
                                <?php elseif ($reportType === 'packages'): ?>
                                    <th>ID</th>
                                    <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'title', 'dir' => ($sort === 'title' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Title</a></th>
                                    <th>Location</th>
                                    <th>Category</th>
                                    <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'price', 'dir' => ($sort === 'price' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Price</a></th>
                                    <th>Rating</th>
                                    <th>Reviews</th>
                                    <th>Trending</th>
                                    <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'created_at', 'dir' => ($sort === 'created_at' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Created</a></th>
                                <?php elseif ($reportType === 'trip_payments'): ?>
                                    <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'id', 'dir' => ($sort === 'id' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">ID</a></th>
                                    <th>Customer</th>
                                    <th>Destination</th>
                                    <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'amount', 'dir' => ($sort === 'amount' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Budget</a></th>
                                    <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'status', 'dir' => ($sort === 'status' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Status</a></th>
                                    <th>Start</th>
                                    <th>People</th>
                                    <th><a href="?<?= htmlspecialchars($baseQs(['sort' => 'created_at', 'dir' => ($sort === 'created_at' && $dir === 'ASC') ? 'DESC' : 'ASC', 'page' => 1])) ?>">Created</a></th>
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
                                        <td><?= (int) ($row['id'] ?? 0) ?></td>
                                        <td><?= htmlspecialchars($row['fullname'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                                        <td><?php $pn = (string)($row['package_name'] ?? ''); echo htmlspecialchars(strlen($pn) > 40 ? substr($pn, 0, 37) . '…' : $pn); ?></td>
                                        <td>LKR <?= number_format((float) ($row['total_amount'] ?? 0), 2) ?></td>
                                        <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['pay_method'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['created_at'] ?? '') ?></td>
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
                            <?php elseif ($reportType === 'packages'): ?>
                                <?php foreach ($reportData as $row): ?>
                                    <tr>
                                        <td><?= (int) ($row['id'] ?? 0) ?></td>
                                        <td><?= htmlspecialchars($row['title'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['location'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['category'] ?? '') ?></td>
                                        <td>LKR <?= number_format((float) ($row['price'] ?? 0), 2) ?></td>
                                        <td><?= htmlspecialchars((string) ($row['rating'] ?? '')) ?></td>
                                        <td><?= (int) ($row['reviews'] ?? 0) ?></td>
                                        <td><?= !empty($row['trending']) ? 'Yes' : 'No' ?></td>
                                        <td><?= htmlspecialchars($row['created_at'] ?? '') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php elseif ($reportType === 'trip_payments'): ?>
                                <?php foreach ($reportData as $row): ?>
                                    <tr>
                                        <td><?= (int) ($row['id'] ?? 0) ?></td>
                                        <td><?= htmlspecialchars($row['customer_name'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['destination'] ?? '') ?></td>
                                        <td>LKR <?= number_format((float) ($row['budget_lkr'] ?? 0), 2) ?></td>
                                        <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
                                        <td><?= htmlspecialchars((string) ($row['start_date'] ?? '')) ?></td>
                                        <td><?= (int) ($row['number_of_people'] ?? 0) ?></td>
                                        <td><?= htmlspecialchars($row['created_at'] ?? '') ?></td>
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

                <h3 class="section-title" style="margin-top:28px;">Charts</h3>
                <p class="sub-text">Trends respect the date range above (all report types use the same period for comparison).</p>
                <div class="chart-grid">
                    <div class="chart-box">
                        <h4>Bookings per month</h4>
                        <canvas id="chartBookingsMonthly"></canvas>
                    </div>
                    <div class="chart-box">
                        <h4>Revenue trend (LKR)</h4>
                        <canvas id="chartRevenueTrend"></canvas>
                    </div>
                    <div class="chart-box">
                        <h4><?= $reportType === 'users' ? 'New customer accounts per month' : 'New accounts per month' ?></h4>
                        <canvas id="chartUserGrowth"></canvas>
                    </div>
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
        window.REPORT_CHARTS = <?= $generated ? json_encode([
            'bookingsMonthly' => [
                'labels' => $charts['bookings_monthly']['labels'] ?? [],
                'values' => $charts['bookings_monthly']['counts'] ?? [],
            ],
            'revenue' => [
                'labels' => $charts['revenue']['labels'] ?? [],
                'values' => $charts['revenue']['amounts'] ?? [],
            ],
            'userGrowth' => [
                'labels' => $charts['user_growth']['labels'] ?? [],
                'values' => $charts['user_growth']['counts'] ?? [],
            ],
        ]) : 'null' ?>;

        document.getElementById('type').addEventListener('change', function () {
            var v = this.value;
            document.querySelectorAll('#dynamicFilters [data-for]').forEach(function (el) {
                el.style.display = el.getAttribute('data-for') === v ? 'block' : 'none';
            });
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
