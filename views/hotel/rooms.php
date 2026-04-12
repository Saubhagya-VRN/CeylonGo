<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ceylon Go | Hotel Portal – Room Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/CeylonGo/public/css/hotel/style.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/tourist/navbar.css">
    <style>
        body {
            font-family: 'Inter', 'Poppins', sans-serif;
            background: #f5f7f5;
        }

        .navbar {
            background: linear-gradient(90deg, #1f4b2a 0%, #2c5530 100%);
        }

        .branding {
            gap: 0.75rem;
        }

        .logo-text {
            color: #fff;
        }

        .main {
            background: #f5f7f5;
        }

        .content {
            padding: 28px 30px 40px;
        }

        .profile-actions {
            display: flex;
            align-items: center;
            justify-content: end;
            gap: 16px;
            margin-bottom: 18px;
            padding: 4px 0 18px;
            border-bottom: 1px solid #e5ebe5;
        }

        .profile-actions .muted {
            color: #5f6f5f;
            font-weight: 500;
        }

        .panel {
            border: 1px solid #e7ece7;
        }

        .panel-header {
            background: #f7f9f7;
        }

        .panel-body {
            padding: 0;
        }

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

        .empty-state {
            padding: 28px;
            text-align: center;
        }

        .empty-state p {
            margin: 0 0 18px;
            color: #5f6f5f;
            font-size: 15px;
        }

        .empty-state .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .profile-actions .btn,
        .profile-actions .btn-primary,
        .empty-state .btn-primary {
            text-decoration: none;
        }

        .room-add-btn {
            border: 0;
            cursor: pointer;
        }

        .room-add-btn:focus-visible {
            outline: 3px solid rgba(44, 85, 48, 0.25);
            outline-offset: 2px;
        }

        .room-notice {
            margin-bottom: 18px;
            padding: 12px 16px;
            border-radius: 10px;
            font-weight: 500;
        }

        .room-notice.success {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .room-notice.error {
            background: #fdecea;
            color: #b71c1c;
        }

        .table-wrap {
            overflow-x: auto;
            border-top: 1px solid #edf1ed;
        }

        .table {
            min-width: 1100px;
        }

        .table thead th {
            background: #f4f7f4;
            color: #2c5530;
            white-space: nowrap;
        }

        .table tbody tr:hover {
            background: #f8fbf8;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
            white-space: nowrap;
        }

        .status-badge.status-available {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-badge.status-booked {
            background: #fff4e5;
            color: #b26a00;
        }

        .status-badge.status-maintenance {
            background: #fdecea;
            color: #c62828;
        }

        .btn-sm {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 12px;
            margin-right: 6px;
            margin-bottom: 6px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            line-height: 1;
        }

        .btn-secondary {
            background: #eef3ef;
            color: #2c5530;
            border: 1px solid #d9e4da;
        }

        .btn-secondary:hover {
            background: #e3ede4;
        }

        .btn-danger {
            background: #fbe9e7;
            color: #b71c1c;
            border: 1px solid #f3c8c2;
        }

        .btn-danger:hover {
            background: #f8d8d4;
        }

        .success-message,
        .error-message {
            margin-bottom: 18px;
            padding: 12px 16px;
            border-radius: 10px;
            font-weight: 500;
        }

        .success-message {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .error-message {
            background: #fdecea;
            color: #b71c1c;
        }

        @media (max-width: 768px) {
            .content {
                padding: 20px 16px 32px;
            }

            .profile-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .profile-actions .muted {
                margin-right: 0 !important;
            }
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="branding">
            <img src="/CeylonGo/public/images/logo.png" alt="Ceylon Go Logo" class="logo-img">
            <div class="logo-text">Ceylon Go</div>
        </div>
        <nav class="nav-links">
            <a href="/CeylonGo/public/hotel/dashboard">Home</a>
            <a href="/CeylonGo/public/logout" class="btn-login">Logout</a>
        </nav>
    </header>

    <?php $active_page = 'room-management'; include(__DIR__ . '/components/hotel_sidebar.php'); ?>

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
                <button type="button" class="btn btn-primary room-add-btn" data-room-modal-open>
                    Add New Room
                </button>
            </div>

            <?php if (!empty($_GET['success'])): ?>
                <div class="room-notice success"><?php echo htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?>

            <?php if (!empty($_GET['error'])): ?>
                <div class="room-notice error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <div class="panel">
                <div class="panel-header">
                    <h2>Available Rooms</h2>
                </div>
                <div class="panel-body">
                    <!-- Display any error -->
                    <!-- <?php if ($error): ?>
                        <div class="error-message">
                            <!-- <?php echo htmlspecialchars($error); ?> -->
                        </div>
                    <?php endif; ?> -->

                    <!-- If no rooms found -->
                    <?php if (empty($rooms)): ?>
                        <div class="empty-state">
                            <p>No rooms found for this hotel.</p>
                            <button type="button" class="btn btn-primary room-add-btn" data-room-modal-open>Add Your First Room</button>
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

    <?php include(__DIR__ . '/components/add_room_modal.php'); ?>

    <script src="/CeylonGo/public/js/hotel.js"></script>
</body>
</html>
