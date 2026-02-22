<?php
/**
 * API Entry Point
 * Handles all API requests
 */

// Load bootstrap
require_once '../core/bootstrap.php';

// Set JSON header
header('Content-Type: application/json');

// Handle CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/CeylonGo/public/api', '', $path);
$path = trim($path, '/');

// Simple routing for API
$segments = explode('/', $path);

try {
    // Example API routing structure
    switch ($segments[0]) {
        case 'guide-requests':
            require_once '../api/GuideRequestAPI.php';
            $api = new GuideRequestAPI();
            $api->handleRequest($method, $segments);
            break;
            
        case 'transport-requests':
            require_once '../api/TransportRequestAPI.php';
            $api = new TransportRequestAPI();
            $api->handleRequest($method, $segments);
            break;
            
        case 'tourists':
            require_once '../api/TouristAPI.php';
            $api = new TouristAPI();
            $api->handleRequest($method, $segments);
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
            break;
    }
} catch (Exception $e) {
    error_log('API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
