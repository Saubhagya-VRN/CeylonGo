<?php
class TransportRequest {
    private $conn;
    private $table = "transport_requests";

    public $id;
    public $userId;
    public $customerName;
    public $contactNumber;
    public $vehicleType;
    public $date;
    public $pickupTime;
    public $pickupLocation;
    public $dropoffLocation;
    public $numPeople;
    public $notes;
    public $estimatedFare;
    public $distance;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addRequest() {
        $query = "INSERT INTO " . $this->table . "
                  (user_id, customer_name, contact_number, vehicle_type, date, pickup_time, pickup_location, dropoff_location, num_people, notes, estimated_fare, distance)
                  VALUES (:user_id, :customer_name, :contact_number, :vehicle_type, :date, :pickup_time, :pickup_location, :dropoff_location, :num_people, :notes, :estimated_fare, :distance)";
        
        $stmt = $this->conn->prepare($query);

        $notes = $this->notes ?: null;
        $estimatedFare = $this->estimatedFare !== null && $this->estimatedFare !== '' ? (float) $this->estimatedFare : null;
        $distance = $this->distance !== null && $this->distance !== '' ? (float) $this->distance : null;

        $stmt->bindParam(":user_id", $this->userId);
        $stmt->bindParam(":customer_name", $this->customerName);
        $stmt->bindParam(":contact_number", $this->contactNumber);
        $stmt->bindParam(":vehicle_type", $this->vehicleType);
        $stmt->bindParam(":date", $this->date);
        $stmt->bindParam(":pickup_time", $this->pickupTime);
        $stmt->bindParam(":pickup_location", $this->pickupLocation);
        $stmt->bindParam(":dropoff_location", $this->dropoffLocation);
        $stmt->bindParam(":num_people", $this->numPeople);
        $stmt->bindParam(":notes", $notes);
        $stmt->bindParam(":estimated_fare", $estimatedFare);
        $stmt->bindParam(":distance", $distance);

        return $stmt->execute();
    }

    public function getRequestById($id) {
        $query = "SELECT id, user_id, customer_name AS customerName, contact_number AS contactNumber, vehicle_type AS vehicleType,
                  date, pickup_time AS pickupTime, pickup_location AS pickupLocation, dropoff_location AS dropoffLocation,
                  num_people AS numPeople, notes, estimated_fare AS estimatedFare, distance, status, created_at, updated_at
                  FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(array($id));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllRequests() {
        $query = "SELECT id, user_id, customer_name AS customerName, contact_number AS contactNumber, vehicle_type AS vehicleType,
                  date, pickup_time AS pickupTime, pickup_location AS pickupLocation, dropoff_location AS dropoffLocation,
                  num_people AS numPeople, notes, estimated_fare AS estimatedFare, distance, status, created_at, updated_at
                  FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRequestsByUserId($userId) {
        $query = "SELECT id, user_id, customer_name AS customerName, contact_number AS contactNumber, vehicle_type AS vehicleType,
                  date, pickup_time AS pickupTime, pickup_location AS pickupLocation, dropoff_location AS dropoffLocation,
                  num_people AS numPeople, notes, estimated_fare AS estimatedFare, distance, status, created_at, updated_at
                  FROM " . $this->table . " WHERE user_id = ? ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

