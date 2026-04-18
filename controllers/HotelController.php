<?php
class HotelController {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function registerView() {
        view('hotel/hotel_register');
    }

    public function register() {
        $data = $_POST;
        $files = $_FILES;

        // Validation
        if (empty($data['hname']) || empty($data['location']) || empty($data['city']) || 
            empty($data['contact']) || empty($data['email']) || empty($data['password']) || 
            empty($data['confirm_password'])) {
            die("<script>alert('Please fill in all fields.'); window.history.back();</script>");
        }

        if ($data['password'] !== $data['confirm_password']) {
            die("<script>alert('Passwords do not match.'); window.history.back();</script>");
        }

        // Handle image upload
        $uploadDir = __DIR__ . "/../public/uploads/hotels/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $image_name = "";
        if (!empty($files['hotel_image']['tmp_name'])) {
            $image_name = time() . '_' . basename($files['hotel_image']['name']);
            $target_file = $uploadDir . $image_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($files['hotel_image']['tmp_name']);
            if ($check === false) {
                die("<script>alert('File is not an image.'); window.history.back();</script>");
            }

            $allowed_types = ["jpg", "jpeg", "png", "gif"];
            if (!in_array($imageFileType, $allowed_types)) {
                die("<script>alert('Only JPG, JPEG, PNG & GIF files are allowed.'); window.history.back();</script>");
            }

            // Additional image validation
            if (!empty($files['hotel_image']['tmp_name'])) {
                // Check file size (max 2MB)
                if ($files['hotel_image']['size'] > 2 * 1024 * 1024) {
                    die("<script>alert('Image size should be less than 2MB.'); window.history.back();</script>");
                }
            }

            if (!move_uploaded_file($files['hotel_image']['tmp_name'], $target_file)) {
                die("<script>alert('Error uploading the image.'); window.history.back();</script>");
            }
        }

        // Improved error handling and logging
        try {
            $hotel = new Hotel($this->db);
            $hotel->hotel_name = htmlspecialchars($data['hname']);
            $hotel->location = htmlspecialchars($data['location']);
            $hotel->city = htmlspecialchars($data['city']);
            $hotel->hotel_image = $image_name;
            $hotel->contact_number = htmlspecialchars($data['contact']);
            $hotel->email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
            $hotel->password = password_hash($data['password'], PASSWORD_DEFAULT);

            if ($hotel->register()) {
                $authUser = new AuthUser($this->db);
                $authUser->ref_id = $hotel->id;
                $authUser->email = $hotel->email;
                $authUser->password = $hotel->password;
                $authUser->role = 'hotel';
                $authUser->addUser();

                error_log('Hotel registered: ' . $hotel->email);
                echo "<script>alert('Hotel registered successfully!'); window.location.href='/CeylonGo/public/hotel/dashboard';</script>";
            } else {
                error_log('Hotel registration failed: ' . $hotel->email);
                echo "<script>alert('Registration failed. Please try again.'); window.history.back();</script>";
            }
        } catch (Exception $e) {
            error_log('Hotel registration error: ' . $e->getMessage());
            echo "<script>alert('An error occurred. Please try again.'); window.history.back();</script>";
        }
    }

    public function dashboard() {
        view('hotel/dashboard');
    }

    public function rooms() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $hotel_id = (int) ($_SESSION['id'] ?? ($_SESSION['user_id'] ?? 0));

            if ($hotel_id <= 0) {
                header("Location: /CeylonGo/public/hotel/rooms?error=" . urlencode("Invalid hotel session"));
                exit();
            }

            $type = trim((string) ($data['room_type'] ?? ''));
            $description = trim((string) ($data['description'] ?? ''));
            $rawRate = (string) ($data['rate'] ?? '0');
            $rawRate = str_replace(',', '', $rawRate);
            $priceValue = (float) $rawRate;

            if ($type === '' || $priceValue <= 0) {
                header("Location: /CeylonGo/public/hotel/rooms?error=" . urlencode("Room type and valid price are required"));
                exit();
            }

            $roomRecord = [
                'type' => $type,
                'description' => $description,
                'price' => 'Rs.' . number_format($priceValue, 0),
                'priceValue' => $priceValue,
                'image' => '/img/5star.jpg'
            ];

            $hotelModel = new Hotel($this->db);
            if ($hotelModel->AddOrUpdateRoom($hotel_id, $roomRecord)) {
                header("Location: /CeylonGo/public/hotel/rooms?success=" . urlencode("Room added successfully"));
            } else {
                header("Location: /CeylonGo/public/hotel/rooms?error=" . urlencode("Failed to add room"));
            }
            exit();
        }

        $hotel = new Hotel($this->db);
        $hotel_id = $_SESSION['id'];
        $hotels = $hotel->GetAccommodationCatalogByUserId($hotel_id);
        
        $rooms = [];
        if (!empty($hotels) && isset($hotels[0]->room_details)) {
            if (is_string($hotels[0]->room_details)) {
                $decodedRooms = json_decode($hotels[0]->room_details, true);
                if (is_array($decodedRooms)) {
                    $rooms = $decodedRooms;
                }
            } elseif (is_array($hotels[0]->room_details)) {
                $rooms = $hotels[0]->room_details;
            }
        }

        view('hotel/rooms', ['rooms' => $rooms]);
    }

    public function addRoomView() {
        view('hotel/add_room');
    }

    public function addRoom() {
        // Backward compatibility: route legacy submissions through rooms() handler.
        $this->rooms();
    }

    public function editRoomView($id) {
        $roomModel = new Room($this->db);
        $room = $roomModel->getRoomById($id);
        if ($room) {
            $room['amenities'] = json_decode($room['amenities'], true) ?? [];
            view('hotel/edit_room', ['room' => $room]);
        } else {
            header("Location: /CeylonGo/public/hotel/rooms?error=" . urlencode("Room not found"));
            exit();
        }
    }

    public function updateRoom() {
        $data = $_POST;

        $room = new Room($this->db);
        $room->id = $data['room_id'];
        $room->room_number = $data['room_number'] ?? '';
        $room->room_type = $data['room_type'] ?? '';
        $room->rate = $data['rate'] ?? 0;
        $room->capacity = $data['capacity'] ?? 1;
        $room->status = $data['status'] ?? 'available';
        $room->description = $data['description'] ?? null;
        
        $amenities = [];
        if (isset($data['amenities']) && is_array($data['amenities'])) {
            $amenities = $data['amenities'];
        }
        $room->amenities = json_encode($amenities);

        if ($room->updateRoom()) {
            header("Location: /CeylonGo/public/hotel/rooms?success=" . urlencode("Room updated successfully"));
        } else {
            header("Location: /CeylonGo/public/hotel/edit-room/{$room->id}?error=" . urlencode("Failed to update room"));
        }
        exit();
    }

    public function deleteRoom($id) {
        $roomModel = new Room($this->db);
        if ($roomModel->deleteRoom($id)) {
            header("Location: /CeylonGo/public/hotel/rooms?success=" . urlencode("Room deleted successfully"));
        } else {
            header("Location: /CeylonGo/public/hotel/rooms?error=" . urlencode("Failed to delete room"));
        }
        exit();
    }

    public function bookings() {
        $hotelBookings = new HotelBookings($this->db);
        $hotel_id = $_SESSION['id'];

        $bookings = $hotelBookings->getHotelBookings($hotel_id);
        view('hotel/bookings', ['bookings' =>$bookings]);
    }

    public function availability() {
        view('hotel/availability');
    }

    public function inquiries() {
        view('hotel/inquiries');
    }

    public function notifications() {
        view('hotel/notifications');
    }

    public function payments() {
        view('hotel/payments');
    }

    public function reviews() {
        view('hotel/reviews');
    }

    public function reportIssue() {
        view('hotel/report_issue');
    }

    // API endpoint to get bookings for calendar (JSON)
    public function getBookingsCalendar() {
        header('Content-Type: application/json');
        
        $bookings = [];
        
        // Query bookings table if it exists
        try {
            $query = "SELECT * FROM hotel_bookings WHERE hotel_id = ? ORDER BY check_in ASC";
            $stmt = $this->db->prepare($query);
            $hotel_id = $_SESSION['hotel_id'] ?? 1; // Get from session or default
            $stmt->execute([$hotel_id]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $booking) {
                $bookings[] = [
                    'id' => $booking['id'] ?? 0,
                    'start' => $booking['check_in'] ?? '',
                    'end' => $booking['check_out'] ?? $booking['check_in'] ?? '',
                    'guest' => $booking['guest_name'] ?? '',
                    'room' => $booking['room_number'] ?? ''
                ];
            }
        } catch (Exception $e) {
            // If table doesn't exist, continue to add sample data
        }
        
        // Add sample bookings if no bookings exist (for testing)
        if (empty($bookings)) {
            $bookings = [
                [
                    'id' => 1,
                    'start' => date('Y-m-d', strtotime('+3 days')) . 'T10:00:00',
                    'end' => date('Y-m-d', strtotime('+5 days')) . 'T10:00:00',
                    'guest' => 'John Doe',
                    'room' => '101'
                ],
                [
                    'id' => 2,
                    'start' => date('Y-m-d', strtotime('+7 days')) . 'T14:00:00',
                    'end' => date('Y-m-d', strtotime('+9 days')) . 'T14:00:00',
                    'guest' => 'Jane Smith',
                    'room' => '205'
                ],
                [
                    'id' => 3,
                    'start' => date('Y-m-d', strtotime('+15 days')) . 'T12:00:00',
                    'end' => date('Y-m-d', strtotime('+17 days')) . 'T12:00:00',
                    'guest' => 'Robert Brown',
                    'room' => '302'
                ]
            ];
        }
        
        echo json_encode($bookings);
        exit;
    }
}
?>

