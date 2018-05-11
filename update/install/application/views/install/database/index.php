<?php $engine->startBlock('prepage'); ?>

<div class="row">
    <div class="col-md-6 mx-md-auto col-lg-4">

        <?= $view->renderAlertTemplate(); ?>

        <div class="card">

            <h4 class="card-header">
                1) <?= translate('Create database'); ?>
            </h4>
            <div class="card-body">

                <form method="post" action="<?= generate_url('install_database_create'); ?>">

                    <input type="hidden" value="<?= $url; ?>" name="url" />

                    <div class="form-group">
                        <label for="inputHost">
                            <?= translate('Host'); ?> *
                        </label>
                        <input id="inputHost" required class="form-control" name="database[host]" type="text" value="<?= $database['host']; ?>" />
                    </div>

                    <div class="form-group">
                        <label for="inputDbName">
                            <?= translate('Database name'); ?> *
                        </label>
                        <input id="inputDbName" required class="form-control" name="database[dbname]" type="text" value="<?= $database['dbname']; ?>" />
                    </div>

                    <div class="form-group">
                        <label for="inputUsername">
                            <?= translate('Username'); ?> *
                        </label>
                        <input id="inputUsername" required class="form-control" name="database[username]" type="text" value="<?= $database['username']; ?>" />
                    </div>

                    <div class="form-group">
                        <label for="inputPassword">
                            <?= translate('Password'); ?>
                        </label>
                        <input id="inputPassword" class="form-control" name="database[password]" type="password"  />
                    </div>

                    <div class="form-group">
                        <label for="inputCharset">
                            <?= translate('Charset'); ?> *
                        </label>
                        <input id="inputCharset" class="form-control" name="database[charset]" required type="text" value="<?= $database['charset']; ?>" />
                        <small class="form-text text-muted">
                            <?= translate('It is recommended to use UTF8mb4 as a charset.'); ?>
                        </small>
                    </div>


                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-icon-left">
                            <span class="btn-icon">
                                <i class="fa fa-save"></i>
                            </span>
                            <?= translate('Install'); ?>
                        </button>

                        <span class="small float-right">
                            * = <?= translate('Required field', [], true); ?>
                        </span>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
<?php
$engine->stopBlock();
