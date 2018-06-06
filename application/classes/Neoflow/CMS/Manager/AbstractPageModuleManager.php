<?php

namespace Neoflow\CMS\Manager;

use Neoflow\CMS\Model\SectionModel;

abstract class AbstractPageModuleManager extends AbstractModuleManager
{
    /**
     * Add module to section.
     *
     * @param SectionModel $section Added section
     *
     * @return bool
     */
    abstract public function add(SectionModel $section): bool;

    /**
     * Remove module from section.
     *
     * @param SectionModel $section Removed section
     *
     * @return bool
     */
    abstract public function remove(SectionModel $section): bool;
}
