<?php
// hotel_details.php - $hotel_id is passed from controller (TouristController@hotelDetails)
$hotel_id = $hotel_id ?? $_GET['id'] ?? '';

// Hotel details with room types and prices
$hotels = array(
    'sunset-beach' => array(
        'name' => 'Sunset Beach Resort',
        'rating' => '5-star',
        'price' => 'Rs.10,000',
        'description' => 'Luxurious beachfront resort with stunning ocean views, private beach access, and world-class amenities.',
        'amenities' => array('Private Beach', 'Swimming Pool', 'Spa & Wellness', 'Restaurant', 'Free WiFi', 'Room Service'),
        'image' => 'resort.jpg',
        'room_types' => array(
            array('name' => 'Standard Room', 'price' => 10000, 'max_guests' => 2),
            array('name' => 'Deluxe Sea View', 'price' => 15000, 'max_guests' => 3),
            array('name' => 'Beachfront Suite', 'price' => 25000, 'max_guests' => 4)
        )
    ),
    'downtown-comfort' => array(
        'name' => 'Downtown Comfort Inn',
        'rating' => '4-star',
        'price' => 'Rs.9,500',
        'description' => 'Comfortable city hotel located in the heart of downtown, perfect for business and leisure travelers.',
        'amenities' => array('Central Location', 'Business Center', 'Restaurant', 'Free WiFi', 'Fitness Center', 'Concierge'),
        'image' => '5star.jpg',
        'room_types' => array(
            array('name' => 'Standard Room', 'price' => 9500, 'max_guests' => 2),
            array('name' => 'Deluxe Room', 'price' => 12000, 'max_guests' => 3),
            array('name' => 'Executive Suite', 'price' => 18000, 'max_guests' => 4)
        )
    ),
    'budget-stay' => array(
        'name' => 'Budget Stay Hostel',
        'rating' => '2-star',
        'price' => 'Rs.1,700',
        'description' => 'Affordable accommodation with clean rooms and friendly staff, ideal for budget-conscious travelers.',
        'amenities' => array('Shared Kitchen', 'Common Lounge', 'Free WiFi', 'Laundry Service', 'Tour Desk', '24/7 Reception'),
        'image' => 'factory.jpg',
        'room_types' => array(
            array('name' => 'Dorm Bed', 'price' => 1200, 'max_guests' => 1),
            array('name' => 'Private Room', 'price' => 3500, 'max_guests' => 2),
            array('name' => 'Double Room', 'price' => 4500, 'max_guests' => 3)
        )
    ),
    'grand-ocean' => array(
        'name' => 'Grand Ocean Resort',
        'rating' => '5-star',
        'price' => 'Rs.35,000',
        'description' => 'Luxury resort in Nuwara Eliya with stunning views, spa, and golf. Perfect for a premium getaway.',
        'amenities' => array('Pool', 'Spa', 'Golf', 'Free WiFi', 'Restaurant', 'Mountain Views'),
        'image' => '5star.jpg',
        'room_types' => array(
            array('name' => 'Garden View Room', 'price' => 28000, 'max_guests' => 2),
            array('name' => 'Deluxe Mountain View', 'price' => 35000, 'max_guests' => 3),
            array('name' => 'Presidential Suite', 'price' => 55000, 'max_guests' => 5)
        )
    ),
    'city-center' => array(
        'name' => 'City Center Hotel',
        'rating' => '4-star',
        'price' => 'Rs.25,000',
        'description' => 'Modern hotel in the city center with easy access to shopping, dining, and attractions.',
        'amenities' => array('City Views', 'Business Center', 'Restaurant', 'Free WiFi', 'Fitness Center', 'Parking'),
        'image' => 'sigiriya.jpg',
        'room_types' => array(
            array('name' => 'Standard Room', 'price' => 22000, 'max_guests' => 2),
            array('name' => 'Deluxe Room', 'price' => 28000, 'max_guests' => 3),
            array('name' => 'Suite', 'price' => 40000, 'max_guests' => 4)
        )
    ),
    'backpackers-paradise' => array(
        'name' => "Backpacker's Paradise",
        'rating' => '3-star',
        'price' => 'Rs.14,000',
        'description' => 'Friendly hostel in Galle with shared spaces, tour desk, and great value for travelers.',
        'amenities' => array('WiFi', 'Shared Kitchen', 'Tour Desk', 'Common Lounge', 'Laundry'),
        'image' => '5star.jpg',
        'room_types' => array(
            array('name' => 'Dorm Bed', 'price' => 2500, 'max_guests' => 1),
            array('name' => 'Private Single', 'price' => 6000, 'max_guests' => 1),
            array('name' => 'Private Double', 'price' => 10000, 'max_guests' => 2),
            array('name' => 'Family Room', 'price' => 14000, 'max_guests' => 4)
        )
    ),
    'mountain-view' => array(
        'name' => 'Mountain View Lodge',
        'rating' => '3-star',
        'price' => 'Rs.8,000',
        'description' => 'Cozy lodge nestled in the hills with breathtaking mountain views and peaceful surroundings.',
        'amenities' => array('Mountain Views', 'Garden', 'Restaurant', 'Free WiFi', 'Hiking Trails', 'Fireplace'),
        'image' => 'hiking.jpg',
        'room_types' => array(
            array('name' => 'Standard Room', 'price' => 6500, 'max_guests' => 2),
            array('name' => 'Deluxe Room', 'price' => 8000, 'max_guests' => 3),
            array('name' => 'Family Room', 'price' => 12000, 'max_guests' => 4)
        )
    ),
    'riverside' => array(
        'name' => 'Riverside Retreat',
        'rating' => '3-star',
        'price' => 'Rs.9,000',
        'description' => 'Peaceful retreat by the river with beautiful natural surroundings and outdoor activities.',
        'amenities' => array('River Views', 'Outdoor Dining', 'Free WiFi', 'Boat Tours', 'Fishing', 'Nature Trails'),
        'image' => 'unawatuna.jpg',
        'room_types' => array(
            array('name' => 'Standard Room', 'price' => 7500, 'max_guests' => 2),
            array('name' => 'River View Room', 'price' => 9000, 'max_guests' => 3),
            array('name' => 'Family Cottage', 'price' => 14000, 'max_guests' => 5)
        )
    )
);

$hotel = isset($hotels[$hotel_id]) ? $hotels[$hotel_id] : $hotels['sunset-beach'];
if (!isset($hotel['room_types'])) {
    $hotel['room_types'] = array(
        array('name' => 'Standard Room', 'price' => 10000, 'max_guests' => 2),
        array('name' => 'Deluxe Room', 'price' => 15000, 'max_guests' => 3),
        array('name' => 'Suite', 'price' => 25000, 'max_guests' => 4)
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - <?php echo $hotel['name']; ?></title>
  <link rel="stylesheet" href="/CeylonGo/public/css/common.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/tourist/tourist_dashboard.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/tourist/navbar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/tourist/footer.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/tourist/booking_form.css">
  <style>
    body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f8f0; color: #2d4a2d; }
    .hotel-details-page .intro {
      background: linear-gradient(135deg, rgba(74, 124, 89, 0.95), rgba(44, 85, 48, 0.95));
      color: #fff;
      padding: 32px 24px;
      text-align: center;
      margin: 0;
    }
    .hotel-details-page .intro h1 {
      margin: 0 0 10px 0;
      font-size: 22px;
      font-weight: 700;
      color: #fff;
      text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    .hotel-details-page .intro p {
      max-width: 600px;
      margin: 0 auto 14px;
      font-size: 14px;
      line-height: 1.5;
      opacity: 0.95;
    }
    .hotel-details-page .hotel-info {
      display: inline-flex;
      gap: 12px;
      align-items: center;
      flex-wrap: wrap;
      justify-content: center;
    }
    .hotel-details-page .hotel-info .rating,
    .hotel-details-page .hotel-info .price {
      background: rgba(255,255,255,0.2);
      padding: 6px 12px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 13px;
    }
    .hotel-details-page .section-wrap {
      max-width: 1100px;
      margin: 0 auto;
      padding: 40px 24px;
    }
    .hotel-details-page .recommended-packages {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 8px 25px rgba(74, 124, 89, 0.12);
      border: 1px solid rgba(74, 124, 89, 0.1);
      padding: 32px;
      margin-bottom: 32px;
    }
    .hotel-details-page .recommended-packages h2 {
      margin: 0 0 20px 0;
      font-size: 22px;
      color: #2c5530;
      font-weight: 700;
      padding-bottom: 12px;
      border-bottom: 2px solid rgba(74, 124, 89, 0.2);
    }
    .hotel-details-page .amenities-list {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin: 0;
      padding: 0;
      list-style: none;
    }
    .hotel-details-page .amenity-item {
      background: linear-gradient(135deg, #f0f7f0, #e8f0e8);
      border: 1px solid rgba(74, 124, 89, 0.15);
      border-radius: 10px;
      padding: 12px 18px;
      margin: 0;
    }
    .hotel-details-page .amenity-item h3 {
      margin: 0;
      font-size: 15px;
      font-weight: 600;
      color: #2c5530;
    }
    .hotel-details-page .room-types-section {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 8px 25px rgba(74, 124, 89, 0.12);
      border: 1px solid rgba(74, 124, 89, 0.1);
      padding: 32px;
      margin-bottom: 32px;
    }
    .hotel-details-page .room-types-section h2 {
      margin: 0 0 8px 0;
      font-size: 22px;
      color: #2c5530;
      font-weight: 700;
    }
    .hotel-details-page .room-types-section .subtitle {
      color: #555;
      margin: 0 0 24px 0;
      font-size: 15px;
    }
    .hotel-details-page .room-cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
    }
    .hotel-details-page .room-card {
      background: #fafcfa;
      border: 1px solid rgba(74, 124, 89, 0.2);
      border-radius: 12px;
      padding: 24px;
      box-shadow: 0 4px 15px rgba(74, 124, 89, 0.08);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hotel-details-page .room-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 22px rgba(74, 124, 89, 0.15);
    }
    .hotel-details-page .room-card h3 {
      margin: 0 0 8px 0;
      color: #2c5530;
      font-size: 18px;
      font-weight: 700;
    }
    .hotel-details-page .room-card .guests {
      color: #666;
      font-size: 14px;
      margin: 0 0 12px 0;
    }
    .hotel-details-page .room-card .price {
      font-size: 22px;
      font-weight: 700;
      color: #2c5530;
      margin: 0;
    }
    .hotel-details-page .room-card .price span {
      font-size: 14px;
      font-weight: 400;
      color: #666;
    }
    .hotel-details-page .booking-section {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 8px 25px rgba(74, 124, 89, 0.12);
      border: 1px solid rgba(74, 124, 89, 0.1);
      padding: 32px;
    }
    .hotel-details-page .booking-section h2 {
      margin: 0 0 24px 0;
      font-size: 22px;
      color: #2c5530;
      font-weight: 700;
      padding-bottom: 12px;
      border-bottom: 2px solid rgba(74, 124, 89, 0.2);
    }
    .hotel-details-page .booking-form label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #2d4a2d;
      font-size: 14px;
    }
    .hotel-details-page .booking-form input,
    .hotel-details-page .booking-form select,
    .hotel-details-page .booking-form textarea {
      width: 100%;
      padding: 12px 14px;
      border: 1.5px solid #d0d7d0;
      border-radius: 8px;
      font-size: 15px;
      font-family: inherit;
      margin-bottom: 20px;
      box-sizing: border-box;
    }
    .hotel-details-page .booking-form input:focus,
    .hotel-details-page .booking-form select:focus,
    .hotel-details-page .booking-form textarea:focus {
      outline: none;
      border-color: #2c5530;
      box-shadow: 0 0 0 3px rgba(44, 85, 48, 0.1);
    }
    .hotel-details-page .booking-form .btn {
      background: linear-gradient(135deg, #4a7c59, #5a8c69);
      color: #fff;
      border: none;
      padding: 14px 28px;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .hotel-details-page .booking-form .btn:hover {
      background: linear-gradient(135deg, #3d6b4a, #4a7c59);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(74, 124, 89, 0.3);
    }
    .hotel-details-page footer {
      background: linear-gradient(135deg, #4a7c59, #5a8c69);
      color: #fff;
      padding: 24px;
      text-align: center;
      margin-top: 40px;
    }
    .hotel-details-page footer ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      gap: 24px;
      flex-wrap: wrap;
    }
    .hotel-details-page footer a {
      color: #fff;
      text-decoration: none;
      font-weight: 500;
    }
    .hotel-details-page footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body class="hotel-details-page">
  <!-- Navbar -->
  <?php include 'header.php'; ?>

  <!-- Hero Section -->
  <section class="intro">
    <h1><?php echo htmlspecialchars($hotel['name']); ?></h1>
    <p><?php echo htmlspecialchars($hotel['description']); ?></p>
    <div class="hotel-info">
      <span class="rating"><?php echo htmlspecialchars($hotel['rating']); ?></span>
      <span class="price"><?php echo htmlspecialchars($hotel['price']); ?> /night</span>
    </div>
  </section>

  <div class="section-wrap">
  <!-- Hotel Details Section -->
  <section class="recommended-packages">
    <h2>Hotel Amenities</h2>
    <div class="amenities-list">
      <?php foreach ($hotel['amenities'] as $amenity): ?>
        <div class="amenity-item">
          <h3><?php echo htmlspecialchars($amenity); ?></h3>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Room Types and Prices -->
  <section class="room-types-section">
    <h2>Room Types & Prices</h2>
    <p class="subtitle">Choose your room type. Prices are per night (LKR).</p>
    <div class="room-cards">
      <?php foreach ($hotel['room_types'] as $idx => $room): ?>
        <div class="room-card">
          <h3><?php echo htmlspecialchars($room['name']); ?></h3>
          <p class="guests">Up to <?php echo (int) $room['max_guests']; ?> guests</p>
          <p class="price">Rs.<?php echo number_format($room['price']); ?> <span>/night</span></p>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Booking Section -->
  <section class="booking-section">
    <h2>Book This Hotel</h2>
    <form class="booking-form" method="GET" action="/CeylonGo/public/tourist/booking-form">
      <input type="hidden" name="hotel_id" value="<?php echo htmlspecialchars($hotel_id); ?>">
      <input type="hidden" name="hotel_name" value="<?php echo htmlspecialchars($hotel['name']); ?>">

      <label for="check-in">Check-in Date</label>
      <input type="date" id="check-in" name="check-in" required>

      <label for="check-out">Check-out Date</label>
      <input type="date" id="check-out" name="check-out" required>

      <label for="guests">Number of Guests</label>
      <input type="number" id="guests" name="guests" min="1" required>

      <label for="room-type">Room Type</label>
      <select id="room-type" name="room-type" required>
        <option value="">Select room type</option>
        <?php foreach ($hotel['room_types'] as $idx => $room): ?>
          <option value="<?php echo htmlspecialchars($room['name']); ?>" data-price="<?php echo (int) $room['price']; ?>"><?php echo htmlspecialchars($room['name']); ?> — Rs.<?php echo number_format($room['price']); ?>/night</option>
        <?php endforeach; ?>
      </select>

      <label for="special-requests">Special Requests</label>
      <textarea id="special-requests" name="special-requests" rows="4" placeholder="Any special requirements or preferences..."></textarea>

      <button type="submit" class="btn">Proceed to Booking</button>
    </form>
  </section>

  </div><!-- .section-wrap -->

  <!-- Footer -->
  <footer>
    <ul>
      <li><a href="/CeylonGo/public/about">About Us</a></li>
      <li><a href="/CeylonGo/public/contact">Contact Us</a></li>
    </ul>
  </footer>
</body>
</html>
