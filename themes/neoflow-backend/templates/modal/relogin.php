<div class="modal fade" id="reloginModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= translate('Relogin'); ?></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form method="post" action="<?= generate_url('api_user_auth'); ?>">

                <div class="modal-body">
                    <p><?= translate('Your session has expired. Please login again.'); ?></p>
                    <div class="form-group">
                        <label for="inputEmail">
                            <?= translate('Email address'); ?>
                        </label>
                        <input id="inputEmail" class="form-control" disabled name="email" type="text">
                    </div>
                    <div class="form-group">
                        <label for="inputPassword">
                            <?= translate('Password'); ?>
                        </label>
                        <input id="inputPassword2" class="form-control" disabled name="password" type="password" value="">
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary btn-icon-left pull-left mr-auto">
                        <span class="btn-icon">
                            <i class="fa fa-sign-in-alt"></i>
                        </span>
                        <?= translate('Login'); ?>
                    </button>
                    <button type="button" class="btn btn-outline-light" data-dismiss="modal">
                        <?= translate('Cancel'); ?>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>