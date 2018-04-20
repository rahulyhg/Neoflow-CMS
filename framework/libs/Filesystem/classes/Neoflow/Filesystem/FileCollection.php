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
     * @param string $order Order (ASC or DESC)
     *
     * @return self
     */
    public function sortBySize(string $order = 'ASC'): self
    {
        $this->sort('getSize', $order);

        return $this;
    }

    /**
     * Sort files by extension.
     *
     * @param string $order Order (ASC or DESC)
     *
     * @return self
     */
    public function sortByExtension(string $order = 'ASC'): self
    {
        $this->sort('getExtension', $order);

        return $this;
    }
}
