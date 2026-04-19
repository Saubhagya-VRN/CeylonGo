<?php

class HotelBookings{
    private $conn;
    private $table = "hotel_bookings";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getHotelBookings($hotel_id){
        $query = "SELECT * FROM " . $this->table . " WHERE hotel_user_id = ? ORDER BY check_in DESC";
        $stmt = $this->conn->prepare($query);
        
        try {
            $stmt->execute([$hotel_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('HotelBookings error: ' . $e->getMessage());
            return [];
        }
    }

    public function getBookingById($booking_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        try {
            $stmt->execute([$booking_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('HotelBookings error: ' . $e->getMessage());
            return null;
        }
    }

    public function updateBookingStatus($booking_id, $status) {
        $query = "UPDATE " . $this->table . " SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        try {
            return $stmt->execute([$status, $booking_id]);
        } catch (PDOException $e) {
            error_log('HotelBookings error: ' . $e->getMessage());
            return false;
        }
    }
}
