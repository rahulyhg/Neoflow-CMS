<?php

use Neoflow\Framework\Handler\Loader;

// Include loader class
require_once ROOT_DIR . '/framework/classes/Neoflow/Framework/Handler/Loader.php';

// Create loader
$loader = new Loader();
$loader
    ->loadFunctionsFromDirectories([
        ROOT_DIR . '/framework/functions',
        ROOT_DIR . '/application/functions',
    ])
    ->addClassDirectories([
        ROOT_DIR . '/framework/classes',
        ROOT_DIR . '/application/classes',
    ])
    ->loadLibraries([
        ROOT_DIR . '/framework/libs',
        ROOT_DIR . '/application/libs',
    ])
    ->registerAutoload();
