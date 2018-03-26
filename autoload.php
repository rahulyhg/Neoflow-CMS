<?php

use Neoflow\Framework\Handler\Loader;

// Include loader class
require_once APP_ROOT . '/framework/classes/Neoflow/Framework/Handler/Loader.php';

// Create loader
$loader = new Loader();
$loader
    ->loadFunctionsFromDirectories([
        APP_ROOT . '/framework/functions',
        APP_ROOT . '/application/functions',
    ])
    ->addClassDirectories([
        APP_ROOT . '/framework/classes',
        APP_ROOT . '/application/classes',
    ])
    ->loadLibraries([
        APP_ROOT . '/framework/libs',
        APP_ROOT . '/application/libs',
    ])
    ->registerAutoload();
