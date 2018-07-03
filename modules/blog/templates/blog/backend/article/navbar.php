<div class="card">
    <ul class="nav nav-pills flex-column flex-sm-row">
        <li class="nav-item text-sm-center">
            <a class="nav-link<?= is_current_route('pmod_blog_backend_article_edit', ' active') ?>"
               href="<?= generate_url('pmod_blog_backend_article_edit', [
                   'id' => $article->id(),
                   'section_id' => $article->section_id
               ]) ?>"
               title="<?= translate('Content', [], true) ?>">
                <i class="fa fa-fw fa-align-left"></i> <?= translate('Content') ?>
            </a>
        </li>
        <li class="nav-item text-sm-center">
            <a class="nav-link<?= is_current_route('pmod_blog_backend_article_edit_metadata', ' active') ?>"
               href="<?= generate_url('pmod_blog_backend_article_edit_metadata', [
                   'id' => $article->id(),
                   'section_id' => $article->section_id
               ]) ?>"
               title="<?= translate('Metadata', [], true) ?>">
                <i class="fa fa-fw fa-database"></i> <?= translate('Metadata') ?>
            </a>
        </li>
    </ul>
</div>