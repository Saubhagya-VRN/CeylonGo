<?php
class User {
    private $conn;
    private $table = "transport_users"; // ✅ updated to match actual database

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
                  (full_name, dob, nic, address, contact_no, email, psw)
                  VALUES (:full_name, :dob, :nic, :address, :contact_no, :email, :psw)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":dob", $this->dob);
        $stmt->bindParam(":nic", $this->nic);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":contact_no", $this->contact_no);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":psw", $this->psw);

        return $stmt->execute();
    }

    // Get user by user_id
    public function getUserById($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
