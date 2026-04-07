<?php 
    // Session is already started in public/index.php
    require_once(__DIR__ . '/../../config/config.php');

    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        header("Location: /CeylonGo/public/login");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Font Awesome (REQUIRED) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <!-- Optional admin-only overrides -->
        <link rel="stylesheet" href="/CeylonGO/public/css/admin/reports.css">

        <!-- Shared Transport Layout -->
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/base.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/navbar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/sidebar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/footer.css">

        <!-- Responsive styles (always last) -->
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">

        <title>Reports and Analysis</title>

        <style>
            /* ── Download Modal ─────────────────────────────────── */
            .modal-backdrop {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,.45);
                z-index: 1000;
                align-items: center;
                justify-content: center;
            }
            .modal-backdrop.open { display: flex; }

            .modal-box {
                background: #fff;
                border-radius: 10px;
                padding: 28px 32px;
                width: 360px;
                box-shadow: 0 8px 32px rgba(0,0,0,.2);
            }
            .modal-box h4 { margin-bottom: 16px; font-size: 17px; }

            .modal-check-list { list-style: none; padding: 0; margin-bottom: 20px; }
            .modal-check-list li {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 7px 0;
                border-bottom: 1px solid #f0f0f0;
                font-size: 14px;
            }
            .modal-check-list li:last-child { border-bottom: none; }
            .modal-check-list input[type=checkbox] { width: 16px; height: 16px; cursor: pointer; }

            .modal-actions { display: flex; gap: 10px; justify-content: flex-end; }
            .modal-btn {
                padding: 8px 20px;
                border-radius: 6px;
                border: 1px solid #ccc;
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
            }
            .modal-btn.primary { background: #000; color: #fff; border-color: #000; }
            .modal-btn.secondary { background: #fff; color: #333; }

            /* ── Booking-type filter tabs ──────────────────────── */
            .type-filter-buttons {
                display: flex;
                gap: 10px;
                margin-bottom: 6px;
            }
            .type-btn {
                padding: 6px 14px;
                border: 1px solid #ccc;
                background: #fff;
                border-radius: 6px;
                cursor: pointer;
                font-size: 13px;
            }
            .type-btn.active {
                background: #198754;
                color: #fff;
                border-color: #198754;
            }

            /* Period filter */
            .filter-group { margin-bottom: 6px; }
            .filter-label { font-size: 12px; color: #888; margin-bottom: 4px; }

            /* Revenue stat colour */
            .stat-box.revenue span { color: #198754; }
            .stat-box.cancellations span { color: #dc3545; }
        </style>
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

        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <div class="page-wrapper">

            <!-- Sidebar -->
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
                <li class="active"><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports and Analysis</a></li>
                </ul>
            </div>

            <div class="main-content">
                <div class="reports-management">
                
                    <h2 class="page-title">Reports and Analysis</h2>
                    <br>

                    <!-- ── Period Filter ─────────────────────── -->
                    <div class="filter-group">
                        <div class="filter-label">TIME PERIOD</div>
                        <div class="filter-buttons">
                            <button class="filter-btn <?= ($period === 'daily')   ? 'active' : '' ?>" data-period="daily">Daily</button>
                            <button class="filter-btn <?= ($period === 'weekly')  ? 'active' : '' ?>" data-period="weekly">Weekly</button>
                            <button class="filter-btn <?= ($period === 'monthly') ? 'active' : '' ?>" data-period="monthly">Monthly</button>
                            <button class="filter-btn <?= ($period === 'yearly')  ? 'active' : '' ?>" data-period="yearly">Yearly</button>
                        </div>
                    </div>

                    <!-- ── Booking Type Filter ───────────────── -->
                    <div class="filter-group" style="margin-top:12px;">
                        <div class="filter-label">BOOKING TYPE</div>
                        <div class="type-filter-buttons">
                            <button class="type-btn <?= ($bookingType === 'both')     ? 'active' : '' ?>" data-type="both">All Bookings</button>
                            <button class="type-btn <?= ($bookingType === 'package')  ? 'active' : '' ?>" data-type="package">Package Bookings</button>
                            <button class="type-btn <?= ($bookingType === 'custom')   ? 'active' : '' ?>" data-type="custom">Custom Trips</button>
                        </div>
                    </div>

                    <p class="sub-text" style="margin-top:10px;">Showing data for: <strong><?= ucfirst($period) ?></strong> · <strong><?= $bookingType === 'both' ? 'All Bookings' : ($bookingType === 'package' ? 'Package Bookings' : 'Custom Trips') ?></strong></p>

                    <!-- ── Key Metrics ───────────────────────── -->
                    <div class="stats-section">
                        <h4>Key Metrics</h4><br>
                        <div class="stats-grid">
                            <div class="stat-box">
                                <strong>Total Bookings</strong><br>
                                <span id="statTotalBookings"><?= $totalBookings ?></span>
                            </div>
                            <div class="stat-box revenue">
                                <strong>Total Revenue</strong><br>
                                <span id="statTotalRevenue">LKR <?= number_format($totalRevenue, 2) ?></span>
                            </div>
                            <div class="stat-box cancellations">
                                <strong>Cancellations</strong><br>
                                <span id="statTotalCancellations"><?= $totalCancellations ?></span>
                            </div>
                        </div>
                    </div>
                    <br><br>

                    <!-- ── Bookings Chart ──────────────────────── -->
                    <div class="chart-section" id="section-bookings">
                        <h3 class="section-title">Number of Bookings</h3>
                        <canvas id="bookingsChart" 
                                data-labels='<?= json_encode($labels) ?>' 
                                data-values='<?= json_encode($bookings) ?>'>
                        </canvas>
                    </div>

                    <!-- ── Revenue Chart ───────────────────────── -->
                    <div class="chart-section" id="section-revenue">
                        <h3 class="section-title">Revenue Generated (LKR)</h3>
                        <canvas id="revenueChart" 
                                data-labels='<?= json_encode($labels) ?>' 
                                data-values='<?= json_encode($revenue) ?>'>
                        </canvas>
                    </div>

                    <!-- ── Cancellations Chart ─────────────────── -->
                    <div class="chart-section" id="section-cancellations">
                        <h3 class="section-title">Cancellations / Refunds</h3>
                        <canvas id="cancellationsChart" 
                                data-labels='<?= json_encode($labels) ?>' 
                                data-values='<?= json_encode($cancellations) ?>'>
                        </canvas>
                    </div>

                    <!-- ── Download Buttons ───────────────────── -->
                    <div class="footer-buttons">
                        <button class="footer-btn black" onclick="openDownloadModal('pdf')">
                            <i class="fa-solid fa-file-pdf"></i> Download PDF
                        </button>
                        <button class="footer-btn black" onclick="openDownloadModal('excel')">
                            <i class="fa-solid fa-file-excel"></i> Download Excel
                        </button>
                    </div>
                </div>
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

        <!-- ── Download Modal ─────────────────────────────────── -->
        <div class="modal-backdrop" id="downloadModal">
            <div class="modal-box">
                <h4 id="modalTitle">Select Charts to Download</h4>
                <ul class="modal-check-list">
                    <li>
                        <input type="checkbox" id="chkBookings" checked>
                        <label for="chkBookings"><i class="fa-solid fa-calendar-check" style="color:#0d6efd;width:18px"></i> Bookings Chart</label>
                    </li>
                    <li>
                        <input type="checkbox" id="chkRevenue" checked>
                        <label for="chkRevenue"><i class="fa-solid fa-coins" style="color:#198754;width:18px"></i> Revenue Chart</label>
                    </li>
                    <li>
                        <input type="checkbox" id="chkCancellations" checked>
                        <label for="chkCancellations"><i class="fa-solid fa-circle-xmark" style="color:#dc3545;width:18px"></i> Cancellations Chart</label>
                    </li>
                </ul>
                <div class="modal-actions">
                    <button class="modal-btn secondary" onclick="closeDownloadModal()">Cancel</button>
                    <button class="modal-btn primary" id="confirmDownloadBtn" onclick="confirmDownload()">Download</button>
                </div>
            </div>
        </div>

        <!-- Hidden data for JS -->
        <script>
            const REPORT_DATA = {
                period:      '<?= $period ?>',
                bookingType: '<?= $bookingType ?>',
                labels:      <?= json_encode($labels) ?>,
                bookings:    <?= json_encode($bookings) ?>,
                revenue:     <?= json_encode($revenue) ?>,
                cancellations: <?= json_encode($cancellations) ?>,
                totalBookings:     <?= $totalBookings ?>,
                totalRevenue:      <?= $totalRevenue ?>,
                totalCancellations: <?= $totalCancellations ?>
            };

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

            // ── Download modal helpers ─────────────────────────
            let _downloadFormat = 'pdf';

            function openDownloadModal(format) {
                _downloadFormat = format;
                document.getElementById('modalTitle').textContent =
                    format === 'pdf' ? 'Download PDF — Select Charts' : 'Download Excel — Select Charts';
                document.getElementById('confirmDownloadBtn').textContent =
                    format === 'pdf' ? 'Download PDF' : 'Download Excel';
                document.getElementById('downloadModal').classList.add('open');
            }

            function closeDownloadModal() {
                document.getElementById('downloadModal').classList.remove('open');
            }

            function confirmDownload() {
                const selected = [];
                if (document.getElementById('chkBookings').checked)     selected.push('bookings');
                if (document.getElementById('chkRevenue').checked)       selected.push('revenue');
                if (document.getElementById('chkCancellations').checked) selected.push('cancellations');

                if (selected.length === 0) {
                    alert('Please select at least one chart.');
                    return;
                }

                closeDownloadModal();

                if (_downloadFormat === 'pdf') {
                    downloadChartsAsPDF(selected);
                } else {
                    downloadChartsAsExcel(selected);
                }
            }

            // Close modal on backdrop click
            document.getElementById('downloadModal').addEventListener('click', function(e) {
                if (e.target === this) closeDownloadModal();
            });
        </script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>
        <script src="/CeylonGo/public/js/reports_charts.js"></script>
    </body>
</html>