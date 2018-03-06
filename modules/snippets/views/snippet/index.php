<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('All snippets'); ?>
            </h4>

            <table class="datatable table display responsive no-wrap" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th data-orderable="false" data-filterable="false" data-priority="0"></th>
                        <th data-priority="0" data-order="true">
                            <?= translate('Title'); ?>
                        </th>
                        <th data-priority="2">
                            <?= translate('Placeholder'); ?>
                        </th>
                        <th class="none" data-priority="1">
                            <?= translate('Description'); ?>
                        </th>
                        <th data-orderable="false" data-filterable="false" data-priority="0"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($snippets as $snippet) {
                        $codeStatus = $snippet->getCodeStatus();

                        ?>
                        <tr class="<?= (!$codeStatus ? 'disabled' : ''); ?>">
                            <td class="<?= (!$codeStatus ? 'text-danger' : 'text-success'); ?> nowrap">
                                <?php if ($codeStatus) {

                                    ?>
                                    <i class="fa fa-fw fa-check"></i>
                                    <?php
                                } else {

                                    ?>
                                    <i class="fa fa-fw fa-times"></i>
                                <?php }

                                ?>
                            </td>
                            <td>
                                <a href="<?= generate_url('tmod_snippets_backend_edit', array('id' => $snippet->id())); ?>" title="<?= translate('Edit snippet'); ?>">
                                    <?= $snippet->title ?>
                                </a>
                            </td>
                            <td><span>[[</span><?= $snippet->placeholder ?><span>]]</span></td>
                            <td><?= $snippet->description ?></td>
                            </td>
                            <td class="text-right nowrap">
                                <a href="<?= generate_url('tmod_snippets_backend_edit', array('id' => $snippet->id())); ?>" class="btn btn-outline-light btn-sm btn-icon-left" title="<?= translate('Edit snippet'); ?>">
                                    <span class="btn-icon">
                                        <i class="fa fa-pencil-alt"></i>
                                    </span>
                                    <?= translate('Edit'); ?>
                                </a>
                                <a href="<?= generate_url('tmod_snippets_backend_delete', array('id' => $snippet->id())); ?>" class="btn btn-primary btn-sm confirm-modal" data-message="<?= translate('Are you sure you want to delete it?'); ?>" title="<?= translate('Delete snippet'); ?>">
                                    <i class="fa fa-fw fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }

                    ?>
                </tbody>
            </table>

            <div class="dataTable_info_src">
                <ul class="list-inline small">
                    <li class="list-inline-item">
                        <span class="text-success"><i class="fa fa-fw fa-check"></i></span> = <?= translate('Code is valid'); ?>
                    </li>
                    <li class="list-inline-item">
                        <span class="text-danger"><i class="fa fa-fw fa-times"></i></span> = <?= translate('Code is invalid'); ?>
                    </li>
                </ul>
            </div>
        </div>

    </div>
    <div class="col-xl-5">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Create snippet'); ?>
            </h4>
            <div class="card-body">
                <form method="post" action="<?= generate_url('tmod_snippets_backend_create'); ?>" class="form-horizontal">
                    <div class="form-group row <?= has_validation_error('title', 'has-error'); ?>">
                        <label for="inputTitle" class="col-sm-3 control-label">
                            <?= translate('Title'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputTitle" type="text" class="form-control" minlength="3" name="title" maxlength="100" />
                        </div>
                    </div>

                    <div class="form-group row <?= has_validation_error('placeholder', 'has-error'); ?>">
                        <label for="inputPlaceholder" class="col-sm-3 control-label">
                            <?= translate('Placeholder'); ?>
                        </label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        [[
                                    </span>
                                </div>
                                <input id="inputPlaceholder" type="text" class="form-control" minlength="3" name="placeholder" maxlength="100" />
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        ]]
                                    </span>
                                </div>
                            </div>

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

    </div>
</div>

