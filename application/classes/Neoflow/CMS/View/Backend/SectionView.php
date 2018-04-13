<?php

namespace Neoflow\CMS\View\Backend;

use Neoflow\Framework\ORM\EntityCollection;

class SectionView extends NavitemView
{
    /**
     * Render sections.
     *
     * @param EntityCollection $sections
     *
     * @return string
     */
    public function renderSectionNestable(EntityCollection $sections, $showBlockTitle = true)
    {
        $output = '';
        if ($sections->count()) {
            $output .= '<ol class="nestable-list list-group">';
            foreach ($sections as $section) {
                $module = $section->module()->fetch();

                $output .= '<li class="nestable-item list-group-item '.(!$section->is_active ? 'list-groupd-item-muted' : '').'" data-collapsed="'.$this->app()->get('request')->getCookies()->exists($section->id()).'" data-id="'.$section->id().'">
                            <div class="nestable-handle">
                                <i class="fa fa-fw fa-arrows-alt"></i>
                            </div>
                            <div class="nestable-content">
                                <table class="table"><tr>
                                    <td>
                                        <ul class="list-inline">
                                            <li class="list-inline-item">
                                                <a href="'.generate_url($module->backend_route, ['section_id' => $section->id()]).'" title="'.translate('Section content').'">
                                                    '.$module->name.'
                                                </a>
                                            </li>
                                            <li class="list-inline-item small text-muted d-none d-sm-inline">
                                                ID: '.$section->id().'
                                            </li>';

                if ($showBlockTitle) {
                    $block = $section->block()->fetch();
                    $output .= '<li class="list-inline-item small text-muted">
                                    '.translate('Block').': '.($block ? $block->title : translate('Not specified')).'
                                </li>';
                }

                $output .= '</ul>
                        </td>
                            <td class="text-right">
                                    <a href="'.generate_url($module->backend_route, ['section_id' => $section->id()]).'" class="btn btn-outline-light btn-sm d-none d-xl-inline-block btn-icon-left" title="'.translate('Section content').'">
                                        <span class="btn-icon">
                                            <i class="fa fa-columns"></i>
                                        </span>
                                        '.translate('Content').'
                                    </a>
                                    <a href="'.generate_url('backend_section_edit', ['id' => $section->id()]).'" class="btn btn-outline-light btn-sm" title="'.translate('Edit section').'">
                                          <i class="fa fa-fw fa-pencil-alt"></i>
                                    </a>';

                if ($section->is_active) {
                    $output .= ' <a href="'.generate_url('backend_section_toggle_activation', ['id' => $section->id()]).'" class="btn btn-outline-light btn-sm confirm-modal" data-message="'.translate('Are you sure you want to disable it?').'" title="'.translate('Disable section').'">
                                    <i class="fa fa-fw fa-toggle-on"></i>
                                </a>';
                } else {
                    $output .= ' <a href="'.generate_url('backend_section_toggle_activation', ['id' => $section->id()]).'" class="btn btn-outline-light btn-sm confirm-modal" data-message="'.translate('Are you sure you want to enable it?').'" title="'.translate('Enable section').'">
                                    <i class="fa fa-fw fa-toggle-off"></i>
                                </a>';
                }

                $output .= ' <a href="'.generate_url('backend_section_delete', ['id' => $section->id()]).'" class="btn btn-primary btn-sm confirm-modal" data-message="'.translate('Are you sure you want to delete this section and all of its content?').'" title="'.translate('Delete section').'">
                                        <i class="fa fa-fw fa-trash-alt"></i>
                                    </a>
                            </td>
                            </tr></table>
                        </div>
                        </li>';
            }
            $output .= '</ol>';
        }

        return $output;
    }
}
