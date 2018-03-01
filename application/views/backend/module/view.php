<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Show module'); ?>
            </h4>
            <div class="card-body">

                <h3>
                    <?= $module->name; ?> v<?= $module->version; ?>
                </h3>

                <p><?= translate($module->description, [], false, false, false); ?></p>

                <div class="row">
                    <div class="col-md-3">
                        <h4><?= translate('License'); ?></h4>
                        <p><?= $module->license; ?></p>
                    </div>
                    <div class="col-md-3">
                        <h4><?= translate('Author'); ?></h4>
                        <p><?= $module->author; ?></p>
                    </div>
                    <div class="col-md-6">
                        <h4><?= translate('Copyright'); ?></h4>
                        <p><?= $module->copyright; ?></p>
                    </div>

                    <div class="col-md-3">
                        <h4><?= translate('Identifier'); ?></h4>
                        <p><?= $module->identifier; ?></p>
                    </div>

                    <div class="col-md-3">
                        <h4><?= translate('Type'); ?></h4>
                        <p><?= $module->type; ?></p>
                    </div>
                    <div class="col-md-6">
                        <h4><?= translate('Folder'); ?></h4>
                        <p><?= $module->folder_name; ?></p>
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="col-md-6">
                        <h4><?= translate('Backend route'); ?></h4>
                        <p><?= $module->backend_route ?: translate('None'); ?></p>

                        <h4><?= translate('Frontend route'); ?></h4>
                        <p><?= $module->frontend_route ?: translate('None'); ?></p>


                        <h4><?= translate('Manager class'); ?></h4>
                        <p><?= $module->manager_class; ?></p>
                    </div>
                    <div class="col-md-6">

                        <h4><?= translate('Required module', [], true); ?></h4>
                        <?php if (count($requiredModules)) {
    ?>
                            <ul>
                                <?php foreach ($requiredModules as $requiredModuleIdentifier => $requiredModule) {
        ?>
                                    <li>
                                        <?php if ($requiredModule) {
            ?>
                                            <a href="<?= generate_url('backend_module_view', array('id' => $requiredModule->id())); ?>"><?= $requiredModule->name; ?></a><span class="text-success"><i class="fa fa-fw fa-check"></i></span>
                                        <?php
        } else {
            ?>
                                                <?= $requiredModuleIdentifier; ?><span class="text-danger"><i class="fa fa-fw fa-times"></i></span>
                                            <?php
        } ?>
                                    </li>
                                <?php
    } ?>
                            </ul>

                            <?php
} else {
        echo translate('None');
    }

                        ?>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <div class="col-xl-5">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Update module'); ?>
            </h4>
            <div class="card-body">
                <p><?= translate('Please upload the latest update as a ZIP file to start the installation.'); ?></p>
                <form method="post" enctype="multipart/form-data" action="<?= generate_url('backend_module_update'); ?>">
                    <input value="<?= $module->id(); ?>" type="hidden" name="module_id" />
                    <div class="form-group row">
                        <label for="inputUpdatePackage" class="col-sm-3 col-form-label">
                            <?= translate('Package'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input type="file" name="file" data-allowed-File-Extensions="zip" data-allowed-File-Size="<?= ((int) ini_get('upload_max_filesize')) * 1024 * 1024; ?>" id="inputUpdatePackage" required class="form-control" />
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
                                <?= translate('Install'); ?>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <div class="card">
            <h4 class="card-header">
                <?= translate('Reload module'); ?>
            </h4>
            <div class="card-body">

                <p><?= translate('Reload modules information'); ?></p>
                <a href="<?= generate_url('backend_module_reload', array('id' => $module->id())); ?>" class="btn btn-primary btn-icon-left d-none d-xl-inline-block confirm-modal" data-message="<?= translate('Are you sure you want to reload it?'); ?>" title="<?= translate('Reload module'); ?>">
                    <span class="btn-icon">
                        <i class="fa fa-sync"></i>
                    </span>
                    <?= translate('Reload'); ?>
                </a>

            </div>
        </div>

    </div>
</div>
