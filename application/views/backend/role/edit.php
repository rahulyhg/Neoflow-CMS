<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Edit role') ?>
            </h4>
            <div class="card-body">

                <form method="post" action="<?= generate_url('backend_role_update') ?>">
                    <input value="<?= $role->id() ?>" type="hidden" name="role_id" />

                    <div class="form-group row <?= has_validation_error('title', 'has-danger') ?>">
                        <label for="inputTitle" class="col-sm-3 col-form-label">
                            <?= translate('Title') ?> *
                        </label>
                        <div class="col-sm-9">
                            <input id="inputTitle" value="<?= $role->title ?>" type="text" required class="form-control" name="title" maxlength="20" />
                        </div>
                    </div>

                    <div class="form-group row <?= has_validation_error('description', 'has-danger') ?>">
                        <label for="textareaDescription" class="col-sm-3 col-form-label">
                            <?= translate('Description') ?>
                        </label>
                        <div class="col-sm-9">
                            <textarea name="description" class="form-control vresize" maxlength="150" id="textareaDescription" rows="3"><?= $role->description ?></textarea>
                        </div>
                    </div>

                    <div class="form-group row <?= has_validation_error('permission_ids', 'has-danger') ?>">
                        <label for="selectPermissions" class="col-sm-3 col-form-label">
                            <?= translate('Permission', [], true) ?>
                        </label>
                        <div class="col-sm-9">
                            <select multiple class="form-control" name="permission_ids[]" id="selectPermissions" data-placeholder="">
                                <?php
                                foreach ($permissions as $permission) {
                                    ?>
                                    <option value="<?= $permission->id() ?>" <?= (in_array($permission->id(), $role->permission_ids) ? 'selected' : '') ?> data-description="<?= translate($permission->description, [], true) ?>" ><?= translate($permission->title, [], true) ?></option>
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
