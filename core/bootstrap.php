<?php
/**
 * Bootstrap File
 * Initializes the application environment
 * This file is loaded by all entry points (web, API, CLI)
 */

// Start session (only if not already started)
if (session_status() === PHP_SESSION_NONE) {
    // Extend session lifetime to 8 hours and ensure cookie covers entire app
    ini_set('session.gc_maxlifetime', 28800);
    ini_set('session.cookie_lifetime', 28800);
    session_set_cookie_params([
        'lifetime' => 28800,
        'path' => '/CeylonGo/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Set error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set default timezone
date_default_timezone_set('Asia/Colombo');

// Load configuration (this will define BASE_PATH)
require_once dirname(__DIR__) . '/config/config.php';

// Composer first so PSR-4 packages (e.g. Dompdf) resolve before the app autoloader.
if (is_readable(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}
require_once BASE_PATH . '/core/autoload.php';
require_once BASE_PATH . '/core/helpers.php';
require_once BASE_PATH . '/core/Database.php';

// Set up error handling for production (uncomment when deploying)
// error_reporting(0);
// ini_set('display_errors', 0);
// set_exception_handler(function($e) {
//     error_log($e->getMessage());
//     if (!headers_sent()) {
//         http_response_code(500);
//         echo json_encode(['error' => 'Internal server error']);
//     }
// });
