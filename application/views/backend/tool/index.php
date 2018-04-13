<div class="row">
    <div class="col-xl-8">

        <div class="card">
            <h4 class="card-header">
                <?= translate('All tools'); ?>
            </h4>

            <table class="datatable table display responsive no-wrap" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th data-priority="0" data-order="true">
                            <?= translate('Name'); ?>
                        </th>
                        <th data-priority="1">
                            <?= translate('Description'); ?>
                        </th>
                        <th data-orderable="false" data-filterable="false" data-priority="0"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($modules as $module) {
    ?>
                        <tr class="<?= (!$module->is_active ? 'disabled' : ''); ?>">
                            <td>
                                <a href="<?= generate_url($module->backend_route); ?>">
                                    <?= translate($module->name, [], false, false, false); ?>
                                </a>
                            </td>
                            <td>
                                <?= translate($module->description, [], false, false, false); ?>
                            </td>
                            <td class="text-right nowrap">
                                <a href="<?= generate_url($module->backend_route); ?>" class="btn btn-primary btn-sm btn-icon-right" title="<?= translate('Execute tool'); ?>">
                                    <?= translate('Execute'); ?>
                                    <span class="btn-icon">
                                        <i class="fa fa-cog"></i>
                                    </span>
                                </a>
                            </td>
                        </tr>
                    <?php
} ?>
                </tbody>
            </table>

        </div>

    </div>
</div>

