<?php
namespace Neoflow\Module\Search\Model;

use Neoflow\CMS\Core\AbstractModel;

class SettingModel extends AbstractModel
{

    /**
     * @var string
     */
    public static $tableName = 'mod_sitemap_settings';

    /**
     * @var string
     */
    public static $primaryKey = 'setting_id';

    /**
     * @var array
     */
    public static $properties = ['setting_id'];

}
