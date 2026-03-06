<?php
// Logout handler for transport providers
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to system login page
header('Location: /CeylonGo/public/login');
exit();
?>
