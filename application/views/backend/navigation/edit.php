<?php
if (1 !== $navigation->id()) {
    echo $view->renderTemplate('backend/navigation/navbar', [
        'navigation' => $navigation,
    ]);
}

?>
<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Edit navigation'); ?>
            </h4>
            <div class="card-body">

                <form method="post" action="<?= generate_url('backend_navigation_update'); ?>">
                    <input value="<?= $navigation->id(); ?>" type="hidden" name="navigation_id" />

                    <div class="form-group row">
                        <label for="inputTitle" class="col-sm-3 col-form-label">
                            <?= translate('Title'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputTitle" type="text" required class="form-control" name="title" maxlength="50" minlength="3" <?= (1 === $navigation->id() ? 'disabled' : ''); ?> value="<?= (1 === $navigation->id() ? translate($navigation->title) : $navigation->title); ?>" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="inputNavigationKey" class="col-sm-3 col-form-label">
                            <?= translate('Key'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputNavigationKey" type="text" required class="form-control" name="navigation_key" maxlength="50" minlength="3" <?= (1 === $navigation->id() ? 'disabled' : ''); ?> value="<?= $navigation->navigation_key; ?>" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9">
                            <button type="submit" <?= (1 === $navigation->id() ? 'disabled' : ''); ?>class="btn btn-primary btn-icon-left">
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
