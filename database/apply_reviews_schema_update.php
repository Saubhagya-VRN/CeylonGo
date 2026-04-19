<?php
/**
 * Apply database/update_reviews_drop_destination_add_admin_reply.sql
 * http://localhost/CeylonGo/database/apply_reviews_schema_update.php
 */
require_once dirname(__DIR__) . '/config/database.php';

$sql = file_get_contents(__DIR__ . '/update_reviews_drop_destination_add_admin_reply.sql');
$sql = preg_replace('/--[^\r\n]*/m', '', $sql);
$parts = array_filter(array_map('trim', explode(';', $sql)));
foreach ($parts as $statement) {
    if ($statement === '') {
        continue;
    }
    if ($conn->query($statement)) {
        echo '<p style="color:green;font-family:sans-serif;">OK</p>';
    } else {
        echo '<p style="color:orange;font-family:sans-serif;">' . htmlspecialchars($conn->error) . '</p>';
    }
}
echo '<p style="font-family:sans-serif;">Done. You can close this page.</p>';
$conn->close();
