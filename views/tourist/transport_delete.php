<?php
require_once('../../config/database.php');
$id = $_GET['id'];

// Delete request
$conn->query("DELETE FROM tourist_transport_requests WHERE id=$id");

// Redirect back to report page
header("Location: transport_report");
exit();
?>
