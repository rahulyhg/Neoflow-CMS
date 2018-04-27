<?php $engine->startBlock('prepage'); ?>

<div class="row">
    <div class="col-md-5 mx-md-auto">

        <div class="card">
            <div class="card-body">
                <h2 class="card-title">
                    <?= translate('Installation welcome title'); ?>
                </h2>
                <p class="card-text">
                    <?= translate('Installation welcome message'); ?>
                </p>
            </div>
        </div>

        <div class="card">
            <h4 class="card-header">
                <?= translate('Configuration'); ?>
            </h4>
            <div class="card-body">

                <?= $view->renderAlertTemplate(); ?>

                <form class="form-horizontal" method="get" action="<?= generate_url('install_database_index'); ?>">

                    <div class="form-group row">
                        <label for="inputUrl" class="col-sm-3 col-form-label">
                            <?= translate('Base URL'); ?> *
                        </label>
                        <div class="col-sm-9">
                            <input id="inputUrl" required class="form-control" type="url" name="url" value="<?= $url; ?>" />
                            <small class="form-text text-muted">
                                <?= translate('It is recommended to use the automatically recognized URL, unless the website is operated behind a gateway for example.'); ?>
                            </small>
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-sm-9 offset-sm-3">
                            <button type="submit" class="btn btn-primary btn-icon-right">
                                <?= translate('Start installation'); ?>
                                <span class="btn-icon">
                                    <i class="fa fa-cog"></i>
                                </span>
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
<?php
$engine->stopBlock();
