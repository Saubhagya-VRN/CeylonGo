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
        view('transport/dashboard');
    }

    public function upcoming() {
        view('transport/upcoming');
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
        view('transport/cancelled');
    }

    public function review() {
        view('transport/review');
    }

    public function profile() {
        view('transport/profile');
    }

    public function vehicle() {
        view('transport/vehicle');
    }

    public function info() {
        view('transport/info');
    }

    public function payment() {
        view('transport/payment');
    }

    // API endpoint to get bookings for calendar (JSON)
    public function getBookingsCalendar() {
        header('Content-Type: application/json');
        
        $transportRequestModel = new TransportRequest($this->db);
        
        // Get all requests (we'll show all future dates, not just today+)
        $allRequests = $transportRequestModel->getAllRequests();
        
        $bookings = [];
        
        foreach ($allRequests as $request) {
            if (isset($request['date']) && !empty($request['date'])) {
                // Format time properly
                $time = isset($request['pickupTime']) && !empty($request['pickupTime']) 
                    ? $request['pickupTime'] 
                    : '09:00:00';
                
                // Ensure time format is correct
                if (strlen($time) == 5) {
                    $time = $time . ':00';
                }
                
                $bookings[] = [
                    'id' => $request['id'] ?? 0,
                    'start' => $request['date'] . 'T' . $time,
                    'location' => $request['pickupLocation'] ?? '',
                    'time' => $time,
                    'customerName' => $request['customerName'] ?? 'Customer'
                ];
            }
        }
        
        // Add sample bookings if no bookings exist (for testing)
        if (empty($bookings)) {
            $bookings = [
                [
                    'id' => 1,
                    'start' => date('Y-m-d', strtotime('+2 days')) . 'T09:00:00',
                    'location' => 'Colombo Airport',
                    'time' => '09:00:00',
                    'customerName' => 'John Smith'
                ],
                [
                    'id' => 2,
                    'start' => date('Y-m-d', strtotime('+5 days')) . 'T14:30:00',
                    'location' => 'Kandy City',
                    'time' => '14:30:00',
                    'customerName' => 'Sarah Johnson'
                ],
                [
                    'id' => 3,
                    'start' => date('Y-m-d', strtotime('+10 days')) . 'T08:00:00',
                    'location' => 'Galle Fort',
                    'time' => '08:00:00',
                    'customerName' => 'Mike Williams'
                ]
            ];
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
        view('transport/cancelled_info');
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
            echo json_encode(['success' => true, 'message' => 'Booking accepted successfully!']);
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

        // First unassign current driver
        $requestModel->assignDriver($bookingId, null, null);
        
        // Try to find another available vehicle
        if ($dbTypeId) {
            $vehicleModel = new Vehicle($this->db);
            $newVehicle = $vehicleModel->findAvailableVehicle($dbTypeId, $booking['date'], (int) $booking['num_people']);
            if ($newVehicle) {
                $requestModel->assignDriver($bookingId, trim($newVehicle['user_id']), $newVehicle['vehicle_no']);
            }
        }
        
        echo json_encode(['success' => true, 'message' => 'Booking rejected. It will be reassigned to another driver.']);
        exit();
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

        // Generate unique user ID
        $user_id = 'TP' . uniqid();

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

            if (!empty($files['license_image']['tmp_name'])) {
                $newFileName = $this->generateFileName($files['license_image']['name']);
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

        // Make sure uploads folder exists
        $uploadDir = __DIR__ . "/../uploads/";
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
            move_uploaded_file($files['vehicle_image']['tmp_name'], $targetPath);
            $transport_vehicle->image = $newFileName;
        }
        $transport_vehicle->addVehicle();

        header('Location: ../../public/transporter/profile');

    }

    public function updateVehicle() {
        $data = $_POST;
        $files = $_FILES;

        // Make sure uploads folder exists
        $uploadDir = __DIR__ . "/../uploads/";
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
            move_uploaded_file($files['vehicle_image']['tmp_name'], $targetPath);
            $transport_vehicle->image = $newFileName;
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
}
?>
