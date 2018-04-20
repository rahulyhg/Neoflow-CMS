<?php
namespace Neoflow\CMS\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\Core\AbstractModel as FrameworkAbstractModel;
use Neoflow\Framework\ORM\EntityCollection;
use Neoflow\Framework\ORM\EntityValidator;
use Neoflow\Framework\ORM\Repository;

class NavigationModel extends AbstractModel
{

    /**
     * @var string
     */
    public static $tableName = 'navigations';

    /**
     * @var string
     */
    public static $primaryKey = 'navigation_id';

    /**
     * @var array
     */
    public static $properties = ['navigation_id', 'title', 'description', 'navigation_key'];

    /**
     * Get repository to fetch navitems.
     *
     * @return Repository
     */
    public function navitems(): Repository
    {
        return $this->hasMany('\\Neoflow\\CMS\\Model\\NavitemModel', 'navigation_id');
    }

    /**
     * Delete navigation.
     *
     * @return bool
     */
    public function delete(): bool
    {
        // Prevent delete of main navigation
        if (1 != $this->id()) {
            NavitemModel::deleteAllByColumn('navigation_id', $this->id());

            return parent::delete();
        }

        return false;
    }

    /**
     * Validate navigation.
     *
     * @return bool
     */
    public function validate(): bool
    {
        $validator = new EntityValidator($this);

        $validator
            ->required()
            ->set('title', translate('Title'));

        $validator
            ->callback(function ($navigationKey, $id) {
                return 0 === NavigationModel::repo()
                    ->where('navigation_key', '=', $navigationKey)
                    ->where('navigation_id', '!=', $id)
                    ->count();
            }, '{0} has to be unique', [$this->id()])
            ->set('navigation_key', 'Key');

        return (bool) $validator->validate();
    }

    /**
     * Set navigation value.
     *
     * @param string $property Navigation property
     * @param mixed  $value  Property value
     * @param bool   $silent Set TRUE to prevent the tracking of the change
     *
     * @return self
     *
     * @throws RuntimeException
     */
    protected function set(string $property, $value = null, bool $silent = false): FrameworkAbstractModel
    {
        if ('navigation_key' === $property) {
            $value = slugify($value);
        }

        return parent::set($property, $value, $silent);
    }

    /**
     * Build navigation tree.
     *
     * @param EntityCollection $navitems
     * @param int              $maxLevel
     * @param bool             $strict
     * @param int              $currentLevel
     *
     * @return array
     */
    protected function buildNavigationTree(EntityCollection $navitems, int $maxLevel = 5, bool $strict = true, int $currentLevel = 0): array
    {
        $navigationTree = [];

        if ($strict) {
            $navitems
                ->where('is_active', true)
                ->sort('position');
        }

        foreach ($navitems as $navitem) {
            // Get page of current navitem
            $page = $navitem->page()->fetch();

            // Check page access
            if (!$strict || ($page->isAccessible() && $navitem->is_active)) {
                // Get children of current navitem
                $navitemChildren = $navitem->childNavitems()->orderByAsc('position')->fetchAll();

                // Get navigation tree from children of current navitem
                $children = $this->buildNavigationTree($navitemChildren, $maxLevel, $strict, $currentLevel + 1);

                // Detect status of current navitem
                $status = '';
                if ($this->app()->get('page')) {
                    if ($this->app()->get('page')->id() == $navitem->page_id) {
                        $status = 'active';
                    } else {
                        foreach ($children as $child) {
                            if (in_array($child['status'], ['active', 'active-parent'])) {
                                $status = 'active-parent';
                                break;
                            }
                        }
                    }
                }

                $navigationTree[] = [
                    'title' => $navitem->title,
                    'url' => $page->getUrl(),
                    'relative_url' => $page->getRelativeUrl(true, true),
                    'level' => $currentLevel,
                    'status' => $status,
                    'children' => $maxLevel > $currentLevel + 1 ? $children : [],
                    'navitem' => $navitem->setReadOnly(),
                    'page' => $page->setReadOnly(),
                ];
            }
        }

        return $navigationTree;
    }

    /**
     * Get navigation tree.
     *
     * @param int  $startLevel
     * @param int  $maxLevel
     * @param bool $strict
     * @param int  $language_id
     *
     * @return array
     */
    public function getNavigationTree(int $startLevel = 0, int $maxLevel = 5, bool $strict = true, int $language_id = 0): array
    {
        $navitems = $this->navitems()
            ->where('parent_navitem_id', '=', null)
            ->where('language_id', '=', $language_id)
            ->orderByAsc('position')
            ->fetchAll();

        $navigationTree = $this->buildNavigationTree($navitems, $maxLevel, $strict);

        for ($i = 1; $i <= $startLevel; ++$i) {
            foreach ($navigationTree as $navTreeItem) {
                if ($navTreeItem['level'] < $startLevel && in_array($navTreeItem['status'], ['active', 'active-parent'])) {
                    $navigationTree = $navTreeItem['children'];
                    break;
                }
            }
        }

        return $navigationTree;
    }
}
