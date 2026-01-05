<?php
// Session initialization and user data fetching for transport providers
// Include this file at the top of transport view pages

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['transporter_id'])) {
    header('Location: /CeylonGo/public/login');
    exit();
}

// Get user ID from session
$user_id = trim($_SESSION['transporter_id']);

// Fetch user data from database
try {
    require_once dirname(__DIR__, 2) . "/config/config.php";
    require_once dirname(__DIR__, 2) . "/core/Database.php";
    require_once dirname(__DIR__, 2) . "/models/User.php";
    
    $db = Database::getConnection();
    $userModel = new User($db);
    $user_data = $userModel->getUserById($user_id);
    
    // Set profile picture path with fallback to default
    if (!empty($user_data['profile_image']) && file_exists(dirname(__DIR__, 2) . "/uploads/" . $user_data['profile_image'])) {
        $profile_picture = "/CeylonGo/uploads/" . $user_data['profile_image'];
    } else {
        $profile_picture = "/CeylonGO/public/images/profile.jpg";
    }
    
    // Store user name for display
    $user_name = $user_data['full_name'] ?? 'User';
    
} catch (Exception $e) {
    // Fallback to default image if there's an error
    $profile_picture = "/CeylonGO/public/images/profile.jpg";
    $user_name = 'User';
}
?>
