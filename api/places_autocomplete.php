<?php
// api/places_autocomplete.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once dirname(__DIR__) . '/config/config.php';

// Get the search query
$input = isset($_GET['input']) ? trim($_GET['input']) : '';

if (empty($input)) {
    echo json_encode(['predictions' => []]);
    exit;
}

// Google Places API key (server-side only)
$apiKey = 'AIzaSyAcuxrNlQgmK79qliHORas-sKGhU9OXPIo';

// Build API URL - Autocomplete for Sri Lanka locations
$apiUrl = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?' . http_build_query([
    'input' => $input,
    'components' => 'country:lk', // Restrict to Sri Lanka
    'key' => $apiKey
]);

// Make the request to Google Places API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo json_encode(['predictions' => [], 'error' => 'API request failed', 'status' => $httpCode]);
    exit;
}

// Decode and check for Google API errors
$data = json_decode($response, true);

if (isset($data['error_message'])) {
    // Google API returned an error (billing, quota, etc.)
    echo json_encode([
        'predictions' => [],
        'error' => $data['error_message'],
        'status' => $data['status'] ?? 'UNKNOWN'
    ]);
    exit;
}

// Return the predictions
echo $response;
