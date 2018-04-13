<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('All themes'); ?>
            </h4>

            <table class="datatable table display responsive no-wrap" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th data-priority="0" data-order="true">
                            <?= translate('Name'); ?>
                        </th>
                        <th data-priority="3">
                            <?= translate('Version'); ?>
                        </th>
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
                    <?php foreach ($themes as $theme) {
    ?>
                        <tr>
                            <td>
                                <a href="<?= generate_url('backend_theme_view', ['id' => $theme->id()]); ?>">
                                    <?= translate($theme->name, [], false, false, false); ?>
                                </a>
                            </td>
                            <td>
                                <?= $theme->version; ?>
                            </td>
                            <td>
                                <?php
                                switch ($theme->type) {
                                    case 'backend':
                                        echo translate('Backend');
                                        break;
                                    case 'frontend':
                                        echo translate('Frontend');
                                } ?>
                            </td>
                            <td>
                                <?= translate($theme->description, [], false, false, false); ?>
                            </td>
                            <td class="text-right nowrap">
                                <a href="<?= generate_url('backend_theme_view', ['id' => $theme->id()]); ?>" class="btn btn-outline-light btn-sm btn-icon-left d-none d-xl-inline-block" title="<?= translate('Show theme'); ?>">
                                    <span class="btn-icon">
                                        <i class="fa fa-info"></i>
                                    </span>
                                    <?= translate('Show'); ?>
                                </a>
                                <a href="<?= generate_url('backend_theme_delete', ['id' => $theme->id()]); ?>" class="btn btn-primary btn-sm confirm-modal" data-message="<?= translate('Are you sure you want to uninstall it?'); ?>" title="<?= translate('Uninstall theme'); ?>">
                                    <i class="fa fa-fw fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php
}

                    ?>
                </tbody>
            </table>
        </div>

    </div>
    <div class="col-xl-5">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Install theme'); ?>
            </h4>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data" action="<?= generate_url('backend_theme_install'); ?>">
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
                <?= translate('Reload all themes'); ?>
            </h4>
            <div class="card-body">
                <p><?= translate('Reload themes information'); ?></p>
                <a href="<?= generate_url('backend_theme_reload_all'); ?>" class="btn btn-primary btn-icon-left confirm-modal" data-message="<?= translate('Are you sure you want to reload all themes?'); ?>" title="<?= translate('Reload theme', [], true); ?>">
                    <span class="btn-icon">
                        <i class="fa fa-sync"></i>
                    </span>
                    <?= translate('Reload'); ?>
                </a>
            </div>
        </div>

    </div>
</div>