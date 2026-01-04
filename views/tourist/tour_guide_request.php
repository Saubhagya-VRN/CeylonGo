<php

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
    <form class="form-card" action="#" method="post">
      <div class="form-row">
        <div class="form-group">
          <label for="customerName">Customer Name</label>
          <input type="text" id="customerName" name="customerName" placeholder="Enter your full name" required>
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
        <a href="tourist_dashboard" class="btn-outline">Cancel</a>
      </div>
    </form>
  </section>

  <footer class="footer-spacer"></footer>
</body>
</html>


