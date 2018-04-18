<?php

namespace Neoflow\Module\Sitemap\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\ORM\EntityValidator;

class UrlModel extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'mod_sitemap_urls';

    /**
     * @var string
     */
    public static $primaryKey = 'url_id';

    /**
     * @var array
     */
    public static $properties = ['url_id', 'loc', 'lastmod', 'changefreq', 'priority'];

    /**
     * Delete URL by location.
     *
     * @param string $location
     *
     * @return type
     */
    public static function deleteByLoc(string $location): bool
    {
        return self::deleteAllByColumn('loc', $location);
    }

    /**
     * Save url.
     *
     * @param bool $preventCacheClearing
     *
     * @return bool
     */
    public function save(bool $preventCacheClearing = false): bool
    {
        if (self::deleteByLoc($this->loc)) {
            return parent::save($preventCacheClearing);
        }

        return false;
    }

    /**
     * Validate url.
     *
     * @return bool
     */
    public function validate(): bool
    {
        $validator = new EntityValidator($this);

        $validator
            ->required()
            ->url()
            ->maxLength(255)
            ->set('loc');

        $validator
            ->maxLength(20)
            ->set('lastmod');

        $validator
            ->maxLength(20)
            ->oneOf(SettingModel::$changeFrequencies)
            ->set('changefreq');

        $validator
            ->maxLength(5)
            ->set('priority');

        return (bool) $validator->validate();
    }
}
