<?php
/**
 * Drops `reviews.destination` on existing databases.
 * http://localhost/CeylonGo/database/apply_reviews_drop_destination.php
 */
require_once dirname(__DIR__) . '/config/database.php';

$sql = trim(file_get_contents(__DIR__ . '/reviews_drop_destination_column.sql'));
$sql = preg_replace('/--[^\r\n]*/m', '', $sql);
if ($conn->query($sql)) {
    echo '<p style="color:green;font-family:sans-serif;">Column <code>destination</code> removed from <code>reviews</code> (or change applied).</p>';
} else {
    echo '<p style="color:orange;font-family:sans-serif;">' . htmlspecialchars($conn->error) . '</p>';
    echo '<p style="font-family:sans-serif;">If it says unknown column, <code>destination</code> was already dropped.</p>';
}
$conn->close();
