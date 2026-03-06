<?php
class TransportRequestModel {
    private $conn;
    private $table = "transport_requests";

    public $id;
    public $user_id;
    public $customer_name;
    public $contact_number;
    public $date;
    public $num_people;
    public $vehicle_type;
    public $pickup_location;
    public $pickup_time;
    public $dropoff_location;
    public $notes;
    public $estimated_fare;
    public $distance;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . "
                  (user_id, customer_name, contact_number, date, num_people, vehicle_type, 
                   pickup_location, pickup_time, dropoff_location, notes, estimated_fare, distance, status)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isssissssdds", 
            $this->user_id,
            $this->customer_name,
            $this->contact_number,
            $this->date,
            $this->num_people,
            $this->vehicle_type,
            $this->pickup_location,
            $this->pickup_time,
            $this->dropoff_location,
            $this->notes,
            $this->estimated_fare,
            $this->distance,
            $this->status
        );

        if ($stmt->execute()) {
            $this->id = $stmt->insert_id;
            return true;
        }
        return false;
    }

    public function getByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE user_id = ? 
                  ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getAll() {
        $query = "SELECT tr.*, tu.first_name, tu.last_name, tu.email 
                  FROM " . $this->table . " tr
                  LEFT JOIN tourist_users tu ON tr.user_id = tu.id
                  ORDER BY tr.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Get all transport requests for a user with driver and vehicle details
     */
    public function getByUserIdWithDriverDetails($user_id) {
        $query = "SELECT tr.*, 
                  tu.full_name AS driver_name, tu.contact_no AS driver_contact, tu.profile_image AS driver_image,
                  tv.vehicle_no AS v_vehicle_no, tv.vehicle_type AS v_vehicle_type, 
                  tv.image AS vehicle_image, tv.psg_capacity AS v_psg_capacity,
                  tvt.type_name AS vehicle_type_name
                  FROM " . $this->table . " tr
                  LEFT JOIN transport_users tu ON TRIM(tr.assigned_driver_id) = TRIM(tu.user_id)
                  LEFT JOIN transport_vehicle tv ON tr.assigned_vehicle_no = tv.vehicle_no
                  LEFT JOIN transport_vehicle_types tvt ON tv.vehicle_type = tvt.type_id
                  WHERE tr.user_id = ?
                  ORDER BY tr.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
