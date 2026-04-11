<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ceylon Go | Hotel Portal – Inquiries</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,600;14..32,700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../public/css/hotel/style.css" />
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css" />
</head>
<body>
  <header class="navbar">
    <div class="branding">
      <img src="../../public/images/logo.png" alt="Ceylon Go Logo" class="logo-img">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="/CeylonGo/public/hotel/dashboard">Home</a>
      <a href="/CeylonGo/public/logout" class="btn-login">Logout</a>
    </nav>
  </header>

  <?php $active_page = 'inquiries'; include(__DIR__ . '/components/hotel_sidebar.php'); ?>

  <div class="main">
    <header class="topbar">
      <div class="left">
        <h1 class="page-title">Inquiries</h1>
        <div class="hotel-name" id="hotelName">Ocean Breeze Hotel</div>
      </div>
      <div class="right">
        <div class="datetime" id="currentDateTime">--</div>
      </div>
    </header>

    <section class="content">
      <div class="profile-actions">
        <div style="margin-right:auto" class="muted">Traveler messages and questions</div>
        <div id="inquiriesToast" class="success-banner" style="display:none;">Reply sent</div>
      </div>

      <div id="inquiriesList" class="inquiries-list"></div>
    </section>
  </div>

  <script src="../../public/js/hotel.js"></script>
</body>
</html>


