<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
    header("Location: /CeylonGo/public/login");
    exit();
}

// Fetch trips for this user
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM trips WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$trips = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Trips - Ceylon Go</title>
    <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #4a6741 0%, #2d5016 100%);
            min-height: 100vh;
        }
        
        .page-header {
            background: linear-gradient(135deg, #4a6741 0%, #2d5016 100%);
            color: white;
            padding: 80px 20px 60px;
            text-align: center;
        }
        
        .page-header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .page-header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        
        .container {
            max-width: 1400px;
            margin: -40px auto 40px;
            padding: 0 20px;
        }
        
        .requests-card {
            background: white;
            border-radius: 15px;
            padding: 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .table-wrapper {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: linear-gradient(135deg, #4a6741 0%, #2d5016 100%);
            color: white;
        }
        
        th {
            padding: 20px;
            text-align: left;
            font-weight: 600;
            font-size: 0.95em;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 20px;
            border-bottom: 1px solid #e8ede8;
            color: #333;
        }
        
        tbody tr {
            transition: background-color 0.2s ease;
        }
        
        tbody tr:hover {
            background-color: #f5f8f5;
        }
        
        tbody tr:last-child td {
            border-bottom: none;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
        
        .no-requests {
            text-align: center;
            padding: 80px 20px;
            color: #666;
        }
        
        .no-requests-icon {
            font-size: 5em;
            margin-bottom: 20px;
            opacity: 0.3;
        }
        
        .no-requests h2 {
            color: #4a6741;
            margin-bottom: 10px;
        }
        
        .back-btn {
            display: inline-block;
            margin: 20px 0;
            padding: 12px 24px;
            background: #4a6741;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s ease;
            font-weight: 500;
        }
        
        .back-btn:hover {
            background: #2d5016;
        }
        
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2em;
            }
            
            th, td {
                padding: 12px 10px;
                font-size: 0.9em;
            }
            
            .status-badge {
                padding: 4px 10px;
                font-size: 0.75em;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="page-header">
        <h1>My Trips</h1>
        <p>Review and manage your planned trips</p>
    </div>
    
    <div class="container">
        <a href="tourist_dashboard.php" class="back-btn">← Back to Dashboard</a>
        
        <div class="requests-card">
            <?php if ($trips->num_rows > 0): ?>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Number of People</th>
                                <th>Start Date</th>
                                <th>Destination</th>
                                <th>Number of Days</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($trip = $trips->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($trip['customer_name']) ?></td>
                                    <td><?= $trip['number_of_people'] ?></td>
                                    <td><?= date('M d, Y', strtotime($trip['start_date'])) ?></td>
                                    <td><?= htmlspecialchars($trip['destination']) ?></td>
                                    <td><?= $trip['number_of_days'] ?> days</td>
                                    <td>
                                        <span class="status-badge status-<?= $trip['status'] ?>">
                                            <?= ucfirst($trip['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($trip['created_at'])) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-requests">
                    <div class="no-requests-icon">✈️</div>
                    <h2>No Trips Planned Yet</h2>
                    <p>Start planning your Sri Lankan adventure!</p>
                    <a href="tourist_dashboard.php" class="back-btn" style="margin-top: 20px;">Plan a Trip</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
