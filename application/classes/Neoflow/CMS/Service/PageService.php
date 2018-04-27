<?php

namespace Neoflow\CMS\Service;

use Neoflow\CMS\Core\AbstractService;
use Neoflow\CMS\Model\NavitemModel;
use Neoflow\CMS\Model\PageModel;

class PageService extends AbstractService
{
    /**
     * Get all parent pages.
     *
     * @param PageModel $page
     * @param array     $parentPages
     *
     * @return array
     */
    public function getParentPages(PageModel $page, array $parentPages = []): array
    {
        $parentPage = $page->getParentPage();
        if ($parentPage) {
            $parentPages[] = $parentPage;
            $parentPages = $this->getParentPages($parentPage, $parentPages);
        }

        return $parentPages;
    }

    /**
     * Update page order.
     *
     * @param array $order             Ordered items (based on main navigation items)
     * @param int   $parent_navitem_id ID of parent navigation item
     *
     * @return bool
     */
    public function updateOrder(array $order, int $parent_navitem_id = null): bool
    {
        foreach ($order as $index => $item) {
            $navitem = NavitemModel::findById($item['id']);
            $navitem->position = ++$index;
            $navitem->parent_navitem_id = $parent_navitem_id;
            $navitem->save();

            if (isset($item['children'])) {
                $this->updateOrder($item['children'], $item['id']);
            }

            $page = PageModel::findById($navitem->page_id);
            $page->save();
        }

        return true;
    }
}
