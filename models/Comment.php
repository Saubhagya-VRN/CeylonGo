<?php
class Comment {
    private $conn;
    private $table = "diary_comments";

    public $id;
    public $entry_id;
    public $user_id;
    public $user_type;
    public $comment_text;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addComment() {
        $query = "INSERT INTO " . $this->table . "
                  (entry_id, user_id, user_type, comment_text, created_at)
                  VALUES (:entry_id, :user_id, :user_type, :comment_text, NOW())";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":entry_id", $this->entry_id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":user_type", $this->user_type);
        $stmt->bindParam(":comment_text", $this->comment_text);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function getCommentsByEntryId($entry_id) {
        $query = "SELECT c.*, 
                         CASE 
                             WHEN c.user_type = 'tourist' THEN CONCAT(tu.first_name, ' ', tu.last_name)
                             WHEN c.user_type = 'guide' THEN CONCAT(gu.first_name, ' ', gu.last_name)
                             WHEN c.user_type = 'hotel' THEN hu.hotel_name
                             WHEN c.user_type = 'transporter' THEN COALESCE(tu_trans.full_name, 'Transport Provider')
                             ELSE 'Unknown'
                         END as user_name,
                         CASE 
                             WHEN c.user_type = 'tourist' THEN tu.email
                             WHEN c.user_type = 'guide' THEN gu.email
                             WHEN c.user_type = 'hotel' THEN hu.email
                             WHEN c.user_type = 'transporter' THEN tu_trans.email
                             ELSE NULL
                         END as user_email
                  FROM " . $this->table . " c
                  LEFT JOIN tourist_users tu ON c.user_id = tu.id AND c.user_type = 'tourist'
                  LEFT JOIN guide_users gu ON c.user_id = gu.id AND c.user_type = 'guide'
                  LEFT JOIN hotel_users hu ON c.user_id = hu.id AND c.user_type = 'hotel'
                  LEFT JOIN transport_users tu_trans ON CAST(c.user_id AS CHAR) = tu_trans.user_id AND c.user_type = 'transporter'
                  WHERE c.entry_id = ?
                  ORDER BY c.created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$entry_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteComment($id, $user_id, $user_type) {
        $query = "DELETE FROM " . $this->table . " 
                  WHERE id = ? AND user_id = ? AND user_type = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id, $user_id, $user_type]);
    }

    public function getCommentById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

