<?php

namespace Neoflow\Module\Blog\Model;

use Neoflow\CMS\App;
use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\Core\AbstractModel as FrameworkAbstractModel;
use Neoflow\Framework\ORM\EntityCollection;
use Neoflow\Framework\ORM\EntityValidator;
use Neoflow\Framework\ORM\Repository;
use Neoflow\Module\Search\ModelSearchInterface;
use Neoflow\Module\Search\Result;
use Neoflow\Module\Search\Results;

class ArticleModel extends AbstractModel implements ModelSearchInterface
{
    /**
     * Traits.
     */
    use SectionTrait;

    /**
     * @var string
     */
    public static $tableName = 'mod_blog_articles';

    /**
     * @var string
     */
    public static $primaryKey = 'article_id';

    /**
     * @var array
     */
    public static $properties = [
        'article_id', 'section_id', 'title',
        'title_slug', 'abstract', 'content',
        'modified_when', 'published_when', 'author_user_id',
        'created_when', 'website_keywords', 'website_description',
        'website_title',
    ];

    /**
     * Search for results.
     *
     * @param string $query Search query string
     *
     * @return Results
     */
    public static function search(string $query): Results
    {
        $articles = static::repo()->whereRaw(
            '`content` LIKE "%?% OR `title` LIKE "%?% OR `abstract` LIKE "%?%', [$query, $query, $query]);

        $language_id = App::instance()->get('translator')->getCurrentLanguage()->id();

        $results = new Results();

        foreach ($articles as $article) {
            $section = $article->getSection();
            if ($section) {
                $page = $section->page()->where('language_id', '=', $language_id)->fetch();

                if ($page) {
                    $quality = 70;
                    if (false !== strpos($article->title, $query)) {
                        $quality = 90;
                    } elseif (false !== strpos($article->abstract, $query)) {
                        $quality = 80;
                    }
                    $quality += (substr_count(strip_tags($article->content), $query));
                    $result = new Result($article->getUrl(), $article->title, $article->abstract, $quality);
                    $results->add($result);
                }
            }
        }

        return $results;
    }

    /**
     * Get URL.
     *
     * @param array $parameters URL query parameters
     *
     * @return string
     */
    public function getUrl(array $parameters = []): string
    {
        return generate_url('pmod_blog_frontend_article_show', [
            'page' => $this->getSection()->getPage()->getRelativeUrl(),
            'title_slug' => $this->title_slug,
        ], $parameters);
    }

    /**
     * Get formatted published when date time.
     *
     * @param bool $withTime Set FALSE to get date without time
     *
     * @return string
     */
    public function getPublishedWhen(bool $withTime = true): string
    {
        return format_timestamp($this->published_when, $withTime);
    }

    /**
     * Get formatted modified when date time.
     *
     * @param bool $withTime Set FALSE to get date without time
     *
     * @return string
     */
    public function getModifiedWhen(bool $withTime = true): string
    {
        return format_timestamp($this->modified_when, $withTime);
    }

    /**
     * Get formatted created when date time.
     *
     * @param bool $withTime Set FALSE to get date without time
     *
     * @return string
     */
    public function getCreatedWhen(bool $withTime = true)
    {
        return format_timestamp($this->created_when, $withTime);
    }

    /**
     * Create article.
     *
     * @param array $data Article data
     *
     * @return FrameworkAbstractModel
     */
    public static function create(array $data): FrameworkAbstractModel
    {
        $data['created_when'] = microtime(true);
        $data['published_when'] = microtime(true);

        return parent::create($data);
    }

    /**
     * Get author user.
     *
     * @return UserModel|null
     */
    public function getAuthorUser()
    {
        $user = $this->authorUser()->fetch();

        if (!$user) {
            // Set administrator when no author is set
            $this->author_user_id = 1;
            $this->save();

            $user = $this->getAuthorUser();
        }

        return $user;
    }

    /**
     * Get repository to fetch author user.
     *
     * @return Repository
     */
    public function authorUser(): Repository
    {
        return $this->belongsTo('Neoflow\\CMS\\Model\\UserModel', 'author_user_id');
    }

    /**
     * Save article.
     *
     * @param bool $preventCacheClearing Prevent that the cached database results will get deleted
     *
     * @return bool
     */
    public function save(bool $preventCacheClearing = false): bool
    {
        $this->title_slug = mb_substr(slugify($this->title), 0, 100);

        $this->modified_when = microtime(true);

        if (parent::save($preventCacheClearing)) {
            if (is_array($this->category_ids)) {
                // Delete current article categories
                ArticleCategoryModel::deleteAllByColumn('article_id', $this->id());

                // Create new article categories
                foreach ($this->category_ids as $category_id) {
                    ArticleCategoryModel::create([
                        'article_id' => $this->id(),
                        'category_id' => $category_id,
                    ])->save($preventCacheClearing);
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Get categories.
     *
     * @return EntityCollection
     */
    public function getCategories(): EntityCollection
    {
        return $this->categories()->fetchAll();
    }

    /**
     * Validate article.
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
                return 0 === ArticleModel::repo()
                        ->where('title', '=', $title)
                        ->where('article_id', '!=', $id)
                        ->count();
            }, '{0} has to be unique', [$this->id()])
            ->set('title', 'Title');

        $validator
            ->maxLength(500)
            ->set('abstract', 'Abstract');

        $validator
            ->callback(function ($category_ids, ArticleModel $article) {
                if (!count($category_ids)) {
                    return count($article->category_ids) || $article->categories()->count();
                }

                return true;
            }, '{0} is required', [$this])
            ->set('category_ids', 'Category');

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
     * Get repository to fetch categories.
     *
     * @return Repository
     */
    public function categories(): Repository
    {
        return $this->hasManyThrough('Neoflow\\Module\\Blog\\Model\\CategoryModel', 'Neoflow\\Module\\Blog\\Model\\ArticleCategoryModel', 'article_id', 'category_id');
    }

    /**
     * Delete article.
     *
     * @return bool
     */
    public function delete(): bool
    {
        ArticleCategoryModel::deleteAllByColumn('article_id', $this->id());

        return parent::delete();
    }
}
