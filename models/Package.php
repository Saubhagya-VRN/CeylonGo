<?php

class Package {
    private $conn;
    private $table = 'packages';

    /** @var int[]|null Package IDs with most bookings (computed), ordered by popularity */
    private $trendingPackageIdsCache = null;

    /** How many packages show the Trending badge (top N by booking count). */
    const TRENDING_TOP_N = 5;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Package IDs that count as "trending": top TRENDING_TOP_N by paid booking volume.
     * Only rows with status = paid in package_bookings are counted.
     */
    public function getTrendingPackageIds()
    {
        if ($this->trendingPackageIdsCache !== null) {
            return $this->trendingPackageIdsCache;
        }
        $ids = [];
        $n = (int) self::TRENDING_TOP_N;
        if ($n < 1) {
            $this->trendingPackageIdsCache = [];
            return [];
        }
        try {
            $sql = "SELECT package_id, COUNT(*) AS cnt
                    FROM package_bookings
                    WHERE status = 'paid'
                    GROUP BY package_id
                    HAVING cnt > 0
                    ORDER BY cnt DESC
                    LIMIT " . $n;
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $ids[] = (int) $row['package_id'];
            }
        } catch (PDOException $e) {
            error_log('Package::getTrendingPackageIds: ' . $e->getMessage());
            $ids = [];
        }
        $this->trendingPackageIdsCache = $ids;
        return $this->trendingPackageIdsCache;
    }

    /**
     * Decode JSON columns and normalize row for frontend (listing or detail).
     */
    private function normalizeRow($row, $forListing = false) {
        if (!$row) return null;
        $row['id'] = (int) $row['id'];
        $row['price'] = (int) $row['price'];
        $row['reviews'] = (int) (isset($row['reviews']) ? $row['reviews'] : 0);
        // trending may be pre-set from DB (admin) or computed from bookings in getAll/getById
        $row['trending'] = !empty($row['trending']);
        if (isset($row['rating'])) $row['rating'] = $row['rating'] !== null ? (float) $row['rating'] : null;

        $jsonCols = ['overview', 'highlights', 'itinerary', 'accommodation', 'included', 'excluded'];
        foreach ($jsonCols as $col) {
            if (isset($row[$col]) && is_string($row[$col])) {
                $decoded = json_decode($row[$col], true);
                $row[$col] = is_array($decoded) ? $decoded : [];
            }
        }

        if ($forListing) {
            $row['meals'] = true;
            $row['category'] = isset($row['category']) ? strtolower(trim($row['category'])) : '';
        }

        return $row;
    }

    public function getAll($filters = []) {
        $sql = "SELECT id, title, location, locations, duration, duration_short, image, category, price, price_child_ratio, price_infant_ratio, rating, reviews, trending, created_at FROM " . $this->table . " ORDER BY id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $trendingIds = $this->getTrendingPackageIds();
        $trendingRank = [];
        foreach ($trendingIds as $rank => $pid) {
            $trendingRank[(int) $pid] = $rank;
        }
        $list = [];
        foreach ($rows as $row) {
            $pid = (int) $row['id'];
            $row['trending'] = isset($trendingRank[$pid]) ? 1 : 0;
            $p = $this->normalizeRow($row, true);
            if (!empty($filters['trending']) && !$p['trending']) continue;
            if (!empty($filters['category']) && strtolower(trim($p['category'])) !== strtolower(trim($filters['category']))) continue;
            $list[] = $p;
        }
        if (!empty($filters['trending']) && count($list) > 1) {
            usort($list, function ($a, $b) use ($trendingRank) {
                $ra = $trendingRank[$a['id']] ?? 999;
                $rb = $trendingRank[$b['id']] ?? 999;
                return $ra - $rb;
            });
        }
        return $list;
    }

    public function getById($id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([(int) $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        $pid = (int) $row['id'];
        $trendingIds = $this->getTrendingPackageIds();
        $row['trending'] = in_array($pid, $trendingIds, true) ? 1 : 0;
        return $this->normalizeRow($row, false);
    }

    public function getListForDropdown() {
        $sql = "SELECT id, title FROM " . $this->table . " ORDER BY id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $data = $this->prepareData($data);
        $cols = array_keys($data);
        $placeholders = array_map(function ($c) { return ':' . $c; }, $cols);
        $sql = "INSERT INTO " . $this->table . " (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->conn->prepare($sql);
        foreach ($data as $k => $v) $stmt->bindValue(':' . $k, $v);
        try {
            if ($stmt->execute()) return (int) $this->conn->lastInsertId();
        } catch (PDOException $e) { error_log("Package::create " . $e->getMessage()); }
        return false;
    }

    public function update($id, $data) {
        $data = $this->prepareData($data);
        unset($data['id']);
        $sets = [];
        foreach (array_keys($data) as $c) $sets[] = "`$c` = :$c";
        $sql = "UPDATE " . $this->table . " SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        foreach ($data as $k => $v) $stmt->bindValue(':' . $k, $v);
        try {
            return $stmt->execute();
        } catch (PDOException $e) { error_log("Package::update " . $e->getMessage()); }
        return false;
    }

    public function delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        try {
            return $stmt->execute([(int) $id]);
        } catch (PDOException $e) { error_log("Package::delete " . $e->getMessage()); }
        return false;
    }

    private function prepareData($data) {
        $allowed = ['title', 'location', 'locations', 'duration', 'duration_short', 'image', 'category', 'price', 'price_child_ratio', 'price_infant_ratio', 'rating', 'reviews', 'trending', 'overview', 'highlights', 'itinerary', 'accommodation', 'included', 'excluded'];
        $out = [];
        foreach ($allowed as $key) {
            if (!array_key_exists($key, $data)) continue;
            $v = $data[$key];
            if (in_array($key, ['overview', 'highlights', 'itinerary', 'accommodation', 'included', 'excluded']) && is_array($v)) {
                $v = json_encode($v);
            }
            if ($key === 'trending') $v = $v ? 1 : 0;
            $out[$key] = $v;
        }
        return $out;
    }
}
