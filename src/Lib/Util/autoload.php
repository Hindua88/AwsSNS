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
    $classPath = str_replace('Lib\\Util\\', '', $className) . '.php';

    if (file_exists(__DIR__ . DS . $classPath)) {
        require(__DIR__ . DS . $classPath);
    }
});
