<?php
/**
 * Admin Reports — aggregated queries with prepared statements.
 * All dynamic SQL uses whitelisted sort columns and bound parameters.
 */
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
        $params = [];
        $datePb = $this->bindDateRange($params, $df, $dt, 'pb.created_at', 'pb');
        $params2 = [];
        $dateTr = $this->bindDateRange($params2, $df, $dt, 't.created_at', 'tr');
        $params = array_merge($params, $params2);

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
                (SELECT COUNT(*) FROM package_bookings pb WHERE 1=1 {$datePb} {$extraPb})
              + (SELECT COUNT(*) FROM trips t WHERE 1=1 {$dateTr} {$extraTr}) AS total,
                (SELECT COUNT(*) FROM package_bookings pb WHERE 1=1 {$datePb} AND pb.status = 'pending')
              + (SELECT COUNT(*) FROM trips t WHERE 1=1 {$dateTr} AND t.status = 'pending') AS pending,
                (SELECT COUNT(*) FROM package_bookings pb WHERE 1=1 {$datePb} AND pb.status = 'approved')
              + (SELECT COUNT(*) FROM trips t WHERE 1=1 {$dateTr} AND t.status IN ('confirmed','completed')) AS confirmed,
                (SELECT COUNT(*) FROM package_bookings pb WHERE 1=1 {$datePb} AND pb.status IN ('cancelled','rejected'))
              + (SELECT COUNT(*) FROM trips t WHERE 1=1 {$dateTr} AND (t.status = 'cancelled' OR t.refund_requested_at IS NOT NULL)) AS cancelled,
                COALESCE((SELECT SUM(pb.total_amount) FROM package_bookings pb WHERE 1=1 {$datePb} {$extraPb}),0)
              + COALESCE((SELECT SUM(CASE WHEN t.status IN ('confirmed','completed') THEN t.budget_lkr ELSE 0 END) FROM trips t WHERE 1=1 {$dateTr} {$extraTr}),0)
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

        $pst = $filters['pay_status'] ?? 'all';
        if ($pst !== 'all' && $pst !== '') {
            $where .= ' AND pb.status = :pstat ';
            $params[':pstat'] = $pst;
        }

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

        $pst = $filters['pay_status'] ?? 'all';
        if ($pst !== 'all' && $pst !== '') {
            $where .= ' AND pb.status = :pstat ';
            $params[':pstat'] = $pst;
        }

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

    /** @return array{total:int,active:int,guide:int,hotel:int,transport:int} */
    public function summarizeProviders(array $filters): array
    {
        $rows = $this->fetchProvidersAll($filters);
        $stats = ['total' => count($rows), 'active' => 0, 'guide' => 0, 'hotel' => 0, 'transport' => 0];
        foreach ($rows as $r) {
            if (!empty($r['is_active'])) {
                $stats['active']++;
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

    /** @return array{total:int,completed_value:float,pending:int} */
    public function summarizeTripPayments(array $filters): array
    {
        $params = [];
        $where = ' WHERE 1=1 ';
        $where .= $this->bindDateRange($params, $filters['date_from'] ?? null, $filters['date_to'] ?? null, 't.created_at', '', true);

        $sql = "
            SELECT
                COUNT(*) AS total,
                COALESCE(SUM(CASE WHEN t.status IN ('confirmed','completed') THEN t.budget_lkr ELSE 0 END), 0) AS completed_value,
                SUM(CASE WHEN t.status = 'pending' THEN 1 ELSE 0 END) AS pending
            FROM trips t
            {$where}
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $r = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'completed_value' => 0, 'pending' => 0];
        return [
            'total'           => (int) ($r['total'] ?? 0),
            'completed_value' => (float) ($r['completed_value'] ?? 0),
            'pending'         => (int) ($r['pending'] ?? 0),
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
}
