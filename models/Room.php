<?php
class Room {
    private $conn;
    private $table = "hotel_rooms";

    public $id;
    public $hotel_id;
    public $room_number;
    public $room_type;
    public $rate;
    public $capacity;
    public $description;
    public $amenities;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addRoom() {
        $query = "INSERT INTO " . $this->table . "
                  (hotel_id, room_number, room_type, rate, capacity, description, amenities, status)
                  VALUES (:hotel_id, :room_number, :room_type, :rate, :capacity, :description, :amenities, :status)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":hotel_id", $this->hotel_id);
        $stmt->bindParam(":room_number", $this->room_number);
        $stmt->bindParam(":room_type", $this->room_type);
        $stmt->bindParam(":rate", $this->rate);
        $stmt->bindParam(":capacity", $this->capacity);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":amenities", $this->amenities);
        $stmt->bindParam(":status", $this->status);

        return $stmt->execute();
    }

    public function updateRoom() {
        $query = "UPDATE " . $this->table . " SET
                  room_number = :room_number,
                  room_type = :room_type,
                  rate = :rate,
                  capacity = :capacity,
                  description = :description,
                  amenities = :amenities,
                  status = :status
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":room_number", $this->room_number);
        $stmt->bindParam(":room_type", $this->room_type);
        $stmt->bindParam(":rate", $this->rate);
        $stmt->bindParam(":capacity", $this->capacity);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":amenities", $this->amenities);
        $stmt->bindParam(":status", $this->status);

        return $stmt->execute();
    }

    public function deleteRoom($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    public function getRoomById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRoomsByHotel($hotel_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE hotel_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$hotel_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

