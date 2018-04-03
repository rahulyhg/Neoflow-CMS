<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('All modules'); ?>
            </h4>

            <table class="datatable table display responsive no-wrap" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th data-priority="0" data-order="true">
                            <?= translate('Name'); ?>
                        </th>
                        <th data-priority="3">
                            <?= translate('Version'); ?></th>
                        <th data-priority="1">
                            <?= translate('Type'); ?>
                        </th>
                        <th class="none" data-priority="1">
                            <?= translate('Description'); ?>
                        </th>
                        <th data-orderable="false" data-filterable="false" data-priority="0"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($modules as $module) {
                        $requiredModuleStatus = $module->getRequiredModuleStatus();

                        ?>
                        <tr class="<?= ($module->is_active ?: 'table-muted'); ?>">
                            <td class="nowrap">
                                <a href="<?= generate_url('backend_module_view', array('id' => $module->id())); ?>">
                                    <?= translate($module->name, [], false, false, false); ?>
                                </a>
                                <span class="<?= ($requiredModuleStatus ? 'text-success' : 'text-danger'); ?>">
                                    <?php if ($requiredModuleStatus) {

                                        ?>
                                        <i class="fa fa-fw fa-check"></i>
                                        <?php
                                    } else {

                                        ?>
                                        <i class="fa fa-fw fa-times"></i>
                                    <?php }

                                    ?>
                                </span>
                            </td>
                            <td>
                                <?= $module->version; ?>
                            </td>
                            <td>
                                <?= $module->type; ?>
                            </td>
                            <td>
                                <?= translate($module->description, [], false, false, false); ?>
                            </td>
                            <td class="text-left-xs text-right nowrap">
                                <a href="<?= generate_url('backend_module_view', array('id' => $module->id())); ?>" class="btn btn-outline-light btn-sm btn-icon-left d-none d-xl-inline-block" title="<?= translate('Show module'); ?>">
                                    <span class="btn-icon">
                                        <i class="fa fa-info"></i>
                                    </span>
                                    <?= translate('Show'); ?>
                                </a>
                                <?php if ($module->is_active) {

                                    ?>
                                    <a href="<?= generate_url('backend_module_toggle_activation', array('id' => $module->id())); ?>" class="btn btn-outline-light btn-sm confirm-modal <?= ($module->is_core ? 'disabled' : ''); ?>" data-message="<?= translate('Are you sure you want to disable it?'); ?>" title="<?= translate('Disable module'); ?>">
                                        <i class="fa fa-fw fa-toggle-on"></i>
                                    </a>
                                    <?php
                                } else {

                                    ?>
                                    <a href="<?= generate_url('backend_module_toggle_activation', array('id' => $module->id())); ?>" class="btn btn-outline-light btn-sm confirm-modal <?= ($module->is_core ? 'disabled' : ''); ?>" data-message="<?= translate('Are you sure you want to enable it?'); ?>" title="<?= translate('Enable module'); ?>">
                                        <i class="fa fa-fw fa-toggle-off"></i>
                                    </a>
                                <?php }

                                ?>

                                <a href="<?= generate_url('backend_module_delete', array('id' => $module->id())); ?>" class="btn btn-primary btn-sm confirm-modal <?= ($module->is_core ? 'disabled' : ''); ?>" data-message="<?= translate('Are you sure you want to delete it?'); ?>" title="<?= translate('Uninstall module'); ?>">
                                    <i class="fa fa-fw fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php }

                    ?>
                </tbody>
            </table>

            <div class="dataTable_info_src">
                <ul class="list-inline small">
                    <li>
                        <span class="text-success"><i class="fa fa-fw fa-check"></i></span> = <?= translate('All required modules are available'); ?>
                    </li>
                    <li>
                        <span class="text-danger"><i class="fa fa-fw fa-times"></i></span> = <?= translate('At least one required module is unavailable'); ?>
                    </li>
                </ul>
            </div>
        </div>

    </div>
    <div class="col-xl-5">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Install module'); ?>
            </h4>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data" action="<?= generate_url('backend_module_install'); ?>">
                    <div class="form-group row">
                        <label for="inputPackage" class="col-sm-3 col-form-label">
                            <?= translate('Package'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input type="file" name="package" data-allowed-File-Extensions="zip" data-allowed-File-Size="<?= ((int) ini_get('upload_max_filesize')) * 1024 * 1024; ?>" id="inputPackage" required class="form-control" />
                            <ul class="list-unstyled form-text text-muted small mb-0">
                                <li>
                                    <?= translate('Maximum uploadable file size: {0}MB', [(int) ini_get('upload_max_filesize')]); ?>
                                </li>
                                <li>
                                    <?= translate('Allowed file extensions: {0}', ['zip']); ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9">
                            <button type="submit" class="btn btn-primary btn-icon-left">
                                <span class="btn-icon">
                                    <i class="fa fa-save"></i>
                                </span>
                                <?= translate('Upload'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <div class="card">
            <h4 class="card-header">
                <?= translate('Reload all modules'); ?>
            </h4>
            <div class="card-body">
                <p><?= translate('Reload modules information', [], true); ?></p>
                <a href="<?= generate_url('backend_module_reload_all'); ?>" class="btn btn-primary btn-icon-left confirm-modal" data-message="<?= translate('Are you sure you want to reload all modules?'); ?>" title="<?= translate('Reload module', [], true); ?>">
                    <span class="btn-icon">
                        <i class="fa fa-sync"></i>
                    </span>
                    <?= translate('Reload'); ?>
                </a>
            </div>
        </div>

    </div>
</div>

