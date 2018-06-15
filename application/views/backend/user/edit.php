<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Edit user') ?>
            </h4>
            <div class="card-body">

                <form method="post" action="<?= generate_url('backend_user_update') ?>">
                    <input value="<?= $user->id() ?>" type="hidden" name="user_id" />

                    <div class="form-group row <?= has_validation_error('email', 'has-danger') ?>">
                        <label for="inputEmail" class="col-sm-3 col-form-label">
                            <?= translate('Email address') ?> *
                        </label>
                        <div class="col-sm-9">
                            <input id="inputEmail" value="<?= $user->email ?>" type="email" required class="form-control" name="email" />
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('firstname', 'has-danger') ?>">
                        <label for="inputFirstname" class="col-sm-3 col-form-label">
                            <?= translate('Firstname') ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputFirstname" value="<?= $user->firstname ?>" type="text" maxlength="50" class="form-control" name="firstname" maxlength="50" />
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('lastname', 'has-danger') ?>">
                        <label for="inputLastname" class="col-sm-3 col-form-label">
                            <?= translate('Lastname') ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputLastname" value="<?= $user->lastname ?>" type="text" maxlength="50" class="form-control" name="lastname" maxlength="50" />
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('role_id', 'has-danger') ?>">
                        <label for="selectRole" class="col-sm-3 col-form-label">
                            <?= translate('Role') ?> *
                        </label>
                        <div class="col-sm-9">
                            <input value="<?= $user->role_id ?>" type="hidden" name="role_id" />

                            <select <?= (1 === $user->id() ? 'disabled' : '') ?> class="form-control" name="role_id" id="selectRole" data-placeholder="">
                                <?php
                                foreach ($roles as $role) {
                                    ?>
                                    <option value="<?= $role->id() ?>" <?= ($role->id() == $user->role_id ? 'selected' : '') ?>><?= $role->title ?></option>
                                    <?php
                                }

                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9">
                            <button type="submit" class="btn btn-primary btn-icon-left">
                                <span class="btn-icon">
                                    <i class="fa fa-save"></i>
                                </span>
                                <?= translate('Save') ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-5">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Change password') ?>
            </h4>
            <div class="card-body">

                <form method="post" action="<?= generate_url('backend_user_update_password') ?>">
                    <input value="<?= $user->id() ?>" type="hidden" name="user_id" />

                    <div class="form-group row <?= has_validation_error('password', 'has-danger') ?>">
                        <label for="inputNewPassword" class="col-sm-3 col-lg-4 col-form-label">
                            <?= translate('New password') ?> *
                        </label>
                        <div class="col-sm-9 col-lg-8">
                            <input id="inputNewPassword" minlength="8" type="password" required class="form-control" name="newPassword" />

                            <span class="form-text small text-muted">
                                <?= translate('The password must be at least 8 characters long and contain a special character or a number.') ?>
                            </span>
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('password2', 'has-danger') ?>">
                        <label for="inputConfirmPassword" class="col-sm-3 col-lg-4 col-form-label">
                            <?= translate('Confirm password') ?>
                        </label>
                        <div class="col-sm-9 col-lg-8">
                            <input id="inputConfirmPassword" type="password" class="form-control" name="confirmPassword" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9 offset-lg-4 col-lg-8">
                            <button type="submit" class="btn btn-primary btn-icon-left">
                                <span class="btn-icon">
                                    <i class="fa fa-save"></i>
                                </span>
                                <?= translate('Save') ?>
                            </button>

                            <span class="small float-right">
                                * = <?= translate('Required field', [], true) ?>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
