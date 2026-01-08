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
        $query = "UPDATE " . $this->table . " SET profile_image = :profile_image WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":profile_image", $image_path);
        $stmt->bindParam(":user_id", $user_id);
        
        return $stmt->execute();
    }
}
?>
