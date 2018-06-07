<?php $engine->startBlock('prepage'); ?>

<div class="row">
    <div class="col-md-6 mx-md-auto col-lg-4 col-xl-3">

        <?= $view->renderAlertTemplate(); ?>

        <div class="card">
            <h4 class="card-header">
                <?= translate('Login'); ?>
            </h4>
            <div class="card-body">
                <form method="post" action="<?= generate_url('backend_auth_authenticate'); ?>">
                    <input name="url" value="<?= $url; ?>" type="hidden">
                    <div class="form-group">
                        <label for="inputEmail">
                            <?= translate('Email address'); ?>
                        </label>
                        <input id="inputEmail" class="form-control" name="email" type="text" autofocus>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword">
                            <?= translate('Password'); ?>
                        </label>
                        <input id="inputPassword" class="form-control" name="password" type="password" value="">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-icon-left">
                            <span class="btn-icon">
                                <i class="fa fa-sign-in-alt"></i>
                            </span>
                            <?= translate('Login'); ?>
                        </button>
                    </div>
                </form>

                <hr/>

                <a href="<?= generate_url('backend_auth_lost_password'); ?>"><?= translate('Lost your password?'); ?></a>
            </div>
        </div>
    </div>
</div>
<?php $engine->stopBlock(); ?>

