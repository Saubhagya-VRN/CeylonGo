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

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
        } catch (PDOException $e) {
            error_log('Hotel model registration error: ' . $e->getMessage());
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

    public function GetAccommodationCatalog() {
        try {
            $query = "SELECT * FROM accommodation_catalog";
            $stmt = $this->conn->prepare($query);

            if ($stmt->execute()) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log('Hotel model GetAccommodationCatalog error: ' . $e->getMessage());
        }

        return [];
    }

    public function GetAccommodationCatalogByUserId($user_id) {
        try {
            $query = "SELECT * FROM accommodation_catalog WHERE hotel_user_id = :hotel_user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':hotel_user_id', (int) $user_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->fetchAll(PDO::FETCH_OBJ);
            }
        } catch (PDOException $e) {
            error_log('Hotel model GetAccommodationCatalogByUserId error: ' . $e->getMessage());
        }

        return [];
    }

    public function AddOrUpdateRoom($hotel_user_id, $room_details) {
        try {
            $hotel_user_id = (int) $hotel_user_id;
            if ($hotel_user_id <= 0) {
                return false;
            }

            if (is_string($room_details)) {
                $decodedRoomDetails = json_decode($room_details, true);
                if (is_array($decodedRoomDetails)) {
                    $room_details = $decodedRoomDetails;
                }
            }

            if (!is_array($room_details)) {
                return false;
            }

            $newRooms = $this->isAssocArray($room_details) ? [$room_details] : $room_details;

            $existingQuery = "SELECT room_details FROM accommodation_catalog WHERE hotel_user_id = :hotel_user_id LIMIT 1";
            $existingStmt = $this->conn->prepare($existingQuery);
            $existingStmt->bindValue(':hotel_user_id', $hotel_user_id, PDO::PARAM_INT);
            $existingStmt->execute();
            $existingRow = $existingStmt->fetch(PDO::FETCH_ASSOC);

            $existingRooms = [];
            if (!empty($existingRow['room_details']) && is_string($existingRow['room_details'])) {
                $decodedExistingRooms = json_decode($existingRow['room_details'], true);
                if (is_array($decodedExistingRooms)) {
                    $existingRooms = $decodedExistingRooms;
                }
            }

            $mergedRooms = [];
            foreach ($existingRooms as $existingRoom) {
                if (is_array($existingRoom)) {
                    $mergedRooms[] = $existingRoom;
                }
            }
            foreach ($newRooms as $newRoom) {
                if (is_array($newRoom)) {
                    $mergedRooms[] = $newRoom;
                }
            }

            $roomDetailsJson = json_encode($mergedRooms, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($roomDetailsJson === false) {
                return false;
            }

            if ($existingRow) {
                $updateQuery = "UPDATE accommodation_catalog SET room_details = :room_details WHERE hotel_user_id = :hotel_user_id";
                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->bindValue(':room_details', $roomDetailsJson, PDO::PARAM_STR);
                $updateStmt->bindValue(':hotel_user_id', $hotel_user_id, PDO::PARAM_INT);
                return $updateStmt->execute();
            }

            $insertQuery = "INSERT INTO accommodation_catalog (hotel_user_id, room_details) VALUES (:hotel_user_id, :room_details)";
            $insertStmt = $this->conn->prepare($insertQuery);
            $insertStmt->bindValue(':hotel_user_id', $hotel_user_id, PDO::PARAM_INT);
            $insertStmt->bindValue(':room_details', $roomDetailsJson, PDO::PARAM_STR);
            return $insertStmt->execute();
        } catch (PDOException $e) {
            error_log('Room adding error: ' . $e->getMessage());
        }

        return false;
    }

    private function isAssocArray(array $array) {
        if ($array === []) {
            return false;
        }

        return array_keys($array) !== range(0, count($array) - 1);
    }
}


