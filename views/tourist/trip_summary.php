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
    header('Location: /CeylonGo/public/tourist/dashboard');
    exit;
}

// Get booking details from database
require_once dirname(__DIR__, 2) . '/config/database.php';

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
    header('Location: /CeylonGo/public/tourist/dashboard');
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

// Fetch transport requests for this user (most recent)
$transport_request = null;
$customer_name = $booking['first_name'] . ' ' . $booking['last_name'];
try {
    // Check if transport requests table exists
    $check_table = $conn->query("SHOW TABLES LIKE 'tourist_transport_requests'");
    if ($check_table && $check_table->num_rows > 0) {
        $stmt = $conn->prepare("
            SELECT 
                customerName,
                vehicleType,
                date,
                pickupTime,
                pickupLocation,
                dropoffLocation,
                numPeople,
                notes
            FROM tourist_transport_requests
            WHERE customerName = ?
            ORDER BY id DESC
            LIMIT 1
        ");
        $stmt->bind_param("s", $customer_name);
        $stmt->execute();
        $transport_result = $stmt->get_result();
        if ($transport_result->num_rows > 0) {
            $transport_request = $transport_result->fetch_assoc();
        }
        $stmt->close();
    }
} catch (Exception $e) {
    error_log("Error fetching transport request: " . $e->getMessage());
}

// Fetch tour guide request for this user (most recent) - if guide is required
$tour_guide_request = null;
if ($booking['guide_required'] === 'Yes') {
    try {
        // Check if there's a tour guide requests table
        $guide_tables = ['tourist_guide_requests', 'tour_guide_requests', 'guide_requests'];
        foreach ($guide_tables as $table) {
            $check_table = $conn->query("SHOW TABLES LIKE '$table'");
            if ($check_table && $check_table->num_rows > 0) {
                $stmt = $conn->prepare("
                    SELECT 
                        customerName,
                        location,
                        language,
                        date as preferred_date,
                        notes
                    FROM $table
                    WHERE customerName = ?
                    ORDER BY id DESC
                    LIMIT 1
                ");
                $stmt->bind_param("s", $customer_name);
                $stmt->execute();
                $guide_result = $stmt->get_result();
                if ($guide_result->num_rows > 0) {
                    $tour_guide_request = $guide_result->fetch_assoc();
                }
                $stmt->close();
                break;
            }
        }
    } catch (Exception $e) {
        error_log("Error fetching tour guide request: " . $e->getMessage());
    }
}

$conn->close();

// Calculate total days and people
$total_days = 0;
$total_people = 0;
foreach ($destinations as $dest) {
    $total_days += $dest['days'];
    $total_people = max($total_people, $dest['people_count']);
}

// Get first destination info
$first_destination = !empty($destinations[0]) ? $destinations[0] : null;
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

    <div class="trip-details">
      <h2>Trip Summary</h2>
      <?php if (!empty($destinations)): ?>
        <div class="trip-summary-box">
          <?php foreach ($destinations as $i => $destination): ?>
            <div class="summary-section">
              <h3 class="section-title">Destination <?= ($i + 1) ?></h3>
              <p class="destination-name"><?= htmlspecialchars($destination['destination']) ?></p>
              <div class="details-list">
                <div class="detail-row">
                  <span class="detail-label">PEOPLE:</span>
                  <span class="detail-value"><?= htmlspecialchars($destination['people_count']) ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-label">DURATION:</span>
                  <span class="detail-value"><?= htmlspecialchars($destination['days']) ?> Day<?= $destination['days'] > 1 ? 's' : '' ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-label">HOTEL:</span>
                  <span class="detail-value">
                    <?= !empty($destination['hotel']) ? htmlspecialchars($destination['hotel']) : 'Not selected' ?>
                    <span class="status-pending">Pending</span>
                  </span>
                </div>
                <div class="detail-row">
                  <span class="detail-label">TRANSPORT:</span>
                  <span class="detail-value">
                    <?= htmlspecialchars($destination['transport']) ?>
                    <?php if ($destination['transport'] === 'Yes'): ?>
                      <span class="status-pending">Pending</span>
                    <?php endif; ?>
                  </span>
                </div>
              </div>
              <div class="diary-action" style="margin-top:10px;">
                <a href="/CeylonGo/public/tourist/add-diary-entry" class="btn-outline">
                  <span>📔</span> My Diaries
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
          
          <!-- Transport Service Section -->
          <?php if ($transport_request): ?>
            <div class="summary-section">
              <h3 class="section-title">Transport Service</h3>
              <div class="details-list">
                <div class="detail-row">
                  <span class="detail-label">VEHICLE:</span>
                  <span class="detail-value"><?= htmlspecialchars($transport_request['vehicleType']) ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-label">DATE:</span>
                  <span class="detail-value"><?= date('M d, Y', strtotime($transport_request['date'])) ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-label">TIME:</span>
                  <span class="detail-value"><?= date('g:i A', strtotime($transport_request['pickupTime'])) ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-label">FROM:</span>
                  <span class="detail-value"><?= htmlspecialchars($transport_request['pickupLocation']) ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-label">TO:</span>
                  <span class="detail-value"><?= htmlspecialchars($transport_request['dropoffLocation']) ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-label">STATUS:</span>
                  <span class="detail-value">
                    <span class="status-pending">Pending</span>
                  </span>
                </div>
              </div>
            </div>
          <?php endif; ?>
          
          <!-- Tour Guide Service Section -->
          <?php if ($booking['guide_required'] === 'Yes'): ?>
            <div class="summary-section">
              <h3 class="section-title">Tour Guide Service</h3>
              <div class="details-list">
                <?php if ($tour_guide_request): ?>
                  <div class="detail-row">
                    <span class="detail-label">LOCATION:</span>
                    <span class="detail-value"><?= htmlspecialchars($tour_guide_request['location']) ?></span>
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">LANGUAGE:</span>
                    <span class="detail-value"><?= htmlspecialchars($tour_guide_request['language']) ?></span>
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">DATE:</span>
                    <span class="detail-value"><?= date('M d, Y', strtotime($tour_guide_request['preferred_date'])) ?></span>
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">STATUS:</span>
                    <span class="detail-value">
                      <span class="status-pending">Pending</span>
                    </span>
                  </div>
                <?php else: ?>
                  <div class="detail-row">
                    <span class="detail-label">INFO:</span>
                    <span class="detail-value">Tour guide requested. Details will be confirmed soon.</span>
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">STATUS:</span>
                    <span class="detail-value">
                      <span class="status-pending">Pending</span>
                    </span>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="actions">
      <button onclick="window.print()" class="btn-outline">
        <span>📄</span> Print Summary
      </button>
      <a href="/CeylonGo/public/tourist/dashboard" class="btn-primary">
        <span>🏠</span> Back to Dashboard
      </a>
      <a href="/CeylonGo/public/tourist/dashboard#customize" class="btn-outline">
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
