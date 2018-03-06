<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Edit snippet'); ?>
            </h4>
            <div class="card-body">

                <form method="post" action="<?= generate_url('tmod_snippets_backend_update'); ?>" class="form-horizontal">
                    <input value="<?= $snippet->id(); ?>" type="hidden" name="snippet_id" />

                    <div class="form-group row <?= has_validation_error('title', 'has-error'); ?>">
                        <label for="inputTitle" class="col-sm-3 control-label">
                            <?= translate('Title'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputTitle" type="text" value="<?= $snippet->title; ?>" class="form-control" minlength="3" name="title" maxlength="100" />
                        </div>
                    </div>

                    <div class="form-group row <?= has_validation_error('placeholder', 'has-error'); ?>">
                        <label for="inputPlaceholder" class="col-sm-3 control-label">
                            <?= translate('Placeholder'); ?>
                        </label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        [[
                                    </span>
                                </div>
                                <input id="inputPlaceholder" type="text" value="<?= $snippet->placeholder; ?>" class="form-control" minlength="3" name="placeholder" maxlength="100" />
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        ]]
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row <?= has_validation_error('description', 'has-error'); ?>">
                        <label for="textareaDescription" class="col-sm-3 control-label">
                            <?= translate('Description'); ?>
                        </label>
                        <div class="col-sm-9">
                            <textarea name="description" class="form-control vresize" maxlength="150" id="textareaDescription" rows="3"><?= $snippet->description; ?></textarea>
                        </div>
                    </div>


                    <div class="form-group row <?= has_validation_error('vars', 'has-danger'); ?>">
                        <label for="inputVars" class="col-sm-3 col-form-label">
                            <?= translate('Variable', [], true); ?>
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control select2" data-tags="true" name="vars[]" multiple id="inputVars">
                                <?php foreach ($snippet->getVars() as $var) {

                                    ?>
                                    <option value="<?= $var ?>" selected><?= $var; ?></option>
                                <?php }

                                ?>
                            </select>

                            <span class="form-text small text-muted">
                                <?= translate('Argument variables for the snippet code which are added as query parameters to the placeholder (e. g.[[MySnippet? foo=1&bar=2]]).'); ?>
                            </span>
                        </div>
                    </div>




                    <div class="form-group row <?= has_validation_error('code', 'has-error'); ?>">
                        <label for="textareaCode" class="col-sm-3 control-label">
                            <?= translate('Code'); ?>
                        </label>
                        <div class="col-sm-9">
                            <?= Neoflow\CMS\App::instance()->getService('code')->renderEditor('code', 'textareaCode', $snippet->code); ?>
                            <span class="help-block <?= ($codeStatus ? 'text-success' : 'text-danger'); ?>">
                                <?php if ($codeStatus) {

                                    ?>
                                    <i class="fa fa-fw fa-check"></i>
                                    <?php
                                } else {

                                    ?>
                                    <i class="fa fa-fw fa-times"></i>
                                    <?php
                                }

                                ?>
                                <?= htmlentities($codeStatusMessage); ?>
                            </span>
                        </div>
                    </div>

                    <div class="form-group row row">
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
