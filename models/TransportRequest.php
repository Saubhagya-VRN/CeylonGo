<?php
class TransportRequest {
    private $conn;
    private $table = "transport_requests";
    private $columnsCache = null;

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
    public $assignedDriverId;
    public $assignedVehicleNo;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    private function loadColumns() {
        if (is_array($this->columnsCache)) return;
        $this->columnsCache = array();
        try {
            $stmt = $this->conn->prepare("SHOW COLUMNS FROM " . $this->table);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $r) {
                if (isset($r['Field'])) {
                    $this->columnsCache[(string) $r['Field']] = true;
                }
            }
        } catch (\Throwable $e) {
            // If we can't introspect, keep cache empty and rely on try/catch fallbacks.
            $this->columnsCache = array();
        }
    }

    private function hasColumn($name) {
        $this->loadColumns();
        return isset($this->columnsCache[(string) $name]);
    }

    public function addRequest() {
        $notes = $this->notes ? $this->notes : null;
        $estimatedFare = $this->estimatedFare !== null && $this->estimatedFare !== '' ? (float) $this->estimatedFare : null;
        $distance = $this->distance !== null && $this->distance !== '' ? (float) $this->distance : null;
        $assignedDriverId = $this->assignedDriverId ? $this->assignedDriverId : null;
        $assignedVehicleNo = $this->assignedVehicleNo ? $this->assignedVehicleNo : null;
        $status = $this->status ? $this->status : 'pending';

        $cols = array('user_id', 'customer_name', 'contact_number', 'vehicle_type', 'date', 'pickup_time', 'pickup_location', 'dropoff_location', 'num_people', 'notes', 'estimated_fare', 'distance', 'status');
        if ($this->hasColumn('assigned_driver_id')) $cols[] = 'assigned_driver_id';
        if ($this->hasColumn('assigned_vehicle_no')) $cols[] = 'assigned_vehicle_no';

        $fieldsSql = implode(', ', $cols);
        $paramsSql = implode(', ', array_map(function ($c) { return ':' . $c; }, $cols));
        $query = "INSERT INTO " . $this->table . " (" . $fieldsSql . ") VALUES (" . $paramsSql . ")";
        $stmt = $this->conn->prepare($query);

        // Bind required
        $stmt->bindValue(':user_id', $this->userId);
        $stmt->bindValue(':customer_name', $this->customerName);
        $stmt->bindValue(':contact_number', $this->contactNumber);
        $stmt->bindValue(':vehicle_type', $this->vehicleType);
        $stmt->bindValue(':date', $this->date);
        $stmt->bindValue(':pickup_time', $this->pickupTime);
        $stmt->bindValue(':pickup_location', $this->pickupLocation);
        $stmt->bindValue(':dropoff_location', $this->dropoffLocation);
        $stmt->bindValue(':num_people', $this->numPeople);
        $stmt->bindValue(':notes', $notes);
        $stmt->bindValue(':estimated_fare', $estimatedFare);
        $stmt->bindValue(':distance', $distance);
        $stmt->bindValue(':status', $status);
        if ($this->hasColumn('assigned_driver_id')) $stmt->bindValue(':assigned_driver_id', $assignedDriverId);
        if ($this->hasColumn('assigned_vehicle_no')) $stmt->bindValue(':assigned_vehicle_no', $assignedVehicleNo);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function getRequestById($id) {
        $sel = "id, user_id, customer_name AS customerName, contact_number AS contactNumber, vehicle_type AS vehicleType,
                  date, pickup_time AS pickupTime, pickup_location AS pickupLocation, dropoff_location AS dropoffLocation,
                  num_people AS numPeople, notes, estimated_fare AS estimatedFare, distance, status";
        if ($this->hasColumn('assigned_driver_id')) $sel .= ", assigned_driver_id AS assignedDriverId";
        if ($this->hasColumn('assigned_vehicle_no')) $sel .= ", assigned_vehicle_no AS assignedVehicleNo";
        $sel .= ", created_at, updated_at";
        $query = "SELECT " . $sel . " FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(array($id));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get full details of a booking including tourist info
     */
    public function getRequestByIdFull($id) {
        $query = "SELECT tr.*, 
                  tu.first_name AS tourist_first_name, tu.last_name AS tourist_last_name, 
                  tu.email AS tourist_email, tu.contact_number AS tourist_contact" .
                  ($this->hasColumn('assigned_vehicle_no') ? ",
                  tv.vehicle_type AS vehicle_type_id, tv.psg_capacity,
                  tvt.type_name AS vehicle_type_name" : "") .
                  " FROM " . $this->table . " tr
                  LEFT JOIN tourist_users tu ON tr.user_id = tu.id" .
                  ($this->hasColumn('assigned_vehicle_no') ? "
                  LEFT JOIN transport_vehicle tv ON tr.assigned_vehicle_no = tv.vehicle_no
                  LEFT JOIN transport_vehicle_types tvt ON tv.vehicle_type = tvt.type_id" : "") .
                  " WHERE tr.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all pending bookings assigned to a specific driver
     */
    public function getPendingByDriverId($driverId) {
        if (!$this->hasColumn('assigned_driver_id')) return array();
        $query = "SELECT tr.id, tr.customer_name, tr.contact_number, tr.vehicle_type, 
                  tr.date, tr.pickup_time, tr.pickup_location, tr.dropoff_location,
                  tr.num_people, tr.notes, tr.estimated_fare, tr.distance, tr.status,
                  " . ($this->hasColumn('assigned_vehicle_no') ? "tr.assigned_vehicle_no, " : "") . "tr.created_at,
                  tu.first_name AS tourist_first_name, tu.last_name AS tourist_last_name,
                  tu.email AS tourist_email
                  FROM " . $this->table . " tr
                  LEFT JOIN tourist_users tu ON tr.user_id = tu.id
                  WHERE TRIM(tr.assigned_driver_id) = TRIM(:driver_id)
                    AND tr.status = 'pending'
                  ORDER BY tr.date ASC, tr.pickup_time ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":driver_id", $driverId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get confirmed (upcoming) bookings for a driver
     */
    public function getConfirmedByDriverId($driverId) {
        if (!$this->hasColumn('assigned_driver_id')) return array();
        $query = "SELECT tr.id, tr.customer_name, tr.contact_number, tr.vehicle_type, 
                  tr.date, tr.pickup_time, tr.pickup_location, tr.dropoff_location,
                  tr.num_people, tr.estimated_fare, tr.status,
                  " . ($this->hasColumn('assigned_vehicle_no') ? "tr.assigned_vehicle_no, " : "") . "tr.created_at
                  FROM " . $this->table . " tr
                  WHERE TRIM(tr.assigned_driver_id) = TRIM(:driver_id)
                    AND tr.status = 'confirmed'
                    AND CONCAT(tr.date, ' ', tr.pickup_time) > NOW()
                  ORDER BY tr.date ASC, tr.pickup_time ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":driver_id", $driverId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllRequests() {
        $sel = "id, user_id, customer_name AS customerName, contact_number AS contactNumber, vehicle_type AS vehicleType,
                  date, pickup_time AS pickupTime, pickup_location AS pickupLocation, dropoff_location AS dropoffLocation,
                  num_people AS numPeople, notes, estimated_fare AS estimatedFare, distance, status,
                  created_at, updated_at";
        if ($this->hasColumn('assigned_driver_id')) $sel .= ", assigned_driver_id AS assignedDriverId";
        if ($this->hasColumn('assigned_vehicle_no')) $sel .= ", assigned_vehicle_no AS assignedVehicleNo";
        $query = "SELECT " . $sel . " FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update status of a request (confirm, cancel, complete)
     */
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Update driver assignment for a request
     */
    public function assignDriver($id, $driverId, $vehicleNo) {
        if (!$this->hasColumn('assigned_driver_id')) return false;
        $set = "assigned_driver_id = :driver_id";
        if ($this->hasColumn('assigned_vehicle_no')) {
            $set .= ", assigned_vehicle_no = :vehicle_no";
        }
        $query = "UPDATE " . $this->table . " SET " . $set . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":driver_id", $driverId);
        if ($this->hasColumn('assigned_vehicle_no')) $stmt->bindParam(":vehicle_no", $vehicleNo);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Get cancelled bookings for a driver
     */
    public function getCancelledByDriverId($driverId) {
        if (!$this->hasColumn('assigned_driver_id')) return array();
        $query = "SELECT tr.id, tr.customer_name, tr.contact_number, tr.vehicle_type, 
                  tr.date, tr.pickup_time, tr.pickup_location, tr.dropoff_location,
                  tr.num_people, tr.notes, tr.estimated_fare, tr.distance, tr.status,
                  " . ($this->hasColumn('assigned_vehicle_no') ? "tr.assigned_vehicle_no, " : "") . "tr.created_at,
                  tu.first_name AS tourist_first_name, tu.last_name AS tourist_last_name,
                  tu.email AS tourist_email
                  FROM " . $this->table . " tr
                  LEFT JOIN tourist_users tu ON tr.user_id = tu.id
                  WHERE TRIM(tr.assigned_driver_id) = TRIM(:driver_id)
                    AND tr.status = 'cancelled'
                  ORDER BY tr.date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":driver_id", $driverId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get completed bookings for payment history (past confirmed bookings)
     */
    public function getPaymentsByDriverId($driverId) {
        if (!$this->hasColumn('assigned_driver_id')) return array();
        $query = "SELECT tr.id, tr.customer_name, tr.contact_number, tr.vehicle_type, 
                  tr.date, tr.pickup_time, tr.pickup_location, tr.dropoff_location,
                  tr.num_people, tr.estimated_fare, tr.distance, tr.status,
                  " . ($this->hasColumn('assigned_vehicle_no') ? "tr.assigned_vehicle_no, " : "") . "tr.created_at
                  FROM " . $this->table . " tr
                  WHERE TRIM(tr.assigned_driver_id) = TRIM(:driver_id)
                    AND tr.status IN ('confirmed', 'completed')
                  ORDER BY tr.date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":driver_id", $driverId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
