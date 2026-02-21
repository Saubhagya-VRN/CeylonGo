<?php
class AdminController {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function dashboard() {
        view('admin/dashboard');
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
            view('admin/profile', ['admin' => $admin]);
        } else {
            view('admin/profile');
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

        view('admin/user', [
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
        $status   = $_GET['status'] ?? 'all';
        $searchId = $_GET['search'] ?? null;
        $date     = $_GET['date']   ?? null;

        // Existing trip bookings
        $bookingModel = new Booking($this->db);
        $bookings     = $bookingModel->getAllBookingsWithUsers($status, $searchId, $date);
        $stats        = $bookingModel->getBookingStats();

        // Package bookings — fetch all from package_bookings table
        $pkgStmt = $this->db->prepare("
            SELECT *
            FROM package_bookings
            ORDER BY created_at DESC
        ");
        $pkgStmt->execute();
        $packageBookings = $pkgStmt->fetchAll(PDO::FETCH_ASSOC);

        // Package booking stats
        $pkgStats = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
        foreach ($packageBookings as $pb) {
            $pkgStats['total']++;
            $s = strtolower($pb['status']);
            if (isset($pkgStats[$s])) $pkgStats[$s]++;
        }

        view('admin/bookings', [
            'bookings'        => $bookings,
            'selectedStatus'  => $status,
            'searchId'        => $searchId,
            'date'            => $date,
            'stats'           => $stats,
            'packageBookings' => $packageBookings,
            'pkgStats'        => $pkgStats,
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

    public function updatePackageBookingStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }

        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id          = intval($_POST['id']          ?? 0);
        $status      = trim($_POST['status']        ?? '');
        $adminNotes  = trim($_POST['admin_notes']   ?? '');
        $adminUserId = $_SESSION['user_ref_id']     ?? null;

        $allowed = ['approved', 'rejected', 'cancelled'];
        if (!$id || !in_array($status, $allowed, true)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
            exit();
        }

        if ($status === 'approved') {
            $stmt = $this->db->prepare("
                UPDATE package_bookings
                SET status      = :status,
                    admin_notes = :notes,
                    approved_at = NOW(),
                    approved_by = :admin_id,
                    updated_at  = NOW()
                WHERE id = :id
            ");
            $stmt->execute([
                ':status'   => $status,
                ':notes'    => $adminNotes,
                ':admin_id' => $adminUserId,
                ':id'       => $id,
            ]);
        } else {
            $stmt = $this->db->prepare("
                UPDATE package_bookings
                SET status      = :status,
                    admin_notes = :notes,
                    updated_at  = NOW()
                WHERE id = :id
            ");
            $stmt->execute([
                ':status' => $status,
                ':notes'  => $adminNotes,
                ':id'     => $id,
            ]);
        }

        $affected = $stmt->rowCount();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $affected > 0,
            'message' => $affected > 0 ? 'Status updated' : 'No rows updated — check the ID',
        ]);
        exit();
    }

    public function payments() {
        view('admin/payments');
    }

    public function reviews()
    {
        $reviewModel = new Review($this->db);

        // Filter
        $rating = $_GET['rating'] ?? 'all';

        // Data
        $reviews = $reviewModel->getAllReviews($rating);
        $metrics = $reviewModel->getReviewMetrics();

        view('admin/reviews', [
            'reviews' => $reviews,
            'selectedRating' => $rating,
            'metrics' => $metrics
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

    public function approveReview()
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
        if (!$reviewId) {
            echo json_encode(['success' => false]);
            exit();
        }

        $stmt = $this->db->prepare(
            "UPDATE reviews SET status = 'approved', approved_at = NOW() WHERE id = :id"
        );
        $success = $stmt->execute([':id' => $reviewId]);

        echo json_encode(['success' => $success]);
        exit();
    }

    public function inquiries() {
        view('admin/inquiries');
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

        view('admin/reports', [
            'totalBookings' => $totalBookings,
            'totalCancellations' => $totalCancellations,
            'labels' => $labels,
            'bookings' => $bookings,
            'cancellations' => $cancellations,
            'period' => $period
        ]);
    }

    public function service() {
        $status = $_GET['status'] ?? 'all';
        $conn = Database::getMysqliConnection();

        $whereStatus = "";
        if ($status === 'active') {
            $whereStatus = " AND is_active = 1 ";
        } elseif ($status === 'inactive') {
            $whereStatus = " AND is_active = 0 ";
        }

        $sql = "
            SELECT g.id AS id,
                CONCAT(g.first_name, ' ', g.last_name) AS provider_name,
                u.email,
                u.role,
                g.is_active
            FROM users u
            JOIN guide_users g ON u.ref_id = g.id
            WHERE u.role = 'guide' $whereStatus

            UNION ALL

            SELECT t.user_id AS id,
                t.full_name AS provider_name,
                u.email,
                u.role,
                t.is_active
            FROM users u
            JOIN transport_users t ON u.ref_id = t.user_id
            WHERE u.role = 'transport' $whereStatus

            UNION ALL

            SELECT h.id AS id,
                h.hotel_name AS provider_name,
                u.email,
                u.role,
                h.is_active
            FROM users u
            JOIN hotel_users h ON u.ref_id = h.id
            WHERE u.role = 'hotel' $whereStatus
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
        view('admin/service', [
            'providers' => $providers,
            'stats' => $stats,
            'roleLabels' => $roleLabels,
            'selectedStatus' => $status
        ]);
    }

    public function settings() {
        view('admin/settings');
    }

    public function forgotPassword() {
        view('admin/forgot_pwd');
    }

    // ─── Helper: admin auth guard ────────────────────────────
    private function requireAdmin() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            header('Location: /CeylonGo/public/login');
            exit();
        }
    }

    // ─── LIST packages ───────────────────────────────────────
    public function packages() {
        $this->requireAdmin();
        $packageModel = new Package($this->db);
        $packages = $packageModel->getAll();
        $success = $_SESSION['pkg_success'] ?? null;
        $error   = $_SESSION['pkg_error']   ?? null;
        unset($_SESSION['pkg_success'], $_SESSION['pkg_error']);
        view('admin/packages', compact('packages', 'success', 'error'));
    }

    // ─── SHOW add form ───────────────────────────────────────
    public function packageNew() {
        $this->requireAdmin();
        $error   = $_SESSION['pkg_error'] ?? null;
        $package = $_SESSION['pkg_old']   ?? null;
        unset($_SESSION['pkg_error'], $_SESSION['pkg_old']);
        $mode = 'create';
        self::loadPackageForm(compact('mode', 'package', 'error'));
    }

    // ─── CREATE package (POST) ───────────────────────────────
    public function packageCreate() {
        $this->requireAdmin();
        $data = $this->buildPackageData($_POST);
        $errors = $this->validatePackageData($data);

        if ($errors) {
            $_SESSION['pkg_error'] = implode('<br>', $errors);
            $_SESSION['pkg_old']   = $_POST;
            header('Location: /CeylonGo/public/admin/packages/new');
            exit();
        }

        $packageModel = new Package($this->db);
        $id = $packageModel->create($data);
        if ($id) {
            $_SESSION['pkg_success'] = 'Package created successfully!';
        } else {
            $_SESSION['pkg_error'] = 'Failed to create package. Please try again.';
        }
        header('Location: /CeylonGo/public/admin/packages');
        exit();
    }

    // ─── SHOW edit form ──────────────────────────────────────
    public function packageEdit() {
        $this->requireAdmin();
        $id = intval($_GET['id'] ?? 0);
        $packageModel = new Package($this->db);
        $package = $packageModel->getById($id);
        if (!$package) {
            $_SESSION['pkg_error'] = 'Package not found.';
            header('Location: /CeylonGo/public/admin/packages');
            exit();
        }
        $error = $_SESSION['pkg_error'] ?? null;
        unset($_SESSION['pkg_error']);
        $mode = 'edit';
        self::loadPackageForm(compact('mode', 'package', 'error'));
    }

    // ─── UPDATE package (POST) ───────────────────────────────
    public function packageUpdate() {
        $this->requireAdmin();
        $id = intval($_POST['id'] ?? 0);
        if (!$id) {
            header('Location: /CeylonGo/public/admin/packages');
            exit();
        }
        $data = $this->buildPackageData($_POST);
        $errors = $this->validatePackageData($data);

        if ($errors) {
            $_SESSION['pkg_error'] = implode('<br>', $errors);
            header('Location: /CeylonGo/public/admin/packages/edit?id=' . $id);
            exit();
        }

        $packageModel = new Package($this->db);
        $ok = $packageModel->update($id, $data);
        if ($ok) {
            $_SESSION['pkg_success'] = 'Package updated successfully!';
        } else {
            $_SESSION['pkg_error'] = 'Failed to update package. Please try again.';
        }
        header('Location: /CeylonGo/public/admin/packages');
        exit();
    }

    // ─── DELETE package (POST) ───────────────────────────────
    public function packageDelete() {
        $this->requireAdmin();
        $id = intval($_POST['id'] ?? 0);
        if ($id) {
            $packageModel = new Package($this->db);
            $ok = $packageModel->delete($id);
            $_SESSION[$ok ? 'pkg_success' : 'pkg_error'] = $ok ? 'Package deleted.' : 'Failed to delete package.';
        }
        header('Location: /CeylonGo/public/admin/packages');
        exit();
    }

    // ─── Private: load the package form view ─────────────────
    private static function loadPackageForm(array $data) {
        extract($data);
        // Build path the same way your view() helper does:
        // views folder sits one level above the controllers folder.
        $file = dirname(__DIR__) . '/views/admin/package_form.php';
        if (file_exists($file)) {
            require $file;
        } else {
            // Safety fallback — shows the real path so you can debug
            die('package_form.php not found. Expected at: ' . $file);
        }
    }
    
    // ─── Private helpers ─────────────────────────────────────

    /**
     * Build the $data array from raw $_POST input.
     * Converts textarea/repeatable fields into the arrays Package model expects.
     */
    private function buildPackageData(array $post): array {
        // Scalar fields
        $data = [
            'title'              => trim($post['title']             ?? ''),
            'location'           => trim($post['location']          ?? ''),
            'locations'          => trim($post['locations']         ?? ''),
            'duration'           => trim($post['duration']          ?? ''),
            'duration_short'     => trim($post['duration_short']    ?? ''),
            'image'              => trim($post['image']             ?? ''),
            'category'           => strtolower(trim($post['category'] ?? '')),
            'price'              => intval($post['price']            ?? 0),
            'price_child_ratio'  => floatval($post['price_child_ratio']  ?? 0.50),
            'price_infant_ratio' => floatval($post['price_infant_ratio'] ?? 0.00),
            'rating'             => $post['rating']  !== '' ? floatval($post['rating'])  : null,
            'reviews'            => $post['reviews'] !== '' ? intval($post['reviews'])   : 0,
            'trending'           => !empty($post['trending']) ? 1 : 0,
        ];

        // overview – one sentence/bullet per line → array of strings
        $data['overview'] = $this->parseLines($post['overview'] ?? '');

        // included / excluded – one item per line → array of strings
        $data['included'] = $this->parseLines($post['included'] ?? '');
        $data['excluded'] = $this->parseLines($post['excluded'] ?? '');

        // highlights – repeatable group: icon[], h_title[], h_desc[]
        $icons   = $post['h_icon']  ?? [];
        $htitles = $post['h_title'] ?? [];
        $hdescs  = $post['h_desc']  ?? [];
        $highlights = [];
        for ($i = 0; $i < count($icons); $i++) {
            $icon  = trim($icons[$i]   ?? '');
            $title = trim($htitles[$i] ?? '');
            $desc  = trim($hdescs[$i]  ?? '');
            if ($icon !== '' || $title !== '') {
                $highlights[] = ['icon' => $icon, 'title' => $title, 'desc' => $desc];
            }
        }
        $data['highlights'] = $highlights;

        // itinerary – repeatable group: it_day[], it_title[], it_activities[] (one activity per line)
        $itDays  = $post['it_day']        ?? [];
        $itTitles= $post['it_title']      ?? [];
        $itActs  = $post['it_activities'] ?? [];
        $itinerary = [];
        for ($i = 0; $i < count($itDays); $i++) {
            $day   = intval($itDays[$i]   ?? 0);
            $title = trim($itTitles[$i]   ?? '');
            $acts  = $this->parseLines($itActs[$i] ?? '');
            if ($day > 0 || $title !== '') {
                $itinerary[] = ['day' => $day ?: ($i + 1), 'title' => $title, 'activities' => $acts];
            }
        }
        $data['itinerary'] = $itinerary;

        // accommodation – repeatable group: acc_nights[], acc_location[], acc_hotel[]
        $accNights   = $post['acc_nights']   ?? [];
        $accLocs     = $post['acc_location'] ?? [];
        $accHotels   = $post['acc_hotel']    ?? [];
        $accommodation = [];
        for ($i = 0; $i < count($accNights); $i++) {
            $nights  = intval(trim($accNights[$i]  ?? ''));
            $loc     = trim($accLocs[$i]   ?? '');
            $hotel   = trim($accHotels[$i] ?? '');
            if ($nights > 0 || $loc !== '' || $hotel !== '') {
                $accommodation[] = ['nights' => $nights, 'location' => $loc, 'hotel' => $hotel];
            }
        }
        $data['accommodation'] = $accommodation;

        return $data;
    }

    /**
     * Validate the $data array. Returns array of error strings (empty = ok).
     */
    private function validatePackageData(array $data): array {
        $errors = [];
        $validCategories = ['cultural','honeymoon','solo','adventure','heritage','safari','family','beach'];

        if (empty($data['title']))    $errors[] = 'Title is required.';
        if (empty($data['category'])) $errors[] = 'Category is required.';
        elseif (!in_array($data['category'], $validCategories, true))
            $errors[] = 'Invalid category. Allowed: ' . implode(', ', $validCategories);
        if (empty($data['price']) || $data['price'] <= 0) $errors[] = 'A valid positive price (LKR) is required.';
        if (isset($data['rating'])  && $data['rating']  !== null && !is_numeric($data['rating']))  $errors[] = 'Rating must be a number.';
        if (isset($data['reviews']) && $data['reviews'] !== null && !is_numeric($data['reviews'])) $errors[] = 'Reviews must be a number.';

        return $errors;
    }

    /**
     * Split a textarea value into an array of non-empty trimmed lines.
     */
    private function parseLines(string $text): array {
        $lines = explode("\n", str_replace("\r", '', $text));
        return array_values(array_filter(array_map('trim', $lines)));
    }

}
?>

