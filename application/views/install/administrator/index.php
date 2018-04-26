<?php $engine->startBlock('prepage'); ?>

<div class="row">
    <div class="col-md-5 mx-md-auto">

        <?= $view->renderAlertTemplate(); ?>

        <div class="card">

            <h4 class="card-header">
                3) <?= translate('Create administrator'); ?>
            </h4>
            <div class="card-body">

                <form method="post" action="<?= generate_url('install_administrator_create'); ?>">
                    <div class="form-group row <?= has_validation_error('email', 'has-danger'); ?>">
                        <label for="inputEmail" class="col-sm-3 col-form-label">
                            <?= translate('Email address'); ?> *
                        </label>
                        <div class="col-sm-9">
                            <input id="inputEmail" value="<?= $user->email; ?>" required type="email" required class="form-control" name="email" />
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('firstname', 'has-danger'); ?>">
                        <label for="inputFirstname" class="col-sm-3 col-form-label">
                            <?= translate('Firstname'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputFirstname" value="<?= $user->firstname; ?>" type="text" maxlength="50" class="form-control" name="firstname" maxlength="50" />
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('lastname', 'has-danger'); ?>">
                        <label for="inputLastname" class="col-sm-3 col-form-label">
                            <?= translate('Lastname'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputLastname" value="<?= $user->lastname; ?>" type="text" maxlength="50" class="form-control" name="lastname" maxlength="50" />
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('password', 'has-danger'); ?>">
                        <label for="inputPassword" class="col-sm-3 col-form-label">
                            <?= translate('Password'); ?> *
                        </label>
                        <div class="col-sm-9">
                            <input id="inputPassword" type="password" required class="form-control" name="password"  />

                            <small class="form-text text-muted">
                                <?= translate('The password must be at least 8 characters long and contain a special character or a number'); ?>
                            </small>
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('password2', 'has-danger'); ?>">
                        <label for="inputConfirmPassword" class="col-sm-3 col-form-label">
                            <?= translate('Confirm password'); ?> *
                        </label>
                        <div class="col-sm-9">
                            <input id="inputConfirmPassword" type="password" required class="form-control" name="confirmPassword"  />
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
<?php
$engine->stopBlock();
