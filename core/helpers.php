<?php
function view($name, $data = []) {
    extract($data);
    $viewPath = __DIR__ . "/../views/$name.php";
    if (file_exists($viewPath)) {
        require $viewPath;
    } else {
        die("View not found: $name");
    }
}