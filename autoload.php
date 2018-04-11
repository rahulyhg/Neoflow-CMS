<?php

use Neoflow\Framework\Handler\Loader;

// Include loader class
require_once APP_PATH . '/framework/classes/Neoflow/Framework/Handler/Loader.php';

// Create loader
$loader = new Loader();
$loader
    ->loadFunctionsFromDirectories([
        APP_PATH . '/framework/functions',
        APP_PATH . '/application/functions',
    ])
    ->addClassDirectories([
        APP_PATH . '/framework/classes',
        APP_PATH . '/application/classes',
    ])
    ->loadLibraries([
        APP_PATH . '/framework/libs',
        APP_PATH . '/application/libs',
    ])
    ->registerAutoload();
