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

    public function users() {
        view('admin/admin_user');
    }

    public function bookings() {
        // Fetch all package bookings from database
        try {
            $sql = "SELECT pb.*, 
                    u.email as user_email,
                    (SELECT first_name FROM tourists WHERE id = pb.user_id) as user_first_name,
                    (SELECT last_name FROM tourists WHERE id = pb.user_id) as user_last_name
                    FROM package_bookings pb
                    LEFT JOIN users u ON u.ref_id = pb.user_id AND u.role = 'tourist'
                    ORDER BY pb.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculate statistics
            $stats = [
                'total' => count($bookings),
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0,
                'cancelled' => 0
            ];
            foreach ($bookings as $booking) {
                $status = $booking['status'] ?? 'pending';
                if (isset($stats[$status])) {
                    $stats[$status]++;
                }
            }
        } catch (PDOException $e) {
            error_log("Error fetching bookings: " . $e->getMessage());
            $bookings = [];
            $stats = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0, 'cancelled' => 0];
        }
        
        view('admin/admin_bookings', ['bookings' => $bookings, 'stats' => $stats]);
    }
    
    public function approveBooking() {
        if (!isset($_SESSION['user_ref_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /CeylonGo/public/login');
            exit;
        }
        
        $booking_id = isset($_POST['booking_id']) ? (int) $_POST['booking_id'] : 0;
        $action = isset($_POST['action']) ? trim($_POST['action']) : '';
        $admin_notes = isset($_POST['admin_notes']) ? trim($_POST['admin_notes']) : '';
        
        if ($booking_id <= 0 || !in_array($action, ['approve', 'reject'])) {
            header('Location: /CeylonGo/public/admin/bookings?error=' . urlencode('Invalid request'));
            exit;
        }
        
        $status = $action === 'approve' ? 'approved' : 'rejected';
        $admin_id = (int) $_SESSION['user_ref_id'];
        
        try {
            $sql = "UPDATE package_bookings 
                    SET status = ?, approved_by = ?, approved_at = NOW(), admin_notes = ?
                    WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$status, $admin_id, $admin_notes, $booking_id]);
            
            header('Location: /CeylonGo/public/admin/bookings?success=' . urlencode('Booking ' . $status . ' successfully'));
        } catch (PDOException $e) {
            error_log("Error updating booking: " . $e->getMessage());
            header('Location: /CeylonGo/public/admin/bookings?error=' . urlencode('Failed to update booking'));
        }
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
        view('admin/admin_service');
    }

    public function settings() {
        view('admin/admin_settings');
    }

    public function forgotPassword() {
        view('admin/admin_forgot_pwd');
    }
}
?>

