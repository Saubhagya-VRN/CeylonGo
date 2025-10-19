<?php
// tour_guides.php
// Tour guides data
$guides = array(
    array(
        'id' => 1,
        'name' => 'Saman Perera',
        'specialization' => 'Cultural Heritage',
        'experience' => '8 years',
        'rating' => 4.9,
        'languages' => array('English', 'Sinhala', 'Tamil'),
        'image' => 'profile.jpg',
        'description' => 'Expert in Sri Lankan cultural heritage with extensive knowledge of ancient cities and temples.',
        'price' => '$50/day'
    ),
    array(
        'id' => 2,
        'name' => 'Priya Fernando',
        'specialization' => 'Wildlife & Nature',
        'experience' => '6 years',
        'rating' => 4.8,
        'languages' => array('English', 'Sinhala'),
        'image' => 'profile.jpg',
        'description' => 'Passionate wildlife guide specializing in national parks and nature conservation.',
        'price' => '$45/day'
    ),
    array(
        'id' => 3,
        'name' => 'Rajesh Kumar',
        'specialization' => 'Adventure Tours',
        'experience' => '10 years',
        'rating' => 4.9,
        'languages' => array('English', 'Tamil', 'Hindi'),
        'image' => 'profile.jpg',
        'description' => 'Experienced adventure guide for hiking, climbing, and outdoor activities.',
        'price' => '$55/day'
    ),
    array(
        'id' => 4,
        'name' => 'Nimali Silva',
        'specialization' => 'Beach & Coastal',
        'experience' => '5 years',
        'rating' => 4.7,
        'languages' => array('English', 'Sinhala'),
        'image' => 'profile.jpg',
        'description' => 'Specialist in coastal areas, marine activities, and beach destinations.',
        'price' => '$40/day'
    ),
    array(
        'id' => 5,
        'name' => 'David Smith',
        'specialization' => 'Historical Sites',
        'experience' => '12 years',
        'rating' => 5.0,
        'languages' => array('English', 'French'),
        'image' => 'profile.jpg',
        'description' => 'Archaeologist and historian with deep knowledge of Sri Lankan historical sites.',
        'price' => '$60/day'
    ),
    array(
        'id' => 6,
        'name' => 'Kumari Wickramasinghe',
        'specialization' => 'Culinary Tours',
        'experience' => '7 years',
        'rating' => 4.8,
        'languages' => array('English', 'Sinhala'),
        'image' => 'profile.jpg',
        'description' => 'Food and culture expert specializing in culinary experiences and local cuisine.',
        'price' => '$45/day'
    )
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tour Guides - Ceylon Go</title>
  <link rel="stylesheet" href="../../public/css/tourist/tour_guides.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
</head>
<body>
  <!-- Navbar -->
  <?php include 'header.php'; ?>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-content">
      <h1>Professional Tour Guides</h1>
      <p>Choose from our experienced and knowledgeable tour guides to make your Sri Lankan adventure unforgettable.</p>
    </div>
  </section>

  <!-- Filters -->
  <section class="filters">
    <div class="container">
      <div class="filter-group">
        <label for="specialization">Specialization:</label>
        <select id="specialization">
          <option value="">All Specializations</option>
          <option value="Cultural Heritage">Cultural Heritage</option>
          <option value="Wildlife & Nature">Wildlife & Nature</option>
          <option value="Adventure Tours">Adventure Tours</option>
          <option value="Beach & Coastal">Beach & Coastal</option>
          <option value="Historical Sites">Historical Sites</option>
          <option value="Culinary Tours">Culinary Tours</option>
        </select>
      </div>
      <div class="filter-group">
        <label for="price-range">Price Range:</label>
        <select id="price-range">
          <option value="">All Prices</option>
          <option value="0-45">$40 - $45</option>
          <option value="46-55">$46 - $55</option>
          <option value="56+">$56+</option>
        </select>
      </div>
      <div class="filter-group">
        <label for="rating">Minimum Rating:</label>
        <select id="rating">
          <option value="">All Ratings</option>
          <option value="4.5">4.5+ Stars</option>
          <option value="4.7">4.7+ Stars</option>
          <option value="4.9">4.9+ Stars</option>
        </select>
      </div>
    </div>
  </section>

  <!-- Tour Guides Grid -->
  <section class="guides-section">
    <div class="container">
      <div class="guides-grid" id="guides-grid">
        <?php foreach ($guides as $guide): ?>
          <div class="guide-card" data-specialization="<?= $guide['specialization'] ?>" data-price="<?= $guide['price'] ?>" data-rating="<?= $guide['rating'] ?>">
            <div class="guide-image">
              <img src="../../images/<?= $guide['image'] ?>" alt="<?= $guide['name'] ?>">
              <div class="rating-badge">
                <span class="stars"><?= str_repeat('★', floor($guide['rating'])) ?></span>
                <span class="rating-number"><?= $guide['rating'] ?></span>
              </div>
            </div>
            <div class="guide-info">
              <h3><?= $guide['name'] ?></h3>
              <p class="specialization"><?= $guide['specialization'] ?></p>
              <p class="experience"><?= $guide['experience'] ?> experience</p>
              <p class="description"><?= $guide['description'] ?></p>
              <div class="languages">
                <strong>Languages:</strong>
                <?= implode(', ', $guide['languages']) ?>
              </div>
              <div class="guide-footer">
                <span class="price"><?= $guide['price'] ?></span>
                <button class="btn-select" onclick="selectGuide(<?= $guide['id'] ?>)">Select Guide</button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Selected Guide Modal -->
  <div id="guide-modal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Guide Selected Successfully!</h2>
      <p>Your selected guide will be added to your booking. You can proceed to complete your booking.</p>
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
      const specializationFilter = document.getElementById('specialization');
      const priceFilter = document.getElementById('price-range');
      const ratingFilter = document.getElementById('rating');
      const guidesGrid = document.getElementById('guides-grid');

      function filterGuides() {
        const guides = document.querySelectorAll('.guide-card');
        const specialization = specializationFilter.value;
        const priceRange = priceFilter.value;
        const rating = ratingFilter.value;

        guides.forEach(guide => {
          const guideSpecialization = guide.dataset.specialization;
          const guidePrice = parseInt(guide.dataset.price.replace('$', ''));
          const guideRating = parseFloat(guide.dataset.rating);

          let show = true;

          if (specialization && guideSpecialization !== specialization) {
            show = false;
          }

          if (priceRange) {
            const [min, max] = priceRange.split('-').map(p => parseInt(p));
            if (max) {
              if (guidePrice < min || guidePrice > max) show = false;
            } else {
              if (guidePrice < min) show = false;
            }
          }

          if (rating && guideRating < parseFloat(rating)) {
            show = false;
          }

          guide.style.display = show ? 'block' : 'none';
        });
      }

      specializationFilter.addEventListener('change', filterGuides);
      priceFilter.addEventListener('change', filterGuides);
      ratingFilter.addEventListener('change', filterGuides);
    });

    // Guide selection
    function selectGuide(guideId) {
      const modal = document.getElementById('guide-modal');
      modal.style.display = 'block';
    }

    function closeModal() {
      const modal = document.getElementById('guide-modal');
      modal.style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      const modal = document.getElementById('guide-modal');
      if (event.target === modal) {
        modal.style.display = 'none';
      }
    }

    // Close modal with X button
    document.querySelector('.close').onclick = closeModal;
  </script>
</body>
</html>