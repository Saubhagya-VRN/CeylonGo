<?php
require_once '../../config/db.php';

$guide_id = 1; // Replace with actual logged in guide ID
$sql = "SELECT gb.*, gp.place_name 
        FROM guide_bookings gb 
        LEFT JOIN guide_places gp ON gb.place_id = gp.id 
        WHERE gb.guide_id = ? AND gb.status = 'cancelled'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $guide_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ceylon Go - Tour Guide Cancelled Tours</title>
    <!-- Base styles -->
    <link rel="stylesheet" href="../../public/css/guide/base.css">
    <link rel="stylesheet" href="../../public/css/guide/navbar.css">
    <link rel="stylesheet" href="../../public/css/guide/sidebar.css">
    <link rel="stylesheet" href="../../public/css/guide/footer.css">
    
    <!-- Component styles -->
    <link rel="stylesheet" href="../../public/css/guide/cards.css">
    <link rel="stylesheet" href="../../public/css/guide/buttons.css">
    <link rel="stylesheet" href="../../public/css/guide/forms.css">
    
    <!-- Page-specific styles -->
    <link rel="stylesheet" href="../../public/css/guide/tables.css">
    <link rel="stylesheet" href="../../public/css/guide/profile.css">
    <link rel="stylesheet" href="../../public/css/guide/reviews.css">
    <link rel="stylesheet" href="../../public/css/guide/charts.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body> 

    <!-- Navbar -->
    <header class="navbar">
        <div class="branding">
            <img src="../../public/images/logo.png" class="logo-img" alt="Logo">
            <div class="logo-text">Ceylon Go</div>
        </div>
        <nav class="nav-links">
            <a href="guide_dashboard.php">Home</a>
            <a href="../tourist/tourist_dashboard.php">Logout</a>
            <img src="../../public/images/user.png" alt="User" class="profile-pic">
        </nav>
    </header>

    <div class="page-wrapper">

        <!-- Sidebar -->
        <div class="sidebar">
            <ul>
                <li><a href="guide_dashboard.php"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
                <li><a href="upcoming.php"><i class="fa-regular fa-calendar"></i> Upcoming Tours</a></li>
                <li><a href="pending.php"><i class="fa-regular fa-clock"></i> Pending Requests</a></li>
                <li class="active"><a href="cancelled.php"><i class="fa-solid fa-xmark"></i> Cancelled Tours</a></li>
                <li><a href="review.php"><i class="fa-regular fa-star"></i> Reviews</a></li>
                <li><a href="profile.php"><i class="fa-regular fa-user"></i> Manage Profile</a></li>
            </ul>
        </div>

        <div class="main-content">

            <!-- Welcome Section -->
            <div class="welcome">
                <h2>Cancelled Tours</h2>
            </div>

            <!-- Tours Table -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer Name</th>
                            <th>Place</th>
                            <th>Booking Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#GB<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></td>
                            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['place_name']); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($row['booking_date'])); ?></td>
                            <td><a href="view_booking.php?id=<?php echo $row['id']; ?>">View Details</a></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer Links -->
    <footer>
        <ul>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Contact Us</a></li>
        </ul>
    </footer>
</body>
</html>
