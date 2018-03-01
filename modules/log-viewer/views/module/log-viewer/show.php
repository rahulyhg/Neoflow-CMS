<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <h4 class="card-header">
                <?= translate('Show logfile'); ?>
            </h4>
            <div class="card-body">

                <div class="embed-responsive embed-responsive-21by9 border border-light">
                    <iframe id="logfileIframe" class="embed-responsive-item" src="<?= generate_url('tmod_log_viewer_backend_get', ['logfile' => $logfile]); ?>"></iframe>
                </div>
                <small class="form-text text-muted">
                    Log file will be updated every 15 seconds...... TRANSLATE!
                </small>
                <script>
                    var logfileIframe = document.getElementById('logfileIframe');

                    // Scroll onload to bottom
                    logfileIframe.onload = function () {
                        logfileIframe.contentWindow.scrollTo(0, 9999999999);
                    };

                    // Refresh every 15 seconds
                    setInterval(function () {
                        logfileIframe.src = logfileIframe.src;
                    }, 15000);
                </script>

            </div>
        </div>
    </div>
</div>