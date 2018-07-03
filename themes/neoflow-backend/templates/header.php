<header id="header">
    <div class="container-fluid">
        <div class="title">
            <h1>
                <?= shortify($view->getTitle(), 35) ?>
                <small><?= $view->getSubtitle() ?></small>
            </h1>
        </div>

        <?php
        $breadcrumbs = $view->getBreadcrumbs();
        if (count($breadcrumbs) > 0) {
            ?>
            <ol class="breadcrumb">
                <?php
                foreach ($breadcrumbs as $breadcrumb) {
                    if ($breadcrumb['url']) {
                        ?>
                        <li class="breadcrumb-item"><a href="<?= $breadcrumb['url'] ?>"><?= $breadcrumb['title'] ?></a></li>
                        <?php
                    } else {
                        ?>
                        <li class="breadcrumb-item"><?= $breadcrumb['title'] ?></li>
                        <?php
                    }
                } ?>
            </ol>
            <?php
        }
        if ($view->getBackUrl() || $view->getPreviewUrl()) {
            ?>
            <ul class="list-inline buttons">
                <?php if ($view->getBackUrl()) { ?>
                    <li class="list-inline-item">
                        <a href="<?= $view->getBackUrl() ?>" class="btn btn-outline-light btn-sm btn-back btn-icon-left">
                            <span class="btn-icon">
                                <i class="fa fa-chevron-left"></i>
                            </span>
                            <?= translate('Back') ?>
                        </a>
                    </li>
                    <?php
                }
                if ($view->getPreviewUrl()) {
                    ?>
                    <li class="list-inline-item">
                        <a href="<?= $view->getPreviewUrl() ?>" target="_blank" class="btn btn-outline-light btn-sm btn-icon-left">
                            <span class="btn-icon">
                                <i class="fa fa-desktop"></i>
                            </span>
                            <?= translate('Preview') ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
    </div>
</header>


