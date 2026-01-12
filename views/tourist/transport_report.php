<?php
require_once(dirname(__DIR__, 2) . '/config/database.php');

// Fetch all requests
$result = $conn->query("SELECT * FROM tourist_transport_requests ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transport Requests - Ceylon Go</title>
  <link rel="stylesheet" href="../../public/css/common.css">
  <link rel="stylesheet" href="../../public/css/tourist/tourist_dashboard.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
</head>
<body>
  <?php include(dirname(__FILE__) . '/header.php'); ?>

  <section class="intro" style="padding: 60px 20px;">
    <h1>Transport Requests</h1>
    <p>Review, edit, or delete your submitted transport requests.</p>
  </section>

  <section style="padding: 40px 20px;">
    <div style="max-width: 1100px; margin: 0 auto;">
      <div style="overflow-x:auto; background:#ffffff; border-radius: 12px; box-shadow: 0 8px 25px rgba(74,124,89,0.15); border: 1px solid rgba(74,124,89,0.1);">
        <table style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr style="background: linear-gradient(135deg, #4a7c59, #5a8c69); color: #fff;">
              <th style="text-align:left; padding: 14px 16px;">ID</th>
              <th style="text-align:left; padding: 14px 16px;">Customer</th>
              <th style="text-align:left; padding: 14px 16px;">Vehicle</th>
              <th style="text-align:left; padding: 14px 16px;">Date</th>
              <th style="text-align:left; padding: 14px 16px;">Time</th>
              <th style="text-align:left; padding: 14px 16px;">Pickup</th>
              <th style="text-align:left; padding: 14px 16px;">Dropoff</th>
              <th style="text-align:left; padding: 14px 16px;">People</th>
              <th style="text-align:left; padding: 14px 16px;">Notes</th>
              <th style="text-align:left; padding: 14px 16px;">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr style="border-top: 1px solid #e0e8e0;">
              <td style="padding: 12px 16px;"><?php echo $row['id']; ?></td>
              <td style="padding: 12px 16px;"><?php echo htmlspecialchars($row['customerName']); ?></td>
              <td style="padding: 12px 16px;"><?php echo htmlspecialchars($row['vehicleType']); ?></td>
              <td style="padding: 12px 16px;"><?php echo htmlspecialchars($row['date']); ?></td>
              <td style="padding: 12px 16px;"><?php echo htmlspecialchars($row['pickupTime']); ?></td>
              <td style="padding: 12px 16px;"><?php echo htmlspecialchars($row['pickupLocation']); ?></td>
              <td style="padding: 12px 16px;"><?php echo htmlspecialchars($row['dropoffLocation']); ?></td>
              <td style="padding: 12px 16px;"><?php echo htmlspecialchars($row['numPeople']); ?></td>
              <td style="padding: 12px 16px;"><?php echo htmlspecialchars($row['notes']); ?></td>
              <td style="padding: 12px 16px; white-space: nowrap;">
                <a class="btn btn-black" href="/CeylonGo/public/tourist/transport-edit/<?php echo $row['id']; ?>">Edit</a>
                <a class="btn" href="/CeylonGo/public/tourist/transport-delete/<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this request?')" style="background:#a33; border:2px solid #a33; color:#fff; margin-left:8px;">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <div style="margin-top: 20px; display:flex; gap: 12px;">
        <a href="/CeylonGo/public/tourist/transport-providers" class="btn">Submit Another Request</a>
        <a href="/CeylonGo/public/tourist/dashboard" class="btn btn-black">Back to Dashboard</a>
      </div>
    </div>
  </section>

  <footer class="footer-spacer"></footer>
</body>
</html>
