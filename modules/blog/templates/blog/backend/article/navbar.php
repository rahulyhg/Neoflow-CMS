<div class="card">
    <ul class="nav nav-pills flex-column flex-sm-row">
        <li class="nav-item text-sm-center">
            <a class="nav-link<?= is_current_route('pmod_blog_backend_article_*', ' active') ?>"
               href="<?= generate_url('pmod_blog_backend_article_index', ['section_id' => $view->get('section')->id()]) ?>"
               title="<?= translate('Article', [], true) ?>">
                <i class="fa fa-fw fa-th-list"></i> <?= translate('Article', [], true) ?>
            </a>
        </li>
        <li class="nav-item text-sm-center">
            <a class="nav-link<?= is_current_route('pmod_blog_backend_category_*', ' active') ?>"
               href="<?= generate_url('pmod_blog_backend_category_index', ['section_id' => $view->get('section')->id()]) ?>"
               title="<?= translate('Category', [], true) ?>">
                <i class="fa fa-fw fa-list"></i> <?= translate('Category', [], true) ?>
            </a>
        </li>
        <li class="nav-item text-sm-center">
            <a class="nav-link<?= is_current_route('pmod_blog_backend_setting_*', ' active') ?>"
               href="<?= generate_url('pmod_blog_backend_setting_index', ['section_id' => $view->get('section')->id()]) ?>"
               title="<?= translate('Setting', [], true) ?>">
                <i class="fa fa-fw fa-cogs"></i> <?= translate('Setting', [], true) ?>
            </a>
        </li>
    </ul>
</div>