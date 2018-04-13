<?php
$index = 1;
$numberOfBreadcrumbs = count($breadcrumbs);
if ($numberOfBreadcrumbs > 0) {
    ?>
    <div class="container">
        <ol class="breadcrumb">
            <?php
            foreach ($breadcrumbs as $breadcrumb) {
                if ($numberOfBreadcrumbs > $index++) {
                    ?>
                    <li class="breadcrumb-item">
                        <a href="<?= $breadcrumb['relative_url']; ?>" ><?= $breadcrumb['title']; ?></a>
                    </li>
                    <?php
                } else {
                    ?>
                    <li class="breadcrumb-item active">
                        <?= $breadcrumb['title']; ?>
                    </li>
                    <?php
                }
            } ?>
        </ol>
    </div>
    <?php
}
