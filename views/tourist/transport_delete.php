<?php
require_once('../../config/database.php');
$id = (int) ($_GET['id'] ?? 0);

if ($id) {
    $stmt = $conn->prepare("DELETE FROM transport_requests WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Redirect back to report page
header("Location: /CeylonGo/public/tourist/transport-report");
exit();
?>
