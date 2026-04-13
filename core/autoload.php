<?php

spl_autoload_register(function ($className) {
    // Resolve from project root — do NOT use ../ relative to getcwd() (breaks under some Apache/CLI CWDs).
    $root = dirname(__DIR__);
    $paths = array(
        $root . '/controllers/',
        $root . '/models/',
        $root . '/core/'
    );

    foreach ($paths as $path) {
        $file = $path . $className . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }

    die("Autoloader error: Class '$className' not found.");
});