<?php
// Models will be loaded by autoloader

class TransportProviderController {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Show login / registration view
    public function loginView() {
        // Load vehicle types from DB
        $vehicleTypeModel = new VehicleType($this->db);
        $vehicleTypes = $vehicleTypeModel->getAllTypes()->fetchAll(PDO::FETCH_ASSOC);

        // Pass data to view
        view('transport/login', [
            'title' => 'Transport Provider Login',
            'vehicleTypes' => $vehicleTypes
        ]);
    }

    public function dashboard() {
        if (!isset($_SESSION['transporter_id'])) {
            header('Location: /CeylonGo/public/login');
            exit();
        }
        $driverId = trim($_SESSION['transporter_id']);
        $requestModel = new TransportRequest($this->db);
        $confirmed_bookings = $requestModel->getConfirmedByDriverId($driverId);
        $pending_bookings = $requestModel->getPendingByDriverId($driverId);
        view('transport/dashboard', [
            'confirmed_bookings' => $confirmed_bookings,
            'pending_bookings' => $pending_bookings
        ]);
    }

    public function upcoming() {
        if (!isset($_SESSION['transporter_id'])) {
            header('Location: /CeylonGo/public/login');
            exit();
        }
        $driverId = trim($_SESSION['transporter_id']);
        $requestModel = new TransportRequest($this->db);
        $confirmed_bookings = $requestModel->getConfirmedByDriverId($driverId);
        view('transport/upcoming', ['confirmed_bookings' => $confirmed_bookings]);
    }

    public function pending() {
        if (!isset($_SESSION['transporter_id'])) {
            header('Location: /CeylonGo/public/login');
            exit();
        }
        $driverId = trim($_SESSION['transporter_id']);
        $requestModel = new TransportRequest($this->db);
        $pending_bookings = $requestModel->getPendingByDriverId($driverId);
        view('transport/pending', ['pending_bookings' => $pending_bookings]);
    }

    public function cancelled() {
        if (!isset($_SESSION['transporter_id'])) {
            header('Location: /CeylonGo/public/login');
            exit();
        }
        $driverId = trim($_SESSION['transporter_id']);
        $requestModel = new TransportRequest($this->db);
        $cancelled_bookings = $requestModel->getCancelledByDriverId($driverId);
        view('transport/cancelled', ['cancelled_bookings' => $cancelled_bookings]);
    }

    public function review() {
        if (!isset($_SESSION['transporter_id'])) {
            header('Location: /CeylonGo/public/login');
            exit();
        }
        $driverId = trim($_SESSION['transporter_id']);
        $reviewModel = new TransportReview($this->db);
        $reviews = $reviewModel->getReviewsByDriverId($driverId);
        $stats = $reviewModel->getReviewStats($driverId);
        view('transport/review', ['reviews' => $reviews, 'stats' => $stats]);
    }

    public function profile() {
        view('transport/profile');
    }

    public function vehicle() {
        view('transport/vehicle');
    }

    public function info() {
        if (!isset($_SESSION['transporter_id'])) {
            header('Location: /CeylonGo/public/login');
            exit();
        }
        $bookingId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($bookingId <= 0) {
            header('Location: /CeylonGo/public/transporter/upcoming');
            exit();
        }
        $requestModel = new TransportRequest($this->db);
        $booking = $requestModel->getRequestByIdFull($bookingId);
        if (!$booking) {
            header('Location: /CeylonGo/public/transporter/upcoming');
            exit();
        }
        view('transport/info', ['booking' => $booking]);
    }

    public function payment() {
        if (!isset($_SESSION['transporter_id'])) {
            header('Location: /CeylonGo/public/login');
            exit();
        }
        $driverId = trim($_SESSION['transporter_id']);
        $requestModel = new TransportRequest($this->db);
        $payments = $requestModel->getPaymentsByDriverId($driverId);
        view('transport/payment', ['payments' => $payments]);
    }

    // API endpoint to get bookings for calendar (JSON)
    public function getBookingsCalendar() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['transporter_id'])) {
            echo json_encode([]);
            exit;
        }

        $driverId = trim($_SESSION['transporter_id']);
        $transportRequestModel = new TransportRequest($this->db);
        
        // Get confirmed bookings for this driver
        $confirmedBookings = $transportRequestModel->getConfirmedByDriverId($driverId);
        // Get pending bookings for this driver
        $pendingBookings = $transportRequestModel->getPendingByDriverId($driverId);
        
        $bookings = [];
        
        foreach ($confirmedBookings as $request) {
            if (isset($request['date']) && !empty($request['date'])) {
                $time = isset($request['pickup_time']) && !empty($request['pickup_time']) 
                    ? $request['pickup_time'] 
                    : '09:00:00';
                if (strlen($time) == 5) $time .= ':00';
                
                $bookings[] = [
                    'id' => $request['id'] ?? 0,
                    'date' => $request['date'],
                    'start' => $request['date'] . 'T' . $time,
                    'location' => $request['pickup_location'] ?? '',
                    'dropoff' => $request['dropoff_location'] ?? '',
                    'time' => $time,
                    'customerName' => $request['customer_name'] ?? 'Customer',
                    'vehicleType' => $request['vehicle_type'] ?? '',
                    'numPeople' => $request['num_people'] ?? 0,
                    'status' => 'confirmed'
                ];
            }
        }

        foreach ($pendingBookings as $request) {
            if (isset($request['date']) && !empty($request['date'])) {
                $time = isset($request['pickup_time']) && !empty($request['pickup_time']) 
                    ? $request['pickup_time'] 
                    : '09:00:00';
                if (strlen($time) == 5) $time .= ':00';
                
                $bookings[] = [
                    'id' => $request['id'] ?? 0,
                    'date' => $request['date'],
                    'start' => $request['date'] . 'T' . $time,
                    'location' => $request['pickup_location'] ?? '',
                    'dropoff' => $request['dropoff_location'] ?? '',
                    'time' => $time,
                    'customerName' => $request['customer_name'] ?? 'Customer',
                    'vehicleType' => $request['vehicle_type'] ?? '',
                    'numPeople' => $request['num_people'] ?? 0,
                    'status' => 'pending'
                ];
            }
        }
        
        echo json_encode($bookings);
        exit;
    }

    public function saveBankDetails() {
        if (!isset($_SESSION['transporter_id'])) {
            header('Location: /CeylonGo/views/transport/login.php');
            exit();
        }
        
        $user_id = trim($_SESSION['transporter_id']);
        
        try {
            $bankModel = new BankDetails($this->db);
            
            $bankModel->ref_id = $user_id;
            $bankModel->bank_name = $_POST['bank_name'] ?? '';
            $bankModel->acc_no = $_POST['acc_no'] ?? '';
            $bankModel->acc_holder_name = $_POST['acc_holder_name'] ?? '';
            $bankModel->branch_name = $_POST['branch_name'] ?? '';
            
            if ($bankModel->saveBankDetails()) {
                $_SESSION['payment_message'] = "Bank details saved successfully!";
            } else {
                $_SESSION['payment_error'] = "Failed to save bank details.";
            }
        } catch (Exception $e) {
            $_SESSION['payment_error'] = "Error: " . $e->getMessage();
        }
        
        header('Location: /CeylonGo/public/transporter/payment');
        exit();
    }

    public function pendingInfo() {
        if (!isset($_SESSION['transporter_id'])) {
            header('Location: /CeylonGo/public/login');
            exit();
        }
        $bookingId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($bookingId <= 0) {
            header('Location: /CeylonGo/public/transporter/pending');
            exit();
        }
        $requestModel = new TransportRequest($this->db);
        $booking = $requestModel->getRequestByIdFull($bookingId);
        
        // Verify this booking belongs to the logged-in driver
        if (!$booking || trim($booking['assigned_driver_id']) !== trim($_SESSION['transporter_id'])) {
            header('Location: /CeylonGo/public/transporter/pending');
            exit();
        }
        view('transport/pending_info', ['booking' => $booking]);
    }

    public function cancelledInfo() {
        if (!isset($_SESSION['transporter_id'])) {
            header('Location: /CeylonGo/public/login');
            exit();
        }
        $bookingId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($bookingId <= 0) {
            header('Location: /CeylonGo/public/transporter/cancelled');
            exit();
        }
        $requestModel = new TransportRequest($this->db);
        $booking = $requestModel->getRequestByIdFull($bookingId);
        if (!$booking) {
            header('Location: /CeylonGo/public/transporter/cancelled');
            exit();
        }
        view('transport/cancelled_info', ['booking' => $booking]);
    }

    /**
     * Accept a pending booking (AJAX)
     */
    public function acceptBooking() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['transporter_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit();
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $bookingId = isset($input['booking_id']) ? (int) $input['booking_id'] : 0;
        if ($bookingId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
            exit();
        }
        $requestModel = new TransportRequest($this->db);
        $booking = $requestModel->getRequestByIdFull($bookingId);
        if (!$booking || trim($booking['assigned_driver_id']) !== trim($_SESSION['transporter_id'])) {
            echo json_encode(['success' => false, 'message' => 'Booking not found or not assigned to you']);
            exit();
        }
        if ($requestModel->updateStatus($bookingId, 'confirmed')) {
            $this->sendTransportConfirmationEmail($booking);
            echo json_encode(['success' => true, 'message' => 'Booking accepted successfully! The tourist has been notified.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to accept booking']);
        }
        exit();
    }

    /**
     * Reject a pending booking (AJAX) — re-assigns to another driver or leaves unassigned
     */
    public function rejectBooking() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['transporter_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit();
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $bookingId = isset($input['booking_id']) ? (int) $input['booking_id'] : 0;
        if ($bookingId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
            exit();
        }
        $requestModel = new TransportRequest($this->db);
        $booking = $requestModel->getRequestByIdFull($bookingId);
        if (!$booking || trim($booking['assigned_driver_id']) !== trim($_SESSION['transporter_id'])) {
            echo json_encode(['success' => false, 'message' => 'Booking not found or not assigned to you']);
            exit();
        }
        
        // Map vehicle type string to DB type ID for re-assignment
        $vehicleTypeMap = [
            'Tuk' => '1', 'Car' => '2', 'Minivan' => '2',
            'Minivan AC' => '2', 'Bus' => '2', 'Bus AC' => '2'
        ];
        $dbTypeId = $vehicleTypeMap[$booking['vehicle_type']] ?? null;

        // Remember the rejecting driver to exclude them
        $rejectingDriverId = trim($_SESSION['transporter_id']);

        // First unassign current driver
        $requestModel->assignDriver($bookingId, null, null);
        
        // Try to find another available vehicle, excluding the rejecting driver
        if ($dbTypeId) {
            $vehicleModel = new Vehicle($this->db);
            $newVehicle = $vehicleModel->findAvailableVehicle($dbTypeId, $booking['date'], (int) $booking['num_people'], [$rejectingDriverId]);
            if ($newVehicle) {
                $requestModel->assignDriver($bookingId, trim($newVehicle['user_id']), $newVehicle['vehicle_no']);
                echo json_encode(['success' => true, 'message' => 'Booking rejected. It has been reassigned to another driver.']);
            } else {
                echo json_encode(['success' => true, 'message' => 'Booking rejected. No other drivers are currently available.']);
            }
        } else {
            echo json_encode(['success' => true, 'message' => 'Booking rejected.']);
        }
        exit();
    }

    private function sendTransportConfirmationEmail($booking) {
        if (empty($booking['user_id'])) {
            return false;
        }

        require_once dirname(__DIR__) . '/models/Tourist.php';
        $touristModel = new Tourist($this->db);
        $tourist = $touristModel->getTouristById((int) $booking['user_id']);
        if (!$tourist || empty($tourist['email'])) {
            return false;
        }

        $touristName = trim(($tourist['first_name'] ?? '') . ' ' . ($tourist['last_name'] ?? '')) ?: 'Customer';
        $driverName = trim($booking['driver_name'] ?? 'Your driver');
        $vehicleNo = trim($booking['assigned_vehicle_no'] ?? 'N/A');
        $pickupTime = !empty($booking['pickup_time']) ? date('g:i A', strtotime($booking['pickup_time'])) : 'N/A';
        $subject = 'Your Ceylon Go transport booking is confirmed';
        $message = "Hello {$touristName},\n\n" .
                   "Good news! Your transport request for {$booking['date']} has been confirmed by {$driverName}.\n\n" .
                   "Booking details:\n" .
                   "- Pickup: {$booking['pickup_location']}\n" .
                   "- Dropoff: {$booking['dropoff_location']}\n" .
                   "- Pickup time: {$pickupTime}\n" .
                   "- Vehicle: {$booking['vehicle_type']}\n" .
                   "- Vehicle number: {$vehicleNo}\n\n" .
                   "If you have any questions, please contact us.\n\n" .
                   "Thank you for booking with Ceylon Go!\n";

        $headers = "From: no-reply@ceylongo.local\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/plain; charset=UTF-8\r\n";

        @mail($tourist['email'], $subject, $message, $headers);
        return true;
    }

    public function registerView() {
        view('transport/transport_register');
    }

    // Helper function to generate unique filenames
    private function generateFileName($originalName) {
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $randomName = uniqid('img_', true) . '.' . $ext;
        return $randomName;
    }

    public function registerProvider() {
        $data = $_POST;
        $files = $_FILES;

        // Validation
        $errors = [];
        if (empty($data['full_name'])) $errors[] = "Full name is required.";
        if (empty($data['dob'])) $errors[] = "Date of birth is required.";
        if (empty($data['nic'])) $errors[] = "NIC is required.";
        if (empty($data['address'])) $errors[] = "Address is required.";
        if (empty($data['contact_no'])) $errors[] = "Contact number is required.";
        if (empty($data['email'])) $errors[] = "Email is required.";
        if (empty($data['password'])) $errors[] = "Password is required.";
        if ($data['password'] !== $data['confirm_password']) $errors[] = "Passwords do not match.";

        if (!empty($errors)) {
            $_SESSION['register_errors'] = $errors;
            header('Location: /CeylonGo/public/transporter/register');
            exit();
        }

        // Make sure uploads folder exists
        $uploadDir = __DIR__ . "/../public/uploads/transport/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate unique user ID - must fit in varchar(12)
        $user_id = substr('TP' . uniqid(), 0, 12);

        try {
            // Start PDO transaction
            $this->db->beginTransaction();

            // 1. Create User in transport_users table
            $transport_users = new User($this->db);
            $transport_users->user_id = $user_id;
            $transport_users->full_name = trim($data['full_name']);
            $transport_users->dob = $data['dob'];
            $transport_users->nic = trim($data['nic']);
            $transport_users->address = trim($data['address']);
            $transport_users->contact_no = trim($data['contact_no']);
            $transport_users->email = trim($data['email']);
            $transport_users->psw = password_hash($data['password'], PASSWORD_BCRYPT);
            
            // Handle profile image upload
            if (!empty($files['profile_image']['tmp_name'])) {
                $newFileName = 'profile_' . uniqid() . '.' . pathinfo($files['profile_image']['name'], PATHINFO_EXTENSION);
                move_uploaded_file($files['profile_image']['tmp_name'], $uploadDir . $newFileName);
                $transport_users->profile_image = $newFileName;
            }
            
            $transport_users->register();

            // 2. Add to users table for authentication
            $authUser = new AuthUser($this->db);
            $authUser->ref_id = $user_id;
            $authUser->email = trim($data['email']);
            $authUser->password = $transport_users->psw;
            $authUser->role = 'transport';
            $authUser->addUser();

            // 3. License
            $transport_license = new License($this->db);
            $transport_license->license_no = trim($data['license_no']);
            $transport_license->license_exp_date = $data['license_exp_date'];
            $transport_license->driver_id = $user_id;
            $transport_license->image = '';

            if (!empty($files['license_image']['tmp_name'])) {
                $ext = pathinfo($files['license_image']['name'], PATHINFO_EXTENSION);
                // Keep filename short to fit varchar(20): "lic_" + 10chars + "." + ext
                $newFileName = 'lic_' . substr(uniqid(), -10) . '.' . $ext;
                move_uploaded_file($files['license_image']['tmp_name'], $uploadDir . $newFileName);
                $transport_license->image = $newFileName;
            }
            $transport_license->addLicense();

            // 4. Vehicle
            $transport_vehicle = new Vehicle($this->db);
            $transport_vehicle->vehicle_no = trim($data['vehicle_no']);
            $transport_vehicle->user_id = $user_id;
            $transport_vehicle->vehicle_type = $data['vehicle_type'];
            $transport_vehicle->psg_capacity = intval($data['psg_capacity']);

            if (!empty($files['vehicle_image']['tmp_name'])) {
                $newFileName = $this->generateFileName($files['vehicle_image']['name']);
                move_uploaded_file($files['vehicle_image']['tmp_name'], $uploadDir . $newFileName);
                $transport_vehicle->image = $newFileName;
            }
            $transport_vehicle->addVehicle();

            // Commit transaction
            $this->db->commit();

            $_SESSION['register_success'] = "Registration successful! Please login.";
            header('Location: /CeylonGo/public/login');
            exit();

        } catch (Exception $e) {
            // Rollback on error
            $this->db->rollBack();
            $_SESSION['register_errors'] = ["Registration failed: " . $e->getMessage()];
            header('Location: /CeylonGo/public/transporter/register');
            exit();
        }
    }

    public function addVehicle() {
        $data = $_POST;
        $files = $_FILES;

        // Make sure uploads folder exists in public so images can be served by the browser
        $uploadDir = __DIR__ . "/../public/uploads/transport/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $transport_vehicle = new Vehicle($this->db);
        $transport_vehicle->vehicle_no = $data['vehicle_no'];
        // Trim user_id to prevent whitespace issues
        $transport_vehicle->user_id = trim($_SESSION['transporter_id']);
        $transport_vehicle->vehicle_type = $data['vehicle_type'];
        $transport_vehicle->psg_capacity = $data['psg_capacity'];

        if (!empty($files['vehicle_image']['tmp_name'])) {
            $newFileName = $this->generateFileName($files['vehicle_image']['name']);
            $targetPath = $uploadDir . $newFileName;
            if (move_uploaded_file($files['vehicle_image']['tmp_name'], $targetPath)) {
                $transport_vehicle->image = $newFileName;
            }
        }
        $transport_vehicle->addVehicle();

        header('Location: ../../public/transporter/profile');
    }

    public function updateVehicle() {
        $data = $_POST;
        $files = $_FILES;

        // Make sure uploads folder exists in public so images can be served by the browser
        $uploadDir = __DIR__ . "/../public/uploads/transport/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $transport_vehicle = new Vehicle($this->db);
        $transport_vehicle->vehicle_no = $data['vehicle_no'];
        // Trim user_id to prevent whitespace issues
        $transport_vehicle->user_id = trim($_SESSION['transporter_id']);
        $transport_vehicle->vehicle_type = $data['vehicle_type'];
        $transport_vehicle->psg_capacity = $data['psg_capacity'];
        
        if (!empty($files['vehicle_image']['tmp_name'])) {
            $newFileName = $this->generateFileName($files['vehicle_image']['name']);
            $targetPath = $uploadDir . $newFileName;
            if (move_uploaded_file($files['vehicle_image']['tmp_name'], $targetPath)) {
                $transport_vehicle->image = $newFileName;
            }
            header('Location: ../../public/transporter/profile#');
        }
        $transport_vehicle->updateVehicle();

        header('Location: ../../public/transporter/profile');
    }

    // Get profile data for a specific driver
    public function getProfileData($user_id) {
        // Get user data
        $userModel = new User($this->db);
        $user = $userModel->getUserById($user_id);
        
        // Get license data
        $licenseModel = new License($this->db);
        $license = $licenseModel->getLicenseByDriverId($user_id);
        
        // Get vehicles data
        $vehicleModel = new Vehicle($this->db);
        $vehicles = $vehicleModel->getVehiclesByUser($user_id);
        
        return [
            'user' => $user,
            'license' => $license,
            'vehicles' => $vehicles
        ];

        var_dump($user);
        var_dump($license);
        var_dump($vehicles);
    }

    // Show profile view with data
    public function profileView($user_id) {
        $data = $this->getProfileData($user_id);
        
        // Pass data to view
        view('transport/profile', [
            'title' => 'Transport Provider Profile',
            'user' => $data['user'],
            'license' => $data['license'],
            'vehicles' => $data['vehicles']
        ]);
    }

    /**
     * AJAX endpoint for tourist to check booking status
     * Returns current status and driver/vehicle details if confirmed
     */
    public function checkBookingStatus() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit();
        }

        $requestId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($requestId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid request ID']);
            exit();
        }

        $requestModel = new TransportRequest($this->db);
        $booking = $requestModel->getRequestByIdFull($requestId);

        if (!$booking) {
            echo json_encode(['success' => false, 'message' => 'Booking not found']);
            exit();
        }

        $response = [
            'success' => true,
            'status' => $booking['status'],
            'bookingId' => $booking['id']
        ];

        // If confirmed, include driver and vehicle details
        if ($booking['status'] === 'confirmed' && !empty($booking['assigned_driver_id'])) {
            // Get driver details
            $userModel = new User($this->db);
            $driver = $userModel->getUserById(trim($booking['assigned_driver_id']));
            
            // Get vehicle details
            $vehicleModel = new Vehicle($this->db);
            $vehicles = $vehicleModel->getVehiclesByUser(trim($booking['assigned_driver_id']));
            $assignedVehicle = null;
            foreach ($vehicles as $v) {
                if ($v['vehicle_no'] === $booking['assigned_vehicle_no']) {
                    $assignedVehicle = $v;
                    break;
                }
            }

            $response['driver'] = [
                'name' => $driver['full_name'] ?? 'Driver',
                'contact' => $driver['contact_no'] ?? '',
                'profileImage' => !empty($driver['profile_image']) 
                    ? '/CeylonGo/public/uploads/transport/' . $driver['profile_image'] 
                    : '/CeylonGo/public/images/profile.jpg'
            ];

            if ($assignedVehicle) {
                $response['vehicle'] = [
                    'vehicleNo' => $assignedVehicle['vehicle_no'],
                    'vehicleType' => $assignedVehicle['vehicle_type'],
                    'capacity' => $assignedVehicle['psg_capacity'],
                    'image' => !empty($assignedVehicle['image'])
                        ? '/CeylonGo/uploads/' . $assignedVehicle['image']
                        : null
                ];
            }
        }

        echo json_encode($response);
        exit();
    }
}
?>
