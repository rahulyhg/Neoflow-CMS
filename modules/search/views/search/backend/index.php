<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Setting', [], true); ?>
            </h4>

            <div class="card-body">

                <form method="post" action="<?= generate_url('tmod_search_backend_update'); ?>" class="form-horizontal">

                    <div class="form-group row <?= has_validation_error('url_path', 'has-error'); ?>">
                        <label for="inputUrlPath" class="col-sm-3 col-form-label">
                            <?= translate('URL path'); ?> *
                        </label>
                        <div class="col-sm-9">
                            <input value="<?= $settings->url_path; ?>" minlength="3" id="inputUrlPath" type="text" class="form-control" required name="url_path" maxlength="200" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9">
                            <input type="hidden" value="0" name="is_active" />
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkboxIsActive" value="1" name="is_active" <?= ($settings->is_active ? 'checked' : ''); ?>>
                                <label class="custom-control-label" for="checkboxIsActive"><?= translate('The search page is active and accessible'); ?></label>
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