<?php $engine->startBlock('prepage'); ?>


<div class="row">
    <div class="col-md-6 mx-md-auto col-lg-5 col-xl-4">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Lost your password?'); ?>
            </h4>
            <div class="card-body">

                <?= $view->renderAlertTemplate(); ?>

                <p>
                    <?= translate('Please enter the email address of your user account. You will receive a link to create a new password via email.'); ?>
                </p>

                <form method="post" action="<?= generate_url('backend_auth_reset_password'); ?>">
                    <div class="form-group">
                        <label for="inputEmail">
                            <?= translate('Email address'); ?>
                        </label>
                        <input id="inputEmail" class="form-control" name="email" type="text">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-icon-left">
                            <span class="btn-icon">
                                <i class="fa fa-save"></i>
                            </span>
                            <?= translate('Save'); ?>
                        </button>
                    </div>
                </form>

                <hr />

                <a href="<?= generate_url('backend_auth_login'); ?>"><?= translate('Login'); ?></a>
            </div>
        </div>
    </div>
</div>

<?php $engine->stopBlock(); ?>