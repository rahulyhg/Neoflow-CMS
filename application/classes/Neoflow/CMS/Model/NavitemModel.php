<?php

namespace Neoflow\CMS\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\ORM\EntityCollection;
use Neoflow\Framework\ORM\EntityValidator;
use Neoflow\Framework\ORM\Repository;
use Neoflow\Framework\Core\AbstractModel as FrameworkAbstractModel;

class NavitemModel extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'navitems';

    /**
     * @var string
     */
    public static $primaryKey = 'navitem_id';

    /**
     * @var array
     */
    public static $properties = ['navitem_id', 'title', 'page_id',
        'parent_navitem_id', 'navigation_id', 'language_id',
        'position', 'is_active', ];

    /**
     * Get repository to fetch child navitems.
     *
     * @return Repository
     */
    public function childNavitems(): Repository
    {
        return $this->hasMany('\\Neoflow\\CMS\\Model\\NavitemModel', 'parent_navitem_id');
    }

    /**
     * Get page.
     *
     * @return PageModel|null
     */
    public function getPage()
    {
        $page = $this->page()->fetch();

        if ($page) {
            return $page;
        }

        return null;
    }

    /**
     * Get repository to fetch parent navitem.
     *
     * @return Repository
     */
    public function parentNavitem(): Repository
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\NavitemModel', 'parent_navitem_id');
    }

    /**
     * Get repository to fetch language.
     *
     * @return Repository
     */
    public function language(): Repository
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\LanguageModel', 'language_id');
    }

    /**
     * Get repository to fetch navigation.
     *
     * @return Repository
     */
    public function navigation(): Repository
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\NavigationModel', 'navigation_id');
    }

    /**
     * Get repository to fetch page.
     *
     * @return Repository
     */
    public function page(): Repository
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\PageModel', 'page_id');
    }

    /**
     * Save navigation item.
     *
     * @param bool $preventCacheClearing Prevent that the cached database results will get deleted
     *
     * @return bool
     */
    public function save(bool $preventCacheClearing = false): bool
    {
        $page = $this->page()->fetch();
        if (!$this->title) {
            $this->title = $page->title;
        }

        if (!$this->position) {
            $this->position = 1;
            $navigation = $this->navigation()->fetch();
            $lastNavitem = $navigation->navitems()
                ->where('parent_navitem_id', '=', $this->parent_navitem_id)
                ->where('language_id', '=', $this->language_id)
                ->orderByDesc('position')
                ->fetch();

            if ($lastNavitem) {
                $this->position = $lastNavitem->position + 1;
            }
        }

        if (parent::save($preventCacheClearing)) {
            // Resave page to get URL updated when navitem is from main navigation
            if (1 == $this->navigation_id) {
                // Set startpage for first page when navitem is part of pagetree navigation
                if (1 == $this->position && !$this->parent_navitem_id) {
                    PageModel::repo()
                        ->where('is_startpage', '=', true)
                        ->where('page_id', '!=', $page->id())
                        ->where('language_id', '=', $this->language_id)
                        ->fetchAll()
                        ->each(function ($page) {
                            $page->is_startpage = false;
                            $page->save();
                        });

                    $page->is_startpage = true;
                }

                $page->save();
                $page->saveUrl();
            }

            // Delete cached navigation trees
            $this->cache()->deleteByTag('navigation-trees');

            return true;
        }

        return false;
    }

    /**
     * Delete navitem.
     *
     * @return bool
     */
    public function delete(): bool
    {
        if (1 === $this->navigation_id) {
            $page = $this->page()->fetch();
            if ($page) {
                $page->delete();
            }
        }

        $childNavitems = $this->childNavitems()->fetchAll();
        if ($childNavitems) {
            foreach ($childNavitems as $childNavitem) {
                $childNavitem->delete();
            }
        }

        // Delete cached navigation trees
        $this->cache()->deleteByTag('navigation-trees');

        return parent::delete();
    }

    /**
     * Validate navitem.
     *
     * @return bool
     */
    public function validate(): bool
    {
        $validator = new EntityValidator($this);

        $validator
            ->callback(function ($parent_navitem_id, $navitem) {
                $forbiddenNavitemIds = $navitem->childNavitems()
                    ->orderByAsc('position')
                    ->fetchAll()
                    ->map(function ($navitem) {
                        return $navitem->id();
                    });

                if ($navitem->id()) {
                    $forbiddenNavitemIds[] = $navitem->id();
                }

                return !in_array($parent_navitem_id, $forbiddenNavitemIds);
            }, 'The navigation item or child items cannot be the parent item', [$this])
            ->set('parent_navitem_id', 'Top navitem');

        $validator
            ->required()
            ->set('page_id', 'Page');

        return (bool) $validator->validate();
    }

    /**
     * Set navigation item value.
     *
     * @param string $property Navigation item property
     * @param mixed  $value    Navigation item value
     * @param bool   $silent   Set TRUE to prevent the tracking of the change
     *
     * @return self
     */
    public function set(string $property, $value = null, bool $silent = false): FrameworkAbstractModel
    {
        // Clean parent navigation item id to null
        if ('parent_navitem_id' === $property && !$value) {
            $value = null;
        }

        return parent::set($property, $value, $silent);
    }

    /**
     * Toggle activation.
     *
     * @return self
     */
    public function toggleActivation(): self
    {
        if ($this->is_active) {
            $this->is_active = false;
        } else {
            $this->is_active = true;
        }

        return $this;
    }

    /**
     * Get child navitems.
     *
     * @return EntityCollection
     */
    public function getChildNavitems(): EntityCollection
    {
        return $this->childNavitems()->fetchAll();
    }
}
