<?php
/**
 * Package model for tour packages.
 * Table: packages
 * Used by: tourist listing, detail, booking form, add review. Admin CRUD can use create(), update(), delete().
 */
class Package {
    private $conn;
    private $table = 'packages';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Decode JSON columns and normalize row for frontend (listing or detail).
     */
    private function normalizeRow($row, $forListing = false) {
        if (!$row) return null;
        $row['id'] = (int) $row['id'];
        $row['price'] = (int) $row['price'];
        $row['reviews'] = (int) (isset($row['reviews']) ? $row['reviews'] : 0);
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

    /**
     * Get all packages, optionally filtered by category or trending.
     * Returns same shape as listing page expects: id, title, location, locations, duration, image, trending, rating, reviews, meals, category, price.
     */
    public function getAll($filters = []) {
        $sql = "SELECT id, title, location, locations, duration, duration_short, image, category, price, price_child_ratio, price_infant_ratio, rating, reviews, trending, created_at FROM " . $this->table . " ORDER BY id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $list = [];
        foreach ($rows as $row) {
            $p = $this->normalizeRow($row, true);
            if (!empty($filters['trending']) && !$p['trending']) continue;
            if (!empty($filters['category']) && strtolower(trim($p['category'])) !== strtolower(trim($filters['category']))) continue;
            $list[] = $p;
        }
        return $list;
    }

    /**
     * Get one package by id for detail page / booking form. Returns null if not found.
     */
    public function getById($id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([(int) $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->normalizeRow($row, false) : null;
    }

    /**
     * Get id and title only for dropdowns (e.g. Add Review).
     */
    public function getListForDropdown() {
        $sql = "SELECT id, title FROM " . $this->table . " ORDER BY id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new package (for admin). $data keys must match table columns; overview/highlights/itinerary/accommodation/included/excluded as arrays will be JSON-encoded.
     * Returns new id or false.
     */
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

    /**
     * Update package by id (for admin).
     */
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

    /**
     * Delete package by id (for admin).
     */
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
