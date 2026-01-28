<?php
class AdminController {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function dashboard() {
        view('admin/admin_dashboard');
    }

    public function profile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateProfile();
            return;
        }
        
        $adminModel = new Admin($this->db);
        $admin_id = $_SESSION['user_ref_id'] ?? null;
        if ($admin_id) {
            $admin = $adminModel->getAdminById($admin_id);
            view('admin/admin_profile', ['admin' => $admin]);
        } else {
            view('admin/admin_profile');
        }
    }

    public function updateProfile() {
        $data = $_POST;
        $admin_id = $_SESSION['user_ref_id'] ?? null;

        if (!$admin_id) {
            header("Location: /CeylonGo/public/admin/profile?error=" . urlencode("Invalid session"));
            exit();
        }

        $admin = new Admin($this->db);
        $admin->id = $admin_id;
        $admin->username = $data['username'] ?? '';
        $admin->email = $data['email'] ?? '';
        $admin->phone_number = $data['phone'] ?? '';
        $admin->role = $data['role'] ?? '';
        
        if (!empty($data['password'])) {
            $admin->password = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if ($admin->updateProfile()) {
            // Update users table
            $authUser = new AuthUser($this->db);
            $authUser->ref_id = $admin_id;
            $authUser->email = $admin->email;
            $authUser->role = 'admin';
            if (!empty($data['password'])) {
                $authUser->password = $admin->password;
            }
            $authUser->updateUser();

            $_SESSION['success'] = "Profile updated successfully!";
            header("Location: /CeylonGo/public/admin/profile");
        } else {
            header("Location: /CeylonGo/public/admin/profile?error=" . urlencode("Failed to update profile"));
        }
        exit();
    }

    public function deleteProfile() {
        $admin_id = $_SESSION['user_ref_id'] ?? null;

        if (!$admin_id) {
            header("Location: /CeylonGo/public/login");
            exit();
        }

        $admin = new Admin($this->db);
        if ($admin->deleteProfile($admin_id)) {
            // Delete from users table
            $authUser = new AuthUser($this->db);
            $authUser->deleteUser($admin_id, 'admin');

            session_destroy();
            header("Location: /CeylonGo/public/login?msg=Profile+Deleted");
        } else {
            header("Location: /CeylonGo/public/admin/profile?error=" . urlencode("Failed to delete profile"));
        }
        exit();
    }

    public function toggleUserStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }

        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            exit();
        }

        $userId = intval($_POST['user_id'] ?? 0);
        $status = intval($_POST['status'] ?? 1);

        $userModel = new User($this->db); // ✅ Use User class
        $success = $userModel->updateStatus($userId, $status);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit();
    }

    public function users() {
        $userModel = new User($this->db); 

        // ✅ Handle POST for editing user
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
            $userId = intval($_POST['user_id']);
            $firstName = trim($_POST['first_name']);
            $lastName = trim($_POST['last_name']);
            $contact = trim($_POST['contact']);
            $email = trim($_POST['email']);

            $success = $userModel->updateUserByAdmin($userId, $firstName, $lastName, $contact, $email);

            if ($success) {
                $_SESSION['success'] = "User updated successfully!";
            } else {
                $_SESSION['error'] = "Failed to update user.";
            }

            header("Location: /CeylonGo/public/admin/users");
            exit();
        }
        // GET / display users
        $status = $_GET['status'] ?? 'all';
        $users = $userModel->getAllUsers($status);
        $stats = $userModel->getUserStats();

        view('admin/admin_user', [
            'users' => $users,
            'selectedStatus' => $status,
            'stats' => $stats
        ]);
    }

    public function bookings() {
        $status = $_GET['status'] ?? 'all';      // from filter button
        $searchId = $_GET['search'] ?? null;    // from search input
        $date = $_GET['date'] ?? null;

        $bookingModel = new Booking($this->db);
        $bookings = $bookingModel->getAllBookingsWithUsers($status, $searchId, $date);
        $stats = $bookingModel->getBookingStats(); // statistics

        view('admin/admin_bookings', [
            'bookings' => $bookings, 
            'selectedStatus' => $status, 
            'searchId' => $searchId,
            'date' => $date,
            'stats' => $stats
        ]);
    }

    public function getBookingDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['booking_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Booking ID missing']);
            exit;
        }

        $bookingId = intval($_GET['booking_id']);
        $bookingModel = new Booking($this->db);

        // Fetch booking info
        $booking = $bookingModel->getBookingById($bookingId);

        if (!$booking) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Booking not found']);
            exit;
        }

        // Fetch destinations
        $destinations = $bookingModel->getBookingDestinations($bookingId);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'booking' => $booking,
            'destinations' => $destinations
        ]);
        exit;
    }

    public function payments() {
        view('admin/admin_payments');
    }

    public function reviews() {
        view('admin/admin_reviews');
    }

    public function inquiries() {
        view('admin/admin_inquiries');
    }

    public function promotions() {
        view('admin/admin_promotions');
    }

    public function reports() {
        view('admin/admin_reports');
    }

    public function service() {
        $conn = Database::getMysqliConnection();

        // Fetch all providers (union guides, hotels, transport)
        $sql = "
            SELECT 
                CONCAT(g.first_name, ' ', g.last_name) AS provider_name,
                u.email,
                u.role
            FROM users u
            JOIN guide_users g ON u.ref_id = g.id
            WHERE u.role = 'guide'

            UNION ALL

            SELECT 
                t.full_name AS provider_name,
                u.email,
                u.role
            FROM users u
            JOIN transport_users t ON u.ref_id = t.user_id
            WHERE u.role = 'transport'

            UNION ALL

            SELECT 
                h.hotel_name AS provider_name,
                u.email,
                u.role
            FROM users u
            JOIN hotel_users h ON u.ref_id = h.id
            WHERE u.role = 'hotel'
        ";

        $result = $conn->query($sql);
        $providers = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $providers[] = $row;
            }
        }

        // Role labels for display
        $roleLabels = [
            'guide'     => 'Tour Guide',
            'hotel'     => 'Hotel',
            'transport' => 'Transport Provider'
        ];

        // Statistics
        $stats = [
            'total' => count($providers),
            'guide' => 0,
            'hotel' => 0,
            'transport' => 0
        ];

        foreach ($providers as $p) {
            if (isset($p['role'])) {
                if ($p['role'] === 'guide') $stats['guide']++;
                if ($p['role'] === 'hotel') $stats['hotel']++;
                if ($p['role'] === 'transport') $stats['transport']++;
            }
        }

        // Pass data to the view
        view('admin/admin_service', [
            'providers' => $providers,
            'stats' => $stats,
            'roleLabels' => $roleLabels
        ]);
    }

    public function settings() {
        view('admin/admin_settings');
    }

    public function forgotPassword() {
        view('admin/admin_forgot_pwd');
    }
}
?>

