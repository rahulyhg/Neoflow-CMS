<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Setting', [], true); ?>
            </h4>

            <div class="card-body">

                <p>
                    <?= translate('The search module has no settings, but can be used in the frontend via this URL:') ?>
                </p>

                <ul>
                    <li>
                        <a target="_blank" href="<?= $view->getWebsiteUrl('/search') ?>">
                            <?= $view->getWebsiteUrl('/search') ?>
                        </a>
                    </li>
                </ul>

            </div>

        </div>

    </div>
</div>