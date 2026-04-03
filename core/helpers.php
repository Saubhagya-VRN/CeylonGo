<?php
/**
 * Absolute URL to a path under public (for PayHere return/notify URLs).
 */
function app_absolute_url(string $path = ''): string {
    $https = !empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base = defined('BASE_URL') ? BASE_URL : '';
    $path = ltrim($path, '/');
    return $path === '' ? ($scheme . '://' . $host . $base) : ($scheme . '://' . $host . $base . '/' . $path);
}

function view($name, $data = []) {
    extract($data);
    $viewPath = __DIR__ . "/../views/$name.php";
    if (file_exists($viewPath)) {
        require $viewPath;
    } else {
        die("View not found: $name");
    }
}