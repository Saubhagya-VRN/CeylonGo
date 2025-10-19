<?php
// hotel_details.php
$hotel_id = isset($_GET['id']) ? $_GET['id'] : '';

// Hotel details based on ID
$hotels = array(
    'sunset-beach' => array(
        'name' => 'Sunset Beach Resort',
        'rating' => '5-star',
        'price' => '$200/night',
        'description' => 'Luxurious beachfront resort with stunning ocean views, private beach access, and world-class amenities.',
        'amenities' => array('Private Beach', 'Swimming Pool', 'Spa & Wellness', 'Restaurant', 'Free WiFi', 'Room Service'),
        'image' => 'resort.jpg'
    ),
    'downtown-comfort' => array(
        'name' => 'Downtown Comfort Inn',
        'rating' => '4-star',
        'price' => '$120/night',
        'description' => 'Comfortable city hotel located in the heart of downtown, perfect for business and leisure travelers.',
        'amenities' => array('Central Location', 'Business Center', 'Restaurant', 'Free WiFi', 'Fitness Center', 'Concierge'),
        'image' => '5star.jpg'
    ),
    'budget-stay' => array(
        'name' => 'Budget Stay Hostel',
        'rating' => '2-star',
        'price' => '$30/night',
        'description' => 'Affordable accommodation with clean rooms and friendly staff, ideal for budget-conscious travelers.',
        'amenities' => array('Shared Kitchen', 'Common Lounge', 'Free WiFi', 'Laundry Service', 'Tour Desk', '24/7 Reception'),
        'image' => 'factory.jpg'
    ),
    'mountain-view' => array(
        'name' => 'Mountain View Lodge',
        'rating' => '3-star',
        'price' => '$80/night',
        'description' => 'Cozy lodge nestled in the hills with breathtaking mountain views and peaceful surroundings.',
        'amenities' => array('Mountain Views', 'Garden', 'Restaurant', 'Free WiFi', 'Hiking Trails', 'Fireplace'),
        'image' => 'hiking.jpg'
    ),
    'city-center' => array(
        'name' => 'City Center Plaza',
        'rating' => '4-star',
        'price' => '$150/night',
        'description' => 'Modern hotel in the city center with easy access to shopping, dining, and attractions.',
        'amenities' => array('City Views', 'Shopping Mall', 'Restaurant', 'Free WiFi', 'Fitness Center', 'Valet Parking'),
        'image' => 'sigiriya.jpg'
    ),
    'riverside' => array(
        'name' => 'Riverside Retreat',
        'rating' => '3-star',
        'price' => '$90/night',
        'description' => 'Peaceful retreat by the river with beautiful natural surroundings and outdoor activities.',
        'amenities' => array('River Views', 'Outdoor Dining', 'Free WiFi', 'Boat Tours', 'Fishing', 'Nature Trails'),
        'image' => 'unawatuna.jpg'
    )
);

$hotel = isset($hotels[$hotel_id]) ? $hotels[$hotel_id] : $hotels['sunset-beach'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - <?php echo $hotel['name']; ?></title>
  <link rel="stylesheet" href="../../public/css/tourist/tourist_dashboard.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
</head>
<body>
  <!-- Navbar -->
  <?php include 'header.php'; ?>

  <!-- Hero Section -->
  <section class="intro">
    <h1><?php echo $hotel['name']; ?></h1>
    <p><?php echo $hotel['description']; ?></p>
    <div class="hotel-info">
      <span class="rating"><?php echo $hotel['rating']; ?></span>
      <span class="price"><?php echo $hotel['price']; ?></span>
    </div>
  </section>

  <!-- Hotel Details Section -->
  <section class="recommended-packages">
    <h2>Hotel Amenities</h2>
    <div class="amenities-list">
      <?php foreach ($hotel['amenities'] as $amenity): ?>
        <div class="amenity-item">
          <h3><?php echo $amenity; ?></h3>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Booking Section -->
  <section class="customize-trip">
    <h2>Book This Hotel</h2>
    <form class="booking-form" method="POST" action="">
      <label for="check-in">Check-in Date</label>
      <input type="date" id="check-in" name="check-in" required>

      <label for="check-out">Check-out Date</label>
      <input type="date" id="check-out" name="check-out" required>

      <label for="guests">Number of Guests</label>
      <input type="number" id="guests" name="guests" min="1" required>

      <label for="room-type">Room Type</label>
      <select id="room-type" name="room-type" required>
        <option value="">Select room type</option>
        <option value="standard">Standard Room</option>
        <option value="deluxe">Deluxe Room</option>
        <option value="suite">Suite</option>
      </select>

      <label for="special-requests">Special Requests</label>
      <textarea id="special-requests" name="special-requests" rows="4" placeholder="Any special requirements or preferences..."></textarea>

      <button type="submit" class="btn">Proceed to Payment</button>
    </form>
  </section>

  <!-- Footer -->
  <footer>
    <ul>
      <li><a href="../about.php">About Us</a></li>
      <li><a href="../contact.php">Contact Us</a></li>
    </ul>
  </footer>
</body>
</html>
