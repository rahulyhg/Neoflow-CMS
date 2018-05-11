<?php

namespace Neoflow\Module\Search\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\ORM\EntityValidator;

class SettingModel extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'mod_search_settings';

    /**
     * @var string
     */
    public static $primaryKey = 'setting_id';

    /**
     * @var array
     */
    public static $properties = [
        'setting_id',
        'url_path',
        'is_active',
    ];

    /**
     * Get search page url.
     *
     * @return string
     */
    public function getSearchPageUrl(): string
    {
        return $this->config()->getUrl($this->url_path);
    }

    /**
     * Validate settings.
     *
     * @return bool
     */
    public function validate(): bool
    {
        $validator = new EntityValidator($this);

        $validator
            ->required()
            ->minLength(3)
            ->maxLength(200)
            ->callback(function ($value, $router) {
                $route = $router->getRoutingByUrl($value);

                return !isset($route['route'][0]) || $route['route'][0] === 'frontend_index';
            }, 'The URL is already in use.', [$this->router()])
            ->set('url_path');

        return (bool) $validator->validate();
    }

    /**
     * Save settings.
     *
     * @param bool $preventCacheClearing Prevent that the cached database results will get deleted
     *
     * @return bool
     */
    public function save(bool $preventCacheClearing = false): bool
    {
        // Delete cached routes
        if (parent::save($preventCacheClearing)) {
            return $this->cache()->deleteByTag('cms_router');
        }

        return false;
    }
}
