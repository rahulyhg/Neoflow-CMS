<div class="card">
    <ul class="nav nav-pills flex-column flex-sm-row">
        <li class="nav-item text-sm-center">
            <a class="nav-link<?= is_current_route(['backend_navitem_*'], ' active') ?>" href="<?= generate_url('backend_navitem_index', ['navigation_id' => $navigation->id()]) ?>" title="<?= translate('Navigation item', [], true) ?>">
                <i class="fa fa-fw fa-th-list"></i> <?= translate('Item', [], true) ?>
            </a>
        </li>
        <li class="nav-item text-sm-center">
            <a class="nav-link<?= is_current_route('backend_navigation_edit', ' active') ?>" href="<?= generate_url('backend_navigation_edit', ['id' => $navigation->id()]) ?>" title="<?= translate('Navigation settings') ?>">
                <i class="fa fa-fw fa-cog"></i> <?= translate('Edit navigation') ?>
            </a>
        </li>
    </ul>
</div>