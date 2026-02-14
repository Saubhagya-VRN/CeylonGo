<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/models/TransportRequestModel.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
    header("Location: /CeylonGo/public/login");
    exit();
}

$db = $conn;
$transportModel = new TransportRequestModel($db);
$requests = $transportModel->getByUserId($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Transport Requests - Ceylon Go</title>
    <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 80px auto 20px;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 2em;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }
        
        .requests-grid {
            display: grid;
            gap: 20px;
        }
        
        .request-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            border-left: 4px solid #667eea;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .request-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .request-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .request-id {
            font-weight: bold;
            color: #667eea;
            font-size: 1.1em;
        }
        
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-completed {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .request-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-size: 0.85em;
            color: #666;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .detail-value {
            color: #333;
            font-weight: 600;
        }
        
        .route-info {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        
        .route-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
        }
        
        .route-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }
        
        .pickup-icon {
            background: #28a745;
        }
        
        .dropoff-icon {
            background: #dc3545;
        }
        
        .fare-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            text-align: center;
        }
        
        .fare-amount {
            font-size: 1.5em;
            font-weight: bold;
        }
        
        .no-requests {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .no-requests-icon {
            font-size: 4em;
            margin-bottom: 20px;
            opacity: 0.3;
        }
        
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        
        .back-btn:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <a href="tourist_dashboard.php" class="back-btn">← Back to Dashboard</a>
        
        <h1>My Transport Requests</h1>
        <p class="subtitle">View and manage your transport booking requests</p>
        
        <div class="requests-grid">
            <?php if ($requests->num_rows > 0): ?>
                <?php while($request = $requests->fetch_assoc()): ?>
                    <div class="request-card">
                        <div class="request-header">
                            <span class="request-id">Request #<?= $request['id'] ?></span>
                            <span class="status-badge status-<?= $request['status'] ?>">
                                <?= ucfirst($request['status']) ?>
                            </span>
                        </div>
                        
                        <div class="request-details">
                            <div class="detail-item">
                                <span class="detail-label">Customer Name</span>
                                <span class="detail-value"><?= htmlspecialchars($request['customer_name']) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Contact Number</span>
                                <span class="detail-value"><?= htmlspecialchars($request['contact_number']) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Date</span>
                                <span class="detail-value"><?= date('M d, Y', strtotime($request['date'])) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Pickup Time</span>
                                <span class="detail-value"><?= date('h:i A', strtotime($request['pickup_time'])) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Vehicle Type</span>
                                <span class="detail-value"><?= htmlspecialchars($request['vehicle_type']) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">No. of People</span>
                                <span class="detail-value"><?= $request['num_people'] ?></span>
                            </div>
                        </div>
                        
                        <div class="route-info">
                            <div class="route-item">
                                <div class="route-icon pickup-icon">A</div>
                                <div>
                                    <div class="detail-label">Pickup Location</div>
                                    <div class="detail-value"><?= htmlspecialchars($request['pickup_location']) ?></div>
                                </div>
                            </div>
                            <div class="route-item">
                                <div class="route-icon dropoff-icon">B</div>
                                <div>
                                    <div class="detail-label">Dropoff Location</div>
                                    <div class="detail-value"><?= htmlspecialchars($request['dropoff_location']) ?></div>
                                </div>
                            </div>
                            <?php if ($request['distance']): ?>
                                <div style="margin-top: 10px; font-size: 0.9em; color: #666;">
                                    Distance: <?= number_format($request['distance'], 2) ?> km
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($request['estimated_fare']): ?>
                            <div class="fare-info">
                                <div>Estimated Fare</div>
                                <div class="fare-amount">LKR <?= number_format($request['estimated_fare'], 2) ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($request['notes']): ?>
                            <div style="margin-top: 15px; padding: 10px; background: #fff; border-radius: 5px;">
                                <div class="detail-label">Notes</div>
                                <div style="color: #666; font-size: 0.9em;"><?= htmlspecialchars($request['notes']) ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <div style="margin-top: 15px; font-size: 0.85em; color: #999;">
                            Requested on: <?= date('M d, Y h:i A', strtotime($request['created_at'])) ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-requests">
                    <div class="no-requests-icon">🚗</div>
                    <h2>No Transport Requests Yet</h2>
                    <p>You haven't made any transport requests. Start planning your journey!</p>
                    <a href="tourist_dashboard.php" class="back-btn" style="margin-top: 20px;">Make a Request</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
