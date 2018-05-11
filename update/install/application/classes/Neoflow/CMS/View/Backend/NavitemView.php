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

    /**
     * Render navigation items for nestable (drag'n drop list).
     *
     * @param EntityCollection $navitems List of navigation items
     *
     * @return string
     */
    public function renderNavitemNestable(EntityCollection $navitems): string
    {
        $output = '';
        if ($navitems->count()) {
            $output .= '<ol class="nestable-list list-group">';

            foreach ($navitems as $navitem) {
                $page = $navitem->page()->fetch();

                $output .= '<li class="nestable-item list-group-item '.(!$navitem->is_active ? 'list-groupd-item-muted' : '').'" data-collapsed="'.$this->app()->get('request')->getCookies()->exists($navitem->id()).'" data-id="'.$navitem->id().'">
                            <div class="nestable-handle">
                                <i class="fa fa-fw fa-arrows-alt"></i>
                            </div>
                            <div class="nestable-content">
                                <table class="table"><tr>
                                    <td class="nestable-toggle"></td>
                                    <td>
                                        <ul class="list-inline">
                                            <li class="list-inline-item">
                                                <a href="'.generate_url('backend_navitem_edit', ['id' => $navitem->id()]).'" title="'.translate('Edit item').'">';
                if ($page->only_logged_in_users) {
                    $output .= '<i class="fa fa-fw fa-lock"></i>';
                }
                $output .= $navitem->title.'</a>
                                            </li>
                                            <li class="list-inline-item small text-muted d-none d-sm-inline">
                                                ID: '.$navitem->id().'
                                            </li>
                                            <li class="list-inline-item small text-muted d-none d-sm-inline">
                                                '.translate('Page').': '.$page->title.'
                                            </li>
                                        </ul>
                                    </td>
                                    <td class="text-right">';

                if (1 != $navitem->navigation_id) {
                    $output .= '<a href="'.generate_url('backend_navitem_edit', ['id' => $navitem->id()]).'" class="btn btn-outline-light btn-sm d-none d-xl-inline-block btn-icon-left" title="'.translate('Edit item').'">
                                        <span class="btn-icon">
                                            <i class="fa fa-pencil-alt"></i>
                                        </span>
                                        '.translate('Edit').'
                                    </a>';
                }

                if ($navitem->is_active) {
                    $output .= ' <a href="'.generate_url('backend_navitem_toggle_activation', ['id' => $navitem->id()]).'" class="btn btn-outline-light btn-sm confirm-modal" data-message="'.translate('Are you sure you want to disable it?').'" title="'.translate('Disable item').'">
                                    <i class="fa fa-fw fa-toggle-on"></i>
                                </a> ';
                } else {
                    $output .= ' <a href="'.generate_url('backend_navitem_toggle_activation', ['id' => $navitem->id()]).'" class="btn btn-outline-light btn-sm confirm-modal" data-message="'.translate('Are you sure you want to enable it?').'" title="'.translate('Enable item').'">
                                    <i class="fa fa-fw fa-toggle-off"></i>
                                </a> ';
                }

                if (1 != $navitem->navigation_id) {
                    $output .= '<a href="'.generate_url('backend_navitem_delete', ['id' => $navitem->id()]).'" class="btn btn-primary btn-sm confirm-modal" data-message="'.translate('Are you sure you want to delete this element all of its subelements?').'" title="'.translate('Delete item').'">
                                <i class="fa fa-fw fa-trash-alt"></i>
                            </a>';
                }
                $output .= '</td>
                        </tr></table>
                        </div>';

                $childNavitems = $navitem->childNavitems()
                    ->orderByAsc('position')
                    ->fetchAll();

                $output .= $this->renderNavitemNestable($childNavitems);

                $output .= '</li>';
            }
            $output .= '</ol>';
        }

        return $output;
    }
}
