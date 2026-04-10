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
        return [
            'date_from'          => $_GET['date_from'] ?? '',
            'date_to'            => $_GET['date_to'] ?? '',
            'user_role'          => $_GET['user_role'] ?? 'all',
            'user_status'        => $_GET['user_status'] ?? 'all',
            'booking_status'     => $_GET['booking_status'] ?? 'all',
            'pay_method'         => $_GET['pay_method'] ?? 'all',
            'pay_status'         => $_GET['pay_status'] ?? 'all',
            'provider_category'  => $_GET['provider_category'] ?? 'all',
            'provider_status'    => $_GET['provider_status'] ?? 'all',
        ];
    }

    private function whitelistSort(string $type, string $sort): string
    {
        $map = [
            'users'     => ['created_at', 'email', 'role'],
            'bookings'  => ['created_at', 'amount', 'status', 'customer', 'type'],
            'payments'  => ['created_at', 'amount', 'status', 'id'],
            'providers' => ['registered_at', 'provider_name', 'role', 'email'],
        ];
        $allowed = $map[$type] ?? ['created_at'];
        return in_array($sort, $allowed, true) ? $sort : $allowed[0];
    }

    public function index(): void
    {
        $this->requireAdmin();

        $generated = isset($_GET['generated']) && $_GET['generated'] === '1';
        $reportType = $_GET['type'] ?? 'bookings';
        $allowedTypes = ['users', 'bookings', 'payments', 'providers'];
        if (!in_array($reportType, $allowedTypes, true)) {
            $reportType = 'bookings';
        }

        $search = trim($_GET['q'] ?? '');
        $sort   = $this->whitelistSort($reportType, $_GET['sort'] ?? 'created_at');
        $dir    = strtoupper($_GET['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
        $page   = max(1, (int) ($_GET['page'] ?? 1));

        $filters = $this->filtersFromRequest();
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
            }

            $df = $filters['date_from'] ?: null;
            $dt = $filters['date_to'] ?: null;
            $charts['bookings_monthly'] = $model->chartBookingsPerMonth($df, $dt);
            $charts['revenue']          = $model->chartRevenueTrend($df, $dt);
            $charts['user_growth']      = $model->chartUserGrowth($df, $dt);
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
        $allowedTypes = ['users', 'bookings', 'payments', 'providers'];
        if (!in_array($reportType, $allowedTypes, true)) {
            $reportType = 'bookings';
        }

        $search  = trim($_GET['q'] ?? '');
        $sort    = $this->whitelistSort($reportType, $_GET['sort'] ?? 'created_at');
        $dir     = strtoupper($_GET['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
        $filters = $this->filtersFromRequest();
        $model   = new Report($this->db);

        $rows = $this->fetchAllForExport($model, $reportType, $filters, $search, $sort, $dir);

        $title = 'CeylonGo Report — ' . ucfirst($reportType);
        $html  = $this->buildHtmlTable($reportType, $rows, $filters);

        $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml('<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
            body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#222;}
            h1{font-size:16px;margin:0 0 12px;}
            .meta{color:#666;font-size:10px;margin-bottom:14px;}
            table{border-collapse:collapse;width:100%;}
            th,td{border:1px solid #ccc;padding:6px 8px;text-align:left;}
            th{background:#f0f0f0;font-weight:bold;}
            tr:nth-child(even){background:#fafafa;}
        </style></head><body>
            <h1>' . htmlspecialchars($title) . '</h1>
            <div class="meta">Generated: ' . htmlspecialchars(date('Y-m-d H:i')) . ' &mdash; Rows: ' . count($rows) . '</div>
            ' . $html . '
        </body></html>');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('ceylongo_report_' . $reportType . '.pdf', ['Attachment' => true]);
        exit();
    }

    public function exportExcel(): void
    {
        $this->requireAdmin();
        $this->loadComposerAutoload();
        if (!class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            http_response_code(500);
            echo 'Excel export requires Composer dependencies. Run: composer install';
            exit();
        }

        $reportType = $_GET['type'] ?? 'bookings';
        $allowedTypes = ['users', 'bookings', 'payments', 'providers'];
        if (!in_array($reportType, $allowedTypes, true)) {
            $reportType = 'bookings';
        }

        $search  = trim($_GET['q'] ?? '');
        $sort    = $this->whitelistSort($reportType, $_GET['sort'] ?? 'created_at');
        $dir     = strtoupper($_GET['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
        $filters = $this->filtersFromRequest();
        $model   = new Report($this->db);

        $rows = $this->fetchAllForExport($model, $reportType, $filters, $search, $sort, $dir);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($reportType, 0, 31));

        $headers = $this->excelHeaders($reportType);
        $col = 1;
        foreach ($headers as $h) {
            $sheet->setCellValueByColumnAndRow($col++, 1, $h);
        }
        $r = 2;
        foreach ($rows as $row) {
            $col = 1;
            foreach ($this->excelRowValues($reportType, $row) as $val) {
                $sheet->setCellValueByColumnAndRow($col++, $r, $val);
            }
            $r++;
            if ($r > self::EXPORT_MAX_ROWS + 1) {
                break;
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ceylongo_report_' . $reportType . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
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
            default:
                return [];
        }
    }

    private function excelHeaders(string $type): array
    {
        switch ($type) {
            case 'users':
                return ['ID', 'Email', 'Role', 'Ref ID', 'Registered', 'Active'];
            case 'bookings':
                return ['Type', 'ID', 'Customer', 'Status', 'Created', 'Amount (LKR)', 'Detail'];
            case 'payments':
                return ['ID', 'Customer', 'Email', 'Package', 'Amount', 'Status', 'Method', 'Created', 'Paid at'];
            case 'providers':
                return ['ID', 'Name', 'Email', 'Category', 'Active', 'Registered'];
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
                    $row['role'] ?? '',
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
            default:
                return [];
        }
    }

    /**
     * @param array<int,array<string,mixed>> $rows
     */
    private function buildHtmlTable(string $type, array $rows, array $filters): string
    {
        if ($rows === []) {
            return '<p>No data for the selected filters.</p>';
        }
        $headers = $this->excelHeaders($type);
        $html = '<table><thead><tr>';
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
