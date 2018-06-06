<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Directory'); ?>
            </h4>
            <?php if ($folders->count() || $files->count()) {
    ?>
                <table class="datatable table display responsive no-wrap" data-ordering="false" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th data-priority="0">
                                <?= translate('Name'); ?>
                            </th>
                            <th data-priority="1">
                                <?= translate('Size'); ?>
                            </th>
                            <th data-filterable="false" data-priority="0"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($folders as $folder) {
        ?>
                            <tr>
                                <td>
                                    <i class="fa fa-fw fa-folder" aria-hidden="true"></i>
                                    <a class="hyphens" href="<?= generate_url('backend_media_index', ['dir' => normalize_path($relativeFolderPath.'/'.$folder->getName(), true)]); ?>" title="<?= translate('Open folder'); ?>">
                                        <?= $folder->getName(); ?>
                                    </a>
                                </td>
                                <td></td>
                                <td class="text-left-xs text-right nowrap">
                                    <span class="d-none d-sm-inline">
                                        <a href="<?= generate_url('backend_media_rename_folder', ['dir' => normalize_path($relativeFolderPath.'/'.$folder->getName(), true)]); ?>" class="btn btn-outline-light btn-icon-left btn-sm" title="<?= translate('Rename folder'); ?>">
                                            <span class="btn-icon">
                                                <i class="fa fa-pencil-alt"></i>
                                            </span>
                                            <?= translate('Rename'); ?>
                                        </a>
                                    </span>
                                    <span class="d-inline d-sm-none">
                                        <a href="<?= generate_url('backend_media_rename_folder', ['dir' => normalize_path($relativeFolderPath.'/'.$folder->getName(), true)]); ?>" class="btn btn-outline-light btn-sm" title="<?= translate('Rename folder'); ?>">
                                            <i class="fa fa-fw fa-pencil-alt"></i>
                                        </a>
                                    </span>
                                    <a href="<?= generate_url('backend_media_delete_folder', ['dir' => normalize_path($relativeFolderPath.'/'.$folder->getName(), true)]); ?>" class="btn btn-primary btn-sm confirm-modal" data-message="<?= translate('Are you sure you want to delete it?'); ?>" title="<?= translate('Delete folder'); ?>">
                                        <i class="fa fa-fw fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php
    }
    foreach ($files as $file) {
        ?>
                            <tr>
                                <td>
                                    <i class="fa d-none d-sm-inline-block fa-fw" aria-hidden="true"></i>
                                    <a class="preview hyphens" href="<?= generate_url('backend_media_download', ['file' => normalize_url($relativeFolderPath.'/'.$file->getName())]); ?>" data-preview="<?= normalize_url($view->config()->getUrl($path.'/'.$file->getName())); ?>" title="<?= translate('Preview'); ?>">
                                        <?= $file->getName(); ?>
                                    </a>
                                </td>
                                <td>
                                    <?= $file->getFormattedSize(); ?>
                                </td>
                                <td class="text-left-xs text-right nowrap">
                                    <a href="<?= generate_url('backend_media_rename_file', ['file' => normalize_path($relativeFolderPath.'/'.$file->getName(), true)]); ?>" class="btn btn-outline-light btn-icon-left btn-sm d-none d-sm-inline-block" title="<?= translate('Rename file'); ?>">
                                        <span class="btn-icon">
                                            <i class="fa fa-pencil-alt"></i>
                                        </span>
                                        <?= translate('Rename'); ?>
                                    </a>
                                    <a href="<?= generate_url('backend_media_rename_file', ['file' => normalize_path($relativeFolderPath.'/'.$file->getName(), true)]); ?>" class="btn btn-outline-light btn-sm d-inline-block d-sm-none" title="<?= translate('Rename file'); ?>">
                                        <i class="fa fa-fw fa-pencil-alt"></i>
                                    </a>
                                    <a href="<?= generate_url('backend_media_download', ['file' => normalize_path($relativeFolderPath.'/'.$file->getName(), true)]); ?>" class="btn btn-outline-light btn-sm d-none d-sm-inline-block" title="<?= translate('Download file'); ?>">
                                        <i class="fa fa-fw fa-download"></i>
                                    </a>
                                    <a href="<?= generate_url('backend_media_delete_file', ['file' => normalize_path($relativeFolderPath.'/'.$file->getName(), true)]); ?>" class="btn btn-primary btn-sm confirm-modal" data-message="<?= translate('Are you sure you want to delete it?'); ?>" title="<?= translate('Delete file'); ?>">
                                        <i class="fa fa-fw fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php
    } ?>
                    </tbody>
                </table>
                <div class="card-body">
                    <small class="text-muted"><?= translate('Path'); ?>: <?= $path; ?></small>
                </div>
            <?php
} else {
        ?>
                <div class="card-body">
                    <p class="text-center text-muted"><?= translate('No folders and files found'); ?></p>
                    <small class="text-muted"><?= translate('Path'); ?>: <?= $path; ?></small>
                </div>
            <?php
    } ?>
        </div>

    </div>
    <div class="col-xl-5">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Upload files'); ?>
            </h4>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data" action="<?= generate_url('backend_media_upload'); ?>">
                    <input type="hidden" value="<?= $relativeFolderPath; ?>" name="dir" />
                    <div class="form-group row">
                        <label for="inputFiles" class="col-sm-3 col-form-label">
                            <?= translate('File', [], true); ?> *
                        </label>
                        <div class="col-sm-9">
                            <input type="file" multiple="" name="files[]" data-allowed-File-Extensions="<?= $view->settings()->allowed_file_extensions; ?>" data-allowed-File-Size="<?= ((int) ini_get('upload_max_filesize')) * 1024 * 1024; ?>" id="inputFiles" required class="form-control" />
                            <ul class="list-unstyled form-text text-muted small mb-0">
                                <li>
                                    <?= translate('Uploadable file size (defined in php.ini): max. {0}MB', [(int) ini_get('upload_max_filesize')]); ?>
                                </li>
                                <li>
                                    <?= translate('Allowed file extensions: {0}', [$view->settings()->getAllowedFileExtensions()]); ?>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9">
                            <input type="hidden" value="0" name="overwrite" />
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" id="checkboxOverwrite" class="custom-control-input" value="1" name="overwrite">
                                <label class="custom-control-label" for="checkboxOverwrite"><?= translate('Overwrite existing files with same name'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9">
                            <button type="submit" class="btn btn-primary btn-icon-left">
                                <span class="btn-icon">
                                    <i class="fa fa-upload"></i>
                                </span>
                                <?= translate('Upload'); ?>
                            </button>

                            <span class="small float-right">
                                * = <?= translate('Required field', [], true); ?>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <h4 class="card-header">
                <?= translate('Create folder'); ?>
            </h4>
            <div class="card-body">
                <form method="post" action="<?= generate_url('backend_media_create_folder'); ?>">
                    <input type="hidden" value="<?= $relativeFolderPath; ?>" name="dir" />
                    <div class="form-group row">
                        <label for="inputName" class="col-sm-3 col-form-label">
                            <?= translate('Name'); ?> *
                        </label>
                        <div class="col-sm-9">
                            <input type="text" name="name" minlength="1" maxlength="50" id="inputName" required class="form-control" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9">
                            <button type="submit" class="btn btn-primary btn-icon-left">
                                <span class="btn-icon">
                                    <i class="fa fa-save"></i>
                                </span>
                                <?= translate('Save'); ?>
                            </button>

                            <span class="small float-right">
                                * = <?= translate('Required field', [], true); ?>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<?= $view->renderTemplate('backend/media/preview-modal'); ?>