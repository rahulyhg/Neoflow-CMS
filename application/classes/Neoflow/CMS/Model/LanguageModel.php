<?php

namespace Neoflow\CMS\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\ORM\Repository;

class LanguageModel extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'languages';

    /**
     * @var string
     */
    public static $primaryKey = 'language_id';

    /**
     * @var array
     */
    public static $properties = ['language_id', 'is_active', 'code', 'title', 'flag_code'];

    /**
     * Get repository to fetch pages.
     *
     * @return Repository
     */
    public function pages(): Repository
    {
        return $this->hasMany('Neoflow\\CMS\\Model\\PageModel', 'language_id');
    }

    /**
     * Get repository to fetch setting.
     *
     * @return Repository
     */
    public function setting(): Repository
    {
        return $this->hasOne('Neoflow\\CMS\\Model\\SettingModel', 'language_id');
    }

    /**
     * Render flag icon to html output.
     *
     * @return string
     */
    public function renderFlagIcon(): string
    {
        return '<i class="flag-icon flag-icon-'.$this->flag_code.'"></i>';
    }
}
