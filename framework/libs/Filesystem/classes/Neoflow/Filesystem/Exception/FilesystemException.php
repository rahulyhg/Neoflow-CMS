<?php

namespace Neoflow\Filesystem\Exception;

abstract class FilesystemException extends \Exception
{
    const NOT_READABLE = 1;
    const DONT_EXIST = 2;
    const NOT_WRITEABLE = 3;
    const ALREADY_EXIST = 4;
}
