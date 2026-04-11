<?php
/**
 * Admin Reports & Analysis — form filters, preview table, charts, PDF/Excel export.
 */
class AdminReportController
{
    private PDO $db;
    private const PER_PAGE = 15;
    private const EXPORT_MAX_ROWS = 10000;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private function requireAdmin(): void
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            header('Location: /CeylonGo/public/login');
            exit();
        }
    }

    private function loadComposerAutoload(): void
    {
        $path = dirname(__DIR__) . '/vendor/autoload.php';
        if (is_readable($path)) {
            require_once $path;
        }
    }

    /**
     * Collect filter array from GET (used by index + exports).
     */
    private function filtersFromRequest(): array
    {
        $df = trim((string) ($_GET['date_from'] ?? ''));
        $dt = trim((string) ($_GET['date_to'] ?? ''));
        if ($df !== '' && $dt !== '' && strcmp($df, $dt) > 0) {
            $tmp = $df;
            $df = $dt;
            $dt = $tmp;
        }

        return [
            'date_from'          => $df,
            'date_to'            => $dt,
            // Role is not exposed in the Users report UI; controller forces tourist for that report.
            'user_role'          => $_GET['user_role'] ?? 'all',
            'user_status'        => $_GET['user_status'] ?? 'all',
            'booking_status'     => $_GET['booking_status'] ?? 'all',
            'pay_method'         => $_GET['pay_method'] ?? 'all',
            'pay_status'         => $_GET['pay_status'] ?? 'all',
            'provider_category'  => $_GET['provider_category'] ?? 'all',
            'provider_status'    => $_GET['provider_status'] ?? 'all',
        ];
    }

    /**
     * Users report = registered customers (tourist accounts) only.
     */
    private function applyReportTypeDefaults(string $reportType, array $filters): array
    {
        if ($reportType === 'users') {
            $filters['user_role'] = 'tourist';
        }
        return $filters;
    }

    private function whitelistSort(string $type, string $sort): string
    {
        $map = [
            'users'          => ['created_at', 'email'],
            'bookings'       => ['created_at', 'amount', 'status', 'customer', 'type'],
            'payments'       => ['created_at', 'amount', 'status', 'id'],
            'providers'      => ['registered_at', 'provider_name', 'role', 'email'],
            'packages'       => ['created_at', 'title', 'price'],
            'trip_payments'  => ['created_at', 'amount', 'status', 'id'],
        ];
        $allowed = $map[$type] ?? ['created_at'];
        return in_array($sort, $allowed, true) ? $sort : $allowed[0];
    }

    private function pdfReportTitle(string $reportType): string
    {
        if ($reportType === 'users') {
            return 'CeylonGo Report — Customers (tourists)';
        }
        if ($reportType === 'packages') {
            return 'CeylonGo Report — Tour packages';
        }
        if ($reportType === 'trip_payments') {
            return 'CeylonGo Report — Custom trip payments';
        }
        return 'CeylonGo Report — ' . ucfirst(str_replace('_', ' ', $reportType));
    }

    public function index(): void
    {
        $this->requireAdmin();

        $generated = isset($_GET['generated']) && $_GET['generated'] === '1';
        $reportType = $_GET['type'] ?? 'bookings';
        $allowedTypes = ['users', 'bookings', 'payments', 'providers', 'packages', 'trip_payments'];
        if (!in_array($reportType, $allowedTypes, true)) {
            $reportType = 'bookings';
        }

        $search = trim($_GET['q'] ?? '');
        $sort   = $this->whitelistSort($reportType, $_GET['sort'] ?? 'created_at');
        $dir    = strtoupper($_GET['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
        $page   = max(1, (int) ($_GET['page'] ?? 1));

        $filters = $this->applyReportTypeDefaults($reportType, $this->filtersFromRequest());
        $model   = new Report($this->db);

        $reportData = [];
        $totalRows  = 0;
        $summary    = [];
        $charts     = [
            'bookings_monthly' => ['labels' => [], 'counts' => []],
            'revenue'          => ['labels' => [], 'amounts' => []],
            'user_growth'      => ['labels' => [], 'counts' => []],
        ];

        if ($generated) {
            switch ($reportType) {
                case 'users':
                    $res = $model->fetchUsers($filters, $search, $sort, $dir, $page, self::PER_PAGE);
                    $reportData = $res['rows'];
                    $totalRows  = $res['total'];
                    $summary    = $model->summarizeUsers($filters);
                    break;
                case 'bookings':
                    $res = $model->fetchBookings($filters, $search, $sort, $dir, $page, self::PER_PAGE);
                    $reportData = $res['rows'];
                    $totalRows  = $res['total'];
                    $summary    = $model->summarizeBookings($filters);
                    break;
                case 'payments':
                    $res = $model->fetchPayments($filters, $search, $sort, $dir, $page, self::PER_PAGE);
                    $reportData = $res['rows'];
                    $totalRows  = $res['total'];
                    $summary    = $model->summarizePayments($filters);
                    break;
                case 'providers':
                    $res = $model->fetchProviders($filters, $search, $sort, $dir, $page, self::PER_PAGE);
                    $reportData = $res['rows'];
                    $totalRows  = $res['total'];
                    $summary    = $model->summarizeProviders($filters);
                    break;
                case 'packages':
                    $res = $model->fetchPackages($filters, $search, $sort, $dir, $page, self::PER_PAGE);
                    $reportData = $res['rows'];
                    $totalRows  = $res['total'];
                    $summary    = $model->summarizePackages($filters);
                    break;
                case 'trip_payments':
                    $res = $model->fetchTripPayments($filters, $search, $sort, $dir, $page, self::PER_PAGE);
                    $reportData = $res['rows'];
                    $totalRows  = $res['total'];
                    $summary    = $model->summarizeTripPayments($filters);
                    break;
            }

            $df = $filters['date_from'] ?: null;
            $dt = $filters['date_to'] ?: null;
            $charts['bookings_monthly'] = $model->chartBookingsPerMonth($df, $dt);
            $charts['revenue']          = $model->chartRevenueTrend($df, $dt);
            $userGrowthRole = $reportType === 'users' ? 'tourist' : null;
            $charts['user_growth']      = $model->chartUserGrowth($df, $dt, $userGrowthRole);
        }

        $totalPages = $totalRows > 0 ? (int) ceil($totalRows / self::PER_PAGE) : 1;

        view('admin/reports', [
            'generated'    => $generated,
            'reportType'   => $reportType,
            'filters'      => $filters,
            'search'       => $search,
            'sort'         => $sort,
            'dir'          => $dir,
            'page'         => $page,
            'perPage'      => self::PER_PAGE,
            'totalRows'    => $totalRows,
            'totalPages'   => $totalPages,
            'reportData'   => $reportData,
            'summary'      => $summary,
            'charts'       => $charts,
        ]);
    }

    /**
     * Build query string for export links (preserve filters).
     */
    public static function exportQueryBase(array $get, string $type): string
    {
        $keys = [
            'generated', 'type', 'date_from', 'date_to',
            'user_role', 'user_status',
            'booking_status',
            'pay_method', 'pay_status',
            'provider_category', 'provider_status',
            'q', 'sort', 'dir',
        ];
        $parts = ['generated=1', 'type=' . rawurlencode($type)];
        foreach ($keys as $k) {
            if (!isset($get[$k]) || $k === 'type') {
                continue;
            }
            $parts[] = rawurlencode($k) . '=' . rawurlencode((string) $get[$k]);
        }
        return implode('&', $parts);
    }

    public function exportPdf(): void
    {
        $this->requireAdmin();
        $this->loadComposerAutoload();
        if (!class_exists(\Dompdf\Dompdf::class)) {
            http_response_code(500);
            echo 'PDF export requires Composer dependencies. Run: composer install';
            exit();
        }

        $reportType = $_GET['type'] ?? 'bookings';
        $allowedTypes = ['users', 'bookings', 'payments', 'providers', 'packages', 'trip_payments'];
        if (!in_array($reportType, $allowedTypes, true)) {
            $reportType = 'bookings';
        }

        $search  = trim($_GET['q'] ?? '');
        $sort    = $this->whitelistSort($reportType, $_GET['sort'] ?? 'created_at');
        $dir     = strtoupper($_GET['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
        $filters = $this->applyReportTypeDefaults($reportType, $this->filtersFromRequest());
        $model   = new Report($this->db);

        $rows = $this->fetchAllForExport($model, $reportType, $filters, $search, $sort, $dir);

        $summary = $this->fetchSummaryForExport($model, $reportType, $filters);

        $title = $this->pdfReportTitle($reportType);
        $html  = $this->buildExportFiltersSummaryHtml($reportType, $filters, $search)
            . $this->buildSummarySectionHtml($reportType, $summary)
            . $this->buildHtmlTable($reportType, $rows, $filters);

        $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml('<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
            body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#222;}
            h1{font-size:16px;margin:0 0 12px;}
            h2{font-size:13px;margin:14px 0 8px;border-bottom:1px solid #ccc;padding-bottom:4px;}
            .meta{color:#666;font-size:10px;margin-bottom:12px;}
            .filters-block{font-size:10px;color:#444;margin-bottom:12px;line-height:1.45;}
            .filters-block strong{color:#222;}
            table.data{border-collapse:collapse;width:100%;}
            table.data th,table.data td{border:1px solid #ccc;padding:6px 8px;text-align:left;}
            table.data th{background:#f0f0f0;font-weight:bold;}
            table.data tr:nth-child(even){background:#fafafa;}
            table.summary-table{border-collapse:collapse;width:100%;max-width:520px;margin:0 0 14px;}
            table.summary-table td{border:1px solid #ddd;padding:6px 10px;}
            table.summary-table td.lbl{font-weight:bold;background:#f5f5f5;width:42%;}
        </style></head><body>
            <h1>' . htmlspecialchars($title) . '</h1>
            <div class="meta">Generated: ' . htmlspecialchars(date('Y-m-d H:i')) . ' &mdash; Detail rows in this file: ' . count($rows) . '</div>
            ' . $html . '
        </body></html>');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('ceylongo_report_' . $reportType . '.pdf', ['Attachment' => true]);
        exit();
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function fetchAllForExport(Report $model, string $reportType, array $filters, string $search, string $sort, string $dir): array
    {
        switch ($reportType) {
            case 'users':
                return $model->fetchUsers($filters, $search, $sort, $dir, 1, self::EXPORT_MAX_ROWS)['rows'];
            case 'bookings':
                return $model->fetchBookings($filters, $search, $sort, $dir, 1, self::EXPORT_MAX_ROWS)['rows'];
            case 'payments':
                return $model->fetchPayments($filters, $search, $sort, $dir, 1, self::EXPORT_MAX_ROWS)['rows'];
            case 'providers':
                return $model->fetchProviders($filters, $search, $sort, $dir, 1, self::EXPORT_MAX_ROWS)['rows'];
            case 'packages':
                return $model->fetchPackages($filters, $search, $sort, $dir, 1, self::EXPORT_MAX_ROWS)['rows'];
            case 'trip_payments':
                return $model->fetchTripPayments($filters, $search, $sort, $dir, 1, self::EXPORT_MAX_ROWS)['rows'];
            default:
                return [];
        }
    }

    private function excelHeaders(string $type): array
    {
        switch ($type) {
            case 'users':
                return ['ID', 'Email', 'Ref ID', 'Registered', 'Active'];
            case 'bookings':
                return ['Type', 'ID', 'Customer', 'Status', 'Created', 'Amount (LKR)', 'Detail'];
            case 'payments':
                return ['ID', 'Customer', 'Email', 'Package', 'Amount', 'Status', 'Method', 'Created', 'Paid at'];
            case 'providers':
                return ['ID', 'Name', 'Email', 'Category', 'Active', 'Registered'];
            case 'packages':
                return ['ID', 'Title', 'Location', 'Category', 'Price (LKR)', 'Rating', 'Reviews', 'Trending', 'Created'];
            case 'trip_payments':
                return ['ID', 'Customer', 'Destination', 'Budget (LKR)', 'Status', 'Start date', 'People', 'Created'];
            default:
                return [];
        }
    }

    /**
     * @param array<string,mixed> $row
     * @return array<int,scalar|null>
     */
    private function excelRowValues(string $type, array $row): array
    {
        switch ($type) {
            case 'users':
                return [
                    $row['id'] ?? '',
                    $row['email'] ?? '',
                    $row['ref_id'] ?? '',
                    $row['created_at'] ?? '',
                    isset($row['account_active']) ? ((int) $row['account_active'] ? 'Yes' : 'No') : '',
                ];
            case 'bookings':
                return [
                    $row['booking_type'] ?? '',
                    $row['row_id'] ?? '',
                    $row['customer'] ?? '',
                    $row['status'] ?? '',
                    $row['created_at'] ?? '',
                    $row['amount'] ?? '',
                    $row['detail'] ?? '',
                ];
            case 'payments':
                return [
                    $row['id'] ?? '',
                    $row['fullname'] ?? '',
                    $row['email'] ?? '',
                    $row['package_name'] ?? '',
                    $row['total_amount'] ?? '',
                    $row['status'] ?? '',
                    $row['pay_method'] ?? '',
                    $row['created_at'] ?? '',
                    $row['paid_at'] ?? '',
                ];
            case 'providers':
                return [
                    $row['id'] ?? '',
                    $row['provider_name'] ?? '',
                    $row['email'] ?? '',
                    $row['role'] ?? '',
                    isset($row['is_active']) ? ((int) $row['is_active'] ? 'Yes' : 'No') : '',
                    $row['registered_at'] ?? '',
                ];
            case 'packages':
                return [
                    $row['id'] ?? '',
                    $row['title'] ?? '',
                    $row['location'] ?? '',
                    $row['category'] ?? '',
                    $row['price'] ?? '',
                    $row['rating'] ?? '',
                    $row['reviews'] ?? '',
                    !empty($row['trending']) ? 'Yes' : 'No',
                    $row['created_at'] ?? '',
                ];
            case 'trip_payments':
                return [
                    $row['id'] ?? '',
                    $row['customer_name'] ?? '',
                    $row['destination'] ?? '',
                    $row['budget_lkr'] ?? '',
                    $row['status'] ?? '',
                    $row['start_date'] ?? '',
                    $row['number_of_people'] ?? '',
                    $row['created_at'] ?? '',
                ];
            default:
                return [];
        }
    }

    /**
     * Aggregate metrics for PDF/Excel (matches on-screen Summary cards; ignores table search).
     *
     * @return array<string,mixed>
     */
    private function fetchSummaryForExport(Report $model, string $reportType, array $filters): array
    {
        switch ($reportType) {
            case 'users':
                return $model->summarizeUsers($filters);
            case 'bookings':
                return $model->summarizeBookings($filters);
            case 'payments':
                return $model->summarizePayments($filters);
            case 'providers':
                return $model->summarizeProviders($filters);
            case 'packages':
                return $model->summarizePackages($filters);
            case 'trip_payments':
                return $model->summarizeTripPayments($filters);
            default:
                return [];
        }
    }

    private function formatExportPeriodPlain(array $filters): string
    {
        $df = trim((string) ($filters['date_from'] ?? ''));
        $dt = trim((string) ($filters['date_to'] ?? ''));
        if ($df === '' && $dt === '') {
            return 'All dates (no from/to filter)';
        }
        if ($df !== '' && $dt !== '') {
            return $df . ' → ' . $dt;
        }
        if ($df !== '') {
            return 'From ' . $df;
        }
        return 'Until ' . $dt;
    }

    /**
     * @return array<int,array{0:string,1:string}>
     */
    private function exportFilterContextRows(string $reportType, array $filters): array
    {
        $rows = [['Period', $this->formatExportPeriodPlain($filters)]];
        switch ($reportType) {
            case 'users':
                $rows[] = ['Audience', 'Customers (tourists only)'];
                $st = $filters['user_status'] ?? 'all';
                $rows[] = ['Account status', $st === 'all' ? 'All' : ucfirst((string) $st)];
                break;
            case 'bookings':
                $bs = (string) ($filters['booking_status'] ?? 'all');
                $rows[] = ['Booking status', $bs === 'all' ? 'All' : ucwords(str_replace('_', ' ', $bs))];
                break;
            case 'payments':
                $pm = $filters['pay_method'] ?? 'all';
                $ps = $filters['pay_status'] ?? 'all';
                $rows[] = ['Payment method', $pm === 'all' ? 'All' : ucwords(str_replace('_', ' ', (string) $pm))];
                $rows[] = ['Payment status', $ps === 'all' ? 'All' : ucfirst((string) $ps)];
                break;
            case 'providers':
                $cat = $filters['provider_category'] ?? 'all';
                $pst = $filters['provider_status'] ?? 'all';
                $rows[] = ['Category', $cat === 'all' ? 'All' : ucfirst((string) $cat)];
                $rows[] = ['Provider status', $pst === 'all' ? 'All' : ucfirst((string) $pst)];
                break;
            case 'packages':
                $rows[] = ['Report', 'Tour packages catalog'];
                break;
            case 'trip_payments':
                $rows[] = ['Report', 'Customized trip payment records (trips)'];
                break;
        }
        return $rows;
    }

    /**
     * @param array<string,mixed> $summary
     */
    private function buildExportFiltersSummaryHtml(string $reportType, array $filters, string $search): string
    {
        $lines = ['<div class="filters-block"><strong>Filters</strong><br>'];
        foreach ($this->exportFilterContextRows($reportType, $filters) as $pair) {
            $lines[] = htmlspecialchars($pair[0]) . ': ' . htmlspecialchars($pair[1]) . '<br>';
        }
        if ($search !== '') {
            $lines[] = 'Table search: ' . htmlspecialchars($search) . '<br>';
        }
        $lines[] = '</div>';
        return implode('', $lines);
    }

    /**
     * @param array<string,mixed> $summary
     */
    private function buildSummarySectionHtml(string $reportType, array $summary): string
    {
        $pairs = [];
        switch ($reportType) {
            case 'users':
                $pairs = [
                    ['Registered customers', (string) (int) ($summary['total'] ?? 0)],
                    ['Active', (string) (int) ($summary['active'] ?? 0)],
                    ['Inactive', (string) (int) ($summary['inactive'] ?? 0)],
                ];
                break;
            case 'bookings':
                $pairs = [
                    ['Total records', (string) (int) ($summary['total'] ?? 0)],
                    ['Pending', (string) (int) ($summary['pending'] ?? 0)],
                    ['Confirmed', (string) (int) ($summary['confirmed'] ?? 0)],
                    ['Cancelled', (string) (int) ($summary['cancelled'] ?? 0)],
                    ['Revenue (LKR)', number_format((float) ($summary['total_revenue'] ?? 0), 2)],
                ];
                break;
            case 'payments':
                $pairs = [
                    ['Total rows', (string) (int) ($summary['total'] ?? 0)],
                    ['Paid amount (LKR)', number_format((float) ($summary['total_revenue'] ?? 0), 2)],
                    ['Paid bookings', (string) (int) ($summary['paid'] ?? 0)],
                    ['Pending / approved', (string) (int) ($summary['pending'] ?? 0)],
                ];
                break;
            case 'providers':
                $pairs = [
                    ['Total providers', (string) (int) ($summary['total'] ?? 0)],
                    ['Active', (string) (int) ($summary['active'] ?? 0)],
                    ['Guides', (string) (int) ($summary['guide'] ?? 0)],
                    ['Hotels', (string) (int) ($summary['hotel'] ?? 0)],
                    ['Transport', (string) (int) ($summary['transport'] ?? 0)],
                ];
                break;
            case 'packages':
                $pairs = [
                    ['Total packages', (string) (int) ($summary['total'] ?? 0)],
                    ['Average price (LKR)', number_format((float) ($summary['avg_price'] ?? 0), 2)],
                    ['Marked trending', (string) (int) ($summary['trending'] ?? 0)],
                ];
                break;
            case 'trip_payments':
                $pairs = [
                    ['Total trips', (string) (int) ($summary['total'] ?? 0)],
                    ['Budget in confirmed/completed (LKR)', number_format((float) ($summary['completed_value'] ?? 0), 2)],
                    ['Pending trips', (string) (int) ($summary['pending'] ?? 0)],
                ];
                break;
            default:
                return '';
        }

        $html = '<h2>Summary</h2><table class="summary-table">';
        foreach ($pairs as $p) {
            $html .= '<tr><td class="lbl">' . htmlspecialchars($p[0]) . '</td><td>' . htmlspecialchars($p[1]) . '</td></tr>';
        }
        $html .= '</table>';
        return $html;
    }

    /**
     * @param array<int,array<string,mixed>> $rows
     */
    private function buildHtmlTable(string $type, array $rows, array $filters): string
    {
        $headers = $this->excelHeaders($type);
        $html = '<h2>Detail data</h2>';
        if ($rows === []) {
            return $html . '<p>No detail rows in this export for the current filters.</p>';
        }
        $html .= '<table class="data"><thead><tr>';
        foreach ($headers as $h) {
            $html .= '<th>' . htmlspecialchars($h) . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($this->excelRowValues($type, $row) as $v) {
                $html .= '<td>' . htmlspecialchars((string) $v) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        return $html;
    }
}
