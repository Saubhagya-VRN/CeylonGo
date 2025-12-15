<?php
class Tourist {
    private $conn;
    private $table = "tourist_users";

    public $id;
    public $first_name;
    public $last_name;
    public $contact_number;
    public $email;
    public $password;
    public $is_active;   // NEW

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register() {
        $query = "INSERT INTO " . $this->table . "
                  (first_name, last_name, contact_number, email, password)
                  VALUES (:first_name, :last_name, :contact_number, :email, :password)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":contact_number", $this->contact_number);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function getTouristById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTouristByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =========================
       Admin Functions (NEW)
    ========================== */

    // Get all tourists for admin panel
    public function getAllTourists() {
        $query = "SELECT id, first_name, last_name, contact_number, email, is_active
                  FROM " . $this->table . "
                  ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Activate / Deactivate tourist (SOFT DELETE)
    public function updateStatus($touristId, $status) {
        // Update tourist_users table
        $stmt1 = $this->conn->prepare(
            "UPDATE tourist_users SET is_active = ? WHERE id = ?"
        );
        $stmt1->execute([$status, $touristId]);

        // Update users auth table
        $stmt2 = $this->conn->prepare(
            "UPDATE users SET is_active = ? WHERE ref_id = ? AND role = 'tourist'"
        );
        return $stmt2->execute([$status, $touristId]);
    }
}
?>

