<?php
// package_details.php

$package = isset($_GET['package']) ? $_GET['package'] : 'cultural';

// Package data
$packages = array(
    'cultural' => array(
        'title' => 'Cultural Experience in Sri Lanka',
        'duration' => '5 days, 4 nights',
        'price' => 'Rs. 8990',
        'image' => 'cultural.jpeg',
        'description' => 'Immerse yourself in the rich cultural heritage of Sri Lanka with this comprehensive tour that takes you through ancient cities, temples, and traditional villages.',
        'highlights' => array(
            'Visit the ancient city of Anuradhapura',
            'Explore the Temple of the Tooth in Kandy',
            'Experience traditional Kandyan dance',
            'Tour the Dambulla Cave Temple',
            'Visit a local spice garden'
        ),
        'itinerary' => array(
            'Day 1: Arrival in Colombo, transfer to Anuradhapura',
            'Day 2: Explore Anuradhapura ancient city',
            'Day 3: Travel to Kandy, visit Temple of the Tooth',
            'Day 4: Dambulla Cave Temple and spice garden tour',
            'Day 5: Return to Colombo, departure'
        ),
        'includes' => array(
            '4 nights accommodation',
            'All meals',
            'Professional guide',
            'Transportation',
            'Entrance fees',
            'Cultural show tickets'
        )
    ),
    'beach' => array(
        'title' => 'Tropical Beach Retreat',
        'duration' => '5 days, 4 nights',
        'price' => 'Rs. 7990',
        'image' => 'beach.jpg',
        'description' => 'Relax and unwind on Sri Lanka\'s pristine beaches with this tropical retreat that combines luxury accommodation with stunning coastal scenery.',
        'highlights' => array(
            'Stay at luxury beachfront resorts',
            'Snorkeling and diving activities',
            'Whale watching tour',
            'Beach yoga sessions',
            'Sunset dinner cruises'
        ),
        'itinerary' => array(
            'Day 1: Arrival in Colombo, transfer to Unawatuna',
            'Day 2: Beach activities and snorkeling',
            'Day 3: Whale watching tour',
            'Day 4: Relaxation and spa treatments',
            'Day 5: Return to Colombo, departure'
        ),
        'includes' => array(
            '4 nights beachfront accommodation',
            'All meals',
            'Snorkeling equipment',
            'Whale watching tour',
            'Spa treatments',
            'Transportation'
        )
    ),
    'adventure' => array(
        'title' => 'Adventure in the Hills',
        'duration' => '5 days, 4 nights',
        'price' => 'Rs. 9990',
        'image' => 'adventure.jpg',
        'description' => 'Get your adrenaline pumping with this action-packed adventure tour through Sri Lanka\'s hill country, featuring hiking, wildlife, and breathtaking landscapes.',
        'highlights' => array(
            'Hiking in Horton Plains National Park',
            'Wildlife safari in Yala National Park',
            'Train journey through tea plantations',
            'Rock climbing in Sigiriya',
            'Camping under the stars'
        ),
        'itinerary' => array(
            'Day 1: Arrival in Colombo, transfer to Nuwara Eliya',
            'Day 2: Horton Plains hiking adventure',
            'Day 3: Tea plantation tour and train journey',
            'Day 4: Yala National Park safari',
            'Day 5: Sigiriya rock climbing, return to Colombo'
        ),
        'includes' => array(
            '4 nights accommodation',
            'All meals',
            'Professional adventure guide',
            'Hiking equipment',
            'Safari jeep and driver',
            'Entrance fees'
        )
    )
);

$currentPackage = isset($packages[$package]) ? $packages[$package] : $packages['cultural'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $currentPackage['title'] ?> - Ceylon Go</title>
  <link rel="stylesheet" href="../../public/css/tourist/package_details.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
</head>
<body>
  <!-- Navbar -->
  <?php include 'header.php'; ?>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-content">
      <h1><?= $currentPackage['title'] ?></h1>
      <p class="hero-duration"><?= $currentPackage['duration'] ?></p>
      <p class="hero-price">Starting from <?= $currentPackage['price'] ?></p>
    </div>
    <div class="hero-image">
      <img src="../../images/<?= $currentPackage['image'] ?>" alt="<?= $currentPackage['title'] ?>">
    </div>
  </section>

  <!-- Package Details -->
  <section class="package-details">
    <div class="container">
      <div class="details-grid">
        <!-- Description -->
        <div class="detail-section">
          <h2>About This Package</h2>
          <p><?= $currentPackage['description'] ?></p>
        </div>

        <!-- Highlights -->
        <div class="detail-section">
          <h2>Package Highlights</h2>
          <ul class="highlights-list">
            <?php foreach ($currentPackage['highlights'] as $highlight): ?>
              <li><?= $highlight ?></li>
            <?php endforeach; ?>
          </ul>
        </div>

        <!-- Itinerary -->
        <div class="detail-section">
          <h2>Itinerary</h2>
          <div class="itinerary">
            <?php foreach ($currentPackage['itinerary'] as $day): ?>
              <div class="itinerary-day"><?= $day ?></div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- What's Included -->
        <div class="detail-section">
          <h2>What's Included</h2>
          <ul class="includes-list">
            <?php foreach ($currentPackage['includes'] as $include): ?>
              <li><?= $include ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="action-buttons">
        <a href="booking_form.php?package=<?= $package ?>" class="btn-primary">Book Now</a>
        <a href="recommended_packages.php" class="btn-outline">Back to Packages</a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include 'footer.php'; ?>
</body>
</html>
