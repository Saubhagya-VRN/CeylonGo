<?php
// views/tourist/profile.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tourist') {
    header('Location: /CeylonGo/public/login');
    exit;
}

$tourist = $tourist ?? null;
$success_message = $_SESSION['success'] ?? '';
$error_message = $_GET['error'] ?? $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Go - My Profile</title>
  <link rel="stylesheet" href="../../public/css/common.css">
  <link rel="stylesheet" href="../../public/css/tourist/navbar.css">
  <link rel="stylesheet" href="../../public/css/tourist/footer.css">
  <link rel="stylesheet" href="../../public/css/tourist/profile.css">
</head>
<body class="bg-app">
  <!-- Navbar include -->
  <?php include 'header.php'; ?>

  <main class="profile-main"></main>

  <!-- Footer include -->
  <?php include 'footer.php'; ?>
</body>
</html>


