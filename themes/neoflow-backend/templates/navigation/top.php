<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top" id="topNavigation">

    <span class="navbar-text">
        <?= $view->getWebsiteTitle(); ?>
    </span>

    <ul class="navbar-nav navbar-expand ml-auto">

        <?php if (!is_current_route('install_*')) {
    ?>

            <?php if ($view->settings()->getLanguages()->count() > 1) {
        ?>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                        <span class="nav-link-icon">
                            <?= $view->translator()->getActiveLanguage()->renderFlagIcon(); ?>
                        </span>
                    </a>
                    <div class="dropdown-menu">
                        <?php foreach ($view->settings()->getLanguages() as $language) {
            ?>
                            <a class="dropdown-item<?= ($language->code === $view->translator()->getActiveLanguageCode() ? ' active' : ''); ?>" href="<?= generate_url('', [], $_GET, $language->code); ?>">
                                <?= $language->renderFlagIcon(); ?>
                            </a>
                        <?php
        } ?>
                    </div>
                </li>
                <?php
    }
    if ($view->getService('auth')->isAuthenticated()) {
        ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= generate_url('backend_profile_index'); ?>" title="<?= translate('Profile'); ?>">
                        <span class="nav-link-icon">
                            <i class="fa fa-fw fa-user"></i>
                        </span>
                        <span class="nav-link-text d-none d-md-inline">
                            <?= translate('Profile'); ?>
                        </span>
                    </a>
                </li>
            <?php
    } ?>

            <li class="nav-item">
                <a class="nav-link" target="_blank" href="<?= generate_url('frontend_index'); ?>" title="<?= translate('To the frontend'); ?>">
                    <span class="nav-link-icon">
                        <i class="fa fa-fw fa-desktop"></i>
                    </span>
                    <span class="nav-link-text d-none d-md-inline">
                        <?= translate('To the frontend'); ?>
                    </span>
                </a>
            </li>

            <?php if ($view->getService('auth')->isAuthenticated()) {
        ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= generate_url('backend_auth_logout'); ?>" title="<?= translate('Logout'); ?>">
                        <span class="nav-link-text d-none d-md-inline">
                            <?= translate('Logout'); ?>
                        </span>
                        <span class="nav-link-icon">
                            <i class="fa fa-fw fa-sign-out-alt"></i>
                        </span>
                    </a>
                </li>
            <?php
    } ?>

        <?php
} ?>

    </ul>
</nav>