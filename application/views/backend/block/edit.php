<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Edit block'); ?>
            </h4>
            <div class="card-body">
                <form method="post" action="<?= generate_url('backend_block_update'); ?>">
                    <input value="<?= $block->id(); ?>" type="hidden" name="block_id" />

                    <div class="form-group row">
                        <label for="inputTitle" class="col-sm-3 col-form-label">
                            <?= translate('Title'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputTitle" type="text" required class="form-control" name="title" maxlength="50" minlength="3" value="<?= $block->title; ?>" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputNavigationKey" class="col-sm-3 col-form-label">
                            <?= translate('Key'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputNavigationKey" type="text" required class="form-control" name="block_key" maxlength="50" minlength="3" value="<?= $block->block_key; ?>" />
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
