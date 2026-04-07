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
    public $assignedDriverId;
    public $assignedVehicleNo;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addRequest() {
        $query = "INSERT INTO " . $this->table . "
                  (user_id, customer_name, contact_number, vehicle_type, date, pickup_time, pickup_location, dropoff_location, num_people, notes, estimated_fare, distance, assigned_driver_id, assigned_vehicle_no, status)
                  VALUES (:user_id, :customer_name, :contact_number, :vehicle_type, :date, :pickup_time, :pickup_location, :dropoff_location, :num_people, :notes, :estimated_fare, :distance, :assigned_driver_id, :assigned_vehicle_no, :status)";
        
        $stmt = $this->conn->prepare($query);

        $notes = $this->notes ?: null;
        $estimatedFare = $this->estimatedFare !== null && $this->estimatedFare !== '' ? (float) $this->estimatedFare : null;
        $distance = $this->distance !== null && $this->distance !== '' ? (float) $this->distance : null;
        $assignedDriverId = $this->assignedDriverId ?: null;
        $assignedVehicleNo = $this->assignedVehicleNo ?: null;
        $status = $this->status ?? 'pending';

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
        $stmt->bindParam(":assigned_driver_id", $assignedDriverId);
        $stmt->bindParam(":assigned_vehicle_no", $assignedVehicleNo);
        $stmt->bindParam(":status", $status);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function getRequestById($id) {
        $query = "SELECT id, user_id, customer_name AS customerName, contact_number AS contactNumber, vehicle_type AS vehicleType,
                  date, pickup_time AS pickupTime, pickup_location AS pickupLocation, dropoff_location AS dropoffLocation,
                  num_people AS numPeople, notes, estimated_fare AS estimatedFare, distance, status,
                  assigned_driver_id AS assignedDriverId, assigned_vehicle_no AS assignedVehicleNo,
                  created_at, updated_at
                  FROM " . $this->table . " WHERE id = ?";
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
                  tu.email AS tourist_email, tu.contact_number AS tourist_contact,
                  tv.vehicle_type AS vehicle_type_id, tv.psg_capacity,
                  tvt.type_name AS vehicle_type_name
                  FROM " . $this->table . " tr
                  LEFT JOIN tourist_users tu ON tr.user_id = tu.id
                  LEFT JOIN transport_vehicle tv ON tr.assigned_vehicle_no = tv.vehicle_no
                  LEFT JOIN transport_vehicle_types tvt ON tv.vehicle_type = tvt.type_id
                  WHERE tr.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all pending bookings assigned to a specific driver
     */
    public function getPendingByDriverId($driverId) {
        $query = "SELECT tr.id, tr.customer_name, tr.contact_number, tr.vehicle_type, 
                  tr.date, tr.pickup_time, tr.pickup_location, tr.dropoff_location,
                  tr.num_people, tr.notes, tr.estimated_fare, tr.distance, tr.status,
                  tr.assigned_vehicle_no, tr.created_at,
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
        $query = "SELECT tr.id, tr.customer_name, tr.contact_number, tr.vehicle_type, 
                  tr.date, tr.pickup_time, tr.pickup_location, tr.dropoff_location,
                  tr.num_people, tr.estimated_fare, tr.status,
                  tr.assigned_vehicle_no, tr.created_at
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
        $query = "SELECT id, user_id, customer_name AS customerName, contact_number AS contactNumber, vehicle_type AS vehicleType,
                  date, pickup_time AS pickupTime, pickup_location AS pickupLocation, dropoff_location AS dropoffLocation,
                  num_people AS numPeople, notes, estimated_fare AS estimatedFare, distance, status,
                  assigned_driver_id AS assignedDriverId, assigned_vehicle_no AS assignedVehicleNo,
                  created_at, updated_at
                  FROM " . $this->table . " ORDER BY id DESC";
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
        $query = "UPDATE " . $this->table . " 
                  SET assigned_driver_id = :driver_id, assigned_vehicle_no = :vehicle_no 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":driver_id", $driverId);
        $stmt->bindParam(":vehicle_no", $vehicleNo);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Get cancelled bookings for a driver
     */
    public function getCancelledByDriverId($driverId) {
        $query = "SELECT tr.id, tr.customer_name, tr.contact_number, tr.vehicle_type, 
                  tr.date, tr.pickup_time, tr.pickup_location, tr.dropoff_location,
                  tr.num_people, tr.notes, tr.estimated_fare, tr.distance, tr.status,
                  tr.assigned_vehicle_no, tr.created_at,
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
        $query = "SELECT tr.id, tr.customer_name, tr.contact_number, tr.vehicle_type, 
                  tr.date, tr.pickup_time, tr.pickup_location, tr.dropoff_location,
                  tr.num_people, tr.estimated_fare, tr.distance, tr.status,
                  tr.assigned_vehicle_no, tr.created_at
                  FROM " . $this->table . " tr
                  WHERE TRIM(tr.assigned_driver_id) = TRIM(:driver_id)
                    AND tr.status IN ('confirmed', 'completed')
                  ORDER BY tr.date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":driver_id", $driverId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get monthly revenue and booking count for reporting
     */
    public function getReportData($driverId) {
        try {
            // Get total bookings (confirmed or completed)
            $queryTotal = "SELECT COUNT(*) as total_bookings, IFNULL(SUM(estimated_fare), 0) as total_revenue 
                          FROM " . $this->table . " 
                          WHERE TRIM(assigned_driver_id) = TRIM(?) 
                            AND status IN ('confirmed', 'completed')";
            $stmtTotal = $this->conn->prepare($queryTotal);
            $stmtTotal->execute([$driverId]);
            $overall = $stmtTotal->fetch(PDO::FETCH_ASSOC);

            // Get monthly breakdown for last 12 months
            $queryMonthly = "SELECT 
                                DATE_FORMAT(date, '%Y-%m') as month,
                                COUNT(*) as bookings,
                                IFNULL(SUM(estimated_fare), 0) as revenue
                             FROM " . $this->table . "
                             WHERE TRIM(assigned_driver_id) = TRIM(?)
                               AND status IN ('confirmed', 'completed')
                               AND date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                             GROUP BY month
                             ORDER BY month ASC";
            $stmtMonthly = $this->conn->prepare($queryMonthly);
            $stmtMonthly->execute([$driverId]);
            $monthly = $stmtMonthly->fetchAll(PDO::FETCH_ASSOC);

            return [
                'overall' => $overall,
                'monthly' => $monthly
            ];
        } catch (PDOException $e) {
            error_log("Error fetching transport report data: " . $e->getMessage());
            return ['overall' => ['total_bookings' => 0, 'total_revenue' => 0], 'monthly' => []];
        }
    }
}
?>
