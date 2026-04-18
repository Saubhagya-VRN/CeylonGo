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
 
        view('admin/dashboard');
    }

    public function profile() {
        $this->requireAdmin();
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
        $this->requireAdmin();
        $data = $_POST;
        $admin_id = $_SESSION['user_ref_id'] ?? null;

        if (!$admin_id) {
            $_SESSION['error'] = 'Invalid session. Please log in again.';
            header('Location: /CeylonGo/public/login');
            exit();
        }

        $errors = Validation::adminProfileErrors([
            'username' => $data['username'] ?? '',
            'email'      => $data['email'] ?? '',
            'phone'      => $data['phone'] ?? '',
            'role'       => $data['role'] ?? '',
            'password'   => $data['password'] ?? '',
        ]);
        if ($errors) {
            $_SESSION['error'] = implode('<br>', $errors);
            header('Location: /CeylonGo/public/admin/profile');
            exit();
        }

        $admin = new Admin($this->db);
        $admin->id = $admin_id;
        $admin->username = trim($data['username'] ?? '');
        $admin->email = trim($data['email'] ?? '');
        $admin->phone_number = trim($data['phone'] ?? '');
        $admin->role = trim($data['role'] ?? '');
        
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
            header('Location: /CeylonGo/public/admin/profile');
        } else {
            $_SESSION['error'] = 'Failed to update profile.';
            header('Location: /CeylonGo/public/admin/profile');
        }
        exit();
    }

    public function deleteProfile() {
        $this->requireAdmin();
        $admin_id = $_SESSION['user_ref_id'] ?? null;

        if (!$admin_id) {
            header('Location: /CeylonGo/public/login');
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
            $_SESSION['error'] = 'Failed to delete profile.';
            header('Location: /CeylonGo/public/admin/profile');
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
        if (!in_array($status, [0, 1], true)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false]);
            exit();
        }

        $userModel = new User($this->db); // ✅ Use User class
        $success = $userModel->updateStatus($userId, $status);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit();
    }

    public function users() {
        $this->requireAdmin();
        $userModel = new User($this->db); 

        // Handle POST for editing user
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
            $errors = Validation::touristAdminEditErrors([
                'user_id'    => $_POST['user_id'] ?? 0,
                'first_name' => $_POST['first_name'] ?? '',
                'last_name'  => $_POST['last_name'] ?? '',
                'contact'    => $_POST['contact'] ?? '',
            ]);
            if ($errors) {
                $_SESSION['error'] = implode('<br>', $errors);
                header('Location: /CeylonGo/public/admin/users');
                exit();
            }

            $userId = (int) $_POST['user_id'];
            $firstName = trim($_POST['first_name']);
            $lastName = trim($_POST['last_name']);
            $contact = trim($_POST['contact']);

            $success = $userModel->updateUserByAdmin($userId, $firstName, $lastName, $contact);

            if ($success) {
                $_SESSION['success'] = 'User updated successfully!';
            } else {
                $_SESSION['error'] = 'Failed to update user.';
            }

            header('Location: /CeylonGo/public/admin/users');
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
 
        $pkgStats = [
            'total'     => 0,
            'approved'  => 0,
            'pending'   => 0,
            'paid'      => 0,
            'cancelled' => 0,
        ];
        foreach ($packageBookings as $pb) {
            $pkgStats['total']++;
            $s = strtolower((string) ($pb['status'] ?? ''));
            if (isset($pkgStats[$s])) {
                $pkgStats[$s]++;
            }
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
        // Package bookings — payment_status_key matches admin/payments getPaymentDisplay()
        $stmt = $this->db->prepare("SELECT * FROM package_bookings ORDER BY created_at DESC");
        $stmt->execute();
        $rawPackageRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $payments = array_map(function ($p) {
            $p['payment_status_key'] = self::packagePaymentStatusKey($p);

            return $p;
        }, $rawPackageRows);

        // Package stats — Payment Status column (awaiting / received / rejected)
        $payStats = [
            'total'     => 0,
            'awaiting'  => 0,
            'received'  => 0,
            'rejected'  => 0,
        ];
        foreach ($payments as $p) {
            $payStats['total']++;
            $k = $p['payment_status_key'] ?? self::packagePaymentStatusKey($p);
            if ($k === 'awaiting') {
                $payStats['awaiting']++;
            } elseif ($k === 'received') {
                $payStats['received']++;
            } elseif ($k === 'rejected') {
                $payStats['rejected']++;
            }
        }
 
        // Trip payments — join tourist_users for email
        $tripStmt = $this->db->prepare("
            SELECT t.*,
                   tu.first_name AS customer_name,
                   tu.email      AS customer_email
            FROM trips t
            LEFT JOIN tourist_users tu ON tu.id = t.user_id
            ORDER BY t.created_at DESC
        ");
        $tripStmt->execute();
        $rawTrips = $tripStmt->fetchAll(PDO::FETCH_ASSOC);
 
        // Normalise trip rows to match what the view expects
        $tripPayments = array_map(function($t) {
            $row = [
                'id'                         => $t['id'],
                'user_id'                    => $t['user_id'],
                'customer_name'              => $t['customer_name'] ?? ($t['fullname'] ?? 'Unknown'),
                'destination'                => $t['destination']   ?? ($t['area'] ?? '—'),
                'budget_lkr'                 => $t['budget_lkr']    ?? null,
                'status'                     => $t['status'],
                'payhere_payment_id'         => $t['payhere_payment_id'] ?? null,
                'paid_at'                    => $t['paid_at'] ?? null,
                'bank_transfer_submitted_at' => $t['bank_transfer_submitted_at'] ?? null,
                'bank_transfer_slip_path'    => $t['bank_transfer_slip_path'] ?? null,
                'refund_requested_at'        => $t['refund_requested_at'] ?? null,
                'refund_approved_at'         => $t['refund_approved_at'] ?? null,
                'refund_rejected_at'         => $t['refund_rejected_at'] ?? null,
                'refund_reject_note'         => $t['refund_reject_note'] ?? null,
                'refund_reason'              => $t['refund_reason'] ?? null,
                'created_at'                 => $t['created_at'],
            ];
            $row['payment_status_key'] = self::tripPaymentStatusKey($row);
            return $row;
        }, $rawTrips);
 
        // Trip stats — aligned with Payment Status column (awaiting / received)
        $tripPayStats = [
            'total'    => 0,
            'awaiting' => 0,
            'received' => 0,
        ];
        foreach ($tripPayments as $t) {
            $tripPayStats['total']++;
            $k = $t['payment_status_key'] ?? self::tripPaymentStatusKey($t);
            if ($k === 'awaiting') {
                $tripPayStats['awaiting']++;
            }
            if ($k === 'received') {
                $tripPayStats['received']++;
            }
        }
 
        view('admin/payments', [
            'payments'      => $payments,
            'payStats'      => $payStats,
            'tripPayments'  => $tripPayments,
            'tripPayStats'  => $tripPayStats,
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

    public function approveTripSlipPayment() {
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
 
        $stmt = $this->db->prepare("
            UPDATE trips
            SET status     = 'completed',
                paid_at    = NOW(),
                updated_at = NOW()
            WHERE id       = :id
              AND bank_transfer_slip_path IS NOT NULL
              AND bank_transfer_slip_path != ''
              AND status = 'pending'
              AND paid_at IS NULL
        ");
        $stmt->execute([':id' => $tripId]);
        $affected = $stmt->rowCount();
 
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $affected > 0,
            'message' => $affected > 0 ? 'Trip payment approved' : 'Nothing updated — check slip exists and status is pending',
        ]);
        exit();
    }

    public function rejectSlipPayment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(); }
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $bookingId   = intval($_POST['booking_id'] ?? 0);
        $rejectNote  = trim($_POST['reject_note']  ?? '');

        if (!$bookingId || !$rejectNote) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing booking ID or rejection reason']);
            exit();
        }

        $fetch = $this->db->prepare("
            SELECT id, fullname, email, bank_transfer_slip_path
            FROM package_bookings
            WHERE id = :id
              AND bank_transfer_slip_path IS NOT NULL
              AND bank_transfer_slip_path != ''
              AND status IN ('pending', 'approved')
              AND paid_at IS NULL
        ");
        $fetch->execute([':id' => $bookingId]);
        $row = $fetch->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Booking not found or slip cannot be rejected']);
            exit();
        }

        $stmt = $this->db->prepare("
            UPDATE package_bookings
            SET bank_transfer_slip_path    = NULL,
                bank_transfer_submitted_at = NULL,
                admin_notes = CONCAT(IFNULL(admin_notes, ''), ' | Bank slip rejected on ', NOW(), ': ', :note2),
                updated_at = NOW()
            WHERE id = :id
              AND bank_transfer_slip_path IS NOT NULL
              AND bank_transfer_slip_path != ''
              AND status IN ('pending', 'approved')
              AND paid_at IS NULL
        ");
        $stmt->execute([':note2' => $rejectNote, ':id' => $bookingId]);
        $affected = $stmt->rowCount();

        if ($affected > 0) {
            $this->removeBankSlipUploadFile($row['bank_transfer_slip_path']);
            if (!empty($row['email'])) {
                $this->sendBankSlipRejectionEmail(
                    $row['email'],
                    $row['fullname'],
                    $bookingId,
                    $rejectNote,
                    'package'
                );
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $affected > 0,
            'message' => $affected > 0
                ? 'Bank slip rejected. Customer has been notified; they can upload a new slip.'
                : 'Nothing updated',
        ]);
        exit();
    }

    public function rejectTripSlipPayment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(); }
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $tripId     = intval($_POST['trip_id']    ?? 0);
        $rejectNote = trim($_POST['reject_note']  ?? '');

        if (!$tripId || !$rejectNote) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing trip ID or rejection reason']);
            exit();
        }

        $fetch = $this->db->prepare("
            SELECT t.id, t.customer_name, t.bank_transfer_slip_path, tu.email
            FROM trips t
            JOIN tourist_users tu ON tu.id = t.user_id
            WHERE t.id = :id
              AND t.bank_transfer_slip_path IS NOT NULL
              AND t.bank_transfer_slip_path != ''
              AND t.status = 'pending'
              AND t.paid_at IS NULL
        ");
        $fetch->execute([':id' => $tripId]);
        $row = $fetch->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            $fetch2 = $this->db->prepare("
                SELECT t.id, t.customer_name, t.bank_transfer_slip_path, u.email AS email
                FROM trips t
                JOIN users u ON u.ref_id = t.user_id AND u.role = 'tourist'
                WHERE t.id = :id
                  AND t.bank_transfer_slip_path IS NOT NULL
                  AND t.bank_transfer_slip_path != ''
                  AND t.status = 'pending'
                  AND t.paid_at IS NULL
            ");
            $fetch2->execute([':id' => $tripId]);
            $row = $fetch2->fetch(PDO::FETCH_ASSOC);
        }

        if (!$row) {
            $fetch3 = $this->db->prepare("
                SELECT id, customer_name, bank_transfer_slip_path, NULL AS email
                FROM trips
                WHERE id = :id
                  AND bank_transfer_slip_path IS NOT NULL
                  AND bank_transfer_slip_path != ''
                  AND status = 'pending'
                  AND paid_at IS NULL
            ");
            $fetch3->execute([':id' => $tripId]);
            $row = $fetch3->fetch(PDO::FETCH_ASSOC);
        }

        if (!$row) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Trip not found or slip cannot be rejected']);
            exit();
        }

        $stmt = $this->db->prepare("
            UPDATE trips
            SET bank_transfer_slip_path    = NULL,
                bank_transfer_submitted_at = NULL,
                updated_at = NOW()
            WHERE id = :id
              AND bank_transfer_slip_path IS NOT NULL
              AND bank_transfer_slip_path != ''
              AND status = 'pending'
              AND paid_at IS NULL
        ");
        $stmt->execute([':id' => $tripId]);
        $affected = $stmt->rowCount();

        if ($affected > 0) {
            $this->removeBankSlipUploadFile($row['bank_transfer_slip_path']);
            if (!empty($row['email'])) {
                $this->sendBankSlipRejectionEmail(
                    $row['email'],
                    $row['customer_name'],
                    $tripId,
                    $rejectNote,
                    'trip'
                );
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $affected > 0,
            'message' => $affected > 0
                ? 'Bank slip rejected. Customer has been notified; they can upload a new slip.'
                : 'Nothing updated',
        ]);
        exit();
    }
 
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
 
        $stmt = $this->db->prepare("
            UPDATE package_bookings
            SET status             = 'cancelled',
                refund_approved_at = NOW(),
                admin_notes        = CONCAT(IFNULL(admin_notes, ''), ' | Refund approved by admin on ', NOW()),
                approved_by        = :admin_id,
                updated_at         = NOW()
            WHERE id = :id
              AND refund_requested_at IS NOT NULL
              AND refund_approved_at  IS NULL
        ");
        $stmt->execute([':admin_id' => $adminUserId, ':id' => $bookingId]);
        $affected = $stmt->rowCount();
 
        if ($affected > 0) {
            $this->sendRefundBankDetailsRequest(
                $booking['email'],
                $booking['fullname'],
                $bookingId,
                (float)$booking['total_amount'],
                'package'
            );
        }
 
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $affected > 0,
            'message' => $affected > 0
                ? 'Refund approved. Email sent to customer for bank details.'
                : 'Nothing updated — check refund was requested and not already processed',
        ]);
        exit();
    }

    public function rejectRefund() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(); }
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
 
        $bookingId   = intval($_POST['booking_id'] ?? 0);
        $rejectNote  = trim($_POST['reject_note']  ?? '');
        $adminUserId = $_SESSION['user_ref_id'] ?? null;
 
        if (!$bookingId || !$rejectNote) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing booking ID or rejection note']);
            exit();
        }
 
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
 
        $stmt = $this->db->prepare("
            UPDATE package_bookings
            SET refund_rejected_at = NOW(),
                refund_reject_note = :note,
                admin_notes        = CONCAT(IFNULL(admin_notes, ''), ' | Refund rejected by admin on ', NOW(), ': ', :note2),
                updated_at         = NOW()
            WHERE id = :id
              AND refund_requested_at IS NOT NULL
              AND refund_rejected_at  IS NULL
        ");
        $stmt->execute([
            ':note'  => $rejectNote,
            ':note2' => $rejectNote,
            ':id'    => $bookingId,
        ]);
        $affected = $stmt->rowCount();
 
        if ($affected > 0) {
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
 
        // Join users table to get the email
        $fetch = $this->db->prepare("
            SELECT t.id, t.customer_name, t.budget_lkr, t.refund_requested_at,
                   u.email
            FROM trips t
            JOIN users u ON u.ref_id = t.user_id AND u.role = 'tourist'
            WHERE t.id = :id AND t.refund_requested_at IS NOT NULL
        ");
        $fetch->execute([':id' => $tripId]);
        $trip = $fetch->fetch(PDO::FETCH_ASSOC);
 
        // Fallback: try tourist_users table if users join returns nothing
        if (!$trip) {
            $fetch2 = $this->db->prepare("
                SELECT t.id, t.customer_name, t.budget_lkr, t.refund_requested_at,
                       tu.email
                FROM trips t
                JOIN tourist_users tu ON tu.id = t.user_id
                WHERE t.id = :id AND t.refund_requested_at IS NOT NULL
            ");
            $fetch2->execute([':id' => $tripId]);
            $trip = $fetch2->fetch(PDO::FETCH_ASSOC);
        }
 
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
              AND refund_approved_at  IS NULL
        ");
        $stmt->execute([':id' => $tripId]);
        $affected = $stmt->rowCount();
 
        if ($affected > 0) {
            $this->sendRefundBankDetailsRequest(
                $trip['email'],
                $trip['customer_name'],
                $tripId,
                (float)($trip['budget_lkr'] ?? 0),
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

    public function rejectTripRefund() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(); }
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
 
        $tripId     = intval($_POST['trip_id']    ?? 0);
        $rejectNote = trim($_POST['reject_note']  ?? '');
 
        if (!$tripId || !$rejectNote) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing trip ID or rejection note']);
            exit();
        }
 
        $fetch = $this->db->prepare("
            SELECT t.id, t.customer_name, u.email
            FROM trips t
            JOIN users u ON u.ref_id = t.user_id AND u.role = 'tourist'
            WHERE t.id = :id AND t.refund_requested_at IS NOT NULL
        ");
        $fetch->execute([':id' => $tripId]);
        $trip = $fetch->fetch(PDO::FETCH_ASSOC);
 
        // Fallback
        if (!$trip) {
            $fetch2 = $this->db->prepare("
                SELECT t.id, t.customer_name, tu.email
                FROM trips t
                JOIN tourist_users tu ON tu.id = t.user_id
                WHERE t.id = :id AND t.refund_requested_at IS NOT NULL
            ");
            $fetch2->execute([':id' => $tripId]);
            $trip = $fetch2->fetch(PDO::FETCH_ASSOC);
        }
 
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
              AND refund_rejected_at  IS NULL
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

    private function removeBankSlipUploadFile(?string $relativePath): void
    {
        if ($relativePath === null || $relativePath === '') {
            return;
        }
        $safe = basename(str_replace(['\\', '..'], '', $relativePath));
        if ($safe === '') {
            return;
        }
        $full = dirname(__DIR__) . '/public/uploads/' . $safe;
        if (is_file($full)) {
            @unlink($full);
        }
    }

    private function createMailer() {
        require_once dirname(__DIR__) . '/lib/PHPMailer/Exception.php';
        require_once dirname(__DIR__) . '/lib/PHPMailer/PHPMailer.php';
        require_once dirname(__DIR__) . '/lib/PHPMailer/SMTP.php';

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'vinudilanya16@gmail.com';  // ← YOUR Gmail address
        $mail->Password   = 'fcmm ooea bqkz wmce';  // ← YOUR 16-char App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        $mail->setFrom('vinudilanya16@gmail.com', 'Ceylon Go');
        return $mail;
    }

    private function sendRefundBankDetailsRequest(
        string $toEmail, string $toName, int $bookingId, float $amount, string $type
    ): void {
        $typeLabel       = $type === 'package' ? 'Package Booking' : 'Custom Trip';
        $amountFormatted = 'LKR ' . number_format($amount, 2);

        $htmlBody = '
        <div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;color:#222;">
            <h2 style="color:#198754;">&#10003; Refund Approved</h2>
            <p>Dear ' . htmlspecialchars($toName) . ',</p>
            <p>Your refund request for <strong>' . htmlspecialchars($typeLabel) . ' #' . $bookingId . '</strong>
            (<strong>' . htmlspecialchars($amountFormatted) . '</strong>) has been approved.</p>
            <p>Please reply to this email with your bank details:</p>
            <ol>
                <li>Bank Name</li>
                <li>Branch Name</li>
                <li>Account Holder Name</li>
                <li>Account Number</li>
            </ol>
            <p>Your refund will be processed within <strong>3–5 business days</strong>.</p>
            <p>Contact us at <a href="mailto:support@ceylongo.com">support@ceylongo.com</a> for questions.</p>
            <p style="margin-top:32px;color:#555;">Best regards,<br><strong>Ceylon Go Support Team</strong></p>
        </div>';

        try {
            $mail = $this->createMailer();
            $mail->addAddress($toEmail, $toName);
            $mail->Subject = 'Your CeylonGo Refund Has Been Approved – Bank Details Required';
            $mail->isHTML(true);
            $mail->Body    = $htmlBody;
            $mail->AltBody = "Dear {$toName}, your refund for {$typeLabel} #{$bookingId} ({$amountFormatted}) has been approved. Please reply with your bank details: 1.Bank Name 2.Branch Name 3.Account Holder Name 4.Account Number";
            $mail->send();
        } catch (\Exception $e) {
            error_log('[CeylonGo] sendRefundBankDetailsRequest failed: ' . $e->getMessage());
        }
    }

    private function sendRefundRejectionEmail(
        string $toEmail, string $toName, int $bookingId, string $rejectNote, string $type
    ): void {
        $typeLabel = $type === 'package' ? 'Package Booking' : 'Custom Trip';

        $htmlBody = '
        <div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;color:#222;">
            <h2 style="color:#c0392b;">&#10007; Refund Request Update</h2>
            <p>Dear ' . htmlspecialchars($toName) . ',</p>
            <p>Your refund request for <strong>' . htmlspecialchars($typeLabel) . ' #' . $bookingId . '</strong>
            has been reviewed and cannot be approved at this time.</p>
            <table style="width:100%;border-collapse:collapse;margin:16px 0;">
                <tr>
                    <td style="padding:10px 14px;font-weight:bold;background:#fdf2f2;border-left:4px solid #c0392b;width:30%;">Reason</td>
                    <td style="padding:10px 14px;background:#fdf2f2;">' . nl2br(htmlspecialchars($rejectNote)) . '</td>
                </tr>
            </table>
            <p>Contact us at <a href="mailto:support@ceylongo.com">support@ceylongo.com</a> if you have questions.</p>
            <p style="margin-top:32px;color:#555;">Best regards,<br><strong>Ceylon Go Support Team</strong></p>
        </div>';

        try {
            $mail = $this->createMailer();
            $mail->addAddress($toEmail, $toName);
            $mail->Subject = 'Update on Your CeylonGo Refund Request';
            $mail->isHTML(true);
            $mail->Body    = $htmlBody;
            $mail->AltBody = "Dear {$toName}, your refund for {$typeLabel} #{$bookingId} was not approved. Reason: {$rejectNote}";
            $mail->send();
        } catch (\Exception $e) {
            error_log('[CeylonGo] sendRefundRejectionEmail failed: ' . $e->getMessage());
        }
    }

    private function sendBankSlipRejectionEmail(
        string $toEmail, string $toName, int $refId, string $rejectNote, string $type
    ): void {
        $typeLabel = $type === 'package' ? 'Package Booking' : 'Custom Trip';

        $htmlBody = '
        <div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;color:#222;">
            <h2 style="color:#c0392b;">&#10007; Bank Slip Could Not Be Verified</h2>
            <p>Dear ' . htmlspecialchars($toName) . ',</p>
            <p>We were unable to verify your bank transfer slip for
            <strong>' . htmlspecialchars($typeLabel) . ' #' . $refId . '</strong>.</p>
            <table style="width:100%;border-collapse:collapse;margin:16px 0;">
                <tr>
                    <td style="padding:10px 14px;font-weight:bold;background:#fdf2f2;border-left:4px solid #c0392b;width:30%;">Reason</td>
                    <td style="padding:10px 14px;background:#fdf2f2;">' . nl2br(htmlspecialchars($rejectNote)) . '</td>
                </tr>
            </table>
            <p>Please log in to your CeylonGo account and upload a new slip.</p>
            <p>Contact us at <a href="mailto:support@ceylongo.com">support@ceylongo.com</a> for help.</p>
            <p style="margin-top:32px;color:#555;">Best regards,<br><strong>Ceylon Go Support Team</strong></p>
        </div>';

        try {
            $mail = $this->createMailer();
            $mail->addAddress($toEmail, $toName);
            $mail->Subject = 'Update on Your CeylonGo Bank Transfer Slip';
            $mail->isHTML(true);
            $mail->Body    = $htmlBody;
            $mail->AltBody = "Dear {$toName}, your bank slip for {$typeLabel} #{$refId} could not be verified. Reason: {$rejectNote}. Please log in and upload a new slip.";
            $mail->send();
        } catch (\Exception $e) {
            error_log('[CeylonGo] sendBankSlipRejectionEmail failed: ' . $e->getMessage());
        }
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

    /**
     * Download inquiries as PDF table (same filters as inquiry list: status, optional search).
     */
    public function exportInquiriesPdf(): void
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit('Forbidden');
        }

        $this->loadComposerAutoload();
        if (!class_exists(\Dompdf\Dompdf::class)) {
            http_response_code(500);
            echo 'PDF export requires Composer dependencies. Run: composer install';
            exit;
        }

        $status = $_GET['status'] ?? 'all';
        if (!in_array($status, ['all', 'pending', 'replied'], true)) {
            $status = 'all';
        }
        $search = trim((string) ($_GET['search'] ?? ''));

        $inquiryModel = new Inquiry($this->db);
        $rows = $inquiryModel->getAllInquiries($status, $search);

        $e = static function (?string $s): string {
            return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        };

        $tbody = '';
        foreach ($rows as $r) {
            $customer = trim((string) ($r['tourist_name'] ?? ''));
            if ($customer === '' && !empty($r['guest_name'])) {
                $customer = trim((string) $r['guest_name']) . ' <' . trim((string) ($r['guest_email'] ?? '')) . '>';
            }
            if ($customer === '') {
                $customer = 'Unknown';
            }
            $dateStr = !empty($r['created_at']) ? date('Y-m-d', strtotime($r['created_at'])) : '';
            $tbody .= '<tr>'
                . '<td>' . $e((string) ($r['id'] ?? '')) . '</td>'
                . '<td>' . $e($customer) . '</td>'
                . '<td>' . $e((string) ($r['subject'] ?? '')) . '</td>'
                . '<td class="c-long">' . $e((string) ($r['message'] ?? '')) . '</td>'
                . '<td>' . $e((string) ($r['status'] ?? '')) . '</td>'
                . '<td>' . $e($dateStr) . '</td>'
                . '<td class="c-long">' . $e((string) ($r['admin_reply'] ?? '')) . '</td>'
                . '</tr>';
        }

        $filterMeta = 'Status: ' . $e($status);
        if ($search !== '') {
            $filterMeta .= ' &mdash; Search: ' . $e($search);
        }

        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
            body{font-family:DejaVu Sans,sans-serif;font-size:9px;color:#222;}
            h1{font-size:15px;margin:0 0 8px;}
            .meta{color:#666;font-size:9px;margin-bottom:10px;line-height:1.4;}
            table.data{border-collapse:collapse;width:100%;}
            table.data th,table.data td{border:1px solid #ccc;padding:5px 6px;text-align:left;vertical-align:top;}
            table.data th{background:#f0f0f0;}
            table.data tr:nth-child(even){background:#fafafa;}
            .c-long{max-width:200px;word-wrap:break-word;}
        </style></head><body>
            <h1>Inquiries report</h1>
            <div class="meta">' . $filterMeta . '<br>Generated: ' . $e(date('Y-m-d H:i')) . ' &mdash; Rows: ' . count($rows) . '</div>
            <table class="data"><thead><tr>
                <th>ID</th><th>Customer</th><th>Subject</th><th>Message</th><th>Status</th><th>Date</th><th>Admin Reply</th>
            </tr></thead><tbody>' . $tbody . '</tbody></table>
        </body></html>';

        $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('inquiries_' . date('Y-m-d_His') . '.pdf', ['Attachment' => true]);
        exit;
    }

    /**
     * Download reviews as PDF table (same filter as reviews list: rating).
     */
    public function exportReviewsPdf(): void
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit('Forbidden');
        }

        $this->loadComposerAutoload();
        if (!class_exists(\Dompdf\Dompdf::class)) {
            http_response_code(500);
            echo 'PDF export requires Composer dependencies. Run: composer install';
            exit;
        }

        $rating = $_GET['rating'] ?? 'all';
        if ($rating !== 'all' && (!ctype_digit((string) $rating) || (int) $rating < 1 || (int) $rating > 5)) {
            $rating = 'all';
        }

        $reviewModel = new Review($this->db);
        $rows = $reviewModel->getAllReviews($rating);

        $e = static function (?string $s): string {
            return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        };

        $tbody = '';
        foreach ($rows as $r) {
            $dateStr = !empty($r['created_at']) ? date('Y-m-d', strtotime($r['created_at'])) : '';
            $tbody .= '<tr>'
                . '<td>' . $e((string) ($r['id'] ?? '')) . '</td>'
                . '<td>' . $e((string) ($r['user_id'] ?? '')) . '</td>'
                . '<td>' . $e((string) ($r['tourist_name'] ?? '')) . '</td>'
                . '<td class="c-long">' . $e((string) ($r['review_text'] ?? '')) . '</td>'
                . '<td>' . $e((string) ($r['rating'] ?? '')) . '</td>'
                . '<td>' . $e((string) ($r['status'] ?? '')) . '</td>'
                . '<td>' . $e($dateStr) . '</td>'
                . '<td class="c-long">' . $e((string) ($r['admin_reply'] ?? '')) . '</td>'
                . '</tr>';
        }

        $ratingLabel = $rating === 'all' ? 'All ratings' : ($rating . ' star(s)');
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
            body{font-family:DejaVu Sans,sans-serif;font-size:9px;color:#222;}
            h1{font-size:15px;margin:0 0 8px;}
            .meta{color:#666;font-size:9px;margin-bottom:10px;line-height:1.4;}
            table.data{border-collapse:collapse;width:100%;}
            table.data th,table.data td{border:1px solid #ccc;padding:5px 6px;text-align:left;vertical-align:top;}
            table.data th{background:#f0f0f0;}
            table.data tr:nth-child(even){background:#fafafa;}
            .c-long{max-width:200px;word-wrap:break-word;}
        </style></head><body>
            <h1>Reviews report</h1>
            <div class="meta">Filter: ' . $e($ratingLabel) . '<br>Generated: ' . $e(date('Y-m-d H:i')) . ' &mdash; Rows: ' . count($rows) . '</div>
            <table class="data"><thead><tr>
                <th>Review ID</th><th>User ID</th><th>User Name</th><th>Comment</th><th>Rating</th><th>Status</th><th>Date</th><th>Admin Reply</th>
            </tr></thead><tbody>' . $tbody . '</tbody></table>
        </body></html>';

        $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('reviews_' . date('Y-m-d_His') . '.pdf', ['Attachment' => true]);
        exit;
    }

    /**
     * Tour packages catalog — PDF table (same records as Manage Packages).
     */
    public function exportPackagesPdf(): void
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit('Forbidden');
        }

        $this->loadComposerAutoload();
        if (!class_exists(\Dompdf\Dompdf::class)) {
            http_response_code(500);
            echo 'PDF export requires Composer dependencies. Run: composer install';
            exit;
        }

        $packageModel = new Package($this->db);
        $rows = $packageModel->getAll();

        $e = static function (?string $s): string {
            return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        };

        $tbody = '';
        foreach ($rows as $p) {
            $dur = $p['duration_short'] ?? $p['duration'] ?? '—';
            $priceStr = isset($p['price']) ? number_format((int) $p['price']) : '0';
            $ratingStr = isset($p['rating']) && $p['rating'] !== null ? number_format((float) $p['rating'], 1) : '—';
            $trend = !empty($p['trending']) ? 'Yes' : 'No';
            $revCount = (int) ($p['reviews'] ?? 0);
            $created = !empty($p['created_at']) ? date('Y-m-d', strtotime($p['created_at'])) : '';
            $hasImage = !empty($p['image']) ? 'Yes' : 'No';
            $tbody .= '<tr>'
                . '<td>' . $e((string) ($p['id'] ?? '')) . '</td>'
                . '<td>' . $e((string) ($p['title'] ?? '')) . '</td>'
                . '<td>' . $e((string) ($p['location'] ?? '')) . '</td>'
                . '<td>' . $e((string) ($p['category'] ?? '')) . '</td>'
                . '<td>' . $e((string) $dur) . '</td>'
                . '<td style="text-align:right;">' . $e($priceStr) . '</td>'
                . '<td>' . $e($ratingStr) . '</td>'
                . '<td>' . $e((string) $revCount) . '</td>'
                . '<td>' . $e($trend) . '</td>'
                . '<td>' . $e($hasImage) . '</td>'
                . '<td>' . $e($created) . '</td>'
                . '</tr>';
        }

        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
            body{font-family:DejaVu Sans,sans-serif;font-size:8px;color:#222;}
            h1{font-size:14px;margin:0 0 8px;}
            .meta{color:#666;font-size:9px;margin-bottom:10px;line-height:1.4;}
            table.data{border-collapse:collapse;width:100%;}
            table.data th,table.data td{border:1px solid #ccc;padding:4px 5px;text-align:left;vertical-align:top;}
            table.data th{background:#f0f0f0;font-size:8px;}
            table.data tr:nth-child(even){background:#fafafa;}
            .t-title{max-width:120px;word-wrap:break-word;}
        </style></head><body>
            <h1>Tour packages catalog</h1>
            <div class="meta">Generated: ' . $e(date('Y-m-d H:i')) . ' &mdash; Packages: ' . count($rows) . '</div>
            <table class="data"><thead><tr>
                <th>ID</th><th>Title</th><th>Location</th><th>Category</th><th>Duration</th><th>Price (LKR)</th><th>Rating</th><th>Reviews</th><th>Trending</th><th>Image</th><th>Created</th>
            </tr></thead><tbody>' . $tbody . '</tbody></table>
        </body></html>';

        $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('packages_' . date('Y-m-d_His') . '.pdf', ['Attachment' => true]);
        exit;
    }

    private function loadComposerAutoload(): void
    {
        $path = dirname(__DIR__) . '/vendor/autoload.php';
        if (is_readable($path)) {
            require_once $path;
        }
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

    private function requireAdmin(): void {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            header('Location: /CeylonGo/public/login');
            exit();
        }
    }

    public function packages() {
        $this->requireAdmin();
        $packageModel = new Package($this->db);
        $packages = $packageModel->getAll();

        $success = $_SESSION['pkg_success'] ?? null;
        $error   = $_SESSION['pkg_error']   ?? null;
        unset($_SESSION['pkg_success'], $_SESSION['pkg_error']);
        view('admin/packages', compact('packages', 'success', 'error'));
    }

    public function packageNew() {
        $this->requireAdmin();
        $error   = $_SESSION['pkg_error'] ?? null;
        $package = $_SESSION['pkg_old']   ?? null;
        unset($_SESSION['pkg_error'], $_SESSION['pkg_old']);
        $mode = 'create';
        self::loadPackageForm(compact('mode', 'package', 'error'));
    }

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

    private static function loadPackageForm(array $data) {
        extract($data);
        $file = dirname(__DIR__) . '/views/admin/package_form.php';
        if (file_exists($file)) {
            require $file;
        } else {
            // Safety fallback — shows the real path so you can debug
            die('package_form.php not found. Expected at: ' . $file);
        }
    }
    
    // ─── Private helpers ─────────────────────────────────────
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
        if (isset($data['rating'])  && $data['rating']  !== null) {
            if (!is_numeric($data['rating'])) {
                $errors[] = 'Rating must be a number.';
            } else {
                $r = (float) $data['rating'];
                if ($r < 0 || $r > 5) {
                    $errors[] = 'Rating must be between 0 and 5.';
                }
            }
        }
        if (isset($data['reviews']) && $data['reviews'] !== null) {
            if (!is_numeric($data['reviews'])) {
                $errors[] = 'Review count must be a number.';
            } elseif ((int) $data['reviews'] < 0) {
                $errors[] = 'Review count cannot be negative.';
            }
        }
        $childRatio = $data['price_child_ratio'] ?? 0;
        $infantRatio = $data['price_infant_ratio'] ?? 0;
        if (!is_numeric($childRatio) || (float) $childRatio < 0 || (float) $childRatio > 1) {
            $errors[] = 'Child price ratio must be between 0 and 1.';
        }
        if (!is_numeric($infantRatio) || (float) $infantRatio < 0 || (float) $infantRatio > 1) {
            $errors[] = 'Infant price ratio must be between 0 and 1.';
        }

        return $errors;
    }

    /**
     * Split a textarea value into an array of non-empty trimmed lines.
     */
    private function parseLines(string $text): array {
        $lines = explode("\n", str_replace("\r", '', $text));
        return array_values(array_filter(array_map('trim', $lines)));
    }

    /**
     * Payment Status column for customized (trip) bookings — matches admin/payments getPaymentDisplay().
     * Keys: awaiting, received, refunded, none (booking cancelled/rejected, no payment to show).
     */
    private static function tripPaymentStatusKey(array $row): string {
        if (!empty($row['refund_approved_at'])) {
            return 'refunded';
        }
        if (!empty($row['paid_at'])) {
            return 'received';
        }
        if (!empty($row['bank_transfer_submitted_at'])) {
            return 'awaiting';
        }
        $status = strtolower((string) ($row['status'] ?? ''));
        if (in_array($status, ['cancelled', 'rejected'], true)) {
            return 'none';
        }
        return 'awaiting';
    }

    /**
     * Payment Status column for package bookings — matches admin/payments getPaymentDisplay().
     * Keys: awaiting, received, rejected (refund approved / refunded), none (no payment row).
     */
    private static function packagePaymentStatusKey(array $row): string {
        if (!empty($row['refund_approved_at'])) {
            return 'rejected';
        }
        if (!empty($row['paid_at'])) {
            return 'received';
        }
        if (!empty($row['bank_transfer_submitted_at'])) {
            return 'awaiting';
        }
        $status = strtolower((string) ($row['status'] ?? ''));
        if (in_array($status, ['cancelled', 'rejected'], true)) {
            return 'none';
        }
        return 'awaiting';
    }

}
?>

