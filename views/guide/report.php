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

// Extract main city from location
function getMainCity($location) {
    $location = trim((string) $location);
    if ($location === '') {
        return 'N/A';
    }

    $parts = preg_split('/[\,\|\-\/]+/', $location);
    foreach ($parts as $part) {
        $value = trim($part);
        if ($value !== '') {
            return $value;
        }
    }

    return $location;
}

// Generated timestamp
$generatedAt = date('F d, Y \a\t h:i A');
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
    <style>
    @media print {
        .navbar, .sidebar, .sidebar-overlay, footer,
        .report-actions, .filter-bar, .charts-row,
        .kpi-grid, .table-search, #tablePagination,
        .report-period, .report-page-header { display: none !important; }

        body { background: #fff !important; margin: 0; padding: 0; }
        .page-wrapper { display: block !important; }
        .main-content { margin: 0 !important; padding: 20px !important; width: 100% !important; }

        .report-brand-header {
            display: flex !important;
            background: #2c5530 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            border-radius: 0 !important;
            margin: -20px -20px 20px -20px !important;
            padding: 18px 24px !important;
        }
        .report-brand-header .brand-logo { width: 40px; height: 40px; }
        .report-brand-header .brand-text h1 { font-size: 18px !important; }

        .report-table-section {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
        }
        .report-table-header h3 { font-size: 16px !important; color: #000 !important; }
        .report-table-header h3 i { display: none; }
        .report-table { font-size: 11px !important; }
        .report-table thead th {
            background: #333 !important;
            color: #fff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            padding: 8px 10px !important;
            font-size: 10px !important;
        }
        .report-table tbody td {
            padding: 6px 10px !important;
            font-size: 10px !important;
            border-bottom: 1px solid #ddd !important;
        }
        .report-table tfoot td {
            background: #f0f0f0 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            padding: 10px !important;
            font-size: 12px !important;
            font-weight: bold !important;
            border-top: 2px solid #333 !important;
        }
        .table-search { display: none !important; }
    }
    </style>
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
                <li><a href="/CeylonGo/public/guide/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
                <li class="active"><a href="/CeylonGo/public/guide/report"><i class="fa-solid fa-chart-line"></i> Performance Report</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="reportContent">

            <!-- Report Branding Header (hidden on screen, visible in print/PDF) -->
            <div class="report-brand-header" id="reportBrandHeader">
                <div class="brand-left">
                    <img src="/CeylonGo/public/images/logo.png" alt="Ceylon Go" class="brand-logo">
                    <div class="brand-text">
                        <h1>Ceylon Go</h1>
                        <span class="brand-tagline">Guide Performance Report</span>
                    </div>
                </div>
                <div class="brand-right">
                    <div class="brand-info-row"><i class="fa-regular fa-user"></i> <strong><?= htmlspecialchars($user_name) ?></strong></div>
                    <div class="brand-info-row"><i class="fa-solid fa-id-badge"></i> Tour Guide</div>
                    <div class="brand-info-row"><i class="fa-regular fa-clock"></i> Generated: <?= $generatedAt ?></div>
                    <div class="brand-info-row"><i class="fa-regular fa-calendar-check"></i> Period: <?= htmlspecialchars($periodLabel) ?></div>
                </div>
            </div>

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
                                <th>Fee (LKR)</th>
                            </tr>
                        </thead>
                        <tbody id="tourTableBody">
                            <?php if (empty($tours)): ?>
                                <tr class="no-data-row">
                                    <td colspan="7">
                                        <i class="fa-regular fa-folder-open"></i>
                                        No tour records found for the selected period.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($tours as $tour): ?>
                                    <tr>
                                        <td class="tour-id">#GT<?= str_pad($tour["id"], 3, "0", STR_PAD_LEFT) ?></td>
                                        <td><?= htmlspecialchars($tour["customerName"]) ?></td>
                                        <td><?= date("M d, Y", strtotime($tour["date"])) ?></td>
                                        <td><?= date("h:i A", strtotime($tour["time"])) ?></td>
                                        <td><?= htmlspecialchars(getMainCity($tour["location"])) ?></td>
                                        <td><span class="lang-tag"><?= htmlspecialchars($tour["language"]) ?></span></td>
                                        <td class="fee-cell">Rs. <?= number_format($tour["fee"], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($tours)): ?>
                        <tfoot style="background: rgba(44, 85, 48, 0.05); font-weight: 700;">
                            <tr>
                                <td colspan="6" style="text-align: right; padding: 14px 20px; color: #1a1a2e; font-size: 14px;">TOTAL REVENUE</td>
                                <td style="border-top: 2px solid #2c5530; color: #1a1a2e; padding: 14px 16px;">Rs. <?= number_format(array_sum(array_column($tours, "fee")), 2) ?></td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
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
    const guideChartLabels = <?= json_encode($chartLabels) ?>;
    const guideChartRevenue = <?= json_encode($chartRevenue) ?>;
    const guideChartBookings = <?= json_encode($chartBookings) ?>;
    const labels = guideChartLabels.length ? guideChartLabels : ['No Data'];
    const revenueData = guideChartRevenue.length ? guideChartRevenue : [0];
    const bookingData = guideChartBookings.length ? guideChartBookings : [0];

    const revenueGradient = revenueCtx.createLinearGradient(0, 0, 0, 350);
    revenueGradient.addColorStop(0, 'rgba(61, 139, 64, 0.25)');
    revenueGradient.addColorStop(1, 'rgba(61, 139, 64, 0.02)');

    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Revenue (LKR)',
                    data: revenueData,
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
                    data: bookingData,
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
        const doc = new jsPDF('l', 'mm', 'a4');
        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();

        // ---- HEADER ----
        doc.setFillColor(44, 85, 48); // System Green
        doc.rect(0, 0, pageWidth, 32, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(22);
        doc.setFont('helvetica', 'bold');
        doc.text('Ceylon Go', 14, 12);
        doc.setFontSize(12);
        doc.setFont('helvetica', 'normal');
        doc.text('Guide Performance Report', 14, 20);
        doc.setFontSize(10);
        doc.text('User: <?= addslashes($user_name) ?>  |  Type: Tour Guide', 14, 27);
        doc.text('Generated: <?= addslashes($generatedAt) ?>  |  Period: <?= addslashes($periodLabel) ?>', pageWidth - 14, 27, { align: 'right' });

        // ---- KPI SUMMARY ----
        let y = 40;
        doc.setTextColor(30, 30, 30);
        doc.setFontSize(13);
        doc.setFont('helvetica', 'bold');
        doc.text('Performance metrics', 14, y);
        y += 8;

        const kpiData = [
            ['Total Revenue', 'Rs. <?= number_format($kpi["total_revenue"], 2) ?>'],
            ['Avg. Fee', 'Rs. <?= number_format($kpi["avg_fee"], 2) ?>'],
            ['Total Bookings', '<?= number_format($kpi["total_bookings"]) ?>'],
            ['Unique Clients', '<?= number_format($kpi["unique_clients"]) ?>'],
            ['Completion Rate', '<?= $kpi["completion_rate"] ?>%']
        ];

        doc.autoTable({
            startY: y,
            head: [['Metric', 'Value']],
            body: kpiData,
            theme: 'grid',
            headStyles: { fillColor: [44, 85, 48], textColor: 255, fontStyle: 'bold', fontSize: 10 },
            bodyStyles: { fontSize: 10 },
            columnStyles: { 0: { fontStyle: 'bold', cellWidth: 50 }, 1: { cellWidth: 50 } },
            margin: { left: 14 },
            tableWidth: 100
        });

        // ---- TOUR DETAILS TABLE ----
        y = doc.lastAutoTable.finalY + 12;
        doc.setFontSize(13);
        doc.setFont('helvetica', 'bold');
        doc.text('Detailed Tour History', 14, y);
        y += 4;

        // Build table data from PHP
        const tourRows = [
            <?php foreach ($tours as $tour): ?>
            [
                '#GT<?= str_pad($tour["id"], 3, "0", STR_PAD_LEFT) ?>',
                '<?= addslashes($tour["customerName"]) ?>',
                '<?= date("M d, Y", strtotime($tour["date"])) ?>',
                '<?= date("h:i A", strtotime($tour["time"])) ?>',
                '<?= addslashes(getMainCity($tour["location"])) ?>',
                '<?= addslashes($tour["language"]) ?>',
                'Rs. <?= number_format($tour["fee"], 2) ?>'
            ],
            <?php endforeach; ?>
        ];

        // Total
        const totalFee = <?= array_sum(array_column($tours, "fee")) ?>;

        doc.autoTable({
            startY: y,
            head: [['Tour ID', 'Customer', 'Date', 'Time', 'Location', 'Language', 'Fee (LKR)']],
            body: tourRows,
            foot: [['', '', '', '', '', 'TOTAL REVENUE', 'Rs. ' + totalFee.toLocaleString('en-US', {minimumFractionDigits: 2})]],
            theme: 'grid',
            headStyles: { fillColor: [44, 85, 48], textColor: 255, fontStyle: 'bold', fontSize: 9, cellPadding: 3 },
            bodyStyles: { fontSize: 8.5, cellPadding: 2.5 },
            footStyles: { fillColor: [240, 248, 240], textColor: [44, 85, 48], fontStyle: 'bold', fontSize: 10, cellPadding: 3 },
            columnStyles: {
                0: { cellWidth: 20 },
                1: { cellWidth: 40 },
                6: { halign: 'right', fontStyle: 'bold' }
            },
            margin: { left: 14, right: 14 }
        });

        // ---- FOOTER ----
        const totalPages = doc.internal.getNumberOfPages();
        const pageHeight = doc.internal.pageSize.height;
        for (let i = 1; i <= totalPages; i++) {
            doc.setPage(i);
            doc.setFontSize(8);
            doc.setTextColor(150);
            doc.text('Ceylon Go Guide Performance Report', 14, pageHeight - 10);
            doc.text('Page ' + i + ' of ' + totalPages, pageWidth - 14, pageHeight - 10, { align: 'right' });
        }

        doc.save('guide_performance_report.pdf');
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
