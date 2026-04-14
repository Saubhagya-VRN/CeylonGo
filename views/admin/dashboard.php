<?php
  require_once(__DIR__ . '/../../config/config.php');
  require_once(__DIR__ . '/../../core/Database.php');

  if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
      header("Location: /CeylonGo/public/login");
      exit();
  }

  $conn = Database::getMysqliConnection();
  $db   = Database::getConnection();

  $admin_id = $_SESSION['user_ref_id'];

  $stmt = $conn->prepare("SELECT username, role FROM admin WHERE id = ?");
  $stmt->bind_param("i", $admin_id);
  $stmt->execute();
  $admin      = $stmt->get_result()->fetch_assoc();
  $admin_name = $admin['username'];
  $admin_role = $admin['role'];
  $stmt->close();

  $totalUsers = 0;
  $r = $conn->query("SELECT COUNT(*) AS total FROM tourist_users");
  if ($r) $totalUsers = $r->fetch_assoc()['total'];

  $totalProviders = 0;
  $r = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role IN ('guide', 'hotel', 'transport')");
  if ($r) $totalProviders = $r->fetch_assoc()['total'];

  $revenueThisMonth = 0;
  $r = $conn->query("SELECT COALESCE(SUM(total_amount),0) AS rev FROM package_bookings WHERE status != 'cancelled' AND DATE_FORMAT(created_at,'%Y-%m') = DATE_FORMAT(NOW(),'%Y-%m')");
  if ($r) $revenueThisMonth += (float)$r->fetch_assoc()['rev'];
  $r = $conn->query("SELECT COALESCE(SUM(budget_lkr),0) AS rev FROM trips WHERE status = 'completed' AND DATE_FORMAT(created_at,'%Y-%m') = DATE_FORMAT(NOW(),'%Y-%m')");
  if ($r) $revenueThisMonth += (float)$r->fetch_assoc()['rev'];

  $totalRevenue = 0;
  $r = $conn->query("SELECT COALESCE(SUM(total_amount),0) AS rev FROM package_bookings WHERE status != 'cancelled'");
  if ($r) $totalRevenue += (float)$r->fetch_assoc()['rev'];
  $r = $conn->query("SELECT COALESCE(SUM(budget_lkr),0) AS rev FROM trips WHERE status = 'completed'");
  if ($r) $totalRevenue += (float)$r->fetch_assoc()['rev'];

  $totalBookings = 0;
  $r = $conn->query("SELECT (SELECT COUNT(*) FROM package_bookings) + (SELECT COUNT(*) FROM trips) AS total");
  if ($r) $totalBookings = $r->fetch_assoc()['total'];

  $totalPendingBookings = 0;
  $r = $conn->query("SELECT (SELECT COUNT(*) FROM package_bookings WHERE status='pending') + (SELECT COUNT(*) FROM trips WHERE status='pending') AS total");
  if ($r) $totalPendingBookings = $r->fetch_assoc()['total'];

  $totalCancellations = 0;
  $r = $conn->query("SELECT (SELECT COUNT(*) FROM package_bookings WHERE status='cancelled') + (SELECT COUNT(*) FROM trips WHERE status='cancelled' OR refund_requested_at IS NOT NULL) AS total");
  if ($r) $totalCancellations = $r->fetch_assoc()['total'];

  $pendingInquiries = [];
  try {
    $r = $conn->query("
        SELECT i.id, i.subject, i.message, i.created_at,
            COALESCE(CONCAT(t.first_name, ' ', t.last_name), i.guest_name, 'Guest') AS name,
            COALESCE(i.guest_email, '') AS email
        FROM inquiries i
        LEFT JOIN tourist_users t ON i.user_id = t.id
        WHERE i.status = 'pending'
        ORDER BY i.created_at DESC
        LIMIT 6
    ");
    if ($r) { while ($row = $r->fetch_assoc()) $pendingInquiries[] = $row; }  
  } catch (Exception $e) { error_log("Dashboard inquiry fetch error: " . $e->getMessage()); }

  $period      = in_array($_GET['period'] ?? '', ['weekly','monthly','yearly']) ? $_GET['period'] : 'monthly';
  $bookingType = in_array($_GET['booking_type'] ?? '', ['both','package','custom']) ? $_GET['booking_type'] : 'both';

  switch ($period) {
      case 'weekly':  $bucketExpr = "CONCAT(YEAR(created_at),'-W',LPAD(WEEK(created_at,1),2,'0'))"; break;
      case 'yearly':  $bucketExpr = "YEAR(created_at)"; break;
      default:        $bucketExpr = "DATE_FORMAT(created_at,'%Y-%m')";
  }

  $rows = [];
  if ($bookingType === 'package' || $bookingType === 'both') {
      $s = $db->query("SELECT {$bucketExpr} AS period, COUNT(*) AS total, COALESCE(SUM(total_amount),0) AS revenue, SUM(status='cancelled') AS cancelled FROM package_bookings GROUP BY period ORDER BY period ASC");
      foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $r) {
          $k = (string)$r['period'];
          if (!isset($rows[$k])) $rows[$k] = ['period'=>$k,'total'=>0,'revenue'=>0.0,'cancelled'=>0];
          $rows[$k]['total'] += (int)$r['total']; $rows[$k]['revenue'] += (float)$r['revenue']; $rows[$k]['cancelled'] += (int)$r['cancelled'];
      }
  }
  if ($bookingType === 'custom' || $bookingType === 'both') {
      $s = $db->query("SELECT {$bucketExpr} AS period, COUNT(*) AS total, COALESCE(SUM(CASE WHEN status='completed' THEN budget_lkr ELSE 0 END),0) AS revenue, SUM(status='cancelled' OR refund_requested_at IS NOT NULL) AS cancelled FROM trips GROUP BY period ORDER BY period ASC");
      foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $r) {
          $k = (string)$r['period'];
          if (!isset($rows[$k])) $rows[$k] = ['period'=>$k,'total'=>0,'revenue'=>0.0,'cancelled'=>0];
          $rows[$k]['total'] += (int)$r['total']; $rows[$k]['revenue'] += (float)$r['revenue']; $rows[$k]['cancelled'] += (int)$r['cancelled'];
      }
  }
  ksort($rows); $rows = array_values($rows);

  $chartLabels        = json_encode(array_column($rows, 'period'));
  $chartBookings      = json_encode(array_column($rows, 'total'));
  $chartRevenue       = json_encode(array_map('floatval', array_column($rows, 'revenue')));
  $chartCancellations = json_encode(array_column($rows, 'cancelled'));
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/admin/dashboard.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/base.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/navbar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/sidebar.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/footer.css">
        <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <title>Ceylon Go - Admin Dashboard</title>
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
                    <li class="active"><a href="/CeylonGo/public/admin/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
                    <li><a href="/CeylonGo/public/admin/users"><i class="fa-solid fa-users"></i> Users</a></li>
                    <li><a href="/CeylonGo/public/admin/bookings"><i class="fa-regular fa-calendar"></i> Bookings</a></li>
                    <li><a href="/CeylonGo/public/admin/service"><i class="fa-solid fa-van-shuttle"></i> Service Providers</a></li>
                    <li><a href="/CeylonGo/public/admin/payments"><i class="fa-solid fa-credit-card"></i> Payments</a></li>
                    <li><a href="/CeylonGo/public/admin/inquiries"><i class="fa-solid fa-circle-question"></i> Inquiries</a></li>
                    <li><a href="/CeylonGo/public/admin/packages"><i class="fa-solid fa-box-open"></i> Packages</a></li>
                    <li><a href="/CeylonGo/public/admin/reviews"><i class="fa-regular fa-star"></i> Reviews</a></li>
                    <li><a href="/CeylonGo/public/admin/reports"><i class="fa-solid fa-chart-line"></i> Reports & Analysis</a></li>
                </ul>
            </div>

            <div class="main-content">

                <div class="dashboard-header">
                    <h2 class="header-title">Admin Dashboard</h2>
                    <div class="header-info">
                        <h3><?= htmlspecialchars($admin_name) ?></h3>
                        <h5><span class="role"><?= htmlspecialchars($admin_role) ?></span></h5>
                    </div>
                </div>

                <!-- KPI Cards -->
                <section class="kpi-section">
                    <div class="kpi-row-1">
                        <a href="/CeylonGo/public/admin/users" class="kpi-card kpi-users">
                            <div class="kpi-icon"><i class="fa-solid fa-users"></i></div>
                            <div class="kpi-info"><p class="kpi-label">Total Users</p><p class="kpi-value"><?= number_format($totalUsers) ?></p></div>
                        </a>
                        <a href="/CeylonGo/public/admin/service" class="kpi-card kpi-providers">
                            <div class="kpi-icon"><i class="fa-solid fa-van-shuttle"></i></div>
                            <div class="kpi-info"><p class="kpi-label">Service Providers</p><p class="kpi-value"><?= number_format($totalProviders) ?></p></div>
                        </a>
                        <a href="/CeylonGo/public/admin/payments" class="kpi-card kpi-revenue">
                            <div class="kpi-icon"><i class="fa-solid fa-sack-dollar"></i></div>
                            <div class="kpi-info"><p class="kpi-label" title="Package bookings (non-cancelled) + completed custom trips">Total Revenue</p><p class="kpi-value">LKR <?= number_format($totalRevenue, 0) ?></p></div>
                        </a>
                        <a href="/CeylonGo/public/admin/payments" class="kpi-card kpi-revenue">
                            <div class="kpi-icon"><i class="fa-solid fa-circle-dollar-to-slot"></i></div>
                            <div class="kpi-info"><p class="kpi-label">Revenue This Month</p><p class="kpi-value">LKR <?= number_format($revenueThisMonth, 0) ?></p></div>
                        </a>
                    </div>
                    <div class="kpi-row-2">
                        <a href="/CeylonGo/public/admin/bookings" class="kpi-card kpi-bookings">
                            <div class="kpi-icon"><i class="fa-regular fa-calendar-check"></i></div>
                            <div class="kpi-info"><p class="kpi-label">Total Bookings</p><p class="kpi-value"><?= number_format($totalBookings) ?></p></div>
                        </a>
                        <a href="/CeylonGo/public/admin/bookings" class="kpi-card kpi-pending">
                            <div class="kpi-icon"><i class="fa-regular fa-clock"></i></div>
                            <div class="kpi-info"><p class="kpi-label">Pending Bookings</p><p class="kpi-value"><?= number_format($totalPendingBookings) ?></p></div>
                        </a>
                        <a href="/CeylonGo/public/admin/bookings" class="kpi-card kpi-cancel">
                            <div class="kpi-icon"><i class="fa-solid fa-ban"></i></div>
                            <div class="kpi-info"><p class="kpi-label">Cancellations</p><p class="kpi-value"><?= number_format($totalCancellations) ?></p></div>
                        </a>
                    </div>
                </section>

                <!-- Analytics Charts -->
                <section class="chart-section">
                    <div class="chart-section-header">
                        <h4>Analytics Overview</h4>
                        <div class="chart-filters">
                            <div class="filter-group">
                                <span class="filter-label">Period:</span>
                                <div class="filter-pills" id="periodFilter">
                                    <button class="pill <?= $period==='weekly'  ? 'active' : '' ?>" data-value="weekly">Weekly</button>
                                    <button class="pill <?= $period==='monthly' ? 'active' : '' ?>" data-value="monthly">Monthly</button>
                                    <button class="pill <?= $period==='yearly'  ? 'active' : '' ?>" data-value="yearly">Yearly</button>
                                </div>
                            </div>
                            <div class="filter-group">
                                <span class="filter-label">Type:</span>
                                <div class="filter-pills" id="typeFilter">
                                    <button class="pill <?= $bookingType==='both'    ? 'active' : '' ?>" data-value="both">Both</button>
                                    <button class="pill <?= $bookingType==='package' ? 'active' : '' ?>" data-value="package">Package</button>
                                    <button class="pill <?= $bookingType==='custom'  ? 'active' : '' ?>" data-value="custom">Custom</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="charts-row-top">
                        <div class="chart-card">
                            <p class="chart-title"><i class="fa-regular fa-calendar-check"></i> Total Bookings</p>
                            <div class="chart-wrap"><canvas id="bookingsChart"></canvas></div>
                        </div>
                    </div>
                    <div class="charts-row-middle">
                        <div class="chart-card">
                            <p class="chart-title"><i class="fa-solid fa-sack-dollar"></i> Revenue (LKR)</p>
                            <div class="chart-wrap"><canvas id="revenueChart"></canvas></div>
                        </div>
                    </div>
                    <div class="charts-row-bottom">
                        <div class="chart-card">
                            <p class="chart-title"><i class="fa-solid fa-ban"></i> Cancellations</p>
                            <div class="chart-wrap"><canvas id="cancellationsChart"></canvas></div>
                        </div>
                    </div>

                </section>

                <!-- Pending Inquiries -->
                <section class="inquiries-section">
                    <div class="section-header">
                        <h4>Pending Inquiries</h4>
                        <a href="/CeylonGo/public/admin/inquiries" class="view-all-link">View All &rarr;</a>
                    </div>
                    <?php if (empty($pendingInquiries)): ?>
                        <div class="empty-state">
                            <i class="fa-solid fa-circle-check"></i>
                            <p>No pending inquiries — all caught up!</p>
                        </div>
                    <?php else: ?>
                        <div class="inquiry-list">
                            <?php foreach ($pendingInquiries as $inq): ?>
                                <div class="inquiry-item">
                                    <div class="inquiry-avatar"><?= strtoupper(substr($inq['name'] ?? 'U', 0, 1)) ?></div>
                                    <div class="inquiry-body">
                                        <p class="inquiry-name"><?= htmlspecialchars($inq['name'] ?? 'Unknown') ?></p>
                                        <p class="inquiry-subject"><?= htmlspecialchars($inq['subject'] ?? 'No subject') ?></p>
                                        <p class="inquiry-msg"><?= htmlspecialchars(mb_strimwidth($inq['message'] ?? '', 0, 80, '…')) ?></p>
                                    </div>
                                    <div class="inquiry-meta">
                                        <span class="badge-pending">Pending</span>
                                        <p class="inquiry-date"><?= date('d M Y', strtotime($inq['created_at'])) ?></p>
                                        <a href="/CeylonGo/public/admin/inquiries" class="reply-link">💬 Reply</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

            </div>
        </div>

        <footer>
            <ul>
                <li><a href="/CeylonGo/public/admin/bookings">View All Bookings</a></li>
                <li><a href="/CeylonGo/public/admin/reports">Reports & Analysis</a></li>
                <li><a href="/CeylonGo/public/admin/payments">Payments</a></li>
            </ul>
        </footer>

        <script>
            function toggleProfileDropdown() {
                document.getElementById('profileDropdown').classList.toggle('show');
            }
            document.addEventListener('click', function(e) {
                const dd  = document.getElementById('profileDropdown');
                const pic = document.querySelector('.profile-pic');
                if (dd && !dd.contains(e.target) && e.target !== pic) dd.classList.remove('show');
            });

            const CHART_DATA = {
                labels        : <?= $chartLabels ?>,
                bookings      : <?= $chartBookings ?>,
                revenue       : <?= $chartRevenue ?>,
                cancellations : <?= $chartCancellations ?>
            };

            let bookingsChart, revenueChart, cancellationsChart;

            function miniOpts(isCurrency) {
                return {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => isCurrency
                                    ? ' LKR ' + Number(ctx.parsed.y).toLocaleString('en-US', {minimumFractionDigits: 0})
                                    : ' ' + ctx.parsed.y
                            }
                        }
                    },
                    scales: {
                        x: { ticks: { font: { size: 10 }, maxRotation: 45 } },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: { size: 10 },
                                callback: isCurrency ? v => 'LKR ' + Number(v).toLocaleString() : v => v
                            }
                        }
                    }
                };
            }

            function buildCharts(data) {
                if (bookingsChart)      bookingsChart.destroy();
                if (revenueChart)       revenueChart.destroy();
                if (cancellationsChart) cancellationsChart.destroy();

                bookingsChart = new Chart(document.getElementById('bookingsChart').getContext('2d'), {
                    type: 'bar',
                    data: { labels: data.labels, datasets: [{ label:'Bookings', data: data.bookings, backgroundColor:'rgba(13,110,253,0.7)', borderColor:'rgba(13,110,253,1)', borderWidth:1, borderRadius:3 }] },
                    options: miniOpts(false)
                });
                revenueChart = new Chart(document.getElementById('revenueChart').getContext('2d'), {
                    type: 'line',
                    data: { labels: data.labels, datasets: [{ label:'Revenue (LKR)', data: data.revenue, borderColor:'rgba(25,135,84,1)', backgroundColor:'rgba(25,135,84,0.12)', borderWidth:2, pointRadius:3, fill:true, tension:0.3 }] },
                    options: miniOpts(true)
                });
                cancellationsChart = new Chart(document.getElementById('cancellationsChart').getContext('2d'), {
                    type: 'bar',
                    data: { labels: data.labels, datasets: [{ label:'Cancellations', data: data.cancellations, backgroundColor:'rgba(220,53,69,0.7)', borderColor:'rgba(220,53,69,1)', borderWidth:1, borderRadius:3 }] },
                    options: miniOpts(false)
                });
            }

            buildCharts(CHART_DATA);

            let activePeriod = '<?= $period ?>';
            let activeType   = '<?= $bookingType ?>';

            function fetchCharts() {
                fetch(`/CeylonGo/public/admin/dashboard?period=${activePeriod}&booking_type=${activeType}&ajax=1`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.json())
                    .then(json => buildCharts(json))
                    .catch(err => console.error('Dashboard chart fetch error:', err));
            }

            document.getElementById('periodFilter').addEventListener('click', function(e) {
                if (!e.target.matches('.pill')) return;
                this.querySelectorAll('.pill').forEach(p => p.classList.remove('active'));
                e.target.classList.add('active');
                activePeriod = e.target.dataset.value;
                fetchCharts();
            });
            document.getElementById('typeFilter').addEventListener('click', function(e) {
                if (!e.target.matches('.pill')) return;
                this.querySelectorAll('.pill').forEach(p => p.classList.remove('active'));
                e.target.classList.add('active');
                activeType = e.target.dataset.value;
                fetchCharts();
            });
        </script>
    </body>
</html>