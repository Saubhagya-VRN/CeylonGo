<?php
class Admin {
    private $conn;
    private $table = "admin";

    public $id;
    public $username;
    public $email;
    public $phone_number;
    public $role;
    public $password;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function updateProfile() {
        if (!empty($this->password)) {
            $query = "UPDATE " . $this->table . " SET
                      username = :username,
                      email = :email,
                      phone_number = :phone_number,
                      role = :role,
                      password = :password
                      WHERE id = :id";
        } else {
            $query = "UPDATE " . $this->table . " SET
                      username = :username,
                      email = :email,
                      phone_number = :phone_number,
                      role = :role
                      WHERE id = :id";
        }
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone_number", $this->phone_number);
        $stmt->bindParam(":role", $this->role);
        
        if (!empty($this->password)) {
            $stmt->bindParam(":password", $this->password);
        }

        return $stmt->execute();
    }

    public function deleteProfile($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    public function getAdminById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAdminByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

