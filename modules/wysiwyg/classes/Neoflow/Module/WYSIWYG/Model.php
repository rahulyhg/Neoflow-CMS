<?php
namespace Neoflow\Module\WYSIWYG;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\CMS\Model\SectionModel;
use Neoflow\Framework\ORM\Repository;
use Neoflow\Module\Search\ModelSearchInterface;
use Neoflow\Module\Search\Result;
use Neoflow\Module\Search\Results;

class Model extends AbstractModel implements ModelSearchInterface
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

    /**
     * Search for results
     * @param string $query Seach query string
     *
     * @return Results
     */
    public static function search(string $query): Results
    {
        $wysiwygs = static::findAllByColumns([
                'content' => '%' . $query . '%',
                ], 'LIKE');

        $results = new Results();

        foreach ($wysiwygs as $wysiwyg) {
            $section = $wysiwyg->getSection();
            if ($section) {
                $page = $section->getPage();
                if ($page) {
                    $quality = 50 + (substr_count(strip_tags($wysiwyg->content), $query));
                    $result = new Result($page->getUrl(), $page->title, $wysiwyg->content, $quality);
                    $results->add($result);
                }
            }
        }

        return $results;
    }
}
