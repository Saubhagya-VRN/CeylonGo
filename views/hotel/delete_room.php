<?php
require_once '../../config/db.php';

$room_id = $_GET['id'] ?? null;
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';

if ($room_id) {
    $sql = "SELECT * FROM hotel_rooms WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();
}

if (!$room) {
    header("Location: rooms.php?error=Room not found");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ceylon Go | Hotel Portal – Delete Room</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/hotel/style.css">
    <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
</head>
<body>
    <header class="navbar">
        <div class="branding">
            <img src="../../public/images/logo.png" alt="Ceylon Go Logo" class="logo-img">
            <div class="logo-text">Ceylon Go</div>
        </div>
        <nav class="nav-links">
            <a href="dashboard.php">Home</a>
            <a href="tourist_dashboard.php" class="btn-login">Logout</a>
        </nav>
    </header>

    <aside class="sidebar">
        <div class="brand">
            <div class="brand-text">Ceylon Go</div>
        </div>
        <nav class="nav">
            <a class="nav-link" href="dashboard.php">Dashboard</a>
            <a class="nav-link" href="availability.php">Availability</a>
            <a class="nav-link" href="bookings.php">Bookings</a>
            <a class="nav-link" href="add_room.php">Booking Management</a>
            <a class="nav-link" href="payments.php">Payments</a>
            <a class="nav-link" href="reviews.php">Reviews</a>
            <a class="nav-link" href="inquiries.php">Inquiries</a>
            <a class="nav-link" href="report_issue.php">Report Issue</a>
            <a class="nav-link" href="notifications.php">Notifications</a>
        </nav>
    </aside>

    <div class="main">
        <header class="topbar">
            <div class="left">
                <h1 class="page-title">Delete Room</h1>
                <div class="hotel-name" id="hotelName">Ocean Breeze Hotel</div>
            </div>
            <div class="right">
                <div class="datetime" id="currentDateTime">--</div>
            </div>
        </header>

        <section class="content">
            <div class="panel">
                <div class="panel-header">
                    <h2>Confirm Room Deletion</h2>
                </div>
                <div class="panel-body">
                    <?php if ($error): ?>
                        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <div class="room-details">
                        <h3>Room Information</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Room Number:</label>
                                <span><?php echo htmlspecialchars($room['room_number']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Room Type:</label>
                                <span><?php echo htmlspecialchars($room['room_type']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Rate:</label>
                                <span>LKR <?php echo htmlspecialchars($room['rate']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Capacity:</label>
                                <span><?php echo htmlspecialchars($room['capacity']); ?> guests</span>
                            </div>
                            <div class="detail-item">
                                <label>Status:</label>
                                <span class="status-badge status-<?php echo strtolower($room['status']); ?>">
                                    <?php echo htmlspecialchars($room['status']); ?>
                                </span>
                            </div>
                            <?php if (!empty($room['description'])): ?>
                            <div class="detail-item full-width">
                                <label>Description:</label>
                                <span><?php echo htmlspecialchars($room['description']); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php 
                            $amenities = json_decode($room['amenities'], true);
                            if (!empty($amenities)): 
                            ?>
                            <div class="detail-item full-width">
                                <label>Amenities:</label>
                                <span>
                                    <?php 
                                    $amenity_names = array_map(function($amenity) {
                                        return ucwords(str_replace('_', ' ', $amenity));
                                    }, $amenities);
                                    echo htmlspecialchars(implode(', ', $amenity_names));
                                    ?>
                                </span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="warning-message">
                        <strong>⚠️ Warning:</strong> This action cannot be undone. Are you sure you want to delete this room?
                    </div>

                    <div class="form-actions">
                        <a href="/CeylonGo/public/hotel/rooms" class="btn btn-secondary">Cancel</a>
                        <a href="/CeylonGo/public/hotel/delete-room/<?php echo htmlspecialchars($room['id']); ?>" class="btn btn-danger" onclick="return confirm('Are you absolutely sure you want to delete this room? This action cannot be undone.');">
                            🗑 Delete Room
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="../../public/js/hotel.js"></script>
    <style>
        .room-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .room-details h3 {
            margin: 0 0 15px 0;
            color: var(--text-color);
        }
        
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 12px;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .detail-item.full-width {
            grid-column: 1 / -1;
        }
        
        .detail-item label {
            font-weight: 600;
            color: var(--text-color);
            font-size: 14px;
        }
        
        .detail-item span {
            color: var(--text-secondary);
        }
        
        .warning-message {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .success-message {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }
    </style>
</body>
</html>
