<?php
// Profile page with database connection
require_once "../config/config.php";
require_once "../core/Database.php";
require_once "../models/User.php";
require_once "../models/License.php";
require_once "../models/Vehicle.php";

$message = null;
$error = null;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if(isset($_SESSION['transporter_id'])){
  $user_id = $_SESSION['transporter_id'];
}
else{
  header('Location: /CeylonGo/views/transport/login.php');
  exit();
}

// Handle all POST operations
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    try {
        $db = Database::getConnection();
        $vehicleModel = new Vehicle($db);
        
        if ($_POST['action'] == 'edit_vehicle') {
            // Handle vehicle edit
            $old_vehicle_no = $_POST['vehicle_no_old'];
            $new_vehicle_no = $_POST['vehicle_no'];
            $vehicle_type = $_POST['vehicle_type'];
            $psg_capacity = $_POST['psg_capacity'];
            
            // Validate passenger capacity
            if ($psg_capacity < 1) {
                $error = "Passenger capacity must be at least 1.";
            } else {
                // Handle file upload
                $image = '';
if (isset($_FILES['vehicle_image']) && $_FILES['vehicle_image']['error'] == 0) {
$uploadDir = dirname(__DIR__, 2) . "/uploads/";

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileInfo = pathinfo($_FILES['vehicle_image']['name']);
    $extension = $fileInfo['extension'];
    $newFileName = uniqid('img_', true) . '.' . $extension;
    $targetPath = $uploadDir . $newFileName;

    if (move_uploaded_file($_FILES['vehicle_image']['tmp_name'], $targetPath)) {
        $image = $newFileName;
    } else {
        echo "<script>alert('Failed to move uploaded file');</script>";
    }
}

                
                // Update vehicle
                $vehicleModel->vehicle_no = $new_vehicle_no;
                $vehicleModel->user_id = trim($user_id);
                $vehicleModel->vehicle_type = $vehicle_type;
                $vehicleModel->psg_capacity = $psg_capacity;
                if ($image) {
                    $vehicleModel->image = $image;
                }
                
                if ($vehicleModel->updateVehicle($old_vehicle_no)) {
                    $message = "Vehicle updated successfully!";
                } else {
                    $error = "Failed to update vehicle.";
                }
            }
            
        } elseif ($_POST['action'] == 'update_profile') {
            // Handle profile update
            $userModel = new User($db);
            $userModel->user_id = $user_id;
            $userModel->full_name = $_POST['full_name'];
            $userModel->dob = $_POST['dob'];
            $userModel->address = $_POST['address'];
            $userModel->contact_no = $_POST['contact_no'];
            $userModel->email = $_POST['email'];
            
            if ($userModel->updateUser()) {
                $message = "Profile updated successfully!";
            } else {
                $error = "Failed to update profile.";
            }
            
        } elseif ($_POST['action'] == 'update_license') {
            // Handle license update
            $licenseModel = new License($db);
            $licenseModel->driver_id = $user_id;
            $licenseModel->license_no = $_POST['license_no'];
            $licenseModel->license_exp_date = $_POST['license_exp_date'];
            $licenseModel->image = ''; // Empty image for now
            
            if ($licenseModel->updateLicense()) {
                $message = "License information updated successfully!";
            } else {
                $error = "Failed to update license information.";
            }
            
        } elseif ($_POST['action'] == 'delete_vehicle') {
            // Handle vehicle delete
            $vehicle_no = $_POST['vehicle_no'];
            
            if ($vehicleModel->deleteVehicle($vehicle_no, trim($user_id))) {
                $message = "Vehicle deleted successfully!";
            } else {
                $error = "Failed to delete vehicle.";
            }
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

try {
    // Get database connection
    $db = Database::getConnection();
    
    // Debug: Uncomment to see what user_id we're working with
    // echo "<pre>Session user_id: [" . $user_id . "]</pre>";
    // echo "<pre>Session data: " . print_r($_SESSION, true) . "</pre>";
    
    // Trim user_id to remove any leading/trailing spaces
    $user_id = trim($user_id);
    
    // Fetch user data
    $userModel = new User($db);
    $user = $userModel->getUserById($user_id);
    
    // Debug: Uncomment to see fetched data
    // echo "<pre>User data: " . print_r($user, true) . "</pre>";
    
    // Fetch license data
    $licenseModel = new License($db);
    $license = $licenseModel->getLicenseByDriverId($user_id);
    
    // Debug: Uncomment to see license data
    // echo "<pre>License data: " . print_r($license, true) . "</pre>";
    
    // // Fetch vehicles data
    $vehicleModel = new Vehicle($db);
    $vehicles = $vehicleModel->getVehiclesByUser($user_id);
    // // var_dump($vehicles);
    
    // Debug: Uncomment to see vehicles data
    // echo "<pre>Vehicles data: " . print_r($vehicles, true) . "</pre>";
    // die(); // Stop execution to see debug output
    
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Transport Provider Dashboard</title>
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/base.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/navbar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/sidebar.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/footer.css">
    
    <!-- Component styles -->
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/cards.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/buttons.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/forms.css">
    
    <!-- Page-specific styles -->
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/timeline.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/tables.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/profile.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/reviews.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/charts.css">
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/vehicle.css">

    <!-- Responsive styles (always last) -->
    <link rel="stylesheet" href="/CeylonGo/public/css/transport/responsive.css">   
    
 
    <link rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Inline responsive fix for vehicle cards -->
    <style>
      @media (max-width: 600px) {
        .vehicle-cards {
          padding: 0 10px !important;
        }
        .vehicle-card {
          flex: 0 1 100% !important;
          max-width: 340px !important;
          margin: 0 auto 20px auto !important;
        }
        .vehicle-card .vehicle-image img {
          height: 180px !important;
        }
        .vehicle-card .vehicle-info {
          padding: 12px !important;
        }
        .vehicle-card .vehicle-info h3 {
          font-size: 18px !important;
        }
        .vehicle-card .vehicle-info p {
          font-size: 13px !important;
        }
      }
    </style>
    
</head>
<body>

  <!-- Navbar -->
  <header class="navbar">
    <div class="branding">
      <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle menu">
        <span></span>
        <span></span>
        <span></span>
      </button>
      <img src="/CeylonGo/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="/CeylonGo/public/transporter/dashboard">Home</a>
      <a href="/CeylonGo/public/logout">Logout</a>
      <img src="<?php echo htmlspecialchars($profile_picture ?? '/CeylonGo/public/images/profile.jpg'); ?>" alt="User" class="profile-pic">
    </nav>
  </header>

  <!-- Sidebar Overlay for Mobile -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="page-wrapper">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
      <ul>
        <li><a href="/CeylonGo/public/transporter/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="/CeylonGo/public/transporter/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/pending"><i class="fa-regular fa-clock"></i> Pending Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li class="active"><a href="/CeylonGo/public/transporter/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/transporter/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </div>
  
    <!-- Main Content -->
    <div class="main-content">
      <div class="container">

        <!-- Success/Error Messages -->
        <?php if ($message): ?>
            <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0;">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px 0;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Profile Header -->
        <div class="profile-header">
          <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="profile-pic">
          <div>
            <h2><?= $user['full_name'] ?? 'N/A' ?></h2>
            <p>Driver ID: <?= $user['user_id'] ?? 'N/A' ?></p>
            <span class="status active">● Active</span>
          </div>
        </div>

        <!-- Personal Information -->
        <div class="section">
          <h3>Personal Information</h3>
          <form method="POST" action="">
            <input type="hidden" name="action" value="update_profile">
            <div class="form-group">
              <label>Full Name</label>
              <input type="text" name="full_name" value="<?= $user['full_name'] ?? '' ?>" required>
            </div>
            <div class="form-group">
              <label>Date of Birth</label>
              <input type="date" name="dob" value="<?= $user['dob'] ?? '' ?>" required>
            </div>
            <div class="form-group full-width">
              <label>Home Address</label>
              <input type="text" name="address" value="<?= $user['address'] ?? '' ?>" required>
            </div>
            <div class="form-group">
              <label>Contact Number</label>
              <input type="text" name="contact_no" value="<?= $user['contact_no'] ?? '' ?>" required>
            </div>
            <div class="form-group">
              <label>Email Address</label>
              <input type="email" name="email" value="<?= $user['email'] ?? '' ?>" required>
            </div>
            <div class="actions">
              <button type="submit" class="save">Save Changes</button>
            </div>
          </form>
        </div>



        <!-- Driving License -->
        <div class="section">
          <h3>Driving License</h3>
          
          <!-- Current License Details Display -->
          <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd;">
            <h4 style="margin-bottom: 10px; color: #333; font-size: 16px;">Current License Information</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
              <div>
                <strong style="color: #666; font-size: 13px;">License Number:</strong>
                <p style="margin: 5px 0 0 0; font-size: 15px; color: #333;">
                  <?= !empty($license['license_no']) ? $license['license_no'] : '<em style="color: #999;">Not set</em>' ?>
                </p>
              </div>
              <div>
                <strong style="color: #666; font-size: 13px;">Expiry Date:</strong>
                <p style="margin: 5px 0 0 0; font-size: 15px; color: #333;">
                  <?= !empty($license['license_exp_date']) ? date('F d, Y', strtotime($license['license_exp_date'])) : '<em style="color: #999;">Not set</em>' ?>
                </p>
              </div>
            </div>
          </div>

          <!-- Update License Form -->
          <h4 style="margin-bottom: 15px; color: #333; font-size: 16px;">Update License Information</h4>
          <form method="POST" action="">
            <input type="hidden" name="action" value="update_license">
            <div class="form-group">
              <label>License Number</label>
              <input type="text" name="license_no" value="<?= $license['license_no'] ?? '' ?>" required>
            </div>
            <div class="form-group">
              <label>Expiry Date</label>
              <input type="date" name="license_exp_date" value="<?= $license['license_exp_date'] ?? '' ?>" required>
            </div>
            <div class="actions">
              <button type="submit" class="save">Update License</button>
            </div>
          </form>
          <!-- <div class="form-group full-width upload-box">
            <?php if(isset($license['image']) && !empty($license['image'])): ?>
              <p>Current License: <a href="/CeylonGo/uploads/<?= $license['image'] ?>" target="_blank">View License</a></p>
            <?php else: ?>
              <p>Click to upload or drag and drop<br>PNG, JPG or PDF (max 5MB)</p>
            <?php endif; ?>
          </div>
        </div> -->

        <!-- Vehicle Information -->
        <div class="section">
          <h3>Vehicle Information</h3>
          
          <button class="add-vehicle">
           <a href="vehicle" class="add-vehicle">
            <i class="fa-solid fa-plus"></i> Add Vehicle
           </a>
          </button>
        </div>

        <!-- My Vehicles Section -->
        <div class="section">
          <h3>My Vehicles</h3>
          <div class="vehicle-cards">
            <?php if(isset($vehicles) && !empty($vehicles)): ?>
              <?php foreach($vehicles as $v): ?>
                <div class="vehicle-card">
                  <div class="vehicle-image">
                    <?php if(isset($v['image']) && !empty($v['image'])): ?>
                      <img src="/CeylonGo/uploads/<?= $v['image'] ?>" alt="<?= $v['vehicle_no'] ?>">
                    <?php else: ?>
                      <img src="/CeylonGo/public/images/logo.png" alt="No Image">
                    <?php endif; ?>
                  </div>
                  <div class="vehicle-info">
                    <h3>
                      <?= 
                          $v['vehicle_type'] == '1' ? 'TUK' :
                          ($v['vehicle_type'] == '2' ? 'VAN' :
                          ($v['vehicle_type'] == '3' ? 'CAR' :
                          ($v['vehicle_type'] == '4' ? 'BUS' : $v['vehicle_type'])))
                        ?>                    </h3>
                    <p><strong>License Plate:</strong> <?= $v['vehicle_no'] ?? 'N/A' ?></p>
                    <p><strong>Passenger Capacity:</strong> <?= $v['psg_capacity'] ?? 'N/A' ?></p>
                  </div>
                  <div class="vehicle-actions">
                    <button class="edit-btn" onclick="openEditModal('<?= $v['vehicle_no'] ?>', '<?= $v['vehicle_type'] ?>', '<?= $v['psg_capacity'] ?>', '<?= $v['image'] ?? '' ?>')">
                      <i class="fa-solid fa-edit"></i> Edit
                    </button>
                    <button class="delete-btn" onclick="deleteVehicle('<?= $v['vehicle_no'] ?>')">
                      <i class="fa-solid fa-trash"></i> Delete
                    </button>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="no-vehicles">
                <p>No vehicles added yet. <a href="vehicle">Add your first vehicle</a></p>
              </div>
            <?php endif; ?>
          </div>
        </div>


      </div>
    </div>
  </div>

    <!-- Edit Vehicle Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Edit Vehicle</h3>
        <span class="close" onclick="closeEditModal()">&times;</span>
      </div>
      <form id="editVehicleForm" method="POST" enctype="multipart/form-data">
        <input type="hidden" id="edit_vehicle_no_old" name="vehicle_no_old">
        <input type="hidden" name="action" value="edit_vehicle">
        
        <div class="form-group">
          <label>Vehicle Number</label>
          <input type="text" id="edit_vehicle_no" name="vehicle_no" required>
        </div>
        
        <div class="form-group">
          <label>Vehicle Type</label>
          <select id="edit_vehicle_type" name="vehicle_type" required>
            <option value="1">TUK</option>
            <option value="2">VAN</option>
            <option value="3">CAR</option>
            <option value="4">BUS</option>
          </select>
        </div>
        
        <div class="form-group">
          <label>Passenger Capacity</label>
          <input type="number" id="edit_psg_capacity" name="psg_capacity" min="1" value="1" required>
        </div>
        
        <div class="form-group">
          <label>Vehicle Image</label>
          <input type="file" id="edit_vehicle_image" name="vehicle_image" accept="image/*">
          <div id="current_image_preview"></div>
        </div>
        
        <div class="modal-actions">
          <button type="button" class="cancel-btn" onclick="closeEditModal()">Cancel</button>
          <button type="submit" class="save-btn">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
  
  <script>
    // Open edit modal
    function openEditModal(vehicleNo, vehicleType, psgCapacity, currentImage) {
      document.getElementById('edit_vehicle_no_old').value = vehicleNo;
      document.getElementById('edit_vehicle_no').value = vehicleNo;
      document.getElementById('edit_vehicle_type').value = vehicleType;
      document.getElementById('edit_psg_capacity').value = psgCapacity;
      
      // Show current image if exists
      const preview = document.getElementById('current_image_preview');
      if (currentImage) {
        preview.innerHTML = `<p>Current Image: <img src="/CeylonGo/uploads/${currentImage}" style="max-width: 100px; max-height: 100px;"></p>`;
      } else {
        preview.innerHTML = '<p>No current image</p>';
      }
      
      document.getElementById('editModal').style.display = 'block';
    }
    
    // Close edit modal
    function closeEditModal() {
      document.getElementById('editModal').style.display = 'none';
    }
    
    // Delete vehicle
    function deleteVehicle(vehicleNo) {
      if (confirm('Are you sure you want to delete this vehicle?')) {
        // Create a form to submit delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
          <input type="hidden" name="action" value="delete_vehicle">
          <input type="hidden" name="vehicle_no" value="${vehicleNo}">
        `;
        document.body.appendChild(form);
        form.submit();
      }
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
      const modal = document.getElementById('editModal');
      if (event.target == modal) {
        closeEditModal();
      }
    }
  </script>

    <!-- Footer -->
  <footer>
    <ul>
      <li><a href="#">About Us</a></li>
      <li><a href="#">Contact Us</a></li>
    </ul>
  </footer>

  <!-- Hamburger Menu Toggle Script -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const hamburgerBtn = document.getElementById('hamburgerBtn');
      const sidebar = document.getElementById('sidebar');
      const sidebarOverlay = document.getElementById('sidebarOverlay');
      
      function toggleSidebar() {
        hamburgerBtn.classList.toggle('active');
        sidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
      }
      
      function closeSidebar() {
        hamburgerBtn.classList.remove('active');
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
      }
      
      if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', toggleSidebar);
      }
      
      if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
      }
      
      const sidebarLinks = document.querySelectorAll('.sidebar ul li a');
      sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
          if (window.innerWidth <= 768) {
            closeSidebar();
          }
        });
      });
      
      window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
          closeSidebar();
        }
      });
    });
  </script>

</body>
</html>