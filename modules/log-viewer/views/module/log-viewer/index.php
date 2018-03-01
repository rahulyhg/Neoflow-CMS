<div class="row">
    <div class="col-xl-8">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Logfile', [], true); ?>
            </h4>

            <table class="datatable table display responsive no-wrap" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th data-priority="0" data-order="true">
                            <?= translate('Filename'); ?>
                        </th>
                        <th data-orderable="false" data-filterable="false" data-priority="0"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logfiles as $logfile) {
    ?>
                        <tr>
                            <td>
                                <a href="<?= generate_url('tmod_log_viewer_backend_show', ['logfile' => $logfile->getName()]); ?>" title="<?= translate('Show logfile'); ?>">
                                    <?= $logfile->getName(); ?>
                                </a>
                            </td>
                            <td class="text-right nowrap">
                                <a href="<?= generate_url('tmod_log_viewer_backend_show', ['logfile' => $logfile->getName()]); ?>" class="btn btn-outline-light btn-sm btn-icon-left" title="<?= translate('Show logfile'); ?>">
                                    <span class="btn-icon">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                    <?= translate('Show'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php
}

                    ?>
                </tbody>
            </table>

        </div>

    </div>
</div>
