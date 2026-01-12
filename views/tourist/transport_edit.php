<?php
require_once('../../config/database.php');
$id = $_GET['id'];

// Update if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customerName = $_POST['customerName'];
    $vehicleType = $_POST['vehicleType'];
    $date = $_POST['date'];
    $pickupTime = $_POST['pickupTime'];
    $pickupLocation = $_POST['pickupLocation'];
    $dropoffLocation = $_POST['dropoffLocation'];
    $numPeople = $_POST['numPeople'];
    $notes = $_POST['notes'];

    $conn->query("UPDATE tourist_transport_requests SET
        customerName='$customerName',
        vehicleType='$vehicleType',
        date='$date',
        pickupTime='$pickupTime',
        pickupLocation='$pickupLocation',
        dropoffLocation='$dropoffLocation',
        numPeople='$numPeople',
        notes='$notes'
        WHERE id=$id");

    header("Location: /CeylonGo/public/tourist/transport-report");
    exit();
}

// Fetch existing data
$row = $conn->query("SELECT * FROM tourist_transport_requests WHERE id=$id")->fetch_assoc();
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
          <input id="customerName" name="customerName" value="<?php echo htmlspecialchars($row['customerName']); ?>" required>
        </div>
        <div class="form-group">
          <label for="vehicleType">Vehicle Type</label>
          <input id="vehicleType" name="vehicleType" value="<?php echo htmlspecialchars($row['vehicleType']); ?>" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="date">Date</label>
          <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($row['date']); ?>" required>
        </div>
        <div class="form-group">
          <label for="pickupTime">Pickup Time</label>
          <input type="time" id="pickupTime" name="pickupTime" value="<?php echo htmlspecialchars($row['pickupTime']); ?>" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="pickupLocation">Pickup Location</label>
          <input id="pickupLocation" name="pickupLocation" value="<?php echo htmlspecialchars($row['pickupLocation']); ?>" required>
        </div>
        <div class="form-group">
          <label for="dropoffLocation">Dropoff Location</label>
          <input id="dropoffLocation" name="dropoffLocation" value="<?php echo htmlspecialchars($row['dropoffLocation']); ?>" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="numPeople">No. of People</label>
          <input type="number" id="numPeople" name="numPeople" value="<?php echo htmlspecialchars($row['numPeople']); ?>" required>
        </div>
        <div class="form-group">
          <label for="notes">Notes (optional)</label>
          <input id="notes" name="notes" value="<?php echo htmlspecialchars($row['notes']); ?>">
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
