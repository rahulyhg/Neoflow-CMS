<?php $engine->startBlock('prepage'); ?>

<div class="row">
    <div class="col-md-5 mx-md-auto">

        <?= $view->renderAlertTemplate(); ?>

        <div class="card">

            <h4 class="card-header">
                1) <?= translate('Create database'); ?>
            </h4>
            <div class="card-body">


                <form class="form-horizontal" method="post" action="<?= generate_url('install_database_create'); ?>">

                    <input type="hidden" value="<?= $url; ?>" name="url" />

                    <div class="form-group row">
                        <label for="inputHost" class="col-sm-3 col-form-label">
                            <?= translate('Host'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputHost" required class="form-control" name="database[host]" type="text" value="<?= $database['host']; ?>" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="inputDbName" class="col-sm-3 col-form-label">
                            <?= translate('Database name'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputDbName" required class="form-control" name="database[dbname]" type="text" value="<?= $database['dbname']; ?>" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="inputUsername" class="col-sm-3 col-form-label">
                            <?= translate('Username'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputUsername" class="form-control" name="database[username]" type="text" value="<?= $database['username']; ?>" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="inputPassword" class="col-sm-3 col-form-label">
                            <?= translate('Password'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputPassword" class="form-control" name="database[password]" type="password"  />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="inputCharset" class="col-sm-3 col-form-label">
                            <?= translate('Charset'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputCharset" class="form-control" name="database[charset]" type="text" value="<?= $database['charset']; ?>" />
                            <small class="form-text text-muted">
                                <?= translate('It is recommended to use UTF8 as a charset'); ?>
                            </small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-9 offset-sm-3">
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
    </div>
</div>
<?php
$engine->stopBlock();
