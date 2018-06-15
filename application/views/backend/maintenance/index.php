<div class="row">    <div class="col-lg-6 col-xl-5">        <div class="card">            <h4 class="card-header">                <?= translate('Cache') ?>            </h4>            <div class="card-body">                <p><?= translate('Please specify which cached data you want to clear') ?>:</p>                <form method="post" action="<?= generate_url('backend_maintenance_clear_cache') ?>">                    <div class="form-group">                        <div class="custom-controls-stacked">                            <div class="custom-control custom-radio">                                <input id="checkboxChache1" name="cache" type="radio" value="all" class="custom-control-input" checked />                                <label class="custom-control-label" for="checkboxChache1">                                    <?= translate('All data') ?>                                </label>                            </div>                            <div class="custom-control custom-radio">                                <input id="checkboxChache2" name="cache" type="radio" value="database-results" class="custom-control-input" />                                <label class="custom-control-label" for="checkboxChache2">                                    <?= translate('Only database results') ?>                                </label>                            </div>                            <div class="custom-control custom-radio">                                <input id="checkboxChache3" name="cache" type="radio" value="cms_core" class="custom-control-input" />                                <label class="custom-control-label" for="checkboxChache3">                                    <?= translate('Only CMS data') ?>                                </label>                            </div>                            <div class="custom-control custom-radio">                                <input id="checkboxChache4" name="cache" type="radio" value="cms_translations" class="custom-control-input" />                                <label class="custom-control-label" for="checkboxChache4">                                    <?= translate('Only translations') ?>                                </label>                            </div>                        </div>                    </div>                    <div class="form-group">                        <button type="submit" title="<?= translate('Clear cache') ?>" class="btn btn-primary btn-icon-left">                            <span class="btn-icon">                                <i class="fa fa-trash-alt"></i>                            </span>                            <?= translate('Clear') ?>                        </button>                    </div>                </form>            </div>        </div>        <div class="card">            <h4 class="card-header">                <?= translate('Logfile', [], true) ?>            </h4>            <div class="card-body">                <p><?= translate('Please specify which logfiles you want to delete') ?>:</p>                <form method="post" action="<?= generate_url('backend_maintenance_delete_logfiles') ?>">                    <div class="form-group">                        <div class="custom-controls-stacked">                            <div class="custom-control custom-radio">                                <input name="logfiles" id="checkboxLogfiles1" type="radio" value="0" class="custom-control-input" checked />                                <label class="custom-control-label" for="checkboxLogfiles1">                                    <?= translate('All (except the file of today)') ?>                                </label>                            </div>                            <div class="custom-control custom-radio">                                <input id="checkboxLogfiles2" name="logfiles" type="radio" value="30" class="custom-control-input" />                                <label class="custom-control-label" for="checkboxLogfiles2">                                    <?= translate('Only older than {0} days', [30]) ?>                                </label>                            </div>                            <div class="custom-control custom-radio">                                <input id="checkboxLogfiles3" name="logfiles" type="radio" value="5" class="custom-control-input" />                                <label class="custom-control-label" for="checkboxLogfiles3">                                    <?= translate('Only older than {0} days', [5]) ?>                                </label>                            </div>                        </div>                    </div>                    <div class="form-group">                        <button type="submit" title="<?= translate('Delete logfiles') ?>" class="btn btn-primary btn-icon btn-icon-left">                            <span class="btn-icon">                                <i class="fa fa-trash-alt"></i>                            </span>                            <?= translate('Delete') ?>                        </button>                    </div>                </form>            </div>        </div>    </div>    <div class="col-lg-6 col-xl-5">        <div class="card">            <h4 class="card-header">                <?= translate('Folder permission', [], true) ?>            </h4>            <div class="card-body">                <p><?= translate('Reset folder permissions message') ?></p>                <a href="<?= generate_url('backend_maintenance_reset_folder_permissions') ?>" title="<?= translate('Reset folder permissions') ?>" class="btn btn-primary btn-icon btn-icon-left">                    <span class="btn-icon">                        <i class="fa fa-sync"></i>                    </span>                    <?= translate('Reset') ?>                </a>            </div>        </div>        <div class="card">            <h4 class="card-header">                <?= translate('Update CMS') ?>            </h4>            <div class="card-body">                <p><?= translate('Please upload the latest update as a ZIP file to start the installation.') ?></p>                <form method="post" enctype="multipart/form-data" action="<?= generate_url('backend_maintenance_install_update') ?>">                    <div class="form-group row">                        <label for="inputUpdatePackage" class="col-sm-3 col-form-label">                            <?= translate('Package') ?> *                        </label>                        <div class="col-sm-9">                            <input type="file" name="file" data-allowed-File-Extensions="zip" data-allowed-File-Size="<?= ((int) ini_get('upload_max_filesize')) * 1024 * 1024 ?>" id="inputUpdatePackage" required class="form-control" />                            <ul class="list-unstyled form-text text-muted small mb-0">                                <li>                                    <?= translate('Uploadable file size (defined in php.ini): max. {0}MB', [(int) ini_get('upload_max_filesize')]) ?>                                </li>                                <li>                                    <?= translate('Allowed file extensions: {0}', ['zip']) ?>                                </li>                            </ul>                        </div>                    </div>                    <div class="form-group row">                        <div class="offset-sm-3 col-sm-9">                            <button type="submit" class="btn btn-primary btn-icon-left">                                <span class="btn-icon">                                    <i class="fa fa-save"></i>                                </span>                                <?= translate('Install') ?>                            </button>                            <span class="small float-right">                                * = <?= translate('Required field', [], true) ?>                            </span>                        </div>                    </div>                </form>            </div>        </div>    </div></div>