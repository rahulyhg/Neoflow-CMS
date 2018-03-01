<?php

namespace Neoflow\CMS\Service;

use Neoflow\CMS\Model\NavitemModel;
use Neoflow\CMS\Model\PageModel;
use Neoflow\CMS\Core\AbstractService;

class PageService extends AbstractService
{
    public function getParentPages(PageModel $page, array $parentPages = []): array
    {
        $parentPage = $page->getParentPage();
        if ($parentPage) {
            $parentPages[] = $parentPage;
            $parentPages = $this->getParentPages($parentPage, $parentPages);
        }

        return $parentPages;
    }

    public function updateOrder(array $order, $parent_id = null)
    {
        foreach ($order as $index => $item) {
            $navitem = NavitemModel::findById($item['id']);
            $navitem->position = ++$index;
            $navitem->parent_navitem_id = $parent_id;
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
