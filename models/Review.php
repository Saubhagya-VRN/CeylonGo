<?php
class Review {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /** Tourist-submitted reviews (`reviews` table) for admin. */
    public function getAllReviews($rating = 'all')
    {
        if ($rating === 'all') {
            $sql = "
                SELECT 
                    r.id,
                    r.user_id,
                    r.destination,
                    r.review_text,
                    r.admin_reply,
                    r.rating,
                    r.status,
                    r.created_at,
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
                    r.destination,
                    r.review_text,
                    r.admin_reply,
                    r.rating,
                    r.status,
                    r.created_at,
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

    /**
     * Approved package-specific reviews for a public package page (`package_reviews` only).
     */
    public function getApprovedForPackage($packageId)
    {
        $packageId = (int) $packageId;
        if ($packageId <= 0) {
            return [];
        }
        try {
            $sql = "
                SELECT pr.id, pr.name, pr.rating, pr.review_text, pr.created_at
                FROM package_reviews pr
                WHERE pr.package_id = :pid AND pr.status = 'approved'
                ORDER BY pr.created_at DESC
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':pid', $packageId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Review::getApprovedForPackage: ' . $e->getMessage());
            return [];
        }
    }

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
                replied_at = NOW(),
                status = 'replied'
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':reply' => $reply,
            ':id' => $reviewId
        ]);
    }

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
        if (!empty($data['total_approved']) && (int)$data['total_approved'] > 0) {
            $positivePercentage = round(
                ((int)$data['positive_reviews'] / (int)$data['total_approved']) * 100
            );
        }

        return [
            'total' => (int)($data['total_all'] ?? 0),
            'approved' => (int)($data['total_approved'] ?? 0),
            'pending' => (int)($data['total_pending'] ?? 0),
            'average' => $data['avg_rating'] ?? 0,
            'positive_percentage' => $positivePercentage
        ];
    }

    /**
     * Package bookings reviews from table `package_reviews` (alias pr). Joins tourist_users (t).
     */
    public function getAllPackageReviews($rating = 'all')
    {
        $base = "
            SELECT 
                pr.id,
                pr.user_id,
                pr.destination,
                pr.review_text,
                pr.admin_reply,
                pr.rating,
                pr.status,
                pr.created_at,
                CONCAT(t.first_name, ' ', t.last_name) AS tourist_name
            FROM package_reviews pr
            JOIN tourist_users t ON pr.user_id = t.id
        ";

        if ($rating === 'all') {
            $sql = $base . " ORDER BY pr.created_at DESC";
            $stmt = $this->db->prepare($sql);
        } else {
            $sql = $base . " WHERE pr.rating = :rating ORDER BY pr.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':rating', (int) $rating, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPackageReviewMetrics()
    {
        $sql = "
            SELECT
                COUNT(*) AS total_all,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS total_pending,
                ROUND(IFNULL(AVG(rating), 0), 1) AS avg_rating
            FROM package_reviews
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'total' => (int) ($data['total_all'] ?? 0),
            'pending' => (int) ($data['total_pending'] ?? 0),
            'average' => $data['avg_rating'] ?? 0,
        ];
    }

    public function deletePackageReview($id)
    {
        $sql = 'DELETE FROM package_reviews WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([(int) $id]);
    }

    public function savePackageAdminReply($reviewId, $reply)
    {
        $sql = "
            UPDATE package_reviews
            SET admin_reply = :reply,
                replied_at = NOW()
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':reply' => $reply,
            ':id' => (int) $reviewId,
        ]);
     * Approved reviews for public marketing (e.g. tourist dashboard carousel).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getApprovedPublicReviews($limit = 15)
    {
        $limit = max(1, min(50, (int) $limit));
        try {
            $sql = "
                SELECT id, name, rating, review_text, created_at
                FROM reviews
                WHERE status = 'approved'
                ORDER BY created_at DESC
                LIMIT :lim";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Review::getApprovedPublicReviews: ' . $e->getMessage());
            return [];
        }
    }
}
