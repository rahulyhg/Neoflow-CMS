<div class="card">
    <h4 class="card-header"><?= translate('Code editor'); ?></h4>
    <div class="card-body">
        <form method="post" action="<?= generate_url('pmod_code_backend_update'); ?>" class="form-horizontal">
            <div class="form-group <?= has_validation_error('code', 'has-error'); ?>">
                <input type="hidden" value="<?= $code->id(); ?>" name="code_id" />
                <input type="hidden" value="<?= $section->id(); ?>" name="section_id" />

                <?= Neoflow\CMS\App::instance()->service('code')->renderEditor('content[section-'.$section->id().']', '', $code->content, '650px'); ?>

                <small class="form-text <?= ($codeStatus ? 'text-success' : 'text-danger'); ?>">
                    <?php if ($codeStatus) {
    ?>
                        <i class="fa fa-fw fa-check"></i>
                        <?php
} else {
        ?>
                        <i class="fa fa-fw fa-times"></i>
                        <?php
    }
                    echo htmlentities($codeStatusMessage);

                    ?>
                </small>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-icon-left">
                    <span class="btn-icon">
                        <i class="fa fa-save"></i>
                    </span>
                    <?= translate('Save'); ?>
                </button>
            </div>
        </form>
    </div>
</div>
