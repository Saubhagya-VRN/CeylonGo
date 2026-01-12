<?php
// views/tourist/tour_guide_request_report.php
$requests = $requests ?? [];
$error = $error ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tour Guide Requests - Ceylon Go</title>
  <link rel="stylesheet" href="../../public/css/common.css">
  <link rel="stylesheet" href="../../public/css/tourist/tourist_dashboard.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
</head>
<body>
  <?php include(dirname(__FILE__) . '/header.php'); ?>

  <section class="intro" style="padding: 60px 20px;">
    <h1>Tour Guide Requests</h1>
    <p>Review your submitted tour guide requests.</p>
  </section>

  <section style="padding: 40px 20px;">
    <div style="max-width: 1100px; margin: 0 auto;">
      <?php if ($error): ?>
        <div class="alert alert-error" style="margin-bottom: 20px;"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <?php if (empty($requests)): ?>
        <div style="text-align: center; padding: 60px 20px; background: #f8f9f8; border-radius: 12px; border: 1px solid rgba(74,124,89,0.1);">
          <p style="font-size: 18px; color: #5a6b5a; margin-bottom: 20px;">No tour guide requests found.</p>
          <a href="/CeylonGo/public/tourist/tour-guide-request" class="btn-primary">Submit Your First Request</a>
        </div>
      <?php else: ?>
        <div style="overflow-x:auto; background:#ffffff; border-radius: 12px; box-shadow: 0 8px 25px rgba(74,124,89,0.15); border: 1px solid rgba(74,124,89,0.1);">
          <table style="width: 100%; border-collapse: collapse;">
            <thead>
              <tr style="background: linear-gradient(135deg, #4a7c59, #5a8c69); color: #fff;">
                <th style="text-align:left; padding: 14px 16px;">ID</th>
                <th style="text-align:left; padding: 14px 16px;">Customer Name</th>
                <th style="text-align:left; padding: 14px 16px;">Location</th>
                <th style="text-align:left; padding: 14px 16px;">Language</th>
                <th style="text-align:left; padding: 14px 16px;">Preferred Date</th>
                <th style="text-align:left; padding: 14px 16px;">Notes</th>
                <th style="text-align:left; padding: 14px 16px;">Requested On</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach($requests as $request): ?>
              <tr style="border-top: 1px solid #e0e8e0;">
                <td style="padding: 12px 16px;">#<?php echo $request['id']; ?></td>
                <td style="padding: 12px 16px;"><?php echo htmlspecialchars($request['customerName']); ?></td>
                <td style="padding: 12px 16px;"><?php echo htmlspecialchars($request['location']); ?></td>
                <td style="padding: 12px 16px;"><?php echo htmlspecialchars($request['language']); ?></td>
                <td style="padding: 12px 16px;">
                  <?php 
                    if (!empty($request['date'])) {
                      echo date('M d, Y', strtotime($request['date']));
                    } else {
                      echo 'N/A';
                    }
                  ?>
                </td>
                <td style="padding: 12px 16px;"><?php echo htmlspecialchars($request['notes'] ?? 'No notes'); ?></td>
                <td style="padding: 12px 16px;">
                  <?php 
                    if (isset($request['created_at'])) {
                      echo date('M d, Y H:i', strtotime($request['created_at']));
                    } else {
                      echo 'N/A';
                    }
                  ?>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <div style="margin-top: 20px; display:flex; gap: 12px;">
        <a href="/CeylonGo/public/tourist/tour-guide-request" class="btn-primary">Submit Another Request</a>
        <a href="/CeylonGo/public/tourist/dashboard" class="btn btn-black">Back to Dashboard</a>
      </div>
    </div>
  </section>

  <footer class="footer-spacer"></footer>
</body>
</html>

