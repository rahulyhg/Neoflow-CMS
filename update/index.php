<?php

if (isset($_GET['url'])) {

    // Get info
    $info = require_once 'info.php';

    // Define target path
    define('PATH', realpath('../'));

    // Get config
    $config = require_once PATH . '/config.php';

    // Include autoload of vendor
    require_once PATH . '/delivery/files/vendor/autoload.php';

    // Copy files to target path
    recursive_copy(PATH . $info['path']['files'], PATH);

    // Delete not needed framework folder
    rrmdir(APP_PATH . '/framework');

    header('Location: ' . $url);
    exit;
}
die('URL not found');
