<?php
// controllers/tourist/SelectionHandler.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle hotel selection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'select_hotel') {
    $hotel_name = isset($_POST['hotel_name']) ? htmlspecialchars($_POST['hotel_name']) : '';
    $group_index = isset($_POST['group_index']) ? intval($_POST['group_index']) : 0;
    
    if (!isset($_SESSION['trip_form_data'])) {
        $_SESSION['trip_form_data'] = array();
    }
    
    if (!isset($_SESSION['trip_form_data']['hotels'])) {
        $_SESSION['trip_form_data']['hotels'] = array();
    }
    
    $_SESSION['trip_form_data']['hotels'][$group_index] = $hotel_name;
    
    // Return to previous page
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

// Handle transport selection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'select_transport') {
    $transport_name = isset($_POST['transport_name']) ? htmlspecialchars($_POST['transport_name']) : '';
    $group_index = isset($_POST['group_index']) ? intval($_POST['group_index']) : 0;
    
    if (!isset($_SESSION['trip_form_data'])) {
        $_SESSION['trip_form_data'] = array();
    }
    
    if (!isset($_SESSION['trip_form_data']['transports'])) {
        $_SESSION['trip_form_data']['transports'] = array();
    }
    
    $_SESSION['trip_form_data']['transports'][$group_index] = $transport_name;
    
    // Return to previous page
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

// Handle guide selection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'select_guide') {
    $guide_name = isset($_POST['guide_name']) ? htmlspecialchars($_POST['guide_name']) : 'No';
    
    if (!isset($_SESSION['trip_form_data'])) {
        $_SESSION['trip_form_data'] = array();
    }
    
    $_SESSION['trip_form_data']['guide'] = $guide_name;
    
    // Return to previous page
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

?>
