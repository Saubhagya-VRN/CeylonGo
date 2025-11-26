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

        $stmt->bindParam(":license_no", $this->license_no);
        $stmt->bindParam(":license_exp_date", $this->license_exp_date);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":driver_id", $this->driver_id);

        return $stmt->execute();
    }

    // Get license by driver_id
    public function getLicenseByDriverId($driver_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE driver_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$driver_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
