<?php
class License {
    private $conn;
    private $table = "transport_license"; // ✅ updated to match actual database

    public $license_no;
    public $license_exp_date;
    public $image;
    public $driver_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addLicense() {
        $query = "INSERT INTO " . $this->table . "
                  (license_no, license_exp_date, image, driver_id)
                  VALUES (:license_no, :license_exp_date, :image, :driver_id)";
        $stmt = $this->conn->prepare($query);

        $trimmedDriverId = trim($this->driver_id);
        $stmt->bindParam(":license_no", $this->license_no);
        $stmt->bindParam(":license_exp_date", $this->license_exp_date);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":driver_id", $trimmedDriverId);

        return $stmt->execute();
    }

    // Get license by driver_id
    public function getLicenseByDriverId($driver_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE TRIM(driver_id) = TRIM(?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([trim($driver_id)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update or insert license information
    public function updateLicense() {
        // Check if a license already exists for this driver
        $checkQuery = "SELECT license_no FROM " . $this->table . " WHERE TRIM(driver_id) = TRIM(:driver_id)";
        $checkStmt = $this->conn->prepare($checkQuery);
        $trimmedDriverId = trim($this->driver_id);
        $checkStmt->bindParam(":driver_id", $trimmedDriverId);
        $checkStmt->execute();
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Update existing license
            $query = "UPDATE " . $this->table . " SET 
                      license_no = :license_no,
                      license_exp_date = :license_exp_date
                      WHERE TRIM(driver_id) = TRIM(:driver_id)";
        } else {
            // Insert new license
            $query = "INSERT INTO " . $this->table . " 
                      (license_no, license_exp_date, image, driver_id)
                      VALUES (:license_no, :license_exp_date, :image, :driver_id)";
        }
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":license_no", $this->license_no);
        $stmt->bindParam(":license_exp_date", $this->license_exp_date);
        $stmt->bindParam(":driver_id", $trimmedDriverId);
        
        if (!$existing) {
            $stmt->bindParam(":image", $this->image);
        }
        
        return $stmt->execute();
    }
}
?>
