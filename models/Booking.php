<?php
class Booking {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Fetch all bookings with user full name
    public function getAllBookingsWithUsers($status = null, $searchId = null, $date = null) {
        $sql = "SELECT 
                    tb.id AS booking_id,
                    CONCAT(tu.first_name, ' ', tu.last_name) AS user_name,
                    tb.status,
                    tb.created_at
                FROM trip_bookings tb
                JOIN tourist_users tu ON tb.user_id = tu.id";

        $conditions = [];
        $params = [];

        // Filter by status
        if ($status && strtolower($status) != 'all') {
            $conditions[] = "tb.status = :status";
            $params[':status'] = $status;
        }

        // Search by booking ID
        if ($searchId) {
            $conditions[] = "tb.id = :booking_id";
            $params[':booking_id'] = $searchId;
        }

        // Filter by date (only the date part of created_at)
        if ($date) {
            $conditions[] = "DATE(tb.created_at) = :date";
            $params[':date'] = $date;
        }

        if ($conditions) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY tb.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookingStats() {
        $sql = "SELECT 
                    COUNT(*) AS total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled
                FROM trip_bookings";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Fetch single booking by ID
    public function getBookingById($bookingId) {
        $stmt = $this->db->prepare("
            SELECT tb.id AS booking_id, 
                CONCAT(tu.first_name,' ',tu.last_name) AS user_name, 
                tb.status, tb.created_at
            FROM trip_bookings tb
            JOIN tourist_users tu ON tb.user_id = tu.id
            WHERE tb.id = :id
        ");
        $stmt->execute([':id' => $bookingId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Fetch destinations for a booking
    public function getBookingDestinations($bookingId) {
        $stmt = $this->db->prepare("
            SELECT destination, people_count, days, hotel, transport 
            FROM trip_destinations 
            WHERE booking_id = :id
        ");
        $stmt->execute([':id' => $bookingId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get aggregated bookings for chart
    public function getAggregatedBookings($period = 'daily') {
        // Determine date format based on period
        switch (strtolower($period)) {
            case 'monthly':
                $dateFormat = "%Y-%M";
                break;
            case 'yearly':
                $dateFormat = "%Y";
                break;
            case 'daily':
            default:
                $dateFormat = "%Y-%m-%d";
                break;
        }

        $sql = "SELECT 
                    DATE_FORMAT(created_at, :dateFormat) AS period,
                    COUNT(*) AS total,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled
                FROM trip_bookings
                GROUP BY period
                ORDER BY period ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':dateFormat', $dateFormat, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Debug: Check what data is being returned
        error_log("Period: " . $period);
        error_log("Date Format: " . $dateFormat);
        error_log("Result: " . print_r($result, true));
        
        return $result;
    }
}
?>
