<div class="card">
    <h4 class="card-header">
        <?= translate('WYSIWYG editor') ?>
    </h4>
    <div class="card-body">
        <form method="post" action="<?= generate_url('pmod_wysiwyg_backend_update') ?>" class="form-horizontal">
            <div class="form-group <?= has_validation_error('content', 'has-error') ?>">
                <input type="hidden" value="<?= $wysiwyg->id() ?>" name="wysiwyg_id" />
                <input type="hidden" value="<?= $section->id() ?>" name="section_id" />
                <?= Neoflow\CMS\App::instance()->service('wysiwyg')->renderEditor('content[section-'.$section->id().']', 'section-'.$section->id(), $wysiwyg->content) ?>
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

