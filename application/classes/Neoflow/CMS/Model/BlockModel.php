<?php

namespace Neoflow\CMS\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\ORM\EntityValidator;
use Neoflow\Framework\ORM\Repository;

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
    public function sections()
    {
        return $this->hasMany('\\Neoflow\\CMS\\Model\\SectionModel', 'block_id');
    }

    /**
     * Set block value.
     *
     * @param string $key    Key of entity value
     * @param mixed  $value  Entity value
     * @param bool   $silent State if setting shouldn't be tracked
     *
     * @return self
     */
    protected function set($key, $value = null, $silent = false)
    {
        if ('block_key' === $key) {
            $value = slugify($value);
        }

        return parent::set($key, $value, $silent);
    }

    /**
     * Validate block.
     *
     * @return bool
     */
    public function validate()
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
                }, '{0} has to be unique', array($this->id()))
                ->set('block_key', 'Key');

        return $validator->validate();
    }
}
