<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ceylon Go - Transport Provider Cancelled Bookings</title>
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/base.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/navbar.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/sidebar.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/footer.css">
    
    <!-- Component styles -->
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/cards.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/buttons.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/forms.css">
    
    <!-- Page-specific styles -->
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/timeline.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/tables.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/profile.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/reviews.css">
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/charts.css">

    <!-- Responsive styles (always last) -->
    <link rel="stylesheet" href="/Ceylon_Go/public/css/transport/responsive.css">  

   <link rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body> 

    <!-- Navbar -->
    <header class="navbar">
        <div class="branding">
            <img src="/Ceylon_Go/public/images/logo.png" class="logo-img" alt="Logo">
            <div class="logo-text">Ceylon Go</div>
        </div>
        <nav class="nav-links">
            <a href="#">Home</a>
            <a href="#">Logout</a>
            <img src="/Ceylon_Go/public/images/profile.jpeg" alt="User" class="profile-pic">
        </nav>
    </header>

    <div class="page-wrapper">

        <!-- Sidebar -->
        <div class="sidebar">
            <ul>
                <li><a href="dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
                <li><a href="upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Bookings</a></li>
                <li><a href="pending"><i class="fa-regular fa-clock"></i> Pending Bookings</a></li>
                <li><a href="cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Bookings</a></li>
                <li><a href="review"><i class="fa-regular fa-star"></i> Reviews</a></li>
                <li><a href="profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
                <li class="active"><a href="payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>

            </ul>
        </div>

        <div class="main-content">

            <!-- Welcome Section -->
            <div class="welcome">
                <h2>My Payments</h2>
            </div>

            <!-- Bookings Table -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Booking No</th>
                            <th>Date</th>
                            <th>Payment Amout</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>#12345</td>
                            <td>2025-03-15</td>
                            <td>Rs.10000</td>
                        </tr>

                        <tr>
                            <td>#77889</td>
                            <td>2025-08-19</td>
                            <td>Rs.15000</td>
                        </tr>
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
