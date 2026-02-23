<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // $bookings, $stats, $selectedStatus, $searchId, $date — trip bookings (existing)
    // $packageBookings, $pkgStats — package bookings (new)

    // Package bookings: apply search, status filter and date filter server-side
    $pkgSearch         = $_GET['pkg_search'] ?? '';
    $pkgSelectedStatus = $_GET['pkg_status'] ?? 'all';
    $pkgDate           = $_GET['pkg_date']   ?? '';

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
?>

<!DOCTYPE html>
    <html lang="en">
        <head>
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
                    <li class="active"><a href="/CeylonGo/public/admin/bookings"><i class="fa-regular fa-calendar"></i> Bookings</a></li>
                    <li><a href="/CeylonGo/public/admin/service"><i class="fa-solid fa-van-shuttle"></i> Service Providers</a></li>
                    <li><a href="/CeylonGo/public/admin/payments"><i class="fa-solid fa-credit-card"></i> Payments</a></li>
                    <li><a href="/CeylonGo/public/admin/inquiries"><i class="fa-solid fa-circle-question"></i> Inquiries</a></li>
                    <li><a href="/CeylonGo/public/admin/packages"><i class="fa-solid fa-box-open"></i> Packages</a></li>
                    <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-solid fa-star"></i> Reviews</a></li>
                    <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports & Analysis</a></li>
                </ul>
            </div>

            <div class="main-content">
                <div class="booking-management">
                    <h2 class="page-title">Booking Management</h2>
                    <br>

                    <form method="GET" action="/CeylonGo/public/admin/bookings">
                        <input type="hidden" name="pkg_search" value="<?= htmlspecialchars($pkgSearch) ?>">
                        <input type="hidden" name="pkg_status" value="<?= htmlspecialchars($pkgSelectedStatus) ?>">
                        <input type="hidden" name="pkg_date"   value="<?= htmlspecialchars($pkgDate) ?>">
                        <div class="toolbar">
                            <div class="search-section">
                                <input type="text" name="search" placeholder="Search by booking ID" class="search-input" value="<?= htmlspecialchars($searchId ?? '') ?>">
                                <button type="submit" class="search-btn">🔍</button>
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
                        <h4>Trip Booking Statistics</h4><br>
                        <div class="stats-grid">
                            <?php
                                $keys = ['total','pending','completed','cancelled'];
                                foreach($keys as $k):
                                    $val = $stats[$k] ?? 0;
                                    echo "<div class='stat-box'><strong>" . ucfirst($k) . "</strong><br><span>{$val}</span></div>";
                                endforeach;
                            ?>
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
                                    <th>Submitted Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="bookingsTableBody">
                                <?php foreach($bookings as $b):
                                    switch (strtolower($b['status'])) {
                                        case 'pending':   $statusClass = 'pending';   break;
                                        case 'completed': $statusClass = 'completed'; break;
                                        case 'cancelled': $statusClass = 'cancelled'; break;
                                        default:          $statusClass = '';
                                    }
                                ?>
                                <tr>
                                    <td><?= $b['booking_id'] ?></td>
                                    <td><?= htmlspecialchars($b['user_name']) ?></td>
                                    <td><span class="status <?= $statusClass ?>"><?= ucfirst($b['status']) ?></span></td>
                                    <td><?= date('Y-m-d', strtotime($b['created_at'])) ?></td>
                                    <td>
                                        <button class="icon-btn view-btn" data-booking-id="<?= $b['booking_id'] ?>" title="View Details">👁️</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="footer-buttons">
                        <button class="footer-btn black" id="exportBtn">Export Details</button>
                    </div>

                    <br><br>

                    <h3 class="page-title" style="font-size:18px;">Package Booking Requests</h3>
                    <br>

                    <form method="GET" action="/CeylonGo/public/admin/bookings">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($searchId ?? '') ?>">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($selectedStatus ?? 'all') ?>">
                        <input type="hidden" name="date"   value="<?= htmlspecialchars($date ?? '') ?>">
                        <div class="toolbar">
                            <div class="search-section">
                                <input type="text" name="pkg_search" placeholder="Search by ID, name or package" class="search-input" value="<?= htmlspecialchars($pkgSearch) ?>">
                                <button type="submit" class="search-btn">🔍</button>
                            </div>
                            <div class="filter-buttons">
                                <?php
                                    $pkgStatuses = ['all','pending','approved','rejected'];
                                    foreach($pkgStatuses as $s):
                                        $active = $pkgSelectedStatus === $s ? 'active' : '';
                                        echo "<button type='submit' name='pkg_status' value='{$s}' class='filter-btn {$active}'>" . ucfirst($s) . "</button>";
                                    endforeach;
                                ?>
                            </div>
                            <div class="date-filter">
                                <input type="date" name="pkg_date" class="date-input" value="<?= htmlspecialchars($pkgDate) ?>" onchange="this.form.submit()">
                            </div>
                        </div>
                    </form>

                    <div class="stats-section">
                        <h4>Package Booking Statistics</h4><br>
                        <div class="stats-grid">
                            <?php
                                $pkgKeys = ['total','pending','approved','rejected'];
                                foreach($pkgKeys as $k):
                                    $val = $pkgStats[$k] ?? 0;
                                    echo "<div class='stat-box'><strong>" . ucfirst($k) . "</strong><br><span>{$val}</span></div>";
                                endforeach;
                            ?>
                        </div>
                    </div>
                    <br>

                    <div class="bookings-section">
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
                                    <tr><td colspan="6" style="text-align:center;">No package booking requests found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($filteredPkgBookings as $pb):
                                        switch (strtolower($pb['status'])) {
                                            case 'pending':   $sc = 'pending';   break;
                                            case 'approved':  $sc = 'approved';  break;
                                            case 'rejected':  $sc = 'rejected';  break;
                                            case 'cancelled': $sc = 'rejected';  break;
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

                    <div class="footer-buttons">
                        <button class="footer-btn black" id="exportPkgBtn">Export Package Bookings</button>
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

            <!-- Reject Reason Modal (package bookings) -->
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
        </div><!-- /page-wrapper -->

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
                        let html = `<p><strong>Booking ID:</strong> ${b.booking_id}</p>`;
                        html += `<p><strong>User:</strong> ${b.user_name}</p>`;
                        html += `<p><strong>Status:</strong> ${b.status}</p>`;
                        html += `<p><strong>Submitted Date:</strong> ${b.created_at}</p>`;
                        html += `<h4>Destinations</h4><table>
                                    <tr><th>Destination</th><th>People</th><th>Days</th><th>Hotel</th><th>Transport</th></tr>`;
                        data.destinations.forEach(d => {
                            html += `<tr><td>${d.destination}</td><td>${d.people_count}</td><td>${d.days}</td><td>${d.hotel}</td><td>${d.transport}</td></tr>`;
                        });
                        html += "</table>";
                        modalContent.innerHTML = html;
                    })
                    .catch(() => { modalContent.innerHTML = "<p style='color:red'>Error loading booking details.</p>"; });
                });
            });

            spanClose.onclick = () => modal.style.display = "none";

            // ── Embed all trip bookings with destinations for export ──
            const tripBookingsData = <?= json_encode(array_map(function($b) {
                return [
                    'booking_id'    => $b['booking_id'],
                    'user_name'     => $b['user_name'],
                    'status'        => $b['status'],
                    'created_at'    => $b['created_at'],
                    'destinations'  => $b['destinations'] ?? [],
                ];
            }, $bookingsWithDestinations ?? []), JSON_UNESCAPED_UNICODE) ?>;

            // ── Export trip bookings — full structured report ──────
            document.getElementById("exportBtn").addEventListener("click", () => {
                if (!tripBookingsData || tripBookingsData.length === 0) {
                    alert("No bookings to export!");
                    return;
                }

                const sep    = '='.repeat(70);
                const subSep = '-'.repeat(70);
                const now    = new Date();
                const dateStr = now.toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' });
                const timeStr = now.toLocaleTimeString('en-GB');

                let report = '';
                report += '='.repeat(70) + '\n';
                report += '        CEYLON GO — TRIP BOOKINGS REPORT\n';
                report += '='.repeat(70) + '\n';
                report += '  Generated on   : ' + dateStr + ' at ' + timeStr + '\n';
                report += '  Total Bookings : ' + tripBookingsData.length + '\n';
                report += '='.repeat(70) + '\n\n';

                tripBookingsData.forEach(function(b, index) {
                    report += 'BOOKING ' + (index + 1) + ' OF ' + tripBookingsData.length + '\n';
                    report += sep + '\n';

                    // ── Booking summary ──
                    report += '  BOOKING DETAILS\n';
                    report += '  ' + subSep + '\n';
                    report += '  Booking ID   : ' + b.booking_id + '\n';
                    report += '  Customer     : ' + b.user_name + '\n';
                    report += '  Status       : ' + b.status.charAt(0).toUpperCase() + b.status.slice(1) + '\n';
                    report += '  Submitted On : ' + b.created_at + '\n\n';

                    // ── Destinations ──
                    if (b.destinations && b.destinations.length > 0) {
                        report += '  DESTINATIONS (' + b.destinations.length + ')\n';
                        report += '  ' + subSep + '\n';
                        b.destinations.forEach(function(d, di) {
                            report += '  Destination ' + (di + 1) + '\n';
                            report += '    Location   : ' + d.destination + '\n';
                            report += '    People     : ' + d.people_count + '\n';
                            report += '    Days       : ' + d.days + '\n';
                            if (d.hotel && d.hotel.trim() !== '')
                                report += '    Hotel      : ' + d.hotel + '\n';
                            report += '    Transport  : ' + d.transport + '\n';
                        });
                    } else {
                        report += '  DESTINATIONS\n';
                        report += '  ' + subSep + '\n';
                        report += '  No destination details recorded.\n';
                    }

                    report += '\n' + sep + '\n\n';
                });

                report += '='.repeat(70) + '\n';
                report += '  END OF REPORT\n';
                report += '  Ceylon Go Admin  |  ' + dateStr + '\n';
                report += '='.repeat(70) + '\n';

                const blob = new Blob([report], { type: 'text/plain' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'ceylongo_trip_bookings_' + now.toISOString().slice(0, 10) + '.txt';
                link.click();
            });

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

            // ── Package Booking: view details (reuses shared modal) ──
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

            // ── Package Booking: approve ──────────────────────────
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

            // ── Package Booking: reject with notes modal ──────────
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

            // ── Export package bookings report ────────────────────
            document.getElementById("exportPkgBtn").addEventListener("click", function() {
                if (!pkgBookingsData || pkgBookingsData.length === 0) {
                    alert("No package bookings to export!");
                    return;
                }

                const sep    = '='.repeat(70);
                const subSep = '-'.repeat(70);
                const now    = new Date();
                const dateStr = now.toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' });
                const timeStr = now.toLocaleTimeString('en-GB');

                let report = '';
                report += '='.repeat(70) + '\n';
                report += '      CEYLON GO — PACKAGE BOOKINGS REPORT\n';
                report += '='.repeat(70) + '\n';
                report += '  Generated on    : ' + dateStr + ' at ' + timeStr + '\n';
                report += '  Total Bookings  : ' + pkgBookingsData.length + '\n';
                report += '='.repeat(70) + '\n\n';

                pkgBookingsData.forEach(function(pb, index) {
                    report += 'BOOKING ' + (index + 1) + ' OF ' + pkgBookingsData.length + '\n';
                    report += sep + '\n';

                    // ── Customer details ──
                    report += '  CUSTOMER DETAILS\n';
                    report += '  ' + subSep + '\n';
                    report += '  Full Name    : ' + pb.fullname + '\n';
                    report += '  Email        : ' + pb.email + '\n';
                    report += '  Phone        : ' + pb.phone + '\n\n';

                    // ── Booking details ──
                    report += '  BOOKING DETAILS\n';
                    report += '  ' + subSep + '\n';
                    report += '  Booking ID   : #' + pb.id + '\n';
                    report += '  Package      : ' + pb.package_name + '\n';
                    report += '  Travel Date  : ' + pb.travel_date + '\n';
                    report += '  Status       : ' + pb.status.charAt(0).toUpperCase() + pb.status.slice(1) + '\n';
                    report += '  Submitted On : ' + pb.created_at + '\n\n';

                    // ── Traveler breakdown ──
                    report += '  TRAVELERS\n';
                    report += '  ' + subSep + '\n';
                    report += '  Total        : ' + pb.travelers + '\n';
                    report += '  Adults       : ' + pb.adults + '\n';
                    if (pb.children > 0) report += '  Children     : ' + pb.children + '\n';
                    if (pb.infants  > 0) report += '  Infants      : ' + pb.infants  + '\n';
                    report += '\n';

                    // ── Payment ──
                    report += '  PAYMENT\n';
                    report += '  ' + subSep + '\n';
                    report += '  Total Amount : LKR ' + Number(pb.total_amount).toLocaleString() + '\n\n';

                    // ── Extra info if present ──
                    if (pb.special_requests) {
                        report += '  SPECIAL REQUESTS\n';
                        report += '  ' + subSep + '\n';
                        report += '  ' + pb.special_requests + '\n\n';
                    }
                    if (pb.admin_notes) {
                        report += '  ADMIN NOTES\n';
                        report += '  ' + subSep + '\n';
                        report += '  ' + pb.admin_notes + '\n\n';
                    }
                    if (pb.approved_at) {
                        report += '  Approved At  : ' + pb.approved_at + '\n\n';
                    }

                    report += sep + '\n\n';
                });

                report += '='.repeat(70) + '\n';
                report += '  END OF REPORT\n';
                report += '  Ceylon Go Admin  |  ' + dateStr + '\n';
                report += '='.repeat(70) + '\n';

                const blob = new Blob([report], { type: 'text/plain' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'package_bookings_' + now.toISOString().slice(0, 10) + '.txt';
                link.click();
            });

            // ── Close all modals on outside click ─────────────────
            window.onclick = e => {
                if (e.target == modal)       modal.style.display       = "none";
                if (e.target == rejectModal) rejectModal.style.display = "none";
            };
        </script>
    </body>
</html>