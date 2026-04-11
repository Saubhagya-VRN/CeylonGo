<?php
class AdminController {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function dashboard()
    {
        // ── Support AJAX chart refresh from the dashboard ────────────────────
        $isAjax = !empty($_GET['ajax']) || !empty($_SERVER['HTTP_X_REQUESTED_WITH']);
 
        if ($isAjax) {
            // Reuse the same chart-data logic as reports(), but return JSON
            $period      = in_array($_GET['period']       ?? '', ['weekly','monthly','yearly'])
                           ? $_GET['period'] : 'monthly';
            $bookingType = in_array($_GET['booking_type'] ?? '', ['both','package','custom'])
                           ? $_GET['booking_type'] : 'both';
 
            switch ($period) {
                case 'weekly':
                    $bucketExpr = "CONCAT(YEAR(created_at),'-W',LPAD(WEEK(created_at,1),2,'0'))";
                    break;
                case 'yearly':
                    $bucketExpr = "YEAR(created_at)";
                    break;
                default: // monthly
                    $bucketExpr = "DATE_FORMAT(created_at,'%Y-%m')";
            }
 
            $rows = [];
 
            if ($bookingType === 'package' || $bookingType === 'both') {
                $sql = "
                    SELECT {$bucketExpr} AS period,
                           COUNT(*) AS total,
                           COALESCE(SUM(total_amount),0) AS revenue,
                           SUM(status='cancelled') AS cancelled
                    FROM package_bookings
                    GROUP BY period ORDER BY period ASC
                ";
                $stmt = $this->db->query($sql);
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
                    $k = (string)$r['period'];
                    if (!isset($rows[$k])) $rows[$k] = ['period'=>$k,'total'=>0,'revenue'=>0.0,'cancelled'=>0];
                    $rows[$k]['total']     += (int)$r['total'];
                    $rows[$k]['revenue']   += (float)$r['revenue'];
                    $rows[$k]['cancelled'] += (int)$r['cancelled'];
                }
            }
 
            if ($bookingType === 'custom' || $bookingType === 'both') {
                $sql = "
                    SELECT {$bucketExpr} AS period,
                           COUNT(*) AS total,
                           COALESCE(SUM(CASE WHEN status='completed' THEN budget_lkr ELSE 0 END),0) AS revenue,
                           SUM(status='cancelled' OR refund_requested_at IS NOT NULL) AS cancelled
                    FROM trips
                    GROUP BY period ORDER BY period ASC
                ";
                $stmt = $this->db->query($sql);
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
                    $k = (string)$r['period'];
                    if (!isset($rows[$k])) $rows[$k] = ['period'=>$k,'total'=>0,'revenue'=>0.0,'cancelled'=>0];
                    $rows[$k]['total']     += (int)$r['total'];
                    $rows[$k]['revenue']   += (float)$r['revenue'];
                    $rows[$k]['cancelled'] += (int)$r['cancelled'];
                }
            }
 
            ksort($rows);
            $rows = array_values($rows);
 
            header('Content-Type: application/json');
            echo json_encode([
                'labels'        => array_column($rows, 'period'),
                'bookings'      => array_column($rows, 'total'),
                'revenue'       => array_map('floatval', array_column($rows, 'revenue')),
                'cancellations' => array_column($rows, 'cancelled'),
            ]);
            exit();
        }
 
        // ── Full page render: just delegate to the view ──────────────────────
        // All PHP data fetching is done inside the view itself (dashboard.php)
        // so that the view stays self-contained (existing project pattern).
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
 
        $bookingModel = new Booking($this->db);
        $bookings     = $bookingModel->getAllBookingsWithUsers($status, $searchId, $date);
        $stats        = $bookingModel->getBookingStats();
 
        // Pre-load trip details for every booking (used in export report)
        $bookingsWithDestinations = [];
        foreach ($bookings as $b) {
            $destinations = $bookingModel->getBookingDestinations($b['booking_id']);
            $bookingsWithDestinations[] = array_merge($b, ['destinations' => $destinations]);
        }
 
        // Package bookings
        $pkgStmt = $this->db->prepare("
            SELECT * FROM package_bookings ORDER BY created_at DESC
        ");
        $pkgStmt->execute();
        $packageBookings = $pkgStmt->fetchAll(PDO::FETCH_ASSOC);
 
        $pkgStats = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
        foreach ($packageBookings as $pb) {
            $pkgStats['total']++;
            $s = strtolower($pb['status']);
            if (isset($pkgStats[$s])) $pkgStats[$s]++;
        }
 
        view('admin/bookings', [
            'bookings'                 => $bookings,
            'bookingsWithDestinations' => $bookingsWithDestinations,
            'selectedStatus'           => $status,
            'searchId'                 => $searchId,
            'date'                     => $date,
            'stats'                    => $stats,
            'packageBookings'          => $packageBookings,
            'pkgStats'                 => $pkgStats,
        ]);
    }
 
    public function getBookingDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['booking_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Booking ID missing']);
            exit;
        }
 
        $bookingId    = intval($_GET['booking_id']);
        $bookingModel = new Booking($this->db);
        $booking      = $bookingModel->getBookingById($bookingId);
 
        if (!$booking) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Booking not found']);
            exit;
        }
 
        $destinations = $bookingModel->getBookingDestinations($bookingId);
 
        header('Content-Type: application/json');
        echo json_encode([
            'success'      => true,
            'booking'      => $booking,
            'destinations' => $destinations,
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
        // Fetch all package bookings that have any payment activity
        // (paid, approved-awaiting-payment, bank transfer submitted, etc.)
        $stmt = $this->db->prepare("
            SELECT *
            FROM package_bookings
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
        // Build stats
        $payStats = [
            'total'     => 0,
            'paid'      => 0,
            'approved'  => 0,
            'pending'   => 0,
            'rejected'  => 0,
            'cancelled' => 0,
        ];
        foreach ($payments as $p) {
            $payStats['total']++;
            $s = strtolower($p['status']);
            if (isset($payStats[$s])) $payStats[$s]++;
        }
 
        view('admin/payments', [
            'payments' => $payments,
            'payStats' => $payStats,
        ]);
    }

    public function verifyPayment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }
 
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
 
        $bookingId   = intval($_POST['booking_id'] ?? 0);
        $adminUserId = $_SESSION['user_ref_id'] ?? null;
 
        if (!$bookingId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
            exit();
        }
 
        $stmt = $this->db->prepare("
            UPDATE package_bookings
            SET status      = 'paid',
                paid_at     = NOW(),
                approved_by = :admin_id,
                updated_at  = NOW()
            WHERE id = :id
              AND status IN ('pending', 'approved')
        ");
        $stmt->execute([
            ':admin_id' => $adminUserId,
            ':id'       => $bookingId,
        ]);
 
        $affected = $stmt->rowCount();
 
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $affected > 0,
            'message' => $affected > 0 ? 'Marked as paid' : 'No rows updated',
        ]);
        exit();
    }

    public function approveSlipPayment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); exit();
        }
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
 
        $bookingId   = intval($_POST['booking_id'] ?? 0);
        $adminUserId = $_SESSION['user_ref_id'] ?? null;
 
        if (!$bookingId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
            exit();
        }
 
        // Only approve if a slip has actually been submitted and it's not already paid
        $stmt = $this->db->prepare("
            UPDATE package_bookings
            SET status      = 'paid',
                paid_at     = NOW(),
                approved_by = :admin_id,
                updated_at  = NOW()
            WHERE id        = :id
              AND bank_transfer_slip_path IS NOT NULL
              AND bank_transfer_slip_path != ''
              AND status IN ('pending', 'approved')
              AND paid_at IS NULL
        ");
        $stmt->execute([':admin_id' => $adminUserId, ':id' => $bookingId]);
        $affected = $stmt->rowCount();
 
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $affected > 0,
            'message' => $affected > 0 ? 'Payment approved' : 'Nothing updated — check slip exists and status is pending/approved',
        ]);
        exit();
    }
 
    // ── Approve a refund request — marks booking as 'cancelled' ─
    // ── Approve a refund request (package) ──────────────────────────────────
    public function approveRefund() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(); }
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $bookingId   = intval($_POST['booking_id'] ?? 0);
        $adminUserId = $_SESSION['user_ref_id'] ?? null;

        if (!$bookingId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
            exit();
        }

        // Fetch booking details (need email, name, amount)
        $fetch = $this->db->prepare("
            SELECT pb.id, pb.fullname, pb.email, pb.total_amount, pb.refund_requested_at
            FROM package_bookings pb
            WHERE pb.id = :id AND pb.refund_requested_at IS NOT NULL
        ");
        $fetch->execute([':id' => $bookingId]);
        $booking = $fetch->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Booking not found or no refund requested']);
            exit();
        }

        // Update DB: cancel booking, record refund_approved_at
        $stmt = $this->db->prepare("
            UPDATE package_bookings
            SET status             = 'cancelled',
                refund_approved_at = NOW(),
                admin_notes        = CONCAT(IFNULL(admin_notes, ''), ' | Refund approved by admin on ', NOW()),
                approved_by        = :admin_id,
                updated_at         = NOW()
            WHERE id = :id
            AND refund_requested_at IS NOT NULL
        ");
        $stmt->execute([':admin_id' => $adminUserId, ':id' => $bookingId]);
        $affected = $stmt->rowCount();

        if ($affected > 0) {
            // Send email asking for bank account details
            $this->sendRefundBankDetailsRequest(
                $booking['email'],
                $booking['fullname'],
                $bookingId,
                $booking['total_amount'],
                'package'
            );
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $affected > 0,
            'message' => $affected > 0
                ? 'Refund approved. Email sent to customer for bank details.'
                : 'Nothing updated — check refund was requested',
        ]);
        exit();
    }

    // ── Reject a refund request (package) ───────────────────────────────────
    public function rejectRefund() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(); }
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $bookingId   = intval($_POST['booking_id'] ?? 0);
        $rejectNote  = trim($_POST['reject_note'] ?? '');
        $adminUserId = $_SESSION['user_ref_id'] ?? null;

        if (!$bookingId || !$rejectNote) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing booking ID or rejection note']);
            exit();
        }

        // Fetch booking details
        $fetch = $this->db->prepare("
            SELECT id, fullname, email FROM package_bookings
            WHERE id = :id AND refund_requested_at IS NOT NULL
        ");
        $fetch->execute([':id' => $bookingId]);
        $booking = $fetch->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Booking not found or no refund requested']);
            exit();
        }

        // Save rejection to DB
        $stmt = $this->db->prepare("
            UPDATE package_bookings
            SET refund_rejected_at = NOW(),
                refund_reject_note = :note,
                admin_notes        = CONCAT(IFNULL(admin_notes, ''), ' | Refund rejected by admin on ', NOW(), ': ', :note2),
                updated_at         = NOW()
            WHERE id = :id
            AND refund_requested_at IS NOT NULL
            AND refund_rejected_at IS NULL
        ");
        $stmt->execute([
            ':note'  => $rejectNote,
            ':note2' => $rejectNote,
            ':id'    => $bookingId,
        ]);
        $affected = $stmt->rowCount();

        if ($affected > 0) {
            // Notify customer of rejection
            $this->sendRefundRejectionEmail(
                $booking['email'],
                $booking['fullname'],
                $bookingId,
                $rejectNote,
                'package'
            );
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $affected > 0,
            'message' => $affected > 0 ? 'Refund rejected and customer notified.' : 'Nothing updated',
        ]);
        exit();
    }

    // ── Approve a refund request (trip/custom booking) ───────────────────────
    public function approveTripRefund() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(); }
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $tripId      = intval($_POST['trip_id'] ?? 0);
        $adminUserId = $_SESSION['user_ref_id'] ?? null;

        if (!$tripId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid trip ID']);
            exit();
        }

        // Fetch trip details — join with users to get email
        // Adjust the column/table names if your trips table stores customer info differently
        $fetch = $this->db->prepare("
            SELECT t.id, t.customer_name, t.budget_lkr, t.refund_requested_at,
                u.email
            FROM trips t
            JOIN users u ON u.ref_id = t.user_id
            WHERE t.id = :id AND t.refund_requested_at IS NOT NULL
        ");
        $fetch->execute([':id' => $tripId]);
        $trip = $fetch->fetch(PDO::FETCH_ASSOC);

        if (!$trip) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Trip not found or no refund requested']);
            exit();
        }

        $stmt = $this->db->prepare("
            UPDATE trips
            SET status             = 'cancelled',
                refund_approved_at = NOW(),
                updated_at         = NOW()
            WHERE id = :id
            AND refund_requested_at IS NOT NULL
        ");
        $stmt->execute([':id' => $tripId]);
        $affected = $stmt->rowCount();

        if ($affected > 0) {
            $this->sendRefundBankDetailsRequest(
                $trip['email'],
                $trip['customer_name'],
                $tripId,
                $trip['budget_lkr'],
                'trip'
            );
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $affected > 0,
            'message' => $affected > 0
                ? 'Refund approved. Email sent to customer for bank details.'
                : 'Nothing updated',
        ]);
        exit();
    }

    // ── Reject a refund request (trip/custom booking) ────────────────────────
    public function rejectTripRefund() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(); }
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $tripId     = intval($_POST['trip_id'] ?? 0);
        $rejectNote = trim($_POST['reject_note'] ?? '');

        if (!$tripId || !$rejectNote) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing trip ID or rejection note']);
            exit();
        }

        $fetch = $this->db->prepare("
            SELECT t.id, t.customer_name, u.email
            FROM trips t
            JOIN users u ON u.ref_id = t.user_id
            WHERE t.id = :id AND t.refund_requested_at IS NOT NULL
        ");
        $fetch->execute([':id' => $tripId]);
        $trip = $fetch->fetch(PDO::FETCH_ASSOC);

        if (!$trip) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Trip not found or no refund requested']);
            exit();
        }

        $stmt = $this->db->prepare("
            UPDATE trips
            SET refund_rejected_at = NOW(),
                refund_reject_note = :note,
                updated_at         = NOW()
            WHERE id = :id
            AND refund_requested_at IS NOT NULL
            AND refund_rejected_at IS NULL
        ");
        $stmt->execute([':note' => $rejectNote, ':id' => $tripId]);
        $affected = $stmt->rowCount();

        if ($affected > 0) {
            $this->sendRefundRejectionEmail(
                $trip['email'],
                $trip['customer_name'],
                $tripId,
                $rejectNote,
                'trip'
            );
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $affected > 0,
            'message' => $affected > 0 ? 'Refund rejected and customer notified.' : 'Nothing updated',
        ]);
        exit();
    }

    // ── Email: ask customer for bank details after approval ──────────────────
    private function sendRefundBankDetailsRequest(
        string $toEmail,
        string $toName,
        int    $bookingId,
        float  $amount,
        string $type   // 'package' or 'trip'
    ): void {
        $subject = "Your CeylonGo Refund Has Been Approved – Bank Details Required";
        $typeLabel = $type === 'package' ? 'Package Booking' : 'Custom Trip';
        $amountFormatted = 'LKR ' . number_format($amount, 2);

        $body = "Dear {$toName},\n\n"
            . "We are pleased to inform you that your refund request for {$typeLabel} #{$bookingId} "
            . "({$amountFormatted}) has been approved.\n\n"
            . "To process your refund via bank transfer, please reply to this email with the following details:\n\n"
            . "  1. Bank Name\n"
            . "  2. Branch Name\n"
            . "  3. Account Holder Name\n"
            . "  4. Account Number\n\n"
            . "Once we receive your details, your refund will be processed within 3–5 business days.\n\n"
            . "If you have any questions, please don't hesitate to contact us.\n\n"
            . "Best regards,\n"
            . "Ceylon Go Support Team\n"
            . "support@ceylongo.com";

        $headers = "From: Ceylon Go <noreply@ceylongo.com>\r\n"
                . "Reply-To: support@ceylongo.com\r\n"
                . "Content-Type: text/plain; charset=UTF-8\r\n";

        // Basic PHP mail — swap for PHPMailer/SendGrid in production (see note below)
        @mail($toEmail, $subject, $body, $headers);
    }

    // ── Email: notify customer their refund was rejected ─────────────────────
    private function sendRefundRejectionEmail(
        string $toEmail,
        string $toName,
        int    $bookingId,
        string $rejectNote,
        string $type
    ): void {
        $subject   = "Update on Your CeylonGo Refund Request";
        $typeLabel = $type === 'package' ? 'Package Booking' : 'Custom Trip';

        $body = "Dear {$toName},\n\n"
            . "Thank you for contacting us regarding your refund request for {$typeLabel} #{$bookingId}.\n\n"
            . "After reviewing your request, we regret to inform you that we are unable to process "
            . "the refund at this time for the following reason:\n\n"
            . "  \"{$rejectNote}\"\n\n"
            . "If you believe this decision is incorrect or would like to discuss further, please "
            . "reply to this email or contact our support team.\n\n"
            . "We apologise for any inconvenience caused.\n\n"
            . "Best regards,\n"
            . "Ceylon Go Support Team\n"
            . "support@ceylongo.com";

        $headers = "From: Ceylon Go <noreply@ceylongo.com>\r\n"
                . "Reply-To: support@ceylongo.com\r\n"
                . "Content-Type: text/plain; charset=UTF-8\r\n";

        @mail($toEmail, $subject, $body, $headers);
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
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid review ID']);
            exit();
        }

        // FIX: removed approved_at — that column does not exist in the reviews table
        $stmt = $this->db->prepare(
            "UPDATE reviews SET status = 'approved' WHERE id = :id"
        );
        $success = $stmt->execute([':id' => $reviewId]);
        $affected = $stmt->rowCount();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success && $affected > 0,
            'message' => $affected > 0 ? 'Approved' : 'No rows updated — check the ID',
        ]);
        exit();
    }

    public function inquiries()
    {
        $inquiryModel = new Inquiry($this->db);
 
        $status = $_GET['status'] ?? 'all';
        $search = trim($_GET['search'] ?? '');
 
        $inquiries = $inquiryModel->getAllInquiries($status, $search);
        $stats     = $inquiryModel->getInquiryStats();
 
        view('admin/inquiries', [
            'inquiries'      => $inquiries,
            'selectedStatus' => $status,
            'search'         => $search,
            'stats'          => $stats,
        ]);
    }
 
    public function deleteInquiry()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }
 
        $inquiryId    = intval($_POST['inquiry_id'] ?? 0);
        $inquiryModel = new Inquiry($this->db);
        $success      = $inquiryModel->deleteInquiry($inquiryId);
 
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit();
    }
 
    public function replyToInquiry()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }
 
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            exit();
        }
 
        $inquiryId = intval($_POST['inquiry_id'] ?? 0);
        $reply     = trim($_POST['reply'] ?? '');
 
        if ($inquiryId === 0 || $reply === '') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false]);
            exit();
        }
 
        $inquiryModel = new Inquiry($this->db);
        $success      = $inquiryModel->saveAdminReply($inquiryId, $reply);
 
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit();
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
                g.is_active,
                u.created_at AS registered_at
            FROM users u
            JOIN guide_users g ON u.ref_id = g.id
            WHERE u.role = 'guide' $whereStatus

            UNION ALL

            SELECT t.user_id AS id,
                t.full_name AS provider_name,
                u.email,
                u.role,
                t.is_active,
                u.created_at AS registered_at
            FROM users u
            JOIN transport_users t ON u.ref_id = t.user_id
            WHERE u.role = 'transport' $whereStatus

            UNION ALL

            SELECT h.id AS id,
                h.hotel_name AS provider_name,
                u.email,
                u.role,
                h.is_active,
                u.created_at AS registered_at
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

