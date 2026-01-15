<?php
class Guide {
    private $conn;
    private $table = "guide_users";

    public $id;
    public $user_type;
    public $first_name;
    public $last_name;
    public $nic;
    public $license_number;
    public $specialization;
    public $languages;
    public $experience;
    public $profile_photo;
    public $license_file;
    public $contact_number;
    public $email;
    public $password;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register() {
        $query = "INSERT INTO " . $this->table . "
                  (user_type, first_name, last_name, nic, license_number, specialization, languages, experience, profile_photo, license_file, contact_number, email, password)
                  VALUES (:user_type, :first_name, :last_name, :nic, :license_number, :specialization, :languages, :experience, :profile_photo, :license_file, :contact_number, :email, :password)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_type", $this->user_type);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":nic", $this->nic);
        $stmt->bindParam(":license_number", $this->license_number);
        $stmt->bindParam(":specialization", $this->specialization);
        $stmt->bindParam(":languages", $this->languages);
        $stmt->bindParam(":experience", $this->experience);
        $stmt->bindParam(":profile_photo", $this->profile_photo);
        $stmt->bindParam(":license_file", $this->license_file);
        $stmt->bindParam(":contact_number", $this->contact_number);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function getGuideById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getGuideByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllGuides() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateGuide() {
        $query = "UPDATE " . $this->table . " SET 
                  first_name = :first_name,
                  last_name = :last_name,
                  contact_number = :contact_number,
                  email = :email,
                  specialization = :specialization,
                  languages = :languages,
                  experience = :experience
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':contact_number', $this->contact_number);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':specialization', $this->specialization);
        $stmt->bindParam(':languages', $this->languages);
        $stmt->bindParam(':experience', $this->experience);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }

    public function updateProfilePhoto($id, $photo) {
        $query = "UPDATE " . $this->table . " SET profile_photo = :profile_photo WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':profile_photo', $photo);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updateLicense($id, $license_number) {
        $query = "UPDATE " . $this->table . " SET license_number = :license_number WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':license_number', $license_number);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updatePassword($id, $password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE " . $this->table . " SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function verifyPassword($id, $password) {
        $guide = $this->getGuideById($id);
        if ($guide && isset($guide['password'])) {
            return password_verify($password, $guide['password']);
        }
        return false;
    }
}
?>

