<?php

namespace Neoflow\Module\Code;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Validation\ValidationException;
use Throwable;

class Model extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'mod_code';

    /**
     * @var string
     */
    public static $primaryKey = 'code_id';

    /**
     * @var array
     */
    public static $properties = ['code_id', 'content', 'section_id'];

    /**
     * Validate code.
     *
     * @param array $parameters
     *
     * @return string
     *
     * @throws ValidationException
     */
    public function validateCode(array $parameters = []): string
    {
        try {
            $result = $this->app()->service('code')->executeCode($this->content, $parameters);
            if (is_string($result)) {
                return $result;
            }
        } catch (Throwable $e) {
            throw new ValidationException(translate('Section code is invalid: "{0}"', [$e->getMessage().' on line '.$e->getLine()]));
        }
        throw new ValidationException(translate('Section code is valid, but has return a string'));
    }

    /**
     * Execute code.
     *
     * @return bool
     */
    public function executeCode(): string
    {
        try {
            return $this->validateCode();
        } catch (ValidationException $ex) {
            return $ex->getMessage();
        }
    }
}
