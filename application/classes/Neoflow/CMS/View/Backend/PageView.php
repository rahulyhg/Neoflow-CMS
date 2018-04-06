<?php
namespace Neoflow\CMS\View\Backend;

use Neoflow\Framework\ORM\EntityCollection;

class PageView extends NavitemView
{

    /**
     * Render navitems.
     *
     * @param EntityCollection $navitems
     *
     * @return string
     */
    public function renderNavitemNestable(EntityCollection $navitems)
    {
        $output = '';
        if ($navitems->count()) {
            $output .= '<ol class="nestable-list list-group" id="pages">';

            foreach ($navitems as $navitem) {
                $page = $navitem->page()->fetch();

                $output .= '<li class="nestable-item list-group-item ' . (!$page->is_active ? 'list-groupd-item-muted' : '') . '" data-collapsed="' . $this->app()->get('request')->getCookies()->exists($navitem->id()) . '" data-id="' . $navitem->id() . '" data-page-id="' . $page->id() . '">
                            <div class="nestable-handle">
                                <i class="fa fa-fw fa-arrows-alt"></i>
                            </div>
                            <div class="nestable-content">
                                <table class="table"><tr>
                                <td class="nestable-toggle"></td>';
                $output .= '<td>
                                    <ul class="list-inline">
                                        <li class="list-inline-item">
                                            <a href="' . generate_url('backend_section_index', ['page_id' => $navitem->page_id]) . '" title="' . translate('Page section', [], true) . '">';

                if ($page->only_logged_in_users) {
                    $output .= '<i class="fa fa-fw fa-lock"></i>';
                }
                $output .= $page->title . '
                                            </a>
                                        </li>
                                        <li class="list-inline-item small text-muted d-none d-md-inline">
                                            ID: ' . $page->id() . '
                                        </li>
                                    </ul>

                                </td>
                                <td class="text-right nowrap">
                                    <a href="' . generate_url('backend_section_index', ['page_id' => $page->id()]) . '" class="btn btn-outline-light btn-sm d-none d-xl-inline-block btn-icon-left" title="' . translate('Page section', [], true) . '">
                                        <span class="btn-icon">
                                            <i class="fa fa-th-list"></i>
                                        </span>
                                        ' . translate('Sections') . '
                                    </a>
                                    <a href="' . generate_url('backend_page_edit', ['id' => $page->id()]) . '" class="btn btn-outline-light btn-sm d-none d-xl-inline-block" title="' . translate('Edit page') . '">
                                        <i class="fa fa-fw fa-pencil-alt"></i>
                                    </a>';

                if ($page->is_active) {
                    $output .= ' <a href="' . generate_url('backend_page_toggle_activation', ['id' => $page->id()]) . '" class="btn btn-outline-light btn-sm d-none d-xl-inline-block confirm-modal" data-message="' . translate('Are you sure you want to disable it?') . '" title="' . translate('Disable page') . '">
                                    <i class="fa fa-fw fa-toggle-on"></i>
                                </a>';
                } else {
                    $output .= ' <a href="' . generate_url('backend_page_toggle_activation', ['id' => $page->id()]) . '" class="btn btn-outline-light btn-sm d-none d-xl-inline-block confirm-modal" data-message="' . translate('Are you sure you want to enable it?') . '" title="' . translate('Enable page') . '">
                                    <i class="fa fa-fw fa-toggle-off"></i>
                                </a>';
                }

                $output .= '
                                <a href="' . generate_url('backend_page_delete', ['id' => $page->id()]) . '" class="btn btn-primary btn-sm confirm-modal" data-message="' . translate('Are you sure you want to delete this page and all of its subpages?') . '" title="' . translate('Delete page') . '">
                                    <i class="fa fa-fw fa-trash-alt"></i>
                                </a>
                            </td>
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
