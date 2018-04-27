<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('All roles'); ?>
            </h4>

            <table class="datatable table display responsive no-wrap" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th data-priority="0" data-order="true">
                            <?= translate('Title'); ?>
                        </th>
                        <th class="none" data-priority="1">
                            <?= translate('Description'); ?>
                        </th>
                        <th data-priority="2">
                            <?= translate('Permission', [], true); ?>
                        </th>
                        <th data-orderable="false" data-filterable="false" data-priority="0"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roles as $role) { ?>
                        <tr>
                            <td>
                                <?php
                                if (1 === $role->id()) {
                                    echo $role->title;
                                } else {

                                    ?>
                                    <a href="<?= generate_url('backend_role_edit', ['id' => $role->id()]); ?>" title="<?= translate('Edit role'); ?>">
                                        <?= $role->title; ?>
                                    </a>
                                <?php }

                                ?>
                            </td>
                            <td><?= nl2br($role->description); ?></td>
                            <td><?php
                                echo $role->permissions()->fetchAll()->implode(function ($role) {
                                    return translate($role->title);
                                }, ', ');

                                ?></td>
                            </td>
                            <td class="text-right nowrap">
                                <a href="<?= generate_url('backend_role_edit', ['id' => $role->id()]); ?>" class="btn btn-outline-light btn-sm btn-icon-left d-none d-xl-inline-block <?= (1 === $role->id() ? 'disabled' : ''); ?>" title="<?= translate('Edit role'); ?>">
                                    <span class="btn-icon">
                                        <i class="fa fa-pencil-alt"></i>
                                    </span>
                                    <?= translate('Edit'); ?>
                                </a>
                                <a href="<?= generate_url('backend_role_delete', ['id' => $role->id()]); ?>" class="btn btn-primary btn-sm confirm-modal <?= (1 === $role->id() ? 'disabled' : ''); ?>" data-message="<?= translate('Are you sure you want to delete it?'); ?>" title="<?= translate('Delete role'); ?>">
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
                <?= translate('Create role'); ?>
            </h4>
            <div class="card-body">
                <form method="post" action="<?= generate_url('backend_role_create'); ?>">
                    <div class="form-group row <?= has_validation_error('title', 'has-danger'); ?>">
                        <label for="inputTitle" class="col-sm-3 col-form-label">
                            <?= translate('Title'); ?> *
                        </label>
                        <div class="col-sm-9">
                            <input type="text" name="title" id="inputTitle" maxlength="20" required class="form-control" />
                        </div>
                    </div>

                    <div class="form-group row <?= has_validation_error('description', 'has-danger'); ?>">
                        <label for="textareaDescription" class="col-sm-3 col-form-label">
                            <?= translate('Description'); ?>
                        </label>
                        <div class="col-sm-9">
                            <textarea name="description" class="form-control vresize" maxlength="150" id="textareaDescription" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="form-group row <?= has_validation_error('permission_ids', 'has-danger'); ?>">
                        <label for="selectPermissions" class="col-sm-3 col-form-label">
                            <?= translate('Permission', [], true); ?>
                        </label>
                        <div class="col-sm-9">
                            <select multiple class="form-control" name="permission_ids[]" id="selectPermissions" data-placeholder="">
                                <?php
                                foreach ($permissions as $permission) {

                                    ?>
                                    <option value="<?= $permission->id(); ?>" data-description="<?= translate($permission->description); ?>" ><?= translate($permission->title, [], true); ?></option>
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

