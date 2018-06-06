<?php

namespace Neoflow\Module\Search\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\ORM\EntityValidator;

class EntityModel extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'mod_search_entities';

    /**
     * @var string
     */
    public static $primaryKey = 'entity_id';

    /**
     * @var array
     */
    public static $properties = ['entity_id', 'entity_class'];

    /**
     * Validate url.
     *
     * @return bool
     *
     * @throws \Neoflow\Validation\ValidationException
     */
    public function validate(): bool
    {
        $validator = new EntityValidator($this);

        $validator
            ->required()
            ->maxLength(255)
            ->callback(function ($value) {
                return class_exists($value);
            }, 'The entity class must exist')
            ->callback(function ($value) {
                $interfaces = class_implements(new $value());

                return in_array('Neoflow\\Module\\Search\\ModelSearchInterface', $interfaces);
            }, 'The entity class has to implement the model search interface')
            ->set('entity_class');

        return (bool) $validator->validate();
    }
}
