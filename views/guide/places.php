<?php
require_once '../../config/db.php';

// Add new place
if(isset($_POST['add_place'])) {
    $guide_id = 1; // Replace with actual logged in guide ID
    $place_name = $_POST['place_name'];
    $address = $_POST['address'];
    $notes = $_POST['notes'];
    
    $sql = "INSERT INTO guide_places (guide_id, place_name, address, notes) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $guide_id, $place_name, $address, $notes);
    $stmt->execute();
}

// Fetch places
$guide_id = 1; // Replace with actual logged in guide ID
$sql = "SELECT * FROM guide_places WHERE guide_id = ?";
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
    <title>Ceylon Go - Guide Places</title>
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
                <li><a href="cancelled.php"><i class="fa-solid fa-xmark"></i> Cancelled Tours</a></li>
                <li class="active"><a href="places.php"><i class="fa-solid fa-location-dot"></i> Manage Places</a></li>
                <li><a href="profile.php"><i class="fa-regular fa-user"></i> Profile</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="welcome">
                <h2>Manage Guide Places</h2>
                <button onclick="showAddForm()" class="add-btn">Add New Place</button>
            </div>

            <!-- Add Place Form -->
            <div id="addPlaceForm" class="form-container" style="display:none;">
                <form method="POST" action="">
                    <input type="text" name="place_name" placeholder="Place Name" required>
                    <input type="text" name="address" placeholder="Address" required>
                    <textarea name="notes" placeholder="Notes"></textarea>
                    <button type="submit" name="add_place">Add Place</button>
                </form>
            </div>

            <!-- Places Table -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Place Name</th>
                            <th>Address</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['place_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td><?php echo htmlspecialchars($row['notes']); ?></td>
                            <td>
                                <a href="edit_place.php?id=<?php echo $row['id']; ?>" class="edit-btn">Edit</a>
                                <a href="delete_place.php?id=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer>
        <ul>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Contact Us</a></li>
        </ul>
    </footer>

    <script>
    function showAddForm() {
        document.getElementById('addPlaceForm').style.display = 'block';
    }
    </script>
</body>
</html>