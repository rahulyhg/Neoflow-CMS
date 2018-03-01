<?php
if (1 !== $navigation->id()) {
    echo $view->renderTemplate('backend/navigation/navbar', array('navigation' => $navigation));
}

?>

<div class="row">
    <div class="col-xl-7">
        <div class="card">
            <h4 class="card-header">
                <?= translate('Navigation item', [], true); ?>
            </h4>

            <?php if (count($languages) > 1) {
    ?>
                <ul class="nav nav-tabs nav-fill">
                    <?php foreach ($languages as $language) {
        ?>
                        <li class="nav-item">
                            <a class="nav-link<?= ($language->id() === $navigationLanguage->id() ? ' active' : ''); ?>" href="<?= generate_url('backend_navitem_index', array('id' => $navigation->id(), 'language_id' => $language->id())); ?>">
                                <?= $language->renderFlagIcon(); ?> <span class="d-none d-sm-inline-block"><?= translate($language->title); ?></span>
                            </a>
                        </li>
                    <?php
    } ?>
                </ul>
            <?php
} ?>

            <div class="card-body">

                <?php if ($navitems->count()) {
        ?>
                    <div class="nestable" data-save-url="<?= generate_url('backend_navitem_reorder'); ?>">
                        <?= $view->renderNavitemNestable($navitems); ?>
                    </div>
                    <ul class="list-inline small">
                        <li><i class="fa fa-lock"></i> = <?= translate('Accessible only for authorized users'); ?></li>
                    </ul>
                <?php
    } else {
        ?>
                    <p class="text-center text-muted"><?= translate('No results found'); ?></p>
                <?php
    } ?>

            </div>
        </div>
    </div>
    <div class="col-xl-5">


        <?php if (1 !== $navigation->id()) {
        ?>
            <div class="card">
                <h4 class="card-header">
                    <?= translate('Create item'); ?>
                </h4>
                <div class="card-body">
                    <form method="post" action="<?= generate_url('backend_navitem_create'); ?>">
                        <input type="hidden" value="<?= $navigationLanguage->id(); ?>" name="language_id" />
                        <input type="hidden" value="<?= $navigation->id(); ?>" name="navigation_id" />
                        <div class="form-group row <?= has_validation_error('title', 'has-danger'); ?>">
                            <label for="inputTitle" class="col-sm-3 col-form-label">
                                <?= translate('Title'); ?>
                            </label>
                            <div class="col-sm-9">
                                <input id="inputTitle" type="text" class="form-control" name="title" maxlength="50" minlength="3" />
                            </div>
                        </div>

                        <div class="form-group row <?= has_validation_error('parent_navitem_id', 'has-danger'); ?>">
                            <label for="selectPage" class="col-sm-3 col-form-label">
                                <?= translate('Page'); ?>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="page_id" id="selectPage">
                                    <?= $view->renderNavitemOptions($pageNavitems, 0, [], [], 'page_id'); ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row <?= has_validation_error('parent_navitem_id', 'has-danger'); ?>">
                            <label for="selectParentNavitem" class="col-sm-3 col-form-label">
                                <?= translate('Top item'); ?>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="parent_navitem_id" id="selectParentNavitem">
                                    <option value=""><?= translate('None'); ?></option>
                                    <?= $view->renderNavitemOptions($navitems); ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="offset-sm-3 col-sm-9">
                                <input type="hidden" value="0" name="is_active" />
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" id="checkboxIsActive" class="custom-control-input" value="1" name="is_active">
                                    <label class="custom-control-label" for="checkboxIsActive"><?= translate('Item is active and visible'); ?></label>
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
        <?php
    } ?>
    </div>
</div>