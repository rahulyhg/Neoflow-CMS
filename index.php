<?php

use Neoflow\CMS\App;

// PHP version check
if (version_compare(PHP_VERSION, '7.1', '<')) {
    die('PHP 7.1 or newer required.');
}

// Start timer
$startTime = microtime(true);

// Set PHP error log
// Don't forget to disable when running as production
ini_set('display_errors', true);
ini_set('display_startup_errors', true);
error_reporting(E_ALL);

// Define constant
define('APP_PATH', __DIR__);
define('APP_MODE', 'PROD');

// Include autoload
require_once 'autoload.php';

// Define config file path
$configFilePath = __DIR__.DIRECTORY_SEPARATOR.'config.php';

// Create, execute and publish CMS app
$app = new App([], false, true);
$app
    ->initialize($startTime, $loader, $configFilePath)
    ->execute()
    ->publish();
