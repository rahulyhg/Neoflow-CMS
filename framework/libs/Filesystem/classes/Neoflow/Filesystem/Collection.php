<?php

namespace Neoflow\Filesystem;

use Neoflow\Framework\Common\Collection as FrameworkCollection;

class Collection extends FrameworkCollection
{
    /**
     * Delete all files and folders.
     *
     * @return bool
     */
    public function delete()
    {
        $result = true;
        $this->each(function ($item) use ($result) {
            if (!$item->delete()) {
                $result = false;
            }
        });

        return $result;
    }

    /**
     * Sort files and folders by name.
     *
     * @param string $order
     *
     * @return self
     */
    public function sortByName($order = 'ASC')
    {
        $this->sort('getName', $order);

        return $this;
    }
}
