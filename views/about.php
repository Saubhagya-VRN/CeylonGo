<?php
// views/about.php
$base = defined('BASE_URL') ? BASE_URL : '/CeylonGo/public';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - Ceylon Go</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base); ?>/css/common.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base); ?>/css/tourist/tourist_dashboard.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base); ?>/css/tourist/navbar.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($base); ?>/css/tourist/footer.css">
</head>
<body class="bg-app about-page">
  <?php include 'index_navbar.php'; ?>

  <section class="about-hero">
    <h1>About Ceylon Go</h1>
    <p>Your trusted partner in exploring the beautiful island of Sri Lanka</p>
  </section>

  <section class="about-content">
    <div class="about-inner">
      <div class="about-mission-grid">
        <article class="about-panel">
          <h3>Our Mission</h3>
          <p>To provide exceptional travel experiences in Sri Lanka by connecting tourists with reliable transport services, comfortable accommodations, and knowledgeable tour guides. We believe in showcasing the natural beauty, rich culture, and warm hospitality that makes Sri Lanka a must-visit destination.</p>
        </article>
        <article class="about-panel">
          <h3>Our Vision</h3>
          <p>To become the leading platform for personalized Sri Lankan travel experiences, making it easy for visitors to discover the island's hidden gems while supporting local businesses and communities.</p>
        </article>
      </div>

      <div class="about-offer-block">
        <h3 class="about-section-title">What We Offer</h3>
        <div class="about-offer-grid">
          <article class="about-offer-card">
            <span class="about-offer-icon" aria-hidden="true"><i class="fa-solid fa-van-shuttle"></i></span>
            <h4>Transport Services</h4>
            <p>From tuk-tuks to luxury buses, we connect you with reliable transport providers across the island.</p>
          </article>
          <article class="about-offer-card">
            <span class="about-offer-icon" aria-hidden="true"><i class="fa-solid fa-hotel"></i></span>
            <h4>Hotel Bookings</h4>
            <p>Find and book the perfect accommodation from beach resorts to hill country hotels.</p>
          </article>
          <article class="about-offer-card">
            <span class="about-offer-icon" aria-hidden="true"><i class="fa-solid fa-map-location-dot"></i></span>
            <h4>Tour Guides</h4>
            <p>Experienced local guides to help you explore Sri Lanka's cultural and natural attractions.</p>
          </article>
          <article class="about-offer-card">
            <span class="about-offer-icon" aria-hidden="true"><i class="fa-solid fa-route"></i></span>
            <h4>Custom Packages</h4>
            <p>Personalized travel packages tailored to your interests and preferences.</p>
          </article>
        </div>
      </div>

      <article class="about-panel about-why">
        <h3>Why Choose Ceylon Go?</h3>
        <ul class="about-checklist">
          <li><strong>Local Expertise:</strong> We know Sri Lanka inside and out</li>
          <li><strong>Verified Partners:</strong> All our service providers are carefully vetted</li>
          <li><strong>24/7 Support:</strong> We're here to help throughout your journey</li>
          <li><strong>Best Prices:</strong> Competitive rates with no hidden fees</li>
          <li><strong>Easy Booking:</strong> Simple, secure online booking process</li>
        </ul>
      </article>
    </div>
  </section>

  <?php include 'tourist/footer.php'; ?>
</body>
</html>
