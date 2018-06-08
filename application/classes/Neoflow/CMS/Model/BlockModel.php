<?php

namespace Neoflow\CMS\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\Core\AbstractModel as FrameworkAbstractModel;
use Neoflow\Framework\ORM\EntityValidator;
use Neoflow\Framework\ORM\Repository;
use RuntimeException;
use function slugify;
use function translate;

class BlockModel extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'blocks';

    /**
     * @var string
     */
    public static $primaryKey = 'block_id';

    /**
     * @var array
     */
    public static $properties = ['block_id', 'block_key', 'title'];

    /**
     * Get repository to fetch sections.
     *
     * @return Repository
     */
    public function sections(): Repository
    {
        return $this->hasMany('Neoflow\\CMS\\Model\\SectionModel', 'block_id');
    }

    /**
     * Set block value.
     *
     * @param string $property Block property
     * @param mixed  $value    Property value
     * @param bool   $silent   Set TRUE to prevent the tracking of the change
     *
     * @return self
     *
     * @throws RuntimeException
     */
    public function set(string $property, $value = null, bool $silent = false): FrameworkAbstractModel
    {
        if ('block_key' === $property) {
            $value = slugify($value);
        }

        return parent::set($property, $value, $silent);
    }

    /**
     * Validate block.
     *
     * @return bool
     */
    public function validate(): bool
    {
        $validator = new EntityValidator($this);

        $validator
            ->required()
            ->set('title', translate('Title'));

        $validator
            ->callback(function ($blockKey, $id) {
                return 0 === BlockModel::repo()
                    ->where('block_key', '=', $blockKey)
                    ->where('block_id', '!=', $id)
                    ->count();
            }, '{0} has to be unique', [$this->id()])
            ->set('block_key', 'Key');

        return (bool) $validator->validate();
    }
}
