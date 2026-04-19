<?php

class Report
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // ─── Shared helpers ─────────────────────────────────────────────────────

    /**
     * Date range WHERE fragment. Use $suffix when the same SQL statement contains multiple
     * subqueries (e.g. UNION): PDO MySQL does not allow reusing one named placeholder twice.
     */
    /**
     * @param bool $dateOnly If true, compare calendar dates with DATE(column) and bind plain Y-m-d values
     *                       (avoids TIMESTAMP/session timezone edge cases for registration dates).
     */
    private function bindDateRange(array &$params, ?string $from, ?string $to, string $column, string $suffix = '', bool $dateOnly = false): string
    {
        $safe = $suffix !== '' ? preg_replace('/[^a-z0-9_]/i', '', $suffix) : '';
        $df = $safe !== '' ? ":date_from_{$safe}" : ':date_from';
        $dt = $safe !== '' ? ":date_to_{$safe}" : ':date_to';

        $conds = [];
        if ($from !== null && $from !== '') {
            if ($dateOnly) {
                $params[$df] = $from;
                $conds[] = "DATE({$column}) >= {$df}";
            } else {
                $params[$df] = $from . ' 00:00:00';
                $conds[] = "{$column} >= {$df}";
            }
        }
        if ($to !== null && $to !== '') {
            if ($dateOnly) {
                $params[$dt] = $to;
                $conds[] = "DATE({$column}) <= {$dt}";
            } else {
                $params[$dt] = $to . ' 23:59:59';
                $conds[] = "{$column} <= {$dt}";
            }
        }
        return $conds ? (' AND ' . implode(' AND ', $conds)) : '';
    }

    // ─── Users ─────────────────────────────────────────────────────────────

    /** @return array{total:int,active:int,inactive:int} */
    public function summarizeUsers(array $filters): array
    {
        $params = [];
        $where = ' WHERE 1=1 ';
        $where .= $this->userFilters($filters, $params, 'u');

        $sql = "
            SELECT
                COUNT(*) AS total,
                SUM(CASE
                        WHEN u.role = 'tourist'   THEN IFNULL(tu.is_active, 1)
                        WHEN u.role = 'guide'     THEN IFNULL(gu.is_active, 1)
                        WHEN u.role = 'hotel'     THEN IFNULL(hu.is_active, 1)
                        WHEN u.role = 'transport' THEN IFNULL(tr.is_active, 1)
                        ELSE 1
                    END = 1
                ) AS active,
                SUM(CASE
                        WHEN u.role = 'tourist'   THEN IFNULL(tu.is_active, 1)
                        WHEN u.role = 'guide'     THEN IFNULL(gu.is_active, 1)
                        WHEN u.role = 'hotel'     THEN IFNULL(hu.is_active, 1)
                        WHEN u.role = 'transport' THEN IFNULL(tr.is_active, 1)
                        ELSE 1
                    END = 0
                ) AS inactive
            FROM users u
            LEFT JOIN tourist_users tu   ON u.role = 'tourist'   AND u.ref_id = tu.id
            LEFT JOIN guide_users gu     ON u.role = 'guide'     AND u.ref_id = gu.id
            LEFT JOIN hotel_users hu     ON u.role = 'hotel'     AND u.ref_id = hu.id
            LEFT JOIN transport_users tr ON u.role = 'transport' AND u.ref_id = tr.user_id
            {$where}
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'active' => 0, 'inactive' => 0];
        return [
            'total'    => (int) $row['total'],
            'active'   => (int) $row['active'],
            'inactive' => (int) $row['inactive'],
        ];
    }

    private function userFilters(array $filters, array &$params, string $alias = 'u'): string
    {
        $sql = '';
        $role = $filters['user_role'] ?? 'all';
        if ($role !== 'all' && $role !== '') {
            $sql .= " AND {$alias}.role = :urole ";
            $params[':urole'] = $role;
        }
        $st = $filters['user_status'] ?? 'all';
        if ($st === 'active') {
            $sql .= " AND (
                ({$alias}.role = 'tourist'   AND IFNULL(tu.is_active,1) = 1) OR
                ({$alias}.role = 'guide'     AND IFNULL(gu.is_active,1) = 1) OR
                ({$alias}.role = 'hotel'     AND IFNULL(hu.is_active,1) = 1) OR
                ({$alias}.role = 'transport' AND IFNULL(tr.is_active,1) = 1) OR
                ({$alias}.role NOT IN ('tourist','guide','hotel','transport'))
            )";
        } elseif ($st === 'inactive') {
            $sql .= " AND (
                ({$alias}.role = 'tourist'   AND IFNULL(tu.is_active,1) = 0) OR
                ({$alias}.role = 'guide'     AND IFNULL(gu.is_active,1) = 0) OR
                ({$alias}.role = 'hotel'     AND IFNULL(hu.is_active,1) = 0) OR
                ({$alias}.role = 'transport' AND IFNULL(tr.is_active,1) = 0)
            )";
        }
        // Tourist customer report uses profile + login row timestamp so the period matches real sign-ups.
        $dateCol = (($filters['user_role'] ?? 'all') === 'tourist')
            ? 'COALESCE(tu.created_at, u.created_at)'
            : "{$alias}.created_at";
        $sql .= $this->bindDateRange($params, $filters['date_from'] ?? null, $filters['date_to'] ?? null, $dateCol, '', true);
        return $sql;
    }

    /** Whitelist sort keys for users listing */
    private function userSortColumn(string $sort): string
    {
        $map = [
            'created_at' => 'u.created_at',
            'email'      => 'u.email',
        ];
        return $map[$sort] ?? 'u.created_at';
    }

    /**
     * @return array{rows: array<int,array>, total: int}
     */
    public function fetchUsers(array $filters, string $search, string $sort, string $dir, int $page, int $perPage): array
    {
        $params = [];
        $where = ' WHERE 1=1 ';
        $where .= $this->userFilters($filters, $params, 'u');

        if ($search !== '') {
            $like = '%' . $search . '%';
            $where .= ' AND (
                u.email LIKE :q_em OR CAST(u.id AS CHAR) LIKE :q_uid
                OR (u.ref_id IS NOT NULL AND TRIM(u.ref_id) LIKE :q_ref)
            ) ';
            $params[':q_em']  = $like;
            $params[':q_uid'] = $like;
            $params[':q_ref'] = $like;
        }

        $countSql = "
            SELECT COUNT(*) FROM users u
            LEFT JOIN tourist_users tu   ON u.role = 'tourist'   AND u.ref_id = tu.id
            LEFT JOIN guide_users gu     ON u.role = 'guide'     AND u.ref_id = gu.id
            LEFT JOIN hotel_users hu     ON u.role = 'hotel'     AND u.ref_id = hu.id
            LEFT JOIN transport_users tr ON u.role = 'transport' AND u.ref_id = tr.user_id
            {$where}
        ";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $orderCol = $this->userSortColumn($sort);
        $orderDir = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';
        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT
                u.id,
                u.email,
                u.role,
                u.ref_id,
                u.created_at,
                CASE
                    WHEN u.role = 'tourist'   THEN IFNULL(tu.is_active, 1)
                    WHEN u.role = 'guide'     THEN IFNULL(gu.is_active, 1)
                    WHEN u.role = 'hotel'     THEN IFNULL(hu.is_active, 1)
                    WHEN u.role = 'transport' THEN IFNULL(tr.is_active, 1)
                    ELSE 1
                END AS account_active
            FROM users u
            LEFT JOIN tourist_users tu   ON u.role = 'tourist'   AND u.ref_id = tu.id
            LEFT JOIN guide_users gu     ON u.role = 'guide'     AND u.ref_id = gu.id
            LEFT JOIN hotel_users hu     ON u.role = 'hotel'     AND u.ref_id = hu.id
            LEFT JOIN transport_users tr ON u.role = 'transport' AND u.ref_id = tr.user_id
            {$where}
            ORDER BY {$orderCol} {$orderDir}
            LIMIT :lim OFFSET :off
        ";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['rows' => $rows, 'total' => $total];
    }

    // ─── Bookings (package + custom trips) ─────────────────────────────────

    /** @return array{total:int,pending:int,confirmed:int,cancelled:int,total_revenue:float} */
    public function summarizeBookings(array $filters): array
    {
        $df = $filters['date_from'] ?? null;
        $dt = $filters['date_to'] ?? null;
        // One unique named placeholder set per subquery — PDO does not allow reusing :name in one statement.
        $params = [];
        $datePb = [];
        $dateTr = [];
        for ($i = 1; $i <= 5; $i++) {
            $datePb[$i] = $this->bindDateRange($params, $df, $dt, 'pb.created_at', 'pb' . $i);
            $dateTr[$i] = $this->bindDateRange($params, $df, $dt, 't.created_at', 'tr' . $i);
        }

        $bf = $filters['booking_status'] ?? 'all';
        $extraPb = '';
        $extraTr = '';
        if ($bf === 'pending') {
            $extraPb = " AND pb.status = 'pending' ";
            $extraTr = " AND t.status = 'pending' ";
        } elseif ($bf === 'confirmed') {
            $extraPb = " AND pb.status = 'approved' ";
            $extraTr = " AND t.status IN ('confirmed','completed') ";
        } elseif ($bf === 'cancelled') {
            $extraPb = " AND pb.status IN ('cancelled','rejected') ";
            $extraTr = " AND (t.status = 'cancelled' OR t.refund_requested_at IS NOT NULL) ";
        }

        $sql = "
            SELECT
                (SELECT COUNT(*) FROM package_bookings pb WHERE 1=1 {$datePb[1]} {$extraPb})
              + (SELECT COUNT(*) FROM trips t WHERE 1=1 {$dateTr[1]} {$extraTr}) AS total,
                (SELECT COUNT(*) FROM package_bookings pb WHERE 1=1 {$datePb[2]} AND pb.status = 'pending')
              + (SELECT COUNT(*) FROM trips t WHERE 1=1 {$dateTr[2]} AND t.status = 'pending') AS pending,
                (SELECT COUNT(*) FROM package_bookings pb WHERE 1=1 {$datePb[3]} AND pb.status = 'approved')
              + (SELECT COUNT(*) FROM trips t WHERE 1=1 {$dateTr[3]} AND t.status IN ('confirmed','completed')) AS confirmed,
                (SELECT COUNT(*) FROM package_bookings pb WHERE 1=1 {$datePb[4]} AND pb.status IN ('cancelled','rejected'))
              + (SELECT COUNT(*) FROM trips t WHERE 1=1 {$dateTr[4]} AND (t.status = 'cancelled' OR t.refund_requested_at IS NOT NULL)) AS cancelled,
                COALESCE((SELECT SUM(pb.total_amount) FROM package_bookings pb WHERE 1=1 {$datePb[5]} {$extraPb}),0)
              + COALESCE((SELECT SUM(CASE WHEN t.status IN ('confirmed','completed') THEN t.budget_lkr ELSE 0 END) FROM trips t WHERE 1=1 {$dateTr[5]} {$extraTr}),0)
                AS total_revenue
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'total'          => (int) ($r['total'] ?? 0),
            'pending'        => (int) ($r['pending'] ?? 0),
            'confirmed'      => (int) ($r['confirmed'] ?? 0),
            'cancelled'      => (int) ($r['cancelled'] ?? 0),
            'total_revenue'  => (float) ($r['total_revenue'] ?? 0),
        ];
    }

    private function bookingSortColumn(string $sort): string
    {
        $map = [
            'created_at' => 'created_at',
            'amount'     => 'amount',
            'status'     => 'status',
            'customer'   => 'customer',
            'type'       => 'booking_type',
        ];
        return $map[$sort] ?? 'created_at';
    }

    /**
     * @return array{rows: array<int,array>, total: int}
     */
    public function fetchBookings(array $filters, string $search, string $sort, string $dir, int $page, int $perPage): array
    {
        $bf = $filters['booking_status'] ?? 'all';
        $params = [];
        $unionParams = [];

        $pbWhere = ' WHERE 1=1 ';
        $pbWhere .= $this->bindDateRange($unionParams, $filters['date_from'] ?? null, $filters['date_to'] ?? null, 'pb.created_at', 'pb');
        if ($bf === 'pending') {
            $pbWhere .= " AND pb.status = 'pending' ";
        } elseif ($bf === 'confirmed') {
            $pbWhere .= " AND pb.status = 'approved' ";
        } elseif ($bf === 'cancelled') {
            $pbWhere .= " AND pb.status IN ('cancelled','rejected') ";
        }

        $trWhere = ' WHERE 1=1 ';
        $trParams = [];
        $trWhere .= $this->bindDateRange($trParams, $filters['date_from'] ?? null, $filters['date_to'] ?? null, 't.created_at', 'tr');
        if ($bf === 'pending') {
            $trWhere .= " AND t.status = 'pending' ";
        } elseif ($bf === 'confirmed') {
            $trWhere .= " AND t.status IN ('confirmed','completed') ";
        } elseif ($bf === 'cancelled') {
            $trWhere .= " AND (t.status = 'cancelled' OR t.refund_requested_at IS NOT NULL) ";
        }
        $unionParams = array_merge($unionParams, $trParams);

        $searchPb = '';
        $searchTr = '';
        if ($search !== '') {
            $like = '%' . $search . '%';
            // Unique placeholders — :sq cannot be repeated in one MySQL prepared statement.
            $searchPb = ' AND (pb.fullname LIKE :sq_pb_fn OR pb.email LIKE :sq_pb_em OR CAST(pb.id AS CHAR) LIKE :sq_pb_id) ';
            $searchTr = ' AND (t.customer_name LIKE :sq_tr_nm OR CAST(t.id AS CHAR) LIKE :sq_tr_id) ';
            $unionParams[':sq_pb_fn'] = $like;
            $unionParams[':sq_pb_em'] = $like;
            $unionParams[':sq_pb_id'] = $like;
            $unionParams[':sq_tr_nm'] = $like;
            $unionParams[':sq_tr_id'] = $like;
        }

        $innerSql = "
            SELECT
                'package' AS booking_type,
                pb.id AS row_id,
                pb.fullname AS customer,
                pb.status AS status,
                pb.created_at,
                pb.total_amount AS amount,
                pb.package_name AS detail
            FROM package_bookings pb
            {$pbWhere} {$searchPb}
            UNION ALL
            SELECT
                'custom' AS booking_type,
                t.id AS row_id,
                t.customer_name AS customer,
                t.status AS status,
                t.created_at,
                t.budget_lkr AS amount,
                t.destination AS detail
            FROM trips t
            {$trWhere} {$searchTr}
        ";

        $countSql = "SELECT COUNT(*) FROM ( {$innerSql} ) c";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($unionParams);
        $total = (int) $stmt->fetchColumn();

        $orderCol = $this->bookingSortColumn($sort);
        $orderDir = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM ( {$innerSql} ) u
            ORDER BY {$orderCol} {$orderDir}
            LIMIT :lim OFFSET :off";
        $stmt = $this->db->prepare($sql);
        foreach ($unionParams as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['rows' => $rows, 'total' => $total];
    }

    // ─── Payments (package_bookings — primary payment records in this app) ─

    private function paymentMethodExpr(): string
    {
        return "CASE
            WHEN pb.bank_transfer_slip_path IS NOT NULL AND pb.bank_transfer_slip_path <> '' THEN 'bank_transfer'
            WHEN pb.bank_transfer_submitted_at IS NOT NULL THEN 'bank_transfer'
            ELSE 'online'
        END";
    }

    /**
     * Normalizes pay_status for payments reports: aligns with admin payments.php Payment column
     * (Awaiting / Received / Refunded). Legacy value "paid" maps to "received".
     */
    private function normalizePaymentDisplayFilter(array $filters): string
    {
        $raw = strtolower(trim((string) ($filters['pay_status'] ?? 'all')));
        if ($raw === '' || $raw === 'all') {
            return 'all';
        }
        if ($raw === 'paid') {
            return 'received';
        }
        if (in_array($raw, ['awaiting', 'received', 'refunded'], true)) {
            return $raw;
        }

        return 'all';
    }

    /** package_bookings — matches getPaymentDisplay() in views/admin/payments.php */
    private function sqlPaymentDisplayFilterPackage(string $pst): string
    {
        switch ($pst) {
            case 'refunded':
                return ' AND pb.refund_approved_at IS NOT NULL ';
            case 'received':
                return ' AND pb.refund_approved_at IS NULL AND pb.paid_at IS NOT NULL ';
            case 'awaiting':
                return " AND pb.refund_approved_at IS NULL AND pb.paid_at IS NULL AND pb.status NOT IN ('cancelled','rejected') ";
            default:
                return '';
        }
    }

    /** trips — same payment display rules as package rows */
    private function sqlPaymentDisplayFilterTrip(string $pst): string
    {
        switch ($pst) {
            case 'refunded':
                return ' AND t.refund_approved_at IS NOT NULL ';
            case 'received':
                return ' AND t.refund_approved_at IS NULL AND t.paid_at IS NOT NULL ';
            case 'awaiting':
                return " AND t.refund_approved_at IS NULL AND t.paid_at IS NULL AND t.status <> 'cancelled' ";
            default:
                return '';
        }
    }

    /** @return array{total:int,total_revenue:float,paid:int,pending:int} */
    public function summarizePayments(array $filters): array
    {
        $params = [];
        $where = ' WHERE 1=1 ';
        $where .= $this->bindDateRange($params, $filters['date_from'] ?? null, $filters['date_to'] ?? null, 'pb.created_at');

        $method = $filters['pay_method'] ?? 'all';
        if ($method === 'bank_transfer') {
            $where .= " AND ({$this->paymentMethodExpr()}) = 'bank_transfer' ";
        } elseif ($method === 'online') {
            $where .= " AND ({$this->paymentMethodExpr()}) = 'online' ";
        }

        $pst = $this->normalizePaymentDisplayFilter($filters);
        $where .= $this->sqlPaymentDisplayFilterPackage($pst);

        $sql = "
            SELECT
                COUNT(*) AS total,
                COALESCE(SUM(CASE WHEN pb.status = 'paid' THEN pb.total_amount ELSE 0 END), 0) AS paid_amount,
                SUM(CASE WHEN pb.status = 'paid' THEN 1 ELSE 0 END) AS paid_cnt,
                SUM(CASE WHEN pb.status IN ('pending','approved') THEN 1 ELSE 0 END) AS pending_cnt
            FROM package_bookings pb
            {$where}
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'total'         => (int) ($r['total'] ?? 0),
            'total_revenue' => (float) ($r['paid_amount'] ?? 0),
            'paid'          => (int) ($r['paid_cnt'] ?? 0),
            'pending'       => (int) ($r['pending_cnt'] ?? 0),
        ];
    }

    private function paymentSortColumn(string $sort): string
    {
        $map = [
            'created_at' => 'pb.created_at',
            'amount'     => 'pb.total_amount',
            'status'     => 'pb.status',
            'id'         => 'pb.id',
        ];
        return $map[$sort] ?? 'pb.created_at';
    }

    /**
     * @return array{rows: array<int,array>, total: int}
     */
    public function fetchPayments(array $filters, string $search, string $sort, string $dir, int $page, int $perPage): array
    {
        $params = [];
        $where = ' WHERE 1=1 ';
        $where .= $this->bindDateRange($params, $filters['date_from'] ?? null, $filters['date_to'] ?? null, 'pb.created_at');

        $method = $filters['pay_method'] ?? 'all';
        if ($method === 'bank_transfer') {
            $where .= " AND ({$this->paymentMethodExpr()}) = 'bank_transfer' ";
        } elseif ($method === 'online') {
            $where .= " AND ({$this->paymentMethodExpr()}) = 'online' ";
        }

        $pst = $this->normalizePaymentDisplayFilter($filters);
        $where .= $this->sqlPaymentDisplayFilterPackage($pst);

        if ($search !== '') {
            $where .= ' AND (pb.fullname LIKE :sq OR pb.email LIKE :sq OR pb.package_name LIKE :sq OR CAST(pb.id AS CHAR) LIKE :sq) ';
            $params[':sq'] = '%' . $search . '%';
        }

        $countSql = "SELECT COUNT(*) FROM package_bookings pb {$where}";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $orderCol = $this->paymentSortColumn($sort);
        $orderDir = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';
        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT
                pb.id,
                pb.fullname,
                pb.email,
                pb.package_name,
                pb.total_amount,
                pb.status,
                pb.created_at,
                pb.paid_at,
                {$this->paymentMethodExpr()} AS pay_method
            FROM package_bookings pb
            {$where}
            ORDER BY {$orderCol} {$orderDir}
            LIMIT :lim OFFSET :off
        ";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['rows' => $rows, 'total' => $total];
    }

    // ─── Service providers (same union as admin service page) ──────────────

    /** @return array{total:int,active:int,inactive:int,guide:int,hotel:int,transport:int} */
    public function summarizeProviders(array $filters): array
    {
        $rows = $this->fetchProvidersAll($filters);
        $stats = ['total' => count($rows), 'active' => 0, 'inactive' => 0, 'guide' => 0, 'hotel' => 0, 'transport' => 0];
        foreach ($rows as $r) {
            if (!empty($r['is_active'])) {
                $stats['active']++;
            } else {
                $stats['inactive']++;
            }
            if (($r['role'] ?? '') === 'guide') {
                $stats['guide']++;
            }
            if (($r['role'] ?? '') === 'hotel') {
                $stats['hotel']++;
            }
            if (($r['role'] ?? '') === 'transport') {
                $stats['transport']++;
            }
        }
        return $stats;
    }

    /**
     * Full list for exports (respects filters, no pagination).
     * @return array<int,array>
     */
    public function fetchProvidersAll(array $filters): array
    {
        $cat = $filters['provider_category'] ?? 'all';
        $st  = $filters['provider_status'] ?? 'all';

        $whereStatus = '';
        if ($st === 'active') {
            $whereStatus = ' AND is_active = 1 ';
        } elseif ($st === 'inactive') {
            $whereStatus = ' AND is_active = 0 ';
        }

        $parts = [];
        if ($cat === 'all' || $cat === 'guide') {
            $parts[] = "
                SELECT g.id AS id,
                    CONCAT(g.first_name, ' ', g.last_name) AS provider_name,
                    u.email,
                    u.role,
                    g.is_active,
                    u.created_at AS registered_at
                FROM users u
                JOIN guide_users g ON u.ref_id = g.id
                WHERE u.role = 'guide' {$whereStatus}
            ";
        }
        if ($cat === 'all' || $cat === 'transport') {
            $parts[] = "
                SELECT t.user_id AS id,
                    t.full_name AS provider_name,
                    u.email,
                    u.role,
                    t.is_active,
                    u.created_at AS registered_at
                FROM users u
                JOIN transport_users t ON u.ref_id = t.user_id
                WHERE u.role = 'transport' {$whereStatus}
            ";
        }
        if ($cat === 'all' || $cat === 'hotel') {
            $parts[] = "
                SELECT h.id AS id,
                    h.hotel_name AS provider_name,
                    u.email,
                    u.role,
                    h.is_active,
                    u.created_at AS registered_at
                FROM users u
                JOIN hotel_users h ON u.ref_id = h.id
                WHERE u.role = 'hotel' {$whereStatus}
            ";
        }

        if (empty($parts)) {
            return [];
        }

        $sql = implode(' UNION ALL ', $parts);
        $params = [];
        $dateFilter = '';
        $df = $filters['date_from'] ?? null;
        $dt = $filters['date_to'] ?? null;
        if (($df !== null && $df !== '') || ($dt !== null && $dt !== '')) {
            // apply on outer query
            $sql = "SELECT * FROM ( {$sql} ) z WHERE 1=1 ";
            $sql .= $this->bindDateRange($params, $df, $dt, 'z.registered_at', '', true);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function providerSortColumn(string $sort): string
    {
        $map = [
            'registered_at' => 'registered_at',
            'provider_name' => 'provider_name',
            'role'          => 'role',
            'email'         => 'email',
        ];
        return $map[$sort] ?? 'registered_at';
    }

    /**
     * @return array{rows: array<int,array>, total: int}
     */
    public function fetchProviders(array $filters, string $search, string $sort, string $dir, int $page, int $perPage): array
    {
        $all = $this->fetchProvidersAll($filters);
        if ($search !== '') {
            $q = mb_strtolower($search);
            $all = array_values(array_filter($all, function ($r) use ($q) {
                $hay = mb_strtolower(
                    ($r['provider_name'] ?? '') . ' ' . ($r['email'] ?? '') . ' ' . ($r['role'] ?? '') . ' ' . ($r['id'] ?? '')
                );
                return strpos($hay, $q) !== false;
            }));
        }
        $total = count($all);
        $col = $this->providerSortColumn($sort);
        $orderDir = strtoupper($dir) === 'ASC' ? 1 : -1;
        usort($all, function ($a, $b) use ($col, $orderDir) {
            $va = $a[$col] ?? '';
            $vb = $b[$col] ?? '';
            if ($va == $vb) {
                return 0;
            }
            $cmp = $va < $vb ? -1 : 1;
            return $cmp * $orderDir;
        });
        $offset = ($page - 1) * $perPage;
        $rows = array_slice($all, $offset, $perPage);

        return ['rows' => $rows, 'total' => $total];
    }

    // ─── Charts (used when a report has been generated) ───────────────────

    /**
     * Bookings per month (package + trips) within optional date range.
     * @return array{labels: string[], counts: int[]}
     */
    public function chartBookingsPerMonth(?string $dateFrom, ?string $dateTo): array
    {
        $params = [];
        $pbDate = $this->bindDateRange($params, $dateFrom, $dateTo, 'pb.created_at', 'pb');
        $params2 = [];
        $trDate = $this->bindDateRange($params2, $dateFrom, $dateTo, 't.created_at', 'tr');
        $mergeParams = array_merge($params, $params2);

        $sql = "
            SELECT ym, SUM(cnt) AS c FROM (
                SELECT DATE_FORMAT(pb.created_at, '%Y-%m') AS ym, COUNT(*) AS cnt
                FROM package_bookings pb WHERE 1=1 {$pbDate}
                GROUP BY ym
                UNION ALL
                SELECT DATE_FORMAT(t.created_at, '%Y-%m') AS ym, COUNT(*) AS cnt
                FROM trips t WHERE 1=1 {$trDate}
                GROUP BY ym
            ) x GROUP BY ym ORDER BY ym ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($mergeParams);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $labels = array_column($rows, 'ym');
        $counts = array_map('intval', array_column($rows, 'c'));
        return ['labels' => $labels, 'counts' => $counts];
    }

    /**
     * Revenue trend: package totals + trip budget for completed trips.
     * @return array{labels: string[], amounts: float[]}
     */
    public function chartRevenueTrend(?string $dateFrom, ?string $dateTo): array
    {
        $params = [];
        $pbDate = $this->bindDateRange($params, $dateFrom, $dateTo, 'pb.created_at', 'pb');
        $params2 = [];
        $trDate = $this->bindDateRange($params2, $dateFrom, $dateTo, 't.created_at', 'tr');
        $mergeParams = array_merge($params, $params2);

        $sql = "
            SELECT ym, SUM(amt) AS a FROM (
                SELECT DATE_FORMAT(pb.created_at, '%Y-%m') AS ym, SUM(pb.total_amount) AS amt
                FROM package_bookings pb WHERE 1=1 {$pbDate}
                GROUP BY ym
                UNION ALL
                SELECT DATE_FORMAT(t.created_at, '%Y-%m') AS ym,
                       SUM(CASE WHEN t.status = 'completed' THEN t.budget_lkr ELSE 0 END) AS amt
                FROM trips t WHERE 1=1 {$trDate}
                GROUP BY ym
            ) x GROUP BY ym ORDER BY ym ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($mergeParams);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $labels = array_column($rows, 'ym');
        $amounts = array_map('floatval', array_column($rows, 'a'));
        return ['labels' => $labels, 'amounts' => $amounts];
    }

    /**
     * New accounts per month (users table). Optional role (e.g. tourist-only for the Customers report).
     *
     * @return array{labels: string[], counts: int[]}
     */
    public function chartUserGrowth(?string $dateFrom, ?string $dateTo, ?string $role = null): array
    {
        $params = [];
        $where = ' WHERE 1=1 ' . $this->bindDateRange($params, $dateFrom, $dateTo, 'created_at', '', true);
        if ($role !== null && $role !== '') {
            $where .= ' AND role = :ug_role ';
            $params[':ug_role'] = $role;
        }
        $sql = "
            SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS c
            FROM users
            {$where}
            GROUP BY ym
            ORDER BY ym ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return [
            'labels' => array_column($rows, 'ym'),
            'counts' => array_map('intval', array_column($rows, 'c')),
        ];
    }

    /**
     * Single Chart.js payload for Reports & Analysis (matches current filters, not the dashboard).
     *
     * @return array{title: string, labels: array, values: array<float|int>, value_kind: 'count'|'currency', chart_kind: 'bar'|'line'}|null
     */
    public function chartForReportsPage(string $reportType, array $filters): ?array
    {
        switch ($reportType) {
            case 'users':
                $c = $this->chartTouristCustomersPerMonth($filters);
                if ($c['labels'] === []) {
                    return null;
                }
                $ust = $filters['user_status'] ?? 'all';
                $ustLabel = $ust === 'all' ? 'all accounts' : $ust;

                return [
                    'title'      => 'New customer registrations by month (' . $ustLabel . ')',
                    'labels'     => $c['labels'],
                    'values'     => $c['counts'],
                    'value_kind' => 'count',
                    'chart_kind' => 'bar',
                ];
            case 'bookings':
                $c = $this->chartBookingsPerMonthFiltered($filters);
                if ($c['labels'] === []) {
                    return null;
                }
                $bs = $filters['booking_status'] ?? 'all';
                $suffix = $bs === 'all' ? '' : (' (' . $bs . ')');

                return [
                    'title'      => 'Bookings by month' . $suffix,
                    'labels'     => $c['labels'],
                    'values'     => $c['counts'],
                    'value_kind' => 'count',
                    'chart_kind' => 'bar',
                ];
            case 'payments':
                $c = $this->chartPaymentsRevenuePerMonthFiltered($filters);
                if ($c['labels'] === []) {
                    return null;
                }
                $src = strtolower((string) ($filters['pay_source'] ?? 'all'));
                $scope = [
                    'all'     => 'package + custom trips',
                    'package' => 'package bookings',
                    'trip'    => 'custom trips',
                ];
                $st = $scope[$src] ?? $src;

                return [
                    'title'      => 'Revenue by month (LKR) — ' . $st,
                    'labels'     => $c['labels'],
                    'values'     => $c['amounts'],
                    'value_kind' => 'currency',
                    'chart_kind' => 'line',
                ];
            case 'providers':
                $c = $this->chartProvidersRegisteredPerMonth($filters);
                if ($c['labels'] === []) {
                    return null;
                }
                $cat = $filters['provider_category'] ?? 'all';
                $pst = $filters['provider_status'] ?? 'all';
                $catLabel = $cat === 'all' ? 'all categories' : $cat;
                $stLabel = $pst === 'all' ? 'all statuses' : $pst;

                return [
                    'title'      => 'New provider registrations by month (' . $catLabel . ', ' . $stLabel . ')',
                    'labels'     => $c['labels'],
                    'values'     => $c['counts'],
                    'value_kind' => 'count',
                    'chart_kind' => 'bar',
                ];
            default:
                return null;
        }
    }

    /**
     * Same scope as the Users report: tourists only, account status + date on COALESCE(tu.created_at, u.created_at).
     *
     * @return array{labels: string[], counts: int[]}
     */
    private function chartTouristCustomersPerMonth(array $filters): array
    {
        $params = [];
        $where = " WHERE u.role = 'tourist' ";
        $st = $filters['user_status'] ?? 'all';
        if ($st === 'active') {
            $where .= ' AND IFNULL(tu.is_active, 1) = 1 ';
        } elseif ($st === 'inactive') {
            $where .= ' AND IFNULL(tu.is_active, 1) = 0 ';
        }
        $dateCol = 'COALESCE(tu.created_at, u.created_at)';
        $where .= $this->bindDateRange($params, $filters['date_from'] ?? null, $filters['date_to'] ?? null, $dateCol, '', true);

        $sql = "
            SELECT DATE_FORMAT({$dateCol}, '%Y-%m') AS ym, COUNT(*) AS c
            FROM users u
            LEFT JOIN tourist_users tu ON u.role = 'tourist' AND u.ref_id = tu.id
            {$where}
            GROUP BY ym
            ORDER BY ym ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'labels' => array_column($rows, 'ym'),
            'counts' => array_map('intval', array_column($rows, 'c')),
        ];
    }

    /**
     * Bookings per month with the same status filter as fetchBookings.
     *
     * @return array{labels: string[], counts: int[]}
     */
    private function chartBookingsPerMonthFiltered(array $filters): array
    {
        $bf = $filters['booking_status'] ?? 'all';
        $params = [];
        $pbWhere = ' WHERE 1=1 ';
        $pbWhere .= $this->bindDateRange($params, $filters['date_from'] ?? null, $filters['date_to'] ?? null, 'pb.created_at', 'pb');
        if ($bf === 'pending') {
            $pbWhere .= " AND pb.status = 'pending' ";
        } elseif ($bf === 'confirmed') {
            $pbWhere .= " AND pb.status = 'approved' ";
        } elseif ($bf === 'cancelled') {
            $pbWhere .= " AND pb.status IN ('cancelled','rejected') ";
        }

        $trParams = [];
        $trWhere = ' WHERE 1=1 ';
        $trWhere .= $this->bindDateRange($trParams, $filters['date_from'] ?? null, $filters['date_to'] ?? null, 't.created_at', 'tr');
        if ($bf === 'pending') {
            $trWhere .= " AND t.status = 'pending' ";
        } elseif ($bf === 'confirmed') {
            $trWhere .= " AND t.status IN ('confirmed','completed') ";
        } elseif ($bf === 'cancelled') {
            $trWhere .= " AND (t.status = 'cancelled' OR t.refund_requested_at IS NOT NULL) ";
        }

        $mergeParams = array_merge($params, $trParams);

        $sql = "
            SELECT ym, SUM(cnt) AS c FROM (
                SELECT DATE_FORMAT(pb.created_at, '%Y-%m') AS ym, COUNT(*) AS cnt
                FROM package_bookings pb
                {$pbWhere}
                GROUP BY ym
                UNION ALL
                SELECT DATE_FORMAT(t.created_at, '%Y-%m') AS ym, COUNT(*) AS cnt
                FROM trips t
                {$trWhere}
                GROUP BY ym
            ) x GROUP BY ym ORDER BY ym ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($mergeParams);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'labels' => array_column($rows, 'ym'),
            'counts' => array_map('intval', array_column($rows, 'c')),
        ];
    }

    /**
     * Monthly revenue aligned with payments report filters (pay_source, method, status on packages).
     *
     * @return array{labels: string[], amounts: float[]}
     */
    private function chartPaymentsRevenuePerMonthFiltered(array $filters): array
    {
        $src = strtolower(trim((string) ($filters['pay_source'] ?? 'all')));
        if (!in_array($src, ['all', 'package', 'trip'], true)) {
            $src = 'all';
        }

        if ($src === 'package') {
            return $this->chartPackagePaymentRevenuePerMonth($filters);
        }
        if ($src === 'trip') {
            return $this->chartTripRevenuePerMonth($filters);
        }

        $pkg = $this->chartPackagePaymentRevenuePerMonth($filters);
        $trp = $this->chartTripRevenuePerMonth($filters);
        $byYm = [];
        foreach ($pkg['labels'] as $i => $ym) {
            $byYm[$ym] = ($byYm[$ym] ?? 0) + (float) ($pkg['amounts'][$i] ?? 0);
        }
        foreach ($trp['labels'] as $i => $ym) {
            $byYm[$ym] = ($byYm[$ym] ?? 0) + (float) ($trp['amounts'][$i] ?? 0);
        }
        ksort($byYm);
        $labels = array_keys($byYm);
        $amounts = array_values($byYm);

        return ['labels' => $labels, 'amounts' => $amounts];
    }

    /**
     * @return array{labels: string[], amounts: float[]}
     */
    private function chartPackagePaymentRevenuePerMonth(array $filters): array
    {
        $params = [];
        $where = ' WHERE 1=1 ';
        $where .= $this->bindDateRange($params, $filters['date_from'] ?? null, $filters['date_to'] ?? null, 'pb.created_at');

        $method = $filters['pay_method'] ?? 'all';
        if ($method === 'bank_transfer') {
            $where .= " AND ({$this->paymentMethodExpr()}) = 'bank_transfer' ";
        } elseif ($method === 'online') {
            $where .= " AND ({$this->paymentMethodExpr()}) = 'online' ";
        }

        $pst = $this->normalizePaymentDisplayFilter($filters);
        $where .= $this->sqlPaymentDisplayFilterPackage($pst);

        $sql = "
            SELECT DATE_FORMAT(pb.created_at, '%Y-%m') AS ym,
                   COALESCE(SUM(CASE WHEN pb.status = 'paid' THEN pb.total_amount ELSE 0 END), 0) AS amt
            FROM package_bookings pb
            {$where}
            GROUP BY ym
            ORDER BY ym ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'labels'  => array_column($rows, 'ym'),
            'amounts' => array_map('floatval', array_column($rows, 'amt')),
        ];
    }

    /**
     * @return array{labels: string[], amounts: float[]}
     */
    private function chartTripRevenuePerMonth(array $filters): array
    {
        $params = [];
        $where = ' WHERE 1=1 ';
        $where .= $this->bindDateRange($params, $filters['date_from'] ?? null, $filters['date_to'] ?? null, 't.created_at', '', true);

        $pst = $this->normalizePaymentDisplayFilter($filters);
        $where .= $this->sqlPaymentDisplayFilterTrip($pst);

        $sql = "
            SELECT DATE_FORMAT(t.created_at, '%Y-%m') AS ym,
                   COALESCE(SUM(CASE WHEN t.status IN ('confirmed','completed') THEN t.budget_lkr ELSE 0 END), 0) AS amt
            FROM trips t
            {$where}
            GROUP BY ym
            ORDER BY ym ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'labels'  => array_column($rows, 'ym'),
            'amounts' => array_map('floatval', array_column($rows, 'amt')),
        ];
    }

    /**
     * @return array{labels: string[], counts: int[]}
     */
    private function chartProvidersRegisteredPerMonth(array $filters): array
    {
        $cat = $filters['provider_category'] ?? 'all';
        $st  = $filters['provider_status'] ?? 'all';

        $whereStatus = '';
        if ($st === 'active') {
            $whereStatus = ' AND is_active = 1 ';
        } elseif ($st === 'inactive') {
            $whereStatus = ' AND is_active = 0 ';
        }

        $parts = [];
        if ($cat === 'all' || $cat === 'guide') {
            $parts[] = "
                SELECT u.created_at AS registered_at
                FROM users u
                JOIN guide_users g ON u.ref_id = g.id
                WHERE u.role = 'guide' {$whereStatus}
            ";
        }
        if ($cat === 'all' || $cat === 'transport') {
            $parts[] = "
                SELECT u.created_at AS registered_at
                FROM users u
                JOIN transport_users t ON u.ref_id = t.user_id
                WHERE u.role = 'transport' {$whereStatus}
            ";
        }
        if ($cat === 'all' || $cat === 'hotel') {
            $parts[] = "
                SELECT u.created_at AS registered_at
                FROM users u
                JOIN hotel_users h ON u.ref_id = h.id
                WHERE u.role = 'hotel' {$whereStatus}
            ";
        }

        if ($parts === []) {
            return ['labels' => [], 'counts' => []];
        }

        $inner = implode(' UNION ALL ', $parts);
        $params = [];
        $sql = "
            SELECT DATE_FORMAT(z.registered_at, '%Y-%m') AS ym, COUNT(*) AS c
            FROM ( {$inner} ) z
            WHERE 1=1
        ";
        $sql .= $this->bindDateRange($params, $filters['date_from'] ?? null, $filters['date_to'] ?? null, 'z.registered_at', '', true);
        $sql .= ' GROUP BY ym ORDER BY ym ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'labels' => array_column($rows, 'ym'),
            'counts' => array_map('intval', array_column($rows, 'c')),
        ];
    }

    // ─── Tour packages (catalog) ───────────────────────────────────────────

    /** @return array{total:int,avg_price:float,trending:int} */
    public function summarizePackages(array $filters): array
    {
        $params = [];
        $where = ' WHERE 1=1 ';
        $where .= $this->bindDateRange($params, $filters['date_from'] ?? null, $filters['date_to'] ?? null, 'created_at', '', true);

        $sql = "
            SELECT
                COUNT(*) AS total,
                COALESCE(AVG(price), 0) AS avg_price,
                SUM(CASE WHEN trending = 1 THEN 1 ELSE 0 END) AS trending
            FROM packages
            {$where}
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $r = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'avg_price' => 0, 'trending' => 0];
        return [
            'total'    => (int) ($r['total'] ?? 0),
            'avg_price'=> (float) ($r['avg_price'] ?? 0),
            'trending' => (int) ($r['trending'] ?? 0),
        ];
    }

    private function packageSortColumn(string $sort): string
    {
        $map = [
            'created_at' => 'p.created_at',
            'title'      => 'p.title',
            'price'      => 'p.price',
        ];
        return $map[$sort] ?? 'p.created_at';
    }

    /**
     * @return array{rows: array<int,array>, total: int}
     */
    public function fetchPackages(array $filters, string $search, string $sort, string $dir, int $page, int $perPage): array
    {
        $params = [];
        $where = ' WHERE 1=1 ';
        $where .= $this->bindDateRange($params, $filters['date_from'] ?? null, $filters['date_to'] ?? null, 'p.created_at', '', true);

        if ($search !== '') {
            $like = '%' . $search . '%';
            $where .= ' AND (p.title LIKE :pq OR p.location LIKE :pq2 OR p.category LIKE :pq3 OR CAST(p.id AS CHAR) LIKE :pq4) ';
            $params[':pq'] = $like;
            $params[':pq2'] = $like;
            $params[':pq3'] = $like;
            $params[':pq4'] = $like;
        }

        $countSql = "SELECT COUNT(*) FROM packages p {$where}";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $orderCol = $this->packageSortColumn($sort);
        $orderDir = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';
        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT
                p.id,
                p.title,
                p.location,
                p.category,
                p.price,
                p.rating,
                p.reviews,
                p.trending,
                p.created_at
            FROM packages p
            {$where}
            ORDER BY {$orderCol} {$orderDir}
            LIMIT :lim OFFSET :off
        ";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['rows' => $rows, 'total' => $total];
    }

    // ─── Custom trip payments (trips table) ────────────────────────────────

    /** @return array{total:int,completed_value:float,pending:int,paid:int} */
    public function summarizeTripPayments(array $filters): array
    {
        $params = [];
        $where = ' WHERE 1=1 ';
        $where .= $this->bindDateRange($params, $filters['date_from'] ?? null, $filters['date_to'] ?? null, 't.created_at', '', true);

        $pst = $this->normalizePaymentDisplayFilter($filters);
        $where .= $this->sqlPaymentDisplayFilterTrip($pst);

        $sql = "
            SELECT
                COUNT(*) AS total,
                COALESCE(SUM(CASE WHEN t.status IN ('confirmed','completed') THEN t.budget_lkr ELSE 0 END), 0) AS completed_value,
                SUM(CASE WHEN t.status = 'pending' THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN t.paid_at IS NOT NULL THEN 1 ELSE 0 END) AS paid_cnt
            FROM trips t
            {$where}
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $r = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'completed_value' => 0, 'pending' => 0, 'paid_cnt' => 0];
        return [
            'total'           => (int) ($r['total'] ?? 0),
            'completed_value' => (float) ($r['completed_value'] ?? 0),
            'pending'         => (int) ($r['pending'] ?? 0),
            'paid'            => (int) ($r['paid_cnt'] ?? 0),
        ];
    }

    private function tripPaymentSortColumn(string $sort): string
    {
        $map = [
            'created_at' => 't.created_at',
            'status'     => 't.status',
            'id'         => 't.id',
            'amount'     => 't.budget_lkr',
        ];
        return $map[$sort] ?? 't.created_at';
    }

    /**
     * @return array{rows: array<int,array>, total: int}
     */
    public function fetchTripPayments(array $filters, string $search, string $sort, string $dir, int $page, int $perPage): array
    {
        $params = [];
        $where = ' WHERE 1=1 ';
        $where .= $this->bindDateRange($params, $filters['date_from'] ?? null, $filters['date_to'] ?? null, 't.created_at', '', true);

        if ($search !== '') {
            $like = '%' . $search . '%';
            $where .= ' AND (
                t.customer_name LIKE :tq OR t.destination LIKE :tq2 OR CAST(t.id AS CHAR) LIKE :tq3
            ) ';
            $params[':tq'] = $like;
            $params[':tq2'] = $like;
            $params[':tq3'] = $like;
        }

        $pst = $this->normalizePaymentDisplayFilter($filters);
        $where .= $this->sqlPaymentDisplayFilterTrip($pst);

        $countSql = "SELECT COUNT(*) FROM trips t {$where}";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $orderCol = $this->tripPaymentSortColumn($sort);
        $orderDir = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';
        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT
                t.id,
                t.customer_name,
                t.destination,
                t.budget_lkr,
                t.status,
                t.start_date,
                t.number_of_people,
                t.created_at
            FROM trips t
            {$where}
            ORDER BY {$orderCol} {$orderDir}
            LIMIT :lim OFFSET :off
        ";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['rows' => $rows, 'total' => $total];
    }

    // ─── Admin payments report (package + custom trip) ─────────────────────

    private function normalizePaySource(string $value): string
    {
        $s = strtolower(trim($value));

        return in_array($s, ['all', 'package', 'trip'], true) ? $s : 'all';
    }

    /**
     * @param array<string,mixed> $r
     *
     * @return array<string,mixed>
     */
    private function normalizePaymentRowPackage(array $r): array
    {
        return [
            'payment_source' => 'package',
            'id'               => $r['id'],
            'customer'         => $r['fullname'] ?? '',
            'email'            => $r['email'] ?? '',
            'detail'           => $r['package_name'] ?? '',
            'amount'           => isset($r['total_amount']) ? (float) $r['total_amount'] : 0.0,
            'status'           => $r['status'] ?? '',
            'pay_method'       => $r['pay_method'] ?? '',
            'created_at'       => $r['created_at'] ?? '',
            'notes'            => $r['paid_at'] ?? '',
        ];
    }

    /**
     * @param array<string,mixed> $r
     *
     * @return array<string,mixed>
     */
    private function normalizePaymentRowTrip(array $r): array
    {
        $people = isset($r['number_of_people']) ? (int) $r['number_of_people'] : 0;
        $start  = (string) ($r['start_date'] ?? '');
        $notes  = trim($start . ' · ' . $people . ' pax');

        return [
            'payment_source' => 'trip',
            'id'             => $r['id'],
            'customer'       => $r['customer_name'] ?? '',
            'email'          => '',
            'detail'         => $r['destination'] ?? '',
            'amount'         => isset($r['budget_lkr']) ? (float) $r['budget_lkr'] : 0.0,
            'status'         => $r['status'] ?? '',
            'pay_method'     => '—',
            'created_at'     => $r['created_at'] ?? '',
            'notes'          => $notes,
        ];
    }

    /**
     * @return array{pay_source: string, package_summary?: array, trip_summary?: array}
     */
    /**
     * total_revenue: primary figure for admins — package paid (status=paid) ± trip budget for confirmed/completed,
     * depending on pay_source (combined when scope is "all").
     *
     * @return array{pay_source: string, total_revenue: float, package_summary?: array, trip_summary?: array}
     */
    public function summarizePaymentsReport(array $filters): array
    {
        $src = $this->normalizePaySource((string) ($filters['pay_source'] ?? 'all'));
        if ($src === 'package') {
            $ps = $this->summarizePayments($filters);

            return [
                'pay_source'       => 'package',
                'total_revenue'    => (float) ($ps['total_revenue'] ?? 0),
                'package_summary'  => $ps,
            ];
        }
        if ($src === 'trip') {
            $ts = $this->summarizeTripPayments($filters);

            return [
                'pay_source'     => 'trip',
                'total_revenue'  => (float) ($ts['completed_value'] ?? 0),
                'trip_summary'   => $ts,
            ];
        }

        $ps = $this->summarizePayments($filters);
        $ts = $this->summarizeTripPayments($filters);
        $combined = (float) ($ps['total_revenue'] ?? 0) + (float) ($ts['completed_value'] ?? 0);

        return [
            'pay_source'       => 'all',
            'total_revenue'    => $combined,
            'package_summary'  => $ps,
            'trip_summary'     => $ts,
        ];
    }

    private function paymentUnionOrderExpr(string $sort): string
    {
        $map = [
            'created_at' => 'u.created_at',
            'amount'     => 'u.amount',
            'status'     => 'u.status',
            'id'         => 'u.id',
        ];

        return $map[$sort] ?? 'u.created_at';
    }

    /**
     * @return array{rows: array<int,array>, total: int}
     */
    private function fetchPaymentsAllUnion(array $filters, string $search, string $sort, string $dir, int $page, int $perPage): array
    {
        $pParams = [];
        $pWhere  = ' WHERE 1=1 ';
        $pWhere .= $this->bindDateRange($pParams, $filters['date_from'] ?? null, $filters['date_to'] ?? null, 'pb.created_at');

        $method = $filters['pay_method'] ?? 'all';
        if ($method === 'bank_transfer') {
            $pWhere .= " AND ({$this->paymentMethodExpr()}) = 'bank_transfer' ";
        } elseif ($method === 'online') {
            $pWhere .= " AND ({$this->paymentMethodExpr()}) = 'online' ";
        }

        $pst = $this->normalizePaymentDisplayFilter($filters);
        $pWhere .= $this->sqlPaymentDisplayFilterPackage($pst);

        if ($search !== '') {
            $pWhere .= ' AND (pb.fullname LIKE :pkg_sq OR pb.email LIKE :pkg_sq OR pb.package_name LIKE :pkg_sq OR CAST(pb.id AS CHAR) LIKE :pkg_sq) ';
            $pParams[':pkg_sq'] = '%' . $search . '%';
        }

        $tParams = [];
        $tWhere  = ' WHERE 1=1 ';
        $tWhere .= $this->bindDateRange($tParams, $filters['date_from'] ?? null, $filters['date_to'] ?? null, 't.created_at', 'tr', true);

        $tWhere .= $this->sqlPaymentDisplayFilterTrip($pst);

        if ($search !== '') {
            $tWhere .= ' AND (t.customer_name LIKE :tr_sq OR t.destination LIKE :tr_sq OR CAST(t.id AS CHAR) LIKE :tr_sq) ';
            $tParams[':tr_sq'] = '%' . $search . '%';
        }

        $allParams = array_merge($pParams, $tParams);

        $pkgInner = "
            SELECT
                'package' AS payment_source,
                pb.id AS id,
                pb.fullname AS customer,
                pb.email AS email,
                pb.package_name AS detail,
                pb.total_amount AS amount,
                pb.status AS status,
                {$this->paymentMethodExpr()} AS pay_method,
                pb.created_at AS created_at,
                IFNULL(DATE_FORMAT(pb.paid_at, '%Y-%m-%d %H:%i:%s'), '') AS notes
            FROM package_bookings pb
            {$pWhere}
        ";

        $tripInner = "
            SELECT
                'trip' AS payment_source,
                t.id AS id,
                t.customer_name AS customer,
                '' AS email,
                t.destination AS detail,
                t.budget_lkr AS amount,
                t.status AS status,
                '—' AS pay_method,
                t.created_at AS created_at,
                TRIM(CONCAT(IFNULL(t.start_date, ''), ' · ', IFNULL(CAST(t.number_of_people AS CHAR), ''), ' pax')) AS notes
            FROM trips t
            {$tWhere}
        ";

        $unionSql = "({$pkgInner}) UNION ALL ({$tripInner})";

        $countSql = "SELECT COUNT(*) FROM ( {$unionSql} ) ucnt";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($allParams);
        $total = (int) $stmt->fetchColumn();

        $orderExpr = $this->paymentUnionOrderExpr($sort);
        $orderDir  = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';
        $offset    = ($page - 1) * $perPage;

        $dataSql = "
            SELECT u.* FROM ( {$unionSql} ) u
            ORDER BY {$orderExpr} {$orderDir}
            LIMIT :lim OFFSET :off
        ";
        $stmt = $this->db->prepare($dataSql);
        foreach ($allParams as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$row) {
            $row['amount'] = isset($row['amount']) ? (float) $row['amount'] : 0.0;
        }
        unset($row);

        return ['rows' => $rows, 'total' => $total];
    }

    /**
     * Package bookings, custom trips, or both — unified rows for admin reports.
     *
     * @return array{rows: array<int,array>, total: int}
     */
    public function fetchPaymentsReport(array $filters, string $search, string $sort, string $dir, int $page, int $perPage): array
    {
        $src = $this->normalizePaySource((string) ($filters['pay_source'] ?? 'all'));
        if ($src === 'package') {
            $r = $this->fetchPayments($filters, $search, $sort, $dir, $page, $perPage);
            $r['rows'] = array_map([$this, 'normalizePaymentRowPackage'], $r['rows']);

            return $r;
        }
        if ($src === 'trip') {
            $r = $this->fetchTripPayments($filters, $search, $sort, $dir, $page, $perPage);
            $r['rows'] = array_map([$this, 'normalizePaymentRowTrip'], $r['rows']);

            return $r;
        }

        return $this->fetchPaymentsAllUnion($filters, $search, $sort, $dir, $page, $perPage);
    }
}
