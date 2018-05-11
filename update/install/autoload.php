<?php

use Neoflow\Framework\Handler\Loader;

// Include composer vendor autoload
require_once APP_PATH . '/vendor/autoload.php';

// Create loader
$loader = new Loader();
$loader
    ->loadFunctionsFromDirectories([
        APP_PATH . '/application/functions',
    ])
    ->addClassDirectories([
        APP_PATH . '/application/classes',
    ])
    ->loadLibraries([
        APP_PATH . '/application/libs',
    ])
    ->registerAutoload();
