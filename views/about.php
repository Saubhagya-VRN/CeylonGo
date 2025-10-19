<?php
// views/about.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - Ceylon Go</title>
  <link rel="stylesheet" href="../public/css/common.css">
  <link rel="stylesheet" href="../public/css/tourist/tourist_dashboard.css">
  <link rel="stylesheet" href="../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../public/css/tourist/footer.css">
</head>
<body class="bg-app">
  <?php include 'index_navbar.php'; ?>

  <section class="intro" style="padding: 80px 20px;">
    <h1>About Ceylon Go</h1>
    <p>Your trusted partner in exploring the beautiful island of Sri Lanka</p>
  </section>

  <section style="padding: 60px 20px; background-color: #f2f8f2;">
    <div style="max-width: 1000px; margin: 0 auto;">
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px; margin-bottom: 40px;">
        <div class="package" style="text-align: left;">
          <h3>Our Mission</h3>
          <p>To provide exceptional travel experiences in Sri Lanka by connecting tourists with reliable transport services, comfortable accommodations, and knowledgeable tour guides. We believe in showcasing the natural beauty, rich culture, and warm hospitality that makes Sri Lanka a must-visit destination.</p>
        </div>
        
        <div class="package" style="text-align: left;">
          <h3>Our Vision</h3>
          <p>To become the leading platform for personalized Sri Lankan travel experiences, making it easy for visitors to discover the island's hidden gems while supporting local businesses and communities.</p>
        </div>
      </div>

      <div class="package" style="text-align: left; margin-bottom: 40px;">
        <h3>What We Offer</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
          <div>
            <h4>Transport Services</h4>
            <p>From tuk-tuks to luxury buses, we connect you with reliable transport providers across the island.</p>
          </div>
          <div>
            <h4>Hotel Bookings</h4>
            <p>Find and book the perfect accommodation from beach resorts to hill country hotels.</p>
          </div>
          <div>
            <h4>Tour Guides</h4>
            <p>Experienced local guides to help you explore Sri Lanka's cultural and natural attractions.</p>
          </div>
          <div>
            <h4>Custom Packages</h4>
            <p>Personalized travel packages tailored to your interests and preferences.</p>
          </div>
        </div>
      </div>

      <div class="package" style="text-align: left;">
        <h3>Why Choose Ceylon Go?</h3>
        <ul style="list-style: none; padding: 0;">
          <li style="margin: 10px 0; padding-left: 20px; position: relative;">✓ <strong>Local Expertise:</strong> We know Sri Lanka inside and out</li>
          <li style="margin: 10px 0; padding-left: 20px; position: relative;">✓ <strong>Verified Partners:</strong> All our service providers are carefully vetted</li>
          <li style="margin: 10px 0; padding-left: 20px; position: relative;">✓ <strong>24/7 Support:</strong> We're here to help throughout your journey</li>
          <li style="margin: 10px 0; padding-left: 20px; position: relative;">✓ <strong>Best Prices:</strong> Competitive rates with no hidden fees</li>
          <li style="margin: 10px 0; padding-left: 20px; position: relative;">✓ <strong>Easy Booking:</strong> Simple, secure online booking process</li>
        </ul>
      </div>
    </div>
  </section>

  <section style="padding: 40px 20px; text-align: center;">
    <h2>Ready to Explore Sri Lanka?</h2>
    <p>Start planning your perfect Sri Lankan adventure today!</p>
    <div style="margin-top: 20px;">
      <a href="tourist/tourist_dashboard.php" class="btn">Plan Your Trip</a>
      <a href="contact.php" class="btn btn-black" style="margin-left: 15px;">Contact Us</a>
    </div>
  </section>

  <?php include 'tourist/footer.php'; ?>
</body>
</html>
