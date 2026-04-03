<?php
class TouristController {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
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

        // Create tourist
        $tourist = new Tourist($this->db);
        $tourist->first_name = trim($data['fname']);
        $tourist->last_name = trim($data['lname']);
        $tourist->contact_number = trim($data['contact']);
        $tourist->email = trim($data['email']);
        $tourist->password = password_hash($data['password'], PASSWORD_DEFAULT);

        if ($tourist->register()) {
            // Add to users table
            $authUser = new AuthUser($this->db);
            $authUser->ref_id = $tourist->id;
            $authUser->email = $tourist->email;
            $authUser->password = $tourist->password;
            $authUser->role = 'tourist';
            $authUser->addUser();

            // Set session
            $_SESSION['user_id'] = $tourist->id;
            $_SESSION['user_role'] = 'tourist';
            $_SESSION['user_type'] = 'tourist';
            $_SESSION['user_email'] = $tourist->email;
            $_SESSION['user_name'] = $tourist->first_name . ' ' . $tourist->last_name;

            header("Location: /CeylonGo/public/tourist/dashboard");
            exit();
        } else {
            echo "<script>alert('Registration failed. Please try again.'); window.history.back();</script>";
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

        // Pass data to view
        view('tourist/dashboard', ['tourist_data' => $tourist_data]);
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
        $tourist_data = $touristModel->getTouristById($_SESSION['user_id']);
        $user_name = isset($_SESSION['user_name']) ? trim($_SESSION['user_name']) : '';
        if ($user_name === '' && $tourist_data) {
            $user_name = trim(($tourist_data['first_name'] ?? '') . ' ' . ($tourist_data['last_name'] ?? ''));
        }
        $placesAutocompleteUrl = (defined('BASE_URL') ? BASE_URL : '/CeylonGo/public') . '/api/places-autocomplete';
        view('tourist/trip', [
            'tourist_data' => $tourist_data,
            'user_name' => $user_name,
            'places_autocomplete_url' => $placesAutocompleteUrl
        ]);
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
        view('tourist/dashboard', [
            'tourist_data' => $tourist_data,
            'is_logged_in' => isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'tourist'
        ]);
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
                echo json_encode(['success' => true]);
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
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'tourist') {
            header('Location: /CeylonGo/public/login');
            exit;
        }

        $data = $_POST;

        $userId       = (int)($data['user_id'] ?? $_SESSION['user_id']);
        $hotelId      = trim($data['hotel_id'] ?? '');
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

        if (!$hotelId || !$hotelName || !$customerName || !$contact || !$checkIn || !$checkOut || !$roomType || $totalPrice <= 0) {
            header('Location: /CeylonGo/public/tourist/customize-trip?error=' . urlencode('Please fill all required fields for hotel booking.'));
            exit;
        }

        try {
            $sql = "INSERT INTO hotel_requests (
                        user_id, hotel_id, hotel_name,
                        customer_name, contact_number, guests, adults, children,
                        check_in_date, check_out_date, nights,
                        room_type, room_count, total_price, currency
                    ) VALUES (
                        :user_id, :hotel_id, :hotel_name,
                        :customer_name, :contact_number, :guests, :adults, :children,
                        :check_in_date, :check_out_date, :nights,
                        :room_type, :room_count, :total_price, 'LKR'
                    )";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id'        => $userId,
                ':hotel_id'       => $hotelId,
                ':hotel_name'     => $hotelName,
                ':customer_name'  => $customerName,
                ':contact_number' => $contact,
                ':guests'         => $guests,
                ':adults'         => $adults,
                ':children'       => $children,
                ':check_in_date'  => $checkIn,
                ':check_out_date' => $checkOut,
                ':nights'         => $nights,
                ':room_type'      => $roomType,
                ':room_count'     => $roomCount,
                ':total_price'    => $totalPrice,
            ]);
            header('Location: /CeylonGo/public/tourist/customize-trip?success=' . urlencode('Hotel request submitted.'));
            exit;
        } catch (\Throwable $e) {
            header('Location: /CeylonGo/public/tourist/customize-trip?error=' . urlencode('Failed to submit hotel request.'));
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
            header('Location: /CeylonGo/public/login?redirect=' . urlencode('/CeylonGo/public/tourist/my-bookings'));
            exit;
        }
        $current_user_id = (int) $_SESSION['user_id'];
        
        // Fetch bookings from database
        try {
            $sql = "SELECT * FROM package_bookings WHERE user_id = ? ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$current_user_id]);
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Convert database results to expected format
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
            $bookings = [];
        }

        $payment_message = $_SESSION['payment_message'] ?? null;
        $payment_error = $_SESSION['payment_error'] ?? null;
        $payment_info = $_SESSION['payment_info'] ?? null;
        unset($_SESSION['payment_message'], $_SESSION['payment_error'], $_SESSION['payment_info']);
        
        view('tourist/my_bookings', [
            'bookings' => $bookings,
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
            try {
                $upd = $this->db->prepare(
                    'UPDATE package_bookings SET bank_transfer_submitted_at = NOW() WHERE id = ? AND user_id = ? AND status = \'approved\''
                );
                $upd->execute([$booking_id, $current_user_id]);
            } catch (PDOException $e) {
                error_log('paymentCheckout bank_transfer_submitted_at: ' . $e->getMessage());
                $_SESSION['payment_error'] = 'Database update failed. If this persists, run the migration: database/migrate_bank_transfer_submitted_at.sql';
                header('Location: /CeylonGo/public/tourist/payment?booking_id=' . $booking_id);
                exit;
            }
            $_SESSION['payment_info'] = 'Your booking stays approved until we confirm the payment (usually within 1–2 business days).';
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
        if (!empty($q['md5sig']) && PayHere::notifyValid($q, (string) PAYHERE_MERCHANT_SECRET)) {
            if ((int) ($q['status_code'] ?? 0) === 2) {
                $applied = $this->payHereCompleteApprovedBooking($q);
            }
        }

        // Sandbox: notify cannot reach localhost; return often has no md5sig or only status_code=0 — still mark pending booking paid when not an explicit decline.
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
        $_SESSION['payment_message'] = 'We could not confirm this payment automatically. Refresh My Bookings in a moment. On a real server, set a public notify URL in PayHere so status updates without this message.';
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

    private function payHereCompleteApprovedBooking(array $post): bool {
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

        if (!$this->payHereCompleteApprovedBooking($post)) {
            error_log('PayHere notify: payHereCompleteApprovedBooking returned false');
        }

        header('Content-Type: text/plain; charset=UTF-8');
        echo 'OK';
    }

    public function tripSummary() {
        view('tourist/trip_summary');
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
        view('tourist/package_details', ['package' => $package]);
    }

    public function packageDetailsQuery() {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 1;
        $package = $this->getPackageDetailById($id);
        if (!$package) {
            header('Location: /CeylonGo/public/tourist/packages');
            exit;
        }
        view('tourist/package_details', ['package' => $package]);
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

        // Validate required fields
        if (empty($data['customerName']) || empty($data['contact']) || empty($data['location']) || 
            empty($data['language']) || empty($data['date']) || empty($data['time'])) {
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
                header("Location: /CeylonGo/public/tourist/tour-guide-report");
            } else {
                header("Location: /CeylonGo/public/tourist/dashboard?error=" . urlencode("Failed to submit request"));
            }
            exit();
            
        } catch (Exception $e) {
            error_log($e->getMessage());
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
        
        if (!empty($data['password'])) {
            $tourist->password = password_hash($data['password'], PASSWORD_DEFAULT);
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
            header("Location: /CeylonGo/public/tourist/profile");
        } else {
            header("Location: /CeylonGo/public/tourist/profile?error=" . urlencode("Failed to update profile"));
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
}
?>

