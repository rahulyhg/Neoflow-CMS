<?php

namespace Neoflow\Module\Blog\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\ORM\EntityValidator;

class SettingModel extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'mod_blog_settings';

    /**
     * @var string
     */
    public static $primaryKey = 'setting_id';

    /**
     * @var array
     */
    public static $properties = [
        'setting_id', 'section_id', 'articles_per_page',
    ];

    /**
     * Validate settings.
     *
     * @return bool
     */
    public function validate(): bool
    {
        $validator = new EntityValidator($this);

        $validator
            ->required()
            ->min(3)
            ->set('articles_per_page');

        return (bool) $validator->validate();
    }
}
