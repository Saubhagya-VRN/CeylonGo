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

    public function getAll($status) {
        $status = (string)$status;
        $where = "";
        $params = array();
        if ($status === 'pending' || $status === 'replied') {
            $where = " WHERE i.status = :status ";
            $params[':status'] = $status;
        }
        $sql = "SELECT i.id, i.user_id, i.subject, i.message, i.admin_reply, i.status, i.created_at, i.replied_at,
                       tu.first_name, tu.last_name, tu.email
                FROM " . $this->table . " i
                LEFT JOIN tourist_users tu ON i.user_id = tu.id
                " . $where . "
                ORDER BY i.created_at DESC";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            error_log('Inquiry::getAll: ' . $e->getMessage());
            return array();
        }
    }

    public function reply($id, $replyText) {
        try {
            $sql = "UPDATE " . $this->table . "
                    SET admin_reply = :reply, status = 'replied', replied_at = NOW()
                    WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute(array(
                ':reply' => (string)$replyText,
                ':id' => (int)$id
            ));
        } catch (\Throwable $e) {
            error_log('Inquiry::reply: ' . $e->getMessage());
            return false;
        }
    }
}

