<?php
require_once "../config/config.php";
require_once "../core/Database.php";
require_once "../models/Vehicle.php";
require_once "../models/VehicleType.php";
require_once "session_init.php";

$user_id = trim($_SESSION['transporter_id']);
$user_id = trim($_SESSION['transporter_id']);
$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $db = Database::getConnection();

        $vehicle_no = $_POST['vehicle_no'] ?? '';
        $vehicle_type = $_POST['vehicle_type'] ?? '';
        $psg_capacity = $_POST['psg_capacity'] ?? 0;

        // Validate passenger capacity
        $capacity_limits = ['1' => 3, '2' => 4, '3' => 7, '4' => 7, '5' => 20, '6' => 20];
        $capacity_names = ['1' => 'TUK', '2' => 'Car', '3' => 'Minivan', '4' => 'Minivan AC', '5' => 'Bus', '6' => 'Bus AC'];
        if ($psg_capacity < 1) {
            $error = "Passenger capacity must be at least 1.";
        } elseif (isset($capacity_limits[$vehicle_type]) && $psg_capacity > $capacity_limits[$vehicle_type]) {
            $error = "Maximum passenger capacity for " . $capacity_names[$vehicle_type] . " is " . $capacity_limits[$vehicle_type] . ".";
        } else {
            // Handle file upload
            $image = '';
            if (isset($_FILES['vehicle_image']) && $_FILES['vehicle_image']['error'] == 0) {
                $uploadDir = dirname(__DIR__, 2) . '/public/uploads/transport/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileInfo = pathinfo($_FILES['vehicle_image']['name']);
                $extension = $fileInfo['extension'];
                $newFileName = uniqid('img_', true) . '.' . $extension;
                $targetPath = $uploadDir . $newFileName;

                if (move_uploaded_file($_FILES['vehicle_image']['tmp_name'], $targetPath)) {
                    $image = $newFileName;
                }
            }

            // Save vehicle
            $vehicle = new Vehicle($db);
            $vehicle->vehicle_no = $vehicle_no;
            $vehicle->user_id = trim($user_id);
            $vehicle->vehicle_type = $vehicle_type;
            $vehicle->psg_capacity = $psg_capacity;
            $vehicle->image = $image;

            if ($vehicle->addVehicle()) {
                header("Location: profile");
                exit;
            } else {
                $error = "Failed to add vehicle. Please try again.";
            }
        }

    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

try {
    $db = Database::getConnection();
    $vehicleTypeModel = new VehicleType($db);
    $vehicleTypes = $vehicleTypeModel->getAllTypes()->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $vehicleTypes = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - Add Vehicle</title>
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/base.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/navbar.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/sidebar.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/footer.css">
    
    <!-- Component styles -->
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/cards.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/buttons.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/forms.css">
    
    <!-- Page-specific styles -->
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/timeline.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/tables.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/profile.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/reviews.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/charts.css">
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/vehicle.css">

    <!-- Responsive styles (always last) -->
    <link rel="stylesheet" href="/CeylonGO/public/css/transport/responsive.css">   
    
    <!-- Font Awesome -->
    <link rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <!-- Navbar -->
  <header class="navbar">
    <div class="branding">
      <img src="/CeylonGO/public/images/logo.png" class="logo-img" alt="Ceylon Go Logo">
      <div class="logo-text">Ceylon Go</div>
    </div>
    <nav class="nav-links">
      <a href="#">Home</a>
      <a href="/CeylonGo/views/transport/logout.php">Logout</a>
      <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="User" class="profile-pic">
    </nav>
  </header>

  <div class="page-wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
      <ul>
        <li><a href="/CeylonGo/public/transporter/dashboard"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li><a href="/CeylonGo/public/transporter/upcoming"><i class="fa-regular fa-calendar"></i> Upcoming Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/pending"><i class="fa-regular fa-clock"></i> Pending Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/cancelled"><i class="fa-solid fa-xmark"></i> Cancelled Bookings</a></li>
        <li><a href="/CeylonGo/public/transporter/review"><i class="fa-regular fa-star"></i> Reviews</a></li>
        <li class="active"><a href="/CeylonGo/public/transporter/profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
        <li><a href="/CeylonGo/public/transporter/report"><i class="fa-solid fa-chart-line"></i> Performance Report</a></li>
        <li><a href="/CeylonGo/public/transporter/payment"><i class="fa-solid fa-credit-card"></i> My Payment</a></li>
      </ul>
    </div>

    <div class="main-content">
        <div style="margin-bottom: 30px;">
          <h2 style="color: #2c5530; font-size: 24px; margin-bottom: 8px;">
            <i class="fa-solid fa-car" style="color: #4CAF50; margin-right: 10px;"></i>
            Add Your Vehicle
          </h2>
          <p style="color: #666; font-size: 16px;">Please fill in your vehicle details below to register.</p>
        </div>

        <!-- Success/Error Messages -->
        <?php if ($message): ?>
            <div style="background: linear-gradient(135deg, #d4edda, #c3e6cb); color: #155724; padding: 15px 20px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid #28a745; display: flex; align-items: center; gap: 12px;">
                <i class="fa-solid fa-circle-check" style="font-size: 20px;"></i>
                <div>
                  <strong><?= $message ?></strong>
                  <br><small>Redirecting to profile page...</small>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div style="background: linear-gradient(135deg, #f8d7da, #f5c6cb); color: #721c24; padding: 15px 20px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid #dc3545; display: flex; align-items: center; gap: 12px;">
                <i class="fa-solid fa-circle-exclamation" style="font-size: 20px;"></i>
                <strong><?= $error ?></strong>
            </div>
        <?php endif; ?>

  <!-- Registration Form -->
  <div class="form-container">
    <h2><i class="fa-solid fa-clipboard"></i> Vehicle Information</h2>
    <form method="POST" enctype="multipart/form-data" action="vehicle">

      <div class="form-grid">
        <div class="form-group">
          <label for="vehicle_type">Vehicle Type <span style="color: #c62828;">*</span></label>
          <select name="vehicle_type" id="vehicle_type" required>
            <option value="">Select Vehicle Type</option>
            <?php foreach($vehicleTypes as $type): ?>
              <option value="<?= $type['type_id'] ?>"><?= $type['type_name'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="vehicle_no">Vehicle Number/License Plate <span style="color: #c62828;">*</span></label>
          <input type="text" id="vehicle_no" name="vehicle_no" placeholder="e.g., ABC-1234" required>
        </div>

        <div class="form-group">
          <label for="psg_capacity">Passenger Capacity <span style="color: #c62828;">*</span></label>
          <input type="number" id="psg_capacity" name="psg_capacity" min="1" value="1" placeholder="Enter passenger capacity" required>
          <div id="capacityError" style="color: #c62828; font-size: 13px; margin-top: 5px; display: none;"></div>
        </div>

        <div class="form-group full-width">
          <label for="vehicle_image">Upload Vehicle Photo <span style="color: #999;">(Optional)</span></label>
          <div class="upload-box" id="uploadBox">
            <i class="fa-solid fa-cloud-arrow-up"></i>
            <p><strong>Drag and drop</strong> or <strong>click</strong> to upload an image</p>
            <small style="color: #999;">JPEG, PNG, or WebP (Max 5MB)</small>
            <input type="file" id="vehicle_image" name="vehicle_image" accept="image/*" style="display: none;">
          </div>
          <div id="fileName" style="margin-top: 10px; color: #666; font-size: 14px;"></div>
        </div>
      </div>

      <input type="hidden" name="user_id" value="<?=$user_id?>">

      <div class="buttons">
        <button type="button" class="btn-cancel" onclick="window.history.back()">
          <i class="fa-solid fa-arrow-left"></i> Cancel
        </button>
        <button type="submit" class="btn-save">
          <i class="fa-solid fa-plus"></i> Add Vehicle
        </button>
      </div>

    </form>
  </div>

    </div>
  </div>

  <!-- Footer -->
  <footer>
    <ul>
      <li><a href="#">About Us</a></li>
      <li><a href="#">Contact Us</a></li>
    </ul>
  </footer>


</body>
</html>

<script>
  // Handle successful form submission
  <?php if ($message): ?>
    setTimeout(function() {
      window.location.href = 'profile';
    }, 2000);
  <?php endif; ?>

  // Passenger capacity limits per vehicle type
  const capacityLimits = { '1': 3, '2': 4, '3': 7, '4': 7, '5': 20, '6': 20 };
  const capacityNames = { '1': 'TUK', '2': 'Car', '3': 'Minivan', '4': 'Minivan AC', '5': 'Bus', '6': 'Bus AC' };
  
  const vehicleTypeSelect = document.getElementById('vehicle_type');
  const psgCapacityInput = document.getElementById('psg_capacity');
  const capacityError = document.getElementById('capacityError');
  const vehicleImageInput = document.getElementById('vehicle_image');
  const uploadBox = document.getElementById('uploadBox');
  const fileNameDisplay = document.getElementById('fileName');

  // Validate capacity
  function validateCapacity() {
    const type = vehicleTypeSelect.value;
    const capacity = parseInt(psgCapacityInput.value) || 0;
    const maxCapacity = capacityLimits[type];

    if (type && maxCapacity && capacity > maxCapacity) {
      capacityError.textContent = `Maximum passenger capacity for ${capacityNames[type]} is ${maxCapacity}.`;
      capacityError.style.display = 'block';
      psgCapacityInput.style.borderColor = '#c62828';
      return false;
    } else {
      capacityError.style.display = 'none';
      psgCapacityInput.style.borderColor = '';
      return true;
    }
  }

  vehicleTypeSelect.addEventListener('change', function() {
    const type = this.value;
    if (capacityLimits[type]) {
      psgCapacityInput.setAttribute('max', capacityLimits[type]);
    }
    validateCapacity();
  });

  psgCapacityInput.addEventListener('input', validateCapacity);

  // File upload with drag and drop
  uploadBox.addEventListener('click', () => vehicleImageInput.click());

  vehicleImageInput.addEventListener('change', updateFileName);

  uploadBox.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadBox.style.borderColor = '#4CAF50';
    uploadBox.style.background = 'rgba(76, 175, 80, 0.1)';
  });

  uploadBox.addEventListener('dragleave', () => {
    uploadBox.style.borderColor = '#bbb';
    uploadBox.style.background = '#f9f9f9';
  });

  uploadBox.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadBox.style.borderColor = '#bbb';
    uploadBox.style.background = '#f9f9f9';
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
      vehicleImageInput.files = files;
      updateFileName();
    }
  });

  function updateFileName() {
    if (vehicleImageInput.files && vehicleImageInput.files[0]) {
      const fileName = vehicleImageInput.files[0].name;
      const fileSize = (vehicleImageInput.files[0].size / 1024 / 1024).toFixed(2);
      fileNameDisplay.innerHTML = `<i class="fa-solid fa-check-circle" style="color: #4CAF50; margin-right: 8px;"></i><strong>${fileName}</strong> (${fileSize} MB)`;
    }
  }

  // Form submission validation
  document.querySelector('form').addEventListener('submit', function(e) {
    if (!validateCapacity()) {
      e.preventDefault();
      psgCapacityInput.focus();
    }
  });
</script>
