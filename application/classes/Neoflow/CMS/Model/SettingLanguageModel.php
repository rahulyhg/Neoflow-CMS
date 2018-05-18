<?php

namespace Neoflow\CMS\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\ORM\Repository;

class SettingLanguageModel extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'settings_languages';

    /**
     * @var string
     */
    public static $primaryKey = 'setting_language_id';

    /**
     * @var array
     */
    public static $properties = ['setting_language_id', 'setting_id', 'language_id'];

    /**
     * Get repository to fetch setting.
     *
     * @return Repository
     */
    public function setting(): Repository
    {
        return $this->belongsTo('Neoflow\\CMS\\Model\\SettingModel', 'setting_id');
    }

    /**
     * Get repository to fetch language.
     *
     * @return Repository
     */
    public function language(): Repository
    {
        return $this->belongsTo('Neoflow\\CMS\\Model\\LanguageModel', 'language_id');
    }
}
