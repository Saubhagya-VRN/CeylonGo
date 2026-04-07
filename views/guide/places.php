<?php require_once 'session_init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - My Places</title>
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/base.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/navbar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/sidebar.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/cards.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/buttons.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/forms.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/tables.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/footer.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/responsive.css">
  <link rel="stylesheet" href="/CeylonGo/public/css/guide/places.css">
</head>
<body>
  <!-- Navbar -->
  <header class="navbar">
    <div class="branding">
      <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle menu">
        <span></span>
        <span></span>
        <span></span>
      </button>
      <img src="/CeylonGo/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="/CeylonGo/public/guide/dashboard">Home</a>
      <div class="profile-dropdown">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="User" class="profile-pic" onclick="toggleProfileDropdown()">
        <div class="profile-dropdown-menu" id="profileDropdown">
          <a href="/CeylonGo/public/guide/profile"><i class="fa-regular fa-user"></i> My Profile</a>
          <a href="/CeylonGo/public/logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
      </div>
    </nav>
  </header>
  
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="page-wrapper">
    <div class="sidebar" id="sidebar">
      <ul>
        <li><a href="/CeylonGo/public/guide/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="/CeylonGo/public/guide/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Tours</a></li>
        <li><a href="/CeylonGo/public/guide/pending"><i class="fa-regular fa-clock"></i> Pending Requests</a></li>
        <li><a href="/CeylonGo/public/guide/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Tours</a></li>
        <li><a href="/CeylonGo/public/guide/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li><a href="/CeylonGo/public/guide/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/guide/report"><i class="fa-solid fa-chart-line"></i> Performance Report</a></li>
        <li class="active"><a href="/CeylonGo/public/guide/places"><i class="fa-solid fa-map-location-dot"></i> My Places</a></li>
        <li><a href="/CeylonGo/public/guide/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </div>

    <div class="main-content">
      <h2 class="page-title"><i class="fa-solid fa-map-location-dot"></i> My Places</h2>

      <button class="btn-add-place" onclick="toggleAddForm()">
        <i class="fa-solid fa-plus"></i> Add New Place
      </button>

      <!-- Add Place Form -->
      <div class="add-form-container" id="addPlaceForm">
        <h3><i class="fa-solid fa-plus-circle"></i> Add New Place</h3>
        <form method="POST" action="">
          <div class="form-row">
            <div class="form-group">
              <label>Place Name</label>
              <input type="text" name="place_name" placeholder="e.g., Sigiriya Rock Fortress" required>
            </div>
            <div class="form-group">
              <label>Location/Address</label>
              <input type="text" name="address" placeholder="e.g., Sigiriya, Central Province" required>
            </div>
          </div>
          <div class="form-group">
            <label>Description/Notes</label>
            <textarea name="notes" placeholder="Describe what makes this place special, best time to visit, etc."></textarea>
          </div>
          <div class="form-actions">
            <button type="submit" name="add_place" class="btn-save">
              <i class="fa-solid fa-save"></i> Save Place
            </button>
            <button type="button" class="btn-cancel" onclick="toggleAddForm()">Cancel</button>
          </div>
        </form>
      </div>

      <!-- Places Grid -->
      <div class="places-grid">
        <div class="place-card">
          <div class="place-card-image">
            <i class="fa-solid fa-mountain-sun"></i>
          </div>
          <div class="place-card-body">
            <h4>Sigiriya Rock Fortress</h4>
            <div class="place-detail">
              <i class="fa-solid fa-location-dot"></i>
              <span>Sigiriya, Central Province</span>
            </div>
            <div class="place-detail">
              <i class="fa-solid fa-info-circle"></i>
              <span>Ancient rock fortress with stunning views and historical frescoes. Best visited early morning.</span>
            </div>
          </div>
          <div class="place-card-actions">
            <button class="btn-edit-place"><i class="fa-solid fa-edit"></i> Edit</button>
            <button class="btn-delete-place"><i class="fa-solid fa-trash"></i> Delete</button>
          </div>
        </div>

        <div class="place-card">
          <div class="place-card-image">
            <i class="fa-solid fa-landmark"></i>
          </div>
          <div class="place-card-body">
            <h4>Temple of the Tooth</h4>
            <div class="place-detail">
              <i class="fa-solid fa-location-dot"></i>
              <span>Kandy, Central Province</span>
            </div>
            <div class="place-detail">
              <i class="fa-solid fa-info-circle"></i>
              <span>Sacred Buddhist temple housing the relic of the tooth of Buddha. Puja ceremonies daily.</span>
            </div>
          </div>
          <div class="place-card-actions">
            <button class="btn-edit-place"><i class="fa-solid fa-edit"></i> Edit</button>
            <button class="btn-delete-place"><i class="fa-solid fa-trash"></i> Delete</button>
          </div>
        </div>

        <div class="place-card">
          <div class="place-card-image">
            <i class="fa-solid fa-umbrella-beach"></i>
          </div>
          <div class="place-card-body">
            <h4>Galle Fort</h4>
            <div class="place-detail">
              <i class="fa-solid fa-location-dot"></i>
              <span>Galle, Southern Province</span>
            </div>
            <div class="place-detail">
              <i class="fa-solid fa-info-circle"></i>
              <span>UNESCO World Heritage Site. Dutch colonial architecture, boutique shops, and beautiful sunsets.</span>
            </div>
          </div>
          <div class="place-card-actions">
            <button class="btn-edit-place"><i class="fa-solid fa-edit"></i> Edit</button>
            <button class="btn-delete-place"><i class="fa-solid fa-trash"></i> Delete</button>
          </div>
        </div>

        <div class="place-card">
          <div class="place-card-image">
            <i class="fa-solid fa-paw"></i>
          </div>
          <div class="place-card-body">
            <h4>Yala National Park</h4>
            <div class="place-detail">
              <i class="fa-solid fa-location-dot"></i>
              <span>Hambantota, Southern Province</span>
            </div>
            <div class="place-detail">
              <i class="fa-solid fa-info-circle"></i>
              <span>Famous for leopards and elephants. Best visited early morning or late afternoon for wildlife sightings.</span>
            </div>
          </div>
          <div class="place-card-actions">
            <button class="btn-edit-place"><i class="fa-solid fa-edit"></i> Edit</button>
            <button class="btn-delete-place"><i class="fa-solid fa-trash"></i> Delete</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    hamburgerBtn.addEventListener('click', function() {
      hamburgerBtn.classList.toggle('active');
      sidebar.classList.toggle('active');
      sidebarOverlay.classList.toggle('active');
    });

    sidebarOverlay.addEventListener('click', function() {
      hamburgerBtn.classList.remove('active');
      sidebar.classList.remove('active');
      sidebarOverlay.classList.remove('active');
    });

    function toggleProfileDropdown() {
      document.getElementById('profileDropdown').classList.toggle('show');
    }

    window.onclick = function(event) {
      if (!event.target.matches('.profile-pic')) {
        var dropdowns = document.getElementsByClassName("profile-dropdown-menu");
        for (var i = 0; i < dropdowns.length; i++) {
          if (dropdowns[i].classList.contains('show')) {
            dropdowns[i].classList.remove('show');
          }
        }
      }
    }

    function toggleAddForm() {
      const form = document.getElementById('addPlaceForm');
      form.classList.toggle('active');
    }
  </script>
  <!-- Footer -->
  <footer>
    <ul>
      <li><a href="#">About Us</a></li>
      <li><a href="#">Contact Us</a></li>
    </ul>
  </footer>
</body>
</html>