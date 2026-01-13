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

    // Verify current password
    public function verifyPassword($user_id, $current_password) {
        $query = "SELECT psw FROM " . $this->table . " WHERE TRIM(user_id) = TRIM(:user_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && password_verify($current_password, $result['psw'])) {
            return true;
        }
        return false;
    }

    // Update user password in both transport_users and users tables
    public function updatePassword($new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Begin transaction to ensure both tables are updated
        $this->conn->beginTransaction();
        
        try {
            // Update transport_users table
            $query1 = "UPDATE " . $this->table . " SET psw = :psw WHERE TRIM(user_id) = TRIM(:user_id)";
            $stmt1 = $this->conn->prepare($query1);
            $stmt1->bindParam(":psw", $hashed_password);
            $stmt1->bindParam(":user_id", $this->user_id);
            $stmt1->execute();
            
            // Get email for this user to update users table
            $emailQuery = "SELECT email FROM " . $this->table . " WHERE TRIM(user_id) = TRIM(:user_id)";
            $emailStmt = $this->conn->prepare($emailQuery);
            $emailStmt->bindParam(":user_id", $this->user_id);
            $emailStmt->execute();
            $emailResult = $emailStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($emailResult) {
                // Update users table (central auth table)
                $query2 = "UPDATE users SET password = :password WHERE email = :email AND role = 'transport'";
                $stmt2 = $this->conn->prepare($query2);
                $stmt2->bindParam(":password", $hashed_password);
                $stmt2->bindParam(":email", $emailResult['email']);
                $stmt2->execute();
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
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
