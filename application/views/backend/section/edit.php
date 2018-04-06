<?= $view->renderTemplate('backend/page/navbar', ['page' => $page, 'section' => $section, 'module' => $module]); ?>

<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Edit section'); ?>
            </h4>
            <div class="card-body">

                <form method="post" action="<?= generate_url('backend_section_update'); ?>">
                    <input value="<?= $section->id(); ?>" type="hidden" name="section_id" />
                    <div class="form-group row <?= has_validation_error('block_key', 'has-danger'); ?>">
                        <label for="selectBlock" class="col-sm-3 col-form-label">
                            <?= translate('Block'); ?>
                        </label>
                        <div class="col-sm-9">
                            <select required class="form-control select2" name="block_id" id="selectBlock" data-placeholder="">
                                <?php if (null === $section->block_id) {

                                    ?>
                                    <option value="0"><?= translate('Not specified'); ?></option>
                                    <?php
                                }
                                foreach ($blocks as $block) {

                                    ?>
                                    <option value="<?= $block->id(); ?>" <?= ($section->block_id == $block->id() ? 'selected' : ''); ?> ><?= $block->title; ?></option>
    <?php }

?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9">
                            <input type="hidden" value="0" name="is_active" />
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkboxIsActive" value="1" name="is_active" <?= ($section->is_active ? 'checked' : ''); ?>>
                                <label class="custom-control-label" for="checkboxIsActive"><?= translate('Section is active and visible'); ?></label>
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


