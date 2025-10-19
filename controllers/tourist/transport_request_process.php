<?php
require_once('../../config/database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $customerName = $_POST['customerName'];
    $vehicleType = $_POST['vehicleType'];
    $date = $_POST['date'];
    $pickupTime = $_POST['pickupTime'];
    $pickupLocation = $_POST['pickupLocation'];
    $dropoffLocation = $_POST['dropoffLocation'];
    $numPeople = $_POST['numPeople'];
    $notes = $_POST['notes'];

    // Insert into database
    $query = "INSERT INTO tourist_transport_requests
              (customerName, vehicleType, date, pickupTime, pickupLocation, dropoffLocation, numPeople, notes)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssis", $customerName, $vehicleType, $date, $pickupTime, $pickupLocation, $dropoffLocation, $numPeople, $notes);

    if ($stmt->execute()) {
        // Redirect to report page after successful insert
        header("Location: ../../views/tourist/transport_report.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
