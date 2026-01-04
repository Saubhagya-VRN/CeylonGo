<?php
// views/tourist/trip_summary.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'tourist') {
    header('Location: ../login');
    exit;
}

// Get booking ID from URL parameter
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

if ($booking_id === 0) {
    header('Location: tourist_dashboard');
    exit;
}

// Get booking details from database
require_once '../../config/database.php';

// Fetch booking information
$stmt = $conn->prepare("
    SELECT 
        tb.id,
        tb.user_id,
        tb.guide_required,
        tb.status,
        tb.created_at,
        tu.first_name,
        tu.last_name,
        tu.email,
        tu.contact_number
    FROM trip_bookings tb
    JOIN tourist_users tu ON tb.user_id = tu.id
    WHERE tb.id = ? AND tb.user_id = ?
");

$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: tourist_dashboard');
    exit;
}

$booking = $result->fetch_assoc();

// Fetch destination details
$stmt = $conn->prepare("
    SELECT 
        destination,
        people_count,
        days,
        hotel,
        transport
    FROM trip_destinations
    WHERE booking_id = ?
    ORDER BY id ASC
");

$stmt->bind_param("i", $booking_id);
$stmt->execute();
$destinations_result = $stmt->get_result();
$destinations = $destinations_result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();

// Calculate total days and people
$total_days = 0;
$total_people = 0;
foreach ($destinations as $dest) {
    $total_days += $dest['days'];
    $total_people = max($total_people, $dest['people_count']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trip Summary - Ceylon Go</title>
  <link rel="stylesheet" href="../../public/css/common.css">
  <link rel="stylesheet" href="../../public/css/tourist/trip_summary.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
</head>
<body class="bg-app">
  <!-- Navbar include -->
  <?php include 'header.php'; ?>

  <section class="trip-summary-container">
    <div class="success-header">
      <div class="success-icon">✓</div>
      <h1>Booking Successful!</h1>
      <p>Your trip has been customized and saved successfully.</p>
    </div>

    <div class="booking-info">
      <div class="booking-id">
        <strong>Booking ID:</strong> #<?= htmlspecialchars($booking['id']) ?>
      </div>
      <div class="booking-status">
        <strong>Status:</strong> 
        <span class="status-badge status-<?= strtolower($booking['status']) ?>">
          <?= ucfirst(htmlspecialchars($booking['status'])) ?>
        </span>
      </div>
      <div class="booking-date">
        <strong>Booked On:</strong> <?= date('F d, Y \a\t g:i A', strtotime($booking['created_at'])) ?>
      </div>
    </div>

    <div class="customer-info">
      <h2>Customer Information</h2>
      <div class="info-grid">
        <div class="info-item">
          <strong>Name:</strong> <?= htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?>
        </div>
        <div class="info-item">
          <strong>Email:</strong> <?= htmlspecialchars($booking['email']) ?>
        </div>
        <div class="info-item">
          <strong>Contact:</strong> <?= htmlspecialchars($booking['contact_number']) ?>
        </div>
      </div>
    </div>

    <div class="trip-details">
      <h2>Trip Summary</h2>
      
      <div class="trip-overview">
        <div class="overview-item">
          <div class="overview-label">Total Destinations</div>
          <div class="overview-value"><?= count($destinations) ?></div>
        </div>
        <div class="overview-item">
          <div class="overview-label">Total Days</div>
          <div class="overview-value"><?= $total_days ?></div>
        </div>
        <div class="overview-item">
          <div class="overview-label">Travelers</div>
          <div class="overview-value"><?= $total_people ?></div>
        </div>
        <div class="overview-item">
          <div class="overview-label">Tour Guide</div>
          <div class="overview-value"><?= htmlspecialchars($booking['guide_required']) ?></div>
        </div>
      </div>

      <div class="destinations-list">
        <?php foreach ($destinations as $index => $dest): ?>
          <div class="destination-card">
            <div class="destination-header">
              <h3>Destination <?= $index + 1 ?></h3>
              <div class="destination-badge"><?= htmlspecialchars($dest['destination']) ?></div>
            </div>
            <div class="destination-details">
              <div class="detail-row">
                <div class="detail-item">
                  <span class="detail-icon">👥</span>
                  <div>
                    <div class="detail-label">Number of People</div>
                    <div class="detail-value"><?= htmlspecialchars($dest['people_count']) ?></div>
                  </div>
                </div>
                <div class="detail-item">
                  <span class="detail-icon">📅</span>
                  <div>
                    <div class="detail-label">Duration</div>
                    <div class="detail-value"><?= htmlspecialchars($dest['days']) ?> Day<?= $dest['days'] > 1 ? 's' : '' ?></div>
                  </div>
                </div>
              </div>
              
              <?php if (!empty($dest['hotel'])): ?>
                <div class="detail-row">
                  <div class="detail-item full-width">
                    <span class="detail-icon">🏨</span>
                    <div>
                      <div class="detail-label">Hotel</div>
                      <div class="detail-value"><?= htmlspecialchars($dest['hotel']) ?></div>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
              
              <div class="detail-row">
                <div class="detail-item">
                  <span class="detail-icon">🚗</span>
                  <div>
                    <div class="detail-label">Transport Required</div>
                    <div class="detail-value"><?= htmlspecialchars($dest['transport']) ?></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="actions">
      <button onclick="window.print()" class="btn-outline">
        <span>📄</span> Print Summary
      </button>
      <a href="tourist_dashboard" class="btn-primary">
        <span>🏠</span> Back to Dashboard
      </a>
      <a href="tourist_dashboard#customize" class="btn-outline">
        <span>➕</span> Plan Another Trip
      </a>
    </div>

    <div class="next-steps">
      <h3>What's Next?</h3>
      <ul>
        <li>✓ Your booking request has been received and is pending confirmation</li>
        <li>✓ We will review your itinerary and contact you within 24 hours</li>
        <li>✓ You will receive an email with detailed information and pricing</li>
        <li>✓ Payment instructions will be provided after confirmation</li>
      </ul>
    </div>
  </section>

  <!-- Footer include -->
  <?php include 'footer.php'; ?>

  <script>
    // Clear localStorage after successful booking
    localStorage.removeItem('tripFormData');
    
    // Optional: Add confetti or celebration animation
    console.log('Booking completed successfully!');
  </script>
</body>
</html>
