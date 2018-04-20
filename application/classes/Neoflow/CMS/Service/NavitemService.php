<?php

namespace Neoflow\CMS\Service;

use Neoflow\CMS\Model\NavitemModel;
use Neoflow\CMS\Core\AbstractService;

class NavitemService extends AbstractService
{
    /**
     * Update navigation item order.
     *
     * @param array $order             Ordered items (based on navigation items)
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
        }

        return true;
    }
}
