<?php

namespace Neoflow\Module\Sitemap\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\ORM\EntityValidator;

class SettingModel extends AbstractModel
{
    /**
     * @var array
     */
    public static $changeFrequencies = [
        'always',
        'hourly',
        'daily',
        'weekly',
        'monthly',
        'yearly',
        'never',
    ];

    /**
     * @var array
     */
    public static $sitemapLifetimes = [
        6 => '6 hours',
        12 => '12 hours',
        24 => '1 day',
        72 => '3 days',
        168 => '1 week',
        0 => 'Unlimited',
    ];

    /**
     * @var string
     */
    public static $tableName = 'mod_sitemap_settings';

    /**
     * @var string
     */
    public static $primaryKey = 'setting_id';

    /**
     * @var array
     */
    public static $properties = [
        'setting_id', 'default_changefreq',
        'default_priority', 'sitemap_lifetime',
        'automated_creation', ];

    /**
     * Setter.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        if ('default_priority' === $name) {
            if ((float) $value >= 1) {
                $value = 1;
            }
        }
        parent::__set($name, $value);
    }

    /**
     * Validate setting.
     *
     * @return bool
     */
    public function validate(): bool
    {
        $validator = new EntityValidator($this);

        $validator
            ->required()
            ->maxLength(20)
            ->oneOf(self::$changeFrequencies)
            ->set('default_changefreq', 'Change frequency');

        $validator
            ->required()
            ->maxLength(5)
            ->set('default_priority', 'Priority');

        return (bool) $validator->validate();
    }

    /**
     * Get sitemap lifetime in specified time type e.g. seconds or minutes.
     *
     * @param string $type Time type (seconds, minutes, hours, days)
     *
     * @return int
     */
    public function getSitemapLifetime(string $type = 'seconds'): int
    {
        $sitemapLifetime = (int) $this->sitemap_lifetime;
        if ('seconds' === $type) {
            return $sitemapLifetime * 3600;
        } elseif ('minutes' === $type) {
            return $sitemapLifetime * 60;
        } elseif ('days' === $type) {
            return $sitemapLifetime / 24;
        }

        return $sitemapLifetime;
    }
}
