<?php
// transport_services.php
// Transport services data
$transportServices = array(
    array(
        'id' => 1,
        'name' => 'Luxury Car Service',
        'type' => 'Private Car',
        'capacity' => '4 passengers',
        'price' => '$80/day',
        'image' => 'train.jpg',
        'description' => 'Comfortable luxury car with professional driver for city tours and short trips.',
        'features' => array('Air Conditioning', 'WiFi', 'Professional Driver', 'Insurance Included')
    ),
    array(
        'id' => 2,
        'name' => 'Minivan Service',
        'type' => 'Minivan',
        'capacity' => '8 passengers',
        'price' => '$120/day',
        'image' => 'train.jpg',
        'description' => 'Spacious minivan perfect for group tours and family trips.',
        'features' => array('Air Conditioning', 'Extra Luggage Space', 'Professional Driver', 'Insurance Included')
    ),
    array(
        'id' => 3,
        'name' => 'Bus Service',
        'type' => 'Bus',
        'capacity' => '25 passengers',
        'price' => '$200/day',
        'image' => 'train.jpg',
        'description' => 'Large bus service for group tours and long-distance travel.',
        'features' => array('Air Conditioning', 'Reclining Seats', 'Professional Driver', 'Insurance Included')
    ),
    array(
        'id' => 4,
        'name' => 'Train Service',
        'type' => 'Train',
        'capacity' => 'Unlimited',
        'price' => '$30/person',
        'image' => 'train.jpg',
        'description' => 'Scenic train journey through Sri Lankan countryside and tea plantations.',
        'features' => array('Scenic Views', 'Comfortable Seating', 'Food Service', 'Historic Route')
    ),
    array(
        'id' => 5,
        'name' => 'Airport Transfer',
        'type' => 'Transfer Service',
        'capacity' => '4 passengers',
        'price' => '$50/trip',
        'image' => 'train.jpg',
        'description' => 'Reliable airport transfer service to and from Colombo Airport.',
        'features' => array('Meet & Greet', 'Flight Tracking', 'Professional Driver', 'Insurance Included')
    ),
    array(
        'id' => 6,
        'name' => 'Motorcycle Rental',
        'type' => 'Motorcycle',
        'capacity' => '2 passengers',
        'price' => '$25/day',
        'image' => 'train.jpg',
        'description' => 'Self-drive motorcycle rental for adventurous travelers.',
        'features' => array('Helmet Included', 'Insurance Available', 'Easy Pickup', 'Fuel Efficient')
    )
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transport Services - Ceylon Go</title>
  <link rel="stylesheet" href="../../public/css/tourist/transport_services.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
</head>
<body>
  <!-- Navbar -->
  <?php include 'header.php'; ?>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-content">
      <h1>Transport Services</h1>
      <p>Choose from our range of reliable and comfortable transport options for your Sri Lankan adventure.</p>
    </div>
  </section>

  <!-- Filters -->
  <section class="filters">
    <div class="container">
      <div class="filter-group">
        <label for="transport-type">Transport Type:</label>
        <select id="transport-type">
          <option value="">All Types</option>
          <option value="Private Car">Private Car</option>
          <option value="Minivan">Minivan</option>
          <option value="Bus">Bus</option>
          <option value="Train">Train</option>
          <option value="Transfer Service">Transfer Service</option>
          <option value="Motorcycle">Motorcycle</option>
        </select>
      </div>
      <div class="filter-group">
        <label for="price-range">Price Range:</label>
        <select id="price-range">
          <option value="">All Prices</option>
          <option value="0-50">$0 - $50</option>
          <option value="51-100">$51 - $100</option>
          <option value="101-200">$101 - $200</option>
          <option value="201+">$200+</option>
        </select>
      </div>
      <div class="filter-group">
        <label for="capacity">Capacity:</label>
        <select id="capacity">
          <option value="">All Capacities</option>
          <option value="1-4">1-4 passengers</option>
          <option value="5-8">5-8 passengers</option>
          <option value="9-25">9-25 passengers</option>
          <option value="25+">25+ passengers</option>
        </select>
      </div>
    </div>
  </section>

  <!-- Transport Services Grid -->
  <section class="transport-section">
    <div class="container">
      <div class="transport-grid" id="transport-grid">
        <?php foreach ($transportServices as $service): ?>
          <div class="transport-card" data-type="<?= $service['type'] ?>" data-price="<?= $service['price'] ?>" data-capacity="<?= $service['capacity'] ?>">
            <div class="transport-image">
              <img src="../../images/<?= $service['image'] ?>" alt="<?= $service['name'] ?>">
              <div class="type-badge">
                <?= $service['type'] ?>
              </div>
            </div>
            <div class="transport-info">
              <h3><?= $service['name'] ?></h3>
              <p class="capacity"><?= $service['capacity'] ?></p>
              <p class="description"><?= $service['description'] ?></p>
              <div class="features">
                <strong>Features:</strong>
                <ul>
                  <?php foreach ($service['features'] as $feature): ?>
                    <li><?= $feature ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
              <div class="transport-footer">
                <span class="price"><?= $service['price'] ?></span>
                <button class="btn-select" onclick="selectTransport(<?= $service['id'] ?>)">Select Service</button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Selected Transport Modal -->
  <div id="transport-modal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Transport Service Selected!</h2>
      <p>Your selected transport service will be added to your booking. You can proceed to complete your booking.</p>
      <div class="modal-actions">
        <a href="recommended_packages.php" class="btn-primary">Continue Booking</a>
        <button class="btn-outline" onclick="closeModal()">Close</button>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <?php include 'footer.php'; ?>

  <script>
    // Filter functionality
    document.addEventListener('DOMContentLoaded', function() {
      const typeFilter = document.getElementById('transport-type');
      const priceFilter = document.getElementById('price-range');
      const capacityFilter = document.getElementById('capacity');
      const transportGrid = document.getElementById('transport-grid');

      function filterTransport() {
        const services = document.querySelectorAll('.transport-card');
        const type = typeFilter.value;
        const priceRange = priceFilter.value;
        const capacity = capacityFilter.value;

        services.forEach(service => {
          const serviceType = service.dataset.type;
          const servicePrice = service.dataset.price;
          const serviceCapacity = service.dataset.capacity;

          let show = true;

          if (type && serviceType !== type) {
            show = false;
          }

          if (priceRange) {
            const price = parseInt(servicePrice.replace(/[^0-9]/g, ''));
            const [min, max] = priceRange.split('-').map(p => parseInt(p));
            if (max) {
              if (price < min || price > max) show = false;
            } else {
              if (price < min) show = false;
            }
          }

          if (capacity) {
            const capacityNum = parseInt(serviceCapacity.replace(/[^0-9]/g, ''));
            const [minCap, maxCap] = capacity.split('-').map(c => parseInt(c));
            if (maxCap) {
              if (capacityNum < minCap || capacityNum > maxCap) show = false;
            } else {
              if (capacityNum < minCap) show = false;
            }
          }

          service.style.display = show ? 'block' : 'none';
        });
      }

      typeFilter.addEventListener('change', filterTransport);
      priceFilter.addEventListener('change', filterTransport);
      capacityFilter.addEventListener('change', filterTransport);
    });

    // Transport selection
    function selectTransport(serviceId) {
      const modal = document.getElementById('transport-modal');
      modal.style.display = 'block';
    }

    function closeModal() {
      const modal = document.getElementById('transport-modal');
      modal.style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      const modal = document.getElementById('transport-modal');
      if (event.target === modal) {
        modal.style.display = 'none';
      }
    }

    // Close modal with X button
    document.querySelector('.close').onclick = closeModal;
  </script>
</body>
</html>