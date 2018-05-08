<?php

namespace Neoflow\Image\Exception;

use Neoflow\Filesystem\Exception\FileException;

class ImageFileException extends FileException
{
    const NOT_SUPPORTED_IMAGE_TYPE = 1001;
}
