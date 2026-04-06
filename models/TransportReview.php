<?php
class TransportReview {
    private $conn;
    private $table = "transport_review";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all reviews for a specific driver
     */
    public function getReviewsByDriverId($driver_id) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE user_id = ? ORDER BY rating DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$driver_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching transport reviews: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get review statistics for a driver
     */
    public function getReviewStats($driver_id) {
        try {
            $query = "SELECT 
                        COUNT(*) as total_reviews,
                        ROUND(AVG(CAST(rating AS DECIMAL(3,1))), 1) as avg_rating,
                        SUM(CASE WHEN CAST(rating AS UNSIGNED) = 5 THEN 1 ELSE 0 END) as star_5,
                        SUM(CASE WHEN CAST(rating AS UNSIGNED) = 4 THEN 1 ELSE 0 END) as star_4,
                        SUM(CASE WHEN CAST(rating AS UNSIGNED) = 3 THEN 1 ELSE 0 END) as star_3,
                        SUM(CASE WHEN CAST(rating AS UNSIGNED) = 2 THEN 1 ELSE 0 END) as star_2,
                        SUM(CASE WHEN CAST(rating AS UNSIGNED) = 1 THEN 1 ELSE 0 END) as star_1
                      FROM " . $this->table . " WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$driver_id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            $total = (int)($data['total_reviews'] ?? 0);
            return [
                'total_reviews' => $total,
                'avg_rating' => $data['avg_rating'] ?? 0,
                'star_5' => (int)($data['star_5'] ?? 0),
                'star_4' => (int)($data['star_4'] ?? 0),
                'star_3' => (int)($data['star_3'] ?? 0),
                'star_2' => (int)($data['star_2'] ?? 0),
                'star_1' => (int)($data['star_1'] ?? 0),
                'pct_5' => $total > 0 ? round(($data['star_5'] / $total) * 100) : 0,
                'pct_4' => $total > 0 ? round(($data['star_4'] / $total) * 100) : 0,
                'pct_3' => $total > 0 ? round(($data['star_3'] / $total) * 100) : 0,
                'pct_2' => $total > 0 ? round(($data['star_2'] / $total) * 100) : 0,
                'pct_1' => $total > 0 ? round(($data['star_1'] / $total) * 100) : 0,
                'positive_pct' => $total > 0 ? round((($data['star_4'] + $data['star_5']) / $total) * 100) : 0,
            ];
        } catch (PDOException $e) {
            error_log("Error fetching transport review stats: " . $e->getMessage());
            return [
                'total_reviews' => 0, 'avg_rating' => 0,
                'star_5' => 0, 'star_4' => 0, 'star_3' => 0, 'star_2' => 0, 'star_1' => 0,
                'pct_5' => 0, 'pct_4' => 0, 'pct_3' => 0, 'pct_2' => 0, 'pct_1' => 0,
                'positive_pct' => 0,
            ];
        }
    }
}
?>
