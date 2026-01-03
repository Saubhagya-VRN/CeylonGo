<?php
class User {
    private $conn;
    private $table = "transport_users";

    public $user_id;
    public $full_name;
    public $dob;
    public $nic;
    public $address;
    public $contact_no;
    public $email;
    public $psw;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register() {
        $query = "INSERT INTO " . $this->table . "
                  (user_id, full_name, dob, nic, address, contact_no, email, psw)
                  VALUES (:user_id, :full_name, :dob, :nic, :address, :contact_no, :email, :psw)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":dob", $this->dob);
        $stmt->bindParam(":nic", $this->nic);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":contact_no", $this->contact_no);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":psw", $this->psw);

        $_SESSION['transporter_id'] = $this->user_id;

        return $stmt->execute();
    }

    // Get user by user_id
    public function getUserById($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Export all users for status filter in admin panel
    public function getAllUsers($status = 'all') {
        $sql = "SELECT id, first_name, last_name, contact_number, email, is_active 
                FROM tourist_users";
        $params = [];

        if ($status === 'active') {
            $sql .= " WHERE is_active = 1";
        } elseif ($status === 'inactive') {
            $sql .= " WHERE is_active = 0";
        }
        $sql .= " ORDER BY id ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get user statistics for admin panel
    public function getUserStats() {
        $sql = "SELECT 
                    COUNT(*) AS total,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS active,
                    SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) AS inactive
                FROM tourist_users";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($userId, $status) {
        $sql = "UPDATE tourist_users SET is_active = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql); // ✅ use $this->conn
        return $stmt->execute([$status, $userId]);
    }

    public function updateUser($userId, $firstName, $lastName, $contact, $email) {
        $sql = "UPDATE tourist_users 
                SET first_name = :first_name, last_name = :last_name, contact_number = :contact, email = :email
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':first_name' => $firstName,
            ':last_name'  => $lastName,
            ':contact'    => $contact,
            ':email'      => $email,
            ':id'         => $userId
        ]);
    }
}
?>
