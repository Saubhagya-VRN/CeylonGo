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
  <style>
    body {
      background: #f0f8f0;
      min-height: 100vh;
    }
    .page-header {
      background: linear-gradient(135deg, #2c5530, #4a7c59);
      color: white;
      text-align: center;
      padding: 40px 20px;
      margin-bottom: 30px;
    }
    .page-header h1 {
      margin: 0 0 10px 0;
      font-size: 32px;
      font-weight: 700;
    }
    .page-header p {
      margin: 0;
      font-size: 16px;
      opacity: 0.95;
    }
    .content-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px 40px;
    }
    .table-container {
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 8px 25px rgba(74,124,89,0.15);
      border: 1px solid rgba(74,124,89,0.1);
      overflow: hidden;
      margin-bottom: 20px;
    }
    .data-table {
      width: 100%;
      border-collapse: collapse;
    }
    .data-table thead tr {
      background: linear-gradient(135deg, #4a7c59, #5a8c69);
      color: #fff;
    }
    .data-table th {
      text-align: left;
      padding: 16px;
      font-weight: 600;
      font-size: 14px;
    }
    .data-table td {
      padding: 14px 16px;
      border-bottom: 1px solid #e8efe8;
      font-size: 14px;
      color: #333;
    }
    .data-table tbody tr:last-child td {
      border-bottom: none;
    }
    .data-table tbody tr:hover {
      background: #f8fff8;
    }
    .status-badge {
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      display: inline-block;
    }
    .status-pending {
      background: #fff3cd;
      color: #856404;
    }
    .status-approved {
      background: #d4edda;
      color: #155724;
    }
    .status-rejected {
      background: #f8d7da;
      color: #721c24;
    }
    .action-buttons {
      display: flex;
      gap: 12px;
      justify-content: center;
      flex-wrap: wrap;
    }
    .btn-primary {
      background: #4a7c59;
      color: white;
      padding: 12px 24px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      font-size: 15px;
      transition: all 0.3s ease;
      display: inline-block;
    }
    .btn-primary:hover {
      background: #2c5530;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(74,124,89,0.3);
    }
    .btn-secondary {
      background: white;
      color: #2c5530;
      padding: 12px 24px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      font-size: 15px;
      border: 2px solid #2c5530;
      transition: all 0.3s ease;
      display: inline-block;
    }
    .btn-secondary:hover {
      background: #2c5530;
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(74,124,89,0.3);
    }
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      background: #f8f9f8;
      border-radius: 12px;
      border: 1px solid rgba(74,124,89,0.1);
    }
    .empty-state p {
      font-size: 18px;
      color: #5a6b5a;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="page-header">
    <h1>Tour Guide Requests</h1>
    <p>Review and manage your submitted tour guide requests</p>
  </div>

  <div class="content-container">
    <?php if ($error): ?>
      <div class="alert alert-error" style="margin-bottom: 20px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (empty($requests)): ?>
      <div class="empty-state">
        <p>No tour guide requests found.</p>
        <a href="/CeylonGo/public/tourist/dashboard" class="btn-primary">Go to Dashboard</a>
      </div>
    <?php else: ?>
      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>Customer Name</th>
              <th>Location</th>
              <th>Language</th>
              <th>Preferred Date</th>
              <th>Assigned Guide</th>
              <th>Status</th>
              <th>Notes</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($requests as $request): ?>
            <tr>
              <td><?php echo htmlspecialchars($request['customerName']); ?></td>
              <td><?php echo htmlspecialchars($request['location']); ?></td>
              <td><?php echo htmlspecialchars($request['language']); ?></td>
              <td>
                <?php 
                  if (!empty($request['date'])) {
                    echo date('M d, Y', strtotime($request['date']));
                  } else {
                    echo 'N/A';
                  }
                ?>
              </td>
              <td>
                <?php 
                  if (!empty($request['guide_name']) && trim($request['guide_name']) !== '') {
                    echo htmlspecialchars($request['guide_name']);
                  } else {
                    echo '<span style="color:#888;">Assigning...</span>';
                  }
                ?>
              </td>
              <td>
                <?php
                  $status = $request['status'] ?? 'pending';
                  if ($status === 'approved') {
                    echo '<span class="status-badge status-approved">✓ Confirmed</span>';
                  } elseif ($status === 'rejected') {
                    echo '<span class="status-badge status-rejected">Rejected</span>';
                  } else {
                    echo '<span class="status-badge status-pending">Pending</span>';
                  }
                ?>
              </td>
              <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                <?php echo !empty($request['notes']) ? htmlspecialchars($request['notes']) : '-'; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

    <div class="action-buttons">
      <a href="/CeylonGo/public/tourist/dashboard#customize" class="btn-primary">Submit Another Request</a>
      <a href="/CeylonGo/public/tourist/dashboard" class="btn-secondary">Back to Dashboard</a>
    </div>
  </div>

</body>
</html>

