<div class="row">    <?php if (has_permission('manage_pages') || has_permission('manage_navigations') || has_permission('manage_blocks')) { ?>        <div class="col-md-6 col-lg-4">            <div class="card">                <h4 class="card-header bg-secondary text-white d-flex flex-row">                    <i class="fa fa-5x fa-copy"></i>                    <span class="text-right ml-4 font-weight-normal"><?= translate('Dashboard content title'); ?></span>                </h4>                <ul class="nav nav-tabs flex-column flex-sm-row">                    <?php if (has_permission('manage_pages')) { ?>                        <li class="nav-item text-sm-center">                            <a class="nav-link" href="<?= generate_url('backend_page_index'); ?>">                                <?= translate('Page', [], true); ?>                            </a>                        </li>                        <?php                    }                    if (has_permission('manage_navigations')) {                        ?>                        <li class="nav-item text-sm-center">                            <a class="nav-link" href="<?= generate_url('backend_navigation_index'); ?>">                                <?= translate('Navigation', [], true); ?>                            </a>                        </li>                        <?php                    }                    if (has_permission('manage_blocks')) {                        ?>                        <li class="nav-item text-sm-center">                            <a class="nav-link" href="<?= generate_url('backend_block_index'); ?>">                                <?= translate('Block', [], true); ?>                            </a>                        </li>                    <?php } ?>                </ul>            </div>        </div>        <?php    }    if (has_permission('manage_modules') || has_permission('manage_themes')) {        ?>        <div class="col-md-6 col-lg-4">            <div class="card">                <h4 class="card-header bg-secondary text-white d-flex flex-row">                    <i class="fa fa-5x fa-puzzle-piece"></i>                    <span class="text-right ml-4 font-weight-normal"><?= translate('Dashboard extension title'); ?></span>                </h4>                <ul class="nav nav-tabs flex-column flex-sm-row">                    <?php if (has_permission('manage_modules')) { ?>                        <li class="nav-item text-sm-center">                            <a class="nav-link" href="<?= generate_url('backend_module_index'); ?>">                                <?= translate('Module', [], true); ?>                            </a>                        </li>                        <?php                    }                    if (has_permission('manage_themes')) {                        ?>                        <li class="nav-item text-sm-center">                            <a class="nav-link" href="<?= generate_url('backend_theme_index'); ?>">                                <?= translate('Theme', [], true); ?>                            </a>                        </li>                    <?php } ?>                </ul>            </div>        </div>        <?php    }    if (has_permission('manage_media')) {        ?>        <div class="col-md-6 col-lg-4">            <div class="card">                <h4 class="card-header bg-secondary text-white d-flex flex-row">                    <i class="fa fa-5x fa-images"></i>                    <span class="text-right ml-4 font-weight-normal"><?= translate('Dashboard media title'); ?></span>                </h4>                <ul class="nav nav-tabs flex-column flex-sm-row">                    <li class="nav-item text-sm-center">                        <a class="nav-link" href="<?= generate_url('backend_media_index'); ?>">                            <?= translate('Media', [], true); ?>                        </a>                    </li>                </ul>            </div>        </div>        <?php    }    if (has_permission('settings') || has_permission('maintenance')) {        ?>        <div class="col-md-6 col-lg-4">            <div class="card">                <h4 class="card-header bg-secondary text-white d-flex flex-row">                    <i class="fa fa-5x fa-cogs"></i>                    <span class="text-right ml-4 font-weight-normal"><?= translate('Dashboard setting title'); ?></span>                </h4>                <ul class="nav nav-tabs flex-column flex-sm-row">                    <?php if (has_permission('settings')) { ?>                        <li class="nav-item text-sm-center">                            <a class="nav-link" href="<?= generate_url('backend_setting_index'); ?>">                                <?= translate('Setting', [], true); ?>                            </a>                        </li>                        <?php                    }                    if (has_permission('maintenance')) {                        ?>                        <li class="nav-item text-sm-center">                            <a class="nav-link" href="<?= generate_url('backend_maintenance_index'); ?>">                                <?= translate('Maintenance', [], true); ?>                            </a>                        </li>                    <?php }                    ?>                </ul>            </div>        </div>        <?php    }    if (has_permission('manage_users') || has_permission('manage_roles')) {        ?>        <div class="col-md-6 col-lg-4">            <div class="card">                <h4 class="card-header bg-secondary text-white d-flex flex-row">                    <i class="fa fa-5x fa-users"></i>                    <span class="text-right ml-4 font-weight-normal"><?= translate('Dashboard account title'); ?></span>                </h4>                <ul class="nav nav-tabs flex-column flex-sm-row">                    <?php if (has_permission('manage_users')) {                        ?>                        <li class="nav-item text-sm-center">                            <a class="nav-link" href="<?= generate_url('backend_user_index'); ?>">                                <?= translate('User', [], true); ?>                            </a>                        </li>                        <?php                    }                    if (has_permission('manage_roles')) {                        ?>                        <li class="nav-item text-sm-center">                            <a class="nav-link" href="<?= generate_url('backend_role_index'); ?>">                                <?= translate('Role', [], true); ?>                            </a>                        </li>                    <?php } ?>                </ul>            </div>        </div>        <?php    }    if (has_permission('run_tools')) {        ?>        <div class="col-md-6 col-lg-4">            <div class="card">                <h4 class="card-header bg-secondary text-white d-flex flex-row">                    <i class="fa fa-5x fa-cubes"></i>                    <span class="text-right ml-4 font-weight-normal"><?= translate('Dashboard tool title'); ?></span>                </h4>                <ul class="nav nav-tabs flex-column flex-sm-row">                    <li class="nav-item text-sm-center">                        <a class="nav-link" href="<?= generate_url('backend_tool_index'); ?>">                            <?= translate('Tool', [], true); ?>                        </a>                    </li>                </ul>            </div>        </div>    <?php } ?></div><div class="card">    <h4 class="card-header">        <?= translate('System information', [], true); ?>    </h4>    <table class="table">        <tbody>            <tr>                <td>                    PHP Version                </td>                <td>                    <?= phpversion(); ?>                </td>            </tr>            <tr>                <td class="nowrap">                    <?= translate('PHP settings'); ?>                </td>                <td>                    max_execution_time (<?= ini_get('max_execution_time'); ?>),                    file_uploads (<?= (ini_get('file_uploads') ? 'on' : 'off'); ?>),                    upload_max_filesize (<?= ini_get('upload_max_filesize'); ?>),                    max_file_uploads (<?= ini_get('max_file_uploads'); ?>),                    post_max_size (<?= ini_get('post_max_size'); ?>),                    memory_limit (<?= ini_get('memory_limit'); ?>)                </td>            </tr>            <tr>                <td class="nowrap">                    <?= translate('PHP extensions'); ?>                </td>                <td>                    <?= implode(', ', get_loaded_extensions()); ?>                </td>            </tr>            <tr>                <td>                    System                </td>                <td>                    <?= $phpinfo['System']; ?>                </td>            </tr>            <tr>                <td>                    Server API                </td>                <td>                    <?= $phpinfo['Server API']; ?>                </td>            </tr>        </tbody>    </table></div>