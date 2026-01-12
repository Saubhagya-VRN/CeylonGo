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
    public $profile_image;
    public $email;
    public $psw;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register() {
        $query = "INSERT INTO " . $this->table . "
                  (user_id, full_name, dob, nic, address, contact_no, profile_image, email, psw)
                  VALUES (:user_id, :full_name, :dob, :nic, :address, :contact_no, :profile_image, :email, :psw)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":dob", $this->dob);
        $stmt->bindParam(":nic", $this->nic);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":contact_no", $this->contact_no);
        $profile_image = $this->profile_image ?? '';
        $stmt->bindParam(":profile_image", $profile_image);
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

    // ===== ADMIN PANEL FUNCTIONS =====

    // Export all users for status filter in admin panel
    public function getAllUsers($status = 'all') {
        $sql = "SELECT id, first_name, last_name, contact_number, email, is_active 
                FROM tourist_users";

        if ($status === 'active') {
            $sql .= " WHERE is_active = 1";
        } elseif ($status === 'inactive') {
            $sql .= " WHERE is_active = 0";
        }
        $sql .= " ORDER BY id ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
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

    public function updateUserByAdmin($userId, $firstName, $lastName, $contact, $email) {
        $sql = "UPDATE tourist_users 
                SET first_name = :first_name, 
                    last_name = :last_name, 
                    contact_number = :contact, 
                    email = :email
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

    // ===== TRANSPORT PROVIDER FUNCTIONS =====

    // Update user profile information
    public function updateUser() {
        $query = "UPDATE " . $this->table . " SET 
                  full_name = :full_name,
                  dob = :dob,
                  address = :address,
                  contact_no = :contact_no,
                  email = :email
                  WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":dob", $this->dob);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":contact_no", $this->contact_no);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":user_id", $this->user_id);
        
        return $stmt->execute();
    }

    // Update user password
    public function updatePassword($new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $query = "UPDATE " . $this->table . " SET psw = :psw WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":psw", $hashed_password);
        $stmt->bindParam(":user_id", $this->user_id);
        
        return $stmt->execute();
    }

    // Update user profile image
    public function updateProfileImage($user_id, $image_path) {
        // Use TRIM to handle any whitespace inconsistencies in user_id
        $query = "UPDATE " . $this->table . " SET profile_image = :profile_image WHERE TRIM(user_id) = TRIM(:user_id)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":profile_image", $image_path);
        $stmt->bindParam(":user_id", $user_id);
        
        $stmt->execute();
        
        // Return true only if at least one row was updated
        return $stmt->rowCount() > 0;
    }
}
?>
