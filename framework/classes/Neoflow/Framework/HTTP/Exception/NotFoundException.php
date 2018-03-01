<?php

namespace Neoflow\Framework\HTTP\Exception;

use Throwable;

class NotFoundException extends HttpException
{
    /**
     * Constructor.
     *
     * @param string    $message
     * @param int       $code
     * @param Throwable $previous
     */
    public function __construct(string $message = '', int $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
