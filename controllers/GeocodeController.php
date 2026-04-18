<?php
class GeocodeController {
    /**
     * Places autocomplete for stop locations (Google Places API, Sri Lanka).
     * Optional GET 'district' biases results to that area so stops match where the tourist is staying.
     * Fallback: Uses a local database of Sri Lankan locations if Google API returns no results.
     */
    public function placesAutocomplete() {
        if (ob_get_level()) ob_end_clean();
        ob_start();
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Origin: *');

        $input = isset($_GET['input']) ? trim($_GET['input']) : '';
        if (strlen($input) < 2) {
            ob_end_clean();
            echo json_encode(['predictions' => []]);
            return;
        }

        $predictions = [];
        $apiKey = defined('GOOGLE_MAPS_API_KEY') ? GOOGLE_MAPS_API_KEY : '';
        
        // 1. Try Google Places API
        if (!empty($apiKey)) {
            $params = [
                'input' => $input,
                'components' => 'country:lk',
                'language' => 'en',
                'key' => $apiKey
            ];
            $district = isset($_GET['district']) ? trim(strtolower($_GET['district'])) : '';
            if ($district !== '' && isset(self::$districtCenter[$district])) {
                $center = self::$districtCenter[$district];
                $params['locationbias'] = 'circle:50000@' . $center['lat'] . ',' . $center['lon'];
            }
            $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?' . http_build_query($params);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                if (!isset($data['error_message']) && !empty($data['predictions']) && is_array($data['predictions'])) {
                    $predictions = $data['predictions'];
                }
            }
        }

        // 2. Fallback to local database if no predictions found (or API empty/errored)
        if (empty($predictions)) {
            $lowerInput = strtolower($input);
            foreach (self::$localLocations as $loc) {
                if (stripos($loc, $lowerInput) !== false) {
                    $predictions[] = [
                        'description' => $loc,
                        'place_id' => '', // Local matches don't have place IDs
                        'types' => ['locality', 'political'],
                        'terms' => [['offset' => 0, 'value' => $loc]]
                    ];
                }
                if (count($predictions) >= 10) break;
            }
        }

        ob_end_clean();
        echo json_encode(['predictions' => $predictions]);
    }

    /** Local Sri Lankan locations database (fallback) */
    private static $localLocations = [
        'Colombo, Sri Lanka', 'Kandy, Sri Lanka', 'Galle, Sri Lanka', 'Jaffna, Sri Lanka', 'Negombo, Sri Lanka',
        'Trincomalee, Sri Lanka', 'Anuradhapura, Sri Lanka', 'Polonnaruwa, Sri Lanka', 'Nuwara Eliya, Sri Lanka',
        'Ella, Sri Lanka', 'Matara, Sri Lanka', 'Batticaloa, Sri Lanka', 'Ratnapura, Sri Lanka', 'Badulla, Sri Lanka',
        'Vavuniya, Sri Lanka', 'Kurunegala, Sri Lanka', 'Gampaha, Sri Lanka', 'Kalutara, Sri Lanka', 'Ampara, Sri Lanka',
        'Hambantota, Sri Lanka', 'Sigiriya, Sri Lanka', 'Mirissa, Sri Lanka', 'Bentota, Sri Lanka', 'Hikkaduwa, Sri Lanka',
        'Arugam Bay, Sri Lanka', 'Dambulla, Sri Lanka', 'Unawatuna, Sri Lanka', 'Tangalle, Sri Lanka', 'Weligama, Sri Lanka',
        'Koggala, Sri Lanka', 'Beruwala, Sri Lanka', 'Pasikudah, Sri Lanka', 'Nilaveli, Sri Lanka', 'Uppuveli, Sri Lanka',
        'Galle Fort, Galle', 'Temple of the Tooth, Kandy', 'Sigiriya Rock Fortress', 'Dambulla Cave Temple',
        'Anuradhapura Ancient City', 'Polonnaruwa Ancient City', 'Mihintale, Anuradhapura', 'Yapahuwa Rock Fortress',
        'Yala National Park', 'Udawalawe National Park', 'Horton Plains National Park', 'Wilpattu National Park',
        'Minneriya National Park', 'Kaudulla National Park', 'Sinharaja Rainforest', 'Knuckles Mountain Range',
        "Adam's Peak (Sri Pada)", 'Pidurangala Rock', "Little Adam's Peak, Ella", 'Nine Arch Bridge, Ella',
        'Ravana Falls, Ella', 'Jetwing Lighthouse, Galle', 'Jetwing Beach, Negombo', 'Jetwing Vil Uyana, Sigiriya',
        'Jetwing Lake, Dambulla', 'Cinnamon Grand, Colombo', 'Cinnamon Lakeside, Colombo', 'Galle Face Hotel, Colombo',
        'Shangri-La Colombo', 'Hilton Colombo', 'Taj Samudra, Colombo', 'Heritance Kandalama, Dambulla',
        'Heritance Tea Factory, Nuwara Eliya', 'Amangalla, Galle', 'Cape Weligama', 'Anantara Peace Haven, Tangalle',
        'Uga Bay, Pasikudah', 'Nuwara Eliya Town', 'Hatton, Sri Lanka', 'Haputale, Sri Lanka', 'Bandarawela, Sri Lanka',
        'Welimada, Sri Lanka', 'Pinnawala Elephant Orphanage', 'Udawalawe Elephant Transit Home', 'Bundala National Park',
        'Ritigala Forest Monastery', 'Mulkirigala Rock Temple', 'Buduruwagala Temple', 'Aukana Buddha Statue'
    ];

    /** District centers (lat/lon) for biasing stop suggestions to the selected destination area */
    private static $districtCenter = [
        'ampara' => ['lat' => 7.2833, 'lon' => 81.6667],
        'anuradhapura' => ['lat' => 8.3114, 'lon' => 80.4037],
        'badulla' => ['lat' => 6.9934, 'lon' => 81.0550],
        'batticaloa' => ['lat' => 7.7310, 'lon' => 81.6747],
        'colombo' => ['lat' => 6.9271, 'lon' => 79.8612],
        'galle' => ['lat' => 6.0535, 'lon' => 80.2210],
        'gampaha' => ['lat' => 7.0917, 'lon' => 79.9942],
        'hambantota' => ['lat' => 6.1429, 'lon' => 81.1212],
        'jaffna' => ['lat' => 9.6615, 'lon' => 80.0255],
        'kalutara' => ['lat' => 6.5854, 'lon' => 79.9607],
        'kandy' => ['lat' => 7.2906, 'lon' => 80.6337],
        'kegalle' => ['lat' => 7.2533, 'lon' => 80.3436],
        'kilinochchi' => ['lat' => 9.4000, 'lon' => 80.4000],
        'kurunegala' => ['lat' => 7.4863, 'lon' => 80.3623],
        'mannar' => ['lat' => 8.9833, 'lon' => 79.9000],
        'matale' => ['lat' => 7.4717, 'lon' => 80.6242],
        'matara' => ['lat' => 5.9549, 'lon' => 80.5550],
        'monaragala' => ['lat' => 6.8714, 'lon' => 81.3486],
        'mullaitivu' => ['lat' => 9.2667, 'lon' => 80.8167],
        'nuwara-eliya' => ['lat' => 6.9497, 'lon' => 80.7891],
        'polonnaruwa' => ['lat' => 7.9403, 'lon' => 81.0188],
        'puttalam' => ['lat' => 8.0333, 'lon' => 79.8333],
        'ratnapura' => ['lat' => 6.7056, 'lon' => 80.3847],
        'trincomalee' => ['lat' => 8.5874, 'lon' => 81.2152],
        'vavuniya' => ['lat' => 8.7500, 'lon' => 80.5000]
    ];

    // Sri Lankan cities database with coordinates
    private static $cities = [
        'colombo' => ['lat' => 6.9271, 'lon' => 79.8612, 'name' => 'Colombo'],
        'kandy' => ['lat' => 7.2906, 'lon' => 80.6337, 'name' => 'Kandy'],
        'galle' => ['lat' => 6.0535, 'lon' => 80.2210, 'name' => 'Galle'],
        'galle fort' => ['lat' => 6.0267, 'lon' => 80.2170, 'name' => 'Galle Fort'],
        'negombo' => ['lat' => 7.2008, 'lon' => 79.8358, 'name' => 'Negombo'],
        'negombo beach' => ['lat' => 7.2094, 'lon' => 79.8358, 'name' => 'Negombo Beach'],
        'jaffna' => ['lat' => 9.6615, 'lon' => 80.0255, 'name' => 'Jaffna'],
        'trincomalee' => ['lat' => 8.5874, 'lon' => 81.2152, 'name' => 'Trincomalee'],
        'batticaloa' => ['lat' => 7.7310, 'lon' => 81.6747, 'name' => 'Batticaloa'],
        'matara' => ['lat' => 5.9549, 'lon' => 80.5550, 'name' => 'Matara'],
        'anuradhapura' => ['lat' => 8.3114, 'lon' => 80.4037, 'name' => 'Anuradhapura'],
        'polonnaruwa' => ['lat' => 7.9403, 'lon' => 81.0188, 'name' => 'Polonnaruwa'],
        'badulla' => ['lat' => 6.9934, 'lon' => 81.0550, 'name' => 'Badulla'],
        'ratnapura' => ['lat' => 6.7056, 'lon' => 80.3847, 'name' => 'Ratnapura'],
        'nuwara eliya' => ['lat' => 6.9497, 'lon' => 80.7891, 'name' => 'Nuwara Eliya'],
        'ella' => ['lat' => 6.8667, 'lon' => 81.0467, 'name' => 'Ella'],
        'sigiriya' => ['lat' => 7.9569, 'lon' => 80.7603, 'name' => 'Sigiriya'],
        'dambulla' => ['lat' => 7.8742, 'lon' => 80.6517, 'name' => 'Dambulla'],
        'bentota' => ['lat' => 6.4218, 'lon' => 79.9951, 'name' => 'Bentota'],
        'hikkaduwa' => ['lat' => 6.1408, 'lon' => 80.1034, 'name' => 'Hikkaduwa'],
        'mirissa' => ['lat' => 5.9467, 'lon' => 80.4517, 'name' => 'Mirissa'],
        'arugam bay' => ['lat' => 6.8406, 'lon' => 81.8364, 'name' => 'Arugam Bay'],
        'unawatuna' => ['lat' => 6.0100, 'lon' => 80.2497, 'name' => 'Unawatuna'],
        'mount lavinia' => ['lat' => 6.8406, 'lon' => 79.8628, 'name' => 'Mount Lavinia'],
        'kalutara' => ['lat' => 6.5854, 'lon' => 79.9607, 'name' => 'Kalutara'],
        'kurunegala' => ['lat' => 7.4863, 'lon' => 80.3623, 'name' => 'Kurunegala'],
        'hambantota' => ['lat' => 6.1429, 'lon' => 81.1212, 'name' => 'Hambantota'],
        'katunayake' => ['lat' => 7.1696, 'lon' => 79.8842, 'name' => 'Katunayake'],
        'airport' => ['lat' => 7.1808, 'lon' => 79.8841, 'name' => 'Colombo Airport'],
        'cia' => ['lat' => 7.1808, 'lon' => 79.8841, 'name' => 'Colombo Airport'],
        'bandaranaike airport' => ['lat' => 7.1808, 'lon' => 79.8841, 'name' => 'Bandaranaike Airport'],
        'mount lavinia beach' => ['lat' => 6.8328, 'lon' => 79.8631, 'name' => 'Mount Lavinia Beach'],
        'bentota beach' => ['lat' => 6.4257, 'lon' => 79.9974, 'name' => 'Bentota Beach'],
        'yala' => ['lat' => 6.3725, 'lon' => 81.5185, 'name' => 'Yala National Park'],
        'yala national park' => ['lat' => 6.3725, 'lon' => 81.5185, 'name' => 'Yala National Park'],
        'udawalawe' => ['lat' => 6.4425, 'lon' => 80.8864, 'name' => 'Udawalawe'],
        'wilpattu' => ['lat' => 8.4833, 'lon' => 80.0333, 'name' => 'Wilpattu National Park'],
        'horton plains' => ['lat' => 6.8097, 'lon' => 80.7988, 'name' => 'Horton Plains'],
        'adams peak' => ['lat' => 6.8094, 'lon' => 80.4994, 'name' => 'Adams Peak'],
        'sri pada' => ['lat' => 6.8094, 'lon' => 80.4994, 'name' => 'Sri Pada'],
        'pidurangala' => ['lat' => 7.9617, 'lon' => 80.7550, 'name' => 'Pidurangala Rock'],
        'temple of tooth' => ['lat' => 7.2937, 'lon' => 80.6408, 'name' => 'Temple of the Tooth'],
        'botanical garden' => ['lat' => 7.2733, 'lon' => 80.5967, 'name' => 'Peradeniya Botanical Garden'],
        'peradeniya' => ['lat' => 7.2667, 'lon' => 80.6000, 'name' => 'Peradeniya']
    ];

    public function geocode() {
        header('Content-Type: application/json');
        
        // Get location from query parameter
        $location = isset($_GET['location']) ? trim($_GET['location']) : '';
        
        if (empty($location)) {
            http_response_code(400);
            echo json_encode(['error' => 'Location parameter is required']);
            return;
        }

        // Normalize the location
        $normalized = strtolower($location);
        $normalized = str_replace(', sri lanka', '', $normalized);
        $normalized = str_replace(' sri lanka', '', $normalized);
        $normalized = trim($normalized);

        // Check exact match in our database
        if (isset(self::$cities[$normalized])) {
            echo json_encode([
                'success' => true,
                'source' => 'local_database',
                'location' => $location,
                'lat' => self::$cities[$normalized]['lat'],
                'lon' => self::$cities[$normalized]['lon'],
                'name' => self::$cities[$normalized]['name']
            ]);
            return;
        }

        // Try partial match
        foreach (self::$cities as $key => $value) {
            if (strpos($normalized, $key) !== false || strpos($key, $normalized) !== false) {
                echo json_encode([
                    'success' => true,
                    'source' => 'local_database_partial',
                    'location' => $location,
                    'lat' => $value['lat'],
                    'lon' => $value['lon'],
                    'name' => $value['name']
                ]);
                return;
            }
        }

        // Fallback to Nominatim API (server-side, no CORS issues)
        $result = $this->geocodeWithNominatim($location);
        
        if ($result) {
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Location not found',
                'location' => $location,
                'suggestion' => 'Try using city names like: Colombo, Kandy, Galle, Negombo, Ella, Sigiriya, Nuwara Eliya'
            ]);
        }
    }

    private function geocodeWithNominatim($location) {
        $query = urlencode($location . ', Sri Lanka');
        $url = "https://nominatim.openstreetmap.org/search?format=json&q={$query}&limit=1";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'CeylonGo/1.0 (Travel Planning App)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local development
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            
            if (!empty($data) && isset($data[0])) {
                return [
                    'success' => true,
                    'source' => 'nominatim_api',
                    'location' => $location,
                    'lat' => floatval($data[0]['lat']),
                    'lon' => floatval($data[0]['lon']),
                    'name' => $data[0]['display_name']
                ];
            }
        }
        
        return null;
    }

    public function calculateFare() {
        header('Content-Type: application/json');
        
        // Get parameters
        $pickup = isset($_GET['pickup']) ? trim($_GET['pickup']) : '';
        $dropoff = isset($_GET['dropoff']) ? trim($_GET['dropoff']) : '';
        $vehicleType = isset($_GET['vehicleType']) ? trim($_GET['vehicleType']) : '';
        $pickupPlaceId = isset($_GET['pickup_place_id']) ? trim($_GET['pickup_place_id']) : '';
        $dropoffPlaceId = isset($_GET['dropoff_place_id']) ? trim($_GET['dropoff_place_id']) : '';
        
        if (empty($pickup) || empty($dropoff) || empty($vehicleType)) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required parameters: pickup, dropoff, vehicleType']);
            return;
        }

        // Geocode both locations (needed for response; also used if place_id not available)
        $pickupCoords = $this->geocodeLocationForFare($pickup);
        $dropoffCoords = $this->geocodeLocationForFare($dropoff);
        if (!$pickupCoords) {
            http_response_code(404);
            echo json_encode(['error' => 'Pickup location not found', 'location' => $pickup]);
            return;
        }
        if (!$dropoffCoords) {
            http_response_code(404);
            echo json_encode(['error' => 'Dropoff location not found', 'location' => $dropoff]);
            return;
        }

        // Use Google Distance Matrix API: place_id preferred (most accurate), else coordinates
        $apiKey = defined('GOOGLE_MAPS_API_KEY') ? GOOGLE_MAPS_API_KEY : '';
        $distance = null;
        $origins = (!empty($pickupPlaceId)) ? 'place_id:' . $pickupPlaceId : ($pickupCoords['lat'] . ',' . $pickupCoords['lon']);
        $destinations = (!empty($dropoffPlaceId)) ? 'place_id:' . $dropoffPlaceId : ($dropoffCoords['lat'] . ',' . $dropoffCoords['lon']);

        if (!empty($apiKey)) {
            $params = [
                'origins' => $origins,
                'destinations' => $destinations,
                'key' => $apiKey,
                'region' => 'lk',
                'units' => 'metric',
                'mode' => 'driving'
            ];
            $url = 'https://maps.googleapis.com/maps/api/distancematrix/json?' . http_build_query($params);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            $response = curl_exec($ch);
            curl_close($ch);
            if ($response) {
                $data = json_decode($response, true);
                if (!empty($data['rows'][0]['elements'][0]['status']) && $data['rows'][0]['elements'][0]['status'] === 'OK') {
                    $elem = $data['rows'][0]['elements'][0];
                    $meters = isset($elem['distance']['value']) ? (float) $elem['distance']['value'] : 0;
                    $distance = $meters / 1000; // Convert to km
                }
            }
        }

        // Fallback to Haversine if Distance Matrix unavailable or failed
        if ($distance === null || $distance <= 0) {
            $distance = $this->calculateDistance(
                $pickupCoords['lat'], $pickupCoords['lon'],
                $dropoffCoords['lat'], $dropoffCoords['lon']
            );
        }

        // Define fare rates per km (LKR)
        $fareRates = [
            'Tuk' => 80,
            'Car' => 120,
            'Minivan' => 150,
            'Minivan AC' => 180,
            'Bus' => 200,
            'Bus AC' => 250,
            // Legacy names
            'Van' => 150,
            'Three-Wheeler' => 80,
            'Bike' => 60
        ];

        $baseRate = isset($fareRates[$vehicleType]) ? $fareRates[$vehicleType] : 100;
        $totalFare = round($distance * $baseRate, 2);

        echo json_encode([
            'success' => true,
            'pickup' => [
                'location' => $pickup,
                'name' => $pickupCoords['name'],
                'lat' => $pickupCoords['lat'],
                'lon' => $pickupCoords['lon']
            ],
            'dropoff' => [
                'location' => $dropoff,
                'name' => $dropoffCoords['name'],
                'lat' => $dropoffCoords['lat'],
                'lon' => $dropoffCoords['lon']
            ],
            'distance' => round($distance, 2),
            'vehicleType' => $vehicleType,
            'baseRate' => $baseRate,
            'totalFare' => $totalFare,
            'currency' => 'LKR'
        ]);
    }

    /**
     * Geocode for fare calculation: prioritises Google (region=lk) for Sri Lankan addresses
     * from Places autocomplete, to avoid wrong resolution (e.g. Anula Vidyalaya in Nugegoda).
     */
    private function geocodeLocationForFare($location) {
        $normalized = strtolower(trim($location));
        $normalized = str_replace([', sri lanka', ' sri lanka'], '', $normalized);
        $normalized = trim($normalized);

        // Check exact match in cities
        if (isset(self::$cities[$normalized])) {
            $c = self::$cities[$normalized];
            return ['lat' => $c['lat'], 'lon' => $c['lon'], 'name' => $c['name']];
        }

        // Try partial match
        foreach (self::$cities as $key => $value) {
            if (strpos($normalized, $key) !== false || strpos($key, $normalized) !== false) {
                return ['lat' => $value['lat'], 'lon' => $value['lon'], 'name' => $value['name']];
            }
        }

        // Prefer Google Geocoding (region=lk) for Places-style addresses - more accurate for Sri Lanka
        $apiKey = defined('GOOGLE_MAPS_API_KEY') ? GOOGLE_MAPS_API_KEY : '';
        if (!empty($apiKey)) {
            $address = strpos($location, 'Sri Lanka') !== false ? $location : $location . ', Sri Lanka';
            $params = ['address' => $address, 'region' => 'lk', 'key' => $apiKey];
            // Bias to Colombo metro when address suggests Colombo/Nugegoda - avoids wrong Anula Vidyalaya elsewhere
            $n = $normalized;
            if (strpos($n, 'nugegoda') !== false || strpos($n, 'colombo') !== false || strpos($n, 'gampaha') !== false || strpos($n, 'dehiwala') !== false || strpos($n, 'maharagama') !== false || strpos($n, 'boralesgamuwa') !== false || strpos($n, 'kotte') !== false) {
                $params['bounds'] = '6.80,79.75|6.98,80.10'; // Colombo metro viewport (SW|NE)
            }
            $url = 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($params);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            curl_close($ch);
            if ($response) {
                $data = json_decode($response, true);
                if (!empty($data['results'][0]) && $data['status'] === 'OK') {
                    $r = $data['results'][0];
                    return [
                        'lat' => $r['geometry']['location']['lat'],
                        'lon' => $r['geometry']['location']['lng'],
                        'name' => $r['formatted_address'] ?? $location
                    ];
                }
            }
        }

        // Fallback to Nominatim
        $result = $this->geocodeWithNominatim($location);
        if ($result && $result['success']) {
            return [
                'lat' => $result['lat'],
                'lon' => $result['lon'],
                'name' => $result['name']
            ];
        }

        return null;
    }

    private function geocodeLocation($location) {
        $normalized = strtolower(trim($location));
        $normalized = str_replace([', sri lanka', ' sri lanka'], '', $normalized);
        $normalized = trim($normalized);

        // Check exact match
        if (isset(self::$cities[$normalized])) {
            return self::$cities[$normalized];
        }

        // Try partial match
        foreach (self::$cities as $key => $value) {
            if (strpos($normalized, $key) !== false || strpos($key, $normalized) !== false) {
                return $value;
            }
        }

        // Try Nominatim API
        $result = $this->geocodeWithNominatim($location);
        if ($result && $result['success']) {
            return [
                'lat' => $result['lat'],
                'lon' => $result['lon'],
                'name' => $result['name']
            ];
        }

        // Try Google Geocoding API for addresses from Places autocomplete
        $apiKey = defined('GOOGLE_MAPS_API_KEY') ? GOOGLE_MAPS_API_KEY : '';
        if (!empty($apiKey)) {
            $address = strpos($location, 'Sri Lanka') !== false ? $location : $location . ', Sri Lanka';
            $url = 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query(['address' => $address, 'region' => 'lk', 'key' => $apiKey]);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            curl_close($ch);
            if ($response) {
                $data = json_decode($response, true);
                if (!empty($data['results'][0]) && $data['status'] === 'OK') {
                    $r = $data['results'][0];
                    return [
                        'lat' => $r['geometry']['location']['lat'],
                        'lon' => $r['geometry']['location']['lng'],
                        'name' => $r['formatted_address'] ?? $location
                    ];
                }
            }
        }

        return null;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $R = 6371; // Earth's radius in kilometers
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $R * $c;
        
        return $distance;
    }
}
