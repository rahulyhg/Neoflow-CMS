<?php

namespace Neoflow\Module\Blog\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\ORM\Repository;

class ArticleCategoryModel extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'mod_blog_articles_categories';

    /**
     * @var string
     */
    public static $primaryKey = 'article_category_id';

    /**
     * @var array
     */
    public static $properties = ['article_category_id', 'article_id', 'category_id'];

    /**
     * Get repository to fetch article.
     *
     * @return Repository
     */
    public function permission(): Repository
    {
        return $this->belongsTo('Neoflow\\Module\\Blog\\Model\\ArticleModel', 'article_id');
    }

    /**
     * Get repository to fetch role.
     *
     * @return Repository
     */
    public function role(): Repository
    {
        return $this->belongsTo('Neoflow\\Module\\Blog\\Model\\CategoryModel', 'category_id');
    }
}
