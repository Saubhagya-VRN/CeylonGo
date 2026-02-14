<?php
class GuideBankDetails {
    private $conn;
    private $table = "tour_guide_acc_details";

    public $id;  // This links to users table id
    public $bank_name;
    public $acc_no;
    public $acc_holder_name;
    public $branch_name;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Add bank details
    public function addBankDetails() {
        $query = "INSERT INTO " . $this->table . "
                  (id, bank_name, acc_no, acc_holder_name, branch_name)
                  VALUES (:id, :bank_name, :acc_no, :acc_holder_name, :branch_name)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":bank_name", $this->bank_name);
        $stmt->bindParam(":acc_no", $this->acc_no);
        $stmt->bindParam(":acc_holder_name", $this->acc_holder_name);
        $stmt->bindParam(":branch_name", $this->branch_name);

        return $stmt->execute();
    }

    // Get bank details by id (user_id)
    public function getBankDetailsById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update or insert bank details
    public function saveBankDetails() {
        // Check if record exists
        $checkQuery = "SELECT id FROM " . $this->table . " WHERE id = ?";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->execute([$this->id]);
        
        if ($checkStmt->fetch()) {
            // Update existing record
            $query = "UPDATE " . $this->table . " SET 
                      bank_name = :bank_name,
                      acc_no = :acc_no,
                      acc_holder_name = :acc_holder_name,
                      branch_name = :branch_name
                      WHERE id = :id";
        } else {
            // Insert new record
            $query = "INSERT INTO " . $this->table . " 
                      (id, bank_name, acc_no, acc_holder_name, branch_name)
                      VALUES (:id, :bank_name, :acc_no, :acc_holder_name, :branch_name)";
        }
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":bank_name", $this->bank_name);
        $stmt->bindParam(":acc_no", $this->acc_no);
        $stmt->bindParam(":acc_holder_name", $this->acc_holder_name);
        $stmt->bindParam(":branch_name", $this->branch_name);
        
        return $stmt->execute();
    }

    // Delete bank details
    public function deleteBankDetails($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>

