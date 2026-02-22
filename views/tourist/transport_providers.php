<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current date and time for validation
$current_date = date('Y-m-d');
$current_time = date('H:i');

// Get user name from session if logged in
$customer_name = '';
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'tourist') {
    // Get name from session if available
    if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
        $customer_name = $_SESSION['user_name'];
    } else {
        // Fetch name from database if not in session
        try {
            require_once '../../config/database.php';
            $stmt = $conn->prepare("SELECT first_name, last_name FROM tourist_users WHERE id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $customer_name = trim($row['first_name'] . ' ' . $row['last_name']);
                // Store in session for future use
                $_SESSION['user_name'] = $customer_name;
            }
            $stmt->close();
            $conn->close();
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }
}
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
          <input type="text" id="customerName" name="customerName" placeholder="Enter your full name" value="<?= htmlspecialchars($customer_name) ?>" required>
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
        <a href="/CeylonGo/public/tourist/transport-report" class="btn-primary">View Requests</a>
        <a href="/CeylonGo/public/tourist/dashboard" class="btn-outline">Cancel</a>
      </div>
    </form>
  </section>

  <footer class="footer-spacer"></footer>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const dateInput = document.getElementById('date');
      const timeInput = document.getElementById('pickupTime');
      const numPeopleInput = document.getElementById('numPeople');
      const currentDate = '<?php echo $current_date; ?>';
      const currentTime = '<?php echo $current_time; ?>';

      // Auto-fill number of people from saved trip form data
      function autoFillNumPeople() {
        // First, try to get from sessionStorage (set when clicking transport Yes button)
        var transportNumPeople = sessionStorage.getItem('transportNumPeople');
        if (transportNumPeople && transportNumPeople > 0 && !numPeopleInput.value) {
          numPeopleInput.value = transportNumPeople;
          // Clear it after using so it doesn't persist
          sessionStorage.removeItem('transportNumPeople');
          return;
        }
        
        // Try to get from localStorage (from the trip form)
        var savedFormData = localStorage.getItem('tripFormData');
        if (savedFormData) {
          try {
            var formData = JSON.parse(savedFormData);
            // Get the number of people from the last trip group (most recent)
            if (formData.people && formData.people.length > 0) {
              // Get the last entry (most recent destination)
              var numPeople = formData.people[formData.people.length - 1];
              if (numPeople && numPeople > 0 && !numPeopleInput.value) {
                numPeopleInput.value = numPeople;
              }
            }
          } catch(e) {
            console.error('Error parsing saved form data:', e);
          }
        }
        
        // Also try sessionStorage as fallback
        if (!numPeopleInput.value) {
          var sessionFormData = sessionStorage.getItem('tripFormData');
          if (sessionFormData) {
            try {
              var formData = JSON.parse(sessionFormData);
              if (formData.people && formData.people.length > 0) {
                var numPeople = formData.people[formData.people.length - 1];
                if (numPeople && numPeople > 0) {
                  numPeopleInput.value = numPeople;
                }
              }
            } catch(e) {
              console.error('Error parsing session form data:', e);
            }
          }
        }
      }

      // Auto-fill number of people on page load
      autoFillNumPeople();

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


