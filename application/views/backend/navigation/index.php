<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('All navigations'); ?>
            </h4>

            <?php if ($navigations->count()) {
    ?>

                <table class="datatable table display responsive no-wrap" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th data-priority="0" data-order="true">
                                <?= translate('Title'); ?>
                            </th>
                            <th  data-priority="1">
                                <?= translate('Key'); ?>
                            </th>
                            <th data-orderable="false" data-filterable="false" data-priority="0"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($navigations as $navigation) {
        ?>
                            <tr>
                                <td class="nowrap">
                                    <a href="<?= generate_url('backend_navitem_index', array('id' => $navigation->id())); ?>" <?= (1 === $navigation->id() ? 'class="disabled"' : ''); ?>  title="<?= translate('Manage items'); ?>">
                                        <?= (1 === $navigation->id() ? translate($navigation->title) : $navigation->title); ?>
                                    </a>
                                </td>
                                <td>
                                    <?= $navigation->navigation_key; ?>
                                </td>
                                <td class="text-right nowrap">

                                    <a href="<?= generate_url('backend_navitem_index', array('id' => $navigation->id())); ?>" class="btn btn-outline-light d-none d-xl-inline-block btn-sm btn-icon-left" title="<?= translate('Navigation item', [], true); ?>">
                                        <span class="btn-icon">
                                            <i class="fa fa-th-list"></i>
                                        </span>
                                        <?= translate('Item', [], true); ?>
                                    </a>
                                    <a href="<?= generate_url('backend_navigation_edit', array('id' => $navigation->id())); ?>" class="btn btn-outline-light btn-sm d-none d-xl-inline-block <?= (1 === $navigation->id() ? 'disabled' : ''); ?>" title="<?= translate('Edit navigation'); ?>">
                                        <i class="fa fa-fw fa-pencil-alt"></i>
                                    </a>
                                    <a href="<?= generate_url('backend_navigation_delete', array('id' => $navigation->id())); ?>" class="btn btn-primary btn-sm confirm-modal <?= (1 === $navigation->id() ? 'disabled' : ''); ?>" data-message="<?= translate('Are you sure you want to delete it?'); ?>" title="<?= translate('Delete navigation'); ?>">
                                        <i class="fa fa-fw fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php
    } ?>
                    </tbody>
                </table>

            <?php
} else {
        ?>
                <div class="card-body">
                    <p class="text-center text-muted"><?= translate('No results found'); ?></p>
                </div>
            <?php
    } ?>

        </div>
    </div>
    <div class="col-xl-5">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Create navigation'); ?>
            </h4>
            <div class="card-body">
                <form method="post" action="<?= generate_url('backend_navigation_create'); ?>">
                    <div class="form-group row">
                        <label for="inputTitle" class="col-sm-3 col-form-label">
                            <?= translate('Title'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputTitle" type="text" required class="form-control" name="title" maxlength="50" minlength="3" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputNavigationKey" class="col-sm-3 col-form-label">
                            <?= translate('Key'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputNavigationKey" type="text" required class="form-control" name="navigation_key" maxlength="50" minlength="3" />
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

        <div class="card">
            <h4 class="card-header">
                <?= translate('Load navigation', [], true); ?>
            </h4>
            <div class="card-body">

                <p><?= translate('Load navigations message'); ?></p>

                <a href="<?= generate_url('backend_navigation_load'); ?>" class="btn btn-primary btn-icon-left confirm-modal" data-message="<?= translate('Are you sure you want to load the navigations of the current frontend theme and maybe rename or even delete the existing navigations?'); ?>" title="<?= translate('Load navigation', [], true); ?>">
                    <span class="btn-icon">
                        <i class="fa fa-sync"></i>
                    </span>
                    <?= translate('Load'); ?>
                </a>

            </div>
        </div>


    </div>
</div>