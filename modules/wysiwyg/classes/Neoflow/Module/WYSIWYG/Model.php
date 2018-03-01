<?php

namespace Neoflow\Module\WYSIWYG;

use Neoflow\CMS\Core\AbstractModel;

class Model extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'mod_wysiwyg';

    /**
     * @var string
     */
    public static $primaryKey = 'wysiwyg_id';

    /**
     * @var array
     */
    public static $properties = ['wysiwyg_id', 'content', 'section_id'];
}
