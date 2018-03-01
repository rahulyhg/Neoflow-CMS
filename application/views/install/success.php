<?php $engine->startBlock('prepage'); ?>

<div class="row">
    <div class="col-md-5 mx-md-auto">

        <?= $view->renderAlertTemplate(); ?>

        <div class="card">
            <div class="card-body">
                <h2 class="card-title">
                    <?= translate('Installation success title'); ?>
                </h2>
                <p class="card-text">
                    <?= translate('Installation success message'); ?>
                </p>
                <hr />
                <a href="<?= generate_url('frontend_index'); ?>" class="btn btn-outline-light btn-icon-left">
                    <span class="btn-icon">
                        <i class="fa fa-desktop"></i>
                    </span>
                    <?= translate('To the frontend'); ?>
                </a>
                <a href="<?= generate_url('backend_index'); ?>" class="btn btn-primary btn-icon-left">
                    <span class="btn-icon">
                        <i class="fas fa-cogs"></i>
                    </span>
                    <?= translate('To the backend'); ?>
                </a>
            </div>
        </div>


    </div>
</div>
<?php
$engine->stopBlock();
