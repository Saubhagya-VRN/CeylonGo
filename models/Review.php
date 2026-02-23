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
                    r.admin_reply,
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
                    r.admin_reply,
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

    public function saveAdminReply($reviewId, $reply)
    {
        $sql = "
            UPDATE reviews 
            SET admin_reply = :reply,
                replied_at = NOW()
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':reply' => $reply,
            ':id' => $reviewId
        ]);
    }

    // Get review metrics for dashboard
    public function getReviewMetrics()
    {
        $sql = "
            SELECT
                COUNT(*) AS total_all,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS total_approved,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS total_pending,
                ROUND(AVG(CASE WHEN status = 'approved' THEN rating END), 1) AS avg_rating,
                SUM(CASE WHEN status = 'approved' AND rating >= 4 THEN 1 ELSE 0 END) AS positive_reviews
            FROM reviews
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        $positivePercentage = 0;
        if ($data['total_approved'] > 0) {
            $positivePercentage = round(
                ($data['positive_reviews'] / $data['total_approved']) * 100
            );
        }

        return [
            'total' => (int)$data['total_all'],
            'approved' => (int)$data['total_approved'],
            'pending' => (int)$data['total_pending'],
            'average' => $data['avg_rating'] ?? 0,
            'positive_percentage' => $positivePercentage
        ];
    }
}
