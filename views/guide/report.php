<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'session_init.php';

// Defaults
if (!isset($kpi)) {
    $kpi = [
        'total_bookings' => 0, 'total_revenue' => 0, 'avg_fee' => 0,
        'approved_count' => 0, 'rejected_count' => 0, 'pending_count' => 0,
        'unique_clients' => 0, 'completion_rate' => 0
    ];
}
if (!isset($monthly)) $monthly = [];
if (!isset($tours)) $tours = [];
if (!isset($start_date)) $start_date = null;
if (!isset($end_date)) $end_date = null;

// Prepare chart data
$chartLabels = array_map(function($m) {
    return date('M Y', strtotime($m['month'] . '-01'));
}, $monthly);
$chartRevenue = array_column($monthly, 'revenue');
$chartBookings = array_column($monthly, 'bookings');
$chartApproved = array_column($monthly, 'approved');
$chartRejected = array_column($monthly, 'rejected');

// Period label
if ($start_date && $end_date) {
    $periodLabel = date('M d, Y', strtotime($start_date)) . ' — ' . date('M d, Y', strtotime($end_date));
} else {
    $periodLabel = 'All Time';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ceylon Go - Guide Performance Report</title>

    <!-- Base layout styles -->
    <link rel="stylesheet" href="/CeylonGo/public/css/guide/base.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/guide/navbar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/guide/sidebar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/guide/footer.css">

    <!-- Component styles -->
    <link rel="stylesheet" href="/CeylonGo/public/css/guide/cards.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/guide/buttons.css">

    <!-- Page-specific -->
    <link rel="stylesheet" href="/CeylonGo/public/css/guide/report.css">

    <!-- Icons & Chart library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>

    <!-- Navbar -->
    <header class="navbar">
        <div class="branding">
            <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
            <img src="/CeylonGo/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
            <div class="logo-text">Ceylon Go</div>
        </div>
        <nav class="nav-links">
            <a href="/CeylonGo/public/guide/dashboard">Home</a>
            <div class="profile-dropdown">
                <img src="<?= htmlspecialchars($profile_picture); ?>" alt="User" class="profile-pic" onclick="toggleProfileDropdown()">
                <div class="profile-dropdown-menu" id="profileDropdown">
                    <a href="/CeylonGo/public/guide/profile"><i class="fa-regular fa-user"></i> My Profile</a>
                    <a href="/CeylonGo/public/logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="page-wrapper">

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <ul>
                <li><a href="/CeylonGo/public/guide/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
                <li><a href="/CeylonGo/public/guide/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Tours</a></li>
                <li><a href="/CeylonGo/public/guide/pending"><i class="fa-regular fa-clock"></i> Pending Requests</a></li>
                <li><a href="/CeylonGo/public/guide/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Tours</a></li>
                <li><a href="/CeylonGo/public/guide/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
                <li><a href="/CeylonGo/public/guide/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
                <li class="active"><a href="/CeylonGo/public/guide/report"><i class="fa-solid fa-chart-line"></i> Performance Report</a></li>
                <li><a href="/CeylonGo/public/guide/places"><i class="fa-solid fa-map-location-dot"></i> My Places</a></li>
                <li><a href="/CeylonGo/public/guide/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="reportContent">

            <!-- Page Header -->
            <div class="report-page-header">
                <div class="header-left">
                    <h2><i class="fa-solid fa-chart-pie"></i> Performance Report</h2>
                    <p>Comprehensive overview of your guiding services, earnings &amp; tour history</p>
                    <div class="report-period">
                        <i class="fa-regular fa-calendar"></i>
                        <?= htmlspecialchars($periodLabel) ?>
                    </div>
                </div>
                <div class="report-actions">
                    <button class="btn-report primary" onclick="window.print()">
                        <i class="fa-solid fa-print"></i> Print
                    </button>
                    <button class="btn-report dark" onclick="downloadPDF()">
                        <i class="fa-solid fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </div>

            <!-- Date Filter Bar -->
            <form class="filter-bar" method="GET" action="/CeylonGo/public/guide/report" id="filterForm">
                <div class="filter-group">
                    <label>Start Date</label>
                    <input type="date" name="start_date" id="startDate" value="<?= htmlspecialchars($start_date ?? '') ?>">
                </div>
                <div class="filter-group">
                    <label>End Date</label>
                    <input type="date" name="end_date" id="endDate" value="<?= htmlspecialchars($end_date ?? '') ?>">
                </div>
                <button type="submit" class="btn-apply">
                    <i class="fa-solid fa-filter"></i> Apply Filter
                </button>
                <button type="button" class="btn-reset" onclick="resetFilters()">
                    <i class="fa-solid fa-rotate-left"></i>
                </button>
                <div class="quick-filters">
                    <button type="button" class="quick-filter-btn" data-range="7">7 Days</button>
                    <button type="button" class="quick-filter-btn" data-range="30">30 Days</button>
                    <button type="button" class="quick-filter-btn" data-range="90">3 Months</button>
                    <button type="button" class="quick-filter-btn" data-range="180">6 Months</button>
                    <button type="button" class="quick-filter-btn" data-range="365">1 Year</button>
                </div>
            </form>

            <!-- KPI Cards -->
            <div class="kpi-grid">
                <div class="kpi-card revenue">
                    <div class="kpi-icon">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                    <div class="kpi-content">
                        <h4>Total Revenue</h4>
                        <p class="kpi-value">Rs. <?= number_format($kpi['total_revenue'], 2) ?></p>
                    </div>
                </div>

                <div class="kpi-card completion">
                    <div class="kpi-icon">
                        <i class="fa-solid fa-check-double"></i>
                    </div>
                    <div class="kpi-content">
                        <h4>Approval Rate</h4>
                        <p class="kpi-value"><?= $kpi['completion_rate'] ?>%</p>
                    </div>
                </div>

                <div class="kpi-card clients">
                    <div class="kpi-icon">
                        <i class="fa-solid fa-user-group"></i>
                    </div>
                    <div class="kpi-content">
                        <h4>Unique Clients</h4>
                        <p class="kpi-value"><?= number_format($kpi['unique_clients']) ?></p>
                    </div>
                </div>

                <div class="kpi-card tours">
                    <div class="kpi-icon">
                        <i class="fa-solid fa-compass"></i>
                    </div>
                    <div class="kpi-content">
                        <h4>Approved Tours</h4>
                        <p class="kpi-value"><?= number_format($kpi['approved_count']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-row">
                <!-- Revenue Trend Chart -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="fa-solid fa-chart-area"></i> Revenue Trend</h3>
                    </div>
                    <div class="chart-canvas-wrap">
                        <canvas id="revenueChart" height="90"></canvas>
                    </div>
                </div>

                <!-- Booking Status Breakdown -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="fa-solid fa-chart-pie"></i> Request Status</h3>
                    </div>
                    <div class="chart-canvas-wrap" style="max-width: 280px; margin: 0 auto;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tour Summary Table -->
            <div class="report-table-section">
                <div class="report-table-header">
                    <h3><i class="fa-solid fa-list-check"></i> Tour Summary</h3>
                    <div class="table-search">
                        <i class="fa-solid fa-search"></i>
                        <input type="text" id="tourSearch" placeholder="Search tours...">
                    </div>
                </div>

                <div class="table-scroll">
                    <table class="report-table" id="tourTable">
                        <thead>
                            <tr>
                                <th>Tour ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Location</th>
                                <th>Language</th>
                                <th>Fee</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="tourTableBody">
                            <?php if (empty($tours)): ?>
                                <tr class="no-data-row">
                                    <td colspan="8">
                                        <i class="fa-regular fa-folder-open"></i>
                                        No tour records found for the selected period.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($tours as $tour): ?>
                                    <tr data-status="<?= htmlspecialchars($tour['status']) ?>">
                                        <td class="tour-id">#GR<?= str_pad($tour['id'], 3, '0', STR_PAD_LEFT) ?></td>
                                        <td><?= htmlspecialchars($tour['customerName']) ?></td>
                                        <td><?= date('M d, Y', strtotime($tour['date'])) ?></td>
                                        <td><?= date('h:i A', strtotime($tour['time'])) ?></td>
                                        <td><?= htmlspecialchars($tour['location']) ?></td>
                                        <td><?= htmlspecialchars($tour['language']) ?></td>
                                        <td class="fee-cell">Rs. <?= number_format($tour['fee'] ?? 0, 2) ?></td>
                                        <td>
                                            <?php
                                            $statusIcon = match($tour['status']) {
                                                'approved' => 'fa-circle-check',
                                                'pending' => 'fa-clock',
                                                'rejected' => 'fa-circle-xmark',
                                                default => 'fa-circle-question'
                                            };
                                            ?>
                                            <span class="status-pill <?= htmlspecialchars($tour['status']) ?>">
                                                <i class="fa-solid <?= $statusIcon ?>"></i>
                                                <?= ucfirst(htmlspecialchars($tour['status'])) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!empty($tours)): ?>
                <div class="table-pagination" id="tablePagination">
                    <span class="page-info" id="pageInfo">Showing 1-10 of <?= count($tours) ?> tours</span>
                    <div class="page-buttons" id="pageButtons"></div>
                </div>
                <?php endif; ?>
            </div>

        </div><!-- /main-content -->
    </div><!-- /page-wrapper -->

    <!-- Footer -->
    <footer>
        <ul>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Contact Us</a></li>
        </ul>
    </footer>

    <script>
    // ========================
    // CHART.JS — Revenue Trend
    // ========================
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueGradient = revenueCtx.createLinearGradient(0, 0, 0, 350);
    revenueGradient.addColorStop(0, 'rgba(61, 139, 64, 0.25)');
    revenueGradient.addColorStop(1, 'rgba(61, 139, 64, 0.02)');

    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [
                {
                    label: 'Revenue (LKR)',
                    data: <?= json_encode($chartRevenue) ?>,
                    borderColor: '#3d8b40',
                    backgroundColor: revenueGradient,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#3d8b40',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8
                },
                {
                    label: 'Bookings',
                    data: <?= json_encode($chartBookings) ?>,
                    borderColor: '#f39c12',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    borderDash: [6, 4],
                    tension: 0.4,
                    pointBackgroundColor: '#f39c12',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    yAxisID: 'yBookings'
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'top',
                    labels: { usePointStyle: true, padding: 20, font: { size: 13 } }
                },
                tooltip: {
                    backgroundColor: '#1a1a2e',
                    titleFont: { size: 14 },
                    bodyFont: { size: 13 },
                    cornerRadius: 10,
                    padding: 14,
                    callbacks: {
                        label: function(ctx) {
                            if (ctx.datasetIndex === 0) return 'Revenue: Rs. ' + ctx.parsed.y.toLocaleString();
                            return 'Bookings: ' + ctx.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: {
                        callback: val => 'Rs. ' + val.toLocaleString(),
                        font: { size: 12 }
                    }
                },
                yBookings: {
                    position: 'right',
                    beginAtZero: true,
                    grid: { drawOnChartArea: false },
                    ticks: { font: { size: 12 }, stepSize: 1 },
                    title: { display: true, text: 'Bookings', font: { size: 12 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 12 } }
                }
            }
        }
    });

    // ========================
    // CHART.JS — Status Donut
    // ========================
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Approved', 'Pending', 'Rejected'],
            datasets: [{
                data: [
                    <?= (int)$kpi['approved_count'] ?>,
                    <?= (int)$kpi['pending_count'] ?>,
                    <?= (int)$kpi['rejected_count'] ?>
                ],
                backgroundColor: ['#3d8b40', '#ffc107', '#dc3545'],
                borderColor: '#fff',
                borderWidth: 3,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, padding: 16, font: { size: 13 } }
                },
                tooltip: {
                    backgroundColor: '#1a1a2e',
                    cornerRadius: 10,
                    padding: 14
                }
            }
        }
    });

    // ========================
    // DATE FILTER LOGIC
    // ========================
    document.querySelectorAll('.quick-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const days = parseInt(this.dataset.range);
            const start = new Date();
            const end = new Date();
            start.setDate(start.getDate() - days);
            end.setDate(end.getDate() + days);

            document.getElementById('startDate').value = formatDate(start);
            document.getElementById('endDate').value = formatDate(end);

            document.querySelectorAll('.quick-filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            document.getElementById('filterForm').submit();
        });
    });

    function formatDate(d) {
        return d.getFullYear() + '-' +
               String(d.getMonth() + 1).padStart(2, '0') + '-' +
               String(d.getDate()).padStart(2, '0');
    }

    function resetFilters() {
        window.location.href = '/CeylonGo/public/guide/report';
    }

    // ========================
    // TABLE SEARCH
    // ========================
    const searchInput = document.getElementById('tourSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tourTableBody tr:not(.no-data-row)');
            rows.forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
            });
        });
    }

    // ========================
    // TABLE PAGINATION
    // ========================
    (function() {
        const rowsPerPage = 10;
        const table = document.getElementById('tourTableBody');
        if (!table) return;
        const rows = Array.from(table.querySelectorAll('tr:not(.no-data-row)'));
        if (rows.length === 0) return;

        const totalPages = Math.ceil(rows.length / rowsPerPage);
        let currentPage = 1;

        function showPage(page) {
            currentPage = page;
            rows.forEach((row, i) => {
                row.style.display = (i >= (page - 1) * rowsPerPage && i < page * rowsPerPage) ? '' : 'none';
            });
            const s = (page - 1) * rowsPerPage + 1;
            const e = Math.min(page * rowsPerPage, rows.length);
            document.getElementById('pageInfo').textContent = `Showing ${s}-${e} of ${rows.length} tours`;
            renderPagination();
        }

        function renderPagination() {
            const container = document.getElementById('pageButtons');
            container.innerHTML = '';
            const prev = document.createElement('button');
            prev.className = 'page-btn'; prev.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
            prev.disabled = currentPage === 1; prev.onclick = () => showPage(currentPage - 1);
            container.appendChild(prev);
            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement('button');
                btn.className = 'page-btn' + (i === currentPage ? ' active' : '');
                btn.textContent = i; btn.onclick = () => showPage(i);
                container.appendChild(btn);
            }
            const next = document.createElement('button');
            next.className = 'page-btn'; next.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
            next.disabled = currentPage === totalPages; next.onclick = () => showPage(currentPage + 1);
            container.appendChild(next);
        }
        showPage(1);
    })();

    // ========================
    // PDF DOWNLOAD
    // ========================
    async function downloadPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'mm', 'a4');
        const element = document.getElementById('reportContent');

        const buttons = document.querySelectorAll('.report-actions, .filter-bar, .table-search, #tablePagination');
        buttons.forEach(el => el.style.display = 'none');

        await html2canvas(element, { scale: 2, useCORS: true, logging: false }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const imgProps = doc.getImageProperties(imgData);
            const pdfWidth = doc.internal.pageSize.getWidth() - 20;
            const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
            let heightLeft = pdfHeight;
            let position = 10;

            doc.addImage(imgData, 'PNG', 10, position, pdfWidth, pdfHeight);
            heightLeft -= (doc.internal.pageSize.getHeight() - 20);
            while (heightLeft > 0) {
                position = heightLeft - pdfHeight + 10;
                doc.addPage();
                doc.addImage(imgData, 'PNG', 10, position, pdfWidth, pdfHeight);
                heightLeft -= (doc.internal.pageSize.getHeight() - 20);
            }
            doc.save('guide_performance_report.pdf');
        });

        buttons.forEach(el => el.style.display = '');
    }

    // ========================
    // PROFILE DROPDOWN
    // ========================
    function toggleProfileDropdown() {
        document.getElementById('profileDropdown').classList.toggle('show');
    }
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('profileDropdown');
        const pic = document.querySelector('.profile-pic');
        if (dropdown && !dropdown.contains(event.target) && event.target !== pic) {
            dropdown.classList.remove('show');
        }
    });

    // ========================
    // HAMBURGER / SIDEBAR
    // ========================
    document.addEventListener('DOMContentLoaded', function() {
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            hamburgerBtn.classList.toggle('active');
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        }
        function closeSidebar() {
            hamburgerBtn.classList.remove('active');
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        if (hamburgerBtn) hamburgerBtn.addEventListener('click', toggleSidebar);
        if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);
        document.querySelectorAll('.sidebar ul li a').forEach(link => {
            link.addEventListener('click', function() { if (window.innerWidth <= 768) closeSidebar(); });
        });
        window.addEventListener('resize', function() { if (window.innerWidth > 768) closeSidebar(); });
    });
    </script>
</body>
</html>
