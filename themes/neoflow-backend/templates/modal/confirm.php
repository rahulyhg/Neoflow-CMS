<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= translate('Please confirm') ?></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= translate('Are you sure?') ?>
            </div>
            <div class="modal-footer d-flex justify-content-end">
                <a href="#" class="btn btn-primary btn-icon-left pull-left mr-auto btn-confirm">
                    <span class="btn-icon">
                        <i class="fa fa-check"></i>
                    </span>
                    <?= translate('Confirm') ?>
                </a>
                <button type="button" class="btn btn-outline-light btn-cancel" data-dismiss="modal"><?= translate('Cancel') ?></button>
            </div>
        </div>
    </div>
</div>