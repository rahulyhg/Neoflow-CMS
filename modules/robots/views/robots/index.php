<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Edit robots.txt') ?>
            </h4>

            <div class="card-body">

                <form method="post" action="<?= generate_url('tmod_robots_backend_update') ?>" class="form-horizontal">

                    <div class="form-group <?= has_validation_error('custom_css', 'is-invalid') ?>">
                        <textarea name="content" class="form-control vresize" rows="8"><?= $content ?></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-icon-left">
                            <span class="btn-icon">
                                <i class="fa fa-save"></i>
                            </span>
                            <?= translate('Save') ?>
                        </button>
                    </div>
                </form>

            </div>

        </div>

    </div>
    <div class="col-xl-5">



    </div>
</div>

