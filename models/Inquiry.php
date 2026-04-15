<?php
class Inquiry {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Get all inquiries, with optional status filter and search term.
     * Joined with tourist_users for registered users; falls back to guest_name/guest_email.
     */
    public function getAllInquiries(string $status = 'all', string $search = ''): array
    {
        $conditions = [];
        $params     = [];

        if ($status !== 'all') {
            $conditions[] = 'i.status = :status';
            $params[':status'] = $status;
        }

        if ($search !== '') {
            $conditions[] = '(
                i.subject      LIKE :search
                OR i.guest_name LIKE :search
                OR i.guest_email LIKE :search
                OR CONCAT(t.first_name, \' \', t.last_name) LIKE :search
            )';
            $params[':search'] = '%' . $search . '%';
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $sql = "
            SELECT
                i.id,
                i.user_id,
                i.guest_name,
                i.guest_email,
                i.subject,
                i.message,
                i.admin_reply,
                i.status,
                i.created_at,
                i.replied_at,
                CONCAT(t.first_name, ' ', t.last_name) AS tourist_name
            FROM inquiries i
            LEFT JOIN tourist_users t ON i.user_id = t.id
            $where
            ORDER BY i.created_at DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get summary statistics for the stats boxes.
     */
    public function getInquiryStats(): array
    {
        $sql = "
            SELECT
                COUNT(*)                                              AS total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN status = 'replied' THEN 1 ELSE 0 END) AS replied,
                SUM(CASE WHEN user_id IS NULL    THEN 1 ELSE 0 END) AS guest
            FROM inquiries
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'total'   => (int)($row['total']   ?? 0),
            'pending' => (int)($row['pending']  ?? 0),
            'replied' => (int)($row['replied']  ?? 0),
            'guest'   => (int)($row['guest']    ?? 0),
        ];
    }

    /**
     * Save admin reply and mark as replied.
     */
    public function saveAdminReply(int $inquiryId, string $reply): bool
    {
        $sql = "
            UPDATE inquiries
            SET admin_reply = :reply,
                status      = 'replied',
                replied_at  = NOW()
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':reply' => $reply, ':id' => $inquiryId]);
    }

    /**
     * Permanently delete an inquiry.
     */
    public function deleteInquiry(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM inquiries WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>