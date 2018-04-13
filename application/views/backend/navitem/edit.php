<?php
if (1 !== $navigation->id()) {
    echo $view->renderTemplate('backend/navigation/navbar', ['navigation' => $navigation]);
}

?>
<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Edit item'); ?>
            </h4>
            <div class="card-body">

                <form method="post" action="<?= generate_url('backend_navitem_update'); ?>">
                    <input value="<?= $navitem->id(); ?>" type="hidden" name="navitem_id" />

                    <div class="form-group row">
                        <label for="inputTitle" class="col-sm-3 col-form-label">
                            <?= translate('Title'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputTitle" type="text" required class="form-control" name="title" maxlength="50" minlength="3" value="<?= $navitem->title; ?>" />
                        </div>
                    </div>

                    <div class="form-group row <?= has_validation_error('parent_navitem_id', 'has-danger'); ?>">
                        <label for="selectPage" class="col-sm-3 col-form-label">
                            <?= translate('Page'); ?>
                        </label>
                        <div class="col-sm-9">
                            <select <?= (1 == $navitem->navigation_id ? 'disabled' : ''); ?> class="form-control select2" name="page_id" id="selectPage">
                                <?= $view->renderNavitemOptions($pageNavitems, 0, [$navitem->page_id], [], 'page_id'); ?>
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
                                <?= $view->renderNavitemOptions($navitems, 0, [$navitem->parent_navitem_id], [$navitem->id()]); ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9">
                            <input type="hidden" value="0" name="is_active" />
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkboxIsActive" value="1" name="is_active" <?= ($navitem->is_active ? 'checked' : ''); ?>>
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

    </div>
</div>
