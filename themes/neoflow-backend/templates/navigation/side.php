<nav class="navbar navbar-expand-lg navbar-dark bg-secondary" id="sideNavigation">

    <ul class="navbar-nav sidenav-toggle-btn">
        <li class="nav-item">
            <a class="nav-link text-center" id="sidenavToggleLeftRightBtn"></a>
        </li>
    </ul>

    <div class="sidenav-brand">
        <?= $view->renderTemplate('brand'); ?>
    </div>

    <button class="sidenav-toggler navbar-toggler navbar-toggler-left" type="button" data-toggle="collapse" data-target="#sidenavCollapse">
        <i class="fa fa-bars" aria-hidden="true"></i>
    </button>

    <div class="collapse navbar-collapse" id="sidenavCollapse">
        <ul class="navbar-nav navbar-sidenav">
            <li class="nav-item<?= is_current_route('backend_dashboard*', ' active'); ?>">
                <a class="nav-link" href="<?= generate_url('backend_dashboard_index'); ?>">
                    <span class="nav-link-icon">
                        <i class="fa fa-fw fa-tachometer-alt"></i>
                    </span>
                    <span class="nav-link-text">
                        <?= translate('Dashboard'); ?>
                    </span>
                </a>
            </li>

            <?php if (has_permission('manage_pages') || has_permission('manage_navigations') || has_permission('manage_blocks')) {
    ?>
                <li class="nav-item<?= is_current_route(['backend_navigation*', 'backend_page*', 'backend_section*', 'pmod_*', 'backend_navitem*', 'backend_block*'], ' active'); ?>">
                    <a href="#content-menu" class="nav-link nav-link-collapse<?= is_current_route(['backend_navigation*', 'backend_page*', 'backend_section*', 'pmod_*', 'backend_navitem*', 'backend_block*'], '', ' collapsed'); ?>" data-toggle="collapse">
                        <span class="nav-link-icon">
                            <i class="fa fa-fw fa-copy"></i>
                        </span>
                        <span class="nav-link-text">
                            <?= translate('Content'); ?>
                        </span>
                    </a>
                    <ul id="content-menu" class="sidenav-second-level collapse<?= is_current_route(['backend_navigation*', 'backend_page*', 'backend_section*', 'pmod_*', 'backend_navitem*', 'backend_block*'], ' show'); ?>">
                        <?php if (has_permission('manage_pages')) {
        ?>
                            <li class="nav-item<?= is_current_route(['backend_page*', 'backend_section*', 'pmod*'], ' active'); ?>">
                                <a class="nav-link" href="<?= generate_url('backend_page_index'); ?>">
                                    <span class="nav-link-text">
                                        <?= translate('Page', [], true); ?>
                                    </span>
                                </a>
                            </li>
                            <?php
    }
    if (has_permission('manage_navigations')) {
        ?>
                            <li class="nav-item<?= is_current_route(['backend_navigation*', 'backend_navitem*'], ' active'); ?>">
                                <a class="nav-link" href="<?= generate_url('backend_navigation_index'); ?>">
                                    <span class="nav-link-text">
                                        <?= translate('Navigation', [], true); ?>
                                    </span>
                                </a>
                            </li>
                            <?php
    }
    if (has_permission('manage_blocks')) {
        ?>
                            <li class="nav-item<?= is_current_route(['backend_block*'], ' active'); ?>">
                                <a class="nav-link" href="<?= generate_url('backend_block_index'); ?>">
                                    <span class="nav-link-text">
                                        <?= translate('Block', [], true); ?>
                                    </span>
                                </a>
                            </li>
                        <?php
    } ?>
                    </ul>
                </li>
                <?php
}
            if (has_permission('manage_modules') || has_permission('manage_templates')) {
                ?>
                <li class="nav-item<?= is_current_route(['backend_module*', 'backend_theme*'], ' active'); ?>">
                    <a href="#extension-menu" class="nav-link nav-link-collapse<?= is_current_route(['backend_module*', 'backend_theme*'], '', ' collapsed'); ?>" data-toggle="collapse">
                        <span class="nav-link-icon">
                            <i class="fa fa-fw fa-puzzle-piece"></i>
                        </span>
                        <span class="nav-link-text">
                            <?= translate('Extension', [], true); ?>
                        </span>
                    </a>
                    <ul id="extension-menu" class="sidenav-second-level collapse<?= is_current_route(['backend_module*', 'backend_theme*'], ' show'); ?>">
                        <?php if (has_permission('manage_modules')) {
                    ?>
                            <li class="nav-item<?= is_current_route(['backend_module*'], ' active'); ?>">
                                <a class="nav-link" href="<?= generate_url('backend_module_index'); ?>">
                                    <span class="nav-link-text">
                                        <?= translate('Module', [], true); ?>
                                    </span>
                                </a>
                            </li>
                            <?php
                }
                if (has_permission('manage_templates')) {
                    ?>
                            <li class="nav-item<?= is_current_route(['backend_theme*'], ' active'); ?>">
                                <a class="nav-link" href="<?= generate_url('backend_theme_index'); ?>">
                                    <span class="nav-link-text">
                                        <?= translate('Theme', [], true); ?>
                                    </span>
                                </a>
                            </li>
                        <?php
                } ?>
                    </ul>
                </li>
                <?php
            }
            if (has_permission('manage_media')) {
                ?>
                <li class="nav-item<?= is_current_route(['backend_media*'], ' active'); ?>">
                    <a class="nav-link" href="<?= generate_url('backend_media_index'); ?>">
                        <span class="nav-link-icon">
                            <i class="fa fa-fw fa-images"></i>
                        </span>
                        <span class="nav-link-text">
                            <?= translate('Media', [], true); ?>
                        </span>
                    </a>
                </li>
                <?php
            }
            if (has_permission('settings')) {
                ?>
                <li class="nav-item<?= is_current_route(['backend_setting*'], ' active'); ?>">
                    <a class="nav-link" href="<?= generate_url('backend_setting_index'); ?>">
                        <span class="nav-link-icon">
                            <i class="fa fa-fw fa-cogs"></i>
                        </span>
                        <span class="nav-link-text">
                            <?= translate('Setting', [], true); ?>
                        </span>
                    </a>
                </li>
                <?php
            }
            if (has_permission('manage_users') || has_permission('manage_roles')) {
                ?>

                <li class="nav-item<?= is_current_route(['backend_user*', 'backend_role*'], ' active'); ?>">
                    <a href="#account-menu" class="nav-link nav-link-collapse<?= is_current_route(['backend_user*', 'backend_role*'], '', ' collapsed'); ?>" data-toggle="collapse">
                        <span class="nav-link-icon">
                            <i class="fa fa-fw fa-users"></i>
                        </span>
                        <span class="nav-link-text">
                            <?= translate('Account', [], true); ?>
                        </span>
                    </a>
                    <ul id="account-menu" class="sidenav-second-level collapse<?= is_current_route(['backend_user*', 'backend_role*'], ' show'); ?>">
                        <?php if (has_permission('manage_users')) {
                    ?>
                            <li class="nav-item<?= is_current_route('backend_user*', ' active'); ?>">
                                <a class="nav-link" href="<?= generate_url('backend_user_index'); ?>">
                                    <span class="nav-link-text">
                                        <?= translate('User', [], true); ?>
                                    </span>
                                </a>
                            </li>
                            <?php
                }
                if (has_permission('manage_roles')) {
                    ?>
                            <li class="nav-item<?= is_current_route('backend_role*', ' active'); ?>">
                                <a class="nav-link" href="<?= generate_url('backend_role_index'); ?>">
                                    <span class="nav-link-text">
                                        <?= translate('Role', [], true); ?>
                                    </span>
                                </a>
                            </li>
                        <?php
                } ?>
                    </ul>
                </li>
                <?php
            }
            if (has_permission('run_tools')) {
                ?>
                <li class="nav-item<?= is_current_route(['backend_tool*', 'tmod_*'], ' active'); ?>">
                    <a class="nav-link" href="<?= generate_url('backend_tool_index'); ?>">
                        <span class="nav-link-icon">
                            <i class="fa fa-fw fa-cubes"></i>
                        </span>
                        <span class="nav-link-text">
                            <?= translate('Tool', [], true); ?>
                        </span>
                    </a>
                </li>
                <?php
            }
            if (has_permission('maintenance')) {
                ?>
                <li class="nav-item<?= is_current_route('backend_maintenance*', ' active'); ?>">
                    <a class="nav-link" href="<?= generate_url('backend_maintenance_index'); ?>">
                        <span class="nav-link-icon">
                            <i class="fa fa-fw fa-wrench"></i>
                        </span>
                        <span class="nav-link-text">
                            <?= translate('Maintenance', [], true); ?>
                        </span>
                    </a>
                </li>
                <?php
            }

            ?>
        </ul>
    </div>

    <div class="sidenav-content">
        <ul class="list-unstyled">
            <li>Version <?= $view->config()->get('app')->get('version'); ?></li>
        </ul>
        <ul class="list-unstyled small">
            <li>
                <?= translate('Logged in as {0}', [$view->getService('auth')->getUser()->getFullname()]); ?>
            </li>
            <li>
                <?= translate('Session timeout in {0}', ['<span class="timer" id="sessionTimer" data-timeout-callback="showReloginModal()" data-time="'.$view->config()->get('session')->get('lifetime').'">'.gmdate('H:i:s', $view->config()->get('session')->get('lifetime')).'</span>'], true, false); ?>
            </li>
            <li>
                <?= translate('Page loaded in {0} seconds', [round($view->getExecutionTime(), 3)]); ?>
            </li>
        </ul>
    </div>
</nav>