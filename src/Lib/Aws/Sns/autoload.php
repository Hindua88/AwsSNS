<?php
spl_autoload_register(function ($className) {
    if ($className[0] == '\\') {
        $className = substr($className, 1);
    }

    $classPath = str_replace('Lib\\Aws\\Sns\\', '', $className) . '.php';

    if (file_exists(__DIR__ . DS . $classPath)) {
        require(__DIR__ . DS . $classPath);
    }
});