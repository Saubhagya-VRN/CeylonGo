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

        // Check if email already exists in guide_users table
        $guide = new Guide($this->db);
        $existingGuide = $guide->getGuideByEmail(trim($data['email']));
        if ($existingGuide) {
            $errors[] = "Email already exists. Please use a different email.";
        }

        // Check if email exists in central users table
        $authUser = new AuthUser($this->db);
        $existingUser = $authUser->getUserByEmail(trim($data['email']));
        if ($existingUser) {
            $errors[] = "Email already registered. Please use a different email.";
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
            // Add to users table for login authentication
            $authUser = new AuthUser($this->db);
            $authUser->ref_id = $guide->id;
            $authUser->email = $guide->email;
            $authUser->password = $guide->password;
            $authUser->role = 'guide';
            
            if (!$authUser->addUser()) {
                // If user table insertion fails, show error
                echo "<h2>Registration Error:</h2><p>Failed to create login credentials. Please contact support.</p>";
                exit;
            }

            // Redirect to login page after successful registration
            header("Location: /CeylonGo/public/login");
            exit();
        } else {
            echo "<h2>Registration failed:</h2><p>Please try again.</p>";
        }
    }

    public function dashboard() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user_id = $_SESSION['user_id'] ?? null;
        $pendingBookings = [];
        $upcomingBookings = [];
        if ($user_id) {
            $guideRequest = new GuideRequest($this->db);
            $pendingBookings = $guideRequest->getPendingByGuide($user_id);
            $upcomingBookings = $guideRequest->getUpcomingByGuide($user_id);
        }
        view('guide/guide_dashboard', [
            'pendingBookings' => $pendingBookings,
            'upcomingBookings' => $upcomingBookings
        ]);
    }

    public function upcoming() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user_id = $_SESSION['user_id'] ?? null;
        $bookings = [];
        if ($user_id) {
            $guideRequest = new GuideRequest($this->db);
            $bookings = $guideRequest->getUpcomingByGuide($user_id);
        }
        view('guide/upcoming', ['bookings' => $bookings]);
    }

    public function pending() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user_id = $_SESSION['user_id'] ?? null;
        $bookings = [];
        if ($user_id) {
            $guideRequest = new GuideRequest($this->db);
            $bookings = $guideRequest->getPendingByGuide($user_id);
        }
        view('guide/pending', ['bookings' => $bookings]);
    }

    public function cancelled() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user_id = $_SESSION['user_id'] ?? null;
        $bookings = [];
        if ($user_id) {
            $guideRequest = new GuideRequest($this->db);
            $bookings = $guideRequest->getCancelledByGuide($user_id);
        }
        view('guide/cancelled', ['bookings' => $bookings]);
    }

    public function review() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user_id = $_SESSION['user_id'] ?? null;
        $reviews = [];
        $stats = [];
        if ($user_id) {
            $reviewModel = new GuideReview($this->db);
            $reviews = $reviewModel->getReviewsByGuideId($user_id);
            $stats = $reviewModel->getReviewStats($user_id);
        }
        view('guide/review', ['reviews' => $reviews, 'stats' => $stats]);
    }

    public function profile() {
        view('guide/profile');
    }

    public function report() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id || ($_SESSION['user_role'] ?? '') !== 'guide') {
            header("Location: /CeylonGo/public/login");
            exit();
        }

        $guideRequest = new GuideRequest($this->db);
        $reportData = $guideRequest->getReportData($user_id);

        view('guide/report', [
            'overall' => $reportData['overall'],
            'monthly' => $reportData['monthly']
        ]);
    }

    public function places() {
        view('guide/places');
    }

    public function info() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $booking = null;
        if ($id > 0) {
            $guideRequest = new GuideRequest($this->db);
            $booking = $guideRequest->getById($id);
        }
        view('guide/info', ['booking' => $booking]);
    }

    public function pendingInfo() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $booking = null;
        if ($id > 0) {
            $guideRequest = new GuideRequest($this->db);
            $booking = $guideRequest->getById($id);
        }
        view('guide/pending_info', ['booking' => $booking]);
    }

    public function cancelledInfo() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $booking = null;
        if ($id > 0) {
            $guideRequest = new GuideRequest($this->db);
            $booking = $guideRequest->getById($id);
        }
        view('guide/cancelled_info', ['booking' => $booking]);
    }

    public function payment() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user_id = $_SESSION['user_id'] ?? null;
        $payments = [];
        if ($user_id) {
            $guideRequest = new GuideRequest($this->db);
            $payments = $guideRequest->getPaymentsByGuide($user_id);
        }
        view('guide/payment', ['payments' => $payments]);
    }

    /**
     * Accept a pending booking (POST)
     */
    public function acceptBooking() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id || ($_SESSION['user_role'] ?? '') !== 'guide') {
            header("Location: /CeylonGo/public/login");
            exit();
        }

        $request_id = isset($_POST['request_id']) ? (int) $_POST['request_id'] : 0;
        if ($request_id <= 0) {
            header("Location: /CeylonGo/public/guide/pending?error=" . urlencode("Invalid booking ID"));
            exit();
        }

        $guideRequest = new GuideRequest($this->db);
        $booking = $guideRequest->getById($request_id);

        // Verify this booking is assigned to the current guide
        if (!$booking || (int) $booking['guide_id'] !== (int) $user_id) {
            header("Location: /CeylonGo/public/guide/pending?error=" . urlencode("Booking not found or not assigned to you"));
            exit();
        }

        if ($guideRequest->updateStatus($request_id, 'approved')) {
            header("Location: /CeylonGo/public/guide/upcoming?success=" . urlencode("Booking accepted successfully!"));
        } else {
            header("Location: /CeylonGo/public/guide/pending?error=" . urlencode("Failed to accept booking"));
        }
        exit();
    }

    /**
     * Reject a pending booking (POST) — tries to re-assign to another guide
     */
    public function rejectBooking() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id || ($_SESSION['user_role'] ?? '') !== 'guide') {
            header("Location: /CeylonGo/public/login");
            exit();
        }

        $request_id = isset($_POST['request_id']) ? (int) $_POST['request_id'] : 0;
        if ($request_id <= 0) {
            header("Location: /CeylonGo/public/guide/pending?error=" . urlencode("Invalid booking ID"));
            exit();
        }

        $guideRequest = new GuideRequest($this->db);
        $booking = $guideRequest->getById($request_id);

        // Verify this booking is assigned to the current guide
        if (!$booking || (int) $booking['guide_id'] !== (int) $user_id) {
            header("Location: /CeylonGo/public/guide/pending?error=" . urlencode("Booking not found or not assigned to you"));
            exit();
        }

        // Mark as rejected first
        $guideRequest->updateStatus($request_id, 'rejected');

        // Try to find another guide, excluding the rejecting one
        $newGuide = $guideRequest->findAvailableGuide($booking['language'], [(int) $user_id]);
        if ($newGuide) {
            // Reassign to new guide (sets status back to 'pending')
            $guideRequest->reassignGuide($request_id, $newGuide['id']);
            header("Location: /CeylonGo/public/guide/pending?success=" . urlencode("Booking rejected and reassigned to another guide."));
        } else {
            header("Location: /CeylonGo/public/guide/pending?success=" . urlencode("Booking rejected. No other guides available."));
        }
        exit();
    }
}
?>

