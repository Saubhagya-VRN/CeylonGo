<?php
class GuideBankDetails {
    private $conn;
    private $table = "guide_acc_details";

    public $ref_id;  // This links to guide_users table id
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
                  (ref_id, bank_name, acc_no, acc_holder_name, branch_name)
                  VALUES (:ref_id, :bank_name, :acc_no, :acc_holder_name, :branch_name)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":ref_id", $this->ref_id);
        $stmt->bindParam(":bank_name", $this->bank_name);
        $stmt->bindParam(":acc_no", $this->acc_no);
        $stmt->bindParam(":acc_holder_name", $this->acc_holder_name);
        $stmt->bindParam(":branch_name", $this->branch_name);

        return $stmt->execute();
    }

    // Get bank details by ref_id (guide user_id)
    public function getBankDetailsById($ref_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE ref_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$ref_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update or insert bank details
    public function saveBankDetails() {
        // Check if record exists
        $checkQuery = "SELECT id FROM " . $this->table . " WHERE ref_id = ?";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->execute([$this->ref_id]);
        
        if ($checkStmt->fetch()) {
            // Update existing record
            $query = "UPDATE " . $this->table . " SET 
                      bank_name = :bank_name,
                      acc_no = :acc_no,
                      acc_holder_name = :acc_holder_name,
                      branch_name = :branch_name
                      WHERE ref_id = :ref_id";
        } else {
            // Insert new record
            $query = "INSERT INTO " . $this->table . " 
                      (ref_id, bank_name, acc_no, acc_holder_name, branch_name)
                      VALUES (:ref_id, :bank_name, :acc_no, :acc_holder_name, :branch_name)";
        }
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":ref_id", $this->ref_id);
        $stmt->bindParam(":bank_name", $this->bank_name);
        $stmt->bindParam(":acc_no", $this->acc_no);
        $stmt->bindParam(":acc_holder_name", $this->acc_holder_name);
        $stmt->bindParam(":branch_name", $this->branch_name);
        
        return $stmt->execute();
    }

    // Delete bank details
    public function deleteBankDetails($ref_id) {
        $query = "DELETE FROM " . $this->table . " WHERE ref_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$ref_id]);
    }
}
?>

