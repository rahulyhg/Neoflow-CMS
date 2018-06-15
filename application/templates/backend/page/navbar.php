<div class="card">
    <ul class="nav nav-pills flex-column flex-sm-row">
        <li class="nav-item text-sm-center">
            <a class="nav-link<?= is_current_route('backend_section*', ' active') ?>" href="<?= generate_url('backend_section_index', ['page_id' => $page->id()]) ?>" title="<?= translate('Page section', [], true) ?>">
                <i class="fa fa-fw fa-th-list"></i> <?= translate('Sections') ?>
            </a>
        </li>
        <li class="nav-item text-sm-center">
            <a class="nav-link<?= is_current_route('backend_page_edit', ' active') ?>" href="<?= generate_url('backend_page_edit', ['id' => $page->id()]) ?>" title="<?= translate('Edit page') ?>">
                <i class="fa fa-fw fa-pencil-alt"></i> <?= translate('Edit page') ?>
            </a>
        </li>
    </ul>
</div>