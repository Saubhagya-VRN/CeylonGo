<?php
if (!file_exists('../../config/db.php')) {
    die("Database configuration file not found");
}

require_once '../../config/db.php';

// Initialize variables
$rooms = [];
$error = null;

// Verify database connection
if (!isset($conn) || $conn->connect_error) {
    $error = "Database connection failed";
} else {
    try {
        // ✅ Define your hotel ID manually or fetch it dynamically
        // Replace this with the actual hotel ID variable you have
        $hotel_id = 1; // Example fixed ID (change if needed)

        // ✅ Prepare the SQL query with a placeholder
        $sql = "SELECT * FROM hotel_rooms WHERE hotel_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        // ✅ Bind the parameter (integer type)
        $stmt->bind_param("i", $hotel_id);

        // ✅ Execute and fetch results
        $stmt->execute();
        $result = $stmt->get_result();
        $rooms = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();
    } catch (Exception $e) {
        $error = "Error fetching rooms: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ceylon Go | Hotel Portal – Room Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/hotel/style.css">
    <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
    <style>
        .text-muted {
            color: #6c757d;
            font-style: italic;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .table th:nth-child(5),
        .table th:nth-child(6) {
            min-width: 150px;
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="branding">
            <img src="../../public/images/logo.png" alt="Ceylon Go Logo" class="logo-img">
            <div class="logo-text">Ceylon Go</div>
        </div>
        <nav class="nav-links">
            <a href="/CeylonGo/public/hotel/dashboard">Home</a>
            <a href="/CeylonGo/public/logout" class="btn-login">Logout</a>
        </nav>
    </header>

    <aside class="sidebar">
        <div class="brand">
            <div class="brand-text">Ceylon Go</div>
        </div>
        <nav class="nav">
            <a class="nav-link" href="/CeylonGo/public/hotel/dashboard">Dashboard</a>
            <a class="nav-link" href="/CeylonGo/public/hotel/availability">Availability</a>
            <a class="nav-link" href="/CeylonGo/public/hotel/bookings">Bookings</a>
            <a class="nav-link" href="/CeylonGo/public/hotel/add-room">Booking Management</a>
            <a class="nav-link" href="/CeylonGo/public/hotel/payments">Payments</a>
            <a class="nav-link" href="/CeylonGo/public/hotel/reviews">Reviews</a>
            <a class="nav-link" href="/CeylonGo/public/hotel/inquiries">Inquiries</a>
            <a class="nav-link" href="/CeylonGo/public/hotel/report-issue">Report Issue</a>
            <a class="nav-link" href="/CeylonGo/public/hotel/notifications">Notifications</a>
        </nav>
    </aside>

    <div class="main">
        <header class="topbar">
            <div class="left">
                <h1 class="page-title">Room Management</h1>
                <div class="hotel-name" id="hotelName">Ocean Breeze Hotel</div>
            </div>
            <div class="right">
                <div class="datetime" id="currentDateTime">--</div>
            </div>
        </header>

        <section class="content">
            <div class="profile-actions">
                <div style="margin-right:auto" class="muted">Manage your hotel rooms</div>
                <a href="/CeylonGo/public/hotel/add-room" class="btn btn-primary">
                    + Add New Room
                </a>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <h2>Available Rooms</h2>
                </div>
                <div class="panel-body">
                    <!-- Display any error -->
                    <?php if ($error): ?>
                        <div class="error-message">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <!-- If no rooms found -->
                    <?php if (empty($rooms)): ?>
                        <div class="empty-state">
                            <p>No rooms found for this hotel.</p>
                            <a href="/CeylonGo/public/hotel/add-room" class="btn btn-primary">Add Your First Room</a>
                        </div>
                    <?php else: ?>
                        <div class="table-wrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Room Number</th>
                                        <th>Type</th>
                                        <th>Rate (Per Night)</th>
                                        <th>Capacity</th>
                                        <th>Description</th>
                                        <th>Amenities</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rooms as $room): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                                            <td><?php echo htmlspecialchars($room['room_type']); ?></td>
                                            <td>LKR <?php echo htmlspecialchars($room['rate']); ?></td>
                                            <td><?php echo htmlspecialchars($room['capacity']); ?></td>
                                            <td>
                                                <?php 
                                                $description = $room['description'];
                                                if (empty($description)) {
                                                    echo '<span class="text-muted">No description</span>';
                                                } else {
                                                    echo htmlspecialchars($description);
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $amenities = json_decode($room['amenities'], true);
                                                if (empty($amenities)) {
                                                    echo '<span class="text-muted">No amenities</span>';
                                                } else {
                                                    $amenity_names = array_map(function($amenity) {
                                                        return ucwords(str_replace('_', ' ', $amenity));
                                                    }, $amenities);
                                                    echo htmlspecialchars(implode(', ', $amenity_names));
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?php echo strtolower($room['status']); ?>">
                                                    <?php echo htmlspecialchars($room['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="/CeylonGo/public/hotel/edit-room/<?php echo $room['id']; ?>" class="btn btn-sm btn-secondary">
                                                    ✏ Edit
                                                </a>
                                                <a href="/CeylonGo/public/hotel/delete-room/<?php echo $room['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this room?');">
                                                    🗑 Delete
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>

    <script src="../../public/js/hotel.js"></script>
</body>
</html>
