<?php
// Get current date and time for validation
$current_date = date('Y-m-d');
$current_time = date('H:i');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transport Providers - Ceylon Go</title>
  <link rel="stylesheet" href="../../public/css/tourist/transport_providers.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
</head>
<body>
  <section class="hero">
    <h1>Transport Providers</h1>
    <p>Fill the details below to request a transport service.</p>
  </section>

  <section class="form-section">
    <form class="form-card" action="/CeylonGo/public/tourist/transport-request" method="post">
      <div class="form-row">
        <div class="form-group">
          <label for="customerName">Customer Name</label>
          <input type="text" id="customerName" name="customerName" placeholder="Enter your full name" required>
        </div>
        <div class="form-group">
          <label for="vehicleType">Vehicle Type</label>
          <select id="vehicleType" name="vehicleType" required>
            <option value="">Select a vehicle</option>
            <option value="Tuk">Tuk (3 People)</option>
            <option value="Car">Car (4 People)</option>
            <option value="SUV">SUV (4 People)</option>
            <option value="Minivan">Minivan (5 People)</option>
            <option value="Bus">Bus (20 People)</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="date">Date</label>
          <input type="date" id="date" name="date" min="<?php echo $current_date; ?>" required>
        </div>
        <div class="form-group">
          <label for="pickupTime">Pickup Time</label>
          <input type="time" id="pickupTime" name="pickupTime" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="pickupLocation">Pickup Location</label>
          <input type="text" id="pickupLocation" name="pickupLocation" placeholder="e.g., Bandaranaike Airport" required>
        </div>
        <div class="form-group">
          <label for="dropoffLocation">Dropoff Location</label>
          <input type="text" id="dropoffLocation" name="dropoffLocation" placeholder="e.g., Galle Fort" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="numPeople">No. of People</label>
          <input type="number" id="numPeople" name="numPeople" min="1" placeholder="Enter number of passengers" required>
        </div>
        <div class="form-group">
          <label for="notes">Notes (optional)</label>
          <input type="text" id="notes" name="notes" placeholder="Any extra details">
        </div>
      </div>

      <div class="actions">
        <button type="submit" class="btn-primary">Submit Request</button>
        <a href="transport_report.php" class="btn-primary">View Requests</a>
        <a href="tourist_dashboard.php" class="btn-outline">Cancel</a>
      </div>
    </form>
  </section>

  <footer class="footer-spacer"></footer>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const dateInput = document.getElementById('date');
      const timeInput = document.getElementById('pickupTime');
      const currentDate = '<?php echo $current_date; ?>';
      const currentTime = '<?php echo $current_time; ?>';

      // Function to validate time when date changes
      function validateDateTime() {
        const selectedDate = dateInput.value;
        const selectedTime = timeInput.value;

        if (selectedDate === currentDate && selectedTime) {
          if (selectedTime <= currentTime) {
            timeInput.setCustomValidity('Please select a time in the future for today');
            timeInput.reportValidity();
          } else {
            timeInput.setCustomValidity('');
          }
        } else {
          timeInput.setCustomValidity('');
        }
      }

      // Add event listeners
      dateInput.addEventListener('change', validateDateTime);
      timeInput.addEventListener('change', validateDateTime);

      // Set minimum time for today
      dateInput.addEventListener('change', function() {
        if (this.value === currentDate) {
          timeInput.min = currentTime;
        } else {
          timeInput.min = '';
        }
      });

      // Initial validation
      validateDateTime();
    });
  </script>
</body>
</html>


