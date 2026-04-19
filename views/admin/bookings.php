<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Package bookings: apply search, status filter and date filter server-side (status column)
    $pkgSearch = $_GET['pkg_search'] ?? '';
    $pkgSelectedStatus = $_GET['pkg_status'] ?? 'all';
    if (!in_array($pkgSelectedStatus, ['all', 'approved', 'pending', 'paid', 'cancelled'], true)) {
        $pkgSelectedStatus = 'all';
    }
    $pkgDate = $_GET['pkg_date'] ?? '';

    $filteredPkgBookings = array_filter($packageBookings ?? [], function($pb) use ($pkgSearch, $pkgSelectedStatus, $pkgDate) {
        if ($pkgSelectedStatus !== 'all' && strtolower($pb['status']) !== $pkgSelectedStatus) return false;
        if ($pkgDate && date('Y-m-d', strtotime($pb['created_at'])) !== $pkgDate) return false;
        if ($pkgSearch) {
            $q = strtolower($pkgSearch);
            $haystack = strtolower($pb['id'] . ' ' . $pb['fullname'] . ' ' . $pb['email'] . ' ' . $pb['package_name']);
            if (strpos($haystack, $q) === false) return false;
        }
        return true;
    });

    $adminView = $_GET['view'] ?? 'all';
    if (!in_array($adminView, ['all', 'custom', 'package'], true)) {
        $adminView = 'all';
    }
    $showCustomBookings = ($adminView === 'all' || $adminView === 'custom');
    $showPackageBookings = ($adminView === 'all' || $adminView === 'package');

    $adminViewQuery = $_GET;
    $adminViewQuery['view'] = 'all';
    $bookingsViewUrlAll = '/CeylonGo/public/admin/bookings?' . http_build_query($adminViewQuery);
    $adminViewQuery['view'] = 'custom';
    $bookingsViewUrlCustom = '/CeylonGo/public/admin/bookings?' . http_build_query($adminViewQuery);
    $adminViewQuery['view'] = 'package';
    $bookingsViewUrlPackage = '/CeylonGo/public/admin/bookings?' . http_build_query($adminViewQuery);
?>

<!DOCTYPE html>
    <html lang="en">
        <head>
            <?php require_once __DIR__ . '/../partials/app_notify_script.php'; ?>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">

            <!-- Font Awesome (REQUIRED) -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

            <!-- Optional admin-only overrides -->
            <link rel="stylesheet" href="/CeylonGO/public/css/admin/bookings.css">

            <!-- Shared Transport Layout -->
            <link rel="stylesheet" href="/CeylonGO/public/css/transport/base.css">
            <link rel="stylesheet" href="/CeylonGO/public/css/transport/navbar.css">
            <link rel="stylesheet" href="/CeylonGO/public/css/transport/sidebar.css">
            <link rel="stylesheet" href="/CeylonGO/public/css/transport/footer.css">
            
            <!-- Responsive styles (always last) -->
            <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">

            <title>Booking Management</title>
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
                    <li class="active"><a href="/CeylonGo/public/admin/bookings"><i class="fa-regular fa-calendar"></i> Bookings</a></li>
                    <li><a href="/CeylonGo/public/admin/service"><i class="fa-solid fa-van-shuttle"></i> Service Providers</a></li>
                    <li><a href="/CeylonGo/public/admin/payments"><i class="fa-solid fa-credit-card"></i> Payments</a></li>
                    <li><a href="/CeylonGo/public/admin/inquiries"><i class="fa-solid fa-circle-question"></i> Inquiries</a></li>
                    <li><a href="/CeylonGo/public/admin/packages"><i class="fa-solid fa-box-open"></i> Packages</a></li>
                    <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-regular fa-star"></i> Reviews</a></li>
                    <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports & Analysis</a></li>
                </ul>
            </div>

            <div class="main-content">
                <div class="booking-management">
                    <h2 class="page-title">Booking Management</h2>
                    <div class="toolbar" style="margin-bottom:18px;flex-wrap:wrap;">
                        <div class="filter-buttons" style="align-items:center;">
                            <span style="font-size:14px;margin-right:6px;">Table view:</span>
                            <a href="<?= htmlspecialchars($bookingsViewUrlAll) ?>" class="filter-btn <?= $adminView === 'all' ? 'active' : '' ?>">All</a>
                            <a href="<?= htmlspecialchars($bookingsViewUrlCustom) ?>" class="filter-btn <?= $adminView === 'custom' ? 'active' : '' ?>">Customized</a>
                            <a href="<?= htmlspecialchars($bookingsViewUrlPackage) ?>" class="filter-btn <?= $adminView === 'package' ? 'active' : '' ?>">Package</a>
                        </div>
                    </div>

                    <div style="<?= $showCustomBookings ? '' : 'display:none;' ?>">
                    <h4 class="page-title" style="font-size:16px;">Customized Booking Requests</h4>

                    <form method="GET" action="/CeylonGo/public/admin/bookings">
                        <input type="hidden" name="view" value="<?= htmlspecialchars($adminView) ?>">
                        <input type="hidden" name="pkg_search" value="<?= htmlspecialchars($pkgSearch) ?>">
                        <input type="hidden" name="pkg_status" value="<?= htmlspecialchars($pkgSelectedStatus) ?>">
                        <input type="hidden" name="pkg_date"   value="<?= htmlspecialchars($pkgDate) ?>">
                        <div class="toolbar">
                            <div class="search-section">
                                <input type="text" id="tripSearchInput" placeholder="Search by booking ID or customer" class="search-input" value="<?= htmlspecialchars($searchId ?? '') ?>">
                                <button type="button" class="search-btn" onclick="applyTripSearch()">🔍</button>
                            </div>
                            <div class="filter-buttons">
                                <?php
                                    $statuses = ['all','pending','completed','cancelled'];
                                    foreach($statuses as $s):
                                        $active = ($selectedStatus ?? 'all') === $s ? 'active' : '';
                                        echo "<button type='submit' name='status' value='{$s}' class='filter-btn {$active}'>" . ucfirst($s) . "</button>";
                                    endforeach;
                                ?>
                            </div>
                            <div class="date-filter">
                                <input type="date" name="date" class="date-input" value="<?= htmlspecialchars($date ?? '') ?>" onchange="this.form.submit()">
                            </div>
                        </div>
                    </form>

                    <div class="stats-section">
                        <div class="stats-grid">
                            <div class="stat-box">
                                <strong>Total</strong><br>
                                <span><?= (int)($stats['total'] ?? 0) ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Pending</strong><br>
                                <span><?= (int)($stats['pending'] ?? 0) ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Completed</strong><br>
                                <span><?= (int)($stats['completed'] ?? 0) ?></span>
                            </div>
                            <div class="stat-box">
                                <strong>Cancelled</strong><br>
                                <span><?= (int)($stats['cancelled'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>
                    <br>

                    <div class="bookings-section">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; flex-wrap:wrap; gap:10px;">
                            <!-- LEFT: Show entries -->
                            <div class="filter-buttons" style="align-items:center;">
                                <span style="font-size:14px;">Show</span>

                                <select id="rowsPerPage" class="filter-btn small-btn">
                                    <option value="10" selected>10</option>
                                    <option value="15">15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>

                                <span style="font-size:14px;">entries</span>
                            </div>
                            <!-- RIGHT: Pagination -->
                            <div id="paginationControls" class="filter-buttons"></div>
                        </div>
                        <table class="booking-table">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Customer</th>
                                    <th>Destination</th>
                                    <th>People</th>
                                    <th>Days</th>
                                    <th>Start Date</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="bookingsTableBody">
                                <?php if (empty($bookings)): ?>
                                    <tr>
                                        <td colspan="9" style="text-align:center;">
                                            No customized bookings can be found.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($bookings as $b):
                                        switch (strtolower($b['status'])) {
                                            case 'pending':   $statusClass = 'pending';   break;
                                            case 'confirmed': $statusClass = 'approved';  break;
                                            case 'completed': $statusClass = 'completed'; break;
                                            case 'cancelled': $statusClass = 'cancelled'; break;
                                            default:          $statusClass = '';
                                        }
                                    ?>
                                    <tr>
                                        <td><?= (int)$b['booking_id'] ?></td>
                                        <td><?= htmlspecialchars($b['user_name']) ?></td>
                                        <td><?= htmlspecialchars($b['destination']) ?></td>
                                        <td><?= (int)$b['number_of_people'] ?></td>
                                        <td><?= (int)$b['number_of_days'] ?></td>
                                        <td><?= htmlspecialchars($b['start_date']) ?></td>
                                        <td><span class="status <?= $statusClass ?>"><?= ucfirst($b['status']) ?></span></td>
                                        <td><?= date('Y-m-d', strtotime($b['created_at'])) ?></td>
                                        <td>
                                            <button class="icon-btn view-btn" data-booking-id="<?= $b['booking_id'] ?>" title="View Details">👁️</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="footer-buttons" style="margin-top: 24px;">
                        <a href="/CeylonGo/public/admin/reports?type=bookings" class="report-link-btn">
                            Generate Bookings Report
                        </a>
                    </div>
                    </div>

                    <div style="<?= $showPackageBookings ? '' : 'display:none;' ?>">
                    <br><br>
                    <h4 class="page-title" style="font-size:16px;">Package Booking Requests</h4>

                    <form method="GET" action="/CeylonGo/public/admin/bookings">
                        <input type="hidden" name="view" value="<?= htmlspecialchars($adminView) ?>">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($searchId ?? '') ?>">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($selectedStatus ?? 'all') ?>">
                        <input type="hidden" name="date"   value="<?= htmlspecialchars($date ?? '') ?>">
                        <div class="toolbar">
                            <div class="search-section">
                                <input type="text" id="pkgSearchInput" placeholder="Search by customer or package" class="search-input" value="<?= htmlspecialchars($pkgSearch) ?>">
                                <button type="button" class="search-btn" onclick="applyPkgSearch()">🔍</button>
                            </div>
                            <div class="filter-buttons">
                                <?php
                                    $pkgStatuses = [
                                        'all'       => 'All',
                                        'pending'   => 'Pending',
                                        'approved'  => 'Approved',
                                        'paid'      => 'Paid',
                                        'cancelled' => 'Cancelled',
                                    ];
                                    foreach ($pkgStatuses as $val => $label):
                                        $active = $pkgSelectedStatus === $val ? 'active' : '';
                                        echo "<button type='submit' name='pkg_status' value='" . htmlspecialchars($val) . "' class='filter-btn {$active}'>" . htmlspecialchars($label) . "</button>";
                                    endforeach;
                                ?>
                            </div>
                            <div class="date-filter">
                                <input type="date" name="pkg_date" class="date-input" value="<?= htmlspecialchars($pkgDate) ?>" onchange="this.form.submit()">
                            </div>
                        </div>
                    </form>

                    <div class="stats-section">
                        <div class="stats-grid">
                            <?php
                                $pkgStatLabels = [
                                    'total'     => 'Total',
                                    'pending'   => 'Pending',
                                    'approved'  => 'Approved',
                                    'paid'      => 'Paid',
                                    'cancelled' => 'Cancelled',
                                ];
                                foreach ($pkgStatLabels as $k => $label):
                                    $val = (int) ($pkgStats[$k] ?? 0);
                                    echo "<div class='stat-box'><strong>" . htmlspecialchars($label) . "</strong><br><span>{$val}</span></div>";
                                endforeach;
                            ?>
                        </div>
                    </div>
                    <br>

                    <div class="bookings-section">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:15px; margin-bottom:20px; flex-wrap:wrap; gap:10px;"> 
                            <!-- LEFT -->
                            <div class="filter-buttons" style="align-items:center;">
                                <span style="font-size:14px;">Show</span>

                                <select id="pkgRowsPerPage" class="filter-btn small-btn">
                                    <option value="10" selected>10</option>
                                    <option value="15">15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>

                                <span style="font-size:14px;">entries</span>
                            </div>
                            <!-- RIGHT -->
                            <div id="pkgPaginationControls" class="filter-buttons"></div>
                        </div>
                        <table class="booking-table">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Customer</th>
                                    <th>Package</th>
                                    <th>Status</th>
                                    <th>Submitted Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="pkgBookingsTableBody">
                                <?php if (empty($filteredPkgBookings)): ?>
                                    <tr>
                                        <td colspan="6" style="text-align:center;">
                                            No package booking can be found.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($filteredPkgBookings as $pb):
                                        switch (strtolower($pb['status'])) {
                                            case 'pending':   $sc = 'pending';   break;
                                            case 'approved':  $sc = 'approved';  break;
                                            case 'paid':      $sc = 'completed'; break;
                                            case 'rejected':  $sc = 'rejected';  break;
                                            case 'cancelled': $sc = 'cancelled'; break;
                                            default:          $sc = '';
                                        }
                                    ?>
                                    <tr>
                                        <td><?= (int)$pb['id'] ?></td>
                                        <td><?= htmlspecialchars($pb['fullname']) ?></td>
                                        <td><?= htmlspecialchars($pb['package_name']) ?></td>
                                        <td><span class="status <?= $sc ?>"><?= ucfirst($pb['status']) ?></span></td>
                                        <td><?= date('Y-m-d', strtotime($pb['created_at'])) ?></td>
                                        <td>
                                            <button class="icon-btn pkg-view-btn"
                                                    data-id="<?= (int)$pb['id'] ?>"
                                                    title="View Details">👁️</button>
                                            <?php if (strtolower($pb['status']) === 'pending'): ?>
                                                <button class="icon-btn pkg-approve-btn"
                                                        data-id="<?= (int)$pb['id'] ?>"
                                                        title="Approve">✅</button>
                                                <button class="icon-btn danger pkg-reject-btn"
                                                        data-id="<?= (int)$pb['id'] ?>"
                                                        title="Reject">✕</button>
                                            <?php else: ?>
                                                <span style="color:#aaa;font-size:12px;">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="footer-buttons" style="margin-top: 24px;">
                        <a href="/CeylonGo/public/admin/reports?type=bookings" class="report-link-btn">
                            Generate Package Bookings Report
                        </a>
                    </div>
                    </div>
                </div>
            </div>

            <div id="bookingModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <u><h3>Booking Details</h3></u>
                    <div id="bookingDetailsContent">Loading...</div>
                </div>
            </div>

            <div id="rejectModal" class="modal">
                <div class="modal-content" style="max-width:420px;">
                    <span class="reject-close">&times;</span>
                    <h3 style="margin-bottom:16px;">Reject Booking</h3>
                    <p style="color:#555;margin-bottom:12px;">Optionally enter a reason for rejection:</p>
                    <textarea id="rejectNotes" rows="4"
                        style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;box-sizing:border-box;font-size:13px;"
                        placeholder="e.g. Dates unavailable, capacity full..."></textarea>
                    <div style="margin-top:16px;display:flex;gap:10px;justify-content:flex-end;">
                        <button class="footer-btn" id="rejectCancelBtn">Cancel</button>
                        <button class="footer-btn black" id="rejectConfirmBtn">Confirm Reject</button>
                    </div>
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

            // ── Shared modal refs ─────────────────────────────────
            const modal        = document.getElementById("bookingModal");
            const modalContent = document.getElementById("bookingDetailsContent");
            const spanClose    = modal.querySelector(".close");

            // ── Trip Booking: view details ────────────────────────
            document.querySelectorAll(".view-btn").forEach(btn => {
                btn.addEventListener("click", () => {
                    const bookingId = btn.dataset.bookingId;
                    if (!bookingId) return alert("Invalid booking ID");

                    modalContent.innerHTML = "Loading...";
                    modal.style.display = "block";

                    fetch('/CeylonGo/public/admin/booking-details?booking_id=' + bookingId)
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            modalContent.innerHTML = `<p style="color:red">${data.message}</p>`;
                            return;
                        }
                        const b = data.booking;
                        let html = `<p><strong>Customer:</strong> ${b.user_name}</p>`;
                        html += `<p><strong>Status:</strong> ${b.status}</p>`;
                        html += `<p><strong>Submitted:</strong> ${b.created_at}</p>`;
                        html += `<h4>Trip Details</h4><table>
                                    <tr><th>Field</th><th>Details</th></tr>
                                    <tr><td>Destination</td><td>${b.destination}</td></tr>
                                    <tr><td>Start Date</td><td>${b.start_date}</td></tr>
                                    <tr><td>People</td><td>${b.number_of_people}</td></tr>
                                    <tr><td>Days</td><td>${b.number_of_days}</td></tr>
                                    ${b.budget_lkr ? `<tr><td>Budget</td><td>LKR ${Number(b.budget_lkr).toLocaleString()}</td></tr>` : ''}
                                </table>`;
                        modalContent.innerHTML = html;
                    })
                    .catch(() => { modalContent.innerHTML = "<p style='color:red'>Error loading booking details.</p>"; });
                });
            });

            spanClose.onclick = () => modal.style.display = "none";

            // ── Embed all trip bookings with destinations for export ──
            const tripBookingsData = <?= json_encode(array_map(function($b) {
                return [
                    'booking_id'      => $b['booking_id'],
                    'user_name'       => $b['user_name'],
                    'status'          => $b['status'],
                    'destination'     => $b['destination'],
                    'number_of_people'=> $b['number_of_people'],
                    'number_of_days'  => $b['number_of_days'],
                    'start_date'      => $b['start_date'],
                    'budget_lkr'      => $b['budget_lkr'],
                    'created_at'      => $b['created_at'],
                ];
            }, $bookingsWithDestinations ?? []), JSON_UNESCAPED_UNICODE) ?>;

            const pkgBookingsData = <?= json_encode(array_values(array_map(function($pb) {
                return [
                    'id'               => $pb['id'],
                    'fullname'         => $pb['fullname'],
                    'email'            => $pb['email'],
                    'phone'            => $pb['phone'],
                    'package_name'     => $pb['package_name'],
                    'package_id'       => $pb['package_id'],
                    'travel_date'      => $pb['travel_date'],
                    'travelers'        => $pb['travelers'],
                    'adults'           => $pb['adults'],
                    'children'         => $pb['children'],
                    'infants'          => $pb['infants'],
                    'total_amount'     => $pb['total_amount'],
                    'status'           => $pb['status'],
                    'special_requests' => $pb['special_requests'] ?? '',
                    'admin_notes'      => $pb['admin_notes'] ?? '',
                    'approved_at'      => $pb['approved_at'] ?? '',
                    'created_at'       => $pb['created_at'],
                ];
            }, $filteredPkgBookings)), JSON_UNESCAPED_UNICODE) ?>;

            // ── Package Booking: view details
            document.querySelectorAll(".pkg-view-btn").forEach(btn => {
                btn.addEventListener("click", function() {
                    const id = parseInt(this.dataset.id);
                    const pb = pkgBookingsData.find(p => p.id === id);
                    if (!pb) return;

                    let html = '';
                    html += `<p><strong>Booking ID:</strong> #${pb.id}</p>`;
                    html += `<p><strong>User:</strong> ${pb.fullname}</p>`;
                    html += `<p><strong>Status:</strong> ${pb.status.charAt(0).toUpperCase() + pb.status.slice(1)}</p>`;
                    html += `<p><strong>Submitted:</strong> ${pb.created_at}</p>`;
                    html += `<h4>Package Details</h4>`;
                    html += `<table>
                                <tr><th>Field</th><th>Details</th></tr>
                                <tr><td>Package</td><td>${pb.package_name}</td></tr>
                                <tr><td>Email</td><td>${pb.email}</td></tr>
                                <tr><td>Phone</td><td>${pb.phone}</td></tr>
                                <tr><td>Travel Date</td><td>${pb.travel_date}</td></tr>
                                <tr><td>Travelers</td><td>
                                    ${pb.travelers} total
                                    (${pb.adults} Adult${pb.adults != 1 ? 's' : ''}
                                    ${pb.children > 0 ? ' / ' + pb.children + ' Child' + (pb.children != 1 ? 'ren' : '') : ''}
                                    ${pb.infants  > 0 ? ' / ' + pb.infants  + ' Infant' + (pb.infants != 1 ? 's' : '') : ''})
                                </td></tr>
                                <tr><td>Total Amount</td><td>LKR ${Number(pb.total_amount).toLocaleString()}</td></tr>
                                ${pb.special_requests ? `<tr><td>Special Requests</td><td>${pb.special_requests}</td></tr>` : ''}
                                ${pb.admin_notes      ? `<tr><td>Admin Notes</td><td>${pb.admin_notes}</td></tr>` : ''}
                                ${pb.approved_at      ? `<tr><td>Approved At</td><td>${pb.approved_at}</td></tr>` : ''}
                            </table>`;

                    modalContent.innerHTML = html;
                    modal.style.display = "block";
                });
            });

            // ── Package Booking: approve
            document.querySelectorAll(".pkg-approve-btn").forEach(btn => {
                btn.addEventListener("click", function() {
                    const id = parseInt(this.dataset.id);
                    if (!confirm("Approve this package booking?")) return;

                    fetch('/CeylonGo/public/admin/package-booking/status', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `id=${id}&status=approved`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) location.reload();
                        else alert("Failed to approve: " + (data.message || 'Unknown error'));
                    })
                    .catch(() => alert("Server error. Please try again."));
                });
            });

            // Package Booking: reject with notes modal
            const rejectModal      = document.getElementById("rejectModal");
            const rejectClose      = rejectModal.querySelector(".reject-close");
            const rejectCancelBtn  = document.getElementById("rejectCancelBtn");
            const rejectConfirmBtn = document.getElementById("rejectConfirmBtn");
            let pendingRejectId    = null;

            document.querySelectorAll(".pkg-reject-btn").forEach(btn => {
                btn.addEventListener("click", function() {
                    pendingRejectId = parseInt(this.dataset.id);
                    document.getElementById("rejectNotes").value = '';
                    rejectModal.style.display = "block";
                });
            });

            rejectClose.onclick     = () => { rejectModal.style.display = "none"; pendingRejectId = null; };
            rejectCancelBtn.onclick = () => { rejectModal.style.display = "none"; pendingRejectId = null; };

            rejectConfirmBtn.addEventListener("click", function() {
                if (!pendingRejectId) return;
                const notes = document.getElementById("rejectNotes").value.trim();

                fetch('/CeylonGo/public/admin/package-booking/status', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `id=${pendingRejectId}&status=rejected&admin_notes=${encodeURIComponent(notes)}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) location.reload();
                    else alert("Failed to reject: " + (data.message || 'Unknown error'));
                })
                .catch(() => alert("Server error. Please try again."));
            });

            // ── Close all modals on outside click ─────────────────
            window.onclick = e => {
                if (e.target == modal)       modal.style.display       = "none";
                if (e.target == rejectModal) rejectModal.style.display = "none";
            };

            // ── LIVE SEARCH: Customized Bookings ─────────────────────
            function applyTripSearch() {
                const term = document.getElementById("tripSearchInput").value.toLowerCase();
                let visible = 0;

                allBookingRows.forEach(row => {
                    if (row.id === "tripNoResultsRow") return;
                    const match = row.innerText.toLowerCase().includes(term);
                    row.style.display = match ? "" : "none";
                    if (match) visible++;
                });

                const existing = document.getElementById("tripNoResultsRow");
                if (existing) existing.remove();

                if (visible === 0) {
                    const noRow = document.createElement("tr");
                    noRow.id = "tripNoResultsRow";
                    noRow.innerHTML = `<td colspan="9" style="text-align:center; padding:20px; color:#888;">No bookings found.</td>`;
                    document.getElementById("bookingsTableBody").appendChild(noRow);
                }
            }

            document.getElementById("tripSearchInput").addEventListener("input", applyTripSearch);
            document.getElementById("tripSearchInput").addEventListener("keydown", function(e) {
                if (e.key === "Enter") { e.preventDefault(); applyTripSearch(); }
            });

            // ── LIVE SEARCH: Package Bookings ────────────────────────
            function applyPkgSearch() {
                const term = document.getElementById("pkgSearchInput").value.toLowerCase();
                let visible = 0;

                allPkgRows.forEach(row => {
                    if (row.id === "pkgNoResultsRow") return;
                    const match = row.innerText.toLowerCase().includes(term);
                    row.style.display = match ? "" : "none";
                    if (match) visible++;
                });

                const existing = document.getElementById("pkgNoResultsRow");
                if (existing) existing.remove();

                if (visible === 0) {
                    const noRow = document.createElement("tr");
                    noRow.id = "pkgNoResultsRow";
                    noRow.innerHTML = `<td colspan="6" style="text-align:center; padding:20px; color:#888;">No package bookings found.</td>`;
                    document.getElementById("pkgBookingsTableBody").appendChild(noRow);
                }
            }

            document.getElementById("pkgSearchInput").addEventListener("input", applyPkgSearch);
            document.getElementById("pkgSearchInput").addEventListener("keydown", function(e) {
                if (e.key === "Enter") { e.preventDefault(); applyPkgSearch(); }
            });

            // ── PAGINATION FOR CUSTOMIZED BOOKINGS ───────────────────

            // Get all rows initially rendered by PHP
            const allBookingRows = Array.from(document.querySelectorAll("#bookingsTableBody tr"));
            const rowsPerPageSelect = document.getElementById("rowsPerPage");
            const paginationControls = document.getElementById("paginationControls");

            let currentPage = 1;
            let rowsPerPage = parseInt(rowsPerPageSelect.value);

            // Render table based on page
            function renderTable() {
                const tbody = document.getElementById("bookingsTableBody");
                tbody.innerHTML = "";

                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                const pageRows = allBookingRows.slice(start, end);

                pageRows.forEach(row => tbody.appendChild(row));

                renderPagination();
            }

            // Pagination buttons
            function renderPagination() {
                const totalPages = Math.ceil(allBookingRows.length / rowsPerPage);

                paginationControls.innerHTML = `
                    <button class="filter-btn small-btn" ${currentPage === 1 ? "disabled" : ""} onclick="prevPage()">Prev</button>

                    <span class="page-info">
                        Page ${currentPage} of ${totalPages}
                    </span>

                    <button class="filter-btn small-btn" ${currentPage === totalPages ? "disabled" : ""} onclick="nextPage()">Next</button>
                `;
            }

            // Navigation
            function nextPage() {
                const totalPages = Math.ceil(allBookingRows.length / rowsPerPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    renderTable();
                }
            }

            function prevPage() {
                if (currentPage > 1) {
                    currentPage--;
                    renderTable();
                }
            }

            // Change rows per page
            rowsPerPageSelect.addEventListener("change", function() {
                rowsPerPage = parseInt(this.value);
                currentPage = 1;
                renderTable();
            });

            // Initialize
            renderTable();

            // ── PAGINATION FOR PACKAGE BOOKINGS ───────────────────

            // Get all rows
            const allPkgRows = Array.from(document.querySelectorAll("#pkgBookingsTableBody tr"));
            const pkgRowsPerPageSelect = document.getElementById("pkgRowsPerPage");
            const pkgPaginationControls = document.getElementById("pkgPaginationControls");

            let pkgCurrentPage = 1;
            let pkgRowsPerPage = parseInt(pkgRowsPerPageSelect.value);

            // Render table
            function renderPkgTable() {
                const tbody = document.getElementById("pkgBookingsTableBody");
                // If only 1 row and it's a "no data" row
                if (allPkgRows.length === 1 && allPkgRows[0].children.length === 1) {
                    tbody.innerHTML = "";
                    tbody.appendChild(allPkgRows[0]); // keep the message
                    pkgPaginationControls.innerHTML = ""; // remove pagination
                    return;
                }
                tbody.innerHTML = "";

                const start = (pkgCurrentPage - 1) * pkgRowsPerPage;
                const end = start + pkgRowsPerPage;

                const pageRows = allPkgRows.slice(start, end);

                pageRows.forEach(row => tbody.appendChild(row));

                renderPkgPagination();
            }

            // Pagination UI
            function renderPkgPagination() {
                const totalPages = Math.ceil(allPkgRows.length / pkgRowsPerPage);

                pkgPaginationControls.innerHTML = `
                    <button class="filter-btn small-btn" ${pkgCurrentPage === 1 ? "disabled" : ""} onclick="pkgPrevPage()">Prev</button>

                    <span class="page-info">
                        Page ${pkgCurrentPage} of ${totalPages}
                    </span>

                    <button class="filter-btn small-btn" ${pkgCurrentPage === totalPages ? "disabled" : ""} onclick="pkgNextPage()">Next</button>
                `;
            }

            // Navigation
            function pkgNextPage() {
                const totalPages = Math.ceil(allPkgRows.length / pkgRowsPerPage);
                if (pkgCurrentPage < totalPages) {
                    pkgCurrentPage++;
                    renderPkgTable();
                }
            }

            function pkgPrevPage() {
                if (pkgCurrentPage > 1) {
                    pkgCurrentPage--;
                    renderPkgTable();
                }
            }

            // Change rows
            pkgRowsPerPageSelect.addEventListener("change", function() {
                pkgRowsPerPage = parseInt(this.value);
                pkgCurrentPage = 1;
                renderPkgTable();
            });

            // Initialize
            renderPkgTable();
        </script>
    </body>
</html>