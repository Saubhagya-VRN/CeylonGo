<?php

spl_autoload_register(function ($className) {
    // Namespaced classes are handled by Composer (loaded before this file in bootstrap).
    if (strpos($className, '\\') !== false) {
        return;
    }

    $paths = [
        '../controllers/',
        '../models/',
        '../core/'
    ];

    foreach ($paths as $path) {
        $file = $path . $className . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }

    // Do not die: allow other registered autoloaders (Composer) to run.
});