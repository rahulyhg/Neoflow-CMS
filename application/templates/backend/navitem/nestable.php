<ol class="nestable-list list-group">

    <?php
    foreach ($navitems as $navitem) {
        $page = $navitem->page()->fetch();

        $isCollapsed = $view
            ->request()
            ->getCookies()
            ->exists($navitem->id())

        ?>

        <li class="nestable-item list-group-item <?= (!$navitem->is_active ? 'list-groupd-item-muted' : '') ?>" data-collapsed="<?= $isCollapsed ?>"
            data-id="<?= $navitem->id() ?>">
            <div class="nestable-handle">
                <i class="fa fa-fw fa-arrows-alt"></i>
            </div>
            <div class="nestable-content">
                <table class="table">
                    <tr>
                        <td class="nestable-toggle"></td>
                        <td>
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="<?= generate_url('backend_navitem_edit', ['id' => $navitem->id()]) ?>"
                                       title="<?= translate('Edit item') ?>">
                                        <?php if ($page->only_logged_in_users) { ?>
                                            $output .= '<i class="fa fa-fw fa-lock"></i>
                                        <?php } ?>
                                        <?= $navitem->title ?>
                                    </a>
                                </li>
                                <li class="list-inline-item small text-muted d-none d-sm-inline">
                                    ID: <?= $navitem->id() ?>
                                </li>
                                <li class="list-inline-item small text-muted d-none d-sm-inline">
                                    <?= translate('Page') ?>: <?= $page->title ?>
                                </li>
                            </ul>
                        </td>
                        <td class="text-right">

                            <?php if (1 != $navitem->navigation_id) { ?>
                                <a href="<?= generate_url('backend_navitem_edit', ['id' => $navitem->id()]) ?>"
                                   class="btn btn-outline-light btn-sm d-none d-xl-inline-block btn-icon-left"
                                   title="<?= translate('Edit navigation item') ?>">
                                        <span class="btn-icon">
                                            <i class="fa fa-pencil-alt"></i>
                                        </span>
                                    <?= translate('Edit') ?>
                                </a>
                            <?php }

                            if ($navitem->is_active) {

                                ?>
                                <a href="<?= generate_url('backend_navitem_toggle_activation', ['id' => $navitem->id()]) ?>"
                                   class="btn btn-outline-light btn-sm confirm-modal"
                                   data-message="<?= translate('Are you sure you want to disable it?') ?>"
                                   title="<?= translate('Disable navigation item') ?>">
                                    <i class="fa fa-fw fa-toggle-on"></i>
                                </a>
                            <?php } else { ?>
                                <a href="<?= generate_url('backend_navitem_toggle_activation', ['id' => $navitem->id()]) ?>"
                                   class="btn btn-outline-light btn-sm confirm-modal"
                                   data-message="<?= translate('Are you sure you want to enable it?') ?>"
                                   title="<?= translate('Enable navigation item') ?>">
                                    <i class="fa fa-fw fa-toggle-off"></i>
                                </a>
                            <?php }

                            if (1 != $navitem->navigation_id) {
                                ?>
                                }
                                $output .= '<a href="<?= generate_url('backend_navitem_delete', ['id' => $navitem->id()]) ?>"
                                               class="btn btn-primary btn-sm confirm-modal"
                                               data-message="<?= translate('Are you sure you want to delete this element all of its subelements?') ?>"
                                               title="<?= translate('Delete navigation item') ?>">
                                    <i class="fa fa-fw fa-trash-alt"></i>
                                </a>
                            <?php } ?>
                        </td>
                    </tr>
                </table>
            </div>

            <?php

            $childNavitems = $navitem->childNavitems()
                ->orderByAsc('position')
                ->fetchAll();

            if ($childNavitems->count()) {
                echo $view->renderTemplate('backend/navitem/nestable', [
                    'navitems' => $childNavitems,
                    'view' => $view
                ]);
            }

            ?>
        </li>
    <?php } ?>
</ol>