
<?php
foreach ($navigation as $navTreeItem) {
    if (count($navTreeItem['children']) && 0 == $navTreeItem['level']) {
        ?>
        <li class="<?= ($navTreeItem['status'] ? 'active' : ''); ?> dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?= $navTreeItem['title']; ?> <span class="sr-only">(current)</span></a>
            <ul class="dropdown-menu" role="menu">
                <?php
                echo $view->renderTemplate(__FILE__, [
                    'navigation' => $navTreeItem['children'],
                ]); ?>
            </ul>
        </li>
    <?php
    } else {
        ?>
        <li class="<?= ($navTreeItem['status'] ? 'active' : ''); ?>">
            <a href="<?= $navTreeItem['relative_url']; ?>"><?= $navTreeItem['title']; ?></a>
        </li>
        <?php
    }
}

?>
