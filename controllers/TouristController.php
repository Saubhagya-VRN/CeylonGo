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

    public function dashboard() {
        view('tourist/tourist_dashboard');
    }

    public function transportRequestView() {
        view('tourist/transport_services');
    }

    public function transportRequest() {
        $data = $_POST;

        $request = new TransportRequest($this->db);
        $request->customerName = $data['customerName'] ?? '';
        $request->vehicleType = $data['vehicleType'] ?? '';
        $request->date = $data['date'] ?? '';
        $request->pickupTime = $data['pickupTime'] ?? '';
        $request->pickupLocation = $data['pickupLocation'] ?? '';
        $request->dropoffLocation = $data['dropoffLocation'] ?? '';
        $request->numPeople = $data['numPeople'] ?? 1;
        $request->notes = $data['notes'] ?? '';

        if ($request->addRequest()) {
            header("Location: /CeylonGo/public/tourist/transport-report");
            exit();
        } else {
            header("Location: /CeylonGo/public/tourist/transport-services?error=" . urlencode("Failed to submit request"));
            exit();
        }
    }

    public function transportReport() {
        $requestModel = new TransportRequest($this->db);
        $requests = $requestModel->getAllRequests();
        view('tourist/transport_report', ['requests' => $requests]);
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

    public function bookingForm() {
        view('tourist/booking_form');
    }

    public function payment() {
        view('tourist/payment');
    }

    public function tripSummary() {
        view('tourist/trip_summary');
    }

    public function recommendedPackages() {
        view('tourist/recommended_packages');
    }

    public function packageDetails($id) {
        view('tourist/package_details', ['package_id' => $id]);
    }

    public function addReview() {
        view('tourist/add_review');
    }

    public function transportProviders() {
        view('tourist/transport_providers');
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
}
?>

