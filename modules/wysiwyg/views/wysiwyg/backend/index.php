<div class="card">
    <h4 class="card-header">
        <?= translate('WYSIWYG editor') ?>
    </h4>
    <div class="card-body">
        <form method="post" action="<?= generate_url('pmod_wysiwyg_backend_update') ?>" class="form-horizontal">
            <div class="form-group <?= has_validation_error('content', 'is-invalid') ?>">
                <input type="hidden" value="<?= $wysiwyg->id() ?>" name="wysiwyg_id"/>
                <input type="hidden" value="<?= $section->section_id ?>" name="section_id"/>
                <?php
                echo Neoflow\CMS\App::instance()
                    ->service('wysiwyg')
                    ->renderEditor($view, 'content[section-' . $section->id() . ']', 'section-' . $section->id(), $wysiwyg->content, '350px', [
                        'uploadDirectory' => [
                            'path' => $view->config()->getMediaPath('/modules/wysiwyg/section-' . $section->id()),
                            'url' => $view->config()->getMediaUrl('/modules/wysiwyg/section-' . $section->id()),
                        ]
                    ]);
                ?>
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

