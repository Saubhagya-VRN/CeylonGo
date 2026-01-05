<?php
// Quick database connection test
require_once '../config/config.php';

echo "<h2>Database Connection Test</h2>";

// Test 1: Check if constants are defined
echo "<h3>1. Checking Constants:</h3>";
echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NOT DEFINED') . "<br>";
echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED') . "<br>";
echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'NOT DEFINED') . "<br>";
echo "DB_PASS: " . (defined('DB_PASS') ? (DB_PASS === '' ? '(empty string)' : '***') : 'NOT DEFINED') . "<br><br>";

// Test 2: Try mysqli connection
echo "<h3>2. Testing mysqli Connection:</h3>";
try {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_error) {
        echo "❌ mysqli Connection FAILED: " . $mysqli->connect_error . "<br>";
    } else {
        echo "✅ mysqli Connection SUCCESS!<br>";
        echo "Server Info: " . $mysqli->server_info . "<br>";
        $mysqli->close();
    }
} catch (Exception $e) {
    echo "❌ mysqli Exception: " . $e->getMessage() . "<br>";
}

echo "<br>";

// Test 3: Try PDO connection with empty string
echo "<h3>3. Testing PDO Connection (with empty string):</h3>";
try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✅ PDO Connection SUCCESS (with empty string)!<br>";
} catch (PDOException $e) {
    echo "❌ PDO Connection FAILED (with empty string): " . $e->getMessage() . "<br>";
}

echo "<br>";

// Test 4: Try PDO connection with null
echo "<h3>4. Testing PDO Connection (with null):</h3>";
try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✅ PDO Connection SUCCESS (with null)!<br>";
} catch (PDOException $e) {
    echo "❌ PDO Connection FAILED (with null): " . $e->getMessage() . "<br>";
}

echo "<br>";

// Test 5: Check MySQL user authentication
echo "<h3>5. MySQL User Information:</h3>";
try {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$mysqli->connect_error) {
        $result = $mysqli->query("SELECT user, host, plugin, authentication_string FROM mysql.user WHERE user = 'root' AND host = 'localhost'");
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "User: " . $row['user'] . "<br>";
                echo "Host: " . $row['host'] . "<br>";
                echo "Plugin: " . $row['plugin'] . "<br>";
                echo "Has Password: " . (!empty($row['authentication_string']) ? 'YES' : 'NO') . "<br>";
            }
        }
        $mysqli->close();
    }
} catch (Exception $e) {
    echo "Could not check user info: " . $e->getMessage() . "<br>";
}

echo "<br><hr>";
echo "<p><strong>Recommendation:</strong> If mysqli works but PDO doesn't, we may need to use mysqli as a fallback or configure MySQL authentication.</p>";
?>

