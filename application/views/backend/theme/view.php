<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Show theme'); ?>
            </h4>
            <div class="card-body">

                <h3>
                    <?= $theme->name; ?> v<?= $theme->version; ?>
                </h3>

                <p><?= translate($theme->description, [], false, false, false); ?></p>

                <div class="row">
                    <div class="col-md-3">
                        <h4><?= translate('License'); ?></h4>
                        <p><?= $theme->license; ?></p>
                    </div>
                    <div class="col-md-3">
                        <h4><?= translate('Author'); ?></h4>
                        <p><?= $theme->author; ?></p>
                    </div>
                    <div class="col-md-6">
                        <h4><?= translate('Copyright'); ?></h4>
                        <p><?= $theme->copyright; ?></p>
                    </div>

                    <div class="col-md-3">
                        <h4><?= translate('Identifier'); ?></h4>
                        <p><?= $theme->identifier; ?></p>
                    </div>

                    <div class="col-md-3">
                        <h4><?= translate('Type'); ?></h4>
                        <p><?= $theme->type; ?></p>
                    </div>
                    <div class="col-md-6">
                        <h4><?= translate('Folder'); ?></h4>
                        <p><?= $theme->folder_name; ?></p>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <div class="col-xl-5">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Update theme'); ?>
            </h4>
            <div class="card-body">
                <p><?= translate('Please upload the latest update as a ZIP file to start the installation.'); ?></p>
                <form method="post" enctype="multipart/form-data" action="<?= generate_url('backend_theme_update'); ?>">
                    <input value="<?= $theme->id(); ?>" type="hidden" name="theme_id" />
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
                <?= translate('Reload theme'); ?>
            </h4>
            <div class="card-body">

                <p><?= translate('Reload themes information'); ?></p>
                <a href="<?= generate_url('backend_theme_reload', array('id' => $theme->id())); ?>" class="btn btn-primary btn-icon-left d-none d-xl-inline-block confirm-modal" data-message="<?= translate('Are you sure you want to reload it?'); ?>" title="<?= translate('Reload theme'); ?>">
                    <span class="btn-icon">
                        <i class="fa fa-sync"></i>
                    </span>
                    <?= translate('Reload'); ?>
                </a>

            </div>
        </div>

    </div>
</div>
