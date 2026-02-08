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

    public function toggleProviderStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }

        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
        exit();
        }

        $providerId = intval($_POST['provider_id'] ?? 0);
        $status = intval($_POST['status'] ?? 1);

        if (!$providerId) {
            echo json_encode(['success' => false]);
            exit();
        }

        // Determine which table to update based on role
        $conn = Database::getMysqliConnection();

        // Fetch provider's role from users table
        $stmt = $conn->prepare("SELECT role FROM users WHERE ref_id=?");
        $stmt->bind_param("i", $providerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if (!$row) {
            echo json_encode(['success' => false]);
            exit();
        }

        $role = $row['role'];
        $table = '';
        $idField = '';

        switch($role){
            case 'guide':
                $table = 'guide_users';
                $idField = 'id';
                break;
            case 'hotel':
                $table = 'hotel_users';
                $idField = 'id';
                break;
            case 'transport':
                $table = 'transport_users';
                $idField = 'user_id';
                break;
            default:
                echo json_encode(['success' => false]);
                exit();
        }

        $stmt = $conn->prepare("UPDATE $table SET is_active=? WHERE $idField=?");
        $stmt->bind_param("ii", $status, $providerId);
        $success = $stmt->execute();

        echo json_encode(['success' => $success]);
        exit();
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

    public function flagBooking() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $bookingId = intval($data['booking_id'] ?? 0);
        $reason = trim($data['reason'] ?? '');

        if(!$bookingId || !$reason){
            echo json_encode(['success'=>false, 'message'=>'Invalid input']);
            exit;
        }

        $stmt = $this->db->prepare("UPDATE trip_bookings SET is_flagged=1, flag_reason=:reason WHERE id=:id");
        $success = $stmt->execute([':reason'=>$reason, ':id'=>$bookingId]);

        echo json_encode(['success'=>$success]);
        exit;
    }

    public function payments() {
        view('admin/admin_payments');
    }

    public function reviews() {
        $reviewModel = new Review($this->db);

        // GET filter (same logic as users)
        $rating = $_GET['rating'] ?? 'all';

        $reviews = $reviewModel->getAllReviews($rating);

        view('admin/admin_reviews', [
            'reviews' => $reviews,
            'selectedRating' => $rating
        ]);
    }

    public function deleteReview() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }

        $reviewId = intval($_POST['review_id'] ?? 0);
        $reviewModel = new Review($this->db);
        $success = $reviewModel->deleteReview($reviewId);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit();
    }

    public function replyToReview()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }

        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            exit();
        }

        $reviewId = intval($_POST['review_id'] ?? 0);
        $reply = trim($_POST['reply'] ?? '');

        if ($reviewId === 0 || $reply === '') {
            echo json_encode(['success' => false]);
            exit();
        }

        $reviewModel = new Review($this->db);
        $success = $reviewModel->saveAdminReply($reviewId, $reply);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit();
    }

    public function inquiries() {
        view('admin/admin_inquiries');
    }

    public function promotions() {
        view('admin/admin_promotions');
    }

    public function reports() {
        $bookingModel = new Booking($this->db);
        $stats = $bookingModel->getBookingStats();

        $totalBookings = $stats['total'] ?? 0;
        $totalCancellations = $stats['cancelled'] ?? 0;

        // Get period from URL, default to 'daily'
        $period = $_GET['period'] ?? 'daily';

        // Get aggregated data based on period
        $chartData = $bookingModel->getAggregatedBookings($period);

        // Extract arrays for Chart.js
        $labels = array_column($chartData, 'period');
        $bookings = array_column($chartData, 'total');
        $cancellations = array_column($chartData, 'cancelled');

        view('admin/admin_reports', [
            'totalBookings' => $totalBookings,
            'totalCancellations' => $totalCancellations,
            'labels' => $labels,
            'bookings' => $bookings,
            'cancellations' => $cancellations,
            'period' => $period
        ]);
    }

    public function service() {
        $conn = Database::getMysqliConnection();

        // Fetch all providers (union guides, hotels, transport)
        $sql = "
            SELECT g.id AS id,
                CONCAT(g.first_name, ' ', g.last_name) AS provider_name,
                u.email,
                u.role,
                g.is_active
            FROM users u
            JOIN guide_users g ON u.ref_id = g.id
            WHERE u.role = 'guide'

            UNION ALL

            SELECT t.user_id AS id,
                t.full_name AS provider_name,
                u.email,
                u.role,
                t.is_active
            FROM users u
            JOIN transport_users t ON u.ref_id = t.user_id
            WHERE u.role = 'transport'

            UNION ALL

            SELECT h.id AS id,
                h.hotel_name AS provider_name,
                u.email,
                u.role,
                h.is_active
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

