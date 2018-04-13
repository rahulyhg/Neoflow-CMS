<?php

namespace Neoflow\Validation;

use Exception;

class ValidationException extends Exception
{
    protected $errors = [];

    /**
     * Constructor.
     *
     * @param type  $message
     * @param array $errors
     */
    public function __construct(string $message, array $errors = [])
    {
        parent::__construct($message);

        if (count($errors)) {
            $this->errors = $errors;
        } else {
            $this->errors = [$message];
        }
    }

    /**
     * Get errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
