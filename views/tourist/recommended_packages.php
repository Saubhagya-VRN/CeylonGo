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
  <link rel="stylesheet" href="../../public/css/tourist/tourist_dashboard.css">
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
        <div class="package-image" style="background-image: url('../../public/images/kandy.jpeg');"></div>
        <h3>Cultural Triangle Explorer</h3>
        <p>Kandy • Sigiriya • Dambulla</p>
        <div class="card-buttons">
          <a href="package_details?package=cultural" class="btn-outline">View Details</a>
          <a href="#booking" class="btn-black">Book Now</a>
        </div>
      </div>

      <div class="package-card">
        <div class="package-image" style="background-image: url('../../public/images/beach.jpg');"></div>
        <h3>Southern Beach Paradise</h3>
        <p>Galle • Mirissa • Unawatuna</p>
        <div class="card-buttons">
          <a href="package_details?package=beach" class="btn-outline">View Details</a>
          <a href="#booking" class="btn-black">Book Now</a>
        </div>
      </div>

      <div class="package-card">
        <div class="package-image" style="background-image: url('../../public/images/greenary.jpg');"></div>
        <h3>Misty Mountain Escape</h3>
        <p>Nuwara Eliya • Ella • Horton Plains</p>
        <div class="card-buttons">
          <a href="package_details?package=adventure" class="btn-outline">View Details</a>
          <a href="#booking" class="btn-black">Book Now</a>
        </div>
      </div>

      <div class="package-card">
        <div class="package-image" style="background-image: url('../../public/images/perehara.jpeg');"></div>
        <h3>Ancient Heritage Trail</h3>
        <p>Temples • Historical Sites</p>
        <div class="card-buttons">
          <a href="package_details?package=heritage" class="btn-outline">View Details</a>
          <a href="#booking" class="btn-black">Book Now</a>
        </div>
      </div>

      <div class="package-card">
        <div class="package-image" style="background-image: url('../../public/images/elephant.jpg');"></div>
        <h3>Safari & Nature Adventure</h3>
        <p>National Parks • Wildlife</p>
        <div class="card-buttons">
          <a href="package_details?package=safari" class="btn-outline">View Details</a>
          <a href="#booking" class="btn-black">Book Now</a>
        </div>
      </div>

      <div class="package-card">
        <div class="package-image" style="background-image: url('../../public/images/train.jpg');"></div>
        <h3>Solo Adventurer Special</h3>
        <p>Curated for independent travelers</p>
        <div class="card-buttons">
          <a href="package_details?package=solo" class="btn-outline">View Details</a>
          <a href="#booking" class="btn-black">Book Now</a>
        </div>
      </div>

      <div class="package-card">
        <div class="package-image" style="background-image: url('../../public/images/resort.jpg');"></div>
        <h3>Family Fun Package</h3>
        <p>Kid-friendly activities & resorts</p>
        <div class="card-buttons">
          <a href="package_details?package=family" class="btn-outline">View Details</a>
          <a href="#booking" class="btn-black">Book Now</a>
        </div>
      </div>

      <div class="package-card">
        <div class="package-image" style="background-image: url('../../public/images/sunset.jpg');"></div>
        <h3>Quick Beach Getaway</h3>
        <p>Bentota • Hikkaduwa</p>
        <div class="card-buttons">
          <a href="package_details?package=weekend" class="btn-outline">View Details</a>
          <a href="#booking" class="btn-black">Book Now</a>
        </div>
      </div>
    </div>
  </section>

  <!-- All Packages Section -->
  <section class="section recommended-packages">
    <h2>All Available Packages</h2>
    <p class="section-subtitle">Browse curated packages by destination, duration, experience type, or travel group to create your ideal Sri Lankan adventure.</p>
    
    <!-- Filter Tabs -->
    <div class="package-filters">
      <button class="filter-btn active" data-filter="all">
        All Packages
      </button>
      <button class="filter-btn" data-filter="region">
        By Region
      </button>
      <button class="filter-btn" data-filter="duration">
        By Duration
      </button>
      <button class="filter-btn" data-filter="experience">
        By Experience
      </button>
      <button class="filter-btn" data-filter="group">
        By Group
      </button>
    </div>

    <!-- Packages Grid -->
    <div class="packages-grid">
      <!-- Region-based Packages -->
      <div class="package-card" data-category="region" data-tags="central cultural heritage">
        <div class="package-image" style="background-image: url('../../public/images/kandy.jpeg');">
          <div class="package-badge">Central</div>
        </div>
        <div class="package-content">
          <h3>Cultural Triangle Explorer</h3>
          <p class="package-description">Kandy • Sigiriya • Dambulla</p>
          <div class="package-meta">
            <span class="meta-item">4-5 Days</span>
            <span class="meta-item">Cultural</span>
          </div>
          <div class="card-buttons">
            <a href="/CeylonGo/public/tourist/package-details/1" class="btn-outline">View Details</a>
            <a href="/CeylonGo/public/tourist/dashboard#customize" class="btn-black">Book Now</a>
          </div>
        </div>
      </div>

      <div class="package-card" data-category="region" data-tags="south beach relaxation">
        <div class="package-image" style="background-image: url('../../public/images/beach.jpg');">
          <div class="package-badge badge-blue">South Coast</div>
        </div>
        <div class="package-content">
          <h3>Southern Beach Paradise</h3>
          <p class="package-description">Galle • Mirissa • Unawatuna</p>
          <div class="package-meta">
            <span class="meta-item">3 Days</span>
            <span class="meta-item">Beach</span>
          </div>
          <div class="card-buttons">
            <a href="/CeylonGo/public/tourist/package-details/2" class="btn-outline">View Details</a>
            <a href="/CeylonGo/public/tourist/dashboard#customize" class="btn-black">Book Now</a>
          </div>
        </div>
      </div>

      <div class="package-card" data-category="region" data-tags="central nature adventure">
        <div class="package-image" style="background-image: url('../../public/images/greenary.jpg');">
          <div class="package-badge badge-green">Hill Country</div>
        </div>
        <div class="package-content">
          <h3>Misty Mountain Escape</h3>
          <p class="package-description">Nuwara Eliya • Ella • Horton Plains</p>
          <div class="package-meta">
            <span class="meta-item">4 Days</span>
            <span class="meta-item">Nature</span>
          </div>
          <div class="card-buttons">
            <a href="/CeylonGo/public/tourist/package-details/3" class="btn-outline">View Details</a>
            <a href="/CeylonGo/public/tourist/dashboard#customize" class="btn-black">Book Now</a>
          </div>
        </div>
      </div>

      <!-- Duration-based Packages -->
      <div class="package-card" data-category="duration" data-tags="day-trip adventure">
        <div class="package-image" style="background-image: url('../../public/images/fort.jpg');">
          <div class="package-badge badge-orange">Day Trip</div>
        </div>
        <div class="package-content">
          <h3>Colombo City Explorer</h3>
          <p class="package-description">Full day city tour with lunch</p>
          <div class="package-meta">
            <span class="meta-item">1 Day</span>
            <span class="meta-item">City Tour</span>
          </div>
          <div class="card-buttons">
            <a href="/CeylonGo/public/tourist/package-details/4" class="btn-outline">View Details</a>
            <a href="/CeylonGo/public/tourist/dashboard#customize" class="btn-black">Book Now</a>
          </div>
        </div>
      </div>

      <div class="package-card" data-category="duration" data-tags="short beach family">
        <div class="package-image" style="background-image: url('../../public/images/sunset.jpg');">
          <div class="package-badge badge-cyan">Weekend</div>
        </div>
        <div class="package-content">
          <h3>Quick Beach Getaway</h3>
          <p class="package-description">Bentota • Hikkaduwa</p>
          <div class="package-meta">
            <span class="meta-item">2-3 Days</span>
            <span class="meta-item">Family</span>
          </div>
          <div class="card-buttons">
            <a href="/CeylonGo/public/tourist/package-details/5" class="btn-outline">View Details</a>
            <a href="/CeylonGo/public/tourist/dashboard#customize" class="btn-black">Book Now</a>
          </div>
        </div>
      </div>

      <!-- Experience-based Packages -->
      <div class="package-card" data-category="experience" data-tags="cultural heritage solo">
        <div class="package-image" style="background-image: url('../../public/images/perehara.jpeg');">
          <div class="package-badge badge-purple">Cultural</div>
        </div>
        <div class="package-content">
          <h3>Ancient Heritage Trail</h3>
          <p class="package-description">Temples • Historical Sites • Local Villages</p>
          <div class="package-meta">
            <span class="meta-item">5 Days</span>
            <span class="meta-item">Heritage</span>
          </div>
          <div class="card-buttons">
            <a href="/CeylonGo/public/tourist/package-details/6" class="btn-outline">View Details</a>
            <a href="/CeylonGo/public/tourist/dashboard#customize" class="btn-black">Book Now</a>
          </div>
        </div>
      </div>

      <div class="package-card" data-category="experience" data-tags="wildlife nature adventure">
        <div class="package-image" style="background-image: url('../../public/images/elephant.jpg');">
          <div class="package-badge badge-green">Wildlife</div>
        </div>
        <div class="package-content">
          <h3>Safari & Nature Adventure</h3>
          <p class="package-description">National Parks • Waterfalls • Wildlife</p>
          <div class="package-meta">
            <span class="meta-item">4 Days</span>
            <span class="meta-item">Safari</span>
          </div>
          <div class="card-buttons">
            <a href="/CeylonGo/public/tourist/package-details/7" class="btn-outline">View Details</a>
            <a href="/CeylonGo/public/tourist/dashboard#customize" class="btn-black">Book Now</a>
          </div>
        </div>
      </div>

      <!-- Group Type Packages -->
      <div class="package-card" data-category="group" data-tags="solo adventure">
        <div class="package-image" style="background-image: url('../../public/images/train.jpg');">
          <div class="package-badge badge-red">Solo</div>
        </div>
        <div class="package-content">
          <h3>Solo Adventurer Special</h3>
          <p class="package-description">Curated for independent travelers</p>
          <div class="package-meta">
            <span class="meta-item">Flexible</span>
            <span class="meta-item">Solo</span>
          </div>
          <div class="card-buttons">
            <a href="/CeylonGo/public/tourist/package-details/8" class="btn-outline">View Details</a>
            <a href="/CeylonGo/public/tourist/dashboard#customize" class="btn-black">Book Now</a>
          </div>
        </div>
      </div>

      <div class="package-card" data-category="group" data-tags="family beach">
        <div class="package-image" style="background-image: url('../../public/images/resort.jpg');">
          <div class="package-badge badge-pink">Family</div>
        </div>
        <div class="package-content">
          <h3>Family Fun Package</h3>
          <p class="package-description">Kid-friendly activities & resorts</p>
          <div class="package-meta">
            <span class="meta-item">4-6 Days</span>
            <span class="meta-item">Family</span>
          </div>
          <div class="card-buttons">
            <a href="/CeylonGo/public/tourist/package-details/9" class="btn-outline">View Details</a>
            <a href="/CeylonGo/public/tourist/dashboard#customize" class="btn-black">Book Now</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    // Package filtering functionality
    const filterBtns = document.querySelectorAll('.filter-btn');
    const packageCards = document.querySelectorAll('.package-card');

    filterBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        // Remove active class from all buttons
        filterBtns.forEach(b => b.classList.remove('active'));
        // Add active class to clicked button
        btn.classList.add('active');

        const filter = btn.getAttribute('data-filter');

        packageCards.forEach(card => {
          if (filter === 'all') {
            card.style.display = 'block';
            setTimeout(() => card.classList.add('show'), 10);
          } else {
            const category = card.getAttribute('data-category');
            if (category === filter) {
              card.style.display = 'block';
              setTimeout(() => card.classList.add('show'), 10);
            } else {
              card.classList.remove('show');
              setTimeout(() => card.style.display = 'none', 300);
            }
          }
        });
      });
    });

    // Initialize all cards as visible
    packageCards.forEach(card => {
      card.classList.add('show');
    });
  </script>

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
          <a href="tour_guides" class="btn-outline">Yes</a>
          <button type="button" class="btn-outline">No</button>
        </div>
      </div>

      <div class="form-group full-width">
        <a href="payment" class="btn-black" style="display: inline-block; padding: 10px 20px; text-decoration: none; color: white; background: #000; border: none; cursor: pointer;">Proceed to Payment</a>
      </div>
    </form>
  </section>

  <!-- Navbar include -->
  <?php include 'footer.php'; ?>
</body>
</html>
