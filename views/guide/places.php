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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .places-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }
    .place-card {
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      transition: transform 0.2s, box-shadow 0.2s;
      border: 1px solid #eee;
    }
    .place-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    }
    .place-card-image {
      height: 180px;
      overflow: hidden;
      background: linear-gradient(135deg, #3d8b40 0%, #2c5530 100%);
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .place-card-image i {
      font-size: 48px;
      color: rgba(255, 255, 255, 0.3);
    }
    .place-card-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .place-card-body {
      padding: 20px;
    }
    .place-card-body h4 {
      margin: 0 0 10px 0;
      color: #2c5530;
      font-size: 1.2em;
    }
    .place-detail {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      margin-bottom: 8px;
      color: #555;
      font-size: 14px;
    }
    .place-detail i {
      color: #4CAF50;
      width: 16px;
      margin-top: 3px;
    }
    .place-card-actions {
      display: flex;
      gap: 10px;
      padding: 15px 20px;
      background: #f8f9fa;
      border-top: 1px solid #eee;
    }
    .btn-edit-place, .btn-delete-place {
      flex: 1;
      padding: 10px;
      border: none;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: all 0.2s;
    }
    .btn-edit-place {
      background: #e3f2fd;
      color: #1976d2;
    }
    .btn-edit-place:hover {
      background: #1976d2;
      color: white;
    }
    .btn-delete-place {
      background: #ffebee;
      color: #c62828;
    }
    .btn-delete-place:hover {
      background: #c62828;
      color: white;
    }
    .btn-add-place {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: linear-gradient(135deg, #3d8b40 0%, #2c5530 100%);
      color: white;
      padding: 12px 25px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      border: none;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .btn-add-place:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(44, 85, 48, 0.3);
    }
    .no-places {
      text-align: center;
      padding: 60px 40px;
      background: #f8f9fa;
      border-radius: 12px;
      color: #666;
    }
    .no-places i {
      font-size: 64px;
      color: #ddd;
      margin-bottom: 20px;
    }
    .no-places p {
      margin-bottom: 20px;
      font-size: 16px;
    }
    .add-form-container {
      background: #fff;
      border-radius: 12px;
      padding: 25px;
      margin-bottom: 25px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      display: none;
    }
    .add-form-container.active {
      display: block;
    }
    .add-form-container h3 {
      margin: 0 0 20px 0;
      color: #333;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .form-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 15px;
      margin-bottom: 15px;
    }
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #555;
      font-size: 14px;
    }
    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 15px;
      transition: border-color 0.3s, box-shadow 0.3s;
      font-family: inherit;
    }
    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #4CAF50;
      box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
    }
    .form-group textarea {
      resize: vertical;
      min-height: 80px;
    }
    .form-actions {
      display: flex;
      gap: 10px;
      margin-top: 20px;
    }
    .btn-save {
      background: linear-gradient(135deg, #3d8b40 0%, #2c5530 100%);
      color: white;
      border: none;
      padding: 12px 30px;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .btn-save:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(44, 85, 48, 0.3);
    }
    .btn-cancel {
      background: #f5f5f5;
      color: #666;
      border: none;
      padding: 12px 30px;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s;
    }
    .btn-cancel:hover {
      background: #e0e0e0;
    }
  </style>
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
        <li class="active"><a href="/CeylonGo/public/guide/places"><i class="fa-solid fa-map-location-dot"></i> My Places</a></li>
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