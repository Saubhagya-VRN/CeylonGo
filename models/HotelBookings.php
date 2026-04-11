<?php

class HotelBookings{
    private $conn;
    private $table = "hotel_bookings";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getHotelBookings($hotel_id){
        $query = "SELECT * FROM " . $this->table . " WHERE hotel_name = ? ORDER BY check_in DESC";
        $stmt = $this->conn->prepare($query);
        
        try {
            $stmt->execute([$hotel_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('HotelBookings error: ' . $e->getMessage());
            return [];
        }
    }
}