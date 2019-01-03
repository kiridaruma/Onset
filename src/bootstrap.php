<?php

spl_autoload_register(function ($name) {
    $name = str_replace('Onset', 'lib', $name);
    $name = str_replace('\\', '/', $name);

    $path = __DIR__ . $name . '.php';
    if (!file_exists($path)) {
        return;
    }
    require_once $path;
});

$config = require_once __DIR__ . '/config.php';

$app = new Onset\App($config);
