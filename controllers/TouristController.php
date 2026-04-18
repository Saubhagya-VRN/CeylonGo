<?php
class TouristController {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    private function tripSubmissionUpsert($tripId, $userId, $tripArr) {
        $tripId = (int) $tripId;
        $userId = (int) $userId;
        if ($tripId <= 0 || $userId <= 0) return;
        if (!is_array($tripArr)) $tripArr = array();
        $paymentStatus = isset($tripArr['payment_status']) ? trim((string) $tripArr['payment_status']) : '';
        if ($paymentStatus === '') $paymentStatus = 'pending';
        $json = json_encode($tripArr, JSON_UNESCAPED_UNICODE);
        if ($json === false) $json = '{}';
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO trip_submissions (trip_id, user_id, trip_json, payment_status, created_at, updated_at)
                 VALUES (?, ?, ?, ?, NOW(), NOW())
                 ON DUPLICATE KEY UPDATE trip_json = VALUES(trip_json), payment_status = VALUES(payment_status), updated_at = NOW()'
            );
            $stmt->execute(array($tripId, $userId, $json, $paymentStatus));
        } catch (PDOException $e) {
            error_log('tripSubmissionUpsert: ' . $e->getMessage());
        }
    }

    private function tripSubmissionSetPaymentStatus($tripId, $userId, $status) {
        $tripId = (int) $tripId;
        $userId = (int) $userId;
        $status = trim((string) $status);
        if ($tripId <= 0 || $userId <= 0 || $status === '') return;
        try {
            $stmt = $this->db->prepare('SELECT trip_json FROM trip_submissions WHERE trip_id = ? AND user_id = ? LIMIT 1');
            $stmt->execute(array($tripId, $userId));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $arr = array();
            if ($row && isset($row['trip_json'])) {
                $dec = json_decode((string) $row['trip_json'], true);
                if (is_array($dec)) $arr = $dec;
            }
            $arr['payment_status'] = $status;
            $arr['payment_status_updated_at'] = date('c');
            $this->tripSubmissionUpsert($tripId, $userId, $arr);
        } catch (PDOException $e) {
            error_log('tripSubmissionSetPaymentStatus: ' . $e->getMessage());
        }
    }

    private function getTripBudgetFromSubmission($tripId, $userId) {
        $tripId = (int) $tripId;
        $userId = (int) $userId;
        if ($tripId <= 0 || $userId <= 0) return 0.0;
        try {
            $stmt = $this->db->prepare('SELECT trip_json FROM trip_submissions WHERE trip_id = ? AND user_id = ? ORDER BY id DESC LIMIT 1');
            $stmt->execute(array($tripId, $userId));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row || !isset($row['trip_json'])) return 0.0;
            $dec = json_decode((string) $row['trip_json'], true);
            if (!is_array($dec)) return 0.0;
            $b = isset($dec['budget_lkr']) ? (float) $dec['budget_lkr'] : 0.0;
            if ($b < 0 || $b > 999999999) return 0.0;
            return $b;
        } catch (\Throwable $e) {
            error_log('getTripBudgetFromSubmission: ' . $e->getMessage());
            return 0.0;
        }
    }

    public function registerView() {
        view('tourist/tourist_register');
    }

    public function register() {
        $data = $_POST;

        // Validation
        if (empty($data['fname']) || empty($data['lname']) || empty($data['contact']) || 
            empty($data['email']) || empty($data['password']) || empty($data['confirm_password'])) {
            die("<script>alert('Please fill in all fields.'); window.history.back();</script>");
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || !preg_match('/\.[a-zA-Z]{2,6}$/', $data['email'])) {
            die("<script>alert('Please enter a valid email address.'); window.history.back();</script>");
        }

        if (!preg_match('/^\d{7,15}$/', $data['contact'])) {
            die("<script>alert('Please enter a valid contact number.'); window.history.back();</script>");
        }

        if ($data['password'] !== $data['confirm_password']) {
            die("<script>alert('Passwords do not match.'); window.history.back();</script>");
        }

        // Prevent duplicate accounts (users table is the source of truth for login).
        try {
            $authChk = new AuthUser($this->db);
            $existing = $authChk->getUserByEmail(trim((string) $data['email']));
            if ($existing && !empty($existing['email'])) {
                die("<script>alert('This email is already registered. Please log in.'); window.history.back();</script>");
            }
        } catch (\Throwable $e) {
            // If lookup fails, continue and let the insert fail with a clearer message below.
        }

        // Create tourist
        $tourist = new Tourist($this->db);
        $tourist->first_name = trim($data['fname']);
        $tourist->last_name = trim($data['lname']);
        $tourist->contact_number = trim($data['contact']);
        $tourist->email = trim($data['email']);
        $tourist->password = password_hash($data['password'], PASSWORD_DEFAULT);

        try {
            // Ensure tourist_users + users stay in sync.
            if (method_exists($this->db, 'beginTransaction')) {
                $this->db->beginTransaction();
            }

            if (!$tourist->register()) {
                if (method_exists($this->db, 'rollBack')) {
                    $this->db->rollBack();
                }
                echo "<script>alert('Registration failed. Please try again.'); window.history.back();</script>";
                return;
            }

            // Add to users table
            $authUser = new AuthUser($this->db);
            $authUser->ref_id = $tourist->id;
            $authUser->email = $tourist->email;
            $authUser->password = $tourist->password;
            $authUser->role = 'tourist';
            $okUser = $authUser->addUser();

            if (!$okUser) {
                if (method_exists($this->db, 'rollBack')) {
                    $this->db->rollBack();
                }
                echo "<script>alert('Registration failed: could not create login user record.'); window.history.back();</script>";
                return;
            }

            if (method_exists($this->db, 'commit')) {
                $this->db->commit();
            }

            // Set session
            $_SESSION['user_id'] = $tourist->id;
            $_SESSION['user_role'] = 'tourist';
            $_SESSION['user_type'] = 'tourist';
            $_SESSION['user_email'] = $tourist->email;
            $_SESSION['user_name'] = $tourist->first_name . ' ' . $tourist->last_name;

            header("Location: /CeylonGo/public/tourist/dashboard");
            exit();
        } catch (\Throwable $e) {
            try {
                if (method_exists($this->db, 'rollBack')) {
                    $this->db->rollBack();
                }
            } catch (\Throwable $e2) {}
            error_log('tourist register: ' . $e->getMessage());
            echo "<script>alert('Registration failed. Please try again.'); window.history.back();</script>";
            return;
        }
    }

    public function newDashboard() {
        // Fetch tourist data if logged in
        $tourist_data = null;
        if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'tourist') {
            require_once dirname(__DIR__) . '/models/Tourist.php';
            $touristModel = new Tourist($this->db);
            $tourist_data = $touristModel->getTouristById($_SESSION['user_id']);
        }

        $packageModel = new Package($this->db);
        $trending_bar_packages = $packageModel->getAll(array('trending' => true));

        view('tourist/dashboard', array(
            'tourist_data' => $tourist_data,
            'trending_bar_packages' => $trending_bar_packages,
        ));
    }

    public function oldDashboard() {
        view('tourist/tourist_dashboard');
    }

    /**
     * Customise trip page (trip.php). Requires tourist login.
     */
    public function trip() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
            header('Location: /CeylonGo/public/tourist/dashboard');
            exit();
        }
        $touristModel = new Tourist($this->db);
        $hotel = new Hotel($this->db);
        $hotels = $hotel->GetAccommodationCatalog();
        $tourist_data = $touristModel->getTouristById($_SESSION['user_id']);
        $user_name = isset($_SESSION['user_name']) ? trim($_SESSION['user_name']) : '';
        if ($user_name === '' && $tourist_data) {
            $fn = isset($tourist_data['first_name']) ? $tourist_data['first_name'] : '';
            $ln = isset($tourist_data['last_name']) ? $tourist_data['last_name'] : '';
            $user_name = trim($fn . ' ' . $ln);
        }
        $placesAutocompleteUrl = (defined('BASE_URL') ? BASE_URL : '/CeylonGo/public') . '/api/places-autocomplete';
        $payhereMax = defined('PAYHERE_PER_TRANSACTION_MAX_LKR') ? (int) PAYHERE_PER_TRANSACTION_MAX_LKR : 0;
        $bankDetails = defined('BANK_TRANSFER_DETAILS') ? BANK_TRANSFER_DETAILS : '';
        $uidTripPage = (int) $_SESSION['user_id'];

        // "Clear data" / start over: drop session trip pointer, then reload once without auto-binding latest DB trip.
        if (isset($_GET['reset']) && (string) $_GET['reset'] === '1') {
            unset($_SESSION['last_trip_id']);
            $_SESSION['trip_wizard_fresh_after_reset'] = 1;
            $base = defined('BASE_URL') ? rtrim((string) BASE_URL, '/') : '/CeylonGo/public';
            header('Location: ' . $base . '/tourist/customize-trip');
            exit();
        }

        $wizard_fresh_start = false;
        $freshAfterReset = !empty($_SESSION['trip_wizard_fresh_after_reset']);
        if ($freshAfterReset) {
            unset($_SESSION['trip_wizard_fresh_after_reset']);
            $lastTripId = 0;
            $wizard_fresh_start = true;
        } else {
            $lastTripId = isset($_SESSION['last_trip_id']) ? (int) $_SESSION['last_trip_id'] : 0;
            if (isset($_GET['trip_id'])) {
                $g = (int) $_GET['trip_id'];
                if ($g > 0) {
                    try {
                        $stmt = $this->db->prepare('SELECT id FROM trips WHERE id = ? AND user_id = ? LIMIT 1');
                        $stmt->execute(array($g, $uidTripPage));
                        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                            $lastTripId = $g;
                            $_SESSION['last_trip_id'] = $g;
                        }
                    } catch (\Throwable $e) {
                        error_log('trip GET trip_id: ' . $e->getMessage());
                    }
                }
            }
            // Wizard + Trip Overview need a trip id even when session was cleared ΓÇö use latest row for this user.
            try {
                $stmt = $this->db->prepare('SELECT id FROM trips WHERE user_id = ? ORDER BY id DESC LIMIT 1');
                $stmt->execute(array($uidTripPage));
                $r = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($r && isset($r['id'])) {
                    $lastTripId = max($lastTripId, (int) $r['id']);
                }
            } catch (\Throwable $e) {
                error_log('trip latest id: ' . $e->getMessage());
            }
        }

        // Multi-trip: step 11 without trip_id opens the booking-status hub (picker), not the wizard with the latest trip id.
        if (!$wizard_fresh_start) {
            $gStep = isset($_GET['step']) ? (int) $_GET['step'] : 0;
            $gTid = isset($_GET['trip_id']) ? (int) $_GET['trip_id'] : 0;
            if ($gStep === 11 && $gTid <= 0) {
                $base = defined('BASE_URL') ? rtrim((string) BASE_URL, '/') : '/CeylonGo/public';
                header('Location: ' . $base . '/tourist/booking-status');
                exit();
            }
        }

        view('tourist/trip', array(
            'tourist_data' => $tourist_data,
            'user_name' => $user_name,
            'hotels' => $hotels,
            'places_autocomplete_url' => $placesAutocompleteUrl,
            'payhere_per_transaction_max_lkr' => $payhereMax,
            'bank_transfer_details' => $bankDetails,
            'last_trip_id' => $lastTripId,
            'wizard_fresh_start' => $wizard_fresh_start,
        ));
    }

    /**
     * Hub: list submitted customised trips that are not fully paid; each card links to that trip's wizard (step 11).
     */
    public function bookingStatusHub() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
            $base = defined('BASE_URL') ? rtrim((string) BASE_URL, '/') : '/CeylonGo/public';
            header('Location: ' . $base . '/tourist/dashboard-side');
            exit();
        }

        $uid = (int) $_SESSION['user_id'];
        $touristModel = new Tourist($this->db);
        $tourist_data = $touristModel->getTouristById($uid);
        $user_name = isset($_SESSION['user_name']) ? trim((string) $_SESSION['user_name']) : '';
        if ($user_name === '' && $tourist_data) {
            $fn = isset($tourist_data['first_name']) ? $tourist_data['first_name'] : '';
            $ln = isset($tourist_data['last_name']) ? $tourist_data['last_name'] : '';
            $user_name = trim($fn . ' ' . $ln);
        }
        $asset_base = defined('BASE_URL') ? rtrim((string) BASE_URL, '/') : '/CeylonGo/public';

        $rows = array();
        try {
            $sql = 'SELECT t.id, t.customer_name, t.destination, t.start_date, t.budget_lkr, t.status, '
                . 't.payhere_payment_id, t.paid_at, t.bank_transfer_submitted_at, t.created_at, t.number_of_people '
                . 'FROM trips t '
                . 'WHERE t.user_id = ? AND EXISTS (SELECT 1 FROM trip_submissions s WHERE s.trip_id = t.id AND s.user_id = t.user_id) '
                . 'ORDER BY t.id DESC';
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array($uid));
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            error_log('bookingStatusHub submissions join: ' . $e->getMessage());
            try {
                $sql = 'SELECT id, customer_name, destination, start_date, budget_lkr, status, '
                    . 'payhere_payment_id, paid_at, bank_transfer_submitted_at, created_at, number_of_people '
                    . 'FROM trips WHERE user_id = ? ORDER BY id DESC';
                $stmt = $this->db->prepare($sql);
                $stmt->execute(array($uid));
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Throwable $e2) {
                error_log('bookingStatusHub fallback: ' . $e2->getMessage());
                $rows = array();
            }
        }

        $pending_trips = array();
        foreach ($rows as $tr) {
            if ($this->isCustomTripFullyPaid($tr)) {
                continue;
            }
            $tid = (int) (isset($tr['id']) ? $tr['id'] : 0);
            if ($tid <= 0) {
                continue;
            }
            $hasBank = isset($tr['bank_transfer_submitted_at']) && trim((string) $tr['bank_transfer_submitted_at']) !== '';
            $status = isset($tr['status']) ? trim((string) $tr['status']) : '';
            $dest = isset($tr['destination']) ? trim((string) $tr['destination']) : '';
            $sd = isset($tr['start_date']) ? trim((string) $tr['start_date']) : '';
            $budget = isset($tr['budget_lkr']) ? (float) $tr['budget_lkr'] : 0.0;
            $pending_trips[] = array(
                'id' => $tid,
                'destination' => $dest !== '' ? $dest : ('Trip #' . $tid),
                'customer_name' => isset($tr['customer_name']) ? trim((string) $tr['customer_name']) : '',
                'start_date' => $sd,
                'budget_lkr' => $budget,
                'number_of_people' => isset($tr['number_of_people']) ? (int) $tr['number_of_people'] : 0,
                'has_bank_pending' => $hasBank,
                'trip_status' => $status,
            );
        }

        view('tourist/booking_status_hub', array(
            'tourist_data' => $tourist_data,
            'user_name' => $user_name,
            'asset_base' => $asset_base,
            'pending_trips' => $pending_trips,
        ));
    }

    /**
     * Read-only summary for a submitted customised trip (`trips` + optional `trip_submissions` snapshot).
     */
    public function customTripSummary() {
        $role = isset($_SESSION['user_role']) ? (string) $_SESSION['user_role'] : '';
        $type = isset($_SESSION['user_type']) ? (string) $_SESSION['user_type'] : '';
        $isTourist = ($role === 'tourist' || $type === 'tourist');
        if (!isset($_SESSION['user_id']) || !$isTourist) {
            header('Location: /CeylonGo/public/tourist/dashboard');
            exit;
        }

        $uid = (int) $_SESSION['user_id'];
        $tripId = isset($_GET['trip_id']) ? (int) $_GET['trip_id'] : 0;

        $trip = null;
        $snapshot = null;
        try {
            if ($tripId > 0) {
                $stmt = $this->db->prepare('SELECT * FROM trips WHERE id = ? AND user_id = ? LIMIT 1');
                $stmt->execute(array($tripId, $uid));
                $trip = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $stmt = $this->db->prepare(
                    'SELECT * FROM trips WHERE user_id = ? ORDER BY id DESC LIMIT 1'
                );
                $stmt->execute(array($uid));
                $trip = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            if ($trip && isset($trip['id'])) {
                $tid = (int) $trip['id'];
                $stmtJ = $this->db->prepare(
                    'SELECT trip_json FROM trip_submissions WHERE trip_id = ? AND user_id = ? ORDER BY id DESC LIMIT 1'
                );
                $stmtJ->execute(array($tid, $uid));
                $rj = $stmtJ->fetch(PDO::FETCH_ASSOC);
                if ($rj && !empty($rj['trip_json'])) {
                    $dec = json_decode((string) $rj['trip_json'], true);
                    if (is_array($dec)) {
                        $snapshot = $dec;
                    }
                }
            }
        } catch (\Throwable $e) {
            error_log('customTripSummary: ' . $e->getMessage());
            $trip = null;
        }

        if (!$trip) {
            header('Location: /CeylonGo/public/tourist/customize-trip');
            exit;
        }

        view('tourist/custom_trip_summary', array(
            'trip' => $trip,
            'trip_snapshot' => $snapshot,
        ));
    }

    /**
     * Pay for a customise-trip row: card (PayHere) or bank transfer. POST trip_id, payment_method.
     */
    public function tripPaymentCheckout() {
        $role = isset($_SESSION['user_role']) ? (string) $_SESSION['user_role'] : '';
        if (!isset($_SESSION['user_id']) || $role !== 'tourist') {
            header('Location: /CeylonGo/public/login?redirect=' . urlencode('/CeylonGo/public/tourist/customize-trip'));
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /CeylonGo/public/tourist/customize-trip');
            exit;
        }

        $tripId = isset($_POST['trip_id']) ? (int) $_POST['trip_id'] : 0;
        $method = isset($_POST['payment_method']) ? trim((string) $_POST['payment_method']) : '';
        $userId = (int) $_SESSION['user_id'];

        if ($tripId <= 0) {
            $_SESSION['payment_error'] = 'Submit your trip on Trip Summary first, then pay here.';
            header('Location: /CeylonGo/public/tourist/customize-trip');
            exit;
        }

        $trip = null;
        try {
            $stmt = $this->db->prepare(
                'SELECT id, user_id, budget_lkr, status, payhere_payment_id FROM trips WHERE id = ? AND user_id = ? LIMIT 1'
            );
            $stmt->execute(array($tripId, $userId));
            $trip = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            // Some installs have an older `trips` table without payment columns.
            error_log('tripPaymentCheckout select trips (full): ' . $e->getMessage());
            try {
                $stmt2 = $this->db->prepare(
                    'SELECT id, user_id, status FROM trips WHERE id = ? AND user_id = ? LIMIT 1'
                );
                $stmt2->execute(array($tripId, $userId));
                $trip = $stmt2->fetch(PDO::FETCH_ASSOC);
            } catch (\Throwable $e2) {
                error_log('tripPaymentCheckout select trips (fallback): ' . $e2->getMessage());
                $_SESSION['payment_error'] = 'Could not load your trip. Try again.';
                header('Location: /CeylonGo/public/tourist/customize-trip');
                exit;
            }
        }

        if (!$trip) {
            $_SESSION['payment_error'] = 'Trip not found. Submit your trip again from Trip Summary.';
            header('Location: /CeylonGo/public/tourist/customize-trip');
            exit;
        }

        $budget = isset($trip['budget_lkr']) ? (float) $trip['budget_lkr'] : 0.0;
        if ($budget <= 0) {
            // Fall back to the snapshot table if budget column isn't available or not populated.
            $budget = $this->getTripBudgetFromSubmission($tripId, $userId);
        }
        if ($budget <= 0) {
            $_SESSION['payment_error'] = 'This trip has no budget total saved. Re-submit your trip from Trip Summary (run database/alter_trips_payment_columns.sql if needed).';
            header('Location: /CeylonGo/public/tourist/customize-trip');
            exit;
        }

        $tripStatus = isset($trip['status']) ? (string) $trip['status'] : '';
        $payId = isset($trip['payhere_payment_id']) ? (string) $trip['payhere_payment_id'] : '';
        if ($payId !== '' || $tripStatus === 'confirmed' || $tripStatus === 'completed') {
            $_SESSION['payment_info'] = 'This trip is already paid or confirmed.';
            header('Location: /CeylonGo/public/tourist/customize-trip');
            exit;
        }

        if ($method === 'bank-transfer') {
            $file = isset($_FILES['bank_transfer_slip']) ? $_FILES['bank_transfer_slip'] : null;
            $fileErr = ($file && isset($file['error'])) ? $file['error'] : UPLOAD_ERR_NO_FILE;
            if (!$file || $fileErr === UPLOAD_ERR_NO_FILE) {
                $_SESSION['payment_error'] = 'Please upload a screenshot of your bank transfer slip.';
                header('Location: /CeylonGo/public/tourist/customize-trip');
                exit;
            }
            $slipPath = $this->saveTripBankTransferSlip($tripId, $file);
            if ($slipPath === null) {
                $_SESSION['payment_error'] = 'Please upload a JPG, PNG, or WebP image (max 5 MB).';
                header('Location: /CeylonGo/public/tourist/customize-trip');
                exit;
            }
            try {
                $upd = $this->db->prepare(
                    'UPDATE trips SET bank_transfer_submitted_at = NOW(), bank_transfer_slip_path = ? WHERE id = ? AND user_id = ? AND status = \'pending\''
                );
                $upd->execute(array($slipPath, $tripId, $userId));
            } catch (\Throwable $e) {
                error_log('tripPaymentCheckout bank: ' . $e->getMessage());
                @unlink((defined('UPLOADS_PATH') ? UPLOADS_PATH : (dirname(__DIR__) . '/public/uploads')) . '/' . str_replace('\\', '/', $slipPath));
                $_SESSION['payment_error'] = 'Could not save your transfer. If the problem persists, run database/alter_trips_payment_columns.sql.';
                header('Location: /CeylonGo/public/tourist/customize-trip');
                exit;
            }
            $_SESSION['payment_info'] = 'We have recorded your bank transfer. We will confirm within 1ΓÇô2 business days.';
            // Also record status in trip_submissions table.
            $this->tripSubmissionSetPaymentStatus($tripId, $userId, 'payment_submitted');
            header('Location: /CeylonGo/public/tourist/customize-trip?afterPayment=1&trip_id=' . (int) $tripId);
            exit;
        }

        if ($method !== 'card') {
            header('Location: /CeylonGo/public/tourist/customize-trip');
            exit;
        }

        $touristModel = new Tourist($this->db);
        $tourist = $touristModel->getTouristById($userId);
        $email = trim((string) (isset($tourist['email']) ? $tourist['email'] : ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['payment_error'] = 'Add a valid email in your profile before paying online.';
            header('Location: /CeylonGo/public/tourist/customize-trip');
            exit;
        }
        $phoneDigits = preg_replace('/\D/', '', (string) (isset($tourist['contact_number']) ? $tourist['contact_number'] : ''));
        if (strlen($phoneDigits) < 9) {
            $_SESSION['payment_error'] = 'Add a valid phone number in your profile (at least 9 digits) before paying online.';
            header('Location: /CeylonGo/public/tourist/customize-trip');
            exit;
        }

        $payhereCap = defined('PAYHERE_PER_TRANSACTION_MAX_LKR') ? (int) PAYHERE_PER_TRANSACTION_MAX_LKR : 0;
        if ($payhereCap > 0 && $budget > $payhereCap + 0.001) {
            $_SESSION['payment_error'] = sprintf(
                'Online card payment is limited to LKR %s per transaction. Use Bank transfer for this amount (LKR %s), or raise the PayHere limit.',
                number_format($payhereCap),
                number_format($budget, 2, '.', ',')
            );
            header('Location: /CeylonGo/public/tourist/customize-trip');
            exit;
        }

        $merchantId = trim((string) PAYHERE_MERCHANT_ID);
        $secret = trim((string) PAYHERE_MERCHANT_SECRET);
        $orderId = 'CTRIP' . $tripId . 'T' . time();
        $currency = 'LKR';
        $amount = number_format($budget, 2, '.', '');

        $hash = PayHere::checkoutHash($merchantId, $orderId, $amount, $currency, $secret);

        $fullname = trim((string) (isset($tourist['first_name']) ? $tourist['first_name'] : '') . ' ' . (isset($tourist['last_name']) ? $tourist['last_name'] : ''));
        if ($fullname === '') {
            $fullname = 'Customer';
        }
        $nameParts = preg_split('/\s+/', $fullname, 2);
        $firstName = $nameParts[0] ?: 'Customer';
        $lastName = isset($nameParts[1]) ? $nameParts[1] : 'N/A';

        $itemsName = 'Custom trip ΓÇö ' . $orderId;
        if (function_exists('iconv')) {
            $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $itemsName);
            if ($ascii !== false) {
                $itemsName = $ascii;
            }
        }
        $itemsName = mb_substr(preg_replace('/\s+/', ' ', trim($itemsName)), 0, 120) ?: 'Custom trip';

        $fields = array(
            'merchant_id' => $merchantId,
            'return_url' => app_absolute_url('tourist/payment/return') . '?',
            'cancel_url' => app_absolute_url('tourist/customize-trip'),
            'notify_url' => app_absolute_url('tourist/payment/notify'),
            'order_id' => $orderId,
            'items' => $itemsName,
            'currency' => $currency,
            'amount' => $amount,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phoneDigits,
            'address' => 'N/A',
            'city' => 'Colombo',
            'country' => 'Sri Lanka',
            'hash' => $hash,
            'custom_1' => (string) $tripId,
        );

        $checkoutUrl = PayHere::checkoutUrl((bool) PAYHERE_SANDBOX);

        $_SESSION['payhere_pending_trip_id'] = $tripId;
        $_SESSION['payhere_pending_trip_order_id'] = $orderId;

        view('tourist/payhere_redirect', array(
            'checkout_url' => $checkoutUrl,
            'fields' => $fields,
        ));
    }

    /**
     * Load trips row for JSON status; tries narrower SELECTs if payment columns are missing.
     *
     * @return array|null Trip row, null if not found, empty array if all queries failed
     */
    private function fetchTripRowForPaymentStatus($tripId, $userId) {
        $tripId = (int) $tripId;
        $userId = (int) $userId;
        if ($tripId <= 0 || $userId <= 0) {
            return null;
        }
        $variants = array(
            'SELECT id, user_id, destination, start_date, number_of_people, budget_lkr, status, payhere_payment_id, paid_at, bank_transfer_submitted_at FROM trips WHERE id = ? AND user_id = ? LIMIT 1',
            'SELECT id, user_id, destination, start_date, number_of_people, budget_lkr, status, payhere_payment_id, paid_at FROM trips WHERE id = ? AND user_id = ? LIMIT 1',
            'SELECT id, user_id, destination, start_date, number_of_people, budget_lkr, status FROM trips WHERE id = ? AND user_id = ? LIMIT 1',
            'SELECT id, user_id, destination, start_date, number_of_people, status FROM trips WHERE id = ? AND user_id = ? LIMIT 1',
        );
        foreach ($variants as $sql) {
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->execute(array($tripId, $userId));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row ? $row : null;
            } catch (PDOException $e) {
                error_log('fetchTripRowForPaymentStatus: ' . $e->getMessage());
            }
        }
        return array();
    }

    /**
     * Card payment / confirmed trip row (excludes bank-transfer-only submission awaiting admin).
     */
    private function isCustomTripFullyPaid(array $tr) {
        $stLc = strtolower(trim((string) (isset($tr['status']) ? $tr['status'] : '')));
        $hasPaidAt = isset($tr['paid_at']) && trim((string) $tr['paid_at']) !== '';
        $hasPayhere = isset($tr['payhere_payment_id']) && trim((string) $tr['payhere_payment_id']) !== '';
        return ($stLc === 'completed' || $stLc === 'confirmed') || $hasPaidAt || $hasPayhere;
    }

    /**
     * JSON: payment status for a custom trip (used by customise-trip UI).
     */
    public function tripPaymentStatus($id) {
        header('Content-Type: application/json; charset=utf-8');

        $role = isset($_SESSION['user_role']) ? (string) $_SESSION['user_role'] : '';
        $type = isset($_SESSION['user_type']) ? (string) $_SESSION['user_type'] : '';
        $isTourist = ($role === 'tourist' || $type === 'tourist');
        if (!isset($_SESSION['user_id']) || !$isTourist) {
            http_response_code(401);
            echo json_encode(array('success' => false, 'error' => 'Unauthenticated.'));
            exit;
        }

        $tripId = (int) $id;
        if ($tripId <= 0) {
            http_response_code(400);
            echo json_encode(array('success' => false, 'error' => 'Invalid trip id.'));
            exit;
        }

        $uid = (int) $_SESSION['user_id'];
        $row = $this->fetchTripRowForPaymentStatus($tripId, $uid);
        if (is_array($row) && $row === array()) {
            http_response_code(500);
            echo json_encode(array(
                'success' => false,
                'error' => 'Database error loading trip. Run database/alter_trips_payment_columns.sql if needed.',
            ));
            exit;
        }
        if ($row === null) {
            http_response_code(404);
            echo json_encode(array('success' => false, 'error' => 'Trip not found.'));
            exit;
        }

        $snapshot = null;
        try {
            $stmtJ = $this->db->prepare(
                'SELECT trip_json FROM trip_submissions WHERE trip_id = ? AND user_id = ? ORDER BY id DESC LIMIT 1'
            );
            $stmtJ->execute(array($tripId, $uid));
            $rj = $stmtJ->fetch(PDO::FETCH_ASSOC);
            if ($rj && !empty($rj['trip_json'])) {
                $dec = json_decode((string) $rj['trip_json'], true);
                if (is_array($dec)) {
                    $snapshot = $dec;
                }
            }
        } catch (\Throwable $e) {
            error_log('tripPaymentStatus snapshot: ' . $e->getMessage());
        }

        $stLc = strtolower((string) (isset($row['status']) ? $row['status'] : ''));
        $hasPaidAt = isset($row['paid_at']) && trim((string) $row['paid_at']) !== '';
        $hasPayhere = isset($row['payhere_payment_id']) && trim((string) $row['payhere_payment_id']) !== '';
        $hasBank = isset($row['bank_transfer_submitted_at']) && trim((string) $row['bank_transfer_submitted_at']) !== '';
        $paymentState = 'pending';
        if ($stLc === 'completed' || $stLc === 'confirmed' || $hasPaidAt || $hasPayhere) {
            $paymentState = 'completed';
        } elseif ($hasBank) {
            $paymentState = 'payment_submitted';
        }

        echo json_encode(array(
            'success' => true,
            'trip' => array(
                'id' => (int) $row['id'],
                'destination' => isset($row['destination']) ? (string) $row['destination'] : '',
                'start_date' => isset($row['start_date']) ? (string) $row['start_date'] : '',
                'number_of_people' => isset($row['number_of_people']) ? (int) $row['number_of_people'] : 0,
                'budget_lkr' => isset($row['budget_lkr']) ? (float) $row['budget_lkr'] : 0.0,
                'status' => isset($row['status']) ? (string) $row['status'] : '',
                'payhere_payment_id' => isset($row['payhere_payment_id']) ? (string) $row['payhere_payment_id'] : '',
                'paid_at' => isset($row['paid_at']) ? (string) $row['paid_at'] : '',
                'bank_transfer_submitted_at' => isset($row['bank_transfer_submitted_at']) ? (string) $row['bank_transfer_submitted_at'] : '',
                'payment_state' => $paymentState,
            ),
            'snapshot' => $snapshot,
        ));
        exit;
    }

    /**
     * Final submit from Trip Summary wizard (AJAX). Stores a summary row in `trips` when the table exists.
     */
    public function tripSubmit() {
        $ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower(trim($_SERVER['HTTP_X_REQUESTED_WITH'])) === 'xmlhttprequest';
        if ($ajax) {
            header('Content-Type: application/json; charset=utf-8');
        }

        $role = isset($_SESSION['user_role']) ? (string) $_SESSION['user_role'] : '';
        if (!isset($_SESSION['user_id']) || $role !== 'tourist') {
            if ($ajax) {
                http_response_code(401);
                echo json_encode(array('success' => false, 'error' => 'Please log in as a tourist to submit your trip.'));
                exit;
            }
            header('Location: /CeylonGo/public/tourist/dashboard');
            exit;
        }

        $userId = (int) $_SESSION['user_id'];
        $destination = trim((string) (isset($_POST['destination']) ? $_POST['destination'] : ''));
        $startDate = trim((string) (isset($_POST['start_date']) ? $_POST['start_date'] : ''));
        $endDate = trim((string) (isset($_POST['end_date']) ? $_POST['end_date'] : ''));
        $customerName = trim((string) (isset($_POST['customer_name']) ? $_POST['customer_name'] : ''));
        if ($customerName === '') {
            $customerName = trim((string) (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Tourist'));
        }
        $numPeople = max(1, (int) (isset($_POST['number_of_people']) ? $_POST['number_of_people'] : 1));
        $numDays = max(1, (int) (isset($_POST['number_of_days']) ? $_POST['number_of_days'] : 1));
        $budgetLkr = isset($_POST['budget_lkr']) ? (float) $_POST['budget_lkr'] : 0.0;
        if ($budgetLkr < 0 || $budgetLkr > 999999999) {
            $budgetLkr = 0.0;
        }

        // Guide requests are queued on the trip wizard and only persisted after "Submit trip".
        $guideReqRaw = isset($_POST['guide_requests']) ? (string) $_POST['guide_requests'] : '';
        $guideReqs = array();
        if ($guideReqRaw !== '') {
            $decodedG = json_decode($guideReqRaw, true);
            if (is_array($decodedG)) {
                foreach ($decodedG as $row) {
                    if (!is_array($row)) continue;
                    $loc = isset($row['location']) ? trim((string) $row['location']) : '';
                    $dt = isset($row['date']) ? trim((string) $row['date']) : '';
                    $lang = isset($row['language']) ? trim((string) $row['language']) : '';
                    $tm = isset($row['time']) ? trim((string) $row['time']) : '';
                    $notes = isset($row['notes']) ? trim((string) $row['notes']) : '';
                    if ($loc === '' || $dt === '' || $lang === '' || $tm === '') continue;
                    $guideReqs[] = array(
                        'location' => $loc,
                        'date' => $dt,
                        'language' => $lang,
                        'time' => $tm,
                        'notes' => $notes,
                    );
                }
            }
        }

        if ($destination === '' || $startDate === '') {
            if ($ajax) {
                http_response_code(400);
                echo json_encode(array('success' => false, 'error' => 'Please complete destination and dates before submitting.'));
                exit;
            }
            header('Location: /CeylonGo/public/tourist/customize-trip?error=' . urlencode('Complete your trip details first.'));
            exit;
        }

        try {
            $sql = 'INSERT INTO trips (user_id, customer_name, number_of_people, start_date, destination, number_of_days, budget_lkr, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array(
                $userId,
                $customerName,
                $numPeople,
                $startDate,
                $destination,
                $numDays,
                $budgetLkr > 0 ? round($budgetLkr, 2) : null,
                'pending',
            ));
            $tripId = (int) $this->db->lastInsertId();
            $_SESSION['last_trip_id'] = $tripId;

            $submissionArr = array(
                'trip_id' => $tripId,
                'user_id' => $userId,
                'destination' => $destination,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'customer_name' => $customerName,
                'number_of_people' => $numPeople,
                'number_of_days' => $numDays,
                'budget_lkr' => $budgetLkr,
                'payment_status' => 'pending',
                'submitted_at' => date('c'),
            );
            $wizRaw = isset($_POST['wizard_snapshot']) ? trim((string) $_POST['wizard_snapshot']) : '';
            if ($wizRaw !== '' && strlen($wizRaw) < 600000) {
                $wizDecoded = json_decode($wizRaw, true);
                if (is_array($wizDecoded)) {
                    $submissionArr['wizard_snapshot'] = $wizDecoded;
                }
            }
            // Record submission snapshot + initial payment status in trip_submissions.
            $this->tripSubmissionUpsert($tripId, $userId, $submissionArr);

            $createdGuideRequests = 0;
            if ($tripId > 0 && count($guideReqs) > 0) {
                try {
                    $contact = '';
                    try {
                        $touristModel = new Tourist($this->db);
                        $tourist = $touristModel->getTouristById($userId);
                        if (is_array($tourist) && isset($tourist['contact_number'])) {
                            $contact = trim((string) $tourist['contact_number']);
                        }
                    } catch (\Throwable $eT) {
                        // Ignore and fall back to session.
                    }
                    if ($contact === '' && isset($_SESSION['user_contact'])) $contact = trim((string) $_SESSION['user_contact']);
                    if ($contact === '' && isset($_SESSION['user_phone'])) $contact = trim((string) $_SESSION['user_phone']);
                    if ($contact === '' && isset($_SESSION['contact_number'])) $contact = trim((string) $_SESSION['contact_number']);
                    if ($contact === '') $contact = 'N/A';

                    $gr = new GuideRequest($this->db);
                    foreach ($guideReqs as $g) {
                        $gr->customerName = $customerName;
                        $gr->contactNumber = $contact;
                        $gr->location = $g['location'];
                        $gr->language = $g['language'];
                        $gr->date = $g['date'];
                        $gr->time = $g['time'];
                        $gr->notes = $g['notes'];
                        $gr->status = 'pending';
                        if ($gr->create()) {
                            $createdGuideRequests++;
                        }
                    }
                } catch (\Throwable $eG) {
                    error_log('tripSubmit create guide requests: ' . $eG->getMessage());
                }
            }
            if ($ajax) {
                echo json_encode(array('success' => true, 'trip_id' => $tripId, 'created_guide_requests' => $createdGuideRequests));
                exit;
            }
            header('Location: /CeylonGo/public/tourist/customize-trip?success=' . urlencode('Trip submitted.'));
            exit;
        } catch (\Throwable $e) {
            error_log('tripSubmit: ' . $e->getMessage());
            if (stripos($e->getMessage(), 'budget_lkr') !== false || stripos($e->getMessage(), 'Unknown column') !== false) {
                try {
                    $sql = 'INSERT INTO trips (user_id, customer_name, number_of_people, start_date, destination, number_of_days, status)
                            VALUES (?, ?, ?, ?, ?, ?, ?)';
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute(array(
                        $userId,
                        $customerName,
                        $numPeople,
                        $startDate,
                        $destination,
                        $numDays,
                        'pending',
                    ));
                    $tripId = (int) $this->db->lastInsertId();
                    $_SESSION['last_trip_id'] = $tripId;

                    $submissionArrFb = array(
                        'trip_id' => $tripId,
                        'user_id' => $userId,
                        'destination' => $destination,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'customer_name' => $customerName,
                        'number_of_people' => $numPeople,
                        'number_of_days' => $numDays,
                        'budget_lkr' => $budgetLkr,
                        'payment_status' => 'pending',
                        'submitted_at' => date('c'),
                    );
                    $wizRawFb = isset($_POST['wizard_snapshot']) ? trim((string) $_POST['wizard_snapshot']) : '';
                    if ($wizRawFb !== '' && strlen($wizRawFb) < 600000) {
                        $wizDecodedFb = json_decode($wizRawFb, true);
                        if (is_array($wizDecodedFb)) {
                            $submissionArrFb['wizard_snapshot'] = $wizDecodedFb;
                        }
                    }
                    // Record submission snapshot + initial payment status in trip_submissions (fallback insert path).
                    $this->tripSubmissionUpsert($tripId, $userId, $submissionArrFb);
                    $createdGuideRequests = 0;
                    if ($tripId > 0 && count($guideReqs) > 0) {
                        try {
                            $contact = '';
                            try {
                                $touristModel = new Tourist($this->db);
                                $tourist = $touristModel->getTouristById($userId);
                                if (is_array($tourist) && isset($tourist['contact_number'])) {
                                    $contact = trim((string) $tourist['contact_number']);
                                }
                            } catch (\Throwable $eT2) {
                                // Ignore and fall back to session.
                            }
                            if ($contact === '' && isset($_SESSION['user_contact'])) $contact = trim((string) $_SESSION['user_contact']);
                            if ($contact === '' && isset($_SESSION['user_phone'])) $contact = trim((string) $_SESSION['user_phone']);
                            if ($contact === '' && isset($_SESSION['contact_number'])) $contact = trim((string) $_SESSION['contact_number']);
                            if ($contact === '') $contact = 'N/A';
                            $gr = new GuideRequest($this->db);
                            foreach ($guideReqs as $g) {
                                $gr->customerName = $customerName;
                                $gr->contactNumber = $contact;
                                $gr->location = $g['location'];
                                $gr->language = $g['language'];
                                $gr->date = $g['date'];
                                $gr->time = $g['time'];
                                $gr->notes = $g['notes'];
                                $gr->status = 'pending';
                                if ($gr->create()) $createdGuideRequests++;
                            }
                        } catch (\Throwable $eG2) {
                            error_log('tripSubmit fallback create guide requests: ' . $eG2->getMessage());
                        }
                    }
                    if ($ajax) {
                        echo json_encode(array(
                            'success' => true,
                            'trip_id' => $tripId,
                            'budget_persisted' => false,
                            'message' => 'Run database/alter_trips_payment_columns.sql to save your budget for online payment.',
                            'created_guide_requests' => $createdGuideRequests,
                        ));
                        exit;
                    }
                    header('Location: /CeylonGo/public/tourist/customize-trip?success=' . urlencode('Trip submitted.'));
                    exit;
                } catch (\Throwable $e2) {
                    error_log('tripSubmit fallback: ' . $e2->getMessage());
                    $e = $e2;
                }
            }
            if ($ajax) {
                if (stripos($e->getMessage(), 'trips') !== false || stripos($e->getMessage(), 'Base table') !== false) {
                    echo json_encode(array(
                        'success' => true,
                        'persisted' => false,
                        'message' => 'Trip submitted (database trips table not available).',
                    ));
                    exit;
                }
                http_response_code(500);
                echo json_encode(array('success' => false, 'error' => 'Could not save your trip. Please try again.'));
                exit;
            }
            header('Location: /CeylonGo/public/tourist/customize-trip?error=' . urlencode('Could not submit trip.'));
            exit;
        }
    }

    /**
     * New dashboard (separate from old dashboard).
     * Uses view dashboard.php and CSS dashboard.css.
     */
    public function dashboardNew() {
        $tourist_data = null;
        if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'tourist') {
            $touristModel = new Tourist($this->db);
            $tourist_data = $touristModel->getTouristById($_SESSION['user_id']);
        }
        $packageModel = new Package($this->db);
        $trending_bar_packages = $packageModel->getAll(array('trending' => true));

        $inquiries = array();
        if (isset($_SESSION['user_id']) && (isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '') === 'tourist') {
            $inqModel = new Inquiry($this->db);
            $inquiries = $inqModel->getByUserId((int)$_SESSION['user_id'], 5);
        }

        view('tourist/dashboard', array(
            'tourist_data' => $tourist_data,
            'is_logged_in' => isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'tourist',
            'trending_bar_packages' => $trending_bar_packages,
            'inquiries' => $inquiries
        ));
    }

    /**
     * Sidebar dashboard stats: customised trips with payment or bank transfer submitted only
     * (same eligibility as /tourist/my-bookings?view=custom). Pending-only planner rows are excluded.
     * Package holidays are not included.
     *
     * Upcoming / completed are based on the trip window (start_date + number_of_days), not DB status alone:
     * ΓÇö upcoming: today is on or before the last day of the trip
     * ΓÇö completed: today is after the last day of the trip (journey has finished)
     *
     * @return array{total_bookings:int,upcoming_trips:int,total_spent_lkr:float,completed_trips:int}
     */
    private function computeTouristDashboardStats($userId) {
        $userId = (int) $userId;
        $out = array(
            'total_bookings' => 0,
            'upcoming_trips' => 0,
            'total_spent_lkr' => 0.0,
            'completed_trips' => 0,
        );
        if ($userId <= 0) {
            return $out;
        }
        $today = date('Y-m-d');

        try {
            $stmt = $this->db->prepare(
                'SELECT start_date, number_of_days, status, budget_lkr, paid_at, payhere_payment_id, bank_transfer_submitted_at '
                . 'FROM trips WHERE user_id = ?'
            );
            $stmt->bindValue(1, $userId, PDO::PARAM_INT);
            $stmt->execute();
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $tr) {
                $st = strtolower(trim((string) (isset($tr['status']) ? $tr['status'] : '')));
                if ($st === 'cancelled') {
                    continue;
                }
                $hasPaidAt = isset($tr['paid_at']) && trim((string) $tr['paid_at']) !== '';
                $hasPayhere = isset($tr['payhere_payment_id']) && trim((string) $tr['payhere_payment_id']) !== '';
                $hasBank = isset($tr['bank_transfer_submitted_at']) && trim((string) $tr['bank_transfer_submitted_at']) !== '';
                $paidComplete = ($st === 'completed' || $st === 'confirmed') || $hasPaidAt || $hasPayhere;
                if (!($paidComplete || $hasBank)) {
                    continue;
                }

                $out['total_bookings']++;
                $start = isset($tr['start_date']) ? trim((string) $tr['start_date']) : '';
                $numDays = isset($tr['number_of_days']) ? (int) $tr['number_of_days'] : 1;
                if ($numDays < 1) {
                    $numDays = 1;
                }

                $endDateStr = '';
                if ($start !== '') {
                    $startTs = strtotime($start . ' 00:00:00');
                    if ($startTs !== false) {
                        $endTs = strtotime('+' . ($numDays - 1) . ' day', $startTs);
                        if ($endTs !== false) {
                            $endDateStr = date('Y-m-d', $endTs);
                        }
                    }
                }
                if ($endDateStr !== '') {
                    if ($today > $endDateStr) {
                        $out['completed_trips']++;
                    } else {
                        $out['upcoming_trips']++;
                    }
                }

                $out['total_spent_lkr'] += (float) (isset($tr['budget_lkr']) ? $tr['budget_lkr'] : 0);
            }
        } catch (PDOException $e) {
            error_log('computeTouristDashboardStats trips: ' . $e->getMessage());
        }

        return $out;
    }

    /**
     * Trip rows for dashboard stat cards (expandable lists). Same eligibility as computeTouristDashboardStats / my-bookings?view=custom.
     *
     * @return array{dashboard_bookings: array<int, array>, dashboard_bookings_upcoming: array<int, array>}
     */
    private function buildDashboardSidebarTripLists($userId, $touristEmail = '') {
        $userId = (int) $userId;
        $bookings = array();
        $upcomingOnly = array();
        if ($userId <= 0) {
            return array(
                'dashboard_bookings' => $bookings,
                'dashboard_bookings_upcoming' => $upcomingOnly,
            );
        }
        $today = date('Y-m-d');
        $base = defined('BASE_URL') ? rtrim((string) BASE_URL, '/') : '/CeylonGo/public';
        $em = trim((string) $touristEmail);

        try {
            $stmt = $this->db->prepare(
                'SELECT id, customer_name, destination, start_date, number_of_days, number_of_people, budget_lkr, status, '
                . 'payhere_payment_id, paid_at, bank_transfer_submitted_at, refund_requested_at '
                . 'FROM trips WHERE user_id = ? ORDER BY id DESC'
            );
            $stmt->execute(array($userId));
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('buildDashboardSidebarTripLists: ' . $e->getMessage());
            return array(
                'dashboard_bookings' => $bookings,
                'dashboard_bookings_upcoming' => $upcomingOnly,
            );
        }

        foreach ($rows as $tr) {
            $stLc = strtolower(trim((string) (isset($tr['status']) ? $tr['status'] : '')));
            if ($stLc === 'cancelled') {
                continue;
            }
            $hasPaidAt = isset($tr['paid_at']) && trim((string) $tr['paid_at']) !== '';
            $hasPayhere = isset($tr['payhere_payment_id']) && trim((string) $tr['payhere_payment_id']) !== '';
            $hasBank = isset($tr['bank_transfer_submitted_at']) && trim((string) $tr['bank_transfer_submitted_at']) !== '';
            $paidComplete = ($stLc === 'completed' || $stLc === 'confirmed') || $hasPaidAt || $hasPayhere;
            if (!($paidComplete || $hasBank)) {
                continue;
            }
            $isBankFlow = $hasBank && !$paidComplete;
            $refundRequested = !empty($tr['refund_requested_at']);

            $tid = (int) (isset($tr['id']) ? $tr['id'] : 0);
            $dest = isset($tr['destination']) ? trim((string) $tr['destination']) : '';
            $start = isset($tr['start_date']) ? trim((string) $tr['start_date']) : '';
            $numDays = isset($tr['number_of_days']) ? (int) $tr['number_of_days'] : 1;
            if ($numDays < 1) {
                $numDays = 1;
            }
            $travelers = isset($tr['number_of_people']) ? (int) $tr['number_of_people'] : 0;
            $budget = isset($tr['budget_lkr']) ? (float) $tr['budget_lkr'] : 0.0;
            $cust = isset($tr['customer_name']) ? trim((string) $tr['customer_name']) : '';
            $contact_line = ($cust === '' && $em === '') ? '' : ($cust . ($em !== '' ? ' · ' . $em : ''));

            if ($refundRequested) {
                $badge_variant = 'refund';
                $badge_text = 'Refund requested';
            } elseif ($isBankFlow) {
                $badge_variant = 'bank';
                $badge_text = 'Payment submitted';
            } else {
                $badge_variant = 'paid';
                $badge_text = 'Completed';
            }
            $note_message = '';
            if ($isBankFlow) {
                $note_message = 'We have recorded your bank transfer. Your booking stays approved while we verify the payment (usually within 1–2 business days).';
            }

            $endDateStr = '';
            if ($start !== '') {
                $startTs = strtotime($start . ' 00:00:00');
                if ($startTs !== false) {
                    $endTs = strtotime('+' . ($numDays - 1) . ' day', $startTs);
                    if ($endTs !== false) {
                        $endDateStr = date('Y-m-d', $endTs);
                    }
                }
            }
            $phase = 'upcoming';
            if ($endDateStr !== '' && $today > $endDateStr) {
                $phase = 'ended';
            }

            $total_line = $budget > 0 ? ('LKR ' . number_format((int) round($budget))) : '';

            $card = array(
                'id' => $tid,
                'destination' => $dest !== '' ? $dest : 'Your trip',
                'start_date' => $start,
                'number_of_days' => $numDays,
                'travelers' => $travelers,
                'phase' => $phase,
                'badge_variant' => $badge_variant,
                'badge_text' => $badge_text,
                'note_message' => $note_message,
                'total_line' => $total_line,
                'contact_line' => $contact_line,
                'overview_url' => $base . '/tourist/customize-trip?step=14&trip_id=' . $tid,
            );
            $bookings[] = $card;
            if ($endDateStr !== '' && $today <= $endDateStr) {
                $upcomingOnly[] = $card;
            }
        }

        return array(
            'dashboard_bookings' => $bookings,
            'dashboard_bookings_upcoming' => $upcomingOnly,
        );
    }

    /**
     * Tourist sidebar dashboard (welcome + stats). Separate from /tourist/dashboard (marketing / inquiry page).
     */
    public function dashboardSide() {
        $role = isset($_SESSION['user_role']) ? (string) $_SESSION['user_role'] : '';
        if (!isset($_SESSION['user_id']) || $role !== 'tourist') {
            header('Location: /CeylonGo/public/tourist/dashboard');
            exit;
        }

        $touristModel = new Tourist($this->db);
        $uid = (int) $_SESSION['user_id'];
        $tourist_data = $touristModel->getTouristById($uid);

        $dashboard_stats = $this->computeTouristDashboardStats($uid);
        $tourist_email = '';
        if (is_array($tourist_data) && isset($tourist_data['email'])) {
            $tourist_email = trim((string) $tourist_data['email']);
        }
        $trip_lists = $this->buildDashboardSidebarTripLists($uid, $tourist_email);

        view('tourist/dashboard_sidebar', array(
            'tourist_data' => $tourist_data,
            'dashboard_stats' => $dashboard_stats,
            'dashboard_bookings' => $trip_lists['dashboard_bookings'],
            'dashboard_bookings_upcoming' => $trip_lists['dashboard_bookings_upcoming'],
        ));
    }

    public function inquirySubmit() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /CeylonGo/public/tourist/dashboard#inquiry');
            exit;
        }

        $isTouristLogged = isset($_SESSION['user_id']) && ((isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '') === 'tourist');
        if (!$isTouristLogged) {
            $_SESSION['inquiry_error'] = 'Please log in to submit an inquiry.';
            header('Location: /CeylonGo/public/tourist/dashboard?openLogin=inquiry');
            exit;
        }

        $subject = trim((string)($_POST['subject'] ?? ''));
        $message = trim((string)($_POST['message'] ?? ''));
        if ($subject === '' || $message === '') {
            $_SESSION['inquiry_error'] = 'Please fill subject and message.';
            header('Location: /CeylonGo/public/tourist/dashboard#inquiry');
            exit;
        }

        $inqModel = new Inquiry($this->db);

        $ok = false;
        try {
            $ok = $inqModel->create((int)$_SESSION['user_id'], $subject, $message);
        } catch (\Throwable $e) {
            error_log('inquirySubmit: ' . $e->getMessage());
            $ok = false;
        }

        if ($ok) {
            $_SESSION['inquiry_info'] = 'Your inquiry was sent. Our team will reply soon.';
        } else {
            $_SESSION['inquiry_error'] = 'Could not send your inquiry. Please try again.';
        }
        header('Location: /CeylonGo/public/tourist/dashboard#inquiry');
        exit;
    }

    public function transportRequestView() {
        view('tourist/transport_services');
    }

    public function transportRequest() {
        $data = $_POST;

        // Validate required fields (notes is optional)
        $required = ['customerName', 'contactNumber', 'vehicleType', 'date', 'pickupTime', 'pickupLocation', 'dropoffLocation', 'numPeople'];
        foreach ($required as $key) {
            $val = trim($data[$key] ?? '');
            if ($val === '' && $key !== 'numPeople') {
                header("Location: /CeylonGo/public/tourist/customize-trip?error=" . urlencode("Please fill all required fields before confirming."));
                exit();
            }
        }

        $request = new TransportRequest($this->db);
        $request->userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
        $request->customerName = trim($data['customerName'] ?? '');
        $request->contactNumber = trim($data['contactNumber'] ?? '');
        $request->vehicleType = trim($data['vehicleType'] ?? '');
        $request->date = trim($data['date'] ?? '');
        $pickupTime = trim($data['pickupTime'] ?? '');
        $request->pickupTime = (strlen($pickupTime) === 5) ? $pickupTime . ':00' : $pickupTime; // HH:MM -> HH:MM:SS
        $request->pickupLocation = trim($data['pickupLocation'] ?? '');
        $request->dropoffLocation = trim($data['dropoffLocation'] ?? '');
        $request->numPeople = max(1, (int) ($data['numPeople'] ?? 1));
        $request->notes = trim($data['notes'] ?? '');
        $request->estimatedFare = isset($data['estimatedFare']) && $data['estimatedFare'] !== '' ? $data['estimatedFare'] : null;
        $request->distance = isset($data['distance']) && $data['distance'] !== '' ? $data['distance'] : null;

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        if ($request->addRequest()) {
            if ($isAjax) {
                header('Content-Type: application/json');
                $row = $request->getRequestById($request->id);
                $status = is_array($row) && isset($row['status']) ? (string) $row['status'] : 'pending';
                echo json_encode([
                    'success' => true,
                    'request_id' => (int) $request->id,
                    'status' => $status,
                ]);
                exit();
            }
            header("Location: /CeylonGo/public/tourist/transport-report");
            exit();
        } else {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Failed to submit transport request.']);
                exit();
            }
            header("Location: /CeylonGo/public/tourist/customize-trip?error=" . urlencode("Failed to submit transport request."));
            exit();
        }
    }

    public function transportReport() {
        $requestModel = new TransportRequest($this->db);
        if (isset($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'tourist') {
            $requests = $requestModel->getRequestsByUserId((int) $_SESSION['user_id']);
        } else {
            $requests = $requestModel->getAllRequests();
        }
        $tourist_data = null;
        $user_name = $_SESSION['user_name'] ?? '';
        if (isset($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'tourist') {
            $touristModel = new Tourist($this->db);
            $tourist_data = $touristModel->getTouristById($_SESSION['user_id']);
            if ($user_name === '' && $tourist_data) {
                $user_name = trim(($tourist_data['first_name'] ?? '') . ' ' . ($tourist_data['last_name'] ?? ''));
            }
        }
        view('tourist/transport_report', [
            'requests' => $requests,
            'tourist_data' => $tourist_data,
            'user_name' => $user_name
        ]);
    }

    public function tourGuides() {
        $guideModel = new Guide($this->db);
        $guides = $guideModel->getAllGuides();
        view('tourist/tour_guides', ['guides' => $guides]);
    }

    public function chooseHotel() {
        view('tourist/choose_hotel');
    }

    public function hotelDetails($id) {
        view('tourist/hotel_details', ['hotel_id' => $id]);
    }

    public function hotelRequestSubmit() {
        $ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower(trim($_SERVER['HTTP_X_REQUESTED_WITH'])) === 'xmlhttprequest';
        if ($ajax) {
            header('Content-Type: application/json; charset=utf-8');
        }

        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'tourist') {
            if ($ajax) {
                http_response_code(401);
                echo json_encode(['success' => false, 'error' => 'Please log in as a tourist to complete a booking.']);
                exit;
            }
            header('Location: /CeylonGo/public/login');
            exit;
        }

        $data = $_POST;

        $userId       = (int)($data['user_id'] ?? $_SESSION['user_id']);
        $hotelSlug    = trim($data['hotel_id'] ?? '');
        $hotelName    = trim($data['hotel_name'] ?? '');
        $customerName = trim($data['customer_name'] ?? '');
        $contact      = trim($data['contact_number'] ?? '');
        $guests       = (int)($data['guests'] ?? 1);
        $adults       = (int)($data['adults'] ?? 0);
        $children     = (int)($data['children'] ?? 0);
        $checkIn      = trim($data['check_in_date'] ?? '');
        $checkOut     = trim($data['check_out_date'] ?? '');
        $nights       = (int)($data['nights'] ?? 1);
        $roomType     = trim($data['room_type'] ?? '');
        $roomCount    = (int)($data['room_count'] ?? 1);
        $totalPrice   = isset($data['total_price']) ? (float)$data['total_price'] : 0.0;

        if (!$hotelSlug || !$hotelName || !$customerName || !$contact || !$checkIn || !$checkOut || !$roomType || $totalPrice <= 0) {
            if ($ajax) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Please fill all required fields for the hotel booking.']);
                exit;
            }
            header('Location: /CeylonGo/public/tourist/customize-trip?error=' . urlencode('Please fill all required fields for hotel booking.'));
            exit;
        }

        try {
            $sql = "INSERT INTO hotel_bookings (
                        user_id, hotel_slug, hotel_name,
                        guest_name, contact_number, guests, adults, children,
                        check_in, check_out, nights,
                        room_type, room_count, total_price, currency, status
                    ) VALUES (
                        :user_id, :hotel_slug, :hotel_name,
                        :guest_name, :contact_number, :guests, :adults, :children,
                        :check_in, :check_out, :nights,
                        :room_type, :room_count, :total_price, 'LKR', 'pending'
                    )";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id'        => $userId,
                ':hotel_slug'     => $hotelSlug,
                ':hotel_name'     => $hotelName,
                ':guest_name'     => $customerName,
                ':contact_number' => $contact,
                ':guests'         => $guests,
                ':adults'         => $adults,
                ':children'       => $children,
                ':check_in'       => $checkIn,
                ':check_out'      => $checkOut,
                ':nights'         => $nights,
                ':room_type'      => $roomType,
                ':room_count'     => $roomCount,
                ':total_price'    => $totalPrice,
            ]);
            $bookingId = (int)$this->db->lastInsertId();
            if ($ajax) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Your accommodation booking has been saved.',
                    'booking_id' => $bookingId,
                    'status' => 'pending',
                    'hotel_name' => $hotelName,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'total_price' => $totalPrice,
                    'total_price_display' => 'Rs.' . number_format($totalPrice, 0, '.', ','),
                ]);
                exit;
            }
            header('Location: /CeylonGo/public/tourist/customize-trip?success=' . urlencode('Hotel booking saved.'));
            exit;
        } catch (\Throwable $e) {
            if ($ajax) {
                http_response_code(500);
                $msg = 'Could not save your booking. If the problem continues, contact support.';
                if (stripos($e->getMessage(), 'hotel_bookings') !== false || stripos($e->getMessage(), 'Base table') !== false) {
                    $msg = 'Database table hotel_bookings is missing. Run database/create_hotel_bookings_table.sql on your database.';
                }
                echo json_encode(['success' => false, 'error' => $msg]);
                exit;
            }
            header('Location: /CeylonGo/public/tourist/customize-trip?error=' . urlencode('Failed to save hotel booking.'));
            exit;
        }
    }

    public function bookingForm() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
            $package_id = isset($_GET['package']) ? (int) $_GET['package'] : 1;
            $redirect = '/CeylonGo/public/tourist/booking-form?package=' . $package_id;
            header('Location: /CeylonGo/public/login?redirect=' . urlencode($redirect));
            exit;
        }
        $package_id = isset($_GET['package']) ? (int) $_GET['package'] : 1;
        $package = $this->getPackageDetailById($package_id);
        if (!$package) {
            header('Location: /CeylonGo/public/tourist/packages');
            exit;
        }
        $fullname = '';
        $email = '';
        $phone = '';
        $is_tourist = isset($_SESSION['user_id']) && ($_SESSION['user_type'] ?? $_SESSION['user_role'] ?? '') === 'tourist';
        if ($is_tourist) {
            require_once dirname(__DIR__) . '/models/Tourist.php';
            $touristModel = new Tourist($this->db);
            $tourist = $touristModel->getTouristById($_SESSION['user_id']);
            if ($tourist) {
                $fullname = trim(($tourist['first_name'] ?? '') . ' ' . ($tourist['last_name'] ?? ''));
                $email = $tourist['email'] ?? $_SESSION['user_email'] ?? '';
                $phone = $tourist['contact_number'] ?? '';
            } else {
                $fullname = $_SESSION['user_name'] ?? '';
                $email = $_SESSION['user_email'] ?? '';
            }
        }
        view('tourist/booking_form', [
            'package' => $package,
            'fullname' => $fullname,
            'email' => $email,
            'phone' => $phone,
            'error' => isset($_GET['error']) ? trim($_GET['error']) : '',
            'success' => isset($_GET['success']) && $_GET['success'] === '1',
        ]);
    }

    public function bookingFormSubmit() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
            $package_id = isset($_POST['package_id']) ? (int) $_POST['package_id'] : 0;
            $redirect = '/CeylonGo/public/tourist/booking-form?package=' . ($package_id ?: 1);
            header('Location: /CeylonGo/public/login?redirect=' . urlencode($redirect) . '&msg=' . urlencode('Please log in to book a package.'));
            exit;
        }
        $package_id = isset($_POST['package_id']) ? (int) $_POST['package_id'] : 0;
        $travel_date = isset($_POST['travel_date']) ? trim($_POST['travel_date']) : '';
        $min_date = date('Y-m-d', strtotime('+21 days'));
        if ($travel_date !== '' && $travel_date < $min_date) {
            header('Location: /CeylonGo/public/tourist/booking-form?package=' . $package_id . '&error=' . urlencode('Preferred Travel Date must be at least 3 weeks from today.'));
            exit;
        }
        $package = $this->getPackageDetailById($package_id);
        if (!$package) {
            header('Location: /CeylonGo/public/tourist/packages');
            exit;
        }
        $price_adult = isset($package['price']) ? (int) $package['price'] : 0;
        $pkg_category = isset($package['category']) ? strtolower(trim($package['category'])) : '';
        $child_ratio = isset($package['price_child_ratio']) ? (float) $package['price_child_ratio'] : 0.5;
        $infant_ratio = isset($package['price_infant_ratio']) ? (float) $package['price_infant_ratio'] : 0.0;

        if ($pkg_category === 'solo') {
            $travelers = 1;
            $total = $price_adult;
            $adults = 1;
            $children = 0;
            $infants = 0;
        } elseif ($pkg_category === 'honeymoon') {
            $travelers = 2;
            if ((int) ($_POST['travelers'] ?? 0) !== 2) {
                header('Location: /CeylonGo/public/tourist/booking-form?package=' . $package_id . '&error=' . urlencode('Honeymoon packages are for 2 travelers only.'));
                exit;
            }
            $total = 2 * $price_adult;
            $adults = 2;
            $children = 0;
            $infants = 0;
        } else {
            $adults = isset($_POST['adults']) ? (int) $_POST['adults'] : 1;
            $children = isset($_POST['children']) ? (int) $_POST['children'] : 0;
            $infants = isset($_POST['infants']) ? (int) $_POST['infants'] : 0;
            if ($adults < 1) {
                header('Location: /CeylonGo/public/tourist/booking-form?package=' . $package_id . '&error=' . urlencode('At least 1 adult is required.'));
                exit;
            }
            $travelers = $adults + $children + $infants;
            $total = (int) round(($adults * $price_adult) + ($children * $price_adult * $child_ratio) + ($infants * $price_adult * $infant_ratio));
        }

        // Save booking to database
        try {
            $sql = "INSERT INTO package_bookings 
                    (user_id, package_id, package_name, travelers, adults, children, infants, travel_date, 
                    fullname, email, phone, special_requests, total_amount, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                (int) $_SESSION['user_id'],
                $package_id,
                isset($_POST['package_name']) ? trim($_POST['package_name']) : ($package['title'] ?? ''),
                $travelers,
                $adults,
                $children,
                $infants,
                $travel_date,
                isset($_POST['fullname']) ? trim($_POST['fullname']) : '',
                isset($_POST['email']) ? trim($_POST['email']) : '',
                isset($_POST['phone']) ? trim($_POST['phone']) : '',
                isset($_POST['special_requests']) ? trim($_POST['special_requests']) : '',
                $total
            ]);
            $booking_id = $this->db->lastInsertId();
            header('Location: /CeylonGo/public/tourist/my-bookings?success=1');
            exit;
        } catch (PDOException $e) {
            error_log("Booking save error: " . $e->getMessage());
            header('Location: /CeylonGo/public/tourist/booking-form?package=' . $package_id . '&error=' . urlencode('Failed to save booking. Please try again.'));
            exit;
        }
    }

    public function myBookings() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
            $loginReturn = '/CeylonGo/public/tourist/my-bookings';
            if (isset($_GET['view']) && (string) $_GET['view'] === 'custom') {
                $loginReturn .= '?view=custom';
            }
            header('Location: /CeylonGo/public/login?redirect=' . urlencode($loginReturn));
            exit;
        }
        $current_user_id = (int) $_SESSION['user_id'];
        $bookings_custom_only = isset($_GET['view']) && (string) $_GET['view'] === 'custom';

        $bookings = array();
        if (!$bookings_custom_only) {
            try {
                $sql = "SELECT * FROM package_bookings WHERE user_id = ? ORDER BY created_at DESC";
                $stmt = $this->db->prepare($sql);
                $stmt->execute(array($current_user_id));
                $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($bookings as &$booking) {
                    $booking['id'] = (string) $booking['id'];
                    $booking['user_id'] = (int) $booking['user_id'];
                    $booking['package_id'] = (int) $booking['package_id'];
                    $booking['travelers'] = (int) $booking['travelers'];
                    $booking['adults'] = (int) $booking['adults'];
                    $booking['children'] = (int) $booking['children'];
                    $booking['infants'] = (int) $booking['infants'];
                    $booking['total_amount'] = (float) $booking['total_amount'];
                }
            } catch (PDOException $e) {
                error_log("Error fetching bookings: " . $e->getMessage());
                $bookings = array();
            }
        }

        $tourist_email = '';
        $custom_trips = array();
        if ($bookings_custom_only) {
            try {
                $touristModel = new Tourist($this->db);
                $touristRow = $touristModel->getTouristById($current_user_id);
                if (is_array($touristRow) && isset($touristRow['email'])) {
                    $tourist_email = trim((string) $touristRow['email']);
                }
            } catch (\Throwable $eT) {
                $tourist_email = '';
            }
            try {
                $stmtT = $this->db->prepare(
                    'SELECT id, customer_name, destination, start_date, number_of_people, budget_lkr, status, payhere_payment_id, paid_at, bank_transfer_submitted_at, refund_requested_at, created_at '
                    . 'FROM trips WHERE user_id = ? ORDER BY id DESC'
                );
                $stmtT->execute(array($current_user_id));
                $tripRows = $stmtT->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $eTr) {
                error_log('myBookings custom trips: ' . $eTr->getMessage());
                $tripRows = array();
            }
            foreach ($tripRows as $tr) {
                $stLc = strtolower((string) (isset($tr['status']) ? $tr['status'] : ''));
                $hasPaidAt = isset($tr['paid_at']) && trim((string) $tr['paid_at']) !== '';
                $hasPayhere = isset($tr['payhere_payment_id']) && trim((string) $tr['payhere_payment_id']) !== '';
                $hasBank = isset($tr['bank_transfer_submitted_at']) && trim((string) $tr['bank_transfer_submitted_at']) !== '';
                $paidComplete = ($stLc === 'completed' || $stLc === 'confirmed') || $hasPaidAt || $hasPayhere;
                if (!($paidComplete || $hasBank)) {
                    continue;
                }
                $isBankFlow = $hasBank && !$paidComplete;
                $tid = (int) (isset($tr['id']) ? $tr['id'] : 0);
                $budget = isset($tr['budget_lkr']) ? (float) $tr['budget_lkr'] : 0.0;
                $travelers = isset($tr['number_of_people']) ? (int) $tr['number_of_people'] : 0;
                $dest = isset($tr['destination']) ? trim((string) $tr['destination']) : '';
                $custName = isset($tr['customer_name']) ? trim((string) $tr['customer_name']) : '';
                $refundRequested = !empty($tr['refund_requested_at']);
                $paidAtRaw = isset($tr['paid_at']) ? trim((string) $tr['paid_at']) : '';
                $refundEligible = false;
                $refundDeadlineTs = null;
                if ($paidComplete && $paidAtRaw !== '' && !$refundRequested && !$isBankFlow) {
                    $pt = strtotime($paidAtRaw);
                    if ($pt !== false) {
                        $refundDeadlineTs = $pt + (3 * 86400);
                        $refundEligible = time() <= (int) $refundDeadlineTs;
                    }
                }
                $dateRaw = '';
                if ($isBankFlow && $hasBank) {
                    $dateRaw = (string) $tr['bank_transfer_submitted_at'];
                } elseif ($paidAtRaw !== '') {
                    $dateRaw = $paidAtRaw;
                } elseif (isset($tr['start_date'])) {
                    $dateRaw = (string) $tr['start_date'];
                }
                $dateLabel = '';
                if ($dateRaw !== '') {
                    $dts = strtotime($dateRaw);
                    $dateLabel = $dts !== false ? date('F j, Y \a\t g:i A', $dts) : $dateRaw;
                }
                $custom_trips[] = array(
                    'id' => $tid,
                    'destination' => $dest,
                    'customer_name' => $custName,
                    'travelers' => $travelers,
                    'budget_lkr' => $budget,
                    'is_bank_submitted' => $isBankFlow,
                    'payment_completed' => $paidComplete,
                    'date_label' => $dateLabel,
                    'refund_requested' => $refundRequested,
                    'refund_requested_at' => isset($tr['refund_requested_at']) ? trim((string) $tr['refund_requested_at']) : '',
                    'refund_eligible' => $refundEligible,
                    'paid_at_raw' => $paidAtRaw,
                    'refund_deadline_ts' => $refundDeadlineTs,
                );
            }
        }

        $payment_message = $_SESSION['payment_message'] ?? null;
        $payment_error = $_SESSION['payment_error'] ?? null;
        $payment_info = $_SESSION['payment_info'] ?? null;
        unset($_SESSION['payment_message'], $_SESSION['payment_error'], $_SESSION['payment_info']);
        
        view('tourist/my_bookings', [
            'bookings' => $bookings,
            'custom_trips' => $custom_trips,
            'tourist_email' => $tourist_email,
            'bookings_custom_only' => $bookings_custom_only,
            'payment_message' => $payment_message,
            'payment_error' => $payment_error,
            'payment_info' => $payment_info,
        ]);
    }

    public function bookingApprove() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
            header('Location: /CeylonGo/public/login');
            exit;
        }
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if ($id === '' || !isset($_SESSION['pending_bookings']) || !is_array($_SESSION['pending_bookings'])) {
            header('Location: /CeylonGo/public/tourist/my-bookings');
            exit;
        }
        $current_user_id = (int) $_SESSION['user_id'];
        foreach ($_SESSION['pending_bookings'] as &$b) {
            if (isset($b['id']) && $b['id'] === $id && isset($b['user_id']) && (int) $b['user_id'] === $current_user_id) {
                $b['status'] = 'approved';
                break;
            }
        }
        unset($b);
        header('Location: /CeylonGo/public/tourist/my-bookings');
        exit;
    }

    public function payment() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
            header('Location: /CeylonGo/public/login?redirect=' . urlencode('/CeylonGo/public/tourist/my-bookings'));
            exit;
        }
        $booking = null;
        $booking_id = isset($_GET['booking_id']) ? (int) $_GET['booking_id'] : 0;
        $current_user_id = (int) $_SESSION['user_id'];
        
        if ($booking_id > 0) {
            try {
                $sql = "SELECT * FROM package_bookings WHERE id = ? AND user_id = ? AND status IN ('approved', 'paid') LIMIT 1";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$booking_id, $current_user_id]);
                $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Error fetching booking: " . $e->getMessage());
            }
        }
        
        $payhereMax = defined('PAYHERE_PER_TRANSACTION_MAX_LKR') ? (int) PAYHERE_PER_TRANSACTION_MAX_LKR : 0;
        $bankDetails = defined('BANK_TRANSFER_DETAILS') ? BANK_TRANSFER_DETAILS : '';
        view('tourist/payment', [
            'booking' => $booking,
            'payhere_per_transaction_max_lkr' => $payhereMax,
            'bank_transfer_details' => $bankDetails,
        ]);
    }

    /**
     * Start PayHere hosted checkout (POST booking_id, payment_method).
     */
    public function paymentCheckout() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
            header('Location: /CeylonGo/public/login?redirect=' . urlencode('/CeylonGo/public/tourist/my-bookings'));
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /CeylonGo/public/tourist/my-bookings');
            exit;
        }

        $booking_id = isset($_POST['booking_id']) ? (int) $_POST['booking_id'] : 0;
        $method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';
        $current_user_id = (int) $_SESSION['user_id'];

        if ($booking_id <= 0) {
            header('Location: /CeylonGo/public/tourist/my-bookings');
            exit;
        }

        if ($method === 'bank-transfer') {
            try {
                $sql = "SELECT id, status FROM package_bookings WHERE id = ? AND user_id = ? AND status = 'approved' LIMIT 1";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$booking_id, $current_user_id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log('paymentCheckout bank-transfer: ' . $e->getMessage());
                $_SESSION['payment_error'] = 'Could not update booking. Please try again.';
                header('Location: /CeylonGo/public/tourist/payment?booking_id=' . $booking_id);
                exit;
            }
            if (!$row) {
                $_SESSION['payment_error'] = 'This booking cannot be updated.';
                header('Location: /CeylonGo/public/tourist/my-bookings');
                exit;
            }

            $file = isset($_FILES['bank_transfer_slip']) ? $_FILES['bank_transfer_slip'] : null;
            if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                $_SESSION['payment_error'] = 'Please upload a screenshot of your bank transfer slip.';
                header('Location: /CeylonGo/public/tourist/payment?booking_id=' . $booking_id);
                exit;
            }
            $slipPath = $this->saveBankTransferSlip($booking_id, $file);
            if ($slipPath === null) {
                $_SESSION['payment_error'] = 'Please upload a JPG, PNG, or WebP image (max 5 MB).';
                header('Location: /CeylonGo/public/tourist/payment?booking_id=' . $booking_id);
                exit;
            }

            try {
                $upd = $this->db->prepare(
                    'UPDATE package_bookings SET bank_transfer_submitted_at = NOW(), bank_transfer_slip_path = ? WHERE id = ? AND user_id = ? AND status = \'approved\''
                );
                $upd->execute([$slipPath, $booking_id, $current_user_id]);
            } catch (PDOException $e) {
                error_log('paymentCheckout bank_transfer_submitted_at: ' . $e->getMessage());
                @unlink((defined('UPLOADS_PATH') ? UPLOADS_PATH : (dirname(__DIR__) . '/public/uploads')) . '/' . str_replace('\\', '/', $slipPath));
                $_SESSION['payment_error'] = 'Database update failed. If this persists, run: database/migrate_bank_transfer_slip_path.sql (and migrate_bank_transfer_submitted_at.sql if needed).';
                header('Location: /CeylonGo/public/tourist/payment?booking_id=' . $booking_id);
                exit;
            }
            $_SESSION['payment_info'] = 'Your booking stays approved until we confirm the payment (usually within 1ΓÇô2 business days).';
            header('Location: /CeylonGo/public/tourist/my-bookings');
            exit;
        }

        try {
            $sql = "SELECT * FROM package_bookings WHERE id = ? AND user_id = ? AND status = 'approved' LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$booking_id, $current_user_id]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("paymentCheckout: " . $e->getMessage());
            $_SESSION['payment_error'] = 'Could not load booking.';
            header('Location: /CeylonGo/public/tourist/payment?booking_id=' . $booking_id);
            exit;
        }

        if (!$booking) {
            $_SESSION['payment_error'] = 'This booking cannot be paid online.';
            header('Location: /CeylonGo/public/tourist/my-bookings');
            exit;
        }

        $email = trim((string) ($booking['email'] ?? ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['payment_error'] = 'Add a valid email to your booking profile before paying online.';
            header('Location: /CeylonGo/public/tourist/payment?booking_id=' . $booking_id);
            exit;
        }
        $phoneDigits = preg_replace('/\D/', '', (string) ($booking['phone'] ?? ''));
        if (strlen($phoneDigits) < 9) {
            $_SESSION['payment_error'] = 'Add a valid phone number (at least 9 digits) to your booking before paying online.';
            header('Location: /CeylonGo/public/tourist/payment?booking_id=' . $booking_id);
            exit;
        }

        $payhereCap = defined('PAYHERE_PER_TRANSACTION_MAX_LKR') ? (int) PAYHERE_PER_TRANSACTION_MAX_LKR : 0;
        $totalLkr = (float) $booking['total_amount'];
        if ($payhereCap > 0 && $totalLkr > $payhereCap + 0.001) {
            $_SESSION['payment_error'] = sprintf(
                'Online card payment is limited to LKR %s per transaction on this payment account. Your booking is LKR %s. Use Bank transfer below, or ask your business to raise the PayHere limit (dashboard / plan upgrade).',
                number_format($payhereCap),
                number_format($totalLkr, 2, '.', ',')
            );
            header('Location: /CeylonGo/public/tourist/payment?booking_id=' . $booking_id);
            exit;
        }

        $merchantId = trim((string) PAYHERE_MERCHANT_ID);
        $secret = trim((string) PAYHERE_MERCHANT_SECRET);
        $orderId = 'PKG' . $booking_id . 'T' . time();
        $currency = 'LKR';
        $amount = number_format((float) $booking['total_amount'], 2, '.', '');

        $hash = PayHere::checkoutHash($merchantId, $orderId, $amount, $currency, $secret);

        $fullname = trim((string) ($booking['fullname'] ?? 'Customer'));
        $nameParts = preg_split('/\s+/', $fullname, 2);
        $firstName = $nameParts[0] ?: 'Customer';
        $lastName = isset($nameParts[1]) ? $nameParts[1] : 'N/A';

        $itemsName = (string) ($booking['package_name'] ?? 'Package booking');
        if (function_exists('iconv')) {
            $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $itemsName);
            if ($ascii !== false) {
                $itemsName = $ascii;
            }
        }
        $itemsName = preg_replace('/\s+/', ' ', $itemsName);
        $itemsName = mb_substr(trim($itemsName), 0, 120) ?: 'Package booking';

        $fields = [
            'merchant_id' => $merchantId,
            'return_url' => app_absolute_url('tourist/payment/return') . '?',
            'cancel_url' => app_absolute_url('tourist/payment?booking_id=' . $booking_id),
            'notify_url' => app_absolute_url('tourist/payment/notify'),
            'order_id' => $orderId,
            'items' => $itemsName,
            'currency' => $currency,
            'amount' => $amount,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phoneDigits,
            'address' => 'N/A',
            'city' => 'Colombo',
            'country' => 'Sri Lanka',
            'hash' => $hash,
            'custom_1' => (string) $booking_id,
        ];

        $checkoutUrl = PayHere::checkoutUrl((bool) PAYHERE_SANDBOX);

        $_SESSION['payhere_pending_booking_id'] = (int) $booking_id;
        $_SESSION['payhere_pending_order_id'] = $orderId;

        view('tourist/payhere_redirect', [
            'checkout_url' => $checkoutUrl,
            'fields' => $fields,
        ]);
    }

    /**
     * Browser return after PayHere. Some setups send no query string on return (notify POST is authoritative).
     */
    public function paymentReturn() {
        $q = $this->payHereCollectReturnParams();

        $applied = false;
        $returnTripId = 0;
        try {
            $oid = isset($q['order_id']) ? trim((string) $q['order_id']) : '';
            if ($oid !== '' && preg_match('/^CTRIP(\d+)T\d+$/', $oid, $m)) {
                $returnTripId = (int) $m[1];
            }
        } catch (\Throwable $eOid) {
            $returnTripId = 0;
        }
        if (!empty($q['md5sig']) && PayHere::notifyValid($q, (string) PAYHERE_MERCHANT_SECRET)) {
            if ((int) ($q['status_code'] ?? 0) === 2) {
                $applied = $this->payHereCompleteCustomTripPayment($q);
                if (!$applied) {
                    $applied = $this->payHereCompleteApprovedBooking($q);
                }
            }
        }

        // Sandbox: notify cannot reach localhost; return often has no md5sig or only status_code=0 ΓÇö still mark pending booking paid when not an explicit decline.
        if (!$applied && (bool) PAYHERE_SANDBOX && defined('PAYHERE_SANDBOX_TRUST_EMPTY_RETURN') && PAYHERE_SANDBOX_TRUST_EMPTY_RETURN) {
            $sc = isset($q['status_code']) ? (int) $q['status_code'] : null;
            $explicitFailure = ($sc === -1 || $sc === -2 || $sc === -3);
            if (!$explicitFailure) {
                $bid = (int) ($_SESSION['payhere_pending_booking_id'] ?? 0);
                $uid = (int) ($_SESSION['user_id'] ?? 0);
                if ($bid > 0 && $uid > 0 && $this->payHereSandboxCompletePendingBooking($bid, $uid)) {
                    $applied = true;
                    unset($_SESSION['payhere_pending_booking_id'], $_SESSION['payhere_pending_order_id']);
                }
            }
        }

        if (!$applied && (bool) PAYHERE_SANDBOX && defined('PAYHERE_SANDBOX_TRUST_EMPTY_RETURN') && PAYHERE_SANDBOX_TRUST_EMPTY_RETURN) {
            $scTrip = isset($q['status_code']) ? (int) $q['status_code'] : null;
            $explicitFailTrip = ($scTrip === -1 || $scTrip === -2 || $scTrip === -3);
            $tripPend = (int) ($_SESSION['payhere_pending_trip_id'] ?? 0);
            if (!$explicitFailTrip && $returnTripId > 0 && $tripPend === $returnTripId && $this->payHereSandboxCompletePendingTrip($returnTripId)) {
                $applied = true;
                unset($_SESSION['payhere_pending_trip_order_id'], $_SESSION['payhere_pending_trip_id']);
            }
        }

        if ($applied) {
            unset($_SESSION['payhere_pending_booking_id'], $_SESSION['payhere_pending_order_id']);
            $_SESSION['payment_message'] = 'Payment completed successfully.';
        } else {
            $status = isset($q['status_code']) ? (int) $q['status_code'] : null;
            if ($status === 2) {
                unset($_SESSION['payhere_pending_booking_id'], $_SESSION['payhere_pending_order_id']);
                $_SESSION['payment_message'] = 'PayHere reported success. If your booking is not Paid yet, wait a few seconds and refresh.';
            } elseif ($status === -1) {
                unset($_SESSION['payhere_pending_booking_id'], $_SESSION['payhere_pending_order_id']);
                $_SESSION['payment_error'] = 'Payment was cancelled.';
            } elseif ($status === 0) {
                $_SESSION['payment_message'] = 'Payment is still processing. Refresh My Bookings shortly.';
            } elseif ($status === -2 || $status === -3) {
                unset($_SESSION['payhere_pending_booking_id'], $_SESSION['payhere_pending_order_id']);
                $_SESSION['payment_error'] = 'Payment was declined or reversed at the gateway.';
            } elseif ($status === null) {
                $this->payHereMessageAfterEmptyReturn();
            } else {
                unset($_SESSION['payhere_pending_booking_id'], $_SESSION['payhere_pending_order_id']);
                $_SESSION['payment_error'] = 'Payment was not completed. If money was debited, contact support with your receipt.';
            }
        }

        error_log('PayHERE return: method=' . ($_SERVER['REQUEST_METHOD'] ?? '') . ' qs=' . ($_SERVER['QUERY_STRING'] ?? '') . ' keys=' . implode(',', array_keys($q)));

        // If this return is for a custom trip, always go back to Trip Overview.
        if ($returnTripId > 0) {
            // Only tourists can view customise-trip; otherwise force login.
            $role = isset($_SESSION['user_role']) ? (string) $_SESSION['user_role'] : '';
            if (!isset($_SESSION['user_id']) || $role !== 'tourist') {
                header('Location: /CeylonGo/public/login?redirect=' . urlencode('/CeylonGo/public/tourist/customize-trip?afterPayment=1&trip_id=' . $returnTripId));
                exit;
            }
            header('Location: /CeylonGo/public/tourist/customize-trip?afterPayment=1&trip_id=' . (int) $returnTripId);
            exit;
        }

        if (!empty($_SESSION['payhere_pending_trip_id'])) {
            $paidTripId = (int) $_SESSION['payhere_pending_trip_id'];
            unset($_SESSION['payhere_pending_trip_order_id'], $_SESSION['payhere_pending_trip_id']);
            $role = isset($_SESSION['user_role']) ? (string) $_SESSION['user_role'] : '';
            if (!isset($_SESSION['user_id']) || $role !== 'tourist') {
                header('Location: /CeylonGo/public/login?redirect=' . urlencode('/CeylonGo/public/tourist/customize-trip'));
                exit;
            }
            // Send user back to Review & Submit after returning from PayHere.
            header('Location: /CeylonGo/public/tourist/customize-trip?afterPayment=1&trip_id=' . (int) $paidTripId);
            exit;
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
            header('Location: /CeylonGo/public/login?redirect=' . urlencode('/CeylonGo/public/tourist/my-bookings'));
            exit;
        }
        header('Location: /CeylonGo/public/tourist/my-bookings');
        exit;
    }

    /**
     * Merge GET/POST/query string from multiple server vars (some Apache setups omit $_GET).
     */
    private function payHereCollectReturnParams(): array {
        $q = array_merge($_GET, $_POST);
        foreach (['QUERY_STRING', 'REDIRECT_QUERY_STRING'] as $sk) {
            if (!empty($_SERVER[$sk])) {
                parse_str((string) $_SERVER[$sk], $p);
                if (is_array($p)) {
                    $q = array_merge($q, $p);
                }
            }
        }
        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '');
        $parsedQs = parse_url($uri, PHP_URL_QUERY);
        if (is_string($parsedQs) && $parsedQs !== '') {
            parse_str($parsedQs, $p2);
            if (is_array($p2)) {
                $q = array_merge($q, $p2);
            }
        }
        if (empty($q) && strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? '')) === 'POST') {
            $raw = file_get_contents('php://input');
            if (is_string($raw) && $raw !== '') {
                parse_str($raw, $parsed);
                if (is_array($parsed)) {
                    $q = array_merge($q, $parsed);
                }
            }
        }
        return $this->payHereNormalizeParams($q);
    }

    /**
     * Sandbox/local: mark approved booking paid when return has no gateway payload (session must match checkout).
     */
    private function payHereSandboxCompletePendingBooking(int $bookingId, int $userId): bool {
        try {
            $pid = 'sandbox-empty-return-' . bin2hex(random_bytes(8));
            $stmt = $this->db->prepare(
                'UPDATE package_bookings SET status = \'paid\', payhere_payment_id = ?, paid_at = NOW()
                 WHERE id = ? AND user_id = ? AND status = \'approved\''
            );
            $stmt->execute([$pid, $bookingId, $userId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('payHereSandboxCompletePendingBooking: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sandbox: mark customise-trip row paid when return cannot verify md5 (localhost) but session matches checkout.
     */
    private function payHereSandboxCompletePendingTrip(int $tripId): bool {
        $tripId = (int) $tripId;
        if ($tripId <= 0) {
            return false;
        }
        try {
            $pid = 'sandbox-empty-return-' . bin2hex(random_bytes(8));
            $stmt = $this->db->prepare(
                'UPDATE trips SET status = \'completed\', payhere_payment_id = ?, paid_at = NOW()
                 WHERE id = ? AND status = \'pending\''
            );
            $stmt->execute([$pid, $tripId]);
            $ok = $stmt->rowCount() > 0;
            if ($ok) {
                $stmtU = $this->db->prepare('SELECT user_id FROM trips WHERE id = ? LIMIT 1');
                $stmtU->execute([$tripId]);
                $urow = $stmtU->fetch(PDO::FETCH_ASSOC);
                $uid = $urow && isset($urow['user_id']) ? (int) $urow['user_id'] : 0;
                if ($uid > 0) {
                    $this->tripSubmissionSetPaymentStatus($tripId, $uid, 'completed');
                }
            }
            return $ok;
        } catch (PDOException $e) {
            error_log('payHereSandboxCompletePendingTrip: ' . $e->getMessage());
            return false;
        }
    }

    private function payHereMessageAfterEmptyReturn(): void {
        $bid = isset($_SESSION['payhere_pending_booking_id']) ? (int) $_SESSION['payhere_pending_booking_id'] : 0;
        $uid = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
        if ($bid > 0 && $uid > 0) {
            try {
                $stmt = $this->db->prepare('SELECT id, status FROM package_bookings WHERE id = ? AND user_id = ? LIMIT 1');
                $stmt->execute([$bid, $uid]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row && $row['status'] === 'paid') {
                    unset($_SESSION['payhere_pending_booking_id'], $_SESSION['payhere_pending_order_id']);
                    $_SESSION['payment_message'] = 'Payment completed. Your booking is marked Paid.';
                    return;
                }
            } catch (PDOException $e) {
                error_log('payHereMessageAfterEmptyReturn: ' . $e->getMessage());
            }
        }
        // Do not show technical PayHere/notify details to end users.
        // On localhost/sandbox, return often has no parameters and notify cannot reach.
        // If status isn't updated yet, just show a neutral message.
        $_SESSION['payment_message'] = 'Payment received. If your booking status has not updated yet, please refresh in a moment.';
    }

    private function payHereNormalizeParams(array $raw): array {
        $out = [];
        foreach ($raw as $k => $v) {
            if (is_array($v)) {
                continue;
            }
            $out[strtolower((string) $k)] = is_string($v) ? trim($v) : $v;
        }
        return $out;
    }

    /**
     * Normalise PayHere amount strings for comparison with trip budget.
     */
    private function payHereParseAmountLkr($raw): float {
        if (is_numeric($raw)) {
            return (float) $raw;
        }
        $s = preg_replace('/[^\d.\-]/', '', (string) $raw);
        return $s === '' ? 0.0 : (float) $s;
    }

    /**
     * PayHere notify / return: custom trip payment (order_id CTRIP{n}T...). Sets trips.status to completed.
     */
    private function payHereCompleteCustomTripPayment(array $post): bool {
        $orderId = trim((string) ($post['order_id'] ?? ''));
        if (!preg_match('/^CTRIP(\d+)T\d+$/', $orderId, $m)) {
            return false;
        }
        $tripId = (int) $m[1];
        $paymentId = isset($post['payment_id']) ? trim((string) $post['payment_id']) : '';
        if ($paymentId === '' || $tripId <= 0) {
            return false;
        }
        $paidF = $this->payHereParseAmountLkr($post['payhere_amount'] ?? '');
        try {
            $stmt = $this->db->prepare(
                'SELECT id, user_id, budget_lkr, status, payhere_payment_id FROM trips WHERE id = ? LIMIT 1'
            );
            $stmt->execute(array($tripId));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return false;
            }
            if (!empty($row['payhere_payment_id']) && (string) $row['payhere_payment_id'] === $paymentId) {
                return true;
            }
            if (($row['status'] ?? '') !== 'pending') {
                return false;
            }
            $uid = isset($row['user_id']) ? (int) $row['user_id'] : 0;
            $budget = isset($row['budget_lkr']) ? (float) $row['budget_lkr'] : 0.0;
            if ($budget <= 0.0 && $uid > 0) {
                $budget = $this->getTripBudgetFromSubmission($tripId, $uid);
            }
            if ($paidF <= 0.0) {
                error_log('PayHere custom trip: missing payhere_amount for trip ' . $tripId);
                return false;
            }
            if ($budget > 0.01) {
                if (abs($paidF - $budget) > 1.0) {
                    error_log(
                        'PayHere custom trip: amount mismatch trip ' . $tripId
                        . ' paid=' . $paidF . ' expected_budget=' . $budget
                    );
                    return false;
                }
            }
            $upd = $this->db->prepare(
                'UPDATE trips SET status = \'completed\', payhere_payment_id = ?, paid_at = NOW() WHERE id = ? AND status = \'pending\''
            );
            $upd->execute(array($paymentId, $tripId));
            $ok = $upd->rowCount() > 0;
            if ($ok) {
                try {
                    if ($uid > 0) {
                        $this->tripSubmissionSetPaymentStatus($tripId, $uid, 'completed');
                    }
                } catch (\Throwable $e2) {
                }
            }
            return $ok;
        } catch (PDOException $e) {
            error_log('payHereCompleteCustomTripPayment: ' . $e->getMessage());
            return false;
        }
    }

    private function payHereCompleteApprovedBooking(array $post): bool {
        $orderIdEarly = trim((string) ($post['order_id'] ?? ''));
        if ($orderIdEarly !== '' && preg_match('/^CTRIP\d+T\d+$/', $orderIdEarly)) {
            return false;
        }
        $bookingId = isset($post['custom_1']) ? (int) $post['custom_1'] : 0;
        if ($bookingId <= 0 && !empty($post['order_id']) && preg_match('/^PKG(\d+)T\d+$/', (string) $post['order_id'], $m)) {
            $bookingId = (int) $m[1];
        }
        $paymentId = isset($post['payment_id']) ? trim((string) $post['payment_id']) : '';
        $payAmount = isset($post['payhere_amount']) ? (string) $post['payhere_amount'] : '';
        if ($bookingId <= 0 || $paymentId === '') {
            error_log('PayHere complete: missing booking_id or payment_id');
            return false;
        }
        try {
            $stmt = $this->db->prepare('SELECT id, total_amount, status, payhere_payment_id FROM package_bookings WHERE id = ? LIMIT 1');
            $stmt->execute([$bookingId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return false;
            }
            if ($row['status'] === 'paid' && (string) $row['payhere_payment_id'] === $paymentId) {
                return true;
            }
            if ($row['status'] !== 'approved') {
                return false;
            }
            $expected = number_format((float) $row['total_amount'], 2, '.', '');
            if ($payAmount !== $expected) {
                error_log('PayHere complete: amount mismatch booking ' . $bookingId);
                return false;
            }
            $upd = $this->db->prepare(
                'UPDATE package_bookings SET status = \'paid\', payhere_payment_id = ?, paid_at = NOW() WHERE id = ? AND status = \'approved\''
            );
            $upd->execute([$paymentId, $bookingId]);
            return true;
        } catch (PDOException $e) {
            error_log('PayHere complete DB: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * PayHere server-to-server notification (needs a public URL; localhost usually cannot receive this).
     */
    public function paymentNotify() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $post = $this->payHereNormalizeParams($_POST);
        if (!PayHere::notifyValid($post, (string) PAYHERE_MERCHANT_SECRET)) {
            error_log('PayHere notify: invalid md5sig');
            http_response_code(400);
            echo 'INVALID';
            return;
        }

        $statusCode = isset($post['status_code']) ? (int) $post['status_code'] : 0;
        if ($statusCode !== 2) {
            header('Content-Type: text/plain; charset=UTF-8');
            echo 'OK';
            return;
        }

        if ($this->payHereCompleteCustomTripPayment($post)) {
            header('Content-Type: text/plain; charset=UTF-8');
            echo 'OK';
            return;
        }

        if (!$this->payHereCompleteApprovedBooking($post)) {
            error_log('PayHere notify: payHereCompleteApprovedBooking returned false');
        }

        header('Content-Type: text/plain; charset=UTF-8');
        echo 'OK';
    }

    public function tripSummary() {
        view('tourist/trip_summary');
    }

    /**
     * Paid package booking: load booking + package and compute accommodation/itinerary breakdown.
     *
     * @return array{booking: array, package: ?array, price_adult_unit: int, price_child_unit: int, price_infant_unit: int, accommodation: array, itinerary: array}|null
     */
    private function getPaidPackageBookingSummaryData(int $booking_id, int $user_id): ?array {
        try {
            $stmt = $this->db->prepare(
                'SELECT * FROM package_bookings WHERE id = ? AND user_id = ? AND status = \'paid\' LIMIT 1'
            );
            $stmt->execute([$booking_id, $user_id]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('getPaidPackageBookingSummaryData: ' . $e->getMessage());
            return null;
        }
        if (!$booking) {
            return null;
        }

        $packageModel = new Package($this->db);
        $package = $packageModel->getById((int) $booking['package_id']);

        $priceAdult = $package ? (int) $package['price'] : 0;
        $cr = $package ? (float) ($package['price_child_ratio'] ?? 0.5) : 0.5;
        $ir = $package ? (float) ($package['price_infant_ratio'] ?? 0) : 0;
        $childUnit = (int) round($priceAdult * $cr);
        $infantUnit = (int) round($priceAdult * $ir);

        $accommodation = [];
        if ($package && !empty($package['accommodation']) && is_array($package['accommodation'])) {
            $dayCursor = 1;
            foreach ($package['accommodation'] as $seg) {
                $nights = isset($seg['nights']) ? max(0, (int) $seg['nights']) : 0;
                if ($nights < 1) {
                    continue;
                }
                $endDay = $dayCursor + $nights - 1;
                if ($dayCursor === $endDay) {
                    $rangeLabel = 'Day ' . $dayCursor;
                } else {
                    $rangeLabel = 'Day ' . $dayCursor . ' to Day ' . $endDay;
                }
                $accommodation[] = [
                    'hotel' => (string) ($seg['hotel'] ?? ''),
                    'location' => (string) ($seg['location'] ?? ''),
                    'range_label' => $rangeLabel,
                ];
                $dayCursor = $endDay + 1;
            }
        }

        $itinerary = [];
        if ($package && !empty($package['itinerary']) && is_array($package['itinerary'])) {
            $itinerary = package_itinerary_for_tourist_display($package['itinerary']);
        }

        return [
            'booking' => $booking,
            'package' => $package,
            'price_adult_unit' => $priceAdult,
            'price_child_unit' => $childUnit,
            'price_infant_unit' => $infantUnit,
            'accommodation' => $accommodation,
            'itinerary' => $itinerary,
        ];
    }

    /**
     * Trip summary for a paid package booking: travel date, pricing breakdown, accommodation, itinerary (from package).
     */
    public function packageBookingTripSummary() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
            header('Location: /CeylonGo/public/login?redirect=' . urlencode('/CeylonGo/public/tourist/my-bookings'));
            exit;
        }
        $booking_id = isset($_GET['booking_id']) ? (int) $_GET['booking_id'] : 0;
        if ($booking_id <= 0) {
            header('Location: /CeylonGo/public/tourist/my-bookings');
            exit;
        }
        $uid = (int) $_SESSION['user_id'];
        $data = $this->getPaidPackageBookingSummaryData($booking_id, $uid);
        if (!$data) {
            header('Location: /CeylonGo/public/tourist/my-bookings');
            exit;
        }

        view('tourist/package_booking_trip_summary', [
            'booking' => $data['booking'],
            'package' => $data['package'],
            'price_adult_unit' => $data['price_adult_unit'],
            'price_child_unit' => $data['price_child_unit'],
            'price_infant_unit' => $data['price_infant_unit'],
            'accommodation' => $data['accommodation'],
            'itinerary' => $data['itinerary'],
        ]);
    }

    /**
     * JSON for trip summary modal (My Bookings ΓÇö paid bookings only).
     */
    public function packageBookingTripSummaryJson() {
        header('Content-Type: application/json; charset=UTF-8');
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
            http_response_code(401);
            echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
            return;
        }
        $booking_id = isset($_GET['booking_id']) ? (int) $_GET['booking_id'] : 0;
        if ($booking_id <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Invalid booking']);
            return;
        }
        $data = $this->getPaidPackageBookingSummaryData($booking_id, (int) $_SESSION['user_id']);
        if (!$data) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Not found']);
            return;
        }
        $booking = $data['booking'];
        $package = $data['package'];
        $travel_date = isset($booking['travel_date']) ? $booking['travel_date'] : '';
        $travel_date_fmt = $travel_date !== '' ? date('F j, Y', strtotime($travel_date)) : 'ΓÇö';
        $ta = (int) ($booking['travelers'] ?? 0);
        $ad = (int) ($booking['adults'] ?? 0);
        $ch = (int) ($booking['children'] ?? 0);
        $inf = (int) ($booking['infants'] ?? 0);
        $parts = [$ad . ' adult' . ($ad !== 1 ? 's' : '')];
        if ($ch > 0) {
            $parts[] = $ch . ' child' . ($ch !== 1 ? 'ren' : '');
        }
        if ($inf > 0) {
            $parts[] = $inf . ' infant' . ($inf !== 1 ? 's' : '');
        }
        $travelers_text = $ta . ' (' . implode(', ', $parts) . ')';
        $pkg_title = ($package && !empty($package['title'])) ? (string) $package['title'] : (string) ($booking['package_name'] ?? 'Your trip');
        $duration_line = $package ? (string) ($package['duration'] ?? ($package['duration_short'] ?? '')) : '';

        echo json_encode([
            'ok' => true,
            'package_title' => $pkg_title,
            'duration_line' => $duration_line,
            'travel_date_formatted' => $travel_date_fmt,
            'travelers_text' => $travelers_text,
            'price_adult_unit' => $data['price_adult_unit'],
            'price_child_unit' => $data['price_child_unit'],
            'price_infant_unit' => $data['price_infant_unit'],
            'total_lkr' => (int) round((float) ($booking['total_amount'] ?? 0)),
            'accommodation' => $data['accommodation'],
            'itinerary' => $data['itinerary'],
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Submit refund request (within 3 days of paid_at).
     */
    public function packageBookingRefundRequest() {
        header('Content-Type: application/json; charset=UTF-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
            return;
        }
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
            http_response_code(401);
            echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
            return;
        }
        $booking_id = isset($_POST['booking_id']) ? (int) $_POST['booking_id'] : 0;
        $reason = isset($_POST['reason']) ? trim((string) $_POST['reason']) : '';
        if (strlen($reason) > 2000) {
            $reason = substr($reason, 0, 2000);
        }
        if ($booking_id <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Invalid booking']);
            return;
        }
        $uid = (int) $_SESSION['user_id'];
        try {
            $stmt = $this->db->prepare(
                'SELECT * FROM package_bookings WHERE id = ? AND user_id = ? AND status = \'paid\' LIMIT 1'
            );
            $stmt->execute([$booking_id, $uid]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('packageBookingRefundRequest: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => 'Server error']);
            return;
        }
        if (!$row) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Booking not found']);
            return;
        }
        if (!empty($row['refund_requested_at'])) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'A refund request was already submitted for this booking.']);
            return;
        }
        $paidAt = $row['paid_at'] ?? null;
        if (!$paidAt) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Payment date is not recorded for this booking. Please contact support.']);
            return;
        }
        $deadline = strtotime($paidAt) + (3 * 86400);
        if (time() > $deadline) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'The 3-day refund window from your payment has ended.']);
            return;
        }
        try {
            $upd = $this->db->prepare(
                'UPDATE package_bookings SET refund_requested_at = NOW(), refund_reason = ? WHERE id = ? AND user_id = ? AND status = \'paid\' AND refund_requested_at IS NULL'
            );
            $upd->execute([$reason !== '' ? $reason : null, $booking_id, $uid]);
            if ($upd->rowCount() === 0) {
                http_response_code(409);
                echo json_encode(['ok' => false, 'error' => 'Could not submit refund request. Please refresh and try again.']);
                return;
            }
        } catch (PDOException $e) {
            error_log('packageBookingRefundRequest update: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => 'Could not save request. Run database/migrate_package_bookings_refund.sql if columns are missing.']);
            return;
        }
        echo json_encode([
            'ok' => true,
            'message' => 'Your refund request has been submitted. Our team will contact you using your booking email.',
        ]);
    }

    /**
     * Custom trip: submit refund request within 3 days of paid_at.
     */
    public function customTripRefundRequest() {
        header('Content-Type: application/json; charset=UTF-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
            return;
        }
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
            http_response_code(401);
            echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
            return;
        }
        $trip_id = isset($_POST['trip_id']) ? (int) $_POST['trip_id'] : 0;
        $reason = isset($_POST['reason']) ? trim((string) $_POST['reason']) : '';
        if (strlen($reason) > 2000) {
            $reason = substr($reason, 0, 2000);
        }
        if ($trip_id <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Invalid trip']);
            return;
        }
        $uid = (int) $_SESSION['user_id'];
        try {
            $stmt = $this->db->prepare('SELECT * FROM trips WHERE id = ? AND user_id = ? LIMIT 1');
            $stmt->execute([$trip_id, $uid]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('customTripRefundRequest: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => 'Server error']);
            return;
        }
        if (!$row) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Trip not found']);
            return;
        }
        if (!empty($row['refund_requested_at'])) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'A refund request was already submitted for this trip.']);
            return;
        }
        $st = strtolower((string) ($row['status'] ?? ''));
        $paidAt = $row['paid_at'] ?? null;
        $paid = ($st === 'confirmed' || $st === 'completed')
            || !empty($row['payhere_payment_id'])
            || !empty($row['bank_transfer_submitted_at'])
            || ($paidAt && trim((string) $paidAt) !== '');
        if (!$paid) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'This trip is not marked as paid.']);
            return;
        }
        if (!$paidAt || trim((string) $paidAt) === '') {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Payment date is not recorded for this trip. Please contact support.']);
            return;
        }
        $deadline = strtotime($paidAt) + (3 * 86400);
        if (time() > $deadline) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'The 3-day refund window from your payment has ended.']);
            return;
        }
        try {
            $upd = $this->db->prepare(
                'UPDATE trips SET refund_requested_at = NOW(), refund_reason = ? WHERE id = ? AND user_id = ? AND refund_requested_at IS NULL'
            );
            $upd->execute([$reason !== '' ? $reason : null, $trip_id, $uid]);
            if ($upd->rowCount() === 0) {
                http_response_code(409);
                echo json_encode(['ok' => false, 'error' => 'Could not submit refund request. Please refresh and try again.']);
                return;
            }
        } catch (PDOException $e) {
            error_log('customTripRefundRequest update: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => 'Could not save request. Run database/alter_trips_refund_columns.sql if columns are missing.']);
            return;
        }
        echo json_encode([
            'ok' => true,
            'message' => 'Your refund request has been submitted. Our team will contact you using your trip email.',
        ]);
    }

    public function recommendedPackages() {
        view('tourist/recommended_packages');
    }

    public function packages() {
        $packageModel = new Package($this->db);
        $category = isset($_GET['category']) ? trim($_GET['category']) : '';
        $trending = isset($_GET['trending']) && $_GET['trending'] === '1';
        $filters = [];
        if ($trending) $filters['trending'] = true;
        if ($category !== '') $filters['category'] = $category;
        $packages = $packageModel->getAll($filters);
        view('tourist/packages', [
            'packages' => $packages,
            'filter_category' => $category,
            'filter_trending' => $trending,
        ]);
    }

    public function packageDetails($id) {
        $package = $this->getPackageDetailById((int) $id);
        if (!$package) {
            header('Location: /CeylonGo/public/tourist/packages');
            exit;
        }
        $reviewModel = new Review($this->db);
        $package_reviews = $reviewModel->getApprovedForPackage((int) $package['id']);
        view('tourist/package_details', ['package' => $package, 'package_reviews' => $package_reviews]);
    }

    public function packageDetailsQuery() {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 1;
        $package = $this->getPackageDetailById($id);
        if (!$package) {
            header('Location: /CeylonGo/public/tourist/packages');
            exit;
        }
        $reviewModel = new Review($this->db);
        $package_reviews = $reviewModel->getApprovedForPackage((int) $package['id']);
        view('tourist/package_details', ['package' => $package, 'package_reviews' => $package_reviews]);
    }

    /**
     * Full package detail for package details page and booking form. Loads from DB via Package model.
     */
    private function getPackageDetailById($id) {
        $packageModel = new Package($this->db);
        $package = $packageModel->getById((int) $id);
        if ($package) return $package;
        $all = $packageModel->getAll();
        return $all[0] ?? null;
    }

    public function addReview() {
        $packageModel = new Package($this->db);
        $packagesList = $packageModel->getListForDropdown();
        $selected_package_id = isset($_GET['package']) ? (int) $_GET['package'] : null;
        view('tourist/add_review', ['packages' => $packagesList, 'selected_package_id' => $selected_package_id]);
    }

    public function transportProviders() {
        view('tourist/transport_providers');
    }

    public function tourGuideRequest() {
        view('tourist/tour_guide_request');
    }

    public function tourGuideRequestSubmit() {
        $data = $_POST;
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        // Validate required fields
        if (empty($data['customerName']) || empty($data['contact']) || empty($data['location']) ||
            empty($data['language']) || empty($data['date']) || empty($data['time'])) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Please fill in all required fields']);
                exit();
            }
            header("Location: /CeylonGo/public/tourist/dashboard?error=" . urlencode("Please fill in all required fields"));
            exit();
        }

        try {
            // Use the GuideRequest model
            $guideRequest = new GuideRequest($this->db);
            $guideRequest->customerName = $data['customerName'];
            $guideRequest->contactNumber = $data['contact'];
            $guideRequest->location = $data['location'];
            $guideRequest->language = $data['language'];
            $guideRequest->date = $data['date'];
            $guideRequest->time = $data['time'];
            $guideRequest->notes = $data['notes'] ?? '';

            if ($guideRequest->create()) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'location' => trim((string) ($data['location'] ?? '')),
                        'request_id' => (int) $guideRequest->id,
                        'status' => (string) ($guideRequest->status ?? 'pending'),
                    ]);
                    exit();
                }
                header("Location: /CeylonGo/public/tourist/tour-guide-report");
            } else {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => 'Failed to submit request']);
                    exit();
                }
                header("Location: /CeylonGo/public/tourist/dashboard?error=" . urlencode("Failed to submit request"));
            }
            exit();

        } catch (Exception $e) {
            error_log($e->getMessage());
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'An error occurred. Please try again.']);
                exit();
            }
            header("Location: /CeylonGo/public/tourist/dashboard?error=" . urlencode("An error occurred. Please try again."));
            exit();
        }
    }

    public function tourGuideRequestReport() {
        try {
            $guideRequest = new GuideRequest($this->db);
            $requests = $guideRequest->getAll();
            
            // Filter by logged-in user if needed
            if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'tourist') {
                $customer_name = $_SESSION['user_name'] ?? '';
                if (!empty($customer_name)) {
                    $requests = $guideRequest->getByCustomerName($customer_name);
                }
            }
            
            view('tourist/tour_guide_request_report', ['requests' => $requests]);
            
        } catch (Exception $e) {
            error_log("Error fetching tour guide requests: " . $e->getMessage());
            view('tourist/tour_guide_request_report', ['requests' => [], 'error' => 'An error occurred while fetching requests.']);
        }
    }

    public function transportEdit($id) {
        view('tourist/transport_edit', ['request_id' => $id]);
    }

    public function transportDelete($id) {
        // Delete logic would go here if needed
        header("Location: /CeylonGo/public/tourist/transport-report");
        exit();
    }

    public function contact() {
        view('contact');
    }

    /**
     * Profile redirect after POST — keeps ?full=1 when the user edited from the minimal (navbar) profile layout.
     */
    private function touristProfileRedirectUrl(array $post, $errorMessage = null) {
        $qs = array();
        if (!empty($post['full']) && (string) $post['full'] === '1') {
            $qs['full'] = '1';
        }
        if ($errorMessage !== null && $errorMessage !== '') {
            $qs['error'] = $errorMessage;
        }
        $url = '/CeylonGo/public/tourist/profile';
        if (!empty($qs)) {
            $url .= '?' . http_build_query($qs);
        }
        return $url;
    }

    // Profile methods
    public function profile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateProfile();
            return;
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
            header("Location: /CeylonGo/public/login");
            exit();
        }

        $touristModel = new Tourist($this->db);
        $tourist_id = $_SESSION['user_id'];
        $tourist = $touristModel->getTouristById($tourist_id);
        view('tourist/profile', ['tourist' => $tourist]);
    }

    public function updateProfile() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
            header("Location: /CeylonGo/public/login");
            exit();
        }

        $data = $_POST;
        $tourist_id = $_SESSION['user_id'];

        $tourist = new Tourist($this->db);
        $tourist->id = $tourist_id;
        $tourist->first_name = trim($data['first_name'] ?? '');
        $tourist->last_name = trim($data['last_name'] ?? '');
        $tourist->contact_number = trim($data['contact_number'] ?? '');
        $tourist->email = trim($data['email'] ?? '');

        if ($tourist->first_name === '' || $tourist->last_name === '' || $tourist->email === '') {
            header('Location: ' . $this->touristProfileRedirectUrl($data, 'Please fill in first name, last name, and email.'));
            exit();
        }
        if (!filter_var($tourist->email, FILTER_VALIDATE_EMAIL)) {
            header('Location: ' . $this->touristProfileRedirectUrl($data, 'Please enter a valid email address.'));
            exit();
        }

        if (!empty($data['password'])) {
            $authLookup = new AuthUser($this->db);
            $userAuthRow = $authLookup->getUserByRefAndRole($tourist_id, 'tourist');
            $storedHash = '';
            if ($userAuthRow && !empty($userAuthRow['password'])) {
                $storedHash = (string) $userAuthRow['password'];
            } else {
                $existingRow = $tourist->getTouristById($tourist_id);
                $storedHash = isset($existingRow['password']) ? (string) $existingRow['password'] : '';
            }
            $currentPlain = trim($data['current_password'] ?? '');
            if ($currentPlain === '') {
                header('Location: ' . $this->touristProfileRedirectUrl($data, 'Enter your current password to set a new password.'));
                exit();
            }
            if ($storedHash === '' || !password_verify($currentPlain, $storedHash)) {
                header('Location: ' . $this->touristProfileRedirectUrl($data, 'Current password is incorrect.'));
                exit();
            }

            $pw = trim($data['password']);
            $pw2 = trim($data['password_confirm'] ?? '');
            if ($pw !== $pw2) {
                header('Location: ' . $this->touristProfileRedirectUrl($data, 'New passwords do not match.'));
                exit();
            }
            if (strlen($pw) < 6) {
                header('Location: ' . $this->touristProfileRedirectUrl($data, 'Password must be at least 6 characters.'));
                exit();
            }
            $tourist->password = password_hash($pw, PASSWORD_DEFAULT);
        }

        if ($tourist->updateProfile()) {
            // Update users table
            $authUser = new AuthUser($this->db);
            $authUser->ref_id = $tourist_id;
            $authUser->email = $tourist->email;
            $authUser->role = 'tourist';
            if (!empty($data['password'])) {
                $authUser->password = $tourist->password;
            }
            $authUser->updateUser();

            $_SESSION['success'] = "Profile updated successfully!";
            $_SESSION['user_email'] = $tourist->email;
            $_SESSION['user_name'] = $tourist->first_name . ' ' . $tourist->last_name;
            header('Location: ' . $this->touristProfileRedirectUrl($data));
        } else {
            header('Location: ' . $this->touristProfileRedirectUrl($data, 'Failed to update profile'));
        }
        exit();
    }

    // Diary methods
    public function myDiary() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
            header("Location: /CeylonGo/public/login");
            exit();
        }

        $diaryModel = new TripDiary($this->db);
        $tourist_id = $_SESSION['user_id'];
        $entries = $diaryModel->getEntriesByTouristId($tourist_id);
        
        // Get images for each entry
        $allImages = [];
        foreach ($entries as $entry) {
            $images = $this->getDiaryImages($entry['id']);
            $allImages[$entry['id']] = $images;
        }
        
        view('tourist/my_diary', ['entries' => $entries, 'images' => $allImages]);
    }

    public function addDiaryEntry() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
            header("Location: /CeylonGo/public/login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $tourist_id = $_SESSION['user_id'];

            $diary = new TripDiary($this->db);
            $diary->tourist_id = $tourist_id;
            $diary->title = trim($data['title'] ?? '');
            $diary->content = trim($data['content'] ?? '');
            $diary->location = trim($data['location'] ?? '');
            $diary->entry_date = $data['entry_date'] ?? date('Y-m-d');
            $diary->is_public = isset($data['is_public']) ? 1 : 0;

            if ($diary->addEntry()) {
                // Handle image uploads
                if (!empty($_FILES['images']['name'][0])) {
                    $this->uploadDiaryImages($diary->id, $_FILES['images']);
                }
                $_SESSION['success'] = "Diary entry added successfully!";
                header("Location: /CeylonGo/public/tourist/my-diary");
            } else {
                header("Location: /CeylonGo/public/tourist/add-diary-entry?error=" . urlencode("Failed to add entry"));
            }
            exit();
        }

        view('tourist/add_diary_entry');
    }

    public function editDiaryEntry($id) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
            header("Location: /CeylonGo/public/login");
            exit();
        }

        $diaryModel = new TripDiary($this->db);
        $entry = $diaryModel->getEntryById($id);

        if (!$entry || $entry['tourist_id'] != $_SESSION['user_id']) {
            header("Location: /CeylonGo/public/tourist/my-diary?error=" . urlencode("Entry not found"));
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $diary = new TripDiary($this->db);
            $diary->id = $id;
            $diary->tourist_id = $_SESSION['user_id'];
            $diary->title = trim($data['title'] ?? '');
            $diary->content = trim($data['content'] ?? '');
            $diary->location = trim($data['location'] ?? '');
            $diary->entry_date = $data['entry_date'] ?? date('Y-m-d');
            $diary->is_public = isset($data['is_public']) ? 1 : 0;

            if ($diary->updateEntry()) {
                // Handle image uploads
                if (!empty($_FILES['images']['name'][0])) {
                    $this->uploadDiaryImages($id, $_FILES['images']);
                }
                $_SESSION['success'] = "Diary entry updated successfully!";
                header("Location: /CeylonGo/public/tourist/my-diary");
            } else {
                header("Location: /CeylonGo/public/tourist/edit-diary-entry/" . $id . "?error=" . urlencode("Failed to update entry"));
            }
            exit();
        }

        // Get images for this entry
        $images = $this->getDiaryImages($id);
        view('tourist/edit_diary_entry', ['entry' => $entry, 'images' => $images]);
    }

    public function viewDiaryEntry($id) {
        $diaryModel = new TripDiary($this->db);
        $entry = $diaryModel->getEntryById($id);

        if (!$entry) {
            header("Location: /CeylonGo/public/tourist/public-diaries?error=" . urlencode("Entry not found"));
            exit();
        }

        // Check if user can view (public or own entry)
        $canView = false;
        if ($entry['is_public'] == 1) {
            $canView = true;
        } elseif (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'tourist' && $entry['tourist_id'] == $_SESSION['user_id']) {
            $canView = true;
        }

        if (!$canView) {
            header("Location: /CeylonGo/public/tourist/public-diaries?error=" . urlencode("You don't have permission to view this entry"));
            exit();
        }

        $images = $this->getDiaryImages($id);
        view('tourist/view_diary_entry', ['entry' => $entry, 'images' => $images]);
    }

    public function deleteDiaryEntry($id) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
            header("Location: /CeylonGo/public/login");
            exit();
        }

        $diaryModel = new TripDiary($this->db);
        $entry = $diaryModel->getEntryById($id);

        if (!$entry || $entry['tourist_id'] != $_SESSION['user_id']) {
            header("Location: /CeylonGo/public/tourist/my-diary?error=" . urlencode("Entry not found"));
            exit();
        }

        if ($diaryModel->deleteEntry($id, $_SESSION['user_id'])) {
            // Delete associated images
            $this->deleteDiaryImages($id);
            $_SESSION['success'] = "Diary entry deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete entry";
        }

        header("Location: /CeylonGo/public/tourist/my-diary");
        exit();
    }

    public function publicDiaries() {
        $diaryModel = new TripDiary($this->db);
        // Get all public entries for feed view
        $entries = $diaryModel->getPublicEntries();
        
        // Get images and comments for each entry
        $allImages = [];
        $allComments = [];
        foreach ($entries as $entry) {
            $images = $this->getDiaryImages($entry['id']);
            $allImages[$entry['id']] = $images;
            
            $commentModel = new Comment($this->db);
            $allComments[$entry['id']] = $commentModel->getCommentsByEntryId($entry['id']);
        }
        
        $is_logged_in = isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
        $current_user_id = $_SESSION['user_id'] ?? null;
        $current_user_type = $_SESSION['user_role'] ?? null;
        
        view('tourist/public_diaries', [
            'entries' => $entries,
            'images' => $allImages,
            'comments' => $allComments,
            'is_logged_in' => $is_logged_in,
            'current_user_id' => $current_user_id,
            'current_user_type' => $current_user_type
        ]);
    }

    // View complete trip for a tourist
    public function viewTrip($tourist_id) {
        $diaryModel = new TripDiary($this->db);
        
        // Get tourist info
        $touristModel = new Tourist($this->db);
        $tourist = $touristModel->getTouristById($tourist_id);
        
        if (!$tourist) {
            header("Location: /CeylonGo/public/tourist/public-diaries?error=" . urlencode("Tourist not found"));
            exit();
        }

        // Get all entries for this tourist
        $entries = $diaryModel->getTripEntriesByTouristId($tourist_id);
        
        // Check if user can view (at least one public entry or own trip)
        $canView = false;
        if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'tourist' && $tourist_id == $_SESSION['user_id']) {
            $canView = true;
        } else {
            // Check if there's at least one public entry
            foreach ($entries as $entry) {
                if ($entry['is_public'] == 1) {
                    $canView = true;
                    break;
                }
            }
        }

        if (!$canView) {
            header("Location: /CeylonGo/public/tourist/public-diaries?error=" . urlencode("This trip is private"));
            exit();
        }

        // Filter to only public entries if not owner
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist' || $tourist_id != $_SESSION['user_id']) {
            $entries = array_filter($entries, function($entry) {
                return $entry['is_public'] == 1;
            });
        }

        // Get locations
        $locations = $diaryModel->getTripLocations($tourist_id);
        
        // Get images for all entries
        $allImages = [];
        foreach ($entries as $entry) {
            $images = $this->getDiaryImages($entry['id']);
            $allImages[$entry['id']] = $images;
        }

        // Get comments for all entries
        $allComments = [];
        foreach ($entries as $entry) {
            $commentModel = new Comment($this->db);
            $allComments[$entry['id']] = $commentModel->getCommentsByEntryId($entry['id']);
        }

        $is_owner = isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'tourist' && $tourist_id == $_SESSION['user_id'];
        
        view('tourist/view_trip', [
            'tourist' => $tourist,
            'entries' => $entries,
            'locations' => $locations,
            'images' => $allImages,
            'comments' => $allComments,
            'is_owner' => $is_owner
        ]);
    }

    // Comment methods
    public function addComment() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Please login to comment']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $entry_id = $data['entry_id'] ?? 0;
        $comment_text = trim($data['comment_text'] ?? '');

        if (empty($comment_text)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Comment cannot be empty']);
            exit();
        }

        // Determine user type from session
        $user_type = 'tourist';
        if (isset($_SESSION['user_role'])) {
            $role = $_SESSION['user_role'];
            if ($role === 'guide') $user_type = 'guide';
            elseif ($role === 'hotel') $user_type = 'hotel';
            elseif ($role === 'transporter') $user_type = 'transporter';
            elseif ($role === 'admin') $user_type = 'admin';
        }

        $comment = new Comment($this->db);
        $comment->entry_id = $entry_id;
        $comment->user_id = $_SESSION['user_id'];
        $comment->user_type = $user_type;
        $comment->comment_text = $comment_text;

        if ($comment->addComment()) {
            // Get the added comment with user info
            $addedComment = $comment->getCommentById($comment->id);
            
            // Get user name based on type - fetch from database
            $user_name = 'Unknown';
            try {
                if ($user_type === 'tourist') {
                    $touristModel = new Tourist($this->db);
                    $user = $touristModel->getTouristById($_SESSION['user_id']);
                    if ($user) $user_name = $user['first_name'] . ' ' . $user['last_name'];
                } elseif ($user_type === 'guide') {
                    $guideModel = new Guide($this->db);
                    $user = $guideModel->getGuideById($_SESSION['user_id']);
                    if ($user) $user_name = $user['first_name'] . ' ' . $user['last_name'];
                } elseif ($user_type === 'hotel') {
                    $hotelModel = new Hotel($this->db);
                    $user = $hotelModel->getHotelById($_SESSION['user_id']);
                    if ($user) $user_name = $user['hotel_name'];
                } elseif ($user_type === 'transporter') {
                    // For transporter, we might need to check the actual table structure
                    $user_name = 'Transport Provider';
                }
            } catch (Exception $e) {
                // Fallback to session name if available
                $user_name = $_SESSION['user_name'] ?? 'Unknown';
            }
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'comment' => [
                    'id' => $comment->id,
                    'comment_text' => $comment_text,
                    'user_name' => $user_name,
                    'user_type' => $user_type,
                    'created_at' => $addedComment['created_at']
                ]
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to add comment']);
        }
        exit();
    }

    public function getComments($entry_id) {
        $commentModel = new Comment($this->db);
        $comments = $commentModel->getCommentsByEntryId($entry_id);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'comments' => $comments]);
        exit();
    }

    public function deleteComment($id) {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Please login']);
            exit();
        }

        $user_type = 'tourist';
        if (isset($_SESSION['user_role'])) {
            $role = $_SESSION['user_role'];
            if ($role === 'guide') $user_type = 'guide';
            elseif ($role === 'hotel') $user_type = 'hotel';
            elseif ($role === 'transporter') $user_type = 'transporter';
            elseif ($role === 'admin') $user_type = 'admin';
        }

        $commentModel = new Comment($this->db);
        if ($commentModel->deleteComment($id, $_SESSION['user_id'], $user_type)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to delete comment']);
        }
        exit();
    }

    // Helper methods for image handling
    private function uploadDiaryImages($entry_id, $files) {
        // Use public/uploads/diary/ as per config
        $uploadDir = dirname(__DIR__) . "/public/uploads/diary/";
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                error_log("Failed to create directory: " . $uploadDir);
                return;
            }
        }
        
        foreach ($files['name'] as $key => $name) {
            if ($files['error'][$key] == 0) {
                $fileInfo = pathinfo($name);
                $extension = strtolower($fileInfo['extension']);
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($extension, $allowed)) {
                    $newFileName = uniqid('diary_', true) . '.' . $extension;
                    $targetPath = $uploadDir . $newFileName;
                    
                    if (move_uploaded_file($files['tmp_name'][$key], $targetPath)) {
                        // Save to database using PDO
                        $query = "INSERT INTO diary_images (entry_id, image_path) VALUES (?, ?)";
                        $stmt = $this->db->prepare($query);
                        $imagePath = 'diary/' . $newFileName;
                        $stmt->execute([$entry_id, $imagePath]);
                    } else {
                        error_log("Failed to move uploaded file to: " . $targetPath);
                    }
                }
            } else {
                error_log("File upload error for key $key: " . $files['error'][$key]);
            }
        }
    }

    private function getDiaryImages($entry_id) {
        $query = "SELECT * FROM diary_images WHERE entry_id = ? ORDER BY id ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$entry_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function deleteDiaryImages($entry_id) {
        $images = $this->getDiaryImages($entry_id);
        foreach ($images as $image) {
            $filePath = dirname(__DIR__) . "/public/uploads/" . $image['image_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        $query = "DELETE FROM diary_images WHERE entry_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$entry_id]);
    }

    /**
     * @return string|null Relative path under public/uploads/ (e.g. bank_slips/booking_1_....jpg), or null on failure.
     */
    private function saveBankTransferSlip(int $bookingId, array $file): ?string {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }
        if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            return null;
        }
        $tmp = $file['tmp_name'] ?? '';
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            return null;
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmp);
        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        if (!isset($map[$mime])) {
            return null;
        }
        $ext = $map[$mime];
        $base = defined('UPLOADS_PATH') ? UPLOADS_PATH : (dirname(__DIR__) . '/public/uploads');
        $dir = $base . DIRECTORY_SEPARATOR . 'bank_slips';
        if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
            error_log('saveBankTransferSlip: cannot create ' . $dir);
            return null;
        }
        $basename = 'booking_' . $bookingId . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $dest = $dir . DIRECTORY_SEPARATOR . $basename;
        if (!move_uploaded_file($tmp, $dest)) {
            return null;
        }
        return 'bank_slips/' . $basename;
    }

    /**
     * @return string|null Relative path under public/uploads/ (e.g. bank_slips/trip_1_....jpg)
     */
    private function saveTripBankTransferSlip(int $tripId, array $file): ?string {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }
        if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            return null;
        }
        $tmp = $file['tmp_name'] ?? '';
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            return null;
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmp);
        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        if (!isset($map[$mime])) {
            return null;
        }
        $ext = $map[$mime];
        $base = defined('UPLOADS_PATH') ? UPLOADS_PATH : (dirname(__DIR__) . '/public/uploads');
        $dir = $base . DIRECTORY_SEPARATOR . 'bank_slips';
        if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
            error_log('saveTripBankTransferSlip: cannot create ' . $dir);
            return null;
        }
        $basename = 'trip_' . $tripId . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $dest = $dir . DIRECTORY_SEPARATOR . $basename;
        if (!move_uploaded_file($tmp, $dest)) {
            return null;
        }
        return 'bank_slips/' . $basename;
    }
}
?>

