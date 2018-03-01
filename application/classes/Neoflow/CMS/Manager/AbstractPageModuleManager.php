<?php

namespace Neoflow\CMS\Manager;

use Neoflow\CMS\Model\SectionModel;

abstract class AbstractPageModuleManager extends AbstractModuleManager
{
    /**
     * Add module to section.
     */
    abstract public function add(SectionModel $section): bool;

    /**
     * Remove module from section.
     */
    abstract public function remove(SectionModel $section): bool;
}
