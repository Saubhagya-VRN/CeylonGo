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
        view('tourist/trip', [
            'tourist_data' => $tourist_data,
            'user_name' => $user_name
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

        $request = new TransportRequest($this->db);
        $request->customerName = $data['customerName'] ?? '';
        $request->contactNumber = $data['contactNumber'] ?? '';
        $request->vehicleType = $data['vehicleType'] ?? '';
        $request->date = $data['date'] ?? '';
        $request->pickupTime = $data['pickupTime'] ?? '';
        $request->pickupLocation = $data['pickupLocation'] ?? '';
        $request->dropoffLocation = $data['dropoffLocation'] ?? '';
        $request->numPeople = (int) ($data['numPeople'] ?? 1);
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
        $package_id = isset($_POST['package_id']) ? (int) $_POST['package_id'] : 0;
        $travel_date = isset($_POST['travel_date']) ? trim($_POST['travel_date']) : '';
        $min_date = date('Y-m-d', strtotime('+21 days'));
        if ($travel_date !== '' && $travel_date < $min_date) {
            header('Location: /CeylonGo/public/tourist/booking-form?package=' . $package_id . '&error=' . urlencode('Preferred Travel Date must be at least 3 weeks from today.'));
            exit;
        }
        $package = $this->getPackageDetailById($package_id);
        $price_per = isset($package['price']) ? (int) $package['price'] : 0;
        $travelers = isset($_POST['travelers']) ? (int) $_POST['travelers'] : 1;
        $total = $travelers * $price_per;
        $booking = [
            'id' => uniqid('b', true),
            'package_id' => $package_id,
            'package_name' => isset($_POST['package_name']) ? trim($_POST['package_name']) : ($package['title'] ?? ''),
            'travelers' => $travelers,
            'travel_date' => $travel_date,
            'fullname' => isset($_POST['fullname']) ? trim($_POST['fullname']) : '',
            'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
            'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : '',
            'special_requests' => isset($_POST['special_requests']) ? trim($_POST['special_requests']) : '',
            'total_amount' => $total,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ];
        if (!isset($_SESSION['pending_bookings']) || !is_array($_SESSION['pending_bookings'])) {
            $_SESSION['pending_bookings'] = [];
        }
        $_SESSION['pending_bookings'][] = $booking;
        header('Location: /CeylonGo/public/tourist/my-bookings');
        exit;
    }

    public function myBookings() {
        $bookings = isset($_SESSION['pending_bookings']) && is_array($_SESSION['pending_bookings']) ? $_SESSION['pending_bookings'] : [];
        view('tourist/my_bookings', ['bookings' => $bookings]);
    }

    public function bookingApprove() {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if ($id === '' || !isset($_SESSION['pending_bookings']) || !is_array($_SESSION['pending_bookings'])) {
            header('Location: /CeylonGo/public/tourist/my-bookings');
            exit;
        }
        foreach ($_SESSION['pending_bookings'] as &$b) {
            if (isset($b['id']) && $b['id'] === $id) {
                $b['status'] = 'approved';
                break;
            }
        }
        unset($b);
        header('Location: /CeylonGo/public/tourist/my-bookings');
        exit;
    }

    public function payment() {
        $booking = null;
        $booking_id = isset($_GET['booking_id']) ? trim($_GET['booking_id']) : '';
        if ($booking_id !== '' && isset($_SESSION['pending_bookings']) && is_array($_SESSION['pending_bookings'])) {
            foreach ($_SESSION['pending_bookings'] as $b) {
                if (isset($b['id']) && $b['id'] === $booking_id && ($b['status'] ?? '') === 'approved') {
                    $booking = $b;
                    break;
                }
            }
        }
        view('tourist/payment', ['booking' => $booking]);
    }

    public function tripSummary() {
        view('tourist/trip_summary');
    }

    public function recommendedPackages() {
        view('tourist/recommended_packages');
    }

    public function packages() {
        $packages = [
            ['id' => 1, 'title' => 'Cultural Triangle', 'location' => 'Kandy', 'locations' => 'Kandy, Sigiriya, Dambulla', 'duration' => '5 Days 4 Nights', 'image' => '/CeylonGo/public/images/kandy.jpeg', 'trending' => true, 'rating' => 4.5, 'reviews' => 203, 'meals' => true, 'category' => 'cultural', 'price' => 125000],
            ['id' => 2, 'title' => 'Southern Coast Honeymoon', 'location' => 'Galle', 'locations' => 'Galle, Mirissa, Unawatuna', 'duration' => '5 Days 4 Nights', 'image' => '/CeylonGo/public/images/beach.jpg', 'trending' => true, 'rating' => 4.5, 'reviews' => 65, 'meals' => true, 'category' => 'honeymoon', 'price' => 185000],
            ['id' => 3, 'title' => 'Hill Country Escape', 'location' => 'Nuwara Eliya', 'locations' => 'Nuwara Eliya, Ella, Horton Plains', 'duration' => '6 Days 5 Nights', 'image' => '/CeylonGo/public/images/greenary.jpg', 'trending' => false, 'rating' => 4.8, 'reviews' => 142, 'meals' => true, 'category' => 'adventure', 'price' => 145000],
            ['id' => 4, 'title' => 'Ancient Heritage Trail', 'location' => 'Anuradhapura', 'locations' => 'Anuradhapura, Dambulla, Sigiriya', 'duration' => '4 Days 3 Nights', 'image' => '/CeylonGo/public/images/perehara.jpeg', 'trending' => true, 'rating' => 4.6, 'reviews' => 98, 'meals' => true, 'category' => 'heritage', 'price' => 95000],
            ['id' => 5, 'title' => 'Wildlife Safari', 'location' => 'Yala', 'locations' => 'Yala, Udawalawe', 'duration' => '4 Days 3 Nights', 'image' => '/CeylonGo/public/images/elephant.jpg', 'trending' => false, 'rating' => 4.7, 'reviews' => 176, 'meals' => true, 'category' => 'safari', 'price' => 165000],
            ['id' => 6, 'title' => 'Solo Explorer', 'location' => 'Ella', 'locations' => 'Colombo, Nuwara Eliya, Ella', 'duration' => '6 Days 5 Nights', 'image' => '/CeylonGo/public/images/train.jpg', 'trending' => true, 'rating' => 4.4, 'reviews' => 89, 'meals' => true, 'category' => 'solo', 'price' => 78000],
            ['id' => 7, 'title' => 'Family Fun', 'location' => 'Bentota', 'locations' => 'Bentota, Colombo', 'duration' => '5 Days 4 Nights', 'image' => '/CeylonGo/public/images/resort.jpg', 'trending' => false, 'rating' => 4.5, 'reviews' => 124, 'meals' => true, 'category' => 'family', 'price' => 195000],
            ['id' => 8, 'title' => 'Beach Getaway', 'location' => 'Hikkaduwa', 'locations' => 'Hikkaduwa, Unawatuna', 'duration' => '3 Days 2 Nights', 'image' => '/CeylonGo/public/images/sunset.jpg', 'trending' => false, 'rating' => 4.3, 'reviews' => 67, 'meals' => true, 'category' => 'beach', 'price' => 65000],
        ];
        $category = isset($_GET['category']) ? trim(strtolower($_GET['category'])) : '';
        $trending = isset($_GET['trending']) && $_GET['trending'] === '1';
        if ($trending) {
            $packages = array_values(array_filter($packages, function ($p) { return !empty($p['trending']); }));
        } elseif ($category !== '') {
            $packages = array_values(array_filter($packages, function ($p) use ($category) { return isset($p['category']) && strtolower($p['category']) === $category; }));
        }
        view('tourist/packages', [
            'packages' => $packages,
            'filter_category' => $category,
            'filter_trending' => $trending,
        ]);
    }

    public function packageDetails($id) {
        $package = $this->getPackageDetailById((int) $id);
        view('tourist/package_details', ['package' => $package]);
    }

    public function packageDetailsQuery() {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 1;
        $package = $this->getPackageDetailById($id);
        view('tourist/package_details', ['package' => $package]);
    }

    /**
     * Full package detail for package details page (Sri Lankan content).
     */
    private function getPackageDetailById($id) {
        $list = [
            1 => [
                'id' => 1, 'title' => 'Cultural Triangle 4N5D — Kandy, Sigiriya & Dambulla', 'location' => 'Kandy',
                'duration' => '5 Days 4 Nights', 'duration_short' => '5 Days / 4 Nights', 'image' => '/CeylonGo/public/images/kandy.jpeg',
                'trending' => true, 'rating' => 4.5, 'reviews' => 203, 'category' => 'Cultural', 'price' => 125000,
                'overview' => [
                    'Arrival at Colombo and transfer to Kandy, the royal and cultural heart of Sri Lanka.',
                    'Explore Kandy: Temple of the Tooth, evening cultural dance show; optional Botanical Gardens.',
                    'Full day to Sigiriya Rock Fortress and Dambulla Cave Temple; return to Kandy.',
                    'Kandy to Nuwara Eliya — scenic hill country, tea factory visit.',
                    'Departure to Colombo as per schedule.',
                ],
                'highlights' => [
                    ['icon' => 'hotel', 'title' => 'Accommodation', 'desc' => '4 nights stay'],
                    ['icon' => 'transfer', 'title' => 'Transfers', 'desc' => 'Private cab for all transfers'],
                    ['icon' => 'sightseeing', 'title' => 'Sightseeing', 'desc' => 'Kandy, Sigiriya, Dambulla covered'],
                    ['icon' => 'meals', 'title' => 'Meals', 'desc' => 'Daily breakfast & dinner included'],
                    ['icon' => 'activities', 'title' => 'Activities', 'desc' => 'Temple of the Tooth, cultural show'],
                    ['icon' => 'support', 'title' => 'Support', 'desc' => '24x7 travel assistance'],
                ],
                'itinerary' => [
                    ['day' => 1, 'title' => 'Arrival – Colombo to Kandy', 'activities' => ['Airport pick-up and transfer to Kandy', 'Check-in and visit Temple of the Tooth', 'Evening cultural dance show']],
                    ['day' => 2, 'title' => 'Sigiriya Rock Fortress (Full Day)', 'activities' => ['Full day at Sigiriya Rock and gardens', 'Dambulla Cave Temple en route', 'Return to Kandy']],
                    ['day' => 3, 'title' => 'Kandy – City & Spice Garden', 'activities' => ['Botanical Gardens (optional)', 'Spice garden tour', 'Leisure in Kandy']],
                    ['day' => 4, 'title' => 'Kandy – Nuwara Eliya (Scenic)', 'activities' => ['Scenic drive to hill country', 'Tea factory visit', 'Overnight in Nuwara Eliya']],
                    ['day' => 5, 'title' => 'Departure', 'activities' => ['Transfer to Colombo airport', 'Departure']],
                ],
                'included' => ['4 nights accommodation', 'Half Board (Breakfast + Dinner)', 'All entrance fees (Sigiriya, Dambulla, Temple of the Tooth)', 'Cultural show ticket', 'Private cab for sightseeing'],
                'excluded' => ['International flights', 'Lunches and beverages unless specified', 'Personal expenses and tips', 'Visa (if applicable)', 'Travel insurance'],
                'accommodation' => [
                    ['nights' => 3, 'location' => 'Kandy', 'hotel' => 'Earl\'s Regent Hotel'],
                    ['nights' => 1, 'location' => 'Nuwara Eliya', 'hotel' => 'Araliya Green City Hotel'],
                ],
            ],
            2 => [
                'id' => 2, 'title' => 'Southern Coast Honeymoon: Galle, Mirissa & Unawatuna 4N/5D', 'location' => 'Galle',
                'duration' => '5 Days 4 Nights', 'duration_short' => '5 Days / 4 Nights', 'image' => '/CeylonGo/public/images/beach.jpg',
                'trending' => true, 'rating' => 4.5, 'reviews' => 65, 'category' => 'Honeymoon', 'price' => 185000,
                'overview' => [
                    'Arrival at Colombo and transfer to Galle, the historic fort city by the sea.',
                    'Explore Galle Fort & Unawatuna — walking tour, beach time, sunset at the fort.',
                    'Head to Mirissa — whale watching (seasonal), beach time.',
                    'Leisure & spa — beach relaxation, optional spa; candlelit dinner.',
                    'Departure to Colombo as per schedule.',
                ],
                'highlights' => [
                    ['icon' => 'hotel', 'title' => 'Accommodation', 'desc' => '4 nights beachfront stay'],
                    ['icon' => 'transfer', 'title' => 'Transfers', 'desc' => 'Private cab for all transfers'],
                    ['icon' => 'sightseeing', 'title' => 'Sightseeing', 'desc' => 'Galle Fort, Mirissa, Unawatuna'],
                    ['icon' => 'meals', 'title' => 'Meals', 'desc' => 'Breakfast & dinner included'],
                    ['icon' => 'activities', 'title' => 'Activities', 'desc' => 'Whale watching, beach experiences'],
                    ['icon' => 'support', 'title' => 'Support', 'desc' => '24x7 travel assistance'],
                ],
                'itinerary' => [
                    ['day' => 1, 'title' => 'Arrival – Airport to Galle', 'activities' => ['Airport pick-up and transfer to Galle', 'Check-in and leisure at resort']],
                    ['day' => 2, 'title' => 'Galle Fort & Unawatuna', 'activities' => ['Galle Fort walking tour', 'Unawatuna beach', 'Sunset at the fort']],
                    ['day' => 3, 'title' => 'Mirissa Whale Watching', 'activities' => ['Whale watching tour (seasonal)', 'Beach time at Mirissa']],
                    ['day' => 4, 'title' => 'Leisure & Spa', 'activities' => ['Beach relaxation', 'Optional spa (discount for honeymooners)', 'Candlelit dinner']],
                    ['day' => 5, 'title' => 'Departure', 'activities' => ['Transfer to Colombo airport', 'Departure']],
                ],
                'included' => ['Airport transfers', '4 nights beachfront accommodation', 'Half Board', 'Whale watching (seasonal)', 'Honeymoon freebies as listed'],
                'excluded' => ['International flights', 'Lunches and beverages', 'Water sports (optional)', 'Personal expenses and tips', 'Visa', 'Travel insurance'],
                'accommodation' => [
                    ['nights' => 4, 'location' => 'Galle / Unawatuna', 'hotel' => 'Jetwing Lighthouse, Galle'],
                ],
            ],
        ];
        $list[3] = [
            'id' => 3, 'title' => 'Hill Country Escape', 'location' => 'Nuwara Eliya',
            'duration' => '6 Days 5 Nights', 'duration_short' => '6 Days / 5 Nights', 'image' => '/CeylonGo/public/images/greenary.jpg',
            'trending' => false, 'rating' => 4.8, 'reviews' => 142, 'category' => 'Adventure', 'price' => 145000,
            'overview' => [
                'Arrival at Colombo and transfer to Nuwara Eliya, the misty tea-country highlands.',
                'Explore Nuwara Eliya: tea estates, Gregory Lake; optional Horton Plains.',
                'Drive to Ella — Nine Arch Bridge, Little Adam\'s Peak, scenic train stretch.',
                'Hill country exploration — tea factory, cool climate, mountain views.',
                'Departure to Colombo as per schedule.',
            ],
            'highlights' => [
                ['icon' => 'hotel', 'title' => 'Accommodation', 'desc' => '5 nights in hill country'],
                ['icon' => 'transfer', 'title' => 'Transfers', 'desc' => 'Private cab for all transfers'],
                ['icon' => 'sightseeing', 'title' => 'Sightseeing', 'desc' => 'Nuwara Eliya, Ella, Horton Plains'],
                ['icon' => 'meals', 'title' => 'Meals', 'desc' => 'Breakfast & dinner included'],
                ['icon' => 'activities', 'title' => 'Activities', 'desc' => 'Tea factory, Nine Arch Bridge, Little Adam\'s Peak'],
                ['icon' => 'support', 'title' => 'Support', 'desc' => '24x7 travel assistance'],
            ],
            'itinerary' => [
                ['day' => 1, 'title' => 'Arrival – Colombo to Nuwara Eliya', 'activities' => ['Airport pick-up and transfer to Nuwara Eliya', 'Check-in', 'Evening at Gregory Lake']],
                ['day' => 2, 'title' => 'Nuwara Eliya – Horton Plains (optional)', 'activities' => ['Horton Plains National Park (optional)', 'Tea estate visit', 'Leisure in Nuwara Eliya']],
                ['day' => 3, 'title' => 'Nuwara Eliya to Ella', 'activities' => ['Scenic drive to Ella', 'Nine Arch Bridge', 'Check-in at Ella']],
                ['day' => 4, 'title' => 'Ella – Little Adam\'s Peak & viewpoints', 'activities' => ['Little Adam\'s Peak hike', 'Ella Rock or viewpoints', 'Relax in Ella']],
                ['day' => 5, 'title' => 'Ella – Tea country & train experience', 'activities' => ['Tea factory tour', 'Scenic train stretch (optional)', 'Leisure in Ella']],
                ['day' => 6, 'title' => 'Departure', 'activities' => ['Transfer to Colombo airport', 'Departure']],
            ],
            'included' => ['Airport transfers', '5 nights accommodation', 'Half Board', 'Tea factory visit', 'Private cab for sightseeing'],
            'excluded' => ['International flights', 'Lunches and beverages', 'Horton Plains entry (optional)', 'Personal expenses', 'Visa', 'Travel insurance'],
            'accommodation' => [
                ['nights' => 2, 'location' => 'Nuwara Eliya', 'hotel' => 'St. Andrew\'s Hotel, Nuwara Eliya'],
                ['nights' => 3, 'location' => 'Ella', 'hotel' => '98 Acres Resort & Spa, Ella'],
            ],
        ];
        $list[4] = [
            'id' => 4, 'title' => 'Ancient Heritage Trail', 'location' => 'Anuradhapura',
            'duration' => '4 Days 3 Nights', 'duration_short' => '4 Days / 3 Nights', 'image' => '/CeylonGo/public/images/perehara.jpeg',
            'trending' => true, 'rating' => 4.6, 'reviews' => 98, 'category' => 'Heritage', 'price' => 95000,
            'overview' => [
                'Arrival at Colombo and transfer to Anuradhapura or the Cultural Triangle.',
                'Explore ancient capitals — temples, stupas, and sacred sites.',
                'Dambulla Cave Temple and Sigiriya or Polonnaruwa.',
                'Departure to Colombo as per schedule.',
            ],
            'highlights' => [
                ['icon' => 'hotel', 'title' => 'Accommodation', 'desc' => '3 nights stay'],
                ['icon' => 'transfer', 'title' => 'Transfers', 'desc' => 'Private cab for all transfers'],
                ['icon' => 'sightseeing', 'title' => 'Sightseeing', 'desc' => 'Anuradhapura, Dambulla, Sigiriya/Polonnaruwa'],
                ['icon' => 'meals', 'title' => 'Meals', 'desc' => 'Breakfast & dinner included'],
                ['icon' => 'activities', 'title' => 'Activities', 'desc' => 'Temples, stupas, heritage sites'],
                ['icon' => 'support', 'title' => 'Support', 'desc' => '24x7 travel assistance'],
            ],
            'itinerary' => [
                ['day' => 1, 'title' => 'Arrival – Colombo to Anuradhapura', 'activities' => ['Airport pick-up and transfer to Anuradhapura', 'Check-in', 'Evening visit to sacred city or rest']],
                ['day' => 2, 'title' => 'Anuradhapura – Ancient capital', 'activities' => ['Anuradhapura sacred city – temples and stupas', 'Sri Maha Bodhi, Ruwanwelisaya', 'Return to hotel']],
                ['day' => 3, 'title' => 'Dambulla & Sigiriya or Polonnaruwa', 'activities' => ['Dambulla Cave Temple', 'Sigiriya Rock Fortress or Polonnaruwa ancient city', 'Return to Anuradhapura']],
                ['day' => 4, 'title' => 'Departure', 'activities' => ['Transfer to Colombo airport', 'Departure']],
            ],
            'included' => ['Airport transfers', '3 nights accommodation', 'Half Board', 'Entrance fees to heritage sites', 'Private cab'],
            'excluded' => ['International flights', 'Lunches and beverages', 'Personal expenses', 'Visa', 'Travel insurance'],
            'accommodation' => [
                ['nights' => 2, 'location' => 'Anuradhapura', 'hotel' => 'Rajarata Hotel, Anuradhapura'],
                ['nights' => 1, 'location' => 'Dambulla / Sigiriya', 'hotel' => 'Heritage Hotel, Sigiriya'],
            ],
        ];
        $list[5] = [
            'id' => 5, 'title' => 'Wildlife Safari', 'location' => 'Yala',
            'duration' => '4 Days 3 Nights', 'duration_short' => '4 Days / 3 Nights', 'image' => '/CeylonGo/public/images/elephant.jpg',
            'trending' => false, 'rating' => 4.7, 'reviews' => 176, 'category' => 'Safari', 'price' => 165000,
            'overview' => [
                'Arrival at Colombo and transfer to Yala or the safari region.',
                'Safari in Yala National Park — leopards, elephants, birdlife.',
                'Udawalawe or second safari — wildlife and nature.',
                'Departure to Colombo as per schedule.',
            ],
            'highlights' => [
                ['icon' => 'hotel', 'title' => 'Accommodation', 'desc' => '3 nights near parks'],
                ['icon' => 'transfer', 'title' => 'Transfers', 'desc' => 'Private cab for all transfers'],
                ['icon' => 'sightseeing', 'title' => 'Safari', 'desc' => 'Yala and Udawalawe National Parks'],
                ['icon' => 'meals', 'title' => 'Meals', 'desc' => 'Breakfast & dinner included'],
                ['icon' => 'activities', 'title' => 'Activities', 'desc' => 'Game drives, wildlife viewing'],
                ['icon' => 'support', 'title' => 'Support', 'desc' => '24x7 travel assistance'],
            ],
            'itinerary' => [
                ['day' => 1, 'title' => 'Arrival – Colombo to Yala / Tissamaharama', 'activities' => ['Airport pick-up and transfer to Yala or Tissamaharama', 'Check-in', 'Evening at leisure']],
                ['day' => 2, 'title' => 'Yala National Park – Safari', 'activities' => ['Early morning or afternoon game drive in Yala', 'Leopards, elephants, birdlife', 'Return to hotel']],
                ['day' => 3, 'title' => 'Udawalawe National Park – Safari', 'activities' => ['Transfer to Udawalawe', 'Safari in Udawalawe – elephants and wildlife', 'Overnight near Udawalawe or return to Yala']],
                ['day' => 4, 'title' => 'Departure', 'activities' => ['Transfer to Colombo airport', 'Departure']],
            ],
            'included' => ['Airport transfers', '3 nights accommodation', 'Half Board', 'Safari jeep and park fees (as per itinerary)', 'Private cab'],
            'excluded' => ['International flights', 'Lunches and beverages', 'Personal expenses', 'Visa', 'Travel insurance'],
            'accommodation' => [
                ['nights' => 2, 'location' => 'Yala / Tissamaharama', 'hotel' => 'Cinnamon Wild Yala'],
                ['nights' => 1, 'location' => 'Udawalawe', 'hotel' => 'Elephant Reach, Udawalawe'],
            ],
        ];
        $list[6] = [
            'id' => 6, 'title' => 'Solo Explorer', 'location' => 'Ella',
            'duration' => '6 Days 5 Nights', 'duration_short' => '6 Days / 5 Nights', 'image' => '/CeylonGo/public/images/train.jpg',
            'trending' => true, 'rating' => 4.4, 'reviews' => 89, 'category' => 'Solo', 'price' => 78000,
            'overview' => [
                'Arrival at Colombo; begin journey to hill country by train or road.',
                'Scenic train to Ella — tea country, Nine Arch Bridge, Little Adam\'s Peak.',
                'Explore Ella and surrounds — hiking, viewpoints, relaxed pace.',
                'Hill country discovery — Nuwara Eliya or Kandy option.',
                'Departure to Colombo as per schedule.',
            ],
            'highlights' => [
                ['icon' => 'hotel', 'title' => 'Accommodation', 'desc' => '5 nights in hill country'],
                ['icon' => 'transfer', 'title' => 'Transfers', 'desc' => 'Train + cab as per itinerary'],
                ['icon' => 'sightseeing', 'title' => 'Sightseeing', 'desc' => 'Ella, Nine Arch Bridge, Little Adam\'s Peak'],
                ['icon' => 'meals', 'title' => 'Meals', 'desc' => 'Breakfast & dinner included'],
                ['icon' => 'activities', 'title' => 'Activities', 'desc' => 'Scenic train, hiking, tea country'],
                ['icon' => 'support', 'title' => 'Support', 'desc' => '24x7 travel assistance'],
            ],
            'itinerary' => [
                ['day' => 1, 'title' => 'Arrival – Colombo to Kandy / Nuwara Eliya by train or road', 'activities' => ['Airport pick-up', 'Scenic train from Colombo to Kandy or transfer by road', 'Check-in at Kandy or Nuwara Eliya']],
                ['day' => 2, 'title' => 'Train / road to Ella', 'activities' => ['Scenic train to Ella (or drive)', 'Check-in at Ella', 'Evening at leisure']],
                ['day' => 3, 'title' => 'Ella – Nine Arch Bridge & Little Adam\'s Peak', 'activities' => ['Nine Arch Bridge', 'Little Adam\'s Peak hike', 'Relax in Ella']],
                ['day' => 4, 'title' => 'Ella – Tea country & viewpoints', 'activities' => ['Tea factory or plantation visit', 'Ella Rock or viewpoints', 'Leisure in Ella']],
                ['day' => 5, 'title' => 'Ella – Extra day or Nuwara Eliya', 'activities' => ['Optional day trip to Nuwara Eliya or rest in Ella', 'Last evening in hill country']],
                ['day' => 6, 'title' => 'Departure', 'activities' => ['Transfer to Colombo airport', 'Departure']],
            ],
            'included' => ['Airport transfers', '5 nights accommodation', 'Half Board', 'Train ticket (as per itinerary)', 'Private cab where needed'],
            'excluded' => ['International flights', 'Lunches and beverages', 'Personal expenses', 'Visa', 'Travel insurance'],
            'accommodation' => [
                ['nights' => 2, 'location' => 'Nuwara Eliya', 'hotel' => 'Tea Factory Hotel, Nuwara Eliya'],
                ['nights' => 3, 'location' => 'Ella', 'hotel' => 'Mountain Heavens, Ella'],
            ],
        ];
        $list[7] = [
            'id' => 7, 'title' => 'Family Fun', 'location' => 'Bentota',
            'duration' => '5 Days 4 Nights', 'duration_short' => '5 Days / 4 Nights', 'image' => '/CeylonGo/public/images/resort.jpg',
            'trending' => false, 'rating' => 4.5, 'reviews' => 124, 'category' => 'Family', 'price' => 195000,
            'overview' => [
                'Arrival at Colombo and transfer to Bentota, the family-friendly beach strip.',
                'Explore Bentota — beach, water sports (optional), turtle hatchery.',
                'Colombo day — city sights, markets, or leisure.',
                'Family time — beach and resort activities.',
                'Departure to Colombo as per schedule.',
            ],
            'highlights' => [
                ['icon' => 'hotel', 'title' => 'Accommodation', 'desc' => '4 nights (Bentota + Colombo)'],
                ['icon' => 'transfer', 'title' => 'Transfers', 'desc' => 'Private cab for all transfers'],
                ['icon' => 'sightseeing', 'title' => 'Sightseeing', 'desc' => 'Bentota beach, Colombo city'],
                ['icon' => 'meals', 'title' => 'Meals', 'desc' => 'Breakfast & dinner included'],
                ['icon' => 'activities', 'title' => 'Activities', 'desc' => 'Beach, turtle hatchery, family activities'],
                ['icon' => 'support', 'title' => 'Support', 'desc' => '24x7 travel assistance'],
            ],
            'itinerary' => [
                ['day' => 1, 'title' => 'Arrival – Colombo to Bentota', 'activities' => ['Airport pick-up and transfer to Bentota', 'Check-in at resort', 'Beach time']],
                ['day' => 2, 'title' => 'Bentota – Beach & turtle hatchery', 'activities' => ['Beach activities', 'Turtle hatchery visit', 'Optional water sports', 'Resort leisure']],
                ['day' => 3, 'title' => 'Bentota – Family day', 'activities' => ['Resort activities', 'Beach or pool', 'Optional boat ride on Madu Ganga']],
                ['day' => 4, 'title' => 'Bentota to Colombo', 'activities' => ['Transfer to Colombo', 'Colombo city tour – markets, Galle Face, or shopping', 'Overnight in Colombo']],
                ['day' => 5, 'title' => 'Departure', 'activities' => ['Transfer to Colombo airport', 'Departure']],
            ],
            'included' => ['Airport transfers', '4 nights accommodation', 'Half Board', 'Turtle hatchery visit', 'Private cab'],
            'excluded' => ['International flights', 'Lunches and beverages', 'Water sports (optional)', 'Personal expenses', 'Visa', 'Travel insurance'],
            'accommodation' => [
                ['nights' => 3, 'location' => 'Bentota', 'hotel' => 'Avani Bentota Resort & Spa'],
                ['nights' => 1, 'location' => 'Colombo', 'hotel' => 'Marino Beach Colombo'],
            ],
        ];
        $list[8] = [
            'id' => 8, 'title' => 'Beach Getaway', 'location' => 'Hikkaduwa',
            'duration' => '3 Days 2 Nights', 'duration_short' => '3 Days / 2 Nights', 'image' => '/CeylonGo/public/images/sunset.jpg',
            'trending' => false, 'rating' => 4.3, 'reviews' => 67, 'category' => 'Beach', 'price' => 65000,
            'overview' => [
                'Arrival at Colombo and transfer to Hikkaduwa or Unawatuna.',
                'Beach time at Hikkaduwa — coral, beach, optional water sports.',
                'Unawatuna — beach, diving or relaxation. Departure to Colombo.',
            ],
            'highlights' => [
                ['icon' => 'hotel', 'title' => 'Accommodation', 'desc' => '2 nights beach stay'],
                ['icon' => 'transfer', 'title' => 'Transfers', 'desc' => 'Private cab for all transfers'],
                ['icon' => 'sightseeing', 'title' => 'Sightseeing', 'desc' => 'Hikkaduwa, Unawatuna beaches'],
                ['icon' => 'meals', 'title' => 'Meals', 'desc' => 'Breakfast & dinner included'],
                ['icon' => 'activities', 'title' => 'Activities', 'desc' => 'Beach, snorkelling (optional)'],
                ['icon' => 'support', 'title' => 'Support', 'desc' => '24x7 travel assistance'],
            ],
            'itinerary' => [
                ['day' => 1, 'title' => 'Arrival – Colombo to Hikkaduwa', 'activities' => ['Airport pick-up and transfer to Hikkaduwa', 'Check-in', 'Beach time and coral viewing']],
                ['day' => 2, 'title' => 'Hikkaduwa to Unawatuna (or stay Hikkaduwa)', 'activities' => ['Transfer to Unawatuna or full day at Hikkaduwa', 'Beach, optional snorkelling or diving', 'Sunset at beach']],
                ['day' => 3, 'title' => 'Departure', 'activities' => ['Transfer to Colombo airport', 'Departure']],
            ],
            'included' => ['Airport transfers', '2 nights accommodation', 'Half Board', 'Private cab'],
            'excluded' => ['International flights', 'Lunches and beverages', 'Water sports and diving (optional)', 'Personal expenses', 'Visa', 'Travel insurance'],
            'accommodation' => [
                ['nights' => 2, 'location' => 'Hikkaduwa', 'hotel' => 'Coral Sands Hotel, Hikkaduwa'],
            ],
        ];
        return isset($list[$id]) ? $list[$id] : $list[1];
    }

    public function addReview() {
        $packagesList = [
            ['id' => 1, 'title' => 'Cultural Triangle 4N5D — Kandy, Sigiriya & Dambulla'],
            ['id' => 2, 'title' => 'Southern Coast Honeymoon: Galle, Mirissa & Unawatuna 4N/5D'],
            ['id' => 3, 'title' => 'Hill Country Escape — Nuwara Eliya, Ella & Horton Plains 5N/6D'],
            ['id' => 4, 'title' => 'Ancient Heritage Trail — Temples & Forts 3N/4D'],
            ['id' => 5, 'title' => 'Wildlife Safari — Yala & Udawalawe 3N/4D'],
            ['id' => 6, 'title' => 'Solo Explorer — Colombo to Ella by Train 5N/6D'],
            ['id' => 7, 'title' => 'Family Fun — Bentota & Colombo 4N/5D'],
            ['id' => 8, 'title' => 'Beach Getaway — Hikkaduwa & Unawatuna 2N/3D'],
        ];
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

