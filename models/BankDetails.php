<?php
class BankDetails {
    private $conn;
    private $table = "transport_provider_acc_details";

    public $ref_id;
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

    // Get bank details by ref_id (user_id)
    public function getBankDetailsByRefId($ref_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE ref_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$ref_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update or insert bank details
    public function saveBankDetails() {
        // Use INSERT ON DUPLICATE KEY UPDATE to handle both new and existing records
        $query = "INSERT INTO " . $this->table . " 
                  (ref_id, bank_name, acc_no, acc_holder_name, branch_name)
                  VALUES (:ref_id, :bank_name, :acc_no, :acc_holder_name, :branch_name)
                  ON DUPLICATE KEY UPDATE
                  bank_name = VALUES(bank_name),
                  acc_no = VALUES(acc_no),
                  acc_holder_name = VALUES(acc_holder_name),
                  branch_name = VALUES(branch_name)";
        
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
