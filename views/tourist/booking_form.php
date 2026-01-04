<?php
// booking_form.php
$package_id = isset($_GET['package']) ? $_GET['package'] : '';

// Package details based on ID
$packages = array(
    'cultural' => array(
        'name' => 'Cultural Experience in Sri Lanka',
        'duration' => '5 days, 4 nights',
        'price' => 'Rs.8990'
    ),
    'beach' => array(
        'name' => 'Tropical Beach Retreat',
        'duration' => '5 days, 4 nights',
        'price' => 'Rs.7990'
    ),
    'adventure' => array(
        'name' => 'Adventure in the Hills',
        'duration' => '5 days, 4 nights',
        'price' => 'Rs.6990'
    )
);

$package = isset($packages[$package_id]) ? $packages[$package_id] : $packages['cultural'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Package - Ceylon Go</title>
  <link rel="stylesheet" href="../../public/css/tourist/booking_form.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
</head>
<body>
  <!-- Navbar -->
  <?php include 'header.php'; ?>

  <!-- Booking Section -->
  <section class="intro">
    <h1>Book Your Package</h1>
    <p>Complete your booking for: <?php echo $package['name']; ?></p>
    <div class="package-info">
      <span class="duration"><?php echo $package['duration']; ?></span>
      <span class="price"><?php echo $package['price']; ?></span>
    </div>
  </section>

  <section class="customize-trip">
    <h2>Booking Details</h2>
    <form class="booking-form" method="POST" action="">
      <label for="package-name">Package</label>
      <input type="text" id="package-name" name="package-name" value="<?php echo $package['name']; ?>" readonly>

      <label for="persons">Number of Persons</label>
      <input type="number" id="persons" name="persons" min="1" required>

      <label for="from-date">Travel Dates</label>
      <div class="date-range">
        <input type="date" id="from-date" name="from-date" required>
        <input type="date" id="to-date" name="to-date" required>
      </div>

      <label for="contact-name">Contact Person Name</label>
      <input type="text" id="contact-name" name="contact-name" required>

      <label for="contact-email">Contact Email</label>
      <input type="email" id="contact-email" name="contact-email" required>

      <label for="contact-phone">Contact Phone</label>
      <input type="tel" id="contact-phone" name="contact-phone" required>

      <label for="special-requests">Special Requests</label>
      <textarea id="special-requests" name="special-requests" rows="4" placeholder="Any special requirements or preferences..."></textarea>

      <label>Add Tour Guide</label>
      <div class="btn-group">
        <a href="tour_guides" class="btn btn-black">Yes</a>
        <button type="button" class="btn btn-black" name="tour-guide" value="no">No</button>
      </div>

      <label>Add Transport</label>
      <div class="btn-group">
        <a href="transport_services" class="btn btn-black">Yes</a>
        <button type="button" class="btn btn-black" name="transport" value="no">No</button>
      </div>

      <button type="submit" class="btn">Proceed to Payment</button>
    </form>
  </section>

  <!-- Footer -->
  <?php include 'footer.php'; ?>
</body>
</html>
