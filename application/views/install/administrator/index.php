<?php $engine->startBlock('prepage'); ?>

<div class="row">
    <div class="col-md-6 mx-md-auto col-lg-4">

        <?= $view->renderAlertTemplate(); ?>

        <div class="card">

            <h4 class="card-header">
                3) <?= translate('Create administrator'); ?>
            </h4>
            <div class="card-body">

                <form method="post" action="<?= generate_url('install_administrator_create'); ?>">
                    <div class="form-group <?= has_validation_error('email', 'has-danger'); ?>">
                        <label for="inputEmail">
                            <?= translate('Email address'); ?> *
                        </label>
                        <input id="inputEmail" value="<?= $user->email; ?>" required type="email" required class="form-control" name="email" />
                    </div>
                    <div class="form-group <?= has_validation_error('firstname', 'has-danger'); ?>">
                        <label for="inputFirstname">
                            <?= translate('Firstname'); ?>
                        </label>
                        <input id="inputFirstname" value="<?= $user->firstname; ?>" type="text" maxlength="50" class="form-control" name="firstname" maxlength="50" />
                    </div>
                    <div class="form-group <?= has_validation_error('lastname', 'has-danger'); ?>">
                        <label for="inputLastname">
                            <?= translate('Lastname'); ?>
                        </label>
                        <input id="inputLastname" value="<?= $user->lastname; ?>" type="text" maxlength="50" class="form-control" name="lastname" maxlength="50" />
                    </div>

                    <hr />

                    <div class="form-group <?= has_validation_error('password', 'has-danger'); ?>">
                        <label for="inputPassword">
                            <?= translate('Password'); ?> *
                        </label>
                        <input id="inputPassword" type="password" required class="form-control" name="password"  />
                        <small class="form-text text-muted">
                            <?= translate('The password must be at least 8 characters long and contain a special character or a number.'); ?>
                        </small>
                    </div>
                    <div class="form-group <?= has_validation_error('password2', 'has-danger'); ?>">
                        <label for="inputConfirmPassword">
                            <?= translate('Confirm password'); ?>
                        </label>
                        <input id="inputConfirmPassword" type="password" class="form-control" name="confirmPassword"  />
                    </div>
                    <div class="form-group">
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
                </form>

            </div>
        </div>
    </div>
</div>
<?php
$engine->stopBlock();
