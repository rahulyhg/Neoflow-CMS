<?php

namespace Neoflow\Module\Blog\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\ORM\EntityValidator;
use Neoflow\Framework\ORM\Repository;
use Neoflow\Module\Search\ModelSearchInterface;
use Neoflow\Module\Search\Results;
use Neoflow\Validation\ValidationException;

class CategoryModel extends AbstractModel implements ModelSearchInterface
{
    /**
     * Traits.
     */
    use SectionTrait;

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
     * Get URL.
     *
     * @param array $parameters URL query parameters
     *
     * @return string
     */
    public function getUrl(array $parameters = []): string
    {
        return generate_url('pmod_blog_frontend_article_index_category', [
            'page' => $this->getSection()->getPage()->getRelativeUrl(),
            'slug' => $this->title_slug,
        ], $parameters);
    }

    /**
     * Get website title.
     *
     * @return string
     */
    public function getWebsiteTitle(): string
    {
        if ($this->website_title) {
            return $this->website_title;
        }

        return $this->title;
    }

    /**
     * Get website descriptiomn.
     *
     * @return string
     */
    public function getWebsiteDescription(): string
    {
        if ($this->website_description) {
            return $this->website_description;
        }

        return $this->description;
    }

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
            ->callback(function ($title, $id) {
                return 0 === CategoryModel::repo()
                        ->where('title', '=', $title)
                        ->where('category_id', '!=', $id)
                        ->count();
            }, '{0} has to be unique', [$this->id()])
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

    /**
     * Get repository to fetch articles.
     *
     * @return Repository
     */
    public function articles(): Repository
    {
        return $this->hasManyThrough('Neoflow\\Module\\Blog\\Model\\ArticleModel', 'Neoflow\\Module\\Blog\\Model\\ArticleCategoryModel', 'category_id', 'article_id');
    }

    /**
     * Save category.
     *
     * @param bool $preventCacheClearing Prevent that the cached database results will get deleted
     *
     * @return bool
     */
    public function save(bool $preventCacheClearing = false): bool
    {
        $this->title = trim($this->title);
        $this->title_slug = slugify($this->title);

        return parent::save($preventCacheClearing);
    }

    /**
     * Delete category.
     *
     * @return bool
     *
     * @throws ValidationException
     */
    public function delete(): bool
    {
        if (!$this->articles()->count()) {
            return parent::delete();
        }
        throw new ValidationException(translate('{0} is in use and cannot be deleted', ['Category']));
    }
}
