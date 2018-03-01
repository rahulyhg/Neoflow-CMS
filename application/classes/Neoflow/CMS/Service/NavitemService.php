<?php

namespace Neoflow\CMS\Service;

use Neoflow\CMS\Model\NavitemModel;
use Neoflow\CMS\Core\AbstractService;

class NavitemService extends AbstractService
{
    /**
     * Update navitem order.
     *
     * @param array $order
     * @param int   $parent_id
     *
     * @return bool
     */
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
        }

        return true;
    }
}
