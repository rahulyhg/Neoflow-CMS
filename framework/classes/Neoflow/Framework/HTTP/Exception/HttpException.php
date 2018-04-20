<?php
namespace Neoflow\Framework\HTTP\Exception;

use Exception;
use InvalidArgumentException;
use Neoflow\Framework\HTTP\Responsing\StatusCode;
use Throwable;

class HttpException extends Exception
{

    /**
     * Constructor.
     *
     * @param string    $message
     * @param int       $code
     * @param Throwable $previous
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $message = '', int $code = 500, Throwable $previous = null)
    {
        if (StatusCode::isValid($code)) {
            if ('' === $message) {
                $message = StatusCode::getMessage($code);
            }
            parent::__construct($message, $code, $previous);
        } else {
            throw new InvalidArgumentException('HTTP status code "' . $code . '" is invalid');
        }
    }
}
