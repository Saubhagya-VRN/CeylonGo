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
        $hotel_id = $_SESSION['id'] ?? 0;
        
        if ($hotel_id > 0) {
            try {
                $query = "SELECT * FROM hotel_bookings WHERE hotel_user_id = ? ORDER BY check_in ASC";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$hotel_id]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($results as $booking) {
                    $bookings[] = [
                        'id' => $booking['id'] ?? 0,
                        'start' => $booking['check_in'] ?? '',
                        'end' => $booking['check_out'] ?? $booking['check_in'] ?? '',
                        'guest' => $booking['guest_name'] ?? '',
                        'room' => $booking['room_type'] ?? ''
                    ];
                }
            } catch (Exception $e) {
                // Return empty if error
            }
        }
        
        echo json_encode($bookings);
        exit;
    }

    // API endpoint for dashboard summary stats
    public function getDashboardStats() {
        header('Content-Type: application/json');
        
        $stats = [
            'totalBookings' => 0,
            'pendingRequests' => 0,
            'totalReviews' => 0,
            'totalEarnings' => 0.00,
            'hotelName' => 'Hotel'
        ];
        
        $user_id = trim($_SESSION['id'] ?? '0');
        $hotel_pk = trim($_SESSION['user_id'] ?? '0');
        
        if ($user_id !== '0') {
            try {
                // 1. Fetch Name & Reviews - Use TRIM to handle database text field inconsistencies
                $hQuery = "SELECT hotel_name, review_count FROM accommodation_catalog 
                          WHERE TRIM(hotel_user_id) = ? OR TRIM(hotel_user_id) = ? LIMIT 1";
                $hStmt = $this->db->prepare($hQuery);
                $hStmt->execute([$user_id, $hotel_pk]);
                $hData = $hStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($hData) {
                    $stats['hotelName'] = $hData['hotel_name'];
                    $stats['totalReviews'] = (int)$hData['review_count'];
                }

                // 2. Bookings count & earnings
                $bQuery = "SELECT COUNT(*) as total, SUM(total_price) as earnings FROM hotel_bookings 
                          WHERE TRIM(hotel_user_id) = ? OR TRIM(hotel_user_id) = ?";
                $bStmt = $this->db->prepare($bQuery);
                $bStmt->execute([$user_id, $hotel_pk]);
                $bData = $bStmt->fetch(PDO::FETCH_ASSOC);
                $stats['totalBookings'] = (int)($bData['total'] ?? 0);
                $stats['totalEarnings'] = round((float)($bData['earnings'] ?? 0), 2);

                // 3. Pending requests
                $rQuery = "SELECT COUNT(*) as total FROM hotel_requests 
                          WHERE (TRIM(hotel_id) = ? OR TRIM(hotel_id) = ?) AND status = 'pending'";
                $rStmt = $this->db->prepare($rQuery);
                $rStmt->execute([$user_id, $hotel_pk]);
                $stats['pendingRequests'] = (int)($rStmt->fetchColumn() ?: 0);

            } catch (Exception $e) {
                error_log("Dashboard Stats Error: " . $e->getMessage());
            }
        }
        
        echo json_encode($stats);
        exit;
    }

    // API endpoint for recent bookings list
    public function getRecentBookings() {
        header('Content-Type: application/json');
        $hotel_id = $_SESSION['id'] ?? 0;
        $bookings = [];
        
        if ($hotel_id > 0) {
            try {
                $query = "SELECT * FROM hotel_bookings WHERE hotel_user_id = ? ORDER BY created_at DESC LIMIT 5";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$hotel_id]);
                $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                // Fail silently
            }
        }
        
        echo json_encode($bookings);
        exit;
    }

    // API endpoint for monthly revenue data (Last 6 months)
    public function getRevenueData() {
        header('Content-Type: application/json');
        $hotel_id = $_SESSION['id'] ?? 0;
        
        $months = [];
        $revenue = [];
        
        if ($hotel_id > 0) {
            try {
                // Get data for last 6 months including current
                for ($i = 5; $i >= 0; $i--) {
                    $monthText = date('M', strtotime("-$i months"));
                    $yearMonth = date('Y-m', strtotime("-$i months"));
                    
                    $months[] = $monthText;
                    
                    $query = "SELECT SUM(total_price) as monthly_total FROM hotel_bookings 
                              WHERE hotel_user_id = ? AND created_at LIKE ? AND status = 'confirmed'";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute([$hotel_id, "$yearMonth%"]);
                    $data = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $revenue[] = (float)($data['monthly_total'] ?? 0);
                }
            } catch (Exception $e) {
                // Return empty if error
            }
        }
        
        echo json_encode([
            'labels' => $months,
            'data' => $revenue
        ]);
        exit;
    }

    // API endpoint for 14-day availability calculation
    public function getAvailabilityData() {
        header('Content-Type: application/json');
        $hotel_id = trim($_SESSION['id'] ?? '0');
        $hotel_pk = trim($_SESSION['user_id'] ?? '0');
        
        $availability = [];
        
        if ($hotel_id != '0') {
            try {
                // 1. Get Room Inventory Counts
                $roomsQuery = "SELECT room_type, COUNT(*) as count FROM hotel_rooms 
                              WHERE TRIM(hotel_id) = ? OR TRIM(hotel_id) = ? GROUP BY room_type";
                $rStmt = $this->db->prepare($roomsQuery);
                $rStmt->execute([$hotel_id, $hotel_pk]);
                $inventoryRaw = $rStmt->fetchAll(PDO::FETCH_ASSOC);
                
                $inventory = ['Single' => 0, 'Double' => 0, 'Suite' => 0, 'Deluxe' => 0];
                foreach ($inventoryRaw as $inv) {
                    $type = $this->mapRoomType($inv['room_type']);
                    $inventory[$type] += $inv['count'];
                }

                // 2. Fetch Bookings for the next 14 days
                $startDate = date('Y-m-d');
                $endDate = date('Y-m-d', strtotime('+14 days'));
                
                $bQuery = "SELECT check_in, check_out, room_type FROM hotel_bookings 
                          WHERE (TRIM(hotel_user_id) = ? OR TRIM(hotel_user_id) = ?) 
                          AND status = 'confirmed' 
                          AND ((check_in BETWEEN ? AND ?) OR (check_out BETWEEN ? AND ?))";
                $bStmt = $this->db->prepare($bQuery);
                $bStmt->execute([$hotel_id, $hotel_pk, $startDate, $endDate, $startDate, $endDate]);
                $bookings = $bStmt->fetchAll(PDO::FETCH_ASSOC);

                // 3. Calculate Daily Availability
                for ($i = 0; $i < 14; $i++) {
                    $currentDate = date('Y-m-d', strtotime("+$i days"));
                    $dailyStatus = [
                        'date' => date('d M', strtotime($currentDate)),
                        'Single' => $inventory['Single'],
                        'Double' => $inventory['Double'],
                        'Suite' => $inventory['Suite'],
                        'Deluxe' => $inventory['Deluxe']
                    ];
                    
                    foreach ($bookings as $booking) {
                        if ($currentDate >= $booking['check_in'] && $currentDate < $booking['check_out']) {
                            $type = $this->mapRoomType($booking['room_type']);
                            if (isset($dailyStatus[$type])) {
                                $dailyStatus[$type] = max(0, $dailyStatus[$type] - 1);
                            }
                        }
                    }
                    $availability[] = $dailyStatus;
                }
            } catch (Exception $e) {
                error_log("Availability API Error: " . $e->getMessage());
            }
        }
        
        echo json_encode($availability);
        exit;
    }

    private function mapRoomType($type) {
        $type = strtolower($type);
        if (strpos($type, 'single') !== false) return 'Single';
        if (strpos($type, 'double') !== false) return 'Double';
        if (strpos($type, 'suite') !== false || strpos($type, 'view') !== false) return 'Suite';
        if (strpos($type, 'deluxe') !== false) return 'Deluxe';
        return 'Double'; // Fallback
    }
}
?>

