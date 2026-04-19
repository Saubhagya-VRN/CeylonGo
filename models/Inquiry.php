<?php
class Inquiry {
    private $conn;
    private $table = "inquiries";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($userId, $subject, $message) {
        try {
            $sql = "INSERT INTO " . $this->table . " (user_id, guest_name, guest_email, subject, message, status)
                    VALUES (:user_id, :guest_name, :guest_email, :subject, :message, 'pending')";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute(array(
                ':user_id' => ($userId === null ? null : (int)$userId),
                ':guest_name' => null,
                ':guest_email' => null,
                ':subject' => (string)$subject,
                ':message' => (string)$message
            ));
        } catch (\Throwable $e) {
            // Table may not be created yet.
            error_log('Inquiry::create: ' . $e->getMessage());
            return false;
        }
    }

    public function createGuest($guestName, $guestEmail, $subject, $message) {
        try {
            $sql = "INSERT INTO " . $this->table . " (user_id, guest_name, guest_email, subject, message, status)
                    VALUES (NULL, :guest_name, :guest_email, :subject, :message, 'pending')";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute(array(
                ':guest_name' => (string)$guestName,
                ':guest_email' => (string)$guestEmail,
                ':subject' => (string)$subject,
                ':message' => (string)$message
            ));
        } catch (\Throwable $e) {
            error_log('Inquiry::createGuest: ' . $e->getMessage());
            return false;
        }
    }

    public function getByUserId($userId, $limit) {
        try {
            $limit = max(1, (int)$limit);
            $sql = "SELECT id, user_id, subject, message, admin_reply, status, created_at, replied_at
                    FROM " . $this->table . "
                    WHERE user_id = :user_id
                    ORDER BY created_at DESC
                    LIMIT " . $limit;
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':user_id' => (int)$userId));
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            // Table may not be created yet.
            error_log('Inquiry::getByUserId: ' . $e->getMessage());
            return array();
        }
    }


    public function getAllInquiries(string $status, string $search = ''): array
    {
        $params = [];
        $where = " WHERE 1=1 ";

        if ($status === 'pending' || $status === 'replied') {
            $where .= " AND i.status = :status ";
            $params[':status'] = $status;
        }

        if ($search !== '') {
            $where .= " AND (i.subject LIKE :q OR i.message LIKE :q OR tu.first_name LIKE :q OR tu.last_name LIKE :q OR i.guest_name LIKE :q) ";
            $params[':q'] = '%' . $search . '%';
        }

        $sql = "
            SELECT i.id, i.user_id, i.subject, i.message, i.admin_reply, i.status, i.created_at, i.replied_at,
                   tu.first_name AS tourist_name, tu.last_name, tu.email,
                   i.guest_name, i.guest_email
            FROM inquiries i
            LEFT JOIN tourist_users tu ON i.user_id = tu.id
            $where
            ORDER BY i.created_at DESC
        ";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            error_log('Inquiry::getAllInquiries: ' . $e->getMessage());
            return [];
        }
    }

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

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'total'   => (int)($row['total']   ?? 0),
            'pending' => (int)($row['pending']  ?? 0),
            'replied' => (int)($row['replied']  ?? 0),
            'guest'   => (int)($row['guest']    ?? 0),
        ];
    }

    public function saveAdminReply(int $inquiryId, string $reply): bool
    {
        $sql = "
            UPDATE inquiries
            SET admin_reply = :reply,
                status      = 'replied',
                replied_at  = NOW()
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':reply' => $reply, ':id' => $inquiryId]);
    }

    public function deleteInquiry(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM inquiries WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

