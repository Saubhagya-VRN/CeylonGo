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
                    <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-regular fa-star"></i> Reviews</a></li>
                    <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports & Analysis</a></li>
                </ul>
            </div>

            <div class="main-content">
                <div class="booking-management">
                    <h2 class="page-title">Booking Management</h2>

                    <h4 class="page-title" style="font-size:16px;">Customized Booking Requests</h4>

                    <form method="GET" action="/CeylonGo/public/admin/bookings">
                        <input type="hidden" name="pkg_search" value="<?= htmlspecialchars($pkgSearch) ?>">
                        <input type="hidden" name="pkg_status" value="<?= htmlspecialchars($pkgSelectedStatus) ?>">
                        <input type="hidden" name="pkg_date"   value="<?= htmlspecialchars($pkgDate) ?>">
                        <div class="toolbar">
                            <div class="search-section">
                                <input type="text" name="search" placeholder="Search by customer" class="search-input" value="<?= htmlspecialchars($searchId ?? '') ?>">
                                <button type="submit" class="search-btn">🔍</button>
                            </div>
                            <div class="filter-buttons">
                                <?php
                                    $statuses = ['all','pending','confirmed','completed','cancelled'];
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
                            <?php
                                $keys = ['total','pending','confirmed','completed','cancelled'];
                                foreach($keys as $k):
                                    $val = $stats[$k] ?? 0;
                                    echo "<div class='stat-box'><strong>" . ucfirst($k) . "</strong><br><span>{$val}</span></div>";
                                endforeach;
                            ?>
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
                                        <td colspan="8" style="text-align:center;">
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

                    <div class="footer-buttons" style="flex-direction:column;align-items:flex-start;gap:10px;">
                        <div class="export-timeline-toolbar" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                            <label for="exportTimelinePreset">Report period (Customized Bookings):</label>
                            <select id="exportTimelinePreset" class="search-input" style="max-width:220px;padding:6px 8px;">
                                <option value="all">All time</option>
                                <option value="7d">Last 7 days</option>
                                <option value="30d">Last 30 days</option>
                                <option value="90d">Last 90 days</option>
                                <option value="ytd">Year to date</option>
                                <option value="custom">Custom range</option>
                            </select>
                            <span id="exportCustomRangeWrap" class="export-custom-date-range" style="display:none;align-items:center;gap:8px;flex-wrap:wrap;">
                                <span class="export-range-label">From</span>
                                <div class="date-filter"><input type="date" id="exportDateFrom" class="date-input"></div>
                                <span class="export-range-label">To</span>
                                <div class="date-filter"><input type="date" id="exportDateTo" class="date-input"></div>
                            </span>
                        </div>
                        <button type="button" class="footer-btn black" id="exportBtn">Download PDF report</button>
                    </div>

                    <br><br>

                    <h4 class="page-title" style="font-size:16px;">Package Booking Requests</h4>

                    <form method="GET" action="/CeylonGo/public/admin/bookings">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($searchId ?? '') ?>">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($selectedStatus ?? 'all') ?>">
                        <input type="hidden" name="date"   value="<?= htmlspecialchars($date ?? '') ?>">
                        <div class="toolbar">
                            <div class="search-section">
                                <input type="text" name="pkg_search" placeholder="Search by customer or package" class="search-input" value="<?= htmlspecialchars($pkgSearch) ?>">
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

                    <div class="footer-buttons" style="flex-direction:column;align-items:flex-start;gap:10px;">
                        <div class="export-timeline-toolbar" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                            <label for="exportTimelinePreset">Report period (Package Bookings):</label>
                            <select id="exportTimelinePreset" class="search-input" style="max-width:220px;padding:6px 8px;">
                                <option value="all">All time</option>
                                <option value="7d">Last 7 days</option>
                                <option value="30d">Last 30 days</option>
                                <option value="90d">Last 90 days</option>
                                <option value="ytd">Year to date</option>
                                <option value="custom">Custom range</option>
                            </select>
                            <span id="exportCustomRangeWrap" class="export-custom-date-range" style="display:none;align-items:center;gap:8px;flex-wrap:wrap;">
                                <span class="export-range-label">From</span>
                                <div class="date-filter"><input type="date" id="exportDateFrom" class="date-input"></div>
                                <span class="export-range-label">To</span>
                                <div class="date-filter"><input type="date" id="exportDateTo" class="date-input"></div>
                            </span>
                        </div>
                        <button type="button" class="footer-btn black" id="exportPkgBtn">Download PDF report</button>
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

        <script src="/CeylonGo/public/js/admin_report_pdf_export.js"></script>
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

            // ── Export date range helpers (trip + package) ─────────
            (function() {
                const presetEl = document.getElementById("exportTimelinePreset");
                const wrap = document.getElementById("exportCustomRangeWrap");
                function pad(n) { return String(n).padStart(2, "0"); }
                function ymd(d) { return d.getFullYear() + "-" + pad(d.getMonth() + 1) + "-" + pad(d.getDate()); }
                function toggleCustom() {
                    if (!presetEl || !wrap) return;
                    wrap.style.display = presetEl.value === "custom" ? "inline-flex" : "none";
                }
                if (presetEl) { presetEl.addEventListener("change", toggleCustom); toggleCustom(); }
            })();

            function resolveBookingsExportRange() {
                const presetEl = document.getElementById("exportTimelinePreset");
                const v = presetEl ? presetEl.value : "all";
                if (v === "custom") {
                    const f = document.getElementById("exportDateFrom").value;
                    const t = document.getElementById("exportDateTo").value;
                    if (!f || !t) { alert("Please select both From and To dates for a custom range."); return null; }
                    if (f > t) { alert("From date must be before or equal to To date."); return null; }
                    return { start: f, end: t };
                }
                if (v === "all") return { start: null, end: null };
                const today = new Date();
                const end = new Date(today.getFullYear(), today.getMonth(), today.getDate());
                let start = new Date(end);
                if (v === "7d") start.setDate(start.getDate() - 6);
                else if (v === "30d") start.setDate(start.getDate() - 29);
                else if (v === "90d") start.setDate(start.getDate() - 89);
                else if (v === "ytd") start = new Date(today.getFullYear(), 0, 1);
                else return { start: null, end: null };
                return { start: ymd(start), end: ymd(end) };
            }
            function ymd(d) {
                const pad = function(n) { return String(n).padStart(2, "0"); };
                return d.getFullYear() + "-" + pad(d.getMonth() + 1) + "-" + pad(d.getDate());
            }
            function inBookingsDateRange(dateStr, range) {
                if (!range || (!range.start && !range.end)) return true;
                const d = (dateStr && String(dateStr).trim().slice(0, 10)) || "";
                if (!d) return false;
                if (range.start && d < range.start) return false;
                if (range.end && d > range.end) return false;
                return true;
            }
            function bookingsPeriodLabel(range) {
                if (!range || (!range.start && !range.end)) return "All time";
                return range.start + " to " + range.end;
            }

            // ── PDF export (same as Reports & Analysis: filters + summary + detail) ──
            document.getElementById("exportBtn").addEventListener("click", () => {
                const range = resolveBookingsExportRange();
                if (range === null) return;
                window.location.href = window.CeylonGoReportPdf.buildPdfUrl("bookings", range);
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

            document.getElementById("exportPkgBtn").addEventListener("click", function() {
                const range = resolveBookingsExportRange();
                if (range === null) return;
                window.location.href = window.CeylonGoReportPdf.buildPdfUrl("bookings", range);
            });

            // ── Close all modals on outside click ─────────────────
            window.onclick = e => {
                if (e.target == modal)       modal.style.display       = "none";
                if (e.target == rejectModal) rejectModal.style.display = "none";
            };

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