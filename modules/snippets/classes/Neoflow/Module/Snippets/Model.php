<?php

namespace Neoflow\Module\Snippets;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\ORM\EntityValidator;
use Neoflow\Validation\ValidationException;
use Throwable;

class Model extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'mod_snippets';

    /**
     * @var string
     */
    public static $primaryKey = 'snippet_id';

    /**
     * @var array
     */
    public static $properties = ['snippet_id', 'title', 'code', 'placeholder', 'description'];

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
            $result = $this->app()->getService('code')->executeCode($this->code, $parameters);
            if (is_string($result)) {
                return $result;
            }
        } catch (Throwable $e) {
            throw new ValidationException(translate('Snippet code is invalid: "{0}"', [$e->getMessage().' on line '.$e->getLine()]));
        }
        throw new ValidationException(translate('Snippet code is valid, but has return a string'));
    }

    /**
     * Validate code and get status.
     *
     * @return bool
     */
    public function getCodeStatus(array $parameters = [])
    {
        try {
            $this->validateCode($parameters);

            return true;
        } catch (ValidationException $ex) {
            return false;
        }
    }

    /**
     * Execute code.
     *
     * @return bool
     */
    public function executeCode(array $parameters = []): string
    {
        try {
            return $this->validateCode($parameters);
        } catch (ValidationException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * Validate setting entity.
     *
     * @return array
     */
    public function validate(): array
    {
        $validator = new EntityValidator($this);

        $validator
                ->required()
                ->betweenLength(3, 100)
                ->callback(function ($title, $snippet) {
                    $codes = Model::repo()
                            ->where('title', '=', $title)
                            ->where('snippet_id', '!=', $snippet->id())
                            ->fetchAll();

                    return 0 === $codes->count();
                }, '{0} has to be unique', array($this))
                ->set('title', 'Title');

        $validator
                ->required()
                ->betweenLength(3, 100)
                ->pregMatch('/^([a-zA-Z0-9\-\_]+)$/', 'Placeholder is invalid. Please use only letters, underscores and hyphens.')
                ->callback(function (string $placeholder, Model $snippet) {
                    $codes = Model::repo()
                            ->where('placeholder', '=', $placeholder)
                            ->where('snippet_id', '!=', $snippet->id())
                            ->fetchAll();

                    return 0 === $codes->count();
                }, '{0} has to be unique', array($this))
                ->set('placeholder', 'Placeholder');

        $validator
                ->maxLength(250)
                ->set('description', 'Description');

        return $validator->validate();
    }
}
