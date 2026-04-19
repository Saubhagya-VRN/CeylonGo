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

    /**
     * Get filtered report data with date range support
     * Returns overall stats, monthly breakdown, and individual tour details
     */
    public function getFilteredReportData($driverId, $startDate = null, $endDate = null) {
        try {
            $dateCondition = "";
            $params = [$driverId];

            if ($startDate && $endDate) {
                $dateCondition = " AND tr.date BETWEEN ? AND ?";
                $params[] = $startDate;
                $params[] = $endDate;
            }

            // Overall KPIs — all aggregations use IFNULL for NULL-safety
            $queryKPI = "SELECT 
                            IFNULL(COUNT(*), 0) as total_bookings,
                            IFNULL(SUM(CASE WHEN tr.status IN ('confirmed','completed') THEN tr.estimated_fare ELSE 0 END), 0) as total_revenue,
                            IFNULL(AVG(CASE WHEN tr.status IN ('confirmed','completed') THEN tr.estimated_fare END), 0) as avg_fare,
                            IFNULL(SUM(CASE WHEN tr.status IN ('confirmed','completed') THEN 1 ELSE 0 END), 0) as completed_count,
                            IFNULL(SUM(CASE WHEN tr.status = 'cancelled' THEN 1 ELSE 0 END), 0) as cancelled_count,
                            IFNULL(SUM(CASE WHEN tr.status = 'pending' THEN 1 ELSE 0 END), 0) as pending_count,
                            IFNULL(SUM(CASE WHEN tr.status IN ('confirmed','completed') THEN tr.distance ELSE 0 END), 0) as total_distance,
                            IFNULL(SUM(CASE WHEN tr.status IN ('confirmed','completed') THEN tr.num_people ELSE 0 END), 0) as total_passengers
                         FROM " . $this->table . " tr
                         WHERE TRIM(tr.assigned_driver_id) = TRIM(?)" . $dateCondition;
            $stmtKPI = $this->conn->prepare($queryKPI);
            $stmtKPI->execute($params);
            $kpi = $stmtKPI->fetch(PDO::FETCH_ASSOC);

            // Fallback if fetch returns false/null
            if (!$kpi) {
                $kpi = [
                    'total_bookings' => 0, 'total_revenue' => 0, 'avg_fare' => 0,
                    'completed_count' => 0, 'cancelled_count' => 0, 'pending_count' => 0,
                    'total_distance' => 0, 'total_passengers' => 0
                ];
            }

            // Completion rate
            $kpi['completion_rate'] = $kpi['total_bookings'] > 0
                ? round(($kpi['completed_count'] / $kpi['total_bookings']) * 100, 1)
                : 0;

            // Monthly breakdown
            $paramsMonthly = [$driverId];
            $dateCondMonthly = "";
            if ($startDate && $endDate) {
                $dateCondMonthly = " AND tr.date BETWEEN ? AND ?";
                $paramsMonthly[] = $startDate;
                $paramsMonthly[] = $endDate;
            }

            $queryMonthly = "SELECT 
                                DATE_FORMAT(tr.date, '%Y-%m') as month,
                                COUNT(*) as bookings,
                                IFNULL(SUM(CASE WHEN tr.status IN ('confirmed','completed') THEN tr.estimated_fare ELSE 0 END), 0) as revenue,
                                SUM(CASE WHEN tr.status IN ('confirmed','completed') THEN 1 ELSE 0 END) as completed,
                                SUM(CASE WHEN tr.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                             FROM " . $this->table . " tr
                             WHERE TRIM(tr.assigned_driver_id) = TRIM(?)" . $dateCondMonthly . "
                             GROUP BY month
                             ORDER BY month ASC";
            $stmtMonthly = $this->conn->prepare($queryMonthly);
            $stmtMonthly->execute($paramsMonthly);
            $monthly = $stmtMonthly->fetchAll(PDO::FETCH_ASSOC);

            // Individual tour details for summary table
            $paramsTours = [$driverId];
            $dateCondTours = "";
            if ($startDate && $endDate) {
                $dateCondTours = " AND tr.date BETWEEN ? AND ?";
                $paramsTours[] = $startDate;
                $paramsTours[] = $endDate;
            }

            $queryTours = "SELECT tr.id, tr.customer_name AS customerName, tr.vehicle_type, tr.date, 
                                  tr.pickup_time AS time, tr.pickup_location, tr.dropoff_location,
                                  tr.num_people AS pax, tr.estimated_fare AS fare, tr.distance, tr.status,
                                  tr.assigned_vehicle_no
                           FROM " . $this->table . " tr
                           WHERE TRIM(tr.assigned_driver_id) = TRIM(?)" . $dateCondTours . "
                           ORDER BY tr.date DESC";
            $stmtTours = $this->conn->prepare($queryTours);
            $stmtTours->execute($paramsTours);
            $tours = $stmtTours->fetchAll(PDO::FETCH_ASSOC);

            return [
                'kpi' => $kpi,
                'monthly' => $monthly,
                'tours' => $tours
            ];
        } catch (PDOException $e) {
            error_log("Error fetching filtered transport report data: " . $e->getMessage());
            return [
                'kpi' => [
                    'total_bookings' => 0, 'total_revenue' => 0, 'avg_fare' => 0,
                    'completed_count' => 0, 'cancelled_count' => 0, 'pending_count' => 0,
                    'total_distance' => 0, 'total_passengers' => 0, 'completion_rate' => 0
                ],
                'monthly' => [],
                'tours' => []
            ];
        }
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
