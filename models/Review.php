<?php
class Review {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Get all reviews
    public function getAllReviews($rating = 'all')
    {
        if ($rating === 'all') {
            $sql = "
                SELECT 
                    r.id,
                    r.user_id,
                    r.review_text,
                    r.rating,
                    r.status,
                    CONCAT(t.first_name, ' ', t.last_name) AS tourist_name
                FROM reviews r
                JOIN tourist_users t ON r.user_id = t.id
                ORDER BY r.created_at DESC
            ";

            $stmt = $this->db->prepare($sql);
        } else {
            $sql = "
                SELECT 
                    r.id,
                    r.user_id,
                    r.review_text,
                    r.rating,
                    r.status,
                    CONCAT(t.first_name, ' ', t.last_name) AS tourist_name
                FROM reviews r
                JOIN tourist_users t ON r.user_id = t.id
                WHERE r.rating = :rating
                ORDER BY r.created_at DESC
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':rating', (int)$rating, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete review
    public function deleteReview($id) {
        $sql = "DELETE FROM reviews WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
