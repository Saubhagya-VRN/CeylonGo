<?php
class GuideRequest {
    private $conn;
    private $table = "guide_requests";

    public $id;
    public $tourist_id;
    public $customerName;
    public $contactNumber;
    public $location;
    public $language;
    public $date;
    public $time;
    public $notes;
    public $status;
    public $fee;
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
            tourist_id INT NULL,
            customerName VARCHAR(255) NOT NULL,
            contactNumber VARCHAR(20) NOT NULL,
            location VARCHAR(255) NOT NULL,
            language VARCHAR(100) NOT NULL,
            date DATE NOT NULL,
            time TIME NOT NULL,
            notes TEXT,
            status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
            fee DECIMAL(10, 2) DEFAULT 0.00,
            guide_id INT NULL,
            approved_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_status (status),
            INDEX idx_guide_id (guide_id),
            INDEX idx_tourist_id (tourist_id)
        )";
        
        try {
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute();
            
            // Add tourist_id column if table already exists but column doesn't
            try {
                $alter = "ALTER TABLE " . $this->table . " ADD COLUMN tourist_id INT NULL AFTER id";
                $this->conn->exec($alter);
            } catch (PDOException $e) {
                // Column already exists, ignore
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error creating guide requests table: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add new guide request (includes tourist_id and guide_id)
     */
    public function create() {
        // Ensure table exists
        $this->createTable();

        $query = "INSERT INTO " . $this->table . "
                  (tourist_id, customerName, contactNumber, location, language, date, time, notes, status, fee, guide_id)
                  VALUES (:tourist_id, :customerName, :contactNumber, :location, :language, :date, :time, :notes, :status, :fee, :guide_id)";
        
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
        $this->fee = $this->fee ?? 3000.00;
        $this->tourist_id = $this->tourist_id ?? null;
        $this->guide_id = $this->guide_id ?? null;

        $stmt->bindParam(":tourist_id", $this->tourist_id);
        $stmt->bindParam(":customerName", $this->customerName);
        $stmt->bindParam(":contactNumber", $this->contactNumber);
        $stmt->bindParam(":location", $this->location);
        $stmt->bindParam(":language", $this->language);
        $stmt->bindParam(":date", $this->date);
        $stmt->bindParam(":time", $this->time);
        $stmt->bindParam(":notes", $this->notes);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":fee", $this->fee);
        $stmt->bindParam(":guide_id", $this->guide_id);

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
     * Get requests by tourist ID (for tourist's own report)
     */
    public function getRequestsByTourist($tourist_id) {
        try {
            $query = "SELECT gr.*, 
                        CONCAT(g.first_name, ' ', g.last_name) as guide_name
                      FROM " . $this->table . " gr
                      LEFT JOIN guide_users g ON gr.guide_id = g.id
                      WHERE gr.tourist_id = ? 
                      ORDER BY gr.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$tourist_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching tourist guide requests: " . $e->getMessage());
            return [];
        }
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
     * Update request status. Sets approved_at timestamp when approving.
     */
    public function updateStatus($id, $status) {
        try {
            if ($status === 'approved') {
                $query = "UPDATE " . $this->table . " SET status = ?, approved_at = NOW() WHERE id = ?";
            } else {
                $query = "UPDATE " . $this->table . " SET status = ? WHERE id = ?";
            }
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$status, $id]);
        } catch (PDOException $e) {
            error_log("Error updating guide request status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reassign a request to a different guide (used after rejection)
     */
    public function reassignGuide($id, $guide_id) {
        try {
            $query = "UPDATE " . $this->table . " SET guide_id = ?, status = 'pending' WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$guide_id, $id]);
        } catch (PDOException $e) {
            error_log("Error reassigning guide: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find an available guide matching the requested language.
     * Excludes guides whose IDs are in $excludeIds.
     * Returns guide data array or null.
     */
    public function findAvailableGuide($language, $excludeIds = []) {
        try {
            $query = "SELECT * FROM guide_users WHERE languages LIKE ?";
            $params = ['%' . $language . '%'];

            if (!empty($excludeIds)) {
                $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
                $query .= " AND id NOT IN ($placeholders)";
                $params = array_merge($params, $excludeIds);
            }

            $query .= " ORDER BY RAND() LIMIT 1";

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Error finding available guide: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get upcoming (approved) bookings for a guide where date >= today
     */
    public function getUpcomingByGuide($guide_id) {
        try {
            $query = "SELECT * FROM " . $this->table . " 
                      WHERE guide_id = ? AND status = 'approved' 
                      AND CONCAT(date, ' ', time) > NOW() 
                      ORDER BY date ASC, time ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$guide_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Table might not exist, try creating it
            $this->createTable();
            try {
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$guide_id]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e2) {
                error_log("Error fetching upcoming bookings: " . $e2->getMessage());
                return [];
            }
        }
    }

    /**
     * Get pending bookings for a guide
     */
    public function getPendingByGuide($guide_id) {
        try {
            $query = "SELECT * FROM " . $this->table . " 
                      WHERE guide_id = ? AND status = 'pending' 
                      ORDER BY date ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$guide_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->createTable();
            try {
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$guide_id]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e2) {
                error_log("Error fetching pending bookings: " . $e2->getMessage());
                return [];
            }
        }
    }

    /**
     * Get cancelled/rejected bookings for a guide
     */
    public function getCancelledByGuide($guide_id) {
        try {
            $query = "SELECT * FROM " . $this->table . " 
                      WHERE guide_id = ? AND status = 'rejected' 
                      ORDER BY date DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$guide_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->createTable();
            try {
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$guide_id]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e2) {
                error_log("Error fetching cancelled bookings: " . $e2->getMessage());
                return [];
            }
        }
    }

    /**
     * Get all approved bookings for payment history
     */
    public function getPaymentsByGuide($guide_id) {
        try {
            $query = "SELECT * FROM " . $this->table . " 
                      WHERE guide_id = ? AND status = 'approved' 
                      ORDER BY date DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$guide_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->createTable();
            try {
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$guide_id]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e2) {
                error_log("Error fetching payment bookings: " . $e2->getMessage());
                return [];
            }
        }
    }
    
    /**
     * Get monthly revenue and booking count for reporting
     */
    public function getReportData($guide_id) {
        try {
            // Get total bookings (approved)
            $queryTotal = "SELECT COUNT(*) as total_bookings, IFNULL(SUM(fee), 0) as total_revenue 
                          FROM " . $this->table . " 
                          WHERE guide_id = ? AND status = 'approved'";
            $stmtTotal = $this->conn->prepare($queryTotal);
            $stmtTotal->execute([$guide_id]);
            $overall = $stmtTotal->fetch(PDO::FETCH_ASSOC);

            // Get monthly breakdown for last 12 months
            $queryMonthly = "SELECT 
                                DATE_FORMAT(date, '%Y-%m') as month,
                                COUNT(*) as bookings,
                                IFNULL(SUM(fee), 0) as revenue
                             FROM " . $this->table . "
                             WHERE guide_id = ? AND status = 'approved'
                             AND date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                             GROUP BY month
                             ORDER BY month ASC";
            $stmtMonthly = $this->conn->prepare($queryMonthly);
            $stmtMonthly->execute([$guide_id]);
            $monthly = $stmtMonthly->fetchAll(PDO::FETCH_ASSOC);

            return [
                'overall' => $overall,
                'monthly' => $monthly
            ];
        } catch (PDOException $e) {
            error_log("Error fetching guide report data: " . $e->getMessage());
            return ['overall' => ['total_bookings' => 0, 'total_revenue' => 0], 'monthly' => []];
        }
    }
}
?>
