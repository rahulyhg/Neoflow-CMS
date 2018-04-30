<?php

namespace Neoflow\Filesystem;

/**
 * @method bool delete()                  Delete all folders
 * @method self sortByName(string $order) Sort folders by name
 */
class FolderCollection extends Collection
{
    /**
     * Collection item type.
     *
     * @var string
     */
    protected static $className = '\\Neoflow\\Filesystem\\Folder';
}
