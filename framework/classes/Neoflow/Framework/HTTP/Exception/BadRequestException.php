<?php

namespace Neoflow\Framework\HTTP\Exception;

use Throwable;

class BadRequestException extends HttpException
{
    /**
     * Constructor.
     *
     * @param string    $message
     * @param int       $code
     * @param Throwable $previous
     */
    public function __construct(string $message = '', int $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
