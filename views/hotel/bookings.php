<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ceylon Go | Hotel Portal – Bookings</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
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
  
  <?php $active_page = 'bookings'; include(__DIR__ . '/components/hotel_sidebar.php'); ?>
  <div class="main">
    <header class="topbar">
      <div class="left">
        <h1 class="page-title">Bookings</h1>
        <div class="hotel-name" id="hotelName">Ocean Breeze Hotel</div>
      </div>
      <div class="right">
        <div class="datetime" id="currentDateTime">--</div>
      </div>
    </header>

    <section class="content">
      <div class="profile-actions">
        <div style="margin-right:auto" class="muted">Manage booking requests</div>
        <div id="bookingsNotice" class="success-banner" style="display:none;">Updating...</div>
      </div>

      <div class="panel">
        <div class="panel-header">
          <h2>Bookings</h2>
        </div>
        <div class="panel-body">
          <div class="table-wrap">
            <table class="table" id="bookingsManageTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Guest</th>
                  <th>Check-in</th>
                  <th>Check-out</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($bookings as $booking) { ?>
                  <tr>
                    <td><?php echo $booking['id']; ?></td>
                    <td><?php echo $booking['guest_name']; ?></td>
                    <td><?php echo $booking['check_in']; ?></td>
                    <td><?php echo $booking['check_out']; ?></td>
                    <td><?php echo $booking['total_price'] . ' ' . $booking['currency']; ?></td>
                    <td><?php echo $booking['status']; ?></td>
                    <td>
                      <button class="btn btn-sm btn-secondary"
                              data-booking-modal-open
                              data-booking-id="<?php echo $booking['id']; ?>"
                              data-guest-name="<?php echo htmlspecialchars($booking['guest_name']); ?>"
                              data-guest-email="<?php echo htmlspecialchars('deegagan@gmail.com'); ?>"
                              data-guest-phone="<?php echo htmlspecialchars($booking['contact_number'] ?? ''); ?>"
                              data-room-type="<?php echo htmlspecialchars($booking['room_type'] ?? ''); ?>"
                              data-check-in="<?php echo $booking['check_in']; ?>"
                              data-check-out="<?php echo $booking['check_out']; ?>"
                              data-num-guests="<?php echo $booking['num_guests'] ?? '1'; ?>"
                              data-total-amount="<?php echo $booking['total_price'] . ' ' . $booking['currency']; ?>"
                              data-payment-status="<?php echo htmlspecialchars($booking['payment_status'] ?? 'Pending'); ?>"
                              data-booking-status="<?php echo htmlspecialchars($booking['status']); ?>"
                              data-special-requests="<?php echo htmlspecialchars($booking['special_requests'] ?? 'None'); ?>">
                        View
                      </button>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </div>

  <?php include(__DIR__ . '/components/booking_modal.php'); ?>

  <script src="../../public/js/hotel.js"></script>
</body>
</html>


