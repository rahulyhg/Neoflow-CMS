<?php

namespace Neoflow\Module\Blog\Model;

use Neoflow\CMS\App;
use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\ORM\EntityValidator;
use Neoflow\Module\Search\ModelSearchInterface;
use Neoflow\Module\Search\Result;
use Neoflow\Module\Search\Results;

class ArticleModel extends AbstractModel implements ModelSearchInterface
{
    /**
     * Traits.
     */
    use SectionTrait;
    use UrlTrait;

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
        'website_keywords', 'website_description', 'website_title',
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
}
