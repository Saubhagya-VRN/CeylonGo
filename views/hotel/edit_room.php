<?php
require_once '../../config/db.php';

$room_id = $_GET['id'] ?? null;
$error = $_GET['error'] ?? '';

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
    <title>Ceylon Go | Hotel Portal – Edit Room</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/hotel/style.css">
    <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
    <style>
        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-top: 8px;
        }
        
        .amenity-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .amenity-item input[type="checkbox"] {
            margin: 0;
        }
        
        .amenity-item label {
            margin: 0;
            font-weight: 400;
            cursor: pointer;
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
                <h1 class="page-title">Edit Room</h1>
                <div class="hotel-name" id="hotelName">Ocean Breeze Hotel</div>
            </div>
            <div class="right">
                <div class="datetime" id="currentDateTime">--</div>
            </div>
        </header>

        <section class="content">
            <div class="panel">
                <div class="panel-header">
                    <h2>Edit Room Details</h2>
                </div>
                <div class="panel-body">
                    <?php if ($error): ?>
                        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <form action="/CeylonGo/public/hotel/update-room" method="POST">
                        <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room['id']); ?>">
                        
                        <div class="form-group">
                            <label>Room Number</label>
                            <input type="text" name="room_number" class="form-control" value="<?php echo htmlspecialchars($room['room_number']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Room Type</label>
                            <select name="room_type" class="form-control" required>
                                <option value="single" <?php echo $room['room_type'] === 'single' ? 'selected' : ''; ?>>Single</option>
                                <option value="double" <?php echo $room['room_type'] === 'double' ? 'selected' : ''; ?>>Double</option>
                                <option value="suite" <?php echo $room['room_type'] === 'suite' ? 'selected' : ''; ?>>Suite</option>
                                <option value="deluxe" <?php echo $room['room_type'] === 'deluxe' ? 'selected' : ''; ?>>Deluxe</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Rate Per Night (LKR)</label>
                            <input type="number" name="rate" class="form-control" value="<?php echo htmlspecialchars($room['rate']); ?>" required min="0" step="0.01">
                        </div>

                        <div class="form-group">
                            <label>Capacity</label>
                            <input type="number" name="capacity" class="form-control" value="<?php echo htmlspecialchars($room['capacity']); ?>" required min="1">
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($room['description']); ?></textarea>
                        </div>

                        <div class="form-group amenities-group">
                            <label>Amenities</label>
                            <?php 
                            $current_amenities = json_decode($room['amenities'], true) ?? [];
                            ?>
                            <div class="amenities-grid">
                                <div class="amenity-item">
                                    <input type="checkbox" name="amenities[]" value="wifi" id="wifi" <?php echo in_array('wifi', $current_amenities) ? 'checked' : ''; ?>>
                                    <label for="wifi">WiFi</label>
                                </div>
                                <div class="amenity-item">
                                    <input type="checkbox" name="amenities[]" value="air_conditioning" id="ac" <?php echo in_array('air_conditioning', $current_amenities) ? 'checked' : ''; ?>>
                                    <label for="ac">Air Conditioning</label>
                                </div>
                                <div class="amenity-item">
                                    <input type="checkbox" name="amenities[]" value="tv" id="tv" <?php echo in_array('tv', $current_amenities) ? 'checked' : ''; ?>>
                                    <label for="tv">TV</label>
                                </div>
                                <div class="amenity-item">
                                    <input type="checkbox" name="amenities[]" value="minibar" id="minibar" <?php echo in_array('minibar', $current_amenities) ? 'checked' : ''; ?>>
                                    <label for="minibar">Minibar</label>
                                </div>
                                <div class="amenity-item">
                                    <input type="checkbox" name="amenities[]" value="balcony" id="balcony" <?php echo in_array('balcony', $current_amenities) ? 'checked' : ''; ?>>
                                    <label for="balcony">Balcony</label>
                                </div>
                                <div class="amenity-item">
                                    <input type="checkbox" name="amenities[]" value="safe" id="safe" <?php echo in_array('safe', $current_amenities) ? 'checked' : ''; ?>>
                                    <label for="safe">Safe</label>
                                </div>
                                <div class="amenity-item">
                                    <input type="checkbox" name="amenities[]" value="room_service" id="room_service" <?php echo in_array('room_service', $current_amenities) ? 'checked' : ''; ?>>
                                    <label for="room_service">Room Service</label>
                                </div>
                                <div class="amenity-item">
                                    <input type="checkbox" name="amenities[]" value="parking" id="parking" <?php echo in_array('parking', $current_amenities) ? 'checked' : ''; ?>>
                                    <label for="parking">Parking</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="available" <?php echo $room['status'] === 'available' ? 'selected' : ''; ?>>Available</option>
                                <option value="occupied" <?php echo $room['status'] === 'occupied' ? 'selected' : ''; ?>>Occupied</option>
                                <option value="maintenance" <?php echo $room['status'] === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                            </select>
                        </div>

                        <div class="form-actions">
                            <a href="/CeylonGo/public/hotel/rooms" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <script src="../../public/js/hotel.js"></script>
</body>
</html>
