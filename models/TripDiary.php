<?php
class TripDiary {
    private $conn;
    private $table = "trip_diary_entries";

    public $id;
    public $tourist_id;
    public $title;
    public $content;
    public $location;
    public $entry_date;
    public $is_public;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addEntry() {
        $query = "INSERT INTO " . $this->table . "
                  (tourist_id, title, content, location, entry_date, is_public, created_at)
                  VALUES (:tourist_id, :title, :content, :location, :entry_date, :is_public, NOW())";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":tourist_id", $this->tourist_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":location", $this->location);
        $stmt->bindParam(":entry_date", $this->entry_date);
        $stmt->bindParam(":is_public", $this->is_public);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function getEntryById($id) {
        $query = "SELECT tde.*, tu.first_name, tu.last_name 
                  FROM " . $this->table . " tde
                  JOIN tourist_users tu ON tde.tourist_id = tu.id
                  WHERE tde.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getEntriesByTouristId($tourist_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE tourist_id = ? 
                  ORDER BY entry_date DESC, created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$tourist_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPublicEntries() {
        $query = "SELECT tde.*, tu.first_name, tu.last_name 
                  FROM " . $this->table . " tde
                  JOIN tourist_users tu ON tde.tourist_id = tu.id
                  WHERE tde.is_public = 1 
                  ORDER BY tde.entry_date DESC, tde.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateEntry() {
        $query = "UPDATE " . $this->table . " SET
                  title = :title,
                  content = :content,
                  location = :location,
                  entry_date = :entry_date,
                  is_public = :is_public,
                  updated_at = NOW()
                  WHERE id = :id AND tourist_id = :tourist_id";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":tourist_id", $this->tourist_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":location", $this->location);
        $stmt->bindParam(":entry_date", $this->entry_date);
        $stmt->bindParam(":is_public", $this->is_public);

        return $stmt->execute();
    }

    public function deleteEntry($id, $tourist_id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ? AND tourist_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id, $tourist_id]);
    }

    // Get all entries for a specific tourist (for trip view)
    public function getTripEntriesByTouristId($tourist_id) {
        $query = "SELECT tde.*, tu.first_name, tu.last_name 
                  FROM " . $this->table . " tde
                  JOIN tourist_users tu ON tde.tourist_id = tu.id
                  WHERE tde.tourist_id = ? 
                  ORDER BY tde.entry_date ASC, tde.created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$tourist_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get public trips (grouped by tourist)
    public function getPublicTrips() {
        $query = "SELECT DISTINCT tde.tourist_id, tu.first_name, tu.last_name,
                         MIN(tde.entry_date) as trip_start,
                         MAX(tde.entry_date) as trip_end,
                         COUNT(tde.id) as entry_count,
                         GROUP_CONCAT(DISTINCT tde.location ORDER BY tde.entry_date SEPARATOR ', ') as locations
                  FROM " . $this->table . " tde
                  JOIN tourist_users tu ON tde.tourist_id = tu.id
                  WHERE tde.is_public = 1 
                  GROUP BY tde.tourist_id, tu.first_name, tu.last_name
                  ORDER BY trip_start DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get unique locations for a tourist's trip
    public function getTripLocations($tourist_id) {
        $query = "SELECT DISTINCT location, entry_date 
                  FROM " . $this->table . " 
                  WHERE tourist_id = ? AND location IS NOT NULL AND location != ''
                  ORDER BY entry_date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$tourist_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>


