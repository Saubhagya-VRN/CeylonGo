<?php
class GuideController {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function registerView() {
        view('guide/guide_register');
    }

    public function register() {
        $data = $_POST;
        $files = $_FILES;

        // Validation
        $errors = [];
        if (empty($data['first_name'])) $errors[] = "First name is required.";
        if (empty($data['last_name'])) $errors[] = "Last name is required.";
        if (empty($data['nic'])) $errors[] = "NIC number is required.";
        if (empty($data['license_number'])) $errors[] = "License number is required.";
        if (empty($data['specialization'])) $errors[] = "Specialization is required.";
        if (empty($data['languages'])) $errors[] = "Languages are required.";
        if (empty($data['contact_number'])) $errors[] = "Contact number is required.";
        if (empty($data['email'])) $errors[] = "Email is required.";
        if (empty($data['password'])) $errors[] = "Password is required.";
        if (empty($data['confirm_password'])) $errors[] = "Confirm password is required.";

        if ($data['password'] !== $data['confirm_password']) {
            $errors[] = "Passwords do not match.";
        }

        if (!preg_match('/^(\d{9}[VvXx]|\d{12})$/', $data['nic'])) {
            $errors[] = "Invalid NIC number format.";
        }

        if (!preg_match('/^\d{10}$/', $data['contact_number'])) {
            $errors[] = "Contact number must be 10 digits.";
        }

        if (!empty($errors)) {
            echo "<h2>Registration Errors:</h2><ul>";
            foreach ($errors as $err) {
                echo "<li>$err</li>";
            }
            echo "</ul>";
            exit;
        }

        // Handle file uploads
        $uploadDir = __DIR__ . "/../public/uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $profile_photo = "";
        if (!empty($files['profile_photo']['tmp_name'])) {
            $profile_photo = basename($files['profile_photo']['name']);
            move_uploaded_file($files['profile_photo']['tmp_name'], $uploadDir . $profile_photo);
        }

        $license_file = "";
        if (!empty($files['license_file']['tmp_name'])) {
            $license_file = basename($files['license_file']['name']);
            move_uploaded_file($files['license_file']['tmp_name'], $uploadDir . $license_file);
        }

        // Create guide
        $guide = new Guide($this->db);
        $guide->user_type = 'guide';
        $guide->first_name = trim($data['first_name']);
        $guide->last_name = trim($data['last_name']);
        $guide->nic = trim($data['nic']);
        $guide->license_number = trim($data['license_number']);
        $guide->specialization = $data['specialization'] ?? '';
        $guide->languages = trim($data['languages']);
        $guide->experience = intval($data['experience']);
        $guide->profile_photo = $profile_photo;
        $guide->license_file = $license_file;
        $guide->contact_number = trim($data['contact_number']);
        $guide->email = trim($data['email']);
        $guide->password = password_hash($data['password'], PASSWORD_BCRYPT);

        if ($guide->register()) {
            // Add to users table
            $authUser = new AuthUser($this->db);
            $authUser->ref_id = $guide->id;
            $authUser->email = $guide->email;
            $authUser->password = $guide->password;
            $authUser->role = 'guide';
            $authUser->addUser();

            header("Location: /CeylonGo/public/guide/dashboard");
            exit();
        } else {
            echo "<h2>Registration failed:</h2><p>Please try again.</p>";
        }
    }

    public function dashboard() {
        view('guide/guide_dashboard');
    }

    public function upcoming() {
        view('guide/upcoming');
    }

    public function pending() {
        view('guide/pending');
    }

    public function cancelled() {
        view('guide/cancelled');
    }

    public function review() {
        view('guide/review');
    }

    public function profile() {
        view('guide/profile');
    }

    public function places() {
        view('guide/places');
    }

    public function info() {
        view('guide/info');
    }

    public function pendingInfo() {
        view('guide/pending_info');
    }

    public function cancelledInfo() {
        view('guide/cancelled_info');
    }

    public function payment() {
        view('guide/payment');
    }
}
?>

