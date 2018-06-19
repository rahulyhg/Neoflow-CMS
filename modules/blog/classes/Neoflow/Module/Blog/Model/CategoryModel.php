<?php

namespace Neoflow\Module\Blog\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\ORM\EntityValidator;
use Neoflow\Module\Search\ModelSearchInterface;
use Neoflow\Module\Search\Results;

class CategoryModel extends AbstractModel implements ModelSearchInterface
{
    /**
     * Traits.
     */
    use SectionTrait;
    use UrlTrait;

    /**
     * @var string
     */
    public static $tableName = 'mod_blog_categories';

    /**
     * @var string
     */
    public static $primaryKey = 'category_id';

    /**
     * @var array
     */
    public static $properties = [
        'category_id', 'section_id', 'title',
        'title_slug', 'description', 'website_keywords',
        'website_description', 'website_title',
    ];

    /**
     * Validate category.
     *
     * @return bool
     */
    public function validate(): bool
    {
        $validator = new EntityValidator($this);

        $validator
            ->required()
            ->set('section_id');

        $validator
            ->required()
            ->minLength(3)
            ->maxLength(100)
            ->set('title', 'Title');

        $validator
            ->maxLength(250)
            ->set('description', 'Description');

        $validator
            ->maxLength(250)
            ->set('website_keywords', 'Website keywords');

        $validator
            ->maxLength(250)
            ->set('website_description', 'Website description');

        $validator
            ->maxLength(100)
            ->set('website_title', 'Website title');

        return (bool) $validator->validate();
    }

    /**
     * Search for results.
     *
     * @param string $query Search query string
     *
     * @return Results
     */
    public static function search(string $query): Results
    {
        $categories = static::repo()->whereRaw(
            '`title` LIKE "%?% OR `description` LIKE "%?%', [$query, $query]);

        $language_id = App::instance()->get('translator')->getCurrentLanguage()->id();

        $results = new Results();

        foreach ($categories as $category) {
            $section = $category->getSection();
            if ($section) {
                $page = $section->page()->where('language_id', '=', $language_id)->fetch();

                if ($page) {
                    $quality = 80;
                    if (false !== strpos($category->title, $query)) {
                        $quality = 90;
                    }
                    $quality += (substr_count(strip_tags($category->description), $query));
                    $result = new Result($category->getUrl(), $category->title, $category->description, $quality);
                    $results->add($result);
                }
            }
        }

        return $results;
    }
}
