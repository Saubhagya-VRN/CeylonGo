<?php
class Booking {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // ── Fetch all trips (customized bookings) with optional filters ──
    public function getAllBookingsWithUsers($status = null, $searchId = null, $date = null) {
        $sql = "SELECT
                    t.id          AS booking_id,
                    t.customer_name AS user_name,
                    t.status,
                    t.created_at,
                    t.destination,
                    t.number_of_people,
                    t.number_of_days,
                    t.start_date,
                    t.budget_lkr
                FROM trips t";

        $conditions = [];
        $params     = [];

        if ($status && strtolower($status) !== 'all') {
            $conditions[]      = "t.status = :status";
            $params[':status'] = $status;
        }

        // Search by booking ID or customer name
        if ($searchId) {
            $conditions[] = "(t.id = :booking_id OR t.customer_name LIKE :name)";
            $params[':booking_id'] = $searchId;
            $params[':name']       = '%' . $searchId . '%';
        }

        if ($date) {
            $conditions[]    = "DATE(t.created_at) = :date";
            $params[':date'] = $date;
        }

        if ($conditions) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY t.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── Stats for the customized bookings table ──
    public function getBookingStats() {
        $sql = "SELECT
                    COUNT(*) AS total,
                    SUM(CASE WHEN status = 'pending'   THEN 1 ELSE 0 END) AS pending,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled
                FROM trips";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ── Fetch a single trip by ID ──
    public function getBookingById($bookingId) {
        $stmt = $this->db->prepare("
            SELECT
                t.id            AS booking_id,
                t.customer_name AS user_name,
                t.status,
                t.created_at,
                t.destination,
                t.number_of_people,
                t.number_of_days,
                t.start_date,
                t.budget_lkr
            FROM trips t
            WHERE t.id = :id
        ");
        $stmt->execute([':id' => $bookingId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ── "Destinations" for a trip — trips table has one row per trip,
    //    so we return it as a single-element array to keep view/export
    //    code working without changes.
    public function getBookingDestinations($bookingId) {
        $stmt = $this->db->prepare("
            SELECT
                destination,
                number_of_people AS people_count,
                number_of_days   AS days,
                start_date,
                budget_lkr
            FROM trips
            WHERE id = :id
        ");
        $stmt->execute([':id' => $bookingId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── Aggregated bookings for charts (reports page) ──
    public function getAggregatedBookings($period = 'daily') {
        switch (strtolower($period)) {
            case 'monthly': $dateFormat = "%Y-%m"; break;
            case 'yearly':  $dateFormat = "%Y";    break;
            default:        $dateFormat = "%Y-%m-%d"; break;
        }

        $sql = "SELECT
                    DATE_FORMAT(created_at, :dateFormat) AS period,
                    COUNT(*) AS total,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled
                FROM trips
                GROUP BY period
                ORDER BY period ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':dateFormat', $dateFormat, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>