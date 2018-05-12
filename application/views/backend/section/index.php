<?= $view->renderTemplate('backend/page/navbar', ['page' => $page]); ?>

<div class="row">
    <div class="col-xl-7">
        <?php
        $frontendTheme = $view->settings()->getFrontendTheme();
        if ('grouped' === $frontendTheme->block_handling) {
            foreach ($blocks as $block) {
                ?>
                <div class="card">
                    <h4 class="card-header">
                        <?= translate('Sections'); ?> <small><?= translate('Block').': '.$block->title; ?></small>
                    </h4>
                    <div class="card-body">
                        <div class="nestable sections" data-group="0" data-id="<?= $block->id(); ?>" data-max-depth="1" data-save-url="<?= generate_url('backend_section_reorder'); ?>">
                            <?= $view->renderSectionNestable($sections->where('block_id', $block->id()), false); ?>
                        </div>
                        <ul class="list-inline small">
                            <li class="list-inline-item">
                                <i class="fa fa-toggle-on"></i> = <?= translate('Enabled'); ?>
                            </li>
                            <li class="list-inline-item">
                                <i class="fa fa-toggle-off"></i> = <?= translate('Disabled'); ?>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php
            }
        } else {
            $sectionsWithBlock = $sections->whereNot('block_id', null); ?>
            <div class="card">
                <h4 class="card-header">
                    <?= translate('Sections'); ?>
                </h4>
                <div class="card-body">

                    <?php if ($sectionsWithBlock->count() > 0) {
                ?>
                        <div class="nestable sections" data-group="2" data-max-depth="1" data-save-url="<?= generate_url('backend_section_reorder'); ?>">
                            <?= $view->renderSectionNestable($sectionsWithBlock, true); ?>
                        </div>
                    <?php
            } else {
                ?>
                        <p class="text-center text-muted"><?= translate('No results found'); ?></p>
                    <?php
            } ?>
                    <ul class="list-inline small">
                        <li class="list-inline-item">
                            <i class="fa fa-toggle-on"></i> = <?= translate('Enabled'); ?>
                        </li>
                        <li class="list-inline-item">
                            <i class="fa fa-toggle-off"></i> = <?= translate('Disabled'); ?>
                        </li>
                    </ul>
                </div>
            </div>

            <?php
        }

        $sectionsWithoutBlock = $sections->where('block_id', null);
        if ($sectionsWithoutBlock->count()) {
            ?>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <?= translate('Sections'); ?> <small><?= translate('Block').': '.translate('Not specified'); ?></small>
                    </h4>
                </div>
                <div class="card-body">

                    <div class="nestable sections" data-group="<?= ('grouped' === $frontendTheme->block_handling ? 0 : 0); ?>" data-max-depth="1">
                        <?= $view->renderSectionNestable($sectionsWithoutBlock, false); ?>
                    </div>

                    <ul class="list-inline small">
                        <li class="list-inline-item">
                            <i class="fa fa-toggle-on"></i> = <?= translate('Enabled'); ?>
                        </li>
                        <li class="list-inline-item">
                            <i class="fa fa-toggle-off"></i> = <?= translate('Disabled'); ?>
                        </li>
                    </ul>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <div class="col-xl-5">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Create section'); ?>
            </h4>
            <div class="card-body">
                <form method="post" action="<?= generate_url('backend_section_create'); ?>">
                    <input value="<?= $page->id(); ?>" type="hidden" name="page_id" />

                    <div class="form-group row <?= has_validation_error('block_key', 'has-danger'); ?>">
                        <label for="selectBlock" class="col-sm-3 col-form-label">
                            <?= translate('Block'); ?> *
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control" name="block_id" id="selectBlock" data-placeholder="">
                                <?php
                                foreach ($blocks as $block) {
                                    ?>
                                    <option value="<?= $block->id(); ?>"><?= $block->title; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row <?= has_validation_error('module_id', 'has-danger'); ?>">
                        <label for="selectModule" class="col-sm-3 col-form-label">
                            <?= translate('Module'); ?> *
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control" name="module_id" id="selectModule" data-placeholder="">
                                <?php
                                foreach ($modules as $module) {
                                    ?>
                                    <option value="<?= $module->id(); ?>"><?= $module->name; ?></option>
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
                                <input type="checkbox" checked id="checkboxIsActive" class="custom-control-input" value="1" name="is_active">
                                <label class="custom-control-label" for="checkboxIsActive"><?= translate('Section is active and visible'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9">
                            <button type="submit" class="btn btn-primary btn-icon-left">
                                <span class="btn-icon">
                                    <i class="fa fa-sync"></i>
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


