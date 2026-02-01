<?php
require_once('../../config/database.php');
$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    header("Location: /CeylonGo/public/tourist/transport-report");
    exit();
}

// Update if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customerName = $conn->real_escape_string($_POST['customerName'] ?? '');
    $contactNumber = $conn->real_escape_string($_POST['contactNumber'] ?? '');
    $vehicleType = $conn->real_escape_string($_POST['vehicleType'] ?? '');
    $date = $conn->real_escape_string($_POST['date'] ?? '');
    $pickupTime = $conn->real_escape_string($_POST['pickupTime'] ?? '');
    $pickupLocation = $conn->real_escape_string($_POST['pickupLocation'] ?? '');
    $dropoffLocation = $conn->real_escape_string($_POST['dropoffLocation'] ?? '');
    $numPeople = (int) ($_POST['numPeople'] ?? 0);
    $notes = $conn->real_escape_string($_POST['notes'] ?? '');

    $stmt = $conn->prepare("UPDATE transport_requests SET
        customer_name=?, contact_number=?, vehicle_type=?, date=?, pickup_time=?,
        pickup_location=?, dropoff_location=?, num_people=?, notes=?
        WHERE id=?");
    $stmt->bind_param("sssssssisi", $customerName, $contactNumber, $vehicleType, $date, $pickupTime, $pickupLocation, $dropoffLocation, $numPeople, $notes, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: /CeylonGo/public/tourist/transport-report");
    exit();
}

// Fetch existing data from transport_requests
$stmt = $conn->prepare("SELECT * FROM transport_requests WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();
if (!$row) {
    header("Location: /CeylonGo/public/tourist/transport-report");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Transport Request - Ceylon Go</title>
  <link rel="stylesheet" href="../../public/css/common.css">
  <link rel="stylesheet" href="../../public/css/tourist/tourist_dashboard.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
</head>
<body>
  <?php include('./header.php'); ?>

  <section class="intro" style="padding: 60px 20px;">
    <h1>Edit Transport Request</h1>
    <p>Update your transport request details below.</p>
  </section>

  <section style="padding: 40px 20px;">
    <form class="form-card" method="POST" style="max-width: 900px; margin: 0 auto; background:#fff; border-radius:12px; box-shadow:0 8px 25px rgba(74,124,89,0.15); border:1px solid rgba(74,124,89,0.1); padding: 24px;">
      <div class="form-row">
        <div class="form-group">
          <label for="customerName">Customer Name</label>
          <input id="customerName" name="customerName" value="<?php echo htmlspecialchars($row['customer_name']); ?>" required>
        </div>
        <div class="form-group">
          <label for="contactNumber">Contact Number</label>
          <input type="tel" id="contactNumber" name="contactNumber" value="<?php echo htmlspecialchars($row['contact_number']); ?>" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="vehicleType">Vehicle Type</label>
          <input id="vehicleType" name="vehicleType" value="<?php echo htmlspecialchars($row['vehicle_type']); ?>" required>
        </div>
        <div class="form-group">
          <label for="numPeople">No. of People</label>
          <input type="number" id="numPeople" name="numPeople" value="<?php echo (int) $row['num_people']; ?>" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="date">Date</label>
          <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($row['date']); ?>" required>
        </div>
        <div class="form-group">
          <label for="pickupTime">Pickup Time</label>
          <input type="time" id="pickupTime" name="pickupTime" value="<?php echo htmlspecialchars($row['pickup_time']); ?>" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="pickupLocation">Pickup Location</label>
          <input id="pickupLocation" name="pickupLocation" value="<?php echo htmlspecialchars($row['pickup_location']); ?>" required>
        </div>
        <div class="form-group">
          <label for="dropoffLocation">Dropoff Location</label>
          <input id="dropoffLocation" name="dropoffLocation" value="<?php echo htmlspecialchars($row['dropoff_location']); ?>" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="notes">Notes (optional)</label>
          <input id="notes" name="notes" value="<?php echo htmlspecialchars($row['notes'] ?? ''); ?>">
        </div>
      </div>

      <div class="actions" style="display:flex; gap:12px;">
        <button type="submit" class="btn btn-black">Update Request</button>
        <a href="/CeylonGo/public/tourist/transport-report" class="btn">Cancel</a>
      </div>
    </form>
  </section>

  <footer class="footer-spacer"></footer>
</body>
</html>
