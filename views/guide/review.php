<?php
// views/guide/review.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ceylon Go - Tour Guide Reviews</title>
    <link rel="stylesheet" href="../../public/css/transport/upcoming.css">
    <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
    <link rel="stylesheet" href="../../public/css/tourist/footer.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
      body {
        background-color: #f0f8f0; /* Light greenish background from tourist_dashboard */
      }
    </style>
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
            <a href="#">Logout</a>
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
                <li><a href="cancelled.php"><i class="fa-solid fa-xmark"></i> Cancelled Tours</a></li>
                <li class="active"><a href="review.php"><i class="fa-regular fa-star"></i> Reviews</a></li>
                <li><a href="profile.php"><i class="fa-regular fa-user"></i> Manage Profile</a></li>
            </ul>
        </div>

        <div class="main-content">

            <!-- Welcome Section -->
            <div class="welcome">
                <h2>Customer Reviews</h2>
            </div>

            <!-- Reviews Table -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tour No</th>
                            <th>Customer Name</th>
                            <th>Date</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#TG001</td>
                            <td>Sarah Johnson</td>
                            <td>2025-02-15</td>
                            <td>⭐⭐⭐⭐⭐</td>
                            <td>Excellent tour guide! Very knowledgeable and friendly.</td>
                            <td><a href="#">Reply</a></td>
                        </tr>
                        <tr>
                            <td>#TG002</td>
                            <td>Michael Brown</td>
                            <td>2025-02-10</td>
                            <td>⭐⭐⭐⭐</td>
                            <td>Great experience, would recommend to others.</td>
                            <td><a href="#">Reply</a></td>
                        </tr>
                        <tr>
                            <td>#TG003</td>
                            <td>Emily Davis</td>
                            <td>2025-02-05</td>
                            <td>⭐⭐⭐⭐⭐</td>
                            <td>Amazing tour! Priya was very professional and informative.</td>
                            <td><a href="#">Reply</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer Links -->
    <?php include '../tourist/footer.php'; ?>
</body>
</html>
