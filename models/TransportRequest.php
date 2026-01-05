<?php
class TransportRequest {
    private $conn;
    private $table = "tourist_transport_requests";

    public $id;
    public $customerName;
    public $vehicleType;
    public $date;
    public $pickupTime;
    public $pickupLocation;
    public $dropoffLocation;
    public $numPeople;
    public $notes;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addRequest() {
        $query = "INSERT INTO " . $this->table . "
                  (customerName, vehicleType, date, pickupTime, pickupLocation, dropoffLocation, numPeople, notes)
                  VALUES (:customerName, :vehicleType, :date, :pickupTime, :pickupLocation, :dropoffLocation, :numPeople, :notes)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":customerName", $this->customerName);
        $stmt->bindParam(":vehicleType", $this->vehicleType);
        $stmt->bindParam(":date", $this->date);
        $stmt->bindParam(":pickupTime", $this->pickupTime);
        $stmt->bindParam(":pickupLocation", $this->pickupLocation);
        $stmt->bindParam(":dropoffLocation", $this->dropoffLocation);
        $stmt->bindParam(":numPeople", $this->numPeople);
        $stmt->bindParam(":notes", $this->notes);

        return $stmt->execute();
    }

    public function getRequestById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllRequests() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

