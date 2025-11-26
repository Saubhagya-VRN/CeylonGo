<?php
// recommended_packages.php
// If you want to handle form submission, you can add PHP logic at the top here.
// Example:
// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $package = $_POST['package'] ?? '';
//     $persons = $_POST['persons'] ?? '';
//     $fromDate = $_POST['from-date'] ?? '';
//     $toDate = $_POST['to-date'] ?? '';
//     // Save or process booking details...
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ceylon Go - Travel Packages</title>
  <link rel="stylesheet" href="../../public/css/tourist/recommended_packages.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
   <link rel="stylesheet" href="../../public/css/tourist/footer.css">
  <style>
    body {
      background-color: #f0f8f0; /* Light greenish background from tourist_dashboard */
    }
  </style>
</head>
<body>
  <!-- Navbar -->
    <?php include 'header.php'; ?>

  <!-- Hero -->
  <section class="hero">
    <h1>Explore Our Travel Packages</h1>
    <p>Discover unforgettable experiences tailored just for you!</p>
  </section>

  <!-- Popular Packages -->
  <section class="section">
    <h2>Popular Packages</h2>
    <p class="section-subtitle">Choose from our best-selling travel packages.</p>
    <div class="packages">
      <!-- Package 1 -->
      <div class="package-card">
        <div class="image-box cultural">Cultural</div>
        <h3>Cultural Experience in Sri Lanka</h3>
        <p>Dates: 5 days, 4 nights</p>
        <div class="card-buttons">
          <a href="package_details.php?package=cultural" class="btn-outline">View Details</a>
          <a href="#booking" class="btn-black">Book Now</a>
        </div>
      </div>

      <div class="package-card">
        <div class="image-box beach">Beach</div>
        <h3>Tropical Beach Retreat</h3>
        <p>Dates: 5 days, 4 nights</p>
        <div class="card-buttons">
          <a href="package_details.php?package=beach" class="btn-outline">View Details</a>
          <a href="#booking" class="btn-black">Book Now</a>
        </div>
      </div>

      <div class="package-card">
        <div class="image-box adventure">Adventure</div>
        <h3>Adventure in the Hills</h3>
        <p>Dates: 5 days, 4 nights</p>
        <div class="card-buttons">
          <a href="package_details.php?package=adventure" class="btn-outline">View Details</a>
          <a href="#booking" class="btn-black">Book Now</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Booking Form -->
  <section id="booking" class="section booking-section">
    <h2>Book Your Package</h2>
    <p class="section-subtitle">Fill in the details to confirm your travel plans!</p>
    <form class="booking-form" method="POST" action="">
      <div class="form-group">
        <label for="package">Select Package</label>
        <select id="package" name="package">
          <option>Cultural Experience in Sri Lanka</option>
          <option>Tropical Beach Retreat</option>
          <option>Adventure in the Hills</option>
        </select>
      </div>

      <div class="form-group">
        <label for="persons">Number of Persons</label>
        <input type="number" id="persons" name="persons" placeholder="Enter number of travelers"/>
      </div>

      <div class="form-group">
        <label for="from-date">Travel Dates</label>
        <div class="date-range">
          <input type="date" id="from-date" name="from-date" placeholder="From">
          <input type="date" id="to-date" name="to-date" placeholder="To">
        </div>
      </div>

      <div class="form-group">
        <label>Add Tour Guide</label>
        <div class="btn-group">
          <a href="tour_guides.php" class="btn-outline">Yes</a>
          <button type="button" class="btn-outline">No</button>
        </div>
      </div>

      <div class="form-group full-width">
        <a href="payment.php" class="btn-black" style="display: inline-block; padding: 10px 20px; text-decoration: none; color: white; background: #000; border: none; cursor: pointer;">Proceed to Payment</a>
      </div>
    </form>
  </section>

  <!-- Navbar include -->
  <?php include 'footer.php'; ?>
</body>
</html>
