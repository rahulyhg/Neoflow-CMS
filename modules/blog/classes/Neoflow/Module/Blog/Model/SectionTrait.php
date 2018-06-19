<?php

namespace Neoflow\Module\Blog\Model;

use Neoflow\CMS\Model\SectionModel;
use Neoflow\Framework\ORM\Repository;

trait SectionTrait
{
    /**
     * Get repository to fetch section.
     *
     * @return Repository
     */
    public function section(): Repository
    {
        return $this->belongsTo('Neoflow\\CMS\\Model\\SectionModel', 'section_id');
    }

    /**
     * Get section.
     *
     * @return SectionModel|null
     */
    public function getSection()
    {
        $section = $this->section()->fetch();

        if ($section) {
            return $section;
        }

        return null;
    }
}
