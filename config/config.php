<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ceylon_go');
define('DB_USER', 'root');
define('DB_PASS', '');

// App path constants
// Project root directory (one level up from this config folder)
define('BASE_PATH', dirname(__DIR__));
// Absolute path to the public web root
define('PUBLIC_PATH', BASE_PATH . '/public');
// Absolute path to views directory
define('VIEW_PATH', BASE_PATH . '/views');
// Absolute path to uploads directory (served from public)
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');

// Base URL for the app (used for API links in views)
define('BASE_URL', '/CeylonGo/public');

// Google Maps API Key for stop locations (server-side only)
define('GOOGLE_MAPS_API_KEY', 'AIzaSyBFdoF5vZo-egKRXYOOoySVbtuvkpFEOKY');
