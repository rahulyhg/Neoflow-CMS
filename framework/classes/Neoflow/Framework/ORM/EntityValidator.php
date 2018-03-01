<?php

namespace Neoflow\Framework\ORM;

use Neoflow\Framework\Core\AbstractModel;
use Neoflow\Validation\Validator;

class EntityValidator extends Validator
{
    /**
     * Constructor.
     *
     * @param AbstractModel $entity
     */
    public function __construct(AbstractModel $entity)
    {
        $this->setData($entity->toArray());

        $this->logger()->debug('Entity validator created', [
            'Type' => $entity->getReflection()->getShortName(),
            'Data' => $entity->toArray(),
        ]);
    }
}
