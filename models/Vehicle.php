<?php
class Vehicle {
    private $conn;
    public $table = "transport_vehicle";

    public $vehicle_no;
    public $user_id;
    public $vehicle_type;
    public $image;
    public $psg_capacity;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Add Vehicle
    public function addVehicle() {
        $query = "INSERT INTO " . $this->table . "
                  (vehicle_no, user_id, vehicle_type, image, psg_capacity)
                  VALUES (:vehicle_no, :user_id, :vehicle_type, :image, :psg_capacity)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":vehicle_no", $this->vehicle_no);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":vehicle_type", $this->vehicle_type);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":psg_capacity", $this->psg_capacity);

        return $stmt->execute();
    }

    // Update Vehicle
    public function updateVehicle($old_vehicle_no) {
        $query = "UPDATE " . $this->table . " SET
                  vehicle_no = :vehicle_no,
                  vehicle_type = :vehicle_type,
                  psg_capacity = :psg_capacity";

        if (!empty($this->image)) {
            $query .= ", image = :image";
        }

        $query .= " WHERE vehicle_no = :old_vehicle_no AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":vehicle_no", $this->vehicle_no);
        $stmt->bindParam(":vehicle_type", $this->vehicle_type);
        $stmt->bindParam(":psg_capacity", $this->psg_capacity);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":old_vehicle_no", $old_vehicle_no);

        if (!empty($this->image)) {
            $stmt->bindParam(":image", $this->image);
        }

        return $stmt->execute();
    }

    // Delete Vehicle (also deletes uploaded image)
    public function deleteVehicle($vehicle_no, $user_id) {
        // Get image first
        $query = "SELECT image FROM " . $this->table . "
                  WHERE vehicle_no = :vehicle_no AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":vehicle_no", $vehicle_no);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

        // Delete vehicle
        $deleteQuery = "DELETE FROM " . $this->table . "
                        WHERE vehicle_no = :vehicle_no AND user_id = :user_id";
        $deleteStmt = $this->conn->prepare($deleteQuery);
        $deleteStmt->bindParam(":vehicle_no", $vehicle_no);
        $deleteStmt->bindParam(":user_id", $user_id);
        $result = $deleteStmt->execute();

        // Delete uploaded image
        if ($result && $vehicle && !empty($vehicle['image'])) {
            $filePath = __DIR__ . "/../../uploads/" . $vehicle['image'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        return $result;
    }

    // Fetch all vehicles for a user
    public function getVehiclesByUser($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find an available vehicle of a given type with enough capacity.
     * Excludes drivers who already have a confirmed booking on the same date.
     *
     * @param string $vehicleTypeId  The vehicle_type ID from transport_vehicle_types
     * @param string $date           The requested date (YYYY-MM-DD)
     * @param int    $numPeople      Minimum passenger capacity needed
     * @return array|null  ['vehicle_no' => ..., 'user_id' => ..., ...] or null
     */
    /**
     * Find an available vehicle of a given type with enough capacity.
     * Prioritizes drivers by: highest average review rating, then most completed trips.
     * Excludes drivers who already have a confirmed/pending booking on the same date.
     * Optionally excludes specific driver IDs (e.g., when re-assigning after rejection).
     *
     * @param string $vehicleTypeId  The vehicle_type ID from transport_vehicle_types
     * @param string $date           The requested date (YYYY-MM-DD)
     * @param int    $numPeople      Minimum passenger capacity needed
     * @param array  $excludeDrivers Driver IDs to exclude from selection
     * @return array|null  ['vehicle_no' => ..., 'user_id' => ..., ...] or null
     */
    public function findAvailableVehicle($vehicleTypeId, $date, $numPeople, $excludeDrivers = []) {
        // Build exclude clause
        $excludeClause = "";
        if (!empty($excludeDrivers)) {
            $placeholders = [];
            foreach ($excludeDrivers as $i => $id) {
                $placeholders[] = ":exclude_$i";
            }
            $excludeClause = " AND TRIM(v.user_id) NOT IN (" . implode(',', $placeholders) . ")";
        }

        $query = "SELECT v.vehicle_no, v.user_id, v.vehicle_type, v.psg_capacity, v.image AS vehicle_image,
                  tu.full_name AS driver_name, tu.contact_no AS driver_contact, tu.profile_image AS driver_image,
                  COALESCE(rv.avg_rating, 0) AS avg_rating,
                  COALESCE(rv.review_count, 0) AS review_count,
                  COALESCE(ct.completed_trips, 0) AS completed_trips
                  FROM " . $this->table . " v
                  INNER JOIN transport_users tu ON TRIM(v.user_id) = TRIM(tu.user_id)
                  LEFT JOIN (
                      SELECT driver_id, AVG(rating) AS avg_rating, COUNT(*) AS review_count
                      FROM transport_reviews
                      GROUP BY driver_id
                  ) rv ON TRIM(v.user_id) = TRIM(rv.driver_id)
                  LEFT JOIN (
                      SELECT assigned_driver_id, COUNT(*) AS completed_trips
                      FROM transport_requests
                      WHERE status = 'completed'
                      GROUP BY assigned_driver_id
                  ) ct ON TRIM(v.user_id) = TRIM(ct.assigned_driver_id)
                  WHERE v.vehicle_type = :vehicle_type
                    AND v.psg_capacity >= :num_people
                    AND TRIM(v.user_id) NOT IN (
                        SELECT TRIM(tr.assigned_driver_id)
                        FROM transport_requests tr
                        WHERE tr.assigned_driver_id IS NOT NULL
                          AND tr.status IN ('confirmed', 'pending')
                          AND tr.date = :booking_date
                    )
                    $excludeClause
                  ORDER BY avg_rating DESC, review_count DESC, completed_trips DESC, v.psg_capacity ASC
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":vehicle_type", $vehicleTypeId);
        $stmt->bindParam(":num_people", $numPeople, PDO::PARAM_INT);
        $stmt->bindParam(":booking_date", $date);
        
        // Bind exclude params
        foreach ($excludeDrivers as $i => $id) {
            $stmt->bindValue(":exclude_$i", trim($id));
        }
        
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}
