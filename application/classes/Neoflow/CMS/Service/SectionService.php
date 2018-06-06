<?php

namespace Neoflow\CMS\Service;

use Neoflow\CMS\Core\AbstractService;
use Neoflow\CMS\Model\SectionModel;

class SectionService extends AbstractService
{
    /**
     * Update section order.
     *
     * @param array $order Ordered items (based on blocks)
     *
     * @return bool
     */
    public function updateOrder(array $order): bool
    {
        foreach ($order as $index => $item) {
            $section = SectionModel::findById($item['id']);
            if ($item['listId']) {
                $section->block_id = $item['listId'];
            }
            $section->position = ++$index;
            $section->save();
        }

        return true;
    }
}
