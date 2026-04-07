<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'session_init.php';

if (!isset($overall)) {
    $overall = ['total_bookings' => 0, 'total_revenue' => 0];
}
if (!isset($monthly)) {
    $monthly = [];
}

// Prepare data for Chart.js
$chartLabels = array_column($monthly, 'month');
$chartData = array_column($monthly, 'revenue');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ceylon Go - Performance Report</title>
    <link rel="stylesheet" href="/CeylonGo/public/css/guide/base.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/guide/navbar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/guide/sidebar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/guide/footer.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/guide/cards.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/guide/buttons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    
    <style>
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            text-align: center;
            border-bottom: 4px solid #3d8b40;
        }
        .stat-card.revenue { border-color: #f39c12; }
        .stat-card h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .stat-card .value {
            font-size: 32px;
            font-weight: 700;
            color: #333;
        }
        .chart-container {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .table-container {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #f8f9fa;
            color: #555;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="branding">
            <button class="hamburger-btn" id="hamburgerBtn">
                <span></span><span></span><span></span>
            </button>
            <img src="/CeylonGo/public/images/logo.png" class="logo-img" alt="Logo">
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

    <div class="page-wrapper">
        <div class="sidebar" id="sidebar">
            <ul>
                <li><a href="/CeylonGo/public/guide/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
                <li><a href="/CeylonGo/public/guide/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Tours</a></li>
                <li><a href="/CeylonGo/public/guide/pending"><i class="fa-regular fa-clock"></i> Pending Requests</a></li>
                <li><a href="/CeylonGo/public/guide/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Tours</a></li>
                <li><a href="/CeylonGo/public/guide/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
                <li><a href="/CeylonGo/public/guide/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
                <li class="active"><a href="#"><i class="fa-solid fa-chart-line"></i> Performance Report</a></li>
                <li><a href="/CeylonGo/public/guide/places"><i class="fa-solid fa-map-location-dot"></i> My Places</a></li>
                <li><a href="/CeylonGo/public/guide/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
            </ul>
        </div>

        <div class="main-content" id="reportContent">
            <div class="report-header">
                <div>
                    <h2 class="page-title">Performance Report</h2>
                    <p style="color: #666;">Detailed overview of your guiding activity and earnings</p>
                </div>
                <button class="btn-save" onclick="downloadPDF()" style="margin-top: 0; background-color: #333;">
                    <i class="fa-solid fa-file-pdf"></i> Download PDF
                </button>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Tours</h3>
                    <div class="value"><?= number_format($overall['total_bookings']) ?></div>
                </div>
                <div class="stat-card revenue">
                    <h3>Total Revenue</h3>
                    <div class="value">Rs. <?= number_format($overall['total_revenue'], 2) ?></div>
                </div>
                <div class="stat-card">
                    <h3>Active Months</h3>
                    <div class="value"><?= count($monthly) ?></div>
                </div>
            </div>

            <div class="chart-container">
                <h3 style="margin-bottom: 20px;">Monthly Revenue (Last 12 Months)</h3>
                <canvas id="revenueChart" height="100"></canvas>
            </div>

            <div class="table-container">
                <h3 style="margin-bottom: 20px;">Earnings Breakdown</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Total Tours</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($monthly)): ?>
                            <tr><td colspan="3" style="text-align: center;">No data available for the last 12 months.</td></tr>
                        <?php else: ?>
                            <?php foreach (array_reverse($monthly) as $m): ?>
                                <tr>
                                    <td><?= date('F Y', strtotime($m['month'])) ?></td>
                                    <td><?= $m['bookings'] ?></td>
                                    <td>Rs. <?= number_format($m['revenue'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chartLabels) ?>,
                datasets: [{
                    label: 'Revenue (LKR)',
                    data: <?= json_encode($chartData) ?>,
                    backgroundColor: 'rgba(61, 139, 64, 0.7)',
                    borderColor: 'rgba(61, 139, 64, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rs. ' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        async function downloadPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'mm', 'a4');
            const element = document.getElementById('reportContent');
            
            // Temporary hide the download button for PDF
            const downloadBtn = document.querySelector('button[onclick="downloadPDF()"]');
            downloadBtn.style.display = 'none';

            await html2canvas(element).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const imgProps = doc.getImageProperties(imgData);
                const pdfWidth = doc.internal.pageSize.getWidth();
                const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
                doc.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                doc.save('guide_performance_report.pdf');
            });

            downloadBtn.style.display = 'block';
        }

        function toggleProfileDropdown() {
            document.getElementById('profileDropdown').classList.toggle('show');
        }
    </script>
</body>
</html>
