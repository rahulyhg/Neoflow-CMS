<?php $engine->startBlock('prepage'); ?>

    <div class="row">
        <div class="col-md-6 mx-md-auto col-lg-5 col-xl-4">

            <?= $view->renderAlertTemplate(); ?>

            <div class="card">
                <h4 class="card-header">
                    <?= translate('Create new password'); ?>
                </h4>
                <div class="card-body">

                    <p>
                        <?= translate('Please enter the new password for your user account, registered under the email address {0}.', [$user->email]); ?>
                    </p>

                    <form method="post" action="<?= generate_url('backend_auth_update_password'); ?>">
                        <input type="hidden" name="user_id" value="<?= $user->id(); ?>"/>
                        <input type="hidden" name="reset_key" value="<?= $user->reset_key; ?>"/>
                        <div class="form-group">
                            <label for="inputNewPassword">
                                <?= translate('Password'); ?>
                            </label>
                            <input id="inputNewPassword" class="form-control" name="new_password" type="password"/>
                        </div>
                        <div class="form-group">
                            <label for="inputConfirmPassword">
                                <?= translate('Confirm password'); ?>
                            </label>
                            <input id="inputConfirmPassword" class="form-control" name="confirm_password"
                                   type="password"/>
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

                    <hr/>

                    <a href="<?= generate_url('backend_auth_login'); ?>"><?= translate('Login'); ?></a>
                </div>
            </div>
        </div>
    </div>

<?php $engine->stopBlock(); ?>