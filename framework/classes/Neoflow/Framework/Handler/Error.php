<?php

namespace Neoflow\Framework\Handler;

class Error
{
    protected function errorHandler($num, $str, $file, $line, $context = null)
    {
        throw new ErrorException($str, 0, $num, $file, $line);
    }
}
