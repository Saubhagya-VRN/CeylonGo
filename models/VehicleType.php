<?php
class VehicleType {
    private $conn;
    private $table = "transport_vehicle_types";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllTypes() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
