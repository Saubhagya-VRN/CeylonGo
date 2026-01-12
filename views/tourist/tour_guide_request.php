<?php
// Start session and get customer name
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get customer name from session if logged in
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
  <title>Tour Guide Request - Ceylon Go</title>
  <link rel="stylesheet" href="../../public/css/tourist/tour_guide_request.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
</head>
<body>
  <section class="hero">
    <h1>Tour Guide Request</h1>
    <p>Provide your preferences to request a tour guide.</p>
  </section>

  <section class="form-section">
    <form class="form-card" action="/CeylonGo/public/tourist/tour-guide-request" method="post">
      <div class="form-row">
        <div class="form-group">
          <label for="customerName">Customer Name</label>
          <input type="text" id="customerName" name="customerName" placeholder="Enter your full name" value="<?= htmlspecialchars($customer_name) ?>" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="location">Location</label>
          <input type="text" id="location" name="location" placeholder="e.g., Kandy" required>
        </div>
        <div class="form-group">
          <label for="language">Preferred Language</label>
          <select id="language" name="language" required>
            <option value="">Select language</option>
            <option value="English">English</option>
            <option value="Sinhala">Sinhala</option>
            <option value="Tamil">Tamil</option>
            <option value="Hindi">Hindi</option>
            <option value="French">French</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="date">Preferred Date</label>
          <input type="date" id="date" name="date" required>
        </div>
        <div class="form-group">
          <label for="notes">Notes (optional)</label>
          <input type="text" id="notes" name="notes" placeholder="Any special requests">
        </div>
      </div>

      <div class="actions">
        <button type="submit" class="btn-primary">Submit Request</button>
        <a href="/CeylonGo/public/tourist/tour-guide-request-report" class="btn-outline">View Requests</a>
        <a href="/CeylonGo/public/tourist/dashboard" class="btn-outline">Cancel</a>
      </div>
    </form>
  </section>

  <footer class="footer-spacer"></footer>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const locationInput = document.getElementById('location');

      // Auto-fill location from saved trip form data
      function autoFillLocation() {
        // First, try to get from sessionStorage (set when clicking guide Yes button)
        var guideLocation = sessionStorage.getItem('guideLocation');
        if (guideLocation && !locationInput.value) {
          locationInput.value = guideLocation;
          // Clear it after using so it doesn't persist
          sessionStorage.removeItem('guideLocation');
          return;
        }
        
        // Try to get from localStorage (from the trip form)
        var savedFormData = localStorage.getItem('tripFormData');
        if (savedFormData) {
          try {
            var formData = JSON.parse(savedFormData);
            // Get the destination from the last trip group (most recent)
            if (formData.destinations && formData.destinations.length > 0) {
              // Get the last entry (most recent destination)
              var location = formData.destinations[formData.destinations.length - 1];
              if (location && !locationInput.value) {
                locationInput.value = location;
              }
            }
          } catch(e) {
            console.error('Error parsing saved form data:', e);
          }
        }
        
        // Also try sessionStorage as fallback
        if (!locationInput.value) {
          var sessionFormData = sessionStorage.getItem('tripFormData');
          if (sessionFormData) {
            try {
              var formData = JSON.parse(sessionFormData);
              if (formData.destinations && formData.destinations.length > 0) {
                var location = formData.destinations[formData.destinations.length - 1];
                if (location) {
                  locationInput.value = location;
                }
              }
            } catch(e) {
              console.error('Error parsing session form data:', e);
            }
          }
        }
      }

      // Auto-fill location on page load
      autoFillLocation();
    });
  </script>
</body>
</html>


