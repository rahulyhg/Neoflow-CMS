<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('All users'); ?>
            </h4>

            <table class="datatable table display responsive no-wrap" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th data-priority="0" data-order="true">
                            <?= translate('Email address'); ?>
                        </th>
                        <th data-priority="1">
                            <?= translate('Firstname'); ?>
                        </th>
                        <th data-priority="3">
                            <?= translate('Lastname'); ?>
                        </th>
                        <th data-priority="2">
                            <?= translate('Role'); ?>
                        </th>
                        <th data-orderable="false" data-filterable="false" data-priority="0"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) {
    ?>
                        <tr>
                            <td>
                                <a href="<?= generate_url('backend_user_edit', ['id' => $user->id()]); ?>" title="<?= translate('Edit user'); ?>">
                                    <?= $user->email; ?>
                                </a>
                            </td>
                            <td><?= $user->firstname; ?></td>
                            <td><?= $user->lastname; ?></td>
                            <td><?= $user->role()->fetch()->title; ?></td>
                            </td>
                            <td class="text-right nowrap">
                                <a href="<?= generate_url('backend_user_edit', ['id' => $user->id()]); ?>" class="btn btn-outline-light btn-sm btn-icon-left d-none d-xl-inline-block" title="<?= translate('Edit user'); ?>">
                                    <span class="btn-icon">
                                        <i class="fa fa-pencil-alt"></i>
                                    </span>
                                    <?= translate('Edit'); ?>
                                </a>
                                <a href="<?= generate_url('backend_user_delete', ['id' => $user->id()]); ?>" class="btn btn-primary btn-sm confirm-modal <?= (1 === $user->id() ? 'disabled' : ''); ?>" data-message="<?= translate('Are you sure you want to delete it?'); ?>" title="<?= translate('Delete user'); ?>">
                                    <i class="fa fa-fw fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php
}

                    ?>
                </tbody>
            </table>

        </div>

    </div>
    <div class="col-xl-5">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Create user'); ?>
            </h4>
            <div class="card-body">
                <form method="post" action="<?= generate_url('backend_user_create'); ?>">
                    <div class="form-group row <?= has_validation_error('email', 'has-danger'); ?>">
                        <label for="inputTitle" class="col-sm-3 col-form-label">
                            <?= translate('Email address'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input type="email" name="email" id="inputTitle" required class="form-control" />
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('firstname', 'has-danger'); ?>">
                        <label for="inputFirstname" class="col-sm-3 col-form-label">
                            <?= translate('Firstname'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputFirstname" type="text" class="form-control" name="firstname" maxlength="50" />
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('lastname', 'has-danger'); ?>">
                        <label for="inputLastname" class="col-sm-3 col-form-label">
                            <?= translate('Lastname'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputLastname" type="text" class="form-control" name="lastname" maxlength="50" />
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('password', 'has-danger'); ?>">
                        <label for="inputPassword" class="col-sm-3 col-form-label">
                            <?= translate('Password'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputPassword" type="password" class="form-control" name="password"  />

                            <small class="form-text text-muted">
                                <?= translate('The password must be at least 8 characters long and contain a special character or a number'); ?>
                            </small>
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('password2', 'has-danger'); ?>">
                        <label for="inputConfirmPassword" class="col-sm-3 col-form-label">
                            <?= translate('Confirm password'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputConfirmPassword" type="password" class="form-control" name="confirmPassword"  />
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('role_id', 'has-danger'); ?>">
                        <label for="selectRole" class="col-sm-3 col-form-label">
                            <?= translate('Role'); ?>
                        </label>
                        <div class="col-sm-9">
                            <select required class="form-control select2" name="role_id" id="selectRole">
                                <?php
                                foreach ($roles as $role) {
                                    ?>
                                    <option value="<?= $role->id(); ?>"><?= $role->title; ?></option>
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
                                <?= translate('Save'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

