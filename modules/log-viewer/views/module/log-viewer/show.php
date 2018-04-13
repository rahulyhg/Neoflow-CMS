
<div class="card">
    <h4 class="card-header">
        <?= translate('Show logfile'); ?>
    </h4>
    <div class="card-body">

        <div class="embed-responsive embed-responsive-21by9 border border-light">
            <iframe id="logfileIframe" class="embed-responsive-item" src="<?= generate_url('tmod_log_viewer_backend_get', ['logfile' => $logfile]); ?>"></iframe>
        </div>
        <small class="form-text text-muted">
            <?= translate('Log file will be updated in {0}', ['<span class="timer" id="logViewerTimer" data-timeout-callback="refreshLogfileIframe()" data-time="15">'.gmdate('H:i:s', 15).'</span>']); ?>
        </small>

    </div>
</div>