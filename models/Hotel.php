<?php
class Hotel {
    private $conn;
    private $table = "hotel_users";

    public $id;
    public $hotel_name;
    public $location;
    public $city;
    public $hotel_image;
    public $contact_number;
    public $email;
    public $password;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register() {
        $query = "INSERT INTO " . $this->table . "
                  (hotel_name, location, city, hotel_image, contact_number, email, password)
                  VALUES (:hotel_name, :location, :city, :hotel_image, :contact_number, :email, :password)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":hotel_name", $this->hotel_name);
        $stmt->bindParam(":location", $this->location);
        $stmt->bindParam(":city", $this->city);
        $stmt->bindParam(":hotel_image", $this->hotel_image);
        $stmt->bindParam(":contact_number", $this->contact_number);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function getHotelById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getHotelByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

