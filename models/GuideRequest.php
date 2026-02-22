<?php
class GuideRequest {
    private $conn;
    private $table = "guide_requests";

    public $id;
    public $customerName;
    public $contactNumber;
    public $location;
    public $language;
    public $date;
    public $time;
    public $notes;
    public $status;
    public $guide_id;
    public $approved_at;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create the table if it doesn't exist
     */
    public function createTable() {
        $query = "CREATE TABLE IF NOT EXISTS " . $this->table . " (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customerName VARCHAR(255) NOT NULL,
            contactNumber VARCHAR(20) NOT NULL,
            location VARCHAR(255) NOT NULL,
            language VARCHAR(100) NOT NULL,
            date DATE NOT NULL,
            time TIME NOT NULL,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error creating guide requests table: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add new guide request
     */
    public function create() {
        // Ensure table exists
        $this->createTable();

        $query = "INSERT INTO " . $this->table . "
                  (customerName, contactNumber, location, language, date, time, notes, status)
                  VALUES (:customerName, :contactNumber, :location, :language, :date, :time, :notes, :status)";
        
        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->customerName = htmlspecialchars(strip_tags($this->customerName));
        $this->contactNumber = htmlspecialchars(strip_tags($this->contactNumber));
        $this->location = htmlspecialchars(strip_tags($this->location));
        $this->language = htmlspecialchars(strip_tags($this->language));
        $this->date = htmlspecialchars(strip_tags($this->date));
        $this->time = htmlspecialchars(strip_tags($this->time));
        $this->notes = htmlspecialchars(strip_tags($this->notes ?? ''));
        $this->status = $this->status ?? 'pending';

        $stmt->bindParam(":customerName", $this->customerName);
        $stmt->bindParam(":contactNumber", $this->contactNumber);
        $stmt->bindParam(":location", $this->location);
        $stmt->bindParam(":language", $this->language);
        $stmt->bindParam(":date", $this->date);
        $stmt->bindParam(":time", $this->time);
        $stmt->bindParam(":notes", $this->notes);
        $stmt->bindParam(":status", $this->status);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error creating guide request: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get request by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all requests
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get requests by customer name
     */
    public function getByCustomerName($customerName) {
        $query = "SELECT * FROM " . $this->table . " WHERE customerName = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$customerName]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete request by ID
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        try {
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting guide request: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update request status (if you add status field later)
     */
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        try {
            return $stmt->execute([$status, $id]);
        } catch (PDOException $e) {
            error_log("Error updating guide request status: " . $e->getMessage());
            return false;
        }
    }
}
?>
