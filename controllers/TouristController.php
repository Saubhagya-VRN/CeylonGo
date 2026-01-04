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

