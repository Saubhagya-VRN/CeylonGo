<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ceylon Go | Hotel Portal – Booking Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/hotel/style.css">
    <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .amenities-group {
            grid-column: 1 / -1;
        }
        
        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }
        
        .amenity-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background: #f9f9f9;
            transition: all 0.3s ease;
        }
        
        .amenity-item:hover {
            background: #f0f0f0;
            border-color: #2c3e50;
        }
        
        .amenity-item input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.2);
        }
        
        .amenity-item label {
            display: flex;
            align-items: center;
            cursor: pointer;
            margin: 0;
            font-weight: 500;
        }
        
        .amenity-item input[type="checkbox"]:checked + label {
            color: #2c3e50;
            font-weight: 600;
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
            <a href="dashboard.php">Home</a>
            <a href="../tourist/tourist_dashboard.php" class="btn-login">Logout</a>
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
            <a class="nav-link active" href="add_room.php">Booking Management</a>
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
                <h1 class="page-title">Booking Management</h1>
                <div class="hotel-name" id="hotelName">Ocean Breeze Hotel</div>
            </div>
            <div class="right">
                <div class="datetime" id="currentDateTime">--</div>
            </div>
        </header>

        <section class="content">
            <?php
            require_once '../../config/db.php';
            $error = isset($_GET['error']) ? $_GET['error'] : '';
            $success = isset($_GET['success']) ? $_GET['success'] : '';
            ?>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php elseif ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <div class="panel">
                <div class="panel-header">
                    <h2>Add New Room</h2>
                </div>
                <div class="panel-body">
                    <form action="/CeylonGo/public/hotel/add-room" method="POST" enctype="multipart/form-data" class="form-grid">
                        <div class="form-group">
                            <label>Room Number</label>
                            <input type="text" name="room_number" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Room Type</label>
                            <select name="room_type" class="form-control" required>
                                <option value="">Select Room Type</option>
                                <option value="single">Single</option>
                                <option value="double">Double</option>
                                <option value="suite">Suite</option>
                                <option value="deluxe">Deluxe</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Rate Per Night (LKR)</label>
                            <input type="number" name="rate" class="form-control" required min="0" step="0.01">
                        </div>

                        <div class="form-group">
                            <label>Capacity</label>
                            <input type="number" name="capacity" class="form-control" required min="1">
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="available">Available</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="occupied">Occupied</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="form-group amenities-group">
                            <label>Amenities</label>
                            <div class="amenities-grid">
                                <div class="amenity-item">
                                    <input type="checkbox" name="amenities[]" value="wifi" id="wifi">
                                    <label for="wifi">WiFi</label>
                                </div>
                                <div class="amenity-item">
                                    <input type="checkbox" name="amenities[]" value="air_conditioning" id="ac">
                                    <label for="ac">Air Conditioning</label>
                                </div>
                                <div class="amenity-item">
                                    <input type="checkbox" name="amenities[]" value="tv" id="tv">
                                    <label for="tv">TV</label>
                                </div>
                                <div class="amenity-item">
                                    <input type="checkbox" name="amenities[]" value="minibar" id="minibar">
                                    <label for="minibar">Minibar</label>
                                </div>
                                <div class="amenity-item">
                                    <input type="checkbox" name="amenities[]" value="balcony" id="balcony">
                                    <label for="balcony">Balcony</label>
                                </div>
                                <div class="amenity-item">
                                    <input type="checkbox" name="amenities[]" value="safe" id="safe">
                                    <label for="safe">Safe</label>
                                </div>
                                <div class="amenity-item">
                                    <input type="checkbox" name="amenities[]" value="room_service" id="room_service">
                                    <label for="room_service">Room Service</label>
                                </div>
                                <div class="amenity-item">
                                    <input type="checkbox" name="amenities[]" value="parking" id="parking">
                                    <label for="parking">Parking</label>
                                </div>
                            </div>
                        </div>

                        <div class="button-group">
                            <a href="dashboard.php" class="btn btn-primary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Add Room</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <script src="../../public/js/hotel.js"></script>
