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
        view('transport/pending');
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

    // Helper function to generate unique filenames
    private function generateFileName($originalName) {
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $randomName = uniqid('img_', true) . '.' . $ext;
        return $randomName;
    }

    public function registerProvider() {
        $data = $_POST;
        $files = $_FILES;

        // Make sure uploads folder exists
        $uploadDir = __DIR__ . "/../uploads/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // 1. Create User
        $transport_users = new User($this->db);
        $transport_users->user_id = uniqid(" ");
        $transport_users->full_name = $data['full_name'];
        $transport_users->dob = $data['dob'];
        $transport_users->nic = $data['nic'];
        $transport_users->address = $data['address'];
        $transport_users->contact_no = $data['contact_no'];
        $transport_users->email = $data['email'];
        $transport_users->psw = password_hash($data['password'], PASSWORD_BCRYPT);
        $transport_users->register();

        // 2. License
        $transport_license = new License($this->db);
        $transport_license->license_no = $data['license_no'];
        $transport_license->license_exp_date = $data['license_exp_date'];
        $transport_license->driver_id = $transport_users->user_id;

        if (!empty($files['license_image']['tmp_name'])) {
            $newFileName = $this->generateFileName($files['license_image']['name']);
            $targetPath = $uploadDir . $newFileName;
            move_uploaded_file($files['license_image']['tmp_name'], $targetPath);
            $transport_license->image = $newFileName;
        }
        $transport_license->addLicense();

        // 3. Vehicle
        $transport_vehicle = new Vehicle($this->db);
        $transport_vehicle->vehicle_no = $data['vehicle_no'];
        $transport_vehicle->user_id = $transport_users->user_id;
        $transport_vehicle->vehicle_type = $data['vehicle_type'];
        $transport_vehicle->psg_capacity = $data['psg_capacity'];

        if (!empty($files['vehicle_image']['tmp_name'])) {
            $newFileName = $this->generateFileName($files['vehicle_image']['name']);
            $targetPath = $uploadDir . $newFileName;
            move_uploaded_file($files['vehicle_image']['tmp_name'], $targetPath);
            $transport_vehicle->image = $newFileName;
        }
        $transport_vehicle->addVehicle();

        header('Location: /CeylonGo/views/login.php');
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
        $transport_vehicle->user_id = $_SESSION['transporter_id'];
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
        $transport_vehicle->user_id = $_SESSION['transporter_id'];
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
