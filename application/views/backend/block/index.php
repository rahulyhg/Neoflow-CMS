<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('All blocks'); ?>
            </h4>


            <?php if ($blocks->count()) {
    ?>

                <table class="datatable table display responsive no-wrap" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th data-priority="0" data-order="true">
                                <?= translate('Title'); ?>
                            </th>
                            <th data-priority="1">
                                <?= translate('Key'); ?>
                            </th>
                            <th data-orderable="false" data-filterable="false" data-priority="0"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($blocks as $block) {
        ?>
                            <tr>
                                <td class="nowrap">
                                    <a href="<?= generate_url('backend_block_edit', array('id' => $block->id())); ?>" title="<?= translate('Edit block'); ?>">
                                        <?= $block->title; ?>
                                    </a>
                                </td>
                                <td>
                                    <?= $block->block_key; ?>
                                </td>
                                <td class="text-right nowrap">
                                    <a href="<?= generate_url('backend_block_edit', array('id' => $block->id())); ?>" class="btn btn-outline-light d-none d-xl-inline-block btn-sm btn-icon-left" title="<?= translate('Edit block'); ?>">
                                        <span class="btn-icon">
                                            <i class="fa fa-pencil-alt"></i>
                                        </span>
                                        <?= translate('Edit'); ?>
                                    </a>
                                    <a href="<?= generate_url('backend_block_delete', array('id' => $block->id())); ?>" class="btn btn-primary btn-sm confirm-modal" data-message="<?= translate('Are you sure you want to delete it?'); ?>" title="<?= translate('Delete block'); ?>">
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
                <?= translate('Create block'); ?>
            </h4>
            <div class="card-body">
                <form method="post" action="<?= generate_url('backend_block_create'); ?>">
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
                            <input id="inputNavigationKey" type="text" required class="form-control" name="block_key" maxlength="50" minlength="3" />
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
                <?= translate('Load block', [], true); ?>
            </h4>
            <div class="card-body">

                <p><?= translate('Load blocks message'); ?></p>

                <a href="<?= generate_url('backend_block_load'); ?>" class="btn btn-primary btn-icon-left confirm-modal" data-message="<?= translate('Are you sure you want to load the blocks of the current frontend theme and maybe rename or even delete the existing blocks?'); ?>" title="<?= translate('Load block', [], true); ?>">
                    <span class="btn-icon">
                        <i class="fa fa-sync"></i>
                    </span>
                    <?= translate('Load'); ?>
                </a>

            </div>
        </div>


    </div>
</div>