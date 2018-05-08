<?php

use Neoflow\Image\ImageFile;

// Define constant
define('APP_PATH', __DIR__ . '/../..');
define('APP_MODE', 'DEV');

include APP_PATH . '/autoload.php';

ImageFile::load(__DIR__ . '/img/asd.jpg')
    ->crop(100, 200)
    ->save(__DIR__ . '/img/cropped-asd.jpg');

ImageFile::load(__DIR__ . '/img/asd.jpg')
    ->resize(2000, 1000)
    ->save(__DIR__ . '/img/resized-asd.jpg');

ImageFile::load(__DIR__ . '/img/asd.jpg')
    ->resizeBestFit(1000, 2000)
    ->save(__DIR__ . '/img/resized-best-fit-asd.jpg');

ImageFile::load(__DIR__ . '/img/asd.jpg')
    ->resizeToHeight(50)
    ->save(__DIR__ . '/img/resized-to-height-asd.jpg');

ImageFile::load(__DIR__ . '/img/asd.jpg')
    ->resizeToWidth(50)
    ->save(__DIR__ . '/img/resized-to-width-asd.jpg');
