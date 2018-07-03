<?php

namespace Neoflow\CMS\View\Backend;

use Neoflow\CMS\View\BackendView;
use Neoflow\Framework\ORM\EntityCollection;

class NavitemView extends BackendView
{
    /**
     * Render navigation items as select options.
     *
     * @param EntityCollection $navitems List of navigation items
     * @param int              $level    Recursive level of rendering
     * @param array            $selected List of selected navigation items
     * @param array            $disabled List of disabled navigation items
     * @param string           $property Return property of navigation item
     *
     * @return string
     */
    public function renderNavitemOptions(EntityCollection $navitems, int $level = 0, array $selected = [], array $disabled = [], string $property = 'navitem_id'): string
    {
        $output = '';
        foreach ($navitems as $navitem) {
            $output .= '<option '.(in_array($navitem->$property, $disabled) ? 'disabled' : '').' '.(in_array($navitem->$property, $selected) ? 'selected' : '').' data-level="'.$level.'" value="'.$navitem->$property.'">'.$navitem->getPage()->title.'</option>';

            $childNavitems = $navitem->childNavitems()
                ->orderByAsc('position')
                ->fetchAll();

            if (in_array($navitem->$property, $disabled)) {
                $disabled = $childNavitems->map(function ($navitem) use ($property) {
                    return $navitem->$property;
                });
            }

            $output .= $this->renderNavitemOptions($childNavitems, $level + 1, $selected, $disabled);
        }

        return $output;
    }
}
