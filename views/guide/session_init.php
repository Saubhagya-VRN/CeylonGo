<?php
// Session initialization and user data fetching for tour guides
// Include this file at the top of guide view pages

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in as guide
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'guide') {
    header('Location: /CeylonGo/public/login');
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Fetch user data from database
try {
    require_once dirname(__DIR__, 2) . "/config/config.php";
    require_once dirname(__DIR__, 2) . "/core/Database.php";
    require_once dirname(__DIR__, 2) . "/models/Guide.php";
    
    $db = Database::getConnection();
    $guideModel = new Guide($db);
    $user_data = null;
    
    // Try to get guide by ID first
    if (!empty($user_id)) {
        $user_data = $guideModel->getGuideById($user_id);
    }
    
    // If guide not found by ID, try by email (fallback for NULL ref_id cases)
    if (!$user_data && isset($_SESSION['user_email'])) {
        $user_data = $guideModel->getGuideByEmail($_SESSION['user_email']);
        
        // Update session with correct user_id if found
        if ($user_data && isset($user_data['id'])) {
            $_SESSION['user_id'] = $user_data['id'];
            $user_id = $user_data['id'];
        }
    }
    
    // If still no user data, redirect to login
    if (!$user_data) {
        header('Location: /CeylonGo/public/login');
        exit();
    }
    
    // Set profile picture path with fallback to default
    if (!empty($user_data['profile_photo'])) {
        $profile_picture = "/CeylonGo/public/uploads/guide/" . $user_data['profile_photo'];
    } else {
        $profile_picture = "/CeylonGo/public/images/profile.jpg";
    }
    
    // Store user name for display
    $user_name = trim(($user_data['first_name'] ?? '') . ' ' . ($user_data['last_name'] ?? ''));
    if (empty($user_name)) {
        $user_name = 'Guide';
    }
    
} catch (Exception $e) {
    // Fallback to default image if there's an error
    $profile_picture = "/CeylonGo/public/images/profile.jpg";
    $user_name = 'Guide';
}
?>
