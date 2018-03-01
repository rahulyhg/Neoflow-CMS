<?php
$index = 1;
$numberOfBreadcrumbs = count($breadcrumbs);
if ($numberOfBreadcrumbs > 0) {
    ?>
    <ol class="breadcrumb">
        <?php
        foreach ($breadcrumbs as $breadcrumb) {
            if ($numberOfBreadcrumbs > $index++) {
                ?>
                <li>
                    <a href="<?= $breadcrumb['relative_url']; ?>" ><?= $breadcrumb['title']; ?></a>
                </li>
            <?php
            } else {
                ?>
                <li class="active">
                    <?= $breadcrumb['title']; ?>
                </li>
                <?php
            }
        } ?>
    </ol>
    <?php
}
