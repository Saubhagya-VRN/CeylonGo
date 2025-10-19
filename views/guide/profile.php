<?php
// views/guide/profile.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ceylon Go - Tour Guide Profile Management</title>
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
                <li><a href="review.php"><i class="fa-regular fa-star"></i> Reviews</a></li>
                <li class="active"><a href="profile.php"><i class="fa-regular fa-user"></i> Manage Profile</a></li>
            </ul>
        </div>

        <div class="main-content">

            <!-- Welcome Section -->
            <div class="welcome">
                <h2>Profile Management</h2>
            </div>

            <!-- Profile Form -->
            <div class="table-container">
                <form class="profile-form">
                    <div class="form-group">
                        <label for="firstName">First Name:</label>
                        <input type="text" id="firstName" name="firstName" value="Priya">
                    </div>
                    
                    <div class="form-group">
                        <label for="lastName">Last Name:</label>
                        <input type="text" id="lastName" name="lastName" value="Silva">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="priya.silva@email.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number:</label>
                        <input type="tel" id="phone" name="phone" value="+94 77 123 4567">
                    </div>
                    
                    <div class="form-group">
                        <label for="specialization">Specialization:</label>
                        <select id="specialization" name="specialization">
                            <option value="cultural">Cultural Heritage</option>
                            <option value="historical">Historical Sites</option>
                            <option value="religious">Religious Sites</option>
                            <option value="nature">Nature & Wildlife</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="languages">Languages Spoken:</label>
                        <input type="text" id="languages" name="languages" value="English, Sinhala, Tamil">
                    </div>
                    
                    <div class="form-group">
                        <label for="experience">Years of Experience:</label>
                        <input type="number" id="experience" name="experience" value="5">
                    </div>
                    
                    <div class="form-group">
                        <label for="bio">Bio:</label>
                        <textarea id="bio" name="bio" rows="4">Experienced tour guide specializing in cultural heritage tours. Passionate about sharing Sri Lanka's rich history and traditions with visitors.</textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="save-btn">Save Changes</button>
                        <button type="button" class="cancel-btn">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer Links -->
    <?php include '../tourist/footer.php'; ?>
</body>
</html>
