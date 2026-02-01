<?php
// api/locations.php - Local Sri Lankan locations database (No external libraries)
header('Content-Type: application/json');

$input = isset($_GET['input']) ? strtolower(trim($_GET['input'])) : '';

if (empty($input)) {
    echo json_encode(['predictions' => []]);
    exit;
}

// Comprehensive list of Sri Lankan locations (cities, hotels, landmarks, tourist spots)
$locations = [
    // Major Cities
    'Colombo, Sri Lanka',
    'Kandy, Sri Lanka',
    'Galle, Sri Lanka',
    'Jaffna, Sri Lanka',
    'Negombo, Sri Lanka',
    'Trincomalee, Sri Lanka',
    'Anuradhapura, Sri Lanka',
    'Polonnaruwa, Sri Lanka',
    'Nuwara Eliya, Sri Lanka',
    'Ella, Sri Lanka',
    'Matara, Sri Lanka',
    'Batticaloa, Sri Lanka',
    'Ratnapura, Sri Lanka',
    'Badulla, Sri Lanka',
    'Vavuniya, Sri Lanka',
    'Kurunegala, Sri Lanka',
    'Gampaha, Sri Lanka',
    'Kalutara, Sri Lanka',
    'Ampara, Sri Lanka',
    'Hambantota, Sri Lanka',
    
    // Tourist Destinations & Beaches
    'Sigiriya, Sri Lanka',
    'Mirissa, Sri Lanka',
    'Bentota, Sri Lanka',
    'Hikkaduwa, Sri Lanka',
    'Arugam Bay, Sri Lanka',
    'Dambulla, Sri Lanka',
    'Unawatuna, Sri Lanka',
    'Tangalle, Sri Lanka',
    'Weligama, Sri Lanka',
    'Koggala, Sri Lanka',
    'Beruwala, Sri Lanka',
    'Pasikudah, Sri Lanka',
    'Nilaveli, Sri Lanka',
    'Uppuveli, Sri Lanka',
    
    // Historical & Cultural Sites
    'Galle Fort, Galle',
    'Temple of the Tooth, Kandy',
    'Sigiriya Rock Fortress',
    'Dambulla Cave Temple',
    'Anuradhapura Ancient City',
    'Polonnaruwa Ancient City',
    'Mihintale, Anuradhapura',
    'Yapahuwa Rock Fortress',
    
    // National Parks & Natural Attractions
    'Yala National Park',
    'Udawalawe National Park',
    'Horton Plains National Park',
    'Wilpattu National Park',
    'Minneriya National Park',
    'Kaudulla National Park',
    'Sinharaja Rainforest',
    'Knuckles Mountain Range',
    'Adam\'s Peak (Sri Pada)',
    'Pidurangala Rock',
    'Little Adam\'s Peak, Ella',
    'Nine Arch Bridge, Ella',
    'Ravana Falls, Ella',
    
    // Popular Hotels & Resorts
    'Jetwing Lighthouse, Galle',
    'Jetwing Beach, Negombo',
    'Jetwing Vil Uyana, Sigiriya',
    'Jetwing Lake, Dambulla',
    'Cinnamon Grand, Colombo',
    'Cinnamon Lakeside, Colombo',
    'Galle Face Hotel, Colombo',
    'Shangri-La Colombo',
    'Hilton Colombo',
    'Taj Samudra, Colombo',
    'Heritance Kandalama, Dambulla',
    'Heritance Tea Factory, Nuwara Eliya',
    'Amangalla, Galle',
    'Cape Weligama',
    'Anantara Peace Haven, Tangalle',
    'Uga Bay, Pasikudah',
    
    // Hill Country
    'Nuwara Eliya Town',
    'Hatton, Sri Lanka',
    'Haputale, Sri Lanka',
    'Bandarawela, Sri Lanka',
    'Welimada, Sri Lanka',
    
    // Other Notable Places
    'Pinnawala Elephant Orphanage',
    'Udawalawe Elephant Transit Home',
    'Bundala National Park',
    'Ritigala Forest Monastery',
    'Mulkirigala Rock Temple',
    'Buduruwagala Temple',
    'Aukana Buddha Statue'
];

// Filter locations based on input
$matches = [];
foreach ($locations as $location) {
    if (stripos($location, $input) !== false) {
        $matches[] = ['description' => $location];
    }
}

// Sort by relevance (starts with query first)
usort($matches, function($a, $b) use ($input) {
    $aStarts = stripos($a['description'], $input) === 0;
    $bStarts = stripos($b['description'], $input) === 0;
    if ($aStarts && !$bStarts) return -1;
    if (!$aStarts && $bStarts) return 1;
    return 0;
});

// Limit to 10 results
$matches = array_slice($matches, 0, 10);

echo json_encode(['predictions' => $matches]);
