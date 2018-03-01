<?php

namespace Neoflow\Filesystem;

/**
 * @method bool delete()                  Delete all files
 * @method self sortByName(string $order) Sort files by name
 */
class FileCollection extends Collection
{
    /**
     * Sort files by size.
     *
     * @param string $order
     *
     * @return self
     */
    public function sortBySize($order = 'ASC')
    {
        $this->sort('getSize', $order);

        return $this;
    }

    /**
     * Sort files by extension.
     *
     * @param string $order
     *
     * @return self
     */
    public function sortByExtension($order = 'ASC')
    {
        $this->sort('getExtension', $order);

        return $this;
    }
}
