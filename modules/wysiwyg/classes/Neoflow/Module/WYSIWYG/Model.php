<?php
namespace Neoflow\Module\WYSIWYG;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\CMS\Model\SectionModel;
use Neoflow\Framework\ORM\Repository;

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

    /**
     * Get repository to fetch section.
     *
     * @return Repository
     */
    public function section(): Repository
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\SectionModel', 'section_id');
    }

    /**
     * Get section.
     *
     * @return SectionModel|null
     */
    public function getSection()
    {
        $section = $this->section()->fetch();

        if ($section) {
            return $section;
        }

        return null;
    }
}
