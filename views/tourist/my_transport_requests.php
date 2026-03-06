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
$requests = $transportModel->getByUserIdWithDriverDetails($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Transport Requests - Ceylon Go</title>
    <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 80px auto 20px;
            padding: 0;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-header h1 {
            color: #fff;
            font-size: 2em;
            font-weight: 700;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .page-header h1 i {
            margin-right: 10px;
            color: #7c6cf0;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: rgba(255,255,255,0.1);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            font-size: 14px;
        }

        .back-btn:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }
        
        .requests-grid {
            display: grid;
            gap: 24px;
        }
        
        .request-card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 0;
            border: 1px solid rgba(255,255,255,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        
        .request-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            background: rgba(255,255,255,0.03);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        
        .request-id {
            font-weight: 700;
            color: #7c6cf0;
            font-size: 1.1em;
        }
        
        .status-badge {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pending {
            background: rgba(255,193,7,0.15);
            color: #ffc107;
            border: 1px solid rgba(255,193,7,0.3);
        }
        
        .status-confirmed {
            background: rgba(76,175,80,0.15);
            color: #66bb6a;
            border: 1px solid rgba(76,175,80,0.3);
        }
        
        .status-cancelled {
            background: rgba(244,67,54,0.15);
            color: #ef5350;
            border: 1px solid rgba(244,67,54,0.3);
        }
        
        .status-completed {
            background: rgba(33,150,243,0.15);
            color: #42a5f5;
            border: 1px solid rgba(33,150,243,0.3);
        }

        .card-body {
            padding: 24px;
        }

        /* Waiting section */
        .waiting-section {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 20px;
            background: rgba(255,193,7,0.08);
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid rgba(255,193,7,0.15);
        }

        .waiting-spinner {
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255,193,7,0.3);
            border-top: 3px solid #ffc107;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .waiting-text {
            color: #ffc107;
            font-size: 14px;
            font-weight: 500;
        }

        .waiting-text span {
            display: block;
            color: rgba(255,255,255,0.5);
            font-size: 12px;
            margin-top: 2px;
            font-weight: 400;
        }

        /* Driver details section (shown on confirmation) */
        .driver-details {
            background: rgba(76,175,80,0.08);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(76,175,80,0.2);
        }

        .driver-details-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            color: #66bb6a;
            font-weight: 600;
            font-size: 15px;
        }

        .driver-details-header i {
            font-size: 16px;
        }

        .driver-card {
            display: flex;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
        }

        .driver-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(76,175,80,0.4);
        }

        .driver-info {
            flex: 1;
            min-width: 150px;
        }

        .driver-info h4 {
            color: #fff;
            font-size: 16px;
            margin-bottom: 4px;
        }

        .driver-info p {
            color: rgba(255,255,255,0.6);
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .driver-info p i {
            color: #66bb6a;
            font-size: 12px;
        }

        .vehicle-info-card {
            display: flex;
            gap: 12px;
            align-items: center;
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
            padding: 12px 16px;
            margin-top: 12px;
        }

        .vehicle-info-card img {
            width: 80px;
            height: 55px;
            object-fit: cover;
            border-radius: 8px;
        }

        .vehicle-info-card .v-details {
            color: rgba(255,255,255,0.8);
            font-size: 13px;
        }

        .vehicle-info-card .v-details strong {
            color: #fff;
            display: block;
            margin-bottom: 2px;
        }
        
        .request-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .detail-label {
            font-size: 0.8em;
            color: rgba(255,255,255,0.4);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-value {
            color: #fff;
            font-weight: 600;
            font-size: 0.95em;
        }
        
        .route-info {
            background: rgba(255,255,255,0.04);
            padding: 16px;
            border-radius: 12px;
            margin-top: 12px;
        }
        
        .route-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 10px 0;
        }
        
        .route-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            font-size: 13px;
            flex-shrink: 0;
        }
        
        .pickup-icon { background: linear-gradient(135deg, #4caf50, #66bb6a); }
        .dropoff-icon { background: linear-gradient(135deg, #f44336, #ef5350); }

        .route-text .detail-label { font-size: 0.75em; }
        .route-text .detail-value { font-size: 0.9em; }
        
        .fare-info {
            background: linear-gradient(135deg, #7c6cf0, #5a4cd4);
            color: white;
            padding: 16px 20px;
            border-radius: 12px;
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .fare-amount {
            font-size: 1.4em;
            font-weight: 700;
        }

        .fare-label {
            font-size: 0.85em;
            opacity: 0.9;
        }

        .fare-distance {
            font-size: 0.85em;
            opacity: 0.8;
        }

        .card-footer {
            padding: 16px 24px;
            border-top: 1px solid rgba(255,255,255,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .card-footer .timestamp {
            font-size: 0.82em;
            color: rgba(255,255,255,0.35);
        }

        .cancel-btn {
            padding: 8px 18px;
            background: rgba(244,67,54,0.15);
            color: #ef5350;
            border: 1px solid rgba(244,67,54,0.3);
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .cancel-btn:hover {
            background: rgba(244,67,54,0.25);
        }
        
        .no-requests {
            text-align: center;
            padding: 80px 20px;
            color: rgba(255,255,255,0.5);
        }
        
        .no-requests-icon {
            font-size: 4em;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .no-requests h2 {
            color: rgba(255,255,255,0.7);
            margin-bottom: 10px;
        }

        .no-requests p {
            margin-bottom: 20px;
        }

        .no-requests .cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #7c6cf0, #5a4cd4);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .no-requests .cta-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(124,108,240,0.4);
        }

        @media (max-width: 768px) {
            .container { margin-top: 70px; }
            .page-header h1 { font-size: 1.4em; }
            .request-details { grid-template-columns: 1fr 1fr; }
            .driver-card { flex-direction: column; align-items: flex-start; }
            .fare-info { flex-direction: column; gap: 4px; text-align: center; }
        }

        @media (max-width: 480px) {
            .request-details { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1><i class="fa-solid fa-car-side"></i>My Transport Requests</h1>
            <a href="tourist_dashboard.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
        </div>
        
        <div class="requests-grid">
            <?php if ($requests && $requests->num_rows > 0): ?>
                <?php while($request = $requests->fetch_assoc()): ?>
                    <div class="request-card" id="request-card-<?= $request['id'] ?>" data-status="<?= $request['status'] ?>">
                        <!-- Card Top -->
                        <div class="card-top">
                            <span class="request-id"><i class="fa-solid fa-hashtag"></i> BK-<?= $request['id'] ?></span>
                            <span class="status-badge status-<?= $request['status'] ?>" id="status-badge-<?= $request['id'] ?>">
                                <?= ucfirst($request['status']) ?>
                            </span>
                        </div>

                        <div class="card-body">
                            <!-- Waiting Section (shown for pending) -->
                            <?php if ($request['status'] === 'pending' && !empty($request['assigned_driver_id'])): ?>
                            <div class="waiting-section" id="waiting-<?= $request['id'] ?>">
                                <div class="waiting-spinner"></div>
                                <div class="waiting-text">
                                    Waiting for driver confirmation...
                                    <span>Your request has been sent to a driver. You'll be notified once confirmed.</span>
                                </div>
                            </div>
                            <?php elseif ($request['status'] === 'pending' && empty($request['assigned_driver_id'])): ?>
                            <div class="waiting-section" id="waiting-<?= $request['id'] ?>" style="border-color: rgba(255,152,0,0.2); background: rgba(255,152,0,0.08);">
                                <div class="waiting-spinner" style="border-color: rgba(255,152,0,0.3); border-top-color: #ff9800;"></div>
                                <div class="waiting-text" style="color: #ff9800;">
                                    Looking for available drivers...
                                    <span>No drivers available yet. We'll keep searching and assign one soon.</span>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Driver Details Section (shown for confirmed) -->
                            <?php if ($request['status'] === 'confirmed' && !empty($request['driver_name'])): ?>
                            <div class="driver-details" id="driver-details-<?= $request['id'] ?>">
                                <div class="driver-details-header">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Driver Confirmed! Here are your ride details:
                                </div>
                                <div class="driver-card">
                                    <img src="<?= !empty($request['driver_image']) ? '/CeylonGo/public/uploads/transport/' . htmlspecialchars($request['driver_image']) : '/CeylonGo/public/images/profile.jpg' ?>" 
                                         alt="Driver" class="driver-avatar">
                                    <div class="driver-info">
                                        <h4><?= htmlspecialchars($request['driver_name']) ?></h4>
                                        <p><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($request['driver_contact'] ?? 'N/A') ?></p>
                                    </div>
                                </div>
                                <?php if (!empty($request['assigned_vehicle_no'])): ?>
                                <div class="vehicle-info-card">
                                    <?php if (!empty($request['vehicle_image'])): ?>
                                    <img src="/CeylonGo/uploads/<?= htmlspecialchars($request['vehicle_image']) ?>" alt="Vehicle">
                                    <?php endif; ?>
                                    <div class="v-details">
                                        <strong><?= htmlspecialchars($request['assigned_vehicle_no']) ?></strong>
                                        <?= htmlspecialchars($request['vehicle_type_name'] ?? $request['vehicle_type']) ?> • <?= htmlspecialchars($request['v_psg_capacity'] ?? '') ?> passengers
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>

                            <!-- Trip Details -->
                            <div class="request-details">
                                <div class="detail-item">
                                    <span class="detail-label"><i class="fa-regular fa-calendar"></i> Date</span>
                                    <span class="detail-value"><?= date('M d, Y', strtotime($request['date'])) ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label"><i class="fa-regular fa-clock"></i> Pickup Time</span>
                                    <span class="detail-value"><?= date('h:i A', strtotime($request['pickup_time'])) ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label"><i class="fa-solid fa-car"></i> Vehicle Type</span>
                                    <span class="detail-value"><?= htmlspecialchars($request['vehicle_type']) ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label"><i class="fa-solid fa-users"></i> Passengers</span>
                                    <span class="detail-value"><?= $request['num_people'] ?> people</span>
                                </div>
                            </div>
                            
                            <!-- Route Info -->
                            <div class="route-info">
                                <div class="route-item">
                                    <div class="route-icon pickup-icon"><i class="fa-solid fa-location-dot"></i></div>
                                    <div class="route-text">
                                        <div class="detail-label">Pickup</div>
                                        <div class="detail-value"><?= htmlspecialchars($request['pickup_location']) ?></div>
                                    </div>
                                </div>
                                <div class="route-item">
                                    <div class="route-icon dropoff-icon"><i class="fa-solid fa-flag-checkered"></i></div>
                                    <div class="route-text">
                                        <div class="detail-label">Dropoff</div>
                                        <div class="detail-value"><?= htmlspecialchars($request['dropoff_location']) ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($request['estimated_fare']): ?>
                            <div class="fare-info">
                                <div>
                                    <div class="fare-label">Estimated Fare</div>
                                    <div class="fare-amount">LKR <?= number_format($request['estimated_fare'], 2) ?></div>
                                </div>
                                <?php if ($request['distance']): ?>
                                <div class="fare-distance">
                                    <i class="fa-solid fa-road"></i> <?= number_format($request['distance'], 1) ?> km
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($request['notes']): ?>
                            <div style="margin-top: 12px; padding: 12px 16px; background: rgba(255,255,255,0.04); border-radius: 8px;">
                                <div class="detail-label" style="margin-bottom: 4px;"><i class="fa-solid fa-comment-dots"></i> Notes</div>
                                <div style="color: rgba(255,255,255,0.7); font-size: 0.9em;"><?= htmlspecialchars($request['notes']) ?></div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Card Footer -->
                        <div class="card-footer">
                            <div class="timestamp">
                                <i class="fa-regular fa-clock"></i> Requested <?= date('M d, Y h:i A', strtotime($request['created_at'])) ?>
                            </div>
                            <?php if ($request['status'] === 'pending'): ?>
                            <button class="cancel-btn" onclick="cancelRequest(<?= $request['id'] ?>)">
                                <i class="fa-solid fa-xmark"></i> Cancel Request
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-requests">
                    <div class="no-requests-icon">🚗</div>
                    <h2>No Transport Requests Yet</h2>
                    <p>You haven't made any transport requests. Start planning your journey!</p>
                    <a href="tourist_dashboard.php" class="cta-btn"><i class="fa-solid fa-plus"></i> Make a Request</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    // Auto-poll for pending booking status updates
    var pendingCards = document.querySelectorAll('.request-card[data-status="pending"]');
    var pollingIntervals = {};

    pendingCards.forEach(function(card) {
        var requestId = card.id.replace('request-card-', '');
        startPolling(requestId);
    });

    function startPolling(requestId) {
        // Poll every 10 seconds
        pollingIntervals[requestId] = setInterval(function() {
            checkStatus(requestId);
        }, 10000);
    }

    function checkStatus(requestId) {
        fetch('/CeylonGo/public/tourist/transport-status?id=' + requestId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data.success) return;
            
            if (data.status === 'confirmed') {
                // Stop polling
                clearInterval(pollingIntervals[requestId]);
                
                // Update status badge
                var badge = document.getElementById('status-badge-' + requestId);
                if (badge) {
                    badge.className = 'status-badge status-confirmed';
                    badge.textContent = 'Confirmed';
                }

                // Hide waiting section
                var waiting = document.getElementById('waiting-' + requestId);
                if (waiting) waiting.style.display = 'none';

                // Hide cancel button
                var card = document.getElementById('request-card-' + requestId);
                if (card) {
                    card.setAttribute('data-status', 'confirmed');
                    var cancelBtn = card.querySelector('.cancel-btn');
                    if (cancelBtn) cancelBtn.style.display = 'none';
                }

                // Show driver details
                if (data.driver) {
                    var driverHtml = '<div class="driver-details" id="driver-details-' + requestId + '">' +
                        '<div class="driver-details-header">' +
                            '<i class="fa-solid fa-circle-check"></i> Driver Confirmed! Here are your ride details:' +
                        '</div>' +
                        '<div class="driver-card">' +
                            '<img src="' + (data.driver.profileImage || '/CeylonGo/public/images/profile.jpg') + '" alt="Driver" class="driver-avatar">' +
                            '<div class="driver-info">' +
                                '<h4>' + data.driver.name + '</h4>' +
                                '<p><i class="fa-solid fa-phone"></i> ' + (data.driver.contact || 'N/A') + '</p>' +
                            '</div>' +
                        '</div>';

                    if (data.vehicle) {
                        driverHtml += '<div class="vehicle-info-card">';
                        if (data.vehicle.image) {
                            driverHtml += '<img src="' + data.vehicle.image + '" alt="Vehicle">';
                        }
                        driverHtml += '<div class="v-details">' +
                            '<strong>' + data.vehicle.vehicleNo + '</strong>' +
                            (data.vehicle.capacity ? data.vehicle.capacity + ' passengers' : '') +
                        '</div></div>';
                    }

                    driverHtml += '</div>';

                    var cardBody = card.querySelector('.card-body');
                    if (cardBody) {
                        cardBody.insertAdjacentHTML('afterbegin', driverHtml);
                    }
                }

                // Show success notification
                showNotification('🎉 Your driver has confirmed the booking!', 'success');

            } else if (data.status === 'cancelled') {
                clearInterval(pollingIntervals[requestId]);
                location.reload();
            }
        })
        .catch(function() {
            // Silent fail for polling
        });
    }

    function cancelRequest(requestId) {
        if (!confirm('Are you sure you want to cancel this transport request?')) return;

        // Use the existing save endpoint or a cancel endpoint
        fetch('/CeylonGo/controllers/tourist/cancel_transport_request.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ request_id: requestId })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                showNotification('Request cancelled successfully', 'info');
                setTimeout(function() { location.reload(); }, 1000);
            } else {
                alert('Error: ' + (data.message || 'Something went wrong'));
            }
        })
        .catch(function() {
            alert('An error occurred. Please try again.');
        });
    }

    function showNotification(message, type) {
        var notification = document.createElement('div');
        notification.style.cssText = 'position:fixed;top:90px;right:20px;padding:16px 24px;border-radius:12px;color:#fff;font-weight:500;z-index:9999;animation:slideIn 0.3s ease;box-shadow:0 8px 25px rgba(0,0,0,0.3);max-width:350px;';
        
        if (type === 'success') {
            notification.style.background = 'linear-gradient(135deg, #4caf50, #66bb6a)';
        } else {
            notification.style.background = 'linear-gradient(135deg, #42a5f5, #2196f3)';
        }
        
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(function() {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.3s ease';
            setTimeout(function() { notification.remove(); }, 300);
        }, 4000);
    }
    </script>

    <style>
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    </style>
</body>
</html>
