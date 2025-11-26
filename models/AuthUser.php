<?php
class AuthUser {
    private $conn;
    private $table = "users";

    public $id;
    public $ref_id;
    public $email;
    public $password;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getUserByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addUser() {
        $query = "INSERT INTO " . $this->table . "
                  (ref_id, email, password, role)
                  VALUES (:ref_id, :email, :password, :role)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":ref_id", $this->ref_id);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":role", $this->role);

        return $stmt->execute();
    }

    public function updateUser() {
        if (!empty($this->password)) {
            $query = "UPDATE " . $this->table . " SET
                      email = :email,
                      password = :password
                      WHERE ref_id = :ref_id AND role = :role";
        } else {
            $query = "UPDATE " . $this->table . " SET
                      email = :email
                      WHERE ref_id = :ref_id AND role = :role";
        }
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":ref_id", $this->ref_id);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":role", $this->role);
        
        if (!empty($this->password)) {
            $stmt->bindParam(":password", $this->password);
        }

        return $stmt->execute();
    }

    public function deleteUser($ref_id, $role) {
        $query = "DELETE FROM " . $this->table . " WHERE ref_id = ? AND role = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$ref_id, $role]);
    }
}
?>

