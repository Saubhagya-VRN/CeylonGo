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
        view('transport/pending_info');
    }

    public function cancelledInfo() {
        view('transport/cancelled_info');
    }

        // Helper function to generate unique filenames
        function generateFileName($originalName) {
            $ext = pathinfo($originalName, PATHINFO_EXTENSION);
            $randomName = uniqid('img_', true) . '.' . $ext;
            return $randomName;
        }

    public function registerView() {
        view('transport/transport_register');
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
