<?php
spl_autoload_register(function ($className) {
    if (empty($className)) {
        return;
    }
    // Remove \ character in className 
    if ($className[0] == '\\') {
        $className = substr($className, 1);
    }

    // Get name file
    $classPath = str_replace('Lib\\Aws\\Sns\\', '', $className) . '.php';

    if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . $classPath)) {
        require(__DIR__ . DIRECTORY_SEPARATOR . $classPath);
    }
});
