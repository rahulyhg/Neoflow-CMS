<?= $view->renderTemplate('backend/page/navbar', ['page' => $page]); ?>
<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Edit page'); ?>
            </h4>
            <div class="card-body">

                <form method="post" action="<?= generate_url('backend_page_update'); ?>">
                    <input value="<?= $page->id(); ?>" type="hidden" name="page_id" />

                    <div class="form-group row <?= has_validation_error('title', 'has-danger'); ?>">
                        <label for="inputTitle" class="col-sm-3 col-form-label">
                            <?= translate('Title'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputTitle" value="<?= $page->title; ?>" type="text" required class="form-control" name="title" maxlength="50" minlength="3" />
                        </div>
                    </div>

                    <div class="form-group row <?= has_validation_error('url', 'has-danger'); ?>">
                        <label for="inputCustomSlug" class="col-sm-3 col-form-label">
                            <?= translate('URL'); ?>
                        </label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <?php
                                        $parentPage = $page->getParentPage();
                                        if ($parentPage) {
                                            echo $parentPage->getRelativeUrl(false, false, true).'/';
                                        } else {
                                            echo '/';
                                        }

                                        ?>
                                    </span>
                                </div>
                                <input id="inputCustomSlug" value="<?= $page->slug; ?>" type="text" class="form-control regexomat" name="custom_slug" maxlength="50" minlength="3" data-regex="[^a-zA-Z0-9\-\_]+" />
                            </div>

                            <?php if ($urlMessage) {
                                            ?>
                                <small class="form-text text-danger"><?= $urlMessage; ?></small>
                            <?php
                                        }

                            ?>
                        </div>
                    </div>

                    <div class="form-group row <?= has_validation_error('description', 'has-danger'); ?>">
                        <label for="textareaDescription" class="col-sm-3 col-form-label">
                            <?= translate('Description'); ?>
                        </label>
                        <div class="col-sm-9">
                            <textarea name="description" class="form-control vresize" maxlength="255" id="textareaDescription" rows="3"><?= $page->description; ?></textarea>
                        </div>
                    </div>

                    <div class="form-group row <?= has_validation_error('keywords', 'has-danger'); ?>">
                        <label for="inputKeywords" class="col-sm-3 col-form-label">
                            <?= translate('Keyword', [], true); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputKeywords" value="<?= $page->keywords; ?>" type="text" class="form-control" name="keywords" maxlength="255" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="selectAuthor" class="col-sm-3 col-form-label">
                            <?= translate('Author'); ?>
                        </label>
                        <div class="col-sm-9">
                            <select data-placeholder="<?= $view->settings()->author; ?>" class="form-control select2" name="author_user_id" id="selectAuthor">
                                <option value="0"><?= $view->settings()->author; ?></option>
                                <?php
                                foreach ($users as $user) {
                                    ?>
                                    <option value="<?= $user->id(); ?>" <?= ($user->id() == $page->author_user_id ? 'selected' : ''); ?>><?= $user->getFullname(); ?></option>
                                    <?php
                                }

                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9">
                            <input type="hidden" value="0" name="is_active" />
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkboxIsActive" value="1" name="is_active" <?= ($page->is_active ? 'checked' : ''); ?>>
                                <label class="custom-control-label" for="checkboxIsActive"><?= translate('Page is active and accessible'); ?></label>
                            </div>
                        </div>
                    </div>

                    <hr />

                    <div class="form-group row <?= has_validation_error('navigation_title', 'has-danger'); ?>">
                        <label for="inputNavigationTitle" class="col-sm-3 col-form-label">
                            <?= translate('Navigation title'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputNavigationTitle" value="<?= $pageNavitem->title; ?>" type="text" class="form-control" name="navigation_title" maxlength="50" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="selectPage" class="col-sm-3 col-form-label">
                            <?= translate('Top page'); ?>
                        </label>
                        <div class="col-sm-9">
                            <select data-placeholder="<?= translate('None'); ?>" class="form-control select2" name="parent_navitem_id" id="selectPage">
                                <option value="0"><?= translate('None'); ?></option>
                                <?= $view->renderNavitemOptions($navitems, 0, [$pageNavitem->parent_navitem_id], [$pageNavitem->id()]); ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9">
                            <input type="hidden" value="0" name="is_visible" />
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkboxIsVisible" value="1" name="is_visible" <?= ($pageNavitem->is_active ? 'checked' : ''); ?>>
                                <label class="custom-control-label" for="checkboxIsVisible"><?= translate('Page is visible in the page tree navigation'); ?></label>
                            </div>
                        </div>
                    </div>

                    <hr />

                    <div class="form-group row">
                        <label for="selectRoles" class="col-sm-3 col-form-label">
                            <?= translate('Authorized role', [], true); ?>
                        </label>
                        <div class="col-sm-9">
                            <select data-placeholder="<?= translate('All roles'); ?>" class="form-control select2" name="role_ids[]" multiple id="selectRoles">
                                <?php
                                foreach ($roles as $role) {
                                    ?>
                                    <option value="<?= $role->id(); ?>" <?= (in_array($role->id(), $page->getRoles()->mapValue('role_id')) ? 'selected' : ''); ?>><?= $role->title; ?></option>
                                    <?php
                                }

                                ?>
                            </select>
                            <small class="form-text text-muted"><?= translate('Page authorization info'); ?></small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9">
                            <input type="hidden" value="0" name="only_logged_in_users" />
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkboxOnlyLoggedInUsers" value="1" name="only_logged_in_users" <?= ($page->only_logged_in_users ? 'checked' : ''); ?>>
                                <label class="custom-control-label" for="checkboxOnlyLoggedInUsers"><?= translate('Page is only accessible for logged in users'); ?></label>
                            </div>
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
